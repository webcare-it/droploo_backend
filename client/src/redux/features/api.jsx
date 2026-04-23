import { baseApi } from './base-api';

export const Api = baseApi.injectEndpoints({
  endpoints: (builder) => ({
    getTypeProducts: builder.query({
      query: () => ({
        url: '/type-products',
        method: 'GET',
      }),
      providesTags: ['products'],
    }),
    getBanner: builder.query({
      query: () => '/home-banner',
    }),
    getProducts: builder.query({
      query: (page) => ({
        url: `/all-products?page=${page}`,
        method: 'GET',
      }),
      providesTags: ['products'],
    }),
    specialCampaignProducts: builder.query({
      query: () => ({
        url: `/campaign-products`,
        method: 'GET',
      }),
      providesTags: ['products'],
    }),
    getCampaignList: builder.query({
      query: () => '/campaign-list',
    }),
    getTime: builder.query({
      query: () => '/offer-time',
    }),
    getCategoryWiseProducts: builder.query({
      query: (slug) => ({
        url: `/filter-category-products/${slug}`,
        method: 'GET',
      }),
      providesTags: ['products'],
    }),
    getSubcategoryWiseProducts: builder.query({
      query: (slug) => ({
        url: `/filter-subcategory-products/${slug}`,
        method: 'GET',
      }),
      providesTags: ['products'],
    }),
    getProductsSearchByName: builder.query({
      query: (slug) => `/product-search/${slug}`,
      providesTags: ['products'],
    }),

    getSpecificProduct: builder.query({
      query: (slug) => `/product/details/${slug}`,
      providesTags: ['products'],
    }),
    getCategories: builder.query({
      query: () => `/categories`,
      providesTags: ['categories'],
    }),
    getCoupons: builder.query({
      query: () => `/coupons`,
      providesTags: ['coupons'],
    }),
    CountCarts: builder.query({
      query: (ip) => `/cart-products-count/${ip}`,
      providesTags: ['carts'],
    }),
    getCartsProducts: builder.query({
      query: (ip) => `/get-cart-products/${ip}`,
      providesTags: ['carts'],
    }),
    generalData: builder.query({
      query: () => '/general-data',
    }),
    getSliders: builder.query({
      query: () => '/home-sliders',
    }),
    getAbout: builder.query({
      query: () => '/about-us',
    }),
    privacyPolicy: builder.query({
      query: () => '/privacy-policy',
    }),
    termsCondition: builder.query({
      query: () => '/terms-conditions',
    }),
    refundPolicy: builder.query({
      query: () => '/refund-policy',
    }),
    paymentPolicy: builder.query({
      query: () => '/payment-policy',
    }),
    getOrder: builder.query({
      query: (orderId) => `/order-details/${orderId}`,
      providesTags: ['orders'],
    }),
    // mutation
    contactForm: builder.mutation({
      query: (data) => ({
        url: '/contact-store',
        method: 'POST',
        body: data,
      }),
    }),
    quantityDecrement: builder.mutation({
      query: (id) => ({
        url: `/decrease-cart/${id}`,
        method: 'GET',
      }),
      invalidatesTags: ['carts'],
    }),
    addToCart: builder.mutation({
      query: ({ data, id }) => ({
        url: `/product/add-to-cart/${id}`,
        method: 'POST',
        body: data,
      }),
      invalidatesTags: ['carts'],
    }),
    placeOrder: builder.mutation({
      query: (data) => ({
        url: '/confirm-order',
        method: 'POST',
        body: data,
      }),
      invalidatesTags: ['carts', 'orders'],
    }),
    deleteCart: builder.mutation({
      query: (id) => `/delete-cart/${id}`,
      invalidatesTags: ['carts'],
    }),
  }),
});

// eslint-disable-next-line react-refresh/only-export-components
export const {
  useGetTypeProductsQuery,
  useGetProductsQuery,
  useGetSlidersQuery,
  useGeneralDataQuery,
  useGetCartsProductsQuery,
  useGetCategoriesQuery,
  useGetCategoryWiseProductsQuery,
  useCountCartsQuery,
  useGetProductsSearchByNameQuery,
  useGetSpecificProductQuery,
  useAddToCartMutation,
  useContactFormMutation,
  usePlaceOrderMutation,
  useDeleteCartMutation,
  useGetAboutQuery,
  useGetSubcategoryWiseProductsQuery,
  usePaymentPolicyQuery,
  usePrivacyPolicyQuery,
  useRefundPolicyQuery,
  useTermsConditionQuery,
  useGetOrderQuery,
  useSpecialCampaignProductsQuery,
  useQuantityDecrementMutation,
  useGetTimeQuery,
  useGetCampaignListQuery,
  useGetBannerQuery,
  useGetCouponsQuery
} = Api;
