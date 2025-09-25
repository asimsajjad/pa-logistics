<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	class Comancontroler extends CI_Controller {
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
		function vehicles() {
		    if(!checkPermission($this->session->userdata('permission'),'vehicle')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/vehicles',$data);
			$this->load->view('admin/layout/footer');
		}
		function vehicleadd() {
		    if(!checkPermission($this->session->userdata('permission'),'vehicle')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('vname', 'vehicle name','required|min_length[3]|max_length[20]|is_unique[vehicles.vname]');
				$this->form_validation->set_rules('vnumber', 'vehicle number','required|min_length[3]|max_length[20]|is_unique[vehicles.vnumber]'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$vname = $this->input->post('vname');
					$vnumber = $this->input->post('vnumber');
					$insert_data=array(
					'vname'=>$vname,
					'vnumber'=>$vnumber,
					'license_plate'=>$this->input->post('license_plate'),
					'vin'=>$this->input->post('vin'),
					'vmodel'=>$this->input->post('vmodel'),
					'vmake'=>$this->input->post('vmake'),
					'vyear'=>$this->input->post('vyear')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'vehicles'); 
					if($res){
						$this->session->set_flashdata('item', 'Vehicle insert successfully.');
                        redirect(base_url('admin/vehicle/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/vehicles_add',$data);
			$this->load->view('admin/layout/footer');
		}
		function vehicleupdate() {
		    if(!checkPermission($this->session->userdata('permission'),'vehicle')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('vname', 'vehicle name','required|min_length[3]|max_length[20]');
				$this->form_validation->set_rules('vnumber', 'vehicle number','required|min_length[3]|max_length[20]'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
                    $config['upload_path'] = 'assets/driver/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
                    if(!empty($_FILES['unitpicture']['name'])){
                        $config['file_name'] = $_FILES['unitpicture']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('unitpicture')){ 
                            $uploadData = $this->upload->data();
                            $unitpicture = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'unitpicture','docs_for'=>'vehicle','fileurl'=>$unitpicture,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
						}
					} 
                    if(!empty($_FILES['insurance']['name'])){
                        $config['file_name'] = $_FILES['insurance']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('insurance')){ 
                            $uploadData = $this->upload->data();
                            $insurance = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'insurance','docs_for'=>'vehicle','fileurl'=>$insurance,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
						}
					} 
                    if(!empty($_FILES['cabcard']['name'])){
                        $config['file_name'] = $_FILES['cabcard']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('cabcard')){ 
                            $uploadData = $this->upload->data();
                            $cabcard = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'cabcard','docs_for'=>'vehicle','fileurl'=>$cabcard,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
						}
					} 
					$vname = $this->input->post('vname');
					$vnumber = $this->input->post('vnumber');
					$insert_data=array(
					'vname'=>$vname,
					'vnumber'=>$vnumber,
					'license_plate'=>$this->input->post('license_plate'),
					'vin'=>$this->input->post('vin'),
					'vmodel'=>$this->input->post('vmodel'),
					'vmake'=>$this->input->post('vmake'),
					'vyear'=>$this->input->post('vyear')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'vehicles',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Vehicle update successfully.');
                        redirect(base_url('admin/vehicle/update/'.$id));
					}
				}
			}
			$data['vehicle'] = $this->Comancontroler_model->get_data_by_id($id,'vehicles');
			$data['documents'] = $this->Comancontroler_model->get_driver_document($id,'vehicle');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/vehicles_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function vehicledelete(){
		    if(!checkPermission($this->session->userdata('permission'),'vehicle')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			//$result = $this->Comancontroler_model->delete($id,'vehicles','id');
			//if($result){
 			redirect('admin/vehicles');
			//}
		}
		
		/****************** companyAddress *****************/
		function address() {
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['companies'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/address',$data);
			$this->load->view('admin/layout/footer');
		}
		function addressadd() {
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
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
					'shippingHours'=>$this->input->post('shippingHours'),
					'receivingHours'=>$this->input->post('receivingHours'),
					'address'=>$this->input->post('address')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'companyAddress'); 
					if($res){
						$this->session->set_flashdata('item', 'Company insert successfully.');
                        redirect(base_url('admin/address/update/').$res);
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/address_add',$data);
			$this->load->view('admin/layout/footer');
		}
		function addressupdate() {
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
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
						'shippingHours'=>$this->input->post('shippingHours'),
						'receivingHours'=>$this->input->post('receivingHours'),
						'address'=>$this->input->post('address')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'companyAddress',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Company update successfully.');
                        redirect(base_url('admin/address/update/'.$id));
					}
				}
			}
			$data['company'] = $this->Comancontroler_model->get_data_by_id($id,'companyAddress');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/address_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function addressdelete(){
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'companyAddress','id');
			if($result){
				redirect('admin/company-address');
			}
		}
		
		/****************** empty-return-information *****************/
		function erInformation() {
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['erInformation'] = $this->Comancontroler_model->get_data_by_table('erInformation');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/erInformation',$data);
			$this->load->view('admin/layout/footer');
		}
		function erInformationAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
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
						$this->session->set_flashdata('item', 'Empty Pick-up information insert successfully.');
                        redirect(base_url('admin/empty-return-information/update/').$res);
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/erInformationAdd',$data);
			$this->load->view('admin/layout/footer');
		}
		function erInformationUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
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
						$this->session->set_flashdata('item', 'Empty Pick-up information update successfully.');
                        redirect(base_url('admin/empty-return-information/update/'.$id));
					}
				}
			}
			$data['erInformation'] = $this->Comancontroler_model->get_data_by_id($id,'erInformation');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/erInformationUpdate',$data);
			$this->load->view('admin/layout/footer');
		}
		function erInformationDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'erInformation','id');
			if($result){
				redirect('admin/empty-return-information');
			}
		}
		
		/****************** companies *****************/
		function companies() {
		    if(!checkPermission($this->session->userdata('permission'),'company')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				if (!empty($company) && is_array($company)){
					$data['companies'] = $this->Comancontroler_model->get_data_by_where_in('id', $company,'companies');
				}else{
					$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies'); 
				}
			} else {
				$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');        
			}
			
			$data['customers'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/companies',$data);
			$this->load->view('admin/layout/footer');
		}
		function companyadd() {
		    if(!checkPermission($this->session->userdata('permission'),'company')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('company', 'company name','required|min_length[3]|max_length[80]|is_unique[companies.company]');
				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[60]');
				$payoutRate = $this->input->post('payoutRate');
				if($payoutRate == '' || (!is_numeric($payoutRate)) || $payoutRate > 1 || $payoutRate < 0.001){
				    $this->form_validation->set_rules('payoutRatedd', 'payout rate','required');
				    $this->form_validation->set_message('required','Payout rate will be a number between 0.001 - 1.000');
				}
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
					'payoutRate'=>$payoutRate,
					'paymenTerms'=>$this->input->post('paymenTerms'),
					// 'contactPerson'=>$this->input->post('contactPerson'),
					// 'department'=>$this->input->post('department'),
					// 'designation'=>$this->input->post('designation'),
					'email2' => implode(',', $this->input->post('email2')),
					'phone2'=>$this->input->post('phone2'),
					'warehouseCustomer'=>$this->input->post('warehouseCustomer'),
					'address'=>$address,
					'phone'=>$phone,
					'accounting_contact_person'=>$this->input->post('accounting_contact_person'),
					'accounting_email'=>$this->input->post('accounting_email'),
					'accounting_phone'=>$this->input->post('accounting_phone')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'companies'); 

					if($res){
						$company_id = $this->db->insert_id();
						$shipping_names       = $this->input->post('shipping_contact_person');
						$shipping_emails      = $this->input->post('shipping_email');
						$shipping_phones      = $this->input->post('shipping_phone');
						$shipping_departments = $this->input->post('shipping_department');
						$shipping_designations= $this->input->post('shipping_designation');

						$existing_ids = []; 
						if (!empty($shipping_names)) {
							foreach ($shipping_names as $index => $name) {
								$data = [
									'company_id'     => $company_id,
									'contact_person' => $name,
									'email'          => $shipping_emails[$index],
									'phone'          => $shipping_phones[$index],
									'department'     => $shipping_departments[$index],
									'designation'    => $shipping_designations[$index],
									'status'         => 1
								];
								$this->db->insert('company_shipping_contacts', $data);					
							}
						}

						$this->session->set_flashdata('item', 'Company insert successfully.');
                        redirect(base_url('admin/company/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/company_add',$data);
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
				$payoutRate = $this->input->post('payoutRate');
				if($payoutRate == '' || (!is_numeric($payoutRate)) || $payoutRate > 1 || $payoutRate < 0.001){
				    $this->form_validation->set_rules('payoutRateddd', 'payout rate','required');
				    $this->form_validation->set_message('required','Payout rate will be a number between 0.001 - 1.000');
				}
				$dayToPay = $this->input->post('dayToPay');
				if($dayToPay == '' || (!is_numeric($dayToPay)) || $dayToPay > 120 || $dayToPay < 0){
				    $this->form_validation->set_rules('dayToPay', 'days to pay','required');
				    $this->form_validation->set_message('required','Days to pay will be a number between 1 - 120');
				}
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
					'payoutRate'=>$payoutRate,
					'dayToPay'=>$dayToPay,
					'contactPerson'=>$this->input->post('contactPerson'),
					'department'=>$this->input->post('department'),
					'designation'=>$this->input->post('designation'),
					'email2' => implode(',', $this->input->post('email2')),
					'phone2'=>$this->input->post('phone2'),
					'paymenTerms'=>$this->input->post('paymenTerms'),
					'warehouseCustomer'=>$this->input->post('warehouseCustomer'),
					'status'=>$this->input->post('status'),
					'phone'=>$phone,
					'accounting_contact_person'=>$this->input->post('accounting_contact_person'),
					'accounting_email'=>$this->input->post('accounting_email'),
					'accounting_phone'=>$this->input->post('accounting_phone')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'companies',$insert_data); 

					$quoteFilesCount = count($_FILES['quote_d']['name']);
					if ($quoteFilesCount > 0 && !empty($_FILES['quote_d']['name'][0])) {  
						$quoteGdFiles = $_FILES['quote_d'];
						
						$config['upload_path']   = FCPATH . 'assets/customer/quote/'; 
						$config['allowed_types'] = 'pdf|doc|docx|png|jpg|jpeg';
						$config['max_size']      = 20480; // 20 MB

						$this->load->library('upload');

						for ($i = 0; $i < $quoteFilesCount; $i++) { 
							$_FILES['quote_d']['name']     = $quoteGdFiles['name'][$i];
							$_FILES['quote_d']['type']     = $quoteGdFiles['type'][$i];
							$_FILES['quote_d']['tmp_name'] = $quoteGdFiles['tmp_name'][$i];
							$_FILES['quote_d']['error']    = $quoteGdFiles['error'][$i];
							$_FILES['quote_d']['size']     = $quoteGdFiles['size'][$i]; 

							$ext = pathinfo($_FILES['quote_d']['name'], PATHINFO_EXTENSION);
							$newFileName = $company . '-QUOTE-' . date("mdY_His") . '_' . sprintf('%06d', (microtime(true) - floor(microtime(true))) * 1000000) . '.' . $ext;
							$config['file_name'] = $newFileName;

							$this->upload->initialize($config);

							if ($this->upload->do_upload('quote_d')) {
								$dataQuote_d = $this->upload->data(); 
								$quote_d = $dataQuote_d['file_name'];

								$addfile = [
									'company_id' => $id,
									'type'       => 'quote',
									'fileurl'    => $quote_d,
									'date'       => date('Y-m-d H:i:s')
								];
								$this->Comancontroler_model->add_data_in_table($addfile,'customer_documents');
							} else {
								log_message('error', 'Quote upload failed: ' . $this->upload->display_errors());
							}
						}
					}
					$shipping_ids         = $this->input->post('shipping_id'); 
					$shipping_names       = $this->input->post('shipping_contact_person');
					$shipping_emails      = $this->input->post('shipping_email');
					$shipping_phones      = $this->input->post('shipping_phone');
					$shipping_departments = $this->input->post('shipping_department');
					$shipping_designations= $this->input->post('shipping_designation');

					$existing_ids = []; 
					if (!empty($shipping_names)) {
						foreach ($shipping_names as $index => $name) {
							$id_sc = !empty($shipping_ids[$index]) ? $shipping_ids[$index] : null;
							$data = [
								'company_id'     => $id,
								'contact_person' => $name,
								'email'          => $shipping_emails[$index],
								'phone'          => $shipping_phones[$index],
								'department'     => $shipping_departments[$index],
								'designation'    => $shipping_designations[$index],
								'status'         => 1
							];

							if ($id_sc) {
								$this->db->where('id', $id_sc)->update('company_shipping_contacts', $data);
								$existing_ids[] = $id_sc;
							} else {
								$this->db->insert('company_shipping_contacts', $data);
								$existing_ids[] = $this->db->insert_id();
							}
						}
					}

					if (!empty($existing_ids)) {
						$this->db->where('company_id', $id);
						$this->db->where_not_in('id', $existing_ids);
						$this->db->update('company_shipping_contacts', ['status' => 0]);
					} else {
						$this->db->where('company_id', $id)->update('company_shipping_contacts', ['status' => 0]);
					}

					if($res){
						$this->session->set_flashdata('item', 'Company update successfully.');
                        redirect(base_url('admin/company/update/'.$id));
					}
				}
			}
			$data['company'] = $this->Comancontroler_model->get_data_by_id($id,'companies');
			$where = array('company_id'=>$id,'status'=>'1');
			$data['shipping_contacts'] = $this->Comancontroler_model->get_data_by_multiple_column($where,'company_shipping_contacts');
			$dccuments_where = array('company_id'=>$id,'type'=>'quote');
			$data['documents'] = $this->Comancontroler_model->get_data_by_multiple_column($dccuments_where,'customer_documents');

			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/company_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function companydelete(){
		    if(!checkPermission($this->session->userdata('permission'),'company')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'companies','id');
			if($result){
				redirect('admin/companies');
			}
		}
		function removefile(){
			$did = $this->uri->segment(5);
			$id = $this->uri->segment(4);
			$file = $this->Comancontroler_model->get_data_by_id($id,'customer_documents');
			if(empty($file)) {
				$this->session->set_flashdata('item', 'File not exist.'); 
			} else {
				if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/customer/quote/'.$file[0]['fileurl'])) {
					unlink(FCPATH.'assets/customer/quote/'.$file[0]['fileurl']);  
					$this->session->set_flashdata('item', 'Document removed successfully.'); 
				}
				$this->Comancontroler_model->delete($id,'customer_documents','id');
			}
			redirect(base_url('admin/company/update/'.$did));
		}
		
		/****************** shipping locations *****************/
		function locations() {
		    if(!checkPermission($this->session->userdata('permission'),'location')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/locations',$data);
			$this->load->view('admin/layout/footer');
		}
		function locationadd() {
		    if(!checkPermission($this->session->userdata('permission'),'location')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('location', 'location','required|min_length[3]|max_length[80]|is_unique[locations.location]');
				$this->form_validation->set_rules('city', 'city','required|min_length[3]|max_length[60]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$location = $this->input->post('location'); 
					$insert_data=array(
					'location'=>$location,
					'city'=>$this->input->post('city'),
					'address'=>$this->input->post('address'),
					'notes'=>$this->input->post('notes')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'locations'); 
					if($res){
						$this->session->set_flashdata('item', 'Location insert successfully.');
                        redirect(base_url('admin/location/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/location_add',$data);
			$this->load->view('admin/layout/footer');
		}
		function locationupdate() {
		    if(!checkPermission($this->session->userdata('permission'),'location')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('location', 'location','required|min_length[3]|max_length[80]|is_unique[locations.location]');
				$this->form_validation->set_rules('city', 'city','required|min_length[3]|max_length[60]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$location = $this->input->post('location');
					$insert_data=array(
					'location'=>$location,
					'city'=>$this->input->post('city'),
					'address'=>$this->input->post('address'),
					'notes'=>$this->input->post('notes')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'locations',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Location update successfully.');
                        redirect(base_url('admin/location/update/'.$id));
					}
				}
			}
			$data['location'] = $this->Comancontroler_model->get_data_by_id($id,'locations');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/location_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function locationdelete(){
		    if(!checkPermission($this->session->userdata('permission'),'location')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'locations','id');
			if($result){
				redirect('admin/locations');
			}
		}
		/****************** cities *****************/
		function cities() {
		    if(!checkPermission($this->session->userdata('permission'),'city')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/cities',$data);
			$this->load->view('admin/layout/footer');
		}
		function cityadd() {
		    if(!checkPermission($this->session->userdata('permission'),'city')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('city', 'city','required|min_length[3]|max_length[20]|is_unique[cities.city]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$city = $this->input->post('city'); 
					$insert_data=array(
					'city'=>$this->input->post('city')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'cities'); 
					if($res){
						$this->session->set_flashdata('item', 'City insert successfully.');
                        redirect(base_url('admin/city/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/city_add',$data);
			$this->load->view('admin/layout/footer');
		}
		function cityupdate() {
		    if(!checkPermission($this->session->userdata('permission'),'city')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('city', 'city','required|min_length[3]|max_length[40]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$city = $this->input->post('city');
					$insert_data=array(
					'city'=>$this->input->post('city')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'cities',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'City update successfully.');
                        redirect(base_url('admin/city/update/'.$id));
					}
				}
			}
			$data['city'] = $this->Comancontroler_model->get_data_by_id($id,'cities');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/city_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function citydelete(){
		    if(!checkPermission($this->session->userdata('permission'),'city')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'cities','id');
			if($result){
				redirect('admin/cities');
			}
		}
		/****************** Drivers *****************/
		function drivers() {
		    if(!checkPermission($this->session->userdata('permission'),'driver')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers','*','dname','asc','All');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/drivers',$data);
			$this->load->view('admin/layout/footer');
		}
		function drivergpslocation() {
		    if(!checkPermission($this->session->userdata('permission'),'driver')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('gps'))	{  
				$port_number    = 1230;
				$IPadress_host    = "192.254.234.194";
				set_time_limit(0);
				$socket_creation = socket_create(AF_INET, SOCK_STREAM, 0) or die("Unable to create socket\n");
				$socket_outcome = socket_bind($socket_creation, $IPadress_host , $port_number ) or die("Unable to bind to socket\n");
				$socket_outcome = socket_listen($socket_creation, 3) or die("Unable to set up socket listener\n");
				$socketAccept = socket_accept($socket_creation) or die("Unable to accept incoming connection\n");
				$data = socket_read($socketAccept, 1024) or die("Unable to read input\n");
				$data = trim($data);
				echo "<br> ----- Client Message : (".$data.')';
				$insert_data=array(
				'gps_info'=>$data
				);
				$this->Comancontroler_model->update_table_by_id($id,'drivers',$insert_data); 
				//$this->session->set_flashdata('item', 'Driver update successfully.');
				//redirect(base_url('admin/driver/gps-location/'.$id));
				//$outcome = strrev($data) . "\n";
				$outcome = "Done lat long update successfully \n";
				socket_write($socketAccept, $outcome, strlen ($outcome)) or die("Unable to  write output\n");
				socket_close($socketAccept);
				socket_close($socket_creation);
			}
			$data['driver'] = $this->Comancontroler_model->get_data_by_id($id,'drivers');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/driver_gps_location',$data);
			$this->load->view('admin/layout/footer');
		}
		function driveradd() {
		    if(!checkPermission($this->session->userdata('permission'),'driver')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('dname', 'driver name','required|min_length[3]|max_length[20]');
				$this->form_validation->set_rules('dcode', 'driver code','required|min_length[2]|max_length[20]');
				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[30]');
				$this->form_validation->set_rules('phone', 'phone','required|min_length[3]|max_length[30]');
				$this->form_validation->set_rules('address', 'address','required|min_length[3]|max_length[30]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$dname = $this->input->post('dname');
					$email = $this->input->post('email');
					$address = $this->input->post('address');
					$phone = $this->input->post('phone');
					$insert_data=array(
					'dname'=>$dname,
					'email'=>$email,
					'address'=>$address,
					'phone'=>$phone ,
					'dcode'=>$this->input->post('dcode')/*,
					    'sdate'=>$this->input->post('sdate'),
					    'dob'=>$this->input->post('dob'),
					    'account_no'=>$this->input->post('account_no'),
					    'routing_no'=>$this->input->post('routing_no'),
					    'bank'=>$this->input->post('bank'),
					    'lsdate'=>$this->input->post('lsdate'),
					    'ledate'=>$this->input->post('ledate'),
					    'license_no'=>$this->input->post('license_no'),
					    'medate'=>$this->input->post('medate'),
					'notes'=>$this->input->post('notes')*/
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'drivers'); 
					if($res){
						$this->session->set_flashdata('item', 'Drivers insert successfully.');
                        redirect(base_url('admin/driver/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/driver_add',$data);
			$this->load->view('admin/layout/footer');
		}
		function driverupdate() {
		    if(!checkPermission($this->session->userdata('permission'),'driver')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('dcode', 'driver code','required|min_length[2]|max_length[20]');
				$this->form_validation->set_rules('dname', 'driver name','required|min_length[3]|max_length[20]');
				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[30]');
				$this->form_validation->set_rules('phone', 'phone','required|min_length[3]|max_length[30]');
				$this->form_validation->set_rules('address', 'address','required|min_length[3]|max_length[30]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
                    $config['upload_path'] = 'assets/driver/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
                    if(!empty($_FILES['license']['name'])){
                        $config['file_name'] = $_FILES['license']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('license')){ 
                            $uploadData = $this->upload->data();
                            $license = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'license','docs_for'=>'driver','fileurl'=>$license,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
						}
					} 
                    if(!empty($_FILES['medical_card']['name'])){
                        $config['file_name'] = $_FILES['medical_card']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('medical_card')){ 
                            $uploadData = $this->upload->data();
                            $medical_card = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'medical_card','docs_for'=>'driver','fileurl'=>$medical_card,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
						}
					} 
                    if(!empty($_FILES['driving_record']['name'])){
                        $config['file_name'] = $_FILES['driving_record']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('driving_record')){ 
                            $uploadData = $this->upload->data();
                            $driving_record = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'driving_record','docs_for'=>'driver','fileurl'=>$driving_record,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
						}
					} 
                    if(!empty($_FILES['onboarding']['name'])){
                        $config['file_name'] = $_FILES['onboarding']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('onboarding')){ 
                            $uploadData = $this->upload->data();
                            $onboarding = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'onboarding','docs_for'=>'driver','fileurl'=>$onboarding,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
						}
					}
					$dname = $this->input->post('dname');
					$email = $this->input->post('email');
					$address = $this->input->post('address');
					$phone = $this->input->post('phone');
					$insert_data=array(
					'dname'=>$dname,
					'email'=>$email,
					'address'=>$address,
					'phone'=>$phone,
					'dcode'=>$this->input->post('dcode'),
					'ssn'=>$this->input->post('ssn'),
					'sdate'=>$this->input->post('sdate'),
					'dob'=>$this->input->post('dob'),
					'account_no'=>$this->input->post('account_no'),
					'routing_no'=>$this->input->post('routing_no'),
					'bank'=>$this->input->post('bank'),
					'lsdate'=>$this->input->post('lsdate'),
					'ledate'=>$this->input->post('ledate'),
					'license_no'=>$this->input->post('license_no'),
					'medate'=>$this->input->post('medate'),
					'status'=>$this->input->post('status'),
					'notes'=>$this->input->post('notes')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'drivers',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Driver update successfully.');
                        redirect(base_url('admin/driver/update/'.$id));
					}
				}
			}
			$data['driver'] = $this->Comancontroler_model->get_data_by_id($id,'drivers');
			$data['documents'] = $this->Comancontroler_model->get_driver_document($id,'driver');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/driver_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function driverdelete(){
		    if(!checkPermission($this->session->userdata('permission'),'driver')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			//$result = $this->Comancontroler_model->delete($id,'drivers','id');
			//if($result){
 			redirect('admin/drivers');
			//}
		}
		function remove_file(){
			if($license!='' && file_exists(FCPATH.'assets/driver/'.$license)) {
				unlink(FCPATH.'assets/driver/'.$license);  
			}
		}
		public function removeOtherFile(){
			$did = $this->uri->segment(5);
			$id = $this->uri->segment(4);
			$type = $this->uri->segment(2);
			$file = $this->Comancontroler_model->get_data_by_id($id,'documents');
			if(empty($file)) {
				$this->session->set_flashdata('item', 'File not exist.'); 
				} else {
				if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/'.$type.'/'.$file[0]['fileurl'])) {
					unlink(FCPATH.'assets/'.$type.'/'.$file[0]['fileurl']);  
					$this->session->set_flashdata('item', 'Document removed successfully.'); 
				}
				$this->Comancontroler_model->delete($id,'documents','id');
			}
			redirect(base_url('admin/'.$type.'/update/'.$did));
		}
		public function removeAllOtherFile($did,$type){ 
			$files = $this->Comancontroler_model->get_document_by_dispach($did);
			if($files) {
				foreach($files as $file) {
					if($file['fileurl']!='' && file_exists(FCPATH.'assets/'.$type.'/'.$file['fileurl'])) {
						unlink(FCPATH.'assets/'.$type.'/'.$file['fileurl']);   
					} 
				}
			} 
		}
		/******** fuel *************/
		function fuel() {
		    if(!checkPermission($this->session->userdata('permission'),'fuel')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$sdate = $edate = date('Y-m-d');
			$truck = $driver = '';
			if($this->input->post('search'))	{
				$sdate = $edate = '';
				$truck = $this->input->post('truck');
				$driver = $this->input->post('driver'); 
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			}
			$data['fuel'] = $this->Comancontroler_model->getOtherInfo('fuel',$sdate,$edate,$truck,$driver);
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/fuel',$data);
			$this->load->view('admin/layout/footer');
		}
		function fueladd() {
		    if(!checkPermission($this->session->userdata('permission'),'fuel')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('driver_id', 'driver id','required|min_length[1]|max_length[4]');
				$this->form_validation->set_rules('amount', 'amount','required|numeric');
				$this->form_validation->set_rules('fdate', 'date','required'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$document = '';
					$config['upload_path'] = 'assets/fuel/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000';
					$insert_data=array(
					'driver_id'=>$this->input->post('driver_id'),
					'amount'=>$this->input->post('amount'),
					'truck'=>$this->input->post('truck'),
					'notes'=>$this->input->post('notes'),
					'fdate'=>$this->input->post('fdate'),
					'document'=>$document
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'fuel'); 
					if($res){
						if(!empty($_FILES['document']['name'])){
							$config['file_name'] = $_FILES['document']['name']; 
							$this->load->library('upload',$config);
							$this->upload->initialize($config); 
							if($this->upload->do_upload('document')){ 
								$uploadData = $this->upload->data();
								$document = $uploadData['file_name'];
								$updateData = array('did'=>$res, 'type'=>'fuel', 'fileurl'=>$document,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($updateData,'documents');
							}
						} 
						$this->session->set_flashdata('item', 'Fuel insert successfully.');
						redirect(base_url('admin/fuel/add'));
					}
				}
			}
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers'); 
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/fuel_add',$data);
        	$this->load->view('admin/layout/footer');
		}
		function fuelupdate() {
		    if(!checkPermission($this->session->userdata('permission'),'fuel')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('driver_id', 'driver id','required|min_length[1]|max_length[4]');
				$this->form_validation->set_rules('amount', 'amount','required|numeric'); 
				$this->form_validation->set_rules('fdate', 'date','required'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$config['upload_path'] = 'assets/fuel/';
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
							$updateData = array('did'=>$id, 'type'=>'fuel', 'fileurl'=>$document,'rdate'=>date('Y-m-d H:i:s'));
							$driver = $this->Comancontroler_model->add_data_in_table($updateData,'documents');
						}
					} 
					$insert_data = array(
					'driver_id'=>$this->input->post('driver_id'),
					'amount'=>$this->input->post('amount'),
					'truck'=>$this->input->post('truck'),
					'fdate'=>$this->input->post('fdate'),
					'notes'=>$this->input->post('notes')
					); 
					$res = $this->Comancontroler_model->update_table_by_id($id,'fuel',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Fuel update successfully.');
						redirect(base_url('admin/fuel/update/'.$id));
					}
				}
			}
			$data['fuel'] = $this->Comancontroler_model->get_data_by_id($id,'fuel');
			$data['documents'] = $this->Comancontroler_model->get_document_by_dispach($id);
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
			//$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/fuel_update',$data);
        	$this->load->view('admin/layout/footer');
		}
		function fueldelete(){
		    if(!checkPermission($this->session->userdata('permission'),'fuel')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$this->removeAllOtherFile($id,'fuel');
     		$result = $this->Comancontroler_model->delete($id,'fuel','id');
     		if($result){
     			redirect('admin/fuel');
			}
		}
		/******** reimbursement *************/
		function reimbursement() {
		    if(!checkPermission($this->session->userdata('permission'),'reimburs')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$sdate = $edate = date('Y-m-d');
			$truck = $driver = '';
			if($this->input->post('search'))	{
				$sdate = $edate = '';
				$truck = $this->input->post('truck');
				$driver = $this->input->post('driver'); 
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			}
			$data['reimbursement'] = $this->Comancontroler_model->getOtherInfo('reimbursement',$sdate,$edate,$truck,$driver);
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/reimbursement',$data);
			$this->load->view('admin/layout/footer');
		}
		function reimbursementadd() {
		    if(!checkPermission($this->session->userdata('permission'),'reimburs')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('driver_id', 'driver id','required|min_length[1]|max_length[4]');
				$this->form_validation->set_rules('amount', 'amount','required|numeric');
				$this->form_validation->set_rules('fdate', 'date','required'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$document = '';
					$config['upload_path'] = 'assets/reimbursement/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000'; 
					$insert_data=array(
					'driver_id'=>$this->input->post('driver_id'),
					'amount'=>$this->input->post('amount'),
					'truck'=>$this->input->post('truck'),
					'notes'=>$this->input->post('notes'),
					'fdate'=>$this->input->post('fdate'),
					'document'=>$document
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'reimbursement'); 
					if($res){
						if(!empty($_FILES['document']['name'])){
							$config['file_name'] = $_FILES['document']['name']; 
							$this->load->library('upload',$config);
							$this->upload->initialize($config); 
							if($this->upload->do_upload('document')){ 
								$uploadData = $this->upload->data();
								$document = $uploadData['file_name'];
								$updateData = array('did'=>$res, 'type'=>'reimbursement', 'fileurl'=>$document,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($updateData,'documents');
							}
						} 
						$this->session->set_flashdata('item', 'Reimbursement insert successfully.');
						redirect(base_url('admin/reimbursement/add'));
					}
				}
			}
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers'); 
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/reimbursement_add',$data);
        	$this->load->view('admin/layout/footer');
		}
		function reimbursementupdate() {
		    if(!checkPermission($this->session->userdata('permission'),'reimburs')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('driver_id', 'driver id','required|min_length[1]|max_length[4]');
				$this->form_validation->set_rules('amount', 'amount','required|numeric'); 
				$this->form_validation->set_rules('fdate', 'date','required'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$config['upload_path'] = 'assets/reimbursement/';
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
							$updateData = array('did'=>$id, 'type'=>'reimbursement', 'fileurl'=>$document,'rdate'=>date('Y-m-d H:i:s'));
							$driver = $this->Comancontroler_model->add_data_in_table($updateData,'documents');
						}
					}  
					$rembursCheck = $this->input->post('rembursCheck');
					if($rembursCheck == '') {
						$rembursCheck = '0';
						$rembursDate = '0000-00-00';
                        } else {
						$rembursCheck = '1';
						$rembursDate = date('Y-m-d');
					}
					$insert_data = array(
					'driver_id'=>$this->input->post('driver_id'),
					'amount'=>$this->input->post('amount'),
					'truck'=>$this->input->post('truck'),
					'fdate'=>$this->input->post('fdate'),
					'notes'=>$this->input->post('notes'),
					'rembursCheck'=>$rembursCheck,
					'rembursDate'=> $rembursDate
					); 
					if($document != '') { $insert_data['document'] = $document; }
					$res = $this->Comancontroler_model->update_table_by_id($id,'reimbursement',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Reimbursement update successfully.');
						redirect(base_url('admin/reimbursement/update/'.$id));
					}
				}
			}
			$data['reimbursement'] = $this->Comancontroler_model->get_data_by_id($id,'reimbursement');
			$data['documents'] = $this->Comancontroler_model->get_document_by_dispach($id);
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/reimbursement_update',$data);
        	$this->load->view('admin/layout/footer');
		}
		function reimbursementCheckboxUpdate_backup() {
		    if(!checkPermission($this->session->userdata('permission'),'reimburs')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($id > 0)	{
    			$rembursCheck = '1';
                $rembursDate = date('Y-m-d');
    			$insert_data = array(
				'rembursCheck'=>$rembursCheck,
				'rembursDate'=> $rembursDate
    			);
    			$this->Comancontroler_model->update_table_by_id($id,'reimbursement',$insert_data); 
				echo json_encode([
					'success' => true,
					'message' => 'Reimbursement successful!'
				]);
        		return;
			}
			echo json_encode([
				'success' => false,
				'message' => 'Invalid reimbursement ID.'
			]);
		}
		public function reimbursementCheckboxUpdate($id = null){
			if (!checkPermission($this->session->userdata('permission'), 'reimburs')) {
				echo json_encode(['success' => false, 'message' => 'Unauthorized']);
				return;
			}
			if ($id > 0) {
				$update = [
					'rembursCheck' => '1',
					'rembursDate' => date('Y-m-d')
				];
				$this->Comancontroler_model->update_table_by_id($id, 'reimbursement', $update);
				echo json_encode(['success' => true, 'message' => 'Reimbursement successful!']);
			} else {
				echo json_encode(['success' => false, 'message' => 'Invalid ID']);
			}
		}
		function reimbursementdelete(){
		    if(!checkPermission($this->session->userdata('permission'),'reimburs')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$this->removeAllOtherFile($id,'reimbursement');
     		$result = $this->Comancontroler_model->delete($id,'reimbursement','id');
     		if($result){
     			redirect('admin/reimbursement');
			}
		}
		/******** reimbursement *************/
		function truck_supplies_request() {
		    if(!checkPermission($this->session->userdata('permission'),'trucksr')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$sdate = $edate = date('Y-m-d');
			$truck = $driver = '';
			if($this->input->post('search'))	{
				$sdate = $edate = '';
				$truck = $this->input->post('truck');
				$driver = $this->input->post('driver'); 
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			}
			$data['truck_supplies_request'] = $this->Comancontroler_model->getOtherInfo('truck_supplies_request',$sdate,$edate,$truck,$driver);
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/truck_supplies_request',$data);
			$this->load->view('admin/layout/footer');
		}
		function truck_supplies_requestadd() {
		    if(!checkPermission($this->session->userdata('permission'),'trucksr')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('driver_id', 'driver id','required|min_length[1]|max_length[4]'); 
				$this->form_validation->set_rules('fdate', 'date','required'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$document = '';
					$config['upload_path'] = 'assets/truck_supplies_request/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
					$config['max_size']= '5000'; 
					$insert_data=array(
					'driver_id'=>$this->input->post('driver_id'),
					'amount'=> '1',
					'truck'=>$this->input->post('truck'),
					'notes'=>$this->input->post('notes'),
					'fdate'=>$this->input->post('fdate'),
					'document'=>$document
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'truck_supplies_request'); 
					if($res){
						if(!empty($_FILES['document']['name'])){
							$config['file_name'] = $_FILES['document']['name']; 
							$this->load->library('upload',$config);
							$this->upload->initialize($config); 
							if($this->upload->do_upload('document')){ 
								$uploadData = $this->upload->data();
								$document = $uploadData['file_name'];
								$updateData = array('did'=>$res, 'type'=>'truck_supplies_request', 'fileurl'=>$document,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($updateData,'documents');
							}
						} 
						$this->session->set_flashdata('item', 'Truck supplies request insert successfully.');
						redirect(base_url('admin/truck_supplies_request/add'));
					}
				}
			}
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers'); 
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/truck_supplies_request_add',$data);
        	$this->load->view('admin/layout/footer');
		}
		function truck_supplies_requestupdate() {
		    if(!checkPermission($this->session->userdata('permission'),'trucksr')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('driver_id', 'driver id','required|min_length[1]|max_length[4]'); 
				$this->form_validation->set_rules('fdate', 'date','required'); 
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					$config['upload_path'] = 'assets/truck_supplies_request/';
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
							$updateData = array('did'=>$id, 'type'=>'truck_supplies_request', 'fileurl'=>$document,'rdate'=>date('Y-m-d H:i:s'));
							$driver = $this->Comancontroler_model->add_data_in_table($updateData,'documents');
						}
					}  
					$insert_data = array(
					'driver_id'=>$this->input->post('driver_id'),
					'truck'=>$this->input->post('truck'),
					'fdate'=>$this->input->post('fdate'),
					'notes'=>$this->input->post('notes')
					); 
					if($document != '') { $insert_data['document'] = $document; }
					$res = $this->Comancontroler_model->update_table_by_id($id,'truck_supplies_request',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Truck supplies request update successfully.');
						redirect(base_url('admin/truck_supplies_request/update/'.$id));
					}
				}
			}
			$data['truck_supplies_request'] = $this->Comancontroler_model->get_data_by_id($id,'truck_supplies_request');
			$data['documents'] = $this->Comancontroler_model->get_document_by_dispach($id);
			$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/truck_supplies_request_update',$data);
        	$this->load->view('admin/layout/footer');
		}
		function truck_supplies_requestdelete(){
		    if(!checkPermission($this->session->userdata('permission'),'trucksr')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$this->removeAllOtherFile($id,'truck_supplies_request');
     		$result = $this->Comancontroler_model->delete($id,'truck_supplies_request','id');
     		if($result){
     			redirect('admin/truck_supplies_request');
			}
		}
		/****************** shipment status *****************/
		function shipmentStatus() {
		    if(!checkPermission($this->session->userdata('permission'),'shipments')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('id >','0','shipmentStatus','*','title','asc');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/shipmentStatus',$data);
			$this->load->view('admin/layout/footer');
		}
		function shipmentStatusAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'shipments')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[3]|max_length[50]|is_unique[shipmentStatus.title]');
				$this->form_validation->set_rules('status','status','required');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					/* order addition*/
					$order = $this->input->post('order');

					if (!empty($order)) {
						$duplicate = $this->db->get_where('shipmentStatus', ['order' => $order])->row();
						if ($duplicate) {
							$shipment_status = $duplicate->title;
							$this->session->set_flashdata('item', '<div class="alert alert-danger">Order number already exists and is assigned to ' . $shipment_status . '.</div>');
							redirect(base_url('admin/shipment-status/add'));
						}
					}

					$insert_data=array(
					'title'=>$this->input->post('title'),
					'status'=>$this->input->post('status'),
					'order' => $this->input->post('order')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'shipmentStatus'); 
					if($res){
						$this->session->set_flashdata('item', 'Shipment insert successfully.');
                        redirect(base_url('admin/shipment-status/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/shipmentStatusAdd',$data);
			$this->load->view('admin/layout/footer');
		}
		function shipmentStatusUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'shipments')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[3]|max_length[50]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					/* order update*/
					$order = $this->input->post('order');
					if(!empty($order)){
						$duplicate = $this->db->where('order', $order)->where('id !=', $id)->get('shipmentStatus')->row();
						if ($duplicate) {
							$shipment_status = $duplicate->title;
							$this->session->set_flashdata('item', '<div class="alert alert-danger">Order number already exists and is assigned to <strong>' . htmlspecialchars($shipment_status) . '</strong>.</div>');
							redirect(base_url('admin/shipment-status/update/' . $id));
						}
					}
					
					$insert_data = array(
					'title'=>$this->input->post('title'),
					'status'=>$this->input->post('status'),
					'order' => $this->input->post('order')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'shipmentStatus',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Shipment updated successfully.');
                        redirect(base_url('admin/shipment-status/update/'.$id));
					}
				}
			}
			$data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_id($id,'shipmentStatus');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/shipmentStatusUpdate',$data);
			$this->load->view('admin/layout/footer');
		}
		function shipmentStatusDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'shipments')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'shipmentStatus','id');
			if($result){
				redirect('admin/shipment-status');
			}
		}

		/****************** warehouse services *****************/
		function warehouseServices() {
		    if(!checkPermission($this->session->userdata('permission'),'shipments')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['warehouseServices'] = $this->Comancontroler_model->get_data_by_column('id >','0','warehouseServices','*','title','asc');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/warehouseServices',$data);
			$this->load->view('admin/layout/footer');
		}
		function warehouseServicesAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'shipments')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('title', 'title','required|min_length[3]|max_length[50]|is_unique[warehouseServices.title]');
				$this->form_validation->set_rules('status','status','required');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$insert_data=array(
					'title'=>$this->input->post('title'),
					'status'=>$this->input->post('status'),
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'warehouseServices'); 
					if($res){
						$this->session->set_flashdata('item', 'Service inserted successfully.');
                        redirect(base_url('admin/warehouseServices/add'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/warehouseServicesAdd',$data);
			$this->load->view('admin/layout/footer');
		}
		function warehouseServicesUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'shipments')){
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
					'status'=>$this->input->post('status'),
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'warehouseServices',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Service updated successfully.');
                        redirect(base_url('admin/warehouseServices/update/'.$id));
					}
				}
			}
			$data['warehouseServices'] = $this->Comancontroler_model->get_data_by_id($id,'warehouseServices');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/warehouseServicesUpdate',$data);
			$this->load->view('admin/layout/footer');
		}
		function warehouseServicesDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'shipments')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'warehouseServices','id');
			if($result){
				redirect('admin/warehouseServices');
			}
		}

		/****************** warehouseAddress *****************/
		function warehouseAddress() {
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/warehouse_address',$data);
			$this->load->view('admin/layout/footer');
		}
		function warehouseAddressAdd() {
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('warehouse', 'warehouse name','required|min_length[3]|max_length[60]');
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
                    
					$warehouse = $this->input->post('warehouse');
					$address = $this->input->post('address');
					$sql = "SELECT * FROM warehouse WHERE warehouse=? AND address=? AND city=? AND state=?";
					$existing = $this->db->query($sql, [$warehouse, $address, $city, $state])->row();

					if($existing){
						$existingWarehouse = $existing->warehouse;
						$this->session->set_flashdata('item', '<div class="alert alert-danger">The warehouse ' . $existingWarehouse . ' already exists with same address, city and state.</div>');
						redirect(base_url('admin/address/warehouseAdd'));
					}

					$insert_data = array(
					'warehouse'=>$this->input->post('warehouse'),
					'city'=>$city,
					'state'=>$state,
					'zip'=>$this->input->post('zip'),
					'phone'=>$this->input->post('phone'),
					'email'=>$this->input->post('email'),
					'shippingHours'=>$this->input->post('shippingHours'),
					'receivingHours'=>$this->input->post('receivingHours'),
					'address'=>$this->input->post('address')
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'warehouse'); 
					if($res){
						$this->session->set_flashdata('item', 'Warehouse insert successfully.');
                        redirect(base_url('admin/warehouseAddress'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/warehouse_address_add',$data);
			$this->load->view('admin/layout/footer');
		}
		function warehouseAddressUpdate() {
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('warehouse', 'warehouse name','required|min_length[3]|max_length[60]');
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
                    
					$warehouse = $this->input->post('warehouse');
					$address = $this->input->post('address');
					$sql = "SELECT * FROM warehouse 
							WHERE warehouse = ? AND address = ? AND city = ? AND state = ? AND id != ?";
					$existing = $this->db->query($sql, [$warehouse, $address, $city, $state, $id])->row();

					if($existing) {
						$existingWarehouse = $existing->warehouse;
						$this->session->set_flashdata('item', 
							'<div class="alert alert-danger">The warehouse "' . $existingWarehouse . '" already exists with same address, city and state.</div>');
						redirect(base_url('admin/address/warehouseUpdate/'.$id));
					}
					$insert_data = array(
						'warehouse'=>$this->input->post('warehouse'),
						'city'=>$city,
						'state'=>$state,
						'zip'=>$this->input->post('zip'),
						'phone'=>$this->input->post('phone'),
						'email'=>$this->input->post('email'),
						'shippingHours'=>$this->input->post('shippingHours'),
						'receivingHours'=>$this->input->post('receivingHours'),
						'address'=>$this->input->post('address')
					);
					$res = $this->Comancontroler_model->update_table_by_id($id,'warehouse',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Warehouse update successfully.');
                        redirect(base_url('admin/address/warehouseUpdate/'.$id));
					}
				}
			}
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($id,'warehouse');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/warehouse_address_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function warehouseAddressDelete(){
		    if(!checkPermission($this->session->userdata('permission'),'companya')){
    	        redirect(base_url('AdminDashboard'));   
    	    }
			$id = $this->uri->segment(4);
			$result = $this->Comancontroler_model->delete($id,'warehouse','id');
			if($result){
				redirect('admin/warehouseAddress');
			}
		}
		function warehouseAddSublocation(){
			if (!checkPermission($this->session->userdata('permission'), 'companya')) {
				redirect(base_url('AdminDashboard'));
			}

			$id = $this->uri->segment(4);

			if ($this->input->post('save')) {
				$warehouse_id = $this->uri->segment(4);
				$sublocations = $this->input->post('sublocations');
				
				$existing_sublocs = $this->db->get_where('warehouse_sublocations', ['warehouse_id' => $warehouse_id])->result_array();
				$existing_ids = array_column($existing_sublocs, 'id');
				$posted_ids = [];

				foreach ($sublocations as $key => $value) {
					if ($key === 'new') {
						foreach ($value as $newName) {
							if (!empty(trim($newName))) {
								$this->db->insert('warehouse_sublocations', [
									'warehouse_id' => $warehouse_id,
									'name' => $newName
								]);
							}
						}
					} else {
						$posted_ids[] = $key;
						if (!empty(trim($value))) {
							$this->db->where('id', $key)->update('warehouse_sublocations', ['name' => $value]);
						}
					}
				}

				$to_delete = array_diff($existing_ids, $posted_ids);
				if (!empty($to_delete)) {
					$this->db->where_in('id', $to_delete)->delete('warehouse_sublocations');
				}

				$this->session->set_flashdata('item', 'Sublocations updated successfully.');
				redirect('admin/address/warehouseAddSublocation/' . $warehouse_id);
			}

			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($id, 'warehouse');
			$data['sublocations'] = $this->Comancontroler_model->get_data_by_column('warehouse_id', $id, 'warehouse_sublocations', '*', '', 'desc', '');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/warehouse_address_sublocation', $data);
			$this->load->view('admin/layout/footer');
		}

		public function getShippingContacts()
		{
			$company_id = $this->input->post('company_id');
			$this->db->where('company_id', $company_id);
			$this->db->where('status', 1);
			$query = $this->db->get('company_shipping_contacts');
			echo json_encode($query->result());
		}
	}
?>