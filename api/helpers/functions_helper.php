<?php

//获取微信access_token
function get_access_token($code){
	$acURL = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.WX_APPID.'&secret='.WX_SECRET.'&code='.$code.'&grant_type=authorization_code';
	$acRes = json_decode(curl_post($acURL),1);
	if(isset($acRes['errcode'])){
        output(array('code'=>1,'msg'=>$acRes['errmsg']));
	}
	return $acRes;
}

// 微信获取用户信息
function get_wx_user_info($access_token,$openid){
	$uiURL = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid;
	$result = json_decode(curl_post($uiURL),1);
	if(isset($result['errcode'])){
        output(array('code'=>1,'msg'=>$result['errmsg']));
	}
	return sqlxss($result);
}

//QQ获取用户信息
function get_qq_user_info($access_token,$openid){
	$uiURL = 'https://graph.qq.com/user/get_user_info?openid='.$openid.'&access_token='.$access_token.'&oauth_consumer_key='.QQ_APPID;
	$result = json_decode(postCurl($uiURL,0,array(),'GET'),1);
	if($result['ret']!=0){
		output(array('code'=>1,'msg'=>$result['msg']));
	}
	return sqlxss($result);
}

/*
 * curl POST
 * @param   string  url
 * @param   array   数据
 * @param   int     请求超时时间
 * @param   bool    HTTPS时是否进行严格认证
 * @return  string
 */
function curl_post($url, $data = array(), $timeout = 30, $CA = false){
	if($url=='') return '';
	$cacert = getcwd() . '/cacert.pem'; //CA根证书
	$SSL = substr($url, 0, 8) == "https://" ? true : false;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout-2);
	if ($SSL && $CA) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书
		curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
	} else if ($SSL && !$CA) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //避免data数据过长问题
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //data with URLEncode
	$ret = curl_exec($ch);
	$error=curl_error($ch);  //查看报错信息
	if(!empty($error)) print_r($error);
	curl_close($ch);
	return $ret;
}

//postCurl方法
function postCurl($url, $body, $header = array(), $method = "POST") {
	array_push($header, 'Accept:application/json');
	array_push($header, 'Content-Type:application/json');
	$ch = curl_init();//启动一个curl会话
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, $method, 1);
	switch ($method){
		case "GET" :
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			break;
		case "POST":
			curl_setopt($ch, CURLOPT_POST,true);
			break;
		case "PUT" :
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			break;
		case "DELETE":
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			break;
	}
	curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	if (isset($body{3}) > 0) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	}
	if (count($header) > 0) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	$ret = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);
	if ($err) {
		return $err;
	}
	return $ret;
}

//curl请求苹果app_store验证地址
function http_post_data($url, $data_string) {
	$curl_handle=curl_init();
	curl_setopt($curl_handle,CURLOPT_URL, $url);
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_handle,CURLOPT_HEADER, 0);
	curl_setopt($curl_handle,CURLOPT_POST, true);
	curl_setopt($curl_handle,CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($curl_handle,CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl_handle,CURLOPT_SSL_VERIFYPEER, 0);
	$response_json =curl_exec($curl_handle);
	$response =json_decode($response_json, TRUE);
	curl_close($curl_handle);
	return $response;
}

/*
 * 字符串防SQL注入编码，对GET,POST,COOKIE的数据进行预处理
 * @param  $input 要处理字符串或者数组
 * @param  $urlencode 是否要URL编码
 */
function escape($input, $urldecode = 0) {
	if(is_array($input)){
		foreach($input as $k=>$v){
			$input[$k]=escape($v,$urldecode);
		}
	}else{
		$input=trim($input);
		if ($urldecode == 1) {
			$input=str_replace(array('+'),array('{addplus}'),$input);
			$input = urldecode($input);
			$input=str_replace(array('{addplus}'),array('+'),$input);
		}
		// PHP版本大于5.4.0，直接转义字符
		if (strnatcasecmp(PHP_VERSION, '5.4.0') >= 0) {
			$input = addslashes($input);
		} else {
			// 魔法转义没开启，自动加反斜杠
			if (!get_magic_quotes_gpc()) {
				$input = addslashes($input);
			}
		}
	}
	//防止最后一个反斜杠引起SQL错误如 'abc\'
	if(substr($input,-1,1)=='\\') $input=$input."'";//$input=substr($input,0,strlen($input)-1);
	return $input;
}

//处理XSS，$input=$_COOKIE,$_GET,$_POST
function sqlxss($input){
	if(is_array($input)){
		foreach($input as $k=>$v){
			$k=sqlxss($k);
			$input[$k]=sqlxss($v);
		}
	}else{
		$input=escape($input,1);
		$input=htmlspecialchars($input,ENT_QUOTES);
	}
	return $input;
}

// 将时间转换成几分钟前
function time_tran($the_time) {
	$now_time = date("Y-m-d H:i:s", time());
	$now_time = strtotime($now_time);
	$show_time = strtotime($the_time);
	$dur = $now_time - $show_time;
	if ($dur < 0) {
		return $the_time;
	} else {
		if ($dur < 60) {
			return $dur . '秒前';
		} else {
			if ($dur < 3600) {
				return floor($dur / 60) . '分钟前';
			} else {
				if ($dur < 86400) {
					return floor($dur / 3600) . '小时前';
				} else {
					if ($dur < 259200) {//3天内
						return floor($dur / 86400) . '天前';
					} else {
						return date('m-d',$show_time);
					}
				}
			}
		}
	}
}

// 生成订单号
function get_rand_orderid() {
	list($t1, $t2) = explode(' ', microtime());
	$order_id = $t2.rand(10,99).rand(10,99).rand(10,99).rand(0,9);
	return $order_id;
}

// 生成确定要传递给支付宝的参数
function build_alipay_sign($data = array()) {
	if(empty($data)) {
		return '';
	}
	$str = '';
	foreach ($data as $key=>$val) {
		$str .=  $key . '="' . $val . '"&';
	}
	return substr($str, 0, -1);
}

/**
 * 支付宝RSA签名
 * @param $data 待签名数据
 * @param $private_key 商户私钥字符串
 * return 签名结果
 */
function aliRsaSign($data, $private_key) {
	//以下为了初始化私钥，保证在您填写私钥时不管是带格式还是不带格式都可以通过验证。
	$private_key=str_replace("-----BEGIN RSA PRIVATE KEY-----","",$private_key);
	$private_key=str_replace("-----END RSA PRIVATE KEY-----","",$private_key);
	$private_key=str_replace("\n","",$private_key);
	$private_key="-----BEGIN RSA PRIVATE KEY-----".PHP_EOL .wordwrap($private_key, 64, "\n", true). PHP_EOL."-----END RSA PRIVATE KEY-----";
	$res=openssl_get_privatekey($private_key);
	if($res) {
		openssl_sign($data, $sign,$res);
	}else {
		die ('{"code":"1","msg":"您的私钥格式不正确","data":""}');
	}
	openssl_free_key($res);
	//base64编码
	$sign = base64_encode($sign);
	return $sign;
}