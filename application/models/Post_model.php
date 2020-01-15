<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Post_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getRows($status='',$params = array(),$count='')
    {
        $this->db->select("po.*,camp.campaign_name,( SELECT COUNT(".WALL_LIKES.".id) AS counts  FROM  ".WALL_LIKES." WHERE  ".WALL_LIKES.".post_id=po.id ) AS total_likes,( SELECT COUNT(".WALL_COMMENTS.".id) AS counts  FROM  ".WALL_COMMENTS." WHERE  ".WALL_COMMENTS.".post_id=po.id ) AS total_comments,( SELECT COUNT(".USER_PROMOCODES.".id) AS counts  FROM  ".USER_PROMOCODES." WHERE  ".USER_PROMOCODES.".post_id=po.id ) AS total_coupons,( SELECT COUNT(".USER_PROMOCODES.".id) AS counts  FROM  ".USER_PROMOCODES." WHERE  ".USER_PROMOCODES.".post_id=po.id AND  ( ".USER_PROMOCODES.".status= '3' OR  ".USER_PROMOCODES.".status= '4') ) AS total_coupon_used");
        $this->db->from(WALL_POSTS. ' as po');
        $this->db->join(CAMPAIGNS. ' as camp','camp.id = po.campaign_id','left');
        if($status)
        $this->db->where($status);
        //$this->db->group_by('b.id');
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('po.id','desc');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }

    public function getlist($status='',$params = array(),$count='')
    {
        $this->db->select('*');
        $this->db->from(WALL_POSTS);
        if($status)
            $this->db->where($status);
        if($count) {
            return $this->db->count_all_results();
        }
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);        
        $this->db->order_by("created_dttm","DESC");
        $query = $this->db->get();        
        return ($query->num_rows() > 0)?$query->result_array():NULL;
    }
   
 

}