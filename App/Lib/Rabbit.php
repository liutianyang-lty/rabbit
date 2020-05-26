<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/25 0025
 * Time: 22:01
 */
//namespace App\Lib;

include "../Common/CreateObject.php";
include "../../Config/rabbit.php";
//include __DIR__ . "/App/Common/CreateObject.php";
//include __DIR__ . "/Config/rabbit.php";

class Rabbit implements CreateObject{

    //保存类实例的静态成员变量
    static private $_instance;
    static private $_channel;
    static private $connection ;
    static private $route = 'key_1'; // 路由
    static private $q ;
    static private $exchange ; // 交换机名
    static private $queue; // 队列名

    /**
     * @return Rabbit
     * @throws \AMQPConnectionException
     */
    public static function getInstance(){
        global $arr;
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($arr['RabbitMq']);
            return self::$_instance;
        }
        return self::$_instance;
    }

    /**
     * 私有化构造函数
     * Rabbit constructor.
     * @param $conn
     * @throws \AMQPConnectionException
     */
    private function __construct($conn)
    {
        // 创建连接和channel
        $conn = new \AMQPConnection($conn);
        if (!$conn->connect()) { // 需要手动连接
            die("Cannot connect to the broker!\n");
        }

        self::$_channel = new \AMQPChannel($conn);
        self::$connection = $conn;
    }

    /**
     * @param $exchangeName // 交换机名称
     * @param $queueName // 队列名称
     * @return mixed
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function listen($exchangeName, $queueName)
    {
        self::$queue = $queueName;
        return $this->setExchange($exchangeName, $queueName);
    }

    /**
     * @param $exchangeName // 交换机名称
     * @param $queueName // 队列名称
     * @return mixed
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function setExchange($exchangeName, $queueName)
    {
        // 创建交换机
        $ex = new \AMQPExchange(self::$_channel);
        self::$exchange = $ex;
        $ex->setName($exchangeName);

        $ex->setType(AMQP_EX_TYPE_DIRECT); // direct类型
        $ex->setFlags(AMQP_DURABLE); // 持久化
        $ex->declareExchange(); // 声明交换机

        return self::setQueue($queueName, $exchangeName);
    }

    /**
     * @param $queueName
     * @param $exchangeName
     * @return mixed
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    private static function setQueue($queueName, $exchangeName)
    {
        // 创建队列
        $q = new \AMQPQueue(self::$_channel);
        $q->setName($queueName);
        $q->setFlags(AMQP_DURABLE); // 持久化
        $q->declareQueue(); // 声明队列

        // 绑定队列和交换机
        $routingKey = self::$route;
        $q->bind($exchangeName,  $routingKey); // 绑定路由监听
        self::$q = $q;
        return(self::$_instance);
    }

    /**
     * 关闭连接
     */
    private static function closeConn(){
        self::$connection->disconnect();
    }

    /**
     * 消费者
     * @param $func
     * @param bool $autoAck
     * @return bool
     * function processMessage($envelope, $queue) {
     *      $msg = $envelope->getBody();
     *      echo $msg."\n"; //处理消息
     *      $queue->ack($envelope->getDeliveryTag());//手动应答
     * }
     */
    public function run($func, $autoAck = true)
    {
        if (!$func || !self::$q) return False;

        while(True){
            if ($autoAck) {
                if(!self::$q->consume($func, AMQP_AUTOACK)){
                    //self::$q->ack($envelope->getDeliveryTag());
                    //失败之后会默认进入 noack 队列。下次重新开启会再次调用，目前还不清楚 回调配置应该这里做一个失败反馈　　　　　　　　　　　　
                }
            }
            self::$q->consume($func);
        }
    }

    /**
     * 生产者
     * @param $msg
     */
    public function publish($msg)
    {
        while (1) {
            sleep(1);
            if (self::$exchange->publish(date('H:i:s') . "用户" . "注册", self::$route)) {
                //写入文件等操作
                echo $msg;
            }
        }
    }
}
