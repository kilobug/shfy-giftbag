<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class GiftBagService {
	const REDIS_CONFIG = 'giftbag';
	public static $REDIS_KEYS = array(
			// list -> giftbagCode[] 活动的礼包码
			'codeList' => 'giftbag/%s/codeList',
			// key -> bool 是否已无兑换码，减少轮询列表
			'codeIsEmpty' => 'giftbag/%s/codeIsEmpty',
			
			// sets -> array(userId,giftbagCode)[] 活动新领取的关系
			'receiveAdd' => 'giftbag/%s/userReceive/add',
			// sets -> giftbagCode[]
			'receiveByUserId' => 'giftbag/%s/userReceive/%s',
			// key -> bool
			'receiveByUserIdIsInit' => 'giftbag/%s/userReceive/%s/isInit',
	);
	
	/**
	 * 获取redis
	 * @return IRedis
	 */
	static function getRedis() {
		return RedisFactory::open(self::REDIS_CONFIG);
	}
	
	/**
	 * 生成礼包兑换码
	 * @param unknown $campaignId
	 * @param string $salt
	 * @return Ambigous <void, string>
	 */
	static function generate($campaignId, $salt = '', $expiry = 0) {
		$code = $campaignId.'|'.microtime().'|'.md5($salt);
		$encode = md5(md5($code));
		$newEncode = '';
		for($i = 0; $i < 4; ++$i) {
			$newEncode .= substr($encode, $i*8, 8) . '-';
		}
		$newEncode = strtoupper(substr($newEncode, 0, -1));
		return $newEncode;
	}
	
	/**
	 * 生成多个礼包
	 * @param IRedis $redis
	 * @param int $campaignId
	 * @param int $quantity
	 */
	static function generateMulti($campaignId, $quantity = 1) {
		// 函数外指定生成到哪台server
		$redis = self::getRedis();
		$key = sprintf(self::$REDIS_KEYS['codeList'], $campaignId);
		for($i = $quantity;$i > 0; --$i) {
			$redis->multiChunk('rpush', $key, self::generate($campaignId, $i));
		}
		$redis->multiChunkFlush();
	}
	
	/**
	 * 根据campaignId获取礼包总数
	 * @param int $campaignId
	 * @return array(total, listTotal)
	 */
	static function getTotalByCampaignId($campaignId) {
		$redis = self::getRedis();
		$servers = $redis->getServers();
		$key = sprintf(self::$REDIS_KEYS['codeList'], $campaignId);
		$listTotal = array();
		$total = 0; $n = 0;
		foreach($servers as $i => $server) {
			$redis->setConnect($server);
			$n = $redis->llen($key)->getResult();
			$total += $n;
			$listTotal[$i] = $n;
		}
		return array($total, $listTotal,);
	}
	
	
	/**
	 * 领取礼包（调用前判断用户是否已领取）
	 * @param int $campaignId
	 * @param int $userId
	 * @return string
	 */
	static function receive($campaignId, $userId) {
		// 随机选择server
		$redis = self::getRedis()->connect();
		$keyCodeList = sprintf(self::$REDIS_KEYS['codeList'], $campaignId);
		$keyIsEmpty = sprintf(self::$REDIS_KEYS['codeIsEmpty'], $campaignId);
		$result = $redis->multi()
							// 弹出一个兑换码
							->lpop($keyCodeList)
							// 当没兑换码，则返回true，避免轮询其它列表
							->get($keyIsEmpty)
							->exec()->getResult();
		list($giftCode, $isEmpty) = $result;
		if(empty($giftCode) && $isEmpty == false) {
			$servers = $redis->getServers();
			$len = count($servers);
			if($len > 1) {
				// 就近尝试，避免集中访问某个server
				$serverId = $redis->serverId;
				for($i = $serverId+1;;++$i) {
					if($i >= $len) $i = 0;
					if($i == $serverId) break;
					$giftCode = $redis->setConnect($servers[$i])->lpop($keyCodeList)->getResult();
					if(empty($giftCode) == false) break;
				}
			}
			// 轮询后没有兑换码，则标记每个server的isEmpty
			if(empty($giftCode)) {
				self::setIsEmpty($campaignId, true);
			}
		}
		return $giftCode;
	}
	
	/**
	 * 设置礼包已空
	 * @param int $campaignId
	 * @param bool $isEmpty
	 */
	static function setIsEmpty($campaignId, $isEmpty) {
		$key = sprintf(self::$REDIS_KEYS['codeIsEmpty'], $campaignId);
		$redis = self::getRedis();
		$servers = $redis->getServers();
		foreach ($servers as $serverId => $server) {
			$redis->setConnect($server)->set($key, $isEmpty);
		}
	}
	
	/**
	 * 已领取总数（redis重启后，通过command从DB导入到redis）
	 * @param int $campaignId
	 * @param int $userId
	 */
	static function receivedTotal($campaignId, $userId) {
		$redis = self::getRedis()->connect($userId);
		$keyReceiveByUserId = sprintf(self::$REDIS_KEYS['receiveByUserId'], $campaignId, $userId);
		$keyReceiveByUserIdIsInit = sprintf(self::$REDIS_KEYS['receiveByUserIdIsInit'], $campaignId, $userId);
		$result = $redis->multi()
						->scard($keyReceiveByUserId)
						->get($keyReceiveByUserIdIsInit)
						->exec()->getResult();
		list($total, $isInit) = $result;
		if($isInit == false) {
			// 初始化该用户的领取信息
			$db = DatabaseFactory::open();
			$rows = $db->createCommand('
									SELECT giftbagCode FROM g_campaign_mapping_giftbag
									WHERE campaignId=:campaignId AND userId=:userId
							')->findAll(array(
								':campaignId' => $campaignId,
								':userId' => $userId,
							));
			if($rows) {
				// 同步到领取列表
				foreach ($rows as $row) {
					$redis->multiChunk('sadd', $keyReceiveByUserId, $row['giftbagCode']);
				}
				$redis->multiChunkFlush();
			}
			// 获取最新的总数
			$result = $redis->multi()
								->scard($keyReceiveByUserId)
								->set($keyReceiveByUserIdIsInit, true)
								->exec()->getResult();
			list($total, $isInit) = $result;
		}
		return $total;
	}
	
	/**
	 * 记住领取礼包
	 * @param int $campaignId
	 * @param int $userId
	 * @param string $giftbagCode
	 */
	static function rememberReceive($campaignId, $userId, $giftbagCode) {
		// 添加mapping到新增列表（定时脚本同步到数据库）
		$redis = self::getRedis();
		$redis->connect($campaignId);
		$keyReceiveAdd = sprintf(self::$REDIS_KEYS['receiveAdd'], $campaignId);
		$redis->sadd($keyReceiveAdd, array($userId, $giftbagCode));
		
		// 增加礼包代码到campaign+user的列表
		$redis->connect($userId);
		$keyReceiveByUserId = sprintf(self::$REDIS_KEYS['receiveByUserId'], $campaignId, $userId);
		$redis->sadd($keyReceiveByUserId, $giftbagCode);
		return true;
	}
}