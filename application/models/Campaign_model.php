<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getRows($status='',$params = array(),$count='')
    {
        $this->db->select("camp.*,( SELECT COUNT(".WALL_POSTS.".id) AS counts  FROM  ".WALL_POSTS." WHERE  ".WALL_POSTS.".campaign_id=camp.id ) AS total_post,( SELECT COUNT(".USER_PROMOCODES.".id) AS counts  FROM  ".USER_PROMOCODES." WHERE  ".USER_PROMOCODES.".campaign_id=camp.id AND  ( ".USER_PROMOCODES.".status= '3' OR  ".USER_PROMOCODES.".status= '4')) AS total_promo_redeemed,( SELECT COUNT(".USER_SAMPLES.".id) AS counts  FROM  ".USER_SAMPLES." WHERE  ".USER_SAMPLES.".campaign_id=camp.id AND  ( ".USER_SAMPLES.".status= '3' OR  ".USER_SAMPLES.".status= '4')) AS total_samples_redeemed");
        $this->db->from(CAMPAIGNS. ' as camp' ,'left');
        if($status)
        $this->db->where($status);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('camp.id','desc');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        //echo $this->db->last_query();die;
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    
    public function NPS($con='',$params = array(),$count='')
    {
      $query= $this->db->query("SELECT
                          CASE 
                            WHEN user_reviews.rating >= 7 THEN 'Promoter'
                            WHEN user_reviews.rating <= 4 THEN 'Passive'
                            WHEN user_reviews.rating IN (5,6) THEN 'Detractor'
                            ELSE 'Error'
                          END AS np_segment
                          , COUNT(*),user_reviews.rating 
                        FROM
                          user_reviews JOIN campaigns ON campaigns.review_id=user_reviews.review_id WHERE ".$con."
                        GROUP BY 1");

        return ($query->num_rows() > 0)?$query->result():NULL;
  }
 

}