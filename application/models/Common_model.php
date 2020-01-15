<?php 
class Common_model extends CI_Model {

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
        public function getResultData($table,$filed,$con='')
        {
            $this->db->select($filed);
            $this->db->from($table);
            if($con)
            $this->db->where($con);
            $data =$this->db->get();
            //echo $this->db->last_query();die;
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
            return ($data->num_rows() >0)?$result->$field:FALSE;
        }

        public function getLastInsertField($table,$field,$con,$order)
        {
           $data = $this->db
                        ->select($field)
                        ->from($table)
                        ->where($con)
                        ->order_by($order)
                        ->get();
            $result = $data->row();
            return ($data->num_rows() >0)?$result->$field:FALSE;
        }
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

}