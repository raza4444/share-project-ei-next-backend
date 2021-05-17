<?php

use Illuminate\Database\Seeder;
use App\Repositories\Mail\MailTemplateRepository;
use App\Entities\Mail\MailPlaceholders;

class MailPlaceholdersSeeder extends Seeder
{

    private $mailTemplateRepository;
    
    public function __construct(MailTemplateRepository $mailTemplateRepository) {
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allMailPlaceholders = $this->mailTemplateRepository->mailPlaceholders();
        foreach($allMailPlaceholders as $placeholder) {
        mailPlaceholders::create([
            'name' => $placeholder,
        ]);
     }
    }
}
