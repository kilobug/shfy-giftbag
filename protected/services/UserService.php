<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class UserService {
	const REDIS_CONFIG = 'user';
	private static $REDIS_KEYS = array(
		// key -> userData
		'getById' => 'user/id/%s',
		// key -> id
		'getByUsername' => 'user/username/%s',
		// key -> id
		'getByMobile' => 'user/mobile/%s',
	);
	
	/**
	 * 获取redis
	 * @return IRedis
	 */
	static function getRedis() {
		return RedisFactory::open(self::REDIS_CONFIG);
	}
	
	/**
	 * 根据id获取用户信息
	 * @param int $userId
	 * @return array
	 */
	static function getById($userId) {
		$redis = self::getRedis()->connect($userId);
		$key = sprintf(self::$REDIS_KEYS['getById'], $userId);
		$result = $redis->get($key)->getResult();
		if($result === false) {
			// 初始化
			$db = DatabaseFactory::open();
			$result = $db->createCommand("SELECT * FROM g_user WHERE id=:id")
								->findFirst(array(':id' => $userId,));
			if(empty($result)) $result = array();
			$redis->set($key, $result);
		}
		return $result;
	}
	
	/**
	 * 根据用户名获取用户信息
	 * @param string $username
	 * @param bool $onlyId
	 * @return mixed userId or userData
	 */
	static function getByUsername($username, $onlyId = true) {
		$redis = self::getRedis()->connect($username);
		$key = sprintf(self::$REDIS_KEYS['getByUsername'], $username);
		$userId = $redis->get($key)->getResult();
		if($userId === false) {
			// 初始化
			$db = DatabaseFactory::open();
			$userId = $db->createCommand("SELECT id FROM g_user WHERE username=:username")
								->findValue(array(':username' => $username,));
			if(empty($userId)) $userId = 0;
			$redis->set($key, $userId);
		}
		$result = $userId && $onlyId == false ? self::getById($userId) : $userId;
		return $result;
	}
	
	/**
	 * 根据手机号获取用户信息
	 * @param string $mobile
	 * @param bool $onlyId
	 * @return mixed userId or userData
	 */
	static function getByMobile($mobile, $onlyId = true) {
		$redis = self::getRedis()->connect($mobile);
		$key = sprintf(self::$REDIS_KEYS['getByMobile'], $mobile);
		$userId = $redis->get($key)->getResult();
		if($userId === false) {
			// 初始化
			$db = DatabaseFactory::open();
			$userId = $db->createCommand("SELECT id FROM g_user WHERE mobile=:mobile LIMIT 1")
								->findValue(array(':mobile' => $mobile,));
			if(empty($userId)) $userId = 0;
			$redis->set($key, $userId);
		}
		$result = $userId && $onlyId == false ? self::getById($userId) : $userId;
		return $result;
	}
	
	/**
	 * 修改用户手机号
	 * @param int $userId
	 * @param int $mobile
	 * @return bool
	 */
	static function updateMobileByUserId($userId, $mobile) {
		$redis = self::getRedis()->connect($mobile);
		$db = DatabaseFactory::open();
		$result = $db->createCommand("UPDATE g_user SET mobile=:mobile WHERE id=:id")
							->execute(array(':id' => $userId, ':mobile' => $mobile,));
		if($result) {
			$key = sprintf(self::$REDIS_KEYS['getByMobile'], $mobile);
			$redis->set($key, $userId);
			// 删除id的值
			$key = sprintf(self::$REDIS_KEYS['getById'], $userId);
			$redis->del($key);
		}
		return $result;
	}
	
	/**
	 * 新增用户
	 * @param array $userData
	 * @return int insertId
	 */
	static function add($userData) {
		$salt = StringUtil::random(10);
		// 初始化
		$db = DatabaseFactory::open();
		$result = $db->createCommand("
			INSERT INTO `g_user` (username,mobile,password,salt)
				VALUES (:username,:mobile,:password,:salt)
		")->execute(array(
			':username' => $userData['username'],
			':mobile' => $userData['mobile'],
			':password' => self::encodePassword($userData['password'], $salt),
			':salt' => $salt,
		));
		if($result) {
			$userId = $db->getInsertId();
			$redis = self::getRedis();
			$redis->connect($userId)->del(sprintf(self::$REDIS_KEYS['getById'], $userId));
			$redis->connect($userData['username'])->del(sprintf(self::$REDIS_KEYS['getByUsername'], $userData['username']));
			$redis->connect($userData['mobile'])->del(sprintf(self::$REDIS_KEYS['getByMobile'], $userData['mobile']));
			return $userId;
		}else{
			return false;
		}
	}
	
	/**
	 * 加密密码
	 * @param string $plainPassword
	 * @param string $salt
	 * @return string
	 */
	static function encodePassword($plainPassword, $salt) {
		return md5(md5($plainPassword).md5($salt));
	}
}