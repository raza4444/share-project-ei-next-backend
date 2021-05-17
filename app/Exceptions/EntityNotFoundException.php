<?php
/**
 * by stephan scheide
 */

namespace App\Exceptions;


class EntityNotFoundException extends \Exception
{

    public static function byEntity($name, $reference)
    {
        return new EntityNotFoundException("$name mit Kennung $reference konnte nicht gefunden werden.");
    }

}