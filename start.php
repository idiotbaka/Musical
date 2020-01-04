<?php
/**
 * run with command
 * php start.php start
 */

ini_set('display_errors', 'on');
use Workerman\Worker;

if(strpos(strtolower(PHP_OS), 'win') === 0)
{
    exit("start.php not support Windows, please run in Linux.\n");
}

// 检查扩展
if(!extension_loaded('pcntl'))
{
    exit("Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

if(!extension_loaded('posix'))
{
    exit("Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

// 标记是全局启动
define('GLOBAL_START', 1);

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/application/musical.php';
require_once __DIR__ . '/application/musical_timer.php';
require_once __DIR__ . '/application/ws.php';
require_once __DIR__ . '/Service.php';


// 运行所有服务
Worker::runAll();