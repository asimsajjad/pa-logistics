<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	class FactoringCompany extends CI_Controller {
		public	 function __construct()
		{
			parent::__construct();
			$this->load->library('session');
			$this->load->helper('url');
			$this->load->library('form_validation');
			$this->load->model('Comancontroler_model');
			//$this->load->database();
			if( empty($this->session->userdata('logged') )) {
				redirect(base_url('AdminLogin'));
			}
		}
		
		/****************** companies *****************/
		function companies() {
		    if(!checkPermission($this->session->userdata('permission'),'company')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['companies'] = $this->Comancontroler_model->get_data_by_table('factoringCompanies');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/factoringCompanies',$data);
			$this->load->view('admin/layout/footer');
		}
		function companyadd() {
		    if(!checkPermission($this->session->userdata('permission'),'company')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('company', 'company name','required|min_length[3]|max_length[80]|is_unique[companies.company]');
				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[60]');
				// $payoutRate = $this->input->post('payoutRate');
				// if($payoutRate == '' || (!is_numeric($payoutRate)) || $payoutRate > 1 || $payoutRate < 0.001){
				//     $this->form_validation->set_rules('payoutRatedd', 'payout rate','required');
				//     $this->form_validation->set_message('required','Payout rate will be a number between 0.001 - 1.000');
				// }
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$company = $this->input->post('company');
					$email = $this->input->post('email');
					$address = $this->input->post('address');
					$phone = $this->input->post('phone');
					$insert_data=array(
					'company'=>$company,
					'email'=>$email,
					'contactPerson'=>$this->input->post('contactPerson'),
					'bankName'=>$this->input->post('bankName'),
					'routingNumber'=>$this->input->post('routingNumber'),
					'accountNumber'=>$this->input->post('accountNumber'),
					'email2' => implode(',', $this->input->post('email2')),
					'phone2'=>$this->input->post('phone2'),
					'address'=>$address,
					'phone'=>$phone
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'factoringCompanies'); 
					if($res){
						$this->session->set_flashdata('item', 'Company inserted successfully.');
                        redirect(base_url('admin/factoringCompany/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/factoring_company_add');
			$this->load->view('admin/layout/footer');
		}
		function companyupdate() {
		    if(!checkPermission($this->session->userdata('permission'),'company')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('company', 'company name','required|min_length[2]|max_length[80]');
				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[60]');
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$company = $this->input->post('company');
					$email = $this->input->post('email');
					$address = $this->input->post('address');
					$phone = $this->input->post('phone');
					$insert_data=array(
					'company'=>$company,
					'email'=>$email,
					'address'=>$address,
					'contactPerson'=>$this->input->post('contactPerson'),
					'bankName'=>$this->input->post('bankName'),
					'routingNumber'=>$this->input->post('routingNumber'),
					'accountNumber'=>$this->input->post('accountNumber'),
					'email2' => implode(',', $this->input->post('email2')),
					'phone2'=>$this->input->post('phone2'),
					'phone'=>$phone
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'factoringCompanies',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Company updated successfully.');
                        redirect(base_url('admin/factoringCompany/update/'.$id));
					}
				}
			}
			$data['company'] = $this->Comancontroler_model->get_data_by_id($id,'factoringCompanies');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/factoring_company_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function companydelete(){
		    if(!checkPermission($this->session->userdata('permission'),'company')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'factoringCompanies','id');
			if($result){
				redirect('admin/factoringCompany');
			}
		}
	}
?>