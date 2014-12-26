<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

interface IDatabase {
	/**
	 * 连接数据
	 * @param string $host
	 * @param int $port
	 * @param string $database
	 * @param string $user
	 * @param string $password
	 * @param string $charset
	 * @return boolean
	 */
	function connect($host, $port, $database, $user, $password, $charset);
	
	/**
	 * 使用指定数据库
	 * @param string $database
	 * @return boolean
	 */
	function useDatabase($database);
	
	/**
	 * 创建命令
	 * @param string $statement
	 * @return IDatabase
	 */
	function createCommand($statement);
	
	/**
	 * 绑定多个参数
	 * @param array $params
	 * @return IDatabase
	 */
	function bindParams($params);
	
	/**
	 * 执行命令
	 * @param array $params
	 * @return int
	 */
	function execute($params = array());
	
	/**
	 * 获取插入ID
	 * @return int 最近的插入ID
	 */
	function getInsertId();
	
	/**
	 * 获取影响行数
	 * @return int
	 */
	function getAffectCount();
	
	/**
	 * 获取单条记录
	 * @return array
	 */
	function find();
	
	/**
	 * 获取结果集
	 * @param array $params
	 * @return array
	 */
	function findAll($params = array());
	
	/**
	 * 获取首行
	 * @param array $params
	 * @return array
	 */
	function findFirst($params = array());
	
	/**
	 * 获取所有行的首列
	 * @param unknown $params
	 * @return array
	 */
	function findFirstColumn($params = array());
	
	/**
	 * 获取首行首列
	 * @param array $params
	 * @return mixed
	 */
	function findValue($params = array());
	
	/**
	 * 开始事务
	 * @return boolean
	 */
	function beginTransaction();
	
	/**
	 * 提交事务
	 * @return boolean
	 */
	function commit();

}