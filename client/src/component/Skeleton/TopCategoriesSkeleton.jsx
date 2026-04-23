import { Swiper, SwiperSlide } from 'swiper/react';
import { Autoplay, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import { Skeleton } from '@mui/material';

const TopCategoriesSkeleton = () => {
  return (
    <div className="my-5 lg:my-10">
      <Skeleton width={200} height={60} className="my-5 lg:my-10 mx-auto" />

      <div>
        <>
          <Swiper
            slidesPerView={2}
            spaceBetween={8}
            pagination={{
              clickable: true,
            }}
            loop={true}
            autoplay={{
              delay: 3000,
              disableOnInteraction: false,
            }}
            breakpoints={{
              // Small mobile devices (portrait)
              480: {
                slidesPerView: 2,
                spaceBetween: 8,
              },
              // Larger mobile devices (landscape)
              640: {
                slidesPerView: 2,
                spaceBetween: 10,
              },
              // Tablets (portrait)
              768: {
                slidesPerView: 3,
                spaceBetween: 10,
              },
              // Tablets (landscape) and small desktops
              1024: {
                slidesPerView: 3,
                spaceBetween: 20,
              },
              // Medium desktops
              1280: {
                slidesPerView: 4.5,
                spaceBetween: 18,
              },
              // Large desktops
              1536: {
                slidesPerView: 5.5,
                spaceBetween: 18,
              },
            }}
            navigation={true}
            modules={[Navigation, Autoplay]}
            className="mySwiper"
          >
            {[1, 2, 3, 4, 5, 6, 7, 8, 9]?.map((c, index) => (
              <SwiperSlide key={index}>
                <div className="border border-primary-500 shadow-primary-300 bg-secondary-50 rounded-md shadow-md p-2 ">
                  <div className="bg-secondary-200 h-60 rounded-md"></div>
                  <div className="text-center py-1">
                    <Skeleton />
                    <Skeleton />
                  </div>
                </div>
              </SwiperSlide>
            ))}
          </Swiper>
        </>
      </div>
    </div>
  );
};

export default TopCategoriesSkeleton;
