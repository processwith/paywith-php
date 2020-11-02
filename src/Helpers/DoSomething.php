<?php
namespace Processwith\Helpers;

class DoSomething
{
    public function generateRandomEmail(string $identifier):string
    {
        return sprintf('%s', $identifier);
    }
}