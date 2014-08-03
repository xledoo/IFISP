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
	}
	
	/*
	初始化数据库配置项目
	*/
	function init_setting(){
		$this->_G = M('common_setting')->cache()->select();
	}

	/*
	用户登录信息
	*/
	function init_member(){
		
	}
}


?>