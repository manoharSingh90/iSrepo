<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        
       
    }

    public function getProfileAge($cond=array()) {
        
        //print_r($cond);die;

        /*
        SELECT agebt.`age_bracket_desc` AS `bracket`, 
        SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, 
        SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, 
        SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
        100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
        100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent
        FROM `users` AS `u` 
        LEFT JOIN `age_brackets` AS `agebt` ON `u`.`age_bracket_id` = `agebt`.`id` WHERE `u`.`is_active` = 1 GROUP BY `age_bracket_id`
        */

            $this->db->select("age_bracket_desc AS bracket,SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, SUM(CASE WHEN gender IS NOT NULL THEN 1 ELSE 0 END) AS Total, 100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Male_percent, 100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Female_percent");
            $this->db->from(AGEBRACKET. ' as agebt' ,'left');
            $this->db->join(USERS. ' as u','agebt.id=u.age_bracket_id','left');
            //$this->db->where('u.is_active',1,);
            $this->db->where($cond);
            $this->db->group_by('age_bracket_id');

        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0)?$query->result():NULL;
    }

    public function getProfileInterest($cond=array()) {
            $this->db->select("inst.`interest_title` AS `intrest`,inst.id AS intrestId,SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female,SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
            100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Male_percent,
            100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Female_percent");
            $this->db->from(INTEREST_MASTER. ' as inst' ,'left');
            $this->db->join(USER_INTERESTS. ' as uinst','uinst.interest_id = inst.id','left');
            $this->db->join(USERS. ' as u','u.id = uinst.user_id','left');
            $this->db->where($cond);
            $this->db->group_by('inst.`interest_title`');
            $this->db->order_by('inst.`interest_title`');
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            return ($query->num_rows() > 0)?$query->result():NULL;

            /*===
                SELECT inst.interest_title AS intrest,inst.id AS intrestId, SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,
SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
 100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Male_percent,
 100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Female_percent
 FROM  interest_masters AS inst  LEFT JOIN user_interests AS uinst ON uinst.interest_id = inst.id LEFT JOIN users as u ON  u.id = uinst.user_id
WHERE u.is_active = 1 GROUP BY inst.interest_title order by intrest
            */
    
    
    }

    public function getProfileCompletion($cond=array())
    {
        /*$this->db->select("inst.`interest_title` AS `intrest`,inst.id AS intrestId,SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female,SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
            100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Male_percent,
            100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Female_percent");
            $this->db->from(INTEREST_MASTER. ' as inst' ,'left');
            $this->db->join(USER_INTERESTS. ' as uinst','uinst.interest_id = inst.id','left');
            $this->db->join(USERS. ' as u','u.id = uinst.user_id','left');
            $this->db->where($cond);
            $this->db->group_by('inst.`interest_title`');
            $this->db->order_by('inst.`interest_title`');
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            return ($query->num_rows() > 0)?$query->result():NULL;*/

            //ROUND(`u`.`profile_completion`,-1) AS Perc,

            $this->db->select("ROUND(u.profile_completion,-1)as ProfileCompletion, count(CASE WHEN u.gender='1' THEN 1 END) as Male,COUNT(CASE WHEN u.gender='2' THEN 1 END) as Female");
            $this->db->from(USERS.' as u');
            $this->db->where($cond);
            $this->db->group_by('u.profile_completion');
            $this->db->order_by('u.profile_completion');
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            return ($query->num_rows() > 0)?$query->result_array():NULL;


            /*SELECT profile_completion,
            COUNT(CASE WHEN Gender='1' THEN 1  END) AS Male,
            COUNT(CASE WHEN Gender='2' THEN 1  END) AS FeMale,
            COUNT(*) AS Total
            FROM users GROUP BY profile_completion ORDER BY profile_completion*/
    }

    public function getProfileCountry($cond=array())
    {
        $this->db->select("cnty.`country_name` AS `country`,cnty.id AS countryId,SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female,SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
            100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Male_percent,
            100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Female_percent");
            $this->db->from(USERS. ' as u' ,'left');
            //$this->db->join(USERS. ' as u','u.country_id = cnty.id','left');
            $this->db->join(COUNTRY. ' as cnty','u.country_id = cnty.id','left');
            $this->db->where($cond);
            $this->db->group_by('cnty.`country_name`');
            $this->db->order_by('cnty.`country_name`');
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            return ($query->num_rows() > 0)?$query->result():NULL;
    }

    public function getProfileCityData($cond=array())
    {
        $this->db->select("cty.`city_name` AS `city`,cty.id AS cityId,SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female,SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
            100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Male_percent,
            100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Female_percent");
        $this->db->from(CITY. ' as cty' ,'left');
        $this->db->join(USERS. ' as u','u.country_id = cty.country_id','left');
        $this->db->where($cond);
        $this->db->group_by('cty.`city_name`');
        $this->db->order_by('cty.`city_name`');
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0)?$query->result_array():NULL;
    }

    public function getAgeBracket() {
        $this->db->select('id,age_bracket_desc');
        $this->db->from(AGEBRACKET);
        $this->db->where('is_active',1);
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0)?$query->result():NULL;
    }

    public function userCount($field,$cond,$group_by='')
        {
            //print_r($cond);die;
            $this->db->select($field);
            $this->db->where($cond);            
            if($group_by)
            $this->db->group_by($group_by);
            $data = $this->db->get(USERS. ' as u');
            //echo $this->db->last_query();die;
            return ($data->num_rows() >0)?$data->num_rows():0;
        }

    public function getProfileQues($field,$cond,$group_by='')
        {
            //print_r( $cond );die;
            $this->db->select($field);
            $this->db->where($cond);
            /*if($group_by)*/
            $this->db->group_by(array('uintr.user_id','u.gender'));
            $this->db->join(USERS.' as u','u.id=uintr.user_id','left');
            $data = $this->db->get(USER_INTERESTS. ' as uintr');
            //echo $this->db->last_query();die;
            return ($data->num_rows() >0)?$data->num_rows():0;
        }
    public function getSampleRedeemed($table,$cond=array())
    {
        /*SELECT DATE(created_dttm) AS DATE, DAYNAME(created_dttm) AS 'Days', COUNT(id) AS COUNT 
        FROM reviews
        WHERE DATE(created_dttm) > DATE_SUB(NOW(), INTERVAL 1 WEEK) AND MONTH(created_dttm) = MONTH(CURDATE()) AND YEAR(created_dttm) = YEAR(CURDATE())
        GROUP BY DAYNAME(created_dttm) ORDER BY (created_dttm)*/

        $this->db->select("DATE(created_dttm) AS DATE, DAYNAME(created_dttm) AS 'Days', COUNT(id) AS COUNT ");
        $this->db->from($table);
        $this->db->where('DATE(created_dttm) > (DATE_SUB(NOW(), INTERVAL 5 WEEK)) AND MONTH(created_dttm) = MONTH(CURDATE()) AND YEAR(created_dttm) = YEAR(CURDATE())');
        $this->db->where($cond);
        $this->db->group_by(array('DAYNAME(created_dttm)'));
        $this->db->order_by('created_dttm');
        
        //echo $this->db->last_query();die;
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getTotalViewed($table,$field,$cond=array())
    {
        //echo $field;die;
        $this->db->select("DATE(created_dttm) AS DATE, DAYNAME(created_dttm) AS 'Days', SUM($field) AS cviews ");
        $this->db->from($table);
        $this->db->where('DATE(created_dttm) > (DATE_SUB(NOW(), INTERVAL 1 WEEK)) AND MONTH(created_dttm) = MONTH(CURDATE()) AND YEAR(created_dttm) = YEAR(CURDATE())');
        $this->db->where($cond);
        $this->db->group_by(array('DAYNAME(created_dttm)'));
        $this->db->order_by('created_dttm');
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->result_array():0;
    }
    public function getcampaignSummery($cond=array())
    {
        /*SELECT cmp.campaign_name, cmp.id,cmp.created_dttm,cmp.total_campaign_samples,cmp.total_campaign_samples_used,cb.`banner_url`,
            (SELECT COUNT( wp.id) FROM wall_posts AS wp 
                    JOIN campaigns AS cp ON cp.id=wp.campaign_id 
                    WHERE wp.campaign_id=cmp.id) 
                    AS wallpost
            FROM campaigns AS cmp
            LEFT JOIN campaign_banners AS cb ON cmp.id=cb.campaign_id
            #left join wall_posts as wp on wp.`campaign_id`=cmp.id
            WHERE cb.cover_image=1 AND cmp.id=1
            GROUP BY cmp .campaign_name*/

        $this->db->select("cmp.campaign_name, cmp.id,cmp.created_dttm,cmp.total_campaign_samples,cmp.total_campaign_samples_used,cb.`banner_url`,(SELECT COUNT( wp.id) FROM wall_posts AS wp JOIN campaigns AS cp ON cp.id=wp.campaign_id WHERE wp.campaign_id=cmp.id) AS wallpost,(SELECT COUNT(up.id) FROM user_promocodes AS up WHERE up.campaign_id = cmp.id AND (up.status=3 OR up.status=4))AS promo");
        $this->db->from(CAMPAIGNS.' as cmp','cmp.id=cb.campaign_id','left');
        $this->db->join(CAMPAIGNBANNERS.' as cb','cmp.id=cb.campaign_id','left');
        $this->db->where($cond);
        $this->db->group_by(array('cmp.campaign_name'));
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->row_array():0;
    }

    public function getPromoActivity($cond=array())
    {
        /*
        SELECT wp.id,wp.post_title,wp.`publish_date`,
        (SELECT COUNT( up.id) FROM user_promocodes AS up         
                WHERE up.post_id=wp.id AND (up.`status`=1 OR up.`status`=2)) AS Saved,
        (SELECT COUNT( up.id) FROM user_promocodes AS up         
                WHERE up.post_id=wp.id AND (up.`status`=3 OR up.`status`=4)) AS Redeemed
        FROM  wall_posts AS wp 
        WHERE wp.`campaign_id`=9
        */

        $this->db->select("wp.id,wp.post_title,wp.post_desc,wp.`publish_date`,wp.banner_type,
        (SELECT COUNT( up.id) FROM user_promocodes AS up         
                WHERE up.post_id=wp.id AND (up.`status`=1 OR up.`status`=2)) AS Saved,
        (SELECT COUNT( up.id) FROM user_promocodes AS up         
                WHERE up.post_id=wp.id AND (up.`status`=3 OR up.`status`=4)) AS Redeemed");
        $this->db->from(WALL_POSTS.' as wp');
        $this->db->where($cond);
        $data = $this->db->get();
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getPromoLike($cond=array())
    {
        /*
        SELECT wp.id,wp.post_title,wp.`publish_date`,
        SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,
        SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female,
        SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
        100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
        100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent
        #FROM users AS u LEFT JOIN user_promocodes AS up ON up.`user_id`=u.id
        #(SELECT COUNT( up.id) FROM user_promocodes AS up  WHERE up.post_id=wp.id AND (up.`status`=1 OR up.`status`=2)) AS Saved,
        #(SELECT COUNT( up.id) FROM user_promocodes AS up  WHERE up.post_id=wp.id AND (up.`status`=3 OR up.`status`=4)) AS Redeemed
        FROM  wall_posts AS wp 
        #LEFT JOIN user_promocodes AS up ON up.post_id = wp.id
        LEFT JOIN wall_likes AS wl ON wl.post_id = wp.id
        LEFT JOIN users AS u ON up.user_id = u.id
        WHERE wp.`campaign_id`=9

        GROUP BY wp.post_title
        */
        $this->db->select("wp.id,wp.no_of_likes,wp.post_title,wp.post_desc,wp.`publish_date`,wp.banner_type,
        SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,
        SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female,
        SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
        100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
        100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
        $this->db->from(WALL_POSTS.' as wp');
        $this->db->join(WALL_LIKES.' as wl','wl.post_id = wp.id','left');
        $this->db->join(USERS.' as u','wl.user_id = u.id','left');        
        $this->db->where($cond);
        $this->db->group_by('wp.post_title');
        $data = $this->db->get();
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getPromoComment($cond=array())
    {
        $this->db->select("wp.id,wp.no_of_comments,wp.post_title,wp.post_desc,wp.`publish_date`,wp.banner_type,
        SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,
        SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female,
        SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
        100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
        100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
        $this->db->from(WALL_POSTS.' as wp');
        $this->db->join(WALL_COMMENTS.' as wc','wc.post_id = wp.id','left');
        $this->db->join(USERS.' as u','wc.user_id = u.id','left');        
        $this->db->where($cond);
        $this->db->group_by('wp.post_title');
        $data = $this->db->get();
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getRegularPost($cond=array())
    {
        $this->db->select("wp.id,wp.no_of_likes,wp.no_of_comments,wp.post_title,wp.post_desc,wp.`publish_date`,wp.banner_type,
        ");
        $this->db->from(WALL_POSTS.' as wp');        
        $this->db->where($cond);
        $this->db->group_by('wp.post_title');
        //$data = $this->db->get();
        $data = $this->db->get()->result_array();
        foreach($data as $i=>$likes) {           
           $this->db->select("SUM(CASE WHEN U.gender= '1' THEN 1 ELSE 0 END) AS MALE,
        SUM(CASE WHEN U.gender = '2' THEN 1 ELSE 0 END) AS Female,
        SUM(CASE WHEN U.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
        100*SUM(CASE WHEN U.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN U.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
        100*SUM(CASE WHEN U.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN U.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
           $this->db->from(WALL_LIKES.' as WL');
           $this->db->join(USERS. ' as U','WL.user_id=U.id');
           $this->db->where('post_id', $likes['id']);
           //$likes_query = $this->db->get(REVIEW_QUESTIONS)->result_array();
           $likes_query = $this->db->get()->result_array();
           $data[$i]['Likes'] = $likes_query;
        }
        //$data = $this->db->get()->result_array();
        foreach($data as $i=>$comment) {
            $this->db->select("SUM(CASE WHEN U.gender= '1' THEN 1 ELSE 0 END) AS MALE,
        SUM(CASE WHEN U.gender = '2' THEN 1 ELSE 0 END) AS Female,
        SUM(CASE WHEN U.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
        100*SUM(CASE WHEN U.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN U.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
        100*SUM(CASE WHEN U.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN U.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
           $this->db->from(WALL_COMMENTS.' as WC');
           $this->db->join(USERS. ' as U','WC.user_id=U.id');
           $this->db->where('post_id', $comment['id']);
           //$comment_query = $this->db->get(REVIEW_QUESTIONS)->result_array();           
           $comment_query = $this->db->get()->result_array();
           $data[$i]['Comments'] = $comment_query;
        }
        //echo $this->db->last_query();die;
        return $data;
        //return ($data->num_rows() >0)?$data->result_array():0;
    }
    public function getCampaignCode($cond=array())
    {
        /*SELECT camp.campaign_name AS compaign_name,camp.id AS campaignId,
        SUM(CASE WHEN us.`status`=1 OR us.`status`=2 THEN 1 ELSE 0 END)AS saved,
        SUM(CASE WHEN us.`status`=3 OR us.`status`=4 THEN 1 ELSE 0 END)AS redeems FROM campaigns AS camp
        LEFT JOIN user_samples AS us ON camp.id=us.`campaign_sample_id`  GROUP BY camp.`campaign_name` ORDER BY camp.`campaign_name`*/

        $this->db->select("camp.campaign_name AS compaign_name,camp.id AS campaignId,
        SUM(CASE WHEN us.`status`=1 OR us.`status`=2 THEN 1 ELSE 0 END)AS saved,
        SUM(CASE WHEN us.`status`=3 OR us.`status`=4 THEN 1 ELSE 0 END)AS redeems");
        $this->db->from(CAMPAIGNS.' as camp');
        $this->db->join(USER_SAMPLES.' as us','camp.id=us.campaign_id','left');
        $this->db->group_by(array('camp.campaign_name'));
        $this->db->order_by('camp.campaign_name');
        $this->db->where($cond);
        //echo $this->db->last_query();die;
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getCampaignReview($params = array())
    {
        /*SELECT ureview.*,u.name,camp.* FROM campaigns AS camp
            LEFT JOIN user_reviews AS ureview ON `camp`.`review_id` = `ureview`.`review_id`
            LEFT JOIN users AS u ON `u`.`id` = `ureview`.`user_id` GROUP BY camp.`campaign_name` ORDER BY `ureview`.`id` DESC */

        /*SELECT cmp.campaign_name AS campaign,cmp.avg_rating AS avg_rating,(SELECT COUNT(id) FROM user_reviews) AS total_review, 
            COUNT(ur.id) AS avg_review
            FROM campaigns AS cmp
            LEFT JOIN user_reviews AS ur ON cmp.`review_id`=ur.`review_id`
            GROUP BY cmp.campaign_name*/

        $this->db->select("cmp.id AS campaign_id,cmp.campaign_name AS campaign_name,cmp.avg_rating AS avg_rating,            
            (SELECT COUNT( id) FROM user_reviews) AS total_review,
            COUNT(ur.id) AS avg_review");
        $this->db->from(CAMPAIGNS.' as cmp');
        $this->db->join(USER_REVIEW.' as ur','cmp.review_id=ur.review_id','left');
        $this->db->where($params);
        $this->db->group_by(array('cmp.campaign_name'));
        $this->db->order_by('cmp.campaign_name');
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->result_array():0;

        
    /*  $this->db->select("camp.*,ureview.*,u.name");
        $this->db->from(CAMPAIGNS. ' as camp'); 
        $this->db->join(USER_REVIEW. ' as ureview','camp.review_id = ureview.review_id','left');
        $this->db->join(USERS. ' as u','u.id = ureview.user_id','left');
        if($status)
        $this->db->where($status);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('ureview.id','desc');        
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0)?$query->result_array():NULL;*/
    }

    public function getCampaignReviewData($cond=array())
    {
        $this->db->select("cmp.id AS campaign_id,cmp.campaign_name AS campaign_name,ur.review_text,ur.rating,ur.user_id,u.name,u.image,");
        $this->db->from(CAMPAIGNS.' as cmp');
        $this->db->join(USER_REVIEW.' as ur','cmp.review_id=ur.review_id','left');
        $this->db->join(USERS.' as u','u.id=ur.user_id','left');
        $this->db->where($cond);
        //$this->db->group_by(array('cmp.campaign_name'));
        $this->db->order_by('cmp.campaign_name');
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getUserReviewData($cond=array())
    {
        $this->db->select("u.id,u.name,u.image,u.created_dttm,agebt.age_bracket_desc");
        $this->db->from(USER_REVIEW_ANS.' as ura');
        //$this->db->join(USER_REVIEW.' as ur','cmp.review_id=ur.review_id','left');
        $this->db->join(USERS.' as u','u.id=ura.user_id','left');
        $this->db->join(AGEBRACKET. ' as agebt','u.age_bracket_id = agebt.id','left');
        $this->db->where($cond);
        $this->db->group_by(array('ura.answer_id','u.id'));
        //$this->db->order_by('cmp.campaign_name');
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getCampaignReviewQus($cond=array(),$st,$ed)
    {
        $this->db->select("cmp.campaign_name AS campaign_name,cmp.id AS campaignId, cmp.review_id AS reviewId,
         SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, 
         SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, 
         SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
         100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
         100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
        $this->db->where('cmp.review_id IS NOT NULL AND cmp.is_publish="1"');
        $this->db->where($cond);
        $this->db->from(CAMPAIGNS.' as cmp');
        $this->db->join(USER_REVIEW.' as us','cmp.review_id=us.review_id','left');
        $this->db->join(USERS.' as u','u.id=us.user_id','left');
        $this->db->group_by('cmp.campaign_name');
        $this->db->order_by('cmp.campaign_name');
        $data = $this->db->get()->result_array();
        foreach($data as $i=>$campaigns) {           
           $this->db->where('review_id', $campaigns['reviewId']);
           $this->db->order_by('id','DESC');
           $reviewQus_query = $this->db->get(REVIEW_QUESTIONS)->result_array();           
           $data[$i]['reviewQus'] = $reviewQus_query;
        }
        for ($q=0; $q < count($data); $q++) { 
            foreach($data[$q]['reviewQus'] as $s=>$ans) {
                $this->db->select("rao.question_id,rao.id,rao.answer_text,
                    SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,
                    SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, 
                    SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
                    100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
                    100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
               $this->db->where('rao.question_id', $ans['id']);
               $this->db->where('rao.is_active','1');
               $this->db->join(USER_REVIEW.' as us','rao.review_id=us.review_id','left');
               //$this->db->join(USER_REVIEW_ANS.' as ua','rao.review_id=ua.review_id');
               $this->db->join(USERS.' as u','u.id=us.user_id','left');
               $this->db->group_by('rao.answer_text');               
               $ans_query = $this->db->get(REVIEW_ANSWER_OPTIONS .' as rao')->result_array();               
               $data[$q]['reviewQus'][$s]['ansOpt'] = $ans_query;
            }
        } 


        /*============old Code ===============*/
        return $data; // ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getCampaignReviewQusTest($cond=array(),$st,$ed)
    {
        /*SELECT cmp.campaign_name AS campaign_name,cmp.id AS campaignId, cmp.review_id AS reviewId,
		SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,
		SUM(CASE WHEN u.gender = '0' THEN 1 ELSE 0 END) AS Female,
		SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
		100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
		100*SUM(CASE WHEN u.gender= '0' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent
		FROM campaigns AS cmp
		LEFT JOIN user_reviews AS us ON us.review_id = cmp.review_id
		LEFT JOIN users AS u ON us.user_id=u.id WHERE cmp.is_publish="1"
		GROUP BY cmp.campaign_name*/
        $revCond = array("us.created_dttm >=" =>$st,"us.created_dttm <=" =>$ed);

		$this->db->select("cmp.campaign_name AS campaign_name,cmp.id AS campaignId, cmp.review_id AS reviewId,
		 SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, 
		 SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, 
		 SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
		 100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
		 100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
		$this->db->where('cmp.review_id IS NOT NULL AND cmp.is_publish="1"');
        $this->db->where($cond);
		$this->db->from(CAMPAIGNS.' as cmp');
		$this->db->join(USER_REVIEW.' as us','cmp.review_id=us.review_id','left');
		$this->db->join(USERS.' as u','u.id=us.user_id','left');
		$this->db->group_by('cmp.campaign_name');
		$this->db->order_by('cmp.campaign_name');
        $data = $this->db->get()->result_array();        
        //echo"<pre>";print_r($data);die;
        foreach($data as $i=>$campaigns) {
           $this->db->where('review_id', $campaigns['reviewId']);
           $reviewQus_query = $this->db->get(REVIEW_QUESTIONS)->result_array();           
           $data[$i]['reviewQus'] = $reviewQus_query;
        }
        //echo"<pre>";print_r($data);die;
        for ($q=0; $q < count($data); $q++) { 
            foreach($data[$q]['reviewQus'] as $s=>$ans) {
                $this->db->select("rao.question_id,rao.id,rao.answer_text,
                    SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,
                    SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, 
                    SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
                    100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
                    100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
               $this->db->from(REVIEW_ANSWER_OPTIONS.' as rao');
               $this->db->where('rao.question_id', $ans['id']);
               $this->db->where('rao.is_active','1');
               //$this->db->where($revCond);
               $this->db->join(USER_REVIEW.' as us','rao.review_id=us.review_id');
               //$this->db->join(USER_REVIEW_ANS.' as ua','rao.review_id=ua.review_id');
               $this->db->join(USERS.' as u','u.id=us.user_id','left');
               $this->db->group_by('rao.answer_text');
               $ans_query = $this->db->get()->result_array();  
               //echo $this->db->last_query();die;             
               $data[$q]['reviewQus'][$s]['ansOpt'] = $ans_query;
            }
        } 
        //echo"<pre>";print_r($data);die;
        //echo $this->db->last_query();die;



        /*=========== Old CODE =============*/

        /*for ($q=0; $q < count($data); $q++) { 
            foreach($data[$q]['reviewQus'] as $s=>$ans) {
                $this->db->select("rao.question_id,rao.id,rao.answer_text,
                    SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,
                    SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, 
                    SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
                    100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
                    100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
               $this->db->from(REVIEW_ANSWER_OPTIONS.' as rao');
               $this->db->where('rao.question_id', $ans['id']);
               $this->db->where('rao.is_active','1');
               $this->db->where($revCond);
               $this->db->join(USER_REVIEW.' as us','rao.review_id=us.review_id');
               //$this->db->join(USER_REVIEW_ANS.' as ua','rao.review_id=ua.review_id');
               $this->db->join(USERS.' as u','u.id=us.user_id','left');
               $this->db->group_by('rao.answer_text');
               $ans_query = $this->db->get()->result_array();  
               //echo $this->db->last_query();die;             
               $data[$q]['reviewQus'][$s]['ansOpt'] = $ans_query;
            }
        } */

        /*============old Code ===============*/
        return $data; // ($data->num_rows() >0)?$data->result_array():0;
    }
/*========================
for ($q=0; $q < count($data); $q++) { 
            foreach($data[$q]['reviewQus'] as $s=>$ans) {
                $this->db->select("rao.question_id,rao.id,rao.answer_text,
                    SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE,
                    SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, 
                    SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
                    100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
                    100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
               $this->db->from(REVIEW_ANSWER_OPTIONS.' as rao');
               $this->db->where('rao.question_id', $ans['id']);
               $this->db->where('rao.is_active','1');
               $this->db->where($revCond);
               $this->db->join(USER_REVIEW.' as us','rao.review_id=us.review_id');
               //$this->db->join(USER_REVIEW_ANS.' as ua','rao.review_id=ua.review_id');
               $this->db->join(USERS.' as u','u.id=us.user_id','left');
               $this->db->group_by('rao.answer_text');
               $ans_query = $this->db->get()->result_array();  
               //echo $this->db->last_query();die;             
               $data[$q]['reviewQus'][$s]['ansOpt'] = $ans_query;
            }
        } 
*/

    function userReviewAns($cond='',$st,$ed){
     $this->db->select("uans.question_id AS qus_id,uans.answer_text AS ans,SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, 
                    SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, 
                    SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total, 
                    100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent, 
                    100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
                    $this->db->from(USERS.' as u');
                    $this->db->join(USER_REVIEW_ANS.' as uans','u.id=uans.user_id');
                    $this->db->where(array("uans.created_dttm >="=>$st,"uans.created_dttm <="=>$ed));
                    $this->db->group_by(array('uans.answer_text', 'uans.answer_id'));
                    $this->db->order_by('uans.question_id','DESC');        
                    $this->db->order_by('uans.answer_text');        
            $ans_query_percent = $this->db->get()->result_array(); 
            //echo $this->db->last_query();die;
           return $ans_query_percent;
    }


    public function getReviewQestion($fields='',$cond='',$count='')
    {
        $this->db->select($fields);
        $this->db->from(REVIEW_QUESTIONS. ' as reviewQuest' ,'left');
        $this->db->join(CAMPAIGNS. ' as camp','camp.review_id=reviewQuest.review_id','left');
        if($cond)
        $this->db->where($cond);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('reviewQuest.ques_order','ASC');        
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result_array():NULL;
    }

    
    public function getMachineActivity($cond=array())
    {
        $this->db->select('camp.id, camp.campaign_name');
        $this->db->where($cond);
        $this->db->group_by(array('camp.campaign_name'));
        $this->db->order_by('camp.campaign_name');
        $query = $this->db->get(CAMPAIGNS.' as camp' )->result_array();
        //echo'<pre>';print_r($query);die;
        foreach($query as $i=>$campaigns) {           
           $this->db->where('campaign_id', $campaigns['id']);
           $vends_query = $this->db->get(CAMPAIGN_VENDS)->result_array();           
           $query[$i]['campaignVends'] = $vends_query;
        }        
        for ($t=0; $t < count($query); $t++) { 
            foreach($query[$t]['campaignVends'] as $j=>$machines) {               
               $this->db->where('id', $machines['vend_machine_id']);
               $vends_machin_query = $this->db->get(VENDING_MACHINES)->result_array();               
               $query[$t]['campaignVends'][$j]['machineVends'] = $vends_machin_query;
            }
        }
        //return ($query->num_rows() > 0)?$query->result_array():NULL;
        
        return $query;
    }

    public function getCampaignDetails($cond=array())
    {
        $this->db->select("camp.*");
        $this->db->from(CAMPAIGNS.' as camp');
        $this->db->group_by(array('camp.campaign_name'));
        $this->db->order_by('camp.campaign_name');
        //echo $this->db->last_query();die;
        $data = $this->db->get();
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getMachineStatus($cond=array())
    {
        /*  SELECT cont.country_name, vend_mach.country,
        COUNT(location_name) AS total_location,
        (SELECT COUNT( cv.id) FROM vending_machines AS vm 
        JOIN campaign_vends AS cv ON vm.id=cv.vend_machine_id 
        WHERE vm.country=vend_mach.country) 
        AS campaigns,
        COUNT(vending_machine_code) AS vending_machine,
        SUM(CASE WHEN vend_mach.`is_active`=1 THEN 1 ELSE 0 END)AS Active, 
        SUM(CASE WHEN vend_mach.`is_active`=0 THEN 1 ELSE 0 END)AS Inactive
        FROM vending_machines AS vend_mach
        LEFT JOIN countries AS cont ON cont.id= vend_mach.`country`
        GROUP BY cont.country_name ORDER BY cont.country_name   */

        //$this->db->select("vend_mach.location_name,vend_mach.country");

        $this->db->select("cont.country_name,vend_mach.country,
            COUNT(vend_mach.location_name) AS total_location,
            (SELECT COUNT( cv.id) FROM vending_machines AS vm 
            JOIN campaign_vends AS cv ON vm.id=cv.vend_machine_id 
            WHERE vm.country=vend_mach.country) 
            AS campaigns,
            COUNT(vend_mach.vending_machine_code) AS vending_machine,
        SUM(CASE WHEN vend_mach.`is_active`=1 THEN 1 ELSE 0 END)AS Active, 
        SUM(CASE WHEN vend_mach.`is_active`=0 THEN 1 ELSE 0 END)AS Inactive");
        $this->db->from(VENDING_MACHINES.' as vend_mach');
        $this->db->join(COUNTRY.' as cont','vend_mach.country=cont.id','left');
        //$this->db->where($cond);
        $this->db->group_by(array('cont.country_name'));
        $this->db->order_by('cont.country_name');
        $data = $this->db->get();
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getMachineCityStatus($cond=array())
    {
        $this->db->select("city.city_name,vend_mach.city,vend_mach.country,
            (SELECT COUNT( cv.id) FROM vending_machines AS vm 
            JOIN campaign_vends AS cv ON vm.id=cv.vend_machine_id 
            WHERE vm.country=vend_mach.country) 
            AS campaigns,
            COUNT(vend_mach.vending_machine_code) AS vending_machine,
        SUM(CASE WHEN vend_mach.`is_active`=1 THEN 1 ELSE 0 END)AS Active, 
        SUM(CASE WHEN vend_mach.`is_active`=0 THEN 1 ELSE 0 END)AS Inactive");
        $this->db->from(VENDING_MACHINES.' as vend_mach');
        $this->db->join(CITY.' as city','vend_mach.city=city.id','left');
        $this->db->where($cond);
        $this->db->group_by(array('city.city_name'));
        $this->db->order_by('city.city_name');
        //echo $this->db->last_query();die;
        $data = $this->db->get();
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getCityVended($cond=array())
    {
        $this->db->select('id,vending_machine_code,is_active,location_address,vend_no_of_sample_used,invalid_try');
        $this->db->from(VENDING_MACHINES);
        $this->db->where($cond);
        $this->db->order_by('id');
        $data = $this->db->get();
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    public function getMachineAlerts($cond=array())
    {
        /*
        SELECT vm.vending_machine_code, SUM(vend_no_of_samples) - SUM(vend_no_of_sample_used)AS sample_left FROM campaign_vends AS cv JOIN vending_machines AS vm ON vm.id=cv.vend_machine_id GROUP BY cv.vend_machine_id

        //  SELECT vm.vending_machine_code, SUM(vend_no_of_samples) - SUM(vend_no_of_sample_used)AS sample_left FROM vending_machines AS vm JOIN campaign_vends AS cv ON vm.id=cv.vend_machine_id GROUP BY cv.vend_machine_id

        SELECT `vm`.`vending_machine_code`, 
            IF ((SUM(vend_no_of_samples) - SUM(vend_no_of_sample_used)) != NULL, "sdf",
            (SUM(vend_no_of_samples) - SUM(vend_no_of_sample_used))
            ) AS t,
            #ISNULL((SUM(vm.vend_no_of_samples) - SUM(vm.vend_no_of_sample_used)),0) as t,
            (SUM(vend_no_of_samples) - SUM(vend_no_of_sample_used)) AS sample_lef FROM `vending_machines` AS `vm` 
            LEFT JOIN `campaign_vends` AS `cv` ON `cv`.`vend_machine_id`=`vm`.`id` GROUP BY `cv`.`vend_machine_id` ORDER BY `cv`.`vend_machine_id`

        */

        $this->db->select("vm.vending_machine_code,
            SUM(cv.vend_no_of_samples) - SUM(cv.vend_no_of_sample_used) AS sample_left");
        $this->db->from(VENDING_MACHINES.' as vm');
        $this->db->where($cond);
        $this->db->join(CAMPAIGN_VENDS.' as cv','cv.vend_machine_id=vm.id');
        $this->db->group_by(array('cv.vend_machine_id'));
        $this->db->order_by('cv.vend_machine_id');
        $data = $this->db->get();
        return ($data->num_rows() >0)?$data->result_array():0;

    }
    public function checkUserCount($table,$cond=array())
    {
        $this->db->select('id');
        $this->db->where($cond);
        $data = $this->db->get($table. ' as u');
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->num_rows():0;
    }
    public function getRows($status='',$params = array(),$count='')
    {
        //$this->db->cache_on();
        //age_bracket_id
        $this->db->select("u.*,CASE WHEN u.gender= '1' THEN 'Male'  WHEN u.gender = '2' THEN 'Female' ELSE '' END AS gender,agebt.age_bracket_desc,count(usample.id) as total_sample_obtained, ( SELECT COUNT(`id`) AS counts  FROM  `user_samples` WHERE `status`='3' AND user_samples.user_id=usample.user_id  ) AS total_sample_reviwed");
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

    public function getCampaignDtl($cond='')
    {
        /*$this->db->select('*');
        $this->db->from(CAMPAIGNS);
        if($cond)
        $this->db->where($cond);
        $data =$this->db->get();
        return ($data->num_rows() > 0)?$data->row_array():NULL;*/

        $this->db->select("cmp.id,cmp.campaign_name,cmp.review_id,cmp.start_date,cmp.end_date,cmp.avg_rating,cmp.buy_now_click_total,cmp_ven.vend_machine_id,cmp_ven.vend_no_of_sample_used,cmp_ven.vend_no_of_samples");
        $this->db->from(CAMPAIGNS.' as cmp');
        $this->db->join(CAMPAIGN_VENDS.' as cmp_ven','cmp.id=cmp_ven.campaign_id','left');
        $this->db->where($cond);
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->row_array():0;

    }

    public function getOpenEndedReview($cond='')
    {
        //
        /*$this->db->select("cmp.id AS campaign_id, cmp.campaign_name AS campaign_name, cmp.avg_rating AS avg_rating, ur.review_text AS 
            openText,(SELECT COUNT( id) FROM user_reviews) AS total_review, COUNT(ur.id) AS avg_review");
        $this->db->from(CAMPAIGNS.' as cmp');
        $this->db->join(USER_REVIEW.' as ur','cmp.review_id=ur.review_id','left');
        $this->db->where($cond);
        $this->db->where("ur.review_text != ''");
        //$this->db->where('cmp.review_id IS NOT NULL AND cmp.is_publish="1"');
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->row_array():0;*/

        $this->db->select("COUNT(ur.id) AS review_count");
        $this->db->from(USER_REVIEW.' as ur');
        $this->db->where($cond);
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->row_array():0;
        
    }

    function getAgeGroupRating($cond='')
    {
        /*SELECT `agebt`.`age_bracket_desc` AS `bracket`, SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, 
        SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
        AVG(ur.`rating`) AS avg_rat,
        FROM `users` AS `u`
        LEFT JOIN `age_brackets` AS `agebt` ON `agebt`.`id`=`u`.`age_bracket_id`
        LEFT JOIN `user_reviews` AS `ur` ON `ur`.`user_id`=`u`.`id`
        LEFT JOIN `campaigns` AS `cmp` ON `cmp`.`review_id`=`ur`.`review_id`
        WHERE `cmp`.`id` = '30'
        GROUP BY `age_bracket_id`*/

        $this->db->select("agebt.age_bracket_desc AS bracket, SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total, AVG(ur.`rating`) AS avg_rat");
        $this->db->from(USERS.' as u');
        $this->db->join(AGEBRACKET. ' as agebt','agebt.id=u.age_bracket_id','left');
        $this->db->join(USER_REVIEW. ' as ur','ur.user_id=u.id','left');
        $this->db->join(CAMPAIGNS. ' as cmp','cmp.review_id=ur.review_id','left');
        $this->db->where($cond);
        $this->db->group_by('age_bracket_id');
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->result_array():0;


    }

    function getUserReviewCount($cond='')
    {
        $this->db->select("COUNT(ura.id) AS usercount");
        $this->db->from(USER_REVIEW_ANS.' as ura');
        $this->db->where($cond);
        $this->db->group_by('ura.user_id');
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->row_array():0;
        
    }

    function getUserGender($cond='')
    {
        $this->db->select("SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female,SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
        $this->db->from(USERS.' as u');
        $this->db->join(USER_REVIEW. ' as ur','ur.user_id=u.id','left');
        $this->db->join(CAMPAIGNS. ' as cmp','cmp.review_id=ur.review_id','left');
        $this->db->where($cond);        
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->row_array():0;
    }
    function getUserCampaignProfile($cond='')
    {
        /*SELECT agebt.`age_bracket_desc` AS `bracket`, 
        SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, 
        SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female, 
        SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,
        100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,
        100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent
        FROM `users` AS `u` 
        LEFT JOIN `age_brackets` AS `agebt` ON `u`.`age_bracket_id` = `agebt`.`id` 
    LEFT JOIN user_reviews AS ur ON ur.user_id = u.id
    LEFT JOIN campaigns AS cmp ON cmp.review_id = ur.review_id
        WHERE `u`.`is_active` = 1 AND cmp.id=30 GROUP BY `age_bracket_id`*/


        $this->db->select("agebt.age_bracket_desc AS bracket,SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END) AS MALE, SUM(CASE WHEN u.gender = '2' THEN 1 ELSE 0 END) AS Female,SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS Total,100*SUM(CASE WHEN u.gender= '1' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS M_percent,100*SUM(CASE WHEN u.gender= '2' THEN 1 ELSE 0 END)/ SUM(CASE WHEN u.gender IS NOT NULL THEN 1 ELSE 0 END) AS F_percent");
        $this->db->from(USERS.' as u');
        //$this->db->from(AGEBRACKET. ' as agebt' ,'left');
        $this->db->join(AGEBRACKET. ' as agebt','agebt.id=u.age_bracket_id','left');
        $this->db->join(USER_REVIEW. ' as ur','ur.user_id=u.id','left');
        $this->db->join(CAMPAIGNS. ' as cmp','cmp.review_id=ur.review_id','left');
        $this->db->where($cond);
        $this->db->group_by('age_bracket_id');
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->result_array():0;
    }

    function getQuesDistribution($cond='')
    {
        $this->db->select("COUNT(u.id) AS tCount");
        $this->db->from(USER_REVIEW_ANS.' as ura');
        $this->db->join(USERS. ' as u','ura.user_id=u.id','left');
        $this->db->where($cond);
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->row_array():0;

    }

    function getQuesDistributionAge($cond="")
    {
        $this->db->select("agebt.age_bracket_desc AS ageGroup");
        $this->db->from(USER_REVIEW_ANS.' as ura');
        $this->db->join(USERS. ' as u','ura.user_id=u.id','left');
        $this->db->join(AGEBRACKET. ' as agebt','agebt.id=u.age_bracket_id','left');
        $this->db->where($cond);
        $this->db->group_by('agebt.age_bracket_desc');
        $data = $this->db->get();
        
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->row_array():0;
    }

    function numUserCount($cond='')
    {
        $this->db->select("count(u.id) AS num_user");
        $this->db->from(USERS.' as u');
        $this->db->join(USER_REVIEW. ' as ur','ur.user_id=u.id','left');
        $this->db->join(CAMPAIGNS. ' as cmp','cmp.review_id=ur.review_id','left');
        $this->db->where($cond);        
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        return ($data->num_rows() >0)?$data->row_array():0;
    }
    function getFeedbackQus($cond="")
    {
        $this->db->select("rq.id,rq.ques_text");
        $this->db->from(REVIEW_QUESTIONS.' as rq');
        $this->db->where($cond);        
        $data = $this->db->get()->result_array();
        //echo $this->db->last_query();die;
        //return ($data->num_rows() >0)?$data->result_array():0;
        foreach ($data as $q => $qus) {
            $this->db->where('question_id', $qus['id']);
            $ra_query = $this->db->get(REVIEW_ANSWER_OPTIONS)->result_array();
            $data[$q]['review_ans'] = $ra_query;
        }
        return $data;
    }


    /*============   ========================*/

    /*$this->db->select('camp.id, camp.campaign_name');
        $this->db->where($cond);
        $this->db->group_by(array('camp.campaign_name'));
        $this->db->order_by('camp.campaign_name');
        $query = $this->db->get(CAMPAIGNS.' as camp' )->result_array();
        //echo'<pre>';print_r($query);die;
        foreach($query as $i=>$campaigns) {           
           $this->db->where('campaign_id', $campaigns['id']);
           $vends_query = $this->db->get(CAMPAIGN_VENDS)->result_array();           
           $query[$i]['campaignVends'] = $vends_query;
        }        
        for ($t=0; $t < count($query); $t++) { 
            foreach($query[$t]['campaignVends'] as $j=>$machines) {               
               $this->db->where('id', $machines['vend_machine_id']);
               $vends_machin_query = $this->db->get(VENDING_MACHINES)->result_array();               
               $query[$t]['campaignVends'][$j]['machineVends'] = $vends_machin_query;
            }
        }
        //return ($query->num_rows() > 0)?$query->result_array():NULL;
        
        return $query;*/

}