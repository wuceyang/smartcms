<?php
    namespace App\M;

    class Recommend extends Model{

        public static $table = 'recommend';

        /**
         * 获取指定状态的推荐位列表
         * @param  int $status   指定的推荐位状态,1:正常,2:禁用
         * @param  int $page     当前页码
         * @param  int $pagesize 每页显示的数量
         * @return array
         */
        public function getRecommendList($status = 0, $page = 0, $pagesize = 0){

            $where = $param = [];

            if($status){

                $where[] = 'status = ?';

                $param[] = intval($status);
            }

            return $this->orderBy(['id DESC'])->page($page)->pagesize($pagesize)->getRows(implode(' AND ', $where), $param);
        }

        /**
         * 统计适合条件的推荐位数量
         * @param  int $status 推荐状态
         * @return int
         */
        public function getRecommendCount($status = 0){

            $where = $param = [];

            if($status){

                $where[] = 'status = ?';

                $param[] = intval($status);
            }

            return $this->getValue($where, $param);
        }

        /**
         * 录入推荐位
         * @param string $name       推荐位名称
         * @param int    $create_uid 推荐位创建人ID
         */
        public function add($name, $create_uid){

            return $this->insert(['name' => $name, 'create_uid' => $create_uid]);
        }
    }