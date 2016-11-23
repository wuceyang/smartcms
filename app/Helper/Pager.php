<?php
	
	namespace App\Helper;
	/**
	 *分页类,使用实例
	 *$param = array(
					'urlFormat' => '/index.php?act=h&m=do&page={page}',
					'curPage' => 100,
					'totalPage' => 100,
					'wrapTag' => 'li',
					'wrapTagClass' => 'pageItem'
					);
	  $pager = new pager($param);
	  echo '<ul>' . $pager -> getPageLinks() . '</ul>';
	 * @author:随风
	 * @email:wcfh07@163.com
	 * @qq:20297988
	 * @modified:2013-09-01
	 */
	class Pager{

		protected $_baseUrl 		= ''; //除开分页参数之外的链接地址"
		protected $_showNumber 	= 3; //当前页前后显示的页码数量
		protected $_curpageClass 	= 'on'; //当前页样式
		protected $_wrapTag 	= ''; //页码a标签外层标签
		protected $_wrapTagClass  = ''; //外层标签样式
		protected $_curPage 		= '1'; //当前页码
		protected $_totalPage 	= '1'; //总页码
		protected $_pageParam 	= 'page'; //分页参数名
		protected $_nextText 	= '下一页'; //下一页文字
		protected $_prevText 	= '上一页'; //上一页文字
		
		
		public function __construct (array $config = array()){
			foreach( $config as $k => $v ){
				$key = '_' . $k;
				if(isset($this -> $key)){
					$this -> $key = $v;
				}
			}
			$this -> _baseUrl = substr($this -> _baseUrl,-1) == '?' ? substr($this -> _baseUrl,0,-1):$this -> _baseUrl;
			$this -> _baseUrl .= (strstr($this -> _baseUrl,'?') ? '&' : '?') . urlencode($this -> _pageParam) . '=';
		}

		/**
		 *设置当前页码
		 */
		public function setCurrentPage ($page){
			$this -> _curPage = intval($page);
		}
		
		/**
		 *设置总页码
		 */
		public function setTotalPage ($totalPage){
			$this -> _totalPage = intval($totalPage);
		}

		/**
		 *设置当前页码两边显示的页码数量
		 */
		public function setShowNumber ($number){
			$this -> _showNumber = intval($number);
		}

		/**
		 *设置a标签外面的标签名称
		 */
		public function setWrapTag ($tag){
			$this -> _wrapTag = $tag;
		}

		/**
		 *设置a标签外层标签的样式
		 */
		public function setWrapTagClass ($class){
			$this -> _wrapTagClass = $class;
		}

		/**
		 *设置当前页码的样式名称
		 */
		public function setCurrentPageClass ($class){
			$this -> _curpageClass = $class;
		}

		/**
		 *获取页码链接
		 */
		public function getPageLinks (){
			if($this -> _totalPage <= 1) return '';
			$aClass = $this -> _wrapTag?'':' class="' . $this -> _curpageClass . '"';
			$dotPage = '...';
			$pageArr = array();
			$pageArr[] = $this -> getPrevLink();
			$pageArr[] = $this -> _curPage == 1?'<a' . $aClass . '>1</a>':'<a href="' . $this -> _baseUrl . '1">1</a>';
			$start = 2;
			if($this -> _curPage - $this -> _showNumber > 2) {
				$pageArr[] = $dotPage;
				$start = $this -> _curPage - $this -> _showNumber;
			}
			for($i = $start; $i < $this -> _curPage; $i++){
				$pageArr[] = '<a href="' . $this -> _baseUrl . $i . '">' . $i . '</a>';
			}
			if($this -> _curPage > 1) $pageArr[] = '<a' . $aClass . '>' . $this -> _curPage . '</a>';
			$end = $this -> _curPage + $this -> _showNumber;
			if($this -> _totalPage <= $this -> _curPage + $this -> _showNumber + 1){
				$end = $this -> _totalPage;
			}
			for($i = $this -> _curPage + 1; $i <= $end; $i++){
				$pageArr[] = '<a href="' . $this -> _baseUrl . $i . '">' . $i . '</a>';
			}
			if($end < $this -> _totalPage){
				$pageArr[] = $dotPage;
				$pageArr[] = '<a href="' . $this -> _baseUrl . $this -> _totalPage . '">' . $this -> _totalPage . '</a>';
			}
			$pageArr[] = $this -> getNextLink();
			if($this -> _wrapTag) $pageArr = array_map(array($this,'pageWrap'),$pageArr);
			return implode('',$pageArr);
		}

		/**
		 *添加a标签的外层标签
		 */
		protected function pageWrap ($pageItem){
			$class = intval(strip_tags($pageItem)) == $this -> _curPage?$this -> _curpageClass:'';
			return '<' . $this -> _wrapTag . ($this -> _wrapTagClass && $class?' class="' . $class . '"':'') . '>' . $pageItem . '</' . $this -> _wrapTag . '>';
		}

		/**
		 *获取上一页的链接
		 */
		protected function getPrevLink(){
				return $this -> _curPage > 1?'<a href="' . $this -> _baseUrl . ($this -> _curPage - 1) . '">' . $this -> _prevText . '</a>':'<a href="javascript:;">' . $this -> _prevText . '</a>';
		}

		/**
		 *获取下一页的链接
		 */
		protected function getNextLink(){
			return $this -> _curPage < $this -> _totalPage?'<a href="' . $this -> _baseUrl . ($this -> _curPage + 1) . '">' . $this -> _nextText . '</a>': '<a href="javascript:;">' . $this -> _nextText . '</a>';
		}
	}
	
?>
