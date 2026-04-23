import React, { useEffect } from 'react';
import DOMPurify from 'dompurify';
import Title from '../../component/helmet/Title';
import { usePaymentPolicyQuery } from '../../redux/features/api';

const Payment = () => {
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  const { data } = usePaymentPolicyQuery();
  const payment = DOMPurify.sanitize(data?.data?.payment_policy);

  return (
    <section className="my-5 lg:my-10">
      <Title title={'Payment'} content={'This is payment policy page'} />
      <h2 className="text-center text-xl lg:text-4xl font-medium">
        Payment Policy
      </h2>
      <div className="my-10">
        <div dangerouslySetInnerHTML={{ __html: payment }} />
      </div>
    </section>
  );
};

export default Payment;
