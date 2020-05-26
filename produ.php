<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/25 0025
 * Time: 22:59
 */
define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");

//echo BASE_PATH;
include __DIR__ . "/App/Lib/Rabbit.php";
$arr =  include(__DIR__ . "/Config/rabbit.php");
//$instance = Rabbit::getInstance()->listen("jiaohuanji", "queue1");
var_dump(Rabbit::getInstance());die;
$instance->publish("请求已发送");
