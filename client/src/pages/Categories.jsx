import React, { useEffect } from 'react';
import CategoriesComponent from '../component/CategoriesComponent';
import Title from '../component/helmet/Title';
import { useGetCategoriesQuery } from '../redux/features/api';

//* This is categories page of small devices.
const Categories = () => {
  // get categories from backend using RTK query
  const { data, isLoading } = useGetCategoriesQuery();

  // always open page from top
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  return (
    <div>
      {/* React helmet */}
      <Title title={'Categories'} content={'This is all categories page.'} />
      {/* Categories component */}
      <CategoriesComponent categories={data?.data} isLoading={isLoading} />
    </div>
  );
};

export default Categories;
