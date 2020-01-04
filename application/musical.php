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
}