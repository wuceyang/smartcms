<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <!-- 当前位置 -->
    <div id="urHere">栏目管理<b>»</b><strong>栏目列表</strong> </div>
    <div class="mainBox">
        <h3><a href="/admin/category/add-category" class="actionBtn add">添加栏目</a> &nbsp; </h3>
        <table border="0" align="center" cellpadding="8" cellspacing="0" class="tableBasic w90">
            <tr>
                <th width="50" align="center">ID</th>
                <th align="center">名称</th>
                <th align="center">状态</th>
                <th width="120" align="center">文章数量</th>
                <th width="120" align="center">显示排序</th>
                <th width="120" align="center">创建日期</th>
                <th width="120" align="center">操作</th>
            </tr>
            <?php if(isset($list) && is_array($list)){  foreach($list as $k =>  $v){ ?>
            <tr>
                <td align="center"><?php echo $v["id"];?></td>
                <td align="left"><?php echo $v["category_name"];?> <a href="javascript:;" data-id="<?php echo $v["id"];?>" name="showsub" title="点击查看子分类" style="font-size:1.2em;">+</a> </td>
                <td align="center"><?php echo $v["status"] == 1 ? '正常' : '禁用';?></td>
                <td align="center"><?php echo $v["content_num"];?></td>
                <td align="center"><?php echo $v["show_order"];?></td>
                <td align="center"><?php echo $v["create_time"];?></td>
                <td align="center"><a class="editbtn" data-id="<?php echo $v["id"];?>" data-pid="<?php echo $v["parent_id"];?>" data-parentpath="<?php echo $v["parent_path"];?>" href="/admin/category/edit-category">编辑</a> | <a class="delbtn" data-id="<?php echo $v["id"];?>" data-status="<?php echo $v["status"];?>" href="/admin/category/disable-category"><?php echo $v["status"] == 1 ? '禁用' : '启用';?></a></td>
            </tr>
            <?php if(isset($sublist[$v["id"]]) && is_array($sublist[$v["id"]])){  foreach($sublist[$v["id"]] as $sk =>  $sv){ ?>
            <tr data-pid="<?php echo $v["id"];?>" class="hide">
                <td align="center"></td>
                <td align="left"> -> <?php echo $sv["category_name"];?></td>
                <td align="center"><?php echo $sv["status"] == 1 ? '正常' : '禁用';?></td>
                <td align="center"><?php echo $sv["content_num"];?></td>
                <td align="center"><?php echo $sv["show_order"];?></td>
                <td align="center"><?php echo $sv["create_time"];?></td>
                <td align="center"><a class="editbtn" data-id="<?php echo $sv["id"];?>" data-pid="<?php echo $sv["parent_id"];?>" data-parentpath="<?php echo $sv["parent_path"];?>" href="/admin/category/edit-category">编辑</a> | <a class="delbtn" data-id="<?php echo $sv["id"];?>" data-status="<?php echo $sv["status"];?>" href="/admin/category/disable-category"><?php echo $sv["status"] == 1 ? '禁用' : '启用';?></a></td>
            </tr>
            <?php }}  }}  if($pageStr){ ?>
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
                        <label class="label">分类名称:</label> <input name="name" class="inpMain" value="" type="text" width="50" /> <label class="tips">*分类名称</label>
                    </li>
                    <li>
                        <label class="label">所属分类:</label> <select name="parent"><option value="0">顶级分类</option></select> <label class="tips">*选择上级分类</label>
                    </li>
                    <li>
                        <label class="label">排序顺序:</label> <input name="show_order" class="inpMain" value="" type="text" width="50" /> <label class="tips">*当前分类排序</label>
                    </li>
                </ul>
            </form>
        </div>
        <div id="editbox">
            <form method="post">
                <input name="catid" type="hidden" value="" />
                <ul class="popContainer">
                    <li>
                        <label class="label">分类名称:</label> <input name="name" class="inpMain" value="" type="text" width="50" /> <label class="tips">*分类名称</label>
                    </li>
                    <li>
                        <label class="label">所属分类:</label> <select name="parent"><option value="0">顶级分类</option></select> <label class="tips">*选择上级分类</label>
                    </li>
                    <li>
                        <label class="label">排序顺序:</label> <input name="show_order" class="inpMain" value="" type="text" width="50" /> <label class="tips">*当前分类排序</label>
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

var pWin = window;

var categories = <?php echo json_encode($allCategory ? $allCategory : []);?>;

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

    var content = "确定要" + action + "当前分类吗？";

    var url     = $(this).attr('href');

    var status  = <?php echo \App\Helper\Enum::STATUS_NORMAL + \App\Helper\Enum::STATUS_DISABLED;?> - data.status;

    pWin.showConfirm(content, "分类" + action, function(){

        pWin.doRequest(url, {id: data.id, status: status}, function(ret){

            if(ret.code == 200){

                location.reload();

                return;
            }

            pWin.showMsg(ret.message || "切换分类状态出错");
        });
    });

    return false;
})

