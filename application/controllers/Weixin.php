<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Weixin extends CI_Controller {
    private $wechat;
    private $message = "%s\n【原价】: %s元\n【内部优惠券】: %s元\n【券后价】: %s元\n【淘口令下单】: 复制这条信息，打开→手机淘宝领取优惠券%s";
	public function __construct() {
		parent::__construct ();
        $this->wechat = new CI_Wechat();
        $this->load->model('api/api_model');
	}
	public function index() {
		// $this->output->enable_profiler(TRUE);
		// $this->wechat->valid ();
        // $b = true;
        // if($b){
        //     return;
        // }
        $type = $this->wechat->getRev()->getRevType ();
		log_message ( 'info', 'type:' . $type );
        log_message ( 'info', 'rev:' . json_encode ( $this->wechat->getRevData () ) );
        $msg = $this->wechat->getRevData();
        log_message(INFO,"msg:text:" . $msg['Content']);
        
		switch ($type) {
			case Wechat::MSGTYPE_TEXT :
                $this->msgHandler($msg);
				exit ();
				break;
			case Wechat::MSGTYPE_EVENT :
			 	//个人帐号无此功能
				$event = $this->wechat->getRevEvent ();
				log_message ( 'info', 'event:' . json_encode ( $event ) );
				$this->event ( $event ['event'] );
				exit ();
				break;
			case Wechat::MSGTYPE_IMAGE :
				break;
			default :
				$this->wechat->text ( "help info" )->reply ();
		}
    }
    private function msgHandler($msg){
        $code = $msg['Content'];

        switch($code){
            case "1":
                $coupon = $this->randomCoupon();
                $title = $coupon['title'];
                $couponInfo = $coupon['coupon_info'];
                $zk_final_price = $coupon['zk_final_price'];
                $commission_rate = $coupon['commission_rate'];
                $coupon_click_url= $coupon['coupon_click_url'];
                preg_match_all('/\d+/',$couponInfo,$arr);
                $tpwd = $coupon['tpwd'];
                $original_price = ((float)$zk_final_price + (float)$arr[1])."";
                $retMsg = sprintf($this->message,$title,$original_price,$arr[1],$zk_final_price,$tpwd);
                log_message(INFO,"retMsg:" . $retMsg);
                $this->wechat->text ( $retMsg )->reply ();
            break;
            case "2":

            break;
            default:
                $this->wechat->text ( "感谢您的关注,我们会给您更好的服务,http://shop.php9.cn 随便逛逛吧！!!更多功能完善中！" )->reply ();
        }
    }
	private function event($event) {
		switch ($event) {
			case Wechat::EVENT_SUBSCRIBE :
				$this->wechat->text ( "感谢您的关注,我们会给您更好的服务\n回复:1 随机获取一个优惠券\n 2 进入搜索模式\n http://shop.php9.cn 随便逛逛吧！" )->reply ();
				exit ();
				break;
		}
    }
    private function randomCoupon(){
        $words = array(
            "女装",
            "男装",
            "童装"
        );
        $word = $words[mt_rand(0,count($words) - 1)];
        $pangeNo = mt_rand(1,20);
        $list =  $this->api_model->get_coupon($word,20,$pageNo);
        return $list[mt_rand(0,count($list) - 1)];
    }
	private function event_key($key) {
		switch ($key) {
			case "TANGTANG_01" :
				$text = "";
				log_message ( 'info', 'text:' . $text );
				$this->wechat->text ( $text )->reply ();
				exit ();
				break;
			case "TANGTANG_01" :
				$this->wechat->text ( "hello, I'm wechat" )->reply ();
				exit ();
				break;
			case "TANGTANG_02" :
				$this->wechat->text ( "hello, I'm wechat" )->reply ();
				exit ();
				break;
			case "TANGTANG_03" :
				$this->wechat->text ( "hello, I'm wechat" )->reply ();
				exit ();
				break;
			case "TANGTANG_04" :
				$this->wechat->text ( "hello, I'm wechat" )->reply ();
				exit ();
				break;
			case "TANGTANG_05" :
				
				$text = "";
				log_message ( 'info', 'text:' . $text );
				$this->wechat->text ( $text )->reply ();
				exit ();
				break;
			case "TANGTANG_06" :
				$this->wechat->text ( "hello, I'm wechat" )->reply ();
				exit ();
				break;
			default :
				log_message ( 'info', 'key is not:' . $key );
				break;
		}
	}
	public function menu() {
		$newmenu = array (
				"button" => array (
						array (
								'name' => '糖糖手册',
								"sub_button" => array (
										array (
												"type" => "click",
												"name" => "入门篇",
												"key" => "TANGTANG_01" 
										),
										array (
												"type" => "click",
												"name" => "进阶篇",
												"key" => "TANGTANG_02" 
										),
										array (
												"type" => "click",
												"name" => "高阶篇",
												"key" => "TANGTANG_03" 
										),
										array (
												"type" => "click",
												"name" => "创意篇",
												"key" => "TANGTANG_04" 
										),
										array (
												"type" => "click",
												"name" => "其他攻略",
												"key" => "TANGTANG_04" 
										) 
								) 
						),
						array (
								'name' => '活动公告',
								"sub_button" => array (
										array (
												"type" => "click",
												"name" => "微信福利",
												"key" => "TANGTANG_05" 
										),
										array (
												"type" => "view",
												"name" => "官网下载地址",
												"key" => "TANGTANG_06",
												"url" => "http://t.cn/RcGeJYM" 
										) 
								) 
						),
						array (
								'name' => '玩家互动',
								"sub_button" => array (
										array (
												"type" => "view",
												"name" => "玩家Q群",
												"key" => "TANGTANG_05",
												"url" => "http://t.cn/RcGeJYM" 
										),
										array (
												"type" => "view",
												"name" => "玩家贴吧",
												"key" => "TANGTANG_06",
												"url" => "http://t.cn/RcGeJYM" 
										),
										array (
												"type" => "view",
												"name" => "BUG建议",
												"key" => "TANGTANG_07",
												"url" => "http://t.cn/RcGeJYM" 
										),
										array (
												"type" => "view",
												"name" => "联系我们",
												"key" => "TANGTANG_08",
												"url" => "http://t.cn/RcGeJYM" 
										) 
								) 
						) 
				) 
		);
		$result = $this->wechat->createMenu ( $newmenu );
	}
	public function test() {
		for($i = 0; $i < 10; $i ++) {
			log_message ( 'error', '#####################test：' . $i );
		}
	}
}
