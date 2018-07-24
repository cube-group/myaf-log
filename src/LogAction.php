<?php

namespace Myaf\Log;

use Exception;

/**
 * 标准动作类日志
 * Class LogAction
 * @package Myaf\Log
 */
class LogAction
{
    /**
     * 应用名称.
     * @var string
     */
    private static $app = '';
    /**
     * 应用版本号.
     * @var string
     */
    private static $version = '';
    /**
     * 日志存储目录.
     * @var string
     */
    private static $logPath = '';

    /**
     * 二维数组日志存储器.
     * @var array
     */
    private static $logs = array();

    /**
     * 开启将每次记录都写日志
     * @var bool
     */
    private static $autoFlush = false;

    /**
     * 初始化日志系统.
     *
     * @param $app string 应用名字
     * @param $version string 应用版本号
     * @param $logPath string 日志存储路径
     * @param $timeZone string 默认时区
     */
    public static function init($app = '', $version = '0.0.0', $logPath = '/data/log', $timeZone = 'Asia/Shanghai')
    {
        if (self::$app) {
            return;
        }

        if (!$app) {
            $app = getenv('APP_NAME');
        }
        self::$app = $app;
        self::$version = $version;
        self::$logPath = $logPath;
        date_default_timezone_set($timeZone);
    }

    /**
     * 设置自动刷日志
     *
     * @param bool $flag
     */
    public static function setAutoFlush($flag = false)
    {
        self::$autoFlush = $flag;
    }

    /**
     * 存储日志
     * @param $uid string 用户关联信息
     * @param $action string 统计动作类型
     * @param array|string $ext 扩展字段
     * @throws Exception
     */
    public static function save($uid, $action, $ext = '')
    {
        if (!self::$logPath) {
            throw new Exception('log not initialized');
        }
        $logContent = array();
        //time
        $logContent[] = date('Y-m-d H:i:s');
        //app
        $logContent[] = self::$app;
        //version
        $logContent[] = self::$version;
        //client
        $logContent[] = 0;
        //os
        $logContent[] = strtolower(PHP_OS);
        //ip
        $logContent[] = self::serverIp();
        //uid
        $logContent[] = $uid;
        //action
        $logContent[] = $action;
        //ext
        $logContent[] = $ext;
        $logString = self::getLogString($logContent);
        array_push(self::$logs, $logString);
        if (self::$autoFlush) {
            self::flush();
        }
    }


    /**
     * 将此次访问的的所有日志录入相关日志文件.
     * @return bool
     * @throws Exception
     */
    public static function flush()
    {
        if (empty(self::$logs)) {
            return true;
        }
        $logPath = self::$logPath;
        if (!realpath(self::$logPath)) {
            if (!mkdir($logPath, 0777, true)) {
                throw new Exception("can not mkdir {$logPath}");
            }
        }
        $logFileName = realpath($logPath) . DIRECTORY_SEPARATOR . date('Y-m-d') . '.bin';
        foreach (self::$logs as $item) {
            self::writeFile($logFileName, $item);
        }
        self::$logs = array();
        return true;
    }

    private static function serverIp()
    {
        return $_SERVER['SERVER_ADDR'];
    }

    /**
     * 写入日志文件
     *
     * @param string $logFile
     * @param string $content
     * @return true
     */
    private static function writeFile($logFile, $content)
    {
        $fp = fopen($logFile, 'a+');
        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $content . "\n");
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return true;
    }

    /**
     * 获取日志内容字符串
     *
     * @param $logContent
     * @return string
     */
    private static function getLogString($logContent)
    {
        foreach ($logContent as $k => $content) {
            if (!$content) {
                $content = '';
            }
            if (is_array($content)) {
                $logContent[$k] = http_build_query($content);
            }
        }
        $logString = implode('|', $logContent);
        return $logString;
    }
}
