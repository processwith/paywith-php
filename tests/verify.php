<?php

use ProcessWith\ProcessWith;

require '../vendor/autoload.php';
require 'config.php';

$amount = '1000';
$email  = 'ikwuje@gmail.com';

$processwith = new ProcessWith('paystack', PSTK_SECRET_KEY);

$transaction = $processwith->transaction();
$transaction->verify($_GET['reference'] ?? '');

if ($transaction->status()) {
    // give value
    // check email and amount before giving value
    if ( $amount == $transaction->getAmount() && $email == $transaction->getEmail() ) {
        echo 'Thank you for making payments';
    }
    else {
        echo 'Amount and Email doesn\'t match';
    }
}
else {
    echo $transaction->getMessage();
}