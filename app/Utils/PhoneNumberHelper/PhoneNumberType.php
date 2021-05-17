<?php
/**
 * Created by PhpStorm.
 * User: jannis2
 * Date: 12/10/15
 * Time: 18:23
 */

namespace App\Utils\PhoneNumberHelper;

use MyCLabs\Enum\Enum;

/**
 * Class PhoneNumberType
 * @package Modules\Shared\Misc\Telephone\PhoneNumberHelper
 *
 * @method static UNKNOWN() PhoneNumberType
 * @method static LANDLINE() PhoneNumberType
 * @method static MOBILE() PhoneNumberType
 */
class PhoneNumberType extends Enum
{

    const UNKNOWN = "unknown";

    const LANDLINE = "landline";

    const MOBILE = "mobile";

}