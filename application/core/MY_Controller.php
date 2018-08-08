<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    protected $db_data = array();
    protected $ret=array();
    protected $now_time = '';
    protected $interface_start_time=0;
    function __construct() {
        parent::__construct();
        $this->load->driver('cache',array(
            "adapter" => 'memcached',
            "backup" => 'file'
        ));
        // $this->cache->memcached->is_supported();
        $this->ret['code'] = Err_Code::ERR_OK;
        $this->ret['message'] = '';
        $this->ret['servertime'] = time();
        $this->now_time = time();
        $this->interface_start_time = microtime(TRUE);
    }

    function __destruct() {
        $exec_time = microtime(TRUE) - $this->interface_start_time;
        $ret = array();
        if(isset($this->ret['code']) && $this->ret['code'] != Err_Code::ERR_OK){
            $this->set_error($this->ret['code']);
            $ret = array(
                'code' => $this->ret['code'],
                'message' => $this->error->error_msg(),
                'data' => array(
                    'servertime' => time(),
                    'exec_time' => $exec_time,
                )
            );
        } else {
            $return_code = Err_Code::ERR_OK;
            $this->set_error($return_code);
            $return_message =  $this->error->error_msg();
            unset($this->ret['code'],$this->ret['message']);
            $this->ret['servertime'] = time();
            $this->ret['exec_time'] = $exec_time;
            $ret = array(
                'code' => $return_code,
                'message' => $return_message,
                'data' => $this->ret
            );
        }
        $url = uri_string();
        $str = date("Y-m-d H:i:s")." $url exec_time:$exec_time client_ip:".$this->get_ipaddress()." ".uniqid();
        $env = get_env_config("project_env");

        
        $output = $this->json_xencode($ret);

        if($env == 'production') {
            ob_clean();
            header('Content-type: application/json; charset=utf-8');
            header('Content-type: text/html; charset=utf-8');
            header('Content-length: '.strlen($output));
        }
        print $output;
    }

    public function json_xencode($value, $options = 0, $unescapee_unicode = true) {
        $v = json_encode($value, $options);
        if ($unescapee_unicode) {
            $v = $this->unicode_encode($v);
            $v = preg_replace('/\\\\\//', '/', $v);
        }
        return $v;
    }

    public function unicode_encode($str) {
        return preg_replace_callback("/\\\\u([0-9a-zA-Z]{4})/", array($this,"encode_callback"), $str);
    }

    public function encode_callback($matches) {
        return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UTF-16");
    }
    
    public function get_post($key){
        if($key == '') {
            return false;
        }
        $p = $this->input->get_post($key,true);
        return trim($p);
    }

    public function get_cookie($key){
        return $this->input->cookie($key,true);
    }
    
    public function set_cookie($cookie){
        return $this->input->set_cookie($cookie);
    }
    
    function get_json_return($data=array()){
        $code = $this->error->get_error();
        if($code == null) {
            $this->error->set_error(Err_Code::ERR_OK);
        }
        if(count($data) > 0){
            return json_encode(array('code' => $this->error->get_error(), 'msg' => $data));
        }
        return json_encode(array('code' => $this->error->get_error(), 'msg' => $this->error->error_msg()));
    }
    
    function set_error($code){
        $this->ret['code'] = $code;
        $this->error->set_error($code);
    }
    
    function get_code(){
        return $this->error->get_error();
    }
    
    function set_session($arr){
        return $this->session->set_userdata($arr);
    }
    
    function get_session($item){
        return $this->session->userdata($item);
    }
    
    function get_all_session(){
        return $this->session->all_userdata();
    }
    
    function destroy_session() {
        return $this->session->sess_destroy();
    }
    
    /*
     * $arr_item 为key=>value
     * 
     * **/
    function del_session($arr_item){
        return $this->session->unset_userdata($arr_item);
    }
    
    function get_ipaddress(){
        return get_ip();
    }
    
    

    function set_cache($key, $data, $expire){
        return $this->cache->save($key, $data, $expire);
    }
    
    function get_cache($key){
        return $this->cache->get($key);
    }
    
	function del_cache($key){
        return $this->cache->delete($key);
    }
    function clean_cache(){
        return $this->cache->clean();
    }
    

}
