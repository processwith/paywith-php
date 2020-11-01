<?php

use Abegpay\Abegpay;

require 'vendor/autoload.php';

$secret_key = 'sk_test_72c5b5f62558f8189aaeccae16ef0a9220be6521';

$abegpay = new Abegpay('paystack', $secret_key);
$abegpay->requestTransaction([
    'amount'    => 1000,
    'email'     => 'ikwuje24@gmail.com'
]);

print_r( $abegpay->response );

echo '\n' . $abegpay->response['body']->data->authorization_url;