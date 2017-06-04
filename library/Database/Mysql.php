<?php

	namespace Library\Database;

	use \Config;
    use \Exception;
    use \PDO;

	class Mysql extends \Library\Database\Database{

        protected $_fields           = [];
		protected $_bind             = [];
        protected $_where            = [];
		protected $_table            = '';
        protected $_conn             = [];
        protected $_selectedFields   = [];
        protected $_having           = [];
        protected $_order            = [];
        protected $_group            = [];
        protected $_limit            = 0;
        protected $_subBuilder       = '';
        protected $_offset           = 0;
        protected $_stmt 		     = null;
        protected $_debug            = false;
        protected $_forUpdate 		 = false;
        protected $_fetchMode        = \PDO::FETCH_ASSOC;

        const EXECUTE       = 0;
        const SELECT        = 1;
        const UPDATE        = 2;
        const DELETE        = 3;
        const INSERT        = 4;
        const MULTI_INSERT  = 5;

        //设置调试模式
        public function setDebug(bool $isDebug = false){

            $this->_debug = $isDebug;

        }

        //设置数据取出的格式
        public function setFetchMode($fetchMode = \PDO::FETCH_ASSOC){

            $this->_fetchMode = $fetchMode;

        }

        /**
         *连接数据库
         */
		public function connect(array $connectionName){


            $this->_connectionName = $connectionName;

            //如果已经连接过当前连接，则直接返回
            if(isset($this->_conn[$this->_connectionName])){

                return $this;

            }

            $driver   = Config::get('database.driver');

			$dbConfig = Config::get('database.' . $driver . '.' . $this->_connectionName);

			if(!$dbConfig || !is_array($dbConfig)){

				throw new Exception("找不到指定的数据库连接设置:" . $this->_connectionName, 101);

			}
			try{

                extract($dbConfig);

				$dsn = "mysql:host={$host}; port={$port}; dbname={$dbname}; charset={$charset}";

                $options = [];

                if(Config::get('database.persistent')){

                    $options[] = [PDO::ATTR_PERSISTENT => true];

                }

				$this->_conn[$this->_connectionName] = new PDO($dsn, $username, $password, $options);

                //设置字符集,为了兼容utf-8，增加了一步字符串替换

                $charset = str_replace(['-','_'], '', $charset);

                $this->execute("SET NAMES $charset");

			}catch(Exception $e){

				throw new Exception("数据库连接失败:" . $e->getMessage(), $e->getCode());

			}

            return $this;
		}

        /**
         * 说明:切换数据表
         */
		public function table(string $tableName){

			$this->_table = $tableName;

            return $this;
		}

        /**
         * 说明:写入一条或者多条记录
         * 参数:$data = ['name' => '张三', 'age' => 25] 或者 $data = [['name' => '张三', 'age' => 25], ['name' => '李四', 'age' => 15]]
         * 返回:插入一条记录时，返回记录id，多条记录时，返回插入的记录条数
         */
		public function insert(array $data = []){

            $firstData  = current($data);

            //是否插入多行记录
            $isMulti    = is_array($firstData);

            $this->_fields = !$isMulti ? array_keys($data) : array_keys($firstData);

			if($isMulti){
				foreach ($data as $k => $v) {
					
					foreach ($v as $sk => $sv) {
					
						$this->_bind[] = $sv;
					}
				}
			}else{
				
				$this->_bind = array_values($data);
			}

            if(!$this->_internalExec($isMulti ? self::MULTI_INSERT : self::INSERT)){

                return 0;
            }

            return !$isMulti ? $this->lastInsertId() : $this->affectedRows();
		}

        /**
         * 说明:更新记录
         * 参数:$data = ['name' => '张三', '`age`' => '`age` + 1'], 值中含有自增或者函数时，请用`包围字段名，如前面的`age`
         * 返回:更新影响到的记录条数
         */
        public function update(array $data){
            
            foreach ($data as $k => $v) {
                //可能是字段自运算或者函数运算，如a=ltrm(a),此时需要把key中的字段用`包围起来,比如`a` => `a`+1
                if(substr($k, 0, 1) == '`' && substr($k, -1, 1) == '`'){
                    
                    $this->_fields[] = $k . ' = ' . $v;
                    
                    continue;
                }
                
                $this->_fields[] = $k;
                
                $this->_bind[]   = $v;
            }

            if(!$this->_internalExec(self::UPDATE)){

                return  0;
            }

            return $this->affectedRows();
        }

        /**
         * 说明:删除记录
         * 参数:$where = ['a = ? AND b =  ?', [ "1", "man"]]
         */
        public function delete(){

            if(!$this->_internalExec(self::DELETE)){

                return 0;
            }

            return $this->affectedRows();
        }

        //获取多行记录
        public function getRows(string $where = '', array $bind = [], bool $forUpdate = false){
	        
            if($where || $bind){
            
                $this->where($where, $bind);
            }
            
            $this->_forUpdate = $forUpdate;
            
            $this->_internalExec(self::SELECT);
            
            $result = $this->_stmt->fetchAll($this->_fetchMode);
            
            return $result ? $result : [];
        }

        //获取单行记录
        public function getRow(string $where = '', array $bind = [], bool $forUpdate = false){
	        
            if($where || $bind){
            
                $this->where($where, $bind);
            }
            
            $this->_forUpdate 	= $forUpdate;
            
            $this->_limit 		= 1;
            
            $this->_internalExec(self::SELECT);
            
            $result = $this->_stmt->fetch($this->_fetchMode);
            
            return $result ? $result : [];
        }

        //获取单列的值
        public function getValue($where = '', $bind = []){
	        
            if($where || $bind){
            
                $this->where($where, $bind);
            }
            
            $this->_internalExec(self::SELECT);
            
            return $this->_stmt->fetchColumn(0);
        }

        //统计总数
        public function getCount($where = '', $bind = []){

            return $this->fields(['COUNT(1) AS num'])->getValue($where, $bind);
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
        public function orderBy(array $orderBy = []){

            $this->_order = $orderBy;

            return $this;
        }

        //分组
        public function groupBy(array $groupBy = []){

            $this->_group = $groupBy;

            return $this;
        }

        //设置having条件
        public function having($having){

            $this->_having = $having;

            return $this;
        }

        //设置查询字段
        public function fields(array $fields = []){

            $this->_selectedFields = $fields;

            return $this;
        }

        //设置查询的偏移量
        public function page(int $page){
	        
            $this->_page = $page;
            
            return $this;
        }

        //设置需要查询的记录数量
        public function pagesize(int $pagesize){
	        
            $this->_pagesize = $pagesize;
            
            return $this;
        }

        //创建子查询
        public function subQuery($subBuilder){

            $this->_subBuilder = $subBuilder;
            
            return $this;
        }

        //直接执行sql语句
        public function execute($sql, $bind = []){
            
            if($bind){
            
                $this->_bind = $bind;
            }
            
            return $this->_internalExec(self::EXECUTE, $sql);
        }

        //执行sql，并绑定数据
        protected function _internalExec($operator, $sql = ''){

            $sql = $operator === self::EXECUTE ? $sql : $this->toSql($operator);

            $this->_stmt = $this->_conn[$this->_connectionName]->prepare($sql);

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

            $starttime   = microtime(true);

            $execResult  = $this->_stmt->execute();

            if(!$execResult){

            	$this->_errorInfo = $this->_stmt->errorInfo();
            }

            $endtime     = microtime(true);
            //根据配置是否调试mysql
            if($this->_debug){

                $this->_sqls[] = ['sql' => $sql, 'params' => $this->_bind, 'time' => round(1000 * ($endtime - $starttime),2)];

            }
            //执行完sql之后，清空数据，以备下次查询使用
            $this->reset();

            return $execResult;
        }

        //转换为sql语句
        public function toSql($operator = self::SELECT){

            $where = '';

            if($this->_where){

                $where = "";

                foreach ($this->_where as $k => $v) {

                    $where .= ' ' . $v['symbol'] . ' ' . $v['string'];

                    $this->_bind = array_merge($this->_bind, $v['bind']);
                }
                
                $where = $where ? ' WHERE ' . $where : '';

                $this->_where = '';
            }

            $table = Config::get('database.tablePrefix') . $this->_table;

            switch($operator){

                case self::SELECT:

                    $order = $having = $group = '';

                    if($this->_order){

                        $order = ' ORDER BY ' . implode(',', $this->_order);

                        $this->_order = [];
                    }

                    if($this->_group){

                        $group = ' GROUP BY ' . implode(',', $this->_group);

                        $this->_group = [];
                    }
                    
                    if($this->_having){
                    
                        $order = ' HAVING ' . $this->_having;
                    
                        $this->_having = '';
                    }
                    
                    if($this->_subBuilder){
                    
                        $table  = '(' . $this->_subBuilder . ') AS tmp_tbl';
                    }
                   
                    $limit  = $this->_page && $this->_pagesize ? (' LIMIT ' . (($this->_page - 1) * $this->_pagesize) . ',' . $this->_pagesize) : '';
                    
                    $fields = $this->_selectedFields ? implode(',', $this->_selectedFields) : '*';
                    
                    $sql    = "SELECT $fields FROM $table " . $where . $group . $order . $having . $limit . ($this->_forUpdate ? ' FOR UPDATE' : '');

                    break;

                case self::INSERT:

                    $placeHolder = array_fill(0, count($this->_fields), '?');

                    $sql         = "INSERT INTO $table (" . implode(',', $this->_fields) . ") values(" . implode(',', $placeHolder) . ")";

                    break;

                case self::MULTI_INSERT:

                    $itemHolder  = '(' . implode(',', array_fill(0, count($this->_fields), '?')) . ')';

                    $placeHolder = implode(',', array_fill(0, count($this->_bind)/count($this->_fields), $itemHolder));
                    $sql         = "INSERT INTO $table (" . implode(',', $this->_fields) . ") values" . $placeHolder;
                    break;

                case self::DELETE:

                    $sql = "DELETE FROM $table" . $where;

                    break;

                case self::UPDATE:

                    $sets = [];

                    foreach ($this->_fields as $k => $v) {

                        $sets[] = strpos($v, ' = ') === false ? $v . ' = ?' : $v;
                    }

                    $sql = "UPDATE $table SET " . implode(',' ,$sets) . $where;

                    break;
                default:

                    throw new \Exception("错误的操作，找不到指定的SQL操作:" . $operator);
            }

            return $sql;
        }

        public function where(string $where, array $bind = []){
	        
            return $this->andWhere($where, $bind);
        }

        public function orWhere(string $where, array $bind = []){
            
            $this->_where[]  = ['symbol' => 'OR', 'string' => $where, 'bind' => $bind];
            
            return $this;
        }

        public function andWhere(string $where, array $bind = []){
	        
            $this->_where[]  = ['symbol' => 'AND', 'string' => $where, 'bind' => $bind];
            
            return $this;
        }
		
        public function transaction(callable $func){

            try{

                $commitFlag = false;

                $startFlag  = $this->_conn[$this->_connectionName]->beginTransaction();

                if(!$startFlag){

                    throw new \Exception("事务启动失败", 201);
                }
                
                $transRet     = call_user_func($func);

                $commitFlag   = $this->_conn[$this->_connectionName]->commit();
                
                return $transRet;
            }catch(\Exception $e){

                throw new \Exception($e->getMessage(), 202);
            }finally{
                //启动事务，并且提交失败时，回滚，防止死锁
                if($startFlag && !$commitFlag){

                    $rollbackFlag = $this->_conn[$this->_connectionName]->rollBack();
                }
            }
        }

        protected function reset(){
            $this->_where          = [];
            $this->_order          = [];
            $this->_having         = [];
            $this->_bind           = [];
            $this->_fields         = [];
            $this->_page           = 0;
            $this->_pagesize       = 0;
            $this->_forUpdate      = false;
            $this->_selectedFields = [];
        }
	}
