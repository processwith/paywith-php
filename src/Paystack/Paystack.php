<?php

use ProcessWith\Paywith\Processor;

class Paystack extends Processor
{
    private const API_URL = 'https://api.paystack.co';

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
}