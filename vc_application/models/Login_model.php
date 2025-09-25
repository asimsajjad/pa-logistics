<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_model {
	
	 public function validate($user,$pass){
		
		$this->db->select('*');
		$this->db->from('admin_login');
		$this->db->where('track',$user);
		$this->db->where('hascode',$pass);
		$query = $this->db->get();
		return $query->result();
		//->num_rows();

	}
	 public function get_login($user,$pass){
		
		$this->db->select('*');
		$this->db->from('user_register');
		$this->db->where('email',$user);
		$this->db->where('password',$pass);
		$query = $this->db->get();
		return $query->result();
		//->num_rows();

	}
public function getall($seid)
{
    $this->db->select('id');
    $this->db->where("track",$seid);
    $q=$this->db->get('admin_login');
    return $q->result_array();
}


	public function check_email($email){
		$this->db->select('*');   
		$this->db->from('admin_login');
		$this->db->where('email',$email);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	
	public function check_key($key){
		$this->db->select('*');   
		$this->db->from('admin_login');
		$this->db->where('key',$key);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	
	public function temp_reset_password($temp_pass){
		$data =array(
					'email' =>$this->input->post('email'),
					'reset_pass'=>$temp_pass);
					$email = $data['email'];

		if($data){
			$this->db->where('email', $email);
			$this->db->update('admin_login', $data);  
			return TRUE;
		}
		else{
			return FALSE;
		}

	}
	public function is_temp_pass_valid($temp_pass){
		$this->db->where('reset_pass', $temp_pass);
		$query = $this->db->get('admin_login');
		if($query->num_rows() == 1){
			return TRUE;
		}
		else return FALSE;
	}
			
	
	
function fetch()
	{
		$this->db->select('*');
		$this->db->from('contact');
		$query=$this->db->get('');
		return $query->result();
		
	}
 function insert($data)
        {
                $this->db->insert('contact',$data);    
                return $this->db->insert_id();
        }
 function insertadd($data)
        {
                $this->db->insert('pages',$data);   
				return $this->db->insert_id();				
                
        }
        function insertblog($data)
        {
                $this->db->insert('blog',$data);   
				return $this->db->insert_id();				
                
        }
          function inserttags($data)
        {
                $this->db->insert('tags',$data);   
				return $this->db->insert_id();				
                
        }
  function insertmenu($data)
        {
                $this->db->insert('menu',$data);    
				return $this->db->insert_id();
             

        }
         function insertcategory($data)
        {
                $this->db->insert('category',$data);   
				return $this->db->insert_id();				
                
        }

  function allpages()
	{
		$this->db->select('*');
		$this->db->from('pages');
		$query=$this->db->get('');
		return $query->result();
		
	}
 function blog()
	{
		$this->db->select('*');
		$this->db->from('blog');
		$query=$this->db->get('');
		return $query->result();
		
	}
function tags()
	{
		$this->db->select('*');
		$this->db->from('tags');
		$query=$this->db->get('');
		return $query->result();
		
	}
	function allslides($id)
	{
		$this->db->select('*');
		$this->db->from('slider_images');
		$this->db->where('img_cat',$id);
		$query=$this->db->get('');
		return $query->result();
		
	}
function allcategory()
	{
		$this->db->select('*');
		$this->db->from('category');
		$this->db->where('parent',0);
		$query=$this->db->get();
	    $categories= $query->result_array();
	
	    	$arraychid=array();
			foreach($categories as $key=>$row){
    		   $ids= $row['id'];
        $this->db->select('*, c.name as childcat');
        $this->db->from('category as c');
        $this->db->where('c.parent',$ids);
        	$query=$this->db->get();
        	$child_categories= $query->result_array();
        	$arraychid[$key]=$child_categories;
        
        
        
        
         
        
		    }
		    //print_r($arraychid);
		   
		    
	return	$data=['categories' =>  $categories,
		            'childcat' =>$arraychid  ];
		
		
	}
function allcategoriesbyparent()
	{
			$this->db->select('*');
		$this->db->from('category');
		$this->db->where('parent',0);
		$this->db->order_by("id", "asc");
		$query=$this->db->get();
	    return $query->result_array();
		
		
	}
	function allcategoriesbyparentid()
	{
			$this->db->select('*');
		$this->db->from('category');
		$this->db->order_by("parent", "asc");
		$this->db->where('parent!=',0);
		$query=$this->db->get();
	    return $query->result_array();
		
		
	}
	
	  function alltaged()
	{
		$this->db->select('*');
		$this->db->from('tags');
		$query=$this->db->get('');
		return $query->result_array();
		
	}
function allcategories()
	{
			$this->db->select('*');
		$this->db->from('category');
		$this->db->where('parent',0);
		$query=$this->db->get();
	    return $query->result_array();
		
	}
	function allcategoriesbyid($id)
	{
			$this->db->select('*');
		$this->db->from('category');
		$this->db->where('id',$id);
		$query=$this->db->get();
	    return $query->result_array();
		
	}
	function allcategories_sub()
	{
			$this->db->select('*');
		$this->db->from('category');
		$this->db->where('parent !=',0);
		$query=$this->db->get();
	    return $query->result_array();
		
	}
	function categories($id)
	{
			$this->db->select('*');
		$this->db->from('category');
		$this->db->where('parent',$id);
		$query=$this->db->get();
	    return $query->result_array();
		
	}
	function subcategories($id)
	{
			$this->db->select('*');
		$this->db->from('category');
		$this->db->where('parent',$id);
		$query=$this->db->get();
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
	
		function commonIns($tbl,$data)
        {
                $this->db->insert($tbl,$data);   
				return $this->db->insert_id();				
                
        }
		
		function commonFetch($tbl){
			$this->db->select('*');
			$this->db->from($tbl);
			$query = $this->db->get();
			return $query->result();
			}

		 
	  	
	function getAllCategories(){
 $this->db->order_by("name", "ASC");
  $query = $this->db->get("category");
  return $query->result_array();
       

}
        
        function can_login($email, $password)
 {
  $this->db->where('email', $email);
  $query = $this->db->get('user_register');

  if($query->num_rows() > 0 )
  {
   foreach($query->result() as $row)
   {
    if($row->is_email_verified == 'yes')
    {
     $store_password = $this->encrypt->decode($row->password);
     if($password == $store_password)
     {
      $this->session->set_userdata('id', $row->id);
      $this->session->set_userdata('name', $row->name);
      
     }
     else
     {
      return 'Wrong Password';
     }
    }
    else
    {
     return 'First verified your email address';
    }
   }
  }else {
	  
	  $this->db->where('email', $email);
  $queryorganization = $this->db->get('organization_register');
  if($queryorganization->num_rows() > 0){
	 foreach($queryorganization->result() as $row)
   {
    if($row->is_email_verified == 'yes')
    {
     $store_password = $this->encrypt->decode($row->password);
     if($password == $store_password)
     {
      $this->session->set_userdata('adminid', $row->id);
      $this->session->set_userdata('name', $row->name);
      $this->session->set_userdata('role', $row->role);
	  $user_basic = array( 
						'is_loggedin' => 'true', 
						'username' => $row->name,
						'id' => $row->id,
						'role' => $row->role,
						'adminid'=> $row->id
					 ); 
		$this->session->set_userdata('is_logged',$user_basic );
     }
     else
     {
      return 'Wrong Password';
     }
    }
    else
    {
     return 'First verified your email address';
    }
   } 
  }else{
	   return 'Wrong Email Address';
  }
 }
}
}		
?>