<?php

namespace ProcessWith\Processors\Flutterwave;

use Curl\Curl;
use ProcessWith\Processors\Processor;

class Flutterwave extends Processor
{
    /**
     * Ravepay Endpoints
     */
    protected $endpoints = [
        'payments' => 'v3'
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
        parent::__construct('flutterwave', $secretKey, 'https://api.flutterwave.com');

        $this->setHeaders([
            'Authorization' => sprintf('Bearer %s', $secretKey),
            'Content-Type'  => 'application/json',
        ]);

        $this->request = new Curl();
        $this->request->setHeaders( $this->getHeaders() );
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
        $this->client_response      = $response;
    }
}