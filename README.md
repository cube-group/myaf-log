## LOG
[![Latest Stable Version](https://poser.pugx.org/cube-group/myaf-log/version)](https://packagist.org/packages/cube-group/myaf-log)
[![Total Downloads](https://poser.pugx.org/cube-group/myaf-log/downloads)](https://packagist.org/packages/cube-group/myaf-log)
[![License](https://poser.pugx.org/cube-group/myaf-log/license)](https://packagist.org/packages/cube-group/myaf-log)
### namespace
```
"Myaf\\Log\\": "src/"
```
### 1.业务日志
>容器内日志路径:
```shell
//业务级日志自动收集
/data/log/2018-05-28.txt
```
>宿主机内日志路径:
```shell
//业务级日志自动收集
/data/log/app/2018-05-28.txt
```

>日志字段说明:
* $time: 日期(例如: 2018-05-10 20:00)
* $ip: 用户请求的ip地址
* $level: 日志级别(INFO ERROR DEBUG WARN)
* $ruid: 请求链唯一id(request unique id,例如: md5)
* $domain: 域(例如: l.eoffcn.com)
* $pid: 进程id或线程id或协程id
* $route: web路由(例如: /user/login 或 user/login)
* $uid: 用户类信息(例如: 相关用户id或用户名或手机号等)
* $code: 业务错误码(例如: 0或10000等)
* $msg: 业务错误信息(例如: ERR_USER_LOGIN)
* $ext: 扩展字段

>业务标准日志Demo:
```shell
2018-05-08 20:00|192.168.0.10|ERROR|1q2w3e4r522|l.eoffcn.com|7732|/user/login|1590214776|9800|ERR_SOMETHING|xxx
```

>业务标准日志打印级别:
* DEBUG 用过调试，级别最低，可以随意的使用于任何觉得有利于在调试时更详细的了解系统运行状态的东东；
* INFO 用于打印程序应该出现的正常状态信息， 便于追踪定位；
* WARN 表明系统出现轻微的不合理但不影响运行和使用；
* ERROR 表明出现了系统错误和异常，无法正常完成目标操作。
* FATAL 相当严重，可以肯定这种错误已经无法修复，并且如果系统继续运行下去的话后果严重

>日志工具包:
SDK地址 https://github.com/cube-group/myaf-log

>composer安装:
```shell
#使用国内镜像
composer config -g repo.packagist composer https://packagist.phpcomposer.com
#安装
composer require cube-group/myaf-log
```
>composer.json:
```json
{require:{cube-group/myaf-log: "*"}}
```
>Log使用:
```
//初始化日志
Log::init('app');
//支持info、warn、error、fatal类日志
Log::info("路由地址", "跟用户相关的数据", "业务线错误码", "错误码对应的错误信息");
//info日志
Log::info("/user/login", "$uid/$phone/$otherAboutUser", $code, $msg);
//demo,日志打印函数(ext尽量使用array)
Log::info("/user/login", "24325", 9999, "ERR_USER_LOGIN", "啊哈哈", [1,2,3], ['key' => 'what you want"]);
//日志压入存储
Log::flush();
```
### 2.统计日志
>容器内日志路径:
```shell
//统计类日志自动收集
/data/log/2018-05-28.bin
```
>宿主机内日志路径:
```shell
//统计类日志自动收集
/data/log/app/2018-05-28.bin
```
>日志字段说明:
* $time: 日期(例如: 2018-05-10 20:00)
* $app: 应用名称
* $version: 应用版本号,如:0.1.2
* $client: 终端代码 （0：backend, 1：pc，2：web，10：android,11：ios，20：微信公众号，21：微信服务号，22：微信小程序）
* $os: 操作系统或设备（win-10.0.0、android-7.0.0、ios-11.0.3、mac-10.13.5、linux、darwin)
* $ip: 用户ip（符合ip标准）
* $uid: 用户类信息(例如: 相关用户id或用户名或手机号等)
* $action: 行为类代码,如:user-login
* $ext: 扩展字段（必须为query string格式，例如：a=1&b=2&...）

>业务标准日志Demo:
```shell
2018-07-12 15:53:39|test|0.2.0|0|darwin|127.0.0.1|123333|user-login|phone=15901214776&address=%E4%B8%AD%E5%85%AC%E6%95%99%E8%82%B2
```

>日志工具包:
SDK地址 https://github.com/cube-group/myaf-log

>composer安装:
```shell
#使用国内镜像
composer config -g repo.packagist composer https://packagist.phpcomposer.com
#安装
composer require cube-group/myaf-log
```
>composer.json:
```json
{require:{cube-group/myaf-log: "*"}}
```
>Log使用:
```
//初始化日志
LogAction::init('test', '0.2.0');
//存储日志
LogAction::save("123333", "user-login", array('phone' => '15901214776','address'=>'中公教育'));
//日志压栈存储
LogAction::flush();
```

