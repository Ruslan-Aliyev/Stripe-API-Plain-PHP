# Stripe API & Plain PHP

`composer require stripe/stripe-php`

- https://www.youtube.com/playlist?list=PLyzY2l387AlPlX5gKQU9SRExGsu0NGW2X

1. Episode 1: Create Customer

```php
\Stripe\Stripe::setApiKey("SECRET_KEY");
 
$customer = \Stripe\Customer::create(array(
	"email" => "what@ever.com",
));
```

- https://www.onlinecode.org/create-customer-stripe-api-php/

2. Episode 2:

- https://www.codexworld.com/stripe-payment-gateway-integration-php/
- https://keithweaverca.medium.com/using-stripe-with-php-c341fcc6b68b
- https://www.greatbigdigitalagency.com/blog/get-stripe-up-and-running-fast-with-php
- https://gist.github.com/boucher/1750375
