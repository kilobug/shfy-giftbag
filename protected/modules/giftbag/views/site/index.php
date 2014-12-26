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
<title>盛世三国2首页</title>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta charset="utf-8">
<meta name="keywords" content="">
<meta name="description" content="">
<script>
var assetsVersion = '<?php echo ConfigFactory::get('controller', 'assetsVersion');?>';
var campaignId = <?php echo $campaignId;?>;
</script>
<script src="http://www.sinaimg.cn/gm/hd/hpct/2014/1118/sssy/jquery.min.1.10.1.js"></script>
<script src="http://www.sinaimg.cn/gm/hd/hpct/2014/1119/ssgw/index.js?abc"></script>
<script src="/assets/js/controller/campaign.js?<?php echo ConfigFactory::get('controller', 'assetsVersion');?>"></script>
<link rel="stylesheet" href="http://wanwan.sina.com.cn/9/2014/1118/244.css?abc" >
<!--[if IE 6]>
<script type="text/javascript" src="http://www.sinaimg.cn/gm/webgame/dh/DD_belatedPNG_0.0.8a-min.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix('.header span,.start,.wd-down,.nav,.gamelogo a,.header div,.header div a,.number span,.newsimg i,.zclbbox,.zclbbox-b,.llb-box-tt .close')
</script>
<![endif]-->
</head>
<body>
<div class="windowblack" onclick="closeAll();"></div>
<!-- 站外注册 begin -->
<div id="outside" class="llb-box">
    <div class="llb-box-tt"><a class="close" href="javascript:closeAll();" title="关闭">关闭</a></div>
    <div class="llb-exp">2014年11月26日至12月3日注册游戏的玩家可领取：<span>50元RMB</span>元宝礼包激活码。<br /><span>每个手机号码只可领取一次礼包</span>，且每个玩家账号只可激活一次该礼包。<br />请填写真实手机号码，妥善保存收到的rmb激活码，开服当天进游戏激活。</div>
    <!-- 注册 begin -->
    <div class="llb-cont" style="display:none">
	    <form>
	    <ul>
	        <li><span>用户名：</span><input type="text" id="outsideUsername" value="" /><i>用户名已存在</i></li>
	        <li><span>密码：</span><input type="password" id="outsidePassword" value="" /><i>两次密码不一致</i></li>
	        <li><span>确认密码：</span><input type="password" id="outsidePassword2" value="" /></li>
	        <li><span>手机号：</span><input type="text" value="" id="outsideMobile" /><i>手机号错误</i></li>
	        <li><span>验证码：</span><input type="text" class="yzmtext" id="outsideCaptcha" value="" /><img src="" data-orgin="?a=captcha" class="yzm" title="验证码 点击刷新" alt="" /><i>验证码错误</i></li>
	    </ul>
	    <div class="llb-btna"><a href="javascript:;" title="注册领礼包">注册领礼包</a></div>
	    </form>
	    <div class="llb-login"><a href="?a=login">我有账号 去登录</a></div>
    </div>
    <!-- 注册 end -->
    <!-- 成功 begin -->
    <div class="llb-cont-ok" style="display:none">
        <strong>恭喜您注册成功</strong>
        <p class="line1">您的账号为：<i class="insideUsername">zhuliang19869509</i></p>
        <span>领兑换码</span>
        <p>游戏礼包兑换码将以手机短信方式发放,请留意查收！</p>
        <div class="line2"></div>
        <span>激活礼包</span>
        <p>12月3日用户可进入游戏内兑换页面激活并领取。</p>
    </div>
    <!-- 成功 end -->
    <!-- 失败 begin -->
    <div class="llb-cont-no">
    <strong>加载中</strong>
    <p class="line1"></p>
    </div>
    <!-- 失败 end -->
</div>
<!-- 站外注册 end -->

<!-- 站内注册 begin -->
<div id="inside" class="llb-box" style="display:none">
    <div class="llb-box-tt"><a class="close" href="javascript:closeAll();" title="关闭">关闭</a></div>
    <div class="llb-exp">2014年11月26日至12月3日注册游戏的玩家可领取：<span>50元RMB</span>元宝礼包激活码。<br /><span>每个手机号码只可领取一次礼包</span>，且每个玩家账号只可激活一次该礼包。<br />请填写真实手机号码，妥善保存收到的rmb激活码，开服当天进游戏激活。</div>
    <!-- 领取 begin -->
    <div class="llb-cont">
	    <p><em>亲爱的，</em><span class="insideUsername"></span><a href="?a=logout" id="logout"><em>[退出]</em></a><br />您已经注册成功！填写资料验证，即可领取礼包金额为50元RMB<br />等值元宝礼包。</p>
	    <form>
	    <ul>
	        <li id="insideMobileLine"><span>手机号：</span><input id="insideMobile" type="text" value="" /><i>手机号错误</i></li>
	        <li><span>验证码：</span><input id="insideCaptcha" type="text" class="yzmtext" value="" /><img src="" class="yzm" data-orgin="?a=captcha" title="验证码 点击刷新" alt="" /><i>验证码错误</i></li>
	    </ul>
	    <div class="llb-btnb"><a href="javascript:;" title="提交领取礼包">提交领取礼包</a></div>
	    </form>
    </div>
    <!-- 领取 end -->
    <!-- 成功 begin -->
    <!-- 成功 begin -->
    <div class="llb-cont-ok" style="display:none">
        <strong>恭喜您注册成功</strong>
        <p class="line1">您的账号为：<i class="insideUsername">zhuliang19869509</i></p>
        <span>领兑换码</span>
        <p>游戏礼包兑换码将以手机短信方式发放,请留意查收！</p>
        <div class="line2"></div>
        <span>激活礼包</span>
        <p>12月3日用户可进入游戏内兑换页面激活并领取。</p>
    </div>
    <!-- 成功 end -->
    <!-- 失败 begin -->
    <div class="llb-cont-no">
    <strong>加载中</strong>
    <p class="line1"></p>
    </div>
    <!-- 失败 end -->
</div>
<!-- 站内注册 end -->

<div id="floatright"></div>
</body>
</html>

