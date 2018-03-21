<?php
	namespace Library\Database;

	use \Exception;

	class DbAdapter{

		private static $_drivers = [];

		public static function Factory($driver = DriverType::MYSQL){

			switch ($driver) {

				case DriverType::MYSQL:

					if(isset(self::$_drivers[$driver])){

						return self::$_drivers[$driver];
					}

					self::$_drivers[$driver] = Mysql::getInstance();

					return self::$_drivers[$driver];

				default:

					throw new Exception("找不到指定的驱动[" . $driver . "]", 404);
				
					break;
			}
		}
	}