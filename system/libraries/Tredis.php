<?php

/**
 * Class CI_Tredis
 * Redis 集群类，支持M/S的集群方案
 *
 * @author      李守杰<1335244575@qq.com>
 * @copyright   Copyright © 2017-2017 www.huiweishang.com. All rights reserved.
 * @created     2017-02-22
 * @updated     2017-02-22
 * @link        http://www.huiweishang.com
 */
class CI_Tredis {
     // 是否使用 M/S 的读写集群方案
     private $_ms = false;
     //CI实例
     private $_ci = null;
     //Master 句柄标记
     private $_mn = 0;
     // Slave 句柄标记
     private $_sn = 0;
     // 服务器连接句柄
     private $_linkHandle = array(
         'master'=>null,// 可以有多台 Master
         'slave'=>array(),// 可以有多台 Slave
     );

     public function __construct( $isMaster = true,$masterIndex = 0 , $slaveIndex = 0,$selectDb = 0 ) {
         if( !extension_loaded('redis')){
             throw new Exception('the redis extension not found');
         }
         $this->_ci = &get_instance();
         $this->_ci->config->load('redis');
         //判断是否开启redis集群
         $ms = $this->_ci->config->item('is_ms');
         if( isset($ms) ){
             $this->_ms = $ms;
         }
         $this->connect( $isMaster,$masterIndex , $slaveIndex,$selectDb);
     }
     /**
      * 连接服务器,注意：这里使用长连接，提高效率，但不会自动关闭
      *
      * @param boolean $isMaster 当前添加的服务器是否为 Master 服务器
      * @param int $masterIndex 当前添加的主服务器的配置索引
      * @param int $slaveIndex 当前添加的从服务器的配置索引
      * @return boolean
      */
     public function connect($isMaster=true , $masterIndex = 0 ,$slaveIndex = 0 ,$selectDb = 0){
         if( $isMaster ){
             $config = $this->_ci->config->item('master');
             if( !isset($config[$masterIndex]) && !empty($config[$masterIndex])){
                 throw new Exception('The redis connect is not config');
             }
             $this->_linkHandle['master'] = new Redis();
             $ret = $this->_linkHandle['master']->pconnect($config[$masterIndex]['host'],$config[$masterIndex]['port']);
             $this->_linkHandle['master']->auth($config[$masterIndex]['password']);
             $this->_linkHandle['master']->select($selectDb);
         } else {
             $config = $this->_ci->config->item('slave');
             if( !isset($config[$slaveIndex]) && !empty($config[$slaveIndex])){
                 throw new Exception('the redis connect is not config');
             }
             $this->_linkHandle['slave'][$this->_sn] = new Redis();
             $ret = $this->_linkHandle['slave'][$this->_sn]->pconnect($config[$slaveIndex]['host'],$config[$slaveIndex]['port']);
             $this->_linkHandle['slave'][$this->_sn]->auth($config[$slaveIndex]['password']);
             $this->_linkHandle['slave'][$this->_sn]->select($selectDb);
             ++$this->_sn;
         }
         return $ret;
     }
     /**
      * 关闭连接
      *
      * @param int $flag 关闭选择 0:关闭 Master 1:关闭 Slave 2:关闭所有
      * @return boolean
      */
     public function close($flag=2){
         switch($flag){
             // 关闭 Master
             case 0:
                 $this->getRedis()->close();
                 break;
             // 关闭 Slave
             case 1:
                 for($i=0; $i<$this->_sn; ++$i){
                     $this->_linkHandle['slave'][$i]->close();
                 }
                 break;
             // 关闭所有
             case 2:
                 $this->getRedis()->close();
                 for($i=0; $i<$this->_sn; ++$i){
                     $this->_linkHandle['slave'][$i]->close();
                 }
                 break;
         }
         return true;
     }
     /**
      * 得到 Redis 原始对象可以有更多的操作
      *
      * @param boolean $isMaster 返回服务器的类型 true:返回Master false:返回Slave
      * @param boolean $slaveOne 返回的Slave选择 true:负载均衡随机返回一个Slave选择 false:返回所有的Slave选择
      * @return redis object
      */
     public function getRedis($isMaster=true,$slaveOne=true){
         // 只返回 Master
         if($isMaster){
             return $this->_linkHandle['master'];
         }else{
             return $slaveOne ? $this->_getSlaveRedis() : $this->_linkHandle['slave'];
         }
     }

     public function __clone()  {
         trigger_error( 'Clone is not allow!', E_USER_ERROR );
     }
     public function __destruct()  {
         if($this->getRedis()){
             $this->close(2);
         }
     }
     /**
      * 写缓存
      *
      * @param string $key 组存KEY
      * @param string $value 缓存值
      * @param int $expire 过期时间， 0:表示无过期时间
      */
     public function set($key, $value, $expire=0){
         // 永不超时
         if($expire == 0){
             $ret = $this->getRedis()->set($key, $value);
         }else{
             $ret = $this->getRedis()->setex($key, $expire, $value);
         }
         return $ret;
     }

     /**
      * 读缓存
      *
      * @param string $key 缓存KEY,支持一次取多个 $key = array('key1','key2')
      * @return string || boolean 失败返回 false, 成功返回字符串
      */
     public function get($key){
         // 是否一次取多个值
         $func = is_array($key) ? 'mGet' : 'get';
         // 没有使用M/S
         if(! $this->_ms){
             return $this->getRedis()->{$func}($key);
         }
         // 使用了 M/S
         return $this->_getSlaveRedis()->{$func}($key);
     }

