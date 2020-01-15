<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VendingMachine extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('common_model'));
		$this->load->helper('mail_helper');
		$this->perPage = 10;
	}
	public function create($brand_id='',$campaign_id=''){ 
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['brand_id'] = $brand_id;
		$data['campaign_id'] = $campaign_id;
	   	$data['state'] = $this->common_model->getResultData(STATE,'*');
	   	$data['country'] = $this->common_model->getResultData(COUNTRY,'*');
		$data['title'] = 'Add Vending machines';
		$this->load->view('common/header',$data);
		$this->load->view('vending_create',true);
		$this->load->view('common/footer');
		

	}
	public function add()
	{
		//print_r($_POST);die;		
		$campaign_id 		= $this->input->post('campaign_id');
		$location_name   	= $this->input->post('location_name');
		$vending_machine_code   	= $this->input->post('vending_machine_code');
		$location_address 	= $this->input->post('location_address');
		$location_address2  = $this->input->post('location_address2');
		$location_address3  = $this->input->post('location_address3');
		$country				= $this->input->post('country');
		$city   			= $this->input->post('city');
		$postal_code     	= $this->input->post('postal_code');
		$landmark   		= $this->input->post('landmark');
		$vend_lat			= $this->input->post('vend_lat');
		$vend_long			= $this->input->post('vend_long');

		if($campaign_id){
			for ($i=0; $i < count($location_name); $i++) { 
				/*if($vending_machine_code[$i]) != '') {
				   $is_unique =  '|is_unique[users.user_name]'
				} else {
				   $is_unique =  ''
				}

				$this->form_validation->set_rules('vending_machine_code', 'vending_machine_code', 'required|trim|xss_clean'.$is_unique);*/
				if($location_name[$i]!=''){
				$vendingData=array(
					'location_name'   	=> $location_name[$i],
					'vending_machine_code'   	=> $vending_machine_code[$i],
					'location_address'  => $location_address[$i],
					'location_address2' => $location_address2[$i],
					'location_address3' => $location_address3[$i],
					'country'   			=> $country[$i],
					'city'   			=> $city[$i],
					'postal_code'   	=> $postal_code[$i],
					'landmark'   		=> $landmark[$i],
					'vend_lat'   		=> $vend_lat[$i],
					'vend_long'   		=> $vend_long[$i],
					'is_active' 		=> 1,
					'created_dttm' 	   	=> date('Y-m-d H:i:s'),
					'modified_dttm' 	=> date('Y-m-d H:i:s'),
				);
				$vend_machine_id=$this->common_model->insert(VENDING_MACHINES,$vendingData);
				/*if($vend_machine_id){
					$campvendingData=array(
					'campaign_id'   		=> $campaign_id[$i],
					'vend_machine_id'  		=> $vend_machine_id[$i],
					'vend_no_of_samples' 	=> 0,
					'vend_no_of_sample_used'=> 0,
					'is_active' 			=> 1,
					'created_dttm' 	   		=> date('Y-m-d H:i:s'),
					'modified_dttm' 		=> date('Y-m-d H:i:s'),
				);
				$vend_id=$this->common_model->insert(CAMP_VEND,$campvendingData);
				}*/
			}
		}
		echo $campaign_id;
		}
		else
			echo "0";
	}
	public function edit($brand_id='',$campaign_id=''){ 
		$data['brand_id']=$brand_id;
		$data['campaign_id']=$campaign_id;
		$data['reviewQuest']='';
		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$campaign_id));
		if($review_id)
		$data['reviewQuest']= $this->common_model->getResultData(REVIEW_QUESTIONS,'*',array('review_id'=>$review_id)); 
	    $data['reviewAns']=array();
	    if($data['reviewQuest']){
		foreach ($data['reviewQuest'] as $key => $value) {
			$data['reviewAns'][]= $this->common_model->getResultData(REVIEW_ANSWER_OPTIONS,'*',array('question_id'=>$value->id));
		}
		}
		//print_r($data['reviewQuest']);
		//echo "<pre>";
		//print_r($data['reviewAns']);
		//die;
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Edit Questionnaire';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_questionnaire_edit',true);
		$this->load->view('common/footer');

	}
	public function editQuestionnaire()
	{
		//print_r($_POST);die;
		$questionnaire_id = $this->input->post('questionnaire_id');
		$campaign_id = $this->input->post('campaign_id');
		$ques_text   = $this->input->post('ques_text');
		$question_type=$this->input->post('question_type');
		$review_id=$this->input->post('review_id');
		$review_id=$this->input->post('review_id');
		$counter= $this->input->post('counter'); 
		//print_r($_POST);die;
		//die;
		//$this->common_model->update(CAMPAIGNS,array('review_id'=>$review_id),array('id'=>$campaign_id));
		if($campaign_id){

			$this->db->delete(REVIEW_ANSWER_OPTIONS,array('review_id'=>$review_id));
			$this->db->delete(REVIEW_QUESTIONS,array('review_id'=>$review_id));
			//echo $this->db->last_query();
			for ($i=1; $i <= count($ques_text); $i++) { 
		    if($ques_text[$i]!=''){
			if(!empty($questionnaire_id) && array_key_exists($i, $questionnaire_id) && $questionnaire_id[$i]!='')
				$id=$questionnaire_id[$i];
			else
				$id='';
			$reviewQues=array(
				'id'   		        => $id,
				'review_id'   		=> $review_id,
				'ques_order'   		=> 0,
				'ques_text'   		=> $ques_text[$i],
				'ques_type'   		=> $question_type[$i],
				'is_active' 		=> 1,
				'created_dttm' 	   	=> date('Y-m-d H:i:s'),
				'modified_dttm' 	=> date('Y-m-d H:i:s'),
			);
			if($id==''){
					unset($reviewQues['id']);
				}
			$question_id=$this->common_model->insert(REVIEW_QUESTIONS,$reviewQues);
			$answer_text 	= $this->input->post('answer_text'.($i));
			$ansId 	= $this->input->post('ansId'.($i));
			//print_r($answer_text);
			//echo "|";
			//print_r($ansId);
			//echo "|";
			//echo $id;

			//echo "|".$question_id;
			
			if($question_id){
				for ($k=1; $k <= count($answer_text) ; $k++) { 
					if(!empty($ansId) && array_key_exists($k, $ansId) && $ansId[$k]!='')
						$idd=$ansId[$k];
					else
						$idd='';
					if($answer_text[$k]!=''){
						$reviewAns=array(
							'id'                => $idd,
							'question_id'   	=> $question_id,
							'review_id'   		=> $review_id,
							'ans_order'   		=> 0,
							'answer_text'   	=> $answer_text[$k],
							'is_correct' 		=> 1,
							'is_active' 		=> 1,
							'created_dttm' 	   	=> date('Y-m-d H:i:s'),
							'modified_dttm' 	=> date('Y-m-d H:i:s'),
						);
						if($idd==''){
							unset($reviewAns['id']);
						}
						$this->common_model->insert(REVIEW_ANSWER_OPTIONS,$reviewAns);
					}
					//print_r($reviewAns);

				}
				//die;
			}
		}
	}
		//die;

		echo $campaign_id;
		}
		else
			echo "0";
	}
	function checkCode()
	{
		//print_r($_POST);die; 
		$vmcode=$this->input->post('vmcode');
		$machine_code=$this->common_model->mysqlNumRows(VENDING_MACHINES,'vending_machine_code',array('vending_machine_code'=>$vmcode));
		if($machine_code > 0){
			$data['type'] = 'error';
		}else{
			$data['type'] = 'success';
		}
		echo json_encode($data);die;
		//print_r($machine_code);die;
	}
	function getcity()
	{
	    $id=$this->input->post('country_id');
		$conn="country_id =$id";
		$city=$this->common_model->getResultData(CITY,'id,city_name',$conn);
		//$js = 'id="citySelect_0" class="form-control singleselect fl_input"';
		$js='';
        $options = array('' => 'Select City');
        foreach($city as $values){
        $options[$values->id] = $values->city_name;
        }
        echo form_dropdown('city', $options, set_value('city'),$js );	

	}
	function city()
	{
		$city=$this->db->query("select * from cities where id > 39666")->result();
		//39666
		foreach($city as $value):
			echo $state_id=$value->state_id;
			$country=$this->db->query("select country_id from states where states.id = ".$state_id)->row();
			if($country){
			print_r($country);
			$this->db->query("update cities  set country_id=".$country->country_id." where cities.id=".$value->id);
			echo $this->db->last_query();

			echo "|";
		}
		endforeach;


	}

	
}


?>