<?php
/**
 * by stephan scheide
 */

namespace App\Validation\Entities;


use Throwable;

class EntityValidatorException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}