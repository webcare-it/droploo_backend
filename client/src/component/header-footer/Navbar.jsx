import { useContext, useEffect, useRef, useState } from 'react';
import LocalPhoneIcon from '@mui/icons-material/LocalPhone';
import ShoppingCartIcon from '@mui/icons-material/ShoppingCart';
import { Link, NavLink, useNavigate } from 'react-router-dom';
import { Badge, Button, Skeleton } from '@mui/material';
import MenuIcon from '@mui/icons-material/Menu';
import CartDropdown from './CartDropdown';
import MenuSidebar from './MenuSidebar';
import CategoriesList from './CategoriesList';
import HomeIcon from '@mui/icons-material/Home';
import WidgetsIcon from '@mui/icons-material/Widgets';
import LocalMallIcon from '@mui/icons-material/LocalMall';
import {
  useCountCartsQuery,
  useGeneralDataQuery,
  useGetCategoriesQuery,
} from '../../redux/features/api';
import axios from 'axios';
import NavbarSkeleton from '../Skeleton/NavbarSkeleton';
import { DataContext } from '../../provider/ContextProvider';
const Nav = [
  { nav: 'Home', id: 1, route: '/' },
  { nav: 'Campaign', id: 5, route: '/special-campaign' },
  { nav: 'Shop', id: 2, route: '/shop' },
  { nav: 'Contact us', id: 3, route: '/contact-us' },
  { nav: 'Return process', id: 4, route: '/refund' },
];

