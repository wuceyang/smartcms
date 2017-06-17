<?php
	namespace App\Helper;

	use \Config;

	class File{

		protected static $_file 	= '';
		protected static $_fileName = '';
		protected static $_fileInfo = [];

		public static function setFile($file){

			if(is_array($file)){

				self::$_file     = $file['tmp_name'];
				
				self::$_fileName = $file['name'];

				return ;

			}

			self::$_file = $file;

		}

		public static function Validate(){

			$uploadConfig = Config::get('global.upload');

			if(!file_exists(self::$_file)){

				return '文件不存在,请检查文件路径';

			}

			$size = filesize(self::$_file);

			if($size > $uploadConfig['maxsize'] * 1024){

				return '文件大小不正确。上传文件不得大于:' . $uploadConfig['maxsize'] . 'K';

			}

			$fileinfo = pathinfo(self::$_fileName);

			if(!in_array(strtolower($fileinfo['extension']), $uploadConfig['extension'])){

				return '文件类型不正确，只允许上传扩展名为:"' . implode(',', $uploadConfig['extension']) . '"的文件';

			}

			$fileinfo['size'] 	= $size;

			self::$_fileInfo 	= $fileinfo;

			return '';

		}

		public static function getFileInfo($key = ''){

			return $key && isset(self::$_fileInfo[$key]) ? self::$_fileInfo[$key] : self::$_fileInfo;
		}

	}
