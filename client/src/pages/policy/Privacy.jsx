import React, { useEffect } from 'react';
import DOMPurify from 'dompurify';
import Title from '../../component/helmet/Title';
import { usePrivacyPolicyQuery } from '../../redux/features/api';

const Privacy = () => {
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);
  const { data } = usePrivacyPolicyQuery();
  const privacy = DOMPurify.sanitize(data?.data?.privacy_policy);

  return (
    <section className="my-5 lg:my-10">
      <Title title={'Privacy'} content={'This is privacy policy page'} />
      <h2 className="text-center text-xl lg:text-4xl font-medium">
        Privacy Policy
      </h2>
      <div className="my-10">
        <div dangerouslySetInnerHTML={{ __html: privacy }} />
      </div>
    </section>
  );
};

export default Privacy;
