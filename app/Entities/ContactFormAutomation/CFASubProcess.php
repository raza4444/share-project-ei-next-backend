<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\ContactFormAutomation;

use App\Entities\Core\AbstractModel;

/**
 * Class CFASubProcess
 * @package App\Entities\ContactFormAutomation
 *
 */
class CFASubProcess extends AbstractModel
{
    protected $fillable = ['id', 'contact_form_automator_process_id', 'log_number', 'pid', 'status'];
    protected $table = "contact_form_automator_sub_process";

    public function cfaLogs()
  {
    return $this->hasMany(CFALogs::class, 'sub_process_id', 'id');
  }
}
