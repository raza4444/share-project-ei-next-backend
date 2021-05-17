<?php

/**
 * by stephan scheide
 */

namespace App\Services\Crawler;

use Cocur\BackgroundProcess\BackgroundProcess;
use App\Repositories\Crawler\CrawlerRepository;
use App\Entities\Crawler\CrawlerTypes;

class CrawlerBackgroundProcessService
{
  private $repository;
  public function __construct(CrawlerRepository $repository)
  {
    $this->repository = $repository;
  }

  public function startCrawler()
  {
    $this->repository->deleteAllProcess();
    $this->repository->previousCrawlingProcessStop(CrawlerTypes::DASHBOARD_CRAWLER);
    if (env('CRAWLER_PATH') && env('CRAWLER_LOG_PATH')) {
      $crawlerPath =    env('CRAWLER_PATH');
      $crawlerProcessCount = env('CRAWLER_PROCESS_COUNT');
      // $crawlerLogPath = env('CRAWLER_LOG_PATH');

      if (!is_writable(dirname($crawlerPath))) {
        return array('success' => false, 'message' => dirname($crawlerPath) . ' must writable!!!');
      }

      // if (!is_writable(dirname($crawlerLogPath))) {
      //   return array('success' => false, 'message' => dirname($crawlerLogPath) . ' must writable!!!');
      // }

      if (count((array) $this->repository->findRunningProcess(CrawlerTypes::DASHBOARD_CRAWLER)) > 0) {
        $this->stopCrawler();
      }

      $mainCrawlerProcessId = ($this->repository->createProcess(array('type' => CrawlerTypes::DASHBOARD_CRAWLER, 'status' => 0)))->id;

      //create sub process of main process
      $this->runCrawlerSubProcess($crawlerProcessCount, CrawlerTypes::DASHBOARD_CRAWLER, $mainCrawlerProcessId);

      return array('success' => true, 'running' => true);
    } else {
      return array('success' => false, 'message' => 'CRAWLER_PATH OR CRAWLER_LOG_PATH is not set');
    }
  }

  public function stopCrawler()
  {
    $crawlerLogPath = env('CRAWLER_LOG_PATH');
    $crawlerProcess = $this->repository->findRunningProcess(CrawlerTypes::DASHBOARD_CRAWLER);
    if (isset($crawlerProcess) && isset($crawlerProcess->id)) {

      foreach ($crawlerProcess->crawlerSubProcess as $subProcess) {
        $process =  BackgroundProcess::createFromPID($subProcess->pid);

        //file_put_contents($crawlerLogPath, "---------------Process Ended : Process with PID " . $process->getPid() . " ended on " . date("Y-m-d H:i:s") . "---------------" . PHP_EOL, FILE_APPEND);
        if ($process->isRunning()) {
          $process->stop();
        }
      }

      $this->repository->completeProcess($crawlerProcess);
      $this->repository->previousCrawlingProcessStop(CrawlerTypes::DASHBOARD_CRAWLER);

      return array('success' => true, 'running' => false);
    } else {
      return array('success' => false, 'running' => false);
    }
  }

  /**
   * @return void
   */
  public function statusCrawler()
  {
    $crawlerPath =    env('CRAWLER_PATH');
    if (!is_writable(dirname($crawlerPath))) {
      return array('success' => false, 'message' => dirname($crawlerPath) . ' must writable!!!');
    }

    $crawlerProcess = $this->repository->findRunningProcess(CrawlerTypes::DASHBOARD_CRAWLER);


    if (isset($crawlerProcess) && isset($crawlerProcess->id)) {

      $crawlerProcessCount = env('CRAWLER_PROCESS_COUNT');
      $totalRunningProcess =  count($crawlerProcess->crawlerSubProcess);

      if ($crawlerProcessCount >  $totalRunningProcess) {

        $addNewProcessCount = $crawlerProcessCount - $totalRunningProcess;
        //create sub process of main process
        $this->runCrawlerSubProcess($addNewProcessCount, CrawlerTypes::DASHBOARD_CRAWLER, $crawlerProcess->id);
      } else if ($crawlerProcessCount <  $totalRunningProcess) {

        $stopProcessCount = $totalRunningProcess - $crawlerProcessCount;
        $this->stopCrawlerSubProcess($stopProcessCount, $crawlerProcess->crawlerSubProcess);
      }

      $crawlerSubProcesses = $this->repository->findRunningSubProcess(CrawlerTypes::DASHBOARD_CRAWLER, $crawlerProcess->id);

      foreach ($crawlerSubProcesses as $subProcess) {

        $process =  BackgroundProcess::createFromPID($subProcess->pid);
        if (!$process->isRunning()) {

          $newProcess = new BackgroundProcess('php ' . $crawlerPath . ' --crawlerType=' . CrawlerTypes::DASHBOARD_CRAWLER);
          $newProcess->run();

          $this->repository->reRunSubProcess($subProcess->id, $newProcess->getPid());
        }
      }

      $crawlerSubProcesses = $this->repository->findRunningSubProcess(CrawlerTypes::DASHBOARD_CRAWLER, $crawlerProcess->id);

      if (count($crawlerSubProcesses) === 0) {

        $this->repository->completeProcess($crawlerProcess);

        return array('success' => true, 'running' => false);
      }

      return array('success' => true, 'running' => true);
    } else {
      return array('success' => true, 'running' => false);
    }
  }

