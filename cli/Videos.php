<?php
        namespace Cli;

        use \App\M\MovieCate;
        use \App\M\Video;

        class Videos extends \App\Helper\HttpRequest{

                public function start(){

                    $cat = new MovieCate();

                    $num = 100;

                    $catpage = 1;

                    $movie = new \App\M\Video();
                    //上次CATE表最大ID:4469，Video表最大ID:19764
                    while($cats = $cat->where('id > 4469')->orderBy(['id ASC'])->page($catpage)->pagesize($num)->getRows()){
                        //while($cats = $cat->where('id = 86')->orderBy(['id ASC'])->page($catpage)->pagesize($num)->getRows()){

                            $catpage++;

                            foreach ($cats as $k => $v) {

                                    $url = $v['desc'];

                                    echo $url . "[" . $v['id'] . "]\n";

                                    $html = $this->getHtml($url);

                                    if(!$html){

                                        echo "获取html失败:" . $url . "\n";

                                        continue;
                                    }

                                    $moviePage = $this->getMoviePage($html);

                                    if(!$moviePage){

                                        echo "获取子剧集分页失败\n";

                                        $moviePage = [1];

                                        echo "自定义分页参数，获取剧集\n";
                                    }

                                    $videoId = $this->getVideoId($html);

                                    echo "剧集ID:" . $videoId . "\n";

                                    if(!$videoId){

                                            echo "获取剧集id失败\n";

                                            continue;
                                    }

                                    if($moviePage){

                                    	$allMovie = [];

                                        foreach($moviePage as $page){

                                            $urls = $this->getUrlByPage($videoId, $page, $v['id']);

                                            if(isset($urls['del'])){

		                                    	echo "检测到更新中剧集,跳过【" . $v['id'] . "】【" . $url . "】\n";

		                                    	continue 2;
		                                    }

		                                    $allMovie = array_merge($allMovie, $urls);
                                        }

                                        if($allMovie && !$movie->insert($allMovie)){

                                            echo "写入分页剧集数据失败:" . var_export($movie->getError(), true) . "\n";

                                            continue;
                                        }

                                        echo "写入分页数据" . count($allMovie) . "条\n"; 
                                    }
                            }
                    }
                }

                public function getHtml($url){

                        return $this->doGet($url);
                }

                public function getVideoId($html){

                        return intval(preg_replace('/.+?albumId:\s+(\d+),.+/is', '$1', $html));
                }

                public function getUrlByPage($videoId, $page, $catid){

                    $url = "http://cache.video.iqiyi.com/jp/avlist/" . $videoId . "/" . $page . "/50/?albumId=" . $videoId . "&pageNum=50&pageNo=" . $page . "&callback=window.Q.__callbacks__.cbturrz5";

                    $retstr = $this->doGet($url);

                    if(!$retstr){

                            echo "请求分页数据失败\n";
                            return [];
                    }

                    $retstr = preg_replace('/^try{window.Q.__callbacks__.cbturrz5\(/i', '', $retstr);

                    $retstr = preg_replace('/\);}catch\(e\){};$/', '', $retstr);

                    $videoinfo = json_decode($retstr, true);

                    if(!$videoinfo){

                            echo "分析分页数据失败\n";
                            return [];
                    }

                    $videos = [];

                    $list = $videoinfo['data']['vlist'];

                    foreach ($list as $k => $v) {

                    	if(strpos($v['vn'], '预告') !== false || strpos($v['vn'], '剧透') !== false){

							return ['del' => true];
						}
                            
                        $videos[] = [
										'title'      => $v['vn'],
										'url'        => $v['vurl'],
										'extension'  => 'mp4',
										'storeat'    => 'iqiyi',
										'`password`' => '',
										'categoryid' => $catid,
										'cookiestr'  => '',
										'state'      => 1,
                                    ];
                    }

                    return $videos;
                }

                public function getMoviePage($html){

                        preg_match_all('/data-avlist-page="(\d+)"/is', $html, $pages);

                        if(!$pages[1]){

                                return '';
                        }

                        $pagelist = [];

                        foreach ($pages[1] as $k => $v) {
                                
                                $pagelist[] = intval(trim($v));
                        }

                        return array_unique(array_values($pagelist));
                }

                public function getPlayUrls($html, $catid){

                    preg_match_all('/<a\s+href="([^"]+)"\s+rseat="juji_jshu_\d+">([^<]+)<\/a>/is', $html, $matchs);

                    if($matchs[1] && $matchs[2]){

                        $urls   = $matchs[1];

                        $titles = $matchs[2];
                    }

                    if(!$urls && !$titles){

                        preg_match_all('/<a\s+href="([^"]+)"[^>]+class="plotNum"\s*>([^<]+)<\/a>/is', $html, $matchs);

                        $urls  = $matchs[1];

                        $title = $matchs[2];
                    }
                    
                    if(!$urls && !$titles){

                        return [];
                    }

                    $subinfo = [];

                    foreach ($urls as $k => $v) {

                        if(!$v || !isset($titles[$k])){

                                continue;
                        }

                        $subinfo[] = [
                                        'title'      => trim($titles[$k]),
                                        'url'        => trim($urls[$k]),
                                        'extension'  => 'mp4',
                                        'storeat'    => 'iqiyi',
                                        '`password`' => '',
                                        'categoryid' => $catid,
                                        'cookiestr'  => '',
                                        'state'      => 1
                                  ];
                    }

                    return $subinfo;
                }
        }

