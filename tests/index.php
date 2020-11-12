<?php
use ProcessWith\ProcessWith;

require '../vendor/autoload.php';
require 'config.php';

$processwith = new ProcessWith('flutterwave');
$processwith->setSecretKey( RAVE_SECRET_KEY );

$transaction = $processwith->transaction();

$transaction->initialize([
    'amount'          => 1000,
    'email'           => 'afuwapesunday12@gmail.com',
    'callback_url'    => 'https://d47b4c1895d2.ngrok.io/tests/verify.php',
    'meta'            => [ 'consumer_id' => 23, 'consumer_mac' => '92a3-912ba-1192a' ],
]); 


if( $transaction->status() ) {
    file_put_contents('ref.txt', $transaction->getReference());
    $transaction->checkout(); // redirect to the gateway checkout page
}
else {
    // beautiful error message display
    die( $transaction->getMessage());
}

?>
