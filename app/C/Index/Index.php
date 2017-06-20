<?php
	namespace App\C\Index;

	class Index extends \App\C\BaseController{

		public function Index(){

			$category = new \App\M\IqiyiCategory();

			$categories = $category->page(1)->pagesize(10)->getRows();

			return $this->response->success($categories);
		}
	}