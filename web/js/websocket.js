var songs_cache = new Array();
var ws;
function ws_start() {
	
	ws = new WebSocket('ws://' + document.domain + ':5601');
	
	ws.onopen = function() {
		terminal.append('<p>Websocket connected.</p>');
		$('#nickname').html('guest');
		terminal.input(true);
	}

	ws.onmessage = function(evt) {
		var msg = JSON.parse(evt.data);

		switch(msg['type']){
			case 'ping':
				ws.send('{"type":"pong"}');
				break;
			case 'album_list': // 获取歌单列表
				if (msg['data'].length == 0) {
					// todo 拉取默认列表
				} else {
					msg['data'].forEach(function(element){
						song_time = parseInt(element.total_seconds / 60) + '分' + (element.total_seconds % 60) + '秒';
						terminal.add_song(terminal.space(element.name), song_time);
					})
					ws.send('{"type":"music_url","data":' + msg['data'][0]['song_id'] + '}');
				}
				break;
			case 'online': // 获取在线用户列表

				break;
			case 'search_music': // 歌曲搜索结果
				if (msg['code'] == 200) {
					msg['data'].forEach(function(element, index){
						songs_cache[index] = element.song_id;
						terminal.append('<p>' + (index + 1) + '. name:' + terminal.space(9) + element.name + '</p>');
						terminal.append('<p>' + terminal.space(index.toString().length + 1) + 
							' author:' + terminal.space(7) + element.author + '</p>');
						terminal.append('<p>' + terminal.space(index.toString().length + 1) + 
							' albun:' + terminal.space(8)  + element.album_name + '</p>');
					});
				} else {
					terminal.append(msg['msg']);
				}
				terminal.input(true);
				break;
			case 'order': // 点播歌曲
				terminal.append(msg['msg']);
				terminal.input(true);
				break;
			case 'music_url': // 获取歌曲播放地址
				if (msg['code'] == 200) {
					musical.play_new(msg['data']);
				} else {
					terminal.append(msg['msg']);
				}
				break;
		}

	}

	ws.onclose = function(){
		console.log('websocket disconnected..');
	}
}
