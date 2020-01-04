<?php
use \Workerman\Worker;
use \GatewayWorker\Lib\Gateway;

// 公共类
class Musical {

	/**
	 * 获取系统参数
	 * @param  string $key       系统参数key
	 * @return string            系统参数
	 */
	public static function getSystemConfig($key) {
		global $db;
		return $db->select('system_value')
		->from('system_config')
		->where('system_key="'.$key.'"')
		->single();
	}

	/**
	 * 获取当前点歌队列
	 * @return array             歌曲队列
	 */
	public static function getAlbumList() {
		global $db;
		return $db->select('*')
		->from('album_list')
		->where('is_success=0')
		->orderByASC(['id'])
		->query();
	}

	/**
	 * 获取歌单中的歌曲信息
	 * @param  string $song_id   歌曲id
	 * @return array             歌曲信息
	 */
	public static function getAlbumSongInfo($song_id) {
		global $db;
		$result = $db->select('*')
		->from('album_list')
		->where('song_id='.(int)$song_id.' AND is_success=0')
		->row();
		if($result) {
			return $result;
		}
		return false;
	}

	/**
	 * 获取歌曲url（1分钟缓存）
	 * @param  string $song_id   歌曲id
	 * @return string            歌曲url
	 */
	public static function getMusicUrl($song_id) {
		global $db;
		global $netease_api;
		$mp3_url = $db->select('mp3_url')
		->from('album_music_url')
		->where('song_id='.$song_id.' AND create_time>"'.date('Y-m-d H:i:s', strtotime('-1 minute')).'"')
		->single();
		if($mp3_url) {
			return $mp3_url;
		}
		else {
			$mp3_url = $netease_api->url((int)$song_id);
			$mp3_url = json_decode($mp3_url, true);
			if(!$mp3_url) {
				return false;
			}
			if(!$mp3_url['data'][0]['url']) {
				return false;
			}
			$db->insert('album_music_url')->cols([
				'song_id' => (int)$song_id,
				'mp3_url' => $mp3_url['data'][0]['url'],
				'create_time' => date('Y-m-d H:i:s')
			])->query();
			return $mp3_url['data'][0]['url'];
		}
	}

	/**
	 * 获取在线列表
	 * @return array             用户列表
	 */
	public static function getOnline() {
		global $db;
		$online_list = $db->select('id,client_id,nickname')
		->from('system_client')
		->where('is_online=1')
		->query();
		foreach ($online_list as &$client) {
			// 如果没有昵称则设定默认昵称
			if(!$client['nickname']) {
				$client['nickname'] = Musical::getSystemConfig('default_nickname').$client['id'];
			}
		}
		return $online_list;
	}

	/**
	 * 将全部用户置为离线（程序启动）
	 */
	public static function setOfflineAll() {
		global $db;
		$db->update('system_client')->cols([
			'is_online' => 0
		])->where('id>0')->query();
	}

	/**
	 * 将用户置为在线
	 * @param  string $client_id   客户端id
	 */
	public static function setOnline($client_id) {
		global $db;
		$id = $db->select('id')
		->from('system_client')
		->where('client_id="'.$client_id.'"')
		->single();
		if($id) {
			$db->update('system_client')->cols([
				'is_online' => 1,
				'nickname' => null,
				'create_time' => date('Y-m-d H:i:s')
			])->where('id='.$id)->query();
		}
		else {
			$db->insert('system_client')->cols([
				'client_id' => $client_id,
				'is_online' => 1,
				'create_time' => date('Y-m-d H:i:s')
			])->query();
		}
	}

	/**
	 * 将用户置为离线
	 * @param  string $client_id   客户端id
	 */
	public static function setOffline($client_id) {
		global $db;
		$id = $db->select('id')
		->from('system_client')
		->where('client_id="'.$client_id.'" AND is_online=1')
		->single();
		if($id) {
			$db->update('system_client')->cols([
				'is_online' => 0
			])->where('id='.$id)->query();
		}
	}

	/**
	 * 设置用户昵称
	 * @param  string $client_id   客户端id
	 */
	public static function setClientNickname($client_id, $nickname) {
		global $db;
		$id = $db->select('id')
		->from('system_client')
		->where('client_id="'.$client_id.'" AND is_online=1')
		->single();
		if($id) {
			$db->update('system_client')->cols([
				'nickname' => $nickname
			])->where('id='.$id)->query();
			return true;
		}
		return false;
	}
}