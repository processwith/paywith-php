<?php declare(strict_types=1);

namespace PayWith\Processors\Paystack;

use Curl\Curl;
use PayWith\Processors\Processor;

class Paystack extends Processor
{
    /**
     * Paystack Endpoints
     */
    protected $endpoints = [
        'transactions' => 'transaction'
    ];

    /**
     * Paystack supported currencies
     */
    protected $currencies = [
        'NGN', 'GHS', 'RND', 'USD'
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

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function setResponse(object $response): void
    {
        $this->response = $response;
    }

    /**
     * Set the default currency
     * 
     * @since 0.5
     */
    public function getCurrencies(): array
    {
        return $currencies;
    }

}