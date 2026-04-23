import Products from '../../component/Products';
import { Link, useParams } from 'react-router-dom';
import { Typography } from '@mui/material';
import BreadCrumbs from '../../component/BreadCrumbs';
import { useGetProductsSearchByNameQuery } from '../../redux/features/api';

const SearchProducts = () => {
  const { slug } = useParams();
  const breadcrumbs = [
    <Link underline="hover" key="1" className="text-primary-500" to={`/`}>
      Home
    </Link>,
    <Link underline="hover" key="1" className="text-primary-500" to={`/shop`}>
      Shop
    </Link>,
    <Typography key="3" color="text.primary">
      {slug}
    </Typography>,
  ];
  const { data, isLoading } = useGetProductsSearchByNameQuery(slug);

  return (
    <div>
      <div className="my-5">
        <BreadCrumbs breadcrumbs={breadcrumbs} />
      </div>
      <div className="my-5">
        <Products products={data} isLoading={isLoading} />
      </div>
    </div>
  );
};

export default SearchProducts;
