<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\ContactFormAutomation;

use App\Entities\Core\AbstractModel;

/**
 * Class CFAProcess
 * @package App\Entities\ContactFormAutomation
 *
 */
class CFAProcess extends AbstractModel
{
  protected $fillable = ['id', 'pid', 'user_id', 'status'];
  protected $table = "contact_form_automator_process";

  public function cfaSubProcess()
  {
    return $this->hasMany('App\Entities\ContactFormAutomation\CFASubProcess', 'contact_form_automator_process_id');
  }
}
