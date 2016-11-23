<?php
    date_default_timezone_set('Asia/Chongqing');
    
    $passwd  = '111111';
    
    $account = 'admin';
    
    echo md5($passwd . '|' . md5($account . $passwd));