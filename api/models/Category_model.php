<?php
/**
 * Created by PhpStorm.
 * User: 李守杰
 * Date: 2017/04/25
 * Desc: 栏目表
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends MY_Model {
    protected $table = 'film_category';
    public function __construct() {
        parent::__construct();
        $this->setDB('db_t');
    }

    /**
     * user：李守杰
     * Date 2017/04/25
     * DESC：插入用户（注册操作）
     */
    public function getCategory($type = 1){
        if(!in_array($type, array(1,2))){ return array();}
        $category = $this->getData('category_id,category_name',array('type'=>$type),0,0,"sortby desc");
        return $category;
    }




}