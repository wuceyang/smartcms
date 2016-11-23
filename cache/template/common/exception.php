<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN"> 
<head> 
<title>代码异常提示</title> 
<meta http-equiv="Content-Type" content="text/html; charset=GBK" /> 
<meta http-equiv="Content-Language" content="zh-CN" /> 
<meta name="keywords" content=""/>
<meta name="description" content="" />
<style type="text/css">
    body{font-size:12px;}
</style>
</head>
<body>
<div style="width:80%; background-color:#FFF; border:1px solid #CCC; line-height:25px; margin-left:auto; margin-right:auto;">
    <ul style="padding:0px; margin:0px;">
        <li style="padding-left:3px; margin:0px; background-color:#2889EA; line-height: 25px; color:#FFF; list-style:none;"><b>喔，代码出错啦!</b></li>
        <li style="padding-left:3px; margin:0px; line-height: 25px; list-style:none;">错误信息: <?php echo $message;?></li>
        <li style="padding-left:3px; margin:0px; line-height: 25px; list-style:none;">错误位置: <?php echo $file;?>, 第<?php echo $line;?>行</li>
        <li style="padding-left:3px; margin:0px; line-height: 25px; list-style:none;">错误类型: <?php echo $type;?></li>
        <li style="padding-left:3px; margin:0px; line-height: 25px; list-style:none;">错误追踪:</li>
        <?php if(isset($trace) && is_array($trace)){  foreach($trace as $k =>  $v){ ?>
        <li style="padding-left:3px; margin:3px; padding:2px; list-style:none;"> -> <?php echo isset($v["file"]) ? (str_replace(APP_ROOT, '/', $v["file"]) . ', ') : ''; echo isset($v["line"]) ? ('第' . $v["line"] . '行, ') : ''; echo isset($v["class"]) ? ($v["class"] . '::') : ''; echo $v["function"];?></li>
        <?php }} ?>
    </ul>
</div>
<script type="text/javascript">
    var li = document.getElementsByTagName('li');
    for(i = 1, n = li.length; i < n; ++i){
        (function(index, obj){
            var line = obj[index];
            line.onmouseover = function(){
                this.style.backgroundColor = "#EFEFEF";
            }
            line.onmouseout = function(){
                this.style.backgroundColor = "#FFF";
            }
        })(i, li);
    }
</script>
</body>
</html>