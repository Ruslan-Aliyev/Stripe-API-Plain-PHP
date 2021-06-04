<?php

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
  "email" => "nowtest2@ever.com",
));

// $source = Customer::createSource($customer->id, [
//   "source" => [
//     "object"=> "bank_account",
//     "account_number"=> "000123456789",
//     "country"=> "US",
//     "currency"=> "usd",
//     "account_holder_name"=> "Test",
//     "account_holder_type"=> "individual",
//     "routing_number"=> "110000000",
//   ]
// ]);

// dd($source);

$token = Token::create([
  'bank_account' => [
    'country' => 'US',
    'currency' => 'usd',
    'account_holder_name' => 'Jenny Rosen',
    'account_holder_type' => 'individual',
    'routing_number' => '110000000',
    'account_number' => '000123456789',
  ],
]);

dd($token);

$source = Customer::createSource($customer->id, [
  "source" => $token->id
]);

dd($source);



/* ----- ----- */


function dd($content) 
{
  echo '<pre>';
  var_dump($content);
  echo '----------';
  echo '</pre>';
}