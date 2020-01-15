<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
// Load the Rest Controller library
//require APPPATH . '/libraries/REST_Controller.php';

class Mobile extends CI_Controller {

	public function __construct() { 
		parent::__construct(); 
        // Load the user model
		$this->load->model('api_model'); 
		$this->load->helper(array('mail_helper','notification_helper'));
		$this->load->helper('upload_helper');
		date_default_timezone_set('Asia/Calcutta');
		$this->load->library('phpqrcode/qrlib');

	}

	public function updateProfilePercent($user_id) 
	{
		$percent 	= 0;
		$conAr=array('id'=>$user_id);
		$users=$this->api_model->getRowData(USERS,'*',$conAr);
		//echo"<pre>";print_r($users);die;
		if(isset($users->name) && $users->name!='')
		{
			$percent = $percent+15;


		}
		if(isset($users->email) && $users->email!='')
		{
			$percent = $percent+15;

		}
		if(isset($users->phone) && $users->phone!='')
		{
			$percent = $percent+10;
		}
		if(isset($users->image) && $users->image!='')
		{
			$percent = $percent+10;

		}
		if(isset($users->gender) && $users->gender!='' && $users->gender!=0)
		{
			$percent = $percent+10;

		}
		if(isset($users->age_bracket_id) && $users->age_bracket_id!=0 && $users->age_bracket_id!='')
		{
			$percent = $percent+10;

		}

		$profilePer = $percent;

		$totalans     = $this->api_model->mysqlNumRows(USER_INTERESTS,'id',array('is_active'=>'1','user_id'=>$user_id));
		//echo "<pre>";print_r($percent);die;

		//echo $totalans;die;
		$revPerc = 0;

		if($totalans > 0 && $totalans < 5)
		{
			$revPerc = $revPerc+5;
		}
		if($totalans >= 5 && $totalans < 10)
		{
			$revPerc = $revPerc+15;
		}
		if($totalans >= 10 && $totalans < 14)
		{
			$revPerc = $revPerc+25;
		}
		if($totalans >= 14)
		{
			$revPerc = $revPerc+30;
		}

		$finalPer = $profilePer + $revPerc;
		//echo $profilePer;die;
		//echo $finalPer;die;
		$Tpercent = ceil($finalPer / 10) * 10;

		$userData = array('profile_completion'=> $Tpercent);
		$update=$this->api_model->update(USERS,$userData,array('id'=>$user_id));

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
	function register(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		$social_login_id='';$name='';$email='';$phone='';$age='';$gender='';$password='';$image='';$device_id='';$device_token='';
		$device_type='';$device_model='';$device_name='';$device_os_version='';$app_version='';

		if(!empty($this->data))
		{
			if(isset($this->data['social_login_id']) && $this->data['social_login_id']!='')
					$social_login_id=$this->data['social_login_id'];
			if(isset($this->data['name']) && $this->data['name']!='')
					$name=$this->data['name'];
			if(isset($this->data['email']) && $this->data['email']!='')
					$email=$this->data['email'];
			if(isset($this->data['phone']) && $this->data['phone']!='')
					$phone=$this->data['phone'];
			if(isset($this->data['age']) && $this->data['age']!='')
					$age=$this->data['age'];
			if(isset($this->data['gender']) && $this->data['gender']!='')
					$gender=$this->data['gender'];
			if(isset($this->data['password']) && $this->data['password']!='')
					$password=$this->data['password'];
			if(isset($this->data['image']) && $this->data['image']!='')
					$image=$this->data['image'];
			if(isset($this->data['device_id']) && $this->data['device_id']!='')
					$device_id=$this->data['device_id'];
			if(isset($this->data['device_token']) && $this->data['device_token']!='')
					$device_token=$this->data['device_token'];
			if(isset($this->data['device_type']) && $this->data['device_type']!='')
					$device_type=$this->data['device_type'];

			if(isset($this->data['device_model']) && $this->data['device_model']!='')
					$device_model=$this->data['device_model'];
			if(isset($this->data['device_name']) && $this->data['device_name']!='')
					$device_name=$this->data['device_name'];
			if(isset($this->data['device_os_version']) && $this->data['device_os_version']!='')
				$device_os_version=$this->data['device_os_version'];
			if(isset($this->data['app_version']) && $this->data['app_version']!='')
				$app_version=$this->data['app_version'];
			if($social_login_id==''){
				$con= "email ='".$email."' OR phone ='".$phone." '";
				$users=$this->api_model->getRowData(USERS,'id,email,phone',$con);
				

					$otp 		  = mt_rand(1000, 9999);
					$otpm 		  = mt_rand(1000, 9999);
					$userData = array(
						'name'					=> $name,
						'email' 				=> $email, 
						'phone' 				=> $phone,
						'age_bracket_id'		=> $age,
						'gender'				=> $gender,
						'password' 				=> md5($password), 
						'image'					=> '',
						'otp'					=> $otp,
						'otp_phone'					=> $otpm,
						'is_active'				=> "1",
						'registration_status'	=> "1",
									//'verified' 			=> $verified,
						'created_dttm'  		=> date('Y-m-d H:i:s'),
						'modified_dttm'  		=> date('Y-m-d H:i:s'),
					);
					if(!empty($users)){
						$this->api_model->update(USERS,$userData,array('id'=>$users->id));
						$user_id=$users->id;
					}
					else
					{
						$user_id=$this->api_model->insert(USERS,$userData);
					}
					if($user_id){
						$userDeviceData = array(
						'user_id'				=> $user_id,
						'device_id' 			=> $device_id, 
						'device_token' 			=> $device_token,
						'device_type'			=> $device_type,
						'device_model'			=> $device_model,
						'device_name' 			=> $device_name, 
						'device_os_version'		=> $device_os_version,
						'app_version'			=> $app_version,
						'created_dttm'  		=> date('Y-m-d H:i:s'),
						'modified_dttm'  		=> date('Y-m-d H:i:s'),
					);
					$this->api_model->insert(USERS_DEVICE_DTL,$userDeviceData);

					
						$msgsms='iSamplez One Time password(OTP) for mobile verification is '.$otpm.' Please dont share the OTP with anyone for security reasons.';
						$mobileno=explode('+',$phone);
						$mobile=$mobileno[1];
						$this->sendSMS($mobile,$msgsms);
						$subject         	=  "iSamplez OTP Details";
						$data['name']    	=  $name;
						$data['otp'] 		=  $otp;
						$message=$this->load->view('emailer/emailer_otp',$data,true);
						$mailResponse    	= sendMail($email,$subject,$message);
						//$usersDetails=$this->api_model->getRowData(USERS,'id as user_id,name,email,phone,age_bracket_id as age,registration_status,gender',array('id'=>$user_id));
						$msg['user_id']  	= "$user_id";
						$msg['status']   	= 'success'; 
						$msg['status_code']	= 200;
						$msg['message']    	= 'Registered successfully.';
					}					
			}
			else {
				$con= "phone ='".$phone." ' OR email ='".$email."' OR social_oath_token ='".$social_login_id."'";
				$users=$this->api_model->getRowData(USERS,'*',$con);
				//echo $this->db->last_query();die;
				if($image=='')
				{
					$image=$users->image;
				}
				$userData = array(
					'name'					=> $name,
					'email' 				=> $email, 
					'image'					=> $image,
					'social_oath_token'		=> $social_login_id,
					'social_login'			=> "1",
					'password' 				=> md5($password), 
					'is_active'				=> "1",
					'created_dttm'  		=> date('Y-m-d H:i:s'),
					'modified_dttm'  		=> date('Y-m-d H:i:s'),
				);
				if(empty($users)){
					$user_id=$this->api_model->insert(USERS,$userData);
				}
				else{

					if($users->age_bracket_id)
						$age=$users->age_bracket_id;
					if($users->gender)
						$gender=$users->gender;
					unset($userData['created_dttm']);
					$user_id=$users->id;
					$update=$this->api_model->update(USERS,$userData,array('id'=>$users->id));

				}
				
				$usersDevDtlCount     = $this->api_model->mysqlNumRows(USERS_DEVICE_DTL,'id',array('user_id' => $user_id));
				$userDeviceData = array(
					'user_id'				=> $user_id,
					'device_id' 			=> $device_id, 
					'device_token' 			=> $device_token,
					'device_type'			=> $device_type,
					'device_model'			=> $device_model,
					'device_name' 			=> $device_name, 
					'device_os_version'		=> $device_os_version,
					'app_version'			=> $app_version,
					'created_dttm'  		=> date('Y-m-d H:i:s'),
					'modified_dttm'  		=> date('Y-m-d H:i:s'),
				);
				if($usersDevDtlCount==0)
					$this->api_model->insert(USERS_DEVICE_DTL,$userDeviceData);
				else if($usersDevDtlCount>0){
					unset($userDeviceData['created_dttm']);
					$this->api_model->update(USERS_DEVICE_DTL,$userDeviceData,array('user_id' => $user_id));
				}
				unset($userData['social_oath_token'],$userData['social_login'],$userData['password'],$userData['is_active'],$userData['created_dttm'],$userData['modified_dttm']);
				$userData['user_id']="$user_id";
				$userData['age']=$age;
				$userData['gender']=$gender;
				$msg['details']=$userData;
				$msg['status']='success'; 
				$msg['status_code']=200;
				$msg['is_otp_verified']=1;
				$msg['message']='Login successfully.'; 
				if($users->phone=='' || $users->registration_status==1)
				{
					$msg['is_otp_verified']=0; 
					$msg['message']='Mobile verification is pending.'; 
				}
				

			}
			$this->updateProfilePercent($user_id);
			
		}
		else
		{
			echo 'Access Denied';die;
		}
		$this->convertNullToEmpty($msg);
		echo json_encode($msg);die;

	}

	function otp_verification(){
		$response=array();
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{	
			$user_id='';$otp='';$motp='';$otp_id='';
			if(isset($this->data['user_id']))
			$user_id=$this->data['user_id'];
			if(isset($this->data['otp']))
			$otp=$this->data['otp'];
			if(isset($this->data['motp']))
			$motp=$this->data['motp'];
			if(isset($this->data['otp_id']))
			$otp_id=$this->data['otp_id'];
			if($user_id==''){

				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id and OTP are required.';
			}
			else{
				$ismobverify=1;$isemlverify=1;
				if($otp!='')
				{
					$con=array('id'=>$user_id,'otp'=>$otp);
					$users=$this->api_model->getRowData(USERS,'email,phone,registration_status',$con);
					if(empty($users))
					{
						$isemlverify=0;
						$response['status']=false; 
						$response['status_code']=300;
						$response['message']='Email OTP is incorrect.';
					}
				}
				if($motp!='')
				{
					$con=array('id'=>$user_id,'otp_phone'=>$motp);
					$users=$this->api_model->getRowData(USERS,'email,phone,registration_status',$con);
					if(empty($users))
					{
						$ismobverify=0;
						$response['status']=false; 
						$response['status_code']=300;
						$response['message']='Mobile OTP is incorrect.';
					}
					else
					{
						if($otp_id!='' && $otp_id!=$user_id)
						{   
							$con= "id ='".$user_id."'";
							$orignl_user=$this->api_model->getRowData(USERS,'*',$con);
							$con= "id ='".$otp_id."'";
							$dup_user=$this->api_model->getRowData(USERS,'*',$con);
							$ph=str_replace("Dup","",$dup_user->phone);
							$userData = array('phone'=>$ph,'otp_chatbot'=>$dup_user->otp_chatbot,'fb_id'=>$dup_user->fb_id,'chtbot_otp_count'=>$dup_user->chtbot_otp_count,'sms_sample_wrong_count'=>$dup_user->sms_sample_wrong_count,'is_sms_sample_use'=>$dup_user->is_sms_sample_use,'imei'=>$dup_user->imei,'smsqr_code_url'=>$dup_user->smsqr_code_url,'modified_dttm'=> date('Y-m-d H:i:s'));
							$this->api_model->update(USERS,$userData,array('id'=>$user_id));
							$userDataDup = array('name'=>$dup_user->name.'Duplicate','email'=>$dup_user->email.'Duplicate','phone'=>$dup_user->phone.'Dup','modified_dttm'=> date('Y-m-d H:i:s'));
							$this->api_model->update(USERS,$userDataDup,array('id'=>$otp_id));
							$dupSamples=$this->api_model->getResultData(USER_SAMPLES,'id,campaign_sample_id,campaign_id',array('user_id'=>$otp_id));
							if(!empty($dupSamples))
							{
								foreach($dupSamples as $dups)
								{ 
									$realSample= $this->api_model->mysqlNumRows(USER_SAMPLES,'id',array('user_id'=> $user_id,'campaign_sample_id'=>$dups->campaign_sample_id,'campaign_id'=>$dups->campaign_id)); 
									if($realSample>0)
									{
										$this->api_model->delete(USER_SAMPLES,array('user_id'=>$user_id,'campaign_sample_id'=>$dups->campaign_sample_id,'campaign_id'=>$dups->campaign_id));
										$this->api_model->update(USER_SAMPLES,array('user_id'=>$user_id),array('id'=>$dups->id));
									}
								}
							}
						}
					}
				}
				if($ismobverify==1 && $isemlverify==1)
				{
					$userData = array(
							'registration_status'=> '2' ,
							'modified_dttm'  	=> date('Y-m-d H:i:s'),
						);
					$signupId=$this->api_model->update(USERS,$userData,array('id'=>$user_id));
					$response['status']=true; 
					$response['status_code']=200;
					$response['message']='OTP verified successfully.';
				}
					
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		echo json_encode($response);die;
		
	}
	
	function resend_otp(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			/*$access_token=getallheaders()['access_token'];
			$prevaccess_token=$this->api_model->getField(USERS,'access_token',array('id'=>$id));
			if($access_token!=$prevaccess_token){
				$response['status']=false; 
				$response['status_code']=300;
				$response["msg"] = "You are not authorized.";
	            echo json_encode($response);
			}
			else{*/
				$user_id='';
				if(isset($this->data['user_id']))
					$user_id=$this->data['user_id'];
				if($user_id==''){
					$response['status']=false; 
					$response['status_code']=300;
					$response['message']='User Id is required.';
				}
				else{
					$users=$this->api_model->getRowData(USERS,'email,phone,name',array('id'=>$user_id));
					if(!empty($users))
					{
						$otp 			= mt_rand(1000, 9999);
						
						$con=array('id'=>$user_id);
						if($this->data['otp_type']=='email')
						{
							$userData = array(
								'otp'			=> $otp ,
								'modified_dttm' => date('Y-m-d H:i:s'),
							);
							$subject ="iSamplez OTP verification ";
							$data['name']=$users->name;
							$data['message']='Your OTP is '.$otp.' ';
							$message=$this->load->view('emailer/emailer_normal',$data,true);
							$mailResponse=sendMail($users->email,$subject,$message);
							$response['message']='OTP is sent to your email address.';
						}
						else
						{
							$userData = array(
								'otp_phone'			=> $otp ,
								'modified_dttm' => date('Y-m-d H:i:s'),
							);
							$msg='iSamplez One Time password(OTP) for mobile verification is '.$otp.' Please dont share the OTP with anyone for security reasons.';
							$mobileno=explode('+',$phone);
							$mobile=$mobileno[1];
							$this->sendSMS($mobile,$msg);
							$response['message']='OTP is sent to your phone number.';
						}
						$signupId=$this->api_model->update(USERS,$userData,$con);
						$response['status']=true; 
						$response['status_code']=200;
							
						
					}
					else
					{

						$response['status']=false; 
						$response['status_code']=300;
						$response['message']="User doesn't exists .";
					}
				}
			//}
		}
		else
		{
			echo 'Access Denied';die;
		}
		echo json_encode($response);die;
	}
	
	function change_phone_number()
	{
		$this->data=json_decode(file_get_contents("php://input"), true); 
		if(!empty($this->data))
		{
			    $con= "phone ='".$this->data['mobile_no']." '";
				$users=$this->api_model->getRowData(USERS,'id,phone',$con);
				
				$otp= mt_rand(1000, 9999);
				if(empty($users))
				$userData = array('otp_phone'=> $otp,'phone'=> $this->data['mobile_no'],'modified_dttm' => date('Y-m-d H:i:s'));
				else
				$userData = array('otp_phone'=> $otp ,'modified_dttm' => date('Y-m-d H:i:s'));
				$con1= "id ='".$this->data['user_id']." '";
				$signupId=$this->api_model->update(USERS,$userData,$con1);	
				$smsg='iSamplez One Time password(OTP) for mobile verification is '.$otp.' Please dont share the OTP with anyone for security reasons.';
				$mobileno=explode('+',$this->data['mobile_no']);
				$mobile=$mobileno[1];
				$this->sendSMS($mobile,$smsg);
				if(empty($users))
				{
					$msg['otp_id']=$this->data['user_id'];
				}
				else
				{
					$msg['otp_id']=$users->id;
				}
				$msg['status']=true; 
				$msg['status_code']=200;
				$msg['message']='OTP send succesfully.';

				
			echo json_encode($msg);die;
		}
	}

	function login(){  
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{	
			$email='';$password='';$device_id='';$device_token='';$device_type='';$device_model='';$device_name='';$device_os_version='';$app_version='';
			if(isset($this->data['email']))
				$email    =$this->data['email'];
			if(isset($this->data['password']))
				$password = $this->data['password'];

			if(isset($this->data['device_id']) && $this->data['device_id']!='')
					$device_id=$this->data['device_id'];
			if(isset($this->data['device_token']) && $this->data['device_token']!='')
					$device_token=$this->data['device_token'];
			if(isset($this->data['device_type']) && $this->data['device_type']!='')
					$device_type=$this->data['device_type'];
			if(isset($this->data['device_model']) && $this->data['device_model']!='')
					$device_model=$this->data['device_model'];
			if(isset($this->data['device_name']) && $this->data['device_name']!='')
					$device_name=$this->data['device_name'];
			if(isset($this->data['device_os_version']) && $this->data['device_os_version']!='')
					$device_os_version=$this->data['device_os_version'];
			if(isset($this->data['app_version']) && $this->data['app_version']!='')
				$app_version=$this->data['app_version'];

			if($email=='' || $password==''){

				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='Email and password are required.';
			}
			else{

				$users     = $this->api_model->getRowData(USERS,'id as user_id,name,email,phone,age_bracket_id as age,gender,image,registration_status,is_pass_forgot',array('email' => $email,'password' =>  md5(trim($password))));
				$user_id=$users->user_id;
				if(!empty($users) && $users->registration_status==2)
				{  
					$usersDevDtlCount     = $this->api_model->mysqlNumRows(USERS_DEVICE_DTL,'id',array('user_id' => $user_id));
					$userDeviceData = array(
						'user_id'				=> $user_id,
						'is_pass_forgot'		=> (int)$users->is_pass_forgot,
						'device_id' 			=> $device_id, 
						'device_token' 			=> $device_token,
						'device_type'			=> $device_type,
						'device_model'			=> $device_model,
						'device_name' 			=> $device_name, 
						'device_os_version'		=> $device_os_version,
						'app_version'			=> $app_version,
						'created_dttm'  		=> date('Y-m-d H:i:s'),
						'modified_dttm'  		=> date('Y-m-d H:i:s'),
					);
					if($usersDevDtlCount==0)
						$this->api_model->insert(USERS_DEVICE_DTL,$userDeviceData);
					else if($usersDevDtlCount>0){
						unset($userDeviceData['created_dttm']);
						$this->api_model->update(USERS_DEVICE_DTL,$userDeviceData,array('user_id' => $user_id));
					}
					$response['details']		= $users; 
					$response['status']			= 'success'; 
					$response['status_code'] 	= 200;
					$response['message']		= 'Login successfully.';
				}
				else if(!empty($users) && $users->registration_status==1)
				{
					$response['status']     	= 'error'; 
					$response['status_code']	= 300;
					$response['message']    	= 'OTP verification is pending.';
					$response['user_id']    	= $user_id;
					$response['details']		= $users;
				}
				else if(!empty($users) && $users->registration_status==3)
				{
					$response['status']='error'; 
					$response['status_code']=300;
					$response['message']='Your account is blocked.';
				}
				else 
				{
					$usersCount     = $this->api_model->mysqlNumRows(USERS,'id',array('email' => $email));
					if($usersCount<1)
						$response['message']="Email address doesn't exist.";
					else
						$response['message']='Password is incorrect.';
					$response['status']='error'; 
					$response['status_code']=300;

				}
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		echo json_encode($response);die;
	}

	function forgot_password(){
		$response['status']=true;
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$email='';
			if(isset($this->data['email']))
				$email=$this->data['email'];
			if($email==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='Email is required.';
			}
			else{
				$users     = $this->api_model->getRowData(USERS,'id as user_id,name,email,phone,is_active,',array('email' => $email));
				if($users)
				{
					$string=$this->generateRandomString();
					$userData = array(
						'password'=> md5($string) ,
						'is_pass_forgot'=>1,
						'modified_dttm'  	=> date('Y-m-d H:i:s'),
					);
					$this->api_model->update(USERS,$userData,array('id'=>$users->user_id));
			      	$subject="iSamplez Forgot password";
	                $data['name']=$users->name;
		      	   	/*$data['message']='Recently a request was submitted to reset a password for your account. If this was a mistake, just ignore this email.
	                <br/>To reset your password, visit the following link: <a href="'.$resetPassLink.'">'.$resetPassLink.'</a>';*/
	                $data['message']='Your new password is : '.$string.'';
					$message=$this->load->view('emailer/emailer_normal',$data,true);
					$mailResponse=sendMail($users->email,$subject,$message);


					if($mailResponse){
						$response['status']=true;
						$response['status_code']=200;
						$response['message']='A reset password link is sent to your respective email id.';
					}
					else
					{
						
						$response['status']=false;
						$response['status_code']=300;
						$response['message']='Mail not send . Please try again.';
					}
				}
				else
				{	
					$response['status']=false;
					$response['status_code']=300;
					$response['message']="Email address doesn't exist.";
				}
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		echo json_encode($response);die;
	}
		
		
	function change_password(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$user_id='';$old_password='';$new_password='';
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if(isset($this->data['old_password']))
				$old_password=$this->data['old_password'];
			if(isset($this->data['new_password']))
				$new_password=$this->data['new_password'];
			if($user_id!='' && $old_password!='' && $new_password!=''){ 
				$users     = $this->api_model->getRowData(USERS,'id,name,email,phone,is_active,',array('id' => $user_id,'password' => md5($old_password)));
				if($users)
				{
					$userData = array(
						'password'=> md5($new_password) ,
						'is_pass_forgot'=>0,
						'modified_dttm'  	=> date('Y-m-d H:i:s'),
					);
					$this->api_model->update(USERS,$userData,array('id'=>$users->id));
					$response['status']=true; 
					$response['status_code']=200;
					$response['message']='Password changed successfully.';
				}
				else
				{
					$response['status']=false; 
					$response['status_code']=300;
					$response['message']='old password not matched';
				}
			}
			else
			{
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id Old password and New password are required.';
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		echo json_encode($response);die;
	}

	function profile(){
		$this->load->model('users_model');
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{	
			$user_id='';$my_interest=0;
			if(isset($this->data['user_id']))
			$user_id=$this->data['user_id'];
			if($user_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id is required.';
			}
			else{
				$conditions=array('u.id'=>$user_id,'u.is_active'=>'1');
				//AgeBracket.age_bracket_desc as age
				$users     	  = $this->users_model->profile('u.id as user_id,u.name,u.email,u.phone,u.image,u.gender,AgeBracket.age_bracket_desc as age,u.password,',$conditions);
				$totalqsn     = $this->api_model->mysqlNumRows(INTEREST_MASTER,'id',array('is_active'=>'1'));
				$totalans     = $this->api_model->mysqlNumRows(USER_INTERESTS,'id',array('is_active'=>'1','user_id'=>$user_id));
				if($totalqsn>0)
				$my_interest =round((($totalans*100)/($totalqsn-1)),2);
				if(!empty($users))
				{
					$users->age = ($users->age) ? $users->age : '' ;
					$response['status']=true;
					$response['status_code']=200;
					$response['message']='success';
					$response['user_id']	= $users->user_id;
					$response['name']   	= $users->name;
					$response['email']  	= $users->email;
					$response['phone']  	= $users->phone;
					$response['image']  	= $users->image;
					$response['gender']  	= $users->gender;
					$response['password']  	= $users->password;
					$response['age']		= $users->age;
					$response['is_email_verified']='1';
					$response['my_interest']=$my_interest;
				}
				else
				{

					$response['status']=false; 
					$response['status_code']=300;
					$response['message']='User does not exists.';
				}
			}
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
		
	function update_profile(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);		
		if(!empty($this->data))
		{
			$user_id='';$name='';$gender='';$phone='';$age='';$age_bracket_id='';$image='';
			if(isset($this->data['user_id']) && $this->data['user_id']!='')
				$user_id=$this->data['user_id'];
			if(isset($this->data['name']) && $this->data['name']!='')
				$name=$this->data['name'];
			if(isset($this->data['gender']) && $this->data['gender']!='')
				$gender=$this->data['gender'];
			if(isset($this->data['phone']) && $this->data['phone']!='')
				$phone=$this->data['phone'];
			if(isset($this->data['age']) && $this->data['age']!='')
				$age=$this->data['age'];
			if(isset($this->data['age_bracket_id']) && $this->data['age_bracket_id']!='')
				$age_bracket_id=$this->data['age_bracket_id'];
			if(isset($this->data['image']) && $this->data['image']!='')
				$image=$this->data['image'];

			$path='';
			if($user_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id is required.';
			}
			else{ 
				if($phone!=''){
					$rowcount     = $this->api_model->mysqlNumRows(USERS,'id',array('id !='=>$user_id,'phone'=>$phone,'is_active'=>'1'));
					if($rowcount>0){
						$response['status']=false; 
						$response['status_code']=300;
						$response['message']='Phone Number already exists.';
						echo json_encode($response);die;
						
					}
				}
				if($image!=''){
					$targetDir = "assets/img/users/";						
					$data = $image;	 
					$image_parts = explode(";base64,", $data);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];
					$image_base64 = base64_decode($image_parts[1]);
					$uniueId = uniqid();
					$file = $targetDir . $uniueId . '.'.$image_type;
					$image = $uniueId . '.'.$image_type;
					$path = base_url().'assets/img/users/'.$image;
					file_put_contents($file, $image_base64);
				}

				$userData = array(
					'name'					=> $name, 
					'phone' 				=> $phone,
					'image'					=> $path,
					'gender'				=> $gender,
					'age_bracket_id'		=> $age, 
					'modified_dttm'  		=> date('Y-m-d H:i:s'),
				);
				$rowchk = $this->api_model->mysqlNumRows(USERS,'id',array('id'=>$user_id,'phone'=>$phone));
				if($rowchk==0 && $phone!='')
				{
					$userData['registration_status']=1;
				}
				if($name=='')
					unset($userData['name']);
				if($phone=='')
					unset($userData['phone']);
				if($image=='')
					unset($userData['image']);
				if($gender=='')
					unset($userData['gender']);
				if($age=='')
					unset($userData['age_bracket_id']);
				$this->api_model->update(USERS,$userData,array('id'=>$user_id));
				
				$response['status']=true; 
				$response['status_code']=200;
				$response['message']='Profile updated successfully.';
			}
			$this->updateProfilePercent($user_id);
		}
		else
		{
			echo 'Access Denied';die;
		}
		echo json_encode($response);die;
	}

	function campaign_list(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$user_id='';$campaign_id='';$campaign_status='';$primary_image='';$buy_now ='';
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if(isset($this->data['campaign_id']))
				$campaign_id=$this->data['campaign_id'];
			if(isset($this->data['campaign_status']))
				$campaign_status=$this->data['campaign_status'];
			if($user_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id is required.';
			}
			else{
				$conditions = "camp.is_active='1' AND brand.is_active='1' AND camp.is_publish='1'";
				if($campaign_id)
					$conditions .= " AND  camp.id = '".$campaign_id."'";
				
				//if($campaign_status==1 || $campaign_status=="")
				if($campaign_status==1)
				{
					$conditions .= " AND ((STR_TO_DATE(camp.start_date, '%Y-%m-%d') <='".date('Y-m-d')."' AND STR_TO_DATE(camp.end_date, '%Y-%m-%d') >='".date('Y-m-d')."') AND (STR_TO_DATE(campsample.start_date, '%Y-%m-%d') <='".date('Y-m-d')."' AND STR_TO_DATE(campsample.end_date, '%Y-%m-%d') >='".date('Y-m-d')."')) ";
				}
				else if($campaign_status==2)
				{
					$conditions .= " AND (STR_TO_DATE(camp.start_date, '%Y-%m-%d') >'".date('Y-m-d')."' OR STR_TO_DATE(campsample.start_date, '%Y-%m-%d') >'".date('Y-m-d')."')";
				}
				else if($campaign_status==3)
				{
					$conditions .= " AND campsample.id!='' AND ((STR_TO_DATE(camp.start_date, '%Y-%m-%d') <'".date('Y-m-d',strtotime(date('Y-m-d')))."' AND STR_TO_DATE(camp.end_date, '%Y-%m-%d') <'".date('Y-m-d')."') OR (STR_TO_DATE(campsample.start_date, '%Y-%m-%d') <'".date('Y-m-d',strtotime(date('Y-m-d')))."' AND STR_TO_DATE(campsample.end_date, '%Y-%m-%d') <'".date('Y-m-d')."')) ";
				}
				
				$campaign=$this->api_model->campaignList($conditions,array());
				//echo $this->db->last_query();die;

				$campList=array();
				$campaignBanners=array();
				if($campaign){
					foreach ($campaign as $value) {						
						$campaignBanners=$this->api_model->getResultData(CAMPAIGNBANNERS,'id,banner_type,banner_url',array('campaign_id'=>$value->id,'is_active'=>'1'),"cover_image","DESC");
						//echo $this->db->last_query();die;
						$coverImage=$this->api_model->getField(CAMPAIGNBANNERS,'banner_url',array('campaign_id'=>$value->id,'cover_image'=>'1','is_active'=>'1'));
						$primary_image=$coverImage? base_url().'assets/campaign/banner/'.$coverImage:'';
					  	$campsMedia=array();
					  	if($campaignBanners):
						  	foreach ($campaignBanners as $banner) :					
						  		$banner_type="{$banner->banner_type}";
					  			$url=base_url().'assets/campaign/banner/'.$banner->banner_url;
						  		$CampMediaArray	= array("type" =>$banner_type,"url"=>$url );
						  		array_push($campsMedia, $CampMediaArray);
						  	endforeach;
					  	endif;
					  	$brandAssetsData=$this->api_model->getResultData(BRANDASSETS,'asset_name,asset_type,asset_url',array('brand_id'=>$value->brand_id,'is_active'=>'1'));
						$brand['images']=array();
						$brand['media']=array();
						$brandMedia=array();
						if($brandAssetsData):
							foreach ($brandAssetsData as $assets) :
								$asset_type="{$assets->asset_type}";
								$url=base_url().'assets/brand/assets/'.$assets->asset_url;
								$mediaArray	= array("type" =>$asset_type,"url"=>$url);
								array_push($brandMedia, $mediaArray);
							endforeach;
						endif;

						$isInterested=$this->api_model->mysqlNumRows(USER_CAMP_INTERESTS,'id',array('campaign_id'=>$value->id,'user_id'=>$user_id,'is_active'=>'1'));
						if($isInterested>0)
							$interested=true;
						else
							$interested=false;
						if(date('Y-m-d',strtotime($value->start_date)) <= date('Y-m-d') && date('Y-m-d',strtotime($value->end_date)) >= date('Y-m-d') && date('Y-m-d',strtotime($value->camp_sample_start_date)) <= date('Y-m-d') && date('Y-m-d',strtotime($value->camp_sample_end_date)) >= date('Y-m-d')){
							$campaign_status='1';
						}
						else if (date('Y-m-d',strtotime($value->start_date)) > date('Y-m-d') || date('Y-m-d',strtotime($value->camp_sample_start_date)) > date('Y-m-d') ) 
							$campaign_status='2';
						else if(date('Y-m-d',strtotime($value->end_date)) < date('Y-m-d') || date('Y-m-d',strtotime($value->camp_sample_end_date)) < date('Y-m-d'))
							$campaign_status='3';
						$requested=false;
						$cond="campaign_sample_id='".$value->campaign_sample_id."' AND user_id='".$user_id."'  AND (status!='2' OR status!='2')";
						$rowcountt=$this->api_model->mysqlNumRows(USER_SAMPLES,'id',$cond);
						if($rowcountt>0)
							$requested=true;

						$rowcount = $this->api_model->mysqlNumRows(USER_SAMPLES,'id',array('campaign_sample_id'=>$value->campaign_sample_id,'user_id'=>$user_id,'status !='=>'2'));
						$is_qrcode_used=0;
						$sampleStatus='';
						if($rowcount>0){
							$UserSample = $this->api_model->getRowData(USER_SAMPLES,'status',array('campaign_id'=>$value->id,'user_id'=> $user_id,'status !='=>'2'));
							if(@count($UserSample)){
								$sampleStatus=$UserSample->status;
								if($sampleStatus=='3' || $sampleStatus=='4'){
									$is_qrcode_used=1;
								}
							}
						}
						if($campaign_status==3)
						{
							$value->is_active=false;							
							
						}
						if(!empty($value->buy_now_link))
								$buy_now = $value->buy_now_link;
							else{
								$buy_now = '';
							}
						
						if($value->total_campaign_samples>0){
							//$percent_left=ceil(100-(($value->total_campaign_samples_used*100)/$value->total_campaign_samples));
						    $percent_left=floor(100-(($value->total_campaign_samples_used*100)/$value->total_campaign_samples));
						    if($percent_left>0)
								$percent_left=$percent_left;
							else
								$percent_left=0;
						   // if (is_float($percent_left))
						    //$percent_left=round($percent_left, 2);
						}
						else
							$percent_left=0;
						$value->camp_sample_start_date = $value->camp_sample_start_date!=''  ? $value->camp_sample_start_date: '';
						$value->camp_sample_end_date = $value->camp_sample_end_date!=''  ? $value->camp_sample_end_date: '';
						$value->campaign_sample_id = $value->campaign_sample_id!=''  ? $value->campaign_sample_id: '';
						if(date('Y-m-d',strtotime($value->camp_sample_end_date))< date('Y-m-d'))
							$sampleOver=true;
						else
							$sampleOver=false;
						$newarray	= array(
							"assets"					=> $campsMedia,
							"primary_image"				=> $primary_image,
							"percent_left"				=> $percent_left,
							"campaign_name"				=> $value->campaign_name,
							"campaign_desc"				=> $value->campaign_desc,
							"start_date"				=> date('Y-m-d H:i:s',strtotime($value->start_date)),
							"end_date"					=> date('Y-m-d H:i:s',strtotime($value->end_date)),
							"rating"					=> $value->avg_rating,
							"campaign_id"				=> $value->id,
							"campaign_sample_id"	    => $value->campaign_sample_id,
							"camp_sample_start_date"	=> $value->camp_sample_start_date,
							"camp_sample_end_date"		=> $value->camp_sample_end_date,
							"camp_is_active"		    => (boolean)$value->is_active,
							"interested"				=> $interested,
							"requested"				    => $requested,
							"is_qrcode_used"		    => $is_qrcode_used,
							"campaign_status"			=> $campaign_status,
							"buy_now_link"				=> $buy_now,
							"sampleStatus"			    => $sampleStatus,
							"sampleOver"			    => $sampleOver,
							"brandDetails"				=> array(
															'brand_name'=>$value->brand_name,'brand_desc'=>trim($value->brand_desc),'brand_logo_url'=>$value->brand_logo_url,
															'media'=>$brandMedia
							)
						);
						array_push($campList, $newarray);
					}
				}
				if($campList)
				{
					$response['status_code']=200;
					$response['message']='success';
					$response['status']=true;
					$response['campaignList']=$campList;
				}
				else
				{
					$response['status_code']=300;
					$response['status']=false;
					//$response['message']='No record exists.';
					$response['message']='Campaigns data is not avilable.';
				}
			}
		}
		else
			{
				echo 'Access Denied';die;
			}
			echo json_encode($response);die;
	}

	public function buy_now_click()
	{
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$campaign_id=''; $post_id='';
			if(isset($this->data['campaign_id'])){
				$campaign_id=$this->data['campaign_id'];
			}
			if(isset($this->data['post_id'])){
				$post_id=$this->data['post_id'];
			}
			if($campaign_id=='' && $post_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='either Campaign Id or Post Id is required.';
			}
			else{

				if($campaign_id !='')
				{
					$buynowCount = $this->api_model->getField(CAMPAIGNS,'buy_now_click_total',array('id'=>$campaign_id));				
					$buynowCount= $buynowCount+1;
					$update=$this->api_model->update(CAMPAIGNS,array('buy_now_click_total'=>$buynowCount),array('id'=>$campaign_id));
				}
				else if($post_id!=''){
					$buynowCountPost = $this->api_model->getField(WALL_POSTS,'buy_now_click_total',array('id'=>$post_id));				
					$buynowCountPost= $buynowCountPost+1;
					$update=$this->api_model->update(WALL_POSTS,array('buy_now_click_total'=>$buynowCountPost),array('id'=>$post_id));
				}				
				$response['status_code']=200;
				$response['message']='success';
				$response['status']=true;
				
			}
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	
	function campaign_review(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$limit=10; $offset=0; $campaign_id=''; 
			if(isset($this->data['limit'])){
				$limit=$this->data['limit'];
			}
			if(isset($this->data['offset'])){
				$offset=$this->data['offset'];
			}
			if(isset($this->data['campaign_id'])){
				$campaign_id=$this->data['campaign_id'];
			}
			if($campaign_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='Campaign Id is required.';
			}
			else{
				$params['start'] = $offset; 
	    		$params['limit'] = $limit;
				$conditions = "ureview.is_active='1' AND ureview.is_published='1'  AND camp.id='".$campaign_id."'";
				$reviewData = $this->api_model->campaignReview($conditions,$params);
				$reviewList=array();
				if($reviewData){
					foreach ($reviewData as $value) {
						$photo_url='';
						if($value->image!='')
							$photo_url=$value->image;
							$newarray	= array(
								"user_id"		=> $value->user_id,
								"camapign_id"	=> $value->camapign_id,
								"user_name"		=> $value->name,
								"reviews"		=> $value->review_text,
								"is_published"	=> $value->is_published,
								"rating"		=> $value->rating,
								"photo_url"		=> $photo_url,
								"created_dttm"  => $value->created_dttm,
							);
							array_push($reviewList, $newarray);
					}
				}
				if($reviewList)
				{
					$response['status_code']=200;
					$response['message']='success';
					$response['status']=true;
					$response['reviewList']=$reviewList;
				}
				else
				{
					$response['status_code']=300;
					$response['status']=false;
					$response['message']='No record exists.';
				}
				
			}
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	function vend_machine_list(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$limit=10;$offset=0;$latitude='';$user_id='';$longitude='';$campaign_id='';
			if(isset($this->data['limit'])){
				$limit=$this->data['limit'];
			}
			if(isset($this->data['offset'])){
				$offset=$this->data['offset'];
			}
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if(isset($this->data['campaign_id']))
				$campaign_id=$this->data['campaign_id'];
			if(isset($this->data['latitude']))
				$latitude=$this->data['latitude'];
			if(isset($this->data['longitude']))
				$longitude=$this->data['longitude'];
			if($campaign_id=='' || $latitude=='' || $longitude==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='Campaign Id, Latitide and  Longitude are required.';
			}
			else{
				$params['start'] = $offset;
	    		$params['limit'] = $limit;
				$conditions = "campVend.is_active='1' AND vm.is_active='1' AND camp.id='".$campaign_id."'";
				$reviewData = $this->api_model->vendMachineList($conditions,$params,'',$latitude,$longitude);
				//echo $this->db->last_query();die;
				//print_r($reviewData);die;
				$vend_machine_list=array();
				if($reviewData){
					foreach ($reviewData as $value) {
						$address= str_replace('—', '-', $value->location_address);
						if($value->postal_code)
							$address =$address.", ".$value->postal_code;
						$newarray	= array(
						'id'                        => $value->id,
						"location_name"				=> $value->location_name,
						"location_address"			=> $address,
						"vend_lat"					=> $value->vend_lat,
						"vend_long"					=> $value->vend_long,
						"vend_no_of_samples"		=> $value->vend_no_of_samples,
						"vend_no_of_sample_used" 	=> $value->vend_no_of_sample_used,
						"distance" 					=> round($value->distance,2),
						
					);
					array_push($vend_machine_list, $newarray);
				}
			}
			if($vend_machine_list)
			{
				$response['status_code']=200;
				$response['message']='success';
				$response['status']=true;
				$response['vend_machine_list']=$vend_machine_list;
			}
			else
			{
				$response['status_code']=300;
				$response['status']=false;
				$response['message']='No record exists.';
			}
				
			}
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	function post_list(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$limit=10; $offset=0;$campaign_id='';$user_id='';$like_status=false;
			if(isset($this->data['limit'])){
				$limit=$this->data['limit'];
			}
			if(isset($this->data['offset'])){
				$offset=$this->data['offset'];
			}
			if(isset($this->data['user_id']))
			$user_id=$this->data['user_id'];

			if($user_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id is required.';
			}
			else{
				$params['start'] = $offset;
	    		$params['limit'] = $limit;
				//$cond="camp.is_active='1' AND post.is_active='1' AND post.is_publish='1' AND b.is_active='1' ";
				$cond="post.is_active='1' AND post.is_publish='1' AND b.is_active='1'";
				$postListData = $this->api_model->postList($cond,$params);
				$buyCon = "post.is_active='1' AND post.is_publish='1' AND post.campaign_id IS NULL AND post.brand_id IS NULL";
				$postListBuyData = $this->api_model->postList($buyCon,$params);
				//echo $this->db->last_query();die;
				//print_r($postListBuyData);
				$postList=array();
				$buyPostList=array();

				if($postListBuyData){
					foreach ($postListBuyData as $buyvalue) {
						$likeCount = $this->api_model->mysqlNumRows(WALL_LIKES,'id',array('post_id'=>$buyvalue->id,'user_id'=>$user_id,'is_active'=>'1'));
						$like_status = $likeCount > 0 ? true : false;

						$commentCount = $this->api_model->mysqlNumRows(WALL_COMMENTS,'id',array('post_id'=>$buyvalue->id,'user_id'=>$user_id,'is_active'=>'1'));
						$comment_status = $commentCount > 0 ? true : false;

						$campaign_status='1';
						$is_qrcode_used=0;
						$UserPromocodeStatus='';
						
						$buyNowUrl='';
						if($buyvalue->buy_now_url!='')
							$buyNowUrl = $buyvalue->buy_now_url;
						
						$promocode = $this->api_model->mysqlNumRows(USER_PROMOCODES,'id',array('post_id'=>$buyvalue->id,'user_id'=>$user_id,'is_active'=>'1','status !='=>'2'));
						if($promocode>0){
							$promocode_status=true;
							
						}
						else
							$promocode_status=false;

						if(date('Y-m-d',strtotime($buyvalue->end_date)) < date('Y-m-d'))
							$campaign_status='3';
						$banner_url='';$brand_logo_url='';
						if($buyvalue->post_banner_url!='')
							$banner_url = file_exists('assets/post/banner/'. $buyvalue->post_banner_url)? base_url('assets/post/banner/'. $buyvalue->post_banner_url) : '';
						
						$campaign_id = !empty($buyvalue->campaign_id)?$buyvalue->campaign_id:'';
						$campaign_name = !empty($buyvalue->campaign_name)?$buyvalue->campaign_name:'';
						$brand_name = !empty($buyvalue->brand_name)?$buyvalue->brand_name:'';
						$brand_desc = !empty($buyvalue->brand_desc)?$buyvalue->brand_desc:'';


						$newarray	= array(
							"post_id"				=> $buyvalue->id,
							"campaign_id"			=> $campaign_id,
							"campaign_name"			=> $campaign_name,
							"post_title"			=> $buyvalue->post_title,
							"post_desc"				=> $buyvalue->post_desc,
							"post_banner_url"		=> $banner_url,
							"banner_type"			=> "{$buyvalue->banner_type}",
							"has_promo"		    	=> (boolean)$buyvalue->has_promo,
							"promocode_status"  	=> $promocode_status,
							"no_of_likes"			=> $buyvalue->no_of_likes,
							"no_of_comments" 		=> $buyvalue->no_of_comments,
							"avg_rating" 			=> $buyvalue->avg_rating,
							"brand_name"			=> $brand_name,
							"brand_desc" 			=> $brand_desc,
							"brand_logo_url" 		=> $brand_logo_url,
							"like_status"       	=> $like_status,
							"comment_status"    	=> $comment_status,
							"is_qrcode_used"    	=> $is_qrcode_used,
							"end_date"    			=> date('Y-m-d',strtotime($buyvalue->end_date)),
							"publish_date"    		=> date('Y-m-d',strtotime($buyvalue->publish_date)),
							"campaign_status"   	=> $campaign_status,
							"user_promocode_status"	=> $UserPromocodeStatus,
							//"buy_now_status"		=> (boolean)$value->buy_now_status,
							"buy_now_url"			=> $buyNowUrl,
							
						);
						array_push($buyPostList, $newarray);
					}
				}

				if($postListData){
					foreach ($postListData as $value) {
						$likeCount = $this->api_model->mysqlNumRows(WALL_LIKES,'id',array('post_id'=>$value->id,'user_id'=>$user_id,'is_active'=>'1'));
						$like_status = $likeCount > 0 ? true : false;

						$commentCount = $this->api_model->mysqlNumRows(WALL_COMMENTS,'id',array('post_id'=>$value->id,'user_id'=>$user_id,'is_active'=>'1'));
						$comment_status = $commentCount > 0 ? true : false;

						$campaign_status='1';
						$is_qrcode_used=0;
						$UserPromocodeStatus='';
						
						$buyNowUrl='';
						if($value->buy_now_url!='')
							$buyNowUrl = $value->buy_now_url;
						
						$promocode = $this->api_model->mysqlNumRows(USER_PROMOCODES,'id',array('post_id'=>$value->id,'user_id'=>$user_id,'is_active'=>'1','status !='=>'2'));
						if($promocode>0){
							$promocode_status=true;
							$UserPromocodeStatus = $this->api_model->getField(USER_PROMOCODES,'status',array('post_id'=>$value->id,'campaign_id'=>$value->campaign_id,'user_id'=>$user_id,'status !='=>'2'));
							if($UserPromocodeStatus=='3' || $UserPromocodeStatus=='4')
								$is_qrcode_used=1;
						}
						else
							$promocode_status=false;

						if(date('Y-m-d',strtotime($value->end_date)) < date('Y-m-d'))
							$campaign_status='3';
						$banner_url='';$brand_logo_url='';
						if($value->post_banner_url!='')
							$banner_url = file_exists('assets/post/banner/'. $value->post_banner_url)? base_url('assets/post/banner/'. $value->post_banner_url) : '';
						if($value->brand_logo_url)
							$brand_logo_url = file_exists('assets/brand/logo/'.$value->brand_logo_url)? base_url('assets/brand/logo/'.$value->brand_logo_url) : '';

						$campaign_id = !empty($value->campaign_id)?$value->campaign_id:'';
						$campaign_name = !empty($value->campaign_name)?$value->campaign_name:'';
						$brand_name = !empty($value->brand_name)?$value->brand_name:'';
						$brand_desc = !empty($value->brand_desc)?$value->brand_desc:'';


						$newarray	= array(
							"post_id"				=> $value->id,
							"campaign_id"			=> $campaign_id,
							"campaign_name"			=> $campaign_name,
							"post_title"			=> $value->post_title,
							"post_desc"				=> $value->post_desc,
							"post_banner_url"		=> $banner_url,
							"banner_type"			=> "{$value->banner_type}",
							"has_promo"		    	=> (boolean)$value->has_promo,
							"promocode_status"  	=> $promocode_status,
							"no_of_likes"			=> $value->no_of_likes,
							"no_of_comments" 		=> $value->no_of_comments,
							"avg_rating" 			=> $value->avg_rating,
							"brand_name"			=> $brand_name,
							"brand_desc" 			=> $brand_desc,
							"brand_logo_url" 		=> $brand_logo_url,
							"like_status"       	=> $like_status,
							"comment_status"    	=> $comment_status,
							"is_qrcode_used"    	=> $is_qrcode_used,
							"end_date"    			=> date('Y-m-d',strtotime($value->end_date)),
							"publish_date"    		=> date('Y-m-d',strtotime($value->publish_date)),
							"campaign_status"   	=> $campaign_status,
							"user_promocode_status"	=> $UserPromocodeStatus,
							//"buy_now_status"		=> (boolean)$value->buy_now_status,
							"buy_now_url"			=> $buyNowUrl,
							
						);
						array_push($postList, $newarray);
					}
				}
				$allpostList = array_merge($buyPostList,$postList);
				if($allpostList )
				{
					/*$withPromo=array();$withutPromo=array();
					foreach($postList as $p)
					{
						if($p['has_promo']==1 || $p['has_promo']==true)
						{
							$p['has_promo']=true;
							$withPromo[]=$p;
						}
						else
						{
							$p['has_promo']=false;
							$withutPromo[]=$p;
						}
					}
					$details=array();
					$withTotal=count($withPromo);
					$withoutTotal=count($withutPromo);
					if($withTotal>=$withoutTotal)
					{
						for($i=0;$i<$withTotal;$i++)
						{
							$details[]=$withPromo[$i];
							if(!empty($withutPromo[$i]))
							{
								$details[]=$withutPromo[$i];
							}
						}
					}
					else
					{
						for($i=0;$i<$withoutTotal;$i++)
						{
							$details[]=$withutPromo[$i];
							if(!empty($withPromo[$i]))
							{
								$details[]=$withPromo[$i];
							}
						}
					}*/
					$response['status_code']=200;
					$response['message']='success';
					$response['status']=true;
					//$response['postList']=$details;
					$response['postList']=$allpostList;
					//$response['postList']=array_merge($buyPostList,$postList);
				}
				else
				{
					$response['status_code']=300;
					$response['status']=false;
					//$response['message']='No record exists.';
					$response['message']='Post data is not available.';
				}
				
			}
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	function post_comment_list(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$limit='10';$offset='0';$post_id='';$user_photo='';
			if(isset($this->data['limit'])){
				$limit=$this->data['limit'];
			}
			if(isset($this->data['offset'])){
				$offset=$this->data['offset'];
			}
			if(isset($this->data['post_id']))
				$post_id=$this->data['post_id'];

			if($post_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='Post Id  is required.';
			}
			else{
				$params['start'] = $offset;
	    		$params['limit'] = $limit;
				$cond="c.is_active='1' AND c.post_id='".$post_id."' ";
				$commentListData = $this->api_model->postCommentList($cond,$params);
				$commentList=array();
				if($commentListData){
					foreach ($commentListData as $value) {
						$user_photo = $value->image? $value->image : '';
						$newarray	= array(
							"comment_id"		=> $value->id,
							"comments"			=> $value->comments,
							"user_id"			=> $value->user_id,
							"user_name"			=> $value->name,
							"user_photo"		=> $user_photo,
							"created_dttm"      => $value->created_dttm,
						);
						array_push($commentList, $newarray);
					}
				}
				if($commentList)
				{
					$response['status_code']=200;
					$response['message']='success';
					$response['status']=true;
					$response['total_comments']=@count($commentList);
					$response['commentList']=$commentList;
				}
				else
				{
					$response['status_code']=300;
					$response['status']=false;
					$response['message']='No record exists.';
				}
				
			}
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	function post_comment(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$user_id='';$post_id='';$comments='';
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if(isset($this->data['post_id']))
				$post_id=$this->data['post_id'];
			if(isset($this->data['comments']))
				$comments=$this->data['comments'];

			if($user_id=='' || $post_id=='' || $comments==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id, Post Id and comments  are required.';
			}
			else{
				$commentData = array(
					'post_id'		=> $post_id, 
					'user_id' 		=> $user_id,
					'comments'		=> $comments,
					'is_active'		=> '1',
					'created_dttm'  => date('Y-m-d H:i:s'),
				);
				$comment_id = $this->api_model->insert(WALL_COMMENTS,$commentData);
				if($comment_id)
				{	
					$no_of_comments = $this->api_model->getField(WALL_POSTS,'no_of_comments',array('id'=>$post_id));
					$update=$this->api_model->update(WALL_POSTS,array('no_of_comments'=>($no_of_comments+1)),array('id'=>$post_id));
					$name = $this->api_model->getField(USERS,'name',array('id'=>$user_id));
					$postDtl = $this->api_model->getRowData(WALL_POSTS,'post_desc,campaign_id',array('id'=>$post_id));
					$likeUserIdArr=array(); $commentUserIdArr=array();
					$likeUserId = $this->api_model->getResultData(WALL_LIKES,'user_id',array('post_id'=>$post_id,'user_id !='=>$user_id,'is_active'=>'1'));
					$commentUserId = $this->api_model->getResultData(WALL_COMMENTS,'user_id',array('post_id'=>$post_id,'user_id !='=>$user_id,'is_active'=>'1'));
					if(!empty($likeUserId))
						$likeUserIdArr=$likeUserId;
					if(!empty($commentUserId))
						$commentUserIdArr=$commentUserId;
					$user_to_id=$likeUserIdArr+$commentUserIdArr;
					$user_to_id = array_unique($user_to_id,SORT_REGULAR);
					$notification=array(
						'user_from_id'=>$user_id,
						'user_to_id'=>$user_to_id,
						'noti_type'=>'2',
						'campaign_id'=>$postDtl->campaign_id,
						'post_id'=>$post_id,
						'msg'=>$name." commented on post ".$postDtl->post_desc,
					);
					//$this->notification($notification);
				
					$response['status']=true;
					$response['status_code']=200;
					$response['message']='success';
					$response['post_comment_id']=$comment_id;
				}
				else
				{
					$response['status']=false;
					$response['status_code']=300;
					$response['message']='No record exists.';
				}
			}
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
		
	}
	function wall_like(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$user_id='';$post_id='';$like='0';
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if(isset($this->data['post_id']))
				$post_id=$this->data['post_id'];
			if(isset($this->data['like']))
				$like=$this->data['like'];

			if($user_id=='' || $post_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id, Post Id And Like are required.';
			}
			else{
				$no_of_likes=(int)$no_of_likes = $this->api_model->getField(WALL_POSTS,'no_of_likes',array('id'=>$post_id));
				$likeId = $this->api_model->getField(WALL_LIKES,'id',array('post_id'=>$post_id,'user_id'=>$user_id));
				$is_activee = $this->api_model->getField(WALL_LIKES,'is_active',array('post_id'=>$post_id,'user_id'=>$user_id));
				if($like=='1'){
					$is_active	= '1';
					if($is_activee!=$is_active)
					$no_of_likes=($no_of_likes+1);

				}
				else if($like=='0')  
				{ 
					$is_active	= '0';
					if($is_activee!=$is_active && $no_of_likes>0)
					$no_of_likes=($no_of_likes-1);

				}
				if($likeId){ 
					$likeData = array(
								'post_id'		=> $post_id, 
								'user_id' 	 	=> $user_id,
								'is_active'     => $is_active,
								'modified_dttm'  => date('Y-m-d H:i:s'),
								);
					$like_id = $this->api_model->update(WALL_LIKES,$likeData,array('id'=>$likeId,'is_active !='=>$is_active));
					$like_id=$likeId;
				}
				else
				{
					$likeData = array(
						'post_id'		=> $post_id, 
						'user_id' 		=> $user_id,
						'is_active'     => $is_active,
						'created_dttm'  => date('Y-m-d H:i:s'),
					);
					$like_id = $this->api_model->insert(WALL_LIKES,$likeData);		
				}
				if($like_id)
				{	
					$update=$this->api_model->update(WALL_POSTS,array('no_of_likes'=>$no_of_likes),array('id'=>$post_id));
					if($like=='1'){
						$name = $this->api_model->getField(USERS,'name',array('id'=>$user_id));
						$postDtl = $this->api_model->getRowData(WALL_POSTS,'post_desc,campaign_id',array('id'=>$post_id));
						$user_to_id = $this->api_model->getResultData(WALL_LIKES,'user_id',array('post_id'=>$post_id,'user_id !='=>$user_id,'is_active'=>'1'));
						$notification=array(
							'user_from_id'=>$user_id,
							'user_to_id'=>$user_to_id,
							'noti_type'=>'1',
							'campaign_id'=>$postDtl->campaign_id,
							'post_id'=>$post_id,
							'msg'=>$name." like on post ".$postDtl->post_desc,
						);
						//$this->notification($notification);
					}
					$response['status']=true;
					$response['status_code']=200;
					$response['message']='success';
					$response['post_like_id']=$like_id;
					$response['total_likes']=$no_of_likes;
					$response['like']=$like;
				}
				else
				{
					$response['status']=false;
					$response['status_code']=300;
					$response['message']='error';
				}
			}
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}

	
	function campaign_interest(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$user_id='';$campaign_id='';$interested='';
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if(isset($this->data['campaign_id']))
				$campaign_id=$this->data['campaign_id'];
			if(isset($this->data['interested']))
				$interested=$this->data['interested'];

			if($user_id=='' || $campaign_id=='' || $interested==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id, Campaign Id  And Interested value  are required.';
			}
			else{
				$interestData = array(
					'campaign_id'	=> $campaign_id, 
					'user_id' 		=> $user_id,
					'is_active'		=> $interested,
					'created_dttm'  => date('Y-m-d H:i:s'),
					'modified_dttm' => date('Y-m-d H:i:s'),
				);
				$rowcount = $this->api_model->mysqlNumRows(USER_CAMP_INTERESTS,'id',array('campaign_id'=>$campaign_id,'user_id'=>$user_id));
				if($rowcount>0){
					$campaign_interest_id = $this->api_model->getField(USER_CAMP_INTERESTS,'id',array('campaign_id'=>$campaign_id,'user_id'=>$user_id));
					unset($interestData['created_dttm']);
				 	$interest_id = $this->api_model->update(USER_CAMP_INTERESTS,$interestData,array('id'=>$campaign_interest_id));
				}
				else
			    	$campaign_interest_id=$interest_id = $this->api_model->insert(USER_CAMP_INTERESTS,$interestData);
				if($interest_id)
				{	
					$response['status']=true;
					$response['status_code']=200;
					$response['message']='success';
					$response['campaign_interest_id']=$campaign_interest_id;
					$response['interested']=$interested;
				}
				else
				{
					$response['status']=false;
					$response['status_code']=300;
					$response['message']='error';
				}
			}
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
		
	}
	function request_sample(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$user_id='';$campaign_id='';$campaign_sample_id='';
			if(isset($this->data['campaign_id']))
				$campaign_id 		= $this->data['campaign_id'];
			if(isset($this->data['user_id']))
				$user_id     		= $this->data['user_id'];
			if(isset($this->data['campaign_sample_id']))
				$campaign_sample_id	= $this->data['campaign_sample_id'];

			if($campaign_sample_id=='' || $user_id=='' || $campaign_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='Campaign Sample Id, User Id And Camapign Id  are required.';
			}
			else{
				$rowcount = $this->api_model->mysqlNumRows(USER_SAMPLES,'id',array('campaign_id'=>$campaign_id,'campaign_sample_id'=>$campaign_sample_id,'user_id'=>$user_id,'status !='=>'2'));
				if($rowcount<1){
					$data = $this->db->select('is_user_consent')->from(USER_SAMPLES)->where(array('campaign_id'=>$campaign_id,'campaign_sample_id'=>$campaign_sample_id,'user_id'=>$user_id,'status'=>'2'))->order_by('id','desc')->get();
					$rowget = $data->row();
					$campaign_name = $this->api_model->getField(CAMPAIGNS,'campaign_name',array('id'=>$campaign_id));
					$name = $this->api_model->getField(USERS,'name',array('id'=>$user_id));
					
					$qr_code=$name.$user_id.$campaign_name;
					$qr_code= preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $qr_code));
					$filename=$this->qrcodeGenerator($qr_code);
					$qr_code_url=$filename?base_url().'assets/img/qrimage/'.$filename:'';
					$sampleData = array( 
						'campaign_sample_id'	=> $campaign_sample_id,
						'campaign_id'			=> $campaign_id, 
						'user_id' 				=> $user_id,
						'qr_code'				=> $qr_code,
						'qr_code_url' 			=> $qr_code_url,
						'is_user_consent'		=> @$rowget->is_user_consent,
						'unlocked_date'  		=> date('Y-m-d H:i:s'),
						'status'				=> '1',
						'is_active'				=> '1',
						'created_dttm' 			=> date('Y-m-d H:i:s'),
					);
					$UserSample_id = $this->api_model->insert(USER_SAMPLES,$sampleData);
					if($UserSample_id)
					{	
						$brnd_id = $this->api_model->getField(CAMPAIGNS,'brand_id',array('id'=>$campaign_id));
						$brnd_name = $this->api_model->getField(BRANDS,'brand_name',array('id'=>$brnd_id));
						$userSampleDtl = $this->api_model->getRowData(CAMPAIGN_SAMPLES,'start_date,end_date',array('id'=>$campaign_sample_id,'is_active='=>'1'));
						$qr_code_status = $this->api_model->mysqlNumRows(USER_SAMPLES,'qr_code_status',array('campaign_id'=>$campaign_id,'campaign_sample_id'=>$campaign_sample_id,'user_id'=>$user_id,'status !='=>'2')); 
						$response['status']=true;
						$response['status_code']=200;
						$response['message']='success'; 
						$response['is_user_consent']=(int)@$rowget->is_user_consent;
						$response['user_sample_id']="$UserSample_id";
						$response['qr_code_url']=$qr_code_url;
						$response['brand_name']=$brnd_name;
						$response['qr_code']=$qr_code;
						$response['camp_sample_start_date']=$userSampleDtl->start_date;
						$response['camp_sample_end_date']=$userSampleDtl->end_date;
						$response['qr_code_status']=$qr_code_status;
					}
					else
					{
						$response['status']=false;
						$response['status_code']=300;
						$response['message']='error';
					}
				}
				else
				{
					$response['status']=false;
					$response['status_code']=300;
					$response['message']='QRcode already generated for this sample.';
				}
			}
			
			$this->convertNullToEmpty($response); 
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	function users_sample_code_list(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$limit='100';$offset='0';$user_id='';
			if(isset($this->data['limit']))
				$limit=$this->data['limit'];
			if(isset($this->data['offset']))
				$offset=$this->data['offset'];
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];

			if($user_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id is required.';
			}
			else{
				$params['start'] = $offset!='' ? $offset:0;
				$params['limit'] = $limit!='' ? $limit:100;
				$cond="b.is_active='1' AND camp.is_active='1' AND campSample.is_active='1' AND uSample.is_active='1' AND  uSample.user_id='".$user_id."' AND uSample.status !='2' AND uSample.status !='4' ";
				$fields="uSample.*,camp.campaign_name,camp.total_campaign_samples_used,camp.total_campaign_samples,campSample.start_date,campSample.end_date,b.brand_logo_url,b.brand_name";
				$userSampleCodeData = $this->api_model->userSamplesList($fields,$cond,$params);
				$userSampleCodeList=array();
				if($userSampleCodeData){
					foreach ($userSampleCodeData as $value) {
						$brand_logo_url="";
						$qr_code_url="";
						if($value->brand_logo_url!='')
							$brand_logo_url=base_url().'assets/brand/logo/'.$value->brand_logo_url;
						if($value->qr_code_url){
							//$qr_code_url=base_url().'assets/img/qrimage/'.$value->qr_code_url;
							$qr_code_url=$value->qr_code_url;
						}


						$totalSampleUsed=$value->total_campaign_samples_used;
						$totalSample=$value->total_campaign_samples;
						if($totalSample>0){
							//$percent_left=ceil(100-(($totalSampleUsed*100)/$totalSample));
							$percent_left=floor(100-(($totalSampleUsed*100)/$totalSample));
							if($percent_left>0)
								$percent_left=$percent_left;
							else
								$percent_left=0;
							//if (is_float($percent_left))
						    //$percent_left=round($percent_left, 2);
						}
						else
							$percent_left=0;

						$newarray	= array(
							"campaign_id"			    => $value->campaign_id,
							"user_sample_id"			=> $value->id,
							"brand_logo_url" 			=> $brand_logo_url,
							"brand_name"				=> $value->brand_name,
							"campaign_name"				=> $value->campaign_name,
							"percent_left"				=> $percent_left,
							"campaign_sample_id"	    => $value->campaign_sample_id,
							"camp_sample_start_date"	=> $value->start_date,
							"camp_sample_end_date"		=> $value->end_date,
							"unlocked_date"		        => $value->unlocked_date,
							"qr_code_url" 			    => $value->qr_code_url,
							"status" 			        => $value->status,
							"is_user_consent" 			=> (int)$value->is_user_consent,

						);
						array_push($userSampleCodeList, $newarray);
					}
				}
				if($userSampleCodeList)
				{
					$response['status_code']=200;
					$response['message']='success';
					$response['status']=true;
					$response['userSampleCodeList']=$userSampleCodeList;
				}
				else
				{
					$response['status_code']=300;
					$response['status']=false;
					$response['message']='No record exists.';
				}
				
			}
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	function users_promo_code_list(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$limit='10';$offset='0';$user_id='';
			if(isset($this->data['limit']))
				$limit=$this->data['limit'];
			if(isset($this->data['offset']))
				$offset=$this->data['offset'];
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];

			if($user_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id is required.';
			}
			else{
				$params['start'] = $offset!='' ? $offset:0;
				$params['limit'] = $limit!='' ? $limit:10;
				$cond="b.is_active='1' AND camp.is_active='1' AND post.is_active='1' AND uPromo.is_active='1' AND  uPromo.user_id='".$user_id."' AND uPromo.status !='2' AND uPromo.status !='4' ";
				$userPromoCodeData = $this->api_model->userPromoCodeList($cond,$params);
			//	echo $this->db->last_query();die;
				$userPromoCodeList=array();
				if($userPromoCodeData){
					foreach ($userPromoCodeData as $k=>$value) {
						$brand_logo_url="";
						$qr_code_url="";
						if($value->brand_logo_url!='')
							$brand_logo_url=base_url().'assets/brand/logo/'.$value->brand_logo_url;
						if($value->qr_code_url)
							$qr_code_url=$value->qr_code_url;

						$totalSampleUsed=$value->total_campaign_samples_used;
						$totalSample=$value->total_campaign_samples;
						$newarray	= array(
							"description" 				=> $value->promo_desc,
							"promo_id"			        => $value->id,
							"post_id"			        => $value->post_id,
							"brand_logo_url" 			=> $brand_logo_url,
							"brand_name" 				=> $value->brand_name,
							"campaign_name"				=> $value->campaign_name,
							"unlocked_date"		        => $value->unlocked_date,
							"promocode_end_date"		=> $value->end_date,
							"qr_code_url" 			    => $value->qr_code_url,
							"status" 			        => $value->status,


						);
						array_push($userPromoCodeList, $newarray);
					}
				}
				if($userPromoCodeList)
				{
					$response['status_code']=200;
					$response['message']='success';
					$response['status']=true;
					$response['userPromoCodeList']=$userPromoCodeList;
				}
				else
				{
					$response['status_code']=300;
					$response['status']=false;
					$response['message']='No record exists.';
				}
				
			}
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	function users_sample_list(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$limit='100';$offset='0';$user_id='';
			if(isset($this->data['limit']))
				$limit=$this->data['limit'];
			if(isset($this->data['offset']))
				$offset=$this->data['offset'];
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];

			if($user_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id is required.';
			}
			else{
				$params['start'] = $offset!='' ? $offset:0;
				$params['limit'] = $limit!='' ? $limit:100;
				$cond="b.is_active='1' AND camp.is_active='1' AND campSample.is_active='1' AND uSample.is_active='1' AND  uSample.user_id='".$user_id."' AND uSample.status !='2' AND uSample.status !='4' AND ((STR_TO_DATE(camp.start_date, '%Y-%m-%d') <='".date('Y-m-d')."') AND (STR_TO_DATE(campSample.start_date, '%Y-%m-%d') <='".date('Y-m-d')."' ))";
				$fields="camp.*,uSample.id,uSample.campaign_id,uSample.campaign_sample_id,uSample.is_active,uSample.status,campSample.start_date as camp_sample_start_date,campSample.end_date as camp_sample_end_date,b.brand_logo_url,b.brand_name";
				$sampleListData = $this->api_model->userSamplesList($fields,$cond,$params);				
				$sampleList=array();
				if($sampleListData){
					foreach ($sampleListData as $value) {
						if($value->brand_logo_url!='')
							$user_photo= base_url().'assets/brand/logo/'.$value->brand_logo_url;
						$CampaignBanner=$this->api_model->getField(CAMPAIGNBANNERS,'banner_url',array('campaign_id'=>$value->campaign_id,'cover_image'=>'1','is_active'=>'1'));
						$primary_image='';
						if($CampaignBanner)
							$primary_image=base_url().'assets/campaign/banner/'.$CampaignBanner;
						$is_qrcode_used=0;
						$is_review=0;
						if($value->status=='3' || $value->status=='4' || $value->end_date < date('Y-m-d') || $value->camp_sample_end_date < date('Y-m-d')){
							$is_qrcode_used=1;

						}
						$review_id=$CampaignBanner=$this->api_model->getField(CAMPAIGNS,'review_id',array('id'=>$value->campaign_id));
						$totalReview = $this->api_model->mysqlNumRows(USER_REVIEW,'id',array('review_id'=>$review_id,'user_id'=>$user_id,'is_campaign_review'=>'1'));
						/*if($totalReview>0 && date('Y-m-d',strtotime($value->end_date)) >= date('Y-m-d'))
							$is_review=1;*/
						if($totalReview>0)
							$is_review=1;
						$rowcount = $this->api_model->mysqlNumRows(USER_SAMPLES,'id',array('campaign_sample_id'=>$value->campaign_sample_id,'user_id'=>$user_id,'status !='=>'2','status !='=>'4'));
						$requested=$rowcount>0 ? true:false;

						$newarray	= array(
							"primary_image"				=> $primary_image,
							"campaign_name"				=> $value->campaign_name,
							"start_date"				=> date('Y-m-d H:i:s',strtotime($value->start_date)),
							"end_date"					=> date('Y-m-d H:i:s',strtotime($value->end_date)),
							"rating"					=> $value->avg_rating,
							"id"	    			    => $value->id,
							"campaign_id"				=> $value->campaign_id,
							"campaign_sample_id"	    => $value->campaign_sample_id,
							"camp_sample_start_date"	=> $value->camp_sample_start_date,
							"camp_sample_end_date"		=> $value->camp_sample_end_date,
							"is_active"					=> $value->is_active,
							"is_qrcode_used"			=> $is_qrcode_used,
							"is_review"				    => $is_review,
							"requested"			        => $requested,
							"user_sample_status"		=> $value->status,
						);
						array_push($sampleList, $newarray);
					}
				}
				if($sampleList)
				{
					$response['status_code']=200;
					$response['message']='success';
					$response['status']=true;
					$response['total_samples']=@count($sampleList);
					$response['sampleList']=$sampleList;
				}
				else
				{
					$response['status_code']=300;
					$response['status']=false;
					$response['message']='No record exists.';
				}

			}
			
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
/*==============	Changes and pre client requirement	============		*/
	function users_sample_list_Jan_2020(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$limit='100';$offset='0';$user_id='';
			if(isset($this->data['limit']))
				$limit=$this->data['limit'];
			if(isset($this->data['offset']))
				$offset=$this->data['offset'];
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];

			if($user_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id is required.';
			}
			else{
				$params['start'] = $offset!='' ? $offset:0;
				$params['limit'] = $limit!='' ? $limit:100;
				$cond="b.is_active='1' AND camp.is_active='1' AND campSample.is_active='1' AND uSample.is_active='1' AND  uSample.user_id='".$user_id."' AND uSample.status !='2' AND uSample.status !='4' AND ((STR_TO_DATE(camp.start_date, '%Y-%m-%d') <='".date('Y-m-d')."' AND STR_TO_DATE(camp.end_date, '%Y-%m-%d') >='".date('Y-m-d')."') AND (STR_TO_DATE(campSample.start_date, '%Y-%m-%d') <='".date('Y-m-d')."' AND STR_TO_DATE(campSample.end_date, '%Y-%m-%d') >='".date('Y-m-d')."'))";
				$fields="camp.*,uSample.id,uSample.campaign_id,uSample.campaign_sample_id,uSample.is_active,uSample.status,campSample.start_date as camp_sample_start_date,campSample.end_date as camp_sample_end_date,b.brand_logo_url,b.brand_name";
				$sampleListData = $this->api_model->userSamplesList($fields,$cond,$params);				
				$sampleList=array();
				if($sampleListData){
					foreach ($sampleListData as $value) {
						if($value->brand_logo_url!='')
							$user_photo= base_url().'assets/brand/logo/'.$value->brand_logo_url;
						$CampaignBanner=$this->api_model->getField(CAMPAIGNBANNERS,'banner_url',array('campaign_id'=>$value->campaign_id,'cover_image'=>'1','is_active'=>'1'));
						$primary_image='';
						if($CampaignBanner)
							$primary_image=base_url().'assets/campaign/banner/'.$CampaignBanner;
						$is_qrcode_used=0;
						$is_review=0;
						if($value->status=='3' || $value->status=='4'){
							$is_qrcode_used=1;

						}
						$review_id=$CampaignBanner=$this->api_model->getField(CAMPAIGNS,'review_id',array('id'=>$value->campaign_id));
						$totalReview = $this->api_model->mysqlNumRows(USER_REVIEW,'id',array('review_id'=>$review_id,'user_id'=>$user_id,'is_campaign_review'=>'1'));
						if($totalReview>0 && date('Y-m-d',strtotime($value->end_date)) >= date('Y-m-d'))
							$is_review=1;
						$rowcount = $this->api_model->mysqlNumRows(USER_SAMPLES,'id',array('campaign_sample_id'=>$value->campaign_sample_id,'user_id'=>$user_id,'status !='=>'2','status !='=>'4'));
						$requested=$rowcount>0 ? true:false;

						$newarray	= array(
							"primary_image"				=> $primary_image,
							"campaign_name"				=> $value->campaign_name,
							"start_date"				=> date('Y-m-d H:i:s',strtotime($value->start_date)),
							"end_date"					=> date('Y-m-d H:i:s',strtotime($value->end_date)),
							"rating"					=> $value->avg_rating,
							"id"	    			    => $value->id,
							"campaign_id"				=> $value->campaign_id,
							"campaign_sample_id"	    => $value->campaign_sample_id,
							"camp_sample_start_date"	=> $value->camp_sample_start_date,
							"camp_sample_end_date"		=> $value->camp_sample_end_date,
							"is_active"					=> $value->is_active,
							"is_qrcode_used"			=> $is_qrcode_used,
							"is_review"				    => $is_review,
							"requested"			        => $requested,
							"user_sample_status"		=> $value->status,
						);
						array_push($sampleList, $newarray);
					}
				}
				if($sampleList)
				{
					$response['status_code']=200;
					$response['message']='success';
					$response['status']=true;
					$response['total_samples']=@count($sampleList);
					$response['sampleList']=$sampleList;
				}
				else
				{
					$response['status_code']=300;
					$response['status']=false;
					$response['message']='No record exists.';
				}

			}
			
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	function review_questions(){
		//ob_start();
		//print_r($_REQUEST);die;
		//$_POST = json_decode(file_get_contents("php://input"), true); 
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$user_id='';$campaign_id='';
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if(isset($this->data['campaign_id']))
				$campaign_id=$this->data['campaign_id'];

			if($user_id=='' || $campaign_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id And campaign Id are required.';
			}
			else{

				$review_id=$this->api_model->getField(CAMPAIGNS,'review_id',array('id' =>  $campaign_id));
				//$review_id=$this->api_model->getField(CAMPAIGNS,'review_id',array('id' =>  $campaign_id));
				$con=array("review_id"=>$review_id,"user_id"=>$user_id,"is_campaign_review"=>'1');
				$countReview = $this->api_model->mysqlNumRows(USER_REVIEW,'id',$con);
				//echo $this->db->last_query();
				
				/*if($countReview > 0)
				{
					$response['status']=false; 
					$response['status_code']=300;
					$response['message']='Review already given.';
				}
				else {*/
					$cond="reviewQuest.is_active='1' AND camp.id='".$campaign_id."'";
					$ReviewQuestionData = $this->api_model->reviewQuestions('reviewQuest.*',$cond);
					$ReviewQuestionList=array();
					if($ReviewQuestionData){
						foreach ($ReviewQuestionData as $value) {
							$ReviewAnswerOptionList=array();
							$ReviewAnswerOptionData = $this->api_model->getResultData(REVIEW_ANSWER_OPTIONS,'*',array('question_id'=>$value->id,'is_active'=>'1'),'ans_order','ASC');
							if($ReviewAnswerOptionData){
								foreach ($ReviewAnswerOptionData as $key => $values) {
									$newarray1	= array(
										"id"			=> $values->id,
										"answer_text"	=> $values->answer_text,
									);
									array_push($ReviewAnswerOptionList, $newarray1);
								}
							}
							$newarray	= array(
								"id"			    	=> $value->id,
								"ques_text"				=> $value->ques_text,
								"ques_type"		        => $value->ques_type,
								"ques_answer"		    => $ReviewAnswerOptionList,
							);
							array_push($ReviewQuestionList, $newarray);
						}
					}
					if($ReviewQuestionList)
					{
						$response['status_code']=200;
						$response['message']='success';
						$response['status']=true;
						$response['ReviewQuestionList']=$ReviewQuestionList;
					}
					else
					{
						$response['status_code']=300;
						$response['status']=false;
						$response['message']='No record exists.';
					}
			//	}
				
			}
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
	}
	function submit_review(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$user_id='';$campaign_id='';$rating='';$question_id='';$answer_id='';$answer_text='';$review_text='';$post_id='';$answerDetail='';$interested='';
			if(isset($this->data['user_id']) && $this->data['user_id']!='')
				$user_id=$this->data['user_id'];

			if(isset($this->data['campaign_id']) && $this->data['campaign_id']!='')
				$campaign_id=$this->data['campaign_id'];

			if(isset($this->data['rating']) && $this->data['rating']!='')
				$rating=$this->data['rating'];

			if(isset($this->data['question_id']) && $this->data['question_id']!='')
				$question_id=$this->data['question_id'];

			if(isset($this->data['answer_id']) && $this->data['answer_id']!='')
				$answer_id=$this->data['answer_id'];

			if(isset($this->data['answer_text']) && $this->data['answer_text']!='')
				$answer_text=$this->data['answer_text'];

			if(isset($this->data['review_text']) && $this->data['review_text']!='')
				$review_text=$this->data['review_text'];

			if(isset($this->data['post_id']) && $this->data['post_id']!='')
				$post_id=$this->data['post_id'];

			if(isset($this->data['answerDetail']) && $this->data['answerDetail']!='')
				$answerDetail=$this->data['answerDetail'];
			if(isset($this->data['interested']))
				$interested=$this->data['interested'];

			if($user_id=='' || $campaign_id=='' || $rating=='' || $answerDetail==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id, Campaign Id, Rating and  Answer details are required.';
			}
			else{
				$is_campaign_review=$post_id ? 0:1;
				$review_id=$this->api_model->getField(CAMPAIGNS,'review_id',array('id' =>  $campaign_id));
				$userReviewData = array(
					'review_id'				=> $review_id, 
					'user_id' 				=> $user_id,
					'is_campaign_review'	=> $is_campaign_review, 
					'review_text' 			=> $review_text,
					'rating' 				=> $rating,
					'is_published'			=> '1',
					'is_active'				=> '1',
					'created_dttm'  		=> date('Y-m-d H:i:s'),
					'modified_dttm' 		=> date('Y-m-d H:i:s'),
				);
				$user_review_id=$this->api_model->insert(USER_REVIEW,$userReviewData);
				//update avg rating
				$this->updateCampaignRating($campaign_id);
				if($answerDetail){
					foreach ($answerDetail as $key => $value) {
						$question_id=$value['question_id'];
						$ques_text=$value['ques_text'];
						$ques_type=$value['ques_type'];
						$ques_answer=$value['ques_answer'];
						foreach ($ques_answer as $values) {
							$answer_id=$values['id'];
							$answer_text=$values['answer_text'];
							$userReviewAnsData = array(
								'user_id' 				=> $user_id,
								'review_id'				=> $user_review_id, 
								'question_id'			=> $question_id, 
								'answer_id' 			=> $answer_id,
								'answer_text' 			=> $answer_text,
								'is_active'				=> '1',
								'created_dttm'  		=> date('Y-m-d H:i:s'),
								'modified_dttm' 		=> date('Y-m-d H:i:s'),
							);
							$this->api_model->insert(USER_REVIEW_ANS,$userReviewAnsData);
						}
					}
				}
				
				if($user_review_id)
				{
					$response['status']=true;
					$response['status_code']=200;
					$response['message']='success';
					$response['user_review_id']=$user_review_id;
					if($post_id!=''){
						$promocode = $this->api_model->mysqlNumRows(USER_PROMOCODES,'id',array('post_id'=>$post_id,'user_id'=>$user_id,'is_active'=>'1','status !='=>'2'));
						if($promocode<1){
							$wallPost = $this->api_model->getRowData(WALL_POSTS,'qr_code_url,promo_end_date',array('id'=>$post_id));
							$userPromoCodeData=array(
								'user_id'			=> $user_id,
								'campaign_id'		=> $campaign_id,
								'post_id'			=> $post_id,
								'qr_code_url'		=> $wallPost->qr_code_url,
								'end_date'	        => $wallPost->promo_end_date,
								'unlocked_date'		=> date('Y-m-d H:i:s'), 
								'status'			=> '1', 
								'is_active'			=> '1',
								'created_dttm'		=> date('Y-m-d H:i:s')
							);
							$this->api_model->insert(USER_PROMOCODES,$userPromoCodeData);

							$promoCodeData	= array(
								"qr_code"			=> array('type'=>'qrcode','code'=>$wallPost->qr_code_url),
								"end_date"			=> $wallPost->promo_end_date,
								"description"		=> '',
							);
							$response['promo_code_details']=$promoCodeData;
						}
						else
						{
							$response['status']=false;
							$response['status_code']=300;
							$response['message']='Promocode already generated for this Post.';
						}

					}

				}
				else
				{
					$response['status']=false;
					$response['status_code']=300;
					$response['message']='Something went wrong.';
				}
			}

			
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		else
		{
			echo 'Access Denied';die;
		}
		
	}
	public function delete_sample_code(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		$sample_id='';
		if(isset($this->data['sample_id']))
			$sample_id=$this->data['sample_id'];
		if($sample_id==''){
			$response['status']=false; 
			$response['status_code']=300;
			$response['message']='Sample Id is required.';
		}
		else{
			$sampleStaus=$this->api_model->getField(USER_SAMPLES,'status',array('id'=>$sample_id));
			if($sampleStaus=='3')
				$status='4';
			else
				$status='2';
				$save=array(
					'status'=>$status,
					'modified_dttm'=>date('Y-m-d H:i:s'),
				);
			$update=$this->api_model->update(USER_SAMPLES,$save,array('id'=>$sample_id));
			if($update){
				$response['status']=true;
				$response['status_code']=200;
				$response['message']='Sample Code deleted successfully.';
			}
			else{
				$response['status']=true;
				$response['status_code']=200;
				$response['message']='Sample Code not deleted.';
			}
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}
	public function delete_promo_code(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		$promo_code_id='';
		if(isset($this->data['promo_code_id']))
			$promo_code_id=$this->data['promo_code_id'];
		if($promo_code_id==''){
			$response['status']=false; 
			$response['status_code']=300;
			$response['message']='Promo Id is required.';
		}
		else{
			$promoStatus=$this->api_model->getField(USER_PROMOCODES,'status',array('id'=>$promo_code_id));
			if($promoStatus=='3')
				$status='4';
			else
				$status='2';
				$save=array(
					'status'=>$status,
					'modified_dttm'=>date('Y-m-d H:i:s'),
				);
			$update=$this->api_model->update(USER_PROMOCODES,$save,array('id'=>$promo_code_id));
			if($update){
				$response['status']=true;
				$response['status_code']=200;
				$response['message']='Promo Code  deleted successfully.';
			}
			else{
				$response['status']=true;
				$response['status_code']=200;
				$response['message']='Promo Code  not deleted.';
			}
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}
	function intrest_qsn_list(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		$user_id='';
		if(isset($this->data['user_id']))
			$user_id=$this->data['user_id'];
		$intrst_master=$this->api_model->getResultData(INTEREST_MASTER,'*',array('is_active'=>'1'));
		$details=array();
		if($intrst_master){
			foreach($intrst_master as $k=>$master)
			{
				$intrst_option=$this->api_model->getResultData(INTEREST_OPTIONS,'*',array('is_active'=>'1','interest_id'=>$master->id));
				$details[$k]['question_id'] = $master->id;
				$details[$k]['ques_text']   = $master->interest_title;
				$details[$k]['ques_type']   = $master->interest_type;
				$optionp=array();
				if($intrst_option){
					foreach($intrst_option as $p=>$option)
					{
						$is_count =$this->api_model->mysqlNumRows(USER_INTEREST_OPTIONS,'id',array('user_id'=>$user_id,'interest_id'=>$master->id,'option_id'=>$option->id));
						$optionp[$p]['id']         = $option->id;
						$optionp[$p]['answer_text']= $option->option_text;
						if($is_count==0)
							$optionp[$p]['is_selected']=0;
						else
							$optionp[$p]['is_selected']=1;
					}
				}
				$details[$k]['ques_answer']=$optionp;
			}
		}
		if(!empty($details))
		{
			$response['status']=true;
			$response['status_code']=200;
			$response['Output']=$details;
			$response['message']='Success';
		}
		else
		{
			$response['status']=false;
			$response['status_code']=300;
			$response['Output']=array();
			$response['message']='No record found';
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}
	function save_intrest(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$answerDetail='';$user_id='';
			if(isset($this->data['answerDetail']))
				$answerDetail=$this->data['answerDetail'];
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if($answerDetail){
				foreach($answerDetail as $answer)
				{
					$uIntrest_id =$this->api_model->getField(USER_INTERESTS,'id',array('user_id'=>$user_id,'interest_id'=>$answer['question_id']));
					$saveInt=array(
						'id'=>$uIntrest_id,
						'user_id'=>$user_id,
						'interest_id'=>$answer['question_id'],
						'is_active'=>'1',
						'created_dttm'=>date('Y-m-d H:i:s'),
						'modified_dttm'=>date('Y-m-d H:i:s'),
					);
					if($uIntrest_id=='')
					{
						unset($saveInt['id']);
						$intrest_id=$this->api_model->insert(USER_INTERESTS,$saveInt);
					}
					else
					{
						unset($saveInt['created_dttm']);
						if(!empty($answer['ques_answer']))
						$this->api_model->update(USER_INTERESTS,$saveInt,array('id'=>$uIntrest_id));
						if(empty($answer['ques_answer']))
						{
							$this->api_model->delete(USER_INTERESTS,array('user_id'=>$user_id,'interest_id'=>$answer['question_id']));
						}
						$this->api_model->delete(USER_INTEREST_OPTIONS,array('user_id'=>$user_id,'interest_id'=>$answer['question_id']));
						$intrest_id=$uIntrest_id;
					}
					foreach($answer['ques_answer'] as $optin)
					{
						$save=array(
							'user_id'=>$user_id,
							'interest_id'=>$answer['question_id'],
							'option_id'=>$optin['id'],
							'is_active'=>'1',
							'created_dttm'=>date('Y-m-d H:i:s'),
							'modified_dttm'=>date('Y-m-d H:i:s'),

						);
						$intrest_id=$this->api_model->insert(USER_INTEREST_OPTIONS,$save);
					}
				}
			}
			$this->updateProfilePercent($user_id);
			$response['status']=true;
			$response['status_code']=200;
			$response['message']='Success';
			
			$this->convertNullToEmpty($response);
			echo json_encode($response);die;
		}
		
	}	
	function rate_us(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data))
		{
			$user_id='';$rating='';$comment='';
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if(isset($this->data['rating']))
				$rating=$this->data['rating'];
			if(isset($this->data['comment']))
				$comment=$this->data['comment'];
			if($user_id!='' && $rating!='' && $comment!=''){
				$save=array(
						'user_id'=>$user_id,
						'rating'=>$rating,
						'comment'=>$comment,
						'created_dttm'=>date('Y-m-d H:i:s'),
						'modified_dttm'=>date('Y-m-d H:i:s'),

					);
				$this->api_model->insert(APPRATES,$save);
				$response['status']=true;
				$response['status_code']=200;
				$response['message']='Thanks for your feedback.';
				
			}
			else
			{
				$response['status']=false;
				$response['status_code']=300;
				$response['message']='User Id, rating and comment are required.';
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}
	public function contact_us(){
		
		$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($this->data)){ 
			$user_id='';$user_name='';$user_email='';$comment='';
			if(isset($this->data['user_id']))
				$user_id=$this->data['user_id'];
			if(isset($this->data['user_name']))
				$user_name=$this->data['user_name'];
			if(isset($this->data['user_email']))
				$user_email=$this->data['user_email'];
			if(isset($this->data['comment']))
				$comment=$this->data['comment'];

			if($user_id!='' && $user_name!='' && $user_email!='' && $comment!=''){
		        $contactData=array(
				    	"user_id" 			=>	$user_id,
				    	"user_name" 	 	=>	$user_name,
				    	"user_email" 		=>	$user_email,
				    	"comment" 	 		=>	$comment,
						'created_dttm'		=>  date('Y-m-d H:i:s'),
						'modified_dttm'		=>  date('Y-m-d H:i:s'),
		    	);
			    $this->api_model->insert(CONTACTS,$contactData);
			    $subject ="iSamplez Contact Us ";
				$data['name']='Admin';
				$data['message']='A new contact message from iSamplez.<br/>Name: '.$user_name.' <br/>Email: '.$user_email.' <br/><br/>Message: '.$comment.' ';
				$message=$this->load->view('emailer/emailer_normal',$data,true);
				$mailResponse=sendMail(ADMINMAIL,$subject,$message,'',$user_email);
				if($mailResponse)
		        {   
		        	$response['status']=true;
			        $response['status_code']=200;
			        $response['message']="Thanks you for your feedback. Our team will get back to you within 72 hours.";
					echo json_encode($response);
		        }
		        else
		        {
			        $response['status']=false;
			    	$response['status_code']=300;
					$response["message"] = "Your message not send.Please try again.";
					echo json_encode($response);
				}
		    }
		    else
		    {
		    	$response['status']=false;
		    	$response['status_code']=300;
		    	$response['message']='User Id, UserName, User Email and  Comment are required.';
		    }
			
		}
		else
		{
			echo 'Access Denied';die;
		}   
	}
	public function validateQRCode(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		$data=$this->data['request']; //$data['sample_QR_code']='605101St-Ives-Apricot-scrub';
		//file_put_contents('/var/www/html/log.txt',"Kinesis dev Process mchin ".json_encode($data).' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
		if(!empty($data))
		{
			$sample=$this->api_model->getRowData(USER_SAMPLES,'id,campaign_id,user_id,status',array('qr_code'=> $data['sample_QR_code'],'is_active'=> '1' ,'status !='=> '2'));
			if(@count($sample)>0){

				$device_token = $this->api_model->getField(USERS_DEVICE_DTL,'device_token',array('user_id'=>$sample->user_id));
			 	if($sample->status=="1"){
					$authorised_code=$this->generateRandomString();
					$save=array('authorised_code'=>$authorised_code,'modified_dttm'=>date('Y-m-d H:i:s'));
					$this->api_model->update(USER_SAMPLES,$save,array('id'=>$sample->id));
					$this->updateQRCodeStatus($sample->id,'1');
					$msg="Congrats! QR code sucessfully verified. Now, you can give a rate & review.";

					$response['status']=true; 
					$response['status_code']=200;
					$response['message']='Valid code';
					$response['authorised_code']=$authorised_code; 
					//$response['vending_machine_code_status']="Valid code";
					
					//file_put_contents('/var/www/html/log.txt',"Kinesis dev Process mchin1 ".json_encode($data).' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
				}
				else
				{
					//status 3 and 4

					$this->updateQRCodeStatus($sample->id,'2');
					$msg="Alert! QR code has been already used. QR codes can only be used once.";
					$response['status']=false; 
					$response['status_code']=300;
					$response['message']='Invalid code as code has been already used';
					//$response['vending_machine_code_status']="Invalid code as code has been already used";
				//	file_put_contents('/var/www/html/log.txt',"Kinesis dev Process mchin2 ".json_encode($data).' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
				}
			 	//start push notification # 08Nov2019
				if($device_token){
					$notificationList	= array(
						"user_from_id"			=> 0,
						"user_to_id"			=> $sample->user_id,
						"noti_type" 			=> '5',
						"campaign_id"			=> $sample->campaign_id,
						"msg" 					=> $msg,
						"is_view" 				=> '0',
						"is_active" 			=> '1',
						"created_dttm" 			=> date('Y-m-d:H:i:s'),
						"modified_dttm" 		=> date('Y-m-d:H:i:s'),

					);
					$notification_id=$this->api_model->insert(NOTIFICATIONS,$notificationList);
					$totalNotification = $this->api_model->mysqlNumRows(NOTIFICATIONS,"id",array('user_to_id'=>$sample->user_id,'is_view'=>'0'));
					$data	= array(
		        					"notification_id"   	=> $notification_id,
									"campaign_id"		   	=> $sample->campaign_id,
									"type" 					=> "5",
						          );
			 		push_notification($device_token,$data,$msg,$totalNotification);
			 	}
			 	//end push notification

			}
			else{
				//file_put_contents('/var/www/html/log.txt',"Kinesis dev Process mchin3 ".json_encode($data).' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='Invalid code as code is fraudulent';
				//$response['vending_machine_code_status']="Invalid code as code is fraudulent";
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}
public function updateQRCodeStatus($sample_id,$status){
	$this->api_model->update(USER_SAMPLES,array('qr_code_status'=>$status),array('id'=>$sample_id));
}
	public function sampleDispenseStatus(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		$data=$this->data['request'];
		//$data['authorised_code']='ZknivBn5YN';$data['vending_machine_id']='16418';$data['dispenseStatus']='1';
		
		//file_put_contents('/var/www/html/log.txt',"Kinesis dev Process dispanse ".json_encode($data).' '.@$verify_token."\n",FILE_APPEND | LOCK_EX);
		if(!empty($data))
		{
			$authorised_code='';
			$vending_machine_id='';
			$vending_machine_code='';
			$dispenseStatus='';
			/*if(isset($data['vending_machine_id']) && $data['vending_machine_id']!='')
				$vending_machine_id=$data['vending_machine_id'];*/
				//here vending_machine_id  is used as vending_machine_code available in db 
			if(isset($data['vending_machine_id']) && $data['vending_machine_id']!='')
				$vending_machine_code=$data['vending_machine_id'];
			if(isset($data['authorised_code']) && $data['authorised_code']!='')
				$authorised_code=$data['authorised_code'];
			if(isset($data['dispenseStatus']) && $data['dispenseStatus']!='')
				$dispenseStatus=$data['dispenseStatus'];
			if($authorised_code=='' || $dispenseStatus=='' || $vending_machine_code==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']=' Authorised Code,Vending Machine Id And Dispense Status are required.';
			}
			else{
				//check the code is used or not
				$vending_machine_id=$this->api_model->getField(VENDING_MACHINES,'id',array('vending_machine_code'=>$vending_machine_code));
				$userSample=$this->api_model->getRowData(USER_SAMPLES,'campaign_id,id,user_id,status,qr_code',array('authorised_code'=> $data['authorised_code'],'is_active'=> '1','status'=> '1'));
				//echo $this->db->last_query();die;
				//print_r($userSample);die;
				if(@count($userSample)>0){
					$campaign_id=$userSample->campaign_id;
					//$totalReqsample =$this->api_model->mysqlNumRows(USER_SAMPLES,'id',array('campaign_id'=>$campaign_id));
					/*if($dispenseStatus=='1'){*/
						//$stockDtl=$this->api_model->getRowData(CAMPAIGN_VENDS,'*',array('campaign_id'=>$campaign_id,'vend_machine_id'=>$vending_machine_id));
						$stockDtl=$this->api_model->getRowData(CAMPAIGN_VENDS,'*',array('campaign_id'=>$campaign_id,'vend_machine_id'=>$vending_machine_id));
						//$stockDtl=$this->api_model->getData(CAMPAIGN_VENDS,'*',array('vend_machine_id'=>$vending_machine_id));
						//print_r($stockDtl);die;
						if(@count($stockDtl)>0){
							if($stockDtl->campaign_id==$campaign_id){
								$total_sample_available=$stockDtl->vend_no_of_samples;
								//$total_sample_available=$stockDtl->vend_no_of_samples-$stockDtl->vend_no_of_sample_used;
								if($total_sample_available>0){ 
									$this->manageSMS($userSample->user_id);
									$uSampleData=array(
										    	"status" 			=>	'3',
												'modified_dttm'		=>  date('Y-m-d H:i:s'),
								    	);
								    $sampleUpateSttaus=$this->api_model->update(USER_SAMPLES,$uSampleData,array('id'=>$userSample->id));
									if($sampleUpateSttaus){
										$campData=array(
										    	"vend_machine_id" 				=>	$vending_machine_id,
										    	"campaign_id" 					=>	$campaign_id,
										    	"vend_no_of_available_sample" 	=>	($total_sample_available-1),
										    	"vend_no_of_sample_used" 	  	=>	($stockDtl->vend_no_of_sample_used+1),
												'modified_dttm'		=>  date('Y-m-d H:i:s'),
								    	);

								        $this->api_model->update(CAMPAIGN_VENDS,$campData,array('id'=>$stockDtl->id));
								        $total_campaign_samples_used=$this->api_model->getField(CAMPAIGNS,"total_campaign_samples_used",array('id'=>$campaign_id));
								        
								        $save = array(
											'total_campaign_samples_used'	=> $total_campaign_samples_used+1,
										);
										$this->api_model->update(CAMPAIGNS,$save,array('id'=>$campaign_id));
										//$this->updateCampaignStock($campaign_id);
									}

								}
								else
								{
									$response['status']=false; 
									$response['status_code']=300;
									$response['message']='In stock, no sample available.';
									
									$this->convertNullToEmpty($response);
									echo json_encode($response);die;
								}
							}
							else{
								$this->updateQRCodeStatus($userSample->id,'4');
								$device_token = $this->api_model->getField(USERS_DEVICE_DTL,'device_token',array('user_id'=>$userSample->user_id));
								//start push notification # 08Nov2019
								if($device_token){
									$msg='Alert! This is a valid QR code but not for this machine at this time. Please check iSamplez app for details on all campaign and machine validities.';
									$notificationList	= array(
										"user_from_id"			=> 0,
										"user_to_id"			=> $userSample->user_id,
										"noti_type" 			=> '8',
										"campaign_id"			=> $userSample->campaign_id,
										"msg" 					=> $msg,
										"is_view" 				=> '0',
										"is_active" 			=> '1',
										"created_dttm" 			=> date('Y-m-d:H:i:s'),
										"modified_dttm" 		=> date('Y-m-d:H:i:s'),

									);
									$notification_id=$this->api_model->insert(NOTIFICATIONS,$notificationList);
									$totalNotification = $this->api_model->mysqlNumRows(NOTIFICATIONS,"id",array('user_to_id'=>$userSample->user_id,'is_view'=>'0'));
									$data	= array(
						        					"notification_id"   	=> $notification_id,
													"campaign_id"		   	=> $userSample->campaign_id,
													"type" 					=> "8",
										          );
							 		push_notification($device_token,$data,$msg,$totalNotification);
							 	}
							 	//end push notification
								$response['status']=false; 
								$response['status_code']=300;
								$response['message']='Code is valid but for a different campaign';
								//$response['vending_machine_code_status']="code is valid but for a different campaign.";
								$this->convertNullToEmpty($response);
								echo json_encode($response);die;	
							}
						}
						else
						{
							$response['status']=false; 
							$response['status_code']=300;
							$response['message']='In stock, no sample available.';
							
							$this->convertNullToEmpty($response);
							echo json_encode($response);die;	
						}
						$response['status']=true; 
						$response['status_code']=200;
						$response['message']='Dispense Status updated successfully.';
						$response['authorised_code']=$data['authorised_code'];
					/*}
					else{
						$response['status']=false; 
						$response['status_code']=300;
						$response['message']='Dispense Status not updated successfully.';
					}*/


				}
				else{
					$response['status']=false; 
					$response['status_code']=300;
					$response['message']='Invalid Authorised Code.';
				}
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;

	}
	public function sampleDispenseStatusOld(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		$data=$this->data['request'];
		if(!empty($data))
		{
			$authorised_code='';
			$vending_machine_id='';
			$dispenseStatus='';
			if(isset($data['vending_machine_id']) && $data['vending_machine_id']!='')
				$vending_machine_id=$data['vending_machine_id'];
			if(isset($data['authorised_code']) && $data['authorised_code']!='')
				$authorised_code=$data['authorised_code'];
			if(isset($data['dispenseStatus']) && $data['dispenseStatus']!='')
				$dispenseStatus=$data['dispenseStatus'];
			if($authorised_code=='' || $dispenseStatus=='' || $vending_machine_id==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']=' Authorised Code,Vending Machine Id And Dispense Status are required.';
			}
			else{
				//check the code is used or not
				$userSample=$this->api_model->getRowData(USER_SAMPLES,'campaign_id,id,user_id',array('authorised_code'=> $data['authorised_code'],'is_active'=> '1','status'=> '1'));
				if(@count($userSample)>0){
					$campaign_id=$userSample->campaign_id;
					$totalReqsample =$this->api_model->mysqlNumRows(USER_SAMPLES,'id',array('campaign_id'=>$campaign_id));
					if($dispenseStatus=='1'){
						$stockDtl=$this->api_model->getRowData(CAMPAIGN_VENDS,'*',array('campaign_id'=>$campaign_id,'vend_machine_id'=>$vending_machine_id));

						if(@count($stockDtl)>0){
							$total_sample_available=$stockDtl->vend_no_of_samples-$stockDtl->vend_no_of_sample_used;
							//$total_sample_available=$stockDtl->vend_no_of_samples-$stockDtl->vend_no_of_sample_used;
							if($total_sample_available>0){
								$this->manageSMS($userSample->user_id);
								$uSampleData=array(
									    	"status" 			=>	'3',
											'modified_dttm'		=>  date('Y-m-d H:i:s'),
							    	);
							    $sampleUpateSttaus=$this->api_model->update(USER_SAMPLES,$uSampleData,array('id'=>$userSample->id));
								if($sampleUpateSttaus){
									$campData=array(
									    	"vend_machine_id" 				=>	$vending_machine_id,
									    	"campaign_id" 					=>	$campaign_id,
									    	"vend_no_of_available_sample" 	=>	($total_sample_available-1),
									    	"vend_no_of_sample_used" 	  	=>	($stockDtl->vend_no_of_sample_used+1),
											'modified_dttm'		=>  date('Y-m-d H:i:s'),
							    	);

							        $this->api_model->update(CAMPAIGN_VENDS,$campData,array('id'=>$stockDtl->id));
									$this->updateCampaignStock($campaign_id);
								}

							}
							else
							{
								$response['status']=false; 
								$response['status_code']=300;
								$response['message']='In stock, no sample available.';
								
								$this->convertNullToEmpty($response);
								echo json_encode($response);die;
							}
						}
						else
						{
							$response['status']=false; 
							$response['status_code']=300;
							$response['message']='In stock, no sample available.';
							
							$this->convertNullToEmpty($response);
							echo json_encode($response);die;	
						}
						$response['status']=true; 
						$response['status_code']=200;
						$response['message']='Dispense Status updated successfully.';
						$response['authorised_code']=$data['authorised_code'];
					}
					else{
						$response['status']=false; 
						$response['status_code']=300;
						$response['message']='Dispense Status not updated successfully.';
					}


				}
				else{
					$response['status']=false; 
					$response['status_code']=300;
					$response['message']='Invalid Authorised Code.';
				}
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;

	}
	function manageSMS($user_id=null)
	{
		$chek_user = $this->api_model->getRowData(USERS,array('id','phone','is_sms_sample_use','smsqr_code_url'),array('id'=>$user_id));
		if($chek_user->is_sms_sample_use==1 && $chek_user->smsqr_code_url!='')
		{
			$appDownload='https://isamplez.com/';
			$msg='Download iSamplez app for other sample campaigns and promo codes. '.$appDownload ;
			$mobileno=explode('+',$chek_user->phone);
			$mobile=$mobileno[1];
			$this->sendSMS($mobile,$msg);
			$detl=file_get_contents($sms);
			$userData = array('is_sms_sample_use'=>2,'modified_dttm'=> date('Y-m-d H:i:s'));
			$this->api_model->update(USERS,$userData,array('id'=>$user_id));
		}
		return 'success';
	}
	function sendSMS($mobile=null,$msg=null)
	{
		$username='zettalyte';$passwd='N7sjwsh5';$msg=urlencode($msg);
		$callerid='';$trackid='iSamplez'.rand(0,2).time();
		$sms="http://www.sendquickasp.com/client_api/index.php?username=$username&passwd=$passwd&tar_num=$mobile&tar_msg=$msg&callerid=iSamplez&route_to=api_send_sms&merchantid=AppSMS1&trackid=$trackid&status_url=https://isamplez.com/ChatBot/getstatusBack";
		$detl=file_get_contents($sms);
		return $detl;
	}
	public function updateInventoryStatus(){
		$this->data=json_decode(file_get_contents("php://input"), true);
		$data=$this->data['request'];
		if(!empty($data))
		{
			$vending_machine_id='';
			$campaign_id='';
			$total_sample='';
			if(isset($data['vending_machine_id']) && $data['vending_machine_id']!='')
				$vending_machine_code=$data['vending_machine_id'];
			if(isset($data['total_sample']) && $data['total_sample']!='')
				$total_sample=$data['total_sample'];

			if($vending_machine_code=='' || $total_sample==''){
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='Vending Machine Id And Total Sample Available are required.';
			}
			else{  
				//$totalReqsample = $this->UserSample->find('count', array('recursive'=>'-1','conditions'=>array('UserSample.campaign_id'=>$campaign_id))); 
				//$totalReqsample=1;
				//$totalReqsample=$this->api_model->mysqlNumRows('user_samples','*',array('campaign_id'=>$campaign_id));
				$vending_machine_id=$this->api_model->getField(VENDING_MACHINES,'id',array('vending_machine_code'=>$vending_machine_code));
				//$stockDtl=$this->api_model->getRowData(CAMPAIGN_VENDS,'*',array('vend_machine_id'=>$vending_machine_id));
				$campDtl=$this->api_model->getResultData(CAMPAIGN_VENDS,'*',array('vend_machine_id'=>$vending_machine_id));
				//print_r($campDtl);die;
				if(!empty($campDtl))
				{
					//echo 4545;die;
					foreach ($campDtl as $cmpkey => $cmpvalue) {
						$activeCampDtl=$this->api_model->getRowData(CAMPAIGNS,'*',array('id'=>$cmpvalue->campaign_id));
						//print_r($activeCampDtl);die;
						if($activeCampDtl>0 && date('Y-m-d',strtotime($activeCampDtl->end_date)) >= date('Y-m-d'))
						{
							$sampleDtl=$this->api_model->getRowData(CAMPAIGN_SAMPLES,'*',array('campaign_id'=>$cmpvalue->campaign_id));
							//print_r($sampleDtl);die;
							if($sampleDtl>0 && date('Y-m-d',strtotime($sampleDtl->end_date)) >= date('Y-m-d'))
							{
								$stockDtl=$this->api_model->getRowData(CAMPAIGN_VENDS,'*',array('campaign_id'=>$cmpvalue->campaign_id,'vend_machine_id'=>$vending_machine_id));
								//print_r($stockDtl);die;
								if(@count($stockDtl)>0){
									$campaign_id=$stockDtl->campaign_id;
									/* if($total_sample<$totalReqsample){
										$response['status']=false; 
										$response['status_code']=300;
										$response['message']='Total sample should be greater than requested samples.';
										$this->convertNullToEmpty($response);
										echo json_encode($response);die;
									}*/
									
									$campvendData=array(
											    	"campaign_id" 			=>	$campaign_id,
											    	"vend_machine_id" 		=>	$vending_machine_id,
											    	"vend_no_of_samples" 	=>	($total_sample),
													'modified_dttm'			=>  date('Y-m-d H:i:s'),
									    	);

							        $this->api_model->update(CAMPAIGN_VENDS,$campvendData,array('id'=>$stockDtl->id));
									//$this->updateCampaignStock($campaign_id);

									$response['status']=true; 
									$response['status_code']=200;
									$response['message']='Inventory updated successfully.';
								}
								else{
									$campvendData=array(
									    	"campaign_id" 			=>	$campaign_id,
									    	"vend_machine_id" 		=>	$vending_machine_id,
									    	"vend_no_of_samples" 	=>	($total_sample),
									    	"is_active" 			=>	'1',
											'created_dttm'			=>  date('Y-m-d H:i:s'),
						    			);

							        $this->api_model->insert(CAMPAIGN_VENDS,$campvendData);
									//$this->updateCampaignStock($campaign_id);

									$response['status']=true; 
									$response['status_code']=200;
									$response['message']='Inventory added successfully.';
								}
							}
						}
					}
					
					
				}
				
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}
	public function updateCampaignStock($campaign_id){
		$con=array('campaign_id'=>$campaign_id);
		$campaign=$this->api_model->getResultData(CAMPAIGN_VENDS,'sum(vend_no_of_samples) as total_campaign_samples,sum(vend_no_of_sample_used) as total_campaign_samples_used',$con);
		$save = array(
					'total_campaign_samples' 		=> $campaign[0]->total_campaign_samples,
					'total_campaign_samples_used'	=> $campaign[0]->total_campaign_samples_used,
				);
		$this->api_model->update(CAMPAIGNS,$save,array('id'=>$campaign_id));
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
	public function convertNullToEmpty($response){
		array_walk_recursive($response, function (&$item, $key) {
			$item = null === $item ? '' : $item;
		});
	}
	function delete_notifications()
	{
		$this->data=json_decode(file_get_contents("php://input"), true); 
		if(!empty($this->data))
		{
			$con= "id ='".$this->data['notification_id']." '";
			$noti = array('is_active'=>0);
			$this->api_model->update(NOTIFICATIONS,$noti,$con);	
			$msg['status']=true; 
			$msg['status_code']=200;
			$msg['message']='Notification removed successfully.';
			echo json_encode($msg);die;
		}
	}
	function count_notification(){
		$data=	$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($data))
		{
			if($data['user_id']!='' ){ 
				$user_id=$data['user_id'];
				$totalNotification = $this->api_model->mysqlNumRows(NOTIFICATIONS,"id",array('user_to_id'=>$user_id,'is_view'=>'0'));
	        	$response['status']=true; 
				$response['status_code']=200;
	        	$response['totalNotification']=$totalNotification;
			}
			else
			{
				$response['status']=false; 
				$response['status_code']=300;
	        	$response['msg']="User Id is required.";
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}
	function notification_list(){
		$data=	$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($data))
		{
			$params=array();
			if($data['user_id']!='' ){
			  $brandLogoUrl=base_url('assets/brand/logo/'); 
				//$params['start'] = $offset;
	    		//$params['limit'] = $limit;
				$conditions = "noti.is_active='1' AND noti.user_to_id=".$data['user_id'];
				$notifications = $this->api_model->notificationList('noti.*,b.id as brand_id,b.brand_logo_url,b.brand_name,b.is_active as brand_is_active,camp.start_date as cmpsdate,camp.end_date as cmpedate',$conditions,$params);
				//echo $this->db->last_query();
				$notificationList=array();
				if($notifications){
					foreach ($notifications as $value) {
						$camp_status=0;
						if($value->cmpsdate>date('Y-m-d'))
						{
							$camp_status=1;
						}

						//print_r($value->campaign_id);die;
						$is_review=0;
						if(!empty($value->campaign_id))
						{
							$conAr=array('id'=>$value->campaign_id);
							$campData=$this->api_model->getRowData(CAMPAIGNS,'*',$conAr);
							
							$totalReview = $this->api_model->mysqlNumRows(USER_REVIEW,'id',array('review_id'=>$campData->review_id,'user_id'=>$data['user_id'],'is_campaign_review'=>'1'));
							//if($totalReview>0 && date('Y-m-d',strtotime($value->end_date)) >= date('Y-m-d'))
							if($totalReview>0 )
								$is_review=1;

						}
						
						$campaign_id = !empty($value->campaign_id)?$value->campaign_id:'';						
						$brand_id = !empty($value->brand_id)?$value->brand_id:'';
						$brand_name = !empty($value->brand_name)?$value->brand_name:'';
						$brand_is_active = !empty($value->brand_is_active)?$value->brand_is_active:'';
						$newarray	= array(
							"is_upcoming"		=> $camp_status,
							"notification_id"		=> $value->id,
							"noti_type"				=> $value->noti_type,
							"campaign_id"			=> $campaign_id,
							"post_id"				=> $value->post_id,
							"brand_id"				=> $brand_id,
							"brand_name"			=> $brand_name,
							"brand_logo_url" 		=> base_url().'assets/brand/logo/'.$value->brand_logo_url,
							"description" 			=> $value->msg,
							"date" 					=> $value->created_dttm,
							"is_view" 				=> $value->is_view,
							"is_review" 			=> $is_review,
							"brand_is_active" 		=> $brand_is_active							

						);
						array_push($notificationList, $newarray);

					}

				}
				$totalUnreadNotification = $this->api_model->mysqlNumRows(NOTIFICATIONS,"id",array('user_to_id'=>$data['user_id'],'is_view'=>'0','is_active'=>'1'));
				$response['status']=true; 
				$response['total_notifications']=@count($notificationList);
				$response['totalUnreadNotification']=$totalUnreadNotification;
				$response['notifications']=$notificationList;
				$response['status_code']=200;
				$response['message']='success';

			}
			else
			{
				$response['status']=false; 
				$response['status_code']=300;
				$response['message']='User Id is required.';
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}
	function changeNotificationStatus(){
		$data=	$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($data))
		{
			if($data['user_id']!='' ){ 
				$user_id=$data['user_id'];
				$con="user_to_id ='".$user_id."' and is_view='0'";
			
				if(isset($data['notification_id']) && $data['notification_id']!='')
				$con .= " AND  id = '".$data['notification_id']."'";
				$notificationData=array('is_view'=>'1','modified_dttm'=>date('Y-m-d:H:i:s'));

		   		$notificationUpdate = $this->api_model->update(NOTIFICATIONS,$notificationData,$con);
	         //echo $this->db->last_query();
		        if($notificationUpdate)
		        {   
		        	
		        	$response['status']=true; 
					$response['status_code']=200;
		        	$response['msg']="Notification updated successfully.";
		        }
		        else
		        {
			        $response['status']=false; 
					$response['status_code']=300;
					$response["msg"] = "Notification not updated successfully.";
				}
			}
			else
			{
				$response['status']=false; 
				$response['status_code']=300;
	        	$response['msg']="User Id is required.";
			}
		}
		else
		{
			echo 'Access Denied';die;
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}
	function notification($data=array()){
		if(!empty($data)){
			$user_from_id=$data['user_from_id'];
			$user_to_id=$data['user_to_id'];
			$noti_type=$data['noti_type'];
			$campaign_id=$data['campaign_id'];
			$post_id=$data['post_id'];
			$msg=$data['msg'];
			if($user_from_id!='' && !empty($user_to_id)){ 
				$device_token='';
				foreach ($user_to_id as $value) {
					$device_token = $this->api_model->getField(USERS_DEVICE_DTL,'device_token',array('user_id'=>$value->user_id));
					if($device_token){
						$notificationList	= array(
							"user_from_id"			=> $user_from_id,
							"user_to_id"			=> $value->user_id,
							"noti_type" 			=> $noti_type,
							"campaign_id"			=> $campaign_id,
							"post_id" 				=> $post_id,
							"msg" 					=> $msg,
							"is_view" 				=> '0',
							"is_active" 			=> '1',
							"created_dttm" 			=> date('Y-m-d:H:i:s'),
							"modified_dttm" 		=> date('Y-m-d:H:i:s'),

						);
						//$con=array("user_from_id"=>$user_from_id,"user_to_id"=>$value->user_id,"noti_type"=>$noti_type,"campaign_id"=> $campaign_id,"post_id"=>$post_id);
						//$countRows = $this->api_model->mysqlNumRows(NOTIFICATIONS,'id',$con);
						//if($countRows<1){
						
						$notification_id=$this->api_model->insert(NOTIFICATIONS,$notificationList);
						$totalNotification = $this->api_model->mysqlNumRows(NOTIFICATIONS,"id",array('user_to_id'=>$value->user_id,'is_view'=>'0'));
						$data	= array(
			        					"notification_id"   	=> $notification_id,
										"campaign_id"		   	=> $campaign_id,
										"post_id"		   		=> $post_id,
										"type" 					=> $noti_type,
							          );
				 		push_notification($device_token,$data,$msg,$totalNotification);
				 		//}
			 		}
		 			//print_r($user_to_id);die;

				}

			}
		}
		
	}	
	//vimlesh



	public function resetPass($fp_code=''){
		//echo $fp_code;die;
		$data=array();
		if($fp_code!=''){
			$data['fp_code']=$fp_code;
			$this->load->view('admin/user-reset-pass',$data);
		}
		else if($this->input->post('resetSubmit')){
		  	$this->load->library('form_validation');
		   	// Set form validation rules
		   	$this->form_validation->set_rules('new_password', 'New Password', 'trim|required');
		   	$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[new_password]');
		   	// Run form validation
		   if($this->form_validation->run() == FALSE) {
		   		//echo validation_errors();die;
		   		$this->resetPass($this->input->post('fp_code'));
		   	} else {
		   	  	//check whether identity code exists in the database
			    $prevUser=$this->common_model->getResultData(USERS,'id',array('forgot_pass_identity' => $fp_code));
	            if(!empty($prevUser)){
	                //update data with new password
	                $conditions = array('forgot_pass_identity' => $this->input->post('fp_code'));
	                $data = array('password' => md5($_POST['new_password']));
	                $update=$this->common_model->update(USERS,$data,$conditions);
	                if($update)
	                	$this->session->set_flashdata('restmsg','<div class="alert alert-success">Your account password has been reset successfully. Please login with your new password.</div>');
	                else
	                 	$this->session->set_flashdata('restmsg','<div class="alert alert-danger">Some problem occurred, please try again.</div>');
	            }
	            else
	                $this->session->set_flashdata('restmsg','<div class="alert alert-danger">You does not authorized to reset new password of this account..</div>');
	            
            	$this->load->view('admin/user-reset-pass',$data);
        	}
	   }
		else{
	 		$this->load->view('admin/user-reset-pass',$data);
		}
		
	}
	public function logout(){
		$userLoginUpdateData = array(
			'device_token' 		=> '', 
			'modified_dttm'  	=> date('Y-m-d H:i:s'),
		);
		$updtUserDtl = $this->api_model->update(USERS_DEVICE_DTL,$userLoginUpdateData,array('user_id'=>$this->input->post('user_id')));
		if($updtUserDtl){
			$response['status']=true; 
			$response['status_code']=200;
			$response["msg"] = "Logout successfully.";
			echo json_encode($response);
		}
		else{
			$response['status']=false; 
			$response['status_code']=300;
			$response["msg"] = "Something went wrong";
			echo json_encode($response);
		}
	}
	public function matchtoken($access_token='',$user_id=''){
		$prevaccess_token=$this->api_model->getField(USERS,'access_token',array('id'=>$user_id));
		return ($access_token==$prevaccess_token) ? TRUE : FALSE; 
	}

	

	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return ucwords($randomString);
	}
	public function checkEmail($email){

		$con=array('email'=>$email,'verified'=>'1');
		$result = $this->api_model->checkUserEmail($con);
		if($result)
			return FALSE;
		else 
			return TRUE;
	}
	public function removeProfileImg(){	
		$user_id    = $this->input->post('user_id');
		$access_token=getallheaders()['access_token']; 
		$token=$this->matchtoken($access_token,$user_id);
		if($token==false){
			$response['status']=false; 
				$response['status_code']=300;
			$response["msg"] = "You are not authorized.";
			echo json_encode($response);
		}
		else{
			$file='';
			$image=$this->api_model->getField(USERS,'image',array('id'=>$user_id));
			if($image!=''){
				unlink('assets/user/'.$image);
				$userData = array(
					'image'					=> $file,
					'modified'  			=> date('Y-m-d H:i:s'),
				);
				$signupId = $this->api_model->update(USERS,$userData,array('id'=>$user_id));
				if($signupId)
				{

					$response['status']=true; 
					$response['status_code']=200;
					$response["msg"] = "Profile image removed successfully";
				//$response["payload"] = array('access_token'=>$access_token,'user_id'=>$user_id);
					echo json_encode($response);
				}else
				{
					$response['status']=false; 
					$response['status_code']=300;
					$response["msg"] = "Remove profile image  error.";
					echo json_encode($response);
				}
			}
			else{
				$response['status']=false; 
				$response['status_code']=300;
				$response["msg"] = "No profile image available.";
				echo json_encode($response);
			}


		}
	}
	public function getVersion(){
   // $coverImgurl=base_url('assets/vendor/coverImage/');
		if($this->input->post('device_type') && $this->input->post('device_type')!=''){
			$device_type=$this->input->post('device_type');
			if($device_type=='1')
				$field="android_version";
			if($device_type=='2')
				$field="ios_version";

			$appVersion = $this->api_model->getField(SETTING,$field,array('device_type'=>$device_type,'id'=>'1'));
     //echo $this->db->last_query();
			if($appVersion)
			{  
				$response['status']=true; 
				$response['status_code']=200;
				$response['app_version']=$appVersion;
				echo json_encode($response);
			}
		}
		else
		{
			$response['status']=false; 
				$response['status_code']=300;
			$response["msg"] = "Device Type is required.";
			echo json_encode($response);
		}
	}
	function update_consent(){
		$data=	$this->data=json_decode(file_get_contents("php://input"), true);
		if(!empty($data))
		{
			$this->api_model->update(USER_SAMPLES,array('is_user_consent'=>1),array('id'=>$data['sample_id']));
	      	$response['status']=true; 
			$response['status_code']=200;
			$response['msg']="Consent updated successfully.";
			
		}
		else
		{
			echo 'Access Denied';die;
		}
		
		$this->convertNullToEmpty($response);
		echo json_encode($response);die;
	}

	}