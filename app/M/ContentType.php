<?php
    namespace App\M;

    class ContentType extends Model{

        public static $table = 'content_type';

        public function addContentType($typeName){

            return $this->insert(['type_name' => $typeName]);
        }

        public function getTypeList(){

            return $this->orderBy(['id DESC'])->getRows();
        }

        public function setTypeInfo($typeid, $typeInfo){

            return $this->update('id = ?', [intval($typeid)], $typeInfo);
        }
    }