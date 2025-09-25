<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	class AdminControler extends CI_Controller {
	    
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
		function permits() {
		    if(!checkPermission($this->session->userdata('permission'),'permit')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['permits'] = $this->Comancontroler_model->get_data_by_table('permits');
			$data['documents'] = $this->Comancontroler_model->get_data_by_column('type','permits','documentsIns');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/permits',$data);
			$this->load->view('admin/layout/footer');
		}
		function permitsAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'permit')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[2]');
				$this->form_validation->set_rules('coast', 'coast','numeric');
				$this->form_validation->set_rules('regDate', 'register date','required'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$document = '';
					$config['upload_path'] = 'assets/permits/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
					$insert_data=array(
					'title'=>$this->input->post('title'),
					'regDate'=>$this->input->post('regDate'),
					'expDate'=>$this->input->post('expDate'),
					'notes'=>$this->input->post('notes'),
					'coast'=>$this->input->post('coast'),
					'complete'=>$this->input->post('complete')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'permits'); 
					if($res){
						if(!empty($_FILES['document']['name'])){
							$config['file_name'] = $_FILES['document']['name']; 
							$this->load->library('upload',$config);
							$this->upload->initialize($config); 
							if($this->upload->do_upload('document')){ 
								$uploadData = $this->upload->data();
								$document = $uploadData['file_name'];
								$updateData = array('did'=>$res, 'type'=>'permits', 'fileurl'=>$document);
								$this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
							}
						} 
						$this->session->set_flashdata('item', 'Permits insert successfully.');
						redirect(base_url('admin/permits/add'));
					}
				}
			}
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/permitsAdd',$data);
        	$this->load->view('admin/layout/footer');
		}
		function permitsUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'permit')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[2]');
				$this->form_validation->set_rules('coast', 'coast','numeric');
				$this->form_validation->set_rules('regDate', 'register date','required');  
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$config['upload_path'] = 'assets/permits/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
					$document = '';
					if(!empty($_FILES['document']['name'])){
						$config['file_name'] = $_FILES['document']['name']; 
						$this->load->library('upload',$config);
						$this->upload->initialize($config); 
						if($this->upload->do_upload('document')){ 
							$uploadData = $this->upload->data();
							$document = $uploadData['file_name'];
							$updateData = array('did'=>$id, 'type'=>'permits', 'fileurl'=>$document);
							$driver = $this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
						}
					} 
					$insert_data = array(
					'title'=>$this->input->post('title'),
					'regDate'=>$this->input->post('regDate'),
					'expDate'=>$this->input->post('expDate'),
					'notes'=>$this->input->post('notes'),
					'coast'=>$this->input->post('coast'),
					'complete'=>$this->input->post('complete')
					); 
					$res = $this->Comancontroler_model->update_table_by_id($id,'permits',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Permits update successfully.');
						redirect(base_url('admin/permits/update/'.$id));
					}
				}
			}
			$data['permits'] = $this->Comancontroler_model->get_data_by_id($id,'permits');
			$data['documents'] = $this->Comancontroler_model->get_document_by_dispach($id,'documentsIns'); 
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/permitsUpdate',$data);
        	$this->load->view('admin/layout/footer');
		}
		function permitsDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'permit')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$this->removeDocument($id,'permits');
     		$result = $this->Comancontroler_model->delete($id,'permits','id');
     		if($result){
     			redirect('admin/permits');
			}
		}
		/********* start insurance  **************/
		function insurance() {
		    if(!checkPermission($this->session->userdata('permission'),'insurance')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['insurance'] = $this->Comancontroler_model->get_data_by_table('insurance');
			$data['documents'] = $this->Comancontroler_model->get_data_by_column('type','insurance','documentsIns');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/insurance',$data);
			$this->load->view('admin/layout/footer');
		}
		function insuranceAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'insurance')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[2]');
				$this->form_validation->set_rules('coast', 'coast','numeric');
				$this->form_validation->set_rules('startDate', 'start date','required'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$document = '';
					$config['upload_path'] = 'assets/insurance/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
					$insert_data=array(
					'title'=>$this->input->post('title'),
					'startDate'=>$this->input->post('startDate'),
					'endDate'=>$this->input->post('endDate'),
					'notes'=>$this->input->post('notes'),
					'coast'=>$this->input->post('coast'),
					'coverageAmount'=>$this->input->post('coverageAmount'),
					'policyNumber'=>$this->input->post('policyNumber'),
					'insuranceProvider'=>$this->input->post('insuranceProvider'),
					'complete'=>$this->input->post('complete')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'insurance'); 
					if($res){
						if(!empty($_FILES['document']['name'])){
							$config['file_name'] = $_FILES['document']['name']; 
							$this->load->library('upload',$config);
							$this->upload->initialize($config); 
							if($this->upload->do_upload('document')){ 
								$uploadData = $this->upload->data();
								$document = $uploadData['file_name'];
								$updateData = array('did'=>$res, 'type'=>'insurance', 'fileurl'=>$document);
								$this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
							}
						} 
						$this->session->set_flashdata('item', 'Insurance insert successfully.');
						redirect(base_url('admin/insurance/add'));
					}
				}
			}
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/insuranceAdd',$data);
        	$this->load->view('admin/layout/footer');
		}
		function insuranceUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'insurance')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[2]');
				$this->form_validation->set_rules('coast', 'coast','numeric');
				$this->form_validation->set_rules('startDate', 'start date','required');  
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$config['upload_path'] = 'assets/insurance/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
					$document = '';
					if(!empty($_FILES['document']['name'])){
						$config['file_name'] = $_FILES['document']['name']; 
						$this->load->library('upload',$config);
						$this->upload->initialize($config); 
						if($this->upload->do_upload('document')){ 
							$uploadData = $this->upload->data();
							$document = $uploadData['file_name'];
							$updateData = array('did'=>$id, 'type'=>'insurance', 'fileurl'=>$document);
							$driver = $this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
						}
					} 
					$insert_data = array(
					'title'=>$this->input->post('title'),
					'startDate'=>$this->input->post('startDate'),
					'endDate'=>$this->input->post('endDate'),
					'notes'=>$this->input->post('notes'),
					'coast'=>$this->input->post('coast'),
					'coverageAmount'=>$this->input->post('coverageAmount'),
					'policyNumber'=>$this->input->post('policyNumber'),
					'insuranceProvider'=>$this->input->post('insuranceProvider'),
					'complete'=>$this->input->post('complete')
					); 
					$res = $this->Comancontroler_model->update_table_by_id($id,'insurance',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Insurance update successfully.');
						redirect(base_url('admin/insurance/update/'.$id));
					}
				}
			}
			$data['insurance'] = $this->Comancontroler_model->get_data_by_id($id,'insurance');
			$data['documents'] = $this->Comancontroler_model->get_document_by_dispach($id,'documentsIns'); 
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/insuranceUpdate',$data);
        	$this->load->view('admin/layout/footer');
		}
		function insuranceDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'insurance')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$this->removeDocument($id,'insurance');
     		$result = $this->Comancontroler_model->delete($id,'insurance','id');
     		if($result){
     			redirect('admin/insurance');
			}
		}
		/********* end insurance  **************/
		/********* start adminUser  **************/
		function adminUser() {
			/*if( $this->session->userdata('role') > 1 ) {
				//redirect(base_url('AdminDashboard'));
			}*/
			if(!checkPermission($this->session->userdata('permission'),'users')){
			    redirect(base_url('AdminDashboard'));
			}
			$data['adminUser'] = $this->Comancontroler_model->get_data_by_table('admin_login');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/adminUser',$data);
			$this->load->view('admin/layout/footer');
		}
		function adminUserAdd() {
			if(!checkPermission($this->session->userdata('permission'),'users')){
			    redirect(base_url('AdminDashboard'));
			}
			
			$data['permission'] = $this->permission;
			
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('uname', 'name','required|min_length[4]');
				$this->form_validation->set_rules('email', 'email','valid_email');
				//$this->form_validation->set_rules('phone', 'phone','numeric');
				$this->form_validation->set_rules('username', 'user name','required|alpha_numeric|min_length[4]|is_unique[admin_login.track]');
				$this->form_validation->set_rules('password', 'password','required|min_length[5]'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
				    $permission = '';
				    if($this->input->post('permission')){
				        $permissions = $this->input->post('permission');
				        $permission = implode(',',$permissions);
				    }
					$password = md5($this->input->post('password'));
					$insert_data = array(
					'uname'=>$this->input->post('uname'),
					'track'=>$this->input->post('username'),
					'phone'=>$this->input->post('phone'),
					'status'=>$this->input->post('status'),
					'hascode'=>$password,
					'role'=>'2',
					'created_on'=>date('Y-m-d'),
					'email'=>$this->input->post('email'),
					'permission'=>$permission
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'admin_login'); 
					if($res){
						$this->session->set_flashdata('item', 'User added successfully.');
						redirect(base_url('admin/admin-user/add'));
					}
				}
			}
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/adminUserAdd',$data);
        	$this->load->view('admin/layout/footer');
		}
		function adminUserUpdate() {
			if(!checkPermission($this->session->userdata('permission'),'users')){
			    redirect(base_url('AdminDashboard'));
			}
			$data['permission'] = $this->permission;
			
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('uname', 'name','required|min_length[4]');
				$this->form_validation->set_rules('email', 'email','valid_email');
				//$this->form_validation->set_rules('phone', 'phone','numeric');
				$this->form_validation->set_rules('username', 'user name','required|min_length[4]');
				//$this->form_validation->set_rules('password', 'password','required|min_length[6]'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{  
				    $permission = '';
				    if($this->input->post('permission')){
				        $permissions = $this->input->post('permission');
				        $permission = implode(',',$permissions);
				    }
					$insert_data = array(
					'uname'=>$this->input->post('uname'), 
					'status'=>$this->input->post('status'), 
					'phone'=>$this->input->post('phone'), 
					'email'=>$this->input->post('email'),
					'permission'=>$permission
					); 
					if($this->input->post('password') != '') {
						$password = md5($this->input->post('password'));
						$insert_data['hascode'] = $password;
					}
					$res = $this->Comancontroler_model->update_table_by_id($id,'admin_login',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'User update successfully.');
						redirect(base_url('admin/admin-user/update/'.$id));
					}
				}
			}
			$data['adminUser'] = $this->Comancontroler_model->get_data_by_id($id,'admin_login');
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/adminUserUpdate',$data);
        	$this->load->view('admin/layout/footer');
		}
		function adminUserDelete(){
			if(!checkPermission($this->session->userdata('permission'),'users')){
			    redirect(base_url('AdminDashboard'));
			}
			$id = $this->uri->segment(4); 
			if($id > 1) {
				$result = $this->Comancontroler_model->delete($id,'insurance','id');
				} else {
				$result = 'true';
			}
     		if($result){
     			redirect('admin/admin-user');
			}
		}
		/********* end adminUser  **************/
		/********* start Equipment  **************/
		function equipment() {
		    if(!checkPermission($this->session->userdata('permission'),'equipment')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['equipment'] = $this->Comancontroler_model->get_data_by_table('equipment'); 
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/equipment',$data);
			$this->load->view('admin/layout/footer');
		}
		function equipmentAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'equipment')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('trailer', 'trailer','required|min_length[2]'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$document = '';
					$config['upload_path'] = 'assets/equipment/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
					$insert_data=array(
					'trailer'=>$this->input->post('trailer'),
					'ownershipStatus'=>$this->input->post('ownershipStatus'),
					'licensePlate'=>$this->input->post('licensePlate'),
					'vVin'=>$this->input->post('vVin'),
					'vMake'=>$this->input->post('vMake'),
					'vModel'=>$this->input->post('vModel'),
					'vYear'=>$this->input->post('vYear'),
					'inspectioDate'=>$this->input->post('inspectioDate'),
					'status'=>'Active',
					'inspectionExpDate'=>$this->input->post('inspectionExpDate')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'equipment'); 
					if($res){
						if(!empty($_FILES['document']['name'])){
							$config['file_name'] = $_FILES['document']['name']; 
							$this->load->library('upload',$config);
							$this->upload->initialize($config); 
							if($this->upload->do_upload('document')){ 
								$uploadData = $this->upload->data();
								$document = $uploadData['file_name'];
								$updateData = array('did'=>$res, 'type'=>'equipment', 'fileurl'=>$document);
								$this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
							}
						} 
						$this->session->set_flashdata('item', 'Equipment insert successfully.');
						redirect(base_url('admin/equipment/add'));
					}
				}
			}
			$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles','id,vname');
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/equipmentAdd',$data);
        	$this->load->view('admin/layout/footer');
		}
		function equipmentUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'equipment')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('trailer', 'trailer','required|min_length[2]'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$config['upload_path'] = 'assets/equipment/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
					$document = '';
					if(!empty($_FILES['document']['name'])){
						$config['file_name'] = $_FILES['document']['name']; 
						$this->load->library('upload',$config);
						$this->upload->initialize($config); 
						if($this->upload->do_upload('document')){ 
							$uploadData = $this->upload->data();
							$document = $uploadData['file_name'];
							$updateData = array('did'=>$id, 'type'=>'equipment', 'fileurl'=>$document);
							$driver = $this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
						}
					} 
					$insert_data = array(
					'trailer'=>$this->input->post('trailer'),
					'ownershipStatus'=>$this->input->post('ownershipStatus'),
					'licensePlate'=>$this->input->post('licensePlate'),
					'vVin'=>$this->input->post('vVin'),
					'vMake'=>$this->input->post('vMake'),
					'vModel'=>$this->input->post('vModel'),
					'vYear'=>$this->input->post('vYear'),
					'inspectioDate'=>$this->input->post('inspectioDate'), 
					'inspectionExpDate'=>$this->input->post('inspectionExpDate')
					); 
					$res = $this->Comancontroler_model->update_table_by_id($id,'equipment',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Equipment update successfully.');
						redirect(base_url('admin/equipment/update/'.$id));
					}
				}
			}
			$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles','id,vname');
			$data['equipment'] = $this->Comancontroler_model->get_data_by_id($id,'equipment');
			$data['documents'] = $this->Comancontroler_model->get_document_by_dispach($id,'documentsIns'); 
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/equipmentUpdate',$data);
        	$this->load->view('admin/layout/footer');
		}
		function equipmentDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'equipment')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$this->removeDocument($id,'equipment');
     		$result = $this->Comancontroler_model->delete($id,'equipment','id');
     		if($result){
     			redirect('admin/equipment');
			}
		}
		/********* end Equipment  **************/
		/********* start services  **************/
		function services() {
		    if(!checkPermission($this->session->userdata('permission'),'service')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$equipment = $service = $startDate = $endDate = '';
			if($this->input->post('search')) {
				$equipment = $this->input->post('equipment');
				$service = $this->input->post('service');
				$startDate = $this->input->post('sdate');
				$endDate = $this->input->post('edate');
			}
			$data['service'] = $this->Comancontroler_model->getServicesFilter($startDate,$endDate,$equipment,$service); 
			$data['equipment'] = $this->Comancontroler_model->get_data_by_table('equipment');
			$data['preServices'] = $this->Comancontroler_model->get_data_by_table('preService'); 
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/service',$data);
			$this->load->view('admin/layout/footer');
		}
		function serviceAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'service')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('equipment', 'equipment','required|numeric'); 
				$this->form_validation->set_rules('repair', 'repair','required|numeric'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$document = '';
					$config['upload_path'] = 'assets/service/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
					$insert_data=array(
					'equipment'=>$this->input->post('equipment'),
					'repair'=>$this->input->post('repair'),
					'serviceDate'=>$this->input->post('serviceDate'),
					'vendor'=>$this->input->post('vendor'),
					'coast'=>$this->input->post('coast'),
					'nextServiceDate'=>$this->input->post('nextServiceDate'),
					'mileage'=>$this->input->post('mileage'),
					'notes'=>$this->input->post('notes'),
					'status'=>'Active'
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'services'); 
					if($res){ 
						if(!empty($_FILES['invoice']['name'])){
							$config['file_name'] = $_FILES['invoice']['name']; 
							$this->load->library('upload',$config);
							$this->upload->initialize($config); 
							if($this->upload->do_upload('invoice')){ 
								$uploadData = $this->upload->data();
								$document = $uploadData['file_name'];
								$updateData = array('did'=>$res, 'type'=>'invoice', 'fileurl'=>$document);
								$this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
							}
						} 
						if(!empty($_FILES['imageBefore']['name'])){
							$config['file_name'] = $_FILES['imageBefore']['name']; 
							$this->load->library('upload',$config);
							$this->upload->initialize($config); 
							if($this->upload->do_upload('imageBefore')){ 
								$uploadData = $this->upload->data();
								$document = $uploadData['file_name'];
								$updateData = array('did'=>$res, 'type'=>'imageBefore', 'fileurl'=>$document);
								$this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
							}
						} 
						if(!empty($_FILES['imageAfter']['name'])){
							$config['file_name'] = $_FILES['imageAfter']['name']; 
							$this->load->library('upload',$config);
							$this->upload->initialize($config); 
							if($this->upload->do_upload('imageAfter')){ 
								$uploadData = $this->upload->data();
								$document = $uploadData['file_name'];
								$updateData = array('did'=>$res, 'type'=>'imageAfter', 'fileurl'=>$document);
								$this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
							}
						} 
						$this->session->set_flashdata('item', 'Service insert successfully.');
						redirect(base_url('admin/service/add'));
					}
				}
			}
			$data['equipment'] = $this->Comancontroler_model->get_data_by_table('equipment');
			$data['preServices'] = $this->Comancontroler_model->get_data_by_table('preService'); 
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/serviceAdd',$data);
        	$this->load->view('admin/layout/footer');
		}
		function serviceUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'service')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('equipment', 'equipment','required|numeric'); 
				$this->form_validation->set_rules('repair', 'repair','required|numeric');  
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$document = '';
					$config['upload_path'] = 'assets/service/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
					if(!empty($_FILES['invoice']['name'])){
						$config['file_name'] = $_FILES['invoice']['name']; 
						$this->load->library('upload',$config);
						$this->upload->initialize($config); 
						if($this->upload->do_upload('invoice')){ 
							$uploadData = $this->upload->data();
							$document = $uploadData['file_name'];
							$updateData = array('did'=>$id, 'type'=>'invoice', 'fileurl'=>$document);
							$this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
						}
					} 
					if(!empty($_FILES['imageBefore']['name'])){
						$config['file_name'] = $_FILES['imageBefore']['name']; 
						$this->load->library('upload',$config);
						$this->upload->initialize($config); 
						if($this->upload->do_upload('imageBefore')){ 
							$uploadData = $this->upload->data();
							$document = $uploadData['file_name'];
							$updateData = array('did'=>$id, 'type'=>'imageBefore', 'fileurl'=>$document);
							$this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
						}
					} 
					if(!empty($_FILES['imageAfter']['name'])){
						$config['file_name'] = $_FILES['imageAfter']['name']; 
						$this->load->library('upload',$config);
						$this->upload->initialize($config); 
						if($this->upload->do_upload('imageAfter')){ 
							$uploadData = $this->upload->data();
							$document = $uploadData['file_name'];
							$updateData = array('did'=>$id, 'type'=>'imageAfter', 'fileurl'=>$document);
							$this->Comancontroler_model->add_data_in_table($updateData,'documentsIns');
						}
					} 
					$insert_data = array(
					'equipment'=>$this->input->post('equipment'),
					'repair'=>$this->input->post('repair'),
					'serviceDate'=>$this->input->post('serviceDate'),
					'vendor'=>$this->input->post('vendor'),
					'coast'=>$this->input->post('coast'),
					'nextServiceDate'=>$this->input->post('nextServiceDate'),
					'mileage'=>$this->input->post('mileage'),
					'notes'=>$this->input->post('notes')
					); 
					$res = $this->Comancontroler_model->update_table_by_id($id,'services',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Service update successfully.');
						redirect(base_url('admin/service/update/'.$id));
					}
				}
			}
			$data['service'] = $this->Comancontroler_model->get_data_by_id($id,'services'); 
			$data['equipment'] = $this->Comancontroler_model->get_data_by_table('equipment');
			$data['preServices'] = $this->Comancontroler_model->get_data_by_table('preService'); 
			$data['documents'] = $this->Comancontroler_model->get_document_by_dispach($id,'documentsIns'); 
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/serviceUpdate',$data);
        	$this->load->view('admin/layout/footer');
		}
		function serviceDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'service')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4); 
     		$result = $this->Comancontroler_model->delete($id,'services','id');
     		if($result){
     			redirect('admin/services');
			}
		}
		/********* end service  **************/
		/********* start pre services  **************/
		function preService() {
		    if(!checkPermission($this->session->userdata('permission'),'pservice')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['preService'] = $this->Comancontroler_model->get_data_by_table('preService'); 
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/preService',$data);
			$this->load->view('admin/layout/footer');
		}
		function preServiceAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'pservice')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[2]'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$insert_data=array(
					'title'=>$this->input->post('title'),
					'frequency'=>$this->input->post('frequency'),
					'status'=>'Active'
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'preService'); 
					if($res){ 
						$this->session->set_flashdata('item', 'Pre service insert successfully.');
						redirect(base_url('admin/pre-service/add'));
					}
				}
			}
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/preServiceAdd',$data);
        	$this->load->view('admin/layout/footer');
		}
		function preServiceUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'pservice')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[2]'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$insert_data = array(
					'title'=>$this->input->post('title'),
					'frequency'=>$this->input->post('frequency')
					); 
					$res = $this->Comancontroler_model->update_table_by_id($id,'preService',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Pre service update successfully.');
						redirect(base_url('admin/pre-service/update/'.$id));
					}
				}
			}
			$data['preService'] = $this->Comancontroler_model->get_data_by_id($id,'preService'); 
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/preServiceUpdate',$data);
        	$this->load->view('admin/layout/footer');
		}
		function preServiceDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'pservice')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4); 
     		$result = $this->Comancontroler_model->delete($id,'preService','id');
     		if($result){
     			redirect('admin/pre-services');
			}
		}
		/********* end pre service  **************/
		public function removeDocument($did,$type){ 
			$files = $this->Comancontroler_model->get_document_by_dispach($did,'documentsIns');
			if($files) {
				foreach($files as $file) {
					if($file['fileurl']!='' && file_exists(FCPATH.'assets/'.$type.'/'.$file['fileurl'])) {
						unlink(FCPATH.'assets/'.$type.'/'.$file['fileurl']);   
					} 
				}
			} 
		}
		public function removeSingleDocument(){
			$did = $this->uri->segment(5);
			$id = $this->uri->segment(4);
			$type = $this->uri->segment(2);
			$file = $this->Comancontroler_model->get_data_by_id($id,'documentsIns');
			if(empty($file)) {
				$this->session->set_flashdata('item', 'File not exist.'); 
			} else {
				if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/'.$type.'/'.$file[0]['fileurl'])) {
					unlink(FCPATH.'assets/'.$type.'/'.$file[0]['fileurl']);  
					$this->session->set_flashdata('item', 'Document removed successfully.'); 
				}
				$this->Comancontroler_model->delete($id,'documentsIns','id');
			}
			redirect(base_url('admin/'.$type.'/update/'.$did));
		}
	
		/****************** shipment status *****************/
		function dispatchInfo() {
		    if(!checkPermission($this->session->userdata('permission'),'dispatchi')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['dispatchInfo'] = $this->Comancontroler_model->get_data_by_column('id >','0','dispatchInfo','*','title','asc');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/dispatchInfo',$data);
			$this->load->view('admin/layout/footer');
		}
		function dispatchInfoAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'dispatchi')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[3]|max_length[50]|is_unique[dispatchInfo.title]');
				$this->form_validation->set_rules('status','status','required');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$insert_data=array(
					'title'=>$this->input->post('title'),
					'status'=>$this->input->post('status')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'dispatchInfo'); 
					if($res){
						$this->session->set_flashdata('item', 'Dispatch info insert successfully.');
                        redirect(base_url('admin/dispatch-info/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/dispatchInfoAdd',$data);
			$this->load->view('admin/layout/footer');
		}
		function dispatchInfoUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'dispatchi')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[3]|max_length[50]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$insert_data = array(
					'title'=>$this->input->post('title'),
					'status'=>$this->input->post('status')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'dispatchInfo',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Dispatch info updated successfully.');
                        redirect(base_url('admin/dispatch-info/update/'.$id));
					}
				}
			}
			$data['dispatchInfo'] = $this->Comancontroler_model->get_data_by_id($id,'dispatchInfo');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/dispatchInfoUpdate',$data);
			$this->load->view('admin/layout/footer');
		}
		function dispatchInfoDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'dispatchi')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'dispatchInfo','id');
			if($result){
				redirect('admin/dispatch-info');
			}
		}
	
		/****************** expenses *****************/
		function expenses() {
			$data['expenses'] = $this->Comancontroler_model->get_data_by_column('id >','0','expenses','*','title','asc');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/expenses',$data);
			$this->load->view('admin/layout/footer');
		}
		function expenseAdd() {
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[3]|max_length[50]|is_unique[expenses.title]');
				$this->form_validation->set_rules('status','status','required');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$insert_data=array(
					'title'=>$this->input->post('title'),
					'type'=>$this->input->post('type'),
					'status'=>$this->input->post('status')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'expenses'); 
					if($res){
						$this->session->set_flashdata('item', 'Expense insert successfully.');
                        redirect(base_url('admin/expense/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/expenseAdd',$data);
			$this->load->view('admin/layout/footer');
		}
		function expenseUpdate() {
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[3]|max_length[50]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$insert_data = array(
					'title'=>$this->input->post('title'),
					'type'=>$this->input->post('type'),
					'status'=>$this->input->post('status')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'expenses',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Expense updated successfully.');
                        redirect(base_url('admin/expense/update/'.$id));
					}
				}
			}
			$data['expense'] = $this->Comancontroler_model->get_data_by_id($id,'expenses');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/expenseUpdate',$data);
			$this->load->view('admin/layout/footer');
		}
		function expenseDelete(){
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'expenses','id');
			if($result){
				redirect('admin/expenses');
			}
		}
	
	
	
	}
?>