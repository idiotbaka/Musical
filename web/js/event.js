terminal.run = function(command) {
	command = command.trim();

	// 未知命令
	index = command.indexOf(' ') 
	if(index > 0) {
		args = command.substring(index + 1).trim() // 空格后为具体参数
		command = command.substring(0, command.indexOf(' '));
	}

	switch(command){
		case 'help': // 帮助
			terminal.append("<p>Usage: [command] [options] [args...]</p><br/>\
			<p>" + terminal.space(2) + "help" + terminal.space(15) + "Get a list of commands</p>\
			<p>" + terminal.space(2) + "search &lt;keyword&gt;" + terminal.space(3) + "Search songs by keyword</p>\
			<p>" + terminal.space(2) + "order &lt;index&gt;" + terminal.space(6) + "Use index to order songs(use the search command to get a index)</p>\
			<p>" + terminal.space(2) + "say &lt;message&gt;" + terminal.space(6) + "Send chat content to chat channel</p>\
			<p>" + terminal.space(2) + "vol &lt;volume&gt;" + terminal.space(7) + "Set the volume(0 ~ 1, such as 0.5)</p>\
			<p>" + terminal.space(2) + "nickname &lt;name&gt;" + terminal.space(4) + "Set nickname</p>");
			break;
		case 'search':// 搜索歌曲
			ws.send('{"type":"search_music","data":"' + args + '"}');
			terminal.input(false);
			break;
		case 'order':// 点歌
			index = Number(terminal.escape(args))
			if (isNaN(index)) {
				terminal.append('<p>Warning: Songs for atomic operations, please fill in a single index.</p>');
			}else{
				if (search_cache[index - 1] != undefined) {
					ws.send('{"type":"order","data":' + search_cache[index - 1] + '}');
					terminal.input(false);
				} else {
					terminal.append('<p>Warning: Please perform a search operation first.</p>');
				}
			}
			break;
		case 'nickname': // 修改昵称
			ws.send('{"type":"change_nickname", "data":"' + args + '"}');
			terminal.input(false);
			break;
		case 'say':
			if(terminal.nickname == 'guest') {
				terminal.append('<p>Please set a nickname first. Use command "nickname &lt;nickname&gt;" to set.</p>');
				break;
			}
			ws.send('{"type":"say", "data":"' + args + '"}');
			terminal.input(false);
			break;
		case 'vol':
			args = parseFloat(args);
			if(args < 0 || args > 1) {
				terminal.append('<p>Please enter a number between 0 and 1.</p>');
			}
			musical.volume(args);
			break;
		case 'ls':
			terminal.append('<p>README</p>');
			break;
		case 'cat':
			if(args == 'README' || args == '~/README') {
				terminal.append('\
					<p>PHP是世界上最好的语言。</p>\
				');
			}
			else {
				terminal.append('<p>cat: ' + terminal.escape(args) + ': No such file or directory</p>');
			}
			break;
		case 'reboot':
			location.reload();
			break;
		case 'shutdown':
			window.location.href = "about:blank";
			window.close();
			break;
		default:
			if(command) {
				terminal.append('<p>' + terminal.escape(command) + ': command not found</p>');
			}
			break;
	}
	
}