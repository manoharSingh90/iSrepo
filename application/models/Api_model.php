<?php 
class Api_model extends CI_Model {

        public function insert($table,$data)
        {
            $this->db->insert($table, $data);
            $insert_id = $this->db->insert_id();
   			return  $insert_id;
        }

        public function update($table,$data,$con='')
        {
                $query=$this->db->update($table, $data, $con);
                if($query){
                    return true;
                }
                else
                    return false;
                // 
        }

        public function getRowData($table,$filed,$con='')
        {
            $this->db->select($filed);
            $this->db->from($table);
            if($con)
            $this->db->where($con);
            $data =$this->db->get();
            return ($data->num_rows() > 0)?$data->row():NULL;
        }
        function getData($table,$field,$con='')
        {
            $this->db->select($filed);
            $this->db->from($table);
            if($con)
            $this->db->where($con);
            $this->db->order_by('id','DESC');
            $data =$this->db->get();
            return ($data->num_rows() > 0)?$data->row():NULL;
        }
        public function getResultData($table,$filed,$con='',$order_by='',$order='')
        {
            $this->db->select($filed);
            $this->db->from($table);
            if($con)
                $this->db->where($con);
            if(!empty($order_by) && !empty($order))
                $this->db->order_by($order_by,$order);

            $data =$this->db->get();
            return ($data->num_rows() > 0)?$data->result():NULL;
        }
        public function getField($table,$field,$con)
        {
           $data = $this->db
                        ->select($field)
                        ->from($table)
                        ->where($con)
                        ->get();
            $result = $data->row();
            //echo $this->db->last_query();die;
            return ($data->num_rows() >0)?$result->$field:FALSE;
        }
		
		//ADDED BY SAHIL
		public function getSamplesCampaignData($sample_id='', $campaignID='')
        {
			$this->db->select('campaign_samples.buy_now_link, campaign_samples_location.location_name, campaign_samples_location.location_image');
			$this->db->from('campaign_samples');
			$this->db->join('campaign_samples_location', 'campaign_samples.id = campaign_samples_location.campaign_samples_id', 'INNER');
			$this->db->where('campaign_samples_location.campaign_samples_id',$sample_id);
			$this->db->where('campaign_samples_location.campaign_id',$campaignID);
			$query = $this->db->get();
			return $query->result();
        }
		
		public function getCampaignData($campaignID='')
        {
			$this->db->select('campaigns.campaign_name, campaigns.campaign_desc, campaigns.review_id, campaigns.avg_rating, campaign_banners.banner_url');
			$this->db->from('campaigns');
			$this->db->join('campaign_banners', 'campaigns.id = campaign_banners.campaign_id', 'INNER');
			$this->db->where('campaigns.id',$campaignID);
			$query = $this->db->get();
			return $query->result();
        }
		
		public function getUserReviewData($reviewID='')
        {
			$this->db->select('users.name, users.image, user_reviews.review_text, user_reviews.rating, user_reviews.created_dttm');
			$this->db->from('users');
			$this->db->limit(50);
             $this->db->order_by('user_reviews.id DESC','user_reviews.review_text DESC');
			$this->db->join('user_reviews', 'users.id = user_reviews.user_id', 'INNER');
			$this->db->where('user_reviews.review_id',$reviewID);
			$query = $this->db->get();
			return $query->result();
        }
		//ADDED BY SAHIL
		
        public function mysqlNumRows($table,$field,$cond,$group_by='')
        {
            $this->db->select($field);
            $this->db->where($cond);
            if($group_by)
            $this->db->group_by($group_by);
            $data = $this->db->get($table);
            return ($data->num_rows() >0)?$data->num_rows():0;
        }
        public function checkUserEmail($table,$con)
        {
            $this->db->select("id");
            $this->db->where($con);
            $client = $this->db->get($table);

            return ($client->num_rows() >0)?TRUE:FALSE;
        }
        //in use
           public function checkIfExist($table,$con)
        {
            $this->db->select("id");
            $this->db->where($con);
            $client = $this->db->get($table);

            return ($client->num_rows() >0)?TRUE:NULL;
        }
           
      public function delete($table,$con){
            //update user from users table
            $delete = $this->db->delete($table,$con);
            //return the status
            return $delete?true:false;
    }

