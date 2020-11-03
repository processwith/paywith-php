<?php
use ProcessWith\ProcessWith;

require '../vendor/autoload.php';
require 'config.php';

$amount = '50000';
$email  = 'ikwuje24@gmail.com';

$processwith = new ProcessWith( 'paystack' );
$processwith->setSecretKey( SECRET_KEY );

$transaction = $processwith->transaction();
$transaction->webhook();

if ( $transaction->status() ) {
    // check the email and the amount
    // before giving value
    if ( $amount == $transaction->amount && $email == $transaction->email ) {
        file_put_contents( 'hook.txt', 'Thanks for making a payment' );
    }
    else {
        file_put_contents( 'hook.txt', 'Amount does not match' );
    }
}
else {
    file_put_contents( 'hook.txt', $transaction->statusMessage() );
}