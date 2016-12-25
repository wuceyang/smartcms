<?php
    namespace App\M;

    class Template extends Model{

        public static $table = 'cms_template';

        /**
         *查询模板列表
         *@param int $status    模板状态
         *@param int $page      当前页码
         *@param int $pagesize  每页显示的数量
         *@return array
         */
        public function getTemplateList($status, $page = 0, $pagesize = 20){

            $where = $param = [];

            if($status){

                $where[] = 'status = ?';

                $param[] = intval($status);
            }

            if($page && $pagesize){

                $this->page($page)->pagesize($pagesize);
            }

            return $this->getRows(implode(' AND ', $where), $param);
        }

        /**
         *添加模板
         *@param string $tplName    模板名称
         *@param string $tplContent 模板名称
         *@param int    $createUid  创建者用户ID
         *@return int
         */
        public function addTemplate($tplName, $tplContent, $createUid){

            $param = [
                'template_name' => trim($tplName),
                'template_html' => trim($tplContent),
                'create_date'   => date('Y-m-d H:i:s'),
                'create_uid'    => intval($createUid),
            ];

            return $this->insert($param);
        }

        /**
         *更新模板信息
         *@param int    $tplid   模板id
         *@param array  $tplInfo 需要更新的模板信息
         *@return int
         */
        public function setTemplateInfo($tplid, $tplInfo){

            return $this->update($tplInfo, 'id = ?', [intval($tplid)]);
        }

        /**
         *根据模板名称查询模板信息
         *@param string $tplName 要查询的模板名称
         *@return array
         */
        public function getTemplateByName($tplName){

            return $this->getRow('template_name = ?', [$tplName]);
        }
    }