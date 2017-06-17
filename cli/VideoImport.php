<?php
	namespace Cli;

	use \App\M\IqiyiVideo;
	use \App\M\IqiyiCategory;
	use \App\Helper\Redis;
	use \App\Helper\RedisKeys;

	class VideoImport{

		public function start(){

			$video    = new IqiyiVideo();
			
			$category = new IqiyiCategory();
			
			$redis    = Redis::getInstance();
			
			$pagesize = 100;
			
			$page     = 1;

			while($categories = $category->where('id > 6 AND state = 1')->page($page)->pagesize($pagesize)->getRows()){

				$page++;

				$cmd      = ['hmset' => [], 'zadd' => []];

				foreach ($categories as $k => $v) {
					
					$videos = $video->where('categoryid = ? AND state = 1', [$v['id']])->getRows();

					if(!$videos) continue;

					$categoryKey = RedisKeys::CATEGORY_LIST . $v['id'];

					foreach ($videos as $sk => $sv) {

						$videoKey 		= RedisKeys::VIDEO_INFO . $sv['id'];
						
						$cmd['hmset'][] = [$videoKey, $sv];

						$cmd['zadd'][]  = [$categoryKey, $sv['id'], $videoKey];
					}
				}

				if(!$cmd['hmset']){

					unset($cmd['hmset']);
				}

				if(!$cmd['zadd']){

					unset($cmd['zadd']);
				}

				if($cmd){

					$redis->multiExec($cmd);
				}

				echo "成功导入视频" . count($categories) . "条\n";
			}
		}
	}