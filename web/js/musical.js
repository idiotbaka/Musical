var musical = new Object();
// 音乐器播放类
musical.ap = null;
// 回调方法：播放结束事件
musical.ended = function(){};
// 回调方法：播放开始事件
musical.played = function(){};
// 错误文本
musical.error = '';
// 是否在播放
musical.is_play = false;
// progress 开关
musical.progress = false;
// 音量
musical.vol = 1;
// 播放新的音频
musical.play_new = function(url) {
	if(musical.is_play == true) {
		musical.is_play = false;
	}
	musical.ap = new APlayer({
	    container: document.getElementById('music_box'),
	    autoplay: true,
	    loop: 'none',
	    volume: 0,
	    audio: [{
	        name: '0',
	        artist: '0',
	        url: url
	    }]
	});
	musical.ap.on('ended', function() {
		musical.is_play = false;
		musical.progress = false;
		musical.ended();
	});
	musical.ap.on('progress', function() {
		musical.is_play = true;
		if(musical.progress == false) {
			musical.progress = true;
			musical.played();
			musical.volume(musical.vol);
		}
	});
	return true;
};
// 暂停播放
musical.pause = function() {
	if(musical.is_play == false) {
		musical.error = 'error: 当前没有歌曲正在播放。';
		return false;
	}
	musical.ap.pause();
	return true;
};
// 开始播放
musical.play = function() {
	musical.ap.play();
	return true;
};
// 切换播放/暂停
musical.toggle = function() {
	if(musical.is_play == false) {
		musical.error = 'error: 当前没有歌曲。';
		return false;
	}
	musical.ap.toggle();
	return true;
}
// 切换到指定秒
musical.seek = function(seconds) {
	if(musical.is_play == false) {
		musical.error = 'error: 当前没有歌曲。';
		return false;
	}
	musical.ap.seek(seconds);
	return true;
};
// 调整音量（0.1~1）
musical.volume = function(num) {
	if(musical.is_play == false) {
		musical.error = 'error: 当前没有歌曲。';
		return false;
	}
	musical.ap.volume(num, true);
	musical.vol = num;
}
// 获取音频播放时间
musical.get_current_time = function() {
	if(musical.is_play == false) {
		musical.error = 'error: 当前没有歌曲。';
		return false;
	}
	return musical.ap.audio.currentTime;
}
// 返回音频总时间
musical.get_all_time = function() {
	if(musical.is_play == false) {
		musical.error = 'error: 当前没有歌曲。';
		return false;
	}
	return musical.ap.audio.duration;
}
