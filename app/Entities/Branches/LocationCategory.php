<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationCategory extends AbstractModel
{
    use SoftDeletes;

    public $table = "campaign_location_categories";

}
