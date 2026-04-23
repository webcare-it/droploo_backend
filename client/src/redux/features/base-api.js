import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

export const baseApi = createApi({
  reducerPath: 'baseApi',
  baseQuery: fetchBaseQuery({
    baseUrl: 'http://localhost:9001/api',
  }),
  tagTypes: ['products', 'carts', 'orders', 'categories'],
  endpoints: () => ({}),
});
