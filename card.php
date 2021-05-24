<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Token;
use Stripe\SetupIntent;

// hear See your keys here: https://dashboard.stripe.com/account/apikeys
Stripe::setApiKey("sk_test_51ISw50HC8M2JxTUFZ0fNbVPvrEMM8ld25Ntemq53sSxyKSOggo2RYs71mpgYASRaxevWSwieCmZPDI8Hs6UOxLWV003IcdkMas");
 
$customer = Customer::create(array(
	"email" => "test@jackdoes.com",
));

dd($customer);

$token = Token::create([
  'card' => [
    'number' => '4242424242424242',
    'exp_month' => 5,
    'exp_year' => 2022,
    'cvc' => '314',
  ],
]);

dd($token);

$card = Customer::createSource(
  $customer->id,
  ['source' => $token->id]
);

dd($card);

$setupIntent = SetupIntent::create([
  'payment_method_types'   => ['card'],
  'payment_method'         => $card->id,
  'customer'               => $customer->id,
  'confirm'                => 'true'
]);

dd($setupIntent);



/* ----- ----- */


function dd($content) 
{
	echo '<pre>';
	var_dump($content);
	echo '----------';
	echo '</pre>';
}