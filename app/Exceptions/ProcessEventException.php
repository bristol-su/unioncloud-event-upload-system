<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ProcessEventException extends Exception
{
    public $erroredClass;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, $erroredClass = null)
    {
        $this->erroredClass = $erroredClass;
        parent::__construct($message, $code, $previous);
    }
}
