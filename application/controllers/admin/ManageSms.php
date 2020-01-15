<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ManageSms extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('api_model','common_model','review_model')); 
		$this->load->helper(array('mail_helper','notification_helper'));
		$this->load->helper('upload_helper');
		date_default_timezone_set('Asia/Calcutta');
		$this->load->library('phpqrcode/qrlib');
	} 

	public function index(){
		file_put_contents('/var/www/html/isamplez/log.txt',"Process start at ".date('Y-m-d H:i:s').' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
		$_REQUEST['mno']='+919956021647';$_REQUEST['txt']='SAMPLE';$_REQUEST['dtm']='12/05/2019';$_REQUEST['imei']='34534553535';$_REQUEST['smsc']='565464646';
		
		$mob=explode('+',@$_REQUEST['mno']);
		$umobile=@$mob[1];$txt=@$_REQUEST['txt'];$dtm=@$_REQUEST['dtm'];$imei=@$_REQUEST['imei'];$smsc=@$_REQUEST['smsc'];
		$tstlngth=str_word_count($txt);
		if($tstlngth==2)
		{
			$txt=explode(' ',trim($txt));
			$txt=$txt[1];
		}
		$cdetl=$this->db->select('id')->from(CAMPAIGNS)->where("campaign_code LIKE '$txt'")->get()->row();
		
		
		echo '<pre>';print_r($cdetl);die;
		//$cdetl = $this->api_model->getRowData(CAMPAIGNS,array('id'),array('campaign_code'=>'SAMPLE'));
		$chek_user = $this->api_model->getRowData(USERS,array('id','sms_sample_wrong_count','is_sms_sample_use'),array('phone'=>'+'.$umobile));
		file_put_contents('/var/www/html/isamplez/log.txt',"user checked ".date('Y-m-d H:i:s').' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
		if(!empty($cdetl))
		{
			if(empty($chek_user))
			{
				$pass=rand(000000,99999999);
				$userData = array('imei'=>$imei,'is_sms_sample_use'=>1,'sms_sample_wrong_count'=>0,'phone'=>'+'.$umobile,'password'=> md5($pass),'image'=> '','gender'=>0,'is_active'=> "2",'registration_status'	=> "3",'created_dttm'=> date('Y-m-d H:i:s'),'modified_dttm'=> date('Y-m-d H:i:s'));
				$user_id=$this->api_model->insert(USERS,$userData);
				
				file_put_contents('/var/www/html/isamplez/log.txt',"user entry ".date('Y-m-d H:i:s').' '.@$verify_token."\n",FILE_APPEND | LOCK_EX); 
				
				$qrimg=$this->request_sample($user_id,1,6);
				file_put_contents('/var/www/html/isamplez/log.txt',"get qr code ".date('Y-m-d H:i:s').' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
				$link=base_url()."ChatBot/downloadQrcode/".base64_encode(convert_uuencode($user_id)).'/'.base64_encode(convert_uuencode($cdetl->id));
				$userData = array('smsqr_code_url'=>$qrimg['qr_code_url'],'sms_sample_wrong_count'=>0,'is_sms_sample_use'=>1,'modified_dttm'=> date('Y-m-d H:i:s'));
				$this->api_model->update(USERS,$userData,array('id'=>$user_id)); 
				file_put_contents('/var/www/html/isamplez/log.txt',"Update User ".date('Y-m-d H:i:s').' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
				$sms='Click on the link below to access QR code that unlocks the machine. '.$link ;
				$smsdtl=$this->manageSMS($umobile,$sms); 
				file_put_contents('/var/www/html/isamplez/log.txt',"msg sent ".date('Y-m-d H:i:s').' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
				
			}
			else
			{
				if($chek_user->is_sms_sample_use==0)
				{
					$qrimg=$this->request_sample($chek_user->id,1,6);
					$link=base_url()."ChatBot/downloadQrcode/".base64_encode(convert_uuencode($chek_user->id)).'/'.base64_encode(convert_uuencode($cdetl->id));
					$userData = array('imei'=>$imei,'smsqr_code_url'=>$qrimg['qr_code_url'],'sms_sample_wrong_count'=>0,'is_sms_sample_use'=>1,'modified_dttm'=> date('Y-m-d H:i:s'));
					$this->api_model->update(USERS,$userData,array('phone'=>'+'.$umobile)); 
					$sms='Click on the link below to access QR code that unlocks the machine. '.$link ;
					$smsdtl=$this->manageSMS($umobile,$sms); 
					
				}
				else if($chek_user->sms_sample_wrong_count<2 && $chek_user->is_sms_sample_use!=2)
				{
					$link=base_url()."ChatBot/downloadQrcode/".base64_encode(convert_uuencode($chek_user->id)).'/'.base64_encode(convert_uuencode($cdetl->id));
					$appDownload='https://isamplez.com/';
					$sms='You have already used the code once. For other sample campaigns and promo codes download iSamplez app. '.$appDownload ;
					$smsdtl=$this->manageSMS($umobile,$sms); 
					$wrng=$chek_user->sms_sample_wrong_count+1;
					$userData = array('sms_sample_wrong_count'=>$wrng,'modified_dttm'=> date('Y-m-d H:i:s'));
					$this->api_model->update(USERS,$userData,array('phone'=>'+'.$umobile));
				}
			}
			
		}
		else
		{
			if(empty($chek_user))
			{
				$pass=rand(000000,99999999);
				$userData = array('imei'=>$imei,'sms_sample_wrong_count'=>1,'phone'=>'+'.$umobile,'password'=> md5($pass),'image'=> '','is_active'=> "2",'registration_status'	=> "3",'created_dttm'=> date('Y-m-d H:i:s'),'modified_dttm'=> date('Y-m-d H:i:s'));
				$user_id=$this->api_model->insert(USERS,$userData);
				$sms='Please provide correct code.';
				$this->manageSMS($umobile,$sms); 
			}
			else
			{
				if($chek_user->sms_sample_wrong_count<2)
				{
					$sms='Please provide correct code.';
					$this->manageSMS($umobile,$sms); 
					$wrng=$chek_user->sms_sample_wrong_count+1;
					$userData = array('imei'=>$imei,'sms_sample_wrong_count'=>$wrng,'modified_dttm'=> date('Y-m-d H:i:s'));
					$this->api_model->update(USERS,$userData,array('phone'=>'+'.$umobile));
				}
			}
		}
		echo '<pre>';print_r('Success');die;

	}
	function manageSMS($mobile=null,$msg=null)
	{
		$username='zettalyte';$passwd='N7sjwsh5';$msg=urlencode($msg);
		$callerid='';$trackid='iSamplez'.rand(00,99).time();
		$sms="http://www.sendquickasp.com/client_api/index.php?username=$username&passwd=$passwd&tar_num=$mobile&tar_msg=$msg&callerid=iSamplez&route_to=api_send_sms&merchantid=AppSMS1&trackid=$trackid&status_url=https://isamplez.com/ChatBot/getstatusBack";
		$detl=file_get_contents($sms);
		return $detl;
	}
	function managestatus()
	{
		 

		
	}
	function downloadQrcode($uid=null)
	{
		$user_id=convert_uudecode(base64_decode($uid));
		$chek_user = $this->api_model->getRowData(USERS,array('smsqr_code_url'),array('id'=>$user_id));
		$url = @$chek_user->smsqr_code_url;
		if ($url!='') {
			$fl=explode('qrimage/',$url);
            $file_name = $fl[1];
			$file_url = $url ;
			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary"); 
			header("Content-disposition: attachment; filename=\"".$file_name."\""); 
			readfile($file_url);
			flush();
			die;
        }
		else
		{
			echo 'Somthing went wrong. Please try again.';die;
		}
	}
	/****************************SMS Process END*********************************/
	function getstatusBack()
	{
		echo 'Welcome';die;
	}
	function saveUser($email=null,$senderId=null)
	{ 
		$con=array('fb_id'=>$senderId);
		$result = $this->api_model->checkUserEmail(USERS,$con);
		if($result==false)
		{
			$con1=array('email'=>$email);
		    $result1 = $this->api_model->checkUserEmail(USERS,$con1);
			if($result1==false)
			{
				$pass=rand(000000,99999999);
				$userData = array('fb_id'=>$senderId,'email'=> $email,'password'=> md5($pass),'image'=> '','is_active'=> "1",'registration_status'	=> "2",'created_dttm'=> date('Y-m-d H:i:s'),'modified_dttm'=> date('Y-m-d H:i:s'),);
				$user_id=$this->api_model->insert(USERS,$userData);
				$subject="iSamplez Login Credentails";
				$data['email']    	=  $email;
				$data['pass'] 		=  $pass;
				$message=$this->load->view('emailer/chatbot_registration',$data,true);
				$mailResponse    	= sendMail($email,$subject,$message);
			}
			else
			{
				$update=$this->api_model->update(USERS,array('fb_id'=>$senderId),$con1);
				$user_id=$this->api_model->getField(USERS,'id',$con1);
			}
		}
		else
		{
			//$update=$this->api_model->update(USERS,array('fb_id'=>$senderId),$con);
			$user_id=$this->api_model->getField(USERS,'id',$con);
		} 
		$qrimg=$this->request_sample($user_id,1,6);
		$res['user_id']=$user_id;
		$res['qrcode_url']=$qrimg['qr_code_url'];
		$res['unlocked_date']=$qrimg['unlocked_date'];
		return $res;
	}
	function request_sample($user_id=null,$campaign_id=null,$campaign_sample_id=null)
	{ 
		$rowcount = $this->api_model->mysqlNumRows(USER_SAMPLES,'id',array('campaign_id'=>$campaign_id,'campaign_sample_id'=>$campaign_sample_id,'user_id'=>$user_id,'status !='=>'2')); 
		if($rowcount<1){
			$campaign_name = $this->api_model->getField(CAMPAIGNS,'campaign_name',array('id'=>$campaign_id));
			
			$qr_code=rand(0000,9999).$user_id.$campaign_name;
			$qr_code= preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $qr_code));
			$filename=$this->qrcodeGenerator($qr_code);
			$qr_code_url=$filename?base_url().'assets/img/qrimage/'.$filename:'';
			$sampleData = array(
				'campaign_sample_id'	=> $campaign_sample_id,
				'campaign_id'			=> $campaign_id, 
				'user_id' 				=> $user_id,
				'qr_code'				=> $qr_code,
				'qr_code_url' 			=> $qr_code_url,
				'unlocked_date'  		=> date('Y-m-d H:i:s'),
				'status'				=> '1',
				'is_active'				=> '1',
				'created_dttm' 			=> date('Y-m-d H:i:s'),
			);
			$UserSample_id = $this->api_model->insert(USER_SAMPLES,$sampleData);
			$qr_code_urls['qr_code_url']=$qr_code_url;
			$qr_code_urls['unlocked_date']=date('Y-m-d H:i:s');
			return $qr_code_urls;
		}
		else
		{
			$qr_code_url=$this->api_model->getRowData(USER_SAMPLES,array('qr_code_url','unlocked_date'),array('campaign_id'=>$campaign_id,'campaign_sample_id'=>$campaign_sample_id,'user_id'=>$user_id,'status !='=>'2'));
			$qr_code_urls['qr_code_url']=$qr_code_url->qr_code_url;
			$qr_code_urls['unlocked_date']=$qr_code_url->unlocked_date;
			return $qr_code_urls;
		}
	}
	public function qrcodeGenerator($qrtext){   
		if(isset($qrtext))
		{
			$folder = 'assets/img/qrimage/';
			$file_name1 = $qrtext.date('Y-m-d:H:i:s').".png";
			$file_name = $folder.$file_name1;
			QRcode::png($qrtext,$file_name,'H',8,8);
			return  $file_name1;
		}
	}
}


?>