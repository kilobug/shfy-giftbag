<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class CampaignService {
	private static $REDIS_KEYS = array(
			// key -> campaignData
			'getById' => 'campaign/id/%s',
	);
	
	/**
	 * 获取campaign
	 * @param int $campaignId
	 * @return array
	 */
	static function getById($campaignId) {
		$redis = RedisFactory::open()->connect($campaignId);
		$key = sprintf(self::$REDIS_KEYS['getById'], $campaignId);
		$result = $redis->get($key)->getResult();
		if($result === false) {
			// 初始化
			$db = DatabaseFactory::open();
			$result = $db->createCommand("SELECT * FROM g_campaign WHERE id=:id")->findFirst(array(':id' => $campaignId,));
			if(empty($result)) $result = array();
			$redis->set($key, $result);
		}
		return $result;
	}
}