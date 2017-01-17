<?php
    namespace App\M;

    class Actor extends Model{

        public static $table = 'tbl_actor';

        public function getActorByName($actorName){

            return $this->where('nickname = ?', [$actorName])->getRow();
        }

        public function actorSearch($actorName){

            $likeStr = "%" . addcslashes($actorName, '_%') . "%";

            return $this->where('nickname like ?', [$likeStr])->page(0)->pagesize(10)->getRows();
        }
    }