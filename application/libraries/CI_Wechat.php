<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *  微信公众平台PHP-SDK, Codeigniter实例
 *  @author nigelvon@gmail.com
 *  @link https://github.com/dodgepudding/wechat-php-sdk
 *  usage:
 *  $this->load->library('CI_Wechat');
 *  $this->ci_wechat->valid();
 *  ...
 *
 */
require_once(dirname(__FILE__) . '/weixin/wechat.class.php');

class CI_Wechat extends Wechat {
    protected $_CI;
    public function __construct() {
        $this->_CI =& get_instance();
        $options = get_env_config('wechat');
        log_message ( 'error', 'type:' . json_encode($options));
        // $this->_CI->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        $this->_CI->load->driver('cache',array(
            "adapter" => 'memcached',
            "backup" => 'file'
        ));

        parent::__construct($options);
    }

    /**
     * 重载设置缓存
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename, $value, $expired) {
        return $this->_CI->cache->save($cachename, $value, $expired);
    }

    /**
     * 重载获取缓存
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename) {
        return $this->_CI->cache->get($cachename);
    }

    /**
     * 重载清除缓存
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename) {
        return $this->_CI->cache->delete($cachename);
    }
}

/* End of file CI_Wechat.php */
/* Location: ./application/libraries/CI_Wechat.php */
