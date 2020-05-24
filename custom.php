<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/25 0025
 * Time: 23:02
 */

include __DIR__ . "/App/Lib/Rabbit.php";
use App\Lib\Rabbit;
$fun = function ($envelope, $queue){

    //消费 （刚取出数据，业务终止了；取出消息，执行不成功）怎么保证消息的可靠性
    //这里是写业务逻辑的地方

    //ack 应答机制
    var_dump($envelope->getBody());
};

Rabbit::getInstance()->listen("jiaohuanji, queue1")->run($fun);