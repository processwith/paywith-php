<?php

use ProcessWith\ProcessWith;

require '../vendor/autoload.php';
require 'config.php';

$amount = '50000';
$email  = 'ikwuje24@gmail.com';

$processwith = new ProcessWith('paystack');
$processwith->setSecretKey( SECRET_KEY );

$transaction = $processwith->transaction();
$transaction->initialize([
    'amount'    => (float) $amount,
    'email'     => $email
]);

if( $transaction->status() ) {
    file_put_contents('ref.txt', $transaction->getReference());
    $transaction->checkout(); // redirect to the gateway checkout page
}
else {
    // beautiful error message display
    die( $transaction->statusMessage() );
}