<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Questionnaire extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('common_model','campaign_model'));
		$this->load->library('Ajax_pagination');
		$this->load->helper('mail_helper');
		$this->perPage = 10;
	}
	public function create($brand_id,$campaign_id=''){ 
		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['brand_id'] = $brand_id;
		$data['campaign_id'] = $campaign_id;		
		$campCount=0;
		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$campaign_id));
		$data['questData']=$this->common_model->getResultData(CAMPAIGN_SAMPLES,'*',array('campaign_id'=>$campaign_id));
		if($review_id)
			$campCount= $this->common_model->mysqlNumRows(REVIEW_QUESTIONS,'id',array('review_id'=>$review_id)); 
		//echo $this->db->last_query();die;
		$data['qusDataCount']=$campCount;
		if($campCount>0)
			redirect('edit-questionnaire/'.$brand_id.'/'.$campaign_id);
		else{

			$data['title'] = 'Create Questionnaire';
			$this->load->view('common/header',$data);
			$this->load->view('campaign_questionnaire',true);
			$this->load->view('common/footer');
		}

	}
	public function addQuestionnaire()
	{
		$campaign_id = $this->input->post('campaign_id');
		$ques_text   = $this->input->post('ques_text');
		$question_type=$this->input->post('question_type');

		if($campaign_id){
			$insertReviewData=array(
				'review_type'   	=> 1,
				'is_active' 		=> 1,
				'created_dttm' 	   	=> date('Y-m-d H:i:s'),
				'modified_dttm' 	=> date('Y-m-d H:i:s'),
			);
			$review_id=$this->common_model->insert(REVIEW,$insertReviewData);
			$this->common_model->update(CAMPAIGNS,array('review_id'=>$review_id),array('id'=>$campaign_id));
			for ($i=1; $i <= count($ques_text); $i++) { 
				if($ques_text[$i]!=''){
					$reviewQues=array(
						'review_id'   		=> $review_id,
						'ques_order'   		=> 0,
						'ques_text'   		=> $ques_text[$i],
						'ques_type'   		=> $question_type[$i],
						'is_active' 		=> 1,
						'created_dttm' 	   	=> date('Y-m-d H:i:s'),
						'modified_dttm' 	=> date('Y-m-d H:i:s'),
					);
					$question_id=$this->common_model->insert(REVIEW_QUESTIONS,$reviewQues);
					$answer_text 	= $this->input->post('answer_text'.($i));
					if($question_id){
						for ($k=1; $k <= count($answer_text) ; $k++) { 
							if($answer_text[$k]!=''){
								$reviewAns=array(
									'question_id'   	=> $question_id,
									'ans_order'   		=> 0,
									'answer_text'   	=> $answer_text[$k],
									'is_correct' 		=> 1,
									'is_active' 		=> 1,
									'created_dttm' 	   	=> date('Y-m-d H:i:s'),
									'modified_dttm' 	=> date('Y-m-d H:i:s'),
								);
								$this->common_model->insert(REVIEW_ANSWER_OPTIONS,$reviewAns);
							}

						}
					}
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
		$campCount=0;
		$data['reviewQuest']='';
		$review_id=$this->common_model->getField(CAMPAIGNS,'review_id',array('id'=>$campaign_id));
		if($review_id)
			$data['reviewQuest']= $this->common_model->getResultData(REVIEW_QUESTIONS,'*',array('review_id'=>$review_id)); 
		$campCount= $this->common_model->mysqlNumRows(REVIEW_QUESTIONS,'id',array('review_id'=>$review_id)); 
		$data['qusDataCount']=$campCount;
		$data['reviewAns']=array();
		if($data['reviewQuest']){
			foreach ($data['reviewQuest'] as $key => $value) {
				$data['reviewAns'][]= $this->common_model->getResultData(REVIEW_ANSWER_OPTIONS,'*',array('question_id'=>$value->id));
			}
		}


		$this->session->set_userdata(array('menu'=>'Brands'));
		$data['title'] = 'Edit Questionnaire';
		$this->load->view('common/header',$data);
		$this->load->view('campaign_questionnaire_edit',true);
		$this->load->view('common/footer');

	}
	public function editQuestionnaire()
	{
		$questionnaire_id = $this->input->post('questionnaire_id');
		$campaign_id = $this->input->post('campaign_id');
		$ques_text   = $this->input->post('ques_text');
		$question_type=$this->input->post('question_type');
		$review_id=$this->input->post('review_id');
		$counter= $this->input->post('counter'); 
		//print_r($ques_text);die;
		//$this->common_model->update(CAMPAIGNS,array('review_id'=>$review_id),array('id'=>$campaign_id));
		if($campaign_id){

			$this->db->delete(REVIEW_ANSWER_OPTIONS,array('review_id'=>$review_id));
			$this->db->delete(REVIEW_ANSWER_OPTIONS,array('review_id'=>'0'));
			//echo $this->db->last_query();
			$this->db->delete(REVIEW_QUESTIONS,array('review_id'=>$review_id));
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