import { Button } from '@mui/material';
import React from 'react';
import { Link } from 'react-router-dom';
import Title from '../component/helmet/Title';

//* This is Error or 404 page
const Error = () => {
  return (
    <div className="min-h-screen flex items-center justify-center bg-secondary-100 px-4 md:px-0">
      <Title title={'Not found'} content={'This is not found error page'} />
      <div className="bg-white p-8 rounded-lg shadow-lg max-w-md text-center">
        <h1 className="text-4xl font-bold text-primary-500 mb-4">404</h1>
        <h2 className="text-2xl font-semibold mb-4">Page Not Found</h2>
        <p className="text-secondary-700 mb-6">
          Sorry, the page you are looking for does not exist. Please check the
          URL or return to the homepage.
        </p>
        <Link to="/" className="">
          <Button
            variant="contained"
            style={{ color: 'white', backgroundColor: '#f43f5e' }}
          >
            Go to Home
          </Button>
        </Link>
      </div>
    </div>
  );
};

export default Error;
