<?php

namespace App\Http\Controllers\Intern\Crawler;

use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;
use App\Services\Crawler\CrawlerMatchService;

class MatchController extends AbstractInternController
{
  private $crawlerMatchService;

  public function __construct(CrawlerMatchService $crawlerMatchService)
  {
    $this->crawlerMatchService = $crawlerMatchService;
  }

  public function getKeywords()
  {
    $keywords = $this->crawlerMatchService->getKeywords();

    if ($keywords) {
      return $keywords;
    } else if (emptyArray($keywords)) {
      return $this->notFound();
    }

    return $this->serverErrorQuick('An error occured while reading the keywords.');
  }

  public function addKeyword(Request $request)
  {
    $validatedData = $this->validate($request, [
      'keyword' => 'required|max:255',
      'report_result' => ['max:255|nullable)'],
      'section' => ['required', 'max:255']
    ]);

    $addedKeyword = $this->crawlerMatchService->addKeyword($validatedData);

    if ($addedKeyword) {
      return $addedKeyword;
    }

    return $this->serverErrorQuick('An error occured while saving the new keyword.');
  }

  public function updateKeyword(Request $request, $id)
  {
    $validatedData = $this->validate($request, [
      'keyword' => 'required|max:255',
      'report_result' => ['max:255|nullable)'],
      'section' => ['required', 'max:255']
    ]);

    $updatedKeyword = $this->crawlerMatchService->updateKeyword($id, $validatedData);

    if ($updatedKeyword) {
      return $updatedKeyword;
    }

    return $this->serverErrorQuick('An error occured while updating the keyword.');
  }

  public function deleteKeyword($id)
  {
    $this->crawlerMatchService->deleteKeyword($id);
    return $this->noContent();
  }

  public function getResults($batchId)
  {
    $results = $this->crawlerMatchService->getResults('url', $batchId);

    if ($results) {
      return $results;
    } else if (emptyArray($results)) {
      return $this->notFound();
    }

    return $this->serverErrorQuick('An error occured while reading the results.');
  }

  public function getContactFormResults($batchId)
  {
    $results = $this->crawlerMatchService->getResults('contact-form', $batchId);

    if ($results) {
      return $results;
    } else if (emptyArray($results)) {
      return $this->notFound();
    }

    return $this->serverErrorQuick('An error occured while reading the contact form results.');
  }

  public function getResultsForDomain(Request $request)
  {
    $validatedData = $this->validate($request, [
      'type' => 'required|max:255',
      'domain' => 'required|max:255'
    ]);

    $parsedDomain = str_replace(
      'www.',
      '',
      parse_url('http://' . str_replace(array('https://', 'http://'), '', $validatedData['domain']), PHP_URL_HOST)
    );

    if (isset($parsedDomain)) {

      $results = $this->crawlerMatchService->getResultsForDomain($validatedData['type'], $parsedDomain);

      if ($results) {
        return $results;
      } else if (emptyArray($results)) {
        return $this->notFound();
      }

      return $this->serverErrorQuick('An error occured while reading the contact form results.');
    } else {
      return $this->badRequest('The given domain is invalid.');
    }
  }
}
