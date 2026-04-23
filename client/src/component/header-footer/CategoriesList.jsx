import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { Card } from '@mui/material';
import { useGetCategoriesQuery } from '../../redux/features/api';

const CategoriesList = ({ isHover }) => {
  const [expandedSlug, setExpandedSlug] = useState(null);
  const { data } = useGetCategoriesQuery();

  return (
    <div className="relative w-full rounded-lg">
      <Card className="h-96 lg:h-72 xl:h-[390px] border border-primary-500">
        <ul className="overflow-y-scroll h-full z-50">
          {data?.data?.map((c, index) => (
            <li
              key={index}
              onMouseEnter={() => setExpandedSlug(c.slug)}
              onMouseLeave={() => setExpandedSlug(null)}
              className="cursor-pointer"
            >
              <div className="flex items-center justify-between px-3 py-2.5 text-secondary-900 hover:text-secondary-50 hover:bg-primary-500 focus:outline-none focus:ring-4 focus:ring-primary-300 font-medium">
                {/* Category name wrapped in Link */}
                <Link to={`/product-category/${c.slug}`} className="flex-1">
                  {isHover
                    ? c.name?.length > 15
                      ? `${c.name.slice(0, 13)}...`
                      : c.name
                    : c.name}
                </Link>

                {/* Show arrow only if subcategories exist */}
                {c.subcategories && c.subcategories.length > 0 && (
                  <svg
                    className={`w-2.5 h-2.5 transform transition-transform ${
                      expandedSlug === c.slug ? 'rotate-180' : ''
                    }`}
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 10 6"
                  >
                    <path
                      stroke="currentColor"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth="2"
                      d="m1 1 4 4 4-4"
                    />
                  </svg>
                )}
              </div>

              {/* Subcategory panel */}
              {expandedSlug === c.slug && c.subcategories && c.subcategories.length > 0 && (
                <Card className="ml-3 mt-1 mb-2 bg-white z-10 overflow-y-scroll max-h-52">
                  <ul>
                    {c.subcategories.map((s, subIndex) => (
                      <li key={subIndex}>
                        <Link
                          to={`/product-subcategory/${s.slug}`}
                          className="text-secondary-900 w-full hover:text-secondary-50 hover:bg-primary-500 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium px-3 py-2 flex items-center justify-between"
                        >
                          <span>{s.name}</span>
                        </Link>
                      </li>
                    ))}
                  </ul>
                </Card>
              )}
            </li>
          ))}
        </ul>
      </Card>
    </div>
  );
};

export default CategoriesList;
