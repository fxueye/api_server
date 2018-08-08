<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends MY_Controller {
    private $tao_client = null;
    private $ase;
    function __construct() {
        parent::__construct();
        
        date_default_timezone_set('Asia/Shanghai'); 
        set_time_limit(0);

        $tao_app_key = get_env_config("tao_app_key");
        $tao_app_secret = get_env_config("tao_app_secret");
        $key = get_env_config("app_key");
        $iv = get_env_config("app_iv");

        $this->load->library("taobao_sdk/Autoloader");
        $this->tao_client = new TopClient();
        $this->tao_client->format = "json";
        $this->tao_client->appkey = $tao_app_key;
        $this->tao_client->secretKey = $tao_app_secret;
        
        $this->load->library("aes");
        $this->aes= new Aes();
        $this->aes->init($key,$iv);
    }
    function index(){
        echo "####";
    }
}