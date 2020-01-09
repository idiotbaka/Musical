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
        $send_data['code'] = 200;
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
                'song_id' => $v['id'],
                'name' => $v['name'],
                'author' => $v['ar'][0]['name'],
                'album_name' => $v['al']['name'],
                'album_pic_url' => $v['al']['picUrl']
            ];
        }
        return Ws::sendToClient($send_data, $client_id);
    }

    /**
     * 点歌&发送点歌结果
     * @param  string $data      歌曲id
     * @param  string $client_id 客户端id
     */
    public static function sendOrder($data, $client_id) {
        global $netease_api;
        if(!(int)$data) {
            return;
        }
        $send_data = [];
        $send_data['type'] = 'order';
        $send_data['code'] = 200;
        // 是否超出点歌队列上限
        $max_album_list = Musical::getSystemConfig('max_album_list');
        if(sizeof(Musical::getAlbumList()) >= $max_album_list) {
            $send_data['code'] = 0;
            $send_data['msg'] = '点歌失败：超出了最多歌单上限，请稍候再点歌吧~';
            return Ws::sendToClient($send_data, $client_id);
        }
        // 是否存在于歌单中
        if(Musical::getAlbumSongInfo((int)$data)) {
            $send_data['code'] = 0;
            $send_data['msg'] = '点歌失败：该歌曲已经在歌单中啦，请等待它的播放吧~';
            return Ws::sendToClient($send_data, $client_id);
        }
        // 获得歌曲信息
        $result = $netease_api->song((int)$data);
        $result = json_decode($result, true);
        if(!$result) {
            $send_data['code'] = 0;
            $send_data['msg'] = '点歌失败：获取歌曲信息失败。';
            return Ws::sendToClient($send_data, $client_id);
        }
        if($result['code'] != 200) {
            $send_data['code'] = 0;
            $send_data['msg'] = '点歌失败：获取歌曲信息失败。';
            return Ws::sendToClient($send_data, $client_id);
        }
        $song_info = [];
        // 歌曲id
        $song_info['song_id'] = (int)$data;
        // 歌曲名称
        $song_info['name'] = $result['songs'][0]['name'];
        // 演奏家
        $song_info['author'] = $result['songs'][0]['ar'][0]['name'];
        // 专辑名称
        $song_info['album_name'] = $result['songs'][0]['al']['name'];
        // 专辑封面图
        $album_info = $netease_api->album($result['songs'][0]['al']['id']);
        $album_info = json_decode($album_info, true);
        if(!$album_info) {
            $send_data['code'] = 0;
            $send_data['msg'] = '点歌失败：获取歌曲信息失败。';
            return Ws::sendToClient($send_data, $client_id);
        }
        $song_info['album_pic_url'] = $album_info['album']['blurPicUrl'];
        // 如果超出最大歌曲秒数上限
        if(ceil($result['songs'][0]['dt']/1000) > Musical::getSystemConfig('max_song_seconds')) {
            $send_data['code'] = 0;
            $send_data['msg'] = '点歌失败：歌曲时间太长啦~超出了最大上限'.(string)Musical::getSystemConfig('max_song_seconds').'秒。';
            return Ws::sendToClient($send_data, $client_id);
        }
        // 总秒数
        $song_info['total_seconds'] = ceil($result['songs'][0]['dt']/1000);
        // 剩余秒数
        $song_info['remaining_seconds'] = ceil($result['songs'][0]['dt']/1000);
        $song_info['create_time'] = date('Y-m-d H:i:s');
        // 获取mp3 url
        $song_url = $netease_api->url((int)$data);
        $song_url = json_decode($song_url, true);
        if(!$song_url) {
            $send_data['code'] = 0;
            $send_data['msg'] = '点歌失败：获取歌曲信息失败。';
            return Ws::sendToClient($send_data, $client_id);
        }
        if(!$song_url['data'][0]['url']) {
            $send_data['code'] = 0;
            $send_data['msg'] = '点歌失败：该歌曲已被下架或不存在或需付费购买，请换一个试试吧。';
            return Ws::sendToClient($send_data, $client_id);
        }
        // 插入点歌队列
        global $db;
        $insert_id = $db->insert('album_list')->cols($song_info)->query();
        if($insert_id) {
            $send_data['song_id'] = (int)$data;
            $send_data['msg'] = '点歌成功！';
            Ws::sendToClient($send_data, $client_id);
            // 向全部用户广播新歌曲添加信息
            $song_info['id'] = $insert_id;
            $add_data = [];
            $add_data['type'] = 'album_add';
            $add_data['data'] = $song_info;
            Ws::sendToClient($add_data);
        }
        else {
            $send_data['code'] = 0;
            $send_data['song_id'] = (int)$data;
            $send_data['msg'] = '点歌失败：系统错误，请稍后再试。';
            return Ws::sendToClient($send_data, $client_id);
        }
    }

    /**
     * 发送当前歌单到客户端
     * @param  string $client_id 客户端id
     */
    public static function sendAlbumList($client_id) {
        $album_list = Musical::getAlbumList();
        $send_data = [];
        $send_data['type'] = 'album_list';
        $send_data['data'] = $album_list;
        return Ws::sendToClient($send_data, $client_id);
    }

    /**
     * 发送当前歌单到客户端
     * @param  string $data      歌曲id
     * @param  string $client_id 客户端id
     */
    public static function sendMusicUrl($data, $client_id) {
        if(!(int)$data) {
            return;
        }
        $mp3_url = Musical::getMusicUrl((int)$data);
        $send_data = [];
        $send_data['type'] = 'music_url';

        if($mp3_url) {
            $send_data['code'] = 200;
            $send_data['song_id'] = (int)$data;
            $send_data['data'] = $mp3_url;
        }
        else {
            $send_data['code'] = 0;
            $send_data['song_id'] = (int)$data;
            $send_data['msg'] = '获取歌曲url失败。';
        }
        return Ws::sendToClient($send_data, $client_id);
    }

    /**
     * 发送在线用户到客户端
     * @param  string $client_id 客户端id
     */
    public static function sendOnline($client_id = '') {
        // 获取在线列表
        $online = Musical::getOnline();
        $send_data = [];
        $send_data['type'] = 'online';
        $send_data['data'] = [
            'number' => sizeof($online),
            'list' => $online
        ];
        return Ws::sendToClient($send_data, $client_id);
    }

    /**
     * 发送变更昵称结果
     * @param  string $data      昵称
     * @param  string $client_id 客户端id
     */
    public static function sendChangeNickname($data, $client_id) {
        $send_data = [];
        $send_data['type'] = 'change_nickname';
        // 如果长度超出了限制
        if(mb_strlen($data) > 20) {
            $send_data['code'] = 0;
            $send_data['msg'] = '设置昵称失败：超出了最大20的字符上限。';
            return Ws::sendToClient($send_data, $client_id);
        }
        // 设置昵称
        $change = Musical::setClientNickname($client_id, $data);
        if($change) {
            $send_data['code'] = 200;
            $send_data['msg'] = '设置昵称成功。';
            Ws::sendToClient($send_data, $client_id);
            // 发送在线列表
            Ws::sendOnline();
        }
        else {
            $send_data['code'] = 0;
            $send_data['msg'] = '设置昵称失败：请刷新后再试一次。';
            return Ws::sendToClient($send_data, $client_id);
        }
    }

    /**
     * 发送消息到客户端
     * @param  array  $send_data 发送数据（数组）
     * @param  string $client_id 客户端id
     */
    public static function sendToClient($send_data, $client_id = '') {
        // 如果是系统调用ws方法
        if($client_id == -1) {
            return;
        }
        // 如果没有指定客户端id，则发送给全部用户
        if($client_id) {
            Gateway::sendToClient($client_id, json_encode($send_data));
        }
        else {
            Gateway::sendToAll(json_encode($send_data));
        }
    }
}