<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaigns extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('common_model','campaign_model','targetAudience_model'));		
		$this->load->helper(array('common_helper','notification_helper'));
		$this->perPage = 10;
		ini_set('max_execution_time', 120);
		ini_set('memory_limit', '1024M');
		ini_set('post_max_size', '128M');
		ini_set('upload_max_filesize', '128M');
	} 

	public function getCampaigns($page){ 
		$data = array();
		$conditions = array();
		$status='';
		$nps=0;
		$offset = 0;
		$data ='';
		$data=array('results'=>'','totalRecords'=>0);
		if($page)
			$offset = $this->perPage*$page;
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		
		$search_text = $this->input->post('search_text');
		$brand_id    = $this->input->post('brand_id');
		//print_r($_POST);die;
		$status = " camp.brand_id='".$brand_id."'";
		if($search_text)
			$status .= " AND  (b.campaign_name LIKE '%".$search_text."%')";
		$brandsList = $this->campaign_model->getRows($status,$conditions);
		//$brandsListy = $this->campaign_model->NPS();
		$data['totalRecords']=$totalRecords = $this->campaign_model->getRows($status,$conditions,'count');
		//echo $this->db->last_query();die;
		if($brandsList){
			foreach ($brandsList  as  $value) {
				if(date("Y-m-d",strtotime($value->start_date)) <= date('Y-m-d') && date("Y-m-d",strtotime($value->end_date)) >=date('Y-m-d')){
					$badge='<b class="badge badge-pill badge-success"> </b>';
					$campainReport = "";
				}
				else{
					$badge="";
					if(date("Y-m-d",strtotime($value->end_date)) >=date('Y-m-d')) {
						$campainReport = '';
					}else{
						$campainReport = '<a href="#" camp_id="'.$value->id.'" class="text-link ml-4 genRepo">Report</a>';	
					}
									
				}
				$nps=NPS($value->id);
				$data['results'] .= '<tr><td class="text-center">'.$badge.'</td><td><a href="'.base_url('campaign_detail_samples/'.$brand_id.'/'.$value->id).'" class="user-link font-weight-bold">'.$value->campaign_name.'</a></td>
				<td class="text-center">'.date("d M Y",strtotime($value->start_date)).'</td>
				<td class="text-center">'.date("d M Y",strtotime($value->end_date)).'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_samples_redeemed.'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_promo_redeemed.'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_post.'</td>
				<td class="text-center text-primary font-weight-bold">'.$nps.'</td>
				<td class="text-center"><a href="'.base_url('campaign_detail_samples/'.$brand_id.'/'.$value->id).'" class="text-link">View</a>
				<a href="'.base_url('edit-campaigns/'.$brand_id.'/'.$value->id).'" class="text-link ml-4">Edit</a>'.$campainReport.' </td>
				</tr>';
			} 
		}
		echo json_encode($data);
       // echo $data['users'];

	}
	public function campaignDetails($brand_id=''){ 
		$data = array();
		$conditions = array();
		$offset = 0;
		$status = "b.id ='".$brand_id."'";
		$data['brandDtl'] = $this->campaign_model->getRows($status,$conditions); 
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Campaign Details';
		$this->load->view('common/header',$data);
		$this->load->view('users_detail',true);
		$this->load->view('common/footer');

	}
	public function create($brand_id,$campaign_id=''){ 
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['brand_id'] = $brand_id;
		$data['campaign_id'] = $campaign_id;
		$data['sampleData']=$this->common_model->getResultData(CAMPAIGN_SAMPLES,'*',array('campaign_id'=>$campaign_id));
		$campCount= $this->common_model->mysqlNumRows(CAMPAIGNS,'id',array('id'=>$campaign_id)); 
		if($campCount>0)
			redirect('edit-campaigns/'.$brand_id.'/'.$campaign_id);
		else{
		
		$data['title'] = 'Create Campaign';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_create',true);
		$this->load->view('common/footer');
	}

	}
	public function addCamapigns(){ 		
		$banner_type='';
		$imgupload=$this->input->post('imgupload');
		$makedefault=$this->input->post('makedefault');
		$imageLoc=$this->input->post('imguploadl');
		$insertData=array(
			'brand_id'   		=> $this->input->post('brand_id'),
			'campaign_name'   	=> $this->input->post('campaign_name'),
			'campaign_desc'   	=> $this->input->post('campaign_desc'),
			'start_date'   		=> date('Y-m-d',strtotime($this->input->post('start_date'))),
			'end_date'   		=> date('Y-m-d',strtotime($this->input->post('end_date'))),
			'is_publish' 		=> 0,
			'is_active' 		=> 1,
			'buy_now_link' 		=> $this->input->post('buy_now_link'),
			'created_dttm' 	   	=> date('Y-m-d H:i:s'),
			'modified_dttm' 	=> date('Y-m-d H:i:s'),
		);
		$campaign_id=$this->common_model->insert(CAMPAIGNS,$insertData);
		if($campaign_id){
			$totlalImage=count($imageLoc)-1;
			
			if($totlalImage>0){
				$targetDirAssets = "assets/campaign/banner/";
				for($i=0;$i<$totlalImage;$i++) 
				{
					$lid=$this->input->post('camp_loation_id')[$i];
					$url=$this->input->post('url')[$i];
					$lname=$this->input->post('lname')[$i];
					if($imageLoc[$i]!='')
					{
						$imgl = $imageLoc[$i];	 
						$image_parts = explode(";base64,", $imgl);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type  	= $image_type_aux[1];
						$image_base64 	= base64_decode($image_parts[1]);
						$uniueId 		= uniqid();
						$file 			= $targetDirAssets . $uniueId .'_avl_loc.'.$image_type;
						$assets 		= $uniueId.'_avl_loc.'.$image_type;
						$path 			= base_url().'assets/campaign/banner/'.$assets;
						file_put_contents($file, $image_base64);
						
						$insertMedia=array('campaign_id'=> $campaign_id,'location_name'=> $lname,'url'=> $url,'location_image'=> $assets,'is_active'=> 1,'created_dttm'=> date('Y-m-d H:i:s'),
						'modified_dttm' => date('Y-m-d H:i:s'));
					}
					$assetid=$this->common_model->insert('campaign_samples_location',$insertMedia);
					$insertMedia=array();;
				}
			}
			for ($i=0; $i < count($imgupload) ; $i++) { 
				//makedefault
				if($imgupload[$i]!=''){
					$targetDirAssets = "assets/campaign/banner/";			
					$data = $imgupload[$i];	 
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
					$image_base64 	= base64_decode($image_parts[1]);
					$uniueId 		= uniqid();
					$file 			= $targetDirAssets . $uniueId . '.'.$image_type;
					$assets 		= $uniueId . '.'.$image_type;
					$path 			= base_url().'assets/campaign/banner/'.$assets;
					file_put_contents($file, $image_base64);
					//$destination2="/var/www/html/isamplez/app/webroot/campaign/banner/".$uniueId . '.'.$image_type;;
					//file_put_contents($destination2, $image_base64);
					if($makedefault==$i)
						$cover_image=1;
					else
						$cover_image=0;

					$insertMedia=array(
						'campaign_id'   	=> $campaign_id,
						'banner_type'   	=> $banner_type,
						'cover_image'		=> $cover_image,
						'banner_url' 		=> $assets,
						'is_active' 		=> 1,
						'created_dttm' 	   	=> date('Y-m-d H:i:s'),
						'modified_dttm' 	=> date('Y-m-d H:i:s'),
					);
					$assetid=$this->common_model->insert(CAMPAIGNBANNERS,$insertMedia);
				}

			}
			//$this->new_campaign($campaign_id);
			echo $campaign_id;
		}
		else
			echo "0";
	}
	/*public function new_campaign($campaign_id){
	    $challengeList 	= $this->common_model->getRowData(CHALLENGE,'*',array('id'=>$campaign_id));

		//$userList=$this->db->query("select id,device_token, ( 6371 * acos ( cos ( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( latitude ) ) ) ) AS distance from users HAVING distance <= 10 ORDER BY distance")->result();
		$userList = $this->common_model->getResultData(USERS,'*',array('is_active'=>'1'));
		foreach ($userList as  $value) {
			$device_token= $this->common_model->getField(USERS_DEVICE_DTL,'device_token',array('user_id'=>$value->id,'is_active'=>'1'));;
			$notificationData=array(
	        		"noti_type"				=>  "4",
	        		//"user_from_id"			=>  $value->id,
	        		"user_to_id"			=>  $value->id,
					"campaign_id"		    =>  $campaign_id,
					"msg"		    		=>  "A new camapign has been launched",
		            "created_at"	    	=> date('Y-m-d'),
		            "updated_at"	    	=> date('Y-m-d'),
	        		);
			$notification_id = $this->common_model->insert(NOTIFICATION,$notificationData);
		 	$message	= array(
	        					"notification_id"   	=> $notification_id,
								"campaign_id"		    => $campaign_id,
								"msg"		    		=> "A new camapign has been launched",
					          );
		 	//push_notification($device_token,$message,'New compaign launched');
		}
	}*/
	public function edit($brand_id,$campaign_id){ 
		$data = array();
		//$con = "id ='".$brand_id."'";
		//$data['brandDtl'] = $this->common_model->getRowData(BRANDS,'*',$con); 
		$data['campData']=$this->common_model->getRowData(CAMPAIGNS,'*',array('id'=>$campaign_id));
		$data['media']=$this->common_model->getResultData(CAMPAIGNBANNERS,'*',array('campaign_id'=>$campaign_id));
		$data['location']=$this->common_model->getResultData('campaign_samples_location','*',array('campaign_id'=>$campaign_id));
		$campCount=0;
		$data['brand_id'] = $brand_id;
		$data['campaign_id'] = $campaign_id;
		$data['sampleData']=$this->common_model->getResultData(CAMPAIGN_SAMPLES,'*',array('campaign_id'=>$campaign_id));
		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$campaign_id));
		if($review_id)
			$campCount= $this->common_model->mysqlNumRows(REVIEW_QUESTIONS,'id',array('review_id'=>$review_id)); 
		$data['qusDataCount']=$campCount;
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Edit Campaigns';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_edit',true);
		$this->load->view('common/footer');

	}
	public function editCamapigns()
	{ //echo '<pre>';print_r($_POST);die;disabled="disabled"
		$banner_types='';
		$banner_types=$this->input->post('banner_type');
		$imgupload=$this->input->post('imgupload');
		$campaign_id=$this->input->post('campaign_id');
		$brand_id=$this->input->post('brand_id');
		$banner_url=$this->input->post('banner_url');
		$camp_banner_id=$this->input->post('camp_banner_id');
		$makedefault=$this->input->post('makedefault');
		$imageLoc=$this->input->post('imguploadl');
		$campData = $this->common_model->getRowData(CAMPAIGNS,'campaign_name,start_date,end_date,is_publish',array('id'=>$campaign_id));

		$editStartDt = date('Y-m-d',strtotime($this->input->post('start_date')));
		$editEndDt = date('Y-m-d',strtotime($this->input->post('end_date')));
		$startDt = $campData->start_date;
		$EndDt = $campData->end_date;
		$publish = $campData->is_publish;

		if(($editStartDt == $startDt) && ($editEndDt == $EndDt))
			$is_push = 0;		
		else
			$is_push = 1;

		if($is_push==1 && $publish==0)
			$is_push = 2;
		

		$updateData=array(
			'campaign_name'   	=> $this->input->post('campaign_name'),
			'buy_now_link'   	=> $this->input->post('buy_now_link'),
			'campaign_desc'   	=> $this->input->post('campaign_desc'),
			'start_date'   		=> date('Y-m-d',strtotime($this->input->post('start_date'))),
			'end_date'   		=> date('Y-m-d',strtotime($this->input->post('end_date'))),
			'modified_dttm' 	=> date('Y-m-d H:i:s'),
		);
		$update=$this->common_model->update(CAMPAIGNS,$updateData,array('id'=>$campaign_id));
		if($update){
			$totlalImage=count($imageLoc)-1;
			//echo"<pre>";print_r($_POST);die;
			if($totlalImage>0){
				$targetDirAssets = "assets/campaign/banner/";
				if(!empty($this->input->post('camp_loation_id')[0]))
				{
					$this->db->select('id,location_image');
					$this->db->where_not_in('id', array_filter($this->input->post('camp_loation_id')));
					$this->db->where('campaign_id', $campaign_id);
					$this->db->from('campaign_samples_location');
					$query = $this->db->get();
					$loclist=$query->result();
					if(!empty($loclist))
					{
						foreach($loclist as $rel)
						{
							$this->db->where('id', $rel->id);
							$this->db->delete('campaign_samples_location');
							unlink($targetDirAssets.$rel->location_image);
						}
					}
				}
				for($i=0;$i<$totlalImage;$i++) 
				{
					$lid=$this->input->post('camp_loation_id')[$i];
					$url=$this->input->post('url')[$i];
					$lname=$this->input->post('lname')[$i];
					if($imageLoc[$i]!='')
					{
						$imgl = $imageLoc[$i];	 
						$image_parts = explode(";base64,", $imgl);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type  	= $image_type_aux[1];
						$image_base64 	= base64_decode($image_parts[1]);
						$uniueId 		= uniqid();
						$file 			= $targetDirAssets . $uniueId .'_avl_loc.'.$image_type;
						$assets 		= $uniueId.'_avl_loc.'.$image_type;
						$path 			= base_url().'assets/campaign/banner/'.$assets;
						file_put_contents($file, $image_base64);
						
						$insertMedia=array('id'=> $lid,'campaign_id'=> $campaign_id,'location_name'=> $lname,'url'=> $url,'location_image'=> $assets,'is_active'=> 1,'created_dttm'=> date('Y-m-d H:i:s'),
						'modified_dttm' => date('Y-m-d H:i:s'));
					}
					else
					{
						$insertMedia=array('id'=> $lid,'campaign_id'=> $campaign_id,'location_name'=> $lname,'url'=> $url,'is_active'=> 1,'created_dttm'=> date('Y-m-d H:i:s'),
						'modified_dttm' => date('Y-m-d H:i:s'));
					}
					if($lid==''){
						
						$assetid=$this->common_model->insert('campaign_samples_location',$insertMedia);
					}
					else{
						
						$assetid=$this->common_model->update('campaign_samples_location',$insertMedia,array('id'=>$lid));
					}
					$insertMedia=array();
				}
			}
			
			if(!empty(array_filter($camp_banner_id))){
				$this->db->where_not_in('id', array_filter($camp_banner_id));
				$this->db->where('campaign_id', $campaign_id);
				$this->db->delete(CAMPAIGNBANNERS);
			}
			
			for ($i=0; $i < count($imgupload)-1 ; $i++) { 
				if($imgupload[$i]!=''){
					$targetDirAssets = "assets/campaign/banner/";			
					$data = $imgupload[$i];	 
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
					$image_base64 	= base64_decode($image_parts[1]);
					$uniueId 		= uniqid();
					$file 			= $targetDirAssets . $uniueId . '.'.$image_type;
					$assets 		= $uniueId . '.'.$image_type;
					$path 			= base_url().'assets/campaign/banner/'.$assets;
					file_put_contents($file, $image_base64);
					//$destination2="/var/www/html/isamplez/app/webroot/campaign/banner/".$uniueId . '.'.$image_type;;
					//file_put_contents($destination2, $image_base64);
					if($camp_banner_id!='')
						$id         = $camp_banner_id[$i];	
					else
						$id='';
					
				}
				else{
					if($banner_url[$i]!='' && $banner_types[$i]!=''){
						$assets=$banner_url[$i];
						$banner_type = $banner_types[$i];
						$id         = $camp_banner_id[$i];	
					}
					else{
						$assets='';
						$banner_type = '';
						$id='' ;
					}		
				}
				if($makedefault==$i)
						$cover_image=1;
					else
						$cover_image=0;
				$insertMedia=array(
					'id'                => $id,
					'campaign_id'   	=> $campaign_id,
					'banner_type'   	=> $banner_type,
					'cover_image'		=> $cover_image,
					'banner_url' 		=> $assets,
					'is_active' 		=> 1,
					'created_dttm' 	   	=> date('Y-m-d H:i:s'),
					'modified_dttm' 	=> date('Y-m-d H:i:s'),
				);
				if($id==''){
					unset($insertMedia['id']);
					$assetid=$this->common_model->insert(CAMPAIGNBANNERS,$insertMedia);
				}
				else{
					unset($insertMedia['created_dttm'],$insertMedia['is_active']);
					$assetid=$this->common_model->update(CAMPAIGNBANNERS,$insertMedia,array('id'=>$id));
				}
				/*if(!empty($asset_url)){
					for ($k=0; $k < count($asset_url) ; $k++) { 
					if(file_exists("assets/campaign/banner/".$asset_url[$k]) && $imgupload[$i]!=$asset_url[$k])
						unlink("assets/campaign/banner/".$asset_url[$k]);
					}
				}*/
			}
			if($is_push==1){
				$is_upcoming = 0;
				$campData = $this->common_model->getRowData(CAMPAIGNS,'campaign_name,start_date,end_date',array('id'=>$campaign_id));
				$user_to_id = $this->common_model->getResultData(USERS,'id',array('registration_status'=>'2','is_active'=>'1'));
				//$msg="A new campaign '".$campData->campaign_name."' launched.";
				$started = date('d M Y',strtotime($campData->start_date));
				$ended = date('d M Y',strtotime($campData->end_date));
				$msg="'".$campData->campaign_name."' sample campaign dates have changed. Now, New validity is from '".$started."' to '".$ended."'.";
				
				if($campData->start_date>date('Y-m-d'))
				{
					//$msg="An existing upcoming sample campaign '".$campData->campaign_name."' date is changed. Now, Start Date '".$started."' and End Date is '".$ended."'.";
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
			   echo $campaign_id;
		}
		else
			echo "0";
		

	}
	public function campaign_detail_samples($brand_id='',$campaign_id=''){
		$data = array();
		$conditions = array();
		$status = "camp.id ='".$campaign_id."'";
		$data['brand_id'] = $brand_id; 
		$data['campaign_id'] = $campaign_id;
		
		$data['campDtl'] = $this->campaign_model->getRows($status,$conditions); 
		$data['media']=$this->common_model->getResultData(CAMPAIGNBANNERS,'*',array('campaign_id'=>$campaign_id));
		$data['coveImage'] = $this->common_model->getField(CAMPAIGNBANNERS,'banner_url',array('campaign_id'=>$campaign_id,'cover_image'=>'1','banner_type'=>'1')); 
		//echo $this->db->last_query();
		//$data['media']=$this->common_model->getResultData(CAMPAIGNS,'*',array('campaign_id'=>$campaign_id));
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Campaign Detail Samples';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_detail_in');
		$this->load->view('campaign_detail_samples',true);
		$this->load->view('common/footer');

	}
	
	public function campaign_detail_audience($brand_id='',$campaign_id=''){
		$data = array();
		$conditions = array();
		$data['brand_id'] 	 = $brand_id; 
		$data['campaign_id'] = $campaign_id;
		$status = "camp.id ='".$campaign_id."'";
		$data['campDtl'] = $this->campaign_model->getRows($status,$conditions); 

		$data['media']=$this->common_model->getResultData(CAMPAIGNBANNERS,'*',array('campaign_id'=>$campaign_id));
		$data['coveImage'] = $this->common_model->getField(CAMPAIGNBANNERS,'banner_url',array('campaign_id'=>$campaign_id,'cover_image'=>'1','banner_type'=>'1')); 
		//$data['audienceData']= $this->common_model->getRowData(TARGET_AUDIENCE,'*',array('campaign_id'=>$campaign_id,'brand_id'=>$brand_id));
		$data['audienceData']= $this->targetAudience_model->getTargetAudience(array('campaign_id'=>$campaign_id,'brand_id'=>$brand_id));
		$targetAudienceId=$data['audienceData']->id;
		$interest_ques_id=explode(',',$data['audienceData']->interest_ques_id);
		$interestOptions=explode(',',$data['audienceData']->interests);
		$interest_id=explode(',', $data['audienceData']->interest_id);
		$interest_title=explode(',', $data['audienceData']->interest_title);
		$option_text=explode(',', $data['audienceData']->option_text);
		$newInterest=array();
		$newInterestTitle=array();
		foreach($interest_id as $key => $value) {
			$keyArray=array_keys($interest_ques_id,$value);
			$arrayValues=array_intersect_key($interestOptions,array_flip($keyArray));
			$newInterest[$value]=implode(',', $arrayValues);
			$keyOptionArray=array_keys($option_text,$value);
		$arrayOptionValues=array();
		foreach ($arrayValues as $index => $value) {
			$arrayOptionValues[$index]=$option_text[$index];
		}
		$newInterestTitle[$interest_title[$key]]=implode(',', $arrayOptionValues);
		}
		$data['inetrests']=$newInterestTitle;
		$con="c.targetaudience_id='".$targetAudienceId."'";
		$data['campBehaviour']= $this->targetAudience_model->campData($con);
		$con="p.targetaudience_id='".$targetAudienceId."'";
		$data['postBehaviour']= $this->targetAudience_model->postData($con);
		
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brand Post Details';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_detail_in');
		$this->load->view('campaign_detail_audience',true);
		$this->load->view('common/footer');

	}
	public function campaign_detail_reviews($brand_id='',$campaign_id=''){
		$data = array();
		$conditions = array();
		
		$data['brand_id'] = $brand_id; 
		$data['campaign_id'] = $campaign_id;
		$status = "camp.id ='".$campaign_id."'";
		$data['campDtl'] = $this->campaign_model->getRows($status,$conditions); 

		$data['media']=$this->common_model->getResultData(CAMPAIGNBANNERS,'*',array('campaign_id'=>$campaign_id));
		$data['coveImage'] = $this->common_model->getField(CAMPAIGNBANNERS,'banner_url',array('campaign_id'=>$campaign_id,'cover_image'=>'1','banner_type'=>'1')); 
		//$data['media']=$this->common_model->getResultData(CAMPAIGNS,'*',array('campaign_id'=>$campaign_id));
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brand Post Details';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_detail_in');
		$this->load->view('campaign_detail_reviews',true);
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
						//echo $this->db->last_query();die;
						//print_r($countRows);die;
						if($countRows>0){
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
	
}


?>