var search_cache = new Array();
var songs_cache = new Array();
var songs_cache_info = {};

var step = 0;
var ws;
function ws_start() {
	terminal.set_nickname('');
	ws = new WebSocket('ws://' + document.domain + ':5601');
	$('.song_info').css('display', '');

	ws.onopen = function() {
		terminal.append('<p>Server connected.</p>');
		// 如果设置了cookie开关
		if(terminal.allow_cookie) {
			nickname = terminal.get_cookie('nickname');
			if(nickname) {
				ws.send('{"type":"change_nickname", "data":"' + nickname + '"}');
			}
			else {
				terminal.set_nickname('guest');
			}
		}
		else {
			terminal.set_nickname('guest');
		}
		if(/Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent)) {
			terminal.append('<p style="color: #ffeb3b;">You may be a safari browser user. Safari browser has disabled automatic audio playback by default.</p><p style="color: #ffeb3b;">If no music is currently playing, run the <b>"play"</b> command to turn it on manually.</p>');
		}
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
					msg['data'].forEach(function(element, index){
						songs_cache[index] = element.song_id;
						songs_cache_info[element.song_id] = {'name': element.name + ' - ' + element.author, 'album_name': element.album_name};
						terminal.add_song(terminal.escape(element.name + ' - ' + element.author), terminal.formate_time(element.total_seconds));
					})
					step = msg['data'][0]['total_seconds'] - msg['data'][0]['remaining_seconds'];
					ws.send('{"type":"music_url","data":' + msg['data'][0]['song_id'] + '}');
				}
				break;
			case 'search_music': // 歌曲搜索结果
				if (msg['code'] == 200) {
					msg['data'].forEach(function(element, index){
						search_cache[index] = element.song_id;
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
			case 'album_add':// 有用户点歌成功时
				songs_cache.push(msg['data']['song_info']['song_id']);
				songs_cache_info[msg['data']['song_info']['song_id']] = {'name': msg['data']['song_info']['name'] + ' - ' + msg['data']['song_info']['author'], 'album_name': msg['data']['song_info']['album_name']};
				// 如果缓存中只有一首歌曲，直接播放
				if (songs_cache.length == 1) {
					ws.send('{"type":"music_url","data":' + msg['data']['song_info']['song_id'] + '}');					
				}
				terminal.add_song(terminal.escape(msg['data']['song_info']['name'] + ' - ' + msg['data']['song_info']['author']), terminal.formate_time(msg['data']['song_info']['total_seconds']));
				if(msg['data']['is_server'] != 1) {
					terminal.add_chat_msg(msg['data']['time'], '', 'User ' + msg['data']['nickname'] + ' on-demand music: ' + msg['data']['song_info']['name'] + ' - ' + msg['data']['song_info']['author'] + '.');
				}
				break;
			case 'online':
				terminal.set_online_number(msg['data']['number']);
				break;
			case 'history_chat_msg':
				msg['data'].forEach(function(element) {
					terminal.add_chat_msg(element.send_time, element.nickname, element.msg);
				});
				break;
			case 'chat_msg_add':
				terminal.add_chat_msg(msg['data']['send_time'], msg['data']['nickname'], msg['data']['msg']);
				break;
			case 'change_nickname':
				if(msg['code'] == 200) {
					terminal.set_nickname(msg['data']);
					terminal.set_cookie('nickname', msg['data'], 365);
				}
				else {
					terminal.append(msg['msg']);
				}
				terminal.input(true);
				break;
			case 'say':
				if(msg['code'] != 200) {
					terminal.append(msg['msg']);
				}
				terminal.input(true);
				break;
		}

	}

	ws.onclose = function() {
		console.log('Server disconnected..');
	}

	musical.played = function() {
		if (step != 0) {
			musical.seek(step)
		}
	}

	musical.ended = function() {
		songs_cache = songs_cache.slice(1);
		terminal.remove_song();
		step = 0;

		if (songs_cache.length) {
			ws.send('{"type":"music_url","data":' + songs_cache[0] + '}');
		}
	}

	setInterval(function() {
		if (musical.is_play) {
			time = terminal.formate_time(musical.get_current_time()) + ' / ' + terminal.formate_time(musical.get_all_time());
			terminal.set_song_time(time);
			if(songs_cache[0]) {
				terminal.set_song_name(songs_cache_info[songs_cache[0]].name);
				terminal.set_song_album(songs_cache_info[songs_cache[0]].album_name);
			}
		} else {
			terminal.set_song_time('--:-- / --:--');
			terminal.set_song_name('Waiting to play...');
			terminal.set_song_album('');
		}
	}, 100);
}
