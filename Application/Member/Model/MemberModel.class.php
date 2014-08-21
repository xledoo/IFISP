<?php
namespace Member\Model;
use Think\Model;

class MemberModel extends Model {

	//自动验证
	protected $_validate = array(

		array('username',	'require',	'用户名必须！'), 
		array('password',	'require',	'密码必须！'), 
		array('password2',	'require',	'确认密码密码必须！'),
		array('email',		'require',	'Email必须！'), 
		array('mobile',		'require',	'手机号码必须！'), 
		array('sign',		'require',	'短信验证码必须！'), 
		// array('scode',		'require',	'安全码必须！'), 

		// array('username', '/^[a-z0-9\x{4e00}-\x{9fa5}]{2,20}$/u' , '用户名格式错误' , 1 , 'regex' ,1),
		// array('password', '/^([\d]+[a-zA-Z]+|[a-zA-Z]+[\d]+){1,}$/' , '密码格式错误' , 1 , 'regex' ,1),
		// array('email', '/^[a-z]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i' , '邮箱格式错误' , 1 , 'regex' ,1),
		// array('mobile', '/^1[3|4|5|8][0-9]\d{4,8}$/' , '手机号码格式错误' , 1 , 'regex' ,1),
		// array('sign',	6,	'短信验证码的范围不正确！',	1,	'length',	1),

		array('username',	'',		'用户名已经存在！',	0,	'unique',	1),
		array('email',		'',		'Email已经存在！',		0,	'unique',	1),
		array('mobile',		'',		'手机号码已经存在！',	0,	'unique',	1),
		array('password','password2','确认密码不正确',		0,	'confirm'),
	);

	//自动完成
	protected $_auto = array (
		array('password', 'hashmd5', 3, 'function'), 
		array('regdate',	'time',	1,	'function'), 
	);

	//UC注册时本地member附表更新操作
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