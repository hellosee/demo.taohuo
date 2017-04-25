<?php
/**
 * Created by PhpStorm.
 * User: 李守杰
 * Date: 2017/04/25
 * Desc:
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Films_model extends MY_Model {
    protected $table = 'films';
    public function __construct() {
        parent::__construct();
        $this->setDB('db_t');
    }
    public function getIndexFilmsLists($category_id,$limit = 0, $offset = 0, $order_by = 'film_ctime desc', $group_by = ''){
        $fields = "film_id,film_name,film_desc,film_thumb,film_isvip";
        $where = array('category'=>$category_id,'film_status'=>1);
        $films = $this->getData($fields,$where,$limit,$offset,$order_by,$group_by);
        return $films;
    }
    
    public function getFilmDetail( $film_id ){
        $fields = "film_id,film_name,film_desc,film_thumb,film_url,category AS category_id,film_source,film_views,film_zan,film_isvip";
        $where = array('film_id'=>$film_id,'film_status'=>1);
        $filmInfo = $this->getOne($fields,$where);
        return $filmInfo;
    }

}