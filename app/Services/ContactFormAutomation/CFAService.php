<?php

namespace App\Services\ContactFormAutomation;

use Cocur\BackgroundProcess\BackgroundProcess;
use App\Repositories\ContactFormAutomation\CFARepository;
use Carbon\Carbon;

class CFAService
{

  /**
   * @var CFARepository
   */
  private $cfaRepository;

  /**
   * @param cfaRepository $cfaRepository
   */
  public function __construct(
    CFARepository $cfaRepository
  ) {
    $this->cfaRepository = $cfaRepository;
  }

  /**
   * @param string $token
   * @return void
   */
  public function startCFA($token)
  {


    if (env('CFA_PATH')) {
      $cfaPath =    env('CFA_PATH');
      $cfaProcessCount = env('CFA_PROCESS_COUNT');


      if (!is_writable(dirname($cfaPath))) {
        return array('success' => false, 'message' => dirname($cfaPath) . ' must writable!!!');
      }

      // $cfaLogPath = env('CFA_LOG_PATH');
      // if (!is_writable(dirname($cfaLogPath))) {
      //   return array('success' => false, 'message' => dirname($cfaPath) . ' must writable!!!');
      // }

      $this->cfaRepository->deleteAllPreviousProcess();

      if (count((array) $this->cfaRepository->findRunningProcess()) > 0) {
        $this->stopCFA();
      }

      $mainCFAProcessId = ($this->cfaRepository->createProcess(array('status' => 0)))->id;

      //create sub process of main process
      $allSubProcess = $this->runSubProcess($cfaProcessCount, $token, $mainCFAProcessId);

      $allLogsFIle = $this->getAllLogsFileLinks($allSubProcess);
     
      return array('success' => true, 'running' => true, 'logs'=> $allLogsFIle);

    } else {
      return array('success' => false, 'message' => 'CFA_PATH is not set');
    }
  }

  /**
   * @param array $allSubProcesses
   * @return void
   */
  function getAllLogsFileLinks($allSubProcesses) {
    $allProcessLogs = [];
    foreach($allSubProcesses as $subProcess) {
      $allProcessLogs[] = $subProcess->log_number;
    }
    return $allProcessLogs;
  }

  /**
   * @return void
   */
  public function stopCFA()
  {
    $cfaProcess = $this->cfaRepository->findRunningProcess();

    if (isset($cfaProcess) && isset($cfaProcess->id)) {

      foreach ($cfaProcess->cfaSubProcess as $subProcess) {
        $process =  BackgroundProcess::createFromPID($subProcess->pid);
        // $this->deleteLogsFile();

        if ($process->isRunning()) {

          $process->stop();
        }
      }

      $this->cfaRepository->completeProcess($cfaProcess);
      return array('success' => true, 'running' => false);
    } else {
      return array('success' => false, 'running' => false);
    }
  }

  /**
   * @param int $count
   * @param string $token
   * @param int $mainProcessId
   * @return void
   */
  private function runSubProcess($count, $token, $mainProcessId)
  {

    $cfaPath = env('CFA_PATH');
    // $logPath = env('CFA_LOG_PATH');
    $newlySavedProcess = [];
    for ($i = 1; $i <=  $count; $i++) {
      $logNumber = $this->createLogsNumber();
      $process = new BackgroundProcess('node ' . $cfaPath . ' ' . $token . ' ' . $logNumber);
      $process->run();


      if ($process->getPid() != 0) {


        $data = array(
          'contact_form_automator_process_id' => $mainProcessId,
          'pid' => $process->getPid(),
          'status' => 0,
          'log_number' => $logNumber
        );

        $newlySavedProcess[] = $this->cfaRepository->createSubProcess($data);
      }
    }
    return $newlySavedProcess;
  }

  /**
   * @return void
   */

  public function statusCFA($token)
  {
    $cfaPath =    env('CFA_PATH');
    // $logPath = env('CFA_LOG_PATH');
    if (!is_writable(dirname($cfaPath))) {
      return array('success' => false, 'message' => dirname($cfaPath) . ' must writable!!!');
    }

    $cfaProcess = $this->cfaRepository->findRunningProcess();


    if (isset($cfaProcess) && isset($cfaProcess->id)) {

      $cfaProcessCount =  env('CFA_PROCESS_COUNT');
      $totalRunningProcess =  count($cfaProcess->cfaSubProcess);

      if ($cfaProcessCount >  $totalRunningProcess) {

        $addNewProcessCount = $cfaProcessCount - $totalRunningProcess;
        //create sub process of main process
        $this->runSubProcess($addNewProcessCount, $token, $cfaProcess->id);
      } else if ($cfaProcessCount <  $totalRunningProcess) {

        $stopProcessCount = $totalRunningProcess - $cfaProcessCount;
        $this->stopSubProcess($stopProcessCount, $cfaProcess->cfaSubProcess);
      }

      $cfaSubProcesses = $this->cfaRepository->findRunningSubProcess($cfaProcess->id);

      foreach ($cfaSubProcesses as $subProcess) {

        $process =  BackgroundProcess::createFromPID($subProcess->pid);
        if (!$process->isRunning()) {
          $newProcess = new BackgroundProcess('node ' . $cfaPath . ' ' . $token . ' ' . $subProcess->log_number);
          $newProcess->run();
          $this->cfaRepository->reRunSubProcess($subProcess->id, $newProcess->getPid());
        }
      }

      $cfaSubProcesses = $this->cfaRepository->findRunningSubProcess($cfaProcess->id);

      if (count($cfaSubProcesses) === 0) {

        $this->cfaRepository->completeProcess($cfaProcess);
        return array('success' => true, 'running' => false);
      }

      $allLogsFIle = $this->getAllLogsFileLinks($cfaSubProcesses);
      return array('success' => true, 'running' => true, 'logs'=> $allLogsFIle);

    } else {
      return array('success' => true, 'running' => false);
    }
  }

  /**
   * @param integer $stopProcessCount
   * @param array $cfaSubProcesses
   * @return void
   */

  private function stopSubProcess($stopProcessCount, $cfaSubProcesses)
  {

    foreach ($cfaSubProcesses as $key => $subProcess) {
      if ($key < $stopProcessCount) {
        $process =  BackgroundProcess::createFromPID($subProcess->pid);

        if ($process->isRunning()) {
          $process->stop();
        }
        $this->cfaRepository->subProcessComplete($subProcess->id);
      }
    }
  }

  function createLogsNumber()
  {
    return Carbon::now()->timestamp . rand(10, 100000);
  }
}
