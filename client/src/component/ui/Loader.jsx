import { Box, CircularProgress } from '@mui/material';
import React from 'react';

const Loader = () => {
  return (
    <div>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          gap: '5px',
        }}
        spacing={2}
        direction="row"
      >
        <CircularProgress color="primary" size={'30px'} />
        <p>Loading...</p>
      </Box>
    </div>
  );
};

export default Loader;
