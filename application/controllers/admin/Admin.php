<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        //$this->load->model('login_model');
		$this->load->model('common_model');
		$this->load->helper('mail_helper');
	}
	
	public function index()
	{  

		if($this->session->userdata('logged_in')==TRUE && $this->session->userdata('LOGIN_BY') =='admin')
		{
			redirect('users-list');	
			
		}
		else{
			$data['title'] 	= COMPANYNAME.' : Login';
			$this->load->view("index",$data);
		} 
	}

	public function login()
	{  
		//print_r($_POST);die;
		
		$email 		= $this->input->post("email");
		$user_pass 	= $this->input->post("password");
		
		$fields = "*";
		$condition_array = array(
			'email'    => $email,
			'password' => md5($user_pass),							 
		);	 
		$result = $this->common_model->getRowData(ADMIN,$fields,$condition_array);
		 // echo $this->db->last_query();die;
		if($result)
		{
			$sessiondata = array(
				'email' 	 	=> $result->email,
				'name' 	 		=> $result->name,	
				'id'  			=> $result->id,
				'logged_in'  	=> TRUE,
				'LOGIN_BY'      => 'admin',
			);
			
			$this->session->set_userdata($sessiondata);
			echo "success";
		}
		else{
			echo "Invalid email or password";
		  	// $this->index();
		}
		
		
		
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect("admin"); 
	}
	
	public function reset_password($token='')
	{
		$data=array();
           // $token = $this->base64url_decode($this->uri->segment(4)); 
		if($this->input->post('token'))
			$token=$this->input->post('token')  ;   
		if($token!=''){   
			$cleanToken = $this->security->xss_clean($token);
	            $user_info = $this->common_model->getRowData(ADMIN,'id,name,email,link_verified',array('forgot_pass_identity'=>$cleanToken)); //either false or array();  
	            
	            
	            if($user_info=='' ){
	            	$this->session->set_flashdata('restmsg', 'Token is invalid or expired');
	            	redirect(base_url().'admin-reset-pass');
	            }            
	            if($user_info->link_verified=='1' ){
	            	$this->session->set_flashdata('msg','<div class="alert alert-danger">Token is expired!.</div>');
	            	redirect(base_url().'admin');
	            }  

	            $data = array(
	            	'name'=> $user_info->name, 
	            	'email'=>$user_info->email,                
	            	'token'=>$token
	            );
	            
	            $this->load->library('form_validation');
	            $this->form_validation->set_rules('new_password', 'Password', 'required|min_length[6]');
	            $this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required|matches[new_password]');              
	            
	            if ($this->form_validation->run() == FALSE) {   
	            	$this->load->view('admin/reset-password', $data);
	                //$this->load->view('admin/common/footer');
	            }else{

	            	$conditions = array(
	            		'forgot_pass_identity' => $token,'link_verified !=' => 1
	            	);
	            	$data = array(
	            		'password' => md5($this->input->post('new_password')),
	            		'link_verified'=>'1'
	            	);
	            	$update=$this->common_model->update(ADMIN,$data,$conditions);
	               // echo $update;die;
	                //echo $this->db->last_query();die;
	            	if($update){
	            		$this->session->set_flashdata('msg','<div class="alert alert-success">Your account password has been reset successfully. Please login with your new password.</div>');
	            		
	            	}else{
	            		$this->session->set_flashdata('msg','<div class="alert alert-danger">Token is invalid or expired!.</div>');
	            	}
	            	redirect(base_url().'admin');
	                 //$this->load->view('admin/reset-password');               
	            }
	        }
	        else{
	        	$this->session->set_flashdata('msg','<div class="alert alert-danger">Token is invalid or expired.</div>');
	        	redirect(base_url().'admin');
	        }

	            //$this->load->view('admin/reset-password');   
        	//}
	    }

	    public function forgotpassword()
	    {
	    	$this->load->library('form_validation');
	    	$this->form_validation->set_rules('email', 'Email','required|trim|xss_clean|callback_check_email['.$this->input->post('email').']');
	    	
	    	if ($this->form_validation->run() == FALSE)
	    	{
			//$this->session->set_flashdata('resetPassmsg', '<div class="alert alert-danger flash" style="text-align:center">Your email does not exist.!!!</div>');
	    		echo "3";
	    	}
	    	else
	    	{
	    		$uniqidStr = md5(uniqid(mt_rand()));
	    		$email=$this->input->post('email');
	    		$con=array("email" =>$email,'status'=>'1');
	    		$Data=array("forgot_pass_identity" =>$uniqidStr,'link_verified'=>'0');
	    		$updtpass = $this->common_model->update(ADMIN,$Data,$con);
	    		$resetPassLink=base_url('admin-reset-pass/'.$uniqidStr);
	    		$userData=$this->common_model->getRowData(ADMIN,'id,name,email',array('email'=>$email));
			   // print_r($userData);die;


	    		$subject="Forgot password";
	    		$data['name']=$userData->name;
	    		$data['message']='Recently a request was submitted to reset a password for your account. If this was a mistake, just ignore this email.
	    		<br/>To reset your password, visit the following link: <a href="'.$resetPassLink.'">'.$resetPassLink.'</a>';
	    		$message=$this->load->view('admin/emailer',$data,true);
	    		
	    		$mailResponse=sendMail($userData->email,$subject,$message);
	    		if($mailResponse)
	    		{
	    			echo "1";
	    		}
	    		else
	    		{
	    			echo "2";
	    		}
	    		
	    	} 
	    	
		    /*else
		    $this->session->set_flashdata('msg', '<div class="alert alert-danger flash" style="text-align:center">Your email does not exist.!!!</div>');*/
		    
			//redirect($_SERVER['HTTP_REFERER']);	
		}
		

		public function check_email($emailid='')
		{
			
			$result = $this->common_model->checkUserEmail(ADMIN,array('email'=>$emailid));

			if($result)
				return TRUE;
			else 
				return FALSE;
		}
		
		
		
		
		public function changepassword()
		{
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('oldpwd', 'Old Password', 'trim|required|md5');
			$this->form_validation->set_rules('newpwd', 'New Password', 'required|min_length[6]');
			$this->form_validation->set_rules('conpwd', 'Confirm Password', 'required|matches[newpwd]');
			if($this->form_validation->run() == FALSE)
			{
				$this->changepass();
			}
			else{
				
				$userid= $this->session->userdata('isp_user');
				$oldpwd= $this->input->post('oldpwd');
				$newpwd= $newpwd= md5($this->input->post('newpwd'));
				$loggedby=$this->session->userdata('LOGIN_BY');
				if($loggedby=='isp_staff')
				{
					$condition_array = array(
						'admin_id'  =>  $userid,
						'password'  =>  $oldpwd,
					);	
					
					$result = $this->login_model->authenticate(ADMIN,$condition_array);
					
					if(count($result)==1)
					{ 
						$con=array("admin_id" =>$userid);
						$data_array=array("password" =>$newpwd);
						$update=$this->common_model->update_data(ADMIN,$data_array,$con);
						
						$this->session->set_flashdata('msg','Your Password Changed Successfully.');
					}
					
					else
						$this->session->set_flashdata('errormsg','The Old Password You Provided Is Incorrect. Please Try Again.!!!');
					redirect('admin/changepass');	
				}		  
				
			} 
			

		}
	}


	?>