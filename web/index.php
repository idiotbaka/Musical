<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>DEMO</title>
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aplayer/1.10.1/APlayer.min.css" integrity="sha256-uqQQGnDcmRKvhKwc5Vm4XT1GQ2oV6t1U0NR2N9tV+BQ=" crossorigin="anonymous" />
</head>
<body>
	<div id="music_box"></div>
	<div class="show_box">
		<b>Musical Demo (Build 0001)</b>
		<p>Copyright (c) 2020 idiotbaka & JokerCats. All right reserved.</p>
		<br/>
		<p>Welcome to Musical Demo (Last updated: 2020/01/06 UTC/GMT +08:00)</p>
		<br/>
		<p> * Documentation:&nbsp;https://github.com/idiotbaka/Musical/blob/master/README.md</p>
		<p> * Github:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;https://github.com/idiotbaka/Musical</p>
		<p> * Support:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;https://github.com/idiotbaka/Musical/issues</p>
		<br/>
		<p>Musical is currently the latest version.</p>
		<p>Run 'help' to get a list of commands.</p>
		<br/>
	</div>
	<div class="command_box">
		<p><span id="nickname" class="can_input" style="display: none;">guest</span><span class="can_input" style="display: none;">@Musical:~# </span><span id="input" class="can_input"></span><span id="buoy" style="opacity: 0;"></span></p>
	</div>
	<div class="song_info">
		<div class="song_info-border">
			<p>[Now Playing]</p>
			<span class="song_name" style="margin-left: -1px;">Waiting to play...</span>
			<p>Album:&nbsp;<span class="song_album"></span></p>
			<p>Time:&nbsp;&nbsp;<span class="song_time"></span></p>
		</div>
		<div class="song_info-list">
			<p>[Music List]</p>
			<div class="song_info-list-container">
				
			</div>
		</div>
	</div>
	<input type="text" id="input_hidden">
	<script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/aplayer/1.10.1/APlayer.min.js" integrity="sha256-6Y7CJDaltoeNgk+ZftgCD9jLgmGv4xKUo8nQ0HgAwVo=" crossorigin="anonymous"></script>
	<script src="js/musical.js"></script>
	<script src="js/terminal.js"></script>
	<script src="js/event.js"></script>
	<script src="js/websocket.js"></script>
</body>
</html>