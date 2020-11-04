<?php

use ProcessWith\ProcessWith;

require '../vendor/autoload.php';
require 'config.php';

// $amount = '500';
// $email  = 'ikwuje24@gmail.com';

// $processwith = new ProcessWith('paystack');
// $processwith->setSecretKey( SECRET_KEY );

// $transaction = $processwith->transaction();
// $transaction->verify('vw7vw4fl95');

// if ( $transaction->status() ) {
//     // give value
//     // check email and amount before giving value
//     if ( $amount == $transaction->amount && $email == $transaction->email ) {
//         echo 'Thank you for making payments';
//     }
//     else {
//         echo 'Amount and Email doesn\'t match';
//     }
// }


$amount = '1000';
$email  = 'afuwapesunday12@gmail.com';

$processwith = new ProcessWith('flutterwave');
$processwith->setSecretKey( RAVE_SECRET_KEY );

$transaction = $processwith->transaction();
$transaction->verify('1674064');

//print_r($transaction->verifyResponse()); // resonse object

if ( $transaction->status() ) {
    // give value
    // check email and amount before giving value
    if ( $amount == $transaction->amount && $email == $transaction->email ) {
        echo 'Thank you for making payments';
    }
    else {
        echo 'Amount and Email doesn\'t match';
    }
}  