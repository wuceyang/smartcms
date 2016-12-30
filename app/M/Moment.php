<?php
    namespace App\M;

    class Moment extends Model{

        public static $table = 'tbl_moment';

        public function getMomentList($actorId = null, $page = 0, $pagesize = 20){

            $where = $param = [];

            if($actorId){

                $where[] = 'aid = ?';

                $param[] = intval($aid);
            }

            if($where){

                $this->where(implode(' AND ', $where), $param);
            }

            if($page && $pagesize){

                $this->page($page)->pagesize($pagesize);
            }

            return $this->orderBy(['mid DESC'])->getRows();

        }

        public function getTotalMoment($actorId = null){

            $where = $param = [];

            if($actorId){

                $where[] = 'aid = ?';

                $param[] = intval($aid);
            }

            if($where){

                $this->where(implode(' AND ', $where), $param);
            }

            return $this->getCount();
        }

        public function doPost($content, $type, $video, $audio, $image, $aid, $uid, $needPay){


            $data = [

                'aid'           => intval($aid),
                'uid'           => intval($uid),
                'content_text'  => $content,
                'img_url'       => json_encode($image),
                'img_count'     => count($image),
                'content_type'  => intval($type),
                'video_url'     => $video,
                'needPay'       => intval($needPay),
                'create_time'   => date('Y-m-d H:i:s'),
            ];

            return $this->insert($data);
        }
    }