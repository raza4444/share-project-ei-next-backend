<?php
/**
 * by stephan scheide
 */

namespace App\Logging;


class LoggingClip implements LogFacade
{

    private $realm;

    private $idPerFile;

    private $id;

    public function __construct($realm, $id, $idPerFile = false)
    {
        $this->realm = $realm;
        $this->id = $id;
        $this->idPerFile = $idPerFile;
    }

    public function info($message)
    {
        $filename = $this->getFilename();
        $line = $this->id . ': ' . date('Y-m-d H:i:s') . ' INFO: ' . $message;
        QuickLog::appendToSingleLog($filename, $line);
    }

    public function error($message)
    {
        $filename = $this->getFilename();
        $line = $this->id . ': ' . date('Y-m-d H:i:s') . ' ERROR: ' . $message;
        QuickLog::appendToSingleLog($filename, $line);
    }

    public function exception(\Throwable $ex)
    {
        $buf = self::createExceptionOutput(0, $ex);
        $this->error($buf);
    }

    public function getFullLoggingFilePath()
    {
        return QuickLog::fullPathForFile($this->getFilename());
    }

    public static function createExceptionOutput($level, \Throwable $ex)
    {
        $buf = '';
        $prefix = str_pad('', $level * 2, ' ');

        $buf .= $prefix . $ex->getMessage() . PHP_EOL;
        $buf .= self::linesToIdentedString($ex->getTraceAsString(), $level) . PHP_EOL;;
        $buf .= PHP_EOL;

        if ($ex->getPrevious() != null) {
            $buf .= self::createExceptionOutput($level + 1, $ex->getPrevious());
        }

        return $buf;
    }

    private function getFilename()
    {
        return $this->idPerFile ? $this->realm . '-' . $this->id : $this->realm;
    }

    private static function linesToIdentedString($lines, $level)
    {
        $prefix = str_pad('', $level * 2, ' ');
        $tmp = explode(PHP_EOL, $lines);
        $buf = '';
        foreach ($tmp as $t) {
            $buf .= $prefix . $t . PHP_EOL;
        }
        return $buf;
    }


}
