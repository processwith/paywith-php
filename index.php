<?php

use ProcessWith\Paywith;

require 'vendor/autoload.php';

$secret_key = 'sk_test_xxxxxx';

$paywith = new Paywith('paystack', $secret_key);
$transaction = $paywith->transaction();
$transaction->initialize([
    'amount'    => (float) 100,
    'email'     => 'ikwuje24@gmail.com'
]); 

if( $transaction->status ) {
    $transaction->checkout(); // redirect to the gateway checkout page
}
else {
    // beautiful error message display
    die( $transaction->statusMessage );
}