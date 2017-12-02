<?php
/**
 * Created by PhpStorm.
 * User: yaha
 * Date: 2017/11/28
 * Time: 下午10:34
 */

namespace App;

use Medoo\Medoo;

class Connect
{
    private static $connect;
    private static $db;
    private static $server;

    private function __construct()
    {
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
        $max = getReadSize(max);
        $min = getReadSize(min);

        $row = self::$db->get(getTable("read_name"), "id", ["ORDER" => ["id" => "DESC"]]);

        $tables = $this->export_frame();

        foreach ($tables as $k => $v) {
            $arr[$k]["table_name"] = $v[0];
        }

        echo json_encode($arr);
        file_put_contents("./temp/tables.bin", json_encode($arr));


        for ($i = 0; $min <= $row; $i++) {
            $min = $i * $max;
            $res = self::$db->select(getTable("read_name"), "*", [
                "LIMIT" => [$min, $max]
            ]);
            $str = json_encode($res) . PHP_EOL;

            file_put_contents(getFile("path") . getFile("backup_name"), $str, FILE_APPEND);
        }
    }

    public function export_frame()
    {
        $sql = "SHOW TABLES ;";
        $res = self::$db->query($sql)->fetchAll();
        return $res;
    }

    public function insert($data)
    {

        self::$db->insert(getTable("write_name"), $data);
    }
}