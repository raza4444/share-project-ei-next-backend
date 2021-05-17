<?php
/**
 * by stephan scheide
 */

namespace App\Entities\States;


use App\Entities\Core\AbstractModel;

/**
 * Class FederalState
 * @package App\Entities\States
 *
 * @property string name
 *
 */
class FederalState extends AbstractModel
{
    protected $table = 'bundeslaender';
}
