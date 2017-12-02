<?php
/**
 * Created by PhpStorm.
 * User: yaha
 * Date: 2017/11/28
 * Time: 下午10:48
 */

function getDatabase($type="master",$key=""){
    if($type == "master"){
        $base = include __DIR__."/conf/master.php";
    }else{
        $base = include __DIR__."/conf/slave.php";
    }

    if($key){
        return $base[$key];
    }

    return $base;
}

function getReadSize($key=""){
    $base = include __DIR__."/conf/base.php";
    if($key){
        return $base["read_size"][$key];
    }
    return $base;
}

function getFile($key=""){
    $base = include __DIR__."/conf/base.php";
    if($key){
        return $base["file"][$key];
    }
    return $base;
}

function getTable($key=""){
    $base = include __DIR__."/conf/base.php";
    if($key){
        return $base["table_option"][$key];
    }
    return $base;
}