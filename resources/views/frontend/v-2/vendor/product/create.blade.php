@extends('frontend.v-2.vendor.master')

@section('content')
    <!-- Page header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Add Product</h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Page header -->

    <!-- Page Content -->
    <section class="content">
        <div class="container-fluid">
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
                                <h5 class="mb-3">Add New Product</h5>
                            </div>
                        </div>

                        <form action="{{ url('/vendor/product/store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Priority</label>
                                                <input type="text" name="priority" value="{{ old('priority') }}" class="form-control" placeholder="Product priority"><br>
                                                <span style="color: red"> {{ $errors->has('priority') ? $errors->first('priority') : ' ' }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Name <small style="color: red; font-size: 18px;">*</small></label>
                                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Product name"><br>
                                                <span style="color: red"> {{ $errors->has('name') ? $errors->first('name') : ' ' }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Category Name <small style="color: red; font-size: 18px;">*</small></label>
                                                <select class="form-control" name="cat_id" id="cat_id" onchange="categoryWiseSubcategory(this.value)">
                                                    <option selected disabled>Select a category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span style="color: red"> {{ $errors->has('cat_id') ? $errors->first('cat_id') : ' ' }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Qty <small style="color: red; font-size: 18px;">*</small></label>
                                                <input type="number" name="qty" value="{{ old('qty') }}" class="form-control" placeholder="Product qty"><br>
                                                <span style="color: red"> {{ $errors->has('qty') ? $errors->first('qty') : ' ' }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Buy Price</label>
                                                <input type="number" name="buy_price" value="{{ old('buy_price') }}" class="form-control" placeholder="Product buy price">
                                                <span style="color: red"> {{ $errors->has('buy_price') ? $errors->first('buy_price') : ' ' }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Sale Price <small style="color: red; font-size: 18px;">*</small></label>
                                                <input type="number" name="regular_price" value="{{ old('regular_price') }}" class="form-control" placeholder="Product regular price"><br>
                                                <span style="color: red"> {{ $errors->has('regular_price') ? $errors->first('regular_price') : ' ' }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Discount Price (Optional)</label>
                                                <input type="text" name="discount_price" value="{{ old('discount_price') }}"
                                                class="form-control" placeholder="Product discount price"><br>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Code ( Optional )</label>
                                                <input type="text" name="product_code " value="{{ old('product_code ') }}" class="form-control" placeholder="Product code"><br>
                                                <span style="color: red"> {{ $errors->has('product_code ') ? $errors->first('product_code ') : ' ' }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Main image <small style="color: red; font-size: 18px;">*</small></label>
                                                <input type="file" name="image" id="image" class="form-control"><br>
                                                <span style="color: red"> {{ $errors->has('image') ? $errors->first('image') : ' ' }}</span>
                                            </div>
                                            <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Gallery image <small style="color: red; font-size: 18px;">*</small></label>
                                            <div class="input-group mb-3">
                                                <input type="file" name="gallery_image[]" id="gallery_image" class="form-control">
                                                <button class="btn btn-sm btn-primary" type="button" id="addMore">
                                                    Add More
                                                </button>
                                            </div>
                                            <span style="color: red"> {{ $errors->has('gallery_image') ? $errors->first('gallery_image') : ' ' }}</span>
                                            <div id="newRow"></div>

                                            <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Size ( Optional )</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="size[]" id="size" class="form-control" placeholder="Product size">
                                                <span style="color: red"> {{ $errors->has('size') ? $errors->first('size') : ' ' }}</span>
                                                <button class="btn btn-sm btn-success" type="button" id="addMoreSize">
                                                    Add More
                                                </button>
                                            </div>
                                            <div id="newRowForSize"></div>

                                            <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Color ( Optional )</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="color[]" id="color" class="form-control" placeholder="Product color">
                                                <span style="color: red"> {{ $errors->has('color') ? $errors->first('color') : ' ' }}</span>
                                                <button class="btn btn-sm btn-info" type="button" id="addMoreColor">
                                                    Add More
                                                </button>
                                            </div>
                                            <div id="newRowForColor"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Short description</label>
                                            <textarea class="form-control" rows="5" name="short_description" placeholder="Enter product short description">{{ old('short_description') }}</textarea><br>
                                            <span style="color: red"> {{ $errors->has('short_description') ? $errors->first('short_description') : ' ' }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Long description <small style="color: red; font-size: 18px;">*</small></label>
                                            <textarea class="ckeditor" name="long_description">{{ old('long_description') }}</textarea><br>
                                            <span style="color: red"> {{ $errors->has('long_description') ? $errors->first('long_description') : ' ' }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product Policy <small style="color: red; font-size: 18px;"></small></label>
                                            <textarea class="ckeditor" name="policy">{{ old('policy') }}</textarea><br>
                                            <span style="color: red"> {{ $errors->has('policy') ? $errors->first('policy') : ' ' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Product type <small style="color: red; font-size: 18px;">*</small></label>
                                            <select class="form-control" name="product_type" id="product_type">
                                                <option selected disabled>Select a product type</option>
                                                <option value="feature">Regular Product</option>
                                                <option value="hot">Hot Product</option>
                                                <option value="discount">Discount Product</option>
                                                <option value="new">New Arrival Product</option>
                                            </select><br>
                                            <span style="color: red"> {{ $errors->has('product_type') ? $errors->first('product_type') : ' ' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success mt-2 float-right">Submit</button>
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Page Content -->
@endsection

@push('script')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });
    </script>
    <script>
        $('#addMore').click(function(){
            let html = '';
            html+='<div class="input-group mb-3" id="removeRow">'
                html+='<input type="file" name="gallery_image[]" id="gallery_image" class="form-control">'
                html+='<button class="btn btn-sm btn-danger" type="button" id="remove">'
                    html+='Remove'
                html+='</button>'
            html+='</div>'

            $('#newRow').append(html);
        });

        // remove row
        $(document).on('click', '#remove', function () {
            $(this).closest('#removeRow').remove();
        });

        $('#addMoreSize').click(function(){
            let html = '';
            html+='<div class="input-group mb-3" id="removeSizeRow">'
                html+='<input type="text" name="size[]" id="size" class="form-control" placeholder="Product size">'
                html+='<button class="btn btn-sm btn-danger" type="button" id="removeSize">'
                    html+='Remove'
                html+='</button>'
            html+='</div>'

            $('#newRowForSize').append(html);
        });

        // remove row
        $(document).on('click', '#removeSize', function () {
            $(this).closest('#removeSizeRow').remove();
        });

        $('#addMoreColor').click(function(){
            let html = '';
            html+='<div class="input-group mb-3" id="removeColorRow">'
                html+='<input type="text" name="color[]" id="color" class="form-control" placeholder="Product color">'
                html+='<button class="btn btn-sm btn-danger" type="button" id="removeColor">'
                    html+='Remove'
                html+='</button>'
            html+='</div>'

            $('#newRowForColor').append(html);
        });

        // remove row
        $(document).on('click', '#removeColor', function () {
            $(this).closest('#removeColorRow').remove();
        });
    </script>
@endpush