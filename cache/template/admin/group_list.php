<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <!-- 当前位置 -->
    <div id="urHere">权限管理<b>»</b><strong>用户分组列表</strong> </div>
    <div class="mainBox">
        <h3><a href="/admin/user-group/add-group" class="actionBtn add">添加分组</a> &nbsp; </h3>
        <table border="0" align="center" cellpadding="8" cellspacing="0" class="tableBasic w90">
            <tr>
                <th width="50" align="center">ID</th>
                <th align="center">分组名称</th>
                <th width="60">固定分组</th>
                <th width="120" align="center">创建日期</th>
                <th width="120" align="center">分组状态</th>
                <th align="center">创建者</th>
                <th width="120" align="center">操作</th>
            </tr>
            <?php if(isset($list) && is_array($list)){  foreach($list as $k =>  $v){ ?>
            <tr>
                <td align="center"><?php echo $v["id"];?></td>
                <td align="left"><?php echo $v["group_name"];?></td>
                <td align="center"><?php echo $v["is_fixed"] ? '是' : '否';?></td>
                <td align="center"><?php echo date('Y-m-d', $v["create_dateline"]);?></td>
                <td align="center"><?php echo $v["group_status"] == 1 ? '正常' : '禁用';?></td>
                <td align="center"><?php echo isset($creator[$v["create_by"]]) ? $creator[$v["create_by"]]["username"] : '未知';?></td>
                <td align="center">
                    <a class="editbtn" href="/admin/user-group/edit-group">编辑</a> | 
                    <a class="delbtn" data-id="<?php echo $v["id"];?>" data-status="<?php echo $v["group_status"];?>" href="/admin/user-group/switch-group"><?php echo $v["group_status"] == 1 ? '禁用' : '启用';?></a>
                </td>
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

    var content = "确定要" + action + "当前用户分组吗？";

    var url     = $(this).attr('href');

    var status  = <?php echo \App\Helper\Enum::STATUS_NORMAL + \App\Helper\Enum::STATUS_DISABLED;?> - data.status;

    var options = {

        onOk:function(){

                pWin.doRequest(url, {id: data.id, status: status}, function(ret){

                    if(ret.code == 200){

                        location.reload();

                        return;
                    }

                    pWin.showMsg(ret.message || "切换用户分组状态出错");
                });
            }
    }

    pWin.showConfirm(content, "帐号" + action, options);

    return false;
})
</script>
<?php } ?>