import { useContext, useEffect } from 'react';
import { Link } from 'react-router-dom';
import DeleteIcon from '@mui/icons-material/Delete';
import swal from 'sweetalert';
import { Button } from '@mui/material';
import Title from '../component/helmet/Title';
import { IconButton } from '@mui/material';
import { Add, Remove } from '@mui/icons-material';
import {
  useAddToCartMutation,
  useDeleteCartMutation,
  useGetCartsProductsQuery,
  useQuantityDecrementMutation,
} from '../redux/features/api';
import ViewCartSkeleton from '../component/Skeleton/ViewCartSkeleton';
import toast from 'react-hot-toast';
import { DataContext } from '../provider/ContextProvider';
import YouMayLike from '../component/YouMayLike';

// view cart page.
const ViewCart = () => {
  const { ip } = useContext(DataContext);
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  const { data: cartProducts, isLoading } = useGetCartsProductsQuery(ip);
  const [deleteCart] = useDeleteCartMutation();

  //Data layer Code...
  useEffect(() => {
    if (cartProducts?.data?.carts && Array.isArray(cartProducts.data.carts)) {
      const items = cartProducts.data.carts.map((cartItem) => ({
        item_name: cartItem.product.name,
        item_id: cartItem.product.id,
        price:
          cartItem.product.discount_price || cartItem.product.regular_price,
        item_brand: 'Unknown',
        item_category: cartItem.product.category.name,
        item_variant: '',
        item_list_name: '',
        item_list_id: '',
        index: 0,
        quantity: cartItem.qty,
      }));

      const total_price = cartProducts.data.subTotal;

      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ ecommerce: null });
      window.dataLayer.push({
        event: 'view_cart',
        ecommerce: {
          currency: 'BDT',
          value: total_price,
          items: items,
        },
      });
    } else {
      console.error('Cart data is not an array:', cartProducts);
    }
  }, [cartProducts]);
  //Data layer Code...

  const [addToCart] = useAddToCartMutation();
  const [quantityDecrement] = useQuantityDecrementMutation();

  const handleDecrease = async (id) => {
    try {
      await quantityDecrement(id);
    } catch (error) {
      console.error(error);
    }
  };

  const handleIncrease = async (id) => {
    const product = cartProducts?.data?.carts?.find((p) => p.id === id);

    const payload = {
      ip_address: ip,
      qty: 1,
      price: product?.price,
      color: product?.color,
      size: product?.size,
    };

    try {
      await addToCart({
        data: payload,
        id: product?.product_id,
      }).unwrap();
    } catch (error) {
      console.error(error);
    }
  };

  const deleteHandler = async (id) => {
    swal({
      title: 'Are you sure?',
      text: 'Once deleted, you will not be able to recover this product!',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    }).then(async (willDelete) => {
      if (willDelete) {
        try {
          const res = await deleteCart(id);
          toast.success(res?.data?.message);
        } catch (error) {
          console.error(error);
        }
      } else {
        toast.error('Your product is safe!');
      }
    });
  };

  return (
    <>
      {isLoading ? (
        <ViewCartSkeleton />
      ) : (
        <section className="">
          <Title title="View Cart" content={'This is view cart page.'} />
          <div className="my-5 lg:my-10">
            <p className="font-semibold text-xl lg:text-3xl text-center uppercase">
              View Cart
            </p>
          </div>
          {/* <!-- Table --> */}
          <div>
            <div className="relative overflow-x-auto mt-10 border-y">
              <table className="w-full text-sm text-left rtl:text-right text-base_500">
                <thead className="text-md text-base_700 uppercase bg-base">
                  <tr>
                    <th scope="col" className="px-4 py-4">
                      Image
                    </th>
                    <th scope="col" className="py-4">
                      Product Name
                    </th>
                    <th scope="col" className="p-4">
                      Price
                    </th>
                    <th scope="col" className="px-4 py-4">
                      Quantity
                    </th>
                    <th scope="col" className="p-4">
                      Total
                    </th>
                    <th scope="col" className="p-2">
                      Remove
                    </th>
                  </tr>
                </thead>
                <tbody>
                  {cartProducts?.data?.carts?.length > 0 && (
                    <>
                      {cartProducts?.data?.carts?.map((cProduct, index) => (
                        <tr
                          className="text-black border-b bg-base_300"
                          key={index}
                        >
                          <td className="px-4 py-2">
                            <img
                              src={cProduct?.product?.imageUrl}
                              alt={cProduct?.product?.name}
                              className="w-20 h-20"
                            />
                          </td>
                          <td className="p-2 font-medium text-md">
                            <h2 className="w-44 lg:w-96">
                              {cProduct?.product?.name}
                            </h2>
                          </td>
                          <td className="px-4 py-2 font-medium whitespace-nowrap text-md">
                            {cProduct?.price} TK.
                          </td>
                          <td className="font-medium whitespace-nowrap">
                            <div className="flex items-center justify-center space-x-2">
                              {/* Decrease Button */}
                              <IconButton
                                color="primary"
                                size="small"
                                onClick={() => handleDecrease(cProduct?.id)}
                              >
                                <Remove />
                              </IconButton>

                              {/* Quantity Display */}
                              <p className="py-2 w-16 border border-primary text-center rounded-md text-md">
                                {cProduct?.qty}
                              </p>

                              {/* Increase Button */}
                              <IconButton
                                color="primary"
                                size="small"
                                onClick={() => handleIncrease(cProduct?.id)}
                              >
                                <Add />
                              </IconButton>
                            </div>
                          </td>
                          <td className="px-4 py-2 font-medium whitespace-nowrap text-md">
                            <p className="">
                              {Number(cProduct?.price * cProduct.qty)} TK.
                            </p>
                          </td>
                          <td className="px-6 py-2 text-center">
                            <button
                              onClick={() => deleteHandler(cProduct?.id)}
                              className="text-primary-500 active:text-danger"
                            >
                              <DeleteIcon />
                            </button>
                          </td>
                        </tr>
                      ))}
                    </>
                  )}
                </tbody>
              </table>
              {!cartProducts?.data?.carts && (
                <div className="my-10">
                  <p className="text-center">There is no products</p>
                </div>
              )}
            </div>
            <div className="text-center my-8">
              {cartProducts?.data?.carts?.length === 0 ? (
                <Button
                  className="!bg-lightOrange-500"
                  variant="contained"
                  disabled
                >
                  Proceed to checkout
                </Button>
              ) : (
                <Button className="!bg-lightOrange-500" variant="contained">
                  <Link to="/checkout">Proceed to checkout</Link>
                </Button>
              )}
            </div>
          </div>

          <div>
            <YouMayLike />
          </div>
        </section>
      )}
    </>
  );
};

export default ViewCart;
