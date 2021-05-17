<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Branches;


class BasicAppointmentFilter
{

    /**
     * Menge der Jahre, in denen der Termin stattfindet(!)
     * @var array
     */
    public $whenyears = [];

    /**
     * Menge der Jahre, in denen der Termin angelegt(!) wurde
     * @var array
     */
    public $years = [];

    /**
     * Jahr, in dem der Termin angelegt(!) wurde
     * @var null
     */
    public $year = null;

    public $werbeaktion = null;

    public $onlyEmptyResult = false;

    public $onlyGone = false;

    public $onlyUpcoming = false;

    public $withAllSubObjects = false;

    public $top = null;

    public $skip = null;

    public $search = null;

    public $type = 0;

}
