<?php
namespace Member\Controller;
use Think\Controller;
class LoginController extends Controller {

	public function _initialize(){
		loaducenter();
	}

    public function index(){
    	loaducenter();
    	$this->display();
    }
}