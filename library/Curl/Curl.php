<?php

	namespace Library\Curl;

	class Curl{

		protected $_url 		= '';
		protected $_params 		= [];
		protected $_method 		= 'get';
		protected $_ch 			= null;
		protected $_err 		= '';
		protected $_retHeader  	= false;
		protected $_header 		= [];
		protected $_timeout		= 10;
		protected $_curlOptions = [];
		protected $_isCustomPost = false; //是否是自定义的post格式
		// protected $_transformWithBinary = false; //使用二进制传输数据

		public function __construct($url = '', $params = [], $method = 'get'){
			
			$this->setUrl($url);
			
			$this->setParam($params);
			
			$this->setMethod($method);
		}

		public function setUrl($url){
			
			$this->_url = $url;
		}

		public function setParam($params){
			
			$this->_params = $params;
		}

		public function setMethod($method){
			
			if(in_array($method, array('get','post'))){
			
				$this->_method = strtolower($method);
			}
		}

		public function returnWithHeader($headerRequired){
			
			$this->_retHeader = $headerRequired;
		}

		public function setTimeout($timeout){
			
			$this->_timeout = $timeout;
		}

		public function setCustomPost(){
			
			$this->_isCustomPost = true;
			
			$this->setMethod('post');
		}

		public function setCustomHeader($header){
			
			if($header){
			
				$this->_header = array_merge(is_array($header)?$header:[$header], $this->_header);
			}
		}
		
		public function setCURLOption(array $options = []){
			
			$this->_curlOptions = array_merge($this->_curlOptions, $option);
		}

		public function clearHeader(){
			
			$this->_header = [];
		}

		public function doRequest(){
			
			if(!$this->_url){
			
				$this->_err = 'Invalid Url';
			
				return false;
			}
			
			$this->_url = $this->buildUrl();
			
			$this->_ch 	= curl_init();
			
			curl_setopt($this->_ch, CURLOPT_URL, $this->_url);
			
			curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
			
			curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, true);
			
			if($this->_method == 'post'){
			
				if($this->_isCustomPost){
			
					curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'POST');
				}else{
			
					curl_setopt($this->_ch, CURLOPT_POST, true);
				}
			
				curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $this->_params);
			}
			if($this->_header){
			
				$header = [];
			
				foreach ($this->_header as $k => $v) {
			
					$header[] = $k . ': ' . $v;
				}
			
				curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $header);
			}
			if($this->isSslRequest()){
			
				curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书  
        	
        		curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名  
			}
			if($this->_retHeader){
			
				curl_setopt($this->_ch, CURLOPT_HEADER, true);
			}
			
			curl_setopt($this->_ch, CURLOPT_TIMEOUT, $this->_timeout);
			
			curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $this->_timeout);
			
			if($this->_curlOptions){
				
				curl_setopt_array($this->_ch, $this->_curlOptions);
			}
			
			$retdata = curl_exec($this->_ch);
			
			if(!$retdata){
			
				$this->_err = curl_error($this->_ch);
			
				curl_close($this->_ch);
			
				return false;
			}
			
			curl_close($this->_ch);
			
			return $retdata;
		}

		protected function buildUrl(){
			
			if($this->_method == 'post') return $this->_url;
			
			$symbol = strpos($this->_url, '?') === false ? '?' : '&';
			
			return $this->_url . $symbol . (is_array($this->_params)?http_build_query($this->_params):$this->_params);
		}

		protected function isSslRequest(){
			
			list($protocol, $reqpath) = explode(':', $this->_url);
			
			return $protocol == 'https';
		}

		public function getError(){
			
			return $this->_err;
		}
	}