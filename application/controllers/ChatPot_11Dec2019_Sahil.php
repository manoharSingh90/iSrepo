<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
// Load the Rest Controller library
//require APPPATH . '/libraries/REST_Controller.php';

class ChatPot extends CI_Controller {

	public function __construct() { 
		parent::__construct();
        // Load the user model
		$this->load->model(array('api_model','common_model','review_model')); 
		$this->load->helper(array('mail_helper','notification_helper'));
		$this->load->helper('upload_helper');
		date_default_timezone_set('Asia/Calcutta');
		$this->load->library('phpqrcode/qrlib');
		
	}
	function products($cmn_id=null,$fb_id=null)
	{ 
		$campaign_id=convert_uudecode(base64_decode($cmn_id));
		$data['media']=$this->common_model->getResultData(CAMPAIGNBANNERS,'*',array('campaign_id'=>$campaign_id));
		$data['fb_id']=$fb_id;
		$data['brand_id']=1;
		$data['campaign_id']=$campaign_id;
		$data['cmn_id']=$cmn_id;
		$this->load->view('chatpot/product',@$data);  
	}
	function review_sample($fb_id=null,$cmn_id=null)
	{
		if(!empty($_POST))
		{
			$usrid='';
			if(isset($_POST['emai_id']) && $_POST['emai_id']!='')
			{
				$detls=$this->saveUser($_POST['emai_id'],$_POST['fb_id']);
				$usrid=$detls['user_id'];
			}
			else
			{
				$con=array('fb_id'=>$_POST['fb_id']);
				$filed=array('id');
				$result=(array)$this->common_model->getRowData(USERS,$filed,$con);
				$usrid=$result['id'];
			}
			$totalQsn=count($_POST['qsn_id']);
			if($totalQsn>0)
			{
				$is_campaign_review=1;
				$review_id=$this->api_model->getField(CAMPAIGNS,'review_id',array('id' =>$_POST['compain_id']));
				$userReviewData = array(
					'review_id'				=> $review_id, 
					'user_id' 				=> $usrid,
					'is_campaign_review'	=> $is_campaign_review, 
					'review_text' 			=> $_POST['comment'],
					'rating' 				=> $_POST['range'],
					'is_published'			=> '1',
					'is_active'				=> '1',
					'created_dttm'  		=> date('Y-m-d H:i:s'),
					'modified_dttm' 		=> date('Y-m-d H:i:s'),
				);
				$user_review_id=$this->api_model->insert(USER_REVIEW,$userReviewData);
				//update avg rating
				$this->updateCampaignRating($_POST['compain_id']);
				for($i=0;$i<$totalQsn;$i++)
				{
					$qsn_id=$_POST['qsn_id'][$i];
					if(!empty($_POST['ansver_'.@$qsn_id]))
					{
						$totalans=count(@$_POST['ansver_'.$qsn_id]);
						for($j=0;$j<$totalans;$j++)
						{
							$answer_id=$_POST['ansver_'.$qsn_id][$j];
							$con=array('id'=>$answer_id);
							$filed=array('answer_text');
							$result=(array)$this->common_model->getRowData(REVIEW_ANSWER_OPTIONS,$filed,$con);
							$userReviewAnsData = array(
							'user_id' 				=> $usrid,
							'review_id'				=> $user_review_id, 
							'question_id'			=> $qsn_id, 
							'answer_id' 			=> $answer_id,
							'answer_text' 			=> $result['answer_text'],
							'is_active'				=> '1',
							'created_dttm'  		=> date('Y-m-d H:i:s'),
							'modified_dttm' 		=> date('Y-m-d H:i:s'),
							);
							$this->api_model->insert(USER_REVIEW_ANS,$userReviewAnsData);
						}
					}
				}
			}
			echo 'success';die;
			
		}
		$con=array('fb_id'=>$fb_id);
		$filed=array('email');
		$result=(array)$this->common_model->getRowData(USERS,$filed,$con);
		if($result)
		$data['is_email']=1;
		else
		$data['is_email']=0;	
		$data['fb_id']=$fb_id;
		$campaign_id=convert_uudecode(base64_decode($cmn_id));
		$data['campaign_id']=$campaign_id;
		$chkcmpn_use = $this->check_review_qruse($fb_id,$campaign_id,'web'); 
		$data['is_review']=$chkcmpn_use;
		$data['cmn_id']=$cmn_id;
		$reviewData=array();
		$cond="reviewQuest.is_active='1' AND camp.id='".$campaign_id."'";
        $ReviewQuestionData = $this->api_model->reviewQuestions('reviewQuest.*',$cond);
		
		foreach($ReviewQuestionData as $k=>$qsn)
		{
			$ReviewAnswerOptionData = $this->api_model->getResultData(REVIEW_ANSWER_OPTIONS,'*',array('question_id'=>$qsn->id,'is_active'=>'1'),'ans_order','ASC');
			$reviewData[$k]['qsn']=$qsn;
			$reviewData[$k]['ans']=$ReviewAnswerOptionData;
		}
		$data['qsnansr']=$reviewData;//echo '<pre>';print_r($data['qsnansr']);die;
		$this->load->view('chatpot/review_sample',@$data);  
	}
	public function updateCampaignRating($campaign_id){

		$review_id=$this->api_model->getField(CAMPAIGNS,'review_id',array('id' =>  $campaign_id));
		$con=array('review_id'=>$review_id);
		$rating=$this->api_model->getResultData(USER_REVIEW,'sum(rating) as rating,count(id) as users',$con);
		$newrating=$rating[0]->rating;
		$users=$rating[0]->users;
		$finalRating="0.00";	
		if($users>0)
			$avg_rating=number_format($newrating/$users,2);
		$this->api_model->update(CAMPAIGNS,array('avg_rating'=>$avg_rating),array('id'=>$campaign_id));
	}
	function brand_details($brand_id=null,$cmp_id=null)
	{
		$campaign_id=convert_uudecode(base64_decode($cmp_id));
		$conditions = "camp.id=$campaign_id AND camp.is_active=1 AND brand.is_active='1' AND camp.is_publish='1'";
		$campaignBanners=$this->api_model->getResultData(BRANDS,'*',array('id'=>$brand_id,'is_active'=>'1'));
		
		$brandAssetsData=$this->api_model->getResultData(BRANDASSETS,'asset_name,asset_type,asset_url',array('brand_id'=>$brand_id,'is_active'=>'1'));
		
		$data['media']=$brandAssetsData;
		$data['campDtl']=$campaignBanners;
		$this->load->view('chatpot/brand_details',@$data);  
	}
	function brand_review($brand_id=null,$cmp_id=null,$fb_id=null)
	{
		$campaign_id=convert_uudecode(base64_decode($cmp_id));
		$conditions = "camp.id=$campaign_id AND camp.is_active=1 AND brand.is_active='1' AND camp.is_publish='1'";
		$campaign=$this->api_model->campaignList($conditions,array());
		$campaignBanners=$this->api_model->getResultData(CAMPAIGNBANNERS,'id,banner_type,banner_url',array('campaign_id'=>$campaign_id,'is_active'=>'1'));
		$status = " ureview.is_active='1' AND camp.id='".$campaign_id."' AND ureview.is_campaign_review='1'";
        $reviewList = $this->review_model->getRows($status,array()); 
		$chkcmpn_use = $this->check_review_qruse($fb_id,$campaign_id,'web');
		$data['is_review']=$chkcmpn_use;
		$data['fb_id']=$fb_id;
		$data['campaign_id']=$campaign_id;
		$data['cmn_id']=$cmp_id;
		$data['campDtl']=$campaign;
		$data['reviewList']=$reviewList;
		$this->load->view('chatpot/brand_review',@$data);
	}
	function email_validation($str) { 
		return (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $str)) 
			? FALSE : TRUE; 
	}
	function vlocation($vendrmcnid=null)
	{
		$ip=$_SERVER['REMOTE_ADDR'];
		$detl=(unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip)));
		$lat=$detl['geoplugin_latitude'];$long=$detl['geoplugin_longitude'];
		$con=array('id'=>$vendrmcnid);
		$filed=array('*');
		$result=(array)$this->common_model->getRowData('vending_machines',$filed,$con);
		$url="http://maps.google.com/?saddr=$lat,$long&daddr=".$result['vend_lat'].",".$result['vend_long']."";
		redirect($url);
	}	
	function download_logo()
	{ 
		$url = 'https://media.geeksforgeeks.org/wp-content/uploads/geeksforgeeks-6-1.png';  header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($url).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($url));
        flush(); // Flush system output buffer
        readfile($url); die;
		redirect('https://isamplez.com');
	}
	function update_consent($type=null)
	{
		if(empty($_POST))
		{
			$_POST['user_id']=$type['user_id'];
			$_POST['sample_id']=$type['sample_id'];
		}
		$this->api_model->update(USER_SAMPLES,array('is_user_consent'=>1),array('id'=>$_POST['sample_id']));
		return true;
	}
	function downloadQrcode($uid=null,$cmpn_id=null,$sample_id=null)
	{$user_id=79;$sample_id=18;
		//$user_id=convert_uudecode(base64_decode($uid));
		//$sample_id=convert_uudecode(base64_decode($sample_id));// $sample_id=5;
		$chek_user = $this->api_model->getRowData(USERS,array('smsqr_code_url'),array('id'=>$user_id));
		$url = @$chek_user->smsqr_code_url;
		if ($url!='') {
			$campaign_date = $this->api_model->getRowData('campaign_samples',array('campaign_id','start_date','end_date'),array('id'=>$sample_id));
			$data['dates']=$campaign_date;
			$data['url']=$url;
			$brnd_id = $this->api_model->getField(CAMPAIGNS,'brand_id',array('id'=>$campaign_date->campaign_id));
			$filed=array('id','brand_name','brand_logo_url');
				$conditions['id'] =$brnd_id;
				$brnd=(array)$this->common_model->getRowData('brands',$filed,$conditions);
			$data['brnd']=$brnd;
			$data['user_id']=$user_id;
			$data['sample_id']=$sample_id;
			$this->load->view('chatpot/download_qr',@$data);  
			//redirect($url);
			/*$fl=explode('qrimage/',$url);
            $file_name = $fl[1];
			$file_url = $url ;
			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary"); 
			header("Content-disposition: attachment; filename=\"".$file_name."\""); 
			readfile($file_url);
			flush();
			die;*/
        }
		else
		{
			echo 'Somthing went wrong. Please try again.';die;
		}
	}
	function download_app()
	{
		redirect('https://isamplez.com');
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
	function check_review_qruse($senderId=null,$campaign_id=null,$type=null)
	{
		$con=array('fb_id'=>$senderId);
		$filed=array('id');
		$user=(array)$this->common_model->getRowData(USERS,$filed,$con);
	
		$is_qrcode_used=0;$is_review=0;
		$UserSample = $this->api_model->getRowData(USER_SAMPLES,'status',array('campaign_id'=>$campaign_id,'user_id'=> $user['id'],'status !='=>'2'));
		
		if(@$UserSample->status=='3' || @$UserSample->status=='4')
		{
			$is_qrcode_used=1;
		}
		if($is_qrcode_used==1)
		{
			$review_id=$CampaignBanner=$this->api_model->getField(CAMPAIGNS,'review_id',array('id'=>$campaign_id));
			$totalReview = $this->api_model->mysqlNumRows(USER_REVIEW,'id',array('review_id'=>$review_id,'user_id'=>$user['id'],'is_campaign_review'=>'1'));
			//if($totalReview>0 && date('Y-m-d',strtotime($value->end_date)) >= date('Y-m-d'))
			if($totalReview>0)
			$is_review=1;
		}
		if($type=='web')
		{
			$web['is_review']=$is_review;
			$web['is_qrcode_used']=$is_qrcode_used;
			return $web;
		}
		$return=array();
		if($is_qrcode_used==0)
		{
			$return=array("type"=>"postback","title"=>"Review Samples","payload"=>"reviewSample_useQrcode");
		}
		else
		{
			if($is_review==0)
			{
				$return=array("type"=>"web_url","title"=>"Review Samples","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/review_sample/$senderId/".base64_encode(convert_uuencode($campaign_id)));
			}
		}
		
		return $return;
	}
	public function isamples()
	{  
		 
		/*$challenge = $_REQUEST['hub_challenge'];
		$verify_token = $_REQUEST['hub_verify_token'];

		if ($verify_token === 'ascchatpotisampleasff') {
			file_put_contents('/var/www/html/catalyyze/webroot/log.txt',"Kinesis dev Process Start ".$challenge.' '.$verify_token."\n",FILE_APPEND | LOCK_EX);
		echo $challenge;die;
		}*/
		$device = 'desk';
		$brand_id=1;$campaign_id=1;
		if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
			$device = "ipad";
		} else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
			$device = "iphone";
		} else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
			$device = "blackberry";
		} else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
			$device = "android";
		}
		//file_put_contents('/var/www/html/isamplez/log.txt',"Kinesis dev Process Start ".json_encode($_SERVER).' '.$device."\n",FILE_APPEND | LOCK_EX);
		$input = json_decode(file_get_contents('php://input'), true);
		
		
		$senderId = @$input['entry'][0]['messaging'][0]['sender']['id'];
		$message = @$input['entry'][0]['messaging'][0]['message']['text'];
		$payload = $input['entry'][0]['messaging'][0];
		$email='';
		if(@$payload['postback']['payload']=='welcome')
		{
			$messageText='welcome';
		}
		else if($message!='')
		{
			$email=$message;
			$messageText='email';
		}
		else
		{
			$messageText=$payload['postback']['payload'];
		}
		$response = null;
		
		
		if($messageText!='')
		{
			
			$answer='';
			if($messageText == "welcome"){
				$filed=array('id','brand_name','brand_logo_url');
				$conditions['id'] =$brand_id;
				$brnd=(array)$this->common_model->getRowData('brands',$filed,$conditions);
				$answer1 = array('text'=>"Get the Free sample in 3 easy steps. First save the QR code."); 
				$this->response_send($senderId,$answer1);
				$answer2 = array('text'=>"Second locate vending machines with the samples  by clicking view Vending machine locations."); 
				$this->response_send($senderId,$answer2);
				$answer3 = array('text'=>"Third scan this QR code in the vending machine to unlock the sample. Only one sample will be dispensed per account."); 
				$this->response_send($senderId,$answer3);
				$path = base_url().'assets/brand/logo/'.$brnd['brand_logo_url'];
				$answer =array("attachment"=>array("type"=>"template",
						"payload"=>array(
					 
						"template_type"=>"generic",
						
						"elements"=>array(
						array(
						   //"composer_input_disabled"=> true,
							"title"=>$brnd['brand_name'],
							"item_url"=>"",
							"image_url"=>$path,
							"subtitle"=>"",
							"buttons"=>array(
							  array(
								"type"=>"postback",
								"title"=>"Request Sample",
								"payload"=>"requestSample"
							  ),
							   
							  /*array("type"=>"web_url",
								 "url"=>base_url()."ChatPot/products/".base64_encode(convert_uuencode($brand_id)).'/'.$senderId,
								"title"=>"Know more about product",
								"webview_height_ratio"=> "tall"
								),*/
							array(
								"type"=>"postback",
								"title"=>"Know more about product",
								"payload"=>"more_abt_product"
							  ), 	
							 array(
								"type"=>"postback",
								"title"=>"About Us",
								"payload"=>"abt_us"
							  ) 
							)
						  )
						)
					  )
					));
				
				$this->response_send($senderId,$answer);
			}
			else if($messageText == "requestSample") {
				
				$con=array('fb_id'=>$senderId);
				$filed=array('email');
				$conditions['id'] =$brand_id;
				$result=(array)$this->common_model->getRowData(USERS,$filed,$con);
				if($result==false)
				{
					$answer =array('text'=>'Please provide your email address');
					$this->response_send($senderId,$answer);
				}
				else
				{
					
					$rvw=$this->check_review_qruse($senderId,$campaign_id);
					if(!empty($rvw))
					{
						$btns=array(array("type"=>"postback","title"=>"View vending machines locations","payload"=>"viewVendingLocation"),array("type"=>"web_url",
								"title"=>"Download app for more samples",
								"url"=>"https://isamplez.com",
								"webview_height_ratio"=> "tall"),$rvw);
					}
					else
					{
						$btns=array(array("type"=>"postback","title"=>"View vending machines locations","payload"=>"viewVendingLocation"),array("type"=>"web_url",
								"title"=>"Download app for more samples",
								"url"=>"https://isamplez.com",
								"webview_height_ratio"=> "tall"));
					}
					$detls=$this->saveUser($result['email'],$senderId);
					$campaign_date = $this->api_model->getRowData(CAMPAIGNS,array('start_date','end_date'),array('id'=>$campaign_id));
					$start_date = date("d M y", strtotime($campaign_date->start_date));
					$end_date = date("d M y", strtotime($campaign_date->end_date));
					$answer2 =array('text'=>"Valid From ".$start_date.' - '.$end_date);
					$this->response_send($senderId,$answer2);
					$answer1 =array("attachment"=>array("type"=>"image","payload"=>array("is_reusable"=>true, "url"=>$detls['qrcode_url'])));
					$this->response_send($senderId,$answer1);
					$answer =array('attachment'=>array('type'=>'template','payload'=>array('template_type'=>'button','text'=>'By using this code I am giving consent to share my data with the owner of this brand in accordance with the privacy policy.','buttons'=>$btns)));
					$this->response_send($senderId,$answer);
				}
			}
			else if($messageText == "abt_us") {
				
			  $answer =array('attachment'=>array('type'=>'template','payload'=>array('template_type'=>'button','text'=>'Digitalizing New Product Launches. Discover New Products Get Free Samples!!!','buttons'=>array(array("type"=>"web_url", "url"=>"https://isamplez.com/privacy.html","title"=>"Privacy Policy","webview_height_ratio"=> "tall"),array("type"=>"web_url", "url"=>"https://isamplez.com/terms.html","title"=>"Terms of Service","webview_height_ratio"=> "tall"),array("type"=>"web_url",
								"title"=>"Download app for more samples",
								"url"=>"https://isamplez.com",
								"webview_height_ratio"=> "tall")))));
			  $this->response_send($senderId,$answer);
			}
			else if($messageText == "more_abt_product") {
				
				$media=$this->common_model->getResultData(CAMPAIGNBANNERS,'*',array('campaign_id'=>$campaign_id,'banner_type'=>1));
				$campaign_name = $this->api_model->getField(CAMPAIGNS,'campaign_name',array('id'=>$campaign_id));
				$knwmr=array();
				$rvw=$this->check_review_qruse($senderId,$campaign_id);
				if(!empty($rvw))
				{
					$btns=array(array("type"=>"web_url","title"=>"View Reviews","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/brand_review/$brand_id/".base64_encode(convert_uuencode($campaign_id))."/$senderId"),array("type"=>"web_url","title"=>"View Brand Details","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/brand_details/$brand_id/".base64_encode(convert_uuencode($campaign_id))),$rvw);
				}
				else
				{
					$btns=array(array("type"=>"web_url","title"=>"View Reviews","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/brand_review/$brand_id/".base64_encode(convert_uuencode($campaign_id))."/$senderId"),array("type"=>"web_url","title"=>"View Brand Details","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/brand_details/$brand_id/".base64_encode(convert_uuencode($campaign_id))));
				}
				foreach($media as $k=>$medi)
				{
					$path=base_url()."assets/campaign/banner/".$medi->banner_url;
					$knwmr[$k]['title']=$campaign_name;
					$knwmr[$k]['item_url']='';
					$knwmr[$k]['image_url']=$path;
					$knwmr[$k]['subtitle']='';
					$knwmr[$k]['buttons']=$btns;
				}
				$answer=array("attachment"=>array(
			  "type"=>"template",
			  "payload"=>array(
				"template_type"=>"generic",
				
				"elements"=>$knwmr)
				));
				$this->response_send($senderId,$answer);
			}
			else if($messageText == "email") {
				$emlvld=$this->email_validation($email);
				if($emlvld==true || $emlvld==1)
				{ 
					$rvw=$this->check_review_qruse($senderId,$campaign_id);
					if(!empty($rvw))
					{
						$btn=array(array("type"=>"postback","title"=>"View vending machines locations","payload"=>"viewVendingLocation"),array("type"=>"web_url",
								"title"=>"Download app for more samples",
								"url"=>"https://isamplez.com",
								"webview_height_ratio"=> "tall"),$rvw);
					}
					else
					{
						$btn=array(array("type"=>"postback","title"=>"View vending machines locations","payload"=>"viewVendingLocation"),array("type"=>"web_url",
								"title"=>"Download app for more samples",
								"url"=>"https://isamplez.com",
								"webview_height_ratio"=> "tall"));
					}
					$detls=$this->saveUser($email,$senderId);
					$campaign_date = $this->api_model->getRowData(CAMPAIGNS,array('start_date','end_date'),array('id'=>$campaign_id));
					$start_date = date("d M y", strtotime($campaign_date->start_date));
					$end_date = date("d M y", strtotime($campaign_date->end_date));
					$answer2 =array('text'=>"Valid From ".$start_date.' - '.$end_date);
					$this->response_send($senderId,$answer2);
					$answer1 =array("attachment"=>array("type"=>"image","payload"=>array("is_reusable"=>true, "url"=>$detls['qrcode_url'])));
					$this->response_send($senderId,$answer1);
					$answer =array('attachment'=>array('type'=>'template','payload'=>array('template_type'=>'button','text'=>'By using this code I am giving consent to share my data with the owner of this brand in accordance with the privacy policy.','buttons'=>$btn)));
					$this->response_send($senderId,$answer);
				}
				else
				{
					$answer =array('text'=>"I'am sorry, I did't understand you.");
					$this->response_send($senderId,$answer);
				}
			}
			else if($messageText == "email1") {
				$answer =array('text'=>'got your email');
				$detls=$this->saveUser($email,$senderId);
				
				$answer =array("attachment"=>array( "type"=>"template","payload"=>array("template_type"=>"generic","elements"=>array(array("title"=>"QR Code","item_url"=>"","image_url"=>$detls['qrcode_url'],"subtitle"=>"","buttons"=>array(array("type"=>"web_url","url"=>$detls['qrcode_url'],"title"=>"By using this code I am giving consent to share my data with the owner of this brand in accordance with the privacy policy."),array("type"=>"postback","title"=>"View vending machines locations","payload"=>"viewVendingLocation" ),array("type"=>"web_url","title"=>"Download app for more samples","url"=>"https://isamplez.com","webview_height_ratio"=> "tall")))))));
				$this->response_send($senderId,$answer);
			}
			else if($messageText == "viewVendingLocation") {
				$path='https://isamplez.com/isamplez/assets/img/map2.jpg';
			
				$conditions = "campVend.is_active='1' AND vm.is_active='1' AND camp.id='".$campaign_id."'";
				$vmlists = $this->api_model->vendMachineList($conditions,array(),'',0,0);
				$venlocation=array();
				
				foreach($vmlists as $k=>$vmlist)
				{
					
					$venlocation[$k]['title']=$vmlist->location_name;
					$venlocation[$k]['item_url']=base_url()."ChatPot/vlocation/".$vmlist->id;
					$venlocation[$k]['image_url']=$path;
					$venlocation[$k]['subtitle']=$vmlist->location_address;
					$venlocation[$k]['buttons']=array(
							  array(
								"type"=>"web_url",
								 "url"=>base_url()."ChatPot/vlocation/".$vmlist->id,
								"title"=>"Direction",
								"webview_height_ratio"=> "tall"
								),
								array(
								"type"=>"web_url",
								"title"=>"Download app for more samples",
								"url"=>"https://isamplez.com",
								"webview_height_ratio"=> "tall"
							  )
							  
							);
				}
				$answer =array("attachment"=>array("type"=>"template",
						"payload"=>array("template_type"=>"generic","elements"=>$venlocation)
					));
				$this->response_send($senderId,$answer);
			}
			 
			else if($messageText == "reviewSample_useQrcode") {
				
				$chkcmpn_use = $this->check_review_qruse($senderId,$campaign_id,'web');
				if($chkcmpn_use['is_qrcode_used']==1)
				{
					$campaign_name = $this->api_model->getField(CAMPAIGNS,'campaign_name',array('id'=>$campaign_id));
					$answer = array("attachment"=>array("type"=>"template","payload"=>array("template_type"=>"button","text"=>$campaign_name,"buttons"=>array(array("type"=>"web_url","title"=>"Click Here To Review Sample","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/review_sample/$senderId/".base64_encode(convert_uuencode($campaign_id)))))));
				}
				else
				{
					$answer =array('text'=>"Please use the QR code to get the sample and try. You will then be able to rate & review the sample.");
				}
				$this->response_send($senderId,$answer);
			}
			
			//send message to facebook bot
			
			//file_put_contents('/var/www/html/catalyyze-test/webroot/log.txt',"g ".$messageText."\n",FILE_APPEND | LOCK_EX);
		}
		exit;
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
	
	function response_send($senderId=null,$answer=null)
	{
		$accessToken =   "EAAHaI4OEvqsBAAXH0JlB3GyXHz2OKbZCJpCI7QFYTj8UBcoMKmmWYqMsGn5vZBu6jxGZB4VpedRp2OkFdMne0tL3wO8R5KDfFx9NQ27aTs8q7s3A4vcsi7NswvPLZAtLDt9zKK66ilBlTZCaRNnHlf9vFLh94s7Y5Hp9xQ0qp9ZA6fwvNbZAh8k";
		$response=['recipient'=>['id' =>$senderId],'message'=>$answer];
		$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		$result = curl_exec($ch);
		return true;
	}
	
	function sendmsg()
	{$senderId='2427201544022510';$campaign_id=1;$brand_id=1;
		$media=$this->common_model->getResultData(CAMPAIGNBANNERS,'*',array('campaign_id'=>$campaign_id,'banner_type'=>1));
				$campaign_name = $this->api_model->getField(CAMPAIGNS,'campaign_name',array('id'=>$campaign_id));
				$knwmr=array();
				$rvw=$this->check_review_qruse($senderId,$campaign_id);
				if(!empty($rvw))
				{
					$btns=array(array("type"=>"web_url","title"=>"View Reviews","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/brand_review/$brand_id/".base64_encode(convert_uuencode($campaign_id))."/$senderId"),array("type"=>"web_url","title"=>"View Brand Details","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/brand_details/$brand_id/".base64_encode(convert_uuencode($campaign_id))),$rvw);
				}
				else
				{
					$btns=array(array("type"=>"web_url","title"=>"View Reviews","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/brand_review/$brand_id/".base64_encode(convert_uuencode($campaign_id))."/$senderId"),array("type"=>"web_url","title"=>"View Brand Details","webview_height_ratio"=>"tall","url"=>base_url()."ChatPot/brand_details/$brand_id/".base64_encode(convert_uuencode($campaign_id))));
				}
				foreach($media as $k=>$medi)
				{
					$path=base_url()."assets/campaign/banner/".$medi->banner_url;
					$knwmr[$k]['title']=$campaign_name;
					$knwmr[$k]['item_url']='';
					$knwmr[$k]['image_url']=$path;
					$knwmr[$k]['subtitle']='';
					$knwmr[$k]['buttons']=$btns;
				}
				$answer=array("attachment"=>array(
			  "type"=>"template",
			  "payload"=>array(
				"template_type"=>"generic",
				
				"elements"=>$knwmr)
				)); 
				$this->response_send($senderId,$answer);echo '<pre>';print_r($answer);die;
	}
	function download_qrcode()
	{
		echo 'Welcome';die;
	}
}