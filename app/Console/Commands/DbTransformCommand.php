<?php
/**
 * by stephan scheide
 */

namespace App\Console\Commands;


use App\ValueObjects\EasyCounter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbTransformCommand extends Command
{

    protected $name = 'application:db-transform';

    public function handle()
    {
        return $this->handleTransformSslJobs20201016();
    }

    private function handleTransformSslJobs20201016()
    {

        $cc = new EasyCounter();

        $this->info('handleTransformSslJobs20201016');
        $jobs = DB::select("select * from ssljobs2 where name='generate-cert'");
        foreach ($jobs as $job) {
            $id = $job->locationid;
            $q = "update campaign_locations set last_ssl_error_message=?,status_cert_gen=?,last_ssl_error=?,ssl_count_processed_gen=?,last_cert_gen=?,last_cert_gen_touched=? where id=$id";
            $this->info($q);
            DB::update($q, [
                $job->lasterrortext,
                $job->status,
                $job->lasterror,
                $job->countprocessed,
                $job->lastcertificatecreation,
                $job->lasttouched
            ]);
            $cc->inc('transformed-gens');
        }

        $jobs = DB::select("select * from ssljobs2 where name='import-cert'");
        foreach ($jobs as $job) {
            $id = $job->locationid;
            $q = "update campaign_locations set last_ssl_error_message=?,status_cert_import=?,last_ssl_error=?,ssl_count_processed_import=?,last_cert_import_touched=?,ssl_options=? where id=$id";
            $this->info($q);
            DB::update($q, [
                $job->lasterrortext,
                $job->status,
                $job->lasterror,
                $job->countprocessed,
                $job->lasttouched,
                $job->options
            ]);
            $cc->inc('transformed-imports');
        }

        $this->info($cc);
    }

    private function handleTransformAppointments()
    {
        $this->st('delete from appointments');
        $this->st('ALTER TABLE appointments AUTO_INCREMENT = 1');

        //Die Tabelle Event zu Termin wird 1:1 übernommen
        $cols = "id,eventId,createdUserId,finishedUserId,preAppointmentId,nextAppointmentId,result,`when`,erinnernAm,nachgehenAm,status,created_at,updated_at,finished_at,seller,ansprechpartner_anrede,ansprechpartner_vorname,ansprechpartner_nachname,preisinfo,assignedUserId";
        $this->st("insert into appointments ($cols) select $cols from location_event_appointments");

        //LocationID nachbestuecken (aus event)
        $list = DB::select('select id,eventId from appointments');
        foreach ($list as $e) {
            $eventId = $e->eventId;
            $id = $e->id;
            $sid = $this->first("select l.id as sid from campaign_locations l inner join campaign_location_events e on e.schoolid=l.id where e.id=$eventId", 'sid');
            $this->st("update appointments set locationId=$sid where id=$id");
        }

        //Letzte ID
        $lastId = $this->first('select max(id) as id from location_event_appointments', 'id');

        //Nun die Termine für Direkt
        //Hier müssen wir die IDs anpassen
        $list = DB::select('select * from location_appointments');

        $idMap = [];
        foreach ($list as $entry) {
            $id = $entry->id;
            $newId = ++$lastId;
            $idMap[$id] = $newId;

            $arr = get_object_vars($entry);
            $arr['id'] = $newId;

            DB::table('appointments')->insert($arr);
        }

        foreach ($idMap as $oldId => $newId) {
            $q = "UPDATE appointments SET preAppointmentId=$newId where eventId=0 and preAppointmentId=$oldId";
            $this->st($q);
            $q = "UPDATE appointments SET nextAppointmentId=$newId where eventId=0 and nextAppointmentId=$oldId";
            $this->st($q);
        }

    }

    private function st($q)
    {
        DB::statement($q);
    }

    private function first($q, $name = 'anzahl')
    {
        $r = DB::select($q);
        return count($r) > 0 ? $r[0]->$name : 0;
    }

}
