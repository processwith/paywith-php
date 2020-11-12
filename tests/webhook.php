<?php
use ProcessWith\ProcessWith;

require '../vendor/autoload.php';
require 'config.php';

$amount = '1000';
$email  = 'afuwapesunday12@gmail.com';

$processwith = new ProcessWith('flutterwave', RAVE_SECRET_KEY);

$transaction = $processwith->transaction();
$transaction->webhookHash('RAVE-SK-HASH');
$transaction->webhook();

// file_put_contents( 'hook.txt', $transaction->webhook() );



if ($transaction->status()) {
    // check the email and the amount
    // before giving value
    if ( $amount == $transaction->getAmount() && $email == $transaction->getEmail() ) {
        file_put_contents( 'hook.txt', 'Thanks for making a payment via flutterwave' );
    }
    else {
        file_put_contents( 'hook.txt', 'Amount does not match via flutterwave' );
    }
}
else {
    file_put_contents( 'hook.txt', $transaction->getMessage() );
}