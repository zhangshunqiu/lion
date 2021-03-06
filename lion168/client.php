<?php
require_once(dirname(__FILE__)."/config.php");
require_once(EK_INC.'/check.member.php');
$keeptime = isset($keeptime) && is_numeric($keeptime) ? $keeptime : -1;
$cfg_cl = new MemberLogin($keeptime);
$cacheid = "login";
if($action=='login'){
	$svali = GetCkVdValue();
	if(strtolower($ValidateCode)!=$svali || $svali=='')
	{
		ResetVdValue();
		ShowMsg('ERROR：无效的验证码，登录失败！','-1');
		exit;
	}else{
		if(Checkuid($username,'',false)!='ok')
		{
			ShowMsg("ERROR：你输入的登录名 {$username} 不合法！",'-1');
			exit;
		}
		elseif($password=='')
		{
			ShowMsg('ERROR：密码不能为空！！','-1');
			exit;
		}else{
			//检查帐号
			$rs = $cfg_cl->CheckUser($username,$password);


			if($rs==0)
			{
				ShowMsg('ERROR：用户名不存在！','-1');
				exit();
			}
			else if($rs==-1) {
				ShowMsg('密码错误！','-1');
				exit();
			}
			else
			{
				//插入登录记录
				$uid=$cfg_cl->M_ID;
				$ip = GetIP();
				$dateline = time();
				$dsql->ExecuteNoneQuery("INSERT INTO `ek_member_login_log` (`uid`,`ip` ,`dateline`)VALUES ('$uid','$ip','$dateline');");
				
				if(empty($gourl) || eregi("action|_do",$gourl))
				{
					CheckNotAllow();
					ShowMsg("成功登录，5秒钟后转向系统主页...","member/index.php",0,2000);
					exit();
				}
				else
				{
					CheckNotAllow();
					ShowMsg("成功登录，现在转向指定页面...",$gourl,0,2000);
					exit();
				}
				exit();
			}
		}
	}
}
 
if(GetCookie('EKuid')!=''){
	$t->assign('loginname',GetCookie('EKUserName'));
	$t->assign('loginuid',GetCookie('EKuid'));
}
$t->assign('noticear',get_notice_list('3'));
$t->display('client_login.html',"$cacheid");