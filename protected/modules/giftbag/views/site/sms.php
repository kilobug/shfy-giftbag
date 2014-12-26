<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
?>
<?php defined('WEB_ROOT') or die('Access denied!');?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<title>短信验证码</title>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta charset="utf-8">
<meta name="keywords" content="">
<meta name="description" content="">
</head>
<body>
<div>
短信验证码：<?php echo $_SESSION['sms'];?>
</div>
</body>
</html>

