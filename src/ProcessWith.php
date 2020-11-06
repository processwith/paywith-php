<?php

namespace ProcessWith;

use Curl\Curl;
use ProcessWith\Helpers\DoSomething;
use ProcessWith\Processors\Paystack\Transaction as PaystackTranx;
use ProcessWith\Processors\Flutterwave\Transaction as RaveTranx;

/**
 * The ProcessWith class
 * 
 * @author ProcessWith
 * @link https://www.processwith.com
 * @version 0.5
 */
class ProcessWith
{
    /**
     *  List of supported payment gateways
     * 
     * @var array
     * @version since 0.5
     */
    protected $processors = [
        'paystack',
        'flutterwave',
        'monnify',
        'paylink'
    ];
    
    /**
     * The current processor in use
     * 
     * @var string
     * @since 0.5
     */
    protected $processor;

    /**
     * The API secret key
     * 
     * @var string
     * @since 0.5
     */
    private $secretKey;
    

    /**
     * Constructor
     * 
     * @since 0.5
     */
    public function __construct(string $processor, string $secretKey = '') {
        $this->processor = $processor;
        $this->secretKey = $secretKey;

        // if the Processor is not supported
        if( ! in_array($processor, $this->processors) ) {
            die('Processor not supported');
        }
    }

    /**
     * Set the secret key of a gateway
     * 
     * @param string $secretKey
     */
    public function setSecretKey(string $secretKey): void
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Return the transaction object for the current processor
     * 
     * @since 0.5 
     */
    public function transaction(): ?object
    {
        $transaction = null;

        switch(strtolower($this->processor)) {
            case 'paystack':
                $transaction = new PaystackTranx($this->secretKey);
            break;
            case 'flutterwave':
                $transaction = new RaveTranx($this->secretKey);
            break;
            default:
                //$transaction = new Processwith\transaction($this->secretKey);
        }

        return $transaction;
    }
}
