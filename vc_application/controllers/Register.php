<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

 public function __construct()
 {
  parent::__construct();
  /*if($this->session->userdata('id'))
  {
   redirect('private_area');
  }*/
  $this->load->library('form_validation');
  $this->load->library('encrypt');
  
  $this->load->model('Register_model');
  $this->load->library('cart');

		$this->load->model('Login_model');
  $this->load->helper('url');
  $this->load->database();
   $this->load->library('session');
 }


  	 public function index(){															//insert 


	$this->load->helper('form'); 
			
			if($this->input->post('save'))
		{
				$this->form_validation->set_rules('name', 'Name','required');
				$this->form_validation->set_rules('email', 'email','required|is_unique[user_register.email]');
				$this->form_validation->set_rules('phone', 'Phone','required');
				$this->form_validation->set_rules('password', 'Password','required');
				$this->form_validation->set_rules('c_password', 'Confirm Password', 'required|matches[password]');
				$this->form_validation->set_rules('lastname', 'Last Name','required');
				$this->form_validation->set_rules('address', 'Address','required');
			//	$this->form_validation->set_rules('address2', 'Home Address','required');
				$this->form_validation->set_rules('city', 'City','required');
				$this->form_validation->set_rules('state', 'State','required');
				$this->form_validation->set_rules('zip', 'Zip Code','required');
					$this->form_validation->set_rules('birth', 'Date of Birth','required');
				
				
				
			
				if ($this->form_validation->run() == FALSE)
                {
                    
                }
                else
                {
                    
					$name = $this->input->post('name');
					
					$email = $this->input->post('email');
					$phone = $this->input->post('phone');
					$password = $this->input->post('password');
					$role = $this->input->post('role');
					$address = $this->input->post('address');
					$lastname = $this->input->post('lastname');
					$address1 = $this->input->post('address2');
					$city = $this->input->post('city');
					$state = $this->input->post('state');
				    $zip = $this->input->post('zip');
					 $birth = $this->input->post('birth');
					
	          
					$data=array(
					 'name'=>$name,
					 
					'email'=>	$email,
				
					'phone'=>$phone,
					'password'=> md5($this->input->post('password')),
					'role'=>$role,
					'address'  => $address,
                   'lastname'  => $lastname,
                  'address1'=>	$address1,
                  'city'=>$city,
                  'state'  => $state,
                   'zip'  => $zip,
                   'birth'=>$birth,
                   
                  	
					
					);
					
					$res=$this->Register_model->add_data_in_table($data,'user_register'); 
				
						if($res){
							$this->session->set_flashdata('message','Register successfully');
                              redirect(base_url().'register');
                              /*$pass = md5($password);
                              
                              $result = $this->Login_model->get_login($email,$pass);
						
						    if($result){
							 $user_basics = array( 
								'is_loggedin' => 'true', 
								'name' =>$result[0]->name,
								'id' => $result[0]->id,
								'email'=>$email,
								'adminid' => $result[0]->id
								
							  ); 
							 
							  $this->session->set_userdata('role',$result[0]->role);
							$this->session->set_userdata('is_logged',$user_basics);
							//redirect('profile/');
							redirect('allappointment');
						    }*/
					}
					else
					{
					$error = array('error' =>$this->upload->display_errors());
				
 				    }
 				   
				}
	}
				$data['main_content'] = 'register'; 
  $this->load->view('includes/front/template', $data);
				
		}
		
		
		
		 function profile()
 {
     $seid=$this->session->userdata('is_logged');
		$id=$seid['id'];
	$data['sorganization'] = $this->Register_model->get_record_user($id);


  	$data['main_content'] = 'profile'; 
  $this->load->view('includes/front/template', $data);
 }

 
   function useredit(){
        $seid=$this->session->userdata('is_logged');
		$id=$seid['id'];
	$data['sorganization'] = $this->Register_model->get_record_user($id);

 	if($this->input->post('update'))
					{
  	 	  	 	   
					$name = $this->input->post('name');
					
					$email = $this->input->post('email');
					$phone = $this->input->post('phone');
				
					$role = $this->input->post('role');
					$address = $this->input->post('address');
					$lastname = $this->input->post('lastname');
					$address1 = $this->input->post('address2');
					$city = $this->input->post('city');
					$state = $this->input->post('state');
				    $zip = $this->input->post('zip');
					$birth = $this->input->post('birth');
					
					
						$up = array(
						
					 'name'=>$name,
					 
					'email'=>	$email,
				
					'phone'=>$phone,
				
					'role'=>$role,
					'address'  => $address,
                   'lastname'  => $lastname,
                  'address1'=>	$address1,
                  'city'=>$city,
                  'state'  => $state,
                   'zip'  => $zip,
				   'birth'  => $birth,
                   
                  	

						
						);
						 
					   if(($this->input->post('password')) != ''){
                       $up['password'] = $this->encrypt->encode($this->input->post('password'));
                          
					       
					   }
    
						
						
						$result = $this->Register_model->edit($id,$up,'user_register','id');
 				    
 				    
					
					if($result){
					       $this->session->set_flashdata('message', 'The data updated successfully.');
					    $in=$this->session->userdata('id');
                        redirect(base_url().'update'); 
                 
					}else{
					       $this->session->set_flashdata('message', 'The data not updated successfully.');
					}
 				   
				
			}
			          

			  	$data['main_content'] = 'user_update'; 
  $this->load->view('includes/front/template', $data);
  }
 

function userdelete(){


		$id = $this->uri->segment(3);
 		$result = $this->Register_model->delete($id,'user_appointment','id');
 		if($result){
 
 			redirect('allappointment');
 		}

}
}
?>
