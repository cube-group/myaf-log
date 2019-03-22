<?php
namespace Myaf\Log;

/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 2019/3/22
 * Time: 10:20 AM
 */
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Log
 * 标准数据日志
 */
class LogAction
{
    /**
     * @var string
     */
    private static $_logPath = '';
    /**
     * @var string
     */
    private static $_app;
    /**
     * @var string
     */
    private static $_version;
    /**
     * @var Logger
     */
    private static $_instance;

    /**
     * @var Logger
     */
    private $_logger;

    public function __construct()
    {
        $output = "%datetime%|%context.app%|%context.version%|%context.client%|%context.os%|%context.ip%|%context.from%|%context.uuid%|%context.action%|%context.uid%|%message%\n";
        $formatter = new LineFormatter($output, 'Y-m-d H:i:s');

        $stream = new StreamHandler(realpath(self::$_logPath) . '/' . date('Y-m-d') . '.bin');
        $stream->setFormatter($formatter);

        $this->_logger = new Logger('my_logger');
        $this->_logger->pushHandler($stream);
    }

    /**
     * 初始化全局配置变量
     * @param string $app
     * @param string $logPath
     * @param string $timeZone
     * @param bool $debug
     */
    public static function init($app = 'text', $version = '', $logPath = '/data/log', $timeZone = 'Asia/Shanghai')
    {
        if (!self::$_instance) {
            date_default_timezone_set($timeZone);
            self::$_app = $app;
            self::$_logPath = realpath($logPath);
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * log append
     * @param $level
     * @param string $route
     * @param string $uid
     * @param int $code
     * @param string $msg
     * @param string $ext
     */
    public function append($action, $uid, $ip, $ext)
    {
        if (is_array($ext)) {
            $ext = json_encode($ext, JSON_UNESCAPED_UNICODE);
        } else if (!is_string($ext)) {
            $ext = '';
        }
        $this->_logger->addInfo($ext, [
            'datetime' => date('Y-m-d H:i:s'),
            'app' => self::$_app,
            'version' => self::$_version,
            'client' => 0,
            'os' => strtolower(PHP_OS),
            'ip' => $ip ? $ip : self::requestIp(),
            'from' => '',
            'uuid' => '',
            'uid' => $uid,
            'action' => $action,
            'ext' => $ext,
        ]);
    }

    /**
     * 获取访问的用户IP
     * @return string
     */
    private static function requestIp()
    {
        if (isset($_SERVER['HTTP_REMOTEIP'])) {
            return $_SERVER['HTTP_REMOTEIP'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }


    /**
     * @param string $action
     * @param string $uid
     * @param string $ip
     * @param null $ext
     */
    public static function save($action = '', $uid = '', $ip = '', $ext = null)
    {
        self::init()->append($action, $uid, $ip, $ext);
    }


    public static function setAutoFlush($flag = true)
    {
        //...todo
    }

    public static function flush()
    {
        //...todo
    }
}