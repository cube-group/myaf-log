<?php

use Myaf\Log\LogAction;

require __DIR__ . '/../src/LogAction.php';

/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 2018/7/12
 * Time: 下午3:25
 */
class LogActionTest
{
    public function testCommon()
    {
        //初始化日志
        LogAction::init('test', '0.2.0', __DIR__);
        //存储日志
        LogAction::save("user-login", "123333", '', array('phone' => '15901214776', 'address' => '中公教育'));
        //日志压栈存储
        LogAction::flush();
    }

    /**
     * 设置每次都刷日志
     */
    public function testAutoFlush()
    {
        //初始化日志
        LogAction::init('test', '0.2.0', __DIR__);
        LogAction::setAutoFlush(true);
        //存储日志
        LogAction::save("123333", "user-buy-success");
    }
}

$test = new LogActionTest();
$test->testCommon();
$test->testAutoFlush();