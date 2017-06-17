<?php

    namespace Library\Session;

    use \Config;
    use \Request;
    use \Exception;
    use \Library\Common\Cryptography;

    class SystemSession extends \Library\Session\Session{

        public function init(){

            session_save_path($this->_config['saveDir']);

            session_name($this->_config['cookieName']);
        }

        public function start(){

            $this->init();

            if(!$this->_sessid){

                $request = Request::getInstance();

                $this->_sessid = $request->cookie($this->_config['cookieName'], '', false);
            }

            if(!$this->_sessid){

                $this->_sessid = $this->createId();
            }

            $this->setCookieSessId();

            session_id($this->_sessid);

            session_start();
        }

        public function getId(){

            return session_id();
        }

        public function setId($sessid){

            $this->_sessid = $sessid;

            session_id($this->_sessid);
        }

        public function get($key, $default = null){

            return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
        }

        public function set($key, $val){

            $_SESSION[$key] = $val;
        }

        public function save(){

        }

        public function destroy(){

            session_destroy();
        }

        public function gc(){

            session_gc();
        }

        public function reGenerateId(){

            $this->_sessid = $this->createId();

            $this->setCookieSessId();

            return $this->sessid;
        }

    }