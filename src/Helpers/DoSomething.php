<?php
namespace ProcessWith\Helpers;

final class DoSomething
{
    /**
     * Check if email is good
     * 
     * @since 0.5
     */
    final public static function goodEmail(string $email): bool
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true; 
        }
        return false;
    }

    /**
     * Validate a url
     * 
     * @since 0.5
     */
    final public static function goodURL(string $URL): bool
    {
        if(filter_var($URL, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }

    /**
     * Encription array of values
     * 
     * Use case on 3DES encription for card payments
     */
    final public static function encrypt3DES(string $data, array $key): string
    {
        $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
        return base64_encode($encData); 
    }
}