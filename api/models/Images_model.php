<?php
/**
 * Created by PhpStorm.
 * User: 李守杰
 * Date: 2017/04/26
 * Desc:
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Images_model extends MY_Model {
    protected $table = 'images';
    public function __construct() {
        parent::__construct();
        $this->setDB('db_t');
    }
    public function getIndexImagesLists($category_id,$limit = 0, $offset = 0, $order_by = 'image_ctime desc', $group_by = ''){
        $fields = "image_id,image_name,image_thumb,image_isvip";
        $where = array('category'=>$category_id,'image_status'=>1);
        $images = $this->getData($fields,$where,$limit,$offset,$order_by,$group_by);
        return $images;
    }

    public function getImageDetail( $image_id ){
        $fields = "image_id,image_name,image_thumb,category AS category_id,image_source,image_isvip,image_views,image_zan";
        $where = array('image_id'=>$image_id,'image_status'=>1);
        $imageInfo = $this->getOne($fields,$where);
        return $imageInfo;
    }

}