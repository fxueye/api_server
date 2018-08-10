<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends MY_Model {
    const DB_KEY = "";
    function __construct() {
        parent::__construct(self::DB_KEY);
        date_default_timezone_set('Asia/Shanghai'); 
        set_time_limit(0);
        $tao_app_key = get_env_config("tao_app_key");
        $tao_app_secret = get_env_config("tao_app_secret");
        $this->load->library("taobao_sdk/Autoloader");
        $this->tao_client = new TopClient();
        $this->tao_client->format = "json";
        $this->tao_client->appkey = $tao_app_key;
        $this->tao_client->secretKey = $tao_app_secret;
    }

    public function get_recommend($num_iid){
        $cache_key = md5(sprintf('recommend_%s',$num_iid));
        $cache_data = $this->get_cache($cache_key);
        if($cache_data != false){
            return $cache_data;
        }
        $req = new TbkItemRecommendGetRequest();
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,volume,item_url");
        $req->setNumIid($num_iid);
        $req->setCount("10");
        $req->setPlatform("2");
        $resp = $this->tao_client->execute($req);
        if(isset($resp->results)){
            $items = $resp->results->n_tbk_item;
            log_message("error",count($items));
            $list = array();
            for($y = 0;$y < count($items);$y ++ ){
                $item = $resp->results->n_tbk_item[$y];
                $tags = "";
                $pict_url = $item->pict_url."_250x250";
                $zk_final_price = $item->zk_final_price;
                $num_iid = $item->num_iid;
                $title = $item->title;
                $reserve_price = $item->reserve_price;
                $volume = $item->volume;
                $user_type = $item->user_type;
                $topic_id = 0;
                $list_item = array(
                    'type'=>1,
                    'num_iid'=>$num_iid,
                    'title'=>$title,
                    'tags'=>$tags,
                    'topic_id'=>$topic_id,
                    'volume'=>$volume,
                    'reserve_price'=>$reserve_price,
                    'zk_final_price'=>$zk_final_price,
                    'user_type'=>$user_type,
                    'pict_url'=>$pict_url,
                    'cat_id'=>1,
                    'update_ts'=>time(),
                );
                $list[] = $list_item;
            }
            $this->set_cache($cache_key, $list, 80);
            return $list;
        }
        $this->CI->error->set_error(Err_Code::ERR_NO_SELECT_DATA);
        return false;
    }

    public function get_api_list_search($keyword,$page=1,$page_size=100){
        $cache_key = md5(sprintf('goods_list_%s_%d_%s',date('YmdH'),$page,md5($keyword)));
        $cache_data = $this->get_cache($cache_key);
        if($cache_data != false){
            return $cache_data;
        }

        $req = new TbkItemGetRequest();
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
        $req->setSort("total_sales_des");
        $req->setIsTmall("false");
        $req->setIsOverseas("false");
        $req->setStartPrice("6");
        $req->setEndPrice("9999");
        $req->setStartTkRate("10");
        $req->setEndTkRate("9999");
        $req->setPlatform("2");
        $req->setPageNo($page."");
        $req->setPageSize($page_size."");
        $req->setQ($keyword);
        $resp = $this->tao_client->execute($req);
        if($resp->total_results > 0){
            $items = $resp->results->n_tbk_item;
            log_message("error",count($items));
            $list = array();
            for($y = 0;$y < count($items);$y ++ ){
                $item = $resp->results->n_tbk_item[$y];
                $tags = "";
                $pict_url = $item->pict_url."_250x250";
                $zk_final_price = $item->zk_final_price;
                $num_iid = $item->num_iid;
                $title = $item->title;
                $reserve_price = $item->reserve_price;
                $volume = $item->volume;
                $user_type = $item->user_type;
                $topic_id = 0;
                $list_item = array(
                    'type'=>1,
                    'num_iid'=>$num_iid,
                    'title'=>$title,
                    'tags'=>$tags,
                    'topic_id'=>$topic_id,
                    'volume'=>$volume,
                    'reserve_price'=>$reserve_price,
                    'zk_final_price'=>$zk_final_price,
                    'user_type'=>$user_type,
                    'pict_url'=>$pict_url,
                    'cat_id'=>1,
                    'update_ts'=>time(),
                );
                $list[] = $list_item;
            }
            $this->set_cache($cache_key, $list, 80);
            return $list;
        }
        $this->CI->error->set_error(Err_Code::ERR_NO_SELECT_DATA);
        return false;
    }
}
