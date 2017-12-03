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

//        var_dump(self::$server);

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
        $this->getTables();
        $this->getExpFrame(self::$file->getTables());
        $this->getData(self::$file->getTables());
    }

    public function synchronize()
    {
        $this->setExpFrame();
        $this->setData(self::$file->getTables());
    }

    /**
     * 同步表名
     */
    private function getTables()
    {
        $sql = "SHOW TABLES ;";
        $res = self::$db->query($sql)->fetchAll();

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
            $res = self::$db->query($sql)->fetchAll();
            self::$file->setExportFrame($res);
        }
    }

    private function setExpFrame()
    {
        $res = self::$db->query(self::$file->getExportFrame())->fetchAll();
    }


    /**
     * 同步数据
     */
    private function getData($tables)
    {
        foreach ($tables as $value) {
            $min = getReadSize("min");
            $max = getReadSize("max");
            $row = self::$db->get($value["table_name"], "id", ["ORDER" => ["id" => "DESC"]]);
            for ($i = 0; $min < $row; $i++) {
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
                $data = self::$file->getData($filename);
                self::$db->debug()->insert($value["table_name"], $data);
            }
        }
    }

    /**
     * @param $table
     * @param $data
     */

    public function insert($table, $data)
    {
        $res = self::$db->insert($table, $data);
    }
}