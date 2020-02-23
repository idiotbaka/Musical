### 介绍
 基于PHP+Workerman开发的网页音乐电台，支持在线聊天、搜索歌曲、点歌等。  
 点歌使用了网易云音乐api。

### 准备
 运行环境：PHP（>=5.6）、Mysql数据库。  
 PHP需要安装BCMath, Curl, OpenSSL, pcntl, posix扩展。  
 如果并发超过1024连接数，需要额外安装event和libevent扩展。

### 安装
 1.clone整个仓库，并且进入仓库目录：
```
git clone https://github.com/idiotbaka/Musical.git
cd Musical
```
 2.使用composer安装：
```
composer install
```
 3.填写配置文件Config.php。   

### 运行
 目前仅支持linux系统，命令行执行 `php start.php start` 启动。  
 可以使用screen等工具保持在后台运行。
