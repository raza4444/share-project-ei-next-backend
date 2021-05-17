<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Customers;


class CustomerInfoDataFactory
{

    /**
     * @param $customerId
     * @param $arr
     * @return CustomerInfoData[]
     */
    public static function createManyFromRegistrationData($customerId, $arr)
    {
        $result = [];
        foreach ($arr as $k => $v) {
            $data = new CustomerInfoData();
            $data->type = CustomerInfoData::TYPE_REGISTRATION;
            $data->customerid = $customerId;
            $data->name = $k;
            $data->value = $v;
            $result[] = $data;
        }
        return $result;
    }

}