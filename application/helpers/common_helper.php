<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_env_config($item_name){
    global $CI;
    if(!isset($CI)) {
        $CI =& get_instance();
    }
    $CI->config->load('passport', true); //passport文件在config目录下

    $item = $CI->config->item($item_name, 'passport');
    return $item;
}

function chk_mobile($mobile) {
    if (strlen ( $mobile ) != 11 || ! preg_match ( '/^1[2|3|4|5|8|6|7|8|9][0-9]\d{4,8}$/', $mobile )) {
        return false;
    }
    return true;
}

function get_ip() {     
    if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
        $ip_arr = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
        return $ip_arr[0];
    }
    if(!empty($_SERVER["REMOTE_ADDR"])){
        return $_SERVER["REMOTE_ADDR"];
    }
    return '0.0.0.0';
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

function http_post_data($url, $data=array()){
    if (is_array($data)) {
        $qry_str = http_build_query($data);
    } else {
        $qry_str = $data;
    }
    
    log_message('error', $url);
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
//        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $qry_str); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 设置超时限制防止死循环
    //curl_setopt($curl, CURLOPT_HEADER, array('Content-Type: application/json')); // 显示返回的Header区域内容
    
    $tmpInfo = curl_exec($curl); // 执行操作
    log_message('error', $tmpInfo);
    //$rescode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
    if (curl_errno($curl)) {
       log_message('error', curl_error($curl));
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据
}

function chk_mac($val){
    $val = strtoupper($val);
    if (!preg_match('/[A-F\d]{2}:[A-F\d]{2}:[A-F\d]{2}:[A-F\d]{2}:[A-F\d]{2}:[A-F\d]{2}/', $val)) {
        return false;
    }
    return true;
}

//手机缩写
function shorten_mobile($val) {
    if($val != ''){
        $val = substr($val, 0, 4) . '****' . substr($val, -3, 3);
        return $val;
    }
    return '';
}

function chk_num_letter($val){
    if (!preg_match('/^\w+$/i', $val)) { //数字字母下划线
        return false;
    }
    return true;
}

//获取一个随机数
function get_token($len = 16) {
    if(!is_numeric($len)) return null;
    $len = trim($len);
    if($len < 1) $len = 1;
    if($len > 256) $len = 256;
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    mt_srand((double)microtime()*1000000*getmypid());
    $rand_str = '';
    while(strlen($rand_str) < $len) {
        $rand_str .= substr($chars, (mt_rand()%strlen($chars)), 1);
    }
    return $rand_str;
}


function log_msg($ret, $params=null) {
    if($params == null) {
        $params = $_REQUEST;
    }
    $path = get_env_config("report_path");
    $name = str_replace("/","_",uri_string());
    $file = date('Ymd').'_'.$name.'.log';
    $str = date("Y-m-d H:i:s")." ".uri_string()." client_ip:".$_SERVER["REMOTE_ADDR"]." ".getmypid();
    write_file($path.$file, $str." in#".json_encode($params)." out#".$ret."\n", "a+");
}

