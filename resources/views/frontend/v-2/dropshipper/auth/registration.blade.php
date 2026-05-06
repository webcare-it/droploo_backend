@extends('frontend.v-2.master')

@section('title')
    Dropshipper Registration
@endsection

@section('content-v2')
    <section class="return-process-section" id="app">
        <div class="container">
            <div class="row">
                <div class="col-md-10 m-auto">
                    <form action="{{ route('dropshipper.reg.store') }}" method="POST" class="return-process-form form-group" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center">
                            <h3 class="return-process-form-title">Dropshipper Registration</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" placeholder="First Name*" class="form-control" required/>
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
                                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone*" class="form-control" required/>
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
                                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Address*" class="form-control" required/>
                                    @error('address')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="domain_name">Website Domain Name</label>
                                    <input type="text" name="domain_name" value="{{ old('domain_name') }}" placeholder="Domain Name*" class="form-control" required/>
                                    @error('domain_name')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-item-wrapper">
                                    <label for="image">Logo</label>
                                    <input type="file" name="image" value="{{ old('image') }}" placeholder="Logo*" class="form-control" accept="image/*"/>
                                    @error('image')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email*" class="form-control" required/>
                                    @error('email')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-item-wrapper">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" placeholder="Password*" class="form-control" required/>
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
                        <div class="offset-md-8 col-md-4 mt-5"><p>Already have an account? <a href="{{url('/dropshipper/login-form')}}">Login</a></p></div>
                    </form>                
                </div>
            </div>
        </div>
    </section>
@endsection
