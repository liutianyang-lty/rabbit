<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/24 0024
 * Time: 21:02
 */

$exchangeName = "trade";
$routingkey = "/trade"; // 路由
$queueName = "trade"; // 队列名称
try {
        //1.建立连接
        $connection = new AMQPConnection([
            'host' => '127.0.0.1',
            'port' => 5672,
            'vhost' => '/',
            'login' => 'guest',
            'password' => 'guest'
        ]);
        $connection->connect();

        //2. 建立通道
        $channel = new AMQPChannel($connection);

        //3. 创建队列
        $queue = new AMQPQueue($channel);
        $queue->setName($queueName);
        $queue->declareQueue();

        //4. 绑定路由监听
        $queue->bind($exchangeName, $routingkey);
        //没数据的时候就是阻塞状态，获取到数据才会执行
        $queue->consume(function ($envelope, $queue){
            var_dump($envelope);
            var_dump($envelope->getBody());
        });
    } catch (\Exception $e) {

    }