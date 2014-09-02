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
        // $top = M('member')->where("username='%s'",'alipiapia')->getField('mobile');debug($top);
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
                //用户数据同步:根据登录时的username从member_old+member_old_profile表中获取所需字段来更新本地member表;
                //更新字段:username,email,password,regdate,mobile;
                //加密方式修改为hashmd5();
                $memb = M('member')->where("username='%s'",$username)->find();
                if(!$memb){
                    $data['username']   =   $username;
                    $data['password']   =   hashmd5($password);
                    $mid = M('member_old')->where("username='%s'",$username)->getField('uid');//获取用户uid
                    $data['email']     =   M('member_old')->where("uid='%d'",$mid)->getField('email');//同步email
                    $data['regdate'] = M('member_old')->where("uid='%d'",$mid)->getField('regdate');//同步注册时间
                    $data['mobile']     =   M('member_old_profile')->where("uid='%d'",$mid)->getField('mobile');//同步手机号码
                    M('member')->add($data);
                }

                unset($member[3]);
                unset($member[4]);
                $member[2]  =   hashmd5($member[2]);
                $auth = authcode(serialize($member), 'ENCODE', C('GLOBAL_AUTH_KEY'));
                session(C('LOGIN_AUTH_NAME'), $auth);
                cookie(C('LOGIN_AUTH_NAME'), $auth);
                $return['msg']  =   '欢迎回来,'.$username;
                $return['err']  =   false;
                break;
        }
        $return['err'] == false ? $this->success($return['msg'],U('member/index/index')) : $this->error($return['msg'],U('member/login/index'));
    }

 }