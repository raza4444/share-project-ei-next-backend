<?php
/**
 * Created by PhpStorm.
 * AdminUser: jannisseemann
 * Date: 29/03/14
 * Time: 05:51 PM
 */

namespace App\Utils\PhoneNumberHelper;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Class PhoneNumberHelperImpl
 * @package Modules\Shared\Repositories\Telephone
 */
class PhoneNumberHelperImpl implements PhoneNumberHelper
{
    /**
     * Convert a phone number into the +49 string using libphonenumber.
     *
     * @param $value
     *
     * @return string
     */
    public function correctPhoneNumber($value, $countryCode = "+49")
    {
        if ($value === null) {
            return null;
        } else {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $tmp = $phoneUtil->parse(
                    $value,
                    $phoneUtil->getRegionCodeForCountryCode(str_replace("+", "", $countryCode))
                );
                if ($tmp instanceof PhoneNumber) {
                    $value = $phoneUtil->format($tmp, PhoneNumberFormat::E164);


                    //if doesn't contain text, trim and remove any special character but the starting '+'
                    if (!preg_match("/[a-zA-Z]/", $value)) {

                        $value = trim($value);
                        $pos = strpos($value, '+');
                        if ($pos === 0) {
                            $value = substr($value, 1);
                            $addPlus = true;
                        }

                        $value = preg_replace("/[^0-9]/", "", $value);

                        if ($addPlus == true) {
                            $value = '+' . $value;
                        }
                    }


                }
            } catch (NumberParseException $e) {
            }
            return $value;
        }
    }

    public function unPrefixPhoneNumber($value)
    {
        if ($value === null) {
            return null;
        } else {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $tmp = $phoneUtil->parse($value, "DE");
                if ($tmp instanceof PhoneNumber) {
                    $value = (string)$phoneUtil->format($tmp, PhoneNumberFormat::NATIONAL);

                    /**
                     * If it's an uncommon phone number, where a "0" in front of it is not possible,
                     * fall back to +49 phone number.
                     */
                    if (substr($value, 0, 1) != "0") {
                        $value = $phoneUtil->format($tmp, PhoneNumberFormat::E164);
                    }
                }
            } catch (NumberParseException $e) {
            }
            return str_replace(" ", "", $value);
        }
    }

    public function getAreaCodeOfPhoneNumber($value)
    {
        if ($value === null) {
            return null;
        } else {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $tmp = $phoneUtil->parse($value, "DE");
                if ($tmp instanceof PhoneNumber) {
                    $value = (string)$phoneUtil->format($tmp, PhoneNumberFormat::NATIONAL);

                    /**
                     * If it's an uncommon phone number, where a "0" in front of it is not possible,
                     * fall back to +49 phone number.
                     */
                    if (substr($value, 0, 1) != "0") {
                        return null;
                    }
                }
            } catch (NumberParseException $e) {
            }
            return explode(" ", $value)[0];
        }
    }

}