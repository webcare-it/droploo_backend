import { Swiper, SwiperSlide } from 'swiper/react';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';
import { useGetSlidersQuery } from '../../redux/features/api';
import BannerSkeleton from '../Skeleton/BannerSkeleton';
import CategoriesList from '../header-footer/CategoriesList';
import { MdVerifiedUser } from 'react-icons/md';
import { IoIosPricetags } from 'react-icons/io';
import CampaignBanner from './CampaignBanner';

const Banner = () => {
  const { data, isLoading } = useGetSlidersQuery();

  if (isLoading) {
    return <BannerSkeleton />;
  }

  return (
    <div>
      <section className="flex flex-col lg:flex-row items-start justify-between gap-4 py-4 mx-auto">
        {/* Categories List - Hidden on smaller screens */}
        <div className="hidden lg:block lg:w-72 xl:w-1/5">
          <CategoriesList />
        </div>

        {/* Swiper Carousel - Takes full width on smaller screens */}
        <div className="w-full h-full  mx-auto">
          <Swiper
            navigation={true}
            pagination={{
              clickable: true,
            }}
            autoplay={{
              delay: 2500,
              disableOnInteraction: false,
            }}
            modules={[Navigation, Pagination, Autoplay]}
            className="mySwiper"
          >
            {data?.data?.map((slider, index) => (
              <SwiperSlide key={index}>
                <img
                  src={slider?.imageUrl}
                  alt="slider"
                  className="w-full rounded-md object-cover"
                />
              </SwiperSlide>
            ))}
          </Swiper>
        </div>

        {/* Campaign Banner */}
        {/* <div className=" w-full lg:w-72 xl:w-1/5">
          <CampaignBanner />
        </div> */}
      </section>

      {/* customer trust */}
      <div className="mt-10 hidden lg:block bg-primary-300 rounded-md">
        <div className="bg-third p-2 w-full rounded-md flex flex-wrap justify-center gap-4 items-center">
          <div className="flex items-center gap-2 usp-item">
            <MdVerifiedUser className="w-6 h-6 text-primary" />
            <p className="my-1">Safe Payments</p>
          </div>
          <div className="flex items-center justify-center gap-2 usp-item">
            <img
              src="/images/delivery.png"
              alt="delivery icon"
              className="w-4 md:w-6"
            />
            <p className="my-1 text-left">Nationwide Delivery</p>
          </div>
          <div className="flex items-center gap-2 usp-item">
            <img
              src="/images/return.png"
              alt="return icon"
              className="w-4 md:w-6"
            />
            <p className="my-1 text-left">Free & Easy Return</p>
          </div>
          <div className="flex items-center gap-2 usp-item">
            <IoIosPricetags className="w-6 h-6 text-primary" />
            <p className="my-1 text-left">Best Price Guaranteed</p>
          </div>
          <div className="flex items-center gap-2 usp-item">
            <img
              src="/images/authentic.png"
              alt="authentic icon"
              className="w-4 md:w-6"
            />
            <p className="my-1 text-left">100% Authentic Products</p>
          </div>
          <div className="flex items-center gap-2">
            <MdVerifiedUser className="w-6 h-6 text-primary" />
            <p className="my-1 text-left">Verified</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Banner;
