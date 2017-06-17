<?php
	namespace App\Helper;

	class Uploader{

		protected $_uploadConfig = [];

		protected $_files 		 = [];

		protected $_errInfo 	 = [];

		const ErrCode = [
						//文件上传成功
						'success' 			 => 0,
						//超出设定大小
						'sizeExcceed'        => 1,
						//文件类型不正确
						'typeInvalid'        => 2,
						//上传时发生错误
						'uploadError'        => 3,
						//文件不存在
						'fileMissing'        => 4,
						//非法的上传文件
						'fileInvalid'        => 5,
						//未指定保存目录
						'savePathInvalid'    => 6,
						//移动到指定目录失败
						'uploadFailed'       => 7,
						//保存目录不可写
						'savePathUnWritable' => 8
						];

		public function __construct($fieldName, $uploadConfigure = []){

			if(isset($_FILES[$fieldName])){

				$files = [];

				if(is_array($_FILES[$fieldName]['name'])){

					foreach ($_FILES[$fieldName]['name'] as $k => $v) {

						if(!$_FILES[$fieldName]['name'][$k]) {

							continue;
						}

						$this->_files[] = [
									'localName' => $_FILES[$fieldName]['name'][$k],
									'filePath'  => $_FILES[$fieldName]['tmp_name'][$k],
									'fileSize'  => $_FILES[$fieldName]['size'][$k],
									'mimeType'  => $_FILES[$fieldName]['type'][$k],
									'errCode'   => $_FILES[$fieldName]['error'][$k],
									];
					}
				}else{

					if($_FILES[$fieldName]['name']) {

						$this->_files[] = [
									'localName' => $_FILES[$fieldName]['name'],
									'filePath'  => $_FILES[$fieldName]['tmp_name'],
									'fileSize'  => $_FILES[$fieldName]['size'],
									'mimeType'  => $_FILES[$fieldName]['type'],
									'errCode'   => $_FILES[$fieldName]['error'],
									];
					}
				}
			}

			if(isset($uploadConfigure['maxsize'])){

				$uploadConfigure['maxsize'] = $this->size2Byte($uploadConfigure['maxsize']);
			}

			if($uploadConfigure) $this->_uploadConfig = $uploadConfigure;
		}

		/**
		 * 设置允许上传的最大文件大小
		 * @param int $maxFileSize 文件大小
		 * @return null
		 */
		public function setMaxSize($maxFileSize){

			$this->_uploadConfig['maxsize'] = $this->size2Byte($maxFileSize);
		}

		/**
		 * 设置允许上传的文件的mime
		 * @param array $mime 允许上传的mime数组
		 * @return null
		 */
		public function setLegalMine($mime = []){

			$this->_uploadConfig['mime'] = is_array($mime) ? $mime : [$mime];
		}

		/**
		 * 指定文件保存路径
		 * @param string $saveDir 保存路径
		 * @return null
		 */
		public function setSavePath($saveDir){

			$this->_uploadConfig['savedir'] = substr($saveDir, -1) == '/' ? $saveDir : $saveDir . '/';
		}

		/**
		 * 文件上传
		 * @param  string $prefix 文件保存前缀
		 * @return array 文件上传结果，包含result:上传成功/失败
		 */
		public function doUpload($prefix = ''){

			$saveInfo 	= [];

			$time 		= microtime();

			foreach ($this->_files as $k => $v) {

				if($v['errCode']){

					$saveInfo[] = ['result' => false, 'file' => $v['localName'], 'code' => self::ErrCode['uploadError'], 'error' => '上传时发生错误，错误码:' . $v['errCode']];

					continue;
				}
				
				if(!file_exists($v['filePath'])){

					$saveInfo[] = ['result' => false, 'file' => $v['localName'], 'code' => self::ErrCode['fileMissing'], 'error' => '文件不存在'];

					continue;
				}

				if(!is_uploaded_file($v['filePath'])){

					$saveInfo[] = ['result' => false, 'file' => $v['localName'], 'code' => self::ErrCode['fileInvalid'], 'error' => '不是合法的上传文件'];

					continue;
				}

				if(isset($this->_uploadConfig['maxsize']) && $v['fileSize'] > $this->_uploadConfig['maxsize']){

					$fileSize   = number_format($v['fileSize'] / (1024 * 1024), 2) . 'M';

					$maxSize    = number_format($this->_uploadConfig['maxsize'] / (1024 * 1024), 2) . 'M';

					if($fileSize < 1){

						$fileSize = number_format($v['fileSize'] / 1024, 2) . 'K';
					}

					if($maxSize < 1){

						$maxSize = number_format($v['fileSize'] / (1024 * 1024), 2) . 'K';
					}

					$saveInfo[] = ['result' => false, 'file' => $v['localName'], 'code' => self::ErrCode['sizeExcceed'], 'error' => '文件大小(' . $fileSize . ')超出限制:' . $maxSize];

					continue;
				}

				if(isset($this->_uploadConfig['mime']) && !in_array($v['mimeType'], $this->_uploadConfig['mime'])){

					$saveInfo[] = ['result' => false, 'file' => $v['localName'], 'code' => self::ErrCode['typeInvalid'], 'error' => '文件类型不正确,只允许上传:' . implode(',', $this->_uploadConfig['mime']) . '类型的文件'];

					continue;
				}

				$saveInfo[] = $this->saveFile($v, $k, $time, $prefix);
			}

			return $saveInfo;
		}

		protected function saveFile($fileinfo, $idx, $nameSeed, $prefix){

			if(!isset($this->_uploadConfig['savedir'])){

				return ['result' => false, 'file' => $fileinfo['localName'], 'code' => self::ErrCode['savePathInvalid'], 'error' => '请指定文件保存路径'];
			}

			if(!is_dir($this->_uploadConfig['savedir'])){

				mkdir($this->_uploadConfig['savedir'], 0777, true);
			}

			if(!is_writeable($this->_uploadConfig['savedir'])){

				return ['result' => false, 'file' => $fileinfo['localName'], 'code' => self::ErrCode['savePathUnWritable'], 'error' => '指定的文件保存目录不可写'];
			}

			$fileName = md5($idx . $nameSeed);

			$savePath = $this->_uploadConfig['savedir'] . $prefix . $fileName . substr($fileinfo['localName'], strrpos($fileinfo['localName'], '.'));

			if(!move_uploaded_file($fileinfo['filePath'], $savePath)){

				return ['result' => false, 'file' => $fileinfo['localName'], 'code' => self::ErrCode['uploadFailed'], 'error' => '移动文件到上传目录失败'];
			}

			return ['result' => true, 'file' => $fileinfo['localName'],'code' => self::ErrCode['success'], 'error' => '', 'url' => $savePath];
		}

		/**
		 * 文件大小单位转换
		 * @param  string $size 文件大小，如20m,1024k，1g等
		 * @return numeric
		 */
		protected function size2Byte($size){

			if (preg_match('/^\d+\.\d+$/', $size) || preg_match('/^\d+$/', $size)) {
				
				return $size;
			}

			$suffix = strtoupper(substr($size, -1));

			switch ($suffix) {

				case 'K':
					
					$times = 1024;

					break;

				case 'M':
					
					$times = pow(1024,2);

					break;

				case 'G':
					
					$times = pow(1024,3);

					break;
				
				default:

					$times = 0;

					break;
			}

			$size = substr($size, 0, -1) * $times;

			return $size;
		}

		/**
		 * 获取错误信息
		 * @return string 错误信息
		 */
		public function getErrorInfo(){

			return $this->_errInfo;
		}
	}