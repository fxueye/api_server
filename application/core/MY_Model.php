<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    protected $CI;
    protected $now_time;
    static private $DB_INSTANCE = array();
            
    function __construct($db_key = false) {
        parent::__construct();
        $this->CI = & get_instance();
        $this->CI->load->driver('cache',array(
            "adapter" => 'memcached',
            "backup" => 'file'
        ));
        // $this->CI->cache->memcached->is_supported();
        $this->now_time = time();
        $this->ip = get_ip();
        if($db_key){
            $this->db = $this->get_db_instance($db_key);
        }
    }
    
    /**
     * 获取数据库对象
     */
    protected function get_db_instance($database) {
        if (empty(self::$DB_INSTANCE[$database])) {
            self::$DB_INSTANCE[$database] = $this->CI->load->database($database, true);
        }
        return self::$DB_INSTANCE[$database];
    }
    
    /**
     * 开始事务
     */
    function start() {
        $this->db->trans_start();
    }

    /**
     * 事务回滚并返回报错
     */
    function error() {
        $this->db->trans_rollback();
        return false;
    }

    /**
     * 事务提交并返回成功
     */
    function success() {
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->log_error(__method__);
            return false;
        }
        return true;
    }

    /* 获取app的user_id */
    function get_userapp_id(){
        $query = $this->db->query("SELECT UUID_SHORT() as id;");
        $ret = $query->result_array();
        $arr = array_pop($ret);
        $app_id = substr($arr['id'], -15);
        return $app_id;
    }

    function set_error($code){
        return $this->CI->error->set_error($code);
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
    
    function get_ipaddress(){
        return get_ip();
    }
}
