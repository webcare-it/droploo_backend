<?php

use Illuminate\Support\Facades\Route;
use App\Exports\FacebookCatalogExport;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Config cache clear
//Config cache clear
Route::get('clear', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('optimize');

    return redirect()->back()->with('success', 'All cache cleared successfully!');
})->name('clear.cache');

Route::get('migrate', function (){
    \Artisan::call('migrate');
    dd('Migrated');
});

Route::get('/export/facebook-catalog', function () {
    return Excel::download(new FacebookCatalogExport, 'facebook_catalog.csv');
});

Route::get('/sitemap.xml', [App\Http\Controllers\Frontend\FrontendController::class, 'sitemap']);

//==================== Social login ======================//
Route::get('auth/facebook', [App\Http\Controllers\Frontend\FrontendController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [App\Http\Controllers\Frontend\FrontendController::class, 'loginWithFacebook']);

// Route::get('/', [App\Http\Controllers\Frontend\FrontendController::class, 'index']);
Route::get('/', [\App\Http\Controllers\Auth\AdminController::class, 'showAdminLoginForm']);
Route::get('/shops', [App\Http\Controllers\Frontend\FrontendController::class, 'shops']);
Route::get('/search/products', [App\Http\Controllers\Frontend\FrontendController::class, 'allProducts']);
Route::get('/view/product/search', [App\Http\Controllers\Frontend\FrontendController::class, 'showSearchProduct']);
Route::get('/get/search/data', [App\Http\Controllers\Frontend\FrontendController::class, 'searchData']);
Route::get('/product/{slug}', [App\Http\Controllers\Frontend\FrontendController::class, 'productDetails']);
Route::get('/variable-product/{slug}', [App\Http\Controllers\Frontend\FrontendController::class, 'variableProductDetails']);
Route::get('/page-product/{slug}', [App\Http\Controllers\Frontend\FrontendController::class, 'pageProductDetails']);
Route::get('/get/categories', [App\Http\Controllers\Frontend\FrontendController::class, 'categories']);
Route::get('/get/related/products', [App\Http\Controllers\Frontend\FrontendController::class, 'getRelatedProducts']);
Route::get('/page/products/{type}', [App\Http\Controllers\Frontend\FrontendController::class, 'pageProducts']);

//========= Type Categories =================//
Route::get('/{type}/product', [App\Http\Controllers\Frontend\FrontendController::class, 'showTypeProduct']);

//========== Blogs ==========//
Route::get('/all/blogs', [App\Http\Controllers\Frontend\FrontendController::class, 'blogs']);
Route::get('/blog-details/{slug}', [App\Http\Controllers\Frontend\FrontendController::class, 'blogDetails']);

//========== Product Return Process ==========//
Route::get('/return/process', [App\Http\Controllers\Frontend\FrontendController::class, 'productReturnProcess']);
Route::post('/customer/product/return/info', [App\Http\Controllers\Frontend\FrontendController::class, 'productReturnProcessStore']);

//===============Contact us =============//
Route::get('/contact-us', [App\Http\Controllers\Frontend\FrontendController::class, 'contactUs']);
Route::post('/contact/store', [App\Http\Controllers\Frontend\FrontendController::class, 'contact']);
//======= Setting policy =========//
Route::get('/privacy-policy', [App\Http\Controllers\Frontend\FrontendController::class, 'privacyPolicy']);
Route::get('/payment-policy', [App\Http\Controllers\Frontend\FrontendController::class, 'paymentPolicy']);
Route::get('/shipping-policy', [App\Http\Controllers\Frontend\FrontendController::class, 'shippingPolicy']);
Route::get('/return-policy', [App\Http\Controllers\Frontend\FrontendController::class, 'returnPolicy']);
Route::get('/refund-policy', [App\Http\Controllers\Frontend\FrontendController::class, 'refundPolicy']);
Route::get('/about-us', [App\Http\Controllers\Frontend\FrontendController::class, 'aboutUs']);
Route::get('/term-conditions', [App\Http\Controllers\Frontend\FrontendController::class, 'termConditions']);
Route::get('/career', [App\Http\Controllers\Frontend\FrontendController::class, 'career']);
//=============== Order tracking ===============//
Route::get('/order/track', [App\Http\Controllers\Frontend\FrontendController::class, 'checkProductStatusForm']);

Route::post('/product/review', [App\Http\Controllers\Frontend\ReviewController::class, 'productReview']);
Route::get('/product/review/count/{product_id}', [App\Http\Controllers\Frontend\ReviewController::class, 'productReviewCount']);
Route::get('/product/avg/review/{product_id}', [App\Http\Controllers\Frontend\ReviewController::class, 'productDetailsReview']);
Route::get('/product/fivestar/count/{product_id}', [App\Http\Controllers\Frontend\ReviewController::class, 'fiveStarRating']);
Route::get('/product/fourstar/count/{product_id}', [App\Http\Controllers\Frontend\ReviewController::class, 'fourStarRating']);
Route::get('/product/threestar/count/{product_id}', [App\Http\Controllers\Frontend\ReviewController::class, 'threeStarRating']);
Route::get('/product/twostar/count/{product_id}', [App\Http\Controllers\Frontend\ReviewController::class, 'twoStarRating']);
Route::get('/product/onestar/count/{product_id}', [App\Http\Controllers\Frontend\ReviewController::class, 'oneStarRating']);

// Product export route (must be before /products/{slug})
Route::get('/products/export', [App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export')->middleware('isAdmin');

Route::get('/products/{slug}', [App\Http\Controllers\Frontend\FrontendController::class, 'categoryProducts']);
Route::get('/subcategory/products/{slug}', [App\Http\Controllers\Frontend\FrontendController::class, 'subcategoryProducts'])->name('subcategory.products');
Route::get('/feature/products', [App\Http\Controllers\Frontend\FeatureProductController::class, 'index']);
Route::get('/hot/products', [App\Http\Controllers\Frontend\HotProductController::class, 'index']);
Route::get('/discount/products', [App\Http\Controllers\Frontend\DiscountProductController::class, 'index']);
Route::get('/new-arrival/products', [App\Http\Controllers\Frontend\NewArrivalProductController::class, 'index']);
Route::get('/top/products', [App\Http\Controllers\Frontend\TopProductController::class, 'index']);
Route::get('/upcomming/products', [App\Http\Controllers\Frontend\UpcommingProductController::class, 'upcommingProducts']);
//Cart products
Route::get('/user/cart/products', [App\Http\Controllers\Frontend\CartController::class, 'userCartProducts']);
Route::get('/get/customer/products/{id}', [App\Http\Controllers\Frontend\CartController::class, 'getUserCartProducts']);
Route::get('/add/to/cart/{product}/{cart_type}', [App\Http\Controllers\Frontend\CartController::class, 'addToCart']);
Route::post('/add/to/cart/details/page/{id}', [App\Http\Controllers\Frontend\CartController::class, 'addToCartDetailsPage']);
Route::post('/add/to/cart/variable-details/page/{id}', [App\Http\Controllers\Frontend\CartController::class, 'addToCartVariableDetailsPage']);
Route::get('/cart/products', [App\Http\Controllers\Frontend\CartController::class, 'cartProducts']);
Route::get('/get/combo/products', [App\Http\Controllers\Frontend\CartController::class, 'comboProducts']);
Route::get('/cart/products/count', [App\Http\Controllers\Frontend\CartController::class, 'totalCartProducts']);
Route::get('/cart/products/price', [App\Http\Controllers\Frontend\CartController::class, 'totalCartProductsPrice']);
Route::get('/remove/cart/product/{id}', [App\Http\Controllers\Frontend\CartController::class, 'removeCartProduct']);
Route::get('/cart-product-update/{id}', [App\Http\Controllers\Frontend\CartController::class, 'updateCartProduct']);
Route::get('/cart-product-decrement/{id}', [App\Http\Controllers\Frontend\CartController::class, 'decrementCartProduct']);
Route::post('/cart-update/{id}', [App\Http\Controllers\Frontend\CartController::class, 'cartUpdate']);
Route::get('/cart/product/delete/{id}', [App\Http\Controllers\Frontend\CartController::class, 'cartProductDelete']);
Route::get('/district-wise-sub_district/{id}', [App\Http\Controllers\Frontend\CartController::class, 'subDistrictList']);

//Checkout controller
Route::get('/checkout', [App\Http\Controllers\Frontend\CheckoutController::class, 'checkout']);
Route::post('/customer/checkout', [App\Http\Controllers\Frontend\CheckoutController::class, 'customer_checkout'])->name('customer.checkout');
Route::get('/payment', [App\Http\Controllers\Frontend\CheckoutController::class, 'payment_form'])->name('customer.payment.form');
Route::post('/customer/payment', [App\Http\Controllers\Frontend\CheckoutController::class, 'customerPayment'])->name('customer.payment');
Route::get('/order/complete', [App\Http\Controllers\Frontend\CheckoutController::class, 'completeOrder'])->name('complete.order');
Route::post('/customer/order/confirm', [App\Http\Controllers\Frontend\CheckoutController::class, 'customerOrderConfirm']);
Route::get('/product/delete/form/cart/{id}', [App\Http\Controllers\Frontend\CheckoutController::class, 'cartProductDelete']);
Route::get('/order-received/{order_id}', [App\Http\Controllers\Frontend\CheckoutController::class, 'customerOrderThankyou']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
