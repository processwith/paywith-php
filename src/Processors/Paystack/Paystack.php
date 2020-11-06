<?php

namespace ProcessWith\Processors\Paystack;

use Curl\Curl;
use ProcessWith\Processors\Processor;

class Paystack extends Processor
{
    /**
     * Paystack Endpoints
     */
    protected $endpoints = [
        'transactions' => 'transaction'
    ];

    /**
     * Curl request
     * 
     * @var Curl
     */
    protected $request;

    /**
     * Constructor
     * 
     * @since 0.5
     */
    public function __construct(string $secretKey)
    {
        parent::__construct('paystack', $secretKey, 'https://api.paystack.co');

        $this->setHeaders([
            'Authorization' => sprintf('Bearer %s', $secretKey),
            'Content-Type'  => 'application/json',
        ]);

        $this->request = new Curl();
        $this->request->setHeaders( $this->getHeaders() );
    }
}