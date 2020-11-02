<?php

use ProcessWith\ProcessWith;

require 'vendor/autoload.php';

$secret_key = 'sk_test_xxxx'; // your gateway secret ( Paystack | Ravepay )

$processwith = new ProcessWith('paystack');
$processwith->setSecretKey($secret_key);

$transaction = $processwith->transaction();
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