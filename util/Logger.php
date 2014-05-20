<?php
class Logger {
    public static function info($message) {
        global $log_level;
        if ($log_level>3) { Logger::log($message, 4); }
    }

    public static function warn($message) {
        global $log_level;
        if ($log_level>2) { Logger::log($message, 3); }
    }

    public static function error($message) {
        global $log_level;
        if ($log_level>1) { Logger::log($message, 2); }
    }

    public static function fatal($message) {
        global $log_level;
        if ($log_level>0) { Logger::log($message, 1); }
    }

    private static function log($message, $level=0) {
        global $log_file;

        $slevel = '';

        switch ($level) {
            case 4:
                $slevel = "INFO - ";
                break;
            case 3:
                $slevel = "WARN - ";
                break;
            case 2:
                $slevel = "ERROR - ";
                break;
            case 1:
                $slevel = "FATAL - ";
                break;
        }

        if (isset($log_file) && !empty($log_file))    {
            $log = fopen($log_file, 'a');
            fwrite($log, '['.date('Y-m-d H:i:s').'] '.$slevel.$message.PHP_EOL);
            fclose($log);
        }
        else {
            error_log($message);
        }
    }

    public static function access($message) {
        global $access_log;

        if (isset($access_log) && !empty($access_log))    {
            $body = '{  "IP":"'.Utility::getClientIp().
                    '", "REQUEST":"'.$message.'"  }';
            $access = fopen($access_log, 'a');
            fwrite($access, '['.date('Y-m-d H:i:s').'] '.$body.PHP_EOL);
            fclose($access);
        }
        else {
            Logger::warn('No access log file provided ...');
        }
    }
}