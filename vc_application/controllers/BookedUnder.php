<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class BookedUnder extends CI_Controller {

	public function __construct() {

		parent::__construct();

		$this->load->library('session');

		$this->load->helper('url');

		$this->load->library('form_validation');

		$this->load->model('Comancontroler_model');

	 

    	if( empty($this->session->userdata('logged') )) {

    		redirect(base_url('AdminLogin'));

    	}

	}


	public function bookedUnder() {

        if(!checkPermission($this->session->userdata('permission'),'companyt')){

	        redirect(base_url('AdminDashboard'));   

	    }

    	$data['bookedUnder'] = $this->Comancontroler_model->get_data_by_table('booked_under','*','company','asc','All');

     

    	$this->load->view('admin/layout/header'); 

    	$this->load->view('admin/layout/sidebar');

    	$this->load->view('admin/booked-under',$data);

    	$this->load->view('admin/layout/footer');

	}

	

	public function bookedUnderAdd(){

	    if(!checkPermission($this->session->userdata('permission'),'companyt')){

	        redirect(base_url('AdminDashboard'));   

	    }

	    if($this->input->post('save'))	{

				

				$this->form_validation->set_rules('company', 'company','required|min_length[3]|max_length[50]');

				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[30]');

				

				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

				if ($this->form_validation->run() == FALSE){}

                else {

					$insert_data=array(

					    'company'=>$this->input->post('company'),

					    'mc'=>$this->input->post('mc'),

					    'dot'=>$this->input->post('dot'),

					    'ein'=>$this->input->post('ein'),

					    'email'=>$this->input->post('email'),

					    'phone'=>$this->input->post('phone'),

					    'status'=>'Active',

					    'owner'=>$this->input->post('owner')

					);

				

					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'booked_under'); 

					if($res){

						$this->session->set_flashdata('item', 'Booked under insert successfully.');

                        redirect(base_url('admin/booked-under/add'));

					}

				}

		}

      

    	$this->load->view('admin/layout/header');

    	$this->load->view('admin/layout/sidebar');

    	$this->load->view('admin/booked-under-add',$data);

    	$this->load->view('admin/layout/footer');

	}

	

	public function bookedUnderUpdate(){

	    if(!checkPermission($this->session->userdata('permission'),'companyt')){

	        redirect(base_url('AdminDashboard'));   

	    }

	    

		$id = $this->uri->segment(4);

		if($this->input->post('save'))	{

				

				$this->form_validation->set_rules('company', 'company','required|min_length[3]|max_length[50]');

				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[30]');

				

				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

				if ($this->form_validation->run() == FALSE){}

                else

                {
					$insert_data=array(

					    'company'=>$this->input->post('company'),

					    'mc'=>$this->input->post('mc'),

					    'dot'=>$this->input->post('dot'),

					    'ein'=>$this->input->post('ein'),

					    'email'=>$this->input->post('email'),

					    'phone'=>$this->input->post('phone'),

					    'status'=>$this->input->post('status'),

					    'owner'=>$this->input->post('owner')

					);

				

					$res = $this->Comancontroler_model->update_table_by_id($id,'booked_under',$insert_data); 

					if($res){

						$this->session->set_flashdata('item', 'Booked under updated successfully.');

                        redirect(base_url('admin/booked-under/update/'.$id));

					}

				}

		}

     
		$data['bookedUnder'] = $this->Comancontroler_model->get_data_by_id($id,'booked_under');


    	$this->load->view('admin/layout/header');

    	$this->load->view('admin/layout/sidebar');

    	$this->load->view('admin/booked-under-update',$data);

    	$this->load->view('admin/layout/footer');

	}

	

	public function bookedUnderDelete(){

	    if(!checkPermission($this->session->userdata('permission'),'companyt')){

	        redirect(base_url('AdminDashboard'));   

	    }

        $id = $this->uri->segment(4);

 		$result = $this->Comancontroler_model->delete($id,'booked_under','id');

 		redirect('admin/booked-under');

    }
		

}