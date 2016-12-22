<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <!-- 当前位置 -->
    <div id="urHere">推荐位管理<b>»</b><strong>推荐位列表</strong> </div>
    <div class="mainBox">
        <h3><a href="/admin/recommend/add-recommend" class="actionBtn add">添加分类</a> &nbsp; </h3>
        <table border="0" align="center" cellpadding="8" cellspacing="0" class="tableBasic w90">
            <tr>
                <th width="50" align="center">ID</th>
                <th align="center">名称</th>
                <th align="center">状态</th>
                <th width="120" align="center">创建日期</th>
                <th width="120" align="center">操作</th>
            </tr>
            <?php if(isset($list) && is_array($list)){  foreach($list as $k =>  $v){ ?>
            <tr>
                <td align="center"><?php echo $v["id"];?></td>
                <td align="center"><?php echo $v["name"];?></td>
                <td align="center"><?php echo $v["status"] == 1 ? '正常' : '禁用';?></td>
                <td align="center"><?php echo $v["create_time"];?></td>
                <td align="center"><a class="editbtn" data-id="<?php echo $v["id"];?>" data-name="<?php echo $v["name"];?>" data-status="<?php echo $v["status"];?>" href="/admin/recommend/edit-recommend">编辑</a> | <a class="delbtn" data-id="<?php echo $v["id"];?>" data-status="<?php echo $v["status"];?>" href="/admin/recommend/disable-recommend"><?php echo $v["status"] == 1 ? '禁用' : '启用';?></a></td>
            </tr>
            <?php }}  if($pageStr){ ?>
            <tr>
                <td colspan="5">
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
                        <label class="label">名称:</label><input name="name" class="inpMain" value="" type="text" width="50" /> <label class="tips">*推荐位名称</label>
                    </li>
                </ul>
            </form>
        </div>
        <div id="editbox">
            <form method="post">
                <input name="id" value="" type="hidden" />
                <ul class="popContainer">
                    <li>
                        <label class="label">名称:</label>
                        <input name="name" value="" class="inpMain" type="text" width="50" />
                        <label class="tips">*推荐位名称</label>
                    </li>
                    <li>
                        <label class="label">状态:</label>
                        <label class="radio"><input type="radio" name="status" value="1">启用</label>
                        <label class="radio"><input type="radio" name="status" value="2">禁用</label>
                    </li>
                </ul>
            </form>
        </div>
    </div>
<?php } ?>

<?php function __js__($params){ extract($params);?>
<script type="text/javascript">
var pWin = window;

$('.editbtn').on('click', function(){

    var data = $(this).data();

    var url  = $(this).attr('href');

    var option = {

        onCreate: function(){

            $('input[name="name"]', this).val(data.name);

            $('input[name="id"]', this).val(data.id);

            $(':radio[name="status"]', this).each(function(){

                if($(this).val() == data.status){

                    $(this).attr('checked', true);

                    return false;
                }
            });
        },
        onOk: function(){

            pWin.doRequest(url, $('form', this).serialize(), function(ret){

                if(ret.code == 200){

                    location.reload();

                    return;
                }

                pWin.showMsg(ret.message || "编辑推荐位出错");
            })
        }
    }

    pWin.showConfirm($('#editbox').html(), "推荐位编辑", option);

    return false;
})

$('.delbtn').on('click', function(){

    var data    = $(this).data();

    var action  = data.status == 1 ? "禁用" : "启用";

    var content = "确定要" + action + "当前推荐位吗？";

    var url     = $(this).attr('href');

    var status  = <?php echo \App\Helper\Enum::STATUS_NORMAL + \App\Helper\Enum::STATUS_DISABLED;?> - data.status;

    pWin.showConfirm(content, "推荐位" + action, function(){

        pWin.doRequest(url, {id: data.id, status: status}, function(ret){

            if(ret.code == 200){

                location.reload();

                return;
            }

            pWin.showMsg(ret.message || "切换推荐位状态出错");
        });
    });

    return false;
})

$('.actionBtn').on('click', function(){
    
    var html = $('#addbox').html();

    var url = $(this).attr('href');

    pWin.showConfirm(html, "添加推荐位", function(){

        pWin.doRequest(url, $('.xcConfirm form', pWin.doc).serialize(), function(ret){

            if(ret.code == 200){

                location.reload();

                return;
            }

            pWin.showMsg(ret.message || "添加推荐位出错");
        });
    });

    return false;
})
</script>
<?php } ?>