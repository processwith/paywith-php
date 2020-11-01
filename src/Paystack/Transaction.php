<?php

namespace ProcessWith\Paywith\Paystack;

use Curl\Curl;
use ProcessWith\Paywith\Paystack;

class Transaction extends Paystack
{
    /**
     * The `amount` of the transaction
     * 
     * @var float
     * @since 0.5
     */
    public $amount;

    /**
     * The `email` of the transaction
     * 
     * @var string
     * @since 0.5
     */
    public $email;

    /**
     * Optional fields of the transaction
     * 
     * @var array
     * @since 0.5
     */
    public $fields;

    /**
     * The `reference` of the transaction
     * 
     * @var string
     * @since 0.5
     */
    protected $reference;

    /**
     * The request body of a transaction
     * 
     * @var array
     * @since 0.5
     */
    public $body;

    /**
     * The transacion endpoint
     */
    public $endpoint = 'transaction';

    /**
     * Constructor
     * 
     * @since 0.5
     */
    public function __construct()
    {
        $this->endpoint = parent::getURL()
    }

    /**
     * Set the reference of the transaction
     * 
     * @since 0.5
     */
    public function setReference(string $reference):void
    {
        $this->reference = $reference;
    }

    /**
     * Get the reference of the transaction
     * 
     * @since 0.5
     */
    public function getReference():string
    {
        return $reference;
    }

    /**
     * Initialize a Paystack transaction
     * 
     * ---------------------------------------------------------------------
     * We make a request to the /transaction endpoint
     * a response will be return containing the keys:
     * {
     *      ...
     *      "authorization_url": "https://checkout.paystack.com/0peioxfhpn",
     *      "access_code": "0peioxfhpn",
     *      "reference": "7PVGX8MEk85tgeEpVDtD"
     * }
     * 
     * We then set this response body
     * 
     * @param $redirect if it set to true, we redirect to the PSTK checkout page
     * @link https://paystack.com/docs/api/#transaction
     * @since 0.5
     */
    public function initialize(array $body):void
    {
        $curl = new Curl();
        $curl->setHeader('Content-Type', "application/json");
        $curl->setHeader('Authorization', sprintf("Bearer %s", $this->secret_key));
        $curl->post(
            sprintf('%s/initialize', $this->endpoint),
            $body
        );
        
        if (!$curl->error) {
            parent::setResponse($curl->getResponse());
        }
        else {
            parent::setResponse();
        }
    }

    /**
     * Verify a transaction
     * 
     * -----------------------------------------------------------
     * By requesting to the /transaction/verify endpoint
     * a reponse body containing the transaction information is
     * returned.
     * 
     * If the transaction status was successfull, we return TRUE
     * -----------------------------------------------------------
     * 
     * @link https://paystack.com/docs/api/#transaction-verify
     * @since 0.5
     */
    public function verify(string $reference):bool
    {
        if(empty($reference)) {
            return false;
        }

    }

    /**
     * Handle webhook
     * 
     * When a transaction is made on Paystack, paystack sends a payload of
     * data to URL you specify on your dashboard.
     * 
     * This method handle the payload and return TRUE|FALSE for a 
     * valid or non valid transaction.
     * 
     * @link 
     * @since 0.5
     */
    public function webhook():bool
    {

    }
}