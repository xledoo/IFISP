<?php
namespace Member\Controller;
use Think\Controller;
class LoginController extends CommonController {

    public function index(){


        if(formcheck('login')){
            
            $this->login(I('username'), I('password'));
        } else {
            $this->assign('formhash', formhash());
            $this->display();
        }
    }

    public function logout(){
        session(C('LOGIN_AUTH_NAME'), null);
        cookie(C('LOGIN_AUTH_NAME'), null);
        $this->success('退出成功', U('home/index/index'));
    }

    public function login($username, $password, $answer = ''){
        loaducenter();
        $safe   =   empty($answer) ? false : true;
        $qu     =   empty($answer) ? '' : '1';
        $member    =   uc_user_login($username, $password, 0, $safe, $qu, $answer);

        $return['err']  =   false;
        switch ($member[0]) {
            case '-1':
                $return['msg'] =  '用户不存在，或者被删除';
                $return['err']  =   true;
                break;
            case '-2':
                $return['msg'] =  '用户名或密码输入错误';
                $return['err']  =   true;
                break;
            case '-3':
                $return['msg'] =  '安全码输入错误';
                $return['err']  =   true;
                break;
            
            default:
                unset($member[3]);
                unset($member[4]);
                $member[2]  =   hashmd5($member[2]);
                $auth = authcode(serialize($member), 'ENCODE', C('GLOBAL_AUTH_KEY'));
                session(C('LOGIN_AUTH_NAME'), $auth);
                cookie(C('LOGIN_AUTH_NAME'), $auth);
                break;
        }

        $return['err'] ? $this->error($return['msg']) : $this->success($return['msg']);
    }

 }