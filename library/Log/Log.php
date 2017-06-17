<?php

	namespace Library\Log;

	use \Exception;
    use \Config;

	class Log{

		protected static $_config    = null;
		protected static $_logs      = [];
		protected static $_logDriver = '';

		protected static function init(){
			
			if(self::$_config === null){

				if(!self::$_logDriver){

					self::getDriver();
				}
				
				if(!class_exists(self::$_logDriver)){
				
					throw new Exception("找不到指定的日志处理类:" . self::$_logDriver, 102);
				}
			}
		}

		public static function getDriver(){

			self::$_config    = Config::get('log');
				
			self::$_logDriver = "\\Library\\Log\\" . ucfirst(strtolower(self::$_config['driver'])) . 'Log';

			$debug 			  = \Config::get('global.debug');

			if($debug){
				//注册会话结束自动保存函数
				register_shutdown_function([self::$_logDriver, 'save']);
			}
		}

		public static function debug($info){
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

		public static function __callStatic($func, $args){

			self::init();

			if(method_exists(self::$_logDriver, $func)){

				return call_user_func_array([self::$_logDriver, $func], $args);
			}

			throw new \Exception("找不到指定的日志处理方法" . self::$_logDriver, 111);
		}
	}

