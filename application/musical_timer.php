<?php
use \Workerman\Worker;
use \Workerman\Lib\Timer;

// Musical定时器
class MusicalTimer {

	// 定时器执行列表
	protected $timer_list = [
		// 测试
		'test_timer' => ['time' => 3, 'id' => null]
	];

	public function test_timer() {
		echo 'testTimer'.PHP_EOL;
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
	public function close_timer($func) {
		if($this->timer_list[$func]['id']) {
			Timer::del($this->timer_list[$func]['id']);
			$this->timer_list[$func]['id'] = null;
		}
	}
}