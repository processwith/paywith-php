<?php

namespace ProcessWith\Processors\Paystack;

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
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function setResponse(object $response): void
    {
        $this->response['status']   = $response->status;
        $this->response['message']  = $response->message;
        $this->response['client']   = $response->data;
        $this->client_response = $response;
    }
}