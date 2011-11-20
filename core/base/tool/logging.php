<?php
/*
	TODO :
	- Log into year/month/date folder
	- configurable log file name, define thru config
	- Markdown log format
*/
/**
 * Severity Level Logging
 *
 * @package izomi.core.tool
 * @author Nguyen Huu Thanh
 */
abstract class SeverityLevel
{
	const EMERGENCY = 0;	# system is unusable
	const ALERT = 1;		# action must be taken immediately 
	const CRITICAL = 2;		# critical conditions 
	const ERROR = 3;		# error conditions
	const WARNING = 4;		# warining conditions
	const NOTICE = 5;		# normal but signification conditions
	const INFORMATIONAL = 6;	# informational messages
	const DEBUG = 7;		# debug-level messages	
}
class IzLogger extends Object
{
	static private $logger = null;
	static public function getInstance(){
        if (!self::$logger)
        {
            self::$logger = object('IzLogger');
            self::$logger->setFilePath(config('root.abs').config('root.log_folder').DIRECTORY_SEPARATOR.'izomi.log');
        }
        return self::$logger;
    }

    public function log($message, $severity = SeverityLevel::INFORMATIONAL, $module = null, $info = null){
        $time = izDateTime::timeStampToString();
		$ip = $_SERVER['REMOTE_ADDR'];
		$data = "{$ip}\t{$time}\t{$severity}\t{$message}\t{$module}\t{$info}";
		file_put_contents(self::$logger->getFilePath(), "\n".$data, FILE_APPEND);
    }
}
/**
 * @Deprecated
 */
class izLogging extends Object
{
    static private $logger = null;

    static public function getInstance(){
        if (!self::$logger)
        {
            self::$logger = object('izLogging');
            self::$logger->setFilePath(config('root.abs').config('root.log_folder').DIRECTORY_SEPARATOR.'izomi.log');
        }
        return self::$logger;
    }

    public function log($data){
        @file_put_contents(self::$logger->getFilePath(), "\n".$data, FILE_APPEND);
    }

}

function izlog($data)
{
    if (!is_string($data) && is_array($data)) $data = print_r($data, true);
    izLogging::getInstance()->log($data);
}
/**
 * Helper function for logging
 *
 * @param string $message message to be logged
 * @param string $severity severity level of the entry
 * @param string $module module where the log occurred
 * @param string $info extra information, can be string or array (converted to json)
 * @return void
 * @author Nguyen Huu Thanh
 */
function logging($message, $severity = SeverityLevel::INFORMATIONAL, $module = null, $info = null)
{
	if (!is_string($info) && is_array($info)){
		$json = new Services_JSON;
		$info = $json->encode($info);
	} 
    IzLogger::getInstance()->log($message, $severity, $module, $info);
}
?>
