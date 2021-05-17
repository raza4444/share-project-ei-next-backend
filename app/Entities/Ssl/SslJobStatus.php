<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Ssl;


class SslJobStatus
{

    /**
     * jeder neue Job erhaelt diesen Status
     */
    const STATUS_NEW = 0;

    /**
     * erfolgreiche Verarbeitung
     */
    const STATUS_SUCCESS = 1;

    /**
     * Fehler bei der Verarbeitung
     * wird erneut versucht
     */
    const STATUS_ERROR = 2;

    const STATUS_IGNORE = 3;

    /**
     * wartet auf Folgejob
     */
    const STATUS_WAITING = 4;

    /**
     * Job deaktiviert, kann reaktiviert werden
     */
    const STATUS_DEACTIVATED = 5;

    /**
     * Job für immer deaktiviert, kann nur händisch reaktiviert werden
     * Für Kündigung eines Kunden notwendig
     * Für Selbstgehostete notwendig
     */
    const STATUS_DEACTIVATED_FOREVER = 6;

    public static function visibleState($status)
    {
        if ($status === self::STATUS_SUCCESS) {
            return 'erfolgreich';
        }
        if ($status === self::STATUS_ERROR) {
            return 'fehlerhaft';
        }
        if ($status === self::STATUS_IGNORE) {
            return 'ignoriert';
        }
        if ($status === self::STATUS_NEW) {
            return 'neu';
        }
        if ($status == self::STATUS_WAITING) {
            return 'wartend';
        }
        if ($status == self::STATUS_DEACTIVATED) {
            return 'deaktiviert';
        }
        if ($status == self::STATUS_DEACTIVATED_FOREVER) {
            return 'deaktiviert für immer (gekündigt)';
        }
        return 'unbekannt';
    }

}
