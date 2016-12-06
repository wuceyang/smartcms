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
                    <td>
                        <div id="contentbox"></div>
                    </td>
                </tr>
                <tr>
                    <td align="right">附加内容</td>
                    <td>
                        <label for="none">
                            <input type="radio" name="datatype" id="none" value="0" checked data-bucket="none" /> 无
                        </label>
                        <label for="imagegroup">
                            <input type="radio" name="datatype" id="imagegroup" value="1" data-bucket="image" /> 套图
                        </label>
                        <label for="video">
                            <input type="radio" name="datatype" value="video" id="2" data-bucket="video" /> 视频
                        </label>
                    </td>
                </tr>
                <tr id="addonRow" class="hide">
                    <td align="right"></td>
                    <td>
                        <div id="addon"></div>
                        <div><button type="button" class="btn">上传</button></div>
                    </td>
                </tr>
                <tr>
                    <td align="right"></td>
                    <td>
                      <button type="submit" class="btnPayment">确定</button>
                    </td>
                </tr>
                 </tbody>
            </table>
            <div class="hide" id="popContainer">
                <div class="imageAdd">
                    <div class="row">
                        <label class="rowLabel">选择图片:</label>
                        <div class="rowEle upBtn">
                            <div class="virtualBtn">选择文件</div>
                            <div class="progressbox hide">
                                <div class="progressbar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="rowLabel">描述文字:</label>
                        <div class="rowEle">
                            <input size="60" type="text" name="brief" value="" class="inpMain"/>
                        </div>
                    </div>
                </div>
            </div>
    </div>
<?php } ?>

<?php function __css__($params){ extract($params);?>

<link rel="stylesheet" type="text/css" href="/wangeditor/css/wangEditor.min.css">
<style type="text/css">
    #contentbox{
        height:400px;
        width:100%;
    }
</style>

<?php } ?>

<?php function __js__($params){ extract($params);?>
<script type="text/javascript" src="/wangeditor/js/wangEditor.min.js"></script>
<script type="text/javascript" src="/jqplugin/qiniu.js"></script>
<script type="text/javascript" src="/jqplugin/pupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/jqplugin/pupload/moxie.js"></script>
<script type="text/javascript">

var pWin = window;

var buckets = {
                image: '<?php echo \App\Helper\Storage\Qiniu::DOMAIN_IMAGE;?>',
                video: '<?php echo \App\Helper\Storage\Qiniu::DOMAIN_VIDEO;?>',
                other: '<?php echo \App\Helper\Storage\Qiniu::DOMAIN_OTHER;?>',
              };

var editor = new wangEditor('contentbox');

    editor.create();

$(':radio[name=datatype]').on('click', function(){
    
    var dataType = $(this).val();

    $('#addonRow').addClass('hide');

    if(dataType == 0) return;

    $('#addonRow').removeClass('hide');

    $('#addonRow button').data('bucket', $(this).data('bucket'));
})

$('#addonRow button').on('click', function(){

    var bucket   = $(this).data('bucket');

    var upParams = {
        runtimes: 'html5,flash,html4',      // 上传模式,依次退化
        uptoken_url: '/admin/upload/token?bucket=' + bucket,
        url: '/admin/upload/do-upload',
        get_new_uptoken: true,
        up_host: 'http://up-z2.qiniu.com',
        unique_names: true,
        domain: buckets[bucket],
        max_file_size: '100mb',
        flash_swf_url: '/jqplugin/pupload/moxie.swf',
        silverlight_xap_url : '/jqplugin/pupload/moxie.xap',
        max_retries: 3,
        chunk_size: '4mb',
        auto_start: true,
        init:{
            FileUploaded: function(){

            },
            BeforeUpload: function(){
                $('.progressbox .progressbar').css({'width': '0%'});
                $('.progressbox').removeClass('hide');
            },
            UploadComplete: function(){
                
            },
            UploadProgress: function(uploader, info){
                $('.progressbox .progressbar').css({'width': info.percent + '%'});
            },
            Error: function(){
                console.log(arguments);
            }
        }
    };

    var params = {
        onCreate: function(){

            upParams['browse_button'] = $('.virtualBtn', this)[0];

            var _this = this;

            $('.progressbox', this).addClass('hide');

            setTimeout(function(){

                Qiniu.uploader.call(_this, upParams);
            }, 300);
        }
    };

    pWin.showConfirm($('#popContainer').html(), '添加图片', params);
})

</script>
<?php } ?>