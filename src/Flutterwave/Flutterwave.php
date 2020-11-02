<?php

use ProcessWith\Paywith\Processor;

class Paystack extends Processor
{
    private const API_URL = 'https://api.paystack.co';

    public array $endPoints = [];

    private array $headers =  [];

    /**
     * Constructor
     * 
     * 
     * @since 0.5
     */
    public function __construct(string $secretKey)
    {
        parent('Paystack', $secretKey, self::API_URL);
    }

    public function getEndpoints() : array
    {}

    public function setHeaders(array, $headers) : void
    {}
}