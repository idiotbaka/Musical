<!DOCTYPE html>
<html>
<head>
	<title>Test</title>
</head>
<body style="margin: 20px;">
	<div>
		<p>Send:</p>
		<textarea style="width: 600px; height: 50px;" id="txt-send"></textarea>
		<br/>
		<button style="height: 24px; width: 60px;" onclick="send()">Send</button>
	</div>
	<div>
		<p>Message:</p>
		<textarea style="width: 600px; height: 250px;" id="txt-receive"></textarea>
	</div>
</body>
<script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript">
	var ws = new WebSocket('ws://' + document.domain + ':5601');
	ws.onopen = function() {
		receive('ws connected.');
	};

	ws.onmessage = function (evt) { 
		var received_msg = evt.data;
		if(JSON.parse(received_msg)["type"] != 'ping') {
			receive(received_msg);
		}
		switch(JSON.parse(received_msg)["type"]) {
			case 'ping':
				send('{"type": "pong"}');
			return;
		}
	};

	ws.onclose = function() { 
		receive('ws disconnected.');
	};

	function send(msg = '') {
		if(msg) {
			ws.send(msg);
		}
		else {
			ws.send($('#txt-send').val());
		}
	}

	function receive(msg) {
		before_txt = $('#txt-receive').val();
		if(before_txt) {
			$('#txt-receive').val(before_txt + '\n' + msg);
		}
		else {
			$('#txt-receive').val(msg);
		}
	}
</script>
</html>