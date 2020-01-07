var musical = new Object();
// 音乐器播放类
musical.ap = null;
// 回调方法：播放结束事件
musical.ended = function(){};
// 错误文本
musical.error = '';
// 是否在播放
musical.is_play = false;
// 播放新的音频
musical.play_new = function(url) {
	if(musical.is_play == true) {
		musical.error = 'error: 当前正在播放中，请等待播放结束。';
		return false;
	}
	musical.ap = new APlayer({
	    container: document.getElementById('music_box'),
	    autoplay: true,
	    loop: 'none',
	    audio: [{
	        name: '0',
	        artist: '0',
	        url: url
	    }]
	});
	musical.is_play = true;
	musical.ap.on('ended', function(){
		musical.is_play = false;
		musical.ended();
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
	if(musical.is_play == false) {
		musical.error = 'error: 当前没有歌曲。';
		return false;
	}
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
