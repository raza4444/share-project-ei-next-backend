<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Publics\Branches;


use App\Http\Controllers\Publics\AbstractPublicsController;
use App\Services\Branches\StatsService;
use Illuminate\Http\Request;

class PublicStatsController extends AbstractPublicsController
{

    private $statsService;

    public function __construct(
        StatsService $statsService
    )
    {
        $this->statsService = $statsService;
    }

    /**
     * Liefert eine Statistik
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPublicStats(Request $request)
    {

        $date = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $view = $request->has('view') && $request->get('view') == 1;

        $result = [$this->statsService->getUserStatsCold($date), $this->statsService->getUserStatsWarm($date)];

        if ($view) {
            echo "<b>Kalt</b><br>";
            foreach ($result[0] as $row) {
                echo $row['username'] . ' ' . $row['appointments'] . ' ' . $row['sales'] . '<br>';
            }
            echo '<br>';
            echo "<b>Warm</b><br>";
            foreach ($result[1] as $row) {
                echo $row[0] . ' ' . $row[1] . ' ' . $row[2] . '<br>';
            }
        } else {
            return $this->singleJson($result);
        }

    }

}