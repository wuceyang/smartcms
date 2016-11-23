<?php

	namespace Library\Session;

	use \Config;
	use \Exception;
	use \Library\Common\Cryptography;

	class FileSession extends \Library\Session\Session{


		protected $sess_data = [];

		public function init(){

            if(!$this->_sessid){

                $this->_sessid = $this->createId();

            }

            $this->setCookieSessId();

            if(!is_dir($this->_config['saveDir'])){

            	mkdir($this->_config['saveDir'], 0755, true);
            }

            if(!is_readable($this->_config['saveDir']) || !is_writable($this->_config['saveDir'])){

                throw new \Exception("存储目录无法读写，请检查文件权限", 101);

            }

			$sess_file = $this->_config['saveDir'] . '/' . $this->_sessid;

			if(!file_exists($sess_file)){

				return;

			}

			$lastAccessTime = fileatime($sess_file);

			if(!$lastAccessTime){

				$this->destroy();

				return;
			}

			if(TIME - $lastAccessTime > $this->_config['maxLifetime']){

				$this->destroy();

				return;
			}

			if(!is_readable($sess_file) || !is_writable($sess_file)){

				throw new \Exception("SESSION文件无法读写，请检查文件权限", 101);

			}

			$sessString = file_get_contents($sess_file);

			$rawString  = Cryptography::decode($sessString, $this->_config['encryptKey']);

			$this->sess_data = unserialize($rawString);

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

            $this->removeSessionFile($this->_sessid);

		}

        //资源回收
		public function gc(){

			$files = glob($this->_config['saveDir'] . '/' . $this->_config['prefix'] . '*');

			foreach ($files as $k => $v) {

				if(TIME - fileatime($v) >= $this->_config['maxLifetime']){

					$this->removeSessionFile(basename($v));

				}

			}

		}

		public function save(){

			$sess_file = $this->_config['saveDir'] . '/' . $this->_sessid;

            $sess_data = \Library\Common\Cryptography::encode(serialize($this->sess_data), $this->_config['encryptKey']);

			@file_put_contents($sess_file, $sess_data);

            //计算资源回收
            $seed = mt_rand(1,100);

            if($seed <= ($this->_config['gc_probability'])){

                $this->gc();

            }

		}

        //重新生成session id
        public function reGenerateId(){

            $this->removeSessionFile($this->sessid);

            $this->sessid = $this->createId();

            $this->setCookieSessId();

            return $this->sessid;

        }

        //删除session文件
        protected function removeSessionFile($sessid){

            $file = $this->_config['saveDir'] . '/' . $sessid;

            if(!$sessid || !file_exists($file)){

                return true;

            }

            return @unlink($file);
        }
	}
