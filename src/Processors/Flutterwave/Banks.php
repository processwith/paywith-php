<?php declare(strict_types=1);

namespace PayWith\Processors\Flutterwave;

use PayWith\Helpers\DoSomething;
use PayWith\Exceptions\PayException;
use PayWith\Processors\Flutterwave\Flutterwave;

class Banks extends Flutterwave
{
    /**
     * Fetch list of available banks
     * 
     * @since 0.6
     */
    public function list(): array
    {
        
    }

    /**
     * Get a bank info
     * 
     * @since 0.6
     */
    public function get(): array
    {
        
    }
}