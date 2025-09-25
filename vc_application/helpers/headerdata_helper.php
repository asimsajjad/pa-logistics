<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_user_details')){
   function get_user_details($user_id=''){
       //get main CodeIgniter object
       $ci =& get_instance();
       
       //load databse library
       $ci->load->database();
       
       //get data from database
       //$ci->db->select('*');
       //$ci->db->from('category');
       $ci->db->where('parent',0);
       $query = $ci->db->get('category');
       
       if($query->num_rows() > 0){
           $result = $query->result_array();
           return $result;
       }else{
           return false;
       }
   }
}

function getIpAddress(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // Otherwise, get the remote IP address
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function cleanSpace($notes) {
    // Remove all types of line breaks
    $notes = str_replace(array("\r\n", "\r", "\n"), ' ', $notes);

    // Replace multiple spaces with a single space
    $notes = preg_replace('/\s+/', ' ', $notes);

    // Trim leading and trailing spaces
    $notes = trim($notes);

    return $notes;
}

function checkPermission($allPermissions,$permission){
    $return = false;
    if($allPermissions != ''){
        $permissions = explode(',',$allPermissions);
        if(in_array($permission,$permissions)) { $return = true; }
    }
    return $return;
}
