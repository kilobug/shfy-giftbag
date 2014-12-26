<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class Util {
	static function validateCaptcha($captcha, $key = 'captcha') {
		$oldCaptcha = $_SESSION[$key];
		if(empty($oldCaptcha) == false) {
			$_SESSION[$key] = null;
			return $oldCaptcha == strtolower(trim($captcha));
		}
		return false;
	}
}
