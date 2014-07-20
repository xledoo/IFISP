<?php
namespace Admincp\Controller;
use Think\Controller;
class PublicController extends Controller {

	public function _initialize(){
		$this->assign('sidebar', $this->init_sidebar());

	}

	/*
	初始化侧边栏菜单
	*/
	public function init_sidebar(){
    	$sidebar	=	M('admincp_sidebar')->where('upid=0')->cache('admincp_sidebar', 60)->order('displayorder ASC')->select();

    	foreach ($sidebar as $key => $value) {
    		$sidebar[$key]['submenu']	=	M('admincp_sidebar')->where("upid='%d'", array($value['id']))->select();
    	}
    	return $sidebar;
	}
}