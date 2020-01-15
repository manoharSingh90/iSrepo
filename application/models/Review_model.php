<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Review_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getRows($status='',$params = array(),$count='')
    {
        $this->db->select("ureview.*,u.name");
        $this->db->from(USER_REVIEW. ' as ureview'); 
        $this->db->join(USERS. ' as u','u.id = ureview.user_id','left');
        $this->db->join(CAMPAIGNS. ' as camp','camp.review_id = ureview.review_id','left');
        if($status)
        $this->db->where($status);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('ureview.id','desc');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        //echo $this->db->last_query();die;
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
 
   
  
 

}