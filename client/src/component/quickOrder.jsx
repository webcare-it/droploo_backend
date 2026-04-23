import { useNavigate } from 'react-router-dom';
import { useAddToCartMutation } from '../redux/features/api';
import toast from 'react-hot-toast';
import { Button } from '@mui/material';
import Loader from './ui/Loader';

const QuickOrder = ({ data, id, discount_price, regular_price, ip }) => {
  const navigate = useNavigate();

  // Add to cart mutation.
  const [addToCart, { isLoading }] = useAddToCartMutation();

  // quick order handle.
  const ip_address = ip;

  const handleQuickOrder = async (path) => {
    const price = discount_price ? discount_price : regular_price;
    const qty = data?.qty || 1;
    const size = data?.size;
    const color = data?.color;

    const payload = {
      ip_address,
      qty,
      price,
      color,
      size,
    };
    try {
      const response = await addToCart({ id, data: payload }).unwrap();
      toast.success(response.message);
      navigate(path);
    } catch (error) {
      console.error('Error adding to cart:', error);
      toast.error('Failed to add to cart');
    }
  };
  return (
    <div>
      <div type="button">
        {isLoading ? (
          <button className="bg-primary-300 w-full lg:px-4 py-2 rounded-md text-white">
            <Loader />
          </button>
        ) : (
          <Button
            onClick={() => handleQuickOrder('/checkout')}
            variant="contained"
            className="!bg-primary-500 w-full lg:px-4 py-2 rounded-md text-white"
          >
            Order Now
          </Button>
        )}
      </div>
    </div>
  );
};

export default QuickOrder;
