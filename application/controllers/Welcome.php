<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	public function __construct()
		{
			parent::__construct();
			$this->load->library('sms');
		}

	public function index()
	{
		var_dump($this->sms->getAccessTokens());
		//echo $this->sms->sendSms(['phone'=>254713154085,'msg'=>'test','offercode'=>003,'linkid'=>'100399483484848343']);

	}
}
