<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Category_model','category');
    }

    public function film() {
        $categorys = $this->category->getCategory(1);
        output(array('code'=>1,'msg'=>'success','data'=>$categorys));
    }

    public function image(){
        $categorys = $this->category->getCategory(2);
        output(array('code'=>1,'msg'=>'success','data'=>$categorys));
    }

}