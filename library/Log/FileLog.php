<?php

	namespace Library\Log;
	use \Exception;
	use \Library\Log\Log;

	class FileLog extends Log{

		public static function save(){

			$logDir = APP_ROOT . 'cache/logs/';

			switch (self::$_config['format']) {

				case 'daily':

					$filename = date('Y-m-d');
					break;

				case 'weekly':

					$filename = date('Y-n');
					break;

				case 'monthly':

					$filename = date('Y-m');
					break;

				default:

					$filename = 'log';
					break;

			}

			$logFile = $logDir . $filename . '.log';
            
            $logstr  = '';

			foreach (self::$_logs as $k => $v) {

				$logstr .= '[' . $v['time'] . '], ' . $v['level'] . ' -> ' . $v['info'] . "\n";

			}
			if(!is_writable($logDir)){

				throw new Exception("日志目录" . $logDir . "不可写，请检查目录权限", 200);

			}
			
			@file_put_contents($logFile, $logstr, FILE_APPEND);

			self::$_logs = [];
		}
	}