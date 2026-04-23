/* eslint-disable no-constant-condition */
import { useContext, useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import DOMPurify from 'dompurify';
import TaskAltIcon from '@mui/icons-material/TaskAlt';
import {
  Box,
  Paper,
  Rating,
  Tab,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableRow,
  Tabs,
  Typography,
} from '@mui/material';
import axios from 'axios';
import CurrencyExchangeIcon from '@mui/icons-material/CurrencyExchange';
import DeliveryDiningIcon from '@mui/icons-material/DeliveryDining';
import PaymentsIcon from '@mui/icons-material/Payments';
import VerifiedIcon from '@mui/icons-material/Verified';
import CancelIcon from '@mui/icons-material/Cancel';
import QuickOrder from '../../component/quickOrder';
import DetailsSkeleton from '../../component/Skeleton/DetailsSkeleton';
import StarIcon from '@mui/icons-material/Star';
import BreadCrumbs from '../../component/BreadCrumbs';
import Reviews from '../../component/Reviews';
import {
  useAddToCartMutation,
  useGeneralDataQuery,
  useGetSpecificProductQuery,
} from '../../redux/features/api';
import toast from 'react-hot-toast';
import Title from '../../component/helmet/Title';
import Loader from '../../component/ui/Loader';
import RelatedProducts from '../../component/RelatedProducts';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Autoplay, Navigation } from 'swiper/modules';
import MagnifyImage from '../../component/MagnifyImage';
import { DataContext } from '../../provider/ContextProvider';
//* Product details page.

