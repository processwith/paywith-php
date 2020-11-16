<?php
use ProcessWith\ProcessWith;

require '../vendor/autoload.php';
require 'config.php';

$processwith = new ProcessWith('paystack');
$processwith->setSecretKey( PSTK_SECRET_KEY );

$transaction = $processwith->transaction();
$transaction->initialize([
    'amount'        => 1000,
    'email'         => 'ikwuje@gmail.com',
    'callback_url'  => 'http://localhost:3000/tests/verify.php',
    'currency'      => 'NGN',
]);

/* Ravepay
$transaction->initialize([
    'amount'          => 1000,
    'customer_email'  => 'afuwapesunday12@gmail.com',
    'redirect_url'    => 'http://localhost:3000/tests/verify.php',
    'meta'            => [ 'consumer_id' => 23, 'consumer_mac' => '92a3-912ba-1192a' ],
]);
*/

if( $transaction->status() ) {
    file_put_contents('results/ref.txt', $transaction->getReference());
    $transaction->checkout(); // redirect to the gateway checkout page
}
else {
    // beautiful error message display
    die( $transaction->getMessage());
}

?>
