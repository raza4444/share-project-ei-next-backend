<?php
/**
 * by stephan scheide
 */

namespace App\Servers;

interface IEventServerClient
{
    /**
     * returns the next free event id > 0 or 0 if not found
     * @param $counterType the type of the counter the employees work on
     * @return int
     */
    function nextFreeEventId($counterType);

    /**
     * forces the server to reload the events
     * usefull for example when many new events where created by a Freischaltung
     * @return void
     */
    function forceReloadOfEvents();

    /**
     * returns true if server is available
     * @return bool
     */
    function isServerAvailable();

}
