/* eslint-disable no-constant-condition */
import axios from 'axios';
import React, { useContext, useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import swal from 'sweetalert';
import { useNavigate } from 'react-router-dom';
import DeleteIcon from '@mui/icons-material/Delete';
import { Button, IconButton } from '@mui/material';
import Title from '../component/helmet/Title';
import {
  useAddToCartMutation,
  useDeleteCartMutation,
  useGetCartsProductsQuery,
  useGetCouponsQuery,
  usePlaceOrderMutation,
  useQuantityDecrementMutation,
} from '../redux/features/api';
import toast from 'react-hot-toast';
import Loader from '../component/ui/Loader';
import CheckoutSkeleton from '../component/Skeleton/CheckoutSkeleton';
import { Add, Remove } from '@mui/icons-material';
import { DataContext } from '../provider/ContextProvider';

// checkout page
const Checkout = () => {
  const { ip } = useContext(DataContext);
  const navigate = useNavigate();

  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  const { data: cartProducts, isLoading } = useGetCartsProductsQuery(ip);
  const { data: couponsData, isLoading: couponsLoading } = useGetCouponsQuery();

  const [addToCart] = useAddToCartMutation();
  const [quantityDecrement] = useQuantityDecrementMutation();

  const handleDecrease = async (id) => {
    try {
      await quantityDecrement(id);
    } catch (error) {
      console.error(error);
    }
  };
  const [couponCode, setCouponCode] = useState('');
  const [discountApplied, setDiscountApplied] = useState(false);
  const [discountAmount, setDiscountAmount] = useState(0);
  const [couponId, setCouponId] = useState(null);
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

  //Datalayer Code...
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
      window.dataLayer.push({ ecommerce: null }); // Clear previous ecommerce object to avoid data leakage
      window.dataLayer.push({
        event: 'begin_checkout',
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
  //Datalayer Code...

  const {
    register,
    handleSubmit,
    reset,
    watch,
    setValue,
    formState: { errors },
  } = useForm({
    defaultValues: {
      delivery: '100', // default value for the radio button
    },
  });

  const deliveryValue = watch('delivery'); // watch the delivery field

  const handleDeliveryChange = (event) => {
    setValue('delivery', event.target.value); // set the value of the delivery field
  };
  // place order mutation
  const [confirmOrder, { isLoading: OrderIsLoading }] = usePlaceOrderMutation();

  // products details
  const productsDetails = cartProducts?.data?.carts
    ? cartProducts?.data?.carts?.map((c) => ({
        id: c?.product?.id,
        color: c?.color ? c?.color : null,
        price: c?.price,
        qty: c?.qty,
        size: c?.size ? c?.size : null,
      }))
    : [];

  // Total cards quantity
  const TotalQuantity = cartProducts?.data?.carts
    ? cartProducts?.data?.carts
        ?.reduce((total, currentProduct) => total + currentProduct.qty, 0)
        .toString()
    : 0;

  const onSubmit = async (data) => {
    const subTotal = (
      cartProducts?.data?.subTotal + Number(deliveryValue)
    ).toString();

    const order = {
      ip_address: ip,
      customer_name: data.name,
      customer_phone: data.phone,
      delivery_area: data.delivery,
      customer_address: data.address,
      discount: discountAmount,
      coupon_id: couponId,
      price: subTotal,
      product_quantity: TotalQuantity,
      payment_type: 'cod',
      order_type: 'Website',
      products: productsDetails,
    };

    if (productsDetails.length > 0) {
      try {
        const res = await confirmOrder(order);
        if (res?.data?.success === true) {
          const orderId = res?.data?.data?.orderId;
          navigate(`/success-order/${orderId}`);
        }
      } catch (error) {
        console.error(error);
      }
    } else {
      toast.error('Please add to cart products.');
    }
    reset();
  };

  const [deleteCart] = useDeleteCartMutation();

  // carts delete handler
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
        toast.success('Your product is safe!');
      }
    });
  };

  const handleApplyCoupon = () => {
    const coupon = couponsData?.data?.find((c) => c.code === couponCode);
    setCouponId(coupon?.id);
    if (coupon) {
      const discountRate = coupon?.discount_value;
      const discountType = coupon?.discount_type;
      const subTotal = cartProducts?.data?.subTotal || 0;
      if (discountType === 'percentage') {
        const calculatedDiscount = subTotal * (coupon?.discount_value / 100);
        setDiscountAmount(calculatedDiscount);
      } else if (discountType === 'fixed') {
        setDiscountAmount(discountRate);
      }

      setDiscountApplied(true);
      toast.success('Coupon applied successfully!');
      setCouponCode('');
    } else {
      setDiscountAmount(0);
      setDiscountApplied(false);
      toast.error('Invalid coupon code');
      setCouponCode('');
    }
  };

  return (
    <>
      {isLoading || couponsLoading ? (
        <CheckoutSkeleton />
      ) : (
        <>
          <section className="container mx-auto px-2 lg:px-20 my-10 lg:py-20 mt-14 md:mt-0">
            <Title title={'Checkout'} content={'This is checkout page.'} />
            <form
              onSubmit={handleSubmit(onSubmit)}
              className="lg:flex justify-between items-start gap-5 mx-auto min-h-full"
              id="checkout_form"
            >
              <div className="w-full xl:w-2/3 rounded-md bg-white shadow-xl py-2 lg:py-10 px-2 lg:px-6 my-6 lg:my-0 h-full">
                {/* <div className="my-2">
            <p className="font-semibold text-center">
              আপনার পছন্দের পণ্যটি অর্ডার করুন কোনো অগ্রিম মূল্য ছাড়াই। পণ্য
              হাতে পাওয়ার পরই ক্যাশ অন ডেলিভারিতে মূল্য পরিশোধ করুন!
            </p>
          </div> */}
                <h3 className="text-xl lg:text-2xl font-semibold py-2">
                  Billing / Shipping Details
                </h3>
                <div className="lg:flex justify-between items-center gap-4 my-4">
                  <div className="w-full">
                    <input
                      type="text"
                      name="name"
                      placeholder="Enter full Name*"
                      className="bg-primary-50 lg:py-4 w-full border border-primary-100 text-secondary-950 rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5"
                      {...register('name', { required: true })}
                      aria-invalid={errors.name ? 'true' : 'false'}
                    />
                    <p className="py-1">
                      {errors.name?.type === 'required' && (
                        <small role="alert" className="text-danger">
                          name is required
                        </small>
                      )}
                    </p>
                  </div>
                  <div className="w-full">
                    <input
                      type="tel"
                      name="phone"
                      placeholder="Phone*"
                      className="bg-primary-50 w-full lg:py-4 border border-primary-100 text-secondary-950 rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5"
                      {...register('phone', { required: true })}
                    />
                    <p className="py-1">
                      {errors.phone?.type === 'required' && (
                        <small role="alert" className="text-danger">
                          Phone is required
                        </small>
                      )}
                    </p>
                  </div>
                </div>
                <div className="my-4">
                  <textarea
                    name="address"
                    cols="30"
                    rows="7"
                    placeholder="Enter your full Address*"
                    className="bg-primary-50 w-full lg:py-4 border border-primary-100 text-secondary-950 rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5"
                    {...register('address', { required: true })}
                    aria-invalid={errors.address ? 'true' : 'false'}
                  ></textarea>
                  <p className="py-1">
                    {errors.address?.type === 'required' && (
                      <small role="alert" className="text-danger">
                        Address is required
                      </small>
                    )}
                  </p>
                </div>
                <div className="my-4">
                  <div className="mb-2 space-x-2">
                    <input
                      type="radio"
                      value="60"
                      name="delivery"
                      id="inside"
                      className="defaultChecked"
                      {...register('delivery', { required: true })}
                      defaultChecked
                      onChange={handleDeliveryChange}
                    />
                    <label htmlFor="inside">Inside Dhaka(60 TK.)</label>
                  </div>
                  <div className="space-x-2">
                    <input
                      type="radio"
                      name="delivery"
                      value="120"
                      id="outside"
                      {...register('delivery', { required: true })}
                      onChange={handleDeliveryChange}
                    />
                    <label htmlFor="outside">Outside Dhaka(120 TK.)</label>
                  </div>
                </div>
              </div>
              <div className="w-full xl:w-1/3 bg-white rounded-md shadow-xl p-2 lg:p-6 h-fit">
                <div className="overflow-y-scroll h-[185px]">
                  {cartProducts?.data?.carts?.length > 0 ? (
                    <>
                      {cartProducts?.data?.carts?.map((cProducts, index) => (
                        <div
                          className="flex justify-between items-center mx-auto gap-4 my-2 border border-primary-500 rounded"
                          key={index}
                        >
                          <div>
                            <img
                              src={cProducts?.product?.imageUrl}
                              alt={cProducts?.product?.name}
                              className="w-20"
                            />
                          </div>
                          <div className="w-72">
                            <h4 className="block lg:hidden">
                              {cProducts?.product?.name.slice(0, 25)}...
                            </h4>
                            <h4 className="hidden lg:block">
                              {cProducts?.product?.name.slice(0, 25)}
                            </h4>
                            <div>
                              <p className="text-primary-500 font-medium">
                                {cProducts?.price} TK.
                              </p>
                              <div className="flex items-center justify-center space-x-2">
                                {/* Decrease Button */}
                                <IconButton
                                  color="primary"
                                  size="small"
                                  onClick={() => handleDecrease(cProducts?.id)}
                                >
                                  <Remove />
                                </IconButton>

                                {/* Quantity Display */}
                                <p className="py-2 w-16 border border-primary text-center rounded-md text-md">
                                  {cProducts?.qty}
                                </p>

                                {/* Increase Button */}
                                <IconButton
                                  color="primary"
                                  size="small"
                                  onClick={() => handleIncrease(cProducts?.id)}
                                >
                                  <Add />
                                </IconButton>
                              </div>
                              <div className="flex items-center gap-2 mx-auto">
                                <p>
                                  Size:{' '}
                                  {cProducts?.size
                                    ? cProducts?.size
                                    : 'No size'}
                                </p>
                                <p>
                                  Color:
                                  {cProducts?.color
                                    ? cProducts?.color
                                    : 'No color'}
                                </p>
                              </div>
                            </div>
                          </div>
                          <Button
                            variant="text"
                            onClick={() => deleteHandler(cProducts?.id)}
                            className="text-primary-500 active:text-danger"
                          >
                            <DeleteIcon />
                          </Button>
                        </div>
                      ))}
                    </>
                  ) : (
                    "You haven't added any product!"
                  )}
                </div>

                <hr className="border my-2" />
                <div>
                  <div className="flex justify-between my-1 items-center">
                    <p className="font-semibold">Sub Total</p>
                    <p>{cartProducts?.data?.subTotal} Tk.</p>
                  </div>
                  <div className="flex justify-between my-1 items-center">
                    <p className="font-semibold">Delivery Charge</p>
                    <p>{deliveryValue} Tk.</p>
                  </div>
                  {discountApplied && (
                    <div className="flex justify-between my-1 items-center">
                      <p className="font-semibold">Discount</p>
                      <p>{discountAmount} Tk.</p>
                    </div>
                  )}
                  <hr className="border my-2" />
                  <div className="flex justify-between items-center">
                    <p className="font-semibold">Grand Total</p>
                    <p>
                      {(
                        Number(cartProducts?.data?.subTotal) +
                        Number(deliveryValue) -
                        discountAmount
                      ).toFixed()}{' '}
                      Tk.
                    </p>
                  </div>
                  <div className="flex justify-between items-center my-2">
                    <input
                      type="text"
                      placeholder="Coupon Code"
                      value={couponCode}
                      onChange={(e) => setCouponCode(e.target.value)}
                      className="border border-primary-100 rounded p-2 flex-1"
                    />
                    <button
                      type="button"
                      onClick={handleApplyCoupon}
                      className="bg-primary-500 text-white px-4 py-2 rounded ml-2"
                    >
                      Apply
                    </button>
                  </div>
                  <div className="mt-4">
                    <div className="space-x-2">
                      <p className="my-1 font-semibold">
                        Select Payment Method
                      </p>

                      <input
                        type="radio"
                        name="paymentMethod"
                        id=""
                        value="Cash on Delivery"
                        defaultChecked
                      />
                      <label htmlFor="paymentMethod">Cash on delivery</label>
                    </div>
                    <div>
                      {OrderIsLoading ? (
                        <button className="bg-primary-300 py-2 w-full mt-6 text-white rounded-md">
                          <Loader />
                        </button>
                      ) : (
                        <button className="bg-primary-500 py-2 w-full mt-6 text-white rounded-md">
                          Place an Order{' '}
                        </button>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </section>
        </>
      )}
    </>
  );
};

export default Checkout;
