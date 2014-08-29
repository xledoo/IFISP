<?php
namespace Member\Controller;
use Common\Controller\BaseController;

class ProfileController extends BaseController {
    public function index(){
        $gm = $this->_G['member']['mobile'];
        $memb = M('member_profile')->where("mobile='%s'",$gm)->find();
        if(!$memb){
            $data = M('member_old_profile')->where("mobile='%s'",$gm)->find();
            M('member_profile')->where("mobile='%s'",$gm)->add($data);
            // debug($data);
        }
        // debug($dot);
        $this->assign('memp',$memb);
    	$this->display();
    }

    //基本资料修改
    public function setBasicInfo(){
        unset($_POST['edit']);
        // debug($_POST);
        if(M('member_profile')->where("mobile='%s'",$this->_G['member']['mobile'])->save($_POST)){
            $this->success("修改成功！");
        }  else {
            $this->error("修改失败！");
        }
    }

    //密码修改
    public function myPw(){
        if(formcheck('edit')){
            // debug($this->_G['member']);
            // debug($_POST);
            loaducenter();
            $pid = uc_user_edit(I('username'), I('oldpw'), I('newpw'));
            switch ($pid) {
                case '0':
                    $return['msg']  = '没有做任何修改';
                    $return['err']  =   true;
                    break;
                case '-1':
                    $return['msg']  = '旧密码不正确';
                    $return['err']  =   true;
                    break;
                case '-4':
                    $return['msg']  = 'Email格式有误';
                    $return['err']  =   true;
                    break;
                case '-5':
                    $return['msg']  = 'Email不允许注册';
                    $return['err']  =   true;
                    break;
                case '-6':
                    $return['msg']  = '该Email已被注册';
                    $return['err']  =   true;
                    break;
                case '-7':
                    $return['msg']  = '没有做任何修改';
                    $return['err']  =   true;
                    break;
                case '-8':
                    $return['msg']  = '该用户受保护无权限修改';
                    $return['err']  =   true;
                    break;
                default:
                    $memb = M('member')->where("username='%s'",$_POST['username'])->find();
                    if($memb){
                        $data['password']   =   hashmd5($_POST['newpw']);
                        M('member')->where("username='%s'",$_POST['username'])->save($data);
                    }
                    $return['msg'] = '密码修改成功！';
                    $return['err'] = false;
                    break;
            }
            if($return['err']){
                $this->error($return['msg']);
            } else {
                $this->success($return['msg']);
            }
        } else {
            $this->assign('formhash', formhash());
            $this->display();            
        }
    }
    
    //我的积分
    public function myInte(){
        // debug($this->_G['member']);
        // debug($_POST);
    	$this->display();
    }

    //我的头像
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