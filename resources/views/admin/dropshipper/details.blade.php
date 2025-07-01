@extends('admin.master')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 d-flex align-items-stretch flex-column">
                    <div class="card bg-light d-flex flex-fill">
                        <div class="card-header text-muted border-bottom-0">
                            Dropshipper Details
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-7">
                                    <h2 class="lead"><b>{{$dropshipper->name}}</b></h2>
                                    <p class="text-muted text-sm"><b>Wallet : </b>{{$dropshipper->total_deposit}} BDT</p>
                                    <p class="text-muted text-sm"><b>Credit : </b>{{$dropshipper->total_credit}} BDT</p>
                                    <p class="text-muted text-sm"><b>Withdraw : </b>{{$dropshipper->total_withdraw}} BDT</p>
                                    <p class="text-muted text-sm"><b>Total Earning : </b>{{$dropshipper->total_withdraw + $dropshipper->total_credit}} BDT</p>
                                    {{-- <p class="text-muted text-sm"><b>Need to Pay: </b>0 BDT</p> --}}
                                    <p class="text-muted text-sm"><b>Orders : </b>{{$ordersCount}}</p>
                                    <ul class="ml-4 mb-0 fa-ul text-muted">
                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span>
                                            Address: {{$dropshipper->address}}</li>
                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone
                                            #: {{$dropshipper->phone}}</li>
                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Email
                                            : {{$dropshipper->email}}</li>
                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Website
                                            : {{$dropshipper->domain_name}}</li>
                                    </ul>
                                </div>
                                <div class="col-5 text-center">
                                    @if ($dropshipper->image != null)
                                    <img src="{{asset('frontend/dropshipper/images/'.$dropshipper->image)}}" alt="user-avatar" class="img-circle img-fluid">
                                    @else
                                    <img src="{{asset('frontend/dropshipper/images/avatar.png')}}" alt="user-avatar" class="img-circle img-fluid">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-right">
                                <a href="{{url('dropshipper-orders/'.$dropshipper->id)}}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-user"></i> View Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
