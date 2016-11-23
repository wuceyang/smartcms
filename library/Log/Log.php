<?php

	namespace Library\Log;

	use \Exception;
    use \Config;

	class Log{

		protected static $_config 	= null;
		protected static $_logs 	= [];

		protected static function init(){
			
			if(self::$_config === null){

				self::$_config = Config::get('log');

				$logDriver = "\\Library\\Log\\" . ucfirst(strtolower(self::$_config['driver'])) . 'Log';
				
				if(!class_exists($logDriver)){
				
					throw new Exception("找不到指定的日志处理类:" . $logDriver, 102);
				
				}

				$debug = \Config::get('global.debug');
				if($debug){
					//注册会话结束自动保存函数
					register_shutdown_function([$logDriver, 'save']);
				}
			}
		}

		public static function debug($info){
			// var_export(debug_backtrace());
			self::init();
			self::setLog('debug', $info);
		}

		public static function error($info){
			self::init();
			self::setLog('error', $info);
		}

		public static function info($info){
			self::init();
			self::setLog('error', $info);
		}

		protected static function setLog($level, $info = ''){

			self::$_logs[] = [
				'level' => $level,
				'time'  => date('Y-m-d H:i:s', time()),
				'info'  => is_array($info) ? var_export($info,true) : $info,
			];
		}
	}

