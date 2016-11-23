<?php
	/**
	 *文件上传类,使用实例
	  $uploader = new uploader('file');
	  $uploader -> setMaxUploadSize(1024);
	  $uploader -> setExtentions(array('png','jpg'));
	  $uploader -> setUploadSaveDir(ROOT . 'upload');
	  $uploadinfo = $uploader -> doUpload();
	 * @author:随风
	 * @email:wcfh07@163.com
	 * @qq:20297988
	 * @modified:2013-09-01
	 */
	class uploader{

		protected $_fieldName = ''; //上传文件字段名称
		protected $saveDir = './'; //文件保存目录
		protected $_files = array(); //存储所有的上传文件，数组
		protected $_failed = array(); //上传失败的文件信息,二维数组，格式:array('name' => '','info' => array() //失败的文件信息)
		protected $_legalExts = array('jpg','gif','png','jpeg'); //合法扩展名
		protected $_basename = ''; //生成的新文件名不变部分
		
		public function __construct ( $fieldName = '' ){
			if($fieldName) $this -> setField($fieldName);
			$this -> _basename = date('YmdHis');
		}

		/**
		 *设置文件字段
		 */
		public function setField($fieldName){
			$this -> _fieldName = $fieldName;
			$this -> extractFiles2Array();
		}

		/**
		 *释放$_FILES到数组
		 */
		public function extractFiles2Array (){
			$fileArray = $_FILES[$this -> _fieldName];
			if(is_array($fileArray['name'])){
				foreach($fileArray['name'] as $k => $v ){
					$this -> _files[] = array(
											'name' => $v,
											'tmp_name' => $fileArray['tmp_name'][$k],
											'type' => $fileArray['type'][$k],
											'size' => $fileArray['size'][$k],
											'error' => $fileArray['error'][$k],
										  );
				}
				return;
			}
			$this -> _files[] = $fileArray;
		}

		/**
		 *检查系统错误代码
		 */
		protected function checkErrors (){
			foreach( $this -> _files as $k => $v ){
				if($v['error'] > 0){
					$this -> _failed[] = array(
											'code' => 1,
											'name' =>$v['name'],
											'info' => '文件大小超出前台设定值'
										   );
					unset($this -> _files[$k]);
					continue;
				}
				if($this -> maxUploadSize && $this -> maxUploadSize <= $v['size']){
					$this -> _failed[] = array(
											'code' => 2,
											'name' =>$v['name'],
											'info' => '文件大小超出程序设定值'
										   );
					unset($this -> _files[$k]);
					continue;
				}
				if($this -> _legalExts && !in_array($this -> getExtensionsByName($v['name']), $this -> _legalExts)){
					$this -> _failed[] = array(
											'code' => 3,
											'name' =>$v['name'],
											'info' => '文件非法，不允许上传此类型文件'
										   );
					unset($this -> _files[$k]);
					continue;
				}
			}
		}

		/**
		 *设置合法的扩展名
		 */
		public function setExtentions (array $exts = array()){
			$this -> _legalExts = $exts;
		}

		/**
		 *根据文件名获取扩展名
		 */
		protected function getExtensionsByName($filename){
			$nameInfo = pathinfo($filename);
			return $nameInfo['extension'];
		}

		/**
		 *设置最大上传文件大小，单位k
		 */
		public function setMaxUploadSize ($size){
			$this -> maxUploadSize = intval($size) * 1024;
		}

		/**
		 *设定上传路径
		 */
		public function setUploadSaveDir ($saveDir){
			$this -> saveDir = substr($saveDir,-1) == '/'?$saveDir:$saveDir . '/';
			if(!is_dir($this -> saveDir)) mkdir($this -> saveDir,0777,true);
		}

		/**
		 *上传文件
		 */
		public function doUpload ($saveDir = '',$savedPrefix = ''){
			if($saveDir) $this -> setUploadSaveDir($saveDir);
			$this -> checkErrors();
			if(count($this -> _files) == 0) return array();
			$uploadInfo = array();
			foreach( $this -> _files as $k => $v ){
				if(is_uploaded_file($v['tmp_name'])){
					$extension = $this -> getExtensionsByName($v['name']);
					$savepath = $this -> saveDir . $this -> getFileName($k,$savedPrefix,$extension);
					if(move_uploaded_file($v['tmp_name'],$savepath)){
						$uploadInfo[] = array(
												'localname' => $v['name'],
												'ext' => $extension,
												'size' => $v['size'],
												'uploadpath' => $savepath
											 );
						continue;
					}
					$this -> _failed[] = array(
											'code' => 4,
											'name' => $v['name'],
											'info' => '文件非法，不是上传的文件'
											);
					continue;
				}
			}
			return $uploadInfo;
		}

		/**
		 *生成文件名
		 */
		protected function getFileName($index,$prefix,$ext){
			$parts = [];
			if($prefix) $parts[] = $prefix;
			$parts[] = $this -> _basename . $index;
			return implode('_',$parts) . '.' . $ext;
		}

		/**
		 *获取错误信息
		 */
		public function getError (){
			return $this -> _failed;
		}
	}
	
?>
