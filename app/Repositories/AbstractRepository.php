<?php
/**
 * by stephan scheide
 */

namespace App\Repositories;

use App\Entities\Branches\Location;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AbstractRepository
{

    private $clazz;

    public function __construct($modelClass)
    {
        $this->clazz = $modelClass;
    }

    public function all()
    {
        return $this->query()->get();
    }

    public function allWithoutDeleted()
    {
        return $this->query()->whereNull('deleted_at')->get();
    }

    /**
     * @param $id
     * @return Location|null
     */
    public function byId($id)
    {
        return $this->query()->where('id', '=', $id)->first();
    }

    public function deleteById($id)
    {
        return $this->query()->where('id', '=', $id)->delete();
    }

    public function deactivateByLocationId($locationId)
    {
        return $this->query()->where('schoolid', '=', $locationId)->update(['deleted_at' => Carbon::now()]);
    }

    public function byIdActive($id)
    {
        return $this->query()
            ->where('id', '=', $id)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * @return Builder
     */
    protected function query()
    {
        return call_user_func([$this->clazz, 'query']);
    }

    /**
     * @return Builder
     */
    public function getQuery()
    {
        return $this->query();
    }

    /**
     * @return string
     */
    public function getTable()
    {
        $c = new $this->clazz();
        return $c->table;
    }

}
