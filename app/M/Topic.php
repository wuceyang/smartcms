<?php
    namespace App\M;

    class Topic extends Model{

        public static $table = 'tbl_topic';

        public function getTopicList($actorId = null, $isdel = 0, $page = 0, $pagesize = 20){

            $where = $param = [];

            if($actorId){

                $where[] = 'aid = ?';

                $param[] = intval($aid);
            }

            if(is_numeric($isdel)){

                $where[] = 'is_del = ?';

                $param[] = intval($isdel);
            }

            if($where){

                $this->where(implode(' AND ', $where), $param);
            }

            if($page && $pagesize){

                $this->page($page)->pagesize($pagesize);
            }

            return $this->orderBy(['tid DESC'])->getRows();

        }

        public function getTotalTopic($actorId = null, $isdel = 0){

            $where = $param = [];

            if($actorId){

                $where[] = 'aid = ?';

                $param[] = intval($aid);
            }

            if(is_numeric($isdel)){

                $where[] = 'is_delete = ?';

                $param[] = intval($isdel);
            }

            if($where){

                $this->where(implode(' AND ', $where), $param);
            }

            return $this->getCount();
        }

        public function doTopic($content, $type, $action, $video, $audio, $image, $aid, $uid, $tag, $hot_available_datetime, $createTime){


            $data = [

                'aid'           => intval($aid),
                'uid'           => intval($uid),
                'content_text'  => $content,
                'img_url'       => json_encode($image),
                'img_count'     => count($image),
                'content_type'  => intval($type),
                'video_url'     => $video,
                'action'        => intval($action),
                '`check`'       => 1,
                'create_time'   => $createTime ? $createTime : date('Y-m-d H:i:s'),
                'tag'           => $tag,
            ];

            if($hot_available_datetime){

                $data['hot_available_time'] = $hot_available_datetime;
            }

            return $this->insert($data);
        }
    }