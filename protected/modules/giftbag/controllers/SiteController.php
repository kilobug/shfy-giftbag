<?php
/**
 * 上海风游信息科技的php测试题
 * @author i@oao.me(Kobe Wang)
 * @date 2014-12-26
 * @copyright Copyright (c) 2014 Kobe wang Inc
 */
defined('WEB_ROOT') or die('Access denied!');

class SiteController extends Controller {
	/**
	 * 首页
	 */
	function actionIndex() {
		$this->clientCache();
		
		$campaignId = (int)$_GET['id'];
		if(empty($campaignId) || $campaignId < 1) {
			$campaignId = 1;
		}
		
		$this->render(array(
			'campaignId' => $campaignId,
		));
	}
	
	/**
	 * 活动状态
	 */
	function actionStatus() {
		$this->openSession();
		
		$id = (int)$_GET['id'];
		$campaign = CampaignService::getById($id);
		if(empty($campaign) == false) {
			$campaign = array(
				'isStarted' => $campaign['startTime'] < $_SERVER['REQUEST_TIME'],
				'isExpired' => 0 < $campaign['endTime'] && $campaign['endTime'] < $_SERVER['REQUEST_TIME'],
			);
		}
		
		$userId = $_SESSION['userId'];
		$user = null;
		if(isset($userId)) {
			$user = UserService::getById($userId);
			if($user) {
				$user = array(
					'id' => $user['id'],
					'username' => $user['username'],
					'mobile' => $user['mobile'],
				);
			}
		}
		
		$this->ajax(array(
			'user' => $user,
			'campaign' => $campaign,
		));
	}
	
	/**
	 * 登陆页
	 */
	function actionLogin() {
		$this->openSession();
		
		if($_POST) {
			$username = trim($_POST['username']);
			$password = trim($_POST['password']);
			$captcha = trim($_POST['captcha']);
			
			$errorTip = '';
			if(Util::validateCaptcha($captcha) == false) {
				$errorTip = '验证码错误';
			}else{
				$user = UserService::getByUsername($username, false);
				if(empty($user) || UserService::encodePassword($password, $user['salt']) != $user['password']) {
					$errorTip = '账号或密码错误';
				}else{
					$_SESSION['userId'] = $user['id'];
				}
			}
		}
		// 已登陆
		if(isset($_SESSION['userId'])) {
			$this->redirect('?');
			exit;
		}
		
		$this->render(array(
			'errorTip' => $errorTip,
		));
	}
	
	/**
	 * 退出
	 */
	function actionLogout() {
		$this->openSession();
		$_SESSION['userId'] = NULL;
		if($this->isAjaxRequest()) {
			$this->ajax(true);
		}else{
			$this->redirect('?');
		}
	}
	
	/**
	 * 领取礼包
	 */
	function actionReceive() {
		$this->openSession();
		$campaignId = (int)$_GET['id'];
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$mobile = trim($_POST['mobile']);
		$captcha = trim($_POST['captcha']);
		$userId = $_SESSION['userId'];
		
		// 验证码
		if(Util::validateCaptcha($captcha) == false) {
			$this->ajax(array('message' => '验证码错误',)); exit;
		}

		// 活动状态
		$campaign = CampaignService::getById($campaignId);
		if(empty($campaign)) {
			$this->ajax(array('message' => '未知活动',)); exit;
		}elseif($campaign['startTime'] > $_SERVER['REQUEST_TIME']) {
			$this->ajax(array('message' => '活动未开始',)); exit;
		}elseif(0 < $campaign['endTime'] && $campaign['endTime'] < $_SERVER['REQUEST_TIME']) {
			$this->ajax(array('message' => '活动已结束',)); exit;
		}
		
		if(empty($mobile)) {
			$this->ajax(array('message' => '请输入手机号',)); exit;
		}

		if(isset($userId)) {
			/* 用户已领取 */
			if(GiftBagService::receivedTotal($campaignId, $userId) > 0) {
				$this->ajax(array('message' => '您已领取过',)); exit;
			}
			$userData = UserService::getById($userId);
			if($userData['mobile'] != $mobile) {
				$this->ajax(array('message' => '手机号不一致',)); exit;
			}
		}else{
			if(empty($username)) {
				$this->ajax(array('message' => '请输入用户名',)); exit;
			}
			
			if(empty($password)) {
				$this->ajax(array('message' => '请输入密码',)); exit;
			}
			
			// 用户名检测
			if(UserService::getByUsername($username)) {
				$this->ajax(array('message' => '用户名已存在',)); exit;
			}
			
			// 手机号码检测
			if(UserService::getByMobile($mobile)) {
				$this->ajax(array('message' => '手机号已存在',)); exit;
			}
			
			$userId = UserService::add(array(
				'username' => $username,
				'mobile' => $mobile,
				'password' => $password,
			));
			if(empty($userId)) {
				$this->ajax(array('message' => '注册失败！请重试',)); exit;
			}
			// 登陆
			$_SESSION['userId'] = $userId;
		}
		// 领取礼包
		$giftbagCode = GiftBagService::receive($campaignId, $userId);
		if(empty($giftbagCode)) {
			$this->ajax(array('message' => '礼包已被抢光！',)); exit;
		}else{
			// 记住领取的礼包
			GiftBagService::rememberReceive($campaignId, $userId, $giftbagCode);
		}

		$this->ajax(array(
			'normal' => true,
			'userId' => $userId,
		));
	}
	
	/**
	 * 测试领取兑换码
	 */
	function actionTest() {
		$campaignId = (int)$_GET['campaignId'];
		$userId = (int)$_GET['userId'];
		
		/* 用户已领取 */
		if(GiftBagService::receivedTotal($campaignId, $userId) > 0) {
			exit('received');
		}
		
		$giftbagCode = GiftBagService::receive($campaignId, $userId);
		if(empty($giftbagCode)) {
			exit('no');
		}else{
			// 记住领取的礼包
			GiftBagService::rememberReceive($campaignId, $userId, $giftbagCode);
			exit('ok');
		}
	}
	
	/**
	 * 验证码
	 */
	function actionCaptcha() {
		$this->openSession();
		
		Header("Content-type:image/png");
		
		$captcha = '';
		$str = 'abcdefghijkmnpqrstuvwxyz1234567890';
		$max = strlen($str)-1;
		for($i=0; $i<4; $i++) {
			$captcha .=  $str[mt_rand(0, $max)];
		}
		
		$_SESSION['captcha'] = strtolower($captcha);
		//图片宽与高
		$im = imagecreate(60,28);
		//黑白灰三色
		$black = ImageColorAllocate($im, 0,0,0);
		$white = ImageColorAllocate($im, 255,255,255);
		$gray = ImageColorAllocate($im, 200,200,200);
		//四位整数写入图片
		imagefill($im,68,30,$gray);
		//干扰线
		$li = ImageColorAllocate($im, 220,220,220);
		for($i=0; $i<3; $i++) {
			imageline($im,rand(0,30),rand(0,21),rand(20,40),rand(0,21),$li);
		}
		imagestring($im, 5, 15, 5, $captcha, $white);
		
		for($i=0;$i<90;$i++) {
			imagesetpixel($im, rand()%70 , rand()%30 , $gray);
		}
		ImagePNG($im);
		ImageDestroy($im);
	}

}
