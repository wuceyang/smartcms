<?php

    namespace App\M;
    
    use \Config;
    use \Exception;
    use \App\Helper\Redis;
    use \App\Helper\RedisKeys;
    
    class Model{
        
        private static $_conn = [];
        
        public function __construct(){

        }
        
        //对象调用数据库驱动的方法时触发
        public function __call($method, $arguments){

            return self::__callStatic($method, $arguments);
        }
        
        //静态方式调用数据库驱动的方法时触发
        public static function __callStatic($method, $arguments){
            
            $dbDriver       = Config::get('database.driver');
            
            $dbDriverClass  = "\\Library\\Database\\" . ucfirst($dbDriver);
            
            if(!class_exists($dbDriverClass)){
            
                throw new Exception("找不到指定的数据库驱动:" . $dbDriverClass, 101);
            }
            
            //查找数据库的驱动设置
            $dbConfig   = Config::get('database.' . $dbDriver);
            
            //设置默认的数据库连接，优先使用model中指定的connection，以便切换数据库
            $connection = isset(static::$connection) ? static::$connection : (isset($dbConfig['read']) ? 'read' : 'default'); 
            
            //连接数据库
            $driverInstance = $dbDriverClass::getInstance(); 
            
            $driverInstance->connect($connection);
            
            //如果不是当前的数据库连接，则需要重新配置
            if(self::$_conn !== $connection){
                
                //数据取出方式
                $fetchMode = Config::get('database.fetchMode');
                
                if($fetchMode){
                    
                    $driverInstance->setFetchMode($fetchMode);
                }
                
                //设置调试模式
                $configDebug = Config::get('global.debugSql');
                
                $isDebug     = (isset($configDebug) && is_bool($configDebug)) ? $configDebug : false;
                
                $driverInstance->setDebug($isDebug);
                
                if(!isset(static::$table)){
                    
                    throw new Exception("找不到指定的数据表名:" . static::$table, 102);
                }
                
                //设置当前model的表名
                $driverInstance->table(static::$table);
            }
            
            if(!method_exists($driverInstance, $method)){
                
                throw new Exception("找不到指定的数据库处理方法:" . $method, 103);
            }
            
            return call_user_func_array([$driverInstance, $method], $arguments);
        }
    }
    
    