<?php

	namespace Library\Session;

    use \Request;
	use \Response;
    use \Exception;

	abstract class Session implements \SessionHandlerInterface{

		protected static $_instance = null;

		protected $_config 			= [];

        protected $_sessid          = '';

        protected $_sessdata        = [];

		public static function getInstance($config = []){

			if(self::$_instance === null){

				self::$_instance = new static($config);

                session_set_save_handler(self::$_instance, true);
			}

            return self::$_instance;
		}

		protected function __construct($config = []){

			$this->_config = $config;
		}

        public function setId($sessid){

            $this->_sessid = $sessid;
        }

        public function getId(){

            return $this->_sessid;
        }

        //启动session函数
        public function start(){

            $request = Request::getInstance();

            $this->_sessid = $this->_sessid? $this->_sessid : $request->cookie($this->_config['cookieName'], '', false);
        }

        //设置cookie在session中的name值
        protected function setCookieSessId(){

            $response = Response::getInstance(Request::getInstance());

            $response->cookie($this->_config['cookieName'], $this->_sessid, $this->_config['maxLifetime'], '/', '', false, true, false);
        }

        //生成session ID
        protected function createId(){

            return ($this->_config['prefix'] ? $this->_config['prefix'] : '') . md5($this->_sessid ? $this->_sessid : (uniqid() . '-' . rand(1, 10000)));
        }

        public function reGenerateId(){

            $this->_sessid = $this->createId();

            $this->setCookieSessId();

            return $this->sessid;
        }

		public function get($key, $default = null){

            return isset($this->_sessdata[$key]) ? $this->_sessdata[$key] : $default;
        }

        public function set($key, $val){

            $this->_sessdata[$key] = $val;

            return true;
        }

        /*//打开连接设置参数
        abstract public function open(string $savepath, string $name):bool;

        //关闭会话连接
        abstract public function close():bool;

		//session资源回收
		abstract public function gc(int $maxLifetime):bool;

		//销毁session
		abstract public function destroy(string $sessid):bool;

		//会话结束时，自动保存session
		abstract public function write(string $sessid, string $session_data):bool;

        //会话结束时，自动保存session
        abstract public function read(string $sessid):string;*/
	}
