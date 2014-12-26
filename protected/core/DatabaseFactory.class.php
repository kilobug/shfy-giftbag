<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class DatabaseFactory {
	static $INSTANCES = array();
	
	/**
	 * 获取单例的数据库实例
	 * @param string $key
	 * @return IDatabase
	 */
	static function open($key = 'default') {
		$instance = &self::$INSTANCES[$key];
		if(isset($instance) == false) {
			$config = ConfigFactory::get('db', $key);
			if(empty($config)) die("Undefined Database Config \"{$key}\"");
			$className = 'Database' . ucfirst($config['type']);
			$instance = new $className();
			$instance->connect(
					$config['host'], $config['port'], $config['database'],
					$config['user'], $config['password'], $config['charset']);
		}
		return $instance;
	}
}
