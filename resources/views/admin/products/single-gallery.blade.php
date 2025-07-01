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
                                    <h5 class="mb-3">Update Gallery Image</h5>
                                </div>
                                <div class="ms-auto">
                                    @if ($product->is_variable == true)
                                        <a href="{{ url('/variable-products/edit/' . $galleryImage->product_id . '/' . $productslug) }}" class="btn btn-primary btn-sm">Cancel</a>
                                        @else
                                        <a href="{{ url('/products/edit/' . $galleryImage->product_id . '/' . $productslug) }}" class="btn btn-primary btn-sm">Cancel</a>
                                    @endif
                                </div>
                            </div>

                            <form action="{{ url('/gallery-image/update/'.$galleryImage->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Gallery Image <small style="color: red; font-size: 18px;">*</small> Price  Color  Size</label>
                                                @if ($product->is_variable == true)
                                                <div class="input-group mb-3">
                                                    <input type="file" name="image" id="image" class="form-control">
                                                    <input type="text" name="price" id="price" value="{{$galleryImage->price}}" class="form-control" placeholder="Price">
                                                    <input type="text" name="color" id="color" class="form-control" value="{{$galleryImage->color}}" placeholder="Product color">
                                                    <input type="text" name="size" id="size" class="form-control" value="{{$galleryImage->size}}" placeholder="Product size">
                                                </div>
                                                @else
                                                <input type="file" name="image" id="image" class="form-control" required>
                                                @endif
                                                <img src="{{ asset('galleryImage/'.$galleryImage->gallery_image) }}" height="100" width="100" />
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
        </div>
    </div>
@endsection
