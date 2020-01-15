<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TargetAudience extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('common_model','targetAudience_model'));
		$this->load->library('Ajax_pagination');
		$this->load->helper('mail_helper');
		$this->perPage = 10;
	} 

/*	public function index(){ 
		$data = array();
		$conditions = array();
		$status='';
		$offset = 0; 
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		$keywords = $this->input->get('keywords');
		if($this->input->get('keywords'))
			$status = "  (u.name LIKE '%".$keywords."%')";

		$data['brandsList'] = $this->campaign_model->getRows($status,$conditions); 
		//echo $this->db->last_query();die;
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Brands';
		$this->load->view('common/header',$data);
		$this->load->view('brands',true);
		$this->load->view('common/footer');

	}*/
	public function getCampaigns($page){ 
		$data = array();
		$conditions = array();
		$status='';
		$offset = 0;
		$data ='';
		$data=array('results'=>'','totalRecords'=>0);
		if($page)
			$offset = 10*$page;
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		//print_r($_POST);die;
		$search_text = $this->input->post('search_text');
		$brand_id    = $this->input->post('brand_id');
		$status = " camp.brand_id='".$brand_id."'";
		if($search_text)
			$status .= " AND  (b.campaign_name LIKE '%".$search_text."%')";
		$brandsList = $this->campaign_model->getRows($status,$conditions);
		//$brandsListy = $this->campaign_model->NPS();
		
		//echo $this->db->last_query();die;
		$data['totalRecords']=$totalRecords = $this->campaign_model->getRows("",array(),'count');
		if($brandsList){
			foreach ($brandsList  as  $value) {
				if($value->start_date <= date('Y-m-d') && $value->end_date >=date('Y-m-d')){
					$badge='<b class="badge badge-pill badge-success"> </b>';
				}
				else{
					$badge="";
				}
				$data['results'] .= '<tr><td class="text-center">'.$badge.'</td><td><a href="'.base_url('campaign-detail-samples/'.$value->id).'" class="user-link font-weight-bold">'.$value->campaign_name.'</a></td>
				<td class="text-center">'.date("d M Y",strtotime($value->start_date)).'</td>
                <td class="text-center">'.date("d M Y",strtotime($value->end_date)).'</td>
                <td class="text-center text-primary font-weight-bold">'.$value->total_samples_redeemed.'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_promo_redeemed.'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_post.'</td>
				<td class="text-center text-primary font-weight-bold">'.$value->total_post.'</td>
				<td class="text-center"><a href="'.base_url('campaign_detail_samples/'.$value->id).'" class="text-link">View</a>
				<a href="#" class="text-link ml-4">Edit</a></td>
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
		$data['title'] = 'Brand Details';
		$this->load->view('common/header',$data);
		$this->load->view('users_detail',true);
		$this->load->view('common/footer');

	}
	public function create($brand_id='',$campaign_id=''){ 	
		$data['brand_id']=$brand_id;
		$data['campaign_id']=$campaign_id;
		$data['sampleData']=$this->common_model->getResultData(CAMPAIGN_SAMPLES,'*',array('campaign_id'=>$campaign_id));
		$campCount=0;
		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$campaign_id));
		if($review_id)
			$campCount= $this->common_model->mysqlNumRows(REVIEW_QUESTIONS,'id',array('review_id'=>$review_id)); 
		$data['qusDataCount']=$campCount;
		$data['campData']=$this->common_model->getResultData(CAMPAIGNS,'*',array('id !='=>$campaign_id,'brand_id'=>$brand_id,'is_active'=>'1'));
		$data['ageBracket']=$this->common_model->getResultData(AGEBRACKET,'*',array('is_active'=>'1'));
		$data['inetrestMaster']=$this->common_model->getResultData(INTEREST_MASTER,'*',array('is_active'=>'1'));
		$data['postData']=$this->common_model->getResultData(WALL_POSTS,'*',array('brand_id'=>$brand_id,'is_active'=>'1'));
		foreach ($data['inetrestMaster'] as $key => $value) {
			$data['inetrestOption'][]=$this->common_model->getResultData(INTEREST_OPTIONS,'*',array('is_active'=>'1','interest_id'=>$value->id));
		}
		//echo"<pre>";print_r($data);die; 
		$count= $this->common_model->mysqlNumRows(TARGET_AUDIENCE,'id',array('campaign_id'=>$campaign_id,'brand_id'=>$brand_id)); 
		if($count>0)
			redirect('edit-targetAudience/'.$brand_id.'/'.$campaign_id);
		else{
			$this->session->set_userdata(array('menu'=>'Brands'));
			$data['title'] = 'Create Campaign Audience';
			$this->load->view('common/header',$data);
			$this->load->view('campaign_audience',true);
			$this->load->view('common/footer');
		}

	}
	public function edit($brand_id='',$campaign_id=''){ 	
		$data['brand_id']=$brand_id;
		$data['campaign_id']=$campaign_id;
		$data['campData']=$this->common_model->getResultData(CAMPAIGNS,'*',array('id !='=>$campaign_id,'brand_id'=>$brand_id,'is_active'=>'1'));
		$data['ageBracket']=$this->common_model->getResultData(AGEBRACKET,'*',array('is_active'=>'1'));
		$data['inetrestMaster']=$this->common_model->getResultData(INTEREST_MASTER,'*',array('is_active'=>'1'));
		$data['postData']=$this->common_model->getResultData(WALL_POSTS,'*',array('brand_id'=>$brand_id,'is_active'=>'1'));
		foreach ($data['inetrestMaster'] as $key => $value) {
			$data['inetrestOption'][]=$this->common_model->getResultData(INTEREST_OPTIONS,'*',array('is_active'=>'1','interest_id'=>$value->id));
		}
		 
		$data['audienceData'] = $this->common_model->getRowData(TARGET_AUDIENCE,'*',array('campaign_id'=>$campaign_id,'brand_id'=>$brand_id));
		
		$targetAudienceId=$data['audienceData']->id;
		$data['campBehaviour']= $this->common_model->getResultData(CAMP_BEHAVIOUR,'*',array('targetaudience_id'=>$targetAudienceId)); 
		$con="p.targetaudience_id='".$targetAudienceId."'";
		$data['postBehaviour']= $this->targetAudience_model->postData($con);  
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Create Campaign Audience';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_audience_edit',true);
		$this->load->view('common/footer');

	}
	public function add(){
		//print_r($_POST);
		//die;
		$gender='';
		$age='';
		$interests='';
		$newinterestQuestId='';
		$camp_id=$this->input->post('camp_id');
		$post_id=$this->input->post('post_id');
		$campaign_id=$this->input->post('campaign_id');
		$interest_ques_id=$this->input->post('interest_ques_id');
		if($this->input->post('gender')){
			$gender=$this->input->post('gender');
			$gender=implode(',', $gender);
		}
		if($this->input->post('age')){
			$age=$this->input->post('age');
			$age=implode(',', $age);
		}
		
		$newInerest=array();
		$newinterestQuestIdArr=array();
		for ($i=0; $i <18 ; $i++) { 
			if($this->input->post('interest_option_'.$i)){
				$interest_options=$this->input->post('interest_option_'.$i);
				if(!empty($interest_options))
				$newInerest[]=implode(',',$interest_options);
				$interestQuestId=$this->input->post('interest_ques_id'.$i);
				if(!empty($interestQuestId))
				$newinterestQuestIdArr[]=implode(',',$interestQuestId);
			}
		}
		if(!empty($newInerest))
		$interests=implode(',', $newInerest);
		if(!empty($newinterestQuestIdArr))
		$newinterestQuestId=implode(',', $newinterestQuestIdArr);
		$insertData=array(
			'campaign_id'   	=> $this->input->post('campaign_id'),
			'brand_id'   		=> $this->input->post('brand_id'),
			'gender'   			=> $gender,
			'age'   			=> $age,
			'interests' 		=> $interests,
			'interest_ques_id' 	=> $newinterestQuestId,
			'is_active' 		=> 1,
			'created_dttm' 	   	=> date('Y-m-d H:i:s'),
			'modified_dttm' 	=> date('Y-m-d H:i:s'),
		);

		//print_r($insertData);die;
		$targetAurdienceId=$this->common_model->insert(TARGET_AUDIENCE,$insertData);

		if($targetAurdienceId){
			for ($i=0; $i <18 ; $i++) { 
				$this->db->delete(AUDIENCE_INTEREST,array('targetaudience_id'=>$targetAurdienceId));
				if($this->input->post('interest_option_'.$i)){
					$interest_optionss=$this->input->post('interest_option_'.$i);
					$newInerestt=implode(',',$interest_optionss);
					$interestQuestIdd=$this->input->post('interest_ques_id'.$i);
					$campDatas=array(
							'targetaudience_id' 	=> $newInerestt,
							'interest_ques_id'   	=> $interestQuestIdd,
							'interest_options_id'   => $newInerest,
							'is_active' 			=> 1,
							'created_dttm' 	   		=> date('Y-m-d H:i:s'),
							'modified_dttm' 		=> date('Y-m-d H:i:s'),
						);
					$audienceId=$this->common_model->insert(AUDIENCE_INTEREST,$campDatas);
				}
			}
			if(!empty($camp_id)){
				for ($i=1; $i <=count($camp_id) ; $i++) { 
					if($this->input->post('camp_behaviour_'.$i)){
					$camp_behaviour=$this->input->post('camp_behaviour_'.$i);
					$camp_behaviour=implode(',',$camp_behaviour);
					$campData=array(
							'targetaudience_id' 	=> $targetAurdienceId,
							'campaign_id'   		=> $camp_id[$i],
							'camp_behaviour'   		=> $camp_behaviour,
							'is_active' 			=> 1,
							'created_dttm' 	   		=> date('Y-m-d H:i:s'),
							'modified_dttm' 		=> date('Y-m-d H:i:s'),
						);
						$campBehaviourId=$this->common_model->insert(CAMP_BEHAVIOUR,$campData);
					}
				}
			}
			if(!empty($post_id)){
				for ($i=0; $i <count($post_id) ; $i++) { 
					if($this->input->post('post_behaviour_'.($i+1))){
					$post_behaviour=$this->input->post('post_behaviour_'.($i+1));
					$post_behaviour=implode(',',$post_behaviour);
					$postData=array(
							'targetaudience_id' 	=> $targetAurdienceId,
							'post_id'   			=> $post_id[$i],
							'post_behaviour'   		=> $post_behaviour,
							'is_active' 			=> 1,
							'created_dttm' 	   		=> date('Y-m-d H:i:s'),
							'modified_dttm' 		=> date('Y-m-d H:i:s'),
						);

						//print_r($postData);die;
						$postBehaviourId=$this->common_model->insert(POST_BEHAVIOUR,$postData);
					}
				}
			}
		echo $campaign_id;
		}
		else
			echo "0";
	}
	public function editAudience(){
		//print_r($_POST);
		$gender='';
		$age='';
		$interests='';
		$newinterestQuestId='';
		$targetaudience_id=$this->input->post('id');
		$camp_behaviour_id=$this->input->post('camp_behaviour_id');
		$post_behaviour_id=$this->input->post('post_behaviour_id');
		$interest_ques_id=$this->input->post('interest_ques_id');
		$camp_id=$this->input->post('camp_id');
		$post_id=$this->input->post('post_id');
		$campaign_id=$this->input->post('campaign_id');
		if($this->input->post('gender')){
			$gender=$this->input->post('gender');
			$gender=implode(',', $gender);
		}
		if($this->input->post('age')){
			$age=$this->input->post('age');
			$age=implode(',', $age);
		}
		
		$newInerest=array();
		$newinterestQuestIdArr=array();
		for ($i=0; $i <18 ; $i++) { 
			if($this->input->post('interest_option_'.$i)){
				$interest_options=$this->input->post('interest_option_'.$i);
				if(!empty($interest_options))
				$newInerest[]=implode(',',$interest_options);
				$interestQuestId=$this->input->post('interest_ques_id'.$i);
				if(!empty($interestQuestId))
				$newinterestQuestIdArr[]=implode(',',$interestQuestId);
			}
		}
		if(!empty($newInerest))
		$interests=implode(',', $newInerest);
		if(!empty($newinterestQuestIdArr))
		$newinterestQuestId=implode(',', $newinterestQuestIdArr);
		$updateData=array(
			'campaign_id'   	=> $this->input->post('campaign_id'),
			'brand_id'   		=> $this->input->post('brand_id'),
			'gender'   			=> $gender,
			'age'   			=> $age,
			'interests' 		=> $interests,
			'interest_ques_id' 	=> $newinterestQuestId,
			'modified_dttm' 	=> date('Y-m-d H:i:s'),
		);

		//print_r($updateData);die;
		$targetAurdienceUpdate=$this->common_model->update(TARGET_AUDIENCE,$updateData,array('id'=>$targetaudience_id));

		if($targetAurdienceUpdate){
			$this->db->delete(AUDIENCE_INTEREST,array('targetaudience_id'=>$targetaudience_id));
			for ($i=4; $i <18 ; $i++) { 
				if($this->input->post('interest_option_'.$i)){
					$interest_optionss=$this->input->post('interest_option_'.$i);
					$newInerestt=implode(',',$interest_optionss);
					$interestQuestId=$this->input->post('interest_ques_id'.$i);
					//print_r($interestQuestId);
					$interestQuestIdd=current($interestQuestId);
					
					$campDatas=array(
							'targetaudience_id' 	=> $targetaudience_id,
							'interest_ques_id'   	=> $interestQuestIdd,
							'interest_options_id'   => $newInerestt,
							'is_active' 			=> 1,
							'created_dttm' 	   		=> date('Y-m-d H:i:s'),
							'modified_dttm' 		=> date('Y-m-d H:i:s'),
						);
					$audienceId=$this->common_model->insert(AUDIENCE_INTEREST,$campDatas);
				}
			}
			if(!empty($camp_id)){
				$this->db->delete(CAMP_BEHAVIOUR,array('targetaudience_id'=>$targetaudience_id));
				for ($i=1; $i <=count($camp_id) ; $i++) { 
					if($camp_id[$i]!=''){
						if($this->input->post('camp_behaviour_'.$i)){
						$camp_behaviour=$this->input->post('camp_behaviour_'.$i);
						$camp_behaviour=implode(',',$camp_behaviour);
						if($camp_behaviour_id[$i]!='')
							$id=$camp_behaviour_id[$i];
						else
							$id='';
						$campData=array(
								'id'                    => $id,
								'targetaudience_id' 	=> $targetaudience_id,
								'campaign_id'   		=> $camp_id[$i],
								'camp_behaviour'   		=> $camp_behaviour,
								'is_active' 			=> 1,
								'created_dttm' 	   		=> date('Y-m-d H:i:s'),
								'modified_dttm' 		=> date('Y-m-d H:i:s'),
							);
						if($id=='')
						unset($campData['id']);
						//print_r($campData);die;
						$campBehaviourId=$this->common_model->insert(CAMP_BEHAVIOUR,$campData);
						}
					}
				}
			}

			if(!empty($post_id)){
				$this->db->delete(POST_BEHAVIOUR,array('targetaudience_id'=>$targetaudience_id));
				for ($i=0; $i <count($post_id) ; $i++) { 
					if($post_id[$i]!=''){
						if($this->input->post('post_behaviour_'.($i+1))){
						$post_behaviour=$this->input->post('post_behaviour_'.($i+1));
						$post_behaviour=implode(',',$post_behaviour);
						if($post_behaviour_id[$i+1]!='')
							$id=$post_behaviour_id[$i+1];
						else
							$id='';
						$postData=array(
								'id'                    => $id,
								'targetaudience_id' 	=> $targetaudience_id,
								'post_id'   			=> $post_id[$i],
								'post_behaviour'   		=> $post_behaviour,
								'is_active' 			=> 1,
								'created_dttm' 	   		=> date('Y-m-d H:i:s'),
								'modified_dttm' 		=> date('Y-m-d H:i:s'),
							);
						if($id=='')
						unset($postData['id']);

							//print_r($postData);die;
							$postBehaviourId=$this->common_model->insert(POST_BEHAVIOUR,$postData);
						}
					}
				}
			}
		echo $campaign_id;
		}
		else
			echo "0";
	}
	
}


?>