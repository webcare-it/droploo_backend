@extends('frontend.dropshipper.master')

@section('content')
    <!-- Page header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Make Deposit</h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Page header -->

    <!-- Page Content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Personal Info Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="mb-4">Personal Info</h3>
                    <form class="form-valide" action="{{url('/dropshipper/profile/update/personal-info')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="name">Name<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" id="name" name="name" value="{{$dropshipper->name}}" required>
                                @error('name')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="phone">Phone<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" id="phone" name="phone" value="{{$dropshipper->phone}}" required>
                                @error('phone')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="address">Address<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <textarea class="form-control" id="address" name="address" rows="5" required>{{$dropshipper->address}}</textarea>
                                @error('address')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="image">Logo</label>
                            <div class="col-lg-6">
                                <input type="file" class="form-control" id="image" name="image">
                                <div class="col-md-12">
                                    @if ($dropshipper->image == null)
                                    <img src="{{asset('frontend/dropshipper/images/avatar.png')}}" height="100" width="100"> 
                                    @else
                                    <img src="{{asset('frontend/dropshipper/images/'.$dropshipper->image)}}" height="100" width="100">    
                                    @endif
                                </div>
                                @error('image')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-8 ml-auto">
                                <button type="submit" class="btn btn-primary">Update Personal Info</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bank Info Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="mb-4">Payment Method Info</h3>
                    <form class="form-valide" action="{{url('/dropshipper/profile/update/bank-info')}}" method="post">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="banking_type">Method Type<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select class="form-control" id="banking_type" name="banking_type" required>
                                    <option value="">Select Type</option>
                                    <option value="nagad" {{ old('banking_type', optional($bankDetails)->banking_type) == 'nagad' ? 'selected' : '' }}>Nagad</option>
                                    <option value="bank" {{ old('banking_type', optional($bankDetails)->banking_type) == 'bank' ? 'selected' : '' }}>Bank</option>
                                </select>
                                @error('banking_type')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="account_details">Account Details<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <textarea class="form-control" id="account_details" name="account_details" rows="5" required>{{ old('account_details', optional($bankDetails)->account_details) }}</textarea>
                                @error('account_details')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-8 ml-auto">
                                <button type="submit" class="btn btn-primary">Update Bank Info</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Credentials Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="mb-4">Credentials</h3>
                    <form class="form-valide" action="{{url('/dropshipper/profile/update/credentials')}}" method="post">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="email">Email</label>
                            <div class="col-lg-6">
                                <input type="email" class="form-control" id="email" name="email" value="{{$dropshipper->email}}" readonly>
                                @error('email')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="old_password">Old Password<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" id="old_password" name="old_password" required>
                                @error('old_password')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="password">New Password<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" id="password" name="password" required>
                                @error('password')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-8 ml-auto">
                                <button type="submit" class="btn btn-primary">Update Credentials</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Page Content -->
@endsection
