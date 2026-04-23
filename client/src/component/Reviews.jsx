/* eslint-disable no-constant-condition */

import StarIcon from '@mui/icons-material/Star';
import { Avatar, Rating } from '@mui/material';

const Reviews = ({ reviews }) => {
  return (
    <div className=" mx-auto px-4">
      <h2 className="text-2xl font-bold mb-4">Customer Reviews</h2>
      {reviews?.length > 0 ? (
        reviews?.map((review, index) => (
          <div
            key={index}
            className="mb-4 p-4 border rounded-lg shadow-md bg-white"
          >
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                <Avatar
                  sx={{ width: 32, height: 32, mr: 1.5, bgcolor: 'grey.300' }}
                >
                  {review?.name?.charAt(0).toUpperCase()}
                </Avatar>
                <h3 className="text-lg font-semibold">{review?.name}</h3>
              </div>
              <div className="flex items-center">
                <Rating
                  name="text-feedback"
                  value={review?.rating}
                  readOnly
                  precision={0.5}
                  emptyIcon={
                    <StarIcon style={{ opacity: 0.55 }} fontSize="inherit" />
                  }
                />
              </div>
            </div>
            <p className="mt-2 text-secondary-700">{review?.message}</p>
          </div>
        ))
      ) : (
        <div>
          <p>There is no reviews</p>
        </div>
      )}
    </div>
  );
};

export default Reviews;
