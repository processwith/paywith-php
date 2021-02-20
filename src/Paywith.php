<?php
namespace PayWith;

use Curl\Curl;
use PayWith\Processors\Flutterwave;
use PayWith\Processors\Paystack;
use PayWith\Helpers\DoSomething;
use PayWith\Exceptions\PayException;

/**
 * The PayWith class
 * 
 * @author PayWith
 * @link https://www.PayWith.com
 * @version 0.5
 */
class PayWith
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
            throw new PayException('Gateway not supported');
        }
    }

    /**
     * Set the secret key of a gateway
     * 
     * @param string $secretKey
     */
    public function setSecretKey(string $secretKey): void
    {
        if (empty($secretKey)) {
            throw new PayException('No secret key supplied');
        }

        $this->secretKey = $secretKey;
    }

    /**
     * Return the transaction object for the current processor
     * 
     * @since 0.5 
     */
    public function transaction(): ?object
    {
        if (!$this->secretKey) {
            throw new PayException('No secret key supplied');
        }

        $transaction = null;

        switch(strtolower($this->processor)) {
            case 'paystack':
                $transaction = new Paystack\Transaction($this->secretKey);
            break;
            case 'flutterwave':
                $transaction = new Flutterwave\Transaction($this->secretKey);
            break;
            default:
                //$transaction = new PayWith\transaction($this->secretKey);
        }

        return $transaction;
    }
}
