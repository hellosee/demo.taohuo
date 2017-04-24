<?php
class wxpay_base{
    /**
     * 取成功响应
     * @return string
     */
    public function getSucessXml(){
        $xml = '<xml>';
        $xml .= '<return_code><![CDATA[SUCCESS]]></return_code>';
        $xml .= '<return_msg><![CDATA[OK]]></return_msg>';
        $xml .= '</xml>';
        return $xml;
    }
     
    /**
     * 取失败响应
     * @return string
     */
    public function getFailXml(){
        $xml = '<xml>';
        $xml .= '<return_code><![CDATA[FAIL]]></return_code>';
        $xml .= '<return_msg><![CDATA[OK]]></return_msg>';
        $xml .= '</xml>';
        return $xml;
    }
     
    /**
     * 数组转成xml字符串
     *
     * @param array $arr
     * @return string
     */
    protected function arrayToXml($arr){
        $xml = '<xml>';
        foreach($arr as $key => $value) {
            $xml .= "<{$key}>";
            $xml .= "<![CDATA[{$value}]]>";
            $xml .= "</{$key}>";
        }
        $xml .= '</xml>';
     
        return $xml;
    }
     
    /**
     * xml 转换成数组
     * @param string $xml
     * @return array
     */
    protected function xmlToArray($xml){
        $xmlObj = simplexml_load_string(
                $xml,
                'SimpleXMLIterator',   //可迭代对象
                LIBXML_NOCDATA
        );
     
        $arr = array();
        $xmlObj->rewind(); //指针指向第一个元素
	$arr = (array)$xmlObj;
	/*
	try{
		if(is_object($xmlObj)){
		    print_r($xmlObj);die;
		}
		while (1) {
		    if( ! is_object($xmlObj->current()) )
		    {
			break;
		    }
		    echo $xmlObj->current()->__toString();
		    die('xcxc[D]');
		    $arr[$xmlObj->key()] = $xmlObj->current()->__toString();
		    $xmlObj->next(); //指向下一个元素
		}
		print_r($arr);die;('sdfsdfsd');

	}catch(Exception $e){
		print_r($e->getMessage());echo '>>';
		die();
	}
        die;
	*/
        return $arr;
    }
    function xml_to_array( $xml ){
	    $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
	    if(preg_match_all($reg, $xml, $matches))
	    {
		$count = count($matches[0]);
		$arr = array();
		for($i = 0; $i < $count; $i++)
		{
		    $key= $matches[1][$i];
		    $val = xml_to_array( $matches[2][$i] );  // 递归
		    if(array_key_exists($key, $arr))
		    {
			if(is_array($arr[$key]))
			{
			    if(!array_key_exists(0,$arr[$key]))
			    {
				$arr[$key] = array($arr[$key]);
			    }
			}else{
			    $arr[$key] = array($arr[$key]);
			}
			$arr[$key][] = $val;
		    }else{
			$arr[$key] = $val;
		    }
		}
		return $arr;
	    }else{
		return $xml;
	    }
	}
     
    /**
     * 数组转成字符串
     * 
     * @param array $arr
     * @return string
     */
    protected  function arrayToString($arr)
    {
        $str = '';
        foreach($arr as $key => $value) {
            $str .= "{$key}={$value}&";
        }
     
        return substr($str, 0, strlen($str)-1);
    }
     
    /**
     * 通过POST方法请求URL
     * @param string $url
     * @param array|string $data post的数据
     *
     * @return mixed
     */
    protected function postUrl($url, $data) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //忽略证书验证
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        return $result;
    }
     
    /**
     * 通过GET方法请求URL
     * @param string $url
     *
     * @return mixed
     */
    protected function getUrl($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //忽略证书验证
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
     
    /**
     * 获取随机字符串
     * @return string 不长于32位
     */
    protected function getRandomStr()
    {
        return substr( rand(10, 999).strrev(uniqid()), 0, 15 );
    }
     
    /**
     * MD5签名
     *
     * @param string $str 待签名字符串
     * @return string 生成的签名
     */
    protected function signMd5($str)
    {
        return md5($str);
    }
     
    /*
     * 过滤待签名数据，sign和空值不参加签名
     *
     * @return array
     */
    protected function filter($params)
    {
        $tmpParams = array();
        foreach ($params as $key => $value) {
            if( $key != 'sign' && ! empty($value) ) {
                $tmpParams[$key] = $value;
            }
        }
     
        return $tmpParams;
    }
    
}