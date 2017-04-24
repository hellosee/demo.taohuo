<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class TTT_Controller extends REST_Controller {
	protected $_session = null;
	public function __construct() {
        parent::__construct();
        $this->_session = new CI_Tsession();
    }

}