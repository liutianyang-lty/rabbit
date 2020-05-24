<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/25 0025
 * Time: 22:59
 */
include __DIR__ . "/App/Lib/Rabbit.php";
use App\Lib\Rabbit;

$instance = Rabbit::getInstance()->listen("jiaohuanji", "queue1");
$instance->publish("请求已发送");
