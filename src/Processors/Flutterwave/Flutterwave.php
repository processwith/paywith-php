<?php declare(strict_types=1);

namespace Paywith\Processors\Flutterwave;

use Curl\Curl;
use Paywith\Processors\Processor;

class Flutterwave extends Processor
{
    /**
     * Ravepay Endpoints
     */
    protected $endpoints = [
        'payments' => 'v3'
    ];

    /**
     * Flutterwave supported currencies
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
        parent::__construct('flutterwave', $secretKey, 'https://api.flutterwave.com');

        $this->setHeaders([
            'Authorization' => sprintf('Bearer %s', $secretKey),
            'Content-Type'  => 'application/json',
        ]);

        $this->request = new Curl();
        $this->request->setHeaders( $this->getHeaders() );
    }

    /**
     * set request with required headers
     *
     * @return void
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * set reponse object
     *
     * @return void
     */
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