<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;
use App\Utils\StringUtils;

/**
 * Class LocationEventMatcherRule
 *
 * @property int categoryId
 * @property int wd0_hour_start
 * @property int wd0_hour_end
 * @property int wd1_hour_start
 * @property int wd1_hour_end
 * @property int wd2_hour_start
 * @property int wd2_hour_end
 * @property int wd3_hour_start
 * @property int wd3_hour_end
 * @property int wd4_hour_start
 * @property int wd4_hour_end
 * @property int wd5_hour_start
 * @property int wd5_hour_end
 * @property int wd6_hour_start
 * @property int wd6_hour_end
 *
 */

class LocationEventMatcherRule extends AbstractModel
{
    protected $table = 'campaign_location_event_matcher_rules';

    /**
     * @return LocationEventMatcherRule
     */
    public static function newEmpty()
    {
        $r = new LocationEventMatcherRule();
        for ($i = 0; $i < 7; $i++) $r->setWeekdayHours($i, null, null);
        return $r;
    }

    public function setWeekdayHours($wd, $start, $end)
    {
        $this->setAttribute('wd' . $wd . '_hour_start', $start);
        $this->setAttribute('wd' . $wd . '_hour_end', $end);
    }

    public function saveHightlanderCategory()
    {
        self::query()->where('categoryId', '=', $this->categoryId)->delete();
        $this->save();
    }

    public function category()
    {
        return $this->hasOne(LocationCategory::class, 'id', 'categoryId');
    }

    public function hasHoursForWeekday($wd)
    {
        return StringUtils::toInt($this->getAttribute('wd' . $wd . '_hour_start')) != null;
    }
}
