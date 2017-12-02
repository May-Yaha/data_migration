<?php
/**
 * Created by PhpStorm.
 * User: yaha
 * Date: 2017/11/28
 * Time: 下午10:29
 */

namespace App;

use App\Connect;

class File
{
    private static $connect;

    /**
     * 创建临时文件
     */

    public function __construct()
    {
        self::$connect = Connect::getConnect("slave");
    }

    public function tempFile()
    {

    }

    /**
     * 读取临时文件
     */
    public function readFile($filename)
    {
        $file = fopen($filename, "r+");
        while (!feof($file)) {
            $data = fgets($file);
            $data = json_decode($data, true);
//            var_dump($data);
            self::$connect->insert($data);
        }
        fclose($file);
    }
}