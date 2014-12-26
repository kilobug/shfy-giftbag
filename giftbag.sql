-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.5.40-MariaDB-0ubuntu0.14.04.1 - (Ubuntu)
-- 服务器操作系统:                      debian-linux-gnu
-- HeidiSQL 版本:                  9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出  表 giftbag.g_campaign 结构
DROP TABLE IF EXISTS `g_campaign`;
CREATE TABLE IF NOT EXISTS `g_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '活动名',
  `startTime` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `endTime` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间，0=永不过期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='活动';

-- 正在导出表  giftbag.g_campaign 的数据：~1 rows (大约)
/*!40000 ALTER TABLE `g_campaign` DISABLE KEYS */;
INSERT IGNORE INTO `g_campaign` (`id`, `name`, `startTime`, `endTime`) VALUES
	(1, '好活动', 0, 0);
/*!40000 ALTER TABLE `g_campaign` ENABLE KEYS */;


-- 导出  表 giftbag.g_campaign_mapping_giftbag 结构
DROP TABLE IF EXISTS `g_campaign_mapping_giftbag`;
CREATE TABLE IF NOT EXISTS `g_campaign_mapping_giftbag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaignId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `giftbagCode` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaignId` (`campaignId`,`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='活动与礼包兑换码的mapping';

-- 正在导出表  giftbag.g_campaign_mapping_giftbag 的数据：~0 rows (大约)
/*!40000 ALTER TABLE `g_campaign_mapping_giftbag` DISABLE KEYS */;
INSERT IGNORE INTO `g_campaign_mapping_giftbag` (`id`, `campaignId`, `userId`, `giftbagCode`) VALUES
	(1, 1, 1, '5257588E-6D544967-51271ABF-C9F6C92E');
/*!40000 ALTER TABLE `g_campaign_mapping_giftbag` ENABLE KEYS */;


-- 导出  表 giftbag.g_user 结构
DROP TABLE IF EXISTS `g_user`;
CREATE TABLE IF NOT EXISTS `g_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `salt` char(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 正在导出表  giftbag.g_user 的数据：~13 rows (大约)
/*!40000 ALTER TABLE `g_user` DISABLE KEYS */;
INSERT IGNORE INTO `g_user` (`id`, `username`, `mobile`, `password`, `salt`) VALUES
	(1, 'kobe', '18613132926', '440b083de0b459db38d15b3836b4996e', '123'),
	(15, 'admin', '18613132927', 'fbb9fb15b4a24a85cb9df88bf0ebdcc8', 'tVGcTAZPNW');
/*!40000 ALTER TABLE `g_user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
