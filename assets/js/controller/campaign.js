(function($){
	var campaignData, userData;
	function dialogTip(title, content) {
		$('.llb-cont-ok,.llb-cont').hide();
		return $('.llb-cont-no').show().each(function(){
			$(this).find('strong').html(title);
			$(this).find('p').html(content);
		});
	}
	
	function showDialog(id) {
		$('.llb-cont-ok,.llb-cont').hide();
		showA(id);
		if(typeof(campaignData) == 'undefined') {
			dialogTip('加载中', '');
			$.ajax({
				url: '?a=status&id='+campaignId+'&_='+assetsVersion,
				dataType: 'json',
				success: function(data){
					if(data.campaign == null) {
						dialogTip('对不起', '活动已停止');
					}
					campaignData = data.campaign;
					userData = data.user;
					dialogInit();
				},
				error: function() {
					dialogTip('对不起', '请求失败！<a href="javascript:;">重试</a>').find('a').click(function(){
						showDialog(id);
					});
				},
			});
		}else{
			dialogInit();
		}
	}
	
	function dialogInit() {
		if(campaignData.isStarted == false) {
			return dialogTip('对不起', '活动尚未开始');
		}else if(campaignData.isExpired) {
			return dialogTip('对不起', '活动已过期');
		}
		
		$('.llb-cont-no').hide();
		if(userData) {
			$('#outside').hide();
			$('#inside').show().find('img.yzm').click();
			$('.insideUsername').html(userData.username);
			$('.llb-btnb').unbind().click(function(){
				var mobile = $.trim($('#insideMobile').val());
				var captcha = $.trim($('#insideCaptcha').val());
				var selfObj = $(this), func = arguments.callee;
				if(/^\d{11}$/i.test(mobile) == false) {
					return alert('请输入正确的手机号码');
				}
				if(captcha.length != 4) {
					return alert('请输入正确的验证码');
				}
				
				$('#insideMobile,#insideCaptcha').attr('disabled', true);
				selfObj.unbind('click');
				$.ajax({
					type:'post',
					url: '?a=receive&id='+campaignId,
					dataType:'json',
					data:{mobile:mobile, captcha:captcha},
					success:function(data) {
						if(data.message) {
							alert(data.message);
							// 重新绑定事件
							selfObj.click('click', func);
							// 刷新验证码
							$('#inside img.yzm').click();
							$('#insideCaptcha').val('');
						}else{
							$('.llb-cont-ok').show().find('strong').html('恭喜您抢到礼包');
							$('.llb-cont-no,.llb-cont').hide();
						}
					},
					error:function() {
						dialogTip('对不起', '请求失败！');
					},
					complete:function(){
						$('#insideMobile,#insideCaptcha').attr('disabled', false);
					}
				});
			});
		}else{
			$('#outside').show().find('img.yzm').click();
			$('#inside').hide();
			$('.llb-btna').unbind().click(function(){
				var username = $.trim($('#outsideUsername').val());
				var password = $.trim($('#outsidePassword').val());
				var password2 = $.trim($('#outsidePassword2').val());
				var mobile = $.trim($('#outsideMobile').val());
				var captcha = $.trim($('#outsideCaptcha').val());
				var selfObj = $(this), func = arguments.callee;
				if(username.length == 0) {
					return alert('请输入用户名');
				}
				if(password.length == 0) {
					return alert('请输入密码');
				}
				if(password != password2) {
					return alert('两次输入密码不一致');
				}
				if(/^\d{11}$/i.test(mobile) == false) {
					return alert('请输入正确的手机号码');
				}
				if(captcha.length != 4) {
					return alert('请输入正确的验证码');
				}
				
				$('#outsideUsername,#outsidePassword,#outsidePassword2,#outsideMobile,#outsideCaptcha').attr('disabled', true);
				selfObj.unbind('click');
				$.ajax({
					type:'post',
					url: '?a=receive&id='+campaignId,
					dataType:'json',
					data:{
						username:username, password:password,
						mobile:mobile, captcha:captcha
					},
					success:function(data) {
						if(data.message) {
							alert(data.message);
							// 重新绑定事件
							selfObj.click('click', func);
							// 刷新验证码
							$('#outside img.yzm').click();
							$('#outsideCaptcha').val('');
						}else{
							$('.insideUsername').html(username+'('+mobile+')');
							$('.llb-cont-ok').show();
							$('.llb-cont-no,.llb-cont').hide();
							userData = {id:data.userId,username:username,mobile:mobile};
						}
					},
					error:function() {
						dialogTip('对不起', '请求失败！');
					},
					complete:function(){
						$('#outsideUsername,#outsidePassword,#outsidePassword2,#outsideMobile,#outsideCaptcha').attr('disabled', false);
					}
				});
			});
		}
		$('.llb-cont').show().find('i').hide();
	}
	
	var winWidth = 0;
	function findDimensions() {
		if (window.innerWidth) {
			winWidth = window.innerWidth;
		} else if ((document.body) && (document.body.clientWidth)) {
			winWidth = document.body.clientWidth;
		};
		if (document.documentElement && document.documentElement.clientWidth) {
			winWidth = document.documentElement.clientWidth;
			var html = "<div class=\"zclbbox\"><a href=\"javascript:;\" class=\"llg_btna\">注册领礼包</a></div>";
			if (winWidth < 1360) {
				html = "<div class=\"zclbbox-b\"><a href=\"javascript:;\" class=\"llg_btnb\">注册领礼包</a></div>";
			}
			$('#floatright').html(html).find('a:first').click(function(){
				showDialog('llb-box');
			});
		};
	};
	
	/* 加载完执行 */
	$(function(){
		$('#logout').click(function(){
			$.ajax({
				url: $(this).attr('href'),
				success:function(){
					userData = null;
					showDialog('llb-box');
				}
			});
			return false;
		});
		findDimensions();
		$('img.yzm').click(function(){
			$(this).attr('src', $(this).attr('data-orgin')+'&'+Math.random());
		});
		window.onresize = findDimensions;
	});
})(jQuery);