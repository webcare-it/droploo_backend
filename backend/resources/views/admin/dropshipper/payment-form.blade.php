@extends('admin.master')

@push('style')
    <style type="text/css">
        .input-group-wrap {
            margin-bottom: 15px;
        }
        .input-group-wrap label {
            font-size: 15px;
            font-weight: 500;
            color: #000;
            margin-bottom: 3px;
        }
        .table td, .table th{
            vertical-align: middle;
            padding: 5px;
        }
    </style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <form action="{{url('/dropshipper-order/manual-payment-store/'.$order->id)}}" method="post" class="order-details-form form-group">
            @csrf
            <div class="row">
                <input type="hidden" name="backLink" value="{{$backLink}}">
                <div class="col-md-4">
                    <div class="customer-details-wrap card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Customer Details </strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- <div class="col-md-6">
                                    <div class="input-group-wrap">
                                        <label for="store">
                                            Store <span style="color: red;">*</span>
                                        </label>
                                        <input type="text" name="store" class="form-control" value="{{$setting->app_name}}.com" readonly>
                                    </div>
                                </div> --}}
                                <div class="col-md-6">
                                    <div class="input-group-wrap">
                                        <label for="domain_name">
                                            Dropshipper <span style="color: red;">*</span>
                                        </label>
                                        <input type="text" name="domain_name" class="form-control" value="{{ $order->dropshipper->domain_name ?? 'N.A' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-wrap">
                                        <label for="phone">
                                            Dropshipper Phone <span style="color: red;">*</span>
                                        </label>
                                        <input type="text" name="phone" class="form-control" value="{{ $order->dropshipper->phone ?? 'N.A' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-wrap">
                                        <label for="invoice">
                                            Invoice <span style="color: red;">*</span>
                                        </label>
                                        <input type="text" name="invoice" class="form-control" value="{{ $order->orderId ?? 'Orderid' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-wrap">
                                        <label for="c_name">
                                            Customer Name <span style="color: red;">*</span>
                                        </label>
                                        <input type="text" name="name" class="form-control" value="{{ $order->name ?? 'Customer name' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-wrap">
                                        <label for="c_phone">
                                            Customer Phone <span style="color: red;">*</span>
                                        </label>
                                        <input type="text" name="phone" class="form-control" value="{{ $order->phone ?? 'No phone' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group-wrap">
                                        <label for="c_address">
                                            Customer Address <span style="color: red;">*</span>
                                        </label>
                                        <textarea class="form-control" rows="4" name="address" readonly>{{ $order->address ?? 'No address' }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="input-group-wrap">
                                        <label for="pathao_special_note">
                                            Special Notes
                                        </label>
                                        <textarea class="form-control" rows="4" name="pathao_special_note" readonly>{{ $order->pathao_special_note ?? 'No Notes' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="order-details-wrap card">
                        <div class="card-header">
                            <strong>Order Details</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th style="width: 20%;">Name</th>
                                    <th style="width: 20%;">Color</th>
                                    <th style="width: 20%;">Size</th>
                                    <th style="width: 15%;">Qty</th>
                                    <th style="width: 20%;">Price</th>
                                </tr>
                                @php
                                    $sum = 0;
                                @endphp
                                @foreach ($order->orderDetails as $orderDetail)
                                    <tr>
                                        <td>
                                            <img src="{{ asset('/product/images/' .$orderDetail->product?->image) }}" height="40" width="40"/><br>
                                            {{ $orderDetail->product?->name ?? 'Product name' }}
                                        </td>
                                        <td>
                                            {{$orderDetail->color ?? 'N.A'}}
                                        </td>
                                        <td>
                                            {{$orderDetail->size ?? 'N.A'}}
                                        </td>
                                        <td>
                                           <input type="number" name="qty" id="qty-{{ $orderDetail?->id }}"  value="{{$orderDetail->qty}}" placeholder="Qty" style="width:80px;" readonly/>
                                        </td>
                                        <td>
                                            <input type="number" name="regular_price" id="regular_price-{{ $orderDetail?->id }}" value="{{ $total = $orderDetail?->qty * $orderDetail->price }}" class="form-control" readonly/>
                                        </td>
                                    </tr>
                                    @php
                                        $sum += $total
                                    @endphp
                                    <input class="form-control" type="hidden" name="per_price" id="per_price" value="{{ $orderDetail->price ?? 'price' }}" readonly>
                                @endforeach
                            </table>

                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Sub Total</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="number" readonly name="price" id="sub_total" value="{{ $sum }}">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Delivery Charge</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="number" name="area" id="area" onkeyup="" value="{{ $area = $order->area }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">

                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Discount</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="discount" id="discount" value="{{ $order->discount ?? '0' }}" placeholder="Enter Discount Price" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Advance</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="advance" id="advance" value="{{ $order->advance ?? '0' }}" placeholder="Enter Advance Price" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Total</strong>
                                    </div>
                                    <div class="col-md-6">
                                        @if ($order->discount != null && $order->advance == null)
                                            <input class="form-control total_price" type="text" name="total_price"
                                                id="total_price" value="{{ $sum + $area - $order->discount }}" readonly>
                                        @endif

                                        @if ($order->discount != null && $order->advance != null)
                                            @php
                                                $x = $sum + $area;
                                                $y = $x - $order->discount;
                                                $z = $y - $order->advance;

                                            @endphp
                                            <input class="form-control total_price" type="text" name="total_price"
                                                id="total_price" value="{{ $z }}" readonly>
                                        @endif

                                        @if ($order->advance == null && $order->discount == null)
                                            <input class="form-control total_price" type="text" name="total_price"
                                                id="total_price" value="{{ $sum + $area }}" readonly>
                                        @endif

                                        @if ($order->advance != null && $order->discount == null)
                                            <input class="form-control total_price" type="text" name="total_price"
                                                id="total_price" value="{{ $order->price }}" readonly>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Amount</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="number" name="credit_amount" id="credit_amount" value="" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" id="submit" class="btn btn-primary btn-sm">Make Payment</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let orderId = {{ $order->id }};
        function deliveryCharge(e){
            axios.post('/api/order/delivery/charge/update/' + orderId, {
                area: e
            })
                .then(response => {
                    if(response.status == 200){
                        //alert('Delivery charge has been updated.')
                        location.reload()
                    }
                }).catch(error => {
                return confirm('Something is wrong, Please try again')
            })
        }

        function productPrice(orderDetailPrice){
            let price = document.getElementById('regular_price-' + orderDetailPrice.id).value;
            axios.post('/api/order/price/update/' + orderDetailPrice.id, {
                regular_price: price
            })
                .then(response => {
                    if(response.status == 200){
                        //alert('Order Price has been updated.')
                        location.reload()
                    }
                }).catch(error => {
                return confirm('Something is wrong, Please try again')
            })
        }

        function productQty(orderDetail){
            let qty = document.getElementById('qty-' + orderDetail.id).value;
            axios.post('/api/order/product/qty/update/' + orderDetail.id, {
                qty: qty
            })
                .then(response => {
                    if(response.status == 200){
                        //alert('Qty has been updated.')
                        location.reload()
                    }
                }).catch(error => {
                return confirm('Something is wrong, Please try again')
            })
        }

        function productColor(orderDetail){
            let color = document.getElementById('color-' + orderDetail.id).value;
            axios.post('/api/order/product/color/update/' + orderDetail.id, {
                color: color
            })
                .then(response => {
                    if(response.status == 200){
                        Swal.fire('Product color has been updated')
                    }
                }).catch(error => {
                    Swal.fire('Something is wrong, Please try again')
            })
        }

        function productSize(orderDetail){
            let size = document.getElementById('size-' + orderDetail.id).value;
            axios.post('/api/order/product/size/update/' + orderDetail.id, {
                size: size
            })
                .then(response => {
                    if(response.status == 200){
                        Swal.fire('Product size has been updated')
                    }
                }).catch(error => {
                    Swal.fire('Something is wrong, Please try again')
            })
        }

        let totalCost = document.getElementById('total_price').value;
        let showCost = document.getElementById('total_price');
        function orderAdvance(advance){
            let afterAdvanceCost = parseInt(totalCost) - parseInt(advance);
            showCost.value = afterAdvanceCost;
        }

        let totalCostForDiscount = document.getElementById('total_price').value;
        let showDiscountCost = document.getElementById('total_price');
        function orderDiscount(discount){
            let afterDiscountCost = parseInt(totalCostForDiscount) - parseInt(discount);
            showDiscountCost.value = afterDiscountCost;
        }

        //Code for fetching zone list....
        document.addEventListener("DOMContentLoaded", function() {
            const citySelect = document.getElementById("city");
            const zoneSelect = document.getElementById("zone");

            citySelect.addEventListener("change", function() {
                const selectedCityId = this.value;
                if (selectedCityId) {
                    fetch(`/get-zones/${selectedCityId}`)
                        .then(response => response.json())
                        .then(data => {
                            const zonesData = data.zones.data;

                            // Clear existing options
                            zoneSelect.innerHTML = '<option selected disabled>-- Select Zone --</option>';

                            // Add new zone options
                            zonesData.forEach(zone => {
                                const option = document.createElement("option");
                                option.value = zone.zone_id;
                                option.textContent = zone.zone_name;
                                zoneSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error(error));
                } else {
                    // Reset zone select if no city is selected
                    zoneSelect.innerHTML = '<option selected disabled>-- Select Zone --</option>';
                }
            });
        });
        //Code for fetching zone list....

        //Code For set city_name in input field...

        const citySelect = document.getElementById('city');
        const cityInput = document.getElementById('city_name');

        citySelect.addEventListener('change', function() {
            const selectedOption = citySelect.options[citySelect.selectedIndex];
            cityInput.value = selectedOption.text;
        });
        //Code For set city_name in input field...

        //Code For set zone_name in input field...

        const zoneSelect = document.getElementById('zone');
        const zoneInput = document.getElementById('zone_name');

        zoneSelect.addEventListener('change', function() {
            const selectedOption = zoneSelect.options[zoneSelect.selectedIndex];
            zoneInput.value = selectedOption.text;
        });
        //Code For set zone_name in input field...

        //If Courier is Othres then City & Zone Will be hidden...
        const courierSelect = document.getElementById('courier');
        const cityZoneWrapper = document.getElementById('cityZoneWrapper');
        const ZoneWrapper = document.getElementById('ZoneWrapper');
        const textareaWrapper = document.getElementById('textareaWrapper');

        courierSelect.addEventListener('change', function () {
            if (courierSelect.value === 'Others') {
                cityZoneWrapper.style.display = 'none';
                ZoneWrapper.style.display = 'none';
                textareaWrapper.style.display = 'block';
            } else {
                cityZoneWrapper.style.display = 'block';
                ZoneWrapper.style.display = 'block';
                textareaWrapper.style.display = 'none';
            }
        });
        //If Courier is Othres then City & Zone Will be hidden...

        if (courierSelect.value === 'Others') {
            cityZoneWrapper.style.display = 'none';
            ZoneWrapper.style.display = 'none';
            textareaWrapper.style.display = 'block';
        }
    </script>
@endpush
