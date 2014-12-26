<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

return array(
	'default' => array(
		'servers' => array(
			array(
					'host' => '127.0.0.1',
					'port' => 6379,
					'timeout' => 0,
					'reserved' => null,
			),
		),
	),
	'user' => array(
		'servers' => array(
			array(
				'host' => '127.0.0.1',
				'port' => 6379,
				'timeout' => 0,
				'reserved' => null,
			),
		),
	),
	'giftbag' => array(
		'servers' => array(
			array(
				'host' => '127.0.0.1',
				'port' => 6379,
				'timeout' => 0,
				'reserved' => null,
			),
			array(
				'host' => '127.0.0.1',
				'port' => 6380,
				'timeout' => 0,
				'reserved' => null,
			),
			array(
				'host' => '127.0.0.1',
				'port' => 6381,
				'timeout' => 0,
				'reserved' => null,
			),
		),
	),
);