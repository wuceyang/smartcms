<?php

	namespace Library\Database;

	use \Exception;
    use \PDO;

	abstract class Database{

		protected $_connectionName   = '';
        protected $_errorInfo        = [];
        protected $_sqls             = [];
        protected static $_instance  = null;
        protected $_forUpdate        = false;

        const FETCH_ARRAY  = PDO::FETCH_ASSOC;
        const FETCH_OBJECT = PDO::FETCH_OBJ;
        const FETCH_BOTH   = PDO::FETCH_BOTH;

        abstract public function table($tableName);

        abstract public function insert($data = []);

		abstract public function update($data, $where = '', $bind = []);

		abstract public function delete($where = '', $bind = []);

		abstract public function getRow($where = '', $bind = []);

		abstract public function getRows($where = '', $bind = []);

        abstract public function getValue($where = '', $bind = []);

        abstract public function getCount($where = '', $bind = []);

		abstract public function orderBy($orderBy = []);

        abstract public function groupBy($groupBy = []);

        abstract public function having($having);

        abstract public function fields($fields = []);

        abstract public function where($where, $bind = []);

        abstract public function andWhere($where, $bind = []);

        abstract public function orWhere($where, $bind = []);

        abstract public function page($page);

		abstract public function pagesize($pagesize);

		abstract public function execute($sql);

		abstract public function connect($connectionName);

        abstract public function lastInsertId();

        abstract public function affectedRows();

        abstract public function setDebug($isDebug);

        abstract public function transaction($func);

        //标记select ... for update
        public function forUpdate(){

            $this->_forUpdate = true;

        }

        public static function getInstance(){

            if(self::$_instance === null){

                self::$_instance = new static();

            }

            return self::$_instance;

        }

        //设置魔术方法，可以静态调用
		public static function __callStatic($method, $arguments){

            $instance = self::getInstance();

			if(method_exists($instance, $method)){

				return call_user_func_array([$instance,$method], $arguments);

			}

			throw new Exception("找不到指定的方法", 101);

		}

		//获取错误信息
		public function getError(){
			return $this->_errorInfo;

		}

		public function getCurrentConnection(){

			return $this->_connectionName;

		}

        public function getSqls(){

            return $this->_sqls;

        }
	}
