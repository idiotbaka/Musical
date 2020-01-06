var temp;

var ws = new WebSocket('ws://' + document.domain + ':5601');
ws.onopen = function() {
	console.log('websocket connected..');
}

ws.onmessage = function(evt) {
	var msg = JSON.parse(evt.data);

	switch(msg['type']){
		case 'ping':
			ws.send('{"type":"pong"}');
			break;
		case 'album_list': // 获取歌单列表

			break;
		case 'online': // 获取在线用户列表

			break;
		case 'search_music': // 歌曲搜索结果
			if (msg['code'] == 200) {
				msg['data'].forEach(function(element, index){
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
	}

}

ws.onclose = function(){
	console.log('websocket disconnected..');
}