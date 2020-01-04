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
        // 如果不是json数据则不处理
        if(!$message_data) {
            return;
        }
        // 如果不含有type则不处理
        if(!isset($message_data['type'])) {
            return;
        }
        // 根据发送类型处理
        switch ($message_data['type']) {
            // 心跳
            case 'ping':
                return;

            // 歌曲搜索
            case 'search_music':
                if(isset($message_data['data'])) {
                    Ws::sendSearchMusic($message_data['data'], $client_id);
                }
                return;

            // 点歌
            case 'order':
                if(isset($message_data['data'])) {
                    Ws::sendOrder($message_data['data'], $client_id);
                }
                return;

            // 请求歌曲url
            case 'music_url':
                if(isset($message_data['data'])) {
                    Ws::sendMusicUrl($message_data['data'], $client_id);
                }
                return;

            // 变更昵称
            case 'change_nickname':
                if(isset($message_data['data'])) {
                    Ws::sendChangeNickname($message_data['data'], $client_id);
                }
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
        // 下线
        Musical::setOffline($client_id);
        // 发送最新在线用户
        Ws::sendOnline();
    }

   /**
    * 当客户端建立连接时
    * @param  integer $client_id 客户端id
    */
    public static function onConnect($client_id) {
        echo 'Client '.$client_id.': connected.'.PHP_EOL;
        // 发送歌单列表
        Ws::sendAlbumList($client_id);
        // 上线
        Musical::setOnline($client_id);
        // 发送最新在线用户
        Ws::sendOnline();
    }
}