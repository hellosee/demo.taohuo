<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/libraries/youku/Constants.class.php';
require_once APPPATH.'/libraries/youku/Ep.class.php';
require_once APPPATH.'/libraries/youku/Video.class.php';

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
	    $film_id = $this->input->post('film_id');
        if($film_id == ""){output(array('code'=>0,'msg'=>'参数错误')); }
        $filmInfo = $this->films->getFilmDetail($film_id);
        $filmInfo['film_play_url'] = "";
        /*解析优酷地址*/
        if($filmInfo['film_source'] == 1){
            preg_match("~id_(.*?)\.html~",$filmInfo['film_url'],$match);
            if(!empty($match)){
                $snoopy = new Snoopy();
                $url = "http://120.24.84.28:8888/api";
                $submit_vars["id"] = $match[1];
                $snoopy->submit($url,$submit_vars);
                $data = json_decode($snoopy->getResults(),1);
                if(!$data['error']){
                    $filmInfo['film_play_url'] = 'http:'.$data['play']['urls'][0];
                }
            }
        }
        /*获取相关推荐*/
        $filmInfo['recommend'] = $this->films->getIndexFilmsLists($filmInfo['category_id'],PAGESIZE,$this->get_limit(PAGESIZE,1));
        output(array('code'=>1,'msg'=>'success','data'=>$filmInfo));
    }
}