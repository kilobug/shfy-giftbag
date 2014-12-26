<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class RedisFactory {
	
	static $INSTANCES = array();
	
	/**
	 * 获取redis client实例
	 * @param string $key
	 * @return IRedis
	 */
	static function open($key = 'default') {
		$instance = &self::$INSTANCES[$key];
		if(isset($instance) == false) {
			$config = ConfigFactory::get('redis', $key);
			if(empty($config)) die("Undefined Redis Config \"{$key}\"");
			$instance = new RedisClient();
			$instance->setServers($config['servers']);
		}
		return $instance;
	}
}