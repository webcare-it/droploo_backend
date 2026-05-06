@extends('frontend.v-2.master')

@section('title')
    Product Details
@endsection

@section('content-v2')
    <div id="app">
        <section class="product-details-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-md-12">
                        <div class="product-details-wrapper">
                            <div class="row">
                                <div class="col-lg-7 col-md-7">
                                    <div class="product-images-slider-outer">
                                        <div class="slider slider-content">
                                            @foreach ($details->productImages as $image)
                                            <div>
                                                <img src="{{asset('galleryImage/'.$image->gallery_image)}}" alt="slider images">
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="slider slider-thumb">
                                            @foreach ($details->productImages as $image)
                                            <div>
                                                <img src="{{asset('galleryImage/'.$image->gallery_image)}}" alt="slider images">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-5">
                                    <div class="product-details-content">
                                        <h3 class="product-name">
                                            {{ $details->name }}
                                        </h3>
                                        @if ($details->discount_price != null)
                                        <div class="product-price">
                                            <span>{{ $details->discount_price}}  Tk.</span>
                                            <span class="" style="color: #f74b81;">
                                                <del>{{ $details->regular_price}} Tk.</del>
                                            </span>
                                        </div>
                                        @else
                                        <div class="product-price">
                                            <span>{{ $details->regular_price}}  Tk.</span>
                                        </div>
                                        @endif
                                        {{-- <p class="shor-description">
                                            {{ $details->short_description }}
                                        </p> --}}
                                        <form action="{{url('/add/to/cart/details/page/'.$details->id)}}" onsubmit="onSubmitForm(event)" id="addToCartForm" method="POST">
                                        @csrf
                                            <div class="product-details-select-items-wrap">
                                                @foreach ($details->colors as $color)
                                                @if ($color->color != null)
                                                <div class="product-details-select-item-outer">
                                                    <input type="radio" name="color" id="color" value="{{$color->color}}" class="category-item-radio">
                                                    <label for="color" class="category-item-label">{{$color->color}}</label>
                                                </div>
                                                @endif
                                                @endforeach
                                            </div>
                                            <div class="product-details-select-items-wrap">
                                                @foreach ( $details->sizes as $size )
                                                @if ($size->size != null)
                                                <div class="product-details-select-item-outer">
                                                    <input type="radio" name="size" value="{{$size->size}}" class="category-item-radio">
                                                    <label for="size" class="category-item-label">{{$size->size}}</label>
                                                </div>
                                                @endif
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="product_id" value="{{$details->id}}">
                                            @if ($details->discount_price != null)
                                            <input type="hidden" name="price" value="{{$details->discount_price}}">
                                            @else
                                            <input type="hidden" name="price" value="{{$details->regular_price}}">
                                            @endif
                                            <div class="purchase-info-outer">
                                                <div class="product-incremnt-decrement-outer" style="display: block">
                                                    <a title="Decrement" class="decrement-btn" style="margin-top: -10px;" onclick="decrementQuantity()">
                                                        <i class="fas fa-minus"></i>
                                                    </a>
                                                    <input type="number" readonly name="qty" placeholder="Qty" value="1" min="1" id="qty" style="height: 35px">
                                                    <a title="Increment" class="increment-btn" style="margin-top: -10px;" onclick="incrementQuantity()">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                </div>
                                                <input type="hidden" name="button_action" id="buttonAction" value="">
                                                <div>
                                                    {{-- <button type="submit" name="action" value="addToCart" id="addToCart" class="cart-btn-inner" onclick="setButtonAction('addToCart')">
                                                        <i class="fas fa-shopping-cart"></i>
                                                        Add to Cart
                                                    </button> --}}
                                                    <button type="submit" name="action" value="buyNow" id="buyNow" class="cart-btn-inner" onclick="setButtonAction('buyNow')">
                                                        <i class="fas fa-truck"></i>
                                                        Order Now
                                                    </button>
                                                </div>
                                            </div>
                                        </form>

                                        {{-- <div class="purchase-info-outer">
                                            <button type="button" class="out-stock">
                                                <i class="fas fa-lock"></i>
                                                Stock Out
                                            </button>
                                        </div> --}}
                                        <button type="button" class="product-details-hot-line">
                                            <i class="fas fa-phone-alt"></i>
                                            For Call : {{$setting->phone}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="product-details-info">
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="pills-description-tab" data-bs-toggle="pill" data-bs-target="#pills-description" type="button" role="tab" aria-controls="pills-description" aria-selected="true">
                                            Description
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-review-tab" data-bs-toggle="pill" data-bs-target="#pills-review" type="button" role="tab" aria-controls="pills-review" aria-selected="true">
                                            Review
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-policy-tab" data-bs-toggle="pill" data-bs-target="#pills-policy" type="button" role="tab" aria-controls="pills-policy" aria-selected="true">
                                            Product Policy
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-description" role="tabpanel" aria-labelledby="pills-description-tab">
                                        {!!$details->long_description!!}
                                    </div>
                                    <div class="tab-pane fade" id="pills-review" role="tabpanel" aria-labelledby="pills-review-tab">
                                        @foreach ($details->reviews as $review)
                                        <div class="review-item-wrapper">
                                            <div class="review-item-left">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="review-item-right">
                                                <h4 class="review-author-name">
                                                    {{$review->name}}
                                                    <span class=" d-inline bg-danger badge-sm badge text-white">Verified</span>
                                                </h4>
                                                <p class="review-item-message">
                                                    {!!$review->message!!}
                                                </p>
                                                <span class="review-item-rating-stars">
                                                    <i class="fa-star fas"></i>
                                                    <i class="fa-star fas"></i>
                                                    <i class="fa-star fas"></i>
                                                    <i class="fa-star fas"></i>
                                                    <i class="fa-star fas"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="tab-pane fade" id="pills-policy" role="tabpanel" aria-labelledby="pills-policy-tab">
                                        {!!$details->policy!!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12">
                        <div class="product-details-sidebar">
                            <div class="product-details-categoris">
                                <h3 class="product-details-title">
                                    Category
                                </h3>
                                @foreach ($categories as $category)
                                <a href="{{url('products/'.$category->slug)}}" class="category-item-outer">
                                    <img src="{{asset('category/'.$category->image)}}" alt="category image">
                                    {{$category->name}}
                                </a>
                                @endforeach
                            </div>
                            <div class="banner-item-outer side-banner" >
                                <img src="{{asset('category/'.$details->category->image)}}" alt="banner image">
                                <div class="banner-content">
                                    <h4>
                                        {{$details->category->name}}
                                    </h4>
                                    <a href="{{url('products/'.$details->category->slug)}}" class="shop-now-btn">
                                        Shop Now <i class="fas fa-long-arrow-alt-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="releted-product-section">
            <div class="container">
                <div class="section-title-outer">
                    <h1 class="title">
                        Related Products
                    </h1>
                </div>
                <div class="row">
                    <div class="related-product-items-wrap">
                        @foreach ($related as $product)
                        <div class="product__item-outer">
                            <div class="product__item-image-outer">
                                @if ($product->is_variable == true)
                                <a href="{{url('variable-product/'.$product->slug)}}" class="product__item-image-inner">
                                @else
                                <a href="{{url('product/'.$product->slug)}}" class="product__item-image-inner">    
                                @endif
                                    <img src="{{asset('product/images/'.$product->image)}}" alt="Product Image" />
                                </a>
                                <div class="product__item-add-cart-btn-outer">
                                    <a href="{{url('/add/to/cart/'.$product->id.'/add_cart')}}" class="product__item-add-cart-btn-inner">
                                        Add to Cart
                                    </a>
                                </div>
                            </div>
                            <div class="product__item-info-outer">
                                @if ($product->is_variable == true)
                                <a href="{{url('variable-product/'.$product->slug)}}" class="product__item-name">
                                @else
                                <a href="{{url('product/'.$product->slug)}}" class="product__item-name">    
                                @endif
                                    {{$product->name}}
                                </a>
                                <div class="product__item-price-outer">
                                    <div class="product__item-discount-price">
                                        <del>{{$product->discount_price}} Tk.</del>
                                    </div>
                                    <div class="product__item-regular-price">
                                        <span>{{$product->regular_price}} Tk.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div style="display: none">
        <input type="text" id="product_name" value="{{ $details->name }}">
        <input type="text" id="price" value="{{ $details->regular_price }}">
        <input type="text" id="product_id" value="{{ $details->id }}">
        <input type="text" id="category" value="{{ $details->category->name }}">
    </div>
@endsection

@push('script')
<script>
    var quantityInput = document.getElementById("qty");
    function incrementQuantity() {
        var currentQuantity = parseInt(quantityInput.value);
        if (!isNaN(currentQuantity)) {
            quantityInput.value = currentQuantity + 1;
        }
    }

    function decrementQuantity() {
        var currentQuantity = parseInt(quantityInput.value);
        if (!isNaN(currentQuantity) && currentQuantity > 1) {
            quantityInput.value = currentQuantity - 1;
        }
    }
</script>

<script>
    window.addEventListener('load', function() {
        var product_name = document.getElementById('product_name').value;
        var price = document.getElementById('price').value;
        var product_id = document.getElementById('product_id').value;
        var category = document.getElementById('category').value;
        dataLayer.push({
            ecommerce: null
        });
        dataLayer.push({
            event: "view_item",
            ecommerce: {
                items: [{
                    item_name: product_name,
                    item_id: product_id,
                    price: price,
                    item_brand: "Unknown",
                    item_category: category,
                    item_variant: "",
                    item_list_name: "",
                    item_list_id: "",
                    index: 0,
                    quantity: 1,
                }]
            }
        });
    });
</script>

<script>
    function onSubmitForm(event) {
        event.preventDefault();

        var product_name = document.getElementById('product_name').value;
        var price = document.getElementById('price').value;
        var product_id = document.getElementById('product_id').value;
        var category = document.getElementById('category').value;

        dataLayer = window.dataLayer || []; 

        dataLayer.push({
            ecommerce: null
        });
        dataLayer.push({
            event: "add_to_cart",
            ecommerce: {
                items: [{
                    item_name: product_name,
                    item_id: product_id,
                    price: price,
                    item_brand: "Unknown",
                    item_category: category,
                    item_variant: "",
                    item_list_name: "",
                    item_list_id: "",
                    index: 0,
                    quantity: 1,
                }]
            }
        });
        document.getElementById('addToCartForm').submit();
    }
</script>
<script>
    // Function to set the value of the hidden input based on the clicked button
    function setButtonAction(action) {
        document.getElementById('buttonAction').value = action;
    }
</script>
@endpush
