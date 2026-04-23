import React, { useEffect } from 'react';
import { useForm } from 'react-hook-form';

import Title from '../component/helmet/Title';
import toast from 'react-hot-toast';
import {
  useContactFormMutation,
  useGeneralDataQuery,
} from '../redux/features/api';
import { Button } from '@mui/material';

const ContactUs = () => {
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);
  const {
    register,
    handleSubmit,
    reset,
    // eslint-disable-next-line no-unused-vars
    formState: { error },
  } = useForm();

  const { data } = useGeneralDataQuery();
  const [contactForm] = useContactFormMutation();

  const onSubmit = async (data) => {
    try {
      const res = await contactForm(data);
      toast.success(res?.data?.message);
      reset();
      // eslint-disable-next-line no-empty
    } catch (error) {}
  };

  return (
    <>
      {/* <!-- contact us section --> */}
      <section className="my-5 lg:my-10">
        <Title title={'Contact us'} content={'This is contact us page.'} />
        <h2 className="text-center text-xl lg:text-5xl font-medium">
          Contact Us
        </h2>
        <div className="grid grid-cols-1 md:grid-cols-3 mx-auto items-center gap-5 my-10">
          <div className="bg-white rounded-md text-center w-full p-6 h-60">
            <img
              src="/assets/images/phone.png"
              alt=""
              className="w-20 mx-auto"
            />
            <p className="text-xl my-2">Phone</p>
            <p>{data?.generalData?.phone}</p>
          </div>
          <div className="bg-white rounded-md text-center w-full p-6 h-60">
            <img
              src="/assets/images/gmail.png"
              alt=""
              className="w-20 mx-auto"
            />
            <p className="text-xl my-2">Email</p>
            <p>{data?.generalData?.email}</p>
          </div>
          <div className="bg-white rounded-md text-center w-full p-6 h-60">
            <img
              src="/assets/images/location-pin.png"
              alt=""
              className="w-20 mx-auto"
            />
            <p className="text-xl my-2">Address</p>
            <p>{data?.generalData?.address}</p>
          </div>
        </div>
        {/* <!-- contact form --> */}
        <form
          action=""
          className="bg-white p-8 rounded-md"
          onSubmit={handleSubmit(onSubmit)}
        >
          <div className="lg:flex items-center justify-between gap-6 mx-auto">
            <div className="w-full my-2">
              <label
                htmlFor="name"
                className="block mb-2 font-medium text-dark"
              >
                Name
              </label>
              <input
                type="text"
                id="name"
                className="bg-primary-50 border border-base_300 text-dark rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5"
                placeholder="Enter your name"
                {...register('name', { required: true })}
              />
            </div>
            <div className="w-full">
              <label
                htmlFor="phone"
                className="block mb-2 font-medium text-dark"
              >
                Phone
              </label>
              <input
                type="tel"
                id="name"
                className="bg-primary-50 border border-base_300 text-dark rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5"
                placeholder="Enter your name"
                {...register('phone', { required: true })}
              />
            </div>
          </div>
          <div className="my-2">
            <label
              htmlFor="message"
              className="block mb-2 font-medium text-dark"
            >
              Message
            </label>
            <textarea
              name="message"
              id=""
              cols="30"
              rows="10"
              placeholder="Type your message"
              className="bg-primary-50 border border-base_300 text-dark rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5"
              {...register('message', { required: true })}
            ></textarea>
          </div>
          <div className="mt-4 text-center" type="button">
            <Button variant="contained">Submit</Button>
          </div>
        </form>
        {/* <!-- map --> */}
        {/* <div className="my-6 p-4 bg-white rounded-md">
          <iframe
            src=""
            width="600"
            className="w-full rounded-md"
            height="450"
            allowfullscreen=""
            loading="lazy"
            title="video"
            referrerPolicy="no-referrer-when-downgrade"
          ></iframe>
        </div> */}
      </section>
    </>
  );
};

export default ContactUs;
