<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * php AES加解密类
 * 如果要与java共用，则密钥长度应该为16位长度
 * 因为java只支持128位加密，所以php也用128位加密，可以与java互转。
 * 同时AES的标准也是128位。只是RIJNDAEL算法可以支持128，192和256位加密。
 * java 要使用AES/CBC/NoPadding标准来加解密
 *
 */
class Aes {

    private $encrypt_key = 'testtesttesttest';

    private $encrypt_vikey = "testtesttesttest";

    public function init($key, $vi) {
        if($key != '' && $vi != '') {
            $this->encrypt_key = $key;
            $this->encrypt_vikey = $vi;
        }
    }
    
    /**
     * This was AES-128 / CBC / NoPadding encrypted.
     * return 16进制数 string
     * @param string $plaintext
     */
    public function AesEncrypt($plaintext) {
        $plaintext = trim($plaintext);
        if ($plaintext == '')
            return '';
        if (!extension_loaded('mcrypt')){
            return '';
        }
        
        /* 打开算法和模式对应的模块 */
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        /* 为加密重新初始化缓冲区 */
        mcrypt_generic_init($module, $this->encrypt_key, $this->encrypt_vikey);

        /* 加密数据 */
        $encrypted = mcrypt_generic($module, $plaintext);
        
       /* 执行清理工作 */
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return strtoupper(bin2hex($encrypted));
    }

    /**
     * This was AES-128 / CBC / NoPadding decrypted.
     * @author Terry
     * @param string $encrypted encrypted string
     * @return string
     */
    public function AesDecrypt($encrypted) {
        if ($encrypted == '')
            return '';
        if (!extension_loaded('mcrypt')){
            return '';
        }
        
        /* 打开算法和模式对应的模块 */
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
       
        /* 为解密重新初始化缓冲区 */
        mcrypt_generic_init($module, $this->encrypt_key, $this->encrypt_vikey);
        /* 解密数据 */
        $decrypted = mdecrypt_generic($module, $this->hexToStr($encrypted));
        
        /* 执行清理工作 */
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return rtrim($decrypted, "\0");
    }

    //16进制的转为2进制字符串    
    public function hexToStr($hex) {
        $bin = "";
        for ($i = 0; $i < strlen($hex) - 1; $i+=2) {
            $bin.=chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $bin;
    }
    
    /**
     * Returns the length of the given string.
     * If available uses the multibyte string function mb_strlen.
     * @param string $string the string being measured for length
     * @return integer the length of the string
     */
    private function strlen($string) {
        return extension_loaded('mbstring') ? mb_strlen($string, '8bit') : strlen($string);
    }

}
