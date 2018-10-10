<?php

	namespace Library\Database;

	use \Config;
    use \Exception;
    use \PDO;

	class Mysql extends \Library\Database\Database{

        protected $_fields           = [];
		protected $_bind             = [];
        protected $_where            = [];
        protected $_page  			 = 0;
        protected $_pagesize 		 = 20;
		protected $_table            = '';
        protected $_conn             = [];
        protected $_having           = [];
        protected $_order            = [];
        protected $_group            = [];
        protected $_limit            = 0;
        protected $_subBuilder       = '';
        protected $_offset           = 0;
        protected $_stmt 		     = null;
        protected $_join             = [];
        protected $_execFlag         = false;

        const EXECUTE       = 0;
        const SELECT        = 1;
        const UPDATE        = 2;
        const DELETE        = 3;
        const INSERT        = 4;
        const MULTI_INSERT  = 5;

        /**
         * 表连接查询
         * @param  string $tableWithAlias   连接的目标表，不带前缀，必须使用别名
         * @param  string $on               连接条件,表名需要使用别名
         * @param  string $type             连接类型
         * @return 当前对象的实例
         */
        public function join($tableWithAlias, $on, $type = JoinType::INNER){

            //如果没有使用别名
            if(strpos($tableWithAlias, ' ') === false){

                throw new \Exception("连表查询时，请使用别名");
            }

            $this->_join[] = ' ' . $type . ' ' . $this->getFullName($tableWithAlias) . ' ON ' . $this->getFullName($on);

            return $this;
        }

        /**
         * 内连接查询
         * @param  string $tableWithAlias   连接的目标表，不带前缀，必须使用别名
         * @param  string $on               连接条件,表名需要使用别名
         * @return 当前对象的实例
         */
        public function innerJoin($tableWithAlias, $on){

            return $this->join($tableWithAlias, $on, JoinType::INNER);
        }

        /**
         * 左连接查询
         * @param  string $tableWithAlias   连接的目标表，不带前缀，必须使用别名
         * @param  string $on               连接条件,表名需要使用别名
         * @return 当前对象的实例
         */
        public function leftJoin($tableWithAlias, $on){

            return $this->join($tableWithAlias, $on, JoinType::LEFT);
        }

        /**
         * 右连接查询
         * @param  string $tableWithAlias   连接的目标表，不带前缀，必须使用别名
         * @param  string $on               连接条件,表名需要使用别名
         * @return 当前对象的实例
         */
        public function rightJoin($tableWithAlias, $on){

            return $this->join($tableWithAlias, $on, JoinType::RIGHT);
        }

        /**
         * 外连接查询
         * @param  string $tableWithAlias   连接的目标表，不带前缀，必须使用别名
         * @param  string $on               连接条件,表名需要使用别名
         * @return 当前对象的实例
         */
        public function outerJoin($tableWithAlias, $on){

            return $this->join($tableWithAlias, $on, JoinType::OUTER);
        }

        /**
         * 交叉连接查询
         * @param  string $tableWithAlias   连接的目标表，不带前缀，必须使用别名
         * @param  string $on               连接条件,表名需要使用别名
         * @return 当前对象的实例
         */
        public function crossJoin($tableWithAlias, $on){

            return $this->join($tableWithAlias, $on, JoinType::CROSS);
        }

        /**
         * 说明:写入一条或者多条记录
         * 参数:$data = ['name' => '张三', 'age' => 25] 或者 $data = [['name' => '张三', 'age' => 25], ['name' => '李四', 'age' => 15]]
         * 返回:插入一条记录时，返回记录id，多条记录时，返回插入的记录条数
         */
		public function insert($data = []){

            if($data && !$this->_execFlag){

                $firstData     = current($data);
                
                //是否插入多行记录
                $isMulti       = is_array($firstData);
                
                $this->_fields = !$isMulti ? array_keys($data) : array_keys($firstData);
                
                $this->_bind   = $isMulti ? $data : [$data];
                
                $sql           = $this->getSql($isMulti ? self::MULTI_INSERT : self::INSERT);

                if(!$this->execute($sql)){

                    return 0;
                }
            }

            return !$isMulti ? $this->lastInsertId() : $this->affectedRows();
		}

        /**
         * 说明:更新记录
         * 参数:$data = ['name' => '张三', '`age`' => 'age + 1'], $where = ['a = ? AND b =  ?', [ "1", "man"]]，值中含有自增或者函数时，请用`包围key中的字段名，如前面的'`age`' => '`age` + 1'
         * 返回:更新影响到的记录条数
         */
        public function update($data = []){

            if($data && !$this->_execFlag){

                foreach ($data as $k => $v) {
                    //可能是字段自运算，如a=a+1,或者函数运算，如a=ltrm(a),此时需要把key中的字段用`包围起来
                    if(substr($k, 0, 1) == '`' && substr($k, -1, 1) == '`'){

                        $this->_fields[] = $k . ' = ' . $v;
                        
                        continue;
                    }
                    $this->_fields[] = $k . ' = ?';
                    
                    $this->_bind[]   = $v;
                }

                $sql = $this->getSql(self::UPDATE);

                if(!$this->execute($sql)){

                    return  0;
                }
            }

            return $this->affectedRows();
        }

        /**
         * 说明:删除记录
         * 参数:无
         */
        public function delete(){

            if(!$this->_execFlag){

                $sql = $this->getSql(self::DELETE);

                if(!$this->execute($sql)){

                    return 0;
                }
            }

            return $this->affectedRows();
        }

        //获取多行记录
        public function getRows($page = null, $pagesize = null){

            if(isset($page)){

                $this->page($page);
            }

            if(isset($pagesize)){

                $this->pagesize($pagesize);
            }

            if(!$this->_execFlag){
            
                $sql = $this->getSql(self::SELECT);

                if(!$this->execute($sql)){

                    return [];
                }
            }
            
            $result = $this->_stmt->fetchAll($this->_fetchMode);
            
            return $result ? $result : [];
        }

        //获取单行记录
        public function getRow(){

            if(!$this->_execFlag){

                $this->_page     = 1;
            
                $this->_pagesize = 1;
            
                $sql = $this->getSql(self::SELECT);

                if(!$this->execute($sql)){

                    return [];
                }
            }
            
            $result = $this->_stmt->fetch($this->_fetchMode);
            
            return $result ? $result : [];
        }

        //获取单列的值
        public function getValue(){

            if(!$this->_execFlag){

                $this->_page     = 1;
                
                $this->_pagesize = 1;
                
                $sql             = $this->getSql(self::SELECT);

                if(!$this->execute($sql)){

                    return '';
                }
            }

            return $this->_stmt->fetchColumn(0);
        }

        //统计总数
        public function getCount(){

            return $this->fields(['COUNT(1) AS num'])->getValue();
        }

        //最后插入的id
        public function lastInsertId(){

            return $this->_conn[$this->_connectionName]->lastInsertId();
        }

        //受影响的行数
        public function affectedRows(){

            return $this->_stmt->rowCount();
        }

        //设置排序
        public function orderBy($orderBy = []){

            $this->_order = $orderBy;

            return $this;
        }

        //分组
        public function groupBy($groupBy = []){

            $this->_group = $groupBy;

            return $this;
        }

        //设置having条件
        public function having($having){

            $this->_having = $having;

            return $this;
        }

        //设置查询字段
        public function fields($fields = []){

            $this->_fields = $fields;

            return $this;
        }

        //设置查询的偏移量
        public function page($page){

            $this->_page = intval($page);

            return $this;
        }

        //设置需要查询的记录数量
        public function pagesize($pagesize){

            $this->_pagesize = intval($pagesize);

            return $this;
        }

        //直接执行sql语句
        public function execute($sql, $bind = []){

            $this->_stmt = $this->_conn[$this->_connectionName]->prepare($sql);

            $this->_bind = array_merge($this->_bind, $bind);

            if($this->_bind){

                for ($i = 0, $total = count($this->_bind); $i < $total; ++$i) {

                    switch (true) {

                        case is_bool($this->_bind[$i]):

                            $type = PDO::PARAM_BOOL;

                            break;

                        case is_int($this->_bind[$i]):
                        
                            $type = PDO::PARAM_INT;
                        
                            break;

                        case is_null($this->_bind[$i]):
                        
                            $type = PDO::PARAM_NULL;
                        
                            break;

                        default:
                        
                            $type = PDO::PARAM_STR;
                        
                            break;
                    }

                    $this->_stmt->bindParam($i + 1, $this->_bind[$i], $type);
                }
            }

            try{

                $starttime       = microtime(true);
                
                $execResult      = $this->_stmt->execute();
                
                $this->_execFlag = true;

                if(!$execResult){

                    $this->_errorInfo = $this->_stmt->errorInfo();

                    throw new Exception($this->_errorInfo[2], 402);
                }

                $endtime     = microtime(true);
                //根据配置是否调试mysql
                if($this->_debug){

                    $this->_sqls[] = ['sql' => $sql, 'params' => $this->_bind, 'time' => round(1000 * ($endtime - $starttime),2)];
                }
                return true;
            }catch(Exception $e){
                throw $e;
            }finally{
                //执行完sql之后，清空数据，以备下次查询使用
                $this->reset();
            }
        }

        /**
         * 获取全部行，效率较高，内存占用较少，可以在循环中使用
         */
        public function getAll($page = 0, $pagesize = 0){

            if(isset($page)){

                $this->page($page);
            }

            if(isset($pagesize)){

                $this->pagesize($pagesize);
            }

            if(!$this->_execFlag){

                $sql = $this->getSql(self::SELECT);

                if(!$this->execute($sql)){

                    yield [];
                }
            }

            while($row = $this->_stmt->fetch($this->_fetchMode)){

                yield $row;
            }
        }

        protected function getSql($sqlMode = self::SELECT){

            switch ($sqlMode) {
                
                case self::SELECT:
                    
                    return $this->getSelectSQL();

                case self::MULTI_INSERT:
                case self::INSERT:

                    return $this->getInsertSQL();

                case self::DELETE:

                    return $this->getDeleteSQL();

                case self::UPDATE:

                    return $this->getUpdateSQL();
                
                default:
                    
                    throw new \Exception("错误的操作，找不到指定的SQL操作:" . $sqlMode);
            }
        }

        /**
         * 获取查询SQL语句
         * @return string
         */
        protected function getSelectSQL(){

            $where  = $this->getWhere();
            
            $order  = $this->getOrder();
            
            $group  = $this->getGroup();
            
            $having = $this->getHaving();
            
            $limit  = $this->getLimit();
            
            $fields = $this->_fields ? implode(',', $this->_fields) : '*';
            
            $join   = !$this->_join ? '' : implode(' ', $this->_join) . '';

            return 'SELECT ' . $fields . ' FROM ' . $this->_table . $join . $where . $order . $group . $limit;
        }

        /**
         * 获取插入SQL语句
         * @return string
         */
        protected function getInsertSQL(){
            
            $bind        = [];
            
            $placeHolder = [];

            foreach ($this->_bind as $k => $v) {

                $placeHolder[$k]     = '';
                
                foreach ($v as $sk => $sv) {
                    
                    $bind[]          = $sv;
                    
                    $placeHolder[$k] .= '?,';
                }

                $placeHolder[$k] = '(' . substr($placeHolder[$k], 0, -1) . ')';
            }

            $this->_bind = $bind;
            
            return "INSERT INTO {$this->_table} (" . implode(',', $this->_fields) . ") values" . implode(',', $placeHolder);
        }

        /**
         * 获取删除SQL语句
         * @return string
         */
        protected function getDeleteSQL(){

            $where  = $this->getWhere();

            return 'DELETE FROM ' . $this->_table . $where;
        }

        /**
         * 获取更新SQL语句
         * @return string
         */
        protected function getUpdateSQL(){

            $where  = $this->getWhere();

            return 'UPDATE ' . $this->_table . ' SET ' . implode(',', $this->_fields) . $where;
        }

        /**
         * 排序查询
         * @return string
         */
        protected function getOrder(){

            if($this->_order){

                $order = ' ORDER BY ' . implode(',', $this->_order);

                return $order;
            }

            return '';
        }

        /**
         * 排序查询
         * @return string
         */
        protected function getGroup(){

            if($this->_group){

                $group = ' GROUP BY ' . implode(',', $this->_group);

                return $group;
            }

            return '';
        }

        /**
         * 排序查询
         * @return string
         */
        protected function getHaving(){

            return $this->_having ? " HAVING " . $this->_having : '';
        }

        /**
         * 获取分页字符串
         * @return string
         */
        protected function getLimit(){

            if($this->_page && $this->_pagesize){

                $offset = ($this->_page - 1) * $this->_pagesize;

                return ' LIMIT ' . $offset . ',' . $this->_pagesize
                ;
            }

            return '';
        }

        /**
         * 获取where条件
         * @return string
         */
        protected function getWhere(){
            
            $where          = '';

            $multiCondition = count($this->_where) > 1;

            foreach ($this->_where as $k => $v) {
                
                $where .= ' ' . ($multiCondition ? $v['symbol'] : '') . ' (' . $v['string'] . ')';
                
                $this->_bind = array_merge($this->_bind, $v['bind']);
            }

            return $where ? ' WHERE ' . $where : "";
        }

        /**
         * and条件(andWhere简写)
         * @param  string $where where条件字符串
         * @param  array  $bind  where条件绑定参数
         * @return Mysql
         */
        public function where($where, $bind = []){

            return $this->andWhere($where, $bind);
        }

        /**
         * or条件
         * @param  string $where where条件字符串
         * @param  array  $bind  where条件绑定参数
         * @return Mysql
         */
        public function orWhere($where, $bind = []){

            $this->_where[]  = ['symbol' => 'OR', 'string' => $where, 'bind' => $bind];

            return $this;
        }

        /**
         * and条件
         * @param  string $where where条件字符串
         * @param  array  $bind  where条件绑定参数
         * @return Mysql
         */
        public function andWhere($where, $bind = []){

            $this->_where[]  = ['symbol' => 'AND', 'string' => $where, 'bind' => $bind];

            return $this;
        }

        /**
         * 事务处理
         * @param  function $func 事务中需要执行的业务逻辑处理函数
         * @return mixed 事务函数返回结果返回给客户端
         */
        public function transaction($func){

            try{
                
                $rollbackFlag = $commitFlag = false;
                
                $startFlag    = $this->_conn[$this->_connectionName]->beginTransaction();

                if(!$startFlag){

                    throw new \Exception("事务启动失败", 101);
                }

                $return     = call_user_func($func);

                if($return !== false){

                    $commitFlag = $this->_conn[$this->_connectionName]->commit();

                    return $return;
                }

            }catch(Exception $e){

                throw new Exception($e->getMessage(), 102);

            }finally{
                //启动事务，并且提交失败时，回滚，防止死锁
                if($startFlag && !$commitFlag){

                    $rollbackFlag = $this->_conn[$this->_connectionName]->rollBack();
                }
            }
        }

        /**
         * 重置内部变量，以备下次使用
         * @return null
         */
        protected function reset(){
            $this->_where     = [];
            $this->_order     = [];
            $this->_having    = [];
            $this->_bind      = [];
            $this->_fields    = [];
            $this->_join      = [];
            $this->_group     = [];
            $this->_page      = 0;
            $this->_pagesize  = 0;
            $this->_execFlag  = false;
            $this->_forUpdate = false;
        }
	}
