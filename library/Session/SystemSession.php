<?php

    namespace Library\Session;

    use \Config;
    use \Request;
    use \Response;
    use \Exception;

    class SystemSession extends Session{

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

        public function get($key, $default = null){

            return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
        }

        public function set($key, $val){

            $this->_sessdata[$key] = $val;

            $_SESSION[$key]        = $val;

            return true;
        }

        public function open($savepath, $sessname):bool{

            return true;
        }

        public function read($sessid):string{

            $sessfile = $this->_config['saveDir'] . $sessid;

            if(!file_exists($sessfile)){

                $this->_sessdata = [];

                $_SESSION        = [];

                return session_encode();
            }

            $sessdata        = file_get_contents($sessfile);

            if(session_decode($sessdata)){

                $this->_sessdata = $_SESSION;
            }

            return $sessdata;
        }

        public function write($key, $val):bool{

            if(!$val) return true;

            $sessfile = $this->_config['saveDir'] . $key;

            return !!file_put_contents($sessfile, $val);
        }

        public function close():bool{

            return true;
        }

        public function destroy($sessid):bool{

            unlink($this->_config['saveDir'] . $sessid);

            return true;
        }

        public function gc($maxLifetime){

            $dirs = scandir($this->_config['saveDir']);

            foreach($dirs as $k => $v){

                if($v == '.' || $v == '..'){

                    continue;
                }

                $sessfile = $this->_config['saveDir'] . $v;

                if(filemtime($sessfile) < TIME - $maxLifetime){

                    unlink($this->_config['saveDir'] . $v);
                }
            }
        }

        public function create_sid():string{

            return $this->createId();
        }
    }