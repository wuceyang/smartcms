<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <!-- 当前位置 -->
    <div id="urHere">内容管理<b>»</b><strong>添加内容</strong> </div>
    <div class="mainBox">
        <table width="100%" border="0" cellpadding="8" cellspacing="0" class="tableBasic">
            <tbody>
                <tr>
                    <th width="131"></th>
                    <th></th>
                </tr>
                <tr>
                    <td align="right">文章标题</td>
                    <td><input type="text" name="title" value="" size="80" class="inpMain"> *</td>
                </tr>
                <tr>
                    <td align="right">关键字</td>
                    <td>
                        <input type="text" name="kw" value="" size="80" class="inpMain">
                    </td>
                </tr>
                <tr>
                    <td align="right">摘要</td>
                    <td>
                        <input type="text" name="site_keywords" value="" size="80" class="inpMain">
                    </td>
                </tr>
                <tr>
                    <td align="right">作者</td>
                    <td>
                        <input type="text" name="author" value="<?php echo $authorinfo["username"];?>" size="80" class="inpMain">
                    </td>
                </tr>
                <tr>
                    <td align="right">来源</td>
                    <td>
                        <input type="text" name="author" value="" size="80" class="inpMain">
                    </td>
                </tr>
                <tr>
                    <td align="right">所属栏目</td>
                    <td>
                        <select name="catid">
                        <?php if(isset($catlist["0"]) && is_array($catlist["0"])){  foreach($catlist["0"] as $k =>  $v){  if(isset($catlist[$v["info"]["id"]])){ ?>
                            <optgroup label="<?php echo $v["info"]["category_name"];?>"/>
                                <?php if(isset($catlist[$v["info"]["id"]]) && is_array($catlist[$v["info"]["id"]])){  foreach($catlist[$v["info"]["id"]] as $sk =>  $sv){ ?>
                                    <option name="<?php echo $sv["info"]["id"];?>"><?php echo $sv["info"]["category_name"];?></option>
                                <?php }} ?>
                            <optgroup/>
                        <?php }else{ ?>
                            <option name="<?php echo $v["info"]["id"];?>"><?php echo $v["info"]["category_name"];?></option>
                        <?php }  }} ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right">文章模板</td>
                    <td>
                        <select name="tplid">
                            <?php if(isset($tpllist) && is_array($tpllist)){  foreach($tpllist as $k =>  $v){ ?>
                            <option value="<?php echo $v["id"];?>"<?php if($catinfo["tplid"] == $v["id"]){ ?> selected<?php } ?>><?php echo $v["template_name"];?></option>
                            <?php }} ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right">内容正文</td>
                    <td id="contentbox">
                    </td>
                </tr>
                <tr>
                    <td align="right">附加内容</td>
                    <td>
                        <label for="none">
                            <input type="radio" name="datatype" id="none" value="images" checked /> 无
                        </label>
                        <label for="imagegroup">
                            <input type="radio" name="datatype" id="imagegroup" value="images" /> 套图
                        </label>
                        <label for="video">
                            <input type="radio" name="datatype" value="video" id="video" /> 视频
                        </label>
                    </td>
                </tr>
                <tr>
                    <td align="right"></td>
                    <td>
                      <input type="text" name="fax" value="" size="80" class="inpMain">
                    </td>
                </tr>
                 </tbody></table>
    </div>
<?php } ?>

<?php function __css__($params){ extract($params);?>

<link rel="stylesheet" type="text/css" href="/wangeditor/css/wangEditor.min.css">

<?php } ?>

<?php function __js__($params){ extract($params);?>
<script type="text/javascript" src="/wangeditor/js/wangEditor.min.js"></script>
<script type="text/javascript">

var pWin = window;

var editor = new wangEditor('contentbox');

</script>
<?php } ?>