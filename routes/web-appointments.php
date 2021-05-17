<?php

use App\Http\RouteUtils;

/**
 * @var $router \Laravel\Lumen\Routing\Router
 */
$router->group(['prefix' => 'rest'], function () use ($router) {

  /**
   * Intern
   */
  $router->group(['prefix' => 'intern', 'middleware' => 'auth'], function () use ($router) {

    //Termine (neue vereinheitlichte Termine)
    $router->get('appointments', RouteUtils::fullMethodeName('Branches', 'Appointment', 'findFiltered'));
    $router->get('appointments/by-day/{ymd}', RouteUtils::fullMethodeName('Branches', 'Appointment', 'findByDay'));
    $router->get('appointments/by-day/{ymd}/by-type/{appointmentTypeId}', RouteUtils::fullMethodeName('Branches', 'Appointment', 'findByDayAndType'));
    $router->get('appointments/{id}', RouteUtils::fullMethodeName('Branches', 'Appointment', 'byId'));
    $router->get('appointment-types', RouteUtils::fullMethodeName('Branches', 'Appointment', 'findTypes'));
    $router->patch('appointments/{id}', RouteUtils::fullMethodeName('Branches', 'Appointment', 'updatePartial'));
    $router->patch('appointments/{id}/result', RouteUtils::fullMethodeName('Branches', 'Appointment', 'updateResult'));
    $router->post('appointments/{id}/assign', RouteUtils::fullMethodeName('Branches', 'Appointment', 'assignToUser'));
    $router->post('appointments/{id}/release', RouteUtils::fullMethodeName('Branches', 'Appointment', 'release'));
    $router->get('locations/{locationId}/appointments', RouteUtils::fullMethodeName('Branches', 'Appointment', 'findAppointmentsForLocationId'));
    $router->post('locations/{locationId}/appointments', RouteUtils::fullMethodeName('Branches', 'Appointment', 'create'));
  });
});
