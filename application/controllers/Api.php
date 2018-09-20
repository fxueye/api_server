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

		// log_message ( 'info', 'key:' . $key );
		$ret = $this->api_model->get_qq_quan();
		echo json_encode($ret);
	}
	function randomCoupon($w="",$app_key="",$app_secret="",$app_pid=""){
		$words = array(
				"女装",
				"男装",
				"童装",
				"情人节"
			      );
		if($w != null){
			$word = $w;
		}else{
			$word = $words[mt_rand(0,count($words) - 1)];
		}
		$pageNo = mt_rand(1,20);
		$this->api_model->set_taoke_param($app_key,$app_secret,$app_pid);
		$list =  $this->api_model->get_coupon($word,20,$pageNo);
		$coupon = $list[mt_rand(0,count($list) - 1)];
		// $small_images = json_decode($coupon['small_images'],true);
		$logo = $coupon['pict_url'];
		// if(count($small_images) > 0){
		// 	$logo = $small_images[0];
		// }
		$title = $coupon['title'];
		$coupon_click_url = $coupon['coupon_click_url'];
		$coupon['tpwd'] = $this->api_model->get_tpwd($title,$coupon_click_url,$logo);
		return $coupon;
	}
	function coupon(){
		$words = array(
				"女装",
				"男装",
				"童装",
				"情人节"
			      );

		$w = $this->get_post('w');
		$app_key = $this->get_post("app_key");
		$app_secret = $this->get_post("app_secret");
		$app_pid = $this->get_post("app_pid");
		$this->api_model->set_taoke_param($app_key,$app_secret,$app_pid);

		if($w == ""){
			$w = $words[mt_rand(0,count($words) - 1)];
		}
		$pageSize = $this->get_post('pageSize');
		if($pageSize == ""){
			$pageSize = 20;
		}
		$pageNo = $this->get_post('pageNo');
		if($pageNo == ""){
			$pageNo = mt_rand(1,20);
		}
		$platform = $this->get_post('platform');
		if($platform == ""){
			$platform = 2;
		}
		$list =  $this->api_model->get_coupon($w,$pageSize,$pageNo,$platform);

		$coupon = $list[mt_rand(0,count($list) - 1)];
		// $small_images = json_decode($coupon['small_images'],true);
		$logo = $coupon['pict_url'];
		// if(count($small_images) > 0){
		// 	$logo = $small_images[0];
		// }
		log_message(INFO,"logo:".$logo);
		$title = $coupon['title'];
		$coupon_click_url = $coupon['coupon_click_url'];
		$coupon['tpwd'] = $this->api_model->get_tpwd($title,$coupon_click_url,$logo);
		$this->ret = $coupon;
	}
	function tpwd($title,$url){
		$title =  $this->get_post('title');
		$url = $this->get_post('url');
		$this->ret = $this->api_model->get_tpwd($title,$url);
	}

}
