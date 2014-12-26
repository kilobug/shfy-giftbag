<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class Command {
	public $self;
	public $action;
	
	public function usage($param = '', $action = NULL) {
		if(isset($action) == false) {
			$action = $this->action;
		}
		echo "Usage: {$this->self} {$action} {$param}\n";
		exit;
	}
}