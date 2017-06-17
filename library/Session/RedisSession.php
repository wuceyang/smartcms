<?php
	namespace Library\Session;

	use \App\Helper\Redis;

	class RedisSession extends Session{

		protected $sess_data = [];

		protected $_conn = null;

		public function init(){

            if(!$this->_sessid){

                $this->_sessid = $this->createId();
            }

            $this->_conn = Redis::getInstance($this->_config['redis']);

            $this->setCookieSessId();

            $sess_data = $this->_conn->get($this->_sessid);

			$this->sess_data = $sess_data ? unserialize(\Library\Common\Cryptography::decode($sess_data, $this->_config['encryptKey'])) : [];
		}

        //获取session值
		public function get($key, $default = null){

			return isset($this->sess_data[$key]) ? $this->sess_data[$key] : $default;

		}

        //设置session值
		public function set($key, $val){

			return $this->sess_data[$key] = $val;

		}

        //销毁当前session
		public function destroy(){

			$this->sess_data = [];

            $this->_conn->del($this->_sessid);
		}

        //资源回收
		public function gc(){

		}

		public function save(){

            $sess_data = \Library\Common\Cryptography::encode(serialize($this->sess_data), $this->_config['encryptKey']);

			$this->_conn->set($this->_sessid, $sess_data, 'EX', $this->_config['maxLifetime']);
		}

        //重新生成session id
        public function reGenerateId(){

            $this->_conn->del($this->_sessid);

            $this->_sessid = $this->createId();

            $this->setCookieSessId();

            return $this->_sessid;
        }
	}