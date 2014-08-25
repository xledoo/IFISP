<?php
namespace Member\Controller;
use Common\Controller\BaseController;

class ProfileController extends BaseController {
    public function index(){
        $gm = $this->_G['member']['mobile'];
        $data = M('member_old_profile')->where("mobile='%s'",$gm)->field('uid',true)->select();
        M('member_profile')->where("mobile='%s'",$gm)->save($data[0]);
        $this->assign('memp',$data[0]);
    	$this->display();
    }

    public function setBasicInfo(){
        
    }

    public function myPw(){
    	$this->display();
    }
    
    public function myInte(){
    	$this->display();
    }

    public function myAva(){
    	$this->display();
    }

    public function qqBind(){
    	$this->display();
    }

    public function wxBind(){
    	$this->display();
    }

    public function other(){
    	$this->display();
    }

}