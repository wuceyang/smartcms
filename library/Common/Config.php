<?php
    namespace Library\Common;

    use \Exception;

    class Config{

        protected static $_configMap = [];

        public static function get($key, $defaultValue = ''){

            if(!$key) throw new Exception("请指定要获取的配置参数名", 100);

            $keys = explode('.', $key);

            $file = $keys[0];

            $configFile = APP_ROOT . '/config/' . $file . '.php';

            if(!file_exists($configFile)){
                throw new Exception("找不到指定的配置文件:" . $file . '.php', 105);
            }

            if(!isset(self::$_configMap[$file])){

                self::$_configMap[$file] = include $configFile;

            }

            unset($keys[0]);

            $retval = self::$_configMap[$file];

            if($keys && is_array($keys)){

                foreach ($keys as $k => $v) {

                    if(!isset($retval[$v])){

                        return $defaultValue;

                    }

                    $retval = $retval[$v];

                }

            }

            return $retval;
        }

        public static function set($key, $val){

            if(!$key) throw new Exception("请指定要设置的配置参数名", 100);

            $keys   = explode('.', $key);

            //补位一个空缺
            $keys[] = '';

            $map    = $result = [];

            for ($i = count($keys) - 1; $i >= 0; $i--) {

                $key     = $keys[$i];

                $nextKey = $keys[$i + 1] ? $keys[$i + 1] : null;

                if($nextKey !== null) {

                    $result     = [$key => $map[$nextKey]];

                    $map[$key]  = $result;

                    continue;
                }

                $map[$key] = $val;
            }

            self::$_configMap = array_merge_recursive(self::$_configMap, $result);
        }
    }
