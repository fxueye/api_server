<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Weixin extends CI_Controller {
	private $wechat;
	public function __construct() {
		parent::__construct ();
		$this->wechat = new CI_Wechat();
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
		switch ($type) {
			case Wechat::MSGTYPE_TEXT :
				$this->wechat->text ( "感谢您的关注,我们会给您更好的服务,http://shop.php9.cn 随便逛逛吧！!!更多功能完善中！" )->reply ();
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
	private function event($event) {
		switch ($event) {
			case Wechat::EVENT_SUBSCRIBE :
				$this->wechat->text ( "感谢您的关注,我们会给您更好的服务,http://shop.php9.cn 随便逛逛吧！" )->reply ();
				exit ();
				break;
		}
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
