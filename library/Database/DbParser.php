<?php
	namespace Library\Database;

	trait DbParser{

		protected function onParser($on){

			if(!$on) return "";

			$prefix = $this->_tablePrefix[$this->connectionName];

			return preg_replace('/([^\s\.])\./', $prefix . '$1.', $on);
		}
	}