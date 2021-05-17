<?php

use Illuminate\Database\Seeder;
use App\Entities\Core\AbsenceTypes;

class AbsenceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = array('Krank', 'Urlaub', 'Schule');
        foreach ($types as $type) {
            AbsenceTypes::create([
                'name' => $type,
            ]);
        }
    }
}
