<?php
// NOT YET COMPLETE
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Token;
use Stripe\Source;
use Stripe\PaymentIntent;

Stripe::setApiKey("sk_test_51ISw50HC8M2JxTUFZ0fNbVPvrEMM8ld25Ntemq53sSxyKSOggo2RYs71mpgYASRaxevWSwieCmZPDI8Hs6UOxLWV003IcdkMas"); // Private Key


$customer = Customer::create(array(
  "email" => "what@ever.com",
));

$source = Source::create([
  'type' => 'ach_credit_transfer',
  // 'bank_account' => [
  //   'country' => 'US',
  //   'currency' => 'usd',
  //   'account_holder_name' => 'Jenny Rosen',
  //   'account_holder_type' => 'individual',
  //   'routing_number' => '110000000',
  //   'account_number' => '000123456789',
  // ],  
  "currency" => "usd",
  "owner" => [
    "email" => "jenny.rosen@example.com"
  ]
]);

dd($source);

// $token = Token::create([
//   'bank_account' => [
//     'country' => 'US',
//     'currency' => 'usd',
//     'account_holder_name' => 'Jenny Rosen',
//     'account_holder_type' => 'individual',
//     'routing_number' => '110000000', // BIC
//     'account_number' => '000123456789', // IBAN
//   ],
// ]);

// dd($token);

// $source = Source::create([
//   'type' => 'ach_credit_transfer', // https://stripe.com/docs/api/sources/object#source_object-type
//   'currency' => 'usd',
//   'token' => $token->id,
//   "owner" => [
//     "email" => $customer->email
//   ]
// ]);

// dd($source);

$payment = PaymentIntent::create(
    array(
        'amount' => 4250, // 42.50 Dollars
        'currency' => 'usd',
        'capture_method' => 'automatic',
        'confirm' => 'true',
        'statement_descriptor' => 'description ...',
        'source' => $source->id,
        'customer' => $customer->id
    )
);

dd($payment);


/* ----- ----- */


function dd($content) 
{
  echo '<pre>';
  var_dump($content);
  echo '----------';
  echo '</pre>';
}