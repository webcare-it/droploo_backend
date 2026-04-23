import { Box, Card, Typography } from '@mui/material';
import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';

const CampaignCard = ({ campaign }) => {
  const [end, setEnd] = useState('');
  const [time, setTime] = useState({});
  useEffect(() => {
    // if (isSuccess && data?.data?.offer_time[0]) {
    const endTime = new Date(campaign?.remainig_time).getTime();

    const timerInterval = setInterval(() => {
      const now = new Date().getTime();
      const distance = endTime - now;

      if (distance < 0) {
        clearInterval(timerInterval);
        setEnd('Sale Ended');
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor(
        (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
      );
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      setTime({ days, hours, minutes, seconds });
    }, 1000);

    return () => clearInterval(timerInterval); // Clear interval on unmount
    // }
  }, [campaign]);

  return (
    <div>
      <div className="hidden lg:block">
        <Card className="py-5 px-4">
          <Link to={'/special-campaign'}>
            <Box
              sx={{
                display: 'flex',
                justifyContent: 'space-between',
                gap: 1,
              }}
            >
              <div className="space-y-3 w-full">
                <p className="text-xl xl:text-2xl font-medium">
                  {campaign?.title}
                </p>
                <Typography variant="body1" component={'p'}>
                  Campaign starts in
                </Typography>
              </div>
              <div className="grid grid-cols-2 gap-4 items-center justify-center mx-auto w-full">
                <Card className="w-14 h-14 flex items-center justify-center">
                  {time.days}d
                </Card>
                <Card className="w-14 h-14 flex items-center justify-center">
                  {time.hours}h
                </Card>
                <Card className="w-14 h-14 flex items-center justify-center">
                  {time.minutes}m
                </Card>
                <Card className="w-14 h-14 flex items-center justify-center">
                  {time.seconds}s
                </Card>
              </div>
            </Box>
          </Link>
        </Card>
      </div>
      <div className="block lg:hidden">
        <Card className="p-4 mb-4 w-full">
          <Link to={'/special-campaign'}>
            <Box
              sx={{
                display: 'flex',
                justifyContent: 'space-between',
              }}
            >
              <div className=" w-full">
                <p className="text-xl xl:text-2xl mb-8 font-medium">
                  Happy Hour
                </p>
                <Typography variant="body1" component={'p'}>
                  Campaign starts in
                </Typography>
              </div>
              <div className="grid grid-cols-2 gap-2 items-center justify-center mx-auto w-full">
                <Card className="w-14 h-14 flex items-center justify-center">
                  {time.days}d
                </Card>
                <Card className="w-14 h-14 flex items-center justify-center">
                  {time.hours}h
                </Card>
                <Card className="w-14 h-14 flex items-center justify-center">
                  {time.minutes}m
                </Card>
                <Card className="w-14 h-14 flex items-center justify-center">
                  {time.seconds}s
                </Card>
              </div>
            </Box>
          </Link>
        </Card>
      </div>
    </div>
  );
};

export default CampaignCard;
