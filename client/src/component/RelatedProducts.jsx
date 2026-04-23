import { Badge, Rating } from '@mui/material';
import StarIcon from '@mui/icons-material/Star';
import { Link } from 'react-router-dom';
import QuickOrder from './quickOrder';
import { useEffect, useState } from 'react';
import axios from 'axios';

const RelatedProducts = ({ products }) => {
  const [ip, setIp] = useState();
  useEffect(() => {
    // fetch ip address.
    const fetchIpAddress = async () => {
      try {
        const response = await axios.get('https://api.ipify.org?format=json');

        setIp(response.data.ip);
      } catch (error) {
        console.error('Error fetching IP address:', error);
        setIp('127.0.0.1');
      }
    };
    fetchIpAddress();
  }, []);
  return (
    <>
      {products?.length > 0 ? (
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 items-center gap-4 mx-auto">
          {products?.map((p, index) => (
            <div
              key={index}
              className="border border-primary-500 rounded-md shadow-md p-2 bg-primary-50 relative"
            >
              <Link
                to={`${
                  p?.is_variable === 0
                    ? `/product-details/${p?.slug}`
                    : `/variable-details/${p?.slug}`
                }`}
              >
                <img
                  src={p?.imageUrl}
                  alt={p?.name}
                  className="rounded-md w-full h-32 lg:h-44"
                />
                <div>
                  <h4 className="text-xl font-medium my-1 h-20 lg:h-14">
                    {p?.name.length > 30
                      ? `${p?.name.slice(0, 30)}...`
                      : p?.name}
                  </h4>
                  <Rating
                    name="text-feedback"
                    value={p?.rating}
                    readOnly
                    precision={0.5}
                    emptyIcon={
                      <StarIcon style={{ opacity: 0.55 }} fontSize="inherit" />
                    }
                  />
                  <div>
                    {p?.discount_price ? (
                      <div className="lg:flex items-center gap-4 h-14 lg:h-auto">
                        <p className="text-primary-500 font-semibold text-xl">
                          {p?.discount_price} TK.
                        </p>
                        <p className="line-through text-secondary-500">
                          {p?.regular_price} TK.
                        </p>
                      </div>
                    ) : (
                      <p className="text-primary-500 font-semibold text-xl h-14 lg:h-auto">
                        {p?.regular_price} TK.
                      </p>
                    )}
                  </div>
                </div>
              </Link>
              <div className="my-1">
                <QuickOrder
                  discount_price={p?.discount_price}
                  regular_price={p?.regular_price}
                  id={p?.id}
                  ip={ip}
                />
              </div>
              {p?.product_type && (
                <div className="absolute top-0 left-0 p-2">
                  <span
                    className={`px-2 py-1 text-xs font-semibold rounded-full uppercase ${
                      p?.product_type === 'new'
                        ? 'bg-green-500 text-white'
                        : p?.product_type === 'hot'
                        ? 'bg-red-500 text-white'
                        : p?.product_type === 'discount'
                        ? 'bg-yellow-500 text-black'
                        : 'bg-blue-500 text-white'
                    }`}
                  >
                    {p?.product_type}
                  </span>
                </div>
              )}
            </div>
          ))}
        </div>
      ) : (
        <div className="h-screen text-center ">
          <p className="text-xl lg:text-3xl font-medium">
            There is no products
          </p>
        </div>
      )}
    </>
  );
};

export default RelatedProducts;
