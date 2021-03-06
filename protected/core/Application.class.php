<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class Application {
	/**
	 * 自动加载
	 * @param string $className
	 */
	static function autoload($className) {
		foreach (array(
			'Service' => 'services',
			'Model' => 'models',
		) as $suffix => $dirName) {
			if(strrpos($className, $suffix) === strlen($className)-strlen($suffix)) {
				return require WEB_SYSTEM."/{$dirName}/{$className}.php";
			}
		}
		foreach (array('class', 'interface') as $k) {
			$path = WEB_SYSTEM."/core/{$className}.{$k}.php";
			if(file_exists($path)) {
				return require $path;
			}
		}
		die("Unknow class \"{$className}\"");
	}
	
	static function init() {
		/* 注册自动加载 */
		spl_autoload_register(__CLASS__.'::autoload');
	}

	static function consoleRun() {
		$argv = $GLOBALS['argv'];
		$className = basename($argv[0], '.php');
		$instance = new $className();
		if(isset($argv[1]) == false) {
			$argv[1] = ConfigFactory::get('controller', 'defaultCommand');
		}
		$instance->self = $argv[0];
		$instance->action = $argv[1];
		$method = 'action'.ucfirst($argv[1]);
		if(method_exists($instance, $method)) {
			call_user_func_array(array($instance, $method), array_slice($argv, 2));
		}else{
			$methods = array();
			foreach(get_class_methods($className) as $name) {
				if(strpos($name, 'action') === 0) {
					$methods[] = lcfirst(substr($name, 6));
				}
			}
			$options = empty($methods) ? '' : '['.implode('|', $methods).']';
			$instance->usage('', $options);
		}
	}
	
	/**
	 * 运行mvc
	 */
	static function mvcRun() {

		/* mvc入口 */
		$config = ConfigFactory::get('controller');
		foreach (array(
				'module' => 'm',
				'controller' => 'c',
				'action' => 'a',
		) as $k => $v) {
			$$k = trim($_GET[$v]);
			if(empty($$k)) {
				$$k = $config['default'.ucfirst($k)];
			}
		}
		$classController = ucfirst($controller) . 'Controller';
		$path = "$module/controllers/{$classController}";
		if(preg_match("/^([a-z\d\_\-]+\/){2}[a-z\d\_\-]+$/i", $path)
				&& file_exists(WEB_SYSTEM."/modules/{$path}.php")) {
			require WEB_SYSTEM."/modules/{$path}.php";
			$instance = new $classController();
			$instance->module = $module;
			$instance->controller = $controller;
			$instance->action = $action;
			$methodAction = 'action'.$action;
			if(method_exists($instance, $methodAction)) {
				call_user_func(array($instance, 'beforeAction'));
				call_user_func(array($instance, $methodAction));
				call_user_func(array($instance, 'afterAction'));
				return true;
			}
		}
		die('Invalid params (path:'.htmlspecialchars($path).')');
		return false;
	}
}
