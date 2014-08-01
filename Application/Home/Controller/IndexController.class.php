<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$auth =  session('finabao');
    	$auth =  authcode($auth, 'DECODE', C('GLOBAL_AUTH_KEY'));
    	zecho(unserialize($auth));
    }
}