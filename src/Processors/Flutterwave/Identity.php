<?php declare(strict_types=1);

namespace PayWith\Processors\Flutterwave;

use PayWith\Helpers\DoSomething;
use PayWith\Exceptions\PayException;
use PayWith\Processors\Flutterwave\Flutterwave;

/**
 * Identity Flutterwave wrapper
 * 
 * @since 0.6
 */
class Identity extends Flutterwave
{
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
        parent::__construct($secretKey);
    }

    /**
     * Get the customer info from BVN
     * 
     * @since 0.6
     */
    public static function bvnResolve(): array
    {
        
    }

    /**
     * Match a customer info with that of their BVN
     * 
     * @since 0.6
     */
    public static function bvnMatch( array $customer_info ): bool
    {
        
    }
}