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

                            <form action="{{url('dropshipper-update/'.$dropshipper->id)}}" method="post" enctype="multipart/form-data">
                                @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Domain Name <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="text" name="domain_name" value="{{$dropshipper->domain_name}}" class="form-control" placeholder="Enter domain_name"><br>
                                                    <span style="color: red"> {{ $errors->has('domain_name') ? $errors->first('domain_name') : ' ' }}</span>
                                                </div>
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Name <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="text" name="name" value="{{$dropshipper->name}}" class="form-control" placeholder="Enter name"><br>
                                                    <span style="color: red"> {{ $errors->has('name') ? $errors->first('name') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Phone <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="text" name="phone" value="{{$dropshipper->phone}}" class="form-control" placeholder="Enter phone"><br>
                                                    <span style="color: red"> {{ $errors->has('phone') ? $errors->first('phone') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Email <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="email" name="email" value="{{$dropshipper->email}}" class="form-control" placeholder="Enter email"><br>
                                                    <span style="color: red"> {{ $errors->has('email') ? $errors->first('email') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Logo <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="file" name="image" id="image" class="form-control"><br>
                                                    <span style="color: red"> {{ $errors->has('image') ? $errors->first('image') : ' ' }}</span>
                                                </div>
                                                <img src="{{asset('frontend/dropshipper/images/'.$dropshipper->image)}}" height="100" width="100" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Address <small style="color: red; font-size: 18px;">*</small></label>
                                                <textarea class="form-control" rows="5" name="address">{!!$dropshipper->address!!}</textarea><br>
                                                <span style="color: red"> {{ $errors->has('address') ? $errors->first('address') : ' ' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Rest Password <small style="color: red; font-size: 18px;">*</small></label>
                                                <input type="text" name="password" value="" class="form-control" placeholder="Enter password"><br>
                                                <span style="color: red"> {{ $errors->has('password') ? $errors->first('password') : ' ' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mt-2 float-right">Update</button>
                          </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
