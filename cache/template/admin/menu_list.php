<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <!-- 当前位置 -->
    <div id="urHere">权限管理<b>»</b><strong>菜单列表</strong> </div>
    <div class="mainBox">
        <h3><a href="/admin/menu/add-menu" class="actionBtn add">添加菜单</a> &nbsp; </h3>
        <table border="0" align="center" cellpadding="8" cellspacing="0" class="tableBasic w90">
            <tr>
                <th width="50" align="center">ID</th>
                <th align="center" width="100">菜单名称</th>
                <th align="center" width="80">菜单状态</th>
                <th width="240" align="center">链接地址</th>
                <th width="100" align="center">Icon图标</th>
                <th width="80" align="center">显示排序</th>
                <th width="120" align="center">操作</th>
            </tr>
            <?php if(isset($list["0"]) && is_array($list["0"])){  foreach($list["0"] as $k =>  $v){ ?>
            <tr>
                <td align="center"><?php echo $v["id"];?></td>
                <td align="left"><?php echo $v["title"];?> <a href="javascript:;" data-id="<?php echo $v["id"];?>" name="showsub" title="点击查看子栏目" style="font-size:1.2em;">+</a> </td>
                <td align="center"><?php echo $v["enable"] == 1 ? '正常' : '禁用';?></td>
                <td align="center"><?php echo '';?></td>
                <td align="center"><?php echo $v["icon"];?></td>
                <td align="center"><?php echo $v["show_order"];?></td>
                <td align="center">
                    <a class="editbtn" data-id="<?php echo $v["id"];?>" href="/admin/category/edit-category">编辑</a> | 
                    <a class="delbtn" data-id="<?php echo $v["id"];?>" data-status="<?php echo $v["enable"];?>" href="/admin/category/switch-category"><?php echo $v["enable"] == 1 ? '禁用' : '启用';?></a>
                </td>
            </tr>
            <?php if(isset($v["sublist"]) && is_array($v["sublist"])){  foreach($v["sublist"] as $sk =>  $sv){ ?>
            <tr data-pid="<?php echo $v["id"];?>" class="hide">
                <td align="center"></td>
                <td align="left"> » <?php echo $sv["title"];?></td>
                <td align="center"><?php echo $sv["enable"] == 1 ? '正常' : '禁用';?></td>
                <td align="left"><?php echo $sv["url"];?></td>
                <td align="center"></td>
                <td align="center"><?php echo $sv["show_order"];?></td>
                <td align="center">
                    <a class="editbtn" data-id="<?php echo $sv["id"];?>" href="/admin/category/edit-category">编辑</a> | 
                    <a class="delbtn" data-id="<?php echo $sv["id"];?>" data-status="<?php echo $sv["enable"];?>" href="/admin/category/switch-category"><?php echo $sv["enable"] == 1 ? '禁用' : '启用';?></a>
            </td>
            </tr>
            <?php }}  }} ?>
        </table>
    </div>
    <div class="hide">
        <div id="addbox">
            <form method="post">
                <ul class="popContainer">
                    <li>
                        <label class="label">菜单名称:</label> <input name="title" class="inpMain" value="" type="text" width="50" /> <label class="tips">*菜单名称</label>
                    </li>
                    <li>
                        <label class="label">上级菜单:</label>
                        <select name="parentid">
                            <option value="0">顶级菜单</option>
                            <?php if(isset($list["0"]) && is_array($list["0"])){  foreach($list["0"] as $k =>  $v){ ?>
                            <option value="<?php echo $v["id"];?>"><?php echo $v["title"];?></option>
                            <?php }} ?>
                        </select>
                        <label class="tips"> *选择上级菜单</label>
                    </li>
                    <li class="opt_url">
                        <label class="label">链接地址:</label> <input name="url" class="inpMain" value="" type="text" width="50" /> <label class="tips">*菜单链接地址</label>
                    </li>
                    <li class="opt_icon">
                        <label class="label">Icon图标:</label> <input name="icon" class="inpMain" value="" type="text" width="50" /> <label class="tips">*菜单链接地址</label>
                    </li>
                    <li>
                        <label class="label">排序顺序:</label> <input name="order" class="inpMain" value="" type="text" width="50" /> <label class="tips"> *当前菜单排序</label>
                    </li>
                </ul>
            </form>
        </div>
        <div id="editbox">
            <form method="post">
                <input name="mid" type="hidden" value="" />
                <ul class="popContainer">
                    <li>
                        <label class="label">菜单名称:</label> <input name="title" class="inpMain" value="" type="text" width="50" /> <label class="tips">*菜单名称</label>
                    </li>
                    <li>
                        <label class="label">上级菜单:</label>
                        <select name="parentid">
                            <option value="0">顶级菜单</option>
                            <?php if(isset($list["0"]) && is_array($list["0"])){  foreach($list["0"] as $k =>  $v){ ?>
                            <option value="<?php echo $v["id"];?>"><?php echo $v["title"];?></option>
                            <?php }} ?>
                        </select>
                        <label class="tips"> *选择上级菜单</label>
                    </li>
                    <li class="opt_url">
                        <label class="label">链接地址:</label> <input name="url" class="inpMain" value="" type="text" width="50" /> <label class="tips">*菜单链接地址</label>
                    </li>
                    <li class="opt_icon">
                        <label class="label">Icon图标:</label> <input name="icon" class="inpMain" value="" type="text" width="50" /> <label class="tips">*顶级菜单必填</label>
                    </li>
                    <li>
                        <label class="label">排序顺序:</label> <input name="order" class="inpMain" value="" type="text" width="50" /> <label class="tips">*当前栏目排序</label>
                    </li>
                    <li>
                        <label class="label">栏目状态:</label> <label><input name="status" value="1" type="radio" /> 启用</label> <label><input name="status" value="2" type="radio"/> 禁用</label><label class="tips">*当前栏目状态</label>
                    </li>
                </ul>
            </form>
        </div>
    </div>
