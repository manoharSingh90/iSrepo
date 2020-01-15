<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        
       
    }
public function getRowsOLd($status='',$params = array(),$count='')
    {
        //$this->db->cache_on();
        $this->db->select('*');
        $this->db->from(USERS);
        if($status)
        $this->db->where($status);
        if($count) {
            return $this->db->count_all_results();
        }
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function getRows($status='',$params = array(),$count='')
    {
        //$this->db->cache_on();
        //age_bracket_id
        $this->db->select("u.*,CASE WHEN u.gender= '1' THEN 'Male'  WHEN u.gender = '2' THEN 'Female' ELSE '' END AS gender,agebt.age_bracket_desc,count(usample.id) as total_sample_obtainedd, ( SELECT COUNT(`id`) AS counts  FROM  `user_reviews` WHERE  user_reviews.is_campaign_review='1' AND user_reviews.user_id=u.id  ) AS total_sample_reviwed, ( SELECT COUNT(`id`) AS counts  FROM  `user_samples` WHERE (`status`='3' OR `status`='4') AND user_samples.user_id=usample.user_id  ) AS total_sample_obtained");
        $this->db->from(USERS. ' as u' ,'left');
        $this->db->join(AGEBRACKET. ' as agebt','u.age_bracket_id = agebt.id','left');
        $this->db->join(USER_SAMPLES. ' as usample', 'u.id = usample.user_id','left');
        if($status)
        $this->db->where($status);
        $this->db->group_by('u.id');
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('u.id','desc');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function getInterestDtl($status='',$params = array(),$count='')
    {
        $this->db->select("uinterest.id,uinterest.interest_id,interestmst.interest_title,interestmst.interest_type");
        $this->db->from(USER_INTERESTS. ' as uinterest' ,'left');
        $this->db->join(INTEREST_MASTER. ' as interestmst','uinterest.interest_id = interestmst.id','left');
        if($status)
        $this->db->where($status);
       // $this->db->group_by('u.id');
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('uinterest.id','ASC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
     public function getInteresOptiontDtl($status='',$params = array(),$count='')
    {
        $this->db->select("uinterestOpt.id,interestOpt.option_text");
        $this->db->from(USER_INTEREST_OPTIONS. ' as uinterestOpt' ,'left');
        $this->db->join(INTEREST_OPTIONS. ' as interestOpt','uinterestOpt.option_id = interestOpt.id','left');
        if($status)
        $this->db->where($status);
       // $this->db->group_by('u.id');
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('uinterestOpt.id','ASC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    
    public function profile($field,$con='')
    {
        $this->db->select($field);
        $this->db->from(USERS. ' as u' ,'left');
        $this->db->join(AGEBRACKET. ' as AgeBracket','u.age_bracket_id = AgeBracket.id','left');
        if($con)
        $this->db->where($con);
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->row():NULL;
    }

    /*public function getActiveUser() {
        $this->db->select('*')
        $this->db->from(USERS);
        $this->db->where('is_active',1);
        $query = $this->db->get();
        return $query->result()
        
    }*/

}