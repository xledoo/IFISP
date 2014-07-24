<?php
namespace Admincp\Controller;
use Think\Controller;
class MemberController extends PublicController {

    public function index(){
    	$members	=	M('member')->select();
    	$this->assign('members', $members);
    	$this->display();
    }

    //添加用户操作
    public function doadd(){
    	// if(formcheck('submit')){
    		$Model = D('member');
    		// $Model->where('uid='.$this->_G['member']['uid'])->find();
    		if(!$Model->create()){
    			exit($Model->getError());
    		}	else {
    			//本地数据添加
                $data = array(
                        'username'  =>  I('username'),
                        'password'  =>  md5(I('password')),
                        'mobile'    =>  I('mobile'),
                        'email'     =>  I('email'),
                        'regdate'	=>	NOW_TIME,
                        // 'sign'      =>  I('sign')
                    );
                $Model->add($data);
                $this->success("用户添加成功！", U('Admincp/Member/index'));
    		}
    	// }
    	// $this->error('非法提交!');
    }

    //编辑用户
    public function edit($id){
    	echo('编辑页面');
    }

    //删除用户
    public function del($id){
    	M('member')->where('uid='.$id)->delete() ? $this->success('删除成功', U('Admincp/Member/index')) : $this->error('删除失败', U('Admincp/Member/index'));
    }
}