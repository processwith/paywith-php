<?php declare(strict_types=1);

namespace ProcessWith\Processors\Flutterwave;

use ProcessWith\Helpers\DoSomething;
use ProcessWith\Exceptions\PayException;
use ProcessWith\Processors\Flutterwave\Flutterwave;

class Transaction extends Flutterwave
{
    /**
     * The `amount` of the transaction
     * 
     * @var float
     * @since 0.5
     */
    protected $amount = 0;

    /**
     * The `email` of the transaction
     * 
     * @var string
     * @since 0.5
     */
    protected $email;

    /**
     * The payer info
     * 
     * @var array
     * @since 0.5
     */
    protected $payer = [
        'name'  => null,
        'email' => null,
        'phone' => null
    ];

    /**
     * meta of the transaction
     * 
     * @var array
     * @since 0.5
     */
    protected $meta = [];

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
    protected $body;

    /**
     * request body nexted array value
     * 
     * @var array
     * @since 0.5
     */
    protected $customer;

    /**
     * The transacion endpoint
     * 
     * @var string
     * @since 0.5
     */
    protected $endpoint;

    /**
     * The redirect url
     * 
     * @var string
     */
    protected $redirect_url;

    /**
     * Checkout url
     * 
     * @var string
     * @since 0.5
     */
    protected $checkout_url;

    /**
     * Transaction dafault currency 
     * 
     * @var array
     * @since 0.5
     */
    protected $currency = 'NGN';

    /**
     * Fallback if payment option not set
     * 
     * @var string
     * @since 0.5
     */
    protected $payment_option = 'card';

    /**
     * local hash to check against event Hash
     * 
     * @var string
     * @since 0.5
     */
    protected $webhookHash;

