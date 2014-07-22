<?php
namespace Admincp\Controller;
use Think\Controller;
class MemberController extends PublicController {
    public function index(){
    	$members	=	M('member')->select();

    	$this->assign('members', $members);
    	$this->display();
    }
}