<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Images extends MY_Controller {

    private $_remote_host = "http://img.phpnote.com/";

    public function __construct(){
        parent::__construct();
        $this->load->model('Images_model','images');
        $this->load->model('ImageFiles_model','image_files');
    }

    public function getIndexLists() {
        $p = $this->input->post('p');
        if(!$p){$p = 1;}
        $category_id = $this->input->post('category_id');
        $imageLists = $this->images->getIndexImagesLists($category_id,PAGESIZE,$this->get_limit(PAGESIZE,$p));
        foreach($imageLists as $k => $imageList){
            $imageLists[$k]['image_thumb'] = $this->_remote_host.$imageList['image_thumb'];
            $imageLists[$k]['image_num'] = $this->image_files->getImageFileCount($imageList['image_id']);
        }
        output(array('code'=>1,'msg'=>'success','data'=>$imageLists));
    }

    public function getImageDetail(){
        $image_id = $this->input->post('image_id');
        if($image_id == ""){output(array('code'=>0,'msg'=>'参数错误')); }
        $ImageInfo = $this->images->getImageDetail($image_id);
        $files = $this->image_files->getImageFileLists($image_id);
        if(!empty($files)){
            foreach ($files as $file) {
                $ImageInfo['files'][] = $this->_remote_host.$file['file_url'];
            }
        } else {
            $ImageInfo['files'] = array();
        }
        $ImageInfo['image_thumb'] = $this->_remote_host.$ImageInfo['image_thumb'];
        output(array('code'=>1,'msg'=>'success','data'=>$ImageInfo));
    }
}