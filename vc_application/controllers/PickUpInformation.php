<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	class PickUpInformation extends CI_Controller {
	    
	    private $permission = array('dispatch'=>'Dispatch','odispatch'=>'Outside Dispatch','invoice'=>'Invoice','statementAcc'=>'Statement Of Account','calendar'=>'Calendar','trip'=>'Driver Trip','shift'=>'Driver Shift','finance'=>'Finances','fuel'=>'Fuel','paysheet'=>'Paysheet','reimburs'=>'Reimbursement','trucksr'=>'Truck Supplies Request','admins'=>'Admin Sections','users'=>'Users','vehicle'=>'Vehicles','companya'=>'Companies Address','company'=>'Companies','companyt'=>'Trucking Companies / Booked Under','expense'=>'Expenses','dispatchi'=>'Dispatch Info','city'=>'Cities','location'=>'Locations','driver'=>'Drivers','ptripe'=>'Pre Made Trip','permit'=>'Permits','insurance'=>'Insurance','equipment'=>'Equipment','service'=>'Services','pservice'=>'Predefined Services','shipments'=>'Shipment Status');
		
		public	 function __construct() {
			parent::__construct();
			$this->load->library('session');
			$this->load->helper('url');
			$this->load->library('form_validation');
			$this->load->model('Comancontroler_model'); 
			if( empty($this->session->userdata('logged') )) {
				redirect(base_url('AdminLogin'));
			}
		}
		function index() {
		    // if(!checkPermission($this->session->userdata('permission'),'equipment')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			$data['PickUpInformation'] = $this->Comancontroler_model->get_data_by_table('erInformation'); 
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/pick_up_information',$data);
			$this->load->view('admin/layout/footer');
		}
		function add() {
		    // if(!checkPermission($this->session->userdata('permission'),'equipment')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }

			if($this->input->post('save'))	{
				$this->form_validation->set_rules('company', 'company name','required|min_length[3]|max_length[60]');
				$this->form_validation->set_rules('city', 'city','required|min_length[3]|max_length[60]');
				$this->form_validation->set_rules('address', 'address','required|min_length[3]|max_length[60]');
				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[60]');
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$city = $this->input->post('city');
                    $city = str_replace(',','',$city);
                    $state = $this->input->post('state');
                    $state = str_replace(',','',$state);
                    
					$insert_data = array(
					'company'=>$this->input->post('company'),
					'city'=>$city,
					'state'=>$state,
					'zip'=>$this->input->post('zip'),
					'phone'=>$this->input->post('phone'),
					'email'=>$this->input->post('email'),
					'address'=>$this->input->post('address')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'erInformation'); 
					if($res){
						$this->session->set_flashdata('item', 'Pickup Information insert successfully.');
                        redirect(base_url('admin/pick-up-Information/update/').$res);
					}
				}
			}
			$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles','id,vname');
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/pick_up_information_add',$data);
        	$this->load->view('admin/layout/footer');
		}
		function update() {
		    // if(!checkPermission($this->session->userdata('permission'),'companya')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }

			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('company', 'company name','required|min_length[3]|max_length[60]');
				$this->form_validation->set_rules('city', 'city','required|min_length[3]|max_length[60]');
				$this->form_validation->set_rules('address', 'address','required|min_length[3]|max_length[60]');
				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[60]');
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
                    $city = $this->input->post('city');
                    $city = str_replace(',','',$city);
                    $state = $this->input->post('state');
                    $state = str_replace(',','',$state);
                    
					$insert_data = array(
						'company'=>$this->input->post('company'),
						'city'=>$city,
						'state'=>$state,
						'zip'=>$this->input->post('zip'),
						'phone'=>$this->input->post('phone'),
						'email'=>$this->input->post('email'),
						'address'=>$this->input->post('address')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'erInformation',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Pickup Information updated successfully.');
                        redirect(base_url('admin/pick-up-Information/update/'.$id));
					}
				}
			}
			$data['PickUpInformation'] = $this->Comancontroler_model->get_data_by_id($id,'erInformation');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/pick_up_information_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function delete(){
			// if(!checkPermission($this->session->userdata('permission'),'companya')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }

			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'erInformation','id');
			if($result){
				redirect('admin/pick-up-Information');
			}
		}
	}
?>