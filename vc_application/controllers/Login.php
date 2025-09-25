<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

 
public	 function __construct()
		{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->load->model('Login_model');
			 $this->load->database();
		 $this->load->library('session');
	$this->load->library('cart');

		}

 function index()
 {
  
  $data['main_content'] = 'login'; 
  $this->load->view('includes/front/template', $data);
 }
 function validations()
 {
	  if($this->input->post('submit'))
			  {
				  
				  $user = $this->input->post('username');
				  $pass = md5($this->input->post('pass'));
				  
				  $this->form_validation->set_rules('username', 'Username', 'required');
				  $this->form_validation->set_rules('pass', 'Password', 'required');
				  if ($this->form_validation->run() == FALSE)
					{
				  
					}
				else
					{
							
							//echo"<script>alert('Login Unsuccessful');</script>";
						$result = $this->Login_model->get_login($user,$pass);
						
						    if($result){
							 $user_basics = array( 
								'is_loggedin' => 'true', 
								'name' =>$result[0]->name,
								'id' => $result[0]->id,
								'email'=>$user,
								'adminid' => $result[0]->id
								
							  ); 
							 
							  $this->session->set_userdata('role',$result[0]->role);
							$this->session->set_userdata('is_logged',$user_basics);
							redirect(base_url().'profile/');
							
							  
						}
						// else{
						 else{
						 $this->session->set_flashdata('message','Invalid email or password');
                           redirect('login');
						 }
						}
					
					 	
					
			  }
}
public function logout()
					{
		  
				$this->session->unset_userdata('is_logged');
				$this->session->unset_userdata('role');
			    redirect(base_url());
					}
 
}
?>
