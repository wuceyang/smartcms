<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <!-- 当前位置 -->
    <div id="urHere">内容管理<b>»</b><strong>添加话题</strong> </div>
    <div class="mainBox">
        <form action="/admin/topic/do-post">
        <table width="100%" border="0" cellpadding="8" cellspacing="0" class="tableBasic">
            <tbody>
                <tr>
                    <th width="131"></th>
                    <th></th>
                </tr>
                <tr>
                    <td align="right">标题</td>
                    <td><input type="text" name="title" value="" size="80" class="inpMain"> *</td>
                </tr>
                <tr>
                    <td align="right">缩略图</td>
                    <td>
                        <div class="imagepath fleft w75">
                        </div>
                        <div class="uploadbox fleft">
                            <div data-bucket="image" data-field="thumb" class="uploader icon-plus icon-large dashed"></div>
                        </div>
                        <div class="fleft" style="height: 50px; line-height: 50px; padding-left: 5px;"> * 标题列表中的图片</div>
                        <div class="clear"></div>
                    </td>
                </tr>
                <tr>
                    <td align="right">主播</td>
                    <td><input type="text" name="actorName" value="" size="10" class="inpMain"> <input type="text" name="actorId" value="" size="10" class="inpMain" readonly="readonly">*发布为该主播的话题</td>
                </tr>
                <tr>
                    <td align="right">类型</td>
                    <td class="topic_type">
                        <label data-target="none"> <input type="radio" name="type" value="1" checked size="80">文字</label>
                        <label data-target="image"> <input type="radio" name="type" value="2" size="80">图片</label>
                        <label data-target="video"> <input type="radio" name="type" value="3" size="80">视频</label>
                        <label data-target="audio"> <input type="radio" name="type" value="4" size="80">音频</label>
                    </td>
                </tr>
                <tr id="image_area" class="area hide">
                    <td></td>
                    <td>
                        <div class="imagepath fleft w670">
                        </div>
                        <div class="uploadbox fleft">
                            <div data-bucket="image" data-field="image" class="uploader icon-plus icon-large dashed"></div>
                        </div>
                        <div class="fleft" style="height: 50px; line-height: 50px; padding-left: 5px;"> * 最多上传9张图片</div>
                        <div class="clear"></div>
                    </td>
                </tr>
                <tr id="video_area" class="area hide">
                    <td></td>
                    <td>
                        <div class="imagepath fleft w75">
                        </div>
                        <div class="uploadbox fleft">
                            <div data-bucket="video" data-field="video" class="uploader icon-plus icon-large dashed"></div>
                        </div>
                        <div class="fleft" style="height: 50px; line-height: 50px; padding-left: 5px;"> * 选择要上传的视频</div>
                        <div class="clear"></div>
                    </td>
                </tr>
                <tr id="audio_area" class="area hide">
                    <td></td>
                    <td>
                        <div class="imagepath fleft w75">
                        </div>
                        <div class="uploadbox fleft">
                            <div data-bucket="video" data-field="audio" class="uploader icon-plus icon-large dashed"></div>
                        </div>
                        <div class="fleft" style="height: 50px; line-height: 50px; padding-left: 5px;"> * 选择要上传的视频</div>
                        <div class="clear"></div>
                    </td>
                </tr>
                <tr>
                    <td align="right">打开方式</td>
                    <td>
                        <label> <input type="radio" name="action" value="1" size="80">原生应用</label>
                        <label> <input type="radio" name="action" value="2" size="80">内部浏览器</label>
                        <label> <input type="radio" name="action" value="3" size="80">外部浏览器</label>
                    </td>
                </tr>
                <tr>
                    <td align="right">标签</td>
                    <td>
                        <?php if(isset($tags) && is_array($tags)){  foreach($tags as $k =>  $v){ ?>
                        <label><input type="radio" name="tag[]" value="<?php echo $k;?>" /> <?php echo $v;?></label>
                        <?php }} ?>
                    </td>
                </tr>
                <tr>
                    <td align="right">热门有效期</td>
                    <td>
                        <div class="calendar">
                            <input type="text" name="hot_expire_after" value="" size="80" readonly="readonly" id="calendar">
                            <div class="clicker laydate-icon"></div>
                            <div class="clear"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="right">内容正文</td>
                    <td>
                        <div id="contentbox"></div>
                    </td>
                </tr>
                <tr>
                    <td align="right"></td>
                    <td>
                      <button type="button" id="submit" class="btnPayment">确定</button>
                    </td>
                </tr>
                </tbody>
            </table>
    </div>
