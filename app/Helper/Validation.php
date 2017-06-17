<?php
	namespace App\Helper;

	class Validation{

		public static function isMobile($str){

			return preg_match('/1[3578]\d{9}/i', $str);
		}

		public static function isUsername($str){

			return preg_match('/^[a-z0-9_]{6,16}$/i', $str);
		}

		public static function isPassword($str){

			return preg_match('/^[^\s]{6,}$/i', $str);
		}
	}