<?php 
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');
class StringUtil {
	static function random($length = 10) {
		$chars = 'abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$max = strlen($chars)-1;
		$str = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$str .= $chars[mt_rand(0, $max)];
		}
		return $str;
	}
}