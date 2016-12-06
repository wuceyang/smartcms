<?php
	namespace App\Helper\Storage;

	use \Qiniu\Auth;

	class Qiniu{

		const ACCESSKEY = 'BTSnm515J0wVluBgLoQHGfiYfPOnQtnQf2DeCAAX';

		const SECRETKEY = 'AvOFWFwm7t8-boPOJ4apEp5cVKhevmdmFX2Ug9tt';

		const DOMAIN_IMAGE = 'http://oh0w1vops.bkt.clouddn.com';
		const DOMAIN_VIDEO = 'http://oh0xnhx0h.bkt.clouddn.com';
		const DOMAIN_OTHER = 'http://oh0x57g5y.bkt.clouddn.com';

		public function getToken($bucket){

			$auth 	 = new Auth(self::ACCESSKEY, self::SECRETKEY);

			$token = $auth->uploadToken($bucket);

			return $token;
		}
	}