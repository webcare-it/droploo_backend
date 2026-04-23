import { Skeleton } from '@mui/material';
import React from 'react';
import { Link } from 'react-router-dom';

const NavbarSkeleton = () => {
  return (
    <header className="animate-pulse">
      {/* Navbar for large device */}
      <nav className="border-y hidden md:block">
        {/* part 1 */}
        <div className="flex items-center justify-between mx-auto container px-4">
          <Link to={'/'}>
            <div className="w-32 h-12 bg-secondary-100 rounded-md"></div>
          </Link>
          <div className="">
            <Skeleton width={100} height={60} />
            <div className="py-2 mx-auto">
              <ul className="flex items-center justify-start gap-4 text-sm">
                {[1, 2, 3, 4, 5, 6]?.map((c, index) => (
                  <li key={index}>
                    <Skeleton width={50} height={30} />
                  </li>
                ))}
              </ul>
            </div>
          </div>
          <div className="flex items-center justify-between border rounded-md p-2 gap-4">
            <div>
              <Skeleton width={20} height={20} />
            </div>
            <div>
              <Skeleton width={50} height={20} />
              <Skeleton width={120} height={20} />
            </div>
          </div>
          <div>
            <button>
              <Skeleton variant="circular" width={40} height={40} />
            </button>
          </div>
        </div>
        {/* part 2 */}
        <div className="bg-primary-200 py-3">
          <div className="container mx-auto flex items-center justify-start gap-10 px-2">
            <div className="relative">
              <div className="flex items-center space-x-2">
                <Skeleton variant="rectangular" width={150} height={36} />
                <Skeleton variant="circular" width={20} height={20} />
              </div>
            </div>
            <div>
              <div>
                <ul className="flex items-center mx-auto gap-5 font-medium">
                  {[1, 2, 3, 4].map((_, index) => (
                    <li key={index}>
                      <Skeleton variant="rounded" width={100} height={36} />
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </div>
        {/* part 2  fixed navbar*/}
        <div
          id="fixed-navbar"
          className="bg-primary-200 py-3 fixed top-0 left-0 right-0 hidden z-30"
        >
          <div className="container mx-auto flex items-center justify-start gap-10 px-2">
            <div className="relative">
              <div className="flex items-center space-x-2">
                <Skeleton variant="rectangular" width={100} height={36} />
                <Skeleton variant="circular" width={20} height={20} />
              </div>
            </div>
            <div>
              <div>
                <ul className="flex items-center mx-auto gap-5 font-medium">
                  {[1, 2, 3, 4].map((_, index) => (
                    <li key={index}>
                      <Skeleton variant="rounded" width={100} height={36} />
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </div>
      </nav>

      {/*Navbar for small device */}
      <nav className="block md:hidden">
        <div className=" bg-white border">
          <div className="w-full flex items-center px-2 py-2 justify-between ">
            <Link to={'/'}>
              <div className="w-32 h-12 bg-secondary-100 rounded-md"></div>
            </Link>
            <div className="flex items-center gap-4 ">
              <button>
                <Skeleton variant="circular" width={40} height={40} />
              </button>
              <button>
                <Skeleton variant="circular" width={40} height={40} />
              </button>
            </div>
          </div>
          <div className="px-2 pb-2">
            <div className="flex items-center max-w-sm mx-auto">
              <div className="relative w-full">
                <Skeleton
                  variant="rectangular"
                  width="100%"
                  height={40}
                  className="rounded-lg"
                />
              </div>
              <div className="ms-2">
                <Skeleton
                  variant="rectangular"
                  width={40}
                  height={40}
                  className="rounded-lg"
                />
              </div>
            </div>
          </div>
        </div>

        {/* fixed */}
        <div className="fixed bottom-0 left-0 right-0 mx-auto bg-secondary-50 z-30 p-2 shadow-md border-t-2">
          <div className="flex items-center justify-between">
            {[1, 2, 3, 4].map((_, index) => (
              <div key={index} className="mx-auto text-center">
                <Skeleton
                  variant="circular"
                  width={24}
                  height={24}
                  className="mx-auto"
                />
                <Skeleton variant="text" width={50} height={20} />
              </div>
            ))}
          </div>
        </div>
      </nav>
    </header>
  );
};

export default NavbarSkeleton;
