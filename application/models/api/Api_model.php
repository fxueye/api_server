<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends MY_Model {
	const DB_KEY = "";
	private $pid = "";
	private $adzoneId = "";
	private $dtkSdk = null;
	function __construct() {
		parent::__construct(self::DB_KEY);
		date_default_timezone_set('Asia/Shanghai'); 
		set_time_limit(0);

		$this->load->library("taobao_sdk/Autoloader");
		$this->tao_client = new TopClient();
		$this->tao_client->format = "json";
		$this->set_taoke_param();

		$this->load->libaray('dataoke/DtkSdk');
		$this->dtkSdk = new DtkSdk();
	}
	public function get_qq_quan(){
		return $this->dtkSdk->get_data(DtkSdk::$qq,"qq_quan",1);
	}
	public function set_taoke_param($app_key = "",$app_secret = "",$tao_pid = ""){
		$tao_app_key = get_env_config("tao_app_key");
		$tao_app_secret = get_env_config("tao_app_secret");
		if($tao_pid == ""){
			$this->pid = get_env_config("tao_pid");
		}else{
			$this->pid = $tao_pid;
		}
		$strs = explode("_",$this->pid);
		if(count($strs) == 4){
			$this->adzoneId= $strs[3];
		}
		if($app_key == ""){
			$this->tao_client->appkey = $tao_app_key;
		}else{
			$this->tao_client->appkey = $app_key;
		}
		if($app_secret == ""){
			$this->tao_client->secretKey = $tao_app_secret;
		}else{
			$this->tao_client->secretKey = $app_secret;
		}
	}
	public function get_favorites_info($favorites_id,$unid = "weixin",$pageSize = 20,$pageNo = 1,$platform = 2){
		$req = new TbkUatmFavoritesItemGetRequest();
		$req->setPlatform($platform."");
		$req->setPageSize($pageSize."");
		$req->setAdzoneId($this->adzoneId."");
		$req->setUnid($unid."");
		$req->setFavoritesId($favorites_id."");
		$req->setPageNo($pageNo."");
		$req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type,coupon_click_url,coupon_end_time,coupon_info,coupon_start_time,coupon_total_count,coupon_remain_count");
		$resp = $this->tao_client->execute($req);
	}
	/**
	 *page_no	Number	false	1	第几页，从1开始计数
	 *page_size	Number	false	20	默认20，页大小，即每一页的活动个数
	 *fields	String	true	favorites_title,favorites_id,type	需要返回的字段列表，不能为空，字段名之间使用逗号分隔
	 *type	Number	false	1	默认值-1；选品库类型，1：普通选品组，2：高佣选品组，-1，同时输出所有类型的选品组
	 */
	public function get_favorites($pageSize = 20,$pageNo = 1,$type = -1){
		$req = new TbkUatmFavoritesGetRequest;
		$req->setPageNo($pageNo."");
		$req->setPageSize($pageSize."");
		$req->setFields("favorites_title,favorites_id,type");
		$req->setType($type."");
		$resp = $this->tao_client->execute($req);
		log_message(ERROR,json_encode($resp));
		$item_list = array();
		if(isset($resp->results)){
			if($resp->total_results > 0){
				$items= $resp->results->tbk_favorites;
				for($i = 0; $i < count($items); $i++){
					$item = $items[$i];
					$data = array(
							"type"=>$item->type,
							"favorites_id"=>$item->favorites_id,
							"favorites_title"=>$item->favorites_title
						     );
					$item_list[] = $data;
				}
			}
		}
		return $item_list;
	}
	public function get_spread_url($url){
		$req = new TbkSpreadGetRequest();
		$requests = new TbkSpreadRequest();
		$requests->url=$url;
		$req->setRequests(json_encode($requests));
		$resp = $this->tao_client->execute($req);
		$item_list = array();
		if(isset($resp->results)){
			if($resp->total_results > 0){
				$items =  $resp->results->tbk_spread;
				for($i = 0; $i < count($items);$i++){
					$item = $items[$i];
					$err_msg = $item->err_msg;
					if($err_msg == "OK"){
						$data = array(
								"content" => $item->content
							     );
						$item_list[] = $data;
					}
				}
			}
		}
		return $item_list;
	}
	public function get_tpwd($text,$url,$logo = "",$user_id = "",$ext = ""){
		$req = new TbkTpwdCreateRequest;
		if($user_id != ""){
			$req->setUserId($user_id."");
		}
		$req->setText($text."");
		$req->setUrl($url);
		if($logo != ""){
			$req->setLogo($logo);
		}
		if($ext != ""){
			$req->setExt($ext);
		}else{
			$req->setExt("{}");
		}
		$resp = $this->tao_client->execute($req);
		return $resp->data->model;
	}
	public function get_coupon_info($itemId,$activityId=""){
		$req = new TbkCouponGetRequest();
		$req->setItemId($itemId."");
		if($activityId != ""){
			$req->setActivityId($activityId."");
		}
		$resp = $this->tao_client->execute($req);
	}
	public function get_coupon($w,$pageSize = 20,$pageNo = 1,$platform = 2){
		$cache_key = md5(sprintf('search_coupon_%s_%d_%s',date('YmdH'),$pageNo,md5($w)));
		$cache_data = $this->get_cache($cache_key);
		if($cache_data != false){
			return $cache_data;
		}
		$req = new TbkDgItemCouponGetRequest();
		$req->setAdzoneId($this->adzoneId."");
		$req->setPlatform($platform."");
		$req->setPageSize($pageSize."");
		$req->setQ($w);
		$req->setPageNo($pageNo."");
		$resp = $this->tao_client->execute($req);
		$item_list = array();
		if(isset($resp->results)){
			if($resp->total_results > 0){
				$items = $resp->results->tbk_coupon;
				for($y = 0; $y < count($items);$y ++){
					$item = $items[$y];
					$small_images = '';
					if (isset($item->small_images)){
						$small_images = json_encode($item->small_images->string);
					}
					$title = $item->title;
					$shop_title = $item->shop_title;
					$user_type = $item->user_type;
					$zk_final_price = $item->zk_final_price;
					$nick = $item->nick;
					$seller_id = $item->seller_id;
					$volume = $item->volume;
					$pict_url = $item->pict_url."_250x250";
					$item_url = $item->item_url;
					$coupon_total_count = isset($item->coupon_total_count) ? $item->coupon_total_count: 0 ;
					$commission_rate = $item->commission_rate;
					if(!isset($item->coupon_info)){
						continue;	
					}
					$coupon_info = isset($item->coupon_info) ? $item->coupon_info : "" ;
					preg_match_all('/\d+/',$coupon_info,$arr);
					if($y == count($items)-1 && count($item_list) == 0){

					}else{
						if($arr[0][1] < 5){
							continue;
						}
					}

					$category = $item->category;
					$num_iid =$item->num_iid;
					$coupon_remain_count = isset($item->coupon_remain_count) ?$item->coupon_remain_count : 0 ;
					$coupon_start_time = "";
					if(isset($item->coupon_start_time)){
						$coupon_start_time=$item->coupon_start_time;
					}
					$coupon_end_time = "";
					if(isset($item->coupon_end_time)){
						strtotime($item->coupon_end_time);
					}

					$item_description = $item->item_description;
					$coupon_click_url = $item->coupon_click_url;
					$tpwd = "";
					$data = array(
							'small_images' => $small_images,
							'title'=>$title,
							'shop_title' =>$shop_title,
							'user_type' =>$user_type,
							'zk_final_price' =>$zk_final_price,
							'nick' => $nick,
							'seller_id' => $seller_id,
							'volume' => $volume,
							'pict_url'=>$pict_url,
							'item_url' => $item_url,
							'coupon_total_count' => $coupon_total_count,
							'commission_rate'=>$commission_rate,
							'coupon_info'=>$coupon_info,
							'category' => $category,
							'num_iid' => $num_iid,
							'coupon_remain_count' => $coupon_remain_count,
							'coupon_start_time' =>$coupon_start_time,
							'coupon_end_time' => $coupon_end_time,
							'item_description' => $item_description,
							'coupon_click_url' => $coupon_click_url,
							'tpwd' => $tpwd
								);
					$item_list[] = $data;
				}
			}
			if(count($item_list) > 0){
				$this->set_cache($cache_key,$item_list,60*60);
				return $item_list;
			}
			return $item_list;
		}
	}

}
