<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Member_model','member');
	}
	
	public function register() {
		/*手机号*/
		$login_mobile = $this->input->post('login_mobile');
		/*密码*/
		$login_pass = $this->input->post('login_pass');
		$verify_mobile = verify_mobile($login_mobile);
		if($verify_mobile){ output(array('code'=>0,'msg'=>$verify_mobile)); }
		$verify_upass = verify_upass($login_pass);
		if($verify_upass){ output(array('code'=>0,'msg'=>$verify_upass)); }
		/*查询手机号码是否已经注册了*/
        $uid = $this->member->getFieldByMobile('uid',$login_mobile);
        if($uid){output(array('code'=>0,'msg'=>"该手机号码已经存在")); }
		$salt = security_code();
		$data = array(
					'username' => $login_mobile,
					'headurl' => "",
					'login_pass' => md5(md5($login_pass).$salt),
					'salt' => $salt,
					'group_id' => 0,
					'is_del' => 0,
					'createtime'=>time(),
					'starttime' => 0,
					'endtime' => 0,
					'login_mobile' => $login_mobile,
					'invite_code' => '',
				);
		
		$autoid = $this->member->register($data);
		if($autoid){
			$userinfo = $this->member->getUserInfoByUid("*",$autoid);
            $userinfo['rsid'] = $this->_session_id;
            unset($userinfo['login_pass'],$userinfo['salt'],$userinfo['is_del']);
            output(array('code'=>1,'msg'=>"注册成功",'data'=>$userinfo));
		} else {
			output(array('code'=>0,'msg'=>"注册失败"));
		}
	}
	
	public function login(){
        /*手机号*/
		$login_mobile = $this->input->post('login_mobile');
        /*密码*/
        $login_pass = $this->input->post('login_pass');
        $verify_mobile = verify_mobile($login_mobile);
        if($verify_mobile){ output(array('code'=>0,'msg'=>$verify_mobile)); }
        $userinfo = $this->member->getUserInfoByMobile('*',$login_mobile);
        if(!$userinfo){   output(array('code'=>0,'msg'=>"该用户不存在"));}
        $login_pass = md5(md5($login_pass).$userinfo['salt']);
        if($login_pass !== $userinfo['login_pass']){  output(array('code'=>0,'msg'=>"密码输入错误"));}
        $userinfo['rsid'] = $this->_session_id;
        unset($userinfo['login_pass'],$userinfo['salt'],$userinfo['is_del']);
        output(array('code'=>1,'msg'=>"登录成功",'data'=>$userinfo));
	}
	
}