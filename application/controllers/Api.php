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

        // $ret = $this->api_model->get_coupon("女装",20,1,2);
        
        // $ret = $this->api_model->get_coupon_info("571659414649");

        // $ret = $this->api_model->get_favorites(20,1,-1);

        // echo json_encode($ret);
        $this->set_cache("key","1",3600 * 24);
        $key = $this->get_cache("key2");
        log_message ( 'info', 'key:' . $key );
    }

    function coupon(){
        $w = $this->get_post('w');
        $pageSize = $this->get_post('pageSize');
        if($pageSize == ""){
            $pageSize = 20;
        }
        $pageNo = $this->get_post('pageNo');
        if($pageNo == ""){
            $pageNo = 1;
        }
        $platform = $this->get_post('platform');
        if($platform == ""){
            $platform = 2;
        }
        $list =  $this->api_model->get_coupon($w,$pageSize,$pageNo,$platform);
        $ret_data = array(
            'list' => $list,
            'total' => count($list),
        );
        $this->ret = $ret_data;
    }
    function tpwd($title,$url){
        $title =  $this->get_post('title');
        $url = $this->get_post('url');
        $this->ret = $this->api_model->get_tpwd($title,$url);
    }
}