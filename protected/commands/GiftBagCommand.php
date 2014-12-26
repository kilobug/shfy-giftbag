#!/usr/bin/env php
<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
require dirname(__FILE__).'/../init.php';

/**
 * 礼包批处理
 * @author kobe
 *
 */
class GiftBagCommand extends Command {
	
	/**
	 * 生成礼包
	 * @param int $campaignId
	 */
	function actionGenerate($campaignId, $quantity = 10) {
		$campaignId = (int)$campaignId;
		if(empty($campaignId)) $this->usage('campaignId [quntity=10]');
		
		$redis = GiftBagService::getRedis();
		$servers = $redis->getServers();
		// 获取总数
		list($total, $listTotal) = GiftBagService::getTotalByCampaignId($campaignId);
	
		// 平均生成
		$avg = floor(($total+$quantity)/count($listTotal));
		$allocQuantity = $quantity;
		foreach ($listTotal as $i => $num) {
			$allocs = $avg-$num;
			if($allocs > 0) {
				$redis->setConnect($servers[$i]);
				// 开始生成
				GiftBagService::generateMulti($campaignId, min($allocs, $allocQuantity));
				$allocQuantity -= $allocs;
				if($allocQuantity <= 0) break;
			}
		}
		if($allocQuantity > 0) {
			GiftBagService::generateMulti($campaignId, $allocQuantity);
		}
		
		// 删除isEmpty标记
		GiftBagService::setIsEmpty($campaignId, false);
		
		echo "Success generate {$quantity} gift bag.\n";
	}
	
	/**
	 * 同步redis到数据库
	 */
	function actionSync() {
		$db = DatabaseFactory::open();
		$redis = GiftBagService::getRedis();
		// 同步步长
		$syncStep = 1000;
		$syncTotal = 0;
		// 遍历所有campaign，获取新领取的userId
		$campaigns = $db->createCommand('SELECT id FROM g_campaign')->findAll();
		if($campaigns) {
			foreach ($campaigns as $campaign) {
				$campaignId = $campaign['id'];
				$redis->connect($campaignId);
				$keyReceiveAdd = sprintf(GiftBagService::$REDIS_KEYS['receiveAdd'], $campaignId);
				$total = $redis->scard($keyReceiveAdd)->getResult();
				if($total <= 0) continue;
				// 同步新领取的用户关系到db
				for($i = $total; $i > 0; $i-=$syncStep) {
					// 按步长批量获取userId和giftbagCode
					$redis->connect($campaignId)->multi();
					for($j = min($i, $syncStep); $j > 0; --$j) {
						$redis->spop($keyReceiveAdd);
					}
					$result = $redis->exec()->getResult();
					foreach($result as $item) {
						list($userId, $code) = $item;
						++$syncTotal;
						$this->insertMapping($campaignId, $userId, $code);
					}
				}
			}
			$this->insertMappingFlush();
		}
		echo "Success Synchronized {$syncTotal} mapping records\n";
	}
	
	private $mappingValues = array();
	/**
	 * 批量插入 giftbag code - campaign - user的mapping
	 * @param int $campaignId
	 * @param int $userId
	 * @param string $giftbagCode
	 */
	private function insertMapping($campaignId, $userId, $giftbagCode) {
		$campaignId = (int)$campaignId;
		$userId = (int)$userId;
		$giftbagCode = addslashes($giftbagCode);
		$this->mappingValues[] = "{$campaignId},{$userId},'{$giftbagCode}'";
		if(count($this->mappingValues) > 1000) {
			$this->insertMappingFlush();
		}
	}
	
	/**
	 * 批量插入 giftbag code - campaign - user的mapping的最终操作
	 */
	private function insertMappingFlush() {
		if(empty($this->mappingValues) == false) {
			$db = DatabaseFactory::open();
			$db->createCommand('
				INSERT INTO g_campaign_mapping_giftbag (campaignId,userId, giftbagCode)
				VALUES ('.implode('),(', $this->mappingValues).')
			')->execute();
			$this->mappingValues = array();
		}
	}
}

Application::consoleRun();
