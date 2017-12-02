<?php
/**
 * Created by PhpStorm.
 * User: yaha
 * Date: 2017/11/29
 * Time: 下午2:53
 */

return [
    //数据库读取量
    "read_size" => [
        //每次读取的数据量
        "max" => 2,
        //从哪里开始读取
        "min" => 0,
    ],
    "file" => [
        //文件写入路径
        "path" => DIR_ROOT . "/temp/",
        //文件名
        "backup_name" => date("YmdH", time()) . "_back.bin",
    ],

    "table_option" => [
        "read_name" => "test",
        "write_name" => "test2"
    ]
];