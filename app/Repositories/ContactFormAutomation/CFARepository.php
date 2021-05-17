<?php

/**
 * by stephan scheide
 */

namespace App\Repositories\ContactFormAutomation;


use App\Entities\ContactFormAutomation\CFAProcess;
use App\Entities\ContactFormAutomation\CFASubProcess;
use App\Entities\Crawler\CrawlerProcess;
use App\Entities\Crawler\CrawlerTypes;
use App\Entities\Crawler\CrawlerData;
use App\Entities\Crawler\CrawlerSubProcess;
use Illuminate\Support\Facades\DB;


class CFARepository
{

    protected $processModel;
    protected $processSubModel;
    private const MAX_RETURN_RESULTS = 20000;

    
    /**
     * @param CFAProcess $processModel
     * @param CFASubProcess $processSubModel
     */

    public function __construct(CFAProcess $processModel, CFASubProcess $processSubModel)
    {
        $this->processModel = $processModel;
        $this->processSubModel = $processSubModel;
    }

    /**
     * @param array $data
     * @return object|null
     */

    public function createProcess(array $data)
    {
        return $this->processModel->create($data);
    }

    /**
     * @param array $data
     * @return void
     */

    public function createSubProcess(array $data)
    {
        return $this->processSubModel->create($data);
    }

    public function updateCrawlerProcess($data, $id)
    {
        return $this->processQuery()
            ->whereId($id)
            ->update($data);
    }

    /**
     * @param string $type
     * @return object
     */

    public function findRunningProcess()
    {
        return $this->processModel::where('status', '=', 0)
                    ->with(array('cfaSubProcess' => function ($query) {
                        $query->where('status', '=', 0);
                    }))
                    ->orderBy('id', 'desc')
                    ->first();
        
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @param integer $id
     * @return array
     */
    public function findRunningSubProcess(int $ProcessId)
    {
        return
            $this->processSubModel
            ->where('status', '=', 0)
            ->where('contact_form_automator_process_id', '=', $ProcessId)
            ->get();
    }

    /**
     * @param integer $processId
     * @param integer $userId
     * @return object
     */

    public function findRunningProcessByProcessIdAndUserId(int $processId, int $userId, string $crawlerType)
    {
        return $this->processQuery()
            ->where('pid', '=', $processId)
            ->where('user_id', '=', $userId)
            ->where('type', $crawlerType)
            ->where('status', '=', 0)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function findRunningProcessByProcessId(int $processId)
    {
        return $this->processQuery()
            ->where('pid', '=', $processId)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function deleteProcess($id)
    {
        $this->processQuery()
            ->where('id', '=', $id)
            ->delete();
    }

    /**
     * @param CrawlerProcess $crawlerRunningProcess
     * @return void
     */

    public function completeProcess($crawlerProcess)
    {

        $crawlerProcess->cfaSubProcess()->update(['status' => 1]);

        $crawlerProcess->update(['status' => 1]);
    }

    /**
     * @return void
     */
    public function deleteAllPreviousProcess()
    {
        $this->processSubModel->delete();
        // delete all process which don't have sub processes
        $this->processModel->delete();
    }

    /**
     * @param integer $id
     * @return void
     */

    public function subProcessComplete($id)
    {
        $this->processSubModel
            ->where('id', $id)
            ->update(['status' => 1]);
    }

    /**
     * @param integer $id
     * @return void
     */

    public function reRunSubProcess($id, $processId)
    {
        $this->processSubModel
            ->where('id', $id)
            ->update(
                [
                    'pid' => $processId,
                    'status' => 0
                ]
            );
    }

    /**
     * @param integer $processId
     * @param integer $userId
     * @return void
     */

    public function completeProcessByUserAndProcessId(int $processId, int $userId)
    {
        $this->processQuery()
            ->where('pid', '=', $processId)
            ->where('user_id', '=', $userId)
            ->update([
                'status' => 1
            ]);
    }

    private function processQuery()
    {
        return CrawlerProcess::query();
    }

    private function processSubQuery()
    {
        return CrawlerSubProcess::query();
    }

    /**
     * @param string $url
     * @param string $type
     * @return boolean
     */
    public function checkUrlExist(string $url, string $type): bool
    {

        switch ($type) {
            case CrawlerTypes::DASHBOARD_CRAWLER:
                $crawlerData = CrawlerData::where('link', $url)
                    ->where(function ($query) use ($type) {
                        $query->where('type', '=', $type)
                            ->orWhereNull('type');
                    })
                    ->count();
                return ($crawlerData === 0) ? true : false;
                break;
            case CrawlerTypes::CONTACT_FORM_CRAWLER:
                $crawlerData = CrawlerData::where(['link' => $url, 'type' => $type])
                    ->count();
                return ($crawlerData === 0) ? true : false;
                break;
        }
    }

    public function getContactFormData()
    {
        return CrawlerData::where(['type' => CrawlerTypes::CONTACT_FORM_CRAWLER, 'is_visited' => 1])
            ->whereNotNull('has_contact_form')
            ->orderBy('scanned_at', 'desc')
            ->limit(self::MAX_RETURN_RESULTS)
            ->get();
    }

    /**
     * @return array CrawlerData
     */
    public function getDataWithCellNo()
    {
        return CrawlerData::where(['in_queue' => 0, 'is_visited' => 1])
            ->whereNotNull('legal_cell_number')->where('legal_cell_number', '<>', '[]')
            ->whereNull('legal_cell_number_checked')
            ->orderBy('scanned_at', 'desc')
            ->limit(self::MAX_RETURN_RESULTS)
            ->get();
    }

    /**
     * @param $data
     * @param int $id
     * @return CrawlerData
     */
    public function checkCellNo($data, $id)
    {
        $crawlerData = CrawlerData::where('id', '=', $id)->first();
        $crawlerData->legal_cell_number_checked = $data['legal_cell_number_checked'];
        $crawlerData->save();
        return $crawlerData;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getMaxBatchId(string $type)
    {
        return CrawlerData::where('type', '=', $type)->max('batch_id');
    }

    /**
     * @param string $type
     * @return array
     */
    public function getAllBatchId(string $type)
    {
        return CrawlerData::where('type', '=', $type)->whereNotNull('batch_id')->orderBy('batch_id', 'desc')->distinct('batch_id')->pluck('batch_id');
    }
}
