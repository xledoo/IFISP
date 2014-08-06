<?php
/*
所有控制器的基类
Date: 20140803
Author: xledoo
*/
namespace Common\Controller;
use Think\Controller;

class BaseController extends Controller{

	/*
	全局变量用于保存登录及配置信息
	*/
	var $_G	=	array();

	/*
	初始化
	*/
	function _initialize(){
		self::init_setting();
		self::init_member();
		$this->assign('_G', $this->_G);
	}
	
	/*
	初始化数据库配置项目
	*/
	function init_setting(){
		$setting = M('common_setting')->cache()->select();
		foreach ($setting as $key => $value) {
			$this->_G[$value['skey']]	=	$value['svalue'];
		}
	}

	/*
	用户登录信息
	*/
	function init_member(){
		$auth	=	session(C('LOGIN_AUTH_NAME')) ? session(C('LOGIN_AUTH_NAME')) : (cookie(C('LOGIN_AUTH_NAME')) ? cookie(C('LOGIN_AUTH_NAME')) : false);
		if(!$auth){
			$this->_G['member']['uid']	=	0;
			$this->_G['islogin']		=	false;
			return false;
		}
		$auth 	=	authcode($auth, 'DECODE', C('GLOBAL_AUTH_KEY'));
		$auth 	=	unserialize($auth);
		if(is_array($auth)){
			$this->_G['member'] = D('member')->where("uid='%d' AND username='%s' AND password='%s'", array($auth[0], $auth[1], $auth[2]))->find() ?: false;
			$this->_G['islogin']	=	true;
		}
	}
}


?>