$('.editbtn').on('click', function(){
    
    var data     = $(this).data();

    var brothers = categories[data.pid];

    var url      = $(this).attr('href');

    info         = {};

    for(var i in brothers){

        if(brothers[i].id == data.id){

            info = brothers[i];

            break;
        }
    }

    var parentpath = data.parentpath;

    var config = {
        onOk: function(){

            pWin.doRequest(url, $('form', this).serialize(), function(ret){

                console.log(ret.message);

                if(ret.code == 200){

                    location.reload();

                    return;
                }

                pWin.showMsg(ret.message || "编辑栏目信息出错");
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

            var sel = $('select', this);

            var parent = sel.parent();

            initSelects.call(sel, parentpath, false);

            sel.parent().delegate('select','change', function(){

                bindChange.call(this);
            });

            sel.remove();
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

            $('select', this).each(function(){

                if($(this).val() == 0){

                    $(this).remove();
                }
            })

            pWin.doRequest(url, $('form', this).serialize(), function(ret){

                if(ret.code == 200){

                    location.reload();

                    return;
                }

                pWin.showMsg(ret.message || "添加栏目信息出错");
            });
        },
        onCreate: function(){

            var sel = $('select', this);

            var parent = sel.parent();

            initSelects.call(sel, '0', true);

            sel.parent().delegate('select','change', function(){

                bindChange.call(this);
            });

            sel.remove();
        }
    };

    pWin.showConfirm(html, "添加分类", config);

    return false;
})

function bindChange(){
    
    var idx = $(this).index() - 1;

    var brothers = $(this).parent().find('select');

    for(var i = 0; i < brothers.length; i++){

        if(i <= idx){

            continue;
        }

        brothers[i].remove();
    }

    var thisVal = $(this).val();
    //选择顶级分类,删除后面的所有select
    if(thisVal == '0'){

        if(idx > 0){

            $(this).remove();
        }

        return;
    }

    //检查所选分类是否有子分类
    if(!categories[thisVal]){

        return;
    }

    var newSel = $('<select name="parent"></select>');

    $('<option value="0">删除此分类</option>').appendTo(newSel);

    for(var i in categories[thisVal]){

        var item = categories[thisVal][i];

        $('<option value="' + item.id + '">' + item.category_name + '</option>').appendTo(newSel);
    }

    newSel.insertAfter($(this));
}

function initSelects(parentPath, editable){
    
    var parentid    = parentPath.length > 0 ? parentPath.replace(/^,|,$/g, '').split(',') : [];

    var selectedVal = 0;

    for(var i in parentid){

        var nextIdx = parseInt(i, 10) + 1;

        selectedVal = parentid[nextIdx] ? parentid[nextIdx] : 0;

        if(i > 0 && selectedVal == 0){

            break;
        }

        var sel = createSelect(parentid[i], categories[i], selectedVal, editable);

        if(!sel){

            break;
        }

        $(sel).insertAfter(this);
    }
}

function createSelect(parentId, optionList, selectedVal, editable){

    if(!optionList) return null;

    var readonly = editable ? '' : ' disabled';
    
    var sel = $('<select' + readonly + ' name="parent"></select>');

    var text = parentId == 0 ? "顶级栏目" : "删除本栏目";

        $('<option value="' + parseInt(parentId, 10) + '">' + text + '</option>').appendTo(sel);

    for(var i in optionList){

        var selected = selectedVal == optionList[i].id ? ' selected' : '';

        $('<option value="' + optionList[i].id + '"' + selected + '>' + optionList[i].category_name + '</option>').appendTo(sel);
    }

    return sel;
}
<?php } ?>