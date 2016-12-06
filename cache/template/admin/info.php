<?php include $__tplbasedir__ . "admin/base.php";?>

<?php function __content__($params){ extract($params);?>
    <div style="maring-left:0px;background-color:#FFF;padding-top:40px; zoom:1;">
        <div id="urHere">信息提示</div>
        <div id="index" class="mainBox" style="padding-top:18px;height:auto!important;height:550px;min-height:550px;">
            <div class="warning" style="line-height: 30px;">
                <p><?php echo $message;?></p>
                </p>系统会在<label id="timer">3</label>秒后自动跳转，如果页面没有自动跳转，请<a href="<?php echo $uri;?>">点击这里</a></p>
            </div>
        </div>
    </div>
<?php } ?>

<?php function __js__($params){ extract($params);?>
<script type="text/javascript">
    var timer = document.getElementById('timer');

    var _timer = setInterval(function(){
        autoRedirect(timer);
    }, 1000);

    function autoRedirect(timer){

        var seconds = parseInt(timer.innerHTML, 10);

        if(seconds <= 0){

            clearInterval(_timer);

            var pNode = timer.parentNode;

            uri = '<?php echo $uri;?>';

            if(uri){

                <?php if(substr($uri, 0, 11) == 'javascript:'){  echo $uri;?>;
                <?php }else{ ?>
                location.href = uri;
                <?php } ?>
                return;
            }

            return;
        }

        timer.innerHTML = seconds - 1;
    }
</script>
<?php } ?>