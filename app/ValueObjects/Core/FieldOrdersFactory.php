<?php
/**
 * by stephan scheide
 */

namespace App\ValueObjects\Core;


use Illuminate\Http\Request;

class FieldOrdersFactory
{

    /**
     * @param $str
     * @return FieldOrders
     */
    public static function byCommaString($str)
    {
        $result = new FieldOrders();

        if ($str && strlen($str) > 2) {
            $tmp = explode(',', $str);
            for ($i = 0; $i < count($tmp); $i += 2) {
                $o = new FieldOrder();
                $o->field = $tmp[$i];
                $o->asc = $tmp[$i + 1] == 'asc';
                $result->orders[] = $o;
            }
        }

        return $result;
    }

    public static function byRequest(Request $request)
    {
        if ($request->has('order')) {
            return self::byCommaString($request->get('order'));
        }
        return new FieldOrders();
    }

}
