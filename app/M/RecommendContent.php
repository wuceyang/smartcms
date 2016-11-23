<?php
    namespace App\M;

    class RecommendContent extends Model{

        public static $table = 'recommend_content';

        /**
         * 获取指定状态的推荐位与文章映射关系列表
         * @param  int $page     当前页码
         * @param  int $pagesize 每页显示的数量
         * @return array
         */
        public function getRelationList($page = 0, $pagesize = 0){

            return $this->orderBy(['id DESC'])->skip(($page - 1) * $pagesize)->limit($pagesize)->getRows();
        }

        /**
         * 统计适合条件的推荐位数量
         * @param  int $status 推荐状态
         * @return int
         */
        public function getRecommendCount(){

            return $this->getCount();
        }
    }