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
- https://stripe.com/docs/api/payment_intents/confirm

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

Tutorials
- Verify customer's bank account
  - https://stripe.com/docs/api/customer_bank_accounts/verify
  - https://stripe.com/docs/payments/bank-debits
    - https://stripe.com/docs/ach#manually-collecting-and-verifying-bank-accounts
    - https://www.youtube.com/watch?v=_1EX-DrikoA
    - http://www.coding4developers.com/stripe/stripe-create-bank-account-token-using-stripe-js/
- SEPA
  - https://en.wikipedia.org/wiki/Single_Euro_Payments_Area
  - https://stripe.com/docs/sources/sepa-debit
- Backend: 
  - https://stripe.com/docs/api/tokens/create_bank_account
  - https://stripe.com/docs/api/sources/object#source_object-type
- JS: https://stripe.com/docs/js/tokens_sources/create_token?type=bank_account

Required Info
- IBAN = International Bank Account Number
- BIC is the 'equivalent' to SWIFT code or Routing Code: https://wise.com/us/swift-codes/

![](/Illustrations/BIC.png)

#### Pre-assigned bank credentials

```php
\Stripe\Stripe::setApiKey("SECRET_KEY");

$customer = Customer::create(array(
  "email" => "what@ever.com",
));

// Bank Transfer

// ------------
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
// ------------
// $token = Token::create([
//   'bank_account' => [
//     'country' => 'US',
//     'currency' => 'usd',
//     'account_holder_name' => 'Jenny Rosen',
//     'account_holder_type' => 'individual',
//     'routing_number' => '110000000',
//     'account_number' => '000123456789',
//   ],
// ]);

// $source = Customer::createSource($customer->id, [
//   "source" => $token->id
// ]);
// ------------

// SEPA

$source = Source::create([
  "type" => "sepa_debit",
  "sepa_debit" => ["iban" => "DE89370400440532013000"],
  "currency" => "eur",
  "owner" => [
    "name" => "Jenny Rosen",
  ],
]);

$source = Customer::createSource($customer->id, [
  'source' => $source->id,
]);

$payment = PaymentIntent::create([
  "payment_method_types"=> [
    "sepa_debit"
  ],
  "amount"=> "500",
  "capture_method"=> "automatic",
  "confirm"=> "true",
  "currency"=> "eur",
  "statement_descriptor"=> "test sd",
  "source"=> $source->id,
  "customer"=> $customer->id
]);
```
#### User enter their bank credentials

- https://jsfiddle.net/ywain/jdbsoe9t/
- https://github.com/Ruslan-Aliyev/Stripe-API-Plain-PHP/blob/master/payment_bank.php

## 3D Secure 

2-Factor Authentication between Stripe and customer's bank.

- https://stripe.com/docs/payments/3d-secure

### None

If Create PaymentIntent returns `"status": "succeeded"`

You can get below response with Credit Card number: `4242424242424242`

```
{
    "id": "pi_3JTeuIHC8M2JxTUF0E9BZGPX",
    "object": "payment_intent",
    "amount": 2000,
    "amount_capturable": 0,
    "amount_received": 2000,
    "charges": {
        "object": "list",
        "data": [
            {
                "id": "ch_3JTeuIHC8M2JxTUF0WfqFAkb",
                "object": "charge",
                "amount": 2000,
                "amount_captured": 2000,
                "amount_refunded": 0,
            }
        ],
    },
    "status": "succeeded",
}
```

### 3D Secure - Version 1

If Create PaymentIntent returns `"status": "requires_action"` and `"next_action"` contains `"three_d_secure_redirect"`

You can get below response with Credit Card number: `4000000000003063`

```
{
    "id": "pi_3JTezVHC8M2JxTUF0dFduXtH",
    "object": "payment_intent",
    "amount": 2000,
    "amount_capturable": 0,
    "amount_received": 0,
    "charges": {
        "object": "list",
        "data": [],
        "has_more": false,
        "total_count": 0,
        "url": "/v1/charges?payment_intent=pi_3JTezVHC8M2JxTUF0dFduXtH"
    },
    "next_action": {
        "type": "use_stripe_sdk",
        "use_stripe_sdk": {
            "type": "three_d_secure_redirect",
            "stripe_js": "https://hooks.stripe.com/redirect/authenticate/src_1JTezWHC8M2JxTUF2p0bLDpS?client_secret=src_client_secret_4P5awMuZ47ZB1XCd9pusgZHt&source_redirect_slug=test_YWNjdF8xSVN3NTBIQzhNMkp4VFVGLF9LN3VwYU9hZDFvWktuTkpiaUE0WmZQM0xMZ01nS2t60100BIm9KDR6",
            "source": "src_1JTezWHC8M2JxTUF2p0bLDpS"
        }
    },
    "status": "requires_action",
}
```

You should then redirect customer to that `three_d_secure_redirect` URL, where customer can authenticate himself with his bank.

Then you check his authentication by `GET https://api.stripe.com/v1/payment_intents/{stripe_payment_intent_id}`.

