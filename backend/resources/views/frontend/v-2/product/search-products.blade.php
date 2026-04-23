@extends('frontend.v-2.master')

@section('title')
  Shop | Products
@endsection

@section('content-v2')
    <section class="product-page-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="product-page-header-wrapper">
                                <div class="left-side-box">
                                    <h4 class="title">
                                        Products
                                    </h4>
                                </div>
                                <div class="right-side-box">
                                    <h4 class="product-qty">
                                        Total Products
                                        <span class="number">{{ $products->count() }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        @if ($products->count() == 0)
                            <div class="row">
                                <div class="col-md-12 text-center align-items-center">
                                    <h3 class="bg-warning">No Product Found!</h3>
                                </div>
                            </div>
                        @endif
                        
                        @foreach ($products as $product)
                        <div class="col-lg-2 col-md-3 col-sm-6">
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
                                    <div class="product__type-badge-outer">
                                        <span class="product__type-badge-inner">
                                            {{ucfirst($product->product_type)}}
                                        </span>
                                    </div>
                                </div>
                                <div class="product__item-info-outer">
                                    @if ($product->is_variable == true)
                                    <a href="{{url('variable-product/'.$product->slug)}}" class="product__item-name">
                                     @else
                                     <a href="{{url('product/'.$product->slug)}}" class="product__item-name">   
                                    @endif
                                        {{mb_strlen($product->name, 'UTF-8') > 50 ? mb_substr($product->name, 0, 50, 'UTF-8') . '....' : $product->name}}
                                    </a>
                                    <div class="product__item-price-outer">
                                        @if ($product->discount_price != null)
                                        <div class="product__item-discount-price">
                                            <del>{{$product->regular_price}} Tk.</del>
                                        </div>
                                        <div class="product__item-regular-price">
                                            <span>{{$product->discount_price}} Tk.</span>
                                        </div>
                                        @else
                                        <div class="product__item-regular-price">
                                            <span>{{$product->regular_price}} Tk.</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
<script>
    function category(){
        document.getElementById('collapseOne').submit();
    }

    function subCategory(){
        document.getElementById('collapseTwo').submit();
    }
</script>
@endpush
