<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class Controller {
	public $module;
	public $controller;
	public $action;
	
	private $openedSession = false;
	
	function beforeAction() {
		
	}
	
	function afterAction() {
		
	}
	
	/**
	 * 开启session
	 * @return Controller
	 */
	function openSession() {
		if($this->openedSession) return;
		session_start();
		$this->openedSession = true;
		return $this;
	}
	
	/**
	 * 客户端缓存
	 * @param string $interval 缓存间隔，默认=6小时
	 * @return Controller
	 */
	function clientCache($interval = NULL) {
		if(isset($interval) == FALSE) {
			$interval = 3600 * 6;
		}
		
		$time = $_SERVER['REQUEST_TIME'];
		$modifiedTime = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
		if(isset($modifiedTime) && strtotime($modifiedTime)+$interval > $time) {
			header("HTTP/1.1 304");
			exit;
		}
		
		header ("Last-Modified: " . gmdate ('r'));
		header ("Expires: " . gmdate ("r", ($time + $interval)));
		header ("Cache-Control: max-age=$interval");
		header("Pragma: cache");
		return $this;
	}
	
	function redirect($url) {
		header('Location:'.$url);
	}
	
	/**
	 * 渲染view
	 * @param string $name
	 * @param array $params
	 * @return Controller
	 */
	function render($name = NULL, $params = array()) {
		if(is_array($name)) {
			$params = $name;
			$name = null;
		}
		if(isset($name) == false) {
			$name = $this->action;
		}
		extract($params);
		require WEB_SYSTEM."/modules/{$this->module}/views/{$this->controller}/{$name}.php";
		return $this;
	}
	
	/**
	 * 兼容jsonp输出
	 * @param mixed $data
	 * @return Controller
	 */
	function ajax($data) {
		$callback = trim($_GET['callback']);
		if(preg_match('/^[a-z\d\_\-]+$/i', $callback)) {
			echo $callback, '(', json_encode($data), ')';
		}else{
			echo json_encode($data);
		}
		return $this;
	}
	
	/**
	 * 是否ajax请求
	 * @return boolean
	 */
	function isAjaxRequest() {
		$isJsonRequest = isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest";
		$isJsonpRequest = isset($_GET['callback']);
		return $isJsonRequest || $isJsonpRequest;
	}
}