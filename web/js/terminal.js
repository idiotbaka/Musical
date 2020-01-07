var terminal = new Object();
// 用户执行命令回调事件
terminal.run = function(command) {};
// 历史输入记录
terminal.history = [];
terminal.history_index = 0;
terminal.history_index_now = 0;
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
}
// 设置昵称
terminal.set_nickname = function(nickname) {
	$('#nickname').html(terminal.escape(nickname));
}
// 转义字符
terminal.escape = function(command) {
	return command.replace(/[<>&" ]/g, function(c) { 
		return {'<':'&lt;','>':'&gt;','&':'&amp;','"':'&quot;',' ':'&nbsp;'}[c];
	});
}
$(function() {
	$("#input_hidden")[0].focus();
	$(document).click(function(){
		$("#input_hidden")[0].focus();
	});
	$('#input_hidden').bind('input propertychange', function() {
		$('#input').html(terminal.escape($('#input_hidden').val()));
	});
	$(document).keydown(function(event) {
		// 回车事件
		if(event.keyCode == 13) {
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
});

