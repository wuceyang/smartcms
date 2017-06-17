<?php
	namespace Cli;

	class IqiyiCategory extends \App\Helper\HttpRequest{

		private $ext = 'mp4';

		public function start(){

			$urls = [
					//网络热门
					// 'http://list.iqiyi.com/www/1/-12---------0---11-{page}-1-iqiyi--.html' => 2,
					// 'http://list.iqiyi.com/www/15/-1264-----28966-------11-{page}-1-iqiyi--.html' => 2,
					// 'http://list.iqiyi.com/www/15/-1264-----28957-------11-{page}-1-iqiyi--.html' => 2,
					// 'http://list.iqiyi.com/www/15/-1264-----28960-------11-{page}-1-iqiyi--.html' => 2,
					// 'http://list.iqiyi.com/www/15/-1264-----28962-------11-{page}-1-iqiyi--.html' => 2,
					// 'http://list.iqiyi.com/www/15/-1264-----28963-------11-{page}-1-iqiyi--.html' => 2,
					// 'http://list.iqiyi.com/www/15/-------------11-{page}-1-iqiyi--.html' => 2,
					//宝贝幼教
					// 'http://list.iqiyi.com/www/15/-1264--4489----------11-{page}-1-iqiyi--.html' => 3,
					//亲子启蒙
					// 'http://list.iqiyi.com/www/15/-1264--28929----------11-{page}-1-iqiyi--.html' => 4,
					//快乐童年
					// 'http://list.iqiyi.com/www/15/-1264--4493----------11-{page}-1-iqiyi--.html' => 5,
					//欢乐驿站
					'http://list.iqiyi.com/www/2/----------0---11-{page}-1---.html' => 6
					];

			foreach ($urls as $aurl => $catid) {

				$totalPage = 1;

				$page      = 1;

				for(; $page <= $totalPage; $page++){

					echo $page . "\n";

					$url  = str_replace('{page}', $page, $aurl);

					echo $url . "\n";

					echo "开始请求html\n";
					$html = $this->getHtml($url);
					echo "请求html完成\n";

					if(!$html){

						echo "获取html发生错误:" . $url . "->";

						continue;
					}

					echo "开始分析数据\n";
					$movies = $this->getMovieInfo($url, $html, $catid);
					echo "分析数据完成\n";

					$category = new \App\M\MovieCate();

					if(!$category->insert($movies)){

						echo "写入分类数据失败" . var_export($category->getError(), true);
					}

					echo "写入分类数据" . count($movies) . "条\n";

					if($page == 1){

						$totalPage =  $this->getTotalPage($html);
					}
				}
			}
		}

		public function getHtml($url){

			return $this->doGet($url);
		}

		public function getTotalPage($html){

			preg_match_all('/<a\s+data-key="(\d+)"/is', $html, $match);

			if(!$match[1]){

				return 1;
			}

			return intval(end($match[1]));
		}

		public function getMovieInfo($url, $html, $catid){

			preg_match_all('/<a[^>]+href="([^"]+)"[^>]+class="site-piclist_pic_link"[^>]+>[^<]*<img[^>]+title="([^"]+)"[^>]+src\s*=\s*"([^"]+)"/is', $html, $match);

			if(!$match[1] || !$match[2] || !$match[3]){

				return [];
			}

			$iteminfo = [];

			foreach ($match[1] as $k => $v) {

				$url   = $match[1][$k];

				$title = $match[2][$k];

				$icon = $match[3][$k];
				
				$iteminfo[] = ['title' => $title, 'icon' => $icon, '`desc`' => $url, 'parentid' => $catid, 'state' => 1];
			}

			return $iteminfo;
		}
	}