const Navbar = () => {
  const navigate = useNavigate();
  const { ip } = useContext(DataContext);
  const [isOpenCart, setIsOpenCart] = useState(false);
  const [isCategoryOpen, setIsCategoryOpen] = useState(false);
  const [isOpenSidebar, setIsOpenSidebar] = useState(false);

  window.addEventListener('scroll', function () {
    const scrollTop = window.scrollY;
    const fixedNavbar = document.getElementById('fixed-navbar');

    if (scrollTop >= 100) {
      fixedNavbar.classList.remove('hidden');
    } else {
      fixedNavbar.classList.add('hidden');
    }
  });

  const categoriesRef = useRef();

  const handleClickOutside = (event) => {
    if (
      categoriesRef.current &&
      !categoriesRef.current.contains(event.target)
    ) {
      setIsCategoryOpen(false);
    }
  };

  const { data, isLoading } = useGeneralDataQuery();
  const { data: categories } = useGetCategoriesQuery();
  const { data: count } = useCountCartsQuery(ip);

  useEffect(() => {
    if (isCategoryOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    } else {
      document.removeEventListener('mousedown', handleClickOutside);
    }
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [isCategoryOpen]);

  const searchHandler = (e) => {
    e.preventDefault();

    const search = e.target.search.value;
    navigate(`/product-search/${search}`);
  };

  return (
    <>
      {isLoading ? (
        <NavbarSkeleton />
      ) : (
        <header className="">
          {/* Navbar for large device */}
          <nav className="border-y hidden md:block">
            {/* part 1 */}
            <div className="flex items-center justify-between mx-auto container px-4">
              <Link to={'/'}>
                {isLoading ? (
                  <Skeleton width={100} height={60} />
                ) : (
                  <img
                    src={data?.generalData?.logo_url}
                    alt="logo"
                    className="w-32"
                  />
                )}
              </Link>
              <div className="">
                <form
                  className="pt-2 flex items-center mx-auto"
                  onSubmit={searchHandler}
                >
                  <label htmlFor="search" className="sr-only">
                    Search
                  </label>
                  <div className="relative">
                    <input
                      type="text"
                      id="search"
                      className="xl:w-[600px] lg:w-[400px] bg-secondary-50 border border-secondary-300 text-secondary-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 p-2.5"
                      placeholder="Search..."
                      required
                    />
                  </div>
                  <button
                    type="submit"
                    className="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-primary-500 rounded-lg border border-primary-500 hover:bg-primary-600 focus:ring-4 focus:outline-none focus:ring-primary-300"
                  >
                    <svg
                      className="w-4 h-4 me-2"
                      aria-hidden="true"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 20 20"
                    >
                      <path
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                      />
                    </svg>
                    Search
                  </button>
                </form>
                <div className="py-2 hidden lg:block mx-auto">
                  <ul className=" flex items-center justify-start gap-4 text-sm">
                    {categories?.data?.slice(0, 6)?.map((c, index) => (
                      <li key={index}>
                        <Link
                          to={`/product-category/${c?.slug}`}
                          className="hover:text-primary-500"
                        >
                          {c?.name}
                        </Link>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
              <div className="flex items-center justify-between border rounded-md p-2 gap-4">
                <div>
                  <LocalPhoneIcon className="text-primary-500" />
                </div>
                <div>
                  <p>Hotline Number:</p>
                  <Link to={`tel: ${data?.generalData?.phone}`}>
                    {data?.generalData?.phone}
                  </Link>
                </div>
              </div>
              <div
                className="relative inline-block text-left"
                onMouseLeave={() => setIsOpenCart(false)}
              >
                <button onMouseEnter={() => setIsOpenCart(true)}>
                  <Badge badgeContent={count?.data} color="primary">
                    <ShoppingCartIcon />
                  </Badge>
                </button>
                <div className="origin-top-right absolute right-0 w-80 rounded-md shadow-lg bg-white focus:outline-none z-30">
                  <CartDropdown
                    isOpen={isOpenCart}
                    setIsOpen={setIsOpenCart}
                    ip={ip}
                  />
                </div>
              </div>
            </div>
            {/* part 2 */}
            <div className="bg-primary-500 py-3">
              <div className="container mx-auto flex items-center justify-start gap-10 px-2">
                <div
                  className="relative"
                  onMouseLeave={() => setIsCategoryOpen(false)}
                >
                  <Button
                    onMouseEnter={() => setIsCategoryOpen(true)}
                    variant="contained"
                    type="button"
                    color="secondary"
                    className="space-x-20"
                  >
                    <span>Categories</span>
                    <svg
                      className="w-2.5 h-2.5"
                      aria-hidden="true"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 10 6"
                    >
                      <path
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="m1 1 4 4 4-4"
                      />
                    </svg>
                  </Button>

                  <div
                    onMouseLeave={() => setIsCategoryOpen(false)}
                    className="absolute w-full bg-white z-30"
                  >
                    {/* <!-- Dropdown menu --> */}
                    <div>
                      {isCategoryOpen && <CategoriesList isHover={true} />}
                    </div>
                  </div>
                </div>
                <div>
                  <ul className="flex flex-wrap items-center mx-auto gap-5 font-medium">
                    {Nav?.map((n) => (
                      <li key={n?.id}>
                        <NavLink
                          className={({ isActive }) =>
                            isActive
                              ? 'text-primary-600 bg-white px-4 py-2 rounded-md shadow-md '
                              : 'px-4 py-2 rounded-md shadow-md bg-white text-secondary-950'
                          }
                          to={n?.route}
                        >
                          {n?.nav}
                        </NavLink>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            </div>
            {/* part 2  fixed navbar*/}
            <div
              id="fixed-navbar"
              className="bg-primary-500 py-3 fixed top-0 left-0 right-0 hidden z-30"
            >
              <div className="container mx-auto flex items-center justify-between gap-10 px-2.5">
                <div
                  className="relative"
                  onMouseLeave={() => setIsCategoryOpen(false)}
                >
                  <Button
                    onMouseEnter={() => setIsCategoryOpen(true)}
                    variant="contained"
                    type="button"
                    color="secondary"
                    className="space-x-20"
                  >
                    <span>Categories</span>
                    <svg
                      className="w-2.5 h-2.5"
                      aria-hidden="true"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 10 6"
                    >
                      <path
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="m1 1 4 4 4-4"
                      />
                    </svg>
                  </Button>

                  <div
                    onMouseLeave={() => setIsCategoryOpen(false)}
                    className="absolute w-full bg-white"
                  >
                    {/* <!-- Dropdown menu --> */}
                    <div>
                      {isCategoryOpen && <CategoriesList isHover={true} />}
                    </div>
                  </div>
                </div>
                <div>
                  <ul className="flex flex-wrap items-center mx-auto gap-5 font-medium">
                    {Nav?.map((n) => (
                      <li key={n?.id}>
                        <NavLink
                          className={({ isActive }) =>
                            isActive
                              ? 'text-primary-600 bg-white px-4 py-2 rounded-md shadow-md '
                              : 'px-4 py-2 rounded-md shadow-md bg-white text-secondary-950'
                          }
                          to={n?.route}
                        >
                          {n?.nav}
                        </NavLink>
                      </li>
                    ))}
                  </ul>
                </div>
                <div
                  className="relative inline-block text-left"
                  onMouseLeave={() => setIsOpenCart(false)}
                >
                  <button onMouseEnter={() => setIsOpenCart(true)}>
                    <Badge badgeContent={count?.data} color="primary">
                      <ShoppingCartIcon color="secondary" />
                    </Badge>
                  </button>
                  <div className="origin-top-right absolute right-0 w-80 rounded-md shadow-lg bg-white focus:outline-none z-30">
                    <CartDropdown
                      isOpen={isOpenCart}
                      setIsOpen={setIsOpenCart}
                      ip={ip}
                    />
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
                  {isLoading ? (
                    <Skeleton width={100} height={60} />
                  ) : (
                    <img
                      src={data?.generalData?.logo_url}
                      alt="logo"
                      className="w-32"
                    />
                  )}
                </Link>
                <div className="flex items-center gap-4 ">
                  <div
                    className="relative inline-block text-left"
                    onMouseLeave={() => setIsOpenCart(false)}
                  >
                    <button onMouseEnter={() => setIsOpenCart(true)}>
                      <Badge badgeContent={count?.data} color="primary">
                        <ShoppingCartIcon />
                      </Badge>
                    </button>
                    <div className="origin-top-right absolute right-0 w-60 md:w-80 rounded-md shadow-lg bg-white focus:outline-none z-30">
                      <CartDropdown
                        isOpen={isOpenCart}
                        setIsOpen={setIsOpenCart}
                        ip={ip}
                      />
                    </div>
                  </div>
                  <button onClick={() => setIsOpenSidebar(true)}>
                    <MenuIcon />
                  </button>
                </div>
              </div>
              <div className="px-2 pb-2">
                <form
                  className="flex items-center max-w-sm mx-auto"
                  onSubmit={searchHandler}
                >
                  <label htmlFor="search" className="sr-only">
                    Search
                  </label>
                  <div className="relative w-full">
                    <input
                      type="text"
                      id="search"
                      className="bg-secondary-50 border border-secondary-300 text-secondary-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full mx-auto p-2.5"
                      placeholder="Search..."
                      required
                    />
                  </div>
                  <button
                    type="submit"
                    className="p-2.5 ms-2 text-sm font-medium text-white bg-primary-500 rounded-lg border border-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300"
                  >
                    <svg
                      className="w-4 h-4"
                      aria-hidden="true"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 20 20"
                    >
                      <path
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                      />
                    </svg>
                    <span className="sr-only">Search</span>
                  </button>
                </form>
              </div>
            </div>

            {/* fixed */}
            <div className="fixed bottom-0 left-0 right-0 mx-auto bg-secondary-50 z-30 p-2 shadow-md border-t-2">
              <div className="flex items-center justify-between">
                <Link to={'/'} className="mx-auto text-center">
                  <HomeIcon />
                  <p>Home</p>
                </Link>
                <Link to={'/categories'} className="mx-auto text-center">
                  <WidgetsIcon />
                  <p>Categories</p>
                </Link>
                <Link className="mx-auto text-center" to={'/shop'}>
                  <LocalMallIcon />
                  <p>Products</p>
                </Link>
                <Link className="mx-auto text-center" to={'/cart'} title="Cart">
                  <Badge badgeContent={count?.data} color="primary">
                    <ShoppingCartIcon />
                  </Badge>
                  <p>Cart</p>
                </Link>
              </div>
            </div>
            <MenuSidebar isOpen={isOpenSidebar} setIsOpen={setIsOpenSidebar} />
          </nav>
        </header>
      )}
    </>
  );
};

export default Navbar;
