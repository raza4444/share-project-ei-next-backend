<?php

/**
 * @var $router \Laravel\Lumen\Routing\Router
 */
$router->group(['prefix' => 'rest'], function () use ($router) {

    $router->group(['prefix' => 'intern/contact-form-automation', 'middleware' => 'auth'], function () use ($router) {
         $router->get('start', 'Intern\ContactFormAutomation\CFAController@start');
         $router->get('stop', 'Intern\ContactFormAutomation\CFAController@stop');
         $router->get('status', 'Intern\ContactFormAutomation\CFAController@status');
         $router->get('logs/paginate/{logNumber}', 'Intern\ContactFormAutomation\CFAController@getPaginatedLogs');
    });
});
