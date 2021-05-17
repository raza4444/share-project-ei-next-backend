<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Monitors;


use App\Entities\Core\AbstractModel;

/**
 * Class MonitorDetail
 * @package App\Entities\Monitor
 *
 * @property int monitorid
 * @property string message
 * @property int state
 */
class MonitorDetail extends AbstractModel
{

    protected $table = 'monitor_details';

    public $timestamps = false;

}
