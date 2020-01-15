<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Brands extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model(array('common_model','brands_model'));
		$this->load->helper(array('common_helper','notification_helper'));
		$this->perPage = 10;
		ini_set('max_execution_time', 120);
		ini_set('memory_limit', '1024M');
		ini_set('post_max_size', '128M');
		ini_set('upload_max_filesize', '128M');
		//echo phpinfo();
	} 

	public function index(){ 
		$data = array();
		$conditions = array();
		$status='';
		$offset = 0; 
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		$keywords = $this->input->get('keywords');
		if($this->input->get('keywords'))
			$status = "  (u.name LIKE '%".$keywords."%')";

		$data['brandsList'] = $this->brands_model->getRows($status,$conditions); 
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brands';
		$this->load->view('common/header',$data);
		$this->load->view('brands',true);
		$this->load->view('common/footer');

	}
	public function getBrands($page){ 
		$data = array();
		$conditions = array();
		$status='';
		$offset = 0;
		$data=array('results'=>'','totalRecords'=>0);
		if($page)
			$offset = $this->perPage*$page;
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		//print_r($_POST);die;
		$search_text = $this->input->post('search_text');
		if($search_text)
			$status = "  (b.brand_name LIKE '%".$search_text."%')";
		$brandsList = $this->brands_model->getRows($status,$conditions);
		//echo $this->db->last_query();die;
		$totalRecords = $this->brands_model->getRows("",array(),'count');
		$data['totalRecords']=$totalRecords;
		//print_r($totalRecords);die;
		if($brandsList){
			foreach ($brandsList  as  $value) {
				if($value->is_active=='1'){
					$text="Active";
					$check="";
				}
				else{
					$text="Deactivate";
					$check="checked";
				}

				$data['results'] .= '<tr><td><a href="'.base_url('brand-detail-campaign/'.$value->id).'" class="user-link"><span>';
				if($value->brand_logo_url!='' && file_exists('assets/brand/logo/'.$value->brand_logo_url)) 
				$data['results'] .= '<img src="'.base_url('assets/brand/logo/'.$value->brand_logo_url).' " alt="'.ucwords($value->brand_name).'"/>';
				$data['results'].= '</span>'.ucwords($value->brand_name).'<small class="d-block">Created on '.date('d M, Y',strtotime($value->created_dttm)).'</small></a></td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_campaign.'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_samples.'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_post.'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_promocodes.'</td>
				<td class="text-center"><a href="'.base_url('brand-detail-campaign/'.$value->id).'" class="text-link">View</a>
				<div class="switchButton">
				<label>
				<input class="deactiveCheck" type="checkbox" value="'.$value->is_active.'" '.$check.' id="switch_'.$value->id.'"/>
				<b></b><span>'.$text.'</span></label>
				</div></td>
				</tr>';
			} 
		}
		echo json_encode($data);
       // echo $data['users'];

	}
	public function brandDetails($brand_id=''){ 
		$data = array();
		$conditions = array();
		$offset = 0;
		$status = "b.id ='".$brand_id."'";
		$data['brandDtl'] = $this->brands_model->getRows($status,$conditions); 
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brand Details';
		$this->load->view('common/header',$data);
		$this->load->view('users_detail',true);
		$this->load->view('common/footer');

	}
	public function create(){ 
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Create Brands';
		$this->load->view('common/header',$data);
		$this->load->view('brand_create',true);
		$this->load->view('common/footer');

	}
 	public function resizeImage($filename){
	    $source_path = 'assets/brand/logo/' . $filename;
	    $target_path = 'assets/brand/logo/';
	    $config_manip = array(
	          'image_library' => 'gd2',
	          'source_image' => $source_path,
	          'new_image' => $target_path,
	          'maintain_ratio' => TRUE,
	          //'create_thumb' => TRUE,
	          'width' => 150,
	          'height' => 150
	     );
	    $this->load->library('image_lib', $config_manip);
	    if (!$this->image_lib->resize()) {
	          echo $this->image_lib->display_errors();
	      }

	      $this->image_lib->clear();
   }
	public function addBrands()
	{
		//print_r($_POST);die;
		ini_set('memory_limit', '1024M');
		$logo='';
		$asset_type='';
		$imgupload=$this->input->post('imgupload');
		if($this->input->post('logo')!=''){
			$targetDir = "assets/brand/logo/";			
			$data = $this->input->post('logo');	 
			$image_parts = explode(";base64,", $data);
			$image_type_aux = explode("image/", $image_parts[0]);
			$image_type = $image_type_aux[1];
			$image_base64 = base64_decode($image_parts[1]);
			$uniueId = uniqid();
			$file = $targetDir . $uniueId . '.'.$image_type;
			$logo = $uniueId . '.'.$image_type;
			$path = base_url().'assets/brand/logo/'.$logo;
			file_put_contents($file, $image_base64);
			//$destination2="/var/www/html/isamplez/app/webroot/brand/logo/".$uniueId . '.'.$image_type;;
			//file_put_contents($destination2, $image_base64);
			$this->resizeImage($uniueId . '.'.$image_type);
		}	
		$insertData=array(
			'brand_name'   		=> $this->input->post('brand_name'),
			'brand_desc'   		=> $this->input->post('brand_desc'),
			'brand_logo_url' 	=> $logo,
			'is_active' 		=> 1,
			'created_dttm' 	   	=> date('Y-m-d H:i:s'),
			'modified_dttm' 	=> date('Y-m-d H:i:s'),
		);
		$brand_id=$this->common_model->insert(BRANDS,$insertData);
		if($brand_id){
			//echo count($imgupload);die;
			for ($i=0; $i < count($imgupload) ; $i++) { 
				if($imgupload[$i]!=''){
					$targetDirAssets = "assets/brand/assets/";			
					$data = $imgupload[$i];	 
					$image_parts = explode(";base64,", $data);
					if(strpos($image_parts[0], 'image') == true)
					{
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type  	= $image_type_aux[1];
						$asset_type  	= '1';
					}
					if(strpos($image_parts[0], 'video') == true)
					{
						$image_type_aux =  explode("video/", $image_parts[0]);
						$image_type 	=  $image_type_aux[1];
						$asset_type		= '2';
					}
					$image_base64 	= base64_decode($image_parts[1]);
					$uniueId 		= uniqid();
					$file 			= $targetDirAssets . $uniueId . '.'.$image_type;
					$assets 		= $uniueId . '.'.$image_type;
					$path 			= base_url().'assets/brand/assets/'.$assets;
					file_put_contents($file, $image_base64);
					//$destination2="/var/www/html/isamplez/app/webroot/brand/assets/".$uniueId . '.'.$image_type;;
					//file_put_contents($destination2, $image_base64);
					$insertAssets=array(
						'brand_id'   		=> $brand_id,
						'asset_type'   		=> $asset_type,
						'asset_url' 		=> $assets,
						'is_active' 		=> 1,
						'created_dttm' 	   	=> date('Y-m-d H:i:s'),
						'modified_dttm' 	=> date('Y-m-d H:i:s'),
					);
					$assetid=$this->common_model->insert(BRANDASSETS,$insertAssets);
				}

			}
			echo "success";
		}
		else
			echo "fail";

	}
	public function edit($brand_id){ 
		$data = array();
		$con = "id ='".$brand_id."'";
		$data['brandDtl'] = $this->common_model->getRowData(BRANDS,'*',$con); 
		$data['media']=$this->common_model->getResultData(BRANDASSETS,'*',array('brand_id'=>$brand_id));
		//print_r($data['media']);die;
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Edit Brands';
		$this->load->view('common/header',$data);
		$this->load->view('brand_edit',true);
		$this->load->view('common/footer');

	}
	public function editBrands(){
		//print_r($_POST);die;
		$logo='';
		$asset_types='';
		$asset_types=$this->input->post('asset_type');
		$imgupload=$this->input->post('imgupload');
		$brand_id=$this->input->post('id');
		$asset_url=$this->input->post('asset_url');
		$brand_asset_id=$this->input->post('brand_asset_id');
		if($this->input->post('logo')!='' && !is_file('assets/brand/logo/'.$this->input->post('logo'))){
			$targetDir = "assets/brand/logo/";			
			$data = $this->input->post('logo');	 
			$image_parts = explode(";base64,", $data);
			$image_type_aux = explode("image/", $image_parts[0]);
			$image_type = $image_type_aux[1];
			$image_base64 = base64_decode($image_parts[1]);
			$uniueId = uniqid();
			$file = $targetDir . $uniueId . '.'.$image_type;
			$logo = $uniueId . '.'.$image_type;
			$path = base_url().'assets/brand/logo/'.$logo;
			file_put_contents($file, $image_base64);
			$this->resizeImage($uniueId . '.'.$image_type);
			//$destination2="/var/www/html/isamplez/app/webroot/brand/logo/".$uniueId . '.'.$image_type;;
			//file_put_contents($destination2, $image_base64);
			if(file_exists('assets/brand/assets/'.$this->input->post('old_logo')) && $this->input->post('old_logo')!='' )
				unlink("assets/brand/assets/".$this->input->post('old_logo'));
			//file_put_contents('http://52.220.197.0/isamplez/qrcodeGenerator/' . $uniueId . '.'.$image_type, $image_base64);
		}	
		$updateData=array(
			'brand_name'   		=> $this->input->post('brand_name'),
			'brand_desc'   		=> $this->input->post('brand_desc'),
			'brand_logo_url' 	=> $logo,
			'modified_dttm' 	=> date('Y-m-d H:i:s'),
		);
		if($logo=="")
			unset($updateData['brand_logo_url']);
		$update=$this->common_model->update(BRANDS,$updateData,array('id'=>$brand_id));
		$assetsIds=$this->common_model->getResultData(BRANDASSETS,'id',array('brand_id'=>$brand_id));
		if($assetsIds){
			foreach ($assetsIds as $key => $value) {
				if(!in_array($value->id ,$brand_asset_id)){
					$assetUrl=$this->common_model->getField(BRANDASSETS,'asset_url',array('id'=>$value->id));
					$filePath = 'assets/brand/assets/'.$assetUrl; // get all file names
					  if(is_file($filePath))
					    unlink($filePath); // delete file
					
					$this->db->delete(CAMPAIGN_BRAND_ASSETS,array('asset_id'=>$value->id));
					$this->db->delete(BRANDASSETS,array('id'=>$value->id));
				}
			}
		}
		if($update){
			//$this->db->delete(BRANDASSETS,array('brand_id'=>$brand_id));
			for ($i=0; $i < count($imgupload)-1 ; $i++) { 
				if($imgupload[$i]!=''){
					$targetDirAssets = "assets/brand/assets/";			
					$data = $imgupload[$i];	 
					$image_parts = explode(";base64,", $data);
					if(strpos($image_parts[0], 'image') == true)
					{
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type  	= $image_type_aux[1];
						$asset_type  	= '1';
					}
					if(strpos($image_parts[0], 'video') == true)
					{
						$image_type_aux =  explode("video/", $image_parts[0]);
						$image_type 	=  $image_type_aux[1];
						$asset_type		= '2';
					}
					$image_base64 	= base64_decode($image_parts[1]);
					$uniueId 		= uniqid();
					$file 			= $targetDirAssets . $uniueId . '.'.$image_type;
					$assets 		= $uniueId . '.'.$image_type;
					$path 			= base_url().'assets/brand/assets/'.$assets;
					file_put_contents($file, $image_base64);
				//	$destination2="/var/www/html/isamplez/app/webroot/brand/assets/".$uniueId . '.'.$image_type;;
					//file_put_contents($destination2, $image_base64);
					if($brand_asset_id!='')
						$id         = $brand_asset_id[$i];	
					else
						$id='';
					
				}
				else{
					if($asset_url[$i]!='' && $asset_types[$i]!=''){
						$assets=$asset_url[$i];
						$asset_type = $asset_types[$i];
						$id         = $brand_asset_id[$i];	
					}
					else{
						$assets='';
						$asset_type = '';
						$id='' ;
					}		
				}
				$insertAssets=array(
					'id'                => $id,
					'brand_id'   		=> $brand_id,
					'asset_type'   		=> $asset_type,
					'asset_url' 		=> $assets,
					'is_active' 		=> 1,
					'created_dttm' 	   	=> date('Y-m-d H:i:s'),
					'modified_dttm' 	=> date('Y-m-d H:i:s'),
				);
				if($id==''){
					unset($insertAssets['id']);
					$assetid=$this->common_model->insert(BRANDASSETS,$insertAssets);
				}
				else{
					unset($insertAssets['created_dttm'],$insertAssets['is_active']);
					$assetid=$this->common_model->update(BRANDASSETS,$insertAssets,array('id'=>$id));
				}

				//echo $this->db->last_query();
				//echo "|";
				//print_r($insertAssets);
				/*if(!empty($asset_url)){
					for ($k=0; $k < count($asset_url) ; $k++) { 
					if(file_exists("assets/brand/assets/".$asset_url[$k]) && $imgupload[$i]!=$asset_url[$k])
						unlink("assets/brand/assets/".$asset_url[$k]);
					}
				}*/
			}
			echo "success";
		}
		else
			echo "fail";
		

	}
	public function create_campaign_brand($brand_id='',$campaign_id=''){ 
		
		$data['brand_id']=$brand_id;
		$data['campaign_id']=$campaign_id;
		$data['media']=$this->common_model->getResultData(BRANDASSETS,'*',array('brand_id'=>$brand_id));
		$campCount= $this->common_model->mysqlNumRows(CAMPAIGN_BRAND_ASSETS,'id',array('campaign_id'=>$campaign_id,'brand_id'=>$brand_id)); 
		//echo $this->db->last_query();die;
		if($campCount>0)
			redirect('edit-campaign-brand/'.$brand_id.'/'.$campaign_id);
		else{
			
			$this->session->set_userdata(array('menu'=>'Brands'));
			$data['title'] = 'Create Campaign Brands';
			$this->load->view('common/header',$data);
			$this->load->view('campaign_brand',true);
			$this->load->view('common/footer');
		}
	}

	function notification($data=array()){
		if(!empty($data)){
			
			$user_to_id=$data['user_to_id'];
			$campaign_id=$data['campaign_id'];
			//"is_review": 0,
			if(isset($data['is_upcoming']) && !empty($data['is_upcoming']))
				$is_upcoming=$data['is_upcoming'];
			else 
				$is_upcoming=0;
			
			$msg=$data['msg'];
			if(!empty($user_to_id)){ 
				$device_token='';
				foreach ($user_to_id as $value) {
					$device_token = $this->common_model->getField(USERS_DEVICE_DTL,'device_token',array('user_id'=>$value->id));
					if($device_token) {
						$notificationList	= array(
							"user_from_id"			=> '0',
							"user_to_id"			=> $value->id,
							"noti_type" 			=> '4',
							"campaign_id"			=> $campaign_id,
							"post_id" 				=> '',
							"msg" 					=> $msg,
							"is_view" 				=> '0',
							"is_active" 			=> '1',
							"created_dttm" 			=> date('Y-m-d:H:i:s'),
							"modified_dttm" 		=> date('Y-m-d:H:i:s'),

						);
						$con=array("user_to_id"=> $value->id,"noti_type"=> '4',"campaign_id"=> $campaign_id);
						$countRows = $this->common_model->mysqlNumRows(NOTIFICATIONS,'id',$con);
						if($countRows<1){
							$notification_id=$this->common_model->insert(NOTIFICATIONS,$notificationList);
							$totalNotification = $this->common_model->mysqlNumRows(NOTIFICATIONS,"id",array('user_to_id'=>$value->id,'is_view'=>'0'));
							$data	= array(
			        					"notification_id"   	=> $notification_id,
										"campaign_id"		   	=> $campaign_id,
										"post_id" 				=> '',
										"type" 					=> '4',
										"is_upcoming"			=>$is_upcoming,										
							          );
							push_notification($device_token,$data,$msg,$totalNotification);
				 		}
			 		}
				}
			}
		}
	}	
	public function addCampBrands()
	{
		ini_set('memory_limit', '1024M');
		$asset_type='';$is_publish='0';
		$imgupload=$this->input->post('imgupload');
		$campaign_id=$this->input->post('campaign_id');
		$brand_id=$this->input->post('brand_id');
		$customCheck=$this->input->post('customCheck');
		$brand_asset_id=$this->input->post('brand_asset_id');
		$asset_url=$this->input->post('asset_url');
		$button_type=$this->input->post('button_type');
		//$asset_type=$this->input->post('asset_type');

		//$this->db->delete(CAMPAIGN_BRAND_ASSETS,array('campaign_id'=>$campaign_id,'brand_id'=>$brand_id));
		if($brand_asset_id){
			for ($k=0; $k <count($brand_asset_id) ; $k++) { 
				if(isset($customCheck[$k])){
					$insertAssets=array(
						'campaign_id'   	=> $campaign_id,
						'brand_id'   		=> $brand_id,
						'asset_id'   		=> $brand_asset_id[$k],
						'is_active' 		=> 1,
						'created_dttm' 	   	=> date('Y-m-d H:i:s'),
						'modified_dttm' 	=> date('Y-m-d H:i:s'),
					);
					$this->common_model->insert(CAMPAIGN_BRAND_ASSETS,$insertAssets);
				}
			}
		}
		if($brand_id){
			if($button_type=="publish")
				$is_publish='1';
			else if($button_type=="save")
				$is_publish='0';
			$updateCamp=$this->common_model->update(CAMPAIGNS,array('is_publish'=>$is_publish),array('id'=>$campaign_id));
			if($updateCamp && $is_publish=='1'){
				$is_upcoming = 0;
				$campData = $this->common_model->getRowData(CAMPAIGNS,'campaign_name,start_date,end_date',array('id'=>$campaign_id));		
				$user_to_id = $this->common_model->getResultData(USERS,'id',array('registration_status'=>'2','is_active'=>'1'));
				$msg="A new campaign '".$campData->campaign_name."' launched.";
				
				if($campData->start_date>date('Y-m-d'))
				{
					$msg="An upcoming campaign '".$campData->campaign_name."' launched.";
					$is_upcoming = 1;
				}
				$notification=array(
					'is_upcoming'=>$is_upcoming,					
					'user_from_id'=>'0',
					'user_to_id'=>$user_to_id,
					'noti_type'=>'4',//nnew compaign launched
					'campaign_id'=>$campaign_id,
					'post_id'=>'0',
					'msg'=> $msg,
				);
				$this->notification($notification);
			}
			//echo count($imgupload);die;
			for ($i=0; $i < count($imgupload) ; $i++) { 
				if($imgupload[$i]!=''){
					$targetDirAssets = "assets/brand/assets/";			
					$data = $imgupload[$i];	 
					$image_parts = explode(";base64,", $data);
					if(strpos($image_parts[0], 'image') == true)
					{
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type  	= $image_type_aux[1];
						$asset_type  	= '1';
					}
					if(strpos($image_parts[0], 'video') == true)
					{
						$image_type_aux =  explode("video/", $image_parts[0]);
						$image_type 	=  $image_type_aux[1];
						$asset_type		= '2';
					}
					$image_base64 	= base64_decode($image_parts[1]);
					$uniueId 		= uniqid();
					$file 			= $targetDirAssets . $uniueId . '.'.$image_type;
					$assets 		= $uniueId . '.'.$image_type;
					$path 			= base_url().'assets/brand/assets/'.$assets;
					//file_put_contents($file, $image_base64);
					//$destination2="/var/www/html/isamplez/app/webroot/brand/assets/".$uniueId . '.'.$image_type;;
					//file_put_contents($destination2, $image_base64);
					$insertAssets=array(
						'brand_id'   		=> $brand_id,
						'asset_type'   		=> $asset_type,
						'asset_url' 		=> $assets,
						'is_active' 		=> 1,
						'created_dttm' 	   	=> date('Y-m-d H:i:s'),
						'modified_dttm' 	=> date('Y-m-d H:i:s'),
					);
					$assetid=$this->common_model->insert(BRANDASSETS,$insertAssets);
					$insertAssets=array(
						'campaign_id'   	=> $campaign_id,
						'brand_id'   		=> $brand_id,
						'asset_id'   		=> $assetid,
						'is_active' 		=> 1,
						'created_dttm' 	   	=> date('Y-m-d H:i:s'),
						'modified_dttm' 	=> date('Y-m-d H:i:s'),
					);
					$this->common_model->insert(CAMPAIGN_BRAND_ASSETS,$insertAssets);
					
				}

			}
			echo $campaign_id;die;
		}
		else
			echo "0";

	}
	public function editCampBrands()
	{
		ini_set('memory_limit', '1024M');
		$asset_type='';$is_publish='0';
		$imgupload=$this->input->post('imgupload');
		$campaign_id=$this->input->post('campaign_id');
		$brand_id=$this->input->post('brand_id');
		$customCheck=$this->input->post('customCheck');
		$brand_asset_id=$this->input->post('brand_asset_id');
		$asset_url=$this->input->post('asset_url');
		$button_type=$this->input->post('button_type');
		$camp_brand_asset_id=$this->input->post('camp_brand_asset_id');
		//print_r($_POST);die;

		$this->db->delete(CAMPAIGN_BRAND_ASSETS,array('campaign_id'=>$campaign_id,'brand_id'=>$brand_id));
		if($brand_asset_id){
			for ($k=0; $k <count($brand_asset_id) ; $k++) { 
				if(!empty($camp_brand_asset_id) && $camp_brand_asset_id[$k]!='')
					$id=$camp_brand_asset_id[$k];
				else
					$id='';
				if(isset($customCheck[$k]) && $customCheck[$k]=='1'){
					$insertAssets=array(
						'id'   				=> $id,
						'campaign_id'   	=> $campaign_id,
						'brand_id'   		=> $brand_id,
						'asset_id'   		=> $brand_asset_id[$k],
						'is_active' 		=> 1,
						'created_dttm' 	   	=> date('Y-m-d H:i:s'),
						'modified_dttm' 	=> date('Y-m-d H:i:s'),
					);
					if($id==''){
						unset($insertAssets['id']);
					}
					$this->common_model->insert(CAMPAIGN_BRAND_ASSETS,$insertAssets);
				}
			}
		}	
		if($brand_id){
			if($button_type=="publish"){
				$is_publish='1';
				$updateCamp=$this->common_model->update(CAMPAIGNS,array('is_publish'=>$is_publish),array('id'=>$campaign_id));
				if($updateCamp && $is_publish=='1'){
					//$campData = $this->common_model->getRowData(CAMPAIGNS,'campaign_name',array('id'=>$campaign_id));
					$campData = $this->common_model->getRowData(CAMPAIGNS,'campaign_name,start_date,end_date',array('id'=>$campaign_id));
					$user_to_id = $this->common_model->getResultData(USERS,'id',array('registration_status'=>'2','is_active'=>'1'));
					//print_r($user_to_id);die;
					$msg="A new campaign '".$campData->campaign_name."' launched.";
					$is_upcoming = 0;
					$is_review = 0;
					if($campData->start_date>date('Y-m-d'))
					{
						$msg="An upcoming campaign '".$campData->campaign_name."' launched.";
						$is_upcoming = 1;
					}
					$notification=array(
						'is_upcoming'=>$is_upcoming,
						'is_review'=>$is_review,
						'user_from_id'=>'0',
						'user_to_id'=>$user_to_id,
						'noti_type'=>'4',//nnew compaign launched
						'campaign_id'=>$campaign_id,
						'post_id'=>'0',
						'msg'=> $msg,
					);
					//$this->notification($notification);
				}
			}
			//echo count($imgupload);die;
			for ($i=0; $i < count($imgupload) ; $i++) { 
				if($imgupload[$i]!=''){
					$targetDirAssets = "assets/brand/assets/";			
					$data = $imgupload[$i];	 
					$image_parts = explode(";base64,", $data);
					if(strpos($image_parts[0], 'image') == true)
					{
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type  	= $image_type_aux[1];
						$asset_type  	= '1';
					}
					if(strpos($image_parts[0], 'video') == true)
					{
						$image_type_aux =  explode("video/", $image_parts[0]);
						$image_type 	=  $image_type_aux[1];
						$asset_type		= '2';
					}
					$image_base64 	= base64_decode($image_parts[1]);
					$uniueId 		= uniqid();
					$file 			= $targetDirAssets . $uniueId . '.'.$image_type;
					$assets 		= $uniueId . '.'.$image_type;
					$path 			= base_url().'assets/brand/assets/'.$assets;
					if (file_put_contents($file, $image_base64)!== false) {
					
						$insertBrandAssets=array(
							'brand_id'   		=> $brand_id,
							'asset_type'   		=> $asset_type,
							'asset_url' 		=> $assets,
							'is_active' 		=> 1,
							'created_dttm' 	   	=> date('Y-m-d H:i:s'),
							'modified_dttm' 	=> date('Y-m-d H:i:s'),
						);
						$assetid=$this->common_model->insert(BRANDASSETS,$insertBrandAssets);
						$insertAssets=array(
							'campaign_id'   	=> $campaign_id,
							'brand_id'   		=> $brand_id,
							'asset_id'   		=> $assetid,
							'is_active' 		=> 1,
							'created_dttm' 	   	=> date('Y-m-d H:i:s'),
							'modified_dttm' 	=> date('Y-m-d H:i:s'),
						);
						$this->common_model->insert(CAMPAIGN_BRAND_ASSETS,$insertAssets);
						$campaign_id=$campaign_id;
					}
					else
						$campaign_id='0';
					
				}

			}
			//print_r($insertBrandAssets);
			//print_r($insertAssets);
			echo $campaign_id;
		}
		else
			echo "0";

	}
	public function edit_campaign_brand($brand_id='',$campaign_id=''){ 
		$data['brand_id']=$brand_id;
		$data['campaign_id']=$campaign_id;
		$data['media']=$this->common_model->getResultData(BRANDASSETS,'*',array('brand_id'=>$brand_id));
		$data['campMedia']=$this->common_model->getResultData(CAMPAIGN_BRAND_ASSETS,'*',array('campaign_id'=>$campaign_id,'brand_id'=>$brand_id));
		//echo"<pre>";print_r($data['media']);die;
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Edit Campaign';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_brand_edit');
		$this->load->view('common/footer');

	}
	public function brand_detail_campaign($brand_id=''){
		$data = array();
		$conditions = array();
		$data['brand_id']=$brand_id;
		$status = "b.id ='".$brand_id."'";
		$data['brandDtl'] = $this->brands_model->getRows($status,$conditions); 
		$data['media']=$this->common_model->getResultData(BRANDASSETS,'*',array('brand_id'=>$brand_id));
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brand campaign Details';
		$this->load->view('common/header',$data);
		$this->load->view('brand_detail_in');
		$this->load->view('brand_detail_campaign');
		$this->load->view('common/footer');

	}
	public function brand_detail_post($brand_id=''){
		$data = array();
		$conditions = array();
		$data['brand_id']=$brand_id;
		$status = "b.id ='".$brand_id."'";
		$data['brandDtl'] = $this->brands_model->getRows($status,$conditions); 
		//print_r($data['brandDtl'] );die;
		$data['media']=$this->common_model->getResultData(BRANDASSETS,'*',array('brand_id'=>$brand_id));
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brand Post Details';
		$this->load->view('common/header',$data);
		$this->load->view('brand_detail_in');
		$this->load->view('brand_detail_post');
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