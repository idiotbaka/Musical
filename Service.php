<?php
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \GatewayWorker\Register;
use \Workerman\Connection\AsyncTcpConnection;
use \Workerman\Autoloader;
use \Metowolf\Meting;

// 全局启动验证
if(!defined('GLOBAL_START'))
{
    exit("Run start.php to start Musical.\n");
}

// 数据库
global $db;
$db = new \Workerman\MySQL\Connection(DB_HOST, DB_PORT, DB_USER, DB_PWD, DB_NAME);

// 网易云音乐API
global $netease_api;
$netease_api = new Meting('netease');

// Musical Worker
$musical_worker = new Worker();
$musical_worker->name = 'MusicalWorker';

$musical_worker->onWorkerStart = function()
{
    
};

// 网站
$web = new WebServer('http://0.0.0.0:'.WEB_PORT);
$web->addRoot(WEB_DOMAIN, __DIR__.'/web');

$register = new Register('text://0.0.0.0:'.REGISTER_PORT);

// Bussiness Worker
$worker = new BusinessWorker();
$worker->name = 'MusicalBusinessWorker';
$worker->count = 1;
$worker->registerAddress = '127.0.0.1:'.REGISTER_PORT;

// gateway
$gateway = new Gateway('Websocket://0.0.0.0:'.WEBSOCKET_PORT);
$gateway->name = 'MusicalGateway';
$gateway->count = 1;
$gateway->lanIp = '127.0.0.1';
$gateway->startPort = GATEWAY_PORT;
// 心跳间隔
$gateway->pingInterval = 5;
// 心跳等待次数
$gateway->pingNotResponseLimit = 6;
$gateway->pingData = '{"type":"ping"}';
$gateway->registerAddress = '127.0.0.1:'.REGISTER_PORT;