    /**
     * Constructor
     * 
     * @since 0.5
     */
    public function __construct(string $secretKey)
    {
        parent::__construct($secretKey);
        $this->endpoint = sprintf('%s/%s', $this->URL, $this->endpoints['payments'] );
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function setEmail(string $email): void
    {
        if (! DoSomething::goodEmail($email)) {
            throw new PayException('Transaction: Email is not valid');
        }
        $this->payer['email'] = $email;
    }

    /**
     * Set the Payer information
     * 
     * @since 0.5
     */
    public function setPayer(array $payer): void
    {
        if (! array_key_exists('email', $payer)) {
            throw Exception('Payer email is required');
        }

        $this->payer['email'] = $payer['email'];

        if ( array_key_exists('name', $payer) ) {
            $this->payer['name'] = $payer['name'];
        }

        if ( array_key_exists('phone', $payer) ) {
            $this->payer['phone'] = $payer['phone'];
        }
    }

    /**
     * Set the transaction currency
     * 
     * @since 0.5
     */
    public function setCurrency(string $currency): void
    {
        if (! in_array($currency, $this->currencies)) {
            throw new PayException('Transaction: Currency not supported by Paystack');
        }

        $this->currency = $currency;
    }

    /**
     * Set the reference of the transaction
     * 
     * @since 0.5
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * Set a transaction request endpoint
     * 
     * @since 0.5
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Set the checkout url
     * @since 0.5
     */
    public function setCheckout(string $checkout_url): void
    {
        if(DoSomething::goodURL($checkout_url)) {
            $this->checkout_url = $checkout_url;
        }
        else {
            throw new PayException('Transaction: Checkout URL not valid');
        }
    }

    /**
     * Set the redirect url
     * 
     * @since 0.5
     */
    public function setRedirect(string $redirect_url): void
    {
        if(DoSomething::goodURL($redirect_url)) {
            $this->redirect_url = $redirect_url;
        }
        else {
            throw new PayException('Transaction: Redirect URL not valid');
        }
    }

    /**
     * Set the transaction request body
     * 
     * @since 0.5
     */
    public function setBody(array $body): void
    {
        $this->body = $body;
    }

    /**
     * Get the reference of the transaction
     * 
     * @since 0.5
     */
    public function getAmount(): float
    {
        if(!$this->amount) {
            return 0.0;
        }

        return (float) $this->amount;
    }

    /**
     * Get the reference of the transaction
     * 
     * @since 0.5
     */
    public function getEmail(): string
    {
        return ($this->payer['email'] ?? '');
    }

    /**
     * Get the endpoint of the transaction
     * 
     * @since 0.5
     */
    public function getEndpoint(): string
    {
        return ($this->endpoint ?? '');
    }

    /**
     * Get the object of the transaction
     * 
     * @since 0.5
     */
    public function getPayer(): array
    {
        return $this->payer;
    }

    /**
     * Get the body of the transaction
     * 
     * @since 0.5
     */
    public function getBody(): object
    {
        return (object) $this->body;
    }

    /**
     * Get the currency of the transaction
     * 
     * @since 0.5
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Get the checkout_url
     * 
     * @since 0.5
     */
    public function getCheckout(): string
    {
        return ($this->checkout_url ?? '');
    }

    /**
     * Get the redirect_url
     * 
     * @since 0.5
     */
    public function getRedirect(): string
    {
        return ($this->redirect_url ?? '');
    }

    /**
     * Get the reference of the transaction
     * 
     * @since 0.5
     */
    public function getReference(): string
    {
        return $this->reference ?? '';
    }

    /**
     * Initialize a Paystack transaction
     * 
     * ---------------------------------------------------------------------
     * We make a request to the /transaction endpoint
     * a response will be returned:
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
    public function initialize(array $body = []) //: void
    {
        
        if( array_key_exists('amount', $body) ) {
            $this->setAmount($body['amount']);
        } 
        else {
            if( $this->getAmount() == 0 ) {
                throw new PayException('Amount is required for transaction');
            }
            else {
                $body['amount'] = $this->getAmount();
            }
        }


        if( array_key_exists('email', $body) ) {
            $this->setEmail($body['email']);
            unset($body['email']);
        } 
        else {
            throw new PayException('Email is required for transaction');
        }

        if( array_key_exists('callback_url', $body) ) {

            // Reassign array with a new key
            // unset the old array key
            $this->setRedirect($body['redirect_url'] = $body['callback_url']);
            $body['redirect_url'] = $this->getRedirect();

            unset($body['callback_url']);
            
        }


        if( array_key_exists('currency', $body) ) {
            $this->setCurrency($body['currency']);
            $body['currency'] = $this->getCurrency();
        }
        else {
            $body['currency'] = $this->currencies[0];
        }


        if( !$this->getPayer() ) {
            $this->setPayer([ 'email' => $body['email'] ]);
        }
        else {
            $body['customer'] = $this->getPayer();
        }

        $this->setReference( bin2hex(random_bytes(7)) );
        $body['tx_ref'] = $this->getReference();

        $body['payment_options'] = $this->payment_option;

       
        $this->setBody($body);

        $request = $this->request;
        $request->post( sprintf('%s/payments', $this->getEndpoint()), $body );

        
        if($request->error) {
            $this->httpStatusCode = $request->errorCode;
            $this->setMessage();
        }
        else {
            $this->setResponse($request->response);
            $this->setRawResponse($request->getRawResponse());

            $this->httpStatusCode = $request->getHttpStatusCode();

            if ('success' == $request->response->status) {
                $this->status = true;

                $this->setReference( $this->getReference() );
                $this->setCheckout($request->response->data->link);
            }

            $this->setMessage($request->response->message);
        }       

    }


    /**
     * Redirect to paystack checkout page
     * 
     * @since 0.5
     */
    public function checkout(): void
    {
        header(sprintf('Location:%s', $this->checkout_url));
        die();
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
    public function verify(string $reference = ''): void
    {
        $this->status = false;

        if(empty($reference)) {
            $reference = $this->reference ?? '';
        }

        if(empty($reference)) {
            throw new PayException('Transaction: No reference supplied');
        }

        $this->setBody([
            'reference' => $reference
        ]);

        $request = $this->request;
        $request->get(sprintf('%s/transactions/%s/verify', $this->endpoint, $reference));

        if($request->error) {
            $this->httpStatusCode = $request->errorCode;
            $this->setMessage();
        }
        else {
            $this->setResponse($request->response);
            $this->setRawResponse($request->getRawResponse());
            $this->setReference($request->response->data->tx_ref);
            $this->setAmount($request->response->data->amount);
            $this->setEmail($request->response->data->customer->email);

            $this->httpStatusCode   = $request->getHttpStatusCode();

            if('success' == $request->response->data->status) {
                $this->status = true;
            }

            $this->setMessage($request->response->data->processor_response);
        }

    }


    /**
     * Set webhook hash to check against event hash
     *
     * @link 
     * @since 0.5
     */
    public function webhookHash(string $hash) : void
    {
        $this->webhookHash = $hash;
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
     * @link https://paystack.com/docs/payments/webhooks/
     * @since 0.5
     */
    public function webhook()
    {

        $checked = true; // default checked

        // check if webhook has isset else throw exception
        if( !isset($this->webhookHash) ) {
            throw new PayException('webhook secret hash is not set');
        }

        // Retrieve the request's body
        $body = @file_get_contents("php://input");

        // confirm request method
        if( (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !array_key_exists('HTTP_VERIF_HASH', $_SERVER) ) {

            $checked = false;
            $this->httpStatusCode = 400;
            $this->message= 'Transaction: Invalid POST signature';
        }
        
        // Retrieve event signature hash
        $eventHash = (isset($_SERVER['HTTP_VERIF_HASH']) ? $_SERVER['HTTP_VERIF_HASH'] : '');        
        
        // confirm the event's signature hash
        if( $this->webhookHash !== $eventHash ) {

            $checked = false;
            $this->httpStatusCode = 401;
            $this->message = 'Transaction: Invalid event signature';
        }
        

        if($checked)
        {
            http_response_code(200);
            $this->httpStatusCode = 200;
            $this->setRawResponse($body);

            $event = json_decode($body);

            if ('charge.completed' == ($event->event ?? '') ) {
                $this->setAmount($event->data->amount);
                $this->setEmail($event->data->customer->email);
                $this->setReference($event->data->tx_ref);
                $this->setResponse($event);
                $this->setMessage($event->data->processor_response);

                if ('successful' == $event->data->status) {
                    $this->status = true;
                }
            }
            else {
                $this->message = 'Transaction: Flutterwave charge.failed';
            }

        }      
        
    }
}