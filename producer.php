<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/24 0024
 * Time: 19:28
 */
$exchangeName = "trade";
$routingkey = "/trade"; //路由

try {
    //1.建立连接
    $connection = new AMQPConnection([
        'host' => '127.0.0.1',
        'port' => 5672,
        'vhost' => '/',
        'login' => 'guest',
        'password' => 'guest'
    ]);

    var_dump($connection->connect());

    //2. 建立通道
    $channel = new AMQPChannel($connection);

    //3. 创建交换机
    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchangeName);
    $exchange->setType(AMQP_EX_TYPE_DIRECT);
    $exchange->declareExchange();
    $data = [
        'msg_type' => 'trade',
        'tid' => uniqid(),
    ];

    //4. 绑定路由关系发送消息
    $exchange->publish(json_encode($data), $routingkey);

    //5.
} catch (\Exception $e) {

}
