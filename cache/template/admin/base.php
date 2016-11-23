<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>后台管理中心</title>
    <meta name="Copyright" content="Douco Design." />
    <link href="<?php echo $__THEME_PATH__;?>css/public.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="/popup/xcconfirm/css/xcConfirm.css"/>
    <?php function_exists("__css__") && __css__($params); ?>
</head>

<body style="height:100%">
    <div id="dcWrap">
        <!-- 头部导航开始 -->
        <div id="dcHead">
            <div id="head">
                <div class="logo">
                    <a href="index.html"><img src="<?php echo $__THEME_PATH__;?>images/dclogo.gif" alt="logo"></a>
                </div>
                <div class="nav">
                    <ul class="navRight">
                        <?php if(isset($userinfo)){ ?>
                        <li class="M noLeft">
                            <div>欢迎回来，<?php echo $userinfo["username"];?>
                                <div class="drop mUser">
                                    <a target="mainbox" href="/admin/user/reset-pwd">修改密码</a>
                                </div>
                            </div>
                        </li>
                        <li class="noRight"><a href="/admin/user/logout">退出登录</a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="clear"/>
            </div>
        </div>
        <!-- 头部导航结束 -->
        <div>
            <!-- 侧面导航开始 -->
            <?php if(isset($userinfo) && isset($menu)){ ?>
            <div id="dcLeft">
                <div id="menu" style="background-color: #EEEEEE;">
                    <ul>
                        <?php if(isset($menu["0"]) && is_array($menu["0"])){  foreach($menu["0"] as $k =>  $v){ ?>
                        <li data-id="<?php echo $v["id"];?>">
                            <i class="<?php echo $v["icon"];?>"></i><em><?php echo $v["name"];?></em>
                            <?php if($v["sub"]){ ?>
                            <ul class="collapse">
                            <?php if(isset($v["sub"]) && is_array($v["sub"])){  foreach($v["sub"] as $sk =>  $sv){ ?>
                            <li><a href="<?php echo $sv["url"];?>"><?php echo $sv["name"];?></a></li>
                            <?php }} ?>
                            </ul>
                            <?php } ?>
                        </li>
                        <?php }} ?>
                    </ul>
                </div>
            </div>
            <?php } ?>
            <!-- 侧面导航结束 -->
            <div id="dcMain">
               <?php function_exists("__content__") && __content__($params); ?>
            </div>
            <div class="clear"/>
        </div>
    </div>
    <div class="hide">
        <div id="msgbox">
            <ul class="popContainer">
                <li>
                    {infomsg}
                </li>
            </ul>
        </div>
        <div id="confirmbox">
            <ul class="popContainer">
                {confirmmsg}
            </ul>
        </div>
    </div>
</body>
<script type="text/javascript" src="<?php echo $__THEME_PATH__;?>js/jquery.min.js"></script>
<script type="text/javascript" src="/popup/xcconfirm/js/xcConfirm.js"></script>
<script type="text/javascript" src="/jqplugin/jquery.nicescroll.js"></script>
<script type="text/javascript">

    doc = document;

    function showMsg(content, title, options) {

        title  = title || "信息提示";

        var config = {
            title: title,
            btn:wxc.xcConfirm.btnEnum.ok,
        }

        if(options){

            for(var i in options){

                config[i] = options[i];
            }

            options = null;
        }

        var html = $('#msgbox').html().replace('{infomsg}', content);

        wxc.xcConfirm(html, wxc.xcConfirm.typeEnum.info, config);
    }

    function showConfirm(content, title, options){

        title  = title || "信息提示";

        var config = {
            title: title,
            btn:wxc.xcConfirm.btnEnum.okcancel,
        }

        if(options){

            for(var i in options){

                config[i] = options[i];
            }

            options = null;
        }

        var withHtml = content.replace(/<.+?>/, "") != content;

        var html = content;

        if(!withHtml){

            html = $('#confirmbox').html().replace('{confirmmsg}', content);            
        }

        wxc.xcConfirm(html, wxc.xcConfirm.typeEnum.custom, config);
    }

    function doRequest(url, data, callback){

        $.ajax({
            url: url,
            method: 'post',
            data: data,
            dataType: 'json',
            success: function(ret){

                if(callback && $.isFunction(callback)){

                    callback.call(null, ret);
                }
            },
            error: function(){

                showMsg("请求服务器数据发生错误", "错误提示");
            }
        });
    }

    function setSideBarHeight(){

        var ifm= document.getElementById("menu");

        if(!ifm) return;

        ifm.style.height = (document.documentElement.clientHeight - document.getElementById("dcHead").offsetHeight) + 'px';
    }

    function setSideBarStyle(jqdom){

        $(jqdom).niceScroll({ 
            cursorcolor: "#ccc",//#CC0071 光标颜色 
            cursoropacitymax: 1, //改变不透明度非常光标处于活动状态（scrollabar“可见”状态），范围从1到0 
            touchbehavior: false, //使光标拖动滚动像在台式电脑触摸设备 
            cursorwidth: "3px", //像素光标的宽度 
            cursorborder: "0", //     游标边框css定义 
            cursorborderradius: "5px",//以像素为光标边界半径 
            autohidemode: true //是否隐藏滚动条 
        });
    }

    function expandSideBar(){

        var cookiestr = document.cookie;

        if(cookiestr.indexOf("expandid") == -1){

            return;
        }

        var cookiepairs = cookiestr.split(';');

        var expandid    = 0;

        for(var i in cookiepairs){

            if(cookiepairs[i].indexOf("expandid=") > -1){

                expandid = parseInt(cookiepairs[i].replace('expandid=', ""), 10);

                break;
            }
        }

        $('#menu>ul>li').each(function(){

            if($(this).data('id') == expandid){

                $('ul', this).show();

                return false;
            }
        })
    }

    window.onresize = window.onload = function(){ setSideBarHeight(); expandSideBar(); setSideBarStyle('#menu'); setSideBarStyle('body')}

    $('#menu>ul>li').on('click', function(){

        var _this = this;

        $('#menu>ul>li').each(function(){

            if(this != _this){

                $('ul', this).slideUp('fast');

                return;
            }

            $('ul', this).slideDown('fast');

            document.cookie = "expandid=" + $(this).data('id') + '; path=/';
        });
    })

    $('.nav .noLeft').mouseenter(function(){

        $(this).find('div.drop').slideDown();
    })

    $('.nav .noLeft').mouseleave(function(){

        $(this).find('div.drop').slideUp();
    })

    <?php function_exists("__js__") && __js__($params); ?>
</script>

</html>