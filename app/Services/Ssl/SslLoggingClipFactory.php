<?php
/**
 * by stephan scheide
 */

namespace App\Services\Ssl;


use App\Entities\Branches\Location;
use App\Entities\Ssl\SslJob;
use App\Logging\LoggingClip;
use App\Utils\StringUtils;

class SslLoggingClipFactory
{

    public static function globalClip()
    {
        return new LoggingClip('ssljobs', 'global', true);
    }

    public static function forJob(Location $loc)
    {
        return self::forJobId($loc->id . '-' . StringUtils::onlyFiguresAndNumbers($loc->domain));
    }

    private static function forJobId($id)
    {
        return new LoggingClip('ssljobs', $id, true);
    }

}
