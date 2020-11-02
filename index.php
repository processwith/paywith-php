<?php

use ProcessWith\Paywith;

require 'vendor/autoload.php';

$secret_key = 'sk_test_72c5b5f62558f8189aaeccae16ef0a9220be6521';

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