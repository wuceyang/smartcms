<?php
    namespace Library\Template;

    use \Exception;
	/**
	 *模板解析类,默认设置使用举例
	 *	<loop $arr $v/>
	 *		<li><=$v.id/></li>
	 *		<li><=$v.path/></li>
	 *	</loop/>
	 *	<include inc.php/>
	 *	<if $a > 2/>
	 *		<="greate"/>
	 *	<else/>
	 *		<="not greate"/>
	 *	</if/>
	 * @author:随风
	 * @email:wcfh07@163.com
	 * @qq:20297988
	 * @modified:2013-09-01
	 */
	class SmartTpl
	{
		protected $start_tag    = '<';
		protected $end_tag      = '/>';
		protected $template_dir = '';
		protected $compiled_dir = './compiled/';
		protected $extend  		= '';

		public function __construct($config = []){
			foreach( $config as $k => $v ){
				if(!isset($this->$k) || $k == 'start_tag' || $k == 'end_tag') continue;
				$this->$k = $v;
			}
			$this->setTags($this -> start_tag,$this -> end_tag);
			if(substr($this->compiled_dir,-1,1) != '/') $this->compiled_dir .= '/';
			if(substr($this->template_dir,-1,1) != '/') $this->template_dir .= '/';
			if(!is_dir($this->compiled_dir)) mkdir($this->compiled_dir,0777,true);
		}

		/**
		 *设置标签开始和结束标记
		 */
		public function setTags($startTag, $endTag){
			$this->start_tag = str_replace(['/'], ['\/'],$startTag);
			$this->end_tag   = str_replace(['/'], ['\/'],$endTag);
		}

		/**
		 *解析输出标签<=$a.b>，允许使用[]，如$a[$b.c].d
		 */
		public function parseOutput($html){
			$output_regxp = '/' . $this -> start_tag . '=(.+?)' . $this -> end_tag . '/';
			preg_match_all($output_regxp, $html, $matches);
			if(is_array($matches[1])){
				foreach( $matches[1] as $k => $v ){
					$matches[1][$k] = '<?php echo ' . $this -> dot2Array($v) . ';?>';
				}
				$html = str_replace($matches[0], $matches[1], $html);
			}
			return $html;
		}

		/**
		 *解析if标签<if $a > 0/>
		 */
		public function parseIf($html){
			$if_regxp = '/' . $this->start_tag . 'if\s+(.+?)' . $this->end_tag . '/i';
			preg_match_all($if_regxp, $html, $matches);
			if(is_array($matches[1])){
				foreach( $matches[1] as $k => $v ){
					$matches[1][$k] = '<?php if(' . $this->dot2Array($v) . '){ ?>';
				}
				$html = str_replace($matches[0], $matches[1], $html);
			}
			$elseif_regxp = '/' . $this->start_tag . 'elseif\s+(.+?)' . $this->end_tag . '/i';
			preg_match_all($elseif_regxp,$html,$matches);
			if(is_array($matches[1])){
				foreach( $matches[1] as $k => $v ){
					$matches[1][$k] = '<?php }elseif(' . $this->dot2Array($v) . '){ ?>';
				}
				$html = str_replace($matches[0], $matches[1], $html);
			}
			$html = preg_replace('/' . $this->start_tag . 'else?' . $this->end_tag . '/i','<?php }else{ ?>', $html);
			$html = preg_replace('/' . $this->start_tag . '\/if' . $this->end_tag . '/i','<?php } ?>', $html);
			return $html;
		}

		/**
		 *转换其他php逻辑代码<php $a = 5; />标签
		 */
		public function parsePhp($html){
			$html       = preg_replace('/\?>\s*?<\?php/i', '', $html);
			$php_regxp  = '/' . $this->start_tag . 'php\s+([^\s].+?)\s*' . $this->end_tag . '/is';
			preg_match_all($php_regxp, $html, $matches);
			if(is_array($matches[1])){
				foreach( $matches[1] as $k => $v ){
					$matches[1][$k] = '<?php ' . $this->dot2Array($v) . '?>';
				}
				$html = str_replace($matches[0], $matches[1], $html);
			}
			return $html;
		}

		/**
		 *解析文件包含标签<include file="admin/index.html">,不建议使用，不会自动检测被包含模板文件是否有更新
		 */
		public function parseInclude($html){
			$include_regxp = '/' . $this->start_tag . 'include\s+file=(?<quote>[\'"])([^\s]+?)\k<quote>\s*' . $this->end_tag . '/i';
			preg_match_all($include_regxp, $html, $matches);
			if(is_array($matches[2])){
                $includes = [];
				foreach( $matches[2] as $k => $v ){
					$subtpl = $this->template_dir . $v;
					if(!file_exists($subtpl)) {
						throw new Exception("找不到指定的模板文件" . $v);
					}
                    $subhtml    = $this->parseTplTags($subtpl);
                    $pathinfo   = pathinfo($v);
                    $cacheFile  = $this->compiled_dir . str_replace([$this->template_dir, '.'], '', $pathinfo['dirname']) . '/' . $pathinfo['filename'] . '.php';
                    $this->save2Php($cacheFile, $subhtml);
                    $includes[] = '<?php include $__tplbasedir__ . "' . str_replace([$this->template_dir, $this->compiled_dir], '', $cacheFile) . '"; ?>';
				}
                $html = str_replace($matches[0], $includes, $html);
			}
			return $html;
		}

		/**
		 *解析循环标签<loop $arr $k $v>
		 */
		public function parseLoop($html){
			$loop_regxp = '/' . $this->start_tag . 'loop\s+?([^\s]+?)\s+?([^\s]+?)(\s+[^\s]+)?\s*' . $this->end_tag . '/i';
			preg_match_all($loop_regxp, $html, $matches);
			if(isset($matches[1]) && is_array($matches[1])){
                $includes = [];
				foreach( $matches[1] as $k => $v ){
					$v   = $this -> dot2Array($v);
					$key = $matches[2][$k];
					$val = $matches[3][$k];
					$php = '<?php if(isset(' . $v . ') && is_array(' . $v . ')){ ?>' . ($val?'<?php foreach(' . $v . ' as ' . $key . ' => ' . $val . '){ ?>':'<?php foreach(' . $v . ' as ' . $key . '){ ?>');
					$html = str_replace($matches[0][$k], $php, $html);
				}
				$html = preg_replace('/' . $this->start_tag . '\/loop' . $this->end_tag . '/i', '<?php }} ?>', $html);
			}
			return $html;
		}

		/**
		 *解析区块定义标签<define name="content"/>
		 */
		public function parseDefine($html){
			$define_regxp = '/' . $this->start_tag . 'define\s+?name=(?<quote>[\'"])([^\s]+?)\k<quote>\s*' . $this->end_tag . '/i';
			preg_match_all($define_regxp, $html, $matches);
			if(isset($matches[2]) && is_array($matches[2])){
				$defines = [];
				foreach ($matches[2] as $k => $v) {
					$defines[] 	= '<?php function __' . $v . '__($params){ extract($params);?>';
				}
				$defines[] 		= '<?php } ?>';
				$matches[0][] 	= str_replace('\\', '', $this->start_tag) . '/define' . str_replace('\\', '', $this->end_tag);
				$html 			= str_replace($matches[0], $defines, $html);
			}
			return $html;
		}

		/**
		 * 解析extend标签<extend from="index.html" />
		 */
		public function parseExtend($extendFile, $phpFile){
			$htmlFile = $this->template_dir . $extendFile;
			if(!file_exists($htmlFile)){
				throw new Exception("模板文件" . $extendFile . '不存在，请检查路径');
			}
			$pathinfo = pathinfo($htmlFile);
			$phpFile  = $this->compiled_dir . $phpFile;
			$parse    = true;
			if(file_exists($phpFile) && filemtime($htmlFile) <= filemtime($phpFile)){
				$parse = false;
			}
			if($parse){
				$html 		= file_get_contents($htmlFile);
				clearstatcache(TRUE,$phpFile);
				$exthtml 	= $this->parseTplTags($html);
				$this->save2Php($phpFile, $exthtml);
			}
		}

		/**
		 * 解析define引用标签block
		 */
		public function parseBlock($html){
			$loop_regxp = '/' . $this->start_tag . 'block\s+?name=(?<quote>[\'"])([^\s]+?)\k<quote>\s*' . $this->end_tag . '/i';
			preg_match_all($loop_regxp, $html, $matches);
			if(isset($matches[2]) && is_array($matches[2])){
				$blocks = [];
				foreach ($matches[2] as $k => $v) {
					$blocks[] 	= '<?php function_exists("__' . $v . '__") && __' . $v . '__($params); ?>';
				}
				$html = str_replace($matches[0], $blocks, $html);
			}
			return $html;
		}

		/**
		 *解析模板，并输出到浏览器
		 */
		public function display ($tplfile, $params = []){
			$tplpath = $this->getAbsTplPath($this->template_dir . $tplfile);
			if ( !file_exists($tplpath) ){
				throw new Exception("模板文件" . $tplfile . '不存在，请检查路径');
			}
            $pathinfo 		= pathinfo($tplfile);
			$compiled_file  = $this->compiled_dir . str_replace([$this->template_dir,'.'], '', $pathinfo['dirname']) . '/' . $pathinfo['filename'] . '.php';
			$parse 			= true;
			$html 			= file_get_contents($tplpath);
			$extend_regxp = '/^.*?' . $this->start_tag . 'extend\s+from=(?<quote>[\'"])(.+?)\k<quote>\s*'. $this->end_tag .'.*$/is';
			$extend_path 	= preg_replace($extend_regxp, '$2', $html);
			if($extend_path != $html){
				$pathinfo = pathinfo($extend_path);
				$phpFile  = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.php';
				//解析extend文件
				$this->parseExtend($extend_path, $phpFile);
				//替换当前html文件中的extend标签为include标签
				$include  = '<?php include $__tplbasedir__ . "' . $phpFile . '";?>';
				$extend_regxp = '/(^.*?)' . $this->start_tag . 'extend\s+from=(?<quote>[\'"]).+?\k<quote>\s*'. $this->end_tag .'(.*)$/is';
				$html 	  = preg_replace($extend_regxp, '$1' . $include . '$3', $html);
			}
			if(file_exists($compiled_file)){
				if(filemtime($tplpath) <= filemtime($compiled_file)){
					$parse = false;
				}
			}
			if($parse){
				clearstatcache(TRUE,$compiled_file);
				$html = $this->parseTplTags($html);
	            $this->save2Php($compiled_file, $html);
	        }
            $params['__tplbasedir__'] = $this->compiled_dir;
			extract($params);
			include $compiled_file;
		}

        /**
		 *解析模板，并返回最终得到的html
		 */
		public function fetch($tplfile, $params = []){
			ob_start();
			$this->display($tplfile, $params);
			$html = ob_get_clean();
			return $html;
		}

		/**
		 *解析模板，允许多层include，但不建议
		 */
		public function parseTplTags($html){
			$html    = $this->parseIf($html);
			$html    = $this->parseInclude($html);
			$html    = $this->parseLoop($html);
			$html    = $this->parseOutput($html);
			$html    = $this->parsePhp($html);
			$html    = $this->parseBlock($html);
			$html    = $this->parseDefine($html);
			return $html;
		}

		/**
		 *解析点号字符串为数组($a.b.c => $a["b"]["c"])
		 */
		protected function dot2Array($str){
			return preg_replace('/(?<!\d)\.(\$?\w+)/', '["$1"]', $str);
		}

		/**
		 *获取绝对路径，去掉绝对路径中的"./"和"../"
		 */
		protected function getAbsTplPath($str){
			return str_replace(array('./','../'), '', $str);
		}

        /**
		 *生成编译后的模板文件
		 */
        protected function save2Php($path, $html){
            $tpldir = dirname($path);
            if(!is_dir($tpldir)){
                mkdir($tpldir, 0777, true);
            }
            if(false === file_put_contents($path, $html)){
                throw new \Exception("生成缓存文件失败", 103);
            }
            return true;
        }
	}
?>
