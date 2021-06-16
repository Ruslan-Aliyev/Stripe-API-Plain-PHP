import {
  CardNumberElement,
  CardExpiryElement,
  CardCvcElement,
  useStripe,
  useElements,
} from '@stripe/react-stripe-js';

const Stripe = () => {
  const stripe = useStripe();
  const elements = useElements();

  const handleClick = () => {

    var cardElement = elements.getElement(CardNumberElement);

    stripe.createToken(cardElement)
      .then(function(result) {
        if (result.error) 
        {
          console.dir(result.error.message);
        } 
        else 
        {
          console.dir(result.token.id);
        }
      });
  };

  return (
    <div>
      <CardNumberElement />
      <CardExpiryElement />
      <CardCvcElement />
      <button onClick={handleClick}>Pay</button>
    </div>
  );
}

export default Stripe;
