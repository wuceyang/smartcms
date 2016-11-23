<?php
    namespace App\Helper;

    use \Library\Curl\Curl;
    use \Config;

    class HttpRequest extends Curl{

        public function __construct(){

            $this->setTimeout(30);
        }

        /**
         * POST请求
         * @param  string $url           请求的URL
         * @param  array  $param         请求的参数
         * @param  bool   $customRequest 是否post发送自定义数据
         * @return string
         */
        public function doPost($url, $param, $customRequest = false){

            $this->initialRequest($url, $param);

            if($customRequest){

                $this->setCustomPost();
            }

            $this->setMethod('post');

            return $this->doRequest();
        }

        /**
         * GET请求
         * @param  string $url   请求的URL
         * @param  array  $param 请求的参数
         * @return string
         */
        public function doGet($url, $param = []){

            if($param){

                $symbol = strpos($url, '?') === false ? '?' : '&';

                $url    = $url . $symbol . http_build_query($param);
            }

            $this->initialRequest($url, []);

            $this->setMethod('get');

            return $this->doRequest();
        }

        /**
         * 初始化请求
         * @param  string $url   请求的url
         * @param  array  $param  请求参数
         * @return null
         */
        private function initialRequest($url, $param = []){

            $this->setUrl($url);

            if($param){

                $this->setParam($param);
            }
        }
    }
