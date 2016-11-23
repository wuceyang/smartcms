<?php
	namespace App\Helper\Storage;

	use \Qiniu\Auth;

	class Qiniu{

		const ACCESSKEY = 'ZGyOtBv9cJXL8FS7XuHqdpVxz-9RgnHIZ5YB69Re';

		const SECRETKEY = 'v2AmKoF3G2RPy9Z2P22p1hGzpn4aqhH1Lc1W11re';

		public function getToken($bucket){

			$auth 	 = new Auth(self::ACCESSKEY, self::SECRETKEY);

			$token = $auth->uploadToken($bucket);

			return $token;
		}
	}