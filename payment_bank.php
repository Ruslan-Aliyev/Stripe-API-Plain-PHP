<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Source;
use Stripe\PaymentIntent;

Stripe::setApiKey("sk_test_51ISw50HC8M2JxTUFZ0fNbVPvrEMM8ld25Ntemq53sSxyKSOggo2RYs71mpgYASRaxevWSwieCmZPDI8Hs6UOxLWV003IcdkMas"); // Private Key

if ($_POST)
{
    $source = $_POST['src'];
    $name   = $_POST['name'];
    $email  = $_POST['email'];
    $amount = $_POST['amount'];

    $customer = Customer::create([
      "email" => $email,
    ]);

    dd($customer);

    $source = Customer::createSource($customer->id, [
      'source' => $source,
    ]);

    dd($source);

    $payment = PaymentIntent::create([
      "payment_method_types"=> [
        "sepa_debit"
      ],
      "amount"=> ($amount * 100),
      "capture_method"=> "automatic",
      "confirm"=> "true",
      "currency"=> "eur",
      "statement_descriptor"=> "test sd",
      "source"=> $source->id,
      "customer"=> $customer->id
    ]);

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
                <label>Name</label>
                <input type="text" name="name" id="name" class="field" placeholder="Jenny Rosen" required="">
            </div>
            <div class="form-group">
                <label>EMAIL</label>
                <input type="email" name="email" id="email" class="field" placeholder="Enter email" required="">
            </div>

            <div class="form-group">
                <label>IBAN NUMBER</label>
                <input type="text" id="iban" class="field" placeholder="DE89370400440532013000"></input>
            </div>

            <input type="hidden" name="src" id="src" class="field"></input>

            <button type="submit" class="btn btn-success" id="payBtn">Submit Payment</button>
        </form>

        <script type="text/javascript">
            var stripe = Stripe('pk_test_51ISw50HC8M2JxTUFdmtaIrYoz714yudM5V9s2U3SEqTN0ugI3TnlVP7Oxj3KAMLNLsYe6GAvC2FpCJ30OheFkLqG00tuhMBMUr'); // Public Key

            var elements = stripe.elements();

            function setOutcome(result) 
            {
                if (result.source) 
                {
                    var form = document.querySelector('#paymentFrm');
                    form.querySelector('input#src').setAttribute('value', result.source.id);
                    form.submit();
                } 
                else if (result.error) 
                {
                    console.dir(result.error.message);
                }
            }

            document.querySelector('#paymentFrm').addEventListener('submit', function(e) {
              e.preventDefault();
              var sourceData = {
                type: 'sepa_debit',
                sepa_debit: {
                    iban: document.getElementById('iban').value,
                },
                currency: 'eur',
                owner: {
                    name: document.getElementById('name').value,
                },
              };

              stripe.createSource(sourceData).then(setOutcome);
            });
        </script>

    </body>
</html>