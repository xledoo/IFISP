<?php
namespace Member\Model;
use Think\Model;

class MemberModel extends Model {

	protected $_validate = array(
		array('username',	'',		'帐号名称已经存在！',	0,	'unique',	1),
		array('email',		'',		'Email已经存在！',		0,	'unique',	1),
		array('mobile',		'',		'手机号码已经存在！',	0,	'unique',	1),
		array('password','password2','确认密码不正确',		0,	'confirm'),
	);
	protected $_auto = array (
		array('password', 'hashmd5', 3, 'function'), 
		array('regdate',	'time',	1,	'function'), 
	);

	protected function _after_insert($data,$options) {
		$countModel		=	M('member_count');
		$profileModel	=	M('member_profile');
		$settleModel	=	M('member_settle');
		$statusModel	=	M('member_status');

		$countModel->add(array('uid' => $data['uid']));
		$profileModel->add(array('uid' => $data['uid'], 'mobile' => $data['mobile']));
		$settleModel->add(array('uid' => $data['uid'], 'verify' => check_verify(array($data['uid'], '0.00', '0.00', '0.00', '0.00'))));
		$statusModel->add(array('uid' => $data['uid'], 'regip' => get_client_ip()));
	}
}

?>