    public function campaignList($cond='',$params = array(),$count='')
    {
        $this->db->select('camp.id,camp.campaign_name,camp.campaign_desc,camp.start_date,camp.end_date,camp.avg_rating,camp.brand_id,camp.total_campaign_samples_used,camp.total_campaign_samples,camp.buy_now_link,brand.brand_name,brand.brand_desc,brand.brand_logo_url,campsample.id as campaign_sample_id,campsample.start_date as camp_sample_start_date,campsample.end_date as camp_sample_end_date,campsample.is_active');
        $this->db->from(CAMPAIGNS. ' as camp' ,'left');
        $this->db->join(BRANDS. ' as brand','brand.id = camp.brand_id','left');
        $this->db->join(REVIEW. ' as review','review.id = camp.review_id','left');
        $this->db->join(CAMPAIGN_SAMPLES. ' as campsample','campsample.campaign_id = camp.id','left');

        if($cond)
        $this->db->where($cond);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('camp.id','DESC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function campaignReview($cond='',$params = array(),$count='')
    {
        $this->db->select('ureview.*,u.name,u.image,u.social_login,camp.id as camapign_id');
        $this->db->from(USER_REVIEW. ' as ureview' ,'left');
        $this->db->join(USERS. ' as u','u.id = ureview.user_id','left');
        $this->db->join(CAMPAIGNS. ' as camp','ureview.review_id = camp.review_id','left');
        if($cond)
        $this->db->where($cond);
		
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('ureview.id','DESC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }

    public function vendMachineList($cond='',$params = array(),$count='',$latitude,$longitude)
    {
        $this->db->select("campVend.*,vm.id,vm.location_name,vm.postal_code,vm.location_address,vm.vend_lat,vm.vend_long,( 6371 * acos ( cos ( radians($latitude) ) * cos( radians( vm.vend_lat ) ) * cos( radians( vm.vend_long ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( vm.vend_lat ) ) ) ) AS distance,vm.is_active");
        $this->db->from(CAMPAIGN_VENDS. ' as campVend' ,'left');
        $this->db->join(VENDING_MACHINES. ' as vm','vm.id = campVend.vend_machine_id','left');
        $this->db->join(CAMPAIGNS. ' as camp','camp.id = campVend.campaign_id','left');
        if($cond)
        $this->db->where($cond);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('campVend.id','ASC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function postList($cond='',$params = array(),$count='')
    {
        $this->db->select("post.*,b.brand_name,b.brand_desc,b.brand_logo_url,camp.start_date,camp.campaign_name,camp.end_date");
        $this->db->from(WALL_POSTS. ' as post' ,'left');
        $this->db->join(BRANDS. ' as b','b.id = post.brand_id','left');
        $this->db->join(CAMPAIGNS. ' as camp','camp.id = post.campaign_id','left');
        $this->db->join(CAMPAIGN_SAMPLES. ' as campSample','campSample.campaign_id = camp.id','left');
        if($cond)
        $this->db->where($cond);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('post.modified_dttm','DESC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function postCommentList($cond='',$params = array(),$count='')
    {
        $this->db->select("c.*,u.name,u.image,u.social_login");
        $this->db->from(WALL_COMMENTS. ' as c' ,'left');
        $this->db->join(USERS. ' as u','u.id = c.user_id','left');
        if($cond)
        $this->db->where($cond);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('c.id','DESC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function userSamplesList($fields='',$cond='',$params = array(),$count='')
    {
        $this->db->select($fields);
        $this->db->from(USER_SAMPLES. ' as uSample' ,'left');
        $this->db->join(CAMPAIGNS. ' as camp','uSample.campaign_id = camp.id','left');
        $this->db->join(CAMPAIGN_SAMPLES. ' as campSample','uSample.campaign_sample_id = campSample.id','left');
        $this->db->join(BRANDS. ' as b','b.id = camp.brand_id','left');
        if($cond)
        $this->db->where($cond);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('uSample.id','DESC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function userPromoCodeList($cond='',$params = array(),$count='')
    {
        $this->db->select("uPromo.*,post.promo_desc,camp.campaign_name,camp.total_campaign_samples_used,camp.total_campaign_samples,b.brand_logo_url,b.brand_name");
        $this->db->from(USER_PROMOCODES. ' as uPromo' ,'left');
        $this->db->join(CAMPAIGNS. ' as camp','uPromo.campaign_id = camp.id','left');
        $this->db->join(WALL_POSTS. ' as post','post.id=uPromo.post_id','left');
        $this->db->join(BRANDS. ' as b','post.brand_id=b.id','left');
        if($cond)
        $this->db->where($cond);
        $this->db->group_by('uPromo.id');
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('uPromo.id','ASC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function reviewQuestions($fields='',$cond='',$params = array(),$count='')
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
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
    public function notificationList($fields,$cond='',$params = array(),$count='')
    {
        $this->db->select($fields);
        $this->db->from(NOTIFICATIONS. ' as noti' ,'left');
        $this->db->join(CAMPAIGNS. ' as camp','camp.id = noti.campaign_id','left');
        $this->db->join(BRANDS. ' as b','b.id = camp.brand_id','left');
        if($cond)
        $this->db->where($cond);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('noti.id','DESC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }

}