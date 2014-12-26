<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class DatabaseMysql implements IDatabase {
	const FETCH_STYLE = PDO::FETCH_ASSOC;
	
	public $pdo;
	private $statement;
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::connect()
	 */
	function connect($host, $port, $database, $user, $password, $charset) {
		try {
			$dsn = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";
			$driverOptions = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			);
			$this->pdo = new PDO($dsn, $user, $password, $driverOptions);
		} catch (PDOException $e) {
			die('Connection failed: ' . $e->getMessage());
		}
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::useDatabase()
	 */
	function useDatabase($database) {
		$this->execute("USE `{$database}`");
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::createCommand()
	 */
	function createCommand($statement) {
		$this->statement = $this->pdo->prepare($statement);
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::bindParams()
	 */
	function bindParams($params) {
		foreach ($params as $k => $v) {
			$this->statement->bindParam($k, $v);
		}
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::execute()
	 */
	function execute($params = array()) {
		$result = $this->statement->execute($params);
		if($result) {
			$result = $this->getAffectCount();
		}
		return $result;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::getInsertId()
	 */
	function getInsertId() {
		return $this->pdo->lastInsertId();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::getAffectCount()
	 */
	function getAffectCount() {
		return $this->statement->rowCount();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::find()
	 */
	function find() {
		 $row = $this->statement->fetch(self::FETCH_STYLE);
		 if(empty($row)) {
		 	$this->statement->closeCursor();
		 }
		 return $row;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::findAll()
	 */
	function findAll($params = array()) {
		$this->statement->execute($params);
		$rows = $this->statement->fetchAll(self::FETCH_STYLE);
		return $rows;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::findFirstColumn()
	 */
	function findFirstColumn($params = array()) {
		// :TODO
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::findFirst()
	 */
	function findFirst($params = array()) {
		$this->statement->execute($params);
		$row = $this->statement->fetch(self::FETCH_STYLE);
		$this->statement->closeCursor();
		return $row;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::findValue()
	 */
	function findValue($params = array()) {
		$this->statement->execute($params);
		$value = $this->statement->fetchColumn(0);
		$this->statement->closeCursor();
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::beginTransaction()
	 */
	function beginTransaction() {
		return $this->pdo->beginTransaction();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDatabase::commit()
	 */
	function commit() {
		return $this->pdo->commit();
	}

}