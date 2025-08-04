<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\RelatedProduct;
use App\Models\Subcategory;
use App\Models\AddPage;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PageProduct;
use App\Models\Supplier;
use App\Models\TopProducts;
use App\Repository\Interface\BrandInterface;
use App\Repository\Interface\CategoryInterface;
use App\Repository\Interface\ProductInterface;
use App\Repository\Interface\PageProductInterface;
use App\Repository\Interface\SubcategoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use DB;

class ProductController extends Controller
{
    protected $product;
    protected $page_product;
    protected $category;
    protected $subcategory;
    protected $brand;
    public function __construct(ProductInterface $product,PageProductInterface $page_product, CategoryInterface $category, SubcategoryInterface $subcategory, BrandInterface $brand)
    {
        $this->product = $product;
        $this->page_product = $page_product;
        $this->category = $category;
        $this->subcategory = $subcategory;
        $this->brand = $brand;
    }

    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->where('is_page_product', 0)->paginate(30);
        return view('admin.products.index', compact('products'));
    }

    public function pageProductIndex ()
    {
        $page_products = Product::orderBy('created_at', 'desc')->where('is_page_product', 1)->paginate(30);
        return view('admin.page_products.index', compact('page_products'));
    }

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'vendors' => Supplier::orderBy('shop_name', 'asc')->where('is_approved', true)->get(),
            'product' => Product::latest()->first()
        ]);
    }

    public function createVariableProduct ()
    {
        return view('admin.products.create-variable-product', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'vendors' => Supplier::orderBy('shop_name', 'asc')->where('is_approved', true)->get(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        $image = $request->file('image');
        $input['image'] = rand().'pro_main'.$request->name.'.'.'webp';
        $destinationPath = 'product/images';
        $imgFile = Image::make($image->getRealPath());
        $imgFile->resize(240, 240, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('webp', 90)->save($destinationPath.'/'.$input['image']);
        $image->move($destinationPath, $input['image']);
        $imageUrl = url($destinationPath.'/'.$input['image']);

        if($request->type){
            //dd($request->type);
            // $product = new PageProduct();
            $product = new Product();
            $page= AddPage::find($request->type);
            // $product->type = Str::slug($page->name);
            $product->page_name = Str::slug($page->name);
            $product->is_page_product = 1;
        }
        else{
            $product = new Product();
            $product->seo_title = $request->seo_title;
            $product->seo_description = $request->seo_description;
            $product->seo_keyword = $request->seo_keyword;
        }

        if(isset($request->priority)){
            $checkPriority = Product::where('priority', $request->priority)->first();
            if($checkPriority == null){
                $product->priority = $request->priority;
            }
            // elseif($checkPriority != null){
            //     $checkPriority->priority = $checkPriority->id;
            //     $product->priority = $request->priority;
            // }
        }
        if(!isset($request->priority)){
            $previousProduct = Product::orderBy('created_at', 'desc')->first();
            $product->priority = $previousProduct->id+1;
        }

        $product->rating = $request->rating;
        $product->drive_link = $request->drive_link;
        $product->name = $request->name;
        $product->vendor_id = $request->vendor_id;
        $product->slug = str_replace(' ', '-', strtolower($request->name));
        $product->cat_id = $request->cat_id;
        $product->sub_cat_id = $request->sub_cat_id;
        $product->qty = $request->qty;
        $product->buy_price = $request->buy_price;
        $product->wholesale_price = $request->wholesale_price;
        $product->regular_price = $request->regular_price;
        if ($request->discount_price){
            $product->discount_price = $request->discount_price;
        }
        $product->product_code = $request->product_code;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->policy = $request->policy;
        $product->product_type = $request->product_type;
        $product->image = $input['image'];
        $product->imageUrl = $imageUrl;
        $product->save();

        if(!empty($product)){

            //Old Gallery Image...
            // if($request->gallery_image){
            //     $imageGallery = $request->gallery_image;
            //     foreach($imageGallery as $image){
            //         $galleryImageName = rand().$request->name.'.'.$image->extension();
            //         $imgGallery = Image::make($image->path());
            //         $imgGallery->resize(440, 440, function ($const) {
            //             $const->aspectRatio();
            //         })->save('galleryImage'. '/'. $galleryImageName);
            //         $imageUrl = url('galleryImage'.'/'.$galleryImageName);

            //         $productGalleryImage = new ProductImage();
            //         if($request->type){
            //             $productGalleryImage->product_id = $product->id;
            //         }
            //         else{
            //             $productGalleryImage->product_id = $product->id;
            //         }
            //         $productGalleryImage->gallery_image = $galleryImageName;
            //         $productGalleryImage->imageUrl = $imageUrl;
            //         $productGalleryImage->save();
            //     }
            // }

            if ($request->gallery_image) {
                $imageGallery = $request->gallery_image;

                foreach ($imageGallery as $image) {
                    // Generate a unique name for the image
                    $galleryImageName = rand() . $request->name . '.' . 'webp';

                    // Move the uploaded image directly to the target directory
                    $image->move('galleryImage', $galleryImageName);

                    // Generate the image URL
                    $imageUrl = url('galleryImage/' . $galleryImageName);

                    // Save the image data in the database
                    $productGalleryImage = new ProductImage();

                    if ($request->type) {
                        $productGalleryImage->product_id = $product->id;
                    } else {
                        $productGalleryImage->product_id = $product->id;
                    }

                    $productGalleryImage->gallery_image = $galleryImageName;
                    $productGalleryImage->imageUrl = $imageUrl;
                    $productGalleryImage->save();
                }
            }
        }

        // Product color
        if($request->filled('color')){
            $colors = $request->color;
            if (is_array($colors) || is_object($colors)){
                foreach ($colors as $key => $color){
                    $colorName = new ProductColor();
                    if($request->type){
                        $colorName->product_id = $product->id;
                    }
                    else{
                        $colorName->product_id = $product->id;
                    }
                    $colorName->color = $request->color[$key];
                    $colorName->save();
                }
            }

        }
        // Product size
        if ($request->filled('size')){
            $sizes = $request->filled('size');
            if (is_array($sizes) || is_object($sizes)){
                foreach ($sizes as $key => $size){
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $product->id;
                    $sizeName->size = $request->size[$key];
                    $sizeName->save();
                }
            }
        }
        // Related product
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
            if (is_array($relatedProducts || is_object($relatedProducts))){
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new RelatedProduct();
                    $relatedProduct->product_id = $product->id;
                    $relatedProduct->related_product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
            }
        }
        if($request->type){
            return redirect()->route('page.products.index')->with('success', 'Product has been successfully created.');
        }
        return redirect()->route('products.index')->with('success', 'Product has been successfully created.');
    }

    public function storeVariableProduct (Request $request)
    {
        $validatedData = $request->validate([

            'priority'  => 'unique:products,priority',
        ]);

        $image = $request->file('image');
        $input['image'] = rand().'pro_main'.$request->name.'.'.'webp';
        $destinationPath = 'product/images';
        $imgFile = Image::make($image->getRealPath());
        $imgFile->resize(240, 240, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('webp', 90)->save($destinationPath.'/'.$input['image']);
        $image->move($destinationPath, $input['image']);
        $imageUrl = url($destinationPath.'/'.$input['image']);

        if($request->type){
            //dd($request->type);
            // $product = new PageProduct();
            $product = new Product();
            $page= AddPage::find($request->type);
            // $product->type = Str::slug($page->name);
            $product->page_name = Str::slug($page->name);
            $product->is_page_product = 1;
        }
        else{
            $product = new Product();
            $product->seo_title = $request->seo_title;
            $product->seo_description = $request->seo_description;
            $product->seo_keyword = $request->seo_keyword;
        }

        if(isset($request->priority)){
            $checkPriority = Product::where('priority', $request->priority)->first();
            if($checkPriority == null){
                $product->priority = $request->priority;
            }
            // elseif($checkPriority != null){
            //     $checkPriority->priority = $checkPriority->id;
            //     $product->priority = $request->priority;
            // }
        }
        if(!isset($request->priority)){
            $previousProduct = Product::orderBy('created_at', 'desc')->first();
            $product->priority = $previousProduct->id+1;
        }

        $product->rating = $request->rating;
        $product->drive_link = $request->drive_link;
        $product->is_variable = true;
        $product->name = $request->name;
        $product->vendor_id = $request->vendor_id;
        $product->slug = str_replace(' ', '-', strtolower($request->name));
        $product->cat_id = $request->cat_id;
        $product->sub_cat_id = $request->sub_cat_id;
        $product->qty = $request->qty;
        $product->buy_price = $request->buy_price;
        $product->wholesale_price = $request->wholesale_price;
        $product->regular_price = $request->regular_price;
        if ($request->discount_price){
            $product->discount_price = $request->discount_price;
        }
        $product->product_code = $request->product_code;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->policy = $request->policy;
        $product->product_type = $request->product_type;
        $product->image = $input['image'];
        $product->imageUrl = $imageUrl;
        $product->save();

        if (!empty($product)) {
            if ($request->hasFile('gallery_image')) {
                $galleryImages = $request->file('gallery_image');
                $colors = $request->input('color');
                $sizes = $request->input('size');
                $prices = $request->input('price');
                $wholesalePrices = $request->input('wholesale_price');

                foreach ($galleryImages as $index => $image) {
                    // Generate a unique name for the image
                    $galleryImageName = rand() . $request->name . '.' . $image->extension();

                    // Move the uploaded image directly to the target directory
                    $image->move('galleryImage', $galleryImageName);

                    // Generate the image URL
                    $imageUrl = url('galleryImage/' . $galleryImageName);

                    $productGalleryImage = new ProductImage();
                    $productGalleryImage->product_id = $product->id;
                    $productGalleryImage->gallery_image = $galleryImageName;
                    $productGalleryImage->imageUrl = $imageUrl;
                    $productGalleryImage->size = $sizes[$index] ?? null;
                    $productGalleryImage->color = $colors[$index] ?? null;
                    $productGalleryImage->price = $prices[$index] ?? 0;
                    $productGalleryImage->wholesale_price = $wholesalePrices[$index] ?? 0;
                    $productGalleryImage->save();
                }
            }
        }
        // Related product
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
            if (is_array($relatedProducts || is_object($relatedProducts))){
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new RelatedProduct();
                    $relatedProduct->product_id = $product->id;
                    $relatedProduct->related_product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
            }
        }
        if($request->type){
            return redirect()->route('page.products.index')->with('success', 'Product has been successfully created.');
        }
        return redirect()->route('products.index')->with('success', 'Product has been successfully created.');
    }

    public function edit($id, $slug)
    {
        return view('admin.products.edit', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'product' => $this->product->edit($id),
            'vendors' => Supplier::orderBy('shop_name', 'asc')->where('is_approved', true)->get(),
        ]);
    }

    public function editVariableProduct ($id, $slug)
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        $subcategories = Subcategory::orderBy('created_at', 'desc')->get();
        $brands = Brand::orderBy('created_at', 'desc')->get();
        $product = $this->product->edit($id);

        return view ('admin.products.edit-variable-product', compact('categories', 'subcategories', 'brands', 'product'));
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $productUpdate = Product::find($id);
        $imageUpdate = $request->file('image');
        if (isset($imageUpdate)){
            if ($imageUpdate && file_exists(('product/images/').$productUpdate['image'])){
                unlink('product/images/'.$productUpdate->image);
            }

            $updateImageName['image'] = rand().'pro_main'.$request->name.'.'.'webp';
            $updateDestinationPath = 'product/images';

            $imgFile = Image::make($imageUpdate->getRealPath());

            $imgFile->resize(240, 240, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90)->save($updateDestinationPath.'/'.$updateImageName['image']);
            $imageUpdate->move($updateDestinationPath, $updateImageName['image']);
            $productUpdate->image = $updateImageName['image'];
            $imageUrl = url($updateDestinationPath.'/'.$updateImageName['image']);
            $productUpdate->imageUrl = $imageUrl;
        }

        if(isset($request->priority)){
            $checkPriority = Product::where('priority', $request->priority)->where('id', '!=', $productUpdate->id)->first();
            if($checkPriority == null){
                $productUpdate->priority = $request->priority;
            }
            elseif($checkPriority != null){
                return redirect()->back()->with('error', 'Priority already exist!!');
            }
        }

        $productUpdate->rating = $request->rating;
        $productUpdate->drive_link = $request->drive_link;
        $productUpdate->vendor_id = $request->vendor_id;
        $productUpdate->name = $request->name;
        $productUpdate->slug = str_replace(' ', '-', strtolower($request->name));
        $productUpdate->cat_id = $request->cat_id;
        $productUpdate->sub_cat_id = $request->sub_cat_id;
        $productUpdate->qty = $request->qty;
        $productUpdate->buy_price = $request->buy_price;
        $productUpdate->wholesale_price = $request->wholesale_price;
        $productUpdate->regular_price = $request->regular_price;
        if ($request->discount_price){
            $productUpdate->discount_price = $request->discount_price;
        }
        $productUpdate->product_code = $request->product_code;
        $productUpdate->short_description = $request->short_description;
        $productUpdate->long_description = $request->long_description;
        $productUpdate->policy = $request->policy;
        $productUpdate->product_type = $request->product_type;
        $productUpdate->seo_title = $request->seo_title;
        $productUpdate->seo_description = $request->seo_description;
        $productUpdate->seo_keyword = $request->seo_keyword;
        $productUpdate->save();


        //Old version of gallery Image...
        // if($request->gallery_image){
        //     $imageGallery = $request->gallery_image;
        //     ProductImage::where('product_id', $productUpdate->id)->delete();
        //     foreach($imageGallery as $image){
        //         $galleryImageName = rand().$request->name.'.'.$image->extension();
        //         $imgGallery = Image::make($image->path());
        //         $imgGallery->resize(440, 440, function ($const) {
        //             $const->aspectRatio();
        //         })->save('galleryImage'. '/'. $galleryImageName);
        //         $imageUrl = url('galleryImage'.'/'.$galleryImageName);

        //         $productGalleryImage = new ProductImage();
        //         $productGalleryImage->product_id = $productUpdate->id;
        //         $productGalleryImage->gallery_image = $galleryImageName;
        //         $productGalleryImage->imageUrl = $imageUrl;
        //         $productGalleryImage->save();
        //     }
        // }

        if ($request->gallery_image) {
            $imageGallery = $request->gallery_image;
            ProductImage::where('product_id', $productUpdate->id)->delete();

            foreach ($imageGallery as $image) {
                // Generate a unique file name
                $galleryImageName = rand().$request->name.'.'.$image->extension();

                // Move the uploaded image to the desired directory
                $image->move('galleryImage', $galleryImageName);

                // Generate the image URL
                $imageUrl = url('galleryImage/' . $galleryImageName);

                // Save the image information to the database
                $productGalleryImage = new ProductImage();
                $productGalleryImage->product_id = $productUpdate->id;
                $productGalleryImage->gallery_image = $galleryImageName;
                $productGalleryImage->imageUrl = $imageUrl;
                $productGalleryImage->save();
            }
        }

        // Product color
            if(!empty($productUpdate)){
                if ($request->filled('color')){
                    $colors = $request->color;
                    ProductColor::where('product_id', $productUpdate->id)->delete();
                    foreach ($colors as $key => $color){
                        $colorName = new ProductColor();
                        $colorName->product_id = $productUpdate->id;
                        $colorName->color = $request->color[$key];
                        $colorName->save();
                    }
                }
            }
        // Product size
        if ($request->has('size')){
            if(!empty($productUpdate)){
                $sizes = $request->size;
                ProductSize::where('product_id', $productUpdate->id)->delete();
                foreach ($sizes as $key => $size){
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $productUpdate->id;
                    $sizeName->size = $request->size[$key];
                    $sizeName->save();
                }
            }
        }

        // Related product
        if(!empty($product)){
            $relatedProducts = $request->related_product_id;
            RelatedProduct::where('product_id', $productUpdate->id)->delete();
            foreach ($relatedProducts as $key => $related){
                $relatedProduct = new RelatedProduct();
                $relatedProduct->product_id = $product->id;
                $relatedProduct->related_product_id = $request->related_product_id[$key];
                $relatedProduct->save();
            }
        }

        return redirect()->route('products.index')->with('success', 'Product has been successfully updated.');
    }

    public function updateVariableProduct (Request $request, $id)
    {
        if($request->type){
            //dd($request->type);
            // $product = new PageProduct();
            $product = new Product();
            $page= AddPage::find($request->type);
            // $product->type = Str::slug($page->name);
            $product->page_name = Str::slug($page->name);
            $product->is_page_product = 1;
        }
        else{
            $product = Product::where('id', $id)->with('productImages')->first();
            $product->seo_title = $request->seo_title;
            $product->seo_description = $request->seo_description;
            $product->seo_keyword = $request->seo_keyword;
        }

        if(isset($request->image)){
            $image = $request->file('image');
            $input['image'] = rand().'pro_main'.$request->name.'.'.'webp';
            $destinationPath = 'product/images';
            $imgFile = Image::make($image->getRealPath());
            $imgFile->resize(240, 240, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90)->save($destinationPath.'/'.$input['image']);
            $image->move($destinationPath, $input['image']);
            $imageUrl = url($destinationPath.'/'.$input['image']);
            $product->image = $input['image'];
            $product->imageUrl = $imageUrl;
        }

        if ($request->has('priority')) {
            // Only run the check if the new priority is different from the existing one
            if ($product->priority != $request->priority) {
                $checkPriority = Product::where('priority', $request->priority)
                    ->where('id', '!=', $product->id)
                    ->first();

                if ($checkPriority) {
                    return redirect()->back()->with('error', 'Priority already exists!');
                }

                $product->priority = $request->priority;
            }
        }

        $product->rating = $request->rating;
        $product->drive_link = $request->drive_link;
        $product->name = $request->name;
        $product->slug = str_replace(' ', '-', strtolower($request->name));
        $product->cat_id = $request->cat_id;
        $product->sub_cat_id = $request->sub_cat_id;
        $product->qty = $request->qty;
        $product->buy_price = $request->buy_price;
        $product->wholesale_price = $request->wholesale_price;
        $product->regular_price = $request->regular_price;
        if ($request->discount_price){
            $product->discount_price = $request->discount_price;
        }
        $product->product_code = $request->product_code;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->policy = $request->policy;
        $product->product_type = $request->product_type;
        $product->save();

        if(!empty($product)){
            if($request->gallery_image){
                //Delete Previous Image...
                $prevousImage = ProductImage::where('product_id', $id)->get();
                if(!empty($prevousImage)){
                    foreach($prevousImage as $image){
                        $image->delete();
                    }
                }
                //Delete Previous Image...
                $galleryImages = $request->file('gallery_image');
                $wholesalePrices = $request->input('wholesale_price_variable');
                $prices = $request->input('price');
                $colors = $request->input('color');
                $sizes = $request->input('size');
                // }

                foreach ($galleryImages as $index => $image) {
                    // Generate a unique file name
                    $galleryImageName = rand().$request->name.'.'.$image->extension();

                    // Move the uploaded image to the desired directory
                    $image->move('galleryImage', $galleryImageName);

                    // Generate the image URL
                    $imageUrl = url('galleryImage/' . $galleryImageName);

                    // Create a new ProductImage record with additional attributes
                    $productGalleryImage = new ProductImage();
                    $productGalleryImage->product_id = $product->id; // assuming $product is available
                    $productGalleryImage->gallery_image = $galleryImageName;
                    $productGalleryImage->price = $prices[$index];
                    $productGalleryImage->wholesale_price = $wholesalePrices[$index];
                    $productGalleryImage->color = $colors[$index];
                    $productGalleryImage->size = $sizes[$index];
                    $productGalleryImage->imageUrl = $imageUrl;
                    $productGalleryImage->save();
                }
            }
        }

        // Product color
        if($request->filled('color')){
            $colors = $request->color;
            if (is_array($colors) || is_object($colors)){
                foreach ($colors as $key => $color){
                    $colorName = new ProductColor();
                    $colorName->product_id = $product->id;
                    $colorName->color = $color;
                    $colorName->save();
                }
            }
        }
        // Product size
        if ($request->filled('size')) {
            $sizes = $request->input('size');
            if (is_array($sizes) || is_object($sizes)) {
                foreach ($sizes as $size) {
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $product->id;
                    $sizeName->size = $size;
                    $sizeName->save();
                }
            }
        }
        // Related product
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
            if (is_array($relatedProducts || is_object($relatedProducts))){
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new RelatedProduct();
                    $relatedProduct->product_id = $product->id;
                    $relatedProduct->related_product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
            }
        }
        if($request->type){
            return redirect()->route('page.products.index')->with('success', 'Product has been successfully created.');
        }
        return redirect()->route('products.index')->with('success', 'Product has been successfully created.');
    }

    public function active($id)
    {
        $this->product->active($id);
        return redirect()->back()->with('success', 'Product has been successfully Inactivated.');
    }

    public function inactive($id)
    {
        $this->product->inactive($id);
        return redirect()->back()->with('success', 'Product has been successfully Actived.');
    }

    public function delete($id)
    {
        $this->product->delete($id);
        return redirect()->back()->with('success', 'Product has been successfully deleted.');
    }

    public function pageProductcreate ()
    {
        return view('admin.page_products.create', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'pages' => AddPage::orderBy('created_at', 'desc')->get()
        ]);
    }

    public function pageProductEdit ($id, $slug)
    {
        // return view('admin.page_products.edit', [
        //     'categories' => Category::orderBy('created_at', 'desc')->get(),
        //     'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
        //     'brands' => Brand::orderBy('created_at', 'desc')->get(),
        //     'product' => $this->page_product->edit($id)
        // ]);
        $categories = Category::orderBy('created_at', 'desc')->get();
        $subcategories = Subcategory::orderBy('created_at', 'desc')->get();
        $brands = Brand::orderBy('created_at', 'desc')->get();
        $product = Product::find($id);

        //dd($product);

        return view('admin.page_products.edit', compact('categories', 'subcategories', 'brands', 'product'));

    }

    public function pageProductUpdate (Request $request, $id)
    {
        // $productUpdate = PageProduct::find($id);
        $productUpdate = Product::find($id);

        $imageUpdate = $request->file('image');
        if (isset($imageUpdate)){
            if ($imageUpdate && file_exists(('product/images/').$productUpdate['image'])){
                unlink('product/images/'.$productUpdate->image);
            }

            $updateImageName['image'] = rand().'pro_main'.$request->name.'.'.'webp';
            $updateDestinationPath = 'product/images';

            $imgFile = Image::make($imageUpdate->getRealPath());

            $imgFile->resize(240, 240, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90)->save($updateDestinationPath.'/'.$updateImageName['image']);
            $imageUpdate->move($updateDestinationPath, $updateImageName['image']);
            $productUpdate->image = $updateImageName['image'];
        }

        $productUpdate->name = $request->name;
        $productUpdate->drive_link = $request->drive_link;
        $productUpdate->slug = str_replace(' ', '-', strtolower($request->name));
        $productUpdate->cat_id = $request->cat_id;
        $productUpdate->sub_cat_id = $request->sub_cat_id;
        $productUpdate->qty = $request->qty;
        $productUpdate->buy_price = $request->buy_price;
        $productUpdate->regular_price = $request->regular_price;
        if ($request->discount_price){
            $productUpdate->discount_price = $request->discount_price;
        }
        $productUpdate->product_code = $request->product_code;
        $productUpdate->short_description = $request->short_description;
        $productUpdate->long_description = $request->long_description;
        $productUpdate->policy = $request->policy;
        $productUpdate->product_type = $request->product_type;
        $productUpdate->save();

        if($request->gallery_image){
            $imageGallery = $request->gallery_image;
            ProductImage::where('product_id', $productUpdate->id)->delete();
            foreach($imageGallery as $image){
                $galleryImageName = rand().$request->name.'.'.$image->extension();
                $imgGallery = Image::make($image->path());
                $imgGallery->resize(440, 440, function ($const) {
                    $const->aspectRatio();
                })->save('galleryImage'. '/'. $galleryImageName);

                $productGalleryImage = new ProductImage();
                $productGalleryImage->product_id = $productUpdate->id;
                $productGalleryImage->gallery_image = $galleryImageName;
                $productGalleryImage->save();
            }
        }

        // Product color
            if(!empty($productUpdate)){
                if ($request->filled('color')){
                    $colors = $request->color;
                    ProductColor::where('product_id', $productUpdate->id)->delete();
                    foreach ($colors as $key => $color){
                        $colorName = new ProductColor();
                        $colorName->product_id = $productUpdate->id;
                        $colorName->color = $request->color[$key];
                        $colorName->save();
                    }
                }
            }
        // Product size
        if ($request->has('size')){
            if(!empty($productUpdate)){
                $sizes = $request->size;
                ProductSize::where('product_id', $productUpdate->id)->delete();
                foreach ($sizes as $key => $size){
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $productUpdate->id;
                    $sizeName->size = $request->size[$key];
                    $sizeName->save();
                }
            }
        }

        // Related product
        if(!empty($product)){
            $relatedProducts = $request->related_product_id;
            RelatedProduct::where('product_id', $productUpdate->id)->delete();
            foreach ($relatedProducts as $key => $related){
                $relatedProduct = new RelatedProduct();
                $relatedProduct->product_id = $product->id;
                $relatedProduct->related_product_id = $request->related_product_id[$key];
                $relatedProduct->save();
            }
        }

        return redirect('/page/products')->with('success', 'Product has been successfully updated.');
    }

    public function duplicate($id)
    {
        $product = Product::findOrFail($id);

        // Duplicate main product
        $newProduct = $product->replicate(); // Clone all fields except ID
        $newProduct->name = $product->name . ' (Copy)';
        $newProduct->slug = Str::slug($newProduct->name . '-' . time());
        $newProduct->priority = $product->priority + 1;
        $newProduct->save();

        // === Duplicate Product Images ===
        $productImages = ProductImage::where('product_id', $product->id)->get();
        foreach ($productImages as $img) {
            $newImage = new ProductImage();
            $newImage->product_id = $newProduct->id;
            $newImage->gallery_image = $img->gallery_image; // Optionally re-copy the image if needed
            $newImage->save();
        }

        // === Duplicate Product Colors ===
        $productColors = ProductColor::where('product_id', $product->id)->get();
        foreach ($productColors as $color) {
            $newColor = new ProductColor();
            $newColor->product_id = $newProduct->id;
            $newColor->color = $color->color;
            $newColor->save();
        }

        // === Duplicate Product Sizes ===
        $productSizes = ProductSize::where('product_id', $product->id)->get();
        foreach ($productSizes as $size) {
            $newSize = new ProductSize();
            $newSize->product_id = $newProduct->id;
            $newSize->size = $size->size;
            $newSize->save();
        }

        // === Duplicate Related Products ===
        $relatedProducts = RelatedProduct::where('product_id', $product->id)->get();
        foreach ($relatedProducts as $related) {
            $newRelated = new RelatedProduct();
            $newRelated->product_id = $newProduct->id;
            $newRelated->related_product_id = $related->related_product_id;
            $newRelated->save();
        }

        return redirect()->back()->with('success', 'Product duplicated successfully');
    }

    public function productQtyUpdate(Request $request, $id)
    {
        $productQty = Product::find($id);
        $productQty->qty = $request->qty;
        $productQty->save();
        return $productQty;
    }

    public function topProductList ()
    {
        $products = TopProducts::orderBy('created_at', 'desc')->with('product')->get();
        return view('admin.products.top-list', compact('products'));
    }

    public function topProductCreate ()
    {
        return view('admin.products.create-top');
    }

    public function topProductStore (Request $request)
    {
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new TopProducts();
                    $relatedProduct->product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
                return redirect('/top/products/list')->with('success', 'Created Successfully!!');
        }
        return redirect('/top/products/list')->with('error', 'Select a product!');
    }

    public function topProductDelete ($id)
    {
        $product = TopProducts::find($id);
        $product->delete();
        return redirect()->back();
    }

    public function galleryImageDelete ($id)
    {
        $galleryImage = ProductImage::find($id);

        if ($galleryImage->gallery_image && file_exists(('galleryImage/').$galleryImage['gallery_image'])){
            unlink('galleryImage/'.$galleryImage->gallery_image);
        }

        $galleryImage->delete();
        return redirect()->back();
    }

    public function galleryImageEdit ($id)
    {
        $galleryImage = ProductImage::find($id);
        $product = Product::find($galleryImage->product_id);
        $productslug = $product->slug;
        return view ('admin.products.single-gallery', compact('galleryImage', 'productslug', 'product'));
    }

    public function galleryImageUpdate (Request $request, $id)
    {
        $galleryImage = ProductImage::find($id);
        $product = Product::find($galleryImage->product->id);
        $productslug = $product->slug;

        if(isset($request->image)){
            if ($galleryImage->gallery_image && file_exists(('galleryImage/').$galleryImage['gallery_image'])){
                unlink('galleryImage/'.$galleryImage->gallery_image);
            }

            $galleryImageName = rand().$request->name.'.'.$request->image->extension();
                        $imgGallery = Image::make($request->image->path());
                        $imgGallery->resize(440, 440, function ($const) {
                            $const->aspectRatio();
                        })->save('galleryImage'. '/'. $galleryImageName);
                        $imageUrl = url('galleryImage'.'/'.$galleryImageName);

            $galleryImage->gallery_image = $galleryImageName;
            $galleryImage->imageUrl = $imageUrl;
        }
        $galleryImage->wholesale_price = $request->wholesale_price;
        $galleryImage->price = $request->price;
        $galleryImage->color = $request->color;
        $galleryImage->size = $request->size;
        $galleryImage->save();

        if($product->is_variable == true){
            return redirect('/variable-products/edit/' . $galleryImage->product_id . '/' . $productslug);
        }
        return redirect('/products/edit/' . $galleryImage->product_id . '/' . $productslug);
    }

     //Custom Order Products...
     public function storeManualProduct(Request $request)
     {
         // Validate the incoming request
         $request->validate([
             'name' => 'required|string|max:255',
             'price' => 'required|numeric',
             'color' => 'nullable|string|max:255',
             'size' => 'nullable|string|max:255',
             'notes' => 'nullable',
             'description' => 'required|string',
             'image' => 'required|image|max:2048',
         ]);

         // Handle the file upload
         $imageUrl = null;
         $galleryImageUrl = null;

         if ($request->hasFile('image')) {
             $image = $request->file('image');
             $imageName = rand() . '_pro_main_' . str_replace(' ', '_', strtolower($request->name)) . '.' . 'webp';
             $destinationPath = 'product/images'; // Ensure the directory exists

             // Move the image to the product folder
             $image->move($destinationPath, $imageName);
             $imageUrl = url('product/images/' . $imageName);

             // Copy the same image for the gallery before it's moved
             $galleryImageName = rand() . '_gallery_' . str_replace(' ', '_', strtolower($request->name)) . '.' . $image->getClientOriginalExtension();
             $galleryDestinationPath = 'galleryImage';

             // Make sure the original file exists before copying
             if (file_exists($destinationPath . '/' . $imageName)) {
                 copy($destinationPath . '/' . $imageName, $galleryDestinationPath . '/' . $galleryImageName);
                 $galleryImageUrl = url('galleryImage/' . $galleryImageName);
             }
         }

         // Save the product in the database
         $product = new Product();
         $product->temp_id = $request->temp_id;
         $product->is_custom = true;
         $product->name = $request->name;
         $product->slug = str_replace(' ', '-', strtolower($request->name));
         $product->cat_id = 0;
         $product->qty = 100;
         $product->buy_price = $request->price - 200;
         $product->regular_price = $request->price;
         $product->long_description = $request->description;
         $product->policy = "আগে পণ্য দেখে নিন, তারপরে ডেলিভারি ম্যানকে টাকা দিন। ব্যবহার করা পণ্য ফেরতযোগ্য নয়।";
         $product->product_type = "feature";
         $product->image = $imageName;
         $product->imageUrl = $imageUrl;
        //  $product->rating = 5;
         $product->save();

         // Save the gallery image
         if (!empty($product) && $galleryImageUrl) {
             $productGalleryImage = new ProductImage();
             $productGalleryImage->product_id = $product->id;
             $productGalleryImage->gallery_image = $galleryImageName;
             $productGalleryImage->imageUrl = $galleryImageUrl;
             $productGalleryImage->save();
         }

         // Save Product Color
         if ($request->filled('color')) {
             $colorName = new ProductColor();
             $colorName->product_id = $product->id;
             $colorName->color = $request->color;
             $colorName->save();
         }

         // Save Product Size
         if ($request->filled('size')) {
             $sizeName = new ProductSize();
             $sizeName->product_id = $product->id;
             $sizeName->size = $request->size;
             $sizeName->save();
         }

         // Return response to the frontend
         return response()->json(['success' => true, 'product' => $product], 200);
     }

     public function storeCustomOrder(Request $request)
     {
         // Validate request data
         $request->validate([
             'customer_name' => 'required|string',
             'customer_phone' => 'required|string',
             'customer_area' => 'required|string',
             'customer_address' => 'required|string',
             'cart' => 'required|array',
         ]);

          //\Log::info('Cart Data:', ['cart' => $request->cart]);


         // Check the product is dropshipping...
         $firstProductId = $request->cart[0]['id'] ?? null;
         if (!$firstProductId) {
             return response()->json(['error' => 'No products are chosen!'], 400);
         }

         $product = Product::where('id', $firstProductId)->orWhereRaw("CAST(temp_id AS CHAR) LIKE ?", ['%' . $firstProductId . '%'])->orderBy('created_at', 'desc')->first();
         if (!$product) {
             return response()->json(['error' => 'Product not found!'], 404);
         }

         // Get total quantity and total cost
         $totalQty = count($request->cart);  // Use count() for arrays
         $totalCost = array_sum(array_column($request->cart, 'price')) + $request->customerArea;

         $order = new Order();
         $order->is_dropshipping = $product->b_product_id != null;
         $order->name = $request->customer_name;
         $order->phone = $request->customer_phone;
         $order->email = $request->email;
         $order->area = $request->customer_area;
         $order->district_id = $request->district_id;
         $order->sub_district_id = $request->sub_district_id;
         $order->address = $request->customer_address;
         $order->orderId = $order->invoiceNumber();
         $order->price = $totalCost;
         $order->qty = $totalQty;
         $order->payment_type = "cod";
         $order->order_type = "Manual";

         $customerCheck = Order::where('phone', $request->customer_phone)->first();
         $order->customer_type = $customerCheck ? 'Old Customer' : 'New Customer';

         // Assign to employee
         $session_user = session('id');
         if ($session_user != null) {
             $order->employee_id = $session_user;
         }
         else{
             $order->employee_id = 1;
         }

         $order->save();

         // Save Order Details
         foreach ($request->cart as $cartItem) {
             $productValidity = Product::where('id', $cartItem['id'])
                                ->orWhere('temp_id', $cartItem['id'])
                                ->orderBy('created_at', 'desc')
                                ->first();

             if (!$productValidity) {
                 return response()->json(['error' => 'Product not found!'], 404);
             }

             OrderDetails::create([
                 'order_id' => $order->id,
                 'product_id' => $productValidity->id,
                 'qty' => 1,
                 'price' => $cartItem['price'],
                 'size' => $cartItem['size'] ?? null,
                 'color' => $cartItem['color'] ?? null,
                 'notes' => $cartItem['notes'] ?? null,
             ]);
         }
         // Update product quantity
         foreach ($request->cart as $cartItem) {
             $product = Product::where('id', $cartItem['id'])->orWhere('temp_id', $cartItem['id'])->orderBy('created_at', 'desc')->first();
             if ($product) {
                 $product->qty -= 1;
                 $product->save();
             }
         }

         return response()->json(['success' => true, 'message' => 'Order successfully submitted!', 'order_id' => $order->orderId]);
     }
}
