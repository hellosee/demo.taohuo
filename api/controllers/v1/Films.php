<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Films extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Films_model','films');
	}
	
	public function getIndexLists() {
        $p = $this->input->post('p');
        if(!$p){$p = 1;}
        $category_id = $this->input->post('category_id');
        $filmLists = $this->films->getIndexFilmsLists($category_id,PAGESIZE,$this->get_limit(PAGESIZE,$p));
        output(array('code'=>1,'msg'=>'success','data'=>$filmLists));
	}

	public function getFilmDetail(){

    }
}