<?php } ?>

<?php function __js__($params){ extract($params);?>
<script type="text/javascript">
var pWin = window;

var categories = <?php echo json_encode($list["0"] ? $list["0"] : []);?>;

$('a[name=showsub]').on('click', function(){

    $(this).text($(this).text() == '+' ? '-' : '+');
    
    var pid = $(this).data('id');

    var thisTr = $(this).parent().parent();

    while(tr = thisTr.next('tr')){

        if(pid != tr.data('pid')){

            break;
        }

        tr.toggleClass('hide');

        thisTr = tr;
    }
})

$('.delbtn').on('click', function(){

    var data    = $(this).data();

    var action  = data.status == 1 ? "禁用" : "启用";

    var content = "确定要" + action + "当前菜单吗？";

    var url     = $(this).attr('href');

    var status  = <?php echo \App\Helper\Enum::STATUS_NORMAL + \App\Helper\Enum::STATUS_DISABLED;?> - data.status;

    var options = {
        onOk:function(){

                pWin.doRequest(url, {id: data.id, status: status}, function(ret){

                    if(ret.code == 200){

                        location.reload();

                        return;
                    }

                    pWin.showMsg(ret.message || "切换菜单状态出错");
                });
            }
    }

    pWin.showConfirm(content, "分类" + action, options);

    return false;
})

$('.editbtn').on('click', function(){
    
    var data     = $(this).data();

    var brothers = categories[data.pid];

    var url      = $(this).attr('href');

    var parentpath = data.parentpath;

    var config = {

        onOk: function(){

            pWin.doRequest(url, $('form', this).serialize(), function(ret){

                if(ret.code == 200){

                    location.reload();

                    return;
                }

                pWin.showMsg(ret.message || "编辑菜单信息出错");
            });
        },
        onCreate: function(){

            $('input[name="name"]', this).val(info.category_name);

            $('input[name=catid]', this).val(data.id);

            $(':radio[name="status"]', this).each(function(){

                if($(this).val() == info.status){

                    $(this).prop('checked', true);

                    return true;
                }
            });

            $('input[name="show_order"]', this).val(info.show_order);

            var sel = $('select', this).eq(0);

            var parent = sel.parent();

            initSelects.call(sel, parentpath, false);

            sel.parent().delegate('select','change', function(){

                bindChange.call(this);
            });

            sel.remove();
        }
    };

    pWin.showConfirm($('#editbox').html(), "菜单信息编辑", config);

    return false;

})

$('.actionBtn').on('click', function(){
    
    var html = $('#addbox').html();

    var url = $(this).attr('href');

    var config = {

        onOk: function(){

            pWin.doRequest(url, $('form', this).serialize(), function(ret){

                if(ret.code == 200){

                    location.reload();

                    return;
                }

                pWin.showMsg(ret.message || "添加菜单信息出错");
            });
        }
    };

    pWin.showConfirm(html, "添加菜单", config);

    return false;
})
</script>
<?php } ?>