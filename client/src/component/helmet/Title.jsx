import React from 'react';
import { Helmet } from 'react-helmet-async';

const Title = ({ title = 'E-commerce platform', content }) => {
  return (
    <div>
      <Helmet>
        <title>{title} - Droploo</title>

        <meta name="description" content={content} />
      </Helmet>
    </div>
  );
};

export default Title;
