<?php
	namespace App\Helper;

	use \Exception;

	class Image{

		protected $im;
		protected $strinfo = array('fontfile' => '', 'fontsize' => 12,'x' => 0, 'y' => 0, 'fontcolor' => '#000', 'vertical' => false,'bold' => false,'italic' => false);
		
		public function __construct(){
			
		}

		public function setImage($imagepath){
			if(!file_exists($imagepath)) throw new Exception('找不到指定的图片文件:' . $imagepath);
			$this -> im = imagecreatefromstring(file_get_contents($imagepath));
		}

		public function setFontFile($fontfile){
			if(!file_exists($fontfile)) throw new Exception('找不到指定的字体文件:' . $imagepath);
			$this -> strinfo['fontfile'] = $fontfile;
		}

		public function setFontSize($fontsize){
			$this -> strinfo['fontsize'] = intval($fontsize);
		}

		public function setFontColor($fontcolor){
			$this -> strinfo['fontcolor'] = $fontcolor;
		}

		public function setXY($x,$y){
			$this -> strinfo['x'] = intval($x);
			$this -> strinfo['y'] = intval($y);
		}

		public function setVertical($isVertical = false){
			$this -> strinfo['vertical'] = !!$isVertical;
		}

		public function setBold($isBold = false){
			$this -> strinfo['bold'] = !!$isBold;
		}

		public function setItalic($isItalic = false){
			$this -> strinfo['italic'] = !!$italic;
		}

		//等比缩放图片
		public function resizeImage($width, $height){
			$orignalWidth 	= imagesx($this -> im);
			$orignalHeight 	= imagesy($this -> im);
			$newsize		= $this -> getImageNewSize($width,$height,$orignalWidth,$orignalHeight);
			$im 			= imagecreatetruecolor($newsize['width'], $newsize['height']);
			imagecopyresampled($im,$this -> im,0,0,0,0,$newsize['width'],$newsize['height'],$orignalWidth,$orignalHeight);
			$this -> im = $im;
		}

		//向图片中添加水印图片
		public function waterImage($waterImage){
			if(!file_exists($waterImage)) throw new Exception('找不到指定的水印文件:' . $waterImage);
			$im 			= imagecreatefromstring(file_get_contents($waterImage));
			$orignalWidth 	= imagesx($this -> im);
			$orignalHeight 	= imagesy($this -> im);
			$waterWidth 	= imagesx($im);
			$waterHeight	= imagesy($im);
			imagecopy($this -> im, $im, $this->strinfo['x'],$this->strinfo['y'],0,0,$waterWidth,$waterHeight);
		}

		//向图片中写入文字水印
		public function drawText($text){
			$rgbColor 	= $this -> hex2RGBColor($this -> strinfo['fontcolor']);
			$cnChar 	= mb_substr($text,0,1,'UTF-8');
			$charBox 	= imagettfbbox($this -> strinfo['fontsize'],0,$this -> strinfo['fontfile'],$this -> strinfo['vertical']?$cnChar:$text);
			$cnCharNum 	= mb_strlen($text);
			$width 		= $charBox[4] - $charBox[6] + 10;
			$height		= $charBox[0] - $charBox[7] + 10;
			if($this -> strinfo['vertical']){
				$charHeight 	= $charBox[1] - $charBox[7];
				$height 		= $charHeight * $cnCharNum + 10;
			}
			//如果没有指定图片，则写入透明图片
			if(!$this -> im){
				$im 			= imagecreatetruecolor($width,$height);
				$color			= imagecolorallocate($im, 110, 110, 110);
				imagefill($im,0,0,$color);
				imagecolortransparent($im,$color);
			}else{
				$im = $this -> im;
			}
			$textcolor 		= imagecolorallocate($im, $rgbColor['R'], $rgbColor['G'], $rgbColor['B']);
			$x = isset($this->strinfo['x']) ? intval($this->strinfo['x']) : 0;
			$y = isset($this->strinfo['y']) ? intval($this->strinfo['y']) : 0;
			if($this -> strinfo['vertical']){
				for($i = 0; $i < $cnCharNum; ++$i){
					$cnChar = mb_substr($text,$i,1,'UTF-8');
					imagettftext($im, $this -> strinfo['fontsize'], 0, $x, $y + $i * $charHeight, $textcolor, $this -> strinfo['fontfile'], $cnChar);
				}
			}else{
				imagettftext($im, $this -> strinfo['fontsize'], 0, $x, $y, $textcolor, $this -> strinfo['fontfile'], $text);
			}
			$this -> im = $im;
		}

		/**
		 *生成图像
		 *@param int $savePath 	图片保存全路径
		 *@param int $format 		图片格式，只能是"png/gif/jpg"三种
		 */
		public function getImage($savePath = '',$format = 'png'){
			$func = 'image' . ($format == 'jpg'?'jpeg':$format);
			if(!function_exists($func)) throw new Exception('格式错误，只能使用"png,gif,jpg"三种格式');
			$savePath?$func($this -> im,$savePath):$func($this -> im);
			if($savePath) return is_file($savePath);
		}

		/**
		 *等比缩放计算新图片大小
		 *@param int $dstw 	目标宽度
		 *@param int $dsth 	目标高度
		 *@param int $orgw 	原始宽度
		 *@param int $orgh 	原始高度
		 *@return array
		 */
		protected function getImageNewSize($dstw,$dsth,$orgw,$orgh){
			$wRotation 		= $dstw / $orgw;
			$hRotation		= $dsth / $orgh;
			$size 			= array();
			if($wRotation > $hRotation){
				$size['width']  = intval($orgw * $hRotation);
				$size['height'] = intval($dsth);
			}else{
				$size['width']  = intval($dstw);
				$size['height'] = intval($orgh * $wRotation);
			}
			return $size;
		}

		//16进制颜色转换为RGB颜色
		protected function hex2RGBColor($hexColor){
			$hexColor 		= str_replace('#','',$hexColor);
			$isShortColor 	= strlen($hexColor) == 3;
			$keyMap 		= ['R','G','B'];
			$rgbColor 		= [];
			$i 				= 0;
			$len 			= $isShortColor ? 1 : 2;
			while($str = substr($hexColor, $len * $i, $len)){

				$rgbColor[$keyMap[$i]] = hexdec($isShortColor ? $str . $str : $str);

				$i++;
			}
			return $rgbColor;
		}
	}
?>
