<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Posts extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('common_model','post_model'));
		$this->load->library('Ajax_pagination');
		$this->load->helper(array('mail_helper','notification_helper'));
		$this->perPage = 10;
		ini_set('max_execution_time', 120);
		ini_set('memory_limit', '1024M');
		ini_set('post_max_size', '128M');
		ini_set('upload_max_filesize', '128M');
	} 

	/*public function index(){ 
		$data = array();
		$conditions = array();
		$status='';
		$offset = 0; 
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		$keywords = $this->input->get('keywords');
		if($this->input->get('keywords'))
			$status = "  (u.name LIKE '%".$keywords."%')";

		$data['brandsList'] = $this->post_model->getRows($status,$conditions); 
		//echo $this->db->last_query();die;
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brands';
		$this->load->view('common/header',$data);
		$this->load->view('post_view',true);
		$this->load->view('common/footer');

	}*/

	function create_buy_now_post()
	{
		$this->session->set_userdata(array('menu'=>'Posts'));
		$data['title'] = 'Create Posts';
		$this->load->view('common/header',$data);
		$this->load->view('buy_post_create',true);
		$this->load->view('common/footer');
	}

	function edit_buy_now_post($post_id='')
	{
		$data = array();
		$conditions = array();		
		$offset = 0;
		$status = "po.id ='".$post_id."'";
		$data['postDtl'] = $this->post_model->getRows($status,$conditions);
		$this->session->set_userdata(array('menu'=>'Posts'));
		$data['title'] = 'Edit Post';
		$data['campData']=$this->post_model->getRows($status,$conditions);
		$this->load->view('common/header',$data);
		$this->load->view('post_buy_edit',true);		
		$this->load->view('common/footer');

	}

	function view_buy_now_post($post_id='')
	{
		$data = array();
		$conditions = array();		
		$offset = 0;
		$status = "po.id ='".$post_id."'";
		$data['postDtl'] = $this->post_model->getRows($status,$conditions);		
		$this->session->set_userdata(array('menu'=>'Posts'));
		$data['title'] = 'View Post';
		$this->load->view('common/header',$data);
		$this->load->view('post_buy_view',true);		
		$this->load->view('common/footer');
	}

	function buyPosts()
	{
		$data = array();
		$conditions = array();
		//$status = "buy_now_status=1";
		$status = "buy_now_status=1 AND campaign_id IS NULL";
		$offset = 0;
        $conditions['start'] = $offset;
        $conditions['limit'] = $this->perPage;
		$data['postsList'] = $this->post_model->getlist($status,$conditions); 		
		$this->session->set_userdata(array('menu'=>'Posts'));
		$data['title'] = 'Buy Now Post';
		$this->load->view('common/header',$data);		
		$this->load->view('buy_now_post_list');
		$this->load->view('common/footer');
	}

	function getBuyPosts($page)
	{
		$data = array();
		$conditions = array();
		$status = "buy_now_status=1 AND campaign_id IS NULL";
		$offset = 0;
		$data=array('results'=>'','totalRecords'=>0);
		if($page)
			$offset = $this->perPage*$page; 
        $conditions['start'] = $offset;
        $conditions['limit'] = $this->perPage;
		$postsList = $this->post_model->getlist($status,$conditions);		
		//$data['totalRecords']=$totalRecords = count($this->post_model->getRows($status));
		$data['totalRecords']=$totalRecords =$this->post_model->getRows($status,$conditions,'count');
		
		if($postsList)
		{
			foreach ($postsList as $postkey => $postvalue) {
				if($postvalue['buy_now_status']=='1'){
					$post_type="Buy Now Post";
				}
				else{					
					$post_type="Regular Post";
				}
				$data['results'] .= '<tr><td><p class="post-link font-weight-bold mb-0">'.$postvalue['post_desc'].'</p></td>				
				<td>'.$post_type.'</td>
				<td class="text-center">';
				if($postvalue['publish_date']!="")
					$data['results'] .= date("d M Y",strtotime($postvalue['publish_date']));
				$data['results'] .= '</td>
				<td class="text-center"><a href="'.base_url('buy-post-view/'.$postvalue["id"]).'" class="text-link">View</a>
				<a href="'.base_url('buy-post-edit/'.$postvalue["id"]).'" class="text-link ml-4">Edit</a>
				</td>

				</tr>';
			}
		}
		echo json_encode($data);
	}

	public function addBuyPosts()
	{
		$imgupload='';
		$couponDoc='';
		$banner_type='';
		$has_promo=0;
		$is_publish=0;
		$coupon_text='';		
		$end_date='';
		$publish_date='';
		$buy_now_status = 1;
		
		
		
		if($this->input->post('button_type')=='publish'){
			$is_publish=1;
			$publish_date=date('Y-m-d');
		}
		$qr_code_url='';
		//print_r($_POST);die;
				
		if($this->input->post('imgupload')!=''){
			$targetDir = "./assets/post/banner/";			
			$data = $this->input->post('imgupload');	 
			$image_parts = explode(";base64,", $data);
			if(strpos($image_parts[0], 'image') == true)
			{
				$image_type_aux = explode("image/", $image_parts[0]);
				$image_type  	= $image_type_aux[1];
				$banner_type  	= '1';
			}
			if(strpos($image_parts[0], 'video') == true)
			{
				$image_type_aux =  explode("video/", $image_parts[0]);
				$image_type 	=  $image_type_aux[1];
				$banner_type		= '2';
			}
			$image_base64 = base64_decode($image_parts[1]);
			$uniueId = uniqid();
			$file = $targetDir . $uniueId . '.'.$image_type;
			$imgupload = $uniueId . '.'.$image_type;
			$path = base_url().'assets/post/banner/'.$imgupload;
			file_put_contents($file, $image_base64);
			//$destination2="/var/www/html/isamplez/app/webroot/post/banner/".$uniueId . '.'.$image_type;;
			//file_put_contents($destination2, $image_base64);
		}	
		
		//$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$this->input->post('campaign_id')));
		$insertRewards=array(

			'post_banner_url'   	=> $imgupload,
			'banner_type'   		=> $banner_type,
			'has_promo'   			=> $has_promo,
			'post_desc' 			=> $this->input->post('post_desc'),
			'qr_code_url'   		=> $qr_code_url,
			'buy_now_status'   		=> $buy_now_status,
			'buy_now_url'   		=> $this->input->post('buy_now_url'),			
			'is_active'   			=> '1',
			'is_publish'   			=> $is_publish,
			'publish_date' 	   		=> $publish_date,
			'created_dttm' 	   		=> date('Y-m-d H:i:s'),
			'modified_dttm' 		=> date('Y-m-d H:i:s'),
				//'reward_no_of_uses' 	=> $this->input->post('reward_no_of_uses'),
		);
			
		$buy_post_id=$this->common_model->insert(WALL_POSTS,$insertRewards);
		if($buy_post_id){
			if($is_publish=='1' && $has_promo==1){
				$post_desc = $this->input->post('post_desc');
				$user_to_id = $this->common_model->getResultData(USERS,'id',array('registration_status'=>'2','is_active'=>'1'));
				$notification=array(					
					'user_from_id'=>'0',
					'user_to_id'=>$user_to_id,
					'noti_type'=>'3',//new post
					'campaign_id'=>'',
					'post_id'=>$buy_post_id,
					'msg'=>"A new post '".$post_desc."' launched." ,
				);
				$this->notification($notification);
			}

			echo "success";
		}
		else
			echo "fail";	

	}

	public function editBuyPosts()
	{
		$post_banner_url='';
		$old_imgupload='';		
		$banner_type='';
		$post_banner_url='';
		$imgupload='';
		$has_promo=0;
		$is_publish=0;
		$promo_desc='';
		$publish_date='';
		
		if($this->input->post('post_id'))
			$post_id=$this->input->post('post_id');
		
		if($this->input->post('button_type')=='publish'){
			$is_publish=1;
			$publish_date=date('Y-m-d');
		}
		if($this->input->post('imgupload'))
			$imgupload=$this->input->post('imgupload');
		if($this->input->post('old_imgupload'))
			$old_imgupload=$this->input->post('old_imgupload');
		if($this->input->post('banner_type'))
			$banner_type=$this->input->post('banner_type');
		
		$qr_code_url='';
		if($imgupload!='' && !is_file('assets/post/banner/'.$imgupload)) {
			$targetDir = "./assets/post/banner/";			
			$data = $imgupload;	 
			$image_parts = explode(";base64,", $data);
			if(strpos($image_parts[0], 'image') == true)
			{
				$image_type_aux = explode("image/", $image_parts[0]);
				$image_type  	= $image_type_aux[1];
				$banner_type  	= '1';
			}
			if(strpos($image_parts[0], 'video') == true)
			{
				$image_type_aux =  explode("video/", $image_parts[0]);
				$image_type 	=  $image_type_aux[1];
				$banner_type		= '2';
			}
			$image_base64 = base64_decode($image_parts[1]);
			$uniueId = uniqid();
			$file = $targetDir . $uniueId . '.'.$image_type;
			$post_banner_url = $uniueId . '.'.$image_type;
			$path = base_url().'assets/post/banner/'.$post_banner_url;
			file_put_contents($file, $image_base64);
			if(is_file('assets/post/banner/'.$this->input->post('old_imgupload')) && $this->input->post('old_imgupload')!='' )
				unlink("assets/post/banner/".$this->input->post('old_imgupload'));
			
		}	
		else{
			if($old_imgupload!='' && $banner_type!=''){
				$post_banner_url=$old_imgupload;
				$banner_type = $banner_type;	
			}
			else{
				$post_banner_url='';
				$banner_type = '';
			}	

		}
		
		
		$updatePost=array(
			
			'post_banner_url'   	=> $post_banner_url,
			'banner_type'   		=> $banner_type,			
			'post_desc' 			=> $this->input->post('post_desc'),			
			'is_publish'   			=> $is_publish,
			'publish_date' 	   		=> $publish_date,
			'modified_dttm' 		=> date('Y-m-d H:i:s'),
				//'reward_no_of_uses' 	=> $this->input->post('reward_no_of_uses'),
		);
		//print_r($updatePost);
		if($imgupload=="")
			unset($updatePost['post_banner_url']);		
		$postUpdate=$this->common_model->update(WALL_POSTS,$updatePost,array('id'=>$post_id));
		//echo $this->db->last_query();die;
		if($postUpdate){
			if($is_publish=='1' && $has_promo==1){
				$post_desc = $this->input->post('post_desc');
				$user_to_id = $this->common_model->getResultData(USERS,'id',array('registration_status'=>'2','is_active'=>'1'));
				$notification=array(					
					'user_from_id'=>'0',
					'user_to_id'=>$user_to_id,
					'noti_type'=>'3',//new post
					'campaign_id'=>'',
					'post_id'=>$post_id,
					'msg'=>"A new post '".$post_desc."' launched." ,
				);
				$this->notification($notification);
			}
			echo "success";
		}
		else
			echo "fail";
	}
	public function getPosts($page){ 
		$data = array();
		$conditions = array();
		$status='';
		$offset = 0;
		$data ='';
		$data=array('results'=>'','totalRecords'=>0);
		if($page)
			$offset = $this->perPage*$page; 
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		//print_r($_POST);die;
		$brand_id    = $this->input->post('brand_id');
		$status = " po.brand_id='".$brand_id."'";
		$search_text = $this->input->post('search_text');
		if($search_text)
			$status .= " AND  (po.post_title LIKE '%".$search_text."%')";
		$brandsList = $this->post_model->getRows($status,$conditions);
		//$data['totalRecords']=$totalRecords = count($this->post_model->getRows($status,$conditions));
		$data['totalRecords']=$totalRecords =$this->post_model->getRows($status,$conditions,'count');
		if($brandsList){
			foreach ($brandsList  as  $value) {
				if($value->has_promo=='1'){
					$post_type="Promo Post";
					$badge='<b class="badge badge-pill badge-warning text-white">!</b>';
				}
				else{
					$badge="";
					$post_type="Regular Post";
				}
				$data['results'] .= '<tr><td class="text-center">'.$badge.'</td>
				<td><p class="post-link font-weight-bold mb-0">'.$value->post_desc.'</p></td>
				<td class="text-center text-primary font-weight-bold">'.$value->no_of_likes.'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->no_of_comments.'</td>
				<td>'.$post_type.'</td>
				<td class="text-center">';
				if($value->publish_date!="0000-00-00")
					$data['results'] .= date("d M Y",strtotime($value->publish_date));
				$data['results'] .= '</td>
				<td class="text-center"><a href="'.base_url('post-view/'.$brand_id.'/'.$value->id).'" class="text-link">View</a>
				<a href="'.base_url('post-edit/'.$brand_id.'/'.$value->id).'" class="text-link ml-4">Edit</a>
				</td>

				</tr>';
			} 
			/*<a href="'.base_url('post_view/'.$value->id).'" class="text-link">View</a>*/
		}
		echo json_encode($data);
       // echo $data['users'];

	}
	public function postView($brand_id='',$post_id=''){ 
		$data = array();
		$conditions = array();
		$data['brand_id'] = $brand_id;
		$offset = 0;
		$status = "po.id ='".$post_id."'";
		$data['postDtl'] = $this->post_model->getRows($status,$conditions);
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'View Post';

		//if($data['postDtl'][0]->is_publish=='1'){
		$this->load->view('common/header',$data);
		$this->load->view('post_view',true);
		//}
		/*else{
			$data['campData']=$this->common_model->getResultData(CAMPAIGNS,'*',array('brand_id'=>$brand_id,'is_active'=>'1'));
			$this->load->view('common/header',$data);
			$this->load->view('post_edit',true);
		}*/
		$this->load->view('common/footer');

	}
	public function postEdit($brand_id='',$post_id=''){ 
		$data = array();
		$conditions = array();
		$data['brand_id'] = $brand_id;
		$offset = 0;
		$status = "po.id ='".$post_id."'";
		$data['postDtl'] = $this->post_model->getRows($status,$conditions);
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Edit Post';
		$data['campData']=$this->common_model->getResultData(CAMPAIGNS,'*',array('brand_id'=>$brand_id,'is_active'=>'1'));
		$this->load->view('common/header',$data);
		$this->load->view('post_edit',true);
		
		$this->load->view('common/footer');

	}
	public function create($brand_id=''){ 
		$data['brand_id']=$brand_id;
		$data['campData']=$this->common_model->getResultData(CAMPAIGNS,'*',array('brand_id'=>$brand_id,'is_active'=>'1','start_date <='=>date('Y-m-d')));
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Create Posts';
		$this->load->view('common/header',$data);
		$this->load->view('post_create',true);
		$this->load->view('common/footer');

	}
	function notification($data=array()){
		if(!empty($data)){
			$user_to_id=$data['user_to_id'];
			$noti_type=$data['noti_type'];
			$campaign_id=$data['campaign_id'];
			$post_id=$data['post_id'];
			$msg=$data['msg'];
			if(!empty($user_to_id)){ 
				//$notificationList=array();
				$device_token ="";
				foreach ($user_to_id as $value) {
					$device_token = $this->common_model->getField(USERS_DEVICE_DTL,'device_token',array('user_id'=>$value->id));
					if($device_token) {
						$notificationList	= array(
							"user_from_id"			=> '0',
							"user_to_id"			=> $value->id,
							"noti_type" 			=> $noti_type,
							"campaign_id"			=> $campaign_id,
							"post_id" 				=> $post_id,
							"msg" 					=> $msg,
							"is_view" 				=> '0',
							"is_active" 			=> '1',
							"created_dttm" 			=> date('Y-m-d:H:i:s'),
							"modified_dttm" 		=> date('Y-m-d:H:i:s'),

						);
						$con=array("user_to_id"=> $value->id,"noti_type"=> $noti_type,"campaign_id"=> $campaign_id,"post_id"=> $post_id);
						$countRows = $this->common_model->mysqlNumRows(NOTIFICATIONS,'id',$con);
						//print_r($countRows);die;
						if($countRows>=0){
							$notification_id=$this->common_model->insert(NOTIFICATIONS,$notificationList);
							$totalNotification = $this->common_model->mysqlNumRows(NOTIFICATIONS,"id",array('user_to_id'=>$value->id,'is_view'=>'0'));
							$data	= array(
								"notification_id"   	=> $notification_id,
								"campaign_id"		   	=> $campaign_id,
								"post_id" 				=> $post_id,
								"type" 					=> '3',
								"is_upcoming" 			=> '',								
							);
							push_notification($device_token,$data,$msg,$totalNotification);
						}
					}
		 			//print_r($a);die;

				}

			}
		}
		
	}
	public function addPosts()
	{
		$imgupload='';
		$couponDoc='';
		$banner_type='';
		$has_promo=0;
		$is_publish=0;
		$coupon_text='';
		$promo_desc='';
		$end_date='';
		$publish_date='';
		$buy_now_status = 0;
		$buynow_url = '';
		if($this->input->post('has_promo'))
			$has_promo=1;
		if($this->input->post('buy_now_status'))
			$buy_now_status=1;
		if($this->input->post('buy_now_url'))
			$buynow_url=$this->input->post('buy_now_url');
		if($this->input->post('end_date'))
			$end_date=date('Y-m-d',strtotime($this->input->post('end_date')));
		if($this->input->post('coupon_text'))
			$promo_desc=$this->input->post('coupon_text');
		if($this->input->post('button_type')=='publish'){
			$is_publish=1;
			$publish_date=date('Y-m-d');
		}
		$qr_code_url='';
	//	print_r($_POST);die;
		if($this->input->post('imgupload')!=''){
			$targetDir = "./assets/post/banner/";			
			$data = $this->input->post('imgupload');	 
			$image_parts = explode(";base64,", $data);
			if(strpos($image_parts[0], 'image') == true)
			{
				$image_type_aux = explode("image/", $image_parts[0]);
				$image_type  	= $image_type_aux[1];
				$banner_type  	= '1';
			}
			if(strpos($image_parts[0], 'video') == true)
			{
				$image_type_aux =  explode("video/", $image_parts[0]);
				$image_type 	=  $image_type_aux[1];
				$banner_type		= '2';
			}
			$image_base64 = base64_decode($image_parts[1]);
			$uniueId = uniqid();
			$file = $targetDir . $uniueId . '.'.$image_type;
			$imgupload = $uniueId . '.'.$image_type;
			$path = base_url().'assets/post/banner/'.$imgupload;
			file_put_contents($file, $image_base64);
			//$destination2="/var/www/html/isamplez/app/webroot/post/banner/".$uniueId . '.'.$image_type;;
			//file_put_contents($destination2, $image_base64);
		}	
		if($this->input->post('couponDoc')!=''){
			$targetDir = "./assets/post/coupon/";			
			$data = $this->input->post('couponDoc');	 
			$image_parts = explode(";base64,", $data);
			$image_type_aux = explode("image/", $image_parts[0]);
			$image_type = $image_type_aux[1];
			$image_base64 = base64_decode($image_parts[1]);
			$uniueId = uniqid();
			$file = $targetDir . $uniueId . '.'.$image_type;
			$couponDoc = $uniueId . '.'.$image_type;
			$qr_code_url = base_url().'assets/post/coupon/'.$couponDoc;
			file_put_contents($file, $image_base64);
			//$destination2="/var/www/html/isamplez/app/webroot/post/coupon/".$uniueId . '.'.$image_type;;
			//file_put_contents($destination2, $image_base64);
		}
		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$this->input->post('campaign_id')));
		$insertRewards=array(
			'campaign_id'   		=> $this->input->post('campaign_id'),
			'brand_id'   			=> $this->input->post('brand_id'),
			'review_id'   			=> $review_id,
			'post_banner_url'   	=> $imgupload,
			'banner_type'   		=> $banner_type,
			'has_promo'   			=> $has_promo,
			'post_desc' 			=> $this->input->post('post_desc'),
				//'coupon_text'   		=> $$is_publish,
			'qr_code_url'   		=> $qr_code_url,
			'buy_now_status'   		=> $buy_now_status,
			'buy_now_url'   		=> $buynow_url,
			'promo_desc'   			=> $promo_desc,
			'promo_end_date' 	   	=> $end_date,
			'is_active'   			=> '1',
			'is_publish'   			=> $is_publish,
			'publish_date' 	   		=> $publish_date,
			'created_dttm' 	   		=> date('Y-m-d H:i:s'),
			'modified_dttm' 		=> date('Y-m-d H:i:s'),
				//'reward_no_of_uses' 	=> $this->input->post('reward_no_of_uses'),
		);
			/*if($this->input->post('reward_usagetime')=='One Time Usage' || $this->input->post('reward_usagetime')=='')
			{
			unset($insertRewards['reward_no_of_uses']);
		}*/
		$post_id=$this->common_model->insert(WALL_POSTS,$insertRewards);
		if($post_id){
			if($is_publish=='1' && $has_promo==1){
				//echo "new post";die;
				$post_desc = $this->input->post('post_desc');
				$user_to_id = $this->common_model->getResultData(USERS,'id',array('registration_status'=>'2','is_active'=>'1'));
				$notification=array(
					'user_from_id'=>'0',
					'user_to_id'=>$user_to_id,
					'noti_type'=>'3',//new post
					'campaign_id'=>$this->input->post('campaign_id'),
					'post_id'=>$post_id,
					'msg'=>"A new post '".$post_desc."' launched." ,
				);
				$this->notification($notification);
			}

			echo "success";
		}
		else
			echo "fail";
		

	}
	public function editPosts()
	{

		$post_banner_url='';$old_imgupload='';$couponDoc='';$old_couponDoc='';
		$banner_type='';$post_banner_url='';$imgupload='';
		$has_promo=0;$is_publish=0;$coupon_text='';$promo_desc='';$end_date='';$publish_date='';
		$buy_now_status=0;$buynow_url='';
		
		if($this->input->post('post_id'))
			$post_id=$this->input->post('post_id');
		if($this->input->post('has_promo'))
			$has_promo=1;
		if($this->input->post('end_date'))
			$end_date=date('Y-m-d',strtotime($this->input->post('end_date')));
		if($this->input->post('coupon_text'))
			$promo_desc=$this->input->post('coupon_text');
		if($this->input->post('button_type')=='publish'){
			$is_publish=1;
			$publish_date=date('Y-m-d');
		}
		if($this->input->post('imgupload'))
			$imgupload=$this->input->post('imgupload');
		if($this->input->post('old_imgupload'))
			$old_imgupload=$this->input->post('old_imgupload');

		if($this->input->post('old_couponDoc'))
			$old_couponDoc=$this->input->post('old_couponDoc');
		if($this->input->post('banner_type'))
			$banner_type=$this->input->post('banner_type');
		if($this->input->post('buy_now_status'))
			$buy_now_status=1;
		if($this->input->post('buy_now_url'))
			$buynow_url=$this->input->post('buy_now_url');
		
		$qr_code_url='';
		if($imgupload!='' && !is_file('assets/post/banner/'.$imgupload)) {
			$targetDir = "./assets/post/banner/";			
			$data = $imgupload;	 
			$image_parts = explode(";base64,", $data);
			if(strpos($image_parts[0], 'image') == true)
			{
				$image_type_aux = explode("image/", $image_parts[0]);
				$image_type  	= $image_type_aux[1];
				$banner_type  	= '1';
			}
			if(strpos($image_parts[0], 'video') == true)
			{
				$image_type_aux =  explode("video/", $image_parts[0]);
				$image_type 	=  $image_type_aux[1];
				$banner_type		= '2';
			}
			$image_base64 = base64_decode($image_parts[1]);
			$uniueId = uniqid();
			$file = $targetDir . $uniueId . '.'.$image_type;
			$post_banner_url = $uniueId . '.'.$image_type;
			$path = base_url().'assets/post/banner/'.$post_banner_url;
			file_put_contents($file, $image_base64);
			if(is_file('assets/post/banner/'.$this->input->post('old_imgupload')) && $this->input->post('old_imgupload')!='' )
				unlink("assets/post/banner/".$this->input->post('old_imgupload'));
			//$destination2="/var/www/html/isamplez/app/webroot/post/banner/".$uniueId . '.'.$image_type;;
			//file_put_contents($destination2, $image_base64);
		}	
		else{
			if($old_imgupload!='' && $banner_type!=''){
				$post_banner_url=$old_imgupload;
				$banner_type = $banner_type;	
			}
			else{
				$post_banner_url='';
				$banner_type = '';
			}	

		}
		if($has_promo=="1"){
			if($this->input->post('couponDoc')!='' && !is_file('assets/post/coupon/'.$this->input->post('couponDoc'))){
				$targetDir = "./assets/post/coupon/";			
				$data = $this->input->post('couponDoc');	 
				$image_parts = explode(";base64,", $data);
				$image_type_aux = explode("image/", $image_parts[0]);
				$image_type = $image_type_aux[1];
				$image_base64 = base64_decode($image_parts[1]);
				$uniueId = uniqid();
				$file = $targetDir . $uniueId . '.'.$image_type;
				$couponDoc = $uniueId . '.'.$image_type;
				$qr_code_url = base_url().'assets/post/coupon/'.$couponDoc;
				file_put_contents($file, $image_base64);
				if(is_file('assets/post/coupon/'.$this->input->post('old_couponDoc')) && $this->input->post('old_couponDoc')!='' )
					unlink("assets/post/coupon/".$this->input->post('old_couponDoc'));
				//$destination2="/var/www/html/isamplez/app/webroot/post/coupon/".$uniueId . '.'.$image_type;;
				//file_put_contents($destination2, $image_base64);
			}
			else{
				if($old_couponDoc!=''){
					$qr_code_url=$qr_code_url = base_url().'assets/post/coupon/'.$old_couponDoc;	
				}
				else{
					$qr_code_url='';
				}	

			}
		}
		else{
			$qr_code_url="";$end_date="";$promo_desc="";
		}
		$is_push = 0;	
		if($has_promo==1)
		{
			$promoEndDate=$this->common_model->getField(WALL_POSTS,'promo_end_date',array('id'=>$post_id,'has_promo'=>'1'));
			$editEndDt = $end_date;
			if($promoEndDate == $editEndDt) 
				$is_push = 0;		
			else
				$is_push = 1;
		}

		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$this->input->post('campaign_id')));
		$updatePost=array(
			'campaign_id'   		=> $this->input->post('campaign_id'),
			'brand_id'   			=> $this->input->post('brand_id'),
			'review_id'   			=> $review_id,
			'post_banner_url'   	=> $post_banner_url,
			'banner_type'   		=> $banner_type,
			'has_promo'   			=> $has_promo,
			'buy_now_status'   		=> $buy_now_status,
			'buy_now_url'   		=> $buynow_url,
			'post_desc' 			=> $this->input->post('post_desc'),
			'qr_code_url'   		=> $qr_code_url,
			'promo_desc'   			=> $promo_desc,
			'promo_end_date' 	   	=> $end_date,
			'is_publish'   			=> $is_publish,
			'publish_date' 	   		=> $publish_date,
			'modified_dttm' 		=> date('Y-m-d H:i:s'),
				//'reward_no_of_uses' 	=> $this->input->post('reward_no_of_uses'),
		);
		//print_r($updatePost);
		if($imgupload=="")
			unset($updatePost['post_banner_url']);
		if($qr_code_url=="" && $has_promo=="1")
			unset($updatePost['qr_code_url']);
		$postUpdate=$this->common_model->update(WALL_POSTS,$updatePost,array('id'=>$post_id));
		//echo $this->db->last_query();die;
		$validDate = date('d M Y',strtotime($end_date));

		if($postUpdate){
			if($is_publish=='1' && $has_promo==1 && $is_push == 1){				
				$post_desc = $this->input->post('post_desc');
				$user_to_id = $this->common_model->getResultData(USERS,'id',array('registration_status'=>'2','is_active'=>'1'));
				$notification=array(
					'user_from_id'=>'0',
					'user_to_id'=>$user_to_id,
					'noti_type'=>'3',//new post
					'campaign_id'=>$this->input->post('campaign_id'),
					'post_id'=>$post_id,
					//'msg'=>"A new post '".$post_desc."' launched." ,
					'msg'=>"Post '".$post_desc."' date have changed. New valid date is ".$validDate."." ,
				);
				$this->notification($notification);
			}
			echo "success";
		}
		else
			echo "fail";
		

	}
	public function editPostsold()
	{
		$imgupload='';
		$couponDoc='';
		$banner_type='';
		$has_promo=0;
		$is_publish=0;
		$coupon_text='';
		$publish_date='';
		if($this->input->post('post_id'))
			$post_id=$this->input->post('post_id');
		if($this->input->post('button_type')=='publish'){
			$is_publish=1;
			$publish_date=date('Y-m-d');
		}
		
		
		$updatePost=array(
			'post_desc' 			=> $this->input->post('post_desc'),
			'is_publish'   			=> $is_publish,
			'publish_date' 	   		=> $publish_date,
				//'created_dttm' 	   		=> date('Y-m-d H:i:s'),
			'modified_dttm' 		=> date('Y-m-d H:i:s'),
		);
		$post_id=$this->common_model->update(WALL_POSTS,$updatePost,array('id'=>$post_id));
		if($post_id){

			echo "success";
		}
		else
			echo "fail";
		

	}
	public function brand_detail_campaign($brand_id=''){
		$data = array();
		$conditions = array();
		$status = "camp.id ='".$brand_id."'";
		$data['brandDtl'] = $this->post_model->getRows($status,$conditions); 
		$data['media']=$this->common_model->getResultData(BRANDASSETS,'*',array('brand_id'=>$brand_id));
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brand campaign Details';
		$this->load->view('common/header',$data);
		$this->load->view('brand_detail_campaign',true);
		$this->load->view('common/footer');

	}
	public function brand_detail_post($brand_id=''){
		$data = array();
		$conditions = array();
		$status = "camp.id ='".$brand_id."'";
		$data['brandDtl'] = $this->post_model->getRows($status,$conditions); 
		$data['media']=$this->common_model->getResultData(BRANDASSETS,'*',array('brand_id'=>$brand_id));
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brand Post Details';
		$this->load->view('common/header',$data);
		$this->load->view('brand_detail_post',true);
		$this->load->view('common/footer');

	}
	public function changeStatus()
	{
		$id=$this->input->post('id');
		$is_active=$this->input->post('is_active');
		$updateChallenge=array('is_active'=>$is_active);
		$returnid=$this->common_model->update(BRANDS,$updateChallenge,array('id'=>$id));
		echo $returnid;

	}

		
}


?>