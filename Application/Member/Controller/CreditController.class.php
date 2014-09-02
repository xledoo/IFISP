<?php
namespace Member\Controller;
use Common\Controller\BaseController;

class CreditController extends BaseController {

	//信用等级
    public function index(){
    	$this->display();
    }

    //认证中心
    public function idenfy(){
    	$province = M('Region')->where ( array('pid'=>1) )->select ();
        $this->assign('province',$province);
        $this->display();
    }
}