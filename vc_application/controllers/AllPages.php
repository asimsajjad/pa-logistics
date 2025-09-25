<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AllPages extends CI_Controller {
		public function __construct()	
		{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->library('form_validation');
		$this->load->database();
        $this->load->helper(array('form','url'));
         $this->load->library('session');
		}


public function index(){

	if( empty($this->session->userdata('logged') )) 
	{
			  redirect('adminlogin/');
	}

	$data['temp']=$this->Login_model->allpages();
	
	$this->load->view('admin/layout/header');
	$this->load->view('admin/layout/sidebar');
	$this->load->view('admin/allpages',$data);
	$this->load->view('admin/layout/footer');
	}
	
	
	public function blog(){

	if( empty($this->session->userdata('logged') )) 
	{
			  redirect('adminlogin/');
	}

	$data['temp']=$this->Login_model->blog();
	
	$this->load->view('admin/layout/header');
	$this->load->view('admin/layout/sidebar');
	$this->load->view('admin/blog',$data);
	$this->load->view('admin/layout/footer');
	}
	
   public function tags(){

	if( empty($this->session->userdata('logged') )) 
	{
			  redirect('adminlogin/');
	}

	$data['temp']=$this->Login_model->tags();
	
	$this->load->view('admin/layout/header');
	$this->load->view('admin/layout/sidebar');
	$this->load->view('admin/alltags',$data);
	$this->load->view('admin/layout/footer');
	}
}


			
			


?>