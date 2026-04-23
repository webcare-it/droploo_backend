import React, { useEffect } from 'react';
import DOMPurify from 'dompurify';
import Title from '../component/helmet/Title';
import { useGetAboutQuery } from '../redux/features/api';

const About = () => {
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);
  const { data } = useGetAboutQuery();
  const about = DOMPurify.sanitize(data?.data?.about);

  return (
    // <!-- contact us section -->
    <section className="">
      <Title title={'About us'} content={'This is About us Page '} />
      <h2 className="text-center text-xl lg:text-5xl font-medium my-5 lg:my-10">
        About Us
      </h2>
      <div className="my-10 text-center">
        <div dangerouslySetInnerHTML={{ __html: about }} className="mx-auto" />
      </div>
    </section>
  );
};

export default About;
