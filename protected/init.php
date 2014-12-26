<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
define("WEB_SYSTEM", dirname(__FILE__));
define("WEB_ROOT", WEB_SYSTEM.'/..');

require_once WEB_SYSTEM.'/core/Application.class.php';

Application::init();