If authentication failed, `status` won't be `succeeded` and `last_payment_error` will have something:
```
{
    "id": "pi_3JTf5fHC8M2JxTUF1qCrUPQx",
    "object": "payment_intent",
    "amount": 2000,
    "amount_capturable": 0,
    "amount_received": 0,
    "charges": {
        "object": "list",
        "data": [],
        "has_more": false,
        "total_count": 0,
        "url": "/v1/charges?payment_intent=pi_3JTf5fHC8M2JxTUF1qCrUPQx"
    },
    "last_payment_error": {
        "code": "payment_intent_authentication_failure",
        "doc_url": "https://stripe.com/docs/error-codes/payment-intent-authentication-failure",
        "message": "The provided source has failed authentication. You can provide source_data or a new source to attempt to fulfill this PaymentIntent again.",
    },
    "status": "requires_payment_method",
}
```

If authentication succeeded, `status` will be `succeeded`.

### 3D Secure - Version 2

If Create PaymentIntent returns `"status": "requires_action"` and `"next_action"` contains `"stripe_3ds2_fingerprint"`

You can get below response with Credit Card number: `4000000000003220`

```
{
    "id": "pi_3JaMbkJBxP8ARq8K1EWflbQy",
    "object": "payment_intent",
    "amount": 2000,
    "amount_capturable": 0,
    "amount_received": 0,
    "charges": {
        "object": "list",
        "data": [],
        "has_more": false,
        "total_count": 0,
        "url": "/v1/charges?payment_intent=pi_3JaMbkJBxP8ARq8K1EWflbQy"
    },
    "client_secret": "pi_3JaMbkJBxP8ARq8K1EWflbQy_secret_8qOovqvYPMmM38G2kjZUPho1U",
    "next_action": {
        "type": "use_stripe_sdk",
        "use_stripe_sdk": {
            "type": "stripe_3ds2_fingerprint",
            "merchant": "acct_1J16v1JBxP8ARq8K",
            "three_d_secure_2_source": "src_1JaMblJBxP8ARq8Kk8KceLtJ",
            "directory_server_name": "visa",
            "server_transaction_id": "8f182044-45fa-4ec1-8908-dcc33f647ec9",
            "three_ds_method_url": "",
            "three_ds_optimizations": "k",
            "directory_server_encryption": {
                "directory_server_id": "A000000003",
                "algorithm": "RSA",
                "certificate": "xxx",
                "root_certificate_authorities": [
                    "xxx"
                ]
            },
            "one_click_authn": null
        }
    },
    "status": "requires_action",
}
```

You should take the `client_secret` and pass into `https://js.stripe.com/v3/`'s `stripe.confirmCardPayment("pi_3JaMbkJBxP8ARq8K1EWflbQy_secret_8qOovqvYPMmM38G2kjZUPho1U")`

```js
Promise.coroutine(function* () {

  var response = yield stripe.confirmCardPayment(paymentIntentResponse.client_secret);
  
  if (response.paymentIntent && response.paymentIntent.status === 'succeeded')
  {
    // response.paymentIntent contains the success info
    // Notable fields are:
    // Payment Intent ID: response.paymentIntent.id
    // Paid amount: response.paymentIntent.amount
  }

  if (response.error)
  {
    // response.error contains the error info, as well as a payment_intent sub-object
    // Notable fields are:
    // Payment Intent ID: response.error.payment_intent.id
    // Amount: response.error.payment_intent.amount
    // Payment Intent status: response.error.payment_intent.status
    // Error Code: response.error.code
    // Error Type: response.error.type
    // Error Message: response.error.message
  }

})().catch(function (errs) {
  console.log(errs);
});
```

- https://stripe.com/docs/js/payment_intents/confirm_card_payment
- https://stripe.com/gb/guides/3d-secure-2
- https://developers.recurly.com/guides/3ds2.html#integration-guide
- https://github.com/topics/3d-secure
- https://stackoverflow.com/questions/57947500/laravel-cashier-3d-secure-sca-issue-stripe/57949694#57949694

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

## Cancel & Refund

- https://stripe.com/docs/api/payment_intents/cancel
- https://stripe.com/docs/refunds

You will need the `Charge ID` before you can `Cancel` or `Refund`. You can get it by `GET https://api.stripe.com/v1/payment_intents/{stripe_payment_intent_id}`.

A `PaymentIntent` object can be canceled when it is in one of these statuses: `requires_payment_method`, `requires_capture`, `requires_confirmation`, or `requires_action`.  
If you can't cancel, then consider refund.

---

# Other Stripe Tutorials

- Checkout session: https://www.greatbigdigitalagency.com/blog/get-stripe-up-and-running-fast-with-php
- Laravel: https://www.remotestack.io/how-to-integrate-stripe-payment-gateway-in-laravel/
- Webhooks: https://www.petekeen.net/stripe-webhook-event-cheatsheet
- "Cheatsheet": https://gist.github.com/briankung/865c7cb5f136e2b1a746e1613c79b312
- Errors:
  - https://stripe.com/docs/api/errors
  - https://github.com/stripe/stripe-php/tree/master/lib/Exception
  - https://stripe.com/docs/api/errors/handling
  - https://stripe.com/docs/error-codes
  - https://stripe.com/docs/declines/codes

# React

- Good tutorial: https://github.com/stripe/react-stripe-js#minimal-example
- My demo: https://github.com/Ruslan-Aliyev/Stripe-API-Plain-PHP/tree/master/react-stripe
- https://stripe.com/docs/stripe-js/react 
- https://www.youtube.com/watch?v=5y5WwF9s-ZI
- https://stackoverflow.com/questions/63640839/only-one-element-of-type-cardnumber-can-be-created 
- https://stackoverflow.com/questions/49239767/how-do-i-add-a-custom-font-to-my-card-element-when-using-react-stripe-elements
