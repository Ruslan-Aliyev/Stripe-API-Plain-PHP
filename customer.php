<?php

require_once __DIR__ . '/vendor/autoload.php';

// hear See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey("sk_test_51ISw50HC8M2JxTUFZ0fNbVPvrEMM8ld25Ntemq53sSxyKSOggo2RYs71mpgYASRaxevWSwieCmZPDI8Hs6UOxLWV003IcdkMas");
 
$customer = \Stripe\Customer::create(array(
	"email" => "test1@jackdoes.com",
));