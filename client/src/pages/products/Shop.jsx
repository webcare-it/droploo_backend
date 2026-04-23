import { useState } from 'react';
import Products from '../../component/Products';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';
import { Link } from 'react-router-dom';
import { Pagination, Stack, Typography } from '@mui/material';
import BreadCrumbs from '../../component/BreadCrumbs';
import Title from '../../component/helmet/Title';
import {
  useGetCategoriesQuery,
  useGetProductsQuery,
} from '../../redux/features/api';
import ProductsSkeleton from '../../component/Skeleton/ProductsSkeleton';
import ShopCategoriesSkeleton from '../../component/Skeleton/ShopCategoriesSkeleton';
import BreadCrumbsSkeleton from '../../component/Skeleton/BreadCrumbsSkeleton';

const breadcrumbs = [
  <Link underline="hover" key="1" className="text-primary-500" to={`/`}>
    Home
  </Link>,
  <Typography key="3" color="text.primary">
    Shop
  </Typography>,
];

const Shop = () => {
  // handle pagination page.
  const [page, setPage] = useState(1);

  const { data } = useGetCategoriesQuery();
  const { data: products, isLoading } = useGetProductsQuery(page);
  const handlePageChange = (event, value) => {
    setPage(value);
    window.scrollTo(0, 0);
  };

  return (
    <div className="">
      <Title title="Shop" content={'This is shop page.'} />
      {/* breadcrumbs */}
      <div className="my-5">
        {isLoading ? (
          <BreadCrumbsSkeleton />
        ) : (
          <BreadCrumbs breadcrumbs={breadcrumbs} />
        )}
      </div>
      {/* categories */}
      {isLoading ? (
        <ShopCategoriesSkeleton />
      ) : (
        <div className="my-5 lg:mb-10 border-b">
          <>
            <Swiper
              slidesPerView={3}
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
                  slidesPerView: 3,
                  spaceBetween: 8,
                },
                // Larger mobile devices (landscape)
                640: {
                  slidesPerView: 4,
                  spaceBetween: 10,
                },
                // Tablets (portrait)
                768: {
                  slidesPerView: 5,
                  spaceBetween: 10,
                },
                // Tablets (landscape) and small desktops
                1024: {
                  slidesPerView: 6,
                  spaceBetween: 20,
                },
                // Medium desktops
                1280: {
                  slidesPerView: 7,
                  spaceBetween: 18,
                },
                // Large desktops
                1536: {
                  slidesPerView: 9,
                  spaceBetween: 18,
                },
              }}
              grabCursor={true}
              modules={[Autoplay]}
              className="mySwiper"
            >
              <SwiperSlide>
                <div className="cursor-grabbing">
                  <Link to={'/shop'}>
                    <img
                      src="/images/all-products.png"
                      alt=""
                      className="w-20 lg:w-32 mx-auto rounded-full border border-primary-300"
                    />
                    <div className="text-center py-1">
                      <h3 className="text-lg font-medium h-12 py-4">
                        All Products
                      </h3>
                    </div>
                  </Link>
                </div>
              </SwiperSlide>

              {data?.data?.map((c) => (
                <SwiperSlide key={c?.id}>
                  <div className="cursor-grabbing">
                    <Link to={`/product-category/${c?.slug}`}>
                      <img
                        src={c?.imageUrl}
                        alt={c?.name}
                        className="size-20 lg:size-32 mx-auto rounded-full border border-primary-300"
                      />
                      <div className="text-center py-1">
                        <h3 className="text-lg font-medium h-12 py-4">
                          {c?.name?.length > 15
                            ? `${c?.name?.slice(0, 15)}...`
                            : c?.name}
                        </h3>
                      </div>
                    </Link>
                  </div>
                </SwiperSlide>
              ))}
            </Swiper>
          </>
        </div>
      )}

      {/* products */}
      <div className="mb-5 lg:mb-10">
        {isLoading ? (
          <ProductsSkeleton />
        ) : (
          <Products products={products?.data} />
        )}
      </div>

      {/* pagination */}
      <div>
        <div className="my-4 mx-auto flex justify-center items-center w-full">
          <Stack spacing={2}>
            <Pagination
              count={products?.data?.last_page}
              page={page}
              onChange={handlePageChange}
              variant="outlined"
              shape="rounded"
              color="primary"
            />
          </Stack>
        </div>
      </div>
    </div>
  );
};

export default Shop;
