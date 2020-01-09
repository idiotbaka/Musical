<?php
use \Workerman\Worker;
use \Workerman\Lib\Timer;

// Musical定时器
class MusicalTimer {

	// 定时器执行列表
	protected $timer_list = [
		// 歌单时间流逝
		'albumFlowTimer' => ['time' => 1, 'id' => null]
	];

	// 定时器：歌单时间流逝
	public function albumFlowTimer() {
		// 如果没有用户在线，则不流逝时间
		if(!count(Musical::getOnline())) {
			return;
		}
		global $db;
		global $netease_api;
		$album_list = Musical::getAlbumList();
		// 有歌单
		if($album_list) {
			$now_played = $album_list[0];
			// 如果是新歌曲，开始播放
			if($now_played['is_play'] == 0) {
				// 更新播放时间
				$db->update('album_list')
				->cols([
					'play_time' => date('Y-m-d H:i:s'),
					'is_play' => 1
				])->where('id='.$now_played['id'])->query();
			}
			// 如果是正在播放歌曲
			else {
				$next_time = $now_played['remaining_seconds'] - 1;
				// 如果播放完毕，设置为播放完成
				if($next_time == 0) {
					$db->update('album_list')
					->cols([
						'is_play' => 0,
						'is_success' => 1,
						'remaining_seconds' => 0
					])->where('id='.$now_played['id'])->query();
				}
				// 如果没有播放完毕，时间流逝1秒
				else {
					$db->update('album_list')
					->cols([
						'remaining_seconds' => $next_time
					])->where('id='.$now_played['id'])->query();
				}
			}
		}
		// 没有歌单系统自动点歌
		else {
			$hot_music = $db->select('*')
			->from('album_hot_music')
			->where('status=1')
			->query();
			if(!$hot_music) {
				return;
			}
			$song = $hot_music[rand(0, count($hot_music) - 1)];
			Ws::sendOrder($song['song_id'], -1);
		}
	}

	/**
	 * 构造方法：运行全部定时器
	 */
	public function __construct() {
		// 循环定时器列表
		foreach ($this->timer_list as $func => $v) {
			// 添加定时器，闭包传入方法名
			$timer_id = Timer::add($v['time'], function()use($func) {
				// 调用方法
				call_user_func(['\MusicalTimer', $func]);
			});
			// 写入定时器ID
			$this->timer_list[$func]['id'] = $timer_id;
		}
	}

	/**
	 * 关闭指定定时器
	 * @param  string $func 定时器方法名
	 */
	public function closeTimer($func) {
		if($this->timer_list[$func]['id']) {
			Timer::del($this->timer_list[$func]['id']);
			$this->timer_list[$func]['id'] = null;
		}
	}
}