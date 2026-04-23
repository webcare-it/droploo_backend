import { createTheme } from '@mui/material/styles';

// material ui custom theme
const theme = createTheme({
  palette: {
    primary: {
      50: '#e6eff7',
      100: '#c1d6eb',
      200: '#9bbde0',
      300: '#75a3d4',
      400: '#4f8ac9',
      500: '#053C6B', // Updated primary color
      600: '#04355f',
      700: '#032b4f',
      800: '#02213f',
      900: '#01172f',
      950: '#000d1f',
      main: '#053C6B', // Main color for primary 500
      contrastText: '#ffffff', // Text color (white) against primary color
    },      
        
    secondary: {
      50: '#f7f8f8',
      100: '#edeef1',
      200: '#d8dbdf',
      300: '#b6bac3',
      400: '#8e95a2',
      500: '#6b7280',
      600: '#5b616e',
      700: '#4a4e5a',
      800: '#40444c',
      900: '#383a42',
      950: '#25272c',
      main: '#ffffff', // main color for secondary 500
      contrastText: '#000000', // text color(white) against secondary color
    },
    error: {
      main: '#f44336',
      contrastText: '#ffffff',
    },
    warning: {
      main: '#ffa726',
      contrastText: '#000000',
    },
    success: {
      main: '#4caf50',
      contrastText: '#ffffff',
    },
    danger: {
      main: '#e11d3f', // Similar to primary[600]
      contrastText: '#ffffff',
    },
    background: {
      default: '#f6f7f9',
      paper: '#ffffff',
    },
  },
  typography: {
    // You can customize typography here if needed
    fontFamily: "'Roboto', 'Helvetica', 'Arial', sans-serif",
  },
});

export default theme;
