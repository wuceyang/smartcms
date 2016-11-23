<?php
    namespace App\M;

    class Content extends Model{

        public static $table = 'content';

        public function getContentList($kw, $catid, $contentType, $status, $page = 0, $pagesize = 20){

            $where = $param = [];

            if($kw){

                $where[] = 'title LIKE ?';

                $param[] = "%" . addcslashes($kw, '%_') . "%";
            }

            if($catid){

                $catid = is_array($catid) ? array_map('intval', $catid) : [intval($catid)];

                $where[] = 'catid IN (' . implode(',', $catid) . ')';
            }

            if($contentType){

                $where[] = 'content_type = ?';

                $param[] = intval($contentType);
            }

            if($status){

                $status = is_array($status) ? array_map('intval', $status) : [intval($status)];

                $where[] = 'status IN (' . implode(',', $status) . ')';
            }

            if($page && $pagesize){

                $this->page($page)->pagesize($pagesize);
            }

            return $this->getRows(implode(' AND ', $where), $param);
        }

        public function getContentTotal($kw, $catid, $contentType, $status, $page = 0, $pagesize = 20){

            $where = $param = [];

            if($kw){

                $where[] = 'title LIKE ?';

                $param[] = "%" . addcslashes($kw, '%_') . "%";
            }

            if($catid){

                $catid = is_array($catid) ? array_map('intval', $catid) : [intval($catid)];

                $where[] = 'catid IN (' . implode(',', $catid) . ')';
            }

            if($contentType){

                $where[] = 'content_type = ?';

                $param[] = intval($contentType);
            }

            if($status){

                $status = is_array($status) ? array_map('intval', $status) : [intval($status)];

                $where[] = 'status IN (' . implode(',', $status) . ')';
            }

            return $this->getCount(implode(' AND ', $where), $param);
        }

        public function addContent($title, $catid, $summary, $content, $keyword, $author, $source, $templateid, $typeid, $createuid){

            $params = [
                'title'       => $title,
                'summary'     => $summary,
                'author'      => $author,
                'source'      => $source,
                'keyword'     => $keyword,
                'template_id' => intval($templateid),
                'content'     => $content,
                'type_id'     => $typeid,
                'create_uid'  => intval($createuid),
            ];

            return $this->insert($params);
        }

        public function setContentInfo($contentid, $contentInfo){

            return $this->update('id = ?', [intval($contentid)], $contentInfo);
        }
    }