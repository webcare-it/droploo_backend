import { Skeleton } from '@mui/material';
import React from 'react';

const ProductsSkeleton = () => {
  return (
    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 items-center gap-4 mx-auto">
      {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]?.map((p) => (
        <div
          key={p}
          className="border border-primary-500 rounded-md shadow-md p-2 bg-primary-50"
        >
          <div className="bg-secondary-200 animate-pulse w-full h-32 lg:h-44"></div>
          <div>
            <Skeleton />
            <Skeleton />
            <Skeleton />
          </div>
          <div className="my-1">
            <Skeleton height={60} />
          </div>
        </div>
      ))}
    </div>
  );
};

export default ProductsSkeleton;
