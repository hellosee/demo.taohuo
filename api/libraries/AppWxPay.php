<?php
/**
 *	文件名称：app_pay.class.php  UTF-8
 *	作者：李守杰  (JackieLee)
 *	创建时间：2016年02月24日 5:58:18 AM
 *	实现目的：微信支付
 *	描述：
 * 	修订记录：
 */
require APPPATH.'/libraries/AppWxBase.php';

class app_pay extends wxpay_base{
    
	
	//=======【curl代理设置】===================================
	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const CURL_PROXY_HOST = "0.0.0.0";
	const CURL_PROXY_PORT = 0;
	//取access token的url
	const C_ORDER_URL	= 	'https://api.mch.weixin.qq.com/pay/unifiedorder';
	const NOTIFY_URL	=	WX_NOTIFY_URL;
	const TRADE_TYPE	=	'APP';
	protected $body 	=	'';
	protected $price	=	0;
	protected $order_id = 	0;
	protected $result   = 	null;
	
	/**
	 * @param $config 微信支付配置数组
	 */
	private $_config;
	private $_ci = NULL;
	public function __construct($config = NULL) {
		if ($config === NULL){
			$this->_ci = get_instance();
			$this->_ci->config->load('payment');
			$config = $this->_ci->config->item('wechat');
		}
		$this->_config = $config;
	}
	
    /**
     * 创建APP支付最终返回参数
     * @throws \Exception
     * @return multitype:string NULL
     */
    public function createAppPayData(){
    	
    	$data = array(
			  "appid" => $this->_config["appid"],
			  "body"  => $this->body,
			  "mch_id"=> $this->_config["mch_id"],
		      "nonce_str"=>$this->getRandomStr(),
			  "notify_url"=>self::NOTIFY_URL,
			  "out_trade_no"=>$this->order_id,
			  "spbill_create_ip"=>gethostbyname($_SERVER['REMOTE_ADDR']),
			  "total_fee"=>$this->price,
			  "trade_type"=>self::TRADE_TYPE,
    		  'key'=>$this->_config["mch_key"],
		);
    	$data['sign'] = $this->md5Sign($data);
    	unset($data['key']);
	
    	$url = self::C_ORDER_URL;
    	$result = $this->postUrl($url, $this->arrayToXml($data));
	    $arr = $this->xmlToArray($result);
    	$this->result = $arr;
    	return is_array($arr)?true:false; 
    }
     
    public function getPaySign($prepay_id){
    	$nowtime = time();
        $params = array(
                'appId'=>$this->_config["appid"],
                'timeStamp'=>"$nowtime",
                'nonceStr'=>$this->getRandomStr(),
                'package'=>"prepay_id=".$prepay_id,
                'signType'=>'MD5',
                
        );
        ksort($params);
        $params['key'] = $this->_config["mch_key"];
        $params['paySign'] = $this->md5Sign($params);
        unset($params['key']);

        //$params['signType'] = 'MD5';
        return $params;
    }
    /**
     * 生成签名
     * @return string
     */
    protected function getAppParams(){
    	$nowtime = time();
    	$params = array(
    			'appid'=>$this->_config["appid"],
    			'noncestr'=>$this->getRandomStr(),
    			'package'=>'Sign=WXpay',
    			'partnerid'=>$this->_config["mch_id"],
    			'prepayid'=>$this->result['prepay_id'],
    			'timestamp'=>"$nowtime",
    			'key'=>$this->_config["mch_key"],
    	);
    	
    	$params['sign'] = $this->md5Sign($params);
    	unset($params['key']);
    	$sign = array(
    			'nonce_str'=>$params['noncestr'],
    			'package'=>'Sign=WXpay',
    			'partener_id'=>$this->result['mch_id'],
    			'prepay_id'=>$this->result['prepay_id'],
    			'timestamp'=>$params['timestamp'],
    			'sign'=>$params['sign'],
    	);
        return $sign;
    }
    protected function md5Sign($arr){
    	//$arr = ksort($arr);
    	return strtoupper(md5($this->arrayToString($arr)));
    }
    
    /**
     * 
     * @param array $params 
     * array(
     * 		'body'=>'订单内容',
     * 		'price'=>0//订单价格
     * 		'order_id'=>'订单号'
     * )
     * @return multitype:string
     */
    public function createOrder($params = array()){
    	
    	$this->traceid	 	=	$params['order_id'];
    	$this->order_id 	=	$params['order_id'];
    	$this->price		= 	floatval($params['price']);
    	$this->body			=	$params['body'];
    	
    	$return = $this->createAppPayData();
    	if($return){
    		$result = $this->getAppParams();
    	}else{
    		return false;
    	}
    	return $result;
    }
}