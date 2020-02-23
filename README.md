### 介绍
 基于PHP+Workerman开发的命令行风格网页音乐电台，支持在线聊天、搜索歌曲、点歌等。  
 点歌使用了网易云音乐api，音乐播放使用aplayer。  
 在线预览：http://musical.iobaka.com/

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
 3.修改配置文件Config.php，填写数据库等。   

### 运行
 目前仅支持linux系统，命令行执行 `php start.php start` 启动。  
 可以使用screen等工具保持在后台运行。

### 支持命令
 | 命令 | 描述 |
 | ------ | ------ |
 | help | 查看帮助 |
 | search <keyword> | 搜索歌曲 |
 | order <index> | 点歌，需要先搜索歌曲 |
 | say <message> | 发送聊天消息 |
 | nickname <name> | 设置聊天昵称 |
 | vol <volume> | 调整音量（0~1，如0.5） |
 
 ### 常见问题
 **1. 打开网页后歌曲不播放**  
 大部分浏览器都默认禁用了自动播放，运行 **play** 命令来开启播放。通常第二次访问时，浏览器就会记住用户习惯来自动播放了。    

 **2. 是否支持wss/https？**  
 workerman是支持的，可以参考文档 https://www.workerman.net/doc 来修改代码。  
 
 **3. 为什么有些歌曲无法点播？**  
 需要网易云音乐VIP的歌曲或者被下架的歌曲无法点播。  
 
 **4. 没人点歌的时候会自动点歌？**  
 是的。考虑到有时候没人点歌的话，系统会自动点播一首歌曲。系统会在初次启动和每天同步一次榜单歌曲，然后自动点歌。如果不需要该功能，可以修改 application/musical_timer.php ，将第68行 `Ws::sendOrder($song['song_id'], -1);` 注释掉。
