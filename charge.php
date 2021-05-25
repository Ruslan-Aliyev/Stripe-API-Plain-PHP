<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Token;
use Stripe\Charge;

Stripe::setApiKey("sk_test_51ISw50HC8M2JxTUFZ0fNbVPvrEMM8ld25Ntemq53sSxyKSOggo2RYs71mpgYASRaxevWSwieCmZPDI8Hs6UOxLWV003IcdkMas");
 
$token = Token::create([
  'card' => [
    'number' => '4242424242424242',
    'exp_month' => 5,
    'exp_year' => 2022,
    'cvc' => '314',
  ],
]);

dd($token);

$charge = Charge::create(
    array(
        'amount' => 4200, // 42 Dollars
        'currency' => 'usd',
        'source' => $token->id
    )
);

dd($charge);


/* ----- ----- */


function dd($content) 
{
	echo '<pre>';
	var_dump($content);
	echo '----------';
	echo '</pre>';
}