import React, { useEffect, useState } from 'react';
import Products from '../Products';
import HomeProductsSkeleton from '../Skeleton/HomeProductsSkeleton';
import { useGetTimeQuery } from '../../redux/features/api';

const FlashSale = ({ products, isLoading }) => {
  const [end, setEnd] = useState('');
  const [time, setTime] = useState({});
  const { data, isSuccess } = useGetTimeQuery();

  useEffect(() => {
    if (isSuccess && data?.data?.offer_time[0]) {
      const endTime = new Date(data.data.offer_time[0]).getTime();

      const timerInterval = setInterval(() => {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance < 0) {
          clearInterval(timerInterval);
          setEnd('Sale Ended');
          return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor(
          (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        );
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        setTime({ days, hours, minutes, seconds });
      }, 1000);

      return () => clearInterval(timerInterval); // Clear interval on unmount
    }
  }, [isSuccess, data]);

  return (
    <>
      {isLoading ? (
        <HomeProductsSkeleton />
      ) : (
        <div className={`${end && 'hidden'} mb-5 lg:mb-10`}>
          <div className="flex flex-col lg:flex-row items-center justify-between lg:mb-5 lg:my-10">
            {/* Flash Sale Heading */}
            <h3 className="text-center uppercase text-xl lg:text-4xl font-semibold">
              Flash Sale
            </h3>

            {/* Timer */}
            <div className="flex space-x-2 lg:space-x-4 items-center bg-primary-500 text-white p-2 rounded-md shadow-lg mt-4 lg:mt-0">
              {time ? (
                <div className="flex items-center space-x-2 lg:space-x-4">
                  <div className="flex flex-col items-center border px-4 rounded-md ">
                    <span className="text-lg lg:text-2xl font-bold">
                      {time.days}
                    </span>
                    <span className="text-xs lg:text-sm">Days</span>
                  </div>
                  <div className="flex flex-col items-center border px-4 rounded-md ">
                    <span className="text-lg lg:text-2xl font-bold">
                      {time.hours}
                    </span>
                    <span className="text-xs lg:text-sm">Hours</span>
                  </div>
                  <div className="flex flex-col items-center border px-4 rounded-md ">
                    <span className="text-lg lg:text-2xl font-bold">
                      {time.minutes}
                    </span>
                    <span className="text-xs lg:text-sm">Minutes</span>
                  </div>
                  <div className="flex flex-col items-center border px-4 rounded-md ">
                    <span className="text-lg lg:text-2xl font-bold">
                      {time.seconds}
                    </span>
                    <span className="text-xs lg:text-sm">Seconds</span>
                  </div>
                </div>
              ) : (
                <span className="text-lg font-semibold">Sale Ended</span>
              )}
            </div>
          </div>

          <div className="mt-8 lg:mt-12">
            <Products products={products} />
          </div>
        </div>
      )}
    </>
  );
};

export default FlashSale;
