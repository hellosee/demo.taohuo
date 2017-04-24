<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
|  超级会员套餐
| -------------------------------------------------------------------
 */
$config['vip']['1'] =  array('id'=>'1', 'text'=>'天天推12个月超级会员','months'=>'12','price_txt'=>'￥238','price'=>'238');
$config['vip']['2'] =  array('id'=>'2', 'text'=>'天天推6个月超级会员','months'=>'6','price_txt'=>'￥118','price'=>'118');
$config['vip']['3'] =  array('id'=>'3', 'text'=>'天天推3个月超级会员','months'=>'3','price_txt'=>'￥60','price'=>'60');
$config['vip']['4'] =  array('id'=>'4', 'text'=>'天天推1个月超级会员','months'=>'1','price_txt'=>'￥25','price'=>'25');

/*
 | -------------------------------------------------------------------
 |  微信支付配置
 |	 * appid        公众账号appid
 |	 * appsecret    公众号appsecret
 |	 * mch_id       商户号
 |	 * apikey       加密key
 | -------------------------------------------------------------------
 */
$config['wechat']['appid']     = 'wx74b311b6181bbaa0';
$config['wechat']['mch_key']   = '2017003f65753a9c6691ad25875ce73e';
$config['wechat']['mch_id']    = '1444538502';

/*
 | -------------------------------------------------------------------
 |  支付宝支付配置
 |	 * partner      		合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串
 |	 * private_key          商户的私钥,此处填写原始私钥去头去尾
 |	 * alipay_public_key    支付宝的公钥
 |	 * service              异步通知接口
 |	 * payment_type         支付类型， 固定值
 |	 * sign_type            签名方式 不需修改
 |	 * input_charset        字符编码格式 目前支持 gbk 或 utf-8
 |	 * it_b_pay             设置未付款交易的超时时间,默认30分钟，一旦超时，该笔交易就会自动被关闭。 m-分钟，h-小时，d-天，1c-当天（无论交易何时创建，都在0点关闭）。
 |	 * return_url           支付宝处理完请求后，当前页面跳转到商户指定页面的路径，可空
 | -------------------------------------------------------------------
 */
$config['alipay']['partner']             = '2088021874337841';
$config['alipay']['private_key']         = 'MIICXgIBAAKBgQDGgLXhNlHh8RcMKzRXSZW4i8FA5lpsexODJXPUZGrmPSgwcZWzzslAc31k3SaYkYB2yFXXgC27D2BoVdRPgmmonTPQ/uJPB/wFfRHlRuc4DhBVkslW+xC7gG9NmWpWpV5/yV0O+Hkyf5Q1KwqPQN04r8U31SQGhj/yamSvdhdAnQIDAQABAoGBAId8uqfpp6IMKKsJokQh2auI2HMvx3Bb4UmWeqTxlXVpMNE/9eowrnTrXx9FQ17HkWOCAfWBa7VOHYOZcbyDSxGy7tTGaWFjsyXtUeA5zVJGKDpHsteJOZLt+IW7MEpek9aTdoZ8WEw46btdK445bbJluQkSrYOeQyxBHMS2KLkBAkEA88IueKXjZVb4VgwsdAMgQbyNhQubj+P94ZBsuaHCd54YfszMtY8lwdcDvdXZfwZGhFCmF4mdHZHYVBGr2Gop0QJBANB4tcrOytYl/fjxLdlWcADrI9+biTdjD/FC/IGeGv7bOeaQwFVfQMR7ICBo6C6w5pEfGQ5lkGr/xAjzFi/4UQ0CQQDNghgV5Z4asUE7opXT1VLbuTEDS28C2DASfOX9d1Bx8tsqBFZOd/pYO4PPB72P52WKZkgxrfbFmFFvR1q/YXoRAkBgQzz+lxqGZCiwKP3Y29cANZPtDu7/ili0ORBL8evZPvWvh6uoQEGx+IDPNmHwHXN6E3gIc9GJ9uxYjZbzM6CJAkEA3zPAE83u2vDLA7eiYxbcbirxucmv9goxen7LY7j9YWhcoVL7L/Pq0Nl4/F22NdZKc5zOtC4m0vpmwjzOzgaYrQ==';
$config['alipay']['alipay_public_key']   = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';
$config['alipay']['service']             = 'mobile.securitypay.pay';
$config['alipay']['payment_type']        = '1';
$config['alipay']['sign_type']           = strtoupper('RSA');
$config['alipay']['input_charset']       = strtolower('utf-8');
$config['alipay']['it_b_pay']   		 = '30m';
$config['alipay']['return_url']   		 = 'm.alipay.com';
$config['alipay']['transport']   		 = 'http';
$config['alipay']['cacert']   		 	 = getcwd().'/cacert.pem';

/*
 | -------------------------------------------------------------------
 |  ios内购价格配置
 | -------------------------------------------------------------------
 */
$config['ios']['huiyuan_yigeyue'] =  array('id'=>'1', 'text'=>'天天推1个月超级会员','months'=>'1','price'=>'25');
$config['ios']['huiyuan_sangeyue'] =  array('id'=>'2', 'text'=>'天天推3个月超级会员','months'=>'3','price'=>'60');
$config['ios']['huiyuan_liugeyue'] =  array('id'=>'3', 'text'=>'天天推6个月超级会员','months'=>'6','price'=>'118');
$config['ios']['huiyuan_shiergeyue'] =  array('id'=>'4', 'text'=>'天天推12个月超级会员','months'=>'12','price'=>'238');