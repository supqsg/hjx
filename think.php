<?php
/**
 * Created by PhpStorm.
 * User: HJX
 * Date: 2017/8/18
 * Time: 17:14
 */

// 记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);
// 记录内存初始使用
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));
if(MEMORY_LIMIT_ON) $GLOBALS['_startUseMems'] = memory_get_usage();

// 版本信息
const THINK_VERSION     =   '3.2.3beta';

// URL 模式定义
const URL_COMMON        =   0;  //普通模式
const URL_PATHINFO      =   1;  //PATHINFO模式
const URL_REWRITE       =   2;  //REWRITE模式
const URL_COMPAT        =   3;  // 兼容模式

// 类文件后缀
const EXT               =   '.class.php';

// 系统常量定义
defined('THINK_PATH')   or define('THINK_PATH',     __DIR__.'/'); //定义当前入口文件所在的目录，即thinkphp框架的目录


/*
定义网站应用所在的目录。一般来说，在index.php文件中我们会定义一下应用路径，
如果说我们在index.php文件中没有定义APP_PATH，那么这里就会执行define('APP_PATH', //dirname($_SERVER['SCRIPT_FILENAME']).'/');这里会获取当前执行脚本的服务器端的绝对
路径。一般是index.php文件所在的目录。在我的电脑上即C:\Work\wamp64\www\，即网站的根目录。
*/

defined('APP_PATH')     or define('APP_PATH',       dirname($_SERVER['SCRIPT_FILENAME']).'/');
defined('APP_STATUS')   or define('APP_STATUS',     ''); // 应用状态 加载对应的配置文件
defined('APP_DEBUG')    or define('APP_DEBUG',      false); // 是否调试模式

/*
这里定义应用模式。到底什么是应用模式？
thinkphp框架为开发者提供了更改核心框架的机会。我们知道一个php框架的核心是定义一些重要的配置文件，引入一些重要的类库和函数以及适应当前主机环境的的php配置等。所以我们可以把这些核心中需要引入的文件代码分离出来，全部定义在一个php配置文件中，这个php配置文件就叫做模式的配置文件。然后我们根据模式不同去引用对应的配置文件，分析配置文件中的配置项，并运行之，从而达到改变框架核心的目的。
就拿最为常用的普通模式来说明。
我们在入口文件中定义了app_mode为common，然后在执行thinkphp的start方法的时候就会去分析定义的模式名称并且根据模式名称去mode文件夹下去寻找模式对应的common.php文件，
 // 读取应用模式
 $mode = include is_file(CONF_PATH.'core.php')?CONF_PATH.'core.php':MODE_PATH.APP_MODE.'.php';
在此文件中定义了用于扩展核心的配置项，在引入了这个文件后，我们可以查看start方法后续就会一一去引入配置项中所定义的各个文件。
与此同时，tp默认支持SAE环境，也就是说官方已经给我们写好了SAE环境的核心框架扩展的代码，只带适当的时候触发。
*/

if(function_exists('saeAutoLoader')){// 自动识别SAE环境
    defined('APP_MODE')     or define('APP_MODE',      'sae');
    defined('STORAGE_TYPE') or define('STORAGE_TYPE',  'Sae');
}else{
    defined('APP_MODE')     or define('APP_MODE',       'common'); // 应用模式 默认为普通模式
    defined('STORAGE_TYPE') or define('STORAGE_TYPE',   'File'); // 存储类型 默认为File
}

defined('RUNTIME_PATH') or define('RUNTIME_PATH',   APP_PATH.'Runtime/');   // 系统运行时目录
defined('LIB_PATH')     or define('LIB_PATH',       realpath(THINK_PATH.'Library').'/'); // 系统核心类库目录
defined('CORE_PATH')    or define('CORE_PATH',      LIB_PATH.'Think/'); // Think类库目录
defined('BEHAVIOR_PATH')or define('BEHAVIOR_PATH',  LIB_PATH.'Behavior/'); // 行为类库目录
defined('MODE_PATH')    or define('MODE_PATH',      THINK_PATH.'Mode/'); // 系统应用模式目录
defined('VENDOR_PATH')  or define('VENDOR_PATH',    LIB_PATH.'Vendor/'); // 第三方类库目录
defined('COMMON_PATH')  or define('COMMON_PATH',    APP_PATH.'Common/'); // 应用公共目录
defined('CONF_PATH')    or define('CONF_PATH',      COMMON_PATH.'Conf/'); // 应用配置目录
defined('LANG_PATH')    or define('LANG_PATH',      COMMON_PATH.'Lang/'); // 应用语言目录
defined('HTML_PATH')    or define('HTML_PATH',      APP_PATH.'Html/'); // 应用静态目录
defined('LOG_PATH')     or define('LOG_PATH',       RUNTIME_PATH.'Logs/'); // 应用日志目录
defined('TEMP_PATH')    or define('TEMP_PATH',      RUNTIME_PATH.'Temp/'); // 应用缓存目录
defined('DATA_PATH')    or define('DATA_PATH',      RUNTIME_PATH.'Data/'); // 应用数据目录
defined('CACHE_PATH')   or define('CACHE_PATH',     RUNTIME_PATH.'Cache/'); // 应用模板缓存目录
defined('CONF_EXT')     or define('CONF_EXT',       '.php'); // 配置文件后缀
defined('CONF_PARSE')   or define('CONF_PARSE',     '');    // 配置文件解析方法
defined('ADDON_PATH')   or define('ADDON_PATH',     APP_PATH.'Addon');


// 系统信息

/*
在magic_quotes_gpc=On的情况下，如果输入的数据有
单引号（’）、双引号（”）、反斜线（）与 NUL（NULL 字符）等字符都会被加上反斜线。这些转义是必须的，如果这个选项为off，那么我们就必须调用addslashes这个函数来为字符串增加转义。
在php5.4以后就废除了此特性。所以我们在以后就不要依靠这个特性了。为了使自己的程序不管服务器是什么设置都能正常执行。可以在程序开始用get_magic_quotes_runtime检测该设置的状态决定是否要手工处理，或者在开始（或不需要自动转义的时候）用set_magic_quotes_runtime(0)关掉该设置。
判断php版本，小于5.4的就手动关掉，定义常量。大于5.4直接定义常量为false。
*/
if(version_compare(PHP_VERSION,'5.4.0','<')) {
    ini_set('magic_quotes_runtime',0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
}else{
    define('MAGIC_QUOTES_GPC',false);
}

/*
php判断解析php服务是由那种服务器软件，是采用那种协议,PHP_ASPI是一个可以直接使用的常量。
如果是nginx+fastcgi环境，那么它的值是cgi-fcgi
如果是apache环境，那么他的值是apache2handler
如果是命令行的形式，那么它的值是cli
PHP_OS PHP所在的操作系统的名字，例如linux和WIN。
充分理解php的各种运行模式，参看：
http://www.jb51.net/article/37756.htm
http://www.cnblogs.com/liuzhang/p/3929198.html
*/

define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

/*
如果不是命令行模式的话，指定当前运行脚本的文件名。
*/
if(!IS_CLI) {
    // 当前文件名
    if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER['PHP_SELF']);
            define('_PHP_FILE_',    rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER['SCRIPT_NAME'],'/'));
        }
    }
    if(!defined('__ROOT__')) {
        $_root  =   rtrim(dirname(_PHP_FILE_),'/');
        define('__ROOT__',  (($_root=='/' || $_root=='\\')?'':$_root));
    }
}

// 加载核心Think类
require CORE_PATH.'Think'.EXT;
// 应用初始化
Think\Think::start();