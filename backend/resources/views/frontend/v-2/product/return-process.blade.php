@extends('frontend.v-2.master')

@section('title')
    Product Return
@endsection

@section('content-v2')
    <section class="return-process-section" id="app">
        <div class="container">
            <div class="row">
                <div class="col-md-10 m-auto">
                    <form action="{{url('/customer/product/return/info')}}" method="POST" class="return-process-form form-group" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center">
                            <h3 class="return-process-form-title">Product Return Process</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" value="{{old('name')}}" placeholder="Name*" class="form-control" />
                                    @error('name')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="phone">Phone</label>
                                    <input type="number" name="phone" value="{{old('phone')}}" placeholder="Phone*" class="form-control" />
                                    @error('phone')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" value="{{old('address')}}" placeholder="Address*" class="form-control" />
                                    @error('address')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="order_id">Order Id</label>
                                    <input type="text" name="order_id" value="{{old('order_id')}}" placeholder="Order Id*" class="form-control" />
                                    @error('order_id')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-item-wrapper">
                                    <label for="issue">Define issues</label>
                                    <textarea name="issues" cols="50" rows="5" class="form-control" required></textarea>
                                    @error('issues')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-item-wrapper">
                                    <label for="images">Image</label>
                                    <input type="file" id="images" name="images" accept="image/*" class="form-control" />
                                    @error('images')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="return-process-btn-outer">
                            <button type="submit" id="productReturnProcess" class="return-process-btn-inner">
                                Submit
                            </button>
                        </div>
                    </form>                
                </div>
            </div>
        </div>
    </section>
@endsection
