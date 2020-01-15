<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reviews extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('common_model','review_model'));
		$this->perPage = 10;
	} 
	public function getReviews($page){ 
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
		$status = " ureview.is_active='1' AND camp.id='".$campaign_id."' AND ureview.is_campaign_review='1'";
		$reviewList = $this->review_model->getRows($status,$conditions);
		//echo $this->db->last_query();die;
		$data['totalRecords']=$totalRecords = $this->review_model->getRows($status,array(),'count');
		if($reviewList){
			foreach ($reviewList  as  $value) {
				$readmore="readMore('".$value->id."','".$value->name."','".$value->review_text."')";
				$data['results'] .= '<tr><td href="'.base_url('user-details/'.$value->user_id).'" target="_blank" class="user-link font-weight-bold">'.$value->name.'</a></td>
				<td class="text-center">'.$value->rating.'</td>
				<td class="text-center">'.date("d M Y",strtotime($value->created_dttm)).'</td>
				<td class="limitText">'.$value->review_text.'</td>
				<td class="text-center"><a href="#" class="text-link readmore" data-toggle="modal" data-target="#moreModal" id="'.$value->id.'" onclick="'.$readmore.'">Read More</a></td>
				<td class="text-center"><a  class="text-danger delete"  id="'.$value->id.'" style="cursor:pointer">Remove</a></td>
				</tr>';
			} 
		}
		else
			$data['results']='<tr><td  colspan="5" class="text-center">No record exists.</td></tr>';
		if(floor($data['totalRecords']/$this->perPage)<$page)
			$data['results']="";
		echo json_encode($data);

	}
	public function changeStatus()
	{
		$id=$this->input->post('id');
		$is_active=$this->input->post('is_active');
		$updateChallenge=array('is_active'=>$is_active);
		$returnid=$this->common_model->update(BRANDS,$updateChallenge,array('id'=>$id));
		echo $returnid;

	}
	public function delete()
	{
		$id = $this->input->post('id');
		if($id){
			$updateData=array('is_active'=>0);
			$returnid=$this->common_model->update(USER_REVIEW,$updateData,array('id'=>$id));
			echo $returnid;
		}

	}
	
}


?>