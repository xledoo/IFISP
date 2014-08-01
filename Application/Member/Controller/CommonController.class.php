<?php
namespace Member\Controller;
use Think\Controller;

class CommonController extends Controller{

	public $_G	=	array();
	function _initialize(){
		$this->init();
	}

	public function init(){
		$this->_G['member']		=	$this->check_member();
		$this->_G['setting']	=	array();
	}

	public function check_member(){
		$auth	=	session(C('LOGIN_AUTH_NAME')) ? session(C('LOGIN_AUTH_NAME')) : (cookie(C('LOGIN_AUTH_NAME')) ? cookie(C('LOGIN_AUTH_NAME')) : false);
		$auth 	=	authcode($auth, 'DECODE', C('GLOBAL_AUTH_KEY'));
		$auth 	=	unserialize($auth);

		if(is_array($auth)){
			return D('member')->where("uid='%d' AND username='%s' AND password='%s'", array($auth[0], $auth[1], $auth[2]))->find() ?: false;
		}
		return false;
	}
}

