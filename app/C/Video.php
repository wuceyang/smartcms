<?php
	namespace App\C;

	use \App\Helper\Redis;
	use \App\Helper\RedisKeys;

	class Video extends BaseController{

		/**
		 * 根据分类获取子分类列表
		 */
		public function GetCatByPage(){

			$catId    = intval($this->request->get('cat_id'));
			
			$page     = max(1, intval($this->request->get('page')));
			
			$pageSize = intval($this->request->get('per'));

			$pageSize = $pageSize ? abs($pageSize) : 6;
			
			$catKey   = RedisKeys::CATEGORY_LIST . $catId;

			$infoKey  = RedisKeys::CATEGORY_INFO;
			
			$redis    = Redis::getInstance();

			$offset   = ($page - 1) * $pageSize;

			$catList  = $redis->zrangebyscore($catKey, '-inf', '+inf', 'LIMIT', $offset, $pageSize);

			$category = [];

			$total    = $redis->zcard($catKey);

			if($catList){

				$cmd = ['hgetall' => $catList];

				/*foreach ($catList as $k => $v) {
					
					$cmd['hgetall'][] = [$infoKey . $v];
				}*/

				$catInfoList = $redis->multiExec($cmd);

				foreach ($catInfoList as $k => $v) {

					if(!$v) continue;
					
					$v['has_sub_list'] = $v['level'] < 3;

					unset($v['level']);
					
					$category[]        = $v;
				}
			}

			$retdata = [
				'data'  => $category,
				'ret'   => 0,
				'total' => $total,
			];

			return $this->response($retdata);
		}

		/**
		 * 根据分类获取剧集列表
		 */
		public function GetVideoListByCatid(){

			$catId    = intval($this->request->get('cat_id'));
			
			$catKey   = RedisKeys::CATEGORY_LIST . $catId;
			
			$redis    = Redis::getInstance();

			$videoId  = $redis->zrangebyscore($catKey, '-inf', '+inf');

			$cmd 	  = ['hgetall' => $videoId];

			$total 		= $redis->zcard($catKey);

			$videoList 	= [];

			if($cmd['hgetall']){

				$videos       = $redis->multiExec($cmd);
				
				$catIds       = array_filter(array_column($videos, 'categoryid'));

				$cmd = ['hgetall' => []];

				foreach ($catIds as $k => $v) {
					
					$cmd['hgetall'][] = [RedisKeys::CATEGORY_INFO . $v];
				}
				
				$category     = [];
				
				$categoryInfo = $redis->multiExec($cmd);

				foreach ($categoryInfo as $k => $v) {
					
					if(!$v) continue;

					$category[$v['id']] = $v['title'];
				}

				foreach ($videos as $k => $v) {

					if(!$v || !isset($v['categoryid']) || !isset($category[$v['categoryid']])) continue;

					$v['cat_title'] = $category[$v['categoryid']];

					$videoList[]    = $v;
				}
			}

			$retdata = [
				'ret'   => 0,
				'total' => $total,
				'data'  => $videoList
			];

			return $this->response($retdata);
		}
	}