const ProductDetails = () => {
  const { slug } = useParams();
  const [image, setImage] = useState();
  // breadcrumbs
  const breadcrumbs = [
    <Link underline="hover" key="1" className="text-primary-500" to={`/`}>
      Home
    </Link>,
    <Typography key="3" color="text.primary">
      {slug}
    </Typography>,
  ];
  // breadcrumbs

  let dataLayer;
  const { ip } = useContext(DataContext);
  const { register, handleSubmit, watch, reset } = useForm();
  const selectedColor = watch('colors');
  const selectedSize = watch('sizes');

  // get specific product by slug from backend.
  const { data, isLoading } = useGetSpecificProductQuery(slug);

  // add to cart mutation.
  const [addToCart, { isLoading: addCartLoading }] = useAddToCartMutation();
  const { data: generalData } = useGeneralDataQuery();

  useEffect(() => {
    window.scrollTo(0, 0);

    setImage(data?.data?.product?.product_images?.[0]?.imageUrl);
  }, [data, slug]);

  //   Datalayer Code...
  useEffect(() => {
    if (data) {
      const productData = data?.data?.product;
      const product_name = productData?.name;
      const product_id = productData?.id;
      const price = productData?.discount_price || productData?.regular_price;
      const category = productData?.category?.name || 'Unknown';

      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ ecommerce: null });
      window.dataLayer.push({
        event: 'view_item',
        ecommerce: {
          currency: 'BDT',
          value: price,
          items: [
            {
              item_name: product_name,
              item_id: product_id,
              price: price,
              item_brand: 'Unknown',
              item_category: category,
              item_variant: '',
              item_list_name: '',
              item_list_id: '',
              index: 0,
              quantity: 1,
            },
          ],
        },
      });
    }
  }, [data]);
  //Datalayer Code...

  // description and policy sanitize.
  const description = DOMPurify.sanitize(data?.data?.product?.long_description);
  const policy = DOMPurify.sanitize(data?.data?.product?.policy);

  //    product price
  const price =
    data?.data?.product?.discount_price || data?.data?.product?.regular_price;

  // product id
  const id = data?.data?.product?.id;

  // Add to cart handler
  const addToCartHandler = async (data) => {
    const qty = 1;
    const size = selectedSize;
    const color = selectedColor;
    const ip_address = ip;

    // add to cart body details.
    const payload = {
      ip_address,
      qty,
      price,
      color,
      size,
    };

    // add to cart
    try {
      const response = await addToCart({ data: payload, id });

      if (response?.data?.success) {
        toast.success(response?.data?.message);
      }

      reset();
    } catch (error) {
      console.error('Error adding to cart:', error);
    }
  };

  // form onSubmit handler.
  const onSubmit = (datalayerData) => {
    // pass data to add to cart handler.
    addToCartHandler(datalayerData);

    //Datalayer Code...
    const productData = data?.data?.product;
    const product_name = productData?.name;
    const product_id = productData?.id;
    const price = productData?.discount_price || productData?.regular_price;
    const category = productData?.category?.name || 'Unknown';

    dataLayer = window.dataLayer || [];
    dataLayer.push({ ecommerce: null });
    dataLayer.push({
      event: 'add_to_cart',
      ecommerce: {
        currency: 'BDT',
        value: price,
        items: [
          {
            item_name: product_name,
            item_id: product_id,
            price: price,
            item_brand: 'Unknown',
            item_category: category,
            item_variant: '',
            item_list_name: '',
            item_list_id: '',
            index: 0,
            quantity: 1,
          },
        ],
      },
    });
    // Datalayer Code...
  };

  // quick order data.
  const quickData = {
    qty: 1,
    size: selectedSize,
    color: selectedColor,
    price: price,
  };

  // Material ui tabs component
  const [value, setValues] = useState(0);

  const handleChange = (event, newValue) => {
    setValues(newValue);
  };

  function TabPanel(props) {
    const { children, value, index, ...other } = props;

    return (
      <div
        role="tabpanel"
        hidden={value !== index}
        id={`simple-tabpanel-${index}`}
        aria-labelledby={`simple-tab-${index}`}
        {...other}
      >
        {value === index && (
          <Box className="p-4">
            <Typography>{children}</Typography>
          </Box>
        )}
      </div>
    );
  }

  function a11yProps(index) {
    return {
      id: `simple-tab-${index}`,
      'aria-controls': `simple-tabpanel-${index}`,
    };
  }
  // Material ui tabs component

  return (
    <div className="overflow-x-hidden">
      <Title title={slug} content={`This is ${slug} product details page.`} />
      {/* <!-- Product Details --> */}

      {isLoading ? (
        // Details page loader skeleton.
        <DetailsSkeleton />
      ) : (
        <section className="container mx-auto px-2 my-6" id="product-section">
          <div className="my-5 hidden md:block">
            <BreadCrumbs breadcrumbs={breadcrumbs} />
          </div>
          <div className="md:flex mx-auto justify-between items-start gap-4 xl:gap-8">
            {/* <!-- product slider --> */}
            <div className="md:w-1/2 relative">
              {/* Main image of product*/}
              <MagnifyImage image={image} />
              {/* image Thumbnails of product*/}
              <Swiper
                slidesPerView={4}
                loop={true}
                spaceBetween={10}
                breakpoints={{
                  640: {
                    slidesPerView: 4,
                    spaceBetween: 10,
                  },
                  768: {
                    slidesPerView: 5,
                    spaceBetween: 15,
                  },
                  1024: {
                    slidesPerView: 8,
                    spaceBetween: 20,
                  },
                }}
                modules={[Navigation, Autoplay]}
                autoplay={{
                  delay: 2500,
                  disableOnInteraction: false,
                }}
                className=" mt-4 space-x-2 mySwiper cursor-pointer"
              >
                {data?.data?.product?.product_images?.map((images, index) => (
                  <SwiperSlide key={index}>
                    <img
                      onClick={() => setImage(images?.imageUrl)}
                      src={images?.imageUrl}
                      alt={data?.data?.product?.name}
                    />
                  </SwiperSlide>
                ))}
              </Swiper>
            </div>

            {/* <!-- Product information --> */}
            <div
              className="w-full md:w-1/2 mx-auto text-center lg:text-left"
              id=""
            >
              <h2 className="text-xl lg:text-2xl font-semibold text-center lg:text-left">
                {data?.data?.product?.name}
              </h2>
              <div className=" my-4  font-medium">
                {data?.data?.product?.discount_price ? (
                  <div className="flex justify-center md:justify-start items-center gap-2">
                    <p className="font-semibold text-primary-500 text-xl lg:text-3xl">
                      TK {data?.data?.product?.discount_price}
                    </p>
                    <p className="font-normal text-secondary-500 line-through">
                      TK {data?.data?.product?.regular_price}
                    </p>
                  </div>
                ) : (
                  <p className="text-primary-500 text-xl lg:text-3xl">
                    TK {data?.data?.product?.regular_price}
                  </p>
                )}
              </div>
              <div className="mb-2">
                {data?.data?.product?.product_code && (
                  <p>Product Code:{data?.data?.product?.product_code} </p>
                )}
                {data?.data?.product?.qty > 0 ? (
                  <p>
                    <TaskAltIcon /> Available in Stock
                  </p>
                ) : (
                  <p>
                    <CancelIcon /> Stock out
                  </p>
                )}
              </div>
              {/* <div className="mb-2">
                <p>Total Sold: 4905 items </p>
              </div> */}
              <div className="">
                <Rating
                  className=""
                  name="text-feedback"
                  value={data?.data?.product?.rating}
                  readOnly
                  size="medium"
                  precision={0.5}
                  emptyIcon={
                    <StarIcon
                      style={{ opacity: 0.55 }}
                      fontSize="inherit"
                      className=""
                    />
                  }
                />
              </div>
              <div>
                {/* Which color and size selected show values. */}
                {selectedColor && (
                  <p>
                    <span className="font-medium">Color:</span> {selectedColor}
                  </p>
                )}
                {selectedSize && (
                  <p>
                    <span className="font-medium">Size:</span> {selectedSize}
                  </p>
                )}
              </div>
              {/* form of color size */}
              <form onSubmit={handleSubmit(onSubmit)}>
                <div
                  className="my-4 flex items-center justify-center md:justify-start gap-6"
                  id="buttonGroup"
                >
                  <div className="flex flex-col">
                    {/* Colors input field*/}
                    {data?.data?.product?.colors && (
                      <div className="flex flex-wrap justify-center md:justify-start items-center my-4 gap-4">
                        {data?.data?.product?.colors
                          ?.filter((c) => c?.color != null)
                          ?.map((c, index) => (
                            <div key={index}>
                              <input
                                type="radio"
                                id={c?.color}
                                value={c?.color}
                                {...register('colors')}
                                className="hidden"
                              />
                              <label
                                htmlFor={c?.color}
                                className={`px-4 py-2 rounded-lg shadow-sm transition-colors duration-300 ease-in-out transform hover:scale-105 cursor-pointer ${
                                  selectedColor === c?.color
                                    ? 'bg-primary-500 text-white border-2 border-primary'
                                    : 'bg-secondary-300 text-secondary-700 border border-secondary-300'
                                }`}
                              >
                                {c?.color}
                              </label>
                            </div>
                          ))}
                      </div>
                    )}

                    {/* Sizes input field */}
                    {data?.data?.product?.sizes && (
                      <div className="flex flex-wrap justify-center md:justify-start items-center my-4 gap-4">
                        {data?.data?.product?.sizes
                          ?.filter((s) => s?.size != null)
                          ?.map((s, index) => (
                            <div key={index}>
                              <input
                                type="radio"
                                id={s?.size}
                                value={s?.size}
                                {...register('sizes')}
                                className="hidden"
                              />
                              <label
                                htmlFor={s?.size}
                                className={`px-4 py-2 rounded-lg shadow-md transition-colors duration-300 ease-in-out transform hover:scale-105 cursor-pointer ${
                                  selectedSize === s?.size
                                    ? 'bg-primary-500 text-white border-2 border-primary'
                                    : 'bg-secondary-300 text-secondary-700 border border-secondary-300'
                                }`}
                              >
                                {s?.size}
                              </label>
                            </div>
                          ))}
                      </div>
                    )}
                  </div>
                </div>

                {/* Add To cart and Quick order button */}
                {data?.data?.product?.qty > 0 ? (
                  <div className=" my-2 lg:flex items-center gap-6 font-medium">
                    <Link to="#" className="my-2 w-full md:w-1/2">
                      <QuickOrder
                        data={quickData}
                        id={id}
                        ip={ip}
                        discount_price={data?.data?.product?.discount_price}
                        regular_price={data?.data?.product?.regular_price}
                      />
                    </Link>
                    {addCartLoading ? (
                      <div className="my-2 w-full md:w-1/2">
                        <button className="bg-primary-300 w-full lg:px-4 py-2 rounded-md text-secondary-50">
                          <Loader />
                        </button>
                      </div>
                    ) : (
                      <div className="my-2 w-full md:w-1/2">
                        <button className="bg-primary-500 w-full lg:px-4 py-2 rounded-md text-secondary-50">
                          Add to cart
                        </button>
                      </div>
                    )}
                  </div>
                ) : (
                  <>
                    {/* stock out  */}
                    <div className="bg-primary-500 text-xl text-center py-2 my-2 rounded-md text-secondary-50">
                      Stock out
                    </div>
                  </>
                )}
              </form>
              {/* Phone */}
              <div className="mx-auto w-full rounded-md bg-primary-500 text-center text-secondary-50 p-3">
                <a
                  href={`tel:${generalData?.generalData?.phone}`}
                  className="font-medium"
                >
                  For Call : {generalData?.generalData?.phone}
                </a>
              </div>

              <div className="my-5">
                <p className="font-semibold text-xl text-center">
                  পণ্যের জন্য এক টাকাও অগ্রিম দিতে হবে না।
                </p>
              </div>

              {/* customer trust */}
              <div className="hidden lg:block">
                <div className="my-5 flex flex-wrap items-center justify-between gap-4">
                  <div className="mx-auto text-center shadow-md p-4 rounded-md border w-full lg:w-72 my-2 xl:my-0">
                    <CurrencyExchangeIcon color="primary" />
                    <h4 className="font-semibold">Money back guaranty!</h4>
                    <p>We Offer competitive price.</p>
                  </div>
                  <div className="mx-auto text-center shadow-md p-4 rounded-md border w-full lg:w-72 my-2 xl:my-0">
                    <DeliveryDiningIcon color="primary" />
                    <h4 className="font-semibold">Fast Delivery</h4>
                    <p>Faster delivery on selected items.</p>
                  </div>
                  <div className="mx-auto text-center shadow-md p-4 rounded-md border w-full lg:w-72 my-2 xl:my-0">
                    <PaymentsIcon color="primary" />
                    <h4 className="font-semibold">Safe payments</h4>
                    <p>Safe payments method</p>
                  </div>
                  <div className="mx-auto text-center shadow-md p-4 rounded-md border w-full lg:w-72 my-2 xl:my-0">
                    <VerifiedIcon color="primary" />
                    <h4 className="font-semibold">100% Authentic products</h4>
                    <p>We provide authentic products.</p>
                  </div>
                </div>
              </div>
              {/* Details of delivery charges. */}
              <div className="my-5">
                <TableContainer component={Paper}>
                  <Table sx={{ minWidth: 250 }} aria-label="simple table">
                    <TableBody>
                      <TableRow
                        sx={{
                          '&:last-child td, &:last-child th': { border: 0 },
                        }}
                        color="secondary"
                      >
                        <TableCell component="th" scope="row">
                          ঢাকায় ডেলিভারি খরচ
                        </TableCell>
                        <TableCell align="right">৳ 60.00</TableCell>
                      </TableRow>
                      <TableRow
                        sx={{
                          '&:last-child td, &:last-child th': { border: 0 },
                        }}
                        color="secondary"
                      >
                        <TableCell component="th" scope="row">
                          ঢাকার বাইরের ডেলিভারি খরচ
                        </TableCell>
                        <TableCell align="right">৳ 120.00</TableCell>
                      </TableRow>
                      <TableRow
                        sx={{
                          '&:last-child td, &:last-child th': { border: 0 },
                        }}
                        color="secondary"
                      >
                        <TableCell component="th" scope="row">
                          বিকাশ ও নগদ (পার্সোনাল) নাম্বার :
                        </TableCell>
                        <TableCell align="right">
                          {generalData?.generalData?.phone}
                        </TableCell>
                      </TableRow>
                    </TableBody>
                  </Table>
                </TableContainer>
              </div>
            </div>
          </div>

          {/* Information tabs */}
          <div className="my-8">
            <Box sx={{ borderBottom: 1, borderColor: 'divider' }}>
              <Tabs
                value={value}
                onChange={handleChange}
                textColor="inherit"
                variant="scrollable"
                scrollButtons="auto"
                aria-label="basic tabs example"
                TabIndicatorProps={{
                  style: {
                    backgroundColor: '#38a538',
                  },
                }}
              >
                <Tab label="Product Details" {...a11yProps(0)} />
                <Tab label="Return Policy" {...a11yProps(1)} />
                <Tab label="Review" {...a11yProps(2)} />
                <Tab label="Video" {...a11yProps(3)} />
              </Tabs>
            </Box>
            {/* Tab content */}
            <TabPanel value={value} index={0}>
              <div dangerouslySetInnerHTML={{ __html: description }} />
            </TabPanel>
            <TabPanel value={value} index={1}>
              <div dangerouslySetInnerHTML={{ __html: policy }} />
            </TabPanel>
            <TabPanel value={value} index={2}>
              <Reviews reviews={data?.data?.product?.reviews} />
            </TabPanel>
            <TabPanel value={value} index={3}>
              {data?.data?.product?.video_link ? (
                <>
                  <iframe
                    src={data?.data?.product?.video_link}
                    title="YouTube video player"
                    frameBorder="0"
                    className="w-full lg:w-96 h-80 lg:h-96"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerPolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                  ></iframe>
                </>
              ) : (
                <p>No video</p>
              )}
            </TabPanel>
          </div>

          {/* Related Products */}
          {data?.data?.related?.length > 0 && (
            <div className="my-8">
              <h4 className="text-xl lg:text-2xl font-bold my-10">
                Related Products
              </h4>
              <div>
                {/* {isLoading && <ProductCardSkeleton />} */}

                <RelatedProducts products={data?.data?.related} />
              </div>
            </div>
          )}
        </section>
      )}
    </div>
  );
};

export default ProductDetails;
