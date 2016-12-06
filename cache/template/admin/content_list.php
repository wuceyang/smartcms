<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <!-- 当前位置 -->
    <div id="urHere">内容管理<b>»</b><strong>文章列表</strong> </div>
    <div class="mainBox">
        <h3><a href="/admin/content/add-content" class="actionBtn add">添加文章</a> &nbsp; </h3>
        <table border="0" align="center" cellpadding="8" cellspacing="0" class="tableBasic w90">
            <tr>
                <th width="50" align="center">ID</th>
                <th align="center">标题</th>
                <th align="center">所属分类</th>
                <th align="center">类型</th>
                <th align="center">状态</th>
                <th width="240" align="center">作者</th>
                <th width="240" align="center">创建日期</th>
                <th width="120" align="center">操作</th>
            </tr>
            <?php if(isset($list) && is_array($list)){  foreach($list as $k =>  $v){ ?>
            <tr>
                <td align="center"><?php echo $v["id"];?></td>
                <td align="left"><?php echo $v["title"];?></td>
                <td align="center"><?php echo isset($catinfo[$v["catid"]]) ? $catinfo[$v["catid"]]["category_name"] : '';?></td>
                <td align="center"><?php echo $v["template_html"] ? strlen($v["template_html"]) : 0;?></td>
                <td align="center"><?php echo $v["create_date"];?></td>
                <td align="center"><a href="/admin/template/edit-template?id=<?php echo $v["id"];?>">编辑</a> | <a class="delbtn" data-id="<?php echo $v["id"];?>" data-status="<?php echo $v["status"];?>" href="/admin/template/switch-template"><?php echo $v["status"] == 1 ? '禁用' : '启用';?></a></td>
            </tr>
            <?php }} ?>
        </table>
    </div>
<?php } ?>

<?php function __js__($params){ extract($params);?>
<script type="text/javascript">
var pWin = window;

$('.delbtn').on('click', function(){

    var data    = $(this).data();

    var action  = data.status == 1 ? "禁用" : "启用";

    var content = "确定要" + action + "当前文章吗？";

    var url     = $(this).attr('href');

    var status  = <?php echo \App\Helper\Enum::STATUS_NORMAL + \App\Helper\Enum::STATUS_DISABLED;?> - data.status;

    var options = {
        onOk:function(){

                pWin.doRequest(url, {id: data.id, status: status}, function(ret){

                    if(ret.code == 200){

                        location.reload();

                        return;
                    }

                    pWin.showMsg(ret.message || "切换分类状态出错");
                });
            }
    }

    pWin.showConfirm(content, "分类" + action, options);

    return false;
})
</script>
<?php } ?>