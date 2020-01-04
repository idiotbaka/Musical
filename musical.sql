-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2020-01-04 19:03:55
-- 服务器版本： 5.7.24-0ubuntu0.16.04.1
-- PHP 版本： 7.0.33-0ubuntu0.16.04.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `musical`
--

-- --------------------------------------------------------

--
-- 表的结构 `album_list`
--

CREATE TABLE `album_list` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL COMMENT '歌曲ID',
  `name` varchar(256) NOT NULL COMMENT '歌曲名称',
  `author` varchar(256) NOT NULL COMMENT '歌曲作者（演奏者）',
  `album_name` varchar(256) NOT NULL COMMENT '歌单名称',
  `album_pic_url` varchar(256) NOT NULL COMMENT '歌单封面图url',
  `total_seconds` int(11) NOT NULL COMMENT '总秒数',
  `remaining_seconds` int(11) NOT NULL COMMENT '剩余秒数',
  `is_play` tinyint(4) NOT NULL DEFAULT '0',
  `is_success` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否播放完成',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `play_time` datetime DEFAULT NULL COMMENT '播放时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='歌单表';

-- --------------------------------------------------------

--
-- 表的结构 `album_music_url`
--

CREATE TABLE `album_music_url` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL COMMENT '歌曲id',
  `mp3_url` varchar(256) NOT NULL COMMENT '歌曲url',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='歌曲mp3 url表';

-- --------------------------------------------------------

--
-- 表的结构 `system_client`
--

CREATE TABLE `system_client` (
  `id` int(11) NOT NULL,
  `client_id` varchar(40) NOT NULL COMMENT '客户端连接id',
  `nickname` varchar(40) DEFAULT NULL COMMENT '昵称',
  `is_online` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否在线',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户端连接表';

-- --------------------------------------------------------

--
-- 表的结构 `system_config`
--

CREATE TABLE `system_config` (
  `id` int(11) NOT NULL,
  `system_key` varchar(128) NOT NULL,
  `system_value` varchar(256) NOT NULL,
  `description` varchar(256) NOT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';

--
-- 转存表中的数据 `system_config`
--

INSERT INTO `system_config` (`id`, `system_key`, `system_value`, `description`) VALUES
(1, 'max_album_list', '20', '最长歌曲队列'),
(2, 'max_song_seconds', '600', '最长歌曲时间（秒）'),
(3, 'default_nickname', '游客', '默认用户昵称');

--
-- 转储表的索引
--

--
-- 表的索引 `album_list`
--
ALTER TABLE `album_list`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `album_music_url`
--
ALTER TABLE `album_music_url`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `system_client`
--
ALTER TABLE `system_client`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `album_list`
--
ALTER TABLE `album_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `album_music_url`
--
ALTER TABLE `album_music_url`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `system_client`
--
ALTER TABLE `system_client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `system_config`
--
ALTER TABLE `system_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
