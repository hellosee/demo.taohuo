<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    protected $_session = null;
    protected $_des = null;
    protected $_session_id = null;
    public function __construct() {
        parent::__construct();
        $this->_session_id = session_id();
    }
	
	/**
	 * 1.对不为空的参数进行升序排列，
	 * 2.循环获取到key,value, 
	 * 3.对key,value进行字符串拼接 sign += key + "=" + value + "&"
	 * 4.sign+="&key="+密钥
	 * 5.md5加密sign
	 * 6.转大写
	 */
	public function getSign( $params  = "" ){
		$sign = "";
		ksort($params);
		foreach( $params as $key => $value ){
			if( $value != "" ){
				$sign .= $key . "=" . $value . "&";
			}
		}
		$sign .= "key=" . SECRET_KEY;
		return strtoupper(md5($sign));
	}

    public function checkLogin(){
        if(!$this->_session->is_login()){
            die('{"code":"2","msg":"nologin"}');
        }
    }
}