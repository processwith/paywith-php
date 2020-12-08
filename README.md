<p align="center">
  <img src="https://raw.githubusercontent.com/processwith/processwith-assets/main/paywith-repo-featured%20image.png" alt="paywith repo image">
  <h1 align="center">Paywith</h1>
  <br><br>
</p>

[![](https://img.shields.io/github/release/processwith/paywith-php.svg)](https://github.com/processwith/paywith-php/releases/)
[![](https://img.shields.io/github/license/processwith/paywith-php.svg)](https://github.com/processwith/paywith-php/blob/master/LICENSE)
[![](https://img.shields.io/travis/processwith/paywith-php.svg)](https://travis-ci.com/github/processwith/paywith-php/)

Paywith makes it easy to use multiple payment gateways in your PHP application.

## Installation

You can install the package via composer:

```bash
composer require processwith/paywith-php
```

## Usage

### basic transaction
``` php
use ProcessWith\PayWith;

$paywith = new PayWith('Paystack', 'Your Paystack Secret');
$transaction->initialize([
    'amount' => 5000,
    'customer' => [
        'email'  => 'jeremiahsucceed@gmail.com',
        'name'  => 'Ade Kolawole'
    ],
    'redirect_url' => 'http://localhost:3000/tests/verify.php',
    'currency' => 'NGN'
]);

$transaction->checkout(); // redirect to checkout page
```
Love more examples, see the example page.

### verify a transaction
``` php
// Paywith must have be initialize with Paystack or Flutterwave 
$transaction = $paywith->transaction();
$transaction->verify( $_GET['reference'] );

if( $transaction->status() )
{
    // check the email and the amount
    // before giving value
    $amount = 5000;
    $email  = 'jeremiah@gmail.com';

    if ( $amount == $transaction->getAmount() && $email == $transaction->getEmail() )
    {
        // give value
        // echo 'thanks for making payment';
    }
}
```

### webhook
``` php
// Paywith must have be initialize with Paystack or Flutterwave 
$transaction    = $paywith->transaction();
$transaction->webhook();

if( $transaction->status() )
{
    // check the email and the amount
    // before giving value
    $amount = 5000;
    $email  = 'jeremiah@gmail.com';
    if ( $amount == $transaction->getAmount() && $email == $transaction->getEmail() )
    {
        // give value
        // echo 'thanks for making payment';
    }
}
```

### Tutorials
We are making some plug and play tutorials. If you like to recieve one when it still HOT, click [here](#).

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email jeremiah@processwith.com instead of using the issue tracker.

## Credits

- [Jeremiah Ikwuje](https://github.com/ijsucceed)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.