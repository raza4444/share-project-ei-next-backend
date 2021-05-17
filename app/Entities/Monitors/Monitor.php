<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Monitors;


use App\Entities\Core\AbstractModel;
use App\Utils\DateTimeUtils;

/**
 * Class Monitor
 * @package App\Entities\Monitor
 * @property string realm
 * @property int referenceid
 * @property int state
 * @property string message
 * @property string lastupdate
 * @property string created
 */
class Monitor extends AbstractModel
{

    protected $table = 'monitors';

    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(MonitorDetail::class, 'monitorid', 'id');
    }

    public function withLastUpdateToNow()
    {
        $this->lastupdate = DateTimeUtils::nowAsString();
        return $this;
    }

    public function withGlobalState($state, $message = null)
    {
        $this->state = $state;
        $this->message = $message;
        return $this->withLastUpdateToNow();
    }

    public function withNewDetail($state, $message)
    {

        $d = new MonitorDetail();
        $d->state = $state;
        $d->message = $message;
        $d->monitorid = $this->id;
        $d->save();

        return $this->withLastUpdateToNow();
    }

}
