import React, { useEffect } from 'react';
import DOMPurify from 'dompurify';
import Title from '../../component/helmet/Title';
import { useTermsConditionQuery } from '../../redux/features/api';

const TermsCondition = () => {
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  const { data } = useTermsConditionQuery();
  const terms_condition = DOMPurify.sanitize(data?.data?.terms_condition);

  return (
    <section className="my-5 lg:my-10">
      <Title
        title={'Terms & condition'}
        content={'This is Terms & condition page'}
      />
      <h2 className="text-center text-xl lg:text-4xl font-medium">
        Terms & Condition
      </h2>
      <div className="my-10">
        <div dangerouslySetInnerHTML={{ __html: terms_condition }} />
      </div>
    </section>
  );
};

export default TermsCondition;
