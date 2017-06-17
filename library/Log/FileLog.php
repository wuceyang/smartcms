<?php

	namespace Library\Log;
	use \Exception;
	use \Library\Log\Log;

	class FileLog extends Log{

		protected static $_logDir = '';

		public static function save(){

			self::checkLogPermission();

			$logFile = self::getLogFile();
            
            $logstr  = '';

			foreach (self::$_logs as $k => $v) {

				$logstr .= '[' . $v['time'] . '], ' . $v['level'] . ' -> ' . $v['info'] . "\n";
			}
			
			@file_put_contents($logFile, $logstr, FILE_APPEND);

			self::$_logs = [];
		}

		protected static function getLogFile(){

			$logDir   = APP_ROOT . 'cache/logs/';

			$filename = date('Y-m-d');

			$logFile  = $logDir . $filename . '.log';

			return $logFile;
		}

		protected static function checkLogPermission(){

			self::$_logDir  = APP_ROOT . 'cache/logs/';

			if(!is_writable(self::$_logDir)){

				throw new Exception("日志目录" . self::$_logDir . "不可写，请检查目录权限", 200);
			}
		}

		public static function record($content){

			self::checkLogPermission();

			$logFile = str_replace('.log', '.rec.log', self::getLogFile());

			$content = is_array($content) ? var_export($content, true) : $content;

			@file_put_contents($logFile, date('Y-m-d H:i:s') . ' => ' . $content . "\n", FILE_APPEND);
		}
	}