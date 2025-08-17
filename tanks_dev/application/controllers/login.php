<?php


class Login_Controller extends Template_Controller
{
	public $template = 'tpl_external';

	public function __construct() {
		parent::__construct();
	}

	public function index($message='') {  // login
		Session::instance()->destroy();

		$view = new View('login');

		$Sam = Sam::instance();
		$view->sep_login_url = $Sam->GetSEPLoginURL();
		$view->sep_register_url = $Sam->GetSEPRegistrationURL();
		$view->message = $message;

		$this->template->content = $view;
	}

	public function logout() {
		$this->index('You have logged out of Onestop.');
	}

	public function timeout() {
		$this->index('Your login session has timed out due to inactivity.');
	}
}
