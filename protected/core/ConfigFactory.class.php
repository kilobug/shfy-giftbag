<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class ConfigFactory {
	private static $data = array();
	static function get($name, $key = NULL) {
		$data = &self::$data[$name];
		if(isset($data) == false) {
			$data = require_once WEB_SYSTEM."/config/{$name}.php";
		}
		return isset($key) ? $data[$key] : $data;
	}
}
