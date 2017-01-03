<?php
	namespace App\M;

	class YouYuUser extends Model{

		public static $table = 'tbl_user';

		public function getRandomUser(){

			return $this->where('user_type = 1 AND uid >= ? AND uid <= ?', [10001, 10133])
					->orderBy(['rand()'])
					->getRow();
		}
	}