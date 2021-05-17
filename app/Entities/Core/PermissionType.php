<?php

namespace App\Entities\Core;

class PermissionType
{
  /** user absence */
  const USER_ABSENCE_SHOW_TABLE = 'benutzerabwesenheiten_tabelle_anzeigen';
  const USER_ABSENCE_SHOW_EXTEND_TABLE = 'benutzerabwesenheiten_tabelle_anzeigen_erweitert';
  const USER_ABSENCE_ADD = 'benutzerabwesenheiten_hinzufuegen';
  const USER_ABSENCE_EDIT = 'benutzerabwesenheiten_editieren';
  const USER_ABSENCE_DELETE = 'benutzerabwesenheiten_loeschen';

  /** duty configurator_task_blocks */

  const DUTY_CONFIGURATOR_TASK_BLOCKS_SHOW = 'aufgabenkonfigurator_bloecke_anzeigen';
  const DUTY_CONFIGURATOR_TASK_BLOCKS_ADD = 'aufgabenkonfigurator_bloecke_hinzufuegen';
  const DUTY_CONFIGURATOR_TASK_BLOCKS_EDIT = 'aufgabenkonfigurator_bloecke_editieren';
  const DUTY_CONFIGURATOR_TASK_BLOCKS_DELETE = 'aufgabenkonfigurator_bloecke_loeschen';

  /** duty configurator_task_lines */

  const DUTY_CONFIGURATOR_TASK_LINES_SHOW = 'aufgabenkonfigurator_zeilen_anzeigen';
  const DUTY_CONFIGURATOR_TASK_LINES_ADD = 'aufgabenkonfigurator_zeilen_hinzufuegen';
  const DUTY_CONFIGURATOR_TASK_LINES_EDIT = 'aufgabenkonfigurator_zeilen_editieren';
  const DUTY_CONFIGURATOR_TASK_LINES_DELETE = 'aufgabenkonfigurator_zeilen_loeschen';


  /** duty configurator_tasks */
  const DUTY_CONFIGURATOR_TASKS_SHOW = 'aufgabenkonfigurator_aufgaben_anzeigen';
  const DUTY_CONFIGURATOR_TASKS_ADD = 'aufgabenkonfigurator_aufgaben_hinzufuegen';
  const DUTY_CONFIGURATOR_TASKS_EDIT = 'aufgabenkonfigurator_aufgaben_editieren';
  const DUTY_CONFIGURATOR_TASKS_DELETE = 'aufgabenkonfigurator_aufgaben_loeschen';

  /** duty configurator_follow_up_action */
  const DUTY_CONFIGURATOR_FOLLOWUP_ACTION_SHOW = 'duty_configurator_followup_action_show';
  const DUTY_CONFIGURATOR_FOLLOWUP_ACTION_ADD = 'duty_configurator_followup_action_add';
  const DUTY_CONFIGURATOR_FOLLOWUP_ACTION_EDIT = 'duty_configurator_followup_action_edit';
  const DUTY_CONFIGURATOR_FOLLOWUP_ACTION_DELETE = 'duty_configurator_followup_action_delete';

  /*
  * Company Details/ Overview Section
  */
  /** company_detail_registration_options_block */
  const COMPANY_DETAILS_REGISTRATION_OPTIONS_BLOCK_SHOW = 'kundenmaske_registrierungsoptionen_block_anzeigen';
  const COMPANY_DETAILS_REGISTRATION_OPTIONS_BLOCK_EDIT = 'kundenmaske_registrierungsoptionen_editieren';

  /** company_detail_website_data_block */
  const COMPANY_DETAILS_WEBSITE_DATA_BLOCK_SHOW = 'kundenmaske_website_daten_block_anzeigen';
  const COMPANY_DETAILS_WEBSITE_DATA_BLOCK_EDIT = 'kundenmaske_website_daten_editieren';

  /** company_detail_revocation_or_cancellation_block */
  const COMPANY_DETAILS_REVOCATION_OR_CANCELLATION_BLOCK_SHOW = 'kundenmaske_widerruf_kuendigung_block_anzeigen';
  const COMPANY_DETAILS_REVOCATION_OR_CANCELLATION_BLOCK_EDIT = 'kundenmaske_widerruf_kuendigung_editieren';

  /** company_detail_customer_data_block */
  const COMPANY_DETAILS_CUSTOMER_DATA_BLOCK_SHOW = 'kundenmaske_kundendaten_block_anzeigen';
  const COMPANY_DETAILS_CUSTOMER_DATA_BLOCK_EDIT = 'kundenmaske_kundendaten_editieren';

  /** company_detail_werbeaktion_block */
  const COMPANY_DETAILS_WERBREAKTION_BLOCK_SHOW = 'kundenmaske_werbeaktion_block_anzeigen';
  const COMPANY_DETAILS_WERBREAKTION_BLOCK_EDIT = 'kundenmaske_werbeaktion_editieren';


  /** company_detail_verkaufsdaten_block */
  const COMPANY_DETAILS_VERKAUFSDATEN_BLOCK_SHOW = 'kundenmaske_verkaufsdaten_anzeigen';

  /** company_detail_notizen_zum_unternehmen_block */
  const COMPANY_DETAILS_NOTES_FOR_COMPANY_BLOCK_SHOW = 'kundenmaske_notizen_block_anzeigen';
  const COMPANY_DETAILS_NOTES_FOR_COMPANY_BLOCK_ADD = 'kundenmaske_notizen_hinzufuegen';
  const COMPANY_DETAILS_NOTES_FOR_COMPANY_BLOCK_EDIT = 'kundenmaske_notizen_editieren';

  /** company_detail_offene_aufgaben_block */
  const COMPANY_DETAILS_OPEN_TASKS_BLOCK_SHOW = 'kundenmaske_offene_aufgaben_block_anzeigen';

  /** company_detail_erledigte_aufgaben_block */
  const COMPANY_DETAILS_DONE_TASKS_BLOCK_SHOW = 'kundenmaske_erledigte_aufgaben_block_anzeigen';

  /** company_detail_termine_block */
  const COMPANY_DETAILS_APPOINTMENTS_BLOCK_SHOW = 'kundenmaske_termine_block_anzeigen';
  const COMPANY_DETAILS_APPOINTMENTS_BLOCK_ADD = 'kundenmaske_termine_hinzufuegen';
  const COMPANY_DETAILS_APPOINTMENTS_BLOCK_EDIT = 'kundenmaske_termine_editieren';

  /** company_detail_optional_block */
  const COMPANY_DETAILS_OPTIONAL_BLOCK_SHOW = 'kundenmaske_sonstiges_block_anzeigen';
  const COMPANY_DETAILS_OPTIONAL_BLOCK_EDIT = 'kundenmaske_sonstiges_editieren';

  const VOCATIONAL_SCHOOL_SCHEDULE_SHOW = 'vocational-school-schedule-show';
  const VOCATIONAL_SCHOOL_SCHEDULE_ADD = 'vocational-school-schedule-add';
  const VOCATIONAL_SCHOOL_SCHEDULE_EDIT = 'vocational-school-schedule-edit';
  const VOCATIONAL_SCHOOL_SCHEDULE_DELETE = 'vocational-school-schedule-delete';
}
