@extends('admin.master')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col">
                    <div class="card radius-10 mb-0">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="d-flex align-items-center">
                                <div>
                                    <h5 class="mb-3">Edit Product</h5>
                                </div>
                                <div class="ms-auto">
                                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Products</a>
                                </div>
                            </div>

                            <form id="productForm" action="{{ route('unified.products.update', $product->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Type <small style="color: red; font-size: 18px;">*</small></label>
                                                        <select class="form-control" name="product_category" id="product_category" required>
                                                            <option value="">Select Product Type</option>
                                                            <option value="single" {{ !$product->is_variable ? 'selected' : '' }}>Single Product</option>
                                                            <option value="variable" {{ $product->is_variable ? 'selected' : '' }}>Variable Product</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Priority</label>
                                                        <input type="text" name="priority" value="{{ $product->priority }}" class="form-control" placeholder="Product priority"><br>
                                                        <span style="color: red"> {{ $errors->has('priority') ? $errors->first('priority') : ' ' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Name <small style="color: red; font-size: 18px;">*</small></label>
                                                        <input type="text" name="name" value="{{ $product->name }}" class="form-control" placeholder="Product name"><br>
                                                        <span style="color: red"> {{ $errors->has('name') ? $errors->first('name') : ' ' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Category Name <small style="color: red; font-size: 18px;">*</small></label>
                                                        <select class="form-control" name="cat_id" id="cat_id" onchange="categoryWiseSubcategory(this.value)">
                                                            <option selected disabled>Select a category</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}" {{ $category->id == $product->cat_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span style="color: red"> {{ $errors->has('cat_id') ? $errors->first('cat_id') : ' ' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Subcategory Name</label>
                                                        <select class="form-control" name="sub_cat_id" id="sub_cat_id">
                                                            <option selected disabled>Select a Subcategory</option>
                                                            @foreach ($subcategories as $subcategory)
                                                                <option value="{{ $subcategory->id }}" {{ $subcategory->id == $product->sub_cat_id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span style="color: red"> {{ $errors->has('sub_cat_id') ? $errors->first('sub_cat_id') : ' ' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Qty <small style="color: red; font-size: 18px;">*</small></label>
                                                        <input type="number" name="qty" value="{{ $product->qty }}" class="form-control" placeholder="Product qty"><br>
                                                        <span style="color: red"> {{ $errors->has('qty') ? $errors->first('qty') : ' ' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Wholesale Price <small style="color: red; font-size: 18px;">*</small></label>
                                                        <input type="number" name="wholesale_price" value="{{ $product->wholesale_price }}" class="form-control" placeholder="Product Wholesale price" required>
                                                        <span style="color: red"> {{ $errors->has('wholesale_price') ? $errors->first('wholesale_price') : ' ' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Common Price <small style="color: red; font-size: 18px;">*</small></label>
                                                        <input type="number" name="regular_price" value="{{ $product->regular_price }}" class="form-control" placeholder="Product regular price"><br>
                                                        <span style="color: red"> {{ $errors->has('regular_price') ? $errors->first('regular_price') : ' ' }}</span>
                                                    </div>
                                                    {{-- <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Discount Price (Optional)</label>
                                                        <input type="text" name="discount_price" value="{{ $product->discount_price }}"
                                                        class="form-control" placeholder="Product discount price"><br>
                                                    </div> --}}
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Code ( Optional )</label>
                                                        <input type="text" name="product_code" value="{{ $product->product_code }}" class="form-control" placeholder="Product code"><br>
                                                        <span style="color: red"> {{ $errors->has('product_code') ? $errors->first('product_code') : ' ' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Rating ( 1 to 5 )</label>
                                                        <input type="number" name="rating" value="{{ $product->rating }}" class="form-control" placeholder="Product rating"><br>
                                                        <span style="color: red"> {{ $errors->has('rating') ? $errors->first('rating') : ' ' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Main image <small style="color: red; font-size: 18px;">*</small></label>
                                                        <input type="file" name="image" id="image" class="form-control"><br>
                                                        <span style="color: red"> {{ $errors->has('image') ? $errors->first('image') : ' ' }}</span>
                                                        @if($product->image)
                                                            <div class="mt-2">
                                                                <img src="{{ asset('product/images/' . $product->image) }}" alt="Current Image" width="100">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Single Product Fields -->
                                                    <div id="singleProductFields" style="display: {{ !$product->is_variable ? 'block' : 'none' }};">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Gallery Images</label>
                                                        <div class="input-group mb-3">
                                                            <input type="file" name="gallery_image[]" class="form-control">
                                                            <button class="btn btn-sm btn-primary add-gallery-btn" type="button">
                                                                <i class="bx bx-plus-circle" aria-hidden="true" style="margin-left: 7px;"></i>
                                                            </button>
                                                        </div>
                                                        <div id="galleryImagesContainer"></div>
                                                        
                                                        <!-- Existing Gallery Images for Single Product -->
                                                        @if(!$product->is_variable && $product->productImages->count() > 0)
                                                            @foreach($product->productImages as $image)
                                                                <div class="input-group mb-3 existing-image-row">
                                                                    <input type="hidden" name="existing_gallery_image_id[]" value="{{ $image->id }}">
                                                                    <input type="file" name="existing_gallery_image[]" class="form-control">
                                                                    @if($image->gallery_image)
                                                                        <div class="mt-2">
                                                                            <img src="{{ asset('galleryImage/' . $image->gallery_image) }}" alt="Gallery Image" width="50">
                                                                        </div>
                                                                    @endif
                                                                    <button class="btn btn-sm btn-danger remove-existing-image" type="button">
                                                                        <i class="bx bx-trash" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                        
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Sizes (Optional)</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" name="size[]" class="form-control" placeholder="Product size">
                                                            <button class="btn btn-sm btn-success add-size-btn" type="button">
                                                                <i class="bx bx-plus-circle" aria-hidden="true" style="margin-left: 7px;"></i>
                                                            </button>
                                                        </div>
                                                        <div id="sizesContainer"></div>
                                                        
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Colors (Optional)</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" name="color[]" class="form-control" placeholder="Product color">
                                                            <button class="btn btn-sm btn-info add-color-btn" type="button">
                                                                <i class="bx bx-plus-circle" aria-hidden="true" style="margin-left: 7px;"></i>
                                                            </button>
                                                        </div>
                                                        <div id="colorsContainer"></div>
                                                    </div>
                                                    
                                                    <!-- Variable Product Fields -->
                                                    <div id="variableProductFields" style="display: {{ $product->is_variable ? 'block' : 'none' }};">
                                                        <label style="padding-bottom: 5px; font-weight: 600; font-size: 15px; letter-spacing: 1px;">
                                                            Gallery Image, Price, Color, and Size
                                                            <small style="color: red; font-size: 18px;">*</small>
                                                        </label>

                                                        <!-- Existing Variations -->
                                                        @if($product->is_variable && $product->productImages->count() > 0)
                                                            @foreach($product->productImages as $index => $image)
                                                                <div class="row g-2 align-items-center mb-3 existing-variation-row">
                                                                    <!-- Hidden ID -->
                                                                    <input type="hidden" name="existing_gallery_image_id[]" value="{{ $image->id }}">
                                                                    
                                                                    <!-- Gallery Image -->
                                                                    <div class="col-md-3">
                                                                        <input type="file" name="existing_gallery_image[]" class="form-control">
                                                                        @if($image->gallery_image)
                                                                            <div class="mt-2">
                                                                                <img src="{{ asset('galleryImage/' . $image->gallery_image) }}" alt="Gallery Image" width="50">
                                                                            </div>
                                                                        @endif
                                                                    </div>

                                                                    <!-- Wholesale Price -->
                                                                    <div class="col-md-2">
                                                                        <input type="number" name="existing_wholesale_price_variable[]" class="form-control" placeholder="Wholesale Price" value="{{ $image->wholesale_price }}">
                                                                    </div>

                                                                    <!-- Retail Price -->
                                                                    <div class="col-md-2">
                                                                        <input type="number" name="existing_price[]" class="form-control" placeholder="Price" value="{{ $image->price }}">
                                                                    </div>

                                                                    <!-- Color -->
                                                                    <div class="col-md-2">
                                                                        <input type="text" name="existing_color[]" class="form-control" placeholder="Color" value="{{ $image->color }}">
                                                                    </div>

                                                                    <!-- Size -->
                                                                    <div class="col-md-2">
                                                                        <input type="text" name="existing_size[]" class="form-control" placeholder="Size" value="{{ $image->size }}">
                                                                    </div>

                                                                    <!-- Remove Button -->
                                                                    <div class="col-md-1">
                                                                        <button class="btn btn-sm btn-danger remove-existing-variation" type="button">
                                                                            <i class="bx bx-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif

                                                        <!-- New Variations -->
                                                        <div class="row g-2 align-items-center mb-3">
                                                            <!-- Gallery Image -->
                                                            <div class="col-md-3">
                                                                <input type="file" name="gallery_image[]" class="form-control">
                                                            </div>

                                                            <!-- Wholesale Price -->
                                                            <div class="col-md-2">
                                                                <input type="number" name="wholesale_price_variable[]" class="form-control" placeholder="Wholesale Price">
                                                            </div>

                                                            <!-- Retail Price -->
                                                            <div class="col-md-2">
                                                                <input type="number" name="price[]" class="form-control" placeholder="Price">
                                                            </div>

                                                            <!-- Color -->
                                                            <div class="col-md-2">
                                                                <input type="text" name="color[]" class="form-control" placeholder="Product Color">
                                                            </div>

                                                            <!-- Size -->
                                                            <div class="col-md-2">
                                                                <input type="text" name="size[]" class="form-control" placeholder="Product Size">
                                                            </div>

                                                            <!-- Add More Button -->
                                                            <div class="col-md-1">
                                                                <button class="btn btn-sm btn-primary" type="button" id="addMore">
                                                                    <i class="bx bx-plus-circle" style="margin-left: 3px;"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <span style="color: red"> {{ $errors->has('gallery_image') ? $errors->first('gallery_image') : ' ' }}</span>
                                                        <span style="color: red"> {{ $errors->has('wholesale_price_variable') ? $errors->first('wholesale_price_variable') : ' ' }}</span>
                                                        <span style="color: red"> {{ $errors->has('price') ? $errors->first('price') : ' ' }}</span>
                                                        <span style="color: red"> {{ $errors->has('color') ? $errors->first('color') : ' ' }}</span>
                                                        <span style="color: red"> {{ $errors->has('size') ? $errors->first('size') : ' ' }}</span>
                                                        <div id="newRow"></div>
                                                    </div>

                                                    <div id="newRowForColor"></div>

                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Related Product ( Optional )</label>
                                                    <select class="multiple-related-product form-control mb-3" name="related_product_id[]" multiple="multiple">
                                                      <option value="AL">Select A Related Product</option>
                                                        @foreach(\App\Models\Product::orderBy('created_at', 'desc')->get() as $relatedproduct)
                                                            <option value="{{ $relatedproduct->id }}" 
                                                                @if($product->comboProducts->contains('related_product_id', $relatedproduct->id)) selected @endif>
                                                                {{ $relatedproduct->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <div class="form-group mt-5">
                                                        <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Vendor (Optional)</label>
                                                        <select class="form-control" name="vendor_id" id="vendor_id">
                                                            <option selected disabled>Select a Vendor</option>
                                                            @foreach ($vendors as $vendor)
                                                                <option value="{{ $vendor->id }}" {{ $vendor->id == $product->vendor_id ? 'selected' : '' }}>{{ $vendor->shop_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span style="color: red"> {{ $errors->has('vendor_id') ? $errors->first('vendor_id') : ' ' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Short description</label>
                                                    <textarea class="form-control" rows="5" name="short_description" placeholder="Enter product short description">{{ $product->short_description }}</textarea><br>
                                                    <span style="color: red"> {{ $errors->has('short_description') ? $errors->first('short_description') : ' ' }}</span>
                                                </div>
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Long description <small style="color: red; font-size: 18px;">*</small></label>
                                                    <textarea class="ckeditor" name="long_description">{{ $product->long_description }}</textarea><br>
                                                    <span style="color: red"> {{ $errors->has('long_description') ? $errors->first('long_description') : ' ' }}</span>
                                                </div>
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Policy <small style="color: red; font-size: 18px;"></small></label>
                                                    <textarea class="ckeditor" name="policy">{{ $product->policy }}</textarea><br>
                                                    <span style="color: red"> {{ $errors->has('policy') ? $errors->first('policy') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product type <small style="color: red; font-size: 18px;">*</small></label>
                                                    <select class="form-control" name="product_type" id="product_type">
                                                        <option selected disabled>Select a product type</option>
                                                        <option value="feature" {{ $product->product_type == 'feature' ? 'selected' : '' }}>Regular Product</option>
                                                        <option value="hot" {{ $product->product_type == 'hot' ? 'selected' : '' }}>Hot Product</option>
                                                        <option value="discount" {{ $product->product_type == 'discount' ? 'selected' : '' }}>Discount Product</option>
                                                        <option value="new" {{ $product->product_type == 'new' ? 'selected' : '' }}>New Arrival Product</option>
                                                    </select><br>
                                                    <span style="color: red"> {{ $errors->has('product_type') ? $errors->first('product_type') : ' ' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 additional-info-form">
                                            <div class="additional-info-wrapper">
                                                <div class="additional-info-title">
                                                    <h6 class="info-title">
                                                        SEO Information
                                                    </h6>
                                                </div>
                                                <hr>
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">SEO Title ( Optional )</label><br>
                                                <input type="text" name="seo_title" class="form-control" value="{{ $product->seo_title }}" placeholder="Seo title"><br>
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">SEO Description ( Optional )</label><br>
                                                <textarea rows="4" name="seo_description" class="form-control" placeholder="Seo description">{{ $product->seo_description }}</textarea><br>
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">SEO Keyword ( Optional )</label><br>
                                                <select type="text" class="form-control" id="multipleTag" name="seo_keyword" multiple="multiple" value="{{ $product->seo_keyword }}"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 additional-info-form mt-3">
                                            <div class="additional-info-wrapper">
                                                <div class="additional-info-title">
                                                    <h6 class="info-title">
                                                        Product RAW Content
                                                    </h6>
                                                </div>
                                                <hr>
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Google Drive Link ( Optional )</label><br>
                                                <input type="text" class="form-control" name="drive_link" value="{{ $product->drive_link }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success mt-2 float-right">Update Product</button>
                              </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });
        
        // Handle product category change
        $('#product_category').change(function() {
            var productType = $(this).val();
            
            if (productType === 'single') {
                $('#singleProductFields').show();
                $('#variableProductFields').hide();
            } else if (productType === 'variable') {
                $('#singleProductFields').hide();
                $('#variableProductFields').show();
            } else {
                $('#singleProductFields').hide();
                $('#variableProductFields').hide();
            }
        });
        
        // Form submission validation
        $('#productForm').submit(function(e) {
            var productType = $('#product_category').val();
            
            if (!productType) {
                e.preventDefault();
                alert('Please select a product type first.');
                return false;
            }
            
            // For variable products, ensure at least one variation is added
            if (productType === 'variable') {
                var galleryImages = $('input[name="gallery_image[]"]');
                if (galleryImages.length <= 1) { // Only the initial field exists
                    e.preventDefault();
                    alert('Please add at least one product variation for variable products.');
                    return false;
                }
            }
        });
        
        // Add more gallery images for single products
        $(document).on('click', '.add-gallery-btn', function(){
            let html = '';
            html += '<div class="input-group mb-3 remove-gallery-row">'
                html += '<input type="file" name="gallery_image[]" class="form-control">'
                html += '<button class="btn btn-sm btn-danger remove-gallery-btn" type="button">'
                    html += '<i class="bx bx-minus" aria-hidden="true" style="margin-left: 7px;"></i>'
                html += '</button>'
            html += '</div>'
            
            $('#galleryImagesContainer').append(html);
        });
        
        // Remove gallery image row for single products
        $(document).on('click', '.remove-gallery-btn', function () {
            $(this).closest('.remove-gallery-row').remove();
        });
        
        // Add more sizes for single products
        $(document).on('click', '.add-size-btn', function(){
            let html = '';
            html += '<div class="input-group mb-3 remove-size-row">'
                html += '<input type="text" name="size[]" class="form-control" placeholder="Product size">'
                html += '<button class="btn btn-sm btn-danger remove-size-btn" type="button">'
                    html += '<i class="bx bx-minus" aria-hidden="true" style="margin-left: 7px;"></i>'
                html += '</button>'
            html += '</div>'
            
            $('#sizesContainer').append(html);
        });
        
        // Remove size row for single products
        $(document).on('click', '.remove-size-btn', function () {
            $(this).closest('.remove-size-row').remove();
        });
        
        // Add more colors for single products
        $(document).on('click', '.add-color-btn', function(){
            let html = '';
            html += '<div class="input-group mb-3 remove-color-row">'
                html += '<input type="text" name="color[]" class="form-control" placeholder="Product color">'
                html += '<button class="btn btn-sm btn-danger remove-color-btn" type="button">'
                    html += '<i class="bx bx-minus" aria-hidden="true" style="margin-left: 7px;"></i>'
                html += '</button>'
            html += '</div>'
            
            $('#colorsContainer').append(html);
        });
        
        // Remove color row for single products
        $(document).on('click', '.remove-color-btn', function () {
            $(this).closest('.remove-color-row').remove();
        });

        // Variable product add more functionality
        $('#addMore').click(function () {
            let html = `
            <div class="row g-2 align-items-center mb-2 removeRow">
                <div class="col-md-3">
                    <input type="file" name="gallery_image[]" class="form-control">
                </div>
                <div class="col-md-2">
                    <input type="number" name="wholesale_price_variable[]" class="form-control" placeholder="Wholesale Price">
                </div>
                <div class="col-md-2">
                    <input type="number" name="price[]" class="form-control" placeholder="Price">
                </div>
                <div class="col-md-2">
                    <input type="text" name="color[]" class="form-control" placeholder="Color">
                </div>
                <div class="col-md-2">
                    <input type="text" name="size[]" class="form-control" placeholder="Size">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-sm btn-danger remove" type="button">
                        <i class="bx bx-minus"></i>
                    </button>
                </div>
            </div>
        `;

            $('#newRow').append(html);
        });

        // Remove row for variable products
        $(document).on('click', '.remove', function () {
            $(this).closest('.removeRow').remove();
        });
        
        // Remove existing variation
        $(document).on('click', '.remove-existing-variation', function () {
            if(confirm('Are you sure you want to remove this variation?')) {
                $(this).closest('.existing-variation-row').remove();
            }
        });
        
        // Remove existing image
        $(document).on('click', '.remove-existing-image', function () {
            if(confirm('Are you sure you want to remove this image?')) {
                $(this).closest('.existing-image-row').remove();
            }
        });
    </script>
@endpush