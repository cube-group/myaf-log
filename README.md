## LOG
[![Latest Stable Version](https://poser.pugx.org/cube-group/myaf-log/version)](https://packagist.org/packages/cube-group/myaf-log)
[![Total Downloads](https://poser.pugx.org/cube-group/myaf-log/downloads)](https://packagist.org/packages/cube-group/myaf-log)
[![License](https://poser.pugx.org/cube-group/myaf-log/license)](https://packagist.org/packages/cube-group/myaf-log)
### namespace
```
"Myaf\\Log\\": "src/"
```
### Log的使用(高性能，一次文件读写)
1. 调用init方法初始化，指定应用名称和日志路径
2. 调用debug|info|warn|error|fatal 方法打印相应级别的日志
3. 调用flush方法存储日志
```
//初始化日志
Log::init('l.eoffcn.com', '/data/log/l.eoffcn.com');
//debug日志
Log::debug("index/index", "18888888888", 9999, '{"status":"Y"}');
//info日志
Log::info("index/hello", "18888888888", 2002, 'hello日志内容');
//警告日志
Log::warn("index/hello", "18888888888", 2002, 'hello日志内容');
//错误日志
Log::error("index/world", "18888888888", 1001, 'world日志内容','其他信息','其他信息2','其他信息3');
//挂掉日志
Log::fatal("index/world", "18888888888", 1003, 'fatal日志内容','其他信息','其他信息2','其他信息3');
//日志压栈存储
Log::flush();
```
上面的Demo最终会一个日志文件.
* /data/log/l.eoffcn.com/20180511.txt


### Log使用(多次文件读写，防止程序中断没记到日志)
1. 调用init方法初始化，指定应用名称和日志路径
2. 调用setAutoFlush方法，传入true
3. 调用debug|info|warn|error|fatal 方法打印相应级别的日志
```
//初始化日志
Log::init('l.eoffcn.com', '/data/log/l.eoffcn.com');
//设置每次都刷日志
Log::setAutoFlush(true);
Log::debug("index/hello", "18888888888", 1001, 'world','其他信息','其他信息2','其他信息3');
Log::debug("index/hello", "18888888888", 1001, 'world','其他信息','其他信息2','其他信息3');
Log::error("index/hello", "18888888888", 1001, 'world','其他信息','其他信息2','其他信息3');
```



### 相关重要方法或属性
* Log::LOG_REQUEST_ID - 日志全局访问的requestId字段名称
* function (string $appName,string $logPath,string $timeZone='Asia/Shanghai',bool $debug=false) - 初始化日志系统
* function init(string $appName,string $logPath,string $timeZone='Asia/Shanghai',bool $debug=false) - 初始化日志系统
* function setAutoFlush($flush=false) - 设置每次记录都写日志, 默认关闭, 需要调用flush方法才写磁盘
* function getGlobalRequestId() - 获取日志全局访问的requestId
* function debug - 打印debug级别的日志
* function info - 打印核心级别的日志
* function warn - 打印警告级别的日志
* function error - 打印错误级别的日志
* function fatal - 打印挂掉级别的日志


### 日志组装结构
```
$date|$level|$ruid|$domain|$pid|$route|$uid|$code|$msg|$ext1|$ext2|$ext3
```
* $date: 日期(例如: 2018-05-10 20:00)
* $level: 日志级别(INFO ERROR DEBUG WARN)
* $ruid: 请求链唯一id(request unique id,例如: md5)
* $domain: 域(例如: l.eoffcn.com)
* $pid: 进程id或线程id或协程id
* $route: web路由(例如: /user/login 或 user/login)
* $uid: 用户类信息(例如: 相关用户id或用户名或手机号等)
* $code: 业务错误码(例如: 0或10000等)
* $msg: 业务错误信息(例如: ERR_USER_LOGIN)
* $ext1: 标准扩展字段1
* $ext2: 标准扩展字段2
* $ext3: 标准扩展字段3
