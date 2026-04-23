import React from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import 'swiper/css';
import 'swiper/css/pagination';
import { Pagination } from 'swiper/modules';
import CampaignCard from '../ui/CampaignCard';
import { Card } from '@mui/material';
import {
  useGetBannerQuery,
  useGetCampaignListQuery,
} from '../../redux/features/api';

const CampaignBanner = () => {
  const { data } = useGetCampaignListQuery();
  const { data: banner } = useGetBannerQuery();

  return (
    <div>
      <div className="lg:h-72 xl:h-[380px] overflow-y-auto space-y-5 hidden lg:block">
        {data?.data?.length > 0 ? (
          data?.data?.map((item, index) => (
            <CampaignCard key={index} campaign={item} />
          ))
        ) : (
          <>
            {banner?.data?.map((item, index) => (
              <div key={index}>
                <img src={item?.imageUrl} alt="" className="rounded-md h-44" />
              </div>
            ))}
          </>
        )}
      </div>
      <div className="block lg:hidden">
        {data?.data?.length > 0 ? (
          <Swiper
            slidesPerView={1}
            spaceBetween={10}
            pagination={{
              clickable: true,
            }}
            loop={true}
            breakpoints={{
              640: {
                slidesPerView: 2,
                spaceBetween: 20,
              },
              768: {
                slidesPerView: 3,
                spaceBetween: 40,
              },
              1024: {
                slidesPerView: 5,
                spaceBetween: 50,
              },
            }}
            modules={[Pagination]}
            className="mySwiper"
          >
            {data?.data?.map((item, index) => (
              <SwiperSlide key={index}>
                <CampaignCard campaign={item} />
              </SwiperSlide>
            ))}
          </Swiper>
        ) : (
          <div className="flex items-center justify-center gap-2">
            {banner?.data?.map((item, index) => (
              <div className="" key={index}>
                <img src={item?.imageUrl} alt="" className="rounded-md" />
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default CampaignBanner;
