<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Brands_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getRows($status='',$params = array(),$count='')
    {
        //$this->db->cache_on();
        //age_bracket_id
        //( SELECT COUNT(".USER_SAMPLES.".id) AS counts  FROM  ".USER_SAMPLES." JOIN ".CAMPAIGNS." where   ".USER_SAMPLES.".campaign_id=".CAMPAIGNS.".id  ) AS total_samples,
        $this->db->select("b.*,( SELECT COUNT(".CAMPAIGNS.".id) AS counts  FROM  ".CAMPAIGNS." WHERE  ".CAMPAIGNS." .brand_id=b.id  ) AS total_campaign,( SELECT COUNT(".CAMPAIGNS.".id) AS counts  FROM  ".CAMPAIGNS." WHERE  ".CAMPAIGNS." .brand_id=b.id and  STR_TO_DATE( ".CAMPAIGNS.".start_date, '%Y-%m-%d') <='".date('Y-m-d',strtotime(date('Y-m-d')))."' && STR_TO_DATE( ".CAMPAIGNS.".end_date, '%Y-%m-%d') >='".date('Y-m-d',strtotime(date('Y-m-d')))."') AS total_live_campaign,( SELECT COUNT(".WALL_POSTS.".id) AS counts  FROM  ".WALL_POSTS." WHERE  ".WALL_POSTS.".brand_id=b.id ) AS total_post,( SELECT COUNT(".WALL_POSTS.".id) AS counts  FROM  ".WALL_POSTS." WHERE  ".WALL_POSTS.".brand_id=b.id AND ".WALL_POSTS.".is_publish='1') AS total_live_post,( SELECT COUNT(".USER_PROMOCODES.".id) AS counts  FROM  ".USER_PROMOCODES."  JOIN ".WALL_POSTS." WHERE  ".USER_PROMOCODES.".post_id=".WALL_POSTS.".id AND ".WALL_POSTS.".brand_id=b.id ) AS total_promocodes,( SELECT COUNT(".USER_SAMPLES.".id) AS counts  FROM  ".USER_SAMPLES."  JOIN ".CAMPAIGNS." WHERE  ".USER_SAMPLES.".campaign_id=".CAMPAIGNS.".id AND ".CAMPAIGNS.".brand_id=b.id ) AS total_samples");
        $this->db->from(BRANDS. ' as b' ,'left');
      /*  $this->db->join(CAMPAIGNS. ' as camp','camp.brand_id = b.id','left');
        $this->db->join(USER_SAMPLES. ' as usample', 'camp.id = usample.campaign_id','left');
        $this->db->join(WALL_POSTS. ' as wallpost', 'wallpost.brand_id = b.id','left');*/
        if($status)
        $this->db->where($status);
        //$this->db->group_by('b.id');
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('b.id','desc');
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
 

}