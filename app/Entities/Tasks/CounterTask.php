<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Tasks;


use App\Entities\Core\AbstractModel;

/**
 * Class CounterTask
 * @package App\Entities\Tasks
 * @property string name
 * @property string title
 *
 */
class CounterTask extends AbstractModel
{

    protected $table = 'counter_tasks';

    protected $fillable = ['name', 'title'];

}
