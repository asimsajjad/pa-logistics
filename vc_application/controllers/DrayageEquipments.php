<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	class DrayageEquipments extends CI_Controller {
	    
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
			$data['DrayageEquipments'] = $this->Comancontroler_model->get_data_by_table('drayageEquipments'); 
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/drayage_equipments',$data);
			$this->load->view('admin/layout/footer');
		}
		function add() {
		    // if(!checkPermission($this->session->userdata('permission'),'equipment')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }

			if($this->input->post('save'))	{
				$this->form_validation->set_rules('name', 'equipment name','required|min_length[3]|max_length[60]');
			
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$insert_data = array(
					'name'=>$this->input->post('name')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'drayageEquipments'); 
					if($res){
						$this->session->set_flashdata('item', 'Equipment inserted successfully.');
                        redirect(base_url('admin/drayage-equipments/update/').$res);
					}
				}
			}
			$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles','id,vname');
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/drayage_equipments_add',$data);
        	$this->load->view('admin/layout/footer');
		}
		function update() {
		    // if(!checkPermission($this->session->userdata('permission'),'companya')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }

			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('name', 'equipment name','required|min_length[3]|max_length[60]');
			
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
                  
                    
					$insert_data = array(
						'name'=>$this->input->post('name')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'drayageEquipments',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Equipment updated successfully.');
                        redirect(base_url('admin/drayage-equipments/update/'.$id));
					}
				}
			}
			$data['DrayageEquipments'] = $this->Comancontroler_model->get_data_by_id($id,'drayageEquipments');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/drayage_equipments_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function delete(){
			// if(!checkPermission($this->session->userdata('permission'),'companya')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }

			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'drayageEquipments','id');
			if($result){
				redirect('admin/drayage-equipments');
			}
		}
	}
?>