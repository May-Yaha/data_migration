<?php
/**
 * Created by PhpStorm.
 * User: yaha
 * Date: 2017/11/28
 * Time: 下午10:34
 */

namespace App;

use Medoo\Medoo;
use App\File;

class Connect
{
    private static $connect;
    private static $db;
    private static $server;
    private static $file;

    private function __construct()
    {
        self::$file = new File();
        self::$db = new Medoo(self::$server);
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    private function __wakeup()
    {
        // TODO: Implement __wakeup() method.
    }

    public static function getConnect($server)
    {
        if ($server) {
            self::$server = getDatabase($server);
        } else {
            self::$server = $server;
        }

        if (self::$connect) {
            return self::$connect;
        }
        return self::$connect = new self();
    }

    public function getConn()
    {
        return self::$db;
    }

    public function backup()
    {
        echo "获取表名..." . PHP_EOL;
        $this->getTables();
        echo "完成" . PHP_EOL . "获取表结构..." . PHP_EOL;
        $this->getExpFrame(self::$file->getTables());
        echo "完成" . PHP_EOL . "获取数据..." . PHP_EOL;
        $this->getData(self::$file->getTables());
        echo "获取数据完成，请执行下一步操作";
    }

    public function synchronize()
    {
        echo "同步表结构..." . PHP_EOL;
        $this->setExpFrame();
        echo "完成" . PHP_EOL . "同步数据..." . PHP_EOL;
        $this->setData(self::$file->getTables());
        echo "同步数据成功！";
    }

    /**
     * 同步表名
     */
    private function getTables()
    {
        $sql = "SHOW TABLES ;";
        try{
            $res = self::$db->query($sql)->fetchAll();
        }catch (\PDOException $exception){
            echo "PDO Error：".$exception->getMessage();
        }

        foreach ($res as $k => $v) {
            $arr[$k]["table_name"] = $v[0];
        }
        self::$file->setTables($arr);
    }

    /**
     * 同步表结构
     * @return array
     */
    private function getExpFrame($tables)
    {
        foreach ($tables as $value) {
            $sql = "show create table " . $value["table_name"] . ";";

            try{
                $res = self::$db->query($sql)->fetchAll();
            }catch (\PDOException $exception){
                echo "PDO Error：".$exception->getMessage();
            }
            self::$file->setExportFrame($res);
        }
    }

    private function setExpFrame()
    {
        try{
            self::$db->query(self::$file->getExportFrame())->fetchAll();
        }catch (\PDOException $exception){
            echo "PDO Error：".$exception->getMessage();
        }
    }


    /**
     * 同步数据
     */
    private function getData($tables)
    {
        foreach ($tables as $value) {
            $min = getReadSize("min");
            $max = getReadSize("max");
            $row = self::$db->count($value["table_name"], "id");

            for ($i = 1; $min <= $row; $i++) {
                $min = $i * $max;
                $res = self::$db->select($value["table_name"], "*", [
                    "LIMIT" => [$min, $max]
                ]);
                $str = json_encode($res) . PHP_EOL;
                self::$file->setData($str, $value["table_name"]);
            }
        }
    }

    private function setData($tables)
    {
        foreach ($tables as $value) {
            $filename = getFile("table_path") . $value["table_name"] . ".bin";
            if (is_file($filename)) {
                echo $value["table_name"] . PHP_EOL;

                foreach (self::$file->getData($filename) as $val){
                    self::$db->insert($value["table_name"], json_decode($val,true));
                }
            }
        }
    }
}