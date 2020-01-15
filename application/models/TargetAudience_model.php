<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class TargetAudience_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    public function campData($status='',$params = array(),$count='')
    {
        $this->db->select("c.*,camp.campaign_name");
        $this->db->from(CAMP_BEHAVIOUR. ' as c' ,'left');
        $this->db->join(CAMPAIGNS. ' as camp','c.campaign_id = camp.id','left');
        if($status)
        $this->db->where($status);
        if($count) {
            return $this->db->count_all_results();
        }
        //$this->db->order_by('p.id','desc');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function postData($status='',$params = array(),$count='')
    {
        $this->db->select("p.*,w.post_desc,w.post_banner_url,w.banner_type");
        $this->db->from(POST_BEHAVIOUR. ' as p' ,'left');
        $this->db->join(WALL_POSTS. ' as w','p.post_id = w.id','left');
        if($status)
        $this->db->where($status);
        if($count) {
            return $this->db->count_all_results();
        }
        //$this->db->order_by('p.id','desc');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
        public function getTargetAudience($status='')
    {
        $this->db->select('t.*,GROUP_CONCAT(DISTINCT age.age_bracket_desc SEPARATOR ", ") as age_bracket ,GROUP_CONCAT(DISTINCT intOpt.option_text SEPARATOR ", ") as option_text,GROUP_CONCAT(DISTINCT intOpt.interest_id SEPARATOR ", ") as interest_id,GROUP_CONCAT(DISTINCT intOpt.interest_id SEPARATOR ", ") as interest_id,GROUP_CONCAT(DISTINCT intMstr.interest_title SEPARATOR ", ") as interest_title');
        $this->db->from(TARGET_AUDIENCE. ' as t' ,'left');
        $this->db->join(AGEBRACKET. ' as age' , ' FIND_IN_SET(age.id, t.age) <> 0','left');
        $this->db->join(INTEREST_OPTIONS. ' as intOpt' , ' FIND_IN_SET(intOpt.id, t.interests) <> 0','left');
        $this->db->join(INTEREST_MASTER. ' as intMstr' , ' FIND_IN_SET(intMstr.id, intOpt.interest_id) <> 0','left');
        if($status)
        $this->db->where($status);
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->row():NULL;
    }
   
  
 

}