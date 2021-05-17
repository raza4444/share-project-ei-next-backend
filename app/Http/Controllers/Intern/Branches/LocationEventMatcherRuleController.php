<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Branches;


use App\Entities\Branches\LocationEventMatcherRule;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Branches\LocationEventMatcherRuleRepository;
use App\Utils\StringUtils;
use Illuminate\Http\Request;

class LocationEventMatcherRuleController extends AbstractInternController
{

    private $locationEventMatcherRuleRepository;

    public function __construct(
        LocationEventMatcherRuleRepository $locationEventMatcherRuleRepository
    )
    {
        $this->locationEventMatcherRuleRepository = $locationEventMatcherRuleRepository;
    }

    public function all()
    {
        $rules = LocationEventMatcherRule::query()->get();
        return $this->json(200, $rules);
    }

    public function create(Request $request)
    {
        $doSave = false;
        $rule = LocationEventMatcherRule::newEmpty();
        $rule->categoryId = $request->get('categoryId');
        for ($w = 0; $w < 7; $w++) {
            $start = StringUtils::toInt($request->get('wd_' . $w . '_hour_start'));
            $end = StringUtils::toInt($request->get('wd_' . $w . '_hour_end'));
            if ($start !== null && $end !== null) {
                $doSave = true;
                $rule->setWeekdayHours($w, $start, $end);
            }
        }

        if ($doSave) {
            $rule->saveHightlanderCategory();
        }

        return $this->created();
    }

    public function delete($id)
    {
        $this->locationEventMatcherRuleRepository->deleteById($id);
    }


}
