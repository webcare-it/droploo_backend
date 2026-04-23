@extends('frontend.v-2.master')

@section('title')
    Vendor Registration
@endsection

@section('content-v2')
    <section class="return-process-section" id="app">
        <div class="container">
            <div class="row">
                <div class="col-md-10 m-auto">
                    <form action="{{ route('vendor.register.store') }}" method="POST" class="return-process-form form-group" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center">
                            <h3 class="return-process-form-title">Vendor Registration</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="first_name">First Name</label>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First Name*" class="form-control" />
                                    @error('first_name')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name*" class="form-control" />
                                    @error('last_name')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email*" class="form-control" />
                                    @error('email')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone*" class="form-control" />
                                    @error('phone')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="shop_name">Shop Name</label>
                                    <input type="text" name="shop_name" value="{{ old('shop_name') }}" placeholder="Shop Name*" class="form-control" />
                                    @error('shop_name')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Address*" class="form-control" />
                                    @error('address')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="logo">Logo</label>
                                    <input type="file" name="logo" value="{{ old('logo') }}" placeholder="Logo*" class="form-control" accept="image/*/>
                                    @error('logo')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" placeholder="Password*" class="form-control" />
                                    @error('password')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="return-process-btn-outer">
                            <button type="submit" id="vendorRegistration" class="return-process-btn-inner">
                                Register
                            </button>
                        </div>
                        <div class="offset-md-8 col-md-4 mt-5"><p>Already have an account? <a href="{{url('/vendor/login/form')}}">Login</a></p></div>
                    </form>                
                </div>
            </div>
        </div>
    </section>
@endsection
