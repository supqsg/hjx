<?php
/**
 * Created by PhpStorm.
 * User: HJX
 * Date: 2017/8/18
 * Time: 16:28
 */
//error_reporting ( E_ERROR );
// /调试、找错时请去掉///前空格
// ini_set ( 'display_errors', true );
// error_reporting ( E_ALL );
// set_time_limit ( 0 );

define ( 'APP_DEBUG', false );   //定义debug的开启aa
define ( 'SHOW_ERROR', true );   //显示错误信息

date_default_timezone_set ( 'PRC' );//定义时区
if (version_compare ( PHP_VERSION, '5.3.0', '<' ))//判断PHP版本是否为5.3以上，这是因为thinphp的环境必须得5.3以上
    die ( 'Your PHP Version is ' . PHP_VERSION . ', But WeiPHP require PHP > 5.3.0 !' );

/**
 * 微信接入验证
 * 在入口进行验证而不是放到框架里验证，主要是解决验证URL超时的问题
 */
if (! empty ( $_GET ['echostr'] ) && ! empty ( $_GET ["signature"] ) && ! empty ( $_GET ["nonce"] )) {
    $signature = $_GET ["signature"];
    $timestamp = $_GET ["timestamp"];
    $nonce = $_GET ["nonce"];

    $tmpArr = array (
        'weiphp',
        $timestamp,
        $nonce
    );
    sort ( $tmpArr, SORT_STRING );
    $tmpStr = sha1 ( implode ( $tmpArr ) );

    if ($tmpStr == $signature) {
        echo $_GET ["echostr"];
    }
    exit ();
}
/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */

define ( 'IN_WEIXIN', false );
define ( 'DEFAULT_TOKEN', '-1' );

/**
 * 官方远程同步服务器地址
 * 应用于后台应用商店、在线升级，配置教程等功能
 */
define ( 'REMOTE_BASE_URL', 'http://wx.inrice.cn' );    //这步是因为weiphp的框架中嵌套的，现在被屏蔽掉了，他是通过访问模板文件来检测框架是否过期及其他。

// 网站根路径设置
define ( 'SITE_PATH', dirname ( __FILE__ ) );    //定义网站的根目录，如/alidata/www/InRiceWeixin
/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define ( 'APP_PATH', './Application/' );       //定义应用的根路径

if (! is_file ( SITE_PATH.'/Data/install.lock' )) {   //这是weiphp的验证，判断系统是不是已经被安装了，如果没有则会重定向到intall.php文件中
    header ( 'Location: ./install.php' );
    exit ();
}
/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/' );    //定义缓存路径

define ( 'APP_LOG_PATH', './Logs/' );      //定义log路径
/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';     //引入应用入口文件