<header class="header-section">
    <div class="container">
        <div class="header-top-wrapper">
            {{-- <a href="{{ url('/') }}" class="brand-logo-outer"> --}}
            <a href="https://nittoz.com/" class="brand-logo-outer">
                <img src="{{asset('setting/'.$setting->logo)}}">
            </a>
            <div class="search-form-outer">
                {{-- <form action="{{url('/view/product/search')}}" method="GET" class="form-group search-form">
                    @csrf
                    <input type="text" name="search" class="form-control" placeholder="Search for items...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form> --}}
            </div>
            <div class="header-top-right-outer">
                <div class="res-search">
                    <i class="fas fa-search"></i>
                </div>
                <div class="header-top-right-item dropdown">
                    <div class="header-top-right-item-link">
                        <span class="icon-outer">
                            <i class="fas fa-cart-plus"></i>
                            <span class="count-number">{{$carts->count()}}</span>
                        </span>
                        Cart
                    </div>
                    <div class="cart-items-wrapper">
                        <div class="cart-items-outer">
                            @foreach ($carts as $cart)
                            <div class="cart-item-outer">
                                <a href="#" class="cart-product-image">
                                    <img src="{{asset('product/images/'.$cart->product->image)}}" alt="product">
                                </a>
                                <div class="cart-product-name-price">
                                    <a href="#" class="product-name">
                                        {{$cart->product->name}}
                                    </a>
                                    <span class="product-price">
                                        ৳ {{$cart->price}}
                                    </span>
                                </div>
                                <div class="cart-item-delete">
                                    <a href="{{url('product/delete/form/cart/'.$cart->id)}}" class="delete-btn">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="shopping-cart-footer">
                            <div class="shopping-cart-total">
                                <h4>
                                    Total <span>৳ {{$carts->sum('price')}}</span>
                                </h4>
                            </div>
                            <div class="shopping-cart-button">
                                {{-- <a href="{{url('/user/cart/products')}}" class="view-cart-link">View cart</a> --}}
                                <a href="https://nittoz.com/view-cart" class="view-cart-link">View cart</a>
                                {{-- <a href="{{url('/checkout')}}" class="checkout-link">Checkout</a> --}}
                                <a href="https://nittoz.com/checkout" class="checkout-link">Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header__bottom-wrapper">
        <div class="container">
            <div class="header__bottom-outer">
                <div class="header__category-outer">
                    <div class="header__category-items-wrapper">
                        <div class="header__category-icon-outer">
                            <span>Categories</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="header__category-items-outer">
                            <ul class="header__category-list">
                                @foreach ($categories as $category)
                                    <li class="header__category-list-item item-has-submenu">
                                        {{-- <a href="{{ url('/products/'.$category->slug) }}" class="header__category-list-item-link"> --}}
                                        <a href="https://nittoz.com/products-collection" class="header__category-list-item-link">
                                            <img src="{{ asset('/category/'.$category->image) }}" alt="category">
                                            {{ $category->name }}
                                        </a>
                                        <ul class="header__nav-item-category-submenu">
                                            @foreach($category->subcategories as $subcategory)
                                                <li class="header__category-submenu-item">
                                                    {{-- <a href="{{ url('/subcategory/products/'.$subcategory->slug) }}" class="header__category-submenu-item-link"> --}}
                                                    <a href="https://nittoz.com/products-collection/subcategory-products/all-subcategory" class="header__category-submenu-item-link">    
                                                        {{ $subcategory->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="nav-toggle-btn">
                    <div class="btn-inner"></div>
                </div>
                <div class="header__dynamic-page-wrapper">
                    <ul class="dynamic-page-list">
                        <li class="dynamic-page-list-item">
                            {{-- <a href="{{ url('/') }}" class="dynamic-page-list-item-link"> --}}
                            <a href="https://nittoz.com/" class="dynamic-page-list-item-link">
                                Home
                            </a>
                        </li>
                        <li class="dynamic-page-list-item">
                            {{-- <a href="{{ url('/shops') }}" class="dynamic-page-list-item-link"> --}}
                            <a href="https://nittoz.com/products-collection" class="dynamic-page-list-item-link">    
                                Shop
                            </a>
                        </li>
                        </li>
                        <li class="dynamic-page-list-item">
                            <a href="{{ url('/return/process') }}" class="dynamic-page-list-item-link">
                                Return Process
                            </a>
                        </li>
                        {{-- @foreach ($allPages as $page)
                            <li class="dynamic-page-list-item">
                                <a href="{{ url('/page/products/'. \Illuminate\Support\Str::slug($page->name)) }}" target="_blank" class="dynamic-page-list-item-link">
                                    {{ ucfirst($page?->name) }}
                                </a>
                            </li>
                        @endforeach --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>    
</header>
