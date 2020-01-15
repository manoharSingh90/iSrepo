<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

	    $this->load->model(array('common_model','users_model'));
	    $this->load->library('Ajax_pagination');
        $this->load->helper('mail_helper');
	    $this->perPage = 10;
	    ini_set('memory_limit', '1024M');
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

        $data['usersList'] = $this->users_model->getRows($status,$conditions); 
        //echo $this->db->last_query();die;
       // print_r($data['usersList']);die;
        $this->session->set_userdata(array('menu'=>'Users'));
		$data['title'] = 'Users';
		$this->load->view('common/header',$data);
		$this->load->view('users',true);
		$this->load->view('common/footer');
			 
	}
	public function getUsers($page){ 
		ini_set('memory_limit', '512M');
		$data = array();
		$conditions = array();
		$status='';
		$offset = 0;
        $data ='';$created_dttm='';
        $data=array('users'=>'','totalRecords'=>0);
		if($page)
			$offset = 10*$page;
        $conditions['start'] = $offset;
        $conditions['limit'] = $this->perPage;
        $search_text = $this->input->post('search_text');
		if($this->input->post('search_text'))
         	$status = "  (u.name LIKE '%".$search_text."%')";
        $usersList = $this->users_model->getRows($status,$conditions);
        //echo $this->db->last_query();die;
        $data['totalRecords']=$totalRecords = count($this->users_model->getRows($status));
       // $data['totalRecords']=$totalRecords = $this->users_model->getRows($status,$conditions,'count');
        if($usersList){
        foreach ($usersList  as  $value) {
         if($value->created_dttm!='0000-00-00 00:00:00' && $value->created_dttm!='')
         	$created_dttm=date('d M, Y',strtotime($value->created_dttm));
         $data['users'] .= '<tr><td><a href="'.base_url('user-details/'.$value->id).'" class="user-link"><span>';
         if($value->image!='') 
         	$data['users'] .= '<img src="'.$value->image.' " alt="'.ucwords($value->name).'"/>';
          	$data['users'] .= '</span>'.ucwords($value->name).'</a></td>
            <td>Joined on '.$created_dttm.'</td>
            <td class="text-center text-primary font-weight-bold">'.$value->total_sample_obtained.'</td>
            <td class="text-center text-primary font-weight-bold">'.$value->total_sample_reviwed.'</td>
            <td class="text-center"><a href="'.base_url('user-details/'.$value->id).'" class="text-link">View</a></td>
            </tr>';
      
        } 
    }
        echo json_encode($data);
       // echo $data['users'];
			 
	}
	public function userDetails($user_id=''){ 
		$data = array();
		$conditions = array();
		$offset = 0;
        $status = "u.id ='".$user_id."'";
        $data['usersDtl'] = $this->users_model->getRows($status,$conditions);
        //print_r($data['usersDtl']) ;die;
        $con = "uinterest.user_id ='".$user_id."'";
        $data['usersInterest'] = $this->users_model->getInterestDtl($con);
       // echo $this->db->last_query();die;
        $usersInterestOptionsList=array() ;
        if($data['usersInterest']){
	        foreach ($data['usersInterest'] as $value){
	        	$statuss = "uinterestOpt.user_id ='".$user_id."' AND uinterestOpt.interest_id='".$value->interest_id."'";
	        	$usersInterestOptions = $this->users_model->getInteresOptiontDtl($statuss); 
	        	$newArray=array('interest_title'=>$value->interest_title,'option'=>$usersInterestOptions);
	        	array_push($usersInterestOptionsList, $newArray);
	        }
	    }
        $data['usersInterestOptionsList']=$usersInterestOptionsList;
      //  echo "<pre>";
     // print_r($usersInterestOptionsList);die;
        $this->session->set_userdata(array('menu'=>'Users'));
		$data['title'] = 'User Deatils';
		$this->load->view('common/header',$data);
		$this->load->view('users_detail',true);
		$this->load->view('common/footer');
			 
	}
	
	

}


?>