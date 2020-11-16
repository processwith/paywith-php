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
     * The customer info
     * 
     * @var array
     * @since 0.5
     */
    protected $customer = [
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
        $this->customer['email'] = $email;
    }

    /**
     * Set the customer information
     * 
     * @since 0.5
     */
    public function setCustomer(array $customer): void
    {
        if( isset($this->customer['email']) ) {
            $this->customer['email'] = $customer['email'];
        }
        else {
            if( !$this->getEmail() ) {
                throw Exception('customer email is required');
            }
        }
        
        $this->customer['phone'] = $customer['phone'] ?? null;
        $this->customer['name'] = $customer['name'] ?? null;
    }

    /**
     * Set the transaction currency
     * 
     * @since 0.5
     */
    public function setCurrency(string $currency): void
    {
        if (! in_array($currency, $this->currencies)) {
            throw new PayException('Transaction: Currency not supported by Flutterwave');
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
        return $this->customer['email'];
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
    public function getCustomer(): object
    {
        return (object) $this->customer;
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
     * Initialize a Flutterwave transaction
     * 
     * ---------------------------------------------------------------------
     * We make a request to the /transaction endpoint
     * a response will be returned:
     * {
     *      ...
     
     * }
     * 
     * We then set this response body
     * 
     * @param $redirect if it set to true, we redirect to the PSTK checkout page
     * @link https://developer.flutterwave.com/docs/flutterwave-standard
     * @since 0.5
     */
    public function initialize(array $body = []) //: void
    {
        if( array_key_exists('amount', $body) ) {
            $this->setAmount($body['amount']);
        } 
        else if( $this->getAmount() == 0 ) {
            throw new PayException('Transaction: Amount is required on Flutterwave');
        }
        else {
            $body['amount'] = $this->getAmount();
        }

        if ( isset($body['customer']) && is_array($body['customer']) ) {
            $customer = $body['customer'];

            if( isset($body['customer_email']) ) {
                $this->setEmail( $body['customer_email'] );
            } 
            else if( !$this->getEmail() ) {
                throw new PayException('Transaction: Customer email is required on Flutterwave');
            }
            else {
                $customer['email'] = $this->getEmail();
            }
               
            if( array_key_exists('phonenumber', $customer) ) {
                $this->customer['phone'] = $customer['phonenumber'];
            }
            else if ( $this->customer['phone'] ) {
                $customer['phonenumber'] = $this->customer['phone'];
            }

            if( isset($customer['name']) ) {
                $this->customer['name'] = $customer['name']; 
            }
            else if( !$this->customer['name'] ) {
                $customer['name'] = $this->customer['name'];
            }
        }
        else {
            $customer = [];

            if( array_key_exists('customer_email', $body) ) {
                $this->setEmail($body['customer_email']);
                $customer['email'] = $this->getEmail();
            } 
            else if( !$this->getEmail() ) {
                throw new PayException('Transaction: Customer email is required on Flutterwave');
            }
            else {
                $customer['email'] = $this->getEmail();
            }

            if( array_key_exists('customer_phone', $body) ) {
                $customer['phonenumber'] = $body['customer_phone'];
            }
            else if ( $this->customer['phone'] ) {
                $customer['phonenumber'] = $this->customer['phone'];
            }

            $name = sprintf(
                '%s %s',
                ($body['customer_firstname'] ?? ''),
                ($body['customer_lastname'] ?? '')
            ); // firstname lastname

            if( !empty($name) ) {
                $customer['name'] = $name;
            }
            else if( $this->customer['name'] ) {
                $customer['name'] = $this->customer['name'];
            }
        }

        $body['customer'] = $customer;

        if( array_key_exists('redirect_url', $body) ) {
            $this->setRedirect($body['redirect_url']);
        }
        else if( !empty( $this->getRedirect()) ) {
            $body['redirect_url'] = $this->getRedirect();
        }

        if( array_key_exists('currency', $body) ) {
            $this->setCurrency($body['currency']);
        }
        else if( ! empty( $this->getCurrency()) ) {
            $body['currency'] = $this->getCurrency();
        }

        if( array_key_exists('tx_ref', $body) ) {
            $this->setReference($body['tx_ref']);
        }
        else if( !empty( $this->getReference()) ) {
            $body['tx_ref'] = $this->getReference();
        }
        else {
            $body['tx_ref'] = bin2hex(random_bytes(7));
            $this->setReference($body['tx_ref']);
        }

        if( array_key_exists('payment_options', $body) ) {
            $this->setReference($body['payment_options']);
        }

        $this->setBody($body);

        $request = $this->request;
        $request->post( sprintf('%s/payments', $this->getEndpoint()), $body );
        
        if( $request->error ) {
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
     * Redirect to Flutterwave checkout page
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
     * @link https://developer.flutterwave.com/v2.0/docs/rave-standard
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
            $this->setMessage($request->response->message);

            if('successful' == $request->response->data->status) {
                $this->status = true;
                $this->setMessage($request->response->data->processor_response);
            }
        }

    }

    /**
     * Handle webhook
     * 
     * When a transaction is made on Flutterwave, Flutterwave sends a payload of
     * data to URL you specify on your dashboard.
     * 
     * This method handle the payload and return TRUE|FALSE for a 
     * valid or non valid transaction.
     * 
     * @link https://developer.flutterwave.com/reference#webhook
     * @since 0.5
     */
    public function webhook(string $secretHash = '') : void
    {
        // Retrieve the request's body
        $body = @file_get_contents("php://input");
        
        $checked = true; // default checked

        // if the has was provided
        if( empty($hashCode) )
        {
            throw new PayException('Secret hash is required. Check Settings/Webhook on your Flutterwave dashboard');
            exit();
        }

        if( ! isset($_SERVER['REQUEST_METHOD']) || strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' )
        {
            throw new PayException('Transaction: Invalid Flutterwave POST request');
            exit();
        }
       
        // Retrieve event signature hash
        $eventHash = (isset($_SERVER['HTTP_VERIF_HASH']) ? $_SERVER['HTTP_VERIF_HASH'] : '');        
                
        // confirm the event's signature hash
        if( $secretHash !== $eventHash )
        {
            $checked = false;
            $this->httpStatusCode = 400;
            $this->message = 'Transaction: Invalid Flutterwave signature';
        }

        if($checked)
        {
            http_response_code(200);
            $this->httpStatusCode = 200;
            $this->setRawResponse($body);

            $response = json_decode($body);

            $this->setAmount($response->data->amount);
            $this->setEmail($response->data->customer->email);
            $this->setCurrency($response->data->currency);
            $this->setReference($response->data->txRef);
            $this->setResponse($response);

            if( 'successful' == $response->data->status ) {
                $this->status = true;
                $this->setMessage('Transaction: Charge was completed', 200);
            }
            else {
                $this->setMessage('Transaction: Charge ' . $response->data->status);
            }
        }      
    }
}