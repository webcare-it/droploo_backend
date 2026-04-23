import React from 'react';

const BannerSkeleton = () => {
  return (
    <div>
      <div className="lg:col-span-2 lg:row-span-2 bg-secondary-200 w-full h-[500px] text-white p-6 rounded-lg relative"></div>

      {/* customer trust */}
      <div className="hidden lg:block animate-pulse">
        <div className="my-5 flex flex-wrap items-center justify-between">
          <div className="mx-auto text-center shadow-md p-4 rounded-md h-20 bg-secondary-200 w-full lg:w-72 my-2 xl:my-0"></div>
          <div className="mx-auto text-center shadow-md p-4 rounded-md w-full h-20 bg-secondary-200 lg:w-72 my-2 xl:my-0"></div>
          <div className="mx-auto text-center shadow-md p-4 rounded-md w-full h-20 bg-secondary-200 lg:w-72 my-2 xl:my-0"></div>
          <div className="mx-auto text-center shadow-md p-4 rounded-md w-full h-20 bg-secondary-200 lg:w-72 my-2 xl:my-0"></div>
          <div className="mx-auto text-center shadow-md p-4 rounded-md w-full h-20 bg-secondary-200 lg:w-72 my-2 xl:my-0"></div>
        </div>
      </div>
    </div>
  );
};

export default BannerSkeleton;
