@extends('frontend.v-2.master')
@push('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <!-- Fontawesome -->
    <script src="https://kit.fontawesome.com/942922f9a6.js" crossorigin="anonymous"></script>
    <!-- Slick slider -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        variableproduct: '#38A538',
                    }
                },
            },
        };
    </script>
@endpush

@section('title')
    Product Details
@endsection

@section('content-v2')
    <!-- Product Details -->
    <section class="container mx-auto lg:px-20 px-2 my-6" id="product-section">
        <div class="flex mx-auto justify-between items-start gap-10">
            <div class="lg:w-3/4">
                <div class="lg:flex mx-auto justify-between items-center gap-20">
                    <!-- product slider -->
                    <div id="carousel-product" class="relative w-full sm:w-[600px]">
                        <!-- Carousel wrapper -->
                        <div id="slide" class="relative">
                            @foreach ($details->productImages as $image)
                                <div class="mySlides">
                                    <img src="{{ asset('galleryImage/' . $image->gallery_image) }}">
                                </div>
                            @endforeach

                            <!-- navigation -->
                            <div class="absolute inset-x-0 top-52 flex justify-center">
                                <div class="flex items-center justify-between w-full max-w-screen-xl">
                                    <button class="prev bg-black text-white rounded-l p-2 md:p-3 lg:p-4"
                                        onclick="plusSlides(-1)">&#10094;</button>
                                    <button class="next bg-black text-white rounded-r p-2 md:p-3 lg:p-4"
                                        onclick="plusSlides(1)">&#10095;</button>
                                </div>
                            </div>
                            <!-- Thumbnail images -->
                            <div class="flex items-center">
                                @foreach ($details->productImages as $image)
                                    <div class="column">
                                        <img class="thumbnail cursor w-20"
                                            src="{{ asset('galleryImage/' . $image->gallery_image) }}"
                                            onclick="currentSlide({{ $loop->index + 1 }})" alt="gallery_image">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- Product information -->
                    <div class="lg:w-1/2" id="">
                        <h2 class="text-xl lg:text-2xl font-semibold lg:text-left lg:text-left">
                            {{ $details->name }}
                        </h2>

                        <form action="{{url('/add/to/cart/variable-details/page/'.$details->id)}}" id="addToCartForm" method="POST">
                          @csrf
                            <div class="my-4 flex items-center justify-center md:justify-start gap-6" id="buttonGroup">
                                @foreach ( $details->productImages as $image )
                                @if ($image->color != null)
                                <button id="button" type="button" class="px-4 rounded-md py-2 bg-gray-300" onclick="currentSlide({{$loop->index+1}}),ProductColor('{{$image->color}}')">{{$image->color}}</button>
                                @endif
                                @endforeach
                                <input type="hidden" name="inputcolor" id="inputcolor" value="">
                            </div>

                            <div id="size" class="hidden sizeButtonGroups">
                                <div class="flex items-center justify-center lg:justify-start gap-2 lg:gap-4">
                                    @foreach ( $details->productImages as $image )
                                    <button type="button" class="px-4 rounded-md py-2 bg-gray-300" onclick="productSize({{ $image->price }}, '{{ $image->size }}')">{{ $image->size }}</button>
                                    @endforeach
                                    <input type="hidden" name="inputsize" id="inputsize" value="">
                                </div>
                            </div>
                            <div class="text-xl lg:text-3xl my-8 md:flex items-center gap-2 font-medium">
                                <p class="text-variableproduct text-center md:text-left"><span id="price"
                                        style="font-size: 20px">
                                      @if ($details->discount_price != null)
                                        {{$details->discount_price}}
                                      @else
                                      {{$details->regular_price}}
                                      @endif
                                      </span> TK.</p>
                                      @if ($details->discount_price != null)
                                      <input type="hidden" id="inputPrice" name="inputPrice" value="{{$details->discount_price}}">
                                      @else
                                      <input type="hidden" id="inputPrice" name="inputPrice" value="{{$details->regular_price}}">
                                      @endif
                            </div>
                            <div
                                class="my-4 flex items-center justify-center md:justify-start gap-6 mx-auto text-xl font-md">
                                <button type="button" class="quantity-btn border border-variableproduct px-2 py-1 rounded-full"><i
                                        class="fa-solid fa-minus text-variableproduct"></i></button>
                                <div class="quantity-display border-variableproduct border px-10 py-1 rounded-lg">1</div>
                                <input type="hidden" name="inputQty" id="inputQty" value="1">
                                <button type="button" class="quantity-btn border border-variableproduct px-2 py-1 rounded-full"><i
                                        class="fa-solid fa-plus text-variableproduct"></i></button>
                            </div>
                            <div class=" my-4 lg:flex items-center gap-6 font-medium">
                              <input type="hidden" name="button_action" id="buttonAction" value="">
                                {{-- <div class="my-2">
                                    <button class="bg-variableproduct w-full lg:px-4 py-2 rounded-md text-white"><i
                                            class="fa-solid fa-cart-shopping"></i>Add to cart</button>
                                </div> --}}
                                <div class="my-2">
                                    <button onclick="setButtonAction('buyNow')" class="bg-variableproduct w-full lg:px-4 py-2 rounded-md text-white"><i class="fa-solid fa-truck"></i>Order Now</button>
                                </div>
                            </div>
                            <div class="">
                                <button class=" font-medium bg-variableproduct text-white py-2 rounded-md w-full lg:w-60"><i
                                        class="fa-solid fa-phone"></i> For Call : {{$setting->phone}}</button>
                            </div>
                        </form>
                    </div>

                </div>

                <!--Product details Tabs -->
                <div class="my-10 bg-white border border-gray-200 rounded-xl">
                    <!-- Tab button -->
                    <ul class="flex-shrink gap-5 md:flex px-10 mt-6 justify-start items-center text-sm font-medium text-center text-gray-500"
                        id="tabContainer">
                        <li class="my-1">
                            <button
                                class="tab-btn inline-block px-1 lg:px-4 py-3 text-white bg-variableproduct rounded-lg active"
                                data-tab="description">Description</button>
                        </li>
                        {{-- <li class=" my-1">
                            <button class="tab-btn inline-block px-1 lg:px-4 py-3 text-black bg-gray-200 rounded-lg"
                                data-tab="review">Review</button>
                        </li> --}}
                        <li class="my-1">
                            <button class="tab-btn inline-block px-1 lg:px-4 py-3 text-black bg-gray-200 rounded-lg"
                                data-tab="policy">Product Policy</button>
                        </li>
                    </ul>
                    <!--Description Tab content -->
                    <div class="px-2 lg:px-10">
                        <div class="tab-content my-10" id="descriptionTab">
                          {!!$details->long_description!!}
                        </div>
                        <!-- Review tab -->
                        <div class="tab-content my-10 rounded-md" id="reviewTab" style="display: none;">

                        </div>
                        <!-- policy tab -->
                        <div class="tab-content my-10" id="policyTab" style="display: none;">
                          {!!$details->policy!!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category left side related product-->
            {{-- <div class="flex-col w-60 hidden md:block">
                <div
                    class=" bg-white shadow-lg overflow-y-scroll hidden md:block rounded-lg h-28 md:h-[250px] xl:h-[400px]">
                    <p class="px-2 text-xl rounded-md font-medium">Category</p>
                    <ul class="p-2">
                        <!-- dropdown button -->
                        <button id="dropdownRightButton" data-dropdown-trigger="hover" data-dropdown-toggle="electronic"
                            data-dropdown-placement="right"
                            class="text-xs md:text-lg flex gap-4 items-center text-left relative my-2 hover:text-variableproduct">
                            <span><img src="/assets/images/electronic.png" alt="" class="w-10" /></span>
                            <p class="w-32">Electronic</p>
                            <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        </button>
                        <!-- dropdown menu -->
                        <div id="electronic"
                            class="z-10 hidden bg-white rounded-lg shadow w-32 lg:w-60 h-[400px] absolute px-2">
                            <ul class="py-2 text-gray-700 px-4" aria-labelledby="dropdownRightButton">
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Laptop</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Mobile</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Accessories</a>
                                </li>
                            </ul>
                        </div>
                        <button id="dropdownRightButton" data-dropdown-trigger="hover"
                            data-dropdown-toggle="health&beauty" data-dropdown-placement="right"
                            class="text-xs md:text-lg flex gap-4 items-center text-left relative my-2 hover:text-variableproduct">
                            <span><img src="/assets/images/health&beauty.png" alt="" class="w-10" /></span>
                            <p class="w-32">Health & Beauty</p>
                            <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        </button>
                        <div id="health&beauty"
                            class="z-10 hidden bg-white rounded-lg shadow w-32 lg:w-60 h-[400px] absolute px-2">
                            <ul class="py-2 text-gray-700 px-4" aria-labelledby="dropdownRightButton">
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Hair Care</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Personal Care</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Men's Care</a>
                                </li>
                            </ul>
                        </div>
                        <button id="dropdownRightButton" data-dropdown-trigger="hover" data-dropdown-toggle="kitchen"
                            data-dropdown-placement="right"
                            class="text-xs md:text-lg flex items-center gap-4 text-left relative my-2 hover:text-variableproduct">
                            <span><img src="/assets/images/kitchen-tools.png" alt="" class="w-10" /></span>
                            <p class="w-32">Kitchen Tools</p>
                            <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        </button>
                        <div id="kitchen"
                            class="z-10 hidden bg-white rounded-lg shadow w-32 lg:w-60 h-[400px] absolute p-2">
                            <ul class="py-2 text-gray-700 px-4" aria-labelledby="dropdownRightButton">
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Cutter</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Knife</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Blender</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Bag</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Rack</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Gloves</a>
                                </li>
                            </ul>
                        </div>
                        <button id="dropdownRightButton" data-dropdown-trigger="hover" data-dropdown-toggle="lifestyle"
                            data-dropdown-placement="right"
                            class="text-xs md:text-lg flex items-center gap-4 text-left relative my-2 hover:text-variableproduct">
                            <span><img src="/assets/images/lifestyles.png" alt="" class="w-10" /></span>
                            <p class="w-32">Life Style</p>
                            <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        </button>

                        <div id="lifestyle"
                            class="z-10 hidden bg-white rounded-lg shadow w-32 lg:w-60 h-[400px] absolute p-2">
                            <ul class="py-2 text-gray-700 px-4" aria-labelledby="dropdownRightButton">
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Kids</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Men's</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Woman's</a>
                                </li>
                            </ul>
                        </div>
                        <button id="dropdownRightButton" data-dropdown-trigger="hover"
                            data-dropdown-toggle="smartgadgets" data-dropdown-placement="right"
                            class="text-xs md:text-lg flex items-center gap-4 text-left relative my-2 hover:text-variableproduct">
                            <span><img src="/assets/images/gadgets.png" alt="" class="w-10" /></span>
                            <p class="w-32">Smart Gadgets</p>
                            <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        </button>
                        <div id="smartgadgets"
                            class="z-10 hidden bg-white rounded-lg shadow w-32 lg:w-60 h-[400px] absolute p-2">
                            <ul class="py-2 text-gray-700 px-4" aria-labelledby="dropdownRightButton">
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Watch</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Camera</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Fan</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Bluetooth</a>
                                </li>
                            </ul>
                        </div>
                        <button id="dropdownRightButton" data-dropdown-trigger="hover"
                            data-dropdown-toggle="furniture&decor" data-dropdown-placement="right"
                            class="text-xs md:text-lg flex items-center gap-4 text-left relative my-2 hover:text-variableproduct">
                            <span><img src="/assets/images/furniture&decore.png" alt="" class="w-10" /></span>
                            <p class="w-32">Furniture & Decor</p>
                            <svg class="w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        </button>
                        <div id="furniture&decor"
                            class="z-10 hidden bg-white rounded-lg shadow w-32 lg:w-60 h-[400px] absolute p-2">
                            <ul class="py-2 text-gray-700 px-4" aria-labelledby="dropdownRightButton">
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Sofas</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Chair</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Wall Art</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Mirrors</a>
                                </li>
                                <li class="my-2">
                                    <a href="/pages/category/index.html">Lighting</a>
                                </li>
                            </ul>
                        </div>
                    </ul>
                </div>
                <div class="mt-12">
                    <h2 class="bg-white text-xl rounded-md font-medium p-2">Related Products</h2>
                    <div class="bg-white rounded-lg border hover:shadow-lg my-2 px-2">
                        <a href="/pages/product.html">
                            <img src="/assets/images/airpods.jpg" alt="" class="mx-auto w-32 md:w-52">
                            <div>
                                <h5 class="font-medium text-sm tracking-tight text-black">
                                    Liqua Vape Disposable Electric Device 10ML flavor for wonderful quality
                                </h5>
                                <div class="flex items-center gap-5 mt-2">
                                    <p class="font-normal text-variableproduct">400 TK</p>
                                    <del class="font-normal text-gray-400">500 TK</del><br />
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="bg-white rounded-lg border hover:shadow-lg my-2 px-2">
                        <a href="/pages/product.html">
                            <img src="/assets/images/airpods.jpg" alt="" class="mx-auto w-32 md:w-52">
                            <div>
                                <h5 class="font-medium text-sm tracking-tight text-black">
                                    Liqua Vape Disposable Electric Device 10ML flavor for wonderful quality
                                </h5>
                                <div class="flex items-center gap-5 mt-2">
                                    <p class="font-normal text-variableproduct">400 TK</p>
                                    <del class="font-normal text-gray-400">500 TK</del><br />
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="bg-white rounded-lg border hover:shadow-lg my-2 px-2">
                        <a href="/pages/product.html">
                            <img src="/assets/images/airpods.jpg" alt="" class="mx-auto w-32 md:w-52">
                            <div>
                                <h5 class="font-medium text-sm tracking-tight text-black">
                                    Liqua Vape Disposable Electric Device 10ML flavor for wonderful quality
                                </h5>
                                <div class="flex items-center gap-5 mt-2">
                                    <p class="font-normal text-variableproduct">400 TK</p>
                                    <del class="font-normal text-gray-400">500 TK</del><br />
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="bg-white rounded-lg border hover:shadow-lg my-2 px-2">
                        <a href="/pages/product.html">
                            <img src="/assets/images/airpods.jpg" alt="" class="mx-auto w-32 md:w-52">
                            <div>
                                <h5 class="font-medium text-sm tracking-tight text-black">
                                    Liqua Vape Disposable Electric Device 10ML flavor for wonderful quality
                                </h5>
                                <div class="flex items-center gap-5 mt-2">
                                    <p class="font-normal text-variableproduct">400 TK</p>
                                    <del class="font-normal text-gray-400">500 TK</del><br />
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="bg-white rounded-lg border hover:shadow-lg my-2 px-2">
                        <a href="/pages/product.html">
                            <img src="/assets/images/airpods.jpg" alt="" class="mx-auto w-32 md:w-52">
                            <div>
                                <h5 class="font-medium text-sm tracking-tight text-black">
                                    Liqua Vape Disposable Electric Device 10ML flavor for wonderful quality
                                </h5>
                                <div class="flex items-center gap-5 mt-2">
                                    <p class="font-normal text-variableproduct">400 TK</p>
                                    <del class="font-normal text-gray-400">500 TK</del><br />
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div> --}}
        </div>
    </section>
@endsection

@push('script')
    <!-- js -->
    <script src="{{ asset('frontend/v-2/assets/js/playground.js') }}"></script>

    <!-- Slick slider -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <!-- flowbite -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

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
