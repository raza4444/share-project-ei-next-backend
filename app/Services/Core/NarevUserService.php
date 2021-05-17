<?php
/**
 * by stephan scheide
 */

namespace App\Services\Core;


use App\Logging\CL;

class NarevUserService
{

    public static function buildNarevUri($appendix)
    {
        return 'https://narev.de/' . $appendix;
    }

    public function findUserInfo($userId)
    {
        CL::debug('findUserInfo from narev for user ' . $userId);

        // $userId = 647;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => self::buildNarevUri('v1/users/' . $userId . '/info'),
            CURLOPT_USERAGENT => 'ei-backend',
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $resp = curl_exec($curl);
        $code = curl_getinfo($curl,CURLINFO_HTTP_CODE);

        CL::debug('got response from narev: '.$code.'/'.$resp);

        curl_close($curl);

        if ($code == 200) {
            $result = json_decode($resp, true);
            return $result;
        } else {
            return null;
        }

    }

    public function canUserAccessEi($userId)
    {
        $info = $this->findUserInfo($userId);
        return $info && $info['canusesystem'];
    }

}
