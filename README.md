环境要求
---------------
* Linux
* php 5.2以上
* mysql 5以上
* redis 2.x以上
* phpredis

安装步骤
---------------
1. 导入SQL文件到mysql：giftbag.sql
2. 配置protected/config/的db.php、redis.php
3. 命令行-生成礼包码
	protected/commands/GiftBagCommand.php generate 1 100
4. crontab加入定时命令（可选）
	protected/commands/GiftBagCommand.php sync
5. 访问index.php



礼包领取（web）：
---------------
index.php

生成礼包码（cli）：
---------------
protected/commands/GiftBagCommand.php generate 1 100

> 1是campaignId（活动ID）
> 100是生成礼包码的数量

同步用户领取礼包码的关系到DB（cli）：
--------------------------------------------------
protected/commands/GiftBagCommand.php sync


