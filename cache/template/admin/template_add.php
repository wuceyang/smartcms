<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <!-- 当前位置 -->
    <div id="urHere">系统设置<b>»</b><strong>添加模板</strong></div>
    <div class="mainBox">
        <form action="/admin/template/add-template" method="post">
        <input type="hidden" name="token" value="<?php echo $token;?>" />
            <table width="100%" border="0" cellpadding="8" cellspacing="0" class="tableBasic">
                <tr>
                    <td width="80" align="right">模板名称</td>
                    <td>
                        <input type="text" name="tplName" value="" size="40" class="inpMain" />
                    </td>
                </tr>
                <tr>
                    <td align="right">状态</td>
                    <td>
                        <label><input type="radio" name="status" value="1" checked /> 正常</label>
                        <label><input type="radio" name="status" value="2" /> 禁用</label>
                    </td>
                </tr>
                <tr>
                    <td align="right">模板正文</td>
                    <td>
                        <textarea name="tplContent" cols="100" rows="20" class="textArea"></textarea>
                    </td>
                </tr>
                <tr>
                    <td align="right"></td>
                    <td align="left">
                        <input name="submit" class="btn" type="submit" value="提交"/> &nbsp; <input type="reset" class="btnGray" value="重置"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php } ?>