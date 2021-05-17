<?php

use App\Http\RouteUtils;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->get('/', function () use ($router) {
  return $router->app->version() . ' ' . date('Y-m-d H:i:s');
});

/**
 * @var $router \Laravel\Lumen\Routing\Router
 */
$router->group(['prefix' => 'rest'], function () use ($router) {

  $router->get('test', function () {
    echo "OK";
  });

  $router->get('repair', 'Publics\RepairController@repair');

  /**
   * Oeffentliches
   */
  $router->group(['prefix' => 'public'], function () use ($router) {
    $router->post('userlogin', 'Publics\UserLoginController@postLogin');
    $router->post('customerlogin', 'Publics\Customers\CustomerLoginController@login');
    $router->post('v1/customer-registrations-complete', 'Publics\Customers\CustomerRegistrationController@completeRegistration');
    $router->get('stats', 'Publics\Branches\PublicStatsController@getPublicStats');

    $router->post('users', 'Publics\Users\PublicUserController@createNarevUser');
    $router->post('narev-users/{id}/tokens/all', 'Publics\Users\PublicUserController@setSingleTokenOfUser');

    //Fuer Importlogik (js mit chrome)
    $router->get('v1/ssl-jobs', 'Publics\Ssl\SslJobsController@getJobs');
    $router->patch('v1/ssl-jobs/{id}/state', 'Publics\Ssl\SslJobsController@changeState');
  });

  /**
   * Intern
   */
  $router->group(['prefix' => 'intern', 'middleware' => 'auth'], function () use ($router) {

    $router->get('v1/ssl-verify-results', 'Intern\Ssl\SslVerifyResultController@all');

    /**
     * punkte nur fuer den Administrator
     */
    $router->group(['prefix' => 'admin'], function () use ($router) {

      //Benutzer
      $router->post('users', 'Admin\Users\UsersController@create');
      $router->get('users', 'Admin\Users\UsersController@all');
      $router->delete('users/{id}', 'Admin\Users\UsersController@deactivate');
      $router->get('users/{id}', 'Admin\Users\UsersController@byId');
      $router->put('users/{id}', 'Admin\Users\UsersController@update');
      $router->post('users/{userId}/individual-permissions', 'Admin\Users\UsersController@updateIndividualPermissions');
      $router->put('users/update/many', 'Admin\Users\UsersController@updateManyUsers');
      $router->get('user-roles/{id}/user-permissions', 'Admin\Users\PermissionsController@allForUserRole');

      $router->group(['prefix' => 'permissions'], function () use ($router) {
      $router->get('/', 'Admin\Users\PermissionsController@all');
      $router->delete('/{id}', 'Admin\Users\PermissionsController@delete');
      $router->post('/', 'Admin\Users\PermissionsController@create');
      $router->post('/{id}', 'Admin\Users\PermissionsController@update');
      $router->get('/name', 'Admin\Users\PermissionsController@onlyName');
      $router->get('/{type}', 'Admin\Users\PermissionsController@allWithSpecificType');
      });

      $router->get('user-roles', 'Admin\Users\RolesController@all');
      $router->get('user-roles-with-permissions', 'Admin\Users\RolesController@allWithPermissions');
      $router->get('user-roles/{roleId}', 'Admin\Users\RolesController@allPermissions');
      $router->post('user-roles/{roleId}/user-permissions', 'Admin\Users\RolesController@linkPermission');
      $router->delete('user-roles/{roleId}/user-permissions/{permissionId}', 'Admin\Users\RolesController@unlinkPermission');
      $router->delete('user-roles/{id}', 'Admin\Users\RolesController@delete');
      $router->post('user-roles', 'Admin\Users\RolesController@create');
      $router->post('user-roles/{id}', 'Admin\Users\RolesController@update');
      $router->get('permission-types', 'Admin\Users\PermissionTypeController@all');
      $router->delete('permission-types/{id}', 'Admin\Users\PermissionTypeController@delete');
      $router->post('permission-types', 'Admin\Users\PermissionTypeController@create');
      $router->post('permission-types/{id}', 'Admin\Users\PermissionTypeController@update');

      //Benutzerabwesenheiten
      $router->get('absences/types', 'Admin\Users\UserAbsencesController@absenceTypes');
      $router->get('absences/{id}', 'Admin\Users\UserAbsencesController@singleUserAbsence');
      $router->get('users/{userId}/absences', 'Admin\Users\UserAbsencesController@allOfUser');
      $router->post('users/{userId}/absences', 'Admin\Users\UserAbsencesController@create');
      $router->post('absences/{id}', 'Admin\Users\UserAbsencesController@update');
      $router->delete('absences/{id}', 'Admin\Users\UserAbsencesController@deleteById');

      //Berufsschule
      $router->get('vocational-school', 'Admin\Users\VocationalSchoolController@all');
      $router->get('vocational-school/{id}', 'Admin\Users\VocationalSchoolController@single');
      $router->post('vocational-school/{userId}/', 'Admin\Users\VocationalSchoolController@create');
      $router->put('vocational-school/{id}', 'Admin\Users\VocationalSchoolController@update');
      $router->delete('vocational-school/{id}', 'Admin\Users\VocationalSchoolController@deleteById');
      //Menu
      $router->get('menu', 'Admin\MenuAdminController@menu');
      $router->post('menu', 'Admin\MenuAdminController@create');
      $router->put('menu/{id}', 'Admin\MenuAdminController@updateMenu');
      $router->delete('menu/{id}', 'Admin\MenuAdminController@deleteMenu');

      $router->get('menu-items', 'Admin\MenuAdminController@menuItems');
      $router->get('menu-items/{id}', 'Admin\MenuAdminController@getMenuItemById');
      $router->post('menu-items', 'Admin\MenuAdminController@createMenuItem');
      $router->delete('menu-items/{id}', 'Admin\MenuAdminController@deleteMenuItem');
      $router->put('menu-items/many', 'Admin\MenuAdminController@updateManyMenuItems');
      $router->post('menu-items/{menuId}', 'Admin\MenuAdminController@updateMenuItem');

      //interne Kontrollen
      $router->get('control', 'Admin\ControlController@control');
    });

    /**
     * Normale, angemeldete Benutzer
     */

    //Logout
    $router->post('userlogout/{userId}', 'Publics\UserLoginController@postLogout');

    //Menustruktur
    $router->get('menu', 'Intern\MenuController@menuStructure');

    //Benutzerabwesenheiten
    $router->get('absences', 'Admin\Users\UserAbsencesController@all');

    //Kategorien
    \App\Http\RouteUtils::internDefaults($router, 'location-categories', 'Branches', 'LocationCategory');

    //Bundesstaaten
    RouteUtils::internDefaults($router, 'federal-states', 'States', 'FederalState');

    //WhatsApp Notification an Unternehmen
    $router->post('notifications', 'Intern\Notifications\WhatsAppNotificationsController@sendNotification');

    //Ereignisse
    $router->post('location-events/from-location', \App\Http\RouteUtils::fullMethodeName('Branches', 'LocationEvent', 'createFromLocation'));
    $router->get('location-events/for-events-per-state-and-cat', RouteUtils::fullMethodeName('Branches', 'LocationEvent', 'eventsPerStateAndCat'));
    $router->get('location-events/{id}', RouteUtils::fullMethodeName('Branches', 'LocationEvent', 'byId'));
    $router->get('location-events/', RouteUtils::fullMethodeName('Branches', 'LocationEvent', 'findGeneric'));

    //Regeln pro Kategorie
    $router->get('location-event-matcher-rules', RouteUtils::fullMethodeName('Branches', 'LocationEventMatcherRule', 'all'));
    $router->post('location-event-matcher-rules', RouteUtils::fullMethodeName('Branches', 'LocationEventMatcherRule', 'create'));
    $router->delete('location-event-matcher-rules/{id}', RouteUtils::fullMethodeName('Branches', 'LocationEventMatcherRule', 'delete'));

    //Ereignisse zum Abarbeiten
    $router->get('events/for-work/count', RouteUtils::fullMethodeName('Branches', 'LocationEventsWorking', 'countToDo'));
    $router->get('events/for-work/next', RouteUtils::fullMethodeName('Branches', 'LocationEventsWorking', 'nextEventForWork'));
    $router->post('events/{id}/for-work', RouteUtils::fullMethodeName('Branches', 'LocationEventsWorking', 'saveResult'));

    //Statistik: Ergebnisse nach Benutzern
    $router->get('statistics/results-by-user', RouteUtils::fullMethodeName('Statistics', 'ResultsByUser', 'all'));

    //moegliche Termine
    $router->get('possible-appointments/{appointmentTypeId}/default-week', 'Intern\Appointments\PossibleAppointmentsController@getDefaultWeek');
    $router->put('possible-appointments/{appointmentTypeId}/default-week', 'Intern\Appointments\PossibleAppointmentsController@updateDefaultWeek');
    $router->get('possible-appointments/{appointmentTypeId}/week/ymd/{ymd}', 'Intern\Appointments\PossibleAppointmentsController@getWeekYmd');
    $router->put('possible-appointments/{appointmentTypeId}/week/ymd/{ymd}', 'Intern\Appointments\PossibleAppointmentsController@updateWeek');
    $router->get('possible-appointments/{appointmentTypeId}/effective-week/ymd/{ymd}', 'Intern\Appointments\PossibleAppointmentsController@getEffectiveWeekYmd');

    //Kunden
    $router->get('customers/for-domain-setup', RouteUtils::fullMethodeName('Customers', 'Customer', 'getAllDomainConfiguration'));

    //Monitore (Ueberwachung/Protokoll von außen)
    $router->post('monitors', RouteUtils::fullMethodeName('Monitors', 'Monitor', 'append'));
    $router->get('monitors', RouteUtils::fullMethodeName('Monitors', 'Monitor', 'find'));
    $router->get('monitors/for-domain-setup', RouteUtils::fullMethodeName('Monitors', 'Monitor', 'findForDomainSetup'));

    //API-Tokens
    $router->delete('apitokens/{token}', RouteUtils::fullMethodeName('Core', 'ApiToken', 'delete'));
    $router->get('apitokens/{token}/user', RouteUtils::fullMethodeName('Core', 'ApiToken', 'getUserByToken'));

    // Event Settings

    // Names
    $router->get('branches/events/settings/event-data-names', 'Intern\Branches\Events\EventDataNameController@all');
    $router->post('branches/events/settings/event-data-names', 'Intern\Branches\Events\EventDataNameController@create');
    $router->delete('branches/events/settings/event-data-names/{id}', 'Intern\Branches\Events\EventDataNameController@delete');
    $router->post('branches/events/settings/event-data-names/{id}', 'Intern\Branches\Events\EventDataNameController@update');

    // Greeting Names
    $router->get('branches/events/settings/event-data-greeting-names', 'Intern\Branches\Events\EventDataGreetingNameController@all');
    $router->post('branches/events/settings/event-data-greeting-names', 'Intern\Branches\Events\EventDataGreetingNameController@create');
    $router->delete('branches/events/settings/event-data-greeting-names/{id}', 'Intern\Branches\Events\EventDataGreetingNameController@delete');
    $router->post('branches/events/settings/event-data-greeting-names/{id}', 'Intern\Branches\Events\EventDataGreetingNameController@update');

    // Subjects
    $router->get('branches/events/settings/event-data-subjects', 'Intern\Branches\Events\EventDataSubjectController@all');
    $router->post('branches/events/settings/event-data-subjects', 'Intern\Branches\Events\EventDataSubjectController@create');
    $router->delete('branches/events/settings/event-data-subjects/{id}', 'Intern\Branches\Events\EventDataSubjectController@delete');
    $router->post('branches/events/settings/event-data-subjects/{id}', 'Intern\Branches\Events\EventDataSubjectController@update');

    // Zip Codes
    $router->get('branches/events/settings/event-data-zip-codes', 'Intern\Branches\Events\EventDataZipController@all');
    $router->post('branches/events/settings/event-data-zip-codes', 'Intern\Branches\Events\EventDataZipController@create');
    $router->delete('branches/events/settings/event-data-zip-codes/{id}', 'Intern\Branches\Events\EventDataZipController@delete');
    $router->post('branches/events/settings/event-data-zip-codes/{id}', 'Intern\Branches\Events\EventDataZipController@update');

    // Addresses
    $router->get('branches/events/settings/event-data-addresses', 'Intern\Branches\Events\EventDataAddressController@all');
    $router->post('branches/events/settings/event-data-addresses', 'Intern\Branches\Events\EventDataAddressController@create');
    $router->delete('branches/events/settings/event-data-addresses/{id}', 'Intern\Branches\Events\EventDataAddressController@delete');
    $router->post('branches/events/settings/event-data-addresses/{id}', 'Intern\Branches\Events\EventDataAddressController@update');

    // Numbers
    $router->get('branches/events/settings/event-data-numbers', 'Intern\Branches\Events\EventDataNumberController@all');
    $router->post('branches/events/settings/event-data-numbers', 'Intern\Branches\Events\EventDataNumberController@create');
    $router->delete('branches/events/settings/event-data-numbers/{id}', 'Intern\Branches\Events\EventDataNumberController@delete');
    $router->post('branches/events/settings/event-data-numbers/{id}', 'Intern\Branches\Events\EventDataNumberController@update');

    // Mail
    $router->get('branches/events/settings/event-data-mails', 'Intern\Branches\Events\EventDataMailController@all');
    $router->post('branches/events/settings/event-data-mails', 'Intern\Branches\Events\EventDataMailController@create');
    $router->delete('branches/events/settings/event-data-mails/{id}', 'Intern\Branches\Events\EventDataMailController@delete');
    $router->post('branches/events/settings/event-data-mails/{id}', 'Intern\Branches\Events\EventDataMailController@update');

    // Follow Up Captions
    $router->get('branches/events/settings/event-data-follow-up-captions', 'Intern\Branches\Events\EventDataFollowUpCaptionController@all');
    $router->get('branches/events/settings/event-data-follow-up-captions-with-segment-values', 'Intern\Branches\Events\EventDataFollowUpCaptionController@allWithSegmentValues');
    $router->post('branches/events/settings/event-data-follow-up-captions', 'Intern\Branches\Events\EventDataFollowUpCaptionController@create');
    $router->delete('branches/events/settings/event-data-follow-up-captions/{id}', 'Intern\Branches\Events\EventDataFollowUpCaptionController@delete');
    $router->post('branches/events/settings/event-data-follow-up-captions/{id}', 'Intern\Branches\Events\EventDataFollowUpCaptionController@update');

    // Segments
    $router->get('branches/events/settings/event-data-segments', 'Intern\Branches\Events\EventDataSegmentController@all');
    $router->post('branches/events/settings/event-data-segments/{id}', 'Intern\Branches\Events\EventDataSegmentValueController@create');
    $router->post('branches/events/settings/event-data-segment-values/{id}', 'Intern\Branches\Events\EventDataSegmentValueController@update');
    $router->delete('branches/events/settings/event-data-segment-values/{id}', 'Intern\Branches\Events\EventDataSegmentValueController@delete');

    // Events
    $router->get('branches/events/{companyId}/event-data', 'Intern\Branches\Events\EventDataController@find');
    $router->post('branches/events/{companyId}/event-data', 'Intern\Branches\Events\EventDataController@saveForCompanyRegisterId');

    // SSL
    $router->get('ssl-stat/counts','Intern\Ssl\SslStatController@counts');
    $router->get('ssl-stat/domains','Intern\Ssl\SslStatController@domains');
    $router->post('ssl-stat/locations/{id}/restart','Intern\Ssl\SslStatController@restart');
    $router->post('ssl-stat/locations/{id}/reimport','Intern\Ssl\SslStatController@reimport');

    $router->group(['prefix' => 'mail'], function () use ($router) {
      $router->post('mail-templates', 'Intern\Mail\MailTemplateController@create');
      $router->get('mail-templates', 'Intern\Mail\MailTemplateController@all');
      $router->get('mail-templates/placeholders', 'Intern\Mail\MailTemplateController@getMailPlaceholders');
      $router->get('mail-templates/{id}', 'Intern\Mail\MailTemplateController@byId');
      $router->delete('mail-templates/{id}', 'Intern\Mail\MailTemplateController@delete');
      $router->post('mail-templates/{id}', 'Intern\Mail\MailTemplateController@update');

      $router->post('mail-signatures', 'Intern\Mail\MailSignatureController@create');
      $router->get('mail-signatures', 'Intern\Mail\MailSignatureController@all');
      $router->get('mail-signatures/{id}', 'Intern\Mail\MailSignatureController@byId');
      $router->delete('mail-signatures/{id}', 'Intern\Mail\MailSignatureController@delete');
      $router->post('mail-signatures/{id}', 'Intern\Mail\MailSignatureController@update');

      // Send email
      $router->post('send', 'Intern\Mail\MailController@sendEmail');

      // Save Sent email
      $router->post('location-mails', 'Intern\Branches\LocationMailController@create');
      $router->get('location-mails/all/{locationId}', 'Intern\Branches\LocationMailController@getListByLocationId');
      $router->get('location-mails/{id}', 'Intern\Branches\LocationMailController@byId');
      $router->delete('location-mails/{id}', 'Intern\Branches\LocationMailController@delete');
    });

  });
});
