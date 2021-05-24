<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Customer;

// hear See your keys here: https://dashboard.stripe.com/account/apikeys
Stripe::setApiKey("sk_test_51ISw50HC8M2JxTUFZ0fNbVPvrEMM8ld25Ntemq53sSxyKSOggo2RYs71mpgYASRaxevWSwieCmZPDI8Hs6UOxLWV003IcdkMas");
 
$customer = Customer::create(array(
	"email" => "test@jackdoes.com",
));

dd($customer);

$response = Customer::retrieve(
  $customer->id,
  []
);

dd($response);



/* ----- ----- */


function dd($content) 
{
	echo '<pre>';
	var_dump($content);
	echo '----------';
	echo '</pre>';
}