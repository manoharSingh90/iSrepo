<?php if(!defined('BASEPATH')) exit('No direct access allowed');
class MY_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Calcutta');
	}
		
}

class Admin_Controller extends MY_Controller
{

	public function __construct()
	{
	    parent::__construct();

	    if($this->session->userdata('logged_in')!=TRUE || $this->session->userdata('LOGIN_BY') !='admin')
			 redirect('admin');
	}
}
class Vendor_Controller extends MY_Controller
{

	public function __construct()
	{
	    parent::__construct();

	    if($this->session->userdata('logged_in')!=TRUE || $this->session->userdata('LOGIN_BY') !='vendor')
			 redirect('vendor');
	}
}




?>