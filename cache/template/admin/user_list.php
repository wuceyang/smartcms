<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <!-- 当前位置 -->
    <div id="urHere">权限管理<b>»</b><strong>用户列表</strong> </div>
    <div class="mainBox">
        <h3><a href="/admin/user/add-user" class="actionBtn add">添加用户</a> &nbsp; </h3>
        <table border="0" align="center" cellpadding="8" cellspacing="0" class="tableBasic w90">
            <tr>
                <th width="50" align="center">ID</th>
                <th align="center">姓名</th>
                <th width="60">帐号</th>
                <th width="120" align="center">创建日期</th>
                <th width="120" align="center">帐号状态</th>
                <th align="center">所属分组</th>
                <th width="120" align="center">操作</th>
            </tr>
            <?php if(isset($list) && is_array($list)){  foreach($list as $k =>  $v){ ?>
            <tr>
                <td align="center"><?php echo $v["id"];?></td>
                <td align="left"><?php echo $v["username"];?></td>
                <td align="center"><?php echo $v["account"];?></td>
                <td align="center"><?php echo date('Y-m-d', $v["reg_time"]);?></td>
                <td align="center"><?php echo $v["status"] == 1 ? '正常' : '禁用';?></td>
                <td align="center"><?php echo implode(',', $v["groups"]);?></td>
                <td align="center">
                    <a class="editbtn" data-id="<?php echo $v["id"];?>" data-status="<?php echo $v["status"];?>" data-groupid="<?php echo $v["group_id"];?>" data-account="<?php echo $v["account"];?>" data-username="<?php echo $v["username"];?>" href="/admin/user/edit-user">编辑</a> | 
                    <a class="delbtn" data-id="<?php echo $v["id"];?>" data-status="<?php echo $v["status"];?>" href="/admin/user/switch-user"><?php echo $v["status"] == 1 ? '禁用' : '启用';?></a>
                </td>
            </tr>
            <?php }}  if($pageStr){ ?>
            <tr>
                <td colspan="7">
                    <div class="row">
                        <div class="col-lg-4 adv-table">
                            <div class="dataTables_info">当前第<?php echo $curPage;?>/<?php echo $totalPage;?>页，共<?php echo $totalRecord;?> 条记录
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <ul class="dataTables_paginate paging_bootstrap pagination">
                                <?php echo $pageStr;?>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <div class="hide">
        <div id="addbox">
            <form method="post">
                <ul class="popContainer">
                    <li>
                        <label class="label">用户姓名:</label>
                        <input name="username" class="inpMain" value="" type="text" width="50" />
                        <label class="tips"> *帐号使用者的姓名</label>
                    </li>
                    <li>
                        <label class="label">登录帐号:</label>
                        <input name="account" class="inpMain" value="" type="text" width="50" />
                    </li>
                    <li>
                        <label class="label">登录密码:</label>
                        <input name="passwd" class="inpMain" value="" type="text" width="50" />
                        <label class="tips"> *登录时使用的密码</label>
                    </li>
                    <li>
                        <label class="label">所属分组:</label>
                        <?php if(isset($groups) && is_array($groups)){  foreach($groups as $k =>  $v){ ?>
                            <label> <input type="checkbox" name="groupid[]" value="<?php echo $v["id"];?>" /> <?php echo $v["group_name"];?></label>
                        <?php }} ?>
                        <label class="tips"> *选择帐号所在的用户分组</label>
                    </li>
                </ul>
            </form>
        </div>
        <div id="editbox">
            <form method="post">
                <input name="userid" type="hidden" value="" />
                <ul class="popContainer">
                    <li>
                        <label class="label">用户姓名:</label>
                        <input name="username" class="inpMain" value="" type="text" width="50" />
                        <label class="tips"> *帐号使用者的姓名</label>
                    </li>
                    <li>
                        <label class="label">登录帐号:</label>
                        <input name="account" class="inpMain" value="" type="text" width="50" />
                    </li>
                    <li>
                        <label class="label">登录密码:</label>
                        <input name="passwd" class="inpMain" value="" type="text" width="50" />
                        <label class="tips"> *留空则表示不修改密码</label>
                    </li>
                    <li>
                        <label class="label">所属分组:</label>
                        <?php if(isset($groups) && is_array($groups)){  foreach($groups as $k =>  $v){ ?>
                            <label> <input type="checkbox" name="groupid[]" value="<?php echo $v["id"];?>" /> <?php echo $v["group_name"];?></label>
                        <?php }} ?>
                        <label class="tips"> *选择帐号所在的用户分组</label>
                    </li>
                    <li>
                        <label class="label">账号状态:</label> <label><input name="status" value="1" type="radio" /> 启用</label> <label><input name="status" value="2" type="radio"/> 禁用</label><label class="tips"> *当前栏目状态</label>
                    </li>
                </ul>
            </form>
        </div>
    </div>
<?php } ?>

<?php function __js__($params){ extract($params);?>
<script type="text/javascript">

var pWin = window;

$('.delbtn').on('click', function(){

    var data    = $(this).data();

    var action  = data.status == 1 ? "禁用" : "启用";

    var content = "确定要" + action + "当前用户帐号吗？";

    var url     = $(this).attr('href');

    var status  = <?php echo \App\Helper\Enum::STATUS_NORMAL + \App\Helper\Enum::STATUS_DISABLED;?> - data.status;

    var options = {

        onOk:function(){

                pWin.doRequest(url, {id: data.id, status: status}, function(ret){

                    if(ret.code == 200){

                        location.reload();

                        return;
                    }

                    pWin.showMsg(ret.message || "切换帐号状态出错");
                });
            }
    }

    pWin.showConfirm(content, "帐号" + action, options);

    return false;
})

$('.editbtn').on('click', function(){
    
    var data     = $(this).data();

    var url     = $(this).attr('href');

    var config = {

        onOk: function(){

            pWin.doRequest(url, $('form', this).serialize(), function(ret){

                if(ret.code == 200){

                    location.reload();

                    return;
                }

                pWin.showMsg(ret.message || "编辑栏目信息出错");
            });
        },
        onCreate: function(){

            $(':hidden[name=userid]').val(data.id);

            $('input[name=username]', this).val(data.username);

            $('input[name=account]', this).val(data.account);

            $(':radio[name=status]', this).each(function(){

                if($(this).val() == data.status){

                    $(this).prop('checked', true);

                    return true;
                }
            });

            $(':checkbox').each(function(){

                if(data.groupid.indexOf(',' + $(this).val() + ',') > -1){

                    $(this).prop('checked', true);
                }
            })
        }
    };

    pWin.showConfirm($('#editbox').html(), "栏目信息编辑", config);

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

                pWin.showMsg(ret.message || "添加栏目信息出错");
            });
        },
        onCreate: function(){

        }
    };

    pWin.showConfirm(html, "添加分类", config);

    return false;
})
</script>
<?php } ?>