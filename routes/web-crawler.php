<?php

/**
 * @var $router \Laravel\Lumen\Routing\Router
 */
$router->group(['prefix' => 'rest'], function () use ($router) {

  /**
   * Intern
   */
  $router->group(['prefix' => 'intern', 'middleware' => 'auth'], function () use ($router) {
    $router->delete('crawler/queue/{urlId}', 'Intern\Crawler\CrawlerController@deleteUrl');
    $router->delete('crawler/keywords/{id}', 'Intern\Crawler\MatchController@deleteKeyword');
    $router->post('crawler/keywords/{id}', 'Intern\Crawler\MatchController@updateKeyword');
    $router->post('crawler/queue', 'Intern\Crawler\CrawlerController@addUrls');
    $router->post('crawler/keywords', 'Intern\Crawler\MatchController@addKeyword');
    $router->get('crawler/results/{batchId}', 'Intern\Crawler\MatchController@getResults');
    $router->get('crawler/results-for-domain', 'Intern\Crawler\MatchController@getResultsForDomain');
    $router->get('crawler/keywords', 'Intern\Crawler\MatchController@getKeywords');
    $router->get('crawler/queue', 'Intern\Crawler\CrawlerController@getQueue');
    $router->get('crawler/start', 'Intern\Crawler\CrawlerController@start');
    $router->get('crawler/stop', 'Intern\Crawler\CrawlerController@stop');
    $router->get('crawler/status', 'Intern\Crawler\CrawlerController@status');
    $router->get('crawler/batch-ids', 'Intern\Crawler\CrawlerController@getAllBatchId');

    $router->get('crawler/results-with-cell-no', 'Intern\Crawler\CrawlerController@getDataWithCellNo');
    $router->put('crawler/check-cell-no/{id}', 'Intern\Crawler\CrawlerController@checkCellNo');

    $router->post('crawler/domain/start', 'Intern\Crawler\CrawlerController@startDomainCrawler');
    $router->get('crawler/domain/status/{userId}/{pid}', 'Intern\Crawler\CrawlerController@domainCrawlerStatus');

    $router->post('crawler/contact-form-queue', 'Intern\Crawler\CrawlerController@addUrlsForContactFormSearch');
    $router->get('crawler/contact-form/start', 'Intern\Crawler\CrawlerController@startContactFormCrawler');
    $router->get('crawler/contact-form/stop', 'Intern\Crawler\CrawlerController@stopContactFormCrawler');
    $router->get('crawler/contact-form/status', 'Intern\Crawler\CrawlerController@statusContactFormCrawler');
    $router->get('crawler/contact-form/queue', 'Intern\Crawler\CrawlerController@getContactFormCrawlerQueue');
    $router->get('crawler/contact-form/results/{batchId}', 'Intern\Crawler\MatchController@getContactFormResults');
    $router->get('crawler/contact-form/batch-ids', 'Intern\Crawler\CrawlerController@getAllBatchIdOfContactFormCrawler');
    
    $router->post('crawler/import-add-urls', 'Intern\Crawler\CrawlerController@importCSVToAddUrls');
  });

});
