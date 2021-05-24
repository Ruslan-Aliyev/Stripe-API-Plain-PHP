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

---

- https://www.codexworld.com/stripe-payment-gateway-integration-php/
- https://keithweaverca.medium.com/using-stripe-with-php-c341fcc6b68b
- https://www.greatbigdigitalagency.com/blog/get-stripe-up-and-running-fast-with-php
- https://gist.github.com/boucher/1750375
- https://artisansweb.net/guide-stripe-integration-website-php/
- https://www.remotestack.io/how-to-integrate-stripe-payment-gateway-in-laravel/
