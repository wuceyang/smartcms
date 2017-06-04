<?php
    namespace App\Helper;

    use \Config;
    use \Exception;

    class Redis extends \Predis\Client{


        protected static $_conn = [];

        public static $_transoption = ['cas' => true, 'retry' => 3];

        //获取redis连接设置
        public static function getInstance($connection = 'default'){

            if(!isset(self::$_conn[$connection])){

                $config = Config::get('database.redis');

                if(!isset($config[$connection])){

                    throw new \Exception("找不到指定的redis设置:" . $connection, 2);
                }

			    self::$_conn[$connection] = new self($config[$connection]);
            }

            return self::$_conn[$connection];
        }

        //重置所有链接
        public static function resetConnection(){

            self::$_conn = [];
        }

        //批量运行redis命令
        public function multiExec(array $cmd = [], $connection = 'default'){

            $conn = self::getInstance($connection);

            $pipe = $conn->pipeline();

    		foreach ($cmd as $k => $v) {

    			if(is_numeric($k)) continue;

    			foreach ($v as $sk => $sv) {

    				call_user_func_array([$pipe, $k], is_array($sv)?$sv:[$sv]);
    			}
    		}

    		return $pipe->execute();
        }

        public function ctransaction(){

            $args = func_get_args();

            $options = isset($args[0]) && is_array($args[0]) ?$args[0] : [];

            $callback = isset($args[1]) && is_callable($args[1]) ? $args[1] : function(){};

            if(!isset($options['watch'])){

                return false;
            }

            $retry    = intval(isset($options['retry']) ? : 3);
            
            $counter  = 0;
            
            $execFlag = false;

            while(!$execFlag && $counter++ < $retry){

                if(isset($options['cas']) && $options['cas']){
                    
                    $watchKeys   = is_array($options['watch']) ? $options['watch'] : [$options['watch']];
                    
                    $watchResult = call_user_func_array([$this, 'watch'], $watchKeys);
                }

                try{

                    $result = call_user_func_array($callback, [$this]);

                    if($result === false){

                        $this->unwatch();

                        continue;
                    }

                    $this->exec();

                    $execFlag = true;

                }catch(\Exception $e){

                    $this->unwatch();

                    continue;
                }
            }

            return $execFlag;
        }
    }
