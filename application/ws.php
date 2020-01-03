<?php
use \Workerman\Worker;
use \GatewayWorker\Lib\Gateway;

// ws操作类
class Ws {

    /**
     * 发送消息到客户端
     * @param  array  $send_data 发送数据（数组）
     * @param  string $client_id 客户端id
     */
    public static function sendToClient($send_data, $client_id = '') {
    	// 如果没有指定客户端id，则发送给全部用户
    	if($client_id) {
    		Gateway::sendToClient($client_id, json_encode($send_data));
    	}
    	else {
    		Gateway::sendToAll(json_encode($send_data));
    	}
    }
}