     /**
      * 条件形式设置缓存，如果 key 不存时就设置，存在时设置失败
      *
      * @param string $key 缓存KEY
      * @param string $value 缓存值
      * @return boolean
      */
     public function setnx($key, $value){
         return $this->getRedis()->setnx($key, $value);
     }
     /**
      * 删除缓存
      *
      * @param string || array $key 缓存KEY，支持单个健:"key1" 或多个健:array('key1','key2')
      * @return int 删除的健的数量
      */
     public function remove($key){
         // $key => "key1" || array('key1','key2')
         return $this->getRedis()->delete($key);
     }

     /**
      * 值加加操作,类似 ++$i ,如果 key 不存在时自动设置为 0 后进行加加操作
      *
      * @param string $key 缓存KEY
      * @param int $default 操作时的默认值
      * @return int　操作后的值
      */
     public function incr($key,$default=1){
         if($default == 1){
             return $this->getRedis()->incr($key);
         }else{
             return $this->getRedis()->incrBy($key, $default);
         }
     }
     /**
      * 值减减操作,类似 --$i ,如果 key 不存在时自动设置为 0 后进行减减操作
      *
      * @param string $key 缓存KEY
      * @param int $default 操作时的默认值
      * @return int　操作后的值
      */
     public function decr($key,$default=1){
         if($default == 1){
             return $this->getRedis()->decr($key);
         }else{
             return $this->getRedis()->decrBy($key, $default);
         }
     }

     /**
      * 添空当前数据库
      *
      * @return boolean
      */
     public function clear(){
         return $this->getRedis()->flushDB();
     }

     /**
      * 随机 HASH 得到 Redis Slave 服务器句柄
      *
      * @return redis object
      */
     private function _getSlaveRedis(){
         // 就一台 Slave 机直接返回
         if($this->_sn <= 1){
             return $this->_linkHandle['slave'][0];
         }
         // 随机 Hash 得到 Slave 的句柄
         $hash = $this->_hashId(mt_rand(), $this->_sn);
         return $this->_linkHandle['slave'][$hash];
     }

     /**
      * 根据ID得到 hash 后 0～m-1 之间的值
      *
      * @param string $id
      * @param int $m
      * @return int
      */
     private function _hashId($id,$m=10)
     {
         //把字符串K转换为 0～m-1 之间的一个值作为对应记录的散列地址
         $k = md5($id);
         $l = strlen($k);
         $b = bin2hex($k);
         $h = 0;
         for($i=0;$i<$l;$i++)
         {
             //相加模式HASH
             $h += substr($b,$i*2,2);
         }
         $hash = ($h*1)%$m;
         return $hash;
     }

     /**
      *  lpush
      */
     public function lpush($key,$value){
         return $this->getRedis()->lpush($key,$value);
     }
     /**
      *  add lpop
      */
     public function lpop($key){
         return $this->getRedis()->lpop($key);
     }
     /**
      * lrange
      */
     public function lrange($key,$start,$end){
         return $this->getRedis()->lrange($key,$start,$end);
     }
     /**
      *  set hash opeation
      */
     public function hset($name,$key,$value){
         if(is_array($value)){
             return $this->getRedis()->hset($name,$key,serialize($value));
         }
         return $this->getRedis()->hset($name,$key,$value);
     }

     /**
      *  get hash opeation
      */
     public function hget($name,$key = null,$serialize=true){
         if($key){
             $row = $this->getRedis()->hget($name,$key);
             if($row && $serialize){
                 unserialize($row);
             }
             return $row;
         }
         return $this->getRedis()->hgetAll($name);
     }
    /**
     * 同时将多个 $field->$value (域-值)对设置到哈希表 $key 中
     */
    public function hash_set_array($key, $arr) {
        return $this->getRedis()->hMset ( $key, $arr );
    }
     /**
      *  delete hash opeation
      */
     public function hdel($name,$key = null){
         if($key){
             return $this->getRedis()->hdel($name,$key);
         }
         return $this->getRedis()->hdel($name);
     }
     /**
      * Transaction start
      */
     public function multi(){
         return $this->getRedis()->multi();
     }
     /**
      * Transaction send
      */
     public function exec(){
         return $this->getRedis()->exec();
     }
    /**
     * 为 $key 设置生存时间 $time 注意 时间是一个时间戳
     */
    public function time_out($key, $time) {
        return $this->getRedis()->expireAt ( $key, $time );
    }
    /**
     * 将哈希表 $key 中的域 $field 的值设为 $value
     */
    public function hash_set($key, $field, $value) {
        return $this->getRedis()->hSet ( $key, $field, $value );
    }
    /**
     * 返回哈希表 $key 中给定域 $field 的值
     */
    public function hash_get($key, $field) {
        return $this->getRedis()->hGet ( $key, $field );
    }

    /**
     * 返回哈希表 $key 中，所有的域和值
     */
    public function hash_get_array($key) {
        return $this->getRedis()->hGetAll ( $key );
    }
    
    /**
     * 保存多维数组信息
     * @param type $key
     * @param type $data
     */
    public function set_array($key, $data){
        return $this->getRedis()->set($key, json_encode($data));
    }
    
    /**
     * 获取多维数组信息
     * @param type $key
     * @return type
     */
    public function get_array($key){
        $value = $this->getRedis()->get($key);
        return $value !== false ? json_decode($value, true) : false;
    }
 }
?>
