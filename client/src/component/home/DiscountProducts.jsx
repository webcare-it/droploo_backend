import React from 'react';
import Products from '../Products';
import HomeProductsSkeleton from '../Skeleton/HomeProductsSkeleton';

const DiscountProducts = ({ products, isLoading }) => {
  return (
    <>
      {isLoading && <HomeProductsSkeleton />}
      {products?.data.length > 0 && (
        <div className="my-5 lg:my-10">
          <h3 className="my-5 lg:my-10 text-center uppercase text-xl lg:text-3xl font-semibold">
            Discount Products.
          </h3>

          <div>
            <Products products={products} />
          </div>
        </div>
      )}
    </>
  );
};

export default DiscountProducts;
