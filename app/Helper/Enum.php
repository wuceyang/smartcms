<?php
    namespace App\Helper;

    class Enum{
        //状态,1:正常,2:禁用
        const STATUS_ALL        = 0;
        const STATUS_NORMAL     = 1;
        const STATUS_DISABLED   = 2;

        const TOPICTAGS = [
                            0 => '官方',
                            1 => '推广',
                            2 => '置顶',
                            3 => '加精',
                          ];
    }