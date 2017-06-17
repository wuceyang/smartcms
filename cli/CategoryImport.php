<?php
	namespace Cli;

	use \App\Helper\Redis;
	use \App\M\IqiyiCategory;
	use \App\Helper\RedisKeys;

	class CategoryImport{

		public function start(){

			$category              = new IqiyiCategory();
			
			$parentId              = 0;
			
			$catInfoKey            = RedisKeys::CATEGORY_INFO;
			
			$catListKey            = RedisKeys::CATEGORY_LIST;
			
			$level                 = 1;
			
			$categoryInfo          = $category->where('parentid = ?', [intval($parentId)])->getRow();
			
			$categoryInfo['level'] = $level;
			
			$categoryQueue         = [$categoryInfo];

			$redis 				   = Redis::getInstance();

			while($categoryQueue && $categoryInfo = array_shift($categoryQueue)){

				$level 				   = $categoryInfo['level'];

				$cmd                   = ['hmset' => [], 'zadd' => []];
				
				$key                   = $catInfoKey . $categoryInfo['id'];
				
				$cmd['hmset'][]        = [$key, $categoryInfo];
				
				$categories            = $category->where('state = 1 AND parentid = ?', [intval($categoryInfo['id'])])->getRows();

				$subCatListKey 		   = $catListKey . $categoryInfo['id'];

				foreach ($categories as $k => $v) {

					$sublevel        = $level + 1;
					
					$key             = $catInfoKey . $v['id'];
					
					$v['level']      = $sublevel;
					
					$categoryQueue[] = $v;
					
					$cmd['hmset'][]  = [$key, $v];
					
					$cmd['zadd'][]   = [$subCatListKey, $v['id'], $key];
				}

				if(!$cmd['zadd']){

					unset($cmd['zadd']);
				}

				$redis->multiExec($cmd);

				echo "成功导入" . count($cmd['hmset']) . "条数据\n";
			}
		}
	}