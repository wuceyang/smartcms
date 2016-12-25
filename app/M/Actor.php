<?php
    namespace App\M;

    class Actor extends Model{

        public static $table = 'tbl_actor';

        public function getActorByName($actorName){

            return $this->where('nickname = ?', [$actorName])->getRow();
        }
    }