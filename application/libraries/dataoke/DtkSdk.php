<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
class DtkSdk{
    private $appkey = "a9d3804ef7";
    public static $baseUrl = "http://api.dataoke.com/index.php";
    public static $www = "goodsLink/www";
    public static $qq = "goodsLink/qq";
    public static $top = "Port/index";
/**
 * 	名称	类型	必填	说明
 * 网站数据
 * 
 * 	type	string	是	www_quan (网站专用、仅加入推广的商品)
 *	appkey	String	是	大淘客appkey
 *	v	int	否	2: utf-8编码的json格式数据
 *  1(默认值): 返回gbk编码json格式数据（此参数主要为了兼容之前的数据,后续将逐步取消,建议使用2）
 *	page	int	否	分页获取数据（每页200条）
 *qq数据
 *	type	string	是	qq_quan 
 *  appkey	String	是	appkey
 *  v	int	否	2: utf-8编码的json格式数据
 *  1(默认值): 返回gbk编码json格式数据（此参数主要为了兼容之前的数据,后续将逐步取消,建议使用2）
 *  page	int	否	分页获取数据（每页200条）
 * 全站领券
 * 	type	string	是	total
 *	appkey	String	是	大淘客appkey
 *	v	int	否	2: utf-8编码的json格式数据
 *  1(默认值): 返回gbk编码json格式数据（此参数主要为了兼容之前的数据,后续将逐步取消,建议使用2）
 *	page	int	否	分页获取数据（每页200条）
 * top100
 * 	type	string	是	top100
 *	appkey	String	是	大淘客appkey
 *	v	int	否	2: utf-8编码的json格式数据
 *  1(默认值): 返回gbk编码json格式数据（此参数主要为了兼容之前的数据,后续将逐步取消,建议使用2）
 * 
 * 实时跑量
 * 	type	string	是	paoliang
 *	appkey	String	是	大淘客appkey
 *	v	int	否	2: utf-8编码的json格式数据
 *   1(默认值): 返回gbk编码json格式数据（此参数主要为了兼容之前的数据,后续将逐步取消,建议使用2）
 * 
 * 单品详情页
 *  	id	int	是	淘宝商品id或者是大淘客商品id
 *	appkey	String	是	大淘客appkey
 *	v	int	否	2: utf-8编码的json格式数据
 *  1(默认值): 返回gbk编码json格式数据（此参数主要为了兼容之前的数据,后续将逐步取消,建议使用2）
 */
    function get_data($r,$type,$page = null){
        $fields = array(){
            "r"=>$r,
            "v"=>2,
            "type" => $type,
            "appkey"=>$this->appkey
        };
        if($page != null){
            $fields["page"] = $page;
        }
        $data = $this->http_get_data($baseUrl,$fields);
        return $data;
    }

    function http_get_data($url, $fields=array()){
        if (is_array($fields)) {
            $qry_str = http_build_query($fields);
        } else {
            $qry_str = $fields;
        }
        if (trim($qry_str) != '') {
            $url = $url . '?' . $qry_str;
        }
        log_message('error', $url);
        $curl = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($curl, CURLOPT_URL, $url);
    //        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl,  CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        // 3. 执行并获取HTML文档内容
        
        $data = curl_exec($curl);
        log_message('error', $data);
        if (curl_errno($curl)) {
            log_message('error', curl_error($curl));//捕抓异常
        }
        curl_close($curl);  
    
        return $data; // 返回数据
    }
    
}