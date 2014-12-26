<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

interface IRedis {
	/**
	 * 获取服务器列表
	 * @return array
	 */
	function getServers();
	/**
	 * 设置服务器列表
	 * @param array $servers
	 */
	function setServers($servers);
	/**
	 * 设置当前连接配置
	 * @param array $server
	 * @return IRedis
	 */
	public function setConnect($server);
	
	/**
	 * 根据key哈希选择服务，不填随机
	 * @param string $code
	 * @return IRedis
	 */
	function connect($code = NULL);
	
	/**
	 * 批量操作，适用分块提交大量操作
	 * @param string $method
	 * @param array $args..
	 */
	function multiChunk($method);
	
	/**
	 * 配合multiOperate方法
	 */
	function multiChunkFlush();
	
	/**
	 * 调用phpredis
	 * @param string $method
	 * @param array $args
	 * @return IRedis
	 */
	function __call($method, $args);
	
	/**
	 * 获取最后结果集
	 * @return mixed
	 */
	function getResult();
}