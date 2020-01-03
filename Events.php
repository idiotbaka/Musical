<?php
use \GatewayWorker\Lib\Gateway;

class Events
{
   /**
    * 有消息时
    * @param int $client_id
    * @param mixed $message
    */
    public static function onMessage($client_id, $message) {
        echo 'Receive message from client '.$client_id.': '.$message.PHP_EOL;
        // json
        $message_data = json_decode($message, true);
        if(!$message_data) {
            return;
        }
        switch ($message_data['type']) {
            // 心跳
            case 'ping':
                return;
            
            default:
                break;
        }
   }
   
   /**
    * 当客户端断开连接时
    * @param integer $client_id 客户端id
    */
    public static function onClose($client_id) {
        echo 'Client '.$client_id.': heartbeat timeout or disconnection, closed.'.PHP_EOL;
    }

   /**
    * 当客户端建立连接时
    * @param  integer $client_id 客户端id
    */
    public static function onConnect($client_id) {
        echo 'Client '.$client_id.': connected.'.PHP_EOL;
    }
}