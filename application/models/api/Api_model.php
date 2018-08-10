<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends MY_Model {
    const DB_KEY = "";
    private $pid = "";
    private $adzoneId = "";
    function __construct() {
        parent::__construct(self::DB_KEY);
        date_default_timezone_set('Asia/Shanghai'); 
        set_time_limit(0);
        $tao_app_key = get_env_config("tao_app_key");
        $tao_app_secret = get_env_config("tao_app_secret");
        $this->pid = get_env_config("tao_pid");
        $strs = explode("_",$this->pid);
        if(count($strs) == 4){
            $this->adzoneId= $strs[3];
        }

        $this->load->library("taobao_sdk/Autoloader");
        $this->tao_client = new TopClient();
        $this->tao_client->format = "json";
        $this->tao_client->appkey = $tao_app_key;
        $this->tao_client->secretKey = $tao_app_secret;

    }
    public function get_coupon($itemId,$activityId=""){
        $req = new TbkCouponGetRequest();
        $req->setItemId($itemId."");
        if($activityId == ""){
            $req->setActivityId($activityId."");
        }
        $resp = $c->execute($req);
        log_message(json_encode($resp));
    }
    public function get_coupon($w,$pageSize,$pageNo,$platform){
        $req = new TbkDgItemCouponGetRequest();
        $req->setAdzoneId($this->adzoneId);
        $req->setPlatform($platform."");
        $req->setPageSize($pageSize."");
        $req->setQ($w);
        $req->setPageNo($pageNo+"");
        $resp = $this->tao_client->execute($req);
        $item_list = array();
        if(isset($resp->results)){
            if($resp->total_results > 0){
                $items = $resp->results->tbk_coupon;
                for($y = 0; $y < count($items);$y ++){
                    $item = $items[$y];
                    $gcat_id=0;
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
                    $data = array(
                        'gcat_id' => $gcat_id,
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
                        'coupon_click_url' => $coupon_click_url
                    );
                    $item_list[] = $data;
                }
            }
            return $item_list;
        }
    }

}
