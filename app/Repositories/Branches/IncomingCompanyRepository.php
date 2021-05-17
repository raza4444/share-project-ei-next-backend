<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Branches;

use App\Entities\Branches\IncomingCompany;

class IncomingCompanyRepository
{

    /**
     * @param $id
     * @return IncomingCompany
     */
    public function findById($id)
    {
        return IncomingCompany::query()->find($id);
    }

    /**
     * @return IncomingCompany[]
     */
    public function findAllNonTransformed()
    {
        return IncomingCompany::query()->where('status', '=', 0)->get();
    }

}
