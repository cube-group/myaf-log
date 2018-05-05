## LOG

### namespace
```
"Myaf\\Log\\": "src/"
```
### Log的使用
```
//Log初始化
Log::init('app-name', '/data/log/app', 'Asia/Shanghai', false);
//debug日志
Log::info('dir1',__FILE__,'HelloWorld');
Log::info('dir2',__FILE__,'Json:',['status'=>'Y']);
//错误日志
Log::error('dir2',__FILE__,'SendData:',['a'=>'123123']);
//...logic
//日志压栈存储
Log::flush();
```
上面的Demo最终会生成两个日志文件.
* /data/log/app/dir1/20170311.txt
* /data/log/app/dir2/20170311.txt
### 相关重要方法或属性
* Log::LOG_REQUEST_ID - 日志全局访问的requestId字段名称
* function init(string $appName,string $logPath,string $timeZone='Asia/Shanghai',bool $debug=false) - 初始化日志系统
* function init(string $appName,string $logPath,string $timeZone='Asia/Shanghai',bool $debug=false) - 初始化日志系统
* function setAutoFlush($flush=false) - 设置每次记录都写日志, 默认关闭, 需要调用flush方法才写磁盘
* function getGlobalRequestId():string - 获取日志全局访问的requestId
* function debug(string $dirName,string $fileName,...$args) - 打印debug级别的日志
* function info(string $dirName,string $fileName,...$args) - 打印核心级别的日志
* function warn(string $dirName,string $fileName,...$args) - 打印警告级别的日志
* function error(string $dirName,string $fileName,...$args) - 打印错误级别的日志
* function fatal(string $dirName,string $fileName,...$args) - 打印挂掉级别的日志
* 注意:$dirName要求只能为1层(为提高日志检索效率)!
### 日志组装结构
```
[年-月-日 时:分:秒][进程id][文件地址][类别][app-name][requestId]...
```
