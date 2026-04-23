@extends('frontend.v-2.master')

@section('title')
    Vendor Login
@endsection

@section('content-v2')
    <section class="return-process-section" id="app">
        <div class="container">
            <div class="row">
                <div class="col-md-10 m-auto">
                    <form action="{{ route('vendor.login') }}" method="POST" class="return-process-form form-group" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center">
                            <h3 class="return-process-form-title">Vendor Login</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-item-wrapper">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" value="{{old('email')}}" placeholder="email*" class="form-control" />
                                    @error('email')
                                        <p class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-item-wrapper">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" value="{{old('password')}}" placeholder="password*" class="form-control" />
                                    @error('password')
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
                        <div class="offset-md-8 col-md-4 mt-5"><p>Don't have an account? <a href="{{url('/vendor/registration')}}">Register</a></p></div>
                    </form>                
                </div>
            </div>
        </div>
    </section>
@endsection
