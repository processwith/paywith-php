<?php

use ProcessWith\ProcessWith;

require '../vendor/autoload.php';
require 'config.php';

$amount = '1000';
$email  = 'ikwuje@gmail.com';

$processwith = new ProcessWith('flutterwave', RAVE_SECRET_KEY);

$transaction = $processwith->transaction();
$transaction->verify($_GET['transaction_id'] ?? '');

if ($transaction->status()) {
    // give value
    // check email and amount before giving value
    if ( $amount == $transaction->getAmount() && $email == $transaction->getEmail() ) {

    	file_put_contents( 'test.txt', 'Thank you for making payment Via Flutterwave' );
        echo 'Thank you for making payments';
    }
    else {
        echo 'Amount and Email doesn\'t match';
    }
}
else {
    echo $transaction->getMessage();
}