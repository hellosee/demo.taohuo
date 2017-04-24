<?php
/**
 * Created by PhpStorm.
 * User: 李守杰
 * 公共函数库
 * Date: 2017/2/23
 * Time: 17:47
 */

function output($data = NULL, $http_code = NULL){
    $json = json_encode($data);
    exit($json) ;
}

function p($obj = array(),$exit = 0){
    echo '<pre>';
    print_r($obj);
    echo '</pre>';
    $exit && exit;
}

/**
 * 生成登录安全码
 */
function security_code($length = 8,$type = 'numstr') {
    $source = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+-=';
    if($type=='number') $source='0123456789';
    if($type=='numstr') $source='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len = strlen($source);
    $return = '';
    for ($i = 0; $i < $length; $i++) {
        $index = rand() % $len;
        $return .= substr($source, $index, 1);
    }
    return $return;
}

// 判断密码
function verify_upass($str) {
	if (strlen($str) >= 6 && strlen($str) <= 20) {
		return '';
	} else {
		return '密码长度为6-20个字符';
	}
	if(strstr($str,"'")) return '密码不能有单引号';
}

// 判断手机号码
function verify_mobile($str) {
	if( preg_match("~^((\(\d{3}\))|(\d{3}\-))?(13\d{9}|15\d{9}|17\d{9}|18\d{9}|14\d{9})$~", $str)){
		return '';
	}else{
		return '手机号码不正确';
	}
}

// 判断用户名
function verify_uname($str) {
	$str=strtolower($str);
	$no_prefix=array('sys','manage','admin');
	foreach($no_prefix as $v){
		if(substr($str,0,strlen($v))==$v) return '不允许 '.implode(' , ',$no_prefix).' 开头';
	}
	if(!preg_match('~^[a-z][a-z0-9_]{5,19}$~', $str)) return '6～20个字符，字母开头，字母、数字组成'; /*return '用户名长度6～20个字符，以字母a～z（不区分大小写）开头，且只能由字母、数字0～9和下划线组成';*/
	return '';
}