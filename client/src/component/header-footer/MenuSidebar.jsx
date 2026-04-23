import { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import {
  useGeneralDataQuery,
  useGetCategoriesQuery,
} from '../../redux/features/api';

//* Menu side bar component use for small device users
const MenuSidebar = ({ isOpen, setIsOpen }) => {
  const [category, setCategory] = useState('');

  const { data: generalData } = useGeneralDataQuery();
  const { data: categories } = useGetCategoriesQuery();

  const sidebarRef = useRef();

  // eslint-disable-next-line react-hooks/exhaustive-deps
  const handleClickOutside = (event) => {
    if (sidebarRef.current && !sidebarRef.current.contains(event.target)) {
      setIsOpen(false);
    }
  };

  useEffect(() => {
    if (isOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    } else {
      document.removeEventListener('mousedown', handleClickOutside);
    }
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [isOpen, handleClickOutside]);

  // framer motion animation
  const container = {
    open: {
      x: 0,
      transition: { type: 'Tween', staggerChildren: 0.3, stiffness: 100 },
    },
    closed: {
      x: -300,
      transition: {
        type: 'Tween',
        stiffness: 100,
        staggerChildren: 0.1,
        staggerDirection: -1,
      },
    },
  };

  const item = {
    open: {
      opacity: 1,
      x: 0,
      transition: { type: 'Tween', stiffness: 100 },
    },
    closed: {
      opacity: 0,
      x: -20,
      transition: { type: 'Tween', stiffness: 100 },
    },
  };

  // const { data } = useGetCategoriesQuery();

  const Nav = [
    { nav: 'Home', id: 1, route: '/' },
    { nav: 'Special Campaign', id: 3, route: '/special-campaign' },
    { nav: 'Shop', id: 2, route: '/shop' },
  ];

  return (
    <div>
      {/* Menu sidebar component contents */}
      <motion.div
        initial="closed"
        animate={isOpen ? 'open' : 'closed'}
        variants={container}
        className={`lg:hidden fixed top-0 left-0 w-64 h-screen z-50 `}
        ref={sidebarRef}
      >
        <div className="h-full px-3 py-4 overflow-y-auto bg-secondary-50 shadow-2xl">
          <Link to={'/'} className="mx-auto">
            <img src={generalData?.generalData?.logo_url} alt="logo" />
          </Link>
          <div className="flex items-center justify-between my-2">
            <h3 className="text-xl font-medium my-4">Menu</h3>
          </div>
          <motion.ul className="space-y-2 font-medium">
            {Nav?.map((n, index) => (
              <motion.li
                variants={item}
                key={index}
                onClick={() => setIsOpen(false)}
              >
                <Link
                  className="block py-2 px-3 text-secondary-900 border rounded md:bg-transparent"
                  to={n?.route}
                >
                  {n?.nav}
                </Link>
              </motion.li>
            ))}
            {categories?.data?.map((c) => (
              <motion.li variants={item} key={c?.id}>
                {category === c?.slug ? (
                  <button
                    type="button"
                    className="flex items-center w-full p-2 border text-base text-secondary-900 transition duration-75 rounded group hover:bg-secondary-100  "
                    onClick={() => setCategory('empty')}
                  >
                    <span className="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">
                      {c?.name}
                    </span>
                    <svg
                      className="w-3 h-3"
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
                  </button>
                ) : (
                  <button
                    type="button"
                    className="flex items-center w-full p-2 border text-base text-secondary-900 transition duration-75 rounded group hover:bg-secondary-100  "
                    onClick={() => setCategory(c?.slug)}
                  >
                    <span className="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">
                      {c?.name}
                    </span>
                    <svg
                      className="w-3 h-3"
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
                  </button>
                )}
                <ul
                  id="dropdown-example"
                  className={`${
                    category === c?.slug ? 'block' : 'hidden'
                  } py-2 space-y-2`}
                >
                  <motion.li variants={item} onClick={() => setIsOpen(false)}>
                    <Link
                      to={`/product-category/${c?.slug}`}
                      className="flex items-center w-full p-2 text-secondary-900 transition duration-75 rounded pl-11 group hover:bg-secondary-100  "
                    >
                      {c?.name}
                    </Link>
                  </motion.li>
                  {c?.subcategories?.map((s) => (
                    <motion.li variants={item} key={s?.id}>
                      <Link
                        to={`/product-subcategory/${s?.slug}`}
                        className="flex items-center w-full p-2 text-secondary-900 transition duration-75 rounded pl-11 group hover:bg-secondary-100  "
                      >
                        {s?.name}
                      </Link>
                    </motion.li>
                  ))}
                </ul>
              </motion.li>
            ))}
            <motion.li variants={item} onClick={() => setIsOpen(false)}>
              <Link
                to="/contact-us"
                className="block py-2 px-3 text-secondary-900 border rounded hover:bg-secondary-100 md:hover:bg-transparent md:hover:text-primary-700 md:p-0 "
              >
                Contact Us
              </Link>
            </motion.li>
            <motion.li variants={item} onClick={() => setIsOpen(false)}>
              <Link
                to="/about-us"
                rel="noreferrer"
                className="block py-2 px-3 text-secondary-900 border rounded hover:bg-secondary-100 md:hover:bg-transparent md:hover:text-primary-700 md:p-0 "
              >
                About
              </Link>
            </motion.li>
          </motion.ul>
        </div>
      </motion.div>
    </div>
  );
};

export default MenuSidebar;
