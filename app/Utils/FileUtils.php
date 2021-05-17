<?php
/**
 * by stephan scheide
 */

namespace App\Utils;


use Laravel\Lumen\Application;

class FileUtils
{

    public static function appPath()
    {
        return Application::getInstance()->basePath() . DIRECTORY_SEPARATOR . 'app';
    }

    public static function directoryOfClass($instance)
    {
        $c = new \ReflectionClass($instance);
        $name = $c->getNamespaceName();
        if (strpos($name, 'App\\') === 0) {
            $name = substr($name, 4);
        }
        $name = str_replace('\\', '%sep%', $name);
        $full = self::appPath() . '%sep%' . $name;
        return str_replace('%sep%', DIRECTORY_SEPARATOR, $full);
    }

    public static function filePathInPackage($instance, $relative)
    {
        return self::directoryOfClass($instance) . DIRECTORY_SEPARATOR . $relative;
    }

    public static function contentOfPackageFile($instance, $relative)
    {
        $path = self::filePathInPackage($instance, $relative);
        return self::contentOfFile($path);
    }

    public static function contentOfFile($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("file not found: $path");
        }
        $fp = fopen($path, 'rb');
        $buf = fread($fp, filesize($path));
        fclose($fp);
        return $buf;
    }

    public static function contentOfFileOrNull($path)
    {
        if (!file_exists($path)) {
            return null;
        }
        $fp = fopen($path, 'rb');
        $buf = fread($fp, filesize($path));
        fclose($fp);
        return $buf;
    }

    public static function firstExistingFile(...$paths)
    {
        foreach ($paths as $p) {
            if (file_exists($p)) {
                return $p;
            }
        }
        throw new \Exception('no file present: ' . implode(',', $paths));
    }

    public static function tmpFile($fileNameOnly)
    {
        return '/tmp/' . $fileNameOnly;
    }

    public static function dumpStringToFile($str, $file)
    {
        $fp = fopen($file, 'wb');
        if (!$fp) {
            throw new \Exception("could not open file for writing $file");
        }
        fwrite($fp, $str);
        fclose($fp);
        if (!file_exists($file)) {
            throw new \Exception("could not write to file $file");
        }
    }

}
