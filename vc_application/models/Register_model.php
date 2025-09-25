<?php
class Register_model extends CI_Model
{

 function insertorganization($data,$id,$org)
 {
     $query= $this->db->get_where('my_organization', array(
           'organization_id'  =>$org,
           'user_id'  =>$id,
           
        ));
 $query->num_rows();
          if ($query->num_rows() === 0) {
	
            $this->db->insert('my_organization', $data);
          return $this->db->insert_id($data);
        }
        
 }
 
 function verify_email($key)
 {
  $this->db->where('verification_key', $key);
  $this->db->where('is_email_verified', 'no');
  $query = $this->db->get('organization_register');
  if($query->num_rows() > 0)
  {
   $data = array(
    'is_email_verified'  => 'yes'
   );
   $this->db->where('verification_key', $key);
   $this->db->update('organization_register', $data);
   return true;
  }
  else
  {
   return false;
  }
 }
 
 function get_availability($date){
    $this->db->select('rdate');
	$this->db->from('availability');
	$this->db->where('adate >=', $date);  
	$query=$this->db->get();
	return $query->result_array();
 }
 function get_data_id($id)
 { 
    $this->db->select('*');
		$this->db->from('user_register');
		 $this->db->where('id', $id);  
		 $query=$this->db->get();
		return $query->result_array();
		}

  function get_user()
 {
     
    $this->db->select('*');
		$this->db->from('user_register');
		  
		 $query=$this->db->get();
		return $query->result_array();
		}
		
		
		
		 function get_activity()
 {
     
    $this->db->select('*');
		$this->db->from('history');
		  
		 $query=$this->db->get();
		return $query->result_array();
		}
		

		 function get_product()
 {
     
    $this->db->select('*');
		$this->db->from('products');
		  
		 $query=$this->db->get();
		return $query->result_array();
		}
		

		function get_user_history($id)
	{
		$this->db->select('*');
		$this->db->from('history');
		$this->db->where('user_id',$id);
		$query=$this->db->get();
		return $query->result_array();
		
	}	
function insert_data($insert)
 {
  $this->db->insert('order_donation', $insert);
  
  return $this->db->insert_id();
 }
public function get_restaurants_by_id()
    {
		$this->db->select('*');
		$this->db->from('appointments');
	    $this->db->where('id',1);
		$query = $this->db->get();
		return $query->result_array(); 
    }
	
	public function getRecordCount() {
    	$this->db->select('count(*) as allcount');
      	$this->db->from('organization_register');
      	$query = $this->db->get();
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}
 
 
 function user_get_data($id)
 {
     if($this->session->userdata('role')==1){
    $this->db->select('*');
		$this->db->from('user_register');
		//$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->result_array();
 }
 else{
        
    $this->db->select('*');
		$this->db->from('user_register');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->result_array();
 }
 }
  
  function user_get_user($user_id)
 {
     $user_id = $this->session->set_userdata('id');
    $this->db->select('*');
		$this->db->from('user_register');
		$this->db->where('id',42);
		$query = $this->db->get();
		return $query->result_array();
 }
  
 
 	function get_record($id,$table,$field)
	{
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where($field,$id);
		$query=$this->db->get();
		return $query->result();
		
	}
	function get_record_array($id,$table,$field)
	{
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where($field,$id);
		$query=$this->db->get();
		return $query->result_array();
		
	}
	function get_record_user($id)
	{
		$this->db->select('*');
		$this->db->from('user_register');
		$this->db->where('id',$id);
		$query=$this->db->get();
		return $query->result_array();
		
	}
		function get_appointment()
	{
		$this->db->select('*');
		$this->db->from('user_appointment');
		
		$query=$this->db->get();
		return $query->result_array();
		
	}
	function get_record_appointment($id)
	{
		$this->db->select('*');
		$this->db->from('user_appointment');
		$this->db->where('user_id',$id);
		$query=$this->db->get();
		return $query->result_array();
		
	}
	
	function get_record_appointment_date($info)
	{
		$this->db->select('*');
		$this->db->from('user_register');
			
		$this->db->where('id',$info);
	
		$this->db->or_like('lastname',$info);
	
		
		$query=$this->db->get();
		return $query->result_array();
		
	}

		function get_user_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('user_register');
		$this->db->where('id',$id);
		$query=$this->db->get();
		return $query->result_array();
		
	}
	 
	
	function edit($id,$data,$table,$field){

		$this->db->where($field, $id);
       $this->db->update($table, $data);
       return true;
	}


function delete($id,$table,$field)
	{

      $this->db->where($field, $id);
      $this->db->delete($table); 
      return true;
  }
    
   public function getLiveGps(){
        $this->db->select('dname,live_gps');
        $this->db->from('drivers');
        $this->db->where('live_gps !=','');
        $query = $this->db->get();
        return $query->result_array();
    } 
    
    public function get_data_from_table($id,$table){
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('id',$id);
        $query = $this->db->get();
        return $query->result_array();
    }  
    function add_data_in_table($data,$table) {
      $this->db->insert($table, $data);
      return $this->db->insert_id();
     }
   

}

?>