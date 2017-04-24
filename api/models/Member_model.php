<?php
/**
 * Created by PhpStorm.
 * User: 李守杰
 * Date: 2017/04/23
 * Desc: 用户表，所有该表操作都在该模型进行
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_model extends MY_Model {
    protected $table = 'member';
    public function __construct() {
        parent::__construct();
        $this->setDB('db_t');
    }

    /**
     * user：李守杰
     * Date 2017/04/23
     * DESC：插入用户（注册操作）
     */
    public function register($data){
    	$autoid = $this->setData($data);
    	return $autoid;
    }
	
	public function getUserInfoByUid( $fields = "*" , $uid ){
		$userinfo = $this->getOne($fields,array('uid'=>$uid));
        return $userinfo;
	}
	public function getFieldByMobile($fields = "uid",$login_mobile){
        $uid = $this->getVal($fields,array('login_mobile'=>$login_mobile));
        return $uid;
    }
    public function getUserInfoByMobile($fields = "*",$login_mobile){
        $uid = $this->getOne($fields,array('login_mobile'=>$login_mobile));
        return $uid;
    }
    
    
}