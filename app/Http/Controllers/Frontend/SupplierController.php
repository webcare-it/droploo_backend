<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\VendorRequest;
use App\Mail\SupplierForgotMail;
use App\Mail\VendorRegistrationEmail;
use App\Models\AddPage;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\RelatedProduct;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\User;
use App\Repository\Interface\ProductInterface;
use Exception;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class SupplierController extends Controller
{

    protected $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function vendorDeshboard()
    {
        if(auth('supplier')->check()){
            $productCount  = Product::where('vendor_id', auth()->guard('supplier')->id())->count();
            $productOrders = Product::with('orderDetails')->where('vendor_id', auth('supplier')->user()->id)->get();
            $orderCount = 0;
            foreach ($productOrders as $product) {
                $orderCount += $product->orderDetails->count();
            }
            $vendorId = auth('supplier')->user()->id;
            return view('frontend.v-2.vendor.dashboard', compact('vendorId', 'productCount', 'orderCount'));
        }else{
            return redirect('vendor/login/form')->with('error', 'Unauthenticated user');
        }

    }
    public function profileSetting()
    {
        if(auth('supplier')->check()){
            $vendorId = auth('supplier')->user()->id;
            return view('frontend.v-2.vendor.profile-setting', compact('vendorId'));
        }else{
            return redirect('vendor/login/form')->with('error', 'Unauthenticated user');
        }

    }

    public function vendorProductUploadForm()
    {
        $data = [
            'categories'=> Category::orderBy('created_at', 'desc')->get(),
            'brands'=> Brand::orderBy('created_at', 'desc')->get()
        ];
        return view('frontend.v-2.vendor.product.create', compact('data'));
    }

    public function register()
    {
        return view('frontend.v-2.vendor.register');
    }
    public function loginForm()
    {
        return view('frontend.v-2.vendor.login');
    }

    public function store(VendorRequest $request)
    {
        try{
            if($request->file('logo')){
                $avatar = time().'vendor-logo'.'.'. $request->logo->extension();
                $request->logo->move(public_path('avatar'), $avatar);
            }
            $supplier = new Supplier();
            $supplier->first_name = $request->first_name;
            $supplier->last_name = $request->last_name;
            $supplier->logo = $avatar;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->shop_name = $request->shop_name;
            $supplier->address = $request->address;
            $supplier->password = bcrypt($request->password);
            $supplier->save();
            // if($supplier->save()){
            //     Mail::to($supplier->email)->send(new VendorRegistrationEmail($supplier));
            // }

            return redirect()->back()->with('success', 'Your information has been submitted. Please wait for the approval!');
        }catch(Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function vendorLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:8'
        ]);

        $supplierEmail = Supplier::where('email', $request->email)->first();

        if($supplierEmail == null){
            return redirect()->back()->with('error', 'Incorrect email.');
        }

        if($supplierEmail->is_approved == 0){
            return redirect()->back()->with('error', 'Your account is not verified yet.');
        }

        if (Auth::guard('supplier')->attempt(['email' => $request->email, 'password' => $request->password])) {

            return redirect()->intended('/supplier/dashboard')->with('success', 'You are logged In');
        }
        return redirect()->back()->with('error', 'Something is wrong please try again.');
    }

    public function categoryWiseSubcategory($id)
    {
        $subcategories = Subcategory::where('cat_id', $id)->get();
        return response()->json([
            'subcategories' => $subcategories
        ]);
    }

    public function vendorProductUpload(ProductRequest $request)
    {
        $image = $request->file('image');
        $input['image'] = rand().'pro_main'.$request->name.'.'.$image->getClientOriginalExtension();
        $destinationPath = 'product/images';
        $imgFile = Image::make($image->getRealPath());
        $imgFile->resize(240, 240, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$input['image']);
        $image->move($destinationPath, $input['image']);

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
            $checkPriority = Product::where('priority', $request->priority)->where('id','!=', $product->id)->first();
            if($checkPriority == null){
                $product->priority = $request->priority;
            }
            elseif($checkPriority != null){
                $checkPriority->priority = 1000;
                $product->priority = $request->priority;
            }
        }
        $product->vendor_id = auth()->guard('supplier')->id();
        $product->name = $request->name;
        $product->slug = str_replace(' ', '-', strtolower($request->name));
        $product->cat_id = $request->cat_id;
        $product->sub_cat_id = $request->sub_cat_id;
        $product->qty = $request->qty;
        $product->buy_price = $request->buy_price;
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
        $product->save();

        if(!empty($product)){

            if($request->gallery_image){
                $imageGallery = $request->gallery_image;
                foreach($imageGallery as $image){
                    $galleryImageName = rand().$request->name.'.'.$image->extension();
                    $imgGallery = Image::make($image->path());
                    $imgGallery->resize(440, 440, function ($const) {
                        $const->aspectRatio();
                    })->save('galleryImage'. '/'. $galleryImageName);

                    $productGalleryImage = new ProductImage();
                    if($request->type){
                        $productGalleryImage->product_id = $product->id;
                    }
                    else{
                        $productGalleryImage->product_id = $product->id;
                    }
                    $productGalleryImage->gallery_image = $galleryImageName;
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
            return redirect('supplier/product/list')->with('success', 'Product has been successfully created.');
        }
        return redirect('supplier/product/list')->with('success', 'Product has been successfully created.');
    }

    public function products()
    {
        $products = Product::with('category', 'supplier', 'reviews')->where('vendor_id', auth()->guard('supplier')->id())->get();
        return view ('frontend.v-2.vendor.product.list', compact('products'));
    }

    public function productsEdit($id, $slug)
    {
        return view('frontend.v-2.vendor.product.edit', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'product' => $this->product->edit($id)
        ]);
    }

    public function productsUpdate(ProductUpdateRequest $request, $id)
    {
        $productUpdate = Product::find($id);
        $imageUpdate = $request->file('image');
        if (isset($imageUpdate)){
            if ($imageUpdate && file_exists(('product/images/').$productUpdate['image'])){
                unlink('product/images/'.$productUpdate->image);
            }

            $updateImageName['image'] = rand().'pro_main'.$request->name.'.'.$imageUpdate->getClientOriginalExtension();
            $updateDestinationPath = 'product/images';

            $imgFile = Image::make($imageUpdate->getRealPath());

            $imgFile->resize(240, 240, function ($constraint) {
                $constraint->aspectRatio();
            })->save($updateDestinationPath.'/'.$updateImageName['image']);
            $imageUpdate->move($updateDestinationPath, $updateImageName['image']);
            $productUpdate->image = $updateImageName['image'];
        }

        if(isset($request->priority)){
            $checkPriority = Product::where('priority', $request->priority)->where('id','!=', $productUpdate->id)->first();
            if($checkPriority == null){
                $productUpdate->priority = $request->priority;
            }
            elseif($checkPriority != null){
                $checkPriority->priority = 1000;
                $productUpdate->priority = $request->priority;
            }
        }
        $productUpdate->name = $request->name;
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
        $productUpdate->seo_title = $request->seo_title;
        $productUpdate->seo_description = $request->seo_description;
        $productUpdate->seo_keyword = $request->seo_keyword;
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
        return redirect()->back()->with('success', 'Product has been successfully updated.');
    }

    public function productsDelete($id)
    {
        $this->product->delete($id);
        return response()->json(['success' => 'Product has been deleted.']);
    }

    public function vendorOrderProduct()
    {
        $orders = Product::with('orderDetails')->where('vendor_id', auth('supplier')->user()->id)->get();
        $orderDetails = collect();

        foreach ($orders as $product) {
            $orderDetails = $orderDetails->merge($product->orderDetails);
        }
        return view('frontend.v-2.vendor.order.list', compact('orderDetails'));
    }



    //================= Social login =======================//

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function loginWithFacebook()
    {
        try {

            $user = Socialite::driver('facebook')->user();

            $finduser = User::where('social_id', $user->id)->first();

            if($finduser){

                Auth::guard('web')->login($finduser);

                return redirect('/');

            }else{
                $newUser = User::create([
                    'social_id'  => $user->id,
                    'first_name' => $user->getName(),
                    'last_name'  => $user->getName(),
                    'email'      => $user->getEmail(),
                    'avatar'     => $user->getAvatar(),
                    'password'   => bcrypt(12345678)
                ]);

                Auth::guard('web')->login($newUser);

                return redirect()->intended('customer/dashboard');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }


    public function loginWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function loginWithGoogleCallback()
    {
        try {

            $user = Socialite::driver('google')->user();

            $finduser = User::where('social_id', $user->id)->first();

            if($finduser){

                Auth::guard('web')->login($finduser);

                return redirect('/checkout');

            }else{
                $newUser = User::create([
                    'social_id'  => $user->id,
                    'first_name' => $user->getName(),
                    'last_name'  => $user->getName(),
                    'email'      => $user->getEmail(),
                    'avatar'     => $user->getAvatar(),
                    'password'   => bcrypt(12345678)
                ]);

                Auth::guard('web')->login($newUser);

                return redirect('/checkout');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }


    //=====================  Supplier Forgot password =====================//

    public function passwordForgotForm()
    {
        return view('frontend.v-2.vendor.forgot');
    }

    public function passwordForgot(Request $request)
    {
        $this->validate($request, [
            'email' => 'required'
        ]);

        $email = Supplier::where('email', $request->email)->first();
        if($email){
            Mail::to($request->email)->send(new SupplierForgotMail($email));
            return redirect()->back()->withSuccess('Your forgot password link send your email. Please check and set new password.');
        }else{
            return redirect()->back()->withSuccess('Sorry your email did not registered our record.');
        }
    }

    public function passwordResetForm($email)
    {
        return view('frontend.v-2.vendor.password-reset-form', compact('email'));
    }

    public function newPasswordUpdate(Request $request, $email)
    {
        $this->validate($request, [
            'password' => 'required|confirmed|min:8|max:10',
        ]);

        $passwordUpdate = Supplier::where('email', $email)->first();

        $passwordUpdate->password = bcrypt($request->password);
        $passwordUpdate->save();
        return redirect()->route('customer.login.form')->withSuccess('Your password has been updated.');
    }


    //======================= Forgot password =========================//

    public function vendorForgotPasswordForm()
    {
        return view('frontend.v-2.vendor.forgot');
    }

    public function vendorForgotPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required'
        ]);

        $email = Supplier::where('email', $request->email)->first();
        if($email){
            Mail::to($request->email)->send(new SupplierForgotMail($email));
            return redirect()->back()->withSuccess('Your forgot password link send your email. Please check and set new password.');
        }else{
            return redirect()->back()->withSuccess('Sorry your email did not registered our record.');
        }
    }

    public function vendorPasswordResetForm($email)
    {
        return view('frontend.v-2.vendor.password-reset-form', compact('email'));
    }

    public function vendorNewPasswordSet(Request $request, $email)
    {
        $this->validate($request, [
            'password' => 'required|confirmed|min:8|max:10',
        ]);

        $passwordUpdate = Supplier::where('email', $email)->first();

        $passwordUpdate->password = bcrypt($request->password);
        $passwordUpdate->save();
        return redirect()->route('vendor.login.form')->withSuccess('Your password has been updated.');
    }
}
