<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Ssl;


use App\Entities\Core\AbstractModel;

/**
 * Class SslVerifyResult
 * @package App\Entities\Ssl
 * @property string domain
 * @property string certissuer
 * @property date dateuntil
 * @property int valid
 * @property int validissuer
 * @property int validdateuntil
 * @property int expired
 * @property string details
 * @property string rawoutput
 *
 */
class SslVerifyResult extends AbstractModel
{

    protected $table = 'ssl_verify_results';

    public static function createNew()
    {
        $r = new SslVerifyResult();
        $r->certissuer = null;
        $r->domain = null;
        $r->dateuntil = null;
        $r->valid = 0;
        $r->validdateuntil = 0;
        $r->validissuer = 0;
        $r->details = null;
        $r->expired = 0;
        $r->rawoutput = null;
        return $r;
    }

}
