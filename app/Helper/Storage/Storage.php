<?php
	namespace App\Helper\Storage;

	class Storage {

		public function __construct($apiName){

			$clsPath = __NAMESPACE__ . '\\' . ucfirst(strtolower($apiName));

			if(!class_exists($clsPath)){

				throw new \Exception("找不到指定的处理文件:" . $apiName, 1);
			}

			$this->_client = new $clsPath();
		}

		public function __call($method, $args){

            if(!method_exists($this->_client, $method)){

                throw new Exception('找不到指定的处理方法:' . $method, 2);
            }

            return call_user_func_array([$this->_client, $method], $args);
        }
	}