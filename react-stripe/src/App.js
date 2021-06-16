import './App.css';
import Stripe from "./components/Stripe";
import {loadStripe} from '@stripe/stripe-js';
import {
  Elements,
} from '@stripe/react-stripe-js';

const stripePromise = loadStripe('pk_test_51ISw50HC8M2JxTUFdmtaIrYoz714yudM5V9s2U3SEqTN0ugI3TnlVP7Oxj3KAMLNLsYe6GAvC2FpCJ30OheFkLqG00tuhMBMUr');

function App() {
  return (
    <div className="App">
      <Elements stripe={stripePromise}>
        <Stripe />
      </Elements>
    </div>
  );
}

export default App;
