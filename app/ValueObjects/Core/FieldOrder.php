<?php
/**
 * by stephan scheide
 */

namespace App\ValueObjects\Core;


/**
 * Sortierung nach einem Feld
 *
 * Class FieldOrder
 * @package App\ValueObjects\Core
 */
class FieldOrder
{

    /**
     * Name des Felders
     *
     * @var null
     */
    public $field = null;

    /**
     * Reihenfolge, asc fuer TRUE
     *
     * @var bool
     */
    public $asc = true;

    public function isValid()
    {
        return $this->field && strlen($this->field) > 0;
    }

}