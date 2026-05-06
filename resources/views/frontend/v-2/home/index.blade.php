@extends('frontend.v-2.master')

@section('title')
    Home
@endsection

@section('content-v2')
<!-- /Home Slider -->
<section class="home-slider-section">
    <div class="container">
        <div class="home__slider-sec-wrap">
            <div class="home__category-outer">
                <ul class="header__category-list">
                    @foreach ($categories as $category)
                        <li class="header__category-list-item item-has-submenu">
                            <a href="{{ url('/products/'.$category->slug) }}" class="header__category-list-item-link">
                                <img src="{{ asset('/category/'.$category->image) }}" alt="category">
                               {{ $category->name }}
                            </a>
                            @if($category->childCategories)
                                <ul class="header__nav-item-category-submenu">
                                    @foreach($category->childCategories as $subcategory)
                                        <li class="header__category-submenu-item">
                                            <a href="#" class="header__category-submenu-item-link">
                                                <img src="{{ asset($category->icon) }}" style="border-radius: 100%; height: 30px; width: 30px;" /> {{ $subcategory->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="home__slider-items-wrapper">
                @foreach($sliders as $slider)
                <div class="home__slider-item-outer">
                    <img src="{{ asset('/setting/'.$slider->image) }}" alt="image" class="home__slider-item-image">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<!-- /Home Slider -->

<!-- Categoris Slider -->
<section class="categoris-slider-section">
    <div class="container">
        <div class="section-title-outer">
            <h1 class="title">
                Categories
            </h1>
        </div>
        <div class="categoris-items-wrapper owl-carousel">
            @foreach ($categories as $category)
                <a href="{{ url('/products/'.$category->slug) }}" class="categoris-item">
                    <img src="{{ asset('/category/'.$category->image) }}" alt="category" />
                    <h6 class="categoris-name">{{ $category->name }}</h6>
                    <span class="items-number">{{ count($category->products) }} items</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
<!-- /Categoris Slider -->

<!-- Banner -->
<section class="banner-section">
    <div class="container">
        <div class="row">
            @foreach($topBanners as $topBanner)
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="banner-item-outer">
                        <img src="{{ asset('/setting/'.$topBanner->image) }}" alt="banner image" />
                        <div class="banner-content">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- /Banner -->
<!-- Popular Product -->
<section class="product-section">
    <div class="container">
        <div class="section-title-outer">
            <h1 class="title">
                Hot Products
            </h1>
            <a href="{{ url('/hot/product') }}" class="product-view-all-btn">
                View All
            </a>
        </div>
        <div class="product-items-wrapper">
            @foreach ($hot_products as $product)
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
            @endforeach
        </div>
    </div>
</section>
<!-- /Popular Product -->

    <!-- Popular Product -->
    <section class="product-section">
        <div class="container">
            <div class="section-title-outer">
                <h1 class="title">
                    New Arrival
                </h1>
                <a href="{{ url('/new/product') }}" class="product-view-all-btn">
                    View All
                </a>
            </div>
            <div class="product-items-wrapper">
                @foreach ($new_products as $product)
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
                @endforeach
            </div>
        </div>
    </section>
    <!-- /Popular Product -->

    <!-- Popular Product -->
    <section class="product-section">
        <div class="container">
            <div class="section-title-outer">
                <h1 class="title">
                    Regular Products
                </h1>
                <a href="{{ url('/feature/product') }}" class="product-view-all-btn">
                    View All
                </a>
            </div>
            <div class="product-items-wrapper">
                @foreach ($regular_products as $product)
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
                @endforeach
            </div>
        </div>
    </section>
    <!-- /Popular Product -->

     <!-- Popular Product -->
     <section class="product-section">
        <div class="container">
            <div class="section-title-outer">
                <h1 class="title">
                    Discount Products
                </h1>
                <a href="{{ url('/discount/product') }}" class="product-view-all-btn">
                    View All
                </a>
            </div>
            <div class="product-items-wrapper">
                @foreach ($discount_products as $product)
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
                @endforeach
            </div>
        </div>
    </section>
    <!-- /Popular Product -->

    <!-- Bottom Banner -->
    <section class="pb-4">
        <div class="container">
            <div class="slider-item-outer">
                @if(isset($bottomBanner->image) != null)
                <img src="{{ asset('/setting/'.$bottomBanner->image) }}" alt="footer banner image"/>
                @endif
                </div>
            </div>
        </div>
    </section>
    <!-- /Bottom Banner -->
@endsection
