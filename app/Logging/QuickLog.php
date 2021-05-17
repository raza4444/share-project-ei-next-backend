<?php
/**
 * by stephan scheide
 */

namespace App\Logging;


class QuickLog
{

    public static function fullPathForFile($fileNameOnly)
    {
        $dir = base_path('media/logs/features');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $full = $dir . '/' . $fileNameOnly . '.usage.log';
        return $full;
    }

    public static function appendToSingleLog($fileNameOnly, $line)
    {

        $full = self::fullPathForFile($fileNameOnly);

        $fp = fopen($full, 'a');
        fwrite($fp, $line . "\r\n");
        fclose($fp);
    }

    /**
     * @param $fileNameOnly
     * @return bool|resource
     */
    public static function createLogFileForWriting($fileNameOnly)
    {
        $full = self::fullPathForFile($fileNameOnly);
        $fp = fopen($full, 'w');
        return $fp;
    }

    public static function quickWithName($name, $param = '')
    {
        $str = '';
        if (is_array($param)) {
            foreach ($param as $k => $v) {
                $str .= "$k=$v ";
            }
        } else {
            $str = $param . '';
        }

        self::appendToSingleLog($name, date('Y-m-d H:i:s') . ' ' . $name . ' - ' . $str);
    }

    public static function quickWithNameAndStackTrace($name, $param = '')
    {
        $str = '';
        if (is_array($param)) {
            foreach ($param as $k => $v) {
                $str .= "$k=$v ";
            }
        } else {
            $str = $param . '';
        }

        $str .= "\r\n";

        $bt = debug_backtrace();
        $bti = 0;
        foreach ($bt as $b) {
            $str .= "\t" . ($bti++) . ': ' . $b['function'] . ' ' . ($b['class'] ?? 'noclass') . ' ' . ($b['file'] ?? 'noFile') . ' ' . ($b['line'] ?? 'noLine') . "\r\n";
        }

        self::appendToSingleLog($name, date('Y-m-d H:i:s') . ' ' . $name . ' - ' . $str);
    }
}
