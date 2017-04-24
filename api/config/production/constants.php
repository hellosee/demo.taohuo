<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


/*
 |--------------------------------------------------------------------------
 | 天天推AppID,AppSecret
 |--------------------------------------------------------------------------
 */
define('WX_APPID','wx74b311b6181bbaa0');
define('WX_SECRET','8b9c10363f86b9d19c3ef168a79a95a7');
define('QQ_APPID','1106029852');
define('QQ_APPKEY','VnPVLhV5SImnUG0D');

/*
 |--------------------------------------------------------------------------
 | 天天推基本配置
 |--------------------------------------------------------------------------
 */
define('PAGESIZE', 10); //分页数
define('WX_NOTIFY_URL', 'http://api.ttt.huiweishang.net/v1.0.0/SuperVip/wxNotify'); //微信支付回调地址
define('ALI_NOTIFY_URL', 'http://api.ttt.huiweishang.net/v1.0.0/SuperVip/aliNotify'); //支付宝支付回调地址

/*
 |--------------------------------------------------------------------------
 | 天天推版本更新配置
 |--------------------------------------------------------------------------
 */
define('ANDROID_VER_NEW', '1.0.0'); //Android新版本号
define('ANDROID_DOWNLOAD_URL', 'http://www.baidu.com'); //Android新版本下载地址
define('IOS_VER_NEW', '1.0.0'); //ios新版本号
define('IOS_DOWNLOAD', 'http://www.baidu.com'); //ios新版本下载地址
define('VER_NEW_DESC', '1、修正部分用户登录失败问题，修正部分用户会出现重复报课失败问题，优化支付功能，修复已知Bug~\n2、修正部分用户登录失败问题，修正部分用户会出现重复报课失败问题，优化支付功能，修复已知Bug~\n3、修正部分用户登录失败问题，修正部分用户会出现重复报课失败问题，优化支付功能，修复已知Bug~'); //更新内容

/*
 |--------------------------------------------------------------------------
 | 天天推网页配置
 |--------------------------------------------------------------------------
 */
define('T_HOST', 'http://wap.ttt.huiweishang.net/'); //网页地址
define('TTT_SECRET_DES','DD8E0593B531478599A76CC42325F81E'); //加密参数

/*
 |--------------------------------------------------------------------------
 | 天天推友盟配置
 |--------------------------------------------------------------------------
 */
define('ANDROID_APPKEY', '58b53795a325112ab500071c');
define('ANDROID_APPMASTERSECRET', 'ozomahymjv7hnmeo6s18fdqolkjv5bld');
define('IOS_APPKEY', '58b53729c8957624c10022a0');
define('IOS_APPMASTERSECRET', 'vgunerluljgvypeozfccqn91xwb6028j');