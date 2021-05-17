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

        // Company Details
        $router->get('locations/count-by-federal-state/{stateId}', RouteUtils::fullMethodeName('Branches', 'Location', 'countByFederalState'));
        $router->get('locations/count-by-category/{id}', RouteUtils::fullMethodeName('Branches', 'Location', 'countByCategory'));
        $router->get('locations/count-by-category-and-no-event-since-months/{id}/{months}', RouteUtils::fullMethodeName('Branches', 'Location', 'countByCategoryAndNoEventsSinceMonths'));
        $router->get('locations', RouteUtils::fullMethodeName('Branches', 'Location', 'find'));
        $router->get('locations/all-without-open-duties', 'Intern\Branches\LocationController@findAllWithoutOpenDuties');
        $router->get('locations/customers/coordinates', 'Intern\Branches\LocationController@findCoordinatesForCustomers');

        // Website Data
        $router->post('locations/ftp/test', 'Intern\Branches\LocationController@testFtpConnection');

        // Duties
        $router->get('locations/{locationId}/finished-tasks', 'Intern\Duties\DutyTaskController@findAllDoneForLocation');

        // Notes
        $router->get('locations/{locationId}/notes', RouteUtils::fullMethodeName('Branches', 'LocationNote', 'findAll'));
        $router->get('locations/{locationId}/notes/count', RouteUtils::fullMethodeName('Branches', 'LocationNote', 'countAll'));
        $router->patch('locations/notes/{id}', RouteUtils::fullMethodeName('Branches', 'LocationNote', 'update'));
        $router->post('locations/notes', RouteUtils::fullMethodeName('Branches', 'LocationNote', 'addNote'));
        $router->post('locations/notes/reorder', RouteUtils::fullMethodeName('Branches', 'LocationNote', 'reorder'));
        $router->delete('locations/notes/{id}', RouteUtils::fullMethodeName('Branches', 'LocationNote', 'deleteNote'));
        $router->post('locations/notes/{id}/comments', RouteUtils::fullMethodeName('Branches', 'LocationNote', 'addComment'));
        $router->post('locations/notes/{id}/send-mail-reply', RouteUtils::fullMethodeName('Branches', 'LocationNote', 'sendMailReply'));
        $router->delete('locations/notes/comments/{id}', RouteUtils::fullMethodeName('Branches', 'LocationNote', 'deleteComment'));

        $router->get('locations/for-locations-per-state-and-cat', RouteUtils::fullMethodeName('Branches', 'Location', 'locationsPerCategoryAndState'));
        $router->delete('locations/{id}', RouteUtils::fullMethodeName('Branches', 'Location', 'deactivateById'));
        $router->patch('locations/{id}', RouteUtils::fullMethodeName('Branches', 'Location', 'updateById'));
        $router->get('locations/{id}', RouteUtils::fullMethodeName('Branches', 'Location', 'findById'));
        $router->post('locations', RouteUtils::fullMethodeName('Branches', 'Location', 'createManually'));
        $router->post('locations/v2', RouteUtils::fullMethodeName('Branches', 'Location', 'createFromLocationV2Event'));

        $router->get('locations/group-by/{columnName}', RouteUtils::fullMethodeName('Branches', 'Location', 'getGroupBySpecificColumn'));
    });
});
