@extends('frontend.v-2.master')

@section('title')
Checkout products
@endsection

@section('content-v2')
<section class="checkout-section">
    <div class="container">
        <form action="{{ url('/customer/order/confirm') }}" method="post" class="form-group billing-address-form"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8 col-md-6">
                    <div class="checkout-wrapper">
                        @php
                        $sum1 = 0;
                        $qtyTotal = 0;
                        @endphp
                        @foreach ($products as $product)
                        <input type="hidden" name="id[]" id="id" value="{{ $product->product_id }}"
                            class="form-control">
                        <input type="hidden" name="qty[]" id="qty" value="{{ $totalQty = $product->qty }}"
                            class="form-control">
                        <input type="hidden" name="total[]" id="totalProductCost"
                            value="{{ $total1 = $product->price }}" class="form-control">
                        @php
                        $sum1 += $total1;
                        $qtyTotal += $totalQty;
                        @endphp
                        @endforeach
                        <input type="hidden" name="totalCost" id="totalCost" value="{{ $sum1 }}" />
                        <input type="hidden" name="totalQty" id="totalQty" value="{{ $qtyTotal }}" />
                        <span id="charge" style="display: none;"></span>
                        <span id="total" style="display: none;"></span>
                        <div class="billing-address-wrapper">
                            <h4 class="title">Billing / Shipping Details</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="hidden" name="order_type" value="Website" class="form-control">
                                    <input type="text" name="name" class="form-control" placeholder="Enter Full Name *">
                                    @if ($errors->has('name'))
                                    <div class="text-danger">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="phone" class="form-control" placeholder="Phone *">
                                    @if ($errors->has('phone'))
                                    <div class="text-danger">{{ $errors->first('phone') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <textarea rows="4" name="address" class="form-control" id="address"
                                        placeholder="Enter Full Address"></textarea>
                                    <!-- <input type="text" name="address" id="address" class="form-control" placeholder="Enter Full Address"> -->
                                    @if ($errors->has('sub_district_id'))
                                    <div class="text-danger">{{ $errors->first('sub_district_id') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div style="background: lightgrey;padding: 10px;margin-bottom: 10px;">
                                        <input type="radio" id="inside_dhaka" name="area" value="80"
                                            onchange="deliveryCharge(this.value)">
                                        <label for="inside_dhaka"
                                            style="font-size: 18px;font-weight: 600;color: #000;">Inside Dhaka (80
                                            Tk.)</label>
                                    </div>
                                    <div style="background: lightgrey;padding: 10px;">
                                        <input type="radio" id="outside_dhaka" name="area" value="150"
                                            onchange="deliveryCharge(this.value)">
                                        <label for="outside_dhaka"
                                            style="font-size: 18px;font-weight: 600;color: #000;">Outside Dhaka (150
                                            Tk.)</label>
                                    </div>
                                    @if ($errors->has('area'))
                                    <div class="text-danger">{{ $errors->first('area') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="checkout-items-wrapper">
                        @foreach ($products as $product)
                        <div class="checkout-item-outer">
                            <div class="checkout-item-left">
                                <div class="checkout-item-image">
                                    <img src="{{ asset('product/images/' . $product->product?->image) }}" />
                                </div>
                                <div class="checkout-item-info">
                                    <h6 class="checkout-item-name">
                                        {{ $product->product?->name }}
                                    </h6>
                                    <p class="checkout-item-price">
                                        {{ $product->price }} Tk.
                                    </p>
                                    <span class="checkout-item-count">
                                        {{ $product->qty }} item
                                    </span><br />
                                    <span class="checkout-item-count">
                                        Size: {{ $product->size ? $product->size : 'No size' }}
                                        <input type="hidden" name="size[]"
                                            value="{{ $product->size ? $product->size : 'No size' }}" </span> <span
                                            class="text-danger" style="font-weight: 600">|</span>
                                        <span class="checkout-item-count">
                                            Color: {{ $product->color ? $product->color : 'No color' }}
                                            <input type="hidden" name="color[]"
                                                value="{{ $product->color ? $product->color : 'No color' }}" </span>
                                            <div class="checkout-product-incre-decre">
                                                <button type="button" title="Decrement"
                                                    onclick="decrementValue({{ $product->id }}); subTotalCost({{$product->id }});"
                                                    class="qty-decrement-btn">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" readonly name="indqty[]" placeholder="Qty" min="1"
                                                    id="indqty-{{ $product->id }}" style="height: 35px;"
                                                    value="{{ $product->qty }}">
                                                <button type="button" title="Increment"
                                                    onclick="incrementValue({{ $product->id }}); subTotalCost({{$product->id }});"
                                                    class="qty-increment-btn">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <input type="hidden" value="{{$product->price* $product->qty}}"
                                                    id="indprice-{{ $product->id }}" name="indprice[]">
                                                    <input type="hidden" value="{{$product->price* $product->qty}}"
                                                    id="indprice1-{{ $product->id }}" name="indprice1[]">
                                                    <span id="showprice-{{ $product->id }}" class="d-none">{{ $product->price* $product->qty }}৳</span>
                                                <input type="hidden" id="totalindprice-{{ $product->id }}">
                                                <input type="hidden" id="updatedCost" name="updatedCost[]">
                                            </div>
                                </div>
                            </div>
                            <div class="checkout-item-right">
                                <a href="{{ url('/product/delete/form/cart/' . $product->id) }}" class="delete-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                        <div class="sub-total-wrap">
                            <div class="sub-total-item">
                                 <strong>Sub Total</strong>
                                <strong id="subTotal">৳ {{ $sum1 }}</strong>
                            </div>
                            <div class="sub-total-item">
                                <strong>Delivery charge</strong>
                                <strong id="deliveryCharge">৳ 0</strong>
                                <input type="hidden" id="deliveryChargeInput">
                            </div>
                            <div class="sub-total-item grand-total">
                                 <strong>Grand Total</strong>
                                 <strong id="grandTotal">৳ 0</strong>
                                 <input type="hidden" id="prevGrandTotal">
                            </div>
                        </div>
                        <div class="payment-item-outer">
                            <h6 class="payment-item-title">
                                Select Payment Method
                            </h6>
                            <div class="payment-items-wrap justify-content-center">
                                <div class="payment-item-outer">
                                    <input type="radio" name="payment_type" id="cod" value="cod" checked="">
                                    <label class="payment-item-outer-lable" for="cod">
                                        <strong>Cash On Delivery</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="order-place-btn-outer">
                            <button type="submit" class="order-place-btn-inner">
                                Place an Order
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
{{-- <section class="releted-product-section" id="app"> --}}
    {{-- <div class="container"> --}}
        {{-- <div class="section-title-outer"> --}}
            {{-- <h1 class="title"> --}}
                {{-- You may also like this Products --}}
                {{-- </h1> --}}
            {{-- </div> --}}
        {{-- <combo-product :combo_products="{{  $comboProducts  }}"></combo-product> --}}
        {{-- </div> --}}
    {{-- </section> --}}
@endsection

@push('script')
<script>
    let total = document.getElementById('total').innerHTML;
        let totalCost = document.getElementById('totalCost').value;

        function deliveryCharge(cost) {
            let charge = document.getElementById('deliveryCharge').innerHTML = cost;
            let deliveryChargeInput = document.getElementById('deliveryChargeInput').value = cost;
            let totalAmount = document.getElementById('grandTotal').innerHTML = parseInt(totalCost) + parseInt(cost);
            let prevGrandTotal = document.getElementById('prevGrandTotal').value = parseInt(totalCost) + parseInt(cost);
            let totalInputAmount = document.getElementById('totalCost').value = parseInt(totalCost) + parseInt(cost);
        }

        function districtName(districtId) {
            axios.get('/district-wise-sub_district/' + districtId)
                .then(response => {
                    console.log(response.data)
                    opt = '';
                    opt += "<option value=''>Select a SubDistrict</option>";
                    for (let i = 0; i <= response.data.length - 1; i++) {
                        opt += "<option value='" + response.data[i].id + "'>" + response.data[i].name + "</option>";
                    }

                    document.getElementById('sub_district').innerHTML = opt;
                }).catch(error => {
                    console.log(error);
                })
        }

        //Payment type change
        function paymentTypeChange(e) {
            console.log(e.value);
        }

        function incrementValue(productId) {
            var value = parseInt(document.getElementById('indqty-' + productId).value, 10);
            value = isNaN(value) ? 0 : value;
            if (value < 10) {
                value++;
                document.getElementById('indqty-' + productId).value = value;
            }
        }

        function decrementValue(productId) {
            var value = parseInt(document.getElementById('indqty-' + productId).value, 10);
            value = isNaN(value) ? 0 : value;
            if (value > 1) {
                value--;
                document.getElementById('indqty-' + productId).value = value;
            }
        }

        function calculateGrandTotal() {
            var updatedCostInputs = document.getElementsByName("indprice1[]");
            var grandTotal = 0;

            for (var i = 0; i < updatedCostInputs.length; i++) {
                grandTotal += parseFloat(updatedCostInputs[i].value);
            }
            let charge = parseInt(document.getElementById('deliveryChargeInput').value);
            if(isNaN(charge)){
                charge = 0;
            }
            var totalWithDelivery = grandTotal + charge;
            //document.getElementById("grandTotal").textContent = grandTotal.toFixed(2) + "৳";
            document.getElementById("grandTotal").textContent = totalWithDelivery.toFixed(2) + "৳";
            document.getElementById("subTotal").textContent = grandTotal.toFixed(2) + "৳";
        }

        function subTotalCost(productId){
            var price = parseFloat(document.getElementById('indprice-' + productId).value);
            var qty = parseInt(document.getElementById('indqty-' + productId).value);
            var totalIndPrice = qty*price;
            console.log(totalIndPrice);
            console.log("Total Price");
            document.getElementById('showprice-'+productId).innerHTML = totalIndPrice + '৳';
            document.getElementById('indprice1-'+productId).value = totalIndPrice;
            calculateGrandTotal();
        }
</script>

{{-- Data Layer... --}}
<script type = "text/javascript">
    window.addEventListener('load', function() {
        dataLayer.push({ ecommerce: null });
        dataLayer.push({
            event    : "begin_checkout",
            ecommerce: {
                items: [@foreach ($products as $product){
                    item_name     : "{{$product->product->name}}",
                    item_id       : "{{$product->product->id}}",
                    price         : "{{$product->product->regular_price}}",
                    item_brand    : "Unknown",
                    item_category : "Unknown",
                    item_variant  : "",
                    item_list_name: "", 
                    item_list_id  : "",
                    index         : 0, 
                    quantity      : "{{$product->qty}}"
                },@endforeach]
            }
        });
    });
</script>
@endpush