  /**
   * @param integer $userId
   * * @param integer $crawlerProcessId
   * @return array
   */

  public function startDomainCrawler(int $userId, int $crawlerProcessId): array
  {
    $this->repository->previousCrawlingProcessStop(CrawlerTypes::DOMAIN_CRAWLER);
    $this->repository->deleteAllProcess();
    if (env('CRAWLER_PATH') && env('CRAWLER_LOG_PATH')) {
      $crawlerPath =    env('CRAWLER_PATH');
      // $crawlerLogPath = env('CRAWLER_LOG_PATH');

      if (!is_writable(dirname($crawlerPath))) {
        return array('success' => false, 'message' => dirname($crawlerPath) . ' must writable!!!');
      }

      // if (!is_writable(dirname($crawlerLogPath))) {
      //   return array('success' => false, 'message' => dirname($crawlerLogPath) . ' must writable!!!');
      // }

      $process = new BackgroundProcess('php ' . $crawlerPath . "  --crawlerType=" . CrawlerTypes::DOMAIN_CRAWLER . "  --userID=" . $userId . "  --processID=" . $crawlerProcessId);

      // $process->run($crawlerLogPath, true);
      $process->run();
      // file_put_contents($crawlerLogPath, PHP_EOL . "---------------Process Started : Process with PID " . $process->getPid() . " ended on " . date("Y-m-d H:i:s") . "---------------" . PHP_EOL, FILE_APPEND);
      $processId = $process->getPid();

      $this->repository->updateCrawlerProcess(array('pid' => $processId, 'user_id' => $userId, 'type' => CrawlerTypes::DOMAIN_CRAWLER, 'status' => 0), $crawlerProcessId);


      return array(
        'success' => true,
        'running' => $process->isRunning(),
        'pid' => $processId,
        'userId' => $userId,
        'status_url' => "intern/crawler/domain/status/{$userId}/{$processId}"
      );
    } else {
      return array('success' => false, 'message' => 'CRAWLER_PATH OR CRAWLER_LOG_PATH is not set');
    }
  }

  /**
   * @param integer $processId
   * @param integer $userId
   * @return void
   */

  public function domainCrawlerStatus(int $processId, int $userId)
  {
    $crawlerProcess = $this->repository->findRunningProcessByProcessIdAndUserId($processId, $userId, CrawlerTypes::DOMAIN_CRAWLER);

    if (isset($crawlerProcess) && isset($crawlerProcess->pid)) {

      $process =  BackgroundProcess::createFromPID($crawlerProcess->pid);
      if (!$process->isRunning()) {
        $this->repository->completeProcessByUserAndProcessId($processId, $userId);
      }

      return array('success' => true, 'running' => $process->isRunning());
    } else {
      return array('success' => true, 'running' => false,  'message' => 'Prozess nicht gefunden. Bitte überprüfen Sie die Prozess-ID und die Benutzer-ID.');
    }
  }

  public function startContactFormCrawler()
  {
    $this->repository->deleteAllProcess();
    $this->repository->previousCrawlingProcessStop(CrawlerTypes::CONTACT_FORM_CRAWLER);
    if (env('CRAWLER_PATH') && env('CRAWLER_LOG_PATH')) {
      $crawlerPath =    env('CRAWLER_PATH');
      $crawlerProcessCount = env('CRAWLER_PROCESS_COUNT');
      // $crawlerLogPath = env('CRAWLER_LOG_PATH');

      if (!is_writable(dirname($crawlerPath))) {
        return array('success' => false, 'message' => dirname($crawlerPath) . ' must writable!!!');
      }


      // if (!is_writable(dirname($crawlerLogPath))) {
      //   return array('success' => false, 'message' => dirname($crawlerLogPath) . ' must writable!!!');
      // }

      if (count((array) $this->repository->findRunningProcess(CrawlerTypes::CONTACT_FORM_CRAWLER)) > 0) {
        $this->stopCrawler();
      }

      $mainCrawlerProcessId = ($this->repository->createProcess(array('type' => CrawlerTypes::CONTACT_FORM_CRAWLER, 'status' => 0)))->id;

      //create sub process of main process
      $this->runCrawlerSubProcess($crawlerProcessCount, CrawlerTypes::CONTACT_FORM_CRAWLER, $mainCrawlerProcessId);

      return array('success' => true, 'running' => true);
    } else {
      return array('success' => false, 'message' => 'CRAWLER_PATH OR CRAWLER_LOG_PATH is not set');
    }
  }

