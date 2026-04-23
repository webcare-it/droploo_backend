import { Swiper, SwiperSlide } from 'swiper/react';
import { Autoplay, Navigation } from 'swiper/modules';
import './categories.css';
import 'swiper/css';
import 'swiper/css/navigation';
import { Link } from 'react-router-dom';
import { useGetCategoriesQuery } from '../../redux/features/api';
import TopCategoriesSkeleton from '../Skeleton/TopCategoriesSkeleton';

const TopCategories = () => {
  const { data, isLoading } = useGetCategoriesQuery();

  if (isLoading) {
    return <TopCategoriesSkeleton />;
  }

  return (
    <div className="my-5 lg:my-10">
      <h3 className="my-5 lg:my-10 text-center uppercase text-xl lg:text-3xl font-semibold">
        Top categories
      </h3>

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
            {data?.data?.map((c, index) => (
              <SwiperSlide key={index}>
                <div className="border my-2 border-primary-50 shadow-primary-300 bg-secondary-50 rounded-md shadow p-2 ">
                  <Link to={`/product-category/${c?.slug}`}>
                    <img
                      src={c?.imageUrl}
                      alt={c?.name}
                      className="w-full h-40 mx-auto rounded-md"
                    />
                    <div className="text-center py-1">
                      <h3 className="text-lg font-medium h-12">{c?.name}</h3>
                      {/* <p className="text-center">
                        {c?.total_products} products
                      </p> */}
                    </div>
                  </Link>
                </div>
              </SwiperSlide>
            ))}
          </Swiper>
        </>
      </div>
    </div>
  );
};

export default TopCategories;
