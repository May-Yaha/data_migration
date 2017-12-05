<?php
/**
 * Created by PhpStorm.
 * User: yaha
 * Date: 2017/11/28
 * Time: 下午10:38
 */

require "./vendor/autoload.php";
require __DIR__ . "/getConf.php";

define("DIR_ROOT", __DIR__);

use App\Connect;

run("synchronize");
//run();
/**
 * @param string $option backup or synchronize
 */
function run($option = "backup")
{
    switch ($option) {
        case "backup" :
            backup();
            break;

        case "synchronize":
            synchronize();
            break;

        default :
            echo "Option not found!";
    }
}

function backup()
{
    $last_time = explode(" ", microtime());

    $connect = Connect::getConnect("master");
    $connect->backup();

    $end_time = explode(" ", microtime());

    echo round($end_time[0] + $end_time[1] - ($last_time[0] + $last_time[1]), 5) . " S";
}

function synchronize()
{
    $last_time = explode(" ", microtime());

    $connect = Connect::getConnect("slave");
    $connect->synchronize();

    $end_time = explode(" ", microtime());
    echo round($end_time[0] + $end_time[1] - ($last_time[0] + $last_time[1]), 5) . " S";
}
