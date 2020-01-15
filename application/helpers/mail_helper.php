<?php 

function sendMail($to,$subject,$message,$cc='',$from=FROMMAIL)
{
	//echo "hdfgsdhdgjh";die;
	$CI =& get_instance();
	$CI->load->library('email');
	$CI->email->initialize(array(
		 'protocol' => 'smtp',
		 'smtp_host' => 'smtp.sendgrid.net',
		 'smtp_user' => 'vimlesh@',
		 'smtp_pass' => 'vimlesh@123',
		 'smtp_port' => 587,
		 'crlf' => "\r\n",
		 'newline' => "\r\n"
	   ));
			   
	$CI->email->from($from,COMPANYNAME);
	$CI->email->to($to);
	if($cc)
		$CI->email->cc($cc);
	$CI->email->set_mailtype("html");
	$CI->email->subject($subject);
	$CI->email->message($message);
    if($CI->email->send()) 
		return true;
	else
		return false;
	//echo $CI->email->print_debugger();
}

function mailAttch($to,$subject,$message,$filepath,$cc='')
{
	$CI =& get_instance();
	$CI->load->library('email');
	
	$CI->email->initialize(array(
		 'protocol' => 'smtp',
		 'smtp_host' => 'smtp.sendgrid.net',
		 'smtp_user' => 'vimlesh@',
		 'smtp_pass' => 'vimlesh@123',
		 'smtp_port' => 587,
		 'crlf' => "\r\n",
		 'newline' => "\r\n"
	   ));
	$CI->email->from(FROMMAIL,COMPANYNAME);
	$CI->email->to($to);
	if($cc)
	$CI->email->cc($cc);
	$CI->email->set_mailtype("html");
	$CI->email->subject($subject);
	$CI->email->attach($filepath);
	$CI->email->message($message);
	//$CI->email->send(); 
	if($CI->email->send()) 
		return true;
	else
		return false;
	//echo $CI->email->print_debugger();
}
function htmlMail($array='')
{
	$CI =& get_instance();

	$CI->load->library('email');
    $config = array (
              'mailtype' => 'html',
              'charset'  => 'utf-8',
              'priority' => '1'
               );

    $CI->email->initialize($config);
    $CI->email->from($array['frommail'], COMPANYNAME);
    $CI->email->to($array['to']);
    $CI->email->subject($array['subject']);
    //'admin/email/email_view'
    $message=$CI->load->view($array['email_template_url'],$array,TRUE);
    $CI->email->message($message);
    $CI->email->send();  
    echo $CI->email->print_debugger();

}
//$data=array('frommail'=>'vimlesh.yadav654@gmail.com','to'=>'vimlesh.yadav654@gmail.com','subject'=>'test','message'=>'hello','name'=>'vimlesh','email_template_url'=>'emailer/emailer_normal');
