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
        log_message("error","pid:"+$this->pid);
        $strs = explode("_",$this->pid);
        log_message("error",json_encode($strs));
        if(count($strs) == 4){
            $this->adzoneId= $strs[3];
        }

        $this->load->library("taobao_sdk/Autoloader");
        $this->tao_client = new TopClient();
        $this->tao_client->format = "json";
        $this->tao_client->appkey = $tao_app_key;
        $this->tao_client->secretKey = $tao_app_secret;

    }

    public function get_coupon($w,$pageSize,$pageNo,$platform){

        $req = new TbkDgItemCouponGetRequest();
        $req->setAdzoneId($this->adzoneId);
        $req->setPlatform($platform+"");
        $req->setPageSize($pageSize+"");
        $req->setQ($w);
        $req->setPageNo($pageNo+"");
        $resp = $this->tao_client->execute($req);
        
        if(isset($resp->results)){
            if($resp->total_results > 0){
                $items = $resp->results->tbk_coupon;
                for($y = 0; $y < count($items);$y ++){
                    $item = $resp->results->tbk_coupon[$y];
                    $num_iid = $item->num_iid;
                    log_message(ERROR,"num_iid:" + $num_iid);
                }
            }
            return json_encode($resp);
        }
        $this->CI->error->set_error(Err_Code::ERR_NO_SELECT_DATA);
        return false;
    }

}
