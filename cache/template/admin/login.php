<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>

<body>
    <div id="login">
        <div class="loginbox">
            <div>
                <form action="/admin/user/login" method="post">
                    <div class="logintitle">管理登录</div>
                    <ul>
                        <li class="inpLi"><b>用户名：</b><input name="account" type="text" class="loginipt" value=""></li>
                        <li class="inpLi"><b>密码：</b><input name="passwd" type="password" class="loginipt" value=""></li>
                        <li class="inpLi">
                            <b>验证码：</b><input name="captcha" class="loginipt" type="text" class="captcha">
                        </li>
                        <li>
                            <input type="submit" name="submit" class="btn" value="登录">
                            <img width="70" id="vcode" src="http://demo.douco.com/captcha.php" alt="启用验证码" border="1" onClick="refreshimage()" title="看不清？点击更换另一个验证码。">
                            <label>点击图片更换</label>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>


    <?php function __css__($params){ extract($params);?>
    <style type="text/css">
        body {
            background-color: #FFF;
        }
        
        .loginbox {
            padding: 10px 30px;
            border: 1px solid #CCC;
            background-color: #EEEEEE;
        }
        
        .loginbox .logintitle {
            padding: 10px 0px;
            height: 34px;
            line-height: 34px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            letter-spacing: 6px;
        }
        
        .loginbox .loginipt {
            height: 100%;
            width: 227px;
            padding-left: 5px;
        }
        
        .captchaPic img {
            margin-top: 5px;
        }
        
        form img {
            vertical-align: middle;
            margin-left: 50px;
        }
    </style>
    <?php } ?>

    <?php function __js__($params){ extract($params);?>
    <script type="text/javascript">
        $('form').on('submit', function(){
            $.ajax({
                    url: $(this).attr('action'), 
                    data: $(this).serialize(), 
                    dataType: 'json',
                    method: 'post',
                    success: function(ret){
                        if(ret.code == 200){
                            top.location.href="/admin";
                            return;
                        }
                        showMsg("登录提示", ret.message || "登录失败，请检查输入");
                        }, 
                    error: function(){
                        showMsg("登录提示", "登录时发生错误，请联系管理员");
                    }
            });

            return false;
        })

        function refreshimage() {
            var cap = document.getElementById('vcode');
            cap.src = cap.src + '?' + Math.random(); 
        }
    </script>
    <?php } ?>