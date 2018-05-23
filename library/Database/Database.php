<?php

	namespace Library\Database;

	use \Exception;
    use \Config;
    use \PDO;

	abstract class Database{

		protected $_connectionName   = '';
        protected $_errorInfo        = [];
        protected $_sqls             = [];
        protected static $_instance  = null;
        protected $_forUpdate        = false;
        protected $_fetchMode        = \PDO::FETCH_ASSOC;
        protected $_debug            = true;
        protected $_dbConfig         = [];
        protected $_tablePrefix      = [];

        const FETCH_ARRAY  = PDO::FETCH_ASSOC;
        const FETCH_OBJECT = PDO::FETCH_OBJ;
        const FETCH_BOTH   = PDO::FETCH_BOTH;

        abstract public function insert($data = []);

		abstract public function update($data);

		abstract public function delete();

		abstract public function getRow();

		abstract public function getRows($page, $pagesize);

        abstract public function getValue();

        abstract public function getCount();

		abstract public function orderBy($orderBy = []);

        abstract public function groupBy($groupBy = []);

        abstract public function having($having);

        abstract public function fields($fields = []);

        abstract public function join($table, $joinCond, $type);

        abstract public function where($where, $bind = []);

        abstract public function andWhere($where, $bind = []);

        abstract public function orWhere($where, $bind = []);

        abstract public function page($page);

		abstract public function pagesize($pagesize);

		abstract public function execute($sql);

        abstract public function lastInsertId();

        abstract public function affectedRows();

        abstract public function transaction($func);

        use Library\Database\DbParser;

        /**
         * 设置调试模式
         * @param bool $isDebug 是否调试模式，true:是,false:否
         * @return  null
         */
        public function setDebug($isDebug = true){

            $this->_debug = $isDebug;
        }

        /**
         * 设置数据取出方式
         * @param int $fetchMode 数据取出方式，按照PDO预置的数据取出方式
         * @return null
         */
        public function setFetchMode($fetchMode = PDO::FETCH_ASSOC){

            $this->_fetchMode = $fetchMode;
        }

        /**
         * 接数据库
         * @param  string $connectionName 连接配置名称
         * @return 当前类的实例
         */
        public function connect($connectionName){

            if(!$connectionName){

                throw new Exception("连接配置名称不能为空", 401);
            }

            $this->_connectionName = $connectionName;

            //如果已经连接过当前连接，则直接返回
            if(isset($this->_conn[$connectionName])){

                return $this;
            }

            $this->_dbConfig = Config::get('database');

            $driver          = strtolower($this->_dbConfig['driver'] ?: '');

            if(!$driver){

                throw new Exception("找不到指定的数据库驱动配置[driver]项", 404);
            }

            $allConns = $this->_dbConfig[$driver] ?: [];

            if(!$allConns){

                throw new Exception("找不到指定的数据连接配置[{$driver}]", 404);
            }

            $connParam = $this->_dbConfig[$driver][$connectionName];

            if(!$connParam || !is_array($connParam)){

                throw new Exception("找不到指定的数据库连接参数[{$connectionName}]", 101);
            }

            try{

                extract($connParam);
                //设置表前缀
                $this->_tablePrefix[$connectionName] = $prefix;

                $dsn     = "mysql:host={$host}; port={$port}; dbname={$dbname}; charset={$charset}";

                $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT];

                if(Config::get('database.persistent')){

                    $options[PDO::ATTR_PERSISTENT] = true;
                }
                
                //设置字符集,为了兼容utf-8，增加了一步字符串替换
                $charset                               = str_replace(['-','_'], '', $charset ?: 'UTF8');
                
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES '{$charset}'";
                
                $this->_conn[$connectionName]          = new PDO($dsn, $username, $password, $options);

                $this->_conn[$connectionName]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            }catch(Exception $e){

                throw new Exception("数据库连接失败:" . $e->getMessage(), $e->getCode());
            }

            return $this;
        }

        /**
         * 设置查询编码
         * @param string $charset 需要设置当前会话使用的编码方式
         * @return null
         */
        public function setCharset($charset){

            $this->execute("SET NAMES '{$charset}'");
        }

        /**
         * 切换数据表,参数为空时则是获取当前表名
         * @param  string $tableName 需要设置的表名
         * @return 当前类的实例对象
         */
        public function table($table = null){

            if(null === $table){

                return $this->_table;
            }

            $this->_table = $this->_tablePrefix[$this->connectionName] . $table;

            return $this->_table;
        }

        /**
         * 设置主表别名
         * @param  string $alias 主表别名
         * @return 当前对象实例
         */
        public function alias($alias){
            //避免重复使用别名时重复拼接别名
            $this->_table = preg_replace('/\s.*+/', '', $this->_table) . ' AS ' . $alias;

            return $this;
        }

        public static function getInstance(){

            if(self::$_instance === null){

                self::$_instance = new static();

            }

            return self::$_instance;
        }

		//获取错误信息
		public function getError(){

			return $this->_errorInfo;
		}

        /**
         * 获取当前的连接名称
         * @return string 当前连接配置名称，如:default
         */
		public function getCurrentConnection(){

			return $this->_connectionName;

		}

        /**
         * 获取全部执行过的SQL语句列表
         * @return array 执行的SQL语句列表
         */
        public function getSqls(){

            return $this->_sqls;
        }

        /**
         * 获取最后一次执行的SQL语句
         * @return array 最后一次执行的SQL语句，包含参数
         */
        public function getLastSql(){

            return $this->_sqls ? $this->_sqls[0] : [];
        }
	}
