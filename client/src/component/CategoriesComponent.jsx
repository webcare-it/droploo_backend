import React from 'react';
import { Link } from 'react-router-dom';
import AllCategoriesSkeleton from './Skeleton/AllCategoriesSkeleton';

// categories component
const CategoriesComponent = ({ categories, isLoading }) => {
  return (
    <div>
      {isLoading ? (
        // This is loader skeleton of categories
        <AllCategoriesSkeleton />
      ) : (
        // dynamic categories from backend
        <div className="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 items-center justify-center gap-4 mx-auto">
          {categories?.map((c, index) => (
            <div
              className="border border-primary-500 shadow-primary-300 bg-secondary-50 rounded-md shadow-md p-2 "
              key={index}
            >
              <Link to={`/product-category/${c?.slug}`}>
                <img
                  src={c?.imageUrl}
                  alt={c?.name}
                  className="w-full h-40 lg:h-60 mx-auto rounded-md"
                />
                <div className="text-center py-1">
                  <h3 className="text-lg font-medium h-12">{c?.name}</h3>
                  {/* <p className="text-center">
                 {c?.total_products} products
               </p> */}
                </div>
              </Link>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default CategoriesComponent;
