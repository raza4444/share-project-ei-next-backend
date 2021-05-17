<?php
/**
 * by stephan scheide
 */

namespace App\ValueObjects\Core;


use Illuminate\Http\Request;

class MatchersFactory
{

    /**
     * @param Request $request
     * @return Matcher[]
     */
    public static function byCommaString($str)
    {
        $arr = [];
        if ($str && strlen($str) > 4) {
            $tmp = explode(',', $str);
            for ($i = 0; $i < count($tmp); $i += 3) {
                $m = new Matcher();
                $m->field = $tmp[0];
                $m->op = $tmp[1];
                $m->value = $tmp[2];
                $arr[] = $m;
            }
        }
        return $arr;
    }

    /**
     * @param Request $request
     * @return Matcher[]
     */
    public static function byRequest(Request $request)
    {
        if ($request->has('matcher')) {
            return self::byCommaString($request->get('matcher'));
        }
        return [];
    }

}
