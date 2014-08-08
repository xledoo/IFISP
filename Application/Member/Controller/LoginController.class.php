<?php
namespace Member\Controller;
use Common\Controller\BaseController;

class LoginController extends BaseController {

    public function index(){

        if($this->_G['member']['uid'] > 0){
            $this->redirect('member/index/index');
        }
        if(formcheck('login')){
            $this->login(I('username'), I('password'));
        } else {
            $this->assign('formhash', formhash());
            $this->display();
            // debug($this->_G);
        }
    }

    public function logout(){
        session(C('LOGIN_AUTH_NAME'), null);
        cookie(C('LOGIN_AUTH_NAME'), null);
        $this->success('退出成功', U('Home/Index/index'));
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
                $return['msg']  =   '会员登录成功';
                $return['err']  =   false;
                break;
        }
        $return['err'] == false ? $this->error($return['msg']) : $this->success($return['msg']);
    }

 }