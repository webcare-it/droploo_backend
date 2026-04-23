@extends('admin.master')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col">
                    <div class="card radius-10 mb-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        <h5 class="mb-1">Export Orders</h5>
                                        <p class="mb-4">Export orders based on date range, order status, and selected columns</p>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('admin.export.orders') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="from" class="form-label">From Date</label>
                                            <input type="date" class="form-control" id="from" name="from">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="to" class="form-label">To Date</label>
                                            <input type="date" class="form-control" id="to" name="to">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="order_status" class="form-label">Order Status</label>
                                            <select class="form-control" id="order_status" name="order_status">
                                                <option value="">All Statuses</option>
                                                <option value="pending">Pending</option>
                                                <option value="processing">Processing</option>
                                                <option value="shipped">Shipped</option>
                                                <option value="delivered">Delivered</option>
                                                <option value="cancelled">Cancelled</option>
                                                <option value="hold">On Hold</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Select Columns to Export</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="ItemType(*)" id="ItemType" checked>
                                                        <label class="form-check-label" for="ItemType">ItemType(*)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="StoreName(*)" id="StoreName" checked>
                                                        <label class="form-check-label" for="StoreName">StoreName(*)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="MerchantOrderId" id="MerchantOrderId" checked>
                                                        <label class="form-check-label" for="MerchantOrderId">Order ID</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="RecipientName(*)" id="RecipientName" checked>
                                                        <label class="form-check-label" for="RecipientName">RecipientName(*)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="RecipientPhone(*)" id="RecipientPhone" checked>
                                                        <label class="form-check-label" for="RecipientPhone">RecipientPhone(*)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="RecipientCity(*)" id="RecipientCity" checked>
                                                        <label class="form-check-label" for="RecipientCity">RecipientCity(*)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="RecipientZone(*)" id="RecipientZone" checked>
                                                        <label class="form-check-label" for="RecipientZone">RecipientZone(*)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="RecipientArea" id="RecipientArea" checked>
                                                        <label class="form-check-label" for="RecipientArea">RecipientArea</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="RecipientAddress(*)" id="RecipientAddress" checked>
                                                        <label class="form-check-label" for="RecipientAddress">RecipientAddress(*)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="AmountToCollect(*)" id="AmountToCollect" checked>
                                                        <label class="form-check-label" for="AmountToCollect">AmountToCollect(*)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="ItemQuantity(*)" id="ItemQuantity" checked>
                                                        <label class="form-check-label" for="ItemQuantity">ItemQuantity(*)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="ItemWeight(*)" id="ItemWeight" checked>
                                                        <label class="form-check-label" for="ItemWeight">ItemWeight(*)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="ItemDesc" id="ItemDesc" checked>
                                                        <label class="form-check-label" for="ItemDesc">ItemDesc</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="SpecialInstruction" id="SpecialInstruction" checked>
                                                        <label class="form-check-label" for="SpecialInstruction">SpecialInstruction</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="columns[]" value="OrderStatus" id="OrderStatus" checked> <!-- Added Order Status checkbox -->
                                                        <label class="form-check-label" for="OrderStatus">Order Status</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Export Orders</button>
                                        <a href="{{ url('/admin/order/report') }}" class="btn btn-secondary">Back to Reports</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection