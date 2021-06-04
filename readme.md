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

Payment Intents
- https://stripe.com/docs/api/payment_intents/create

### Credit Card

Tutorials
- https://www.codexworld.com/stripe-payment-gateway-integration-php/
- https://gist.github.com/boucher/1750375  
- https://keithweaverca.medium.com/using-stripe-with-php-c341fcc6b68b
- https://artisansweb.net/guide-stripe-integration-website-php/

#### Pre-assigned card credentials

```php
\Stripe\Stripe::setApiKey("SECRET_KEY");

$customer = Customer::create(array(
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

#### User enter their card credentials

https://github.com/Ruslan-Aliyev/Stripe-API-Plain-PHP/blob/master/payment_cc.php

### Bank Transfer

SEPA is: https://en.wikipedia.org/wiki/Single_Euro_Payments_Area

Tutorials
- Backend: https://stripe.com/docs/api/tokens/create_bank_account
- JS: https://stripe.com/docs/js/tokens_sources/create_token?type=bank_account

Details
- IBAN = International Bank Account Number
- BIC is the 'equivalent' to SWIFT code or Routing Code: https://wise.com/us/swift-codes/

![](/Illustrations/BIC.png)

#### Pre-assigned bank credentials

```php

```
#### User enter their bank credentials

https://github.com/Ruslan-Aliyev/Stripe-API-Plain-PHP/blob/master/payment_bank.php

## Create Subscriptions

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

$product = Product::create([
  'name' => 'Test Product',
]);

$price = Price::create([
  'unit_amount' => 2000,
  'currency' => 'usd',
  'recurring' => ['interval' => 'day'],
  'product' => $product->id,
]);

$subscription = Subscription::create([
  'customer' => $customer->id,
  'items' => [
    ['price' => $price->id], // Recurring cost
  ],
]);
```

- https://stripe.com/docs/api/products/create
- https://stripe.com/docs/api/prices/create
- https://stripe.com/docs/api/subscriptions/create

---

# Other Stripe Tutorials

- Checkout session: https://www.greatbigdigitalagency.com/blog/get-stripe-up-and-running-fast-with-php
- Laravel: https://www.remotestack.io/how-to-integrate-stripe-payment-gateway-in-laravel/
- Webhooks: https://www.petekeen.net/stripe-webhook-event-cheatsheet