  public function stopContactFormCrawler()
  {
    $crawlerLogPath = env('CRAWLER_LOG_PATH');
    $crawlerProcess = $this->repository->findRunningProcess(CrawlerTypes::CONTACT_FORM_CRAWLER);

    if (isset($crawlerProcess) && isset($crawlerProcess->id)) {

      foreach ($crawlerProcess->crawlerSubProcess as $subProcess) {
        $process =  BackgroundProcess::createFromPID($subProcess->pid);

        //file_put_contents($crawlerLogPath, "---------------Process Ended : Process with PID " . $process->getPid() . " ended on " . date("Y-m-d H:i:s") . "---------------" . PHP_EOL, FILE_APPEND);
        if ($process->isRunning()) {
          $process->stop();
        }
      }
      $this->repository->previousCrawlingProcessStop(CrawlerTypes::CONTACT_FORM_CRAWLER);
      $this->repository->completeProcess($crawlerProcess);


      return array('success' => true, 'running' => false);
    } else {
      return array('success' => false, 'running' => false);
    }
  }

  public function statusContactFormCrawler()
  {
    $crawlerPath =    env('CRAWLER_PATH');
    if (!is_writable(dirname($crawlerPath))) {
      return array('success' => false, 'message' => dirname($crawlerPath) . ' must writable!!!');
    }

    $crawlerProcess = $this->repository->findRunningProcess(CrawlerTypes::CONTACT_FORM_CRAWLER);

    if (isset($crawlerProcess) && isset($crawlerProcess->id)) {
      $crawlerProcessCount = env('CRAWLER_PROCESS_COUNT');
      $totalRunningProcess =  count($crawlerProcess->crawlerSubProcess);

      if ($crawlerProcessCount >  $totalRunningProcess) {

        $addNewProcessCount = $crawlerProcessCount - $totalRunningProcess;
        //create sub process of main process
        $this->runCrawlerSubProcess($addNewProcessCount, CrawlerTypes::CONTACT_FORM_CRAWLER, $crawlerProcess->id);
      } else if ($crawlerProcessCount <  $totalRunningProcess) {

        $stopProcessCount = $totalRunningProcess - $crawlerProcessCount;
        $this->stopCrawlerSubProcess($stopProcessCount, $crawlerProcess->crawlerSubProcess);
      }

      $crawlerSubProcesses = $this->repository->findRunningSubProcess(CrawlerTypes::CONTACT_FORM_CRAWLER, $crawlerProcess->id);

      foreach ($crawlerSubProcesses as $subProcess) {
        $process =  BackgroundProcess::createFromPID($subProcess->pid);
        if (!$process->isRunning()) {

          $newProcess = new BackgroundProcess('php ' . $crawlerPath . ' --crawlerType=' . CrawlerTypes::CONTACT_FORM_CRAWLER);
          $newProcess->run();
          $this->repository->reRunSubProcess($subProcess->id, $newProcess->getPid());
        }
      }

      $crawlerSubProcesses = $this->repository->findRunningSubProcess(CrawlerTypes::CONTACT_FORM_CRAWLER, $crawlerProcess->id);

      if (count($crawlerSubProcesses) === 0) {
        $this->repository->completeProcess($crawlerProcess);
        return array('success' => true, 'running' => false);
      }
      return array('success' => true, 'running' => true);
    } else {
      return array('success' => false, 'running' => false);
    }
  }

  /**
   * @param integer $count
   * @param string $type
   * @param integer $mainCrawlerProcessId
   * @return void
   */

  private function runCrawlerSubProcess($count, $type, $mainCrawlerProcessId)
  {

    $crawlerPath =    env('CRAWLER_PATH');

    for ($i = 1; $i <=  $count; $i++) {

      $process = new BackgroundProcess('php ' . $crawlerPath . ' --crawlerType=' . $type);

      /*
      * 
      * -- to check output logs of crawler -- 
      *
      * $crawlerLogPath = env('CRAWLER_LOG_PATH' . $i);
      * $process->run($crawlerLogPath, true);
      *
      *
      */

      $process->run();
      if ($process->getPid() != 0) {
        $data = array(
          'crawler_process_id' => $mainCrawlerProcessId,
          'pid' => $process->getPid(),
          'type' => $type,
          'status' => 0
        );

        $this->repository->createSubProcess($data);
      }
    }
  }

  /**
   * @param integer $stopProcessCount
   * @param array $crawlerSubProcesses
   * @return void
   */

  private function stopCrawlerSubProcess($stopProcessCount, $crawlerSubProcesses)
  {

    foreach ($crawlerSubProcesses as $key => $subProcess) {
      if ($key < $stopProcessCount) {
        $process =  BackgroundProcess::createFromPID($subProcess->pid);

        if ($process->isRunning()) {
          $process->stop();
        }
        $this->repository->subProcessComplete($subProcess->id);
      }
    }
  }
}
