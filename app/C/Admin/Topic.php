<?php
    namespace App\C\Admin;

    use \Request;
    use \Response;
    use \App\M\Topic AS mTopic;

    class Topic extends Base{

        public function index(Request $req, Response $resp){


        }

        public function doPost(Request $req, Response $resp){

            if(!$req->isPost()){

                $param = [
                            'tags' => [
                                        1 => '官方',
                                        2 => '推广',
                                        4 => '置顶',
                                        8 => '加精',
                                      ],
                         ];

                return $resp->withVars($param)->withView('admin/topic_add.html')->display();
            }

            $aid        = intval($req->post('actorId'));

            $title      = trim($req->post('title'));

            $content    = trim($req->post('content'));

            $type       = intval($req->post('type'));

            $video      = trim($req->post('video'));

            $image      = $req->post('image');

            $action     = intval($req->post('action'));

            $uid        = 1;

            $tag        = intval($req->post('tag'));

            $hotExpireAfter = $tag ? trim($req->post('hot_expire_after')) : null;

            if(!in_array($type, [1, 2, 3, 4])){

                return $this->error("参数错误，话题类型不正确", 101, "");
            }
            //文字话题
            if($type == 1){

                $content = strip_tags($content);
            }

            $audio = '';

            $topic = new mTopic();

            if(!$topic->doTopic($title, $content, $type, $action, $video, $audio, $image, $aid, $uid, $tag, $hotExpireAfter)){

                return $this->error('话题发表失败' . var_export($topic->getError(), true), 201, '');
            }
            //非文字话题，则需要生成html文件，并存储到七牛
            if($type != 1){

                $param = [
                            'title' => $title,
                            'content' => $content,
                            'video' => $video,
                            'audio' => $audio,
                            'image' => $image,
                         ];

                $html = $resp->withVars($param)->withView('admin/topic_info.html')->toString();

                $htmlfile = APP_ROOT . 'cache/html/' . $topicId . '.html';

                @file_put_contents($htmlfile, $html);

                if(file_exists($htmlfile)){

                    //上传文件到7牛
                    @unlink($htmlfile);
                }
            }

            return $this->success('话题发表成功','');
        }
    }