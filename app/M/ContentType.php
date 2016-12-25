<?php
    namespace App\M;

    use \App\Helper\Enum;

    class ContentType extends Model{

        public static $table = 'cms_content_type';

        public function addContentType($typeName){

            return $this->insert(['type_name' => $typeName]);
        }

        public function getTypeList($status = Enum::STATUS_ALL){

            $where = $param = [];

            if($status){

                $where[] = 'status = ?';

                $param[] = intval($status);
            }

            return $this->orderBy(['id DESC'])->getRows(implode(' AND ', $where), $param);
        }

        public function setTypeInfo($typeid, $typeInfo){

            return $this->update('id = ?', [intval($typeid)], $typeInfo);
        }
    }