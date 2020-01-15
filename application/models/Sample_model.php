<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sample_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getRows($status='',$params = array(),$count='')
    {
        $this->db->select("campVend.*,vendM.location_name,vendM.location_address,vendM.location_address2,vendM.postal_code,vendM.landmark,vendM.vend_lat,vendM.vend_long,c.city_name,ct.country_name,st.name as state_name");
        $this->db->from(CAMPAIGN_VENDS. ' as campVend' ,'left'); 
        $this->db->join(CAMPAIGNS. ' as camp','camp.id = campVend.campaign_id','left');
        $this->db->join(VENDING_MACHINES. ' as vendM','vendM.id = campVend.vend_machine_id','left');
        $this->db->join(CITY. ' as c','c.id = vendM.city','left');
        $this->db->join(STATE. ' as st','st.id = vendM.state','left');
        $this->db->join(COUNTRY. ' as ct','ct.id = vendM.country','left');
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
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
      public function getVendingList($status='',$params = array(),$count='')
    {
        $this->db->select("vendM.*,c.city_name,ct.country_name,st.name as state_name");
        $this->db->from(VENDING_MACHINES. ' as vendM' ,'left'); 
        $this->db->join(CITY. ' as c','c.id = vendM.city','left');
        $this->db->join(STATE. ' as st','st.id = vendM.state','left');
        $this->db->join(COUNTRY. ' as ct','ct.id = vendM.country','left');
        if($status)
        $this->db->where($status);
        if($count) {
            return $this->db->count_all_results();
        }
        $this->db->order_by('vendM.id','ASC');
        if(array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit'],$params['start']);
        elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params))
            $this->db->limit($params['limit']);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0)?$query->result():NULL;
    }
 
   
  
 

}