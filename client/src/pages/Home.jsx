import { useEffect } from 'react';
import Banner from '../component/home/Banner';
import NewArrivalProducts from '../component/home/NewArrivalProducts';
import HotProducts from '../component/home/HotProducts';
import DiscountProducts from '../component/home/DiscountProducts';
import TopCategories from '../component/home/TopCategories';
import Title from '../component/helmet/Title';
import { useGetTypeProductsQuery } from '../redux/features/api';
import RegularProducts from '../component/home/RegularProducts';
import FlashSale from '../component/home/FlashSale';

const Home = () => {
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  const { data, isLoading } = useGetTypeProductsQuery();

  const hotProducts = data?.data?.hotProducts;
  const discountProducts = data?.data?.discountProducts;
  const regularProducts = data?.data?.regularProducts;
  const newProducts = data?.data?.newProducts;

  return (
    <div>
      <Title title="Home" content={'This is home page of munaypark.com'} />
      <Banner />
      {/* <TopCategories /> */}
      {/* <FlashSale products={hotProducts} isLoading={isLoading} /> */}
      <HotProducts products={hotProducts} isLoading={isLoading} />
      <DiscountProducts products={discountProducts} isLoading={isLoading} />
      <NewArrivalProducts products={newProducts} isLoading={isLoading} />
      <RegularProducts products={regularProducts} isLoading={isLoading} />
    </div>
  );
};

export default Home;
