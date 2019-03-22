<?php
namespace Myaf\Log;

/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 2019/3/22
 * Time: 10:20 AM
 */
use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Log
 * 标准业务日志
 */
class Log
{
    /**
     * 日志唯一id.
     */
    const LOG_REQUEST_ID = 'requestUniqueId';

    /**
     * @var bool
     */
    private static $_debug = false;
    /**
     * @var string
     */
    private static $_requestId = '';
    /**
     * @var string
     */
    private static $_logPath = '';
    /**
     * @var string
     */
    private static $_app;
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
        $output = "%datetime%|%context.ip%|%context.level%|%context.ruid%|%context.app%|%context.pid%|%context.route%|%context.uid%|%context.code%|%context.msg%|%message%\n";
        $formatter = new LineFormatter($output, 'Y-m-d H:i:s');

        $stream = new StreamHandler(realpath(self::$_logPath) . '/' . date('Y-m-d') . '.txt');
        $stream->setFormatter($formatter);
        $stream->setLevel(self::$_debug ? Logger::DEBUG : Logger::INFO);

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
    public static function init($app = 'text', $logPath = '/data/log', $timeZone = 'Asia/Shanghai', $debug = false)
    {
        if (!self::$_instance) {
            date_default_timezone_set($timeZone);
            self::$_app = $app;
            self::$_debug = $debug;
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
    public function append($level, $route = '', $uid = '', $code = 0, $msg = '', $ext = '')
    {
        if (is_array($ext)) {
            $ext = json_encode($ext, JSON_UNESCAPED_UNICODE);
        } else if (!is_string($ext)) {
            $ext = '';
        }
        $funcName = 'addInfo';
        switch ($level) {
            case 'DEBUG':
                $funcName = 'addDebug';
                break;
            case 'INFO':
                $funcName = 'addInfo';
                break;
            case 'WARN':
                $funcName = 'addWarning';
                break;
            case 'ERROR':
                $funcName = 'addError';
                break;
            case 'FATAL':
                $funcName = 'addEmergency';
                break;
        }
        $this->_logger->$funcName($ext, [
            'datetime' => date('Y-m-d H:i:s'),
            'ip' => self::requestIp(),
            'level' => $level,
            'ruid' => self::getGlobalRequestId(),
            'app' => self::$_app,
            'pid' => getmygid(),
            'route' => $route,
            'uid' => $uid,
            'code' => $code,
            'msg' => $msg
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
     * 获取全局唯一请求id.
     * 如果未设置则从$_GET、$_POST参数中区搜索.
     * 如果还未找到则直接创建.
     *
     * @return string
     */
    public static function getGlobalRequestId()
    {
        if (!self::$_requestId) {
            try {
                if (isset($_GET[self::LOG_REQUEST_ID]) && $_GET[self::LOG_REQUEST_ID]) {
                    self::$_requestId = $_GET[self::LOG_REQUEST_ID];
                } else if (isset($_POST[self::LOG_REQUEST_ID]) && $_POST[self::LOG_REQUEST_ID]) {
                    self::$_requestId = $_POST[self::LOG_REQUEST_ID];
                } else {
                    self::$_requestId = uniqid();
                }
            } catch (Exception $e) {
                self::$_requestId = uniqid();
            }
        }
        return self::$_requestId;
    }

    /**
     * debug日志
     *
     * @param string $route 路由
     * @param string $uid 用户类信息(例如: 相关用户id或用户名或手机号等)
     * @param string $code 业务错误码
     * @param string $msg 业务错误信息
     * @param string|array|number $ext 标准扩展字段
     * @throws Exception
     */
    public static function debug($route = '', $uid = '', $code = '', $msg = '', $ext = '')
    {
        self::init()->append('DEBUG', $route, $uid, $code, $msg, $ext);
    }


    /**
     * 常规日志
     *
     * @param string $route 路由
     * @param string $uid 用户类信息(例如: 相关用户id或用户名或手机号等)
     * @param string $code 业务错误码
     * @param string $msg 业务错误信息
     * @param string|array|number $ext 标准扩展字段
     * @throws Exception
     */
    public static function info($route = '', $uid = '', $code = '', $msg = '', $ext = '')
    {
        self::init()->append('INFO', $route, $uid, $code, $msg, $ext);
    }


    /**
     * 警告日志
     *
     * @param string $route 路由
     * @param string $uid 用户类信息(例如: 相关用户id或用户名或手机号等)
     * @param string $code 业务错误码
     * @param string $msg 业务错误信息
     * @param string|array|number $ext 标准扩展字段
     * @throws Exception
     */
    public static function warn($route = '', $uid = '', $code = '', $msg = '', $ext = '')
    {
        self::init()->append('WARN', $route, $uid, $code, $msg, $ext);
    }

    /**
     * 挂掉日志
     *
     * @param string $route 路由
     * @param string $uid 用户类信息(例如: 相关用户id或用户名或手机号等)
     * @param string $code 业务错误码
     * @param string $msg 业务错误信息
     * @param string|array|number $ext 标准扩展字段
     * @throws Exception
     */
    public static function fatal($route = '', $uid = '', $code = '', $msg = '', $ext = '')
    {
        self::init()->append('FATAL', $route, $uid, $code, $msg, $ext);
    }


    /**
     * 错误日志
     *
     * @param string $route 路由
     * @param string $uid 用户类信息(例如: 相关用户id或用户名或手机号等)
     * @param string $code 业务错误码
     * @param string $msg 业务错误信息
     * @param string|array|number $ext 标准扩展字段
     * @throws Exception
     */
    public static function error($route = '', $uid = '', $code = '', $msg = '', $ext = '')
    {
        self::init()->append('ERROR', $route, $uid, $code, $msg, $ext);
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