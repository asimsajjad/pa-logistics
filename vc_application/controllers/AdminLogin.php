<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class AdminLogin extends CI_Controller 
	{
		public	 function __construct()
		{
			parent::__construct();
			//$this->load->model('Login_model');
			//$this->load->helper(array('form','url'));
			$this->load->library('form_validation');
			$this->load->model('Login_model');
			$this->load->database();
			$this->load->helper(array('form','url'));
			$this->load->library('session');
		}
		public function index(){
			$data['error'] = '';
		    if($this->session->userdata('logged') )
			{
				redirect('AdminDashboard/');
			}
			else
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
						$row = $this->Login_model->validate($user,$pass);
						if($row){
							if($row[0]->status == 'Deactive') {
								$data['error'] =  '<div class="alert alert-danger">Your account is deactive please contact administrator.</div>';
							} else {
								$user_basic = array( 
								'is_userloggedin' => 'true', 
								'username' => $user, 
								'adminid' => $row[0]->id,
								); 
								$this->session->set_userdata('permission',$row[0]->permission);
								$this->session->set_userdata('adminid',$row[0]->id);
								$this->session->set_userdata('roles',$row[0]->role);
								$this->session->set_userdata('logged',$user_basic);
								$this->session->set_userdata('role',$row[0]->role);
								redirect('AdminLogin/');
							}
						}else{
							$data['error'] =  '<div class="alert alert-danger">Username or Password is wrong.</div>';
						}
					}
				}
				$this->load->view('admin/login',$data);
			}
		}
		public function logout()
		{
			$this->session->unset_userdata('logged');
			$this->session->unset_userdata('role');
			redirect(base_url().'AdminLogin');
		}
		public function recover()
		{
			$this->load->view('admin/recoverpass');
		}
		public function recover_password(){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean');
			if ($this->form_validation->run() == FALSE){
			}else{
				$email = $this->input->post('email');
				$varify_email = $this->Login_model->check_email($email);
				if($varify_email){
					$key = md5(rand(99999,100000));
					$from_email = "email@example.com";
					$to_email = $this->input->post('email');
					//Load email library
					$this->load->library('email');
					$this->email->from($from_email, 'Identification');
					$this->email->to($to_email);
					$this->email->subject('Send Email Codeigniter');
					$this->email->message('Please click on given below link and reset password. <a href="'.base_url().'update_password/'.$key.'">Change your password</a>');
					//Send mail
					if($this->email->send()){
						$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
						$data = array(
						'key'=>$key
						);
						$this->Login_model->edit($email,$data,'admin_login','email');
						}else{
						$this->session->set_flashdata("email_sent","You have encountered an error");
					} 
				}
			}
		}		
		public function reset_password($temp_pass){
			if($this->Login_model->is_temp_pass_valid($temp_pass)){
				$this->load->view('admin/recoverpass');
			}else{
				echo "the key is not valid";    
			}
		}
		public function update_password(){
			$key = $this->uri->segment(3);
			$this->load->library('form_validation');
			$this->form_validation->set_rules('pass', 'Password', 'required|trim');
			$this->form_validation->set_rules('cpass', 'Confirm Password', 'required|trim|matches[password]');
			if($this->form_validation->run()){
				$check_key = $this->Login_model->check_key($key);
				if($check_key){
					$pass = md5($this->input->post('pass'));
					$data = array(
					'hascode'=>$pass
					);
					$result = $this->Login_model->edit($key,$data,'admin_login','key');
					if($result){
						redirect('login/');
					}
				}
			}
		}
	}
?>