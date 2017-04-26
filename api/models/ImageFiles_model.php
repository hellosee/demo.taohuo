<?php
/**
 * Created by PhpStorm.
 * User: 李守杰
 * Date: 2017/04/26
 * Desc:
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class ImageFiles_model extends MY_Model {
    protected $table = 'image_files';
    public function __construct() {
        parent::__construct();
        $this->setDB('db_t');
    }
    public function getImageFileLists($image_id){
        $fields = "file_id,file_url";
        $where = array('image_id'=>$image_id);
        $images = $this->getData($fields,$where);
        return $images;
    }
}