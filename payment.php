<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Source;
use Stripe\PaymentIntent;

Stripe::setApiKey("sk_test_51ISw50HC8M2JxTUFZ0fNbVPvrEMM8ld25Ntemq53sSxyKSOggo2RYs71mpgYASRaxevWSwieCmZPDI8Hs6UOxLWV003IcdkMas"); // Private Key

if ($_POST)
{
    $token  = $_POST['stripeToken']; 
    $amount = $_POST['amount']; 
    $email = $_POST['email']; 

    dd($token);
    dd($amount);
    dd($email);

    $customer = \Stripe\Customer::create(array(
      "email" => $email,
    ));
    dd($customer);

    $source = Source::create([
      'type' => 'card',
      'token' => $token,
    ]);
    dd($source);

    $payment = PaymentIntent::create(
        array(
            'amount' => $amount * 100,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
            'capture_method' => 'automatic',
            'confirm' => 'true',
            'statement_descriptor' => 'description ...',
            'source' => $source->id,
            'customer' => $customer->id
        )
    );
    dd($payment);
}

// ---

function dd($content) 
{
    echo '<pre>';
    var_dump($content);
    echo '----------';
    echo '</pre>';
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Stripe Payment</title>
        <script src="https://js.stripe.com/v3/"></script>
    </head>
    <body>
        <div id="paymentResponse"></div>
        
        <form action="" method="POST" id="paymentFrm">
            <div class="form-group">
                <label>AMOUNT</label>
                <input type="number" name="amount" id="amount" step="0.01" class="field" placeholder="Enter amount" required="" autofocus="">
            </div>
            <div class="form-group">
                <label>EMAIL</label>
                <input type="email" name="email" id="email" class="field" placeholder="Enter email" required="">
            </div>

            <!-- This -->
            <div class="form-group">
                <label>CARD NUMBER</label>
                <div id="card_number" class="field"></div>
            </div>
            <div class="row">
                <div class="left">
                    <div class="form-group">
                        <label>EXPIRY DATE</label>
                        <div id="card_expiry" class="field"></div>
                    </div>
                </div>
                <div class="right">
                    <div class="form-group">
                        <label>CVC CODE</label>
                        <div id="card_cvc" class="field"></div>
                    </div>
                </div>
            </div>
            <!-- Or: Whole generic card input element, instead of 1 by 1 -->
            <!-- <div id="card-element"></div> -->
            <!--  -->

            <button type="submit" class="btn btn-success" id="payBtn">Submit Payment</button>
        </form>

        <script type="text/javascript">
            var stripe = Stripe('pk_test_51ISw50HC8M2JxTUFdmtaIrYoz714yudM5V9s2U3SEqTN0ugI3TnlVP7Oxj3KAMLNLsYe6GAvC2FpCJ30OheFkLqG00tuhMBMUr'); // Public Key

            var style = {
                base: {
                    fontWeight: 400,
                    fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
                    fontSize: '16px',
                    lineHeight: '1.4',
                    color: '#555',
                    backgroundColor: '#fff',
                    '::placeholder': {
                        color: '#888',
                    },
                },
                invalid: {
                    color: '#eb1c26',
                }
            };

            var elements = stripe.elements();

            // ---- This ----
            var cardElement = elements.create('cardNumber', {
                style: style
            });
            cardElement.mount('#card_number');

            elements.create('cardExpiry', {
                'style': style
            }).mount('#card_expiry');
            elements.create('cardCvc', {
                'style': style
            }).mount('#card_cvc');
            // ---- Or ----
            // Whole generic card input element, instead of 1 by 1
            // elements.create('card', {style: style}).mount('#card-element');
            // ----  ----

            // Validate input of the card elements
            var resultContainer = document.getElementById('paymentResponse');
            cardElement.addEventListener('change', function(event) {
                if (event.error) {
                    resultContainer.innerHTML = '<p>'+event.error.message+'</p>';
                } else {
                    resultContainer.innerHTML = '';
                }
            });

            var form = document.getElementById('paymentFrm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                stripe.createToken(cardElement).then(function(result) {
                    if (result.error) 
                    {
                        resultContainer.innerHTML = '<p>'+result.error.message+'</p>';
                    } 
                    else 
                    {
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) 
            {
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);
                form.submit();
            }
        </script>

    </body>
</html>