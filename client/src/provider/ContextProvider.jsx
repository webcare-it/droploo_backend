import axios from 'axios';
import { createContext, useEffect, useState } from 'react';

export const DataContext = createContext();

const ContextProvider = ({ children }) => {
  const [ip, setIp] = useState(null);
  const [isLoading, setLoading] = useState(false);

  useEffect(() => {
    // Fetch ip address and set ip
    const fetchIpAddress = async () => {
      try {
        const response = await axios.get('https://api.ipify.org?format=json');
        setIp(response.data.ip);
        setLoading(true);
      } catch (error) {
        console.error('Error fetching IP address:', error);
        setIp('127.0.0.1');
      }
    };
    fetchIpAddress();
  }, []);

  return (
    <DataContext.Provider value={{ ip, isLoading }}>
      {children}
    </DataContext.Provider>
  );
};

export default ContextProvider;
