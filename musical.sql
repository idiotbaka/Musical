CREATE TABLE `album_hot_music` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `album_list` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL COMMENT '歌曲ID',
  `name` varchar(256) NOT NULL COMMENT '歌曲名称',
  `author` varchar(256) NOT NULL COMMENT '歌曲作者（演奏者）',
  `album_name` varchar(256) NOT NULL COMMENT '歌单名称',
  `album_pic_url` varchar(256) DEFAULT NULL COMMENT '歌单封面图url',
  `total_seconds` int(11) NOT NULL COMMENT '总秒数',
  `remaining_seconds` int(11) NOT NULL COMMENT '剩余秒数',
  `is_play` tinyint(4) NOT NULL DEFAULT '0',
  `is_success` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否播放完成',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `play_time` datetime DEFAULT NULL COMMENT '播放时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='歌单表';

CREATE TABLE `album_music_url` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL COMMENT '歌曲id',
  `mp3_url` varchar(256) NOT NULL COMMENT '歌曲url',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='歌曲mp3 url表';

CREATE TABLE `system_chat_msg` (
  `id` int(11) NOT NULL,
  `nickname` varchar(40) DEFAULT NULL COMMENT '昵称',
  `msg` varchar(256) NOT NULL COMMENT '消息',
  `send_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发送时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='聊天记录表';

CREATE TABLE `system_client` (
  `id` int(11) NOT NULL,
  `client_id` varchar(40) NOT NULL COMMENT '客户端连接id',
  `nickname` varchar(40) DEFAULT NULL COMMENT '昵称',
  `is_online` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否在线',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户端连接表';

CREATE TABLE `system_config` (
  `id` int(11) NOT NULL,
  `system_key` varchar(128) NOT NULL,
  `system_value` varchar(256) NOT NULL,
  `description` varchar(256) NOT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';

INSERT INTO `system_config` (`id`, `system_key`, `system_value`, `description`) VALUES
(1, 'max_album_list', '10', '最长歌曲队列'),
(2, 'max_song_seconds', '600', '最长歌曲时间（秒）'),
(3, 'default_nickname', 'guest', '默认用户昵称'),
(4, 'max_chat_msg_length', '120', '最大聊天发送消息长度');

ALTER TABLE `album_hot_music`
  ADD PRIMARY KEY (`id`),
  ADD KEY `song_id` (`song_id`),
  ADD KEY `status` (`status`);

ALTER TABLE `album_list`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `album_music_url`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system_chat_msg`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system_client`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `album_hot_music`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `album_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `album_music_url`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `system_chat_msg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `system_client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `system_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;