<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('upload_img'))
{
    function upload_img($foldername,$fieldname){
        $CI = & get_instance();
        $CI->load->helper('string');
        $config['upload_path']   =  $foldername;
        $config['allowed_types'] = 'jpeg|jpg|png|mp4'; 
        $config['file_name']     = random_string('alnum',32);
        $config['max_size']      = '0';
        

        $CI->load->library('upload',$config);
        $CI->load->library('user_agent');
          
        if (!$CI->upload->do_upload($fieldname))
        {
           $data = array('msg' => $CI->upload->display_errors());

            $CI->session->set_flashdata('msg',$data['msg']);
            if ($CI->agent->is_referral())
            {
                redirect($CI->agent->referrer());
            } 
        } 
        else
        { 
            $upload_details = $CI->upload->data(); //uploading
            $data = array('msg' =>'uploaded','upload_data' => $upload_details);
        }

        return $data;
    }
}