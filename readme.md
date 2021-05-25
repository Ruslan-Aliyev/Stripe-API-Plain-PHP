# Stripe API & Plain PHP

`composer require stripe/stripe-php`

Tutorial: https://www.youtube.com/playlist?list=PLyzY2l387AlPlX5gKQU9SRExGsu0NGW2X

If you are ever unsure about how to accomplish things via API, just go to Stripe dashboard, do it there, then go to https://dashboard.stripe.com/test/logs and see how the equivalent is done via API.

## Create Customer

```php
\Stripe\Stripe::setApiKey("SECRET_KEY");
 
$customer = \Stripe\Customer::create(array(
	"email" => "what@ever.com",
));
```

- https://stripe.com/docs/api/customers/create

## Retrieve Customer

```php
\Stripe\Stripe::setApiKey("SECRET_KEY");
 
$customer = \Stripe\Customer::create(array(
	"email" => "what@ever.com",
));

$response = \Stripe\Customer::retrieve(
  $customer->id,
  []
);
```

- https://stripe.com/docs/api/customers/retrieve

## Create card for customer

```php
\Stripe\Stripe::setApiKey("SECRET_KEY");
 
$customer = \Stripe\Customer::create(array(
	"email" => "what@ever.com",
));

$token = Token::create([
  'card' => [
    'number' => '4242424242424242',
    'exp_month' => 5,
    'exp_year' => 2022,
    'cvc' => '314',
  ],
]);

$card = Customer::createSource(
  $customer->id,
  ['source' => $token->id]
);

$setupIntent = SetupIntent::create([
  'payment_method_types'   => ['card'],
  'payment_method'         => $card->id,
  'customer'               => $customer->id,
  'confirm'                => 'true'
]);
```

1. Create token to hold sensitive info: https://stripe.com/docs/api/tokens/create_card
2. Create source as a means of payment: https://stripe.com/docs/api/cards/create
3. Create SetupIntent to save customer's payment credentials for future payments: https://stripe.com/docs/api/setup_intents/create

## Create a charge (Customer-less payment)

```php
\Stripe\Stripe::setApiKey("SECRET_KEY");
 
$token = Token::create([
  'card' => [
    'number' => '4242424242424242',
    'exp_month' => 5,
    'exp_year' => 2022,
    'cvc' => '314',
  ],
]);

$charge = Charge::create(
    array(
        'amount' => 4200, // 42 Dollars
        'currency' => 'usd',
        'source' => $token->id
    )
);
```

- https://stripe.com/docs/api/charges/create

## Create a payment

```php
\Stripe\Stripe::setApiKey("SECRET_KEY");

$customer = \Stripe\Customer::create(array(
  "email" => "what@ever.com",
));

// $source = Source::create([
//   'type' => 'card',
//   'card' => [
//     'number' => '4242424242424242',
//     'exp_month' => 5,
//     'exp_year' => 2022,
//     'cvc' => '314',
//   ],
// ]);

$token = Token::create([
  'card' => [
    'number' => '4242424242424242',
    'exp_month' => 5,
    'exp_year' => 2022,
    'cvc' => '314',
  ],
]);

$source = Source::create([
  'type' => 'card',
  'token' => $token->id,
]);

$payment = PaymentIntent::create(
    array(
        'amount' => 4250, // 42.50 Dollars
        'currency' => 'usd',
        'payment_method_types' => ['card'],
        'capture_method' => 'automatic',
        'confirm' => 'true',
        'statement_descriptor' => 'description ...',
        'source' => $source->id,
        'customer' => $customer->id
    )
);

```

The above examples show how to pre-assign a card credentials.  
Now we learn how to let the user enter their card credentials, then pay.

Payment Intents
- https://stripe.com/docs/api/payment_intents/create

Whole Tutorials
- https://www.codexworld.com/stripe-payment-gateway-integration-php/
- https://gist.github.com/boucher/1750375  
- https://keithweaverca.medium.com/using-stripe-with-php-c341fcc6b68b
- https://artisansweb.net/guide-stripe-integration-website-php/

---

- https://www.greatbigdigitalagency.com/blog/get-stripe-up-and-running-fast-with-php
- https://www.remotestack.io/how-to-integrate-stripe-payment-gateway-in-laravel/
