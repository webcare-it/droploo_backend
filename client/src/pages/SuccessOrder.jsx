import React, { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import Title from '../component/helmet/Title';
import { useGetOrderQuery } from '../redux/features/api';

const SuccessOrder = () => {
  const { id } = useParams();

  const { data, isLoading } = useGetOrderQuery(id);

  const [eventFired, setEventFired] = useState(false);

  // eslint-disable-next-line react-hooks/exhaustive-deps
  const transactionDetails = {
    transactionId: id,
    value: data?.data?.order?.price,
    shipping: data?.data?.order?.area,
    items: data?.data?.order?.order_details?.map((order) => ({
      item_name: order.product.name,
      item_id: order.product.id,
      price: order.product.discount_price
        ? order.product.discount_price
        : order.product.regular_price,
      item_brand: 'Unknown',
      item_category: order.product.category.name,
      item_variant: '',
      quantity: order.qty,
    })),
  };

  useEffect(() => {
    window.scrollTo(0, 0);

    if (!eventFired && data) {
      //Datalayer Code..
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
        event: 'purchase',
        ecommerce: {
          transaction_id: transactionDetails.transactionId,
          affiliation: 'Banggomart',
          value: transactionDetails.value,
          tax: 0,
          shipping: transactionDetails.shipping,
          currency: 'BDT',
          coupon: null,
          items: transactionDetails.items,
        },
      });
      //Datalayer Code..

      setEventFired(true); // Mark event as fired
    }
  }, [data, eventFired, transactionDetails]);

  return (
    <>
      {isLoading ? (
        <div>
          <div className="flex h-screen flex-col items-center justify-center bg-white relative overflow-hidden">
            <h1 className="text-4xl md:text-6xl font-bold text-primary-500 my-2">
              Thank You for Your Order!
            </h1>
            <p className="text-xl text-secondary-700 my-1">Your order no is</p>
            <p className="text-xl text-secondary-700 my-2">
              আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে। আমাদের কল সেন্টার থেকে ফোন
              করে আপনার অর্ডারটি কনফার্ম করা হবে।
            </p>
            <Link
              to={'/'}
              className="px-6 py-3 bg-primary-500 text-white rounded-md hover:bg-primary-900 transition duration-300"
            >
              Continue Shopping
            </Link>

            {/* Flower Animation */}
            <div className="absolute inset-0 pointer-events-none overflow-hidden">
              {[...Array(50)].map((_, i) => (
                <img
                  key={i}
                  src="/images/flower.png"
                  alt="flower"
                  className="absolute w-10 h-10 animate-fall"
                  style={{
                    animationDuration: `${Math.random() * 5 + 3}s`,
                    animationDelay: `${Math.random() * 5}s`,
                    left: `${Math.random() * 100}%`,
                    top: `-${Math.random() * 20}vh`,
                    transform: `rotate(${Math.random() * 360}deg)`,
                  }}
                />
              ))}
            </div>
            <div className="bg-primary-500/25 min-h-screen flex items-center justify-center backdrop-blur-lg absolute top-0 left-0 w-screen h-screen z-30"></div>
          </div>
        </div>
      ) : (
        <div className="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-primary-100 to-white relative overflow-hidden">
          <Title
            title={'Thank you'}
            content={'This is success order page of salvia style.'}
          />
          <h1 className="text-4xl md:text-6xl font-bold text-primary-500 my-2">
            Thank You for Your Order!
          </h1>
          <p className="text-xl text-secondary-700 my-1">
            Your order no is {id}
          </p>
          <p className="text-xl text-secondary-700 my-2">
            আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে। আমাদের কল সেন্টার থেকে ফোন
            করে আপনার অর্ডারটি কনফার্ম করা হবে।
          </p>
          <Link
            to={'/'}
            className="px-6 py-3 bg-primary-500 text-white rounded-md hover:bg-primary-900 transition duration-300"
          >
            Continue Shopping
          </Link>

          {/* Flower Animation */}
          <div className="absolute inset-0 pointer-events-none overflow-hidden">
            {[...Array(50)].map((_, i) => (
              <img
                key={i}
                src="/images/flower.png"
                alt="flower"
                className="absolute w-10 h-10 animate-fall"
                style={{
                  animationDuration: `${Math.random() * 5 + 3}s`,
                  animationDelay: `${Math.random() * 5}s`,
                  left: `${Math.random() * 100}%`,
                  top: `-${Math.random() * 20}vh`,
                  transform: `rotate(${Math.random() * 360}deg)`,
                }}
              />
            ))}
          </div>
        </div>
      )}
    </>
  );
};

export default SuccessOrder;
