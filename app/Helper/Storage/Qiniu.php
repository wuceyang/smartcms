<?php
	namespace App\Helper\Storage;

	use \Qiniu\Auth;
	use Qiniu\Storage\UploadManager;

	class Qiniu{

		const ACCESSKEY = 'BTSnm515J0wVluBgLoQHGfiYfPOnQtnQf2DeCAAX';

		const SECRETKEY = 'AvOFWFwm7t8-boPOJ4apEp5cVKhevmdmFX2Ug9tt';

		const DOMAIN_IMAGE = 'http://oh0w1vops.bkt.clouddn.com';
		const DOMAIN_VIDEO = 'http://oh0xnhx0h.bkt.clouddn.com';
		const DOMAIN_AUDIO = 'http://oistx3ss2.bkt.clouddn.com';
		const DOMAIN_OTHER = 'http://oh0x57g5y.bkt.clouddn.com';

		const BUCKET_IMAGE = 'image';
		const BUCKET_VIDEO = 'video';
		const BUCKET_AUDIO = 'audio';
		const BUCKET_OTHER = 'other';

		public function getToken($bucket){

			$auth  = new Auth(self::ACCESSKEY, self::SECRETKEY);

			$token = $auth->uploadToken($bucket);

			return $token;
		}

		public function doUpload($bucket, $filepath, $content = '', $targetname = null){

			$auth 	 	= new Auth(self::ACCESSKEY, self::SECRETKEY);

			$token 	 	= $auth->uploadToken($bucket);

			$uploadMgr 	= new UploadManager();

			$key 		= $targetname;

			list($ret, $err) = $content ? $uploadMgr->put($token, $key, $content) : $uploadMgr->putFile($token, $key, $filePath);

			\Log::debug($err);

			return $err === null;
		}
	}