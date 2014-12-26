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
<title>登陆</title>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta charset="utf-8">
<meta name="keywords" content="">
<meta name="description" content="">
</head>
<body>
<form action="" method="post">
<div>
账号：<input type="text" name="username" value="">
</div>
<div>
密码：<input type="password" name="password" value="">
</div>
<div>
验证码：<input type="text" name="captcha" value="" maxlength="4"><br/>
<img src="?a=captcha&<?php echo $_SERVER['REQUEST_TIME'];?>" onclick="this.src='?a=captcha&'+Math.random()">
</div>
<div style="font-weight:bold;color:red;"><?php echo $errorTip;?></div>
<input type="submit" value="登陆">
</form>
</body>
</html>

