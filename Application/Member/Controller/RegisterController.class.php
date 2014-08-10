<?php
namespace Member\Controller;
use Common\Controller\BaseController;

class RegisterController extends BaseController {

    //注册页面
    public function index(){
    
        if(formcheck('register')){
            loaducenter();

            $uid = uc_user_register(I('username'), I('password'), I('email'));
            switch ($uid) {
                case '-1':
                    $return['msg']  = '用户名不合法';
                    $return['err']  =   true;
                    break;
                case '-2':
                    $return['msg']  = '包含不允许注册的词语';
                    $return['err']  =   true;
                    break;
                case '-3':
                    $return['msg']  = '用户名已经存在';
                    $return['err']  =   true;
                    break;
                case '-4':
                    $return['msg']  = 'Email 格式有误';
                    $return['err']  =   true;
                    break;
                case '-5':
                    $return['msg']  = 'Email 不允许注册';
                    $return['err']  =   true;
                    break;
                case '-6':
                    $return['msg']  = '该 Email 已经被注册';
                    $return['err']  =   true;
                    break;                
                default:
                    $Model  =   D('Member');
                    $Model->create();
                    $Model->add();
                    $return['msg']  = '注册成功';
                    $return['err']  =   false;
                    break;

               
            }

           if($return['err']){
                $this->error($return['msg']);
           } else {
                $this->success($return['msg'],U('Member/Login/index'));
                // R('Login/login', array('username' => I('username'), 'password' => I('password')));
                // $this->redirect('Login/index', array('username' => I('username'), 'password' => I('password')), 3, '注册成功，正在登录...');
           }
        } else {
            $this->assign('formhash', formhash());
            $this->display();            
        } 

    }

    //短信发送
    public function SMSend_verify($mobile){
        $random     =   random(6, 1);
        import('Org.Util.SMSender');
        $SMS    =   new \Org\Util\SMSender(C('SMS_USERNAME'),C('SMS_PASSWORD'),C('SMS_CHARSET'),C('SMS_INTERFACE'));

        $content    =   '您的手机号：'.$mobile.'，注册验证码：'.$random.'，一天内提交有效。感谢您的注册！';
    
        $returnJSON =   array(
            'error' =>  0,
            'message'   =>  '',
        );

        if(M('member_checkmobile')->where("mobile='%s' AND dateline > ".NOW_TIME-300, array($mobile))->find()){
            $returnJSON =   array(
                'error' =>  1,
                'message'   =>  '验证短信已经发送 请稍后'
            );
            exit(json_encode($returnJSON));
        } else {
            M('member_checkmobile')->where("mobile='%s'")->delete();
        }

        $result = $SMS->SendSMS($mobile, $content, 'register');

        if($result = $SMS->SendSMS($mobile, $content, 'register')){
            $checkModel =   M('member_checkmobile');
            $data   =   array(
                'mobile'    =>  $mobile,
                'sign'      =>  $random,
                'sendip'    =>  get_client_ip(),
                'dateline'  =>  NOW_TIME   
            );
            if($checkModel->add($data)){
                unset($data['sign']);
                $smsModel   =   M('admincp_smsender');
                $data['action'] =  'register';
                $data['message'] =  $content;
                $data['status'] =  ($result['result'] != 0) ? 1 : 0;
                $data['result'] =  $result['message'];
                $smsModel->add($data);
                $returnJSON =   array(
                    'error' =>  0,
                    'message'   =>  '验证短信发送成功,请等待接收',
                );
                exit(json_encode($returnJSON));                              
            }
        } else {
            $returnJSON =   array(
                'error' =>  1,
                'message'   =>  '验证短信发送失败,请联系管理员',
            );            
            exit(json_encode($returnJSON));
        } 
    }

    function check_username($data){
        loaducenter();
        $JSON['error']  =   1;
        $return =   uc_user_checkname($data);
        switch ($return) {
            case '-1':
                $JSON['message']    =   '输入的用户名格式错误';
                break;
            case '-2':
                $JSON['message']    =   '包含要允许注册的词语';
                break;
            case '-3':
                $JSON['message']    =   '该用户名已经被注册了';
                break;
            case '1':
                $JSON['error']      =   0;
                $JSON['message']    =   '用户名可以注册';
                break;
            default:
                break;
        }
        if(!preg_match('/^[a-zA-Z0-9_]{6,15}$/', $data)){
            $JSON['error']      =   1;
            $JSON['message']    =   '用户名必须为 6-16 位的非中文字符串';           
        }
        if(M('member')->where("username='%s'", array($data))->find()){
            $JSON['error']      =   1;
            $JSON['message']    =   '该用户名已经被注册了';

        }

        exit(json_encode($JSON));
    }

    function check_password($data){

    }
    function check_password2($data){
        
    }


    function check_email($data){
        loaducenter();
        $JSON['error']  =   1;
        $return = uc_user_checkemail($data);
        switch ($return) {
            case '-4':
                $JSON['message']    =   'Email 格式有误';
                break;
            case '-5':
                $JSON['message']    =   'Email 不允许注册';
                break;
            case '-6':
                $JSON['message']    =   '该 Email 已经被注册';
                break;
            case '1':
                $JSON['error']      =   0;
                $JSON['message']    =   'Email可以注册';
                break;
            default:
                break;
        }
        if(M('member')->where("email='%s'", array($data))->find()){
            $JSON['error']      =   1;
            $JSON['message']    =   '该Email已经被注册了';
        }
        exit(json_encode($JSON));
    }

    function check_mobile($data){
        $JSON['error']  =   0;
        if(!(strlen($data) == 11 && preg_match("/^1[3|4|5|8][0-9]\d{4,8}$/", $data))){
            $JSON['error']      =   1;
            $JSON['message']    =   '手机号码格式错误';           
        }
        if(M('member')->where("mobile='%s'", array($data))->find()){
            $JSON['error']      =   1;
            $JSON['message']    =   '该手机号码已经被注册了';
        }

        exit(json_encode($JSON));
    }

    function check_sign($data){
        $JSON['error']  =   1;
        $JSON['message']    =   '验证码错误';
        if(M('member_checkmobile')->where('sign=%s', array($data))->find()){
            $JSON['error']      =   0;
            $JSON['message']    =   '可以注册';
        }
        exit(json_encode($JSON));
    }

    public function ajax_check($type,$data){
        if(!in_array($type, array('username', 'email', 'mobile', 'password', 'sign')))  return false;
        if($type == 'email'){
            $this->check_email($data);
        } else {
            eval('$this->check_'.$type.'('.$data.');');
        }
        
    }
}
