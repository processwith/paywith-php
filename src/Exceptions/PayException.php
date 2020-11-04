<?php

namespace ProcessWith\Exceptions;

class PayException extends \Exception
{
    /**
     * Base Merchants Exceptions.
     *
     * @return string
     */
    public function errorMessage(string $error) : string
    {
        return '<strong>' . htmlspecialchars($this->getMessage()) . "</strong><br/>\n";
    }
}
