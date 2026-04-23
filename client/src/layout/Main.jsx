import Navbar from '../component/header-footer/Navbar';
import { Outlet } from 'react-router-dom';
import Footer from '../component/header-footer/Footer';
import ChatSpeedDial from '../component/ui/ChatSpeedDial';

const Main = () => {
  return (
    <div className="relative">
      <Navbar />
      <div className="container mx-auto px-2">
        <Outlet />
        <div className="fixed bottom-14 md:bottom-0 z-30 left-[calc(100vw-0px)] 2xl:left-[calc(100vw-100px)]">
          <ChatSpeedDial />
        </div>
      </div>
      <Footer />
    </div>
  );
};

export default Main;
