import { createBrowserRouter } from 'react-router-dom';
import Main from '../layout/Main';
import Error from '../pages/Error';
import Home from '../pages/Home';
import ProductDetails from '../pages/products/ProductDetails';
import VariableProductDetails from '../pages/products/VariableProductDetails';
import Shop from '../pages/products/Shop';
import CategoriesProducts from '../pages/products/CategoriesProducts';
import SearchProducts from '../pages/products/SearchProducts';
import SuccessOrder from '../pages/SuccessOrder';
import ViewCart from '../pages/ViewCart';
import Checkout from '../pages/Checkout';
import Privacy from '../pages/policy/Privacy';
import Payment from '../pages/policy/Payment';
import Refund from '../pages/policy/Refund';
import TermsCondition from '../pages/policy/TermsCondition';
import About from '../pages/About';
import ContactUs from '../pages/ContactUs';
import Categories from '../pages/Categories';
import SubcategoriesProducts from '../pages/products/SubcategoriesProducts';
import SpecialCampaign from '../pages/products/SpecialCampaign';

export const router = createBrowserRouter([
  {
    path: '/',
    element: <Main />,
    errorElement: <Error />,
    children: [
      {
        path: '/',
        element: <Home />,
      },
      {
        path: '/shop',
        element: <Shop />,
      },
      {
        path: '/special-campaign',
        element: <SpecialCampaign />,
      },
      {
        path: '/categories',
        element: <Categories />,
      },
      {
        path: '/product-category/:slug',
        element: <CategoriesProducts />,
      },
      {
        path: '/product-subcategory/:slug',
        element: <SubcategoriesProducts />,
      },
      {
        path: '/product-search/:slug',
        element: <SearchProducts />,
      },
      {
        path: '/product-details/:slug',
        element: <ProductDetails />,
      },
      {
        path: '/variable-details/:slug',
        element: <VariableProductDetails />,
      },
      {
        path: '/cart',
        element: <ViewCart />,
      },
      {
        path: '/checkout',
        element: <Checkout />,
      },
      {
        path: '/success-order/:id',
        element: <SuccessOrder />,
      },
      {
        path: '/about-us',
        element: <About />,
      },
      {
        path: '/contact-us',
        element: <ContactUs />,
      },
      {
        path: '/privacy',
        element: <Privacy />,
      },
      {
        path: '/payment',
        element: <Payment />,
      },
      {
        path: '/refund',
        element: <Refund />,
      },
      {
        path: '/terms-condition',
        element: <TermsCondition />,
      },
    ],
  },
]);
