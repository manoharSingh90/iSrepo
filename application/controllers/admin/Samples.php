<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Samples extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('common_model','sample_model'));
		$this->perPage = 10;
	} 
	public function getSamples($page){ 
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
		$search_text = $this->input->post('search_text');
		$campaign_id    = $this->input->post('campaign_id');
		$status = " campVend.is_active='1'  AND vendM.is_active='1' AND campVend.campaign_id='".$campaign_id."'";
		$vendMachineList = $this->sample_model->getRows($status,$conditions);
		//echo $this->db->last_query();die;
		$data['totalRecords']=$totalRecords = $this->sample_model->getRows($status,array(),'count');
		if($vendMachineList){
			foreach ($vendMachineList  as  $value) {
				//$Instock=$value->vend_no_of_samples-$value->vend_no_of_sample_used;
				$Instock=$value->vend_no_of_samples;
				if($Instock<=10){
					$badge='<b class="badge badge-pill badge-danger">!</b>';
					$text='text-danger';
				}
				else{
					$badge="";
					$text='';
				}
			 	$address=$value->location_address;
	            if($value->landmark)
	              $address .= ', '.$value->landmark;
	            if($value->city_name)
	              $address .= ', '.$value->city_name;
	            if($value->country_name)
	              $address .= ' '.$value->country_name;
	            if($value->postal_code)
	              $address .= ' '.$value->postal_code;

				$addSample="addSample('".$value->id."')";
				$data['results'] .= '<tr><td class="user-link font-weight-bold">'.$value->location_name.'</a></td>
				<td>'.$address.'</td>
				<td class="text-center '.$text.'" id="stock_id_'.$value->id.'">'.$Instock.' '.$badge.'</td>
				<td class="text-center"><a href="#" class="text-link" data-toggle="modal" data-target="#addModal" onclick="'.$addSample.'">Add Sample</a></td>
				<td class="text-center"><a  class="text-danger delete"  id="'.$value->id.'" style="cursor:pointer">Remove</a></td>
				</tr>';
			} 
		}
		else
			$data['results']='<tr><td  colspan="4" class="text-center">No record exists.</td></tr>';
		if(floor($data['totalRecords']/$this->perPage)<$page)
			$data['results']="";
		echo json_encode($data);
       // echo $data['users'];

	}

	public function create($brand_id='',$campaign_id=''){ 
		$data['vendMachines'] = $this->sample_model->getVendingList(array('is_active'=>'1')); 
		$data['brand_id']=$brand_id;
		$data['campaign_id']=$campaign_id;
		$data['sampleData']=$this->common_model->getResultData(CAMPAIGN_SAMPLES,'*',array('campaign_id'=>$campaign_id));
		$countQus = 0;
		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$campaign_id));
		if($review_id)
			$campCount= $this->common_model->mysqlNumRows(REVIEW_QUESTIONS,'id',array('review_id'=>$review_id)); 
		$data['qusDataCount']=$countQus;
		$data['campData']= $this->common_model->getRowData(CAMPAIGNS,'start_date,end_date',array('id'=>$campaign_id)); 
		$campCount= $this->common_model->mysqlNumRows(CAMPAIGN_VENDS,'id',array('campaign_id'=>$campaign_id)); 
		if($campCount>0)
			redirect('edit-samples/'.$brand_id.'/'.$campaign_id);
		else{
			$this->session->set_userdata(array('menu'=>'Brands'));
			$data['title'] = 'Create Samples';
			$this->load->view('common/header',$data);
			$this->load->view('campaign_sample_create',true);
			$this->load->view('common/footer');
		}

	}
	public function edit($brand_id='',$campaign_id=''){ 
		$data['vendMachines'] = $this->sample_model->getVendingList(array('is_active'=>'1')); 
		$data['brand_id']	= $brand_id;
		$data['campaign_id']= $campaign_id;
		$data['sampleData']=$this->common_model->getResultData(CAMPAIGN_SAMPLES,'*',array('campaign_id'=>$campaign_id));
		$countQus = 0;
		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$campaign_id));
		if($review_id)
			$campCount= $this->common_model->mysqlNumRows(REVIEW_QUESTIONS,'id',array('review_id'=>$review_id)); 
		$data['qusDataCount']=$countQus;
		$data['campData']	= $this->common_model->getRowData(CAMPAIGNS,'start_date,end_date,total_campaign_samples',array('id'=>$campaign_id)); 
		$data['campVend']	= $this->common_model->getResultData(CAMPAIGN_VENDS,'*',array('campaign_id'=>$campaign_id)); 
		//print_r($data['campData']);die;
		//$data['campData']= $this->common_model->getRowData(CAMPAIGNS,'*',array('id'=>$campaign_id)); 
		$data['campSampleData']= $this->common_model->getRowData(CAMPAIGN_SAMPLES,'*',array('campaign_id'=>$campaign_id)); 
		
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Edit Samples';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_sample_edit',true);
		$this->load->view('common/footer');
		

	}
	public function addSamples()
	{
		$id=$_POST['id'];
		$total_samples=$_POST['total_samples'];
		$campaign_id=$_POST['campaign_id'];
		//print_r($_POST);die;
		//$vend_no_of_samples=$this->common_model->getField(CAMPAIGN_VENDS,'vend_no_of_samples',array('id'=>$id));
		$totalSamples = $this->common_model->getRowData(CAMPAIGN_VENDS,'vend_no_of_samples,vend_no_of_sample_used',array('id'=>$id));
		$TotalNewSample=$totalSamples->vend_no_of_samples+$total_samples;
		$updateSample=array(
			'vend_no_of_samples'   	=> $TotalNewSample,
			'modified_dttm' 	   	=> date('Y-m-d H:i:s'),
		);
		$sample=$this->common_model->update(CAMPAIGN_VENDS,$updateSample,array('id'=>$id));
		if($sample){
		if($campaign_id)
		//$this->updateCampaignStock($campaign_id);
			echo $TotalNewSample-$totalSamples->vend_no_of_sample_used;
		}
		else
			echo $totalSamples->vend_no_of_samples-$totalSamples->vend_no_of_sample_used;
	}
	public function updateCampaignStock($campaign_id,$total_campaign_samples){
		$sample=$this->common_model->update(CAMPAIGNS,array('total_campaign_samples'=>$total_campaign_samples),array('id'=>$campaign_id));
	}

	public function updateCampaignStockOLd($campaign_id){
		$camp=$this->db->query("select sum(vend_no_of_samples) as total_campaign_samples,sum(vend_no_of_sample_used) as total_campaign_samples_used from campaign_vends where campaign_id='".$campaign_id."'")->row();
		//print_r($camp);die;
		$sample=$this->common_model->update(CAMPAIGNS,array('total_campaign_samples'=>$camp->total_campaign_samples,'total_campaign_samples_used'=>$camp->total_campaign_samples_used),array('id'=>$campaign_id));
	}
	public function updateCampaignRating($campaign_id){
		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$campaign_id));
		$rating=$this->db->query("select sum(rating) as rating,count(id) as users from user_reviews where review_id='".$review_id."'")->row();
		$newrating=$rating->rating;
		$users=$rating->users;
		$finalRating="0.00";
		if($users>0)
		$finalRating=number_format($newrating/$users,2);
		//print_r($camp);die;
		$sample=$this->common_model->update(CAMPAIGNS,array('avg_rating'=>$finalRating),array('id'=>$campaign_id));
	}
	public function addCampSamples(){
		//print_r($_POST);die;
		$vending_machine_id=$this->input->post('vending_machine_id');
		$campaign_id=$this->input->post('campaign_id');
		$total_campaign_samples      = $this->input->post('total_campaign_samples');
		if($campaign_id){
			$insertData=array(
				'campaign_id'   	=> $this->input->post('campaign_id'),
				'start_date'   		=> date('Y-m-d 00:00:00',strtotime($this->input->post('start_date'))),
				'end_date'   		=> date('Y-m-d 23:59:59',strtotime($this->input->post('end_date'))),
				'is_active' 		=> 1,
				'created_dttm' 	   	=> date('Y-m-d H:i:s'),
				'modified_dttm' 	=> date('Y-m-d H:i:s'),
			);
			$sample_id=$this->common_model->insert(CAMPAIGN_SAMPLES,$insertData);
			//$sample_id=1;
			if($sample_id){
				$sample_vending_machine=0;
				for ($i=0; $i < count($vending_machine_id) ; $i++) { 
					$vending_machineid=$this->input->post('sample_vending_machine_'.$i);
					if($vending_machineid!=''){
						$CampVenDtl=array(
							'campaign_id'   	=> $campaign_id,
							'vend_machine_id'   => $vending_machineid,
						//	'vend_no_of_samples' => $total_samples[$i],
							'is_active' 		=> 1,
							'created_dttm' 	   	=> date('Y-m-d H:i:s'),
							'modified_dttm' 	=> date('Y-m-d H:i:s'),
						);
						$this->common_model->insert(CAMPAIGN_VENDS,$CampVenDtl);
					}

				}
				
			}
			$this->updateCampaignStock($campaign_id,$total_campaign_samples);
			$this->updateCampaignRating($campaign_id);
			echo $campaign_id;
		}
		else
			echo "0";
	}
	public function editCampSamples(){
		//echo "<pre>";
		//print_r($_POST);die;
		$vending_machine_id 			= $this->input->post('vending_machine_id');
		$campvending_machine_id			= $this->input->post('campvending_machine_id');
		$campaign_id 					= $this->input->post('campaign_id');
		$brand_id 						= $this->input->post('brand_id');
		$sample_id 						= $this->input->post('sample_id');
		$total_samples      			= $this->input->post('total_samples');
		$vend_no_of_sample_used      	= $this->input->post('vend_no_of_sample_used');
		$campvending_machine_id 	    = $this->input->post('campvending_machine_id');
		$total_campaign_samples      	= $this->input->post('total_campaign_samples');
		if($sample_id){
			$insertData=array(
				'campaign_id'   	=> $this->input->post('campaign_id'),				
				'start_date'   		=> date('Y-m-d 00:00:00',strtotime($this->input->post('start_date'))),
				'end_date'   		=> date('Y-m-d 23:59:59',strtotime($this->input->post('end_date'))),
				'modified_dttm' 	=> date('Y-m-d H:i:s'),
			);
			$updateSample=$this->common_model->update(CAMPAIGN_SAMPLES,$insertData,array('id'=>$sample_id));
			//$sample_id=1;
			if($updateSample){
				$sample_vending_machine=0;
				$this->db->delete(CAMPAIGN_VENDS,array('campaign_id'=>$campaign_id));
				for ($i=0; $i < count($vending_machine_id) ; $i++) { 
					$sample_vending_machine=$this->input->post('sample_vending_machine_'.$i);
					if($campvending_machine_id[$i]!=''){
						$id=$campvending_machine_id[$i];
					}
					else
						$id='';
					if($sample_vending_machine!=''){
						$CampVenDtl=array(
							'id'   						=> $id,
							'campaign_id'   			=> $campaign_id,
							'vend_machine_id'   		=> $sample_vending_machine,
							'vend_no_of_samples'		=> $total_samples[$i],
							'vend_no_of_sample_used'	=> $vend_no_of_sample_used[$i],
							'is_active' 				=> '1',
							'created_dttm' 	   			=> date('Y-m-d H:i:s'),
							'modified_dttm' 			=> date('Y-m-d H:i:s'),
						);
						if($id==''){
							//unset($CampVenDtl['id'],$CampVenDtl['created_dttm'],$CampVenDtl['is_active']);
							unset($CampVenDtl['id']);
						}
						$this->common_model->insert(CAMPAIGN_VENDS,$CampVenDtl);
						
					}

				}
			}

			$this->updateCampaignStock($campaign_id,$total_campaign_samples);
			$this->updateCampaignRating($campaign_id);
			echo $campaign_id;
		}
		else
			echo "0";
	}
	public function changeStatus()
	{
		$id=$this->input->post('id');
		$is_active=$this->input->post('is_active');
		$updateChallenge=array('is_active'=>$is_active);
		$returnid=$this->common_model->update(CAMPAIGN_VENDS,$updateChallenge,array('id'=>$id));
		echo $returnid;

	}
	public function delete($campaign_id='')
	{
		$id = $this->input->post('id');
		if($id){
			$updateData=array('is_active'=>0);
			$returnid=1;
			$returnid=$this->common_model->update(CAMPAIGN_VENDS,$updateData,array('id'=>$id));
			if($returnid){
				//if($campaign_id)
				//$this->updateCampaignStock($campaign_id,"0");
			}
			//echo $this->db->last_query();die;
			echo $returnid;
		}

	}
	
}


?>