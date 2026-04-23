import { Badge, Rating } from '@mui/material';
import StarIcon from '@mui/icons-material/Star';
import { Link } from 'react-router-dom';
import { useEffect, useState } from 'react';
import axios from 'axios';
import QuickOrder from '../quickOrder';

const SpecialProducts = ({ products }) => {
  const [ip, setIp] = useState();

  useEffect(() => {
    window.scrollTo(0, 0);
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
        <>
          <div className="mb-8 py-6 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg text-center shadow-lg">
            <p className="text-2xl font-semibold text-white drop-shadow-md px-6 max-w-screen-xl mx-auto leading-relaxed">
              🎉 Discover limited-time offers and exclusive deals tailored to
              elevate your shopping experience! Check back often for the latest
              campaigns, and grab the best deals before they’re gone! 🛍️
            </p>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 items-center gap-4 mx-auto">
            {products?.map((p, index) => (
              <div
                key={index}
                className="border border-primary-500 rounded-md shadow-md p-2 bg-primary-50 relative"
              >
                <Link
                  to={`${
                    p?.product?.is_variable === 0
                      ? `/product-details/${p?.product?.slug}`
                      : `/variable-details/${p?.product?.slug}`
                  }`}
                >
                  <img
                    src={p?.product?.imageUrl}
                    alt={p?.product?.name}
                    className="rounded-md w-full h-32 lg:h-44"
                  />
                  <div>
                    <h4 className="text-xl font-medium my-1 h-20 lg:h-14">
                      {p?.product?.name.length > 25
                        ? `${p?.product?.name.slice(0, 25)}...`
                        : p?.product?.name}
                    </h4>
                    <Rating
                      name="text-feedback"
                      value={p?.product?.rating}
                      readOnly
                      precision={0.5}
                      emptyIcon={
                        <StarIcon
                          style={{ opacity: 0.55 }}
                          fontSize="inherit"
                        />
                      }
                    />
                    <div>
                      {p?.product?.discount_price ? (
                        <div className="lg:flex items-center gap-4 h-14 lg:h-auto">
                          <p className="text-primary-500 font-semibold text-xl">
                            {p?.product?.discount_price} TK.
                          </p>
                          <p className="line-through text-secondary-500">
                            {p?.product?.regular_price} TK.
                          </p>
                        </div>
                      ) : (
                        <p className="text-primary-500 font-semibold text-xl h-14 lg:h-auto">
                          {p?.product?.regular_price} TK.
                        </p>
                      )}
                    </div>
                  </div>
                </Link>
                <div className="my-1">
                  <QuickOrder
                    discount_price={p?.product?.discountPrice}
                    regular_price={p?.product?.regular_price}
                    id={p?.product?.id}
                    ip={ip}
                  />
                </div>
                {p?.product?.product_type && (
                  <div className="absolute top-0 left-0 p-2">
                    <span
                      className={`px-2 py-1 text-xs font-semibold rounded-full uppercase ${
                        p?.product?.product_type === 'new'
                          ? 'bg-green-500 text-white'
                          : p?.product?.product_type === 'hot'
                          ? 'bg-red-500 text-white'
                          : p?.product?.product_type === 'discount'
                          ? 'bg-yellow-500 text-black'
                          : 'bg-blue-500 text-white'
                      }`}
                    >
                      {p?.product?.product_type}
                    </span>
                  </div>
                )}
              </div>
            ))}
          </div>
        </>
      ) : (
        <div className="h-96 text-center flex items-center justify-center bg-gray-100">
          <p className="text-xl font-medium text-gray-700 leading-relaxed max-w-lg mx-auto">
            Sorry, there are currently no active campaigns or exclusive deals
            available. Please check back soon for our next round of exciting
            offers and discounts. We can’t wait to help you save on your next
            purchase!
          </p>
        </div>
      )}
    </>
  );
};

export default SpecialProducts;
