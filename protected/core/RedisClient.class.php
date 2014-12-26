<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class RedisClient implements IRedis {
	private $servers = array();
	public $serverId;
	
	private $redisInstances = array();
	private $redis;
	private $result;
	const MAX_MULTI = 1000;
	private $multiTotal = 0;
	
	/**
	 * (non-PHPdoc)
	 * @see IRedis::getServers()
	 */
	function getServers() {
		return $this->servers;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRedis::setServers()
	 */
	function setServers($servers) {
		foreach ($servers as $serverId => &$server) {
			$server['serverId'] = $serverId;
		}
		unset($server);
		$this->servers = $servers;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRedis::connect()
	 */
	function connect($code = NULL) {
		$this->serverId = isset($code) ? bcmod(sprintf("%u", crc32($code)), count($this->servers)) : array_rand($this->servers);
		$server = $this->servers[$this->serverId];
		return $this->setConnect($server);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRedis::setConnect()
	 */
	public function setConnect($server) {
		$this->serverId = $server['serverId'];
		$redis = &$this->redisInstances[$this->serverId];
		if(isset($redis) == false) {
			$redis = new Redis();
			$redis->connect($server['host'], $server['port'], $server['timeout'], $server['reserved']);
			$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
		}
		$this->redis = $redis;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRedis::multiChunk()
	 */
	function multiChunk($method) {
		$args = func_get_args();
		array_shift($args);
		if($this->multiTotal == 0) {
			$this->redis->multi();
		}
		call_user_func_array(array($this->redis, $method), $args);
		if(++$this->multiTotal >= self::MAX_MULTI) {
			$this->multiChunkFlush();
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRedis::multiChunkFlush()
	 */
	function multiChunkFlush() {
		if($this->multiTotal > 0) { 
			$this->redis->exec();
		}else{
			$this->redis->discard();
		}
		$this->multiTotal = 0;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRedis::__call()
	 */
	function __call($method, $args) {
		$this->result = call_user_func_array(array($this->redis, $method), $args);
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IRedis::getResult()
	 */
	function getResult() {
		return $this->result;
	}
}