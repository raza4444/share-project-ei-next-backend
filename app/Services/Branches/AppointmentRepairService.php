<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Entities\Repair\RepairAble;
use Illuminate\Support\Facades\DB;

class AppointmentRepairService implements RepairAble
{
    function repair()
    {
        $q = 'select id,preAppointmentId from appointments where preAppointmentId is not null';
        $list = DB::select($q);

        foreach ($list as $app) {
            $preId = $app->preAppointmentId;
            echo $preId;
            $nextId = $app->id;
            $q = "update appointments set nextAppointmentId=$nextId where id=$preId and nextAppointmentId is null";
            DB::update($q);
        }

    }


}