<?php

	namespace Library\Session;

    use \Request;
	use \Response;
    use \Exception;

	abstract class Session{

		protected static $_instance = null;

		protected $_config 			= [];

        protected $_sessid          = '';

		public static function getInstance($config = []){

			if(self::$_instance === null){

				self::$_instance = new static($config);
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

            register_shutdown_function([$this, 'save']);

            $this->init();
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

		//初始化session
		abstract public function init();

		//从session中取值
		abstract public function get($key);

		//向session中设置值
		abstract public function set($key, $val);

        //重新生成session ID
        abstract public function reGenerateId();

		//session资源回收
		abstract public function gc();

		//销毁session
		abstract public function destroy();

		//会话结束时，自动保存session
		abstract public function save();
	}
