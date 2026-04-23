import React, { useEffect } from 'react';
import DOMPurify from 'dompurify';
import Title from '../../component/helmet/Title';
import { useRefundPolicyQuery } from '../../redux/features/api';

const Refund = () => {
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  const { data } = useRefundPolicyQuery();
  const refund = DOMPurify.sanitize(data?.data?.refund_policy);

  return (
    <section className="my-5 lg:my-10">
      <Title title={'Refund'} content={'This is refund policy page'} />
      <h2 className="text-center text-xl lg:text-4xl font-medium">
        Refund Policy
      </h2>
      <div className="my-10">
        <div dangerouslySetInnerHTML={{ __html: refund }} />
      </div>
    </section>
  );
};

export default Refund;
