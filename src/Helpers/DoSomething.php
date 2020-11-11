<?php
namespace ProcessWith\Helpers;

class DoSomething
{
    /**
     * Check if email is good
     * 
     * @since 0.5
     */
    public static function goodEmail(string $email): bool
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
    public static function goodURL(string $URL): bool
    {
        if(filter_var($URL, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }
}