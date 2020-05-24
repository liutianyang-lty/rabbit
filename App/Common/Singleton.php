<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/25 0025
 * Time: 22:10
 */
namespace App\Common;

/**
 * 单例模式--代码高度复用
 * Trait Singleton
 * @package App\Common
 */
trait Singleton{
    private static $instance;

    static function getInstance(...$args)
    {
        if(!isset(self::$instance)){
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }
}