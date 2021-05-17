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

    // Aufgaben

    // Blocks
    $router->get('duties/config/blocks', 'Intern\Duties\DutyBlockController@getAll');
    $router->get('duties/blocks/{id}', 'Intern\Duties\DutyBlockController@getForId');
    $router->post('duties/config/blocks/order', 'Intern\Duties\DutyBlockController@updateBlockOrder');
    $router->post('duties/config/blocks', 'Intern\Duties\DutyBlockController@create');
    $router->patch('duties/config/blocks/{id}', 'Intern\Duties\DutyBlockController@updatePartial');
    $router->delete('duties/config/blocks/{id}', 'Intern\Duties\DutyBlockController@delete');

    // Rows and columns
    $router->get('duties/config/rows', 'Intern\Duties\DutyRowTemplController@getAll');
    $router->get('duties/config/blocks/{blockId}/rows', 'Intern\Duties\DutyRowTemplController@getAllForBlock');
    $router->get('duties/blocks/{blockId}/rows', 'Intern\Duties\DutyRowController@getAllInclDataForBlock');
    $router->get('duties/companies/{locId}/rows', 'Intern\Duties\DutyRowController@getAllInclDataForCompany');
    
    // duty row breakTime
    $router->get('duties/rows/breaks/{timeInterval}', 'Intern\Duties\DutyRowController@getUserBreaksData');
    
    $router->get('duties/config/rows/{rowId}/columns', 'Intern\Duties\DutyRowTemplController@getColumnsForRow');
    $router->get('duties/rows/{dutyRowId}', 'Intern\Duties\DutyRowController@getRowWithAddInfo');
    $router->post('duties/config/rows/{rowId}/columns', 'Intern\Duties\DutyRowTemplController@addColumnsToRow');
    $router->post('duties/config/blocks/{blockId}/rows/order', 'Intern\Duties\DutyRowTemplController@updateRowOrderForRow');
    $router->post('duties/config/blocks/{blockId}/rows', 'Intern\Duties\DutyRowTemplController@create');
    $router->post('duties/rows/{rowId}/assign', 'Intern\Duties\DutyRowController@assign');
    $router->post('duties/rows/{rowId}/release', 'Intern\Duties\DutyRowController@release');
    $router->post('duties/rows/{rowId}/close', 'Intern\Duties\DutyRowController@close');
    $router->post('duties/config/blocks/{blockId}/rows/link/{rowId}', 'Intern\Duties\DutyRowTemplController@linkRowToBlock');
    $router->delete('duties/config/blocks/{blockId}/rows/unlink/{rowId}', 'Intern\Duties\DutyRowTemplController@unlinkRowFromBlock');
    $router->delete('duties/config/rows/{rowId}', 'Intern\Duties\DutyRowTemplController@delete');
    $router->patch('duties/config/rows/{rowId}', 'Intern\Duties\DutyRowTemplController@updatePartial');
    $router->patch('duties/config/rows/{rowTemplId}/columns', 'Intern\Duties\DutyRowTemplController@updateColumnsPartial');

    // Tasks & Follow Ups
    $router->get('duties/rows/{rowId}/tasks', 'Intern\Duties\DutyTaskController@getAllForRow');
    $router->post('duties/rows/{rowId}/tasks/{taskId}', 'Intern\Duties\DutyTaskController@updateStatus');
    $router->get('duties/config/blocks/{blockId}/rows/{rowId}/tasks', 'Intern\Duties\DutyTaskTemplController@getAllTemplatesForRow');
    $router->get('duties/config/blocks/{blockId}/rows/{rowId}/tasks/{taskId}/follow-ups', 'Intern\Duties\DutyFollowUpController@getFollowUpsForTaskTempl');
    $router->post('duties/config/blocks/{blockId}/rows/{rowId}/tasks/link/{taskId}', 'Intern\Duties\DutyTaskTemplController@linkTaskToRow');
    $router->post('duties/config/blocks/{blockId}/rows/{rowId}/tasks/order', 'Intern\Duties\DutyTaskTemplController@updateTaskOrderForRow');
    $router->post('duties/config/blocks/{blockId}/rows/{rowId}/tasks/{taskId}/follow-up', 'Intern\Duties\DutyFollowUpController@create');
    $router->delete('duties/config/blocks/{blockId}/rows/{rowId}/tasks/unlink/{taskId}', 'Intern\Duties\DutyTaskTemplController@unlinkTaskFromRow');
    $router->delete('duties/config/follow-ups/{followUpId}', 'Intern\Duties\DutyFollowUpController@deleteFollowUp');
    $router->get('duties/config/tasks', 'Intern\Duties\DutyTaskTemplController@getAll');
    $router->post('duties/config/tasks', 'Intern\Duties\DutyTaskTemplController@create');
    $router->delete('duties/config/tasks/{id}', 'Intern\Duties\DutyTaskTemplController@delete');
    $router->patch('duties/config/tasks/{taskId}', 'Intern\Duties\DutyTaskTemplController@updatePartial');
    $router->patch('duties/config/follow-ups/{followUpId}', 'Intern\Duties\DutyFollowUpController@updatePartial');
    $router->post('duties/rows/{rowId}/follow-ups/{followUpId}', 'Intern\Duties\DutyFollowUpController@handleFollowUp');
    $router->get('duties/finished-today-tasks', 'Intern\Duties\DutyTaskController@finishedTodayTasks');
    $router->get('duties/finished-yesterday-tasks', 'Intern\Duties\DutyTaskController@finishedYesterdayTasks');
    $router->get('duties/finished-all-tasks', 'Intern\Duties\DutyTaskController@finishedLastMonthTasks');
  
    // Interaction Types
    $router->get('duties/config/follow-ups/interaction-types', 'Intern\Duties\DutyFollowUpInteractionTypeController@getAll');

    // Triggers
    $router->get('duties/config/triggers', 'Intern\Duties\DutyTriggerController@all');

    // Columns
    $router->get('duties/config/columns', 'Intern\Duties\DutyColumnsController@allColumns');
    $router->get('duties/config/columns/datetypes', 'Intern\Duties\DutyColumnsController@allDateTypes');

    //Allgemeines Counter und Aufgabenhandling
    $router->get('counter-tasks/{id}/events/count-open', RouteUtils::fullMethodeName('Tasks', 'CounterTask', 'countOpenEvents'));
    $router->get('counter-tasks/{id}/events/next-id', RouteUtils::fullMethodeName('Tasks', 'CounterTask', 'nextEventId'));
    $router->get('counter-task-events/{id}/work', RouteUtils::fullMethodeName('Tasks', 'CounterTask', 'eventByIdForWork'));
    $router->post('counter-task-events/{id}/done', RouteUtils::fullMethodeName('Tasks', 'CounterTask', 'markAsDone'));
    $router->get('counter-task-events/overview', RouteUtils::fullMethodeName('Tasks', 'CounterTask', 'eventsForOverview'));
  });
});
