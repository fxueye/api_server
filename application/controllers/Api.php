<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends MY_Controller {
    private $ase;
    function __construct() {
        parent::__construct();
        
        date_default_timezone_set('Asia/Shanghai'); 
        set_time_limit(0);

        $key = get_env_config("app_key");
        $iv = get_env_config("app_iv");
        
        $this->load->library("aes");
        $this->aes= new Aes();
        $this->aes->init($key,$iv);
        $this->load->model('api/api_model');
    }
    function index(){
        $ret = $this->api_model->get_coupon("女装",20,1,2);
        echo json_encode($ret);
    }
}