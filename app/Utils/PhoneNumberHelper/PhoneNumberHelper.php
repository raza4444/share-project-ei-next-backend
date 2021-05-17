<?php
/**
 * Created by PhpStorm.
 * AdminUser: jannisseemann
 * Date: 29/03/14
 * Time: 05:52 PM
 */

namespace App\Utils\PhoneNumberHelper;

interface PhoneNumberHelper
{
    /**
     * Convert a phone number into the +49 string using libphonenumber.
     *
     * @param $value
     *
     * @return string
     */
    public function correctPhoneNumber($value, $countryCode = "+49");

    /**
     * Removes the country prefix using libphonenumber.
     *
     * @param $value
     * @return mixed
     */
    public function unPrefixPhoneNumber($value);

    /**
     * @param $value
     * @return string
     */
    public function getAreaCodeOfPhoneNumber($value);

}