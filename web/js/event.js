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
			<p>" + terminal.space(2) + "online" + terminal.space(13) + "Get online user list</p>\
			<p>" + terminal.space(2) + "album" + terminal.space(14) + "Get the current album list</p>\
			<p>" + terminal.space(2) + "search &lt;keyword&gt;" + terminal.space(3) + "Search songs by keyword</p>\
			<p>" + terminal.space(2) + "order &lt;song_id&gt;" + terminal.space(4) + "Use song_id to order songs(use the search command to get song_id)</p>\
			<p>" + terminal.space(2) + "point &lt;index&gt;" + terminal.space(6) + "Add to album list by index</p>\
			<p>" + terminal.space(2) + "say &lt;message&gt;" + terminal.space(6) + "Send chat content to chat channel</p>\
			<p>" + terminal.space(2) + "nickname &lt;name&gt;" + terminal.space(4) + "Set nickname</p>");
			break;
		case 'search':// 搜索歌曲
			ws.send('{"type":"search_music","data":"' + args + '"}');
			terminal.input(false);
			break;
		case 'point':// 点歌
			index = Number(terminal.escape(args))
			if (isNaN(index)) {
				terminal.append('<p>Warning: Songs for atomic operations, please fill in a single index.</p>');
			}else{
				ws.send('{"type":"order","data":' + songs_cache[index - 1] + '}');
			}
			terminal.input(false);
			break;
		case 'nickname': // 修改昵称
			terminal.set_nickname(args);
			break;
		case 'online': // 获取在线列表
			terminal.input(false);
			break;
		case 'ls':
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