<?php } ?>

<?php function __css__($params){ extract($params);?>
<link rel="stylesheet" type="text/css" href="/theme/FontAwesome/css/font-awesome.css">
<!--[if IE 7]>
<link rel="stylesheet" href="/theme/FontAwesome/css/font-awesome-ie7.min.css">
<![endif]-->
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
<script type="text/javascript" src="/jqplugin/calendar/laydate.js"></script>
<script type="text/javascript">

$('.clicker').on('click', function(){
    laydate.skin('dahong');//切换皮肤，请查看skins下面皮肤库
    laydate({elem: '#calendar',format: 'YYYY-MM-DD hh:mm:ss', istime: true, istoday: true});//绑定元素
})
var pWin = window;

var data = {};

var url = '';

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

function createUploader(){
    $('.uploader').each(function(){
        var _this    = this;
        var bucket   = $(this).data('bucket');
        var field    = $(this).data('field');
        var upParams = {
            runtimes: 'html5,flash,html4',      // 上传模式,依次退化
            uptoken_url: '/admin/upload/token?bucket=' + bucket,
            get_new_uptoken: true,
            multi_selection: false,
            up_host: 'http://up-z2.qiniu.com',
            unique_names: true,
            domain: buckets[bucket],
            max_file_size: '100mb',
            flash_swf_url: '/jqplugin/pupload/moxie.swf',
            silverlight_xap_url : '/jqplugin/pupload/moxie.xap',
            max_retries: 3,
            chunk_size: '4mb',
            auto_start: true,
            browse_button: this,
            init:{
                FileUploaded: function(uploader, fileinfo){
                    var td = $(_this).parent('div').parent('td');
                    var url = buckets[bucket] + '/' + fileinfo.target_name;
                    var fieldName = field == 'image' ? 'image[]' : field;
                    var formEle = '<input type="hidden" name="' + fieldName + '" value="' + url + '">';
                    var preview = '<img src="' + url + '" height="100%"/>';
                    if(bucket == 'video'){
                        //视频预览
                    }
                    $('.progressbox', td).replaceWith(formEle + preview);
                },
                BeforeUpload: function(){
                    var td = $(_this).parent('div').parent('td');
                    $('<div class="imgpreview"><div class="progressbox"><div class="progressbar"></div></div></div>').appendTo($('.imagepath',td));
                },
                UploadComplete: function(uploader, fileinfo){
                    $('.moxie-shim').remove();
                    if(bucket == 'image'){
                        createUploader();
                    }
                },
                UploadProgress: function(uploader, info){
                    var td = $(_this).parent('div').parent('td');
                    $('.progressbar', td).css({'width': info.percent + '%'});
                },
                Error: function(){
                    
                }
            }
        };

        Qiniu.uploader(upParams);
    })
}

createUploader();

$('.topic_type label').on('click', function(){

    var target = $(this).data('target');

    var uploader = '#' + target + '_area';

    $('.area').hide();

    if(target != 'none'){

        $(uploader).show();
    }
})

$('input[name=actorName]').on('blur', function(){

    var actorName = $.trim($(this).val());

    if(actorName.length == 0){

        return;
    }

    pWin.doRequest('/admin/actor/get-actor', {actorName: actorName}, function(ret){

        if(ret.code == 200){

            if(ret.data.id > 0){

                $('input[name=actorId]').val(ret.data.id);
            }
        }
    });
})

$('#submit').on('click', function(){

    var form = $('form');

    pWin.doRequest(form.attr('action'), form.serialize(), function(ret){
            pWin.showMsg(ret.code = 200 ? '话题发布成功' : (ret.message || '话题发布失败'), '话题发布');
    });
})

</script>
<?php } ?>