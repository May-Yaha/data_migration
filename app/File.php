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
    /**
     *生成表名二进制文件
     */

    public function setTables($table)
    {
        file_put_contents(getFile("path") . getFile("tables_name"), json_encode($table));
    }

    /**
     * 获取表名
     * @return bool|mixed|string
     */
    public function getTables()
    {
        $filename = getFile("path") . getFile("tables_name");
        $table = $this->readFile($filename);
        return json_decode($table, true);
    }

    /**
     *生成框架二进制文件
     */
    public function setExportFrame($exp_frame)
    {
        foreach ($exp_frame as $value) {
            $sql = "SET NAMES utf8;" . PHP_EOL . "SET FOREIGN_KEY_CHECKS = 0;" . PHP_EOL . "DROP TABLE IF EXISTS \"" . $value[0] . "\";" . PHP_EOL;
            file_put_contents(getFile("path") . getFile("exp_frame"), $sql . $value[1] . ";" . PHP_EOL, FILE_APPEND);
        }
    }

    /**
     *获取框架
     * @return array
     */
    public function getExportFrame()
    {
        $filename = getFile("path") . getFile("exp_frame");
        $exp_frame = $this->readFile($filename);
        return $exp_frame;
    }

    /**
     * 同步数据
     * @param $data
     */
    public function setData($data, $filename)
    {
        file_put_contents(getFile("table_path") . $filename . ".bin", $data, FILE_APPEND);
    }

    /**
     * @param $filename
     * @return mixed
     * 获取数据
     */
    public function getData($filename)
    {
        $fp = fopen($filename, "r") or die("File not found!");
        while (!feof($fp)){
            yield fgets($fp);
        }
        fclose($fp);
    }

    /**
     * @param $filename
     * @return bool|string
     * 读取文件
     */
    public function readFile($filename)
    {
        $table_file = fopen($filename, "r") or die("Unable to open file!");
        $res = fread($table_file, filesize($filename));
        fclose($table_file);
        return $res;
    }
}