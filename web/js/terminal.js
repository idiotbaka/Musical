var terminal = new Object();
// 用户执行命令回调事件
terminal.run = function(command) {};
// 历史输入记录
terminal.history = [];
terminal.history_index = 0;
terminal.history_index_now = 0;
// 歌单长度
terminal.song_list = 0;
// cookie相关
terminal.allow_cookie = 0;
terminal.cookie_pass = 0;
// append显示内容
terminal.append = function(html) {
	$('.show_box').append(html);
}
// 清空面板
terminal.clear = function() {
	$('.show_box').html('');
}
// 返回空格
terminal.space = function(num) {
	var spaces = '';
	for (var i = 0; i < num; i++) {
		spaces += '&nbsp;';
	}
	return spaces;
}
// 可输入开关
terminal.input_switch = true;
terminal.input = function(key) {
	if(key == true) {
		$('.can_input').css('display', '');
		terminal.input_switch = true;
	}
	else {
		$('.can_input').css('display', 'none');
		terminal.input_switch = false;
	}
	document.body.scrollTop = document.body.scrollHeight;
	document.documentElement.scrollTop = document.documentElement.scrollHeight;
}
// 设置昵称
terminal.set_nickname = function(nickname) {
	$('#nickname').html(terminal.escape(nickname));
}
// 设置音乐名
terminal.set_song_name = function(song_name) {
	$('.song_name').html(terminal.escape(song_name));
}
// 设置专辑名
terminal.set_song_album = function(song_album) {
	$('.song_album').html(terminal.escape(song_album));
}
// 设置时间
terminal.set_song_time = function(song_time) {
	$('.song_time').html(terminal.escape(song_time));
}
// 转义字符
terminal.escape = function(command) {
	return command.replace(/[<>&" ]/g, function(c) { 
		return {'<':'&lt;','>':'&gt;','&':'&amp;','"':'&quot;',' ':'&nbsp;'}[c];
	});
}
// 格式化时间
terminal.formate_time = function(song_time) {
	song_time = parseInt(song_time);
	minute = parseInt(song_time / 60);
	second = song_time % 60;

	if (minute < 10) {
		minute = '0' + minute;
	}

	if (second < 10) {
		second = '0' + second;
	}

	return minute + ':' + second;
}
// 添加歌曲
terminal.add_song = function(title, time) {
	if(terminal.song_list == 10) {
		return false;
	}
	terminal.song_list += 1;
	$('.song_info-list-container').append('\
		<div>\
			<div class="music-single">\
				<p><i class="music-single-index">' + terminal.song_list + '</i>. ' + title + '</p>\
				<span>' + time + '</span>\
			</div>\
			<div style="clear: both;"></div>\
		</div>\
	');
	return true;
}
// 移除歌单第一首歌曲
terminal.remove_song = function() {
	if(terminal.song_list <= 0) {
		return false;
	}
	$(".song_info-list-container div")[0].remove();
	$('.music-single-index').each(function(index) {
		$(this).html(index + 1);
	});
	terminal.song_list -= 1;
	return true;
}
$(function() {
	terminal.append('\
	<p>---------------------------------------------------------------------------------</p>\
	<p>Music may use cookies to store some of your information.</p>\
	<p>Such as nicknames, historical commands, etc.</p>\
	<p>The stored information is only used to provide you with more convenient services.</p>\
	<p>Our server will not store any user privacy data.</p>\
	<p>Do you allow us to use cookies?</p>\
	<br/>\
	<p>[Y]es, I agree&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[N]o, thanks</p>\
	<p>---------------------------------------------------------------------------------</p>');
	$('#nickname').html('Please enter Y or N:');
	$('#nickname').css('display', '');

	$("#input_hidden")[0].focus();
	$(document).click(function(){
		$("#input_hidden")[0].focus();
	});
	$('#input_hidden').bind('input propertychange', function() {
		$('#input').html(terminal.escape($('#input_hidden').val()));
	});
	$('#buoy').css('opacity', '1');
	$('#buoy').html('&nbsp;');
	$(document).keydown(function(event) {
		// 回车事件
		if(event.keyCode == 13) {
			if(terminal.cookie_pass == 0) {
				var command = $('#input_hidden').val();
				$('.show_box').append('<p>Please enter Y or N:' + terminal.escape(command) + '</p>');
				$('#input').html('');
				$('#input_hidden').val('');
				if(command.trim() == 'Y' || command.trim() == 'y') {
					terminal.input(false);
					$('.show_box').append('<p>Start connecting to server...</p>');
					terminal.cookie_pass = 1;
					terminal.allow_cookie = 1;
					ws_start();
				}
				else if(command.trim() == 'N' || command.trim() == 'n') {
					terminal.input(false);
					$('.show_box').append('<p>Start connecting to server...</p>');
					terminal.cookie_pass = 1;
					terminal.allow_cookie = 0;
					ws_start();
				}
				return;
			}
			var command = $('#input_hidden').val();
			$('.show_box').append('<p>' + terminal.escape($('#nickname').html()) + '@Musical:~# ' + terminal.escape(command) + '</p>');
			$('#input').html('');
			$('#input_hidden').val('');
			terminal.run(command);
			if(command) {
				terminal.history.push(command);
				terminal.history_index += 1;
				terminal.history_index_now = terminal.history_index;
			}
			document.body.scrollTop = document.body.scrollHeight;
			document.documentElement.scrollTop = document.documentElement.scrollHeight;
		}
		// CTRL+C事件
		if(event.ctrlKey && event.keyCode == 67) {
			if(terminal.input_switch == false) {
				terminal.append('<p>^C</p>');
				terminal.input(true);
			}
			else {
				terminal.append('<p>' + terminal.escape($('#nickname').html()) + '@Musical:~# ^C</p>');
				terminal.input(true);
			}
		}
		// up事件
		if(event.keyCode == 38) {
			if(terminal.history_index_now - 1 >= 0) {
				terminal.history_index_now -= 1;
				$('#input').html(terminal.escape(terminal.history[terminal.history_index_now]));
				$('#input_hidden').val(terminal.history[terminal.history_index_now]);
			}
		}
		// down事件
		if(event.keyCode == 40) {
			if(terminal.history_index_now + 1 < terminal.history_index) {
				terminal.history_index_now += 1;
				$('#input').html(terminal.escape(terminal.history[terminal.history_index_now]));
				$('#input_hidden').val(terminal.history[terminal.history_index_now]);
			}
			else {
				terminal.history_index_now = terminal.history_index;
				$('#input').html('');
				$('#input_hidden').val('');
			}
		}
	});
	$(document).keyup(function(event) {
		// 按键事件
		if(event.keyCode >= 37 && event.keyCode <= 40) {
			var command = $("#input_hidden").val();
			$("#input_hidden").val('').focus().val(command);
		}
	});
	// 歌曲名滚动显示
	setInterval(function() {
		var width = $('.song_name').width();
		var shifting = $('.song_name').css('margin-left');
		shifting = shifting.substring(0, shifting.indexOf('p'));
		if(shifting < 0 && Math.abs(shifting) > width) {
			$('.song_name').css('margin-left', '420px');
		}
		else {
			$('.song_name').css('margin-left', shifting - 7);
		}
	}, 200);
});

