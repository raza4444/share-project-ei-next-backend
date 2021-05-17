<?php

namespace MyTests\Integration;

use App\Entities\Branches\Appointment;

class AppointmentIntegrationTest extends \MyTests\Integration\AbstractIntegrationTest
{

    public function afterSetUp()
    {
    }

    public function beforeTearDown()
    {
        $this->deleteAllOfTable('users');
        $this->deleteAllOfTable('campaign_locations');
        $this->deleteAllOfTable('appointments');
    }

    public function createCompany($name, $werbeaktion)
    {
        $l = new \App\Entities\Branches\Location();
        $l->title = $name;
        $l->werbeaktion = $werbeaktion;
        $l->save();
        return $l;
    }

    public function createWarmAppointment($companyId, $when, $createdUserId = 1)
    {
        $a = new Appointment();
        $a->locationId = $companyId;
        $a->when = $when;
        $a->createdUserId = $createdUserId;
        $a->save();
        return $a;
    }

    public function testUpdateResult_ResultOnlyOnce()
    {
        //deaktiviert
        $ts = $this->getTestDataService();
        $admin = $ts->createAdminUser();
        $c1 = $this->createCompany('c1', 'xing');
        $a1 = $this->createWarmAppointment($c1->id, '2019-10-10 13:30:00');

        $headers = ['apitoken' => $this->login($admin->username, 'test')->apitoken];

        //no auth
        $this->json('patch', "rest/intern/appointments/0/result", [])->seeStatusCode(401);

        //auth, but not found
        $this->json('patch', "rest/intern/appointments/0/result", [], $headers)->seeStatusCode(404);

        //update to default result
        $this->json('patch', "rest/intern/appointments/{$a1->id}/result", ['result' => Appointment::RESULT_GESCHEITERT], $headers)
            ->seeStatusCode(200)
            ->seeInDatabase('appointments', ['id' => $a1->id, 'result' => Appointment::RESULT_GESCHEITERT]);

        //once result is set, it is always set
        $this->json('patch', "rest/intern/appointments/{$a1->id}/result", ['result' => Appointment::RESULT_VERKAUFT], $headers)
            ->seeStatusCode(400)
            ->seeInDatabase('appointments', ['id' => $a1->id, 'result' => Appointment::RESULT_GESCHEITERT]);

    }

}
