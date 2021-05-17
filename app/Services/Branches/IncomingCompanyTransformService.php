<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;

use App\Entities\Branches\IncomingCompany;
use App\Repositories\Branches\IncomingCompanyRepository;
use App\Repositories\Branches\LocationRepository;
use App\Repositories\States\FederalStateRepository;

class IncomingCompanyTransformService
{

    private $incomingCompanyRepository;

    private $schoolRepository;

    private $federalStateRepository;

    public function __construct(
        IncomingCompanyRepository $incomingCompanyRepository,
        LocationRepository $schoolRepository,
        FederalStateRepository $federalStateRepository
    )
    {
        $this->incomingCompanyRepository = $incomingCompanyRepository;
        $this->schoolRepository = $schoolRepository;
        $this->federalStateRepository = $federalStateRepository;
    }

    /**
     * @param IncomingCompany $i
     * @return int|\Modules\Shared\Entities\Campaign\Location\School
     * 0 - incoming company not found
     * 1 - could not create school (dublette?)
     * 2 - could not create school coz of duplicate phone number
     * school - found and transformed
     * @throws \Exception
     */
    public function transformSingleImportedCompany(IncomingCompany $i)
    {
        //branche bestimmen und anlegen
        $brancheName = $i->branche;

        $federalState = $this->federalStateRepository->findOrCreateByNameForGermany($i->bundesland);

        if ($this->schoolRepository->existsByPhoneNumber($i->telefonnummer)) {
            $i->status = 3;
            $i->save();
            return 2;
        }

        $school = $this->schoolRepository->importSchool(
            $i->name,
            $i->ort,
            $federalState,
            $i->land,
            $i->telefonnummer,
            null,
            $i->email,
            $brancheName,
            $i->plz,
            $i->strasse
        );

        if ($school == null) {
            $i->status = 2;
            $i->save();
            return 1;
        } else {
            $i->status = 1;
            $i->locationId = $school->id;
            $i->save();
            return $school;
        }

    }

    public function transformSingleImportedCompanyById($id)
    {
        //load imported entry
        $i = $this->incomingCompanyRepository->findById($id);
        if ($i === null) {
            return 0;
        }

        //keine dubletten uebernehmen
        if ($i->status != 0) {
            return 0;
        }

        return $this->transformSingleImportedCompany($i);
    }

}