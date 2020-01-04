<?php
use \Workerman\Worker;
use \GatewayWorker\Lib\Gateway;

// ws操作类
class Ws {

    /**
     * 发送歌曲搜索结果
     * @param  string $data      搜索关键词
     * @param  string $client_id 客户端id
     */
    public static function sendSearchMusic($data, $client_id) {
        global $netease_api;
        if(!$data) {
            return;
        }
        // 搜索歌曲
        $result = $netease_api->search($data, [
            'limit' => 5
        ]);
        $result = json_decode($result, true);
        $send_data = [];
        $send_data['type'] = 'search_music';
        $send_data['data'] = [];
        // 如果没有搜索结果则返回空
        if(!$result) {
            return Ws::sendToClient($send_data, $client_id);
        }
        if($result['code'] != 200) {
            return Ws::sendToClient($send_data, $client_id);
        }
        if(!isset($result['result']['songs'])) {
            return Ws::sendToClient($send_data, $client_id);
        }
        foreach ($result['result']['songs'] as $v) {
            $send_data['data'][] = [
                'id' => $v['id'],
                'name' => $v['name'],
                'author' => $v['ar'][0]['name'],
                'album_name' => $v['al']['name'],
                'album_pic_url' => $v['al']['picUrl']
            ];
        }
        return Ws::sendToClient($send_data, $client_id);
    }

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