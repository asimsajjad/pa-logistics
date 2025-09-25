<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	class Warehouse extends CI_Controller {
		public	 function __construct()
		{
			parent::__construct();
			$this->load->library('session');
			$this->load->helper('url');
			$this->load->library('form_validation');
			$this->load->model('Comancontroler_model');
			$this->load->model('Warehouse_model');
			//$this->load->database();
			if( empty($this->session->userdata('logged') )) {
				redirect(base_url('AdminLogin'));
			}
		 	
		}
		
		/****************** companies *****************/
		function index() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
		    $company = $sdate = $edate = $materialId = $warehouse_id = $sublocationId = '';
        
			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				$warehouse_id = $this->input->post('warehouse_id');
				$sublocationId = $this->input->post('sublocationId');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$data['warehouse'] = $this->Warehouse_model->warehouse($sdate,$edate,$company,$materialId,$warehouse_id,$sublocationId);
			} else {
				$data['warehouse'] = $this->Warehouse_model->warehouse($sdate,$edate,$company,$materialId,$warehouse_id,$sublocationId);
			}

			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');

			// $data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
			$data['materials'] = $this->getMaterials();
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$data['warehouse_sublocations'] = $this->Comancontroler_model->get_data_by_table('warehouse_sublocations');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse',$data);
			$this->load->view('admin/layout/footer');
		}
		/*******************Warehouse Materials ************************ */
		function materials() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			$company = $materialId ='';

			if($this->input->post('generateCSV')){
            	$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');            
            	$materials = $this->Warehouse_model->downloadWarehouseMaterials($company, $materialId);
           
				// Data to be written to the CSV file (example data)
				$heading = array('Material ID','Customer','Material Number','Description','Batch','Expiration Date');
				
				$data = array($heading);
			
				if(!empty($materials)) {
					foreach($materials as $row){
						// $expirationDate = date('m/d/Y',strtotime($row['expirationDate']));
						// $expirationDate = '';
						// if($row['expirationDate']!='0000-00-00') {
						// 	$expirationDate = '="' . date('m-d-Y', strtotime($row['expirationDate'])) . '"';
						// }
						
					$dataRow = array($row['id'],$row['customer'],$row['materialNumber'],$row['description'],$row['batch'],$row['expirationDate']);
						$data[] = $dataRow;
					}
				}
            
			
			if($this->input->post('generateCSV')){
				$fileName = "warehouseMaterials.csv";   //"data_$date.xlsx";
				// Open the file for writing
				$file = fopen($fileName, 'w');
				// Write data to the CSV file
				foreach ($data as $row) {
					fputcsv($file, $row);
				}
				// Close the file
				fclose($file); 

				// Set headers to force download
				header('Content-Type: application/csv');
				header("Content-Disposition: attachment; filename=\"$fileName\"");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fileName));

				// Output the file to the browser
				readfile($fileName);
			}

			// Delete the file from the server
			unlink($fileName);
			exit;
			die('csv');
        }
        
			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				// if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				// if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$data['warehouse'] = $this->Warehouse_model->materials($company, $materialId);
			} else {
				$data['warehouse'] = $this->Warehouse_model->materials($company, $materialId);
			}

			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');

			$data['materials'] = $this->getMaterials();

			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_materials',$data);
			$this->load->view('admin/layout/footer');
		}
		function addMaterials() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			 
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('materialNumber', 'Material Number','required|min_length[3]|max_length[80]');
				// $payoutRate = $this->input->post('payoutRate');
				// if($payoutRate == '' || (!is_numeric($payoutRate)) || $payoutRate > 1 || $payoutRate < 0.001){
				//     $this->form_validation->set_rules('payoutRatedd', 'payout rate','required');
				//     $this->form_validation->set_message('required','Payout rate will be a number between 0.001 - 1.000');
				// }
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$customerId = $this->input->post('customerId');
					$materialNumber = $this->input->post('materialNumber');
					$description = $this->input->post('description');
					$batch = $this->input->post('batch');
					$expirationDate = $this->input->post('expirationDate');
					$insert_data=array(
					'addedBy'=>$userid,
					'customerId'=>$customerId,
					'materialNumber'=>$materialNumber,
					'description'=>$description,
					'batch'=>$batch,
					'expirationDate'=>$expirationDate
					);

					$compnay=$this->Comancontroler_model->get_data_by_id($customerId,'companies','company');
					$customer_name = $compnay[0]['company'];

					$this->db->where('customerId', $customerId);
					$this->db->where('materialNumber', $materialNumber);
					$this->db->where('batch', $batch);
					$duplicate = $this->db->get('warehouseMaterials')->row();
					if($duplicate){
						$this->session->set_flashdata('error', 'Material number "'.$materialNumber.'" with batch "'.$batch.'" already exist for customer "'.$customer_name.'".');
                        redirect(base_url('admin/warehouse/addMaterials'));
					}
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'warehouseMaterials'); 
					if($res){
						$this->session->set_flashdata('item', 'Item inserted successfully.');
                        redirect(base_url('admin/warehouse/addMaterials'));
					}
				}
			}
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_materials_add',$data);
			$this->load->view('admin/layout/footer');
		}
		function updateMaterials() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$this->form_validation->set_rules('materialNumber', 'Material Number','required|min_length[3]|max_length[80]');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$materialNumber = $this->input->post('materialNumber');
					// $duplicate = $this->db->where('materialNumber', $materialNumber)->where('id !=', $id)->get('warehouseMaterials')->row();
					// if ($duplicate) {
					// 	$this->session->set_flashdata('item', '<div class="alert alert-danger">Material Number already exists.</div>');
					// 	redirect(base_url('admin/warehouse/updateMaterials/' . $id));
					// }
					$customerId = $this->input->post('customerId');
					$description = $this->input->post('description');
					$batch = $this->input->post('batch');
					$expirationDate = $this->input->post('expirationDate');
					$insert_data=array(
					'addedBy'=>$userid,
					'customerId'=>$customerId,
					'materialNumber'=>$materialNumber,
					'description'=>$description,
					'batch'=>$batch,
					'expirationDate'=>$expirationDate
					);

					$compnay=$this->Comancontroler_model->get_data_by_id($customerId,'companies','company');
					$customer_name = $compnay[0]['company'];

					$this->db->where('customerId', $customerId);
					$this->db->where('materialNumber', $materialNumber);
					$this->db->where('batch', $batch);
					if (!empty($id)) {
						$this->db->where('id !=', $id);
					}
					$duplicate = $this->db->get('warehouseMaterials')->row();
					if($duplicate){
						$this->session->set_flashdata('error', 'Material number "'.$materialNumber.'" with batch "'.$batch.'" already exist for customer "'.$customer_name.'".');
                        redirect(base_url('admin/warehouse/updateMaterials/'.$id));
					}

					$res = $this->Comancontroler_model->update_table_by_id($id,'warehouseMaterials',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Item updated successfully.');
                        redirect(base_url('admin/warehouse/updateMaterials/'.$id));
					}
				}
			}
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($id,'warehouseMaterials');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_materials_update',$data);
			$this->load->view('admin/layout/footer');
		}
		function deleteMaterials(){
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			$id = $this->uri->segment(4);
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			$updateInbound = array(
				'deleted' => 'Y',
				'addedBy' => $userid,
				'date' => date('Y-m-d H:i:s')
			);
			$result=$this->Comancontroler_model->update_table_by_id($id, 'warehouseMaterials', $updateInbound);	
			if($result){
				$this->session->set_flashdata('item', 'Item deleted successfully.');
				redirect('admin/warehouseMaterials');
			}
			redirect('admin/warehouseMaterials');
			// $result = $this->Comancontroler_model->delete($id,'warehouseMaterials','id');
			
		}
		function uploadMaterials(){
			if(!checkPermission($this->session->userdata('permission'),'odispatch')){
				redirect(base_url('AdminDashboard'));   
			}
			$data['error'] = array();
			$data['upload'] = '';
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			if(isset($_GET['dummy']) && $_GET['dummy']=='csv'){
				$data = array(
					array('Material ID','Customer','Material Number','Description','Batch','Expiration Date'),
					array('', 'Customer A', 'Material-001', 'Sample Material A', 'BATCH001', '6/30/2025'),
					array('', 'Customer B', 'Material-002', 'Sample Material B', 'BATCH002', '7/15/2025')
				);
				$fileName = "WarehouseMatrials_Sample.csv";   //"data_$date.xlsx";
				$file = fopen($fileName, 'w');
				foreach ($data as $row) {
					fputcsv($file, $row);
				}
				fclose($file); 
				header('Content-Type: application/csv');
				header("Content-Disposition: attachment; filename=\"$fileName\"");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fileName));
				readfile($fileName);
					
				die();    
			} 
			if($this->input->post('uploadcsv'))	{ 
				$this->form_validation->set_rules('csvfile1', 'csv file','required');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{ 
					$config['upload_path'] = 'assets/csvfiles/';
					$config['allowed_types'] = 'csv';
					$this->load->library('upload',$config);
					$this->upload->initialize($config); 
					if($this->upload->do_upload('csvfile')){
						$uploadData = $this->upload->data();
						$csv_file = $uploadData['full_path'];
						$csv = array_map('str_getcsv', file($csv_file));
						// print_r($csv);exit;		
						$skipped_duplicates = []; 
						$missing_customers = [];
						
						foreach ($csv as $index => $row) { 
							if (strtolower(trim($row[0])) == 'material id') continue;

							$mid             = trim($row[0]);
							$customer        = trim($row[1]);
							$materialNumber  = trim($row[2]);
							$description     = trim($row[3]);
							$batch           = trim($row[4]);
							$expirationRaw   = str_replace('-', '/', trim($row[5]));
							$expirationDateObj = DateTime::createFromFormat('n/j/Y', $expirationRaw);
							$expirationDate  = $expirationDateObj ? $expirationDateObj->format('Y-m-d') : null;

							$customerId = $this->getCustomerId($customer);
							if ($customerId === false) {
								$missing_customers[] = "Row #".($index+1)." skipped: Customer '<strong>".htmlspecialchars($customer)."</strong>' not found.";
								continue;
							}
							$insert_data = array(
								'addedBy'        => $userid,
								'customerId'     => $customerId,
								'materialNumber' => $materialNumber,
								'description'    => $description,
								'batch'          => $batch,
								'expirationDate' => $expirationDate
							);

							$this->db->where('customerId', $customerId);
							$this->db->where('materialNumber', $materialNumber);
							$this->db->where('batch', $batch);
							if (!empty($mid)) {
								$this->db->where('id !=', $mid);
							}

							$duplicate = $this->db->get('warehouseMaterials')->row();

							if ($duplicate) {
								// Log duplicate
								$skipped_duplicates[] = array(
									'rowNumber'      => $index + 1,
									'customer'       => $customer,
									'materialNumber' => $materialNumber,
									'batch'          => $batch
								);
								continue; // skip insert/update
							}

							if (!empty($mid)) {
								$res = $this->Comancontroler_model->update_table_by_id($mid, 'warehouseMaterials', $insert_data);
							} else {
								$res = $this->Comancontroler_model->add_data_in_table($insert_data, 'warehouseMaterials');
							}
						}
							$data['skipped'] = $skipped_duplicates;
							$data['error'] = $missing_customers;
							$data['upload'] = 'done';
						}
						
						unlink($csv_file);
					}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/upload_warehouse_materials',$data);
			$this->load->view('admin/layout/footer');
		}
		/*******************Warehouse Inbounds ************************ */
		function inbounds() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			$company = $sdate = $edate = $materialId = $warehouse_id = $sublocationId = '';
        
		 	if($this->input->post('generateCSV')){
            	$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				$warehouse_id = $this->input->post('warehouse_id');
				$sublocationId = $this->input->post('sublocationId');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$warehouseInbounds = $this->Warehouse_model->downloadWarehouseBounds('inbound',$sdate,$edate,$company,$materialId,$warehouse_id,$sublocationId);

				$heading = array('inbound_id', 'bound_id', 'Date In','Customer', 'Warehouse', 'Sublocation', 'Material Number','Lot Number','Pallet Number', 'Pallet Position', 'Pallet Quantity', 'Pieces Quantity');
				
				$data = array($heading);
			
				if(!empty($warehouseInbounds)) {
					foreach($warehouseInbounds as $row){
					$dataRow = array(
						$row['id'],$row['bound_id'],$row['dated'],$row['customer'],$row['warehouse'],$row['sublocation'],$row['material_number'],$row['lot_number'],$row['pallet_number'],$row['pallet_position'],$row['pallet_quantity'],$row['pieces_quantity']);
						$data[] = $dataRow;
					}
				}
            
			
			if($this->input->post('generateCSV')){
				$fileName = "warehouseInbounds.csv";   
				$file = fopen($fileName, 'w');
				foreach ($data as $row) {
					fputcsv($file, $row);
				}
				
				fclose($file); 

				header('Content-Type: application/csv');
				header("Content-Disposition: attachment; filename=\"$fileName\"");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fileName));

				readfile($fileName);
			}

			unlink($fileName);
			exit;
			die('csv');
        	}

			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				$warehouse_id = $this->input->post('warehouse_id');
				$sublocationId = $this->input->post('sublocationId');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$data['warehouse'] = $this->Warehouse_model->inbounds($sdate,$edate,$company,$materialId,$warehouse_id,$sublocationId);
			} else {
				$data['warehouse'] = $this->Warehouse_model->inbounds($sdate,$edate,$company,$materialId,$warehouse_id,$sublocationId);
			}
			$data['materials'] = $this->getMaterials();
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$data['warehouse_sublocations'] = $this->Comancontroler_model->get_data_by_table('warehouse_sublocations');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_inbounds',$data);
			$this->load->view('admin/layout/footer');
		}
		function addInbounds_old() {
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			if($this->input->post('save'))	{
				$customerId = $this->input->post('customerId');
				$warehouseAddressId = $this->input->post('warehouseAddressId');
				$sublocationId = $this->input->post('sublocationId');
				$materialId = $this->input->post('materialId');
				$batch = $this->input->post('batch');
				$palletPosition = $this->input->post('palletPosition');
				$lotNumber = $this->input->post('lotNumber');					
				$palletNumber = $this->input->post('palletNumber');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$dateIn = $this->input->post('dateIn');
				$allInserted = true;
				$this->db->trans_start();
				for ($i = 0; $i < count($materialId); $i++) {
					if (empty($materialId[$i])) continue;
					$insert_data = array(
						'addedBy'        => $userid,
						'customerId'     => $customerId,
						'warehouseAddressId'=>$warehouseAddressId,
						'sublocationId'=>$sublocationId,
						'materialId'     => $materialId[$i],
						'lotNumber'	=>	$lotNumber[$i],
						'palletNumber'   => $palletNumber[$i],
						'palletPosition'   => $palletPosition[$i],
						'palletQuantity' => $palletQuantity[$i],
						'piecesQuantity' => $piecesQuantity[$i],
						'dateIn'         => $dateIn
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data, 'warehouseInbounds');
					if ($res){
						$where = array(
							'customerId' => $customerId,
							'materialId' => $materialId[$i]
						);
						$existing = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse');
						if (!empty($existing)) {
							$update_data = array(
								'quantity'   => $existing[0]['quantity'] + $piecesQuantity[$i],
								'updatedBy'  => $userid,
								'date'       => date('Y-m-d')
							);
							$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse', $update_data);
							$log_data = array(
								'userId'        => $userid,
								'customerId'     => $customerId,
								'materialId'     => $materialId[$i],
								'action' => 'Inserted',
								'table' => 'warehouseInbounds',
								'recordId' => $res
							);
							$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');
						} else {
							$warehouse_data = array(
								'customerId' => $customerId,
								'materialId' => $materialId[$i],
								'quantity'   => $piecesQuantity[$i],
								'updatedBy'  => $userid				
							);
							$this->Comancontroler_model->add_data_in_table($warehouse_data, 'warehouse');
							$log_data = array(
								'userId'        => $userid,
								'customerId'     => $customerId,
								'materialId'     => $materialId[$i],
								'action' => 'Inserted',
								'table' => 'warehouseInbounds',
								'recordId' => $res
							);
							$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');
						}
					}else{
						$allInserted = false;
						break;
					}
				}
				$this->db->trans_complete();
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Addition failed. No data was inserted.');
				} else {
					$this->db->trans_commit();
					if ($allInserted) {
						$this->session->set_flashdata('item', 'Item(s) inserted successfully.');
					} else {
						$this->session->set_flashdata('error', 'Failed to insert all items.');
					}                   
					redirect(base_url('admin/warehouse/addInbounds'));
				}
				redirect(base_url('admin/warehouse/addInbounds'));				
			}
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
	    	$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_inbounds_add',$data);
			$this->load->view('admin/layout/footer');
		}
		public function addInbounds() {
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			if ($this->input->post('save')) {
				$customerId = $this->input->post('customerId');
				$warehouseId = $this->input->post('warehouse_id');
				$sublocationId = $this->input->post('sublocationId');
				$materialId = $this->input->post('materialId');
				$lotNumber = $this->input->post('lotNumber');
				$palletNumber = $this->input->post('palletNumber');
				$palletPosition = $this->input->post('palletPosition');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$dateIn = $this->input->post('dateIn');
				$notes = $this->input->post('notes');
		
				$total_pallets = array_sum($palletQuantity);
				$total_pieces = array_sum($piecesQuantity);

				$this->db->trans_start();

				$inboundFilesCount = count($_FILES['inboundFile']['name']);
				$inboundFiles = ''; 
				if($inboundFilesCount > 0) {  
					$uploadedFiles = [];
					$inboundFileArray = $_FILES['inboundFile'];

					$config['upload_path']   = 'assets/warehouse-inbounds/inbound-files/';
					$config['allowed_types'] = '*'; 
					$this->load->library('upload', $config);
					$this->upload->initialize($config); 

					for($i = 0; $i < $inboundFilesCount; $i++) {
						$_FILES['inboundFile']['name']     = $inboundFileArray['name'][$i];
						$_FILES['inboundFile']['type']     = $inboundFileArray['type'][$i];
						$_FILES['inboundFile']['tmp_name'] = $inboundFileArray['tmp_name'][$i];
						$_FILES['inboundFile']['error']    = $inboundFileArray['error'][$i];
						$_FILES['inboundFile']['size']     = $inboundFileArray['size'][$i]; 
						if ($this->upload->do_upload('inboundFile')) { 
							$dataInboundFile = $this->upload->data(); 
							$original_name = pathinfo($dataInboundFile['orig_name'], PATHINFO_FILENAME);
							$extension = $dataInboundFile['file_ext'];
							$unique_name = $original_name . '_' . round(microtime(true) * 10000) . $extension;

							rename($dataInboundFile['full_path'], $dataInboundFile['file_path'] . $unique_name);
							$uploadedFiles[] = $unique_name;
						}

					}
					$inboundFiles = implode(',', $uploadedFiles);
				}

				$parent_data = array(
					'added_by'        => $userid,
					'customer_id'     => $customerId,
					'warehouse_id'    => $warehouseId,
					'sublocation_id'  => $sublocationId,
					'total_pallets'   => $total_pallets,
					'total_pieces'    => $total_pieces,
					'dated'           => $dateIn,
					'type'            => 'inbound',
					'file'	  => $inboundFiles
				);

				$this->db->insert('warehouse_bounds', $parent_data);
				$bound_id = $this->db->insert_id();

				for ($i = 0; $i < count($materialId); $i++) {
					if (empty($materialId[$i])) continue;
					
					$inboundMaterialFile = '';
					$uploadedInboundMaterialFiles = [];
					if (isset($_FILES['inboundMaterialFile']['name'][$i])) {
						$fileCount = count($_FILES['inboundMaterialFile']['name'][$i]);
						for ($j = 0; $j < $fileCount; $j++) {
							$_FILES['file_temp']['name']     = $_FILES['inboundMaterialFile']['name'][$i][$j];
							$_FILES['file_temp']['type']     = $_FILES['inboundMaterialFile']['type'][$i][$j];
							$_FILES['file_temp']['tmp_name'] = $_FILES['inboundMaterialFile']['tmp_name'][$i][$j];
							$_FILES['file_temp']['error']    = $_FILES['inboundMaterialFile']['error'][$i][$j];
							$_FILES['file_temp']['size']     = $_FILES['inboundMaterialFile']['size'][$i][$j];
							$config['upload_path']   = 'assets/warehouse-inbounds/inbound-materials/';
							$config['allowed_types'] = '*';
							$this->load->library('upload', $config);
							$this->upload->initialize($config);
							if ($this->upload->do_upload('file_temp')) {
								$data = $this->upload->data();
								$original_name = pathinfo($data['orig_name'], PATHINFO_FILENAME);
								$extension = $data['file_ext'];
								$unique_name = $original_name . '_' . round(microtime(true) * 10000) . $extension;
								rename($data['full_path'], $data['file_path'] . $unique_name);
								$uploadedInboundMaterialFiles[] = $unique_name;
							}
						}
					}
					$inboundMaterialFile = implode(',', $uploadedInboundMaterialFiles);
					
					$detail_data = array(
						'bound_id'        => $bound_id,
						'type'            => 'inbound',
						'customer_id'     => $customerId,
						'warehouse_id'    => $warehouseId,
						'sublocation_id'  => $sublocationId,
						'material_id'     => $materialId[$i],
						'lot_number'      => $lotNumber[$i],
						'pallet_number'   => $palletNumber[$i],
						'pallet_position' => $palletPosition[$i],
						'pallet_quantity' => $palletQuantity[$i],
						'pieces_quantity' => $piecesQuantity[$i],
						'file'			  => $inboundMaterialFile,
						'notes' => $notes[$i]
					);

					$this->db->insert('warehouse_bound_details', $detail_data);
					$log_data = array(
						'userId'     => $userid,
						'customer_id' => $customerId,
						'material_id' => $materialId[$i],
						'action'     => 'Inserted',
						'type'      => 'inbound',
						'detail_id'   => $bound_id
					);
					$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');

					$stock_where = array(
						'warehouse_id'   => $warehouseId,
						'sublocation_id' => $sublocationId,
						'customer_id'    => $customerId,
						'material_id'    => $materialId[$i]
					);

					$existing_stock = $this->Comancontroler_model->get_data_by_multiple_column($stock_where, 'warehouse_stock');

					if (!empty($existing_stock)) {
						$updated_stock = array(
							'total_pallets' => $existing_stock[0]['total_pallets'] + $palletQuantity[$i],
							'total_pieces'  => $existing_stock[0]['total_pieces'] + $piecesQuantity[$i],
							'updated_by'    => $userid
						);
						$this->Comancontroler_model->update_table_by_multiple_column($stock_where, 'warehouse_stock', $updated_stock);
					} else {
						$new_stock = $stock_where + array(
							'total_pallets' => $palletQuantity[$i],
							'total_pieces'  => $piecesQuantity[$i],
							'updated_by'    => $userid
						);
						$this->Comancontroler_model->add_data_in_table($new_stock, 'warehouse_stock');
					}
				}

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Inbound addition failed.');
				} else {
					$this->session->set_flashdata('item', 'Inbound inserted successfully.');
				}

				redirect(base_url('admin/warehouse/addInbounds'));
			}

			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_inbounds_add', $data);
			$this->load->view('admin/layout/footer');
		}
		function updateInbounds_old() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$customerId = $this->input->post('customerId');
				$warehouseAddressId = $this->input->post('warehouseAddressId');
				$sublocationId = $this->input->post('sublocationId');
				$materialId = $this->input->post('materialId');
				$batch = $this->input->post('batch');	
				$lotNumber = $this->input->post('lotNumber');									
				$palletNumber = $this->input->post('palletNumber');
				$palletPosition = $this->input->post('palletPosition');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$dateIn = $this->input->post('dateIn');
				$insert_data=array(
					'addedBy'=>$userid,
					'customerId'=>$customerId,
					'warehouseAddressId'=>$warehouseAddressId,
					'sublocationId'=>$sublocationId,
					'materialId'=>$materialId,
					'lotNumber'=>$lotNumber,
					'palletNumber'=>$palletNumber,
					'palletPosition'=>$palletPosition,
					'palletQuantity'=>$palletQuantity,
					'piecesQuantity'=>$piecesQuantity,
					'dateIn'=>$dateIn
				);
				$existingInbound = $this->Comancontroler_model->get_data_by_id($id, 'warehouseInbounds');
				$oldQuantity = $existingInbound[0]['piecesQuantity'];
				// print_r($existingInbound[0]['piecesQuantity']);exit; 
				
				$res = $this->Comancontroler_model->update_table_by_id($id,'warehouseInbounds',$insert_data); 
				if ($res) {
					$where = array(
						'customerId' => $customerId,
						'materialId' => $materialId
					);
					$existingWarehouse = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse');
					if (!empty($existingWarehouse)) {
						$newQuantity = $existingWarehouse[0]['quantity'] - $oldQuantity + $piecesQuantity;
						$update_data = array(
							'quantity'  => max(0, $newQuantity), // avoid negative
							'updatedBy' => $userid,
							'date'      => date('Y-m-d')
						);
						$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse', $update_data);
						$log_data = array(
								'userId' => $userid,
								'customerId' => $customerId,
								'materialId' => $materialId,
								'action' => 'Updated',
								'table' => 'warehouseInbounds',
								'recordId' => $res
							);
							$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');
						if ($newQuantity <= 0) {
							$this->session->set_flashdata('warning', 'Note: Warehouse quantity for this material is now 0.');
						}
					} else {
						$warehouse_data = array(
							'customerId' => $customerId,
							'materialId' => $materialId,
							'quantity'   => $piecesQuantity,
							'updatedBy'  => $userid,
							'date'       => date('Y-m-d')
						);
						$this->Comancontroler_model->add_data_in_table($warehouse_data, 'warehouse');
						$log_data = array(
								'userId' => $userid,
								'customerId' => $customerId,
								'materialId' => $materialId,
								'action' => 'Updated',
								'table' => 'warehouseInbounds',
								'recordId' => $res
							);
							$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');
					}
					$this->session->set_flashdata('item', 'Item updated successfully.');
					redirect(base_url('admin/warehouse/updateInbounds/'.$id));
				}
				
			}
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($id,'warehouseInbounds');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_inbounds_update',$data);
			$this->load->view('admin/layout/footer');
		}
		public function updateInbounds() {
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			$detail_id = $this->uri->segment(4); 
			$dated = $this->input->get('dated');


			if ($this->input->post('save')) {
				$customerId     = $this->input->post('customerId');
				$warehouseId    = $this->input->post('warehouse_id');
				$sublocationId  = $this->input->post('sublocationId');
				$materialId     = $this->input->post('materialId');
				$lotNumber      = $this->input->post('lotNumber');
				$palletNumber   = $this->input->post('palletNumber');
				$palletPosition = $this->input->post('palletPosition');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$dateIn         = $this->input->post('dateIn');
				$notes          = $this->input->post('notes');

				$existingDetail = $this->Comancontroler_model->get_data_by_id($detail_id, 'warehouse_bound_details');
				$oldDetail = $existingDetail[0];

				$oldPieces     = $oldDetail['pieces_quantity'];
				$oldPallets    = $oldDetail['pallet_quantity'];
				$bound_id      = $oldDetail['bound_id'];
				$existingFiles = !empty($oldDetail['file']) ? explode(',', $oldDetail['file']) : [];

				$this->db->trans_start();

				$uploadedInboundMaterialFiles = [];
				$inboundMaterialFile = '';

				if (isset($_FILES['inboundMaterialFile']['name']) && is_array($_FILES['inboundMaterialFile']['name'])) {
					$fileCount = count($_FILES['inboundMaterialFile']['name']);
					$files = $_FILES['inboundMaterialFile'];

					$config['upload_path'] = 'assets/warehouse-inbounds/inbound-materials/';
					$config['allowed_types'] = '*';
					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					for ($i = 0; $i < $fileCount; $i++) {
						$_FILES['inbound_file']['name']     = $files['name'][$i];
						$_FILES['inbound_file']['type']     = $files['type'][$i];
						$_FILES['inbound_file']['tmp_name'] = $files['tmp_name'][$i];
						$_FILES['inbound_file']['error']    = $files['error'][$i];
						$_FILES['inbound_file']['size']     = $files['size'][$i];

						if ($this->upload->do_upload('inbound_file')) {
							$data = $this->upload->data();
							$original_name = pathinfo($data['orig_name'], PATHINFO_FILENAME);
							$extension = $data['file_ext'];
							$unique_name = $original_name . '_' . round(microtime(true) * 10000) . $extension;
							$oldPath = $data['full_path'];
							$newPath = $data['file_path'] . $unique_name;

							if (file_exists($oldPath)) {
								if (rename($oldPath, $newPath)) {
									$uploadedInboundMaterialFiles[] = $unique_name;
								}
							}
						} else {
							log_message('error', 'Upload error: ' . $this->upload->display_errors());
						}
					}

					// Delete old files if new ones were uploaded
					if (!empty($uploadedInboundMaterialFiles) && !empty($oldDetail['file'])) {
						$existingFiles = explode(',', $oldDetail['file']);
						foreach ($existingFiles as $oldFile) {
							$oldFilePath = FCPATH . 'assets/warehouse-inbounds/inbound-materials/' . $oldFile;
							if (file_exists($oldFilePath)) {
								unlink($oldFilePath);
							}
						}
					}
				}

				// Final assignment
				$inboundMaterialFile = !empty($uploadedInboundMaterialFiles)
					? implode(',', $uploadedInboundMaterialFiles)
					: $oldDetail['file'];


				// Update inbound detail
				$update_data = array(
					'customer_id'     => $customerId,
					'warehouse_id'    => $warehouseId,
					'sublocation_id'  => $sublocationId,
					'material_id'     => $materialId,
					'lot_number'      => $lotNumber,
					'pallet_number'   => $palletNumber,
					'pallet_position' => $palletPosition,
					'pallet_quantity' => $palletQuantity,
					'pieces_quantity' => $piecesQuantity,
					'file' 			  => $inboundMaterialFile,
					'notes'			  => $notes
				);

				$this->Comancontroler_model->update_table_by_id($detail_id, 'warehouse_bound_details', $update_data);

				// Update warehouse_bounds summary
				$this->db->select('SUM(pallet_quantity) AS total_pallets, SUM(pieces_quantity) AS total_pieces');
				$this->db->where('bound_id', $bound_id);
				$this->db->where('type', 'inbound');
				$this->db->where('deleted', 'N');
				$sums = $this->db->get('warehouse_bound_details')->row_array();

				$this->Comancontroler_model->update_table_by_id($bound_id, 'warehouse_bounds', array(
					'total_pallets' => $sums['total_pallets'],
					'total_pieces'  => $sums['total_pieces'],
					'dated'         => $dateIn
				));

				// Revert stock from old material
				$oldStockWhere = array(
					'warehouse_id'   => $oldDetail['warehouse_id'],
					'sublocation_id' => $oldDetail['sublocation_id'],
					'customer_id'    => $oldDetail['customer_id'],
					'material_id'    => $oldDetail['material_id']
				);
				$oldStock = $this->Comancontroler_model->get_data_by_multiple_column($oldStockWhere, 'warehouse_stock');
				if (!empty($oldStock)) {
					$this->Comancontroler_model->update_table_by_multiple_column($oldStockWhere, 'warehouse_stock', array(
						'total_pieces'  => max(0, $oldStock[0]['total_pieces'] - $oldPieces),
						'total_pallets' => max(0, $oldStock[0]['total_pallets'] - $oldPallets),
						'updated_by'    => $userid
					));
				}

				// Add to new material's stock
				$newStockWhere = array(
					'warehouse_id'   => $warehouseId,
					'sublocation_id' => $sublocationId,
					'customer_id'    => $customerId,
					'material_id'    => $materialId
				);
				$newStock = $this->Comancontroler_model->get_data_by_multiple_column($newStockWhere, 'warehouse_stock');
				if (!empty($newStock)) {
					$this->Comancontroler_model->update_table_by_multiple_column($newStockWhere, 'warehouse_stock', array(
						'total_pieces'  => max(0, $newStock[0]['total_pieces'] + $piecesQuantity),
						'total_pallets' => max(0, $newStock[0]['total_pallets'] + $palletQuantity),
						'updated_by'    => $userid
					));
				} else {
					$newStockData = $newStockWhere + array(
						'total_pieces'  => $piecesQuantity,
						'total_pallets' => $palletQuantity,
						'updated_by'    => $userid
					);
					$this->Comancontroler_model->add_data_in_table($newStockData, 'warehouse_stock');
				}

				// Log the update
				$log_data = array(
					'userId'      => $userid,
					'customer_id' => $customerId,
					'material_id' => $materialId,
					'action'      => 'Updated',
					'type'        => 'inbound',
					'detail_id'   => $detail_id
				);
				$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Inbound update failed. Operation rolled back.');
				} else {
					$this->session->set_flashdata('item', 'Inbound updated successfully.');
				}

				redirect(base_url('admin/warehouse/updateInbounds/' . $detail_id));
			}

			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($detail_id, 'warehouse_bound_details');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$bound_id = isset($data['warehouse'][0]['bound_id']) ? $data['warehouse'][0]['bound_id'] : null;
			if ($bound_id) {
				$boundDetail = $this->Comancontroler_model->get_data_by_id($bound_id, 'warehouse_bounds');
				$data['dated'] = isset($boundDetail[0]['dated']) ? $boundDetail[0]['dated'] : '';
			} else {
				$data['dated'] = '';
			}
			// $data['dated'] = $dated;
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_inbounds_update', $data);
			$this->load->view('admin/layout/footer');
		}
		public function deleteInbounds_old() {
			$id = $this->uri->segment(4);
			$record = $this->Comancontroler_model->get_data_by_id($id, 'warehouseInbounds');

			if (empty($record)) {
				$this->session->set_flashdata('error', 'Inbound record not found.');
				redirect('admin/warehouseInbounds');
				return;
			}

			$record = $record[0];
			$customerId = $record['customerId'];
			$materialId = $record['materialId'];
			$piecesQuantity = $record['piecesQuantity'];

			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			// 1. Get current stock
			$where = array(
				'customerId' => $customerId,
				'materialId' => $materialId
			);
			$existingWarehouse = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse');

			if (!empty($existingWarehouse)) {
				$currentQty = $existingWarehouse[0]['quantity'];
				$newQty = $currentQty - $piecesQuantity;

				if ($newQty < 0) {
					$this->session->set_flashdata('error', 'Cannot delete this record. Stock will go negative.');
					redirect('admin/warehouseInbounds');
					return;
				}

				$update_data = array(
					'quantity'  => $newQty,
					'updatedBy' => $userid,
					'date'      => date('Y-m-d')
				);
				$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse', $update_data);
			}

			$updateInbound = array(
				'deleted' => 'Y',
				'addedBy' => $userid,
				'date' => date('Y-m-d H:i:s')
			);
			$this->Comancontroler_model->update_table_by_id($id, 'warehouseInbounds', $updateInbound);

			// 4. Log the soft delete
			$log_data = array(
				'userId'     => $userid,
				'customerId' => $customerId,
				'materialId' => $materialId,
				'action'     => 'Deleted',
				'table'      => 'warehouseInbounds',
				'recordId'   => $id,
				'date'       => date('Y-m-d H:i:s')
			);
			$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');

			$this->session->set_flashdata('item', 'Inbound record deleted and stock updated.');
			redirect('admin/warehouseInbounds');
		}
		public function deleteInbounds() {
			$id = $this->uri->segment(4);
			$record = $this->Comancontroler_model->get_data_by_id($id, 'warehouse_bound_details');

			if (empty($record)) {
				$this->session->set_flashdata('error', 'Inbound record not found.');
				redirect('admin/warehouseInbounds');
				return;
			}

			$record = $record[0];
			$customerId     = $record['customer_id'];
			$materialId     = $record['material_id'];
			$piecesQuantity = $record['pieces_quantity'];
			$palletQuantity = $record['pallet_quantity'];
			$warehouseId    = $record['warehouse_id'];
			$sublocationId  = $record['sublocation_id'];
			$bound_id       = $record['bound_id'];

			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			$where = array(
				'customer_id'    => $customerId,
				'material_id'    => $materialId,
				'warehouse_id'   => $warehouseId,
				'sublocation_id' => $sublocationId
			);

			$this->db->trans_start();

			$existingStock = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse_stock');

			if (!empty($existingStock)) {
				$currentPieces = $existingStock[0]['total_pieces'];
				$currentPallets = $existingStock[0]['total_pallets'];

				$newPieces = $currentPieces - $piecesQuantity;
				$newPallets = $currentPallets - $palletQuantity;

				if ($newPieces < 0 || $newPallets < 0) {
					$this->session->set_flashdata('error', 'Cannot delete this record. Stock will go negative.');
					redirect('admin/warehouseInbounds');
					return;
				}

				$update_data = array(
					'total_pieces'  => $newPieces,
					'total_pallets' => $newPallets,
					'updated_by'    => $userid
				);

				$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse_stock', $update_data);
			}

			// 2. Soft delete the inbound detail
			$updateInbound = array(
				'deleted' => 'Y',
				'date'    => date('Y-m-d H:i:s')
			);
			$this->Comancontroler_model->update_table_by_id($id, 'warehouse_bound_details', $updateInbound);

			// 3. Recalculate parent totals in warehouse_bounds
			$this->db->select('SUM(pallet_quantity) AS total_pallets, SUM(pieces_quantity) AS total_pieces');
			$this->db->where('bound_id', $bound_id);
			$this->db->where('type', 'inbound');
			$this->db->where('deleted', 'N');
			$totals = $this->db->get('warehouse_bound_details')->row_array();

			$this->Comancontroler_model->update_table_by_id($bound_id, 'warehouse_bounds', array(
				'total_pallets' => $totals['total_pallets'],
				'total_pieces'  => $totals['total_pieces']
			));

			// 4. Log the soft delete
			$log_data = array(
				'userId'     => $userid,
				'customer_id' => $customerId,
				'material_id' => $materialId,
				'action'     => 'Deleted',
				'type'      => 'inbound',
				'detail_id'   => $id,
				'date'       => date('Y-m-d H:i:s')
			);
			$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');

			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'Failed to delete inbound. Operation rolled back.');
			} else {
				$this->db->trans_commit();
				$this->session->set_flashdata('item', 'Inbound record deleted and stock updated.');
			}

			redirect('admin/warehouseInbounds');
		}
		// public function importWarehouseInboundsFromCSV() {
		// 	$user = $this->session->userdata('logged');
		// 	$userid = $user['adminid'];

		// 	if ($_FILES['csvFile']['name']) {
		// 		$file = $_FILES['csvFile']['tmp_name'];
		// 		$handle = fopen($file, "r");

		// 		if (!$handle) {
		// 			$this->session->set_flashdata('error', 'Failed to open uploaded file.');
		// 			redirect(base_url('admin/warehouse/importInbounds'));
		// 			return;
		// 		}

		// 		$rowIndex = 0;
		// 		$headers = [];
		// 		$groupedNew = [];
		// 		$existingRows = [];

		// 		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		// 			if ($rowIndex == 0) {
		// 				$headers = $data;
		// 				$rowIndex++;
		// 				continue;
		// 			}

		// 			$row = array_combine($headers, $data);

		// 			$inbound_id = trim($row['inbound_id'] ?? '');
		// 			$bound_id = trim($row['bound_id'] ?? '');

		// 			if ($inbound_id && $bound_id) {
		// 				$existingRows[] = $row;
		// 			} else {
		// 				$key = trim($row['Date In']) . '|' . trim($row['Customer']) . '|' . trim($row['Warehouse']) . '|' . trim($row['Sublocation']);
		// 				$groupedNew[$key][] = $row;
		// 			}
		// 		}

		// 		fclose($handle);
		// 		$this->db->trans_start();

		// 		foreach ($groupedNew as $key => $entries) {
		// 			[$dateIn, $customerName, $warehouseName, $sublocationName] = explode('|', $key);

		// 			$customer_id    = $this->Comancontroler_model->get_id_by_column_value('companies', 'company', $customerName);
		// 			$warehouse_id   = $this->Comancontroler_model->get_id_by_column_value('warehouse', 'warehouse', $warehouseName);
		// 			$sublocation_id = $this->Comancontroler_model->get_id_by_column_value('warehouse_sublocations', 'name', $sublocationName);

		// 			if (!$customer_id || !$warehouse_id || !$sublocation_id) continue;

		// 			$total_pallets = 0;
		// 			$total_pieces = 0;

		// 			foreach ($entries as $entry) {
		// 				$total_pallets += (int)$entry['Pallet Quantity'];
		// 				$total_pieces  += (int)$entry['Pieces Quantity'];
		// 			}

		// 			$parent_data = [
		// 				'added_by' => $userid,
		// 				'customer_id' => $customer_id,
		// 				'warehouse_id' => $warehouse_id,
		// 				'sublocation_id' => $sublocation_id,
		// 				'total_pallets' => $total_pallets,
		// 				'total_pieces'  => $total_pieces,
		// 				'dated' => $dateIn,
		// 				'type' => 'inbound'
		// 			];

		// 			$this->db->insert('warehouse_bounds', $parent_data);
		// 			$bound_id = $this->db->insert_id();

		// 			foreach ($entries as $entry) {
		// 				$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', trim($entry['Material Number']));
		// 				if (!$material_id) continue;

		// 				$detail_data = [
		// 					'bound_id'        => $bound_id,
		// 					'type'            => 'inbound',
		// 					'customer_id'     => $customer_id,
		// 					'warehouse_id'    => $warehouse_id,
		// 					'sublocation_id'  => $sublocation_id,
		// 					'material_id'     => $material_id,
		// 					'lot_number'      => $entry['Lot Number'],
		// 					'pallet_number'   => $entry['Pallet Number'],
		// 					'pallet_position' => $entry['Pallet Position'],
		// 					'pallet_quantity' => $entry['Pallet Quantity'],
		// 					'pieces_quantity' => $entry['Pieces Quantity']
		// 				];

		// 				$this->db->insert('warehouse_bound_details', $detail_data);

		// 				$this->updateStock($customer_id, $warehouse_id, $sublocation_id, $material_id, $entry['Pallet Quantity'], $entry['Pieces Quantity'], $userid);
		// 			}
		// 		}

		// 		foreach ($existingRows as $entry) {
		// 			$inbound_id = trim($entry['inbound_id']);
		// 			$bound_id = trim($entry['bound_id']);
		// 			$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', trim($entry['Material Number']));
		// 			if (!$material_id) continue;

		// 			$newPallets = (int)$entry['Pallet Quantity'];
		// 			$newPieces = (int)$entry['Pieces Quantity'];

		// 			$existingDetail = $this->Comancontroler_model->get_data_by_id($inbound_id, 'warehouse_bound_details');

		// 			if ($existingDetail) {
		// 				$oldPallets = (int) $existingDetail[0]['pallet_quantity'];
		// 				$oldPieces  = (int) $existingDetail[0]['pieces_quantity'];
		// 				$customer_id = $existingDetail[0]['customer_id'];
		// 				$warehouse_id = $existingDetail[0]['warehouse_id'];
		// 				$sublocation_id = $existingDetail[0]['sublocation_id'];

		// 				$updateData = [
		// 					'lot_number'      => $entry['Lot Number'],
		// 					'pallet_number'   => $entry['Pallet Number'],
		// 					'pallet_position' => $entry['Pallet Position'],
		// 					'pallet_quantity' => $newPallets,
		// 					'pieces_quantity' => $newPieces
		// 				];
		// 				$this->Comancontroler_model->update_table_by_id($inbound_id, 'warehouse_bound_details', $updateData);

		// 				$this->db->select('SUM(pallet_quantity) AS total_pallets, SUM(pieces_quantity) AS total_pieces');
		// 				$this->db->where('bound_id', $bound_id);
		// 				$this->db->where('type', 'inbound');
		// 				$this->db->where('deleted', 'N');
		// 				$sums = $this->db->get('warehouse_bound_details')->row_array();

		// 				$this->Comancontroler_model->update_table_by_id($bound_id, 'warehouse_bounds', [
		// 					'total_pallets' => $sums['total_pallets'],
		// 					'total_pieces'  => $sums['total_pieces']
		// 				]);

		// 				$diffPallets = $newPallets - $oldPallets;
		// 				$diffPieces  = $newPieces - $oldPieces;

		// 				$stockWhere = [
		// 					'warehouse_id'   => $warehouse_id,
		// 					'sublocation_id' => $sublocation_id,
		// 					'customer_id'    => $customer_id,
		// 					'material_id'    => $material_id
		// 				];

		// 				$existingStock = $this->Comancontroler_model->get_data_by_multiple_column($stockWhere, 'warehouse_stock');

		// 				if (!empty($existingStock)) {
		// 					$updatedStock = [
		// 						'total_pallets' => max(0, $existingStock[0]['total_pallets'] + $diffPallets),
		// 						'total_pieces'  => max(0, $existingStock[0]['total_pieces'] + $diffPieces),
		// 						'updated_by'    => $userid
		// 					];
		// 					$this->Comancontroler_model->update_table_by_multiple_column($stockWhere, 'warehouse_stock', $updatedStock);
		// 				}
		// 			}
		// 		}

		// 		$this->db->trans_complete();

		// 		if ($this->db->trans_status() === FALSE) {
		// 			$this->session->set_flashdata('error', 'CSV import failed.');
		// 		} else {
		// 			$this->session->set_flashdata('item', 'CSV imported successfully.');
		// 		}

		// 		redirect(base_url('admin/warehouse/importInbounds'));
		// 	}
		// }

		private function updateStock($customer_id, $warehouse_id, $sublocation_id, $material_id, $palletQty, $piecesQty, $userid) {
			$where = [
				'warehouse_id' => $warehouse_id,
				'sublocation_id' => $sublocation_id,
				'customer_id' => $customer_id,
				'material_id' => $material_id
			];

			$existing = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse_stock');

			$palletQty = (int)$palletQty;
   			$piecesQty = (int)$piecesQty;

			if ($existing) {
				$existingPallets = (int)$existing[0]['total_pallets'];
        		$existingPieces = (int)$existing[0]['total_pieces'];
				$update = [
					'total_pallets' => $existingPallets + $palletQty,
            		'total_pieces'  => $existingPieces + $piecesQty,
					'updated_by' => $userid
				];
				$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse_stock', $update);
			} else {
				$data = $where + [
					'total_pallets' => $palletQty,
					'total_pieces' => $piecesQty,
					'updated_by' => $userid
				];
				$this->Comancontroler_model->add_data_in_table($data, 'warehouse_stock');
			}
		}
		public function uploadInbounds()
		{
			if (!checkPermission($this->session->userdata('permission'), 'odispatch')) {
				redirect(base_url('AdminDashboard'));
			}
			$data['error'] = [];
			$data['upload'] = '';
			$data['skipped_rows'] = [];
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			if (isset($_GET['dummy']) && $_GET['dummy'] == 'csv') {
				$data = [
					['inbound_id', 'bound_id', 'Date In', 'Customer', 'Warehouse', 'Sublocation', 'Material Number', 'Lot Number', 'Pallet Number', 'Pallet Position', 'Pallet Quantity', 'Pieces Quantity'],
					['', '', '6/30/2025', 'Customer A', 'Warehouse A', 'Sublocation A', 'Material-001', 'Lot A', 'Pallet A', 'Pallet Position A', '1', '50'],
					['', '', '6/30/2025', 'Customer B', 'Warehouse B', 'Sublocation B', 'Material-002', 'Lot B', 'Pallet B', 'Pallet Position B', '1', '30']
				];

				$fileName = "WarehouseInbounds_Sample.csv";
				$file = fopen($fileName, 'w');
				foreach ($data as $row) {
					fputcsv($file, $row);
				}
				fclose($file);

				header('Content-Type: application/csv');
				header("Content-Disposition: attachment; filename=\"$fileName\"");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fileName));
				readfile($fileName);
				exit;
			}

			if ($this->input->post('uploadcsv')) {
				$this->form_validation->set_rules('csvfile1', 'CSV file', 'required');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

				if ($this->form_validation->run() !== FALSE) {
					if ($_FILES['csvfile']['name']) {
						$file = $_FILES['csvfile']['tmp_name'];
						$handle = fopen($file, "r");

						if (!$handle) {
							$data['error'][] = "Failed to open uploaded file.";
						} else {
							$rowIndex = 0;
							$headers = [];
							$groupedNew = [];
							$existingRows = [];
							$errors = [];

							while (($dataRow = fgetcsv($handle, 1000, ",")) !== FALSE) {
								if ($rowIndex == 0) {
									$headers = $dataRow;
									
									$normalizedHeaders = array_map('strtolower', $headers);
									if (in_array('outbound_id', $normalizedHeaders) && in_array('date out', $normalizedHeaders)) {
										$this->session->set_flashdata('error', 'You are trying to upload an Outbound file in the Inbound importer.');
										redirect(base_url('admin/warehouse/uploadOutbounds'));
										return;
									}
									$rowIndex++;
									continue;
								}

								$row = array_combine($headers, $dataRow);
								$rowNumber = $rowIndex + 1;

								$material = trim($row['Material Number']);
								$customer = trim($row['Customer']);
								$warehouse = trim($row['Warehouse']);
								$sublocation = trim($row['Sublocation']);
								$palletQty = is_numeric(trim($row['Pallet Quantity'])) ? trim($row['Pallet Quantity']) : 0;
								$piecesQty = trim($row['Pieces Quantity']);
								$dateInRaw = str_replace('-', '/', trim($row['Date In']));
								$dateInObj = DateTime::createFromFormat('n/j/Y', $dateInRaw);
								$dateIn = $dateInObj ? $dateInObj->format('Y-m-d') : null;
								// echo $dateIn;exit;
								if (!$material || !$customer || !$warehouse || !$sublocation || !$dateIn) {
									$errors[] = "Row #$rowNumber | Missing required fields.";
									$rowIndex++;
									continue;
								}

								if (!is_numeric($piecesQty)) {
									$errors[] = "Row #$rowNumber | Material: $material — Pieces must be numeric.";
									$rowIndex++;
									continue;
								}

								$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', $material, true);
								if (!$material_id) {
									$errors[] = "Row #$rowNumber | Material: $material — Material not found.";
									$rowIndex++;
									continue;
								}

								$row['rowNumber'] = $rowNumber;
								$inbound_id = trim($row['inbound_id'] ?? '');
								$bound_id = trim($row['bound_id'] ?? '');

								if ($inbound_id && $bound_id) {
									$existingRows[] = $row;
								} else {
									$key = $dateIn . '|' . $customer . '|' . $warehouse . '|' . $sublocation;
									$groupedNew[$key][] = $row;
								}

								$rowIndex++;
							}

							fclose($handle);

							if (!empty($errors)) {
								$data['error'] = $errors;
							} else {
								$this->db->trans_start();
								foreach ($groupedNew as $key => $entries) {
									[$dateIn, $customerName, $warehouseName, $sublocationName] = explode('|', $key);

									$customer_id = $this->Comancontroler_model->get_id_by_column_value('companies', 'company', $customerName);
									$warehouse_id = $this->Comancontroler_model->get_id_by_column_value('warehouse', 'warehouse', $warehouseName);
									$sublocation_id = $this->Comancontroler_model->get_id_by_column_value('warehouse_sublocations', 'name', $sublocationName);

									$total_pallets = array_sum(array_column($entries, 'Pallet Quantity'));
									$total_pieces = array_sum(array_column($entries, 'Pieces Quantity'));

									$parent_data = [
										'added_by' => $userid,
										'customer_id' => $customer_id,
										'warehouse_id' => $warehouse_id,
										'sublocation_id' => $sublocation_id,
										'total_pallets' => $total_pallets,
										'total_pieces' => $total_pieces,
										'dated' => $dateIn,
										'type' => 'inbound'
									];

									$this->db->insert('warehouse_bounds', $parent_data);
									$bound_id = $this->db->insert_id();

									foreach ($entries as $entry) {
										$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', trim($entry['Material Number']));
										if (!$material_id) continue;

										$detail_data = [
											'bound_id' => $bound_id,
											'type' => 'inbound',
											'customer_id' => $customer_id,
											'warehouse_id' => $warehouse_id,
											'sublocation_id' => $sublocation_id,
											'material_id' => $material_id,
											'lot_number' => $entry['Lot Number'],
											'pallet_number' => $entry['Pallet Number'],
											'pallet_position' => $entry['Pallet Position'],
											'pallet_quantity' => $entry['Pallet Quantity'],
											'pieces_quantity' => $entry['Pieces Quantity']
										];

										$this->db->insert('warehouse_bound_details', $detail_data);

										$this->updateStock($customer_id, $warehouse_id, $sublocation_id, $material_id, $entry['Pallet Quantity'], $entry['Pieces Quantity'], $userid);
									}
								}

								foreach ($existingRows as $entry) {
									$rowNumber = $entry['rowNumber'];
									$inbound_id = trim($entry['inbound_id']);
									$bound_id = trim($entry['bound_id']);

									$existingDetail = $this->Comancontroler_model->get_data_by_id($inbound_id, 'warehouse_bound_details');
									if (!$existingDetail) continue;

									$oldPallets = (int)$existingDetail[0]['pallet_quantity'];
									$oldPieces = (int)$existingDetail[0]['pieces_quantity'];
									$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', trim($entry['Material Number']));

									$diffPallets = (int) ($entry['Pallet Quantity'] ?? 0) - $oldPallets;
									$diffPieces = $entry['Pieces Quantity'] - $oldPieces;

									$stockWhere = [
										'warehouse_id' => $existingDetail[0]['warehouse_id'],
										'sublocation_id' => $existingDetail[0]['sublocation_id'],
										'customer_id' => $existingDetail[0]['customer_id'],
										'material_id' => $material_id
									];

									$existingStock = $this->Comancontroler_model->get_data_by_multiple_column($stockWhere, 'warehouse_stock');
									$availablePallets = $existingStock[0]['total_pallets'] ?? 0;
									$availablePieces = $existingStock[0]['total_pieces'] ?? 0;

									if (($availablePallets + $diffPallets) < 0 || ($availablePieces + $diffPieces) < 0) {
										$data['skipped_rows'][] = "Row #$rowNumber | Material: {$entry['Material Number']} — Update would cause negative stock.";
										continue;
									}

									$updateData = [
										'lot_number' => $entry['Lot Number'],
										'pallet_number' => $entry['Pallet Number'],
										'pallet_position' => $entry['Pallet Position'],
										'pallet_quantity' => $entry['Pallet Quantity'],
										'pieces_quantity' => $entry['Pieces Quantity']
									];

									$this->Comancontroler_model->update_table_by_id($inbound_id, 'warehouse_bound_details', $updateData);

									$this->db->select('SUM(pallet_quantity) AS total_pallets, SUM(pieces_quantity) AS total_pieces');
									$this->db->where('bound_id', $bound_id);
									$this->db->where('type', 'inbound');
									$this->db->where('deleted', 'N');
									$sums = $this->db->get('warehouse_bound_details')->row_array();

									$this->Comancontroler_model->update_table_by_id($bound_id, 'warehouse_bounds', [
										'total_pallets' => $sums['total_pallets'],
										'total_pieces' => $sums['total_pieces']
									]);

									$updatedStock = [
										'total_pallets' => $availablePallets + $diffPallets,
										'total_pieces' => $availablePieces + $diffPieces,
										'updated_by' => $userid
									];
									$this->Comancontroler_model->update_table_by_multiple_column($stockWhere, 'warehouse_stock', $updatedStock);
								}

								$this->db->trans_complete();

								if ($this->db->trans_status() === FALSE) {
									$data['error'][] = 'Transaction failed. Please check data.';
								} else {
									$this->session->set_flashdata('item', 'CSV imported successfully.');
								}
							}
						}
					}
				}
			}

			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/upload_warehouse_inbounds', $data);
			$this->load->view('admin/layout/footer');
		}
		function uploadInbounds_bakup(){
			if(!checkPermission($this->session->userdata('permission'),'odispatch')){
				redirect(base_url('AdminDashboard'));   
			}
			// echo 'test';exit;
			$data['error'] = array();
			$data['upload'] = '';
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			if(isset($_GET['dummy']) && $_GET['dummy']=='csv'){
				$data = array(
					array('inbound_id', 'bound_id', 'Date In','Customer', 'Warehouse', 'Sublocation', 'Material Number','Lot Number','Pallet Number', 'Pallet Position', 'Pallet Quantity', 'Pieces Quantity'),
					array('','', '6/30/2025','Customer A', 'Warehhouse A', 'Sublocation A', 'Material-001', 'Lot A', 'Pallet A', 'Pallet Position A', '1', '50'),
					array('', '', '6/30/2025','Customer B', 'Warehouse B', 'Sublocation B', 'Material-002', 'Lot B', 'Pallet B', 'Pallet Positon B', '1', '30')
				);
				$fileName = "WarehouseInbounds_Sample.csv";   
				$file = fopen($fileName, 'w');
				foreach ($data as $row) {
					fputcsv($file, $row);
				}
				fclose($file); 
				header('Content-Type: application/csv');
				header("Content-Disposition: attachment; filename=\"$fileName\"");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fileName));
				readfile($fileName);
					
				die();    
			} 
			if($this->input->post('uploadcsv'))	{ 
				$this->form_validation->set_rules('csvfile1', 'csv file','required');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{ 
					$user = $this->session->userdata('logged');
					$userid = $user['adminid'];

					if ($_FILES['csvfile']['name']) {
						$file = $_FILES['csvfile']['tmp_name'];
						$handle = fopen($file, "r");

						if (!$handle) {
							$this->session->set_flashdata('error', 'Failed to open uploaded file.');
							redirect(base_url('admin/warehouse/uploadInbounds'));
							return;
						}

						$rowIndex = 0;
						$headers = [];
						$groupedNew = [];
						$existingRows = [];

						while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
							if ($rowIndex == 0) {
								$headers = $data;
								$rowIndex++;
								continue;
							}

							$row = array_combine($headers, $data);

							$inbound_id = trim($row['inbound_id'] ?? '');
							$bound_id = trim($row['bound_id'] ?? '');
							$dateInRaw   = str_replace('-', '/', trim($row['Date In']));
							$dateInObj = DateTime::createFromFormat('n/j/Y', $dateInRaw);
							$dateIn  = $dateInObj ? $dateInObj->format('Y-m-d') : null;

							if ($inbound_id && $bound_id) {
								$existingRows[] = $row;
							} else {
								$key = trim($dateIn) . '|' . trim($row['Customer']) . '|' . trim($row['Warehouse']) . '|' . trim($row['Sublocation']);
								$groupedNew[$key][] = $row;
							}
						}

						fclose($handle);
						$this->db->trans_start();


						// ✅ 1. INSERT new grouped inbounds
						foreach ($groupedNew as $key => $entries) {
							[$dateIn, $customerName, $warehouseName, $sublocationName] = explode('|', $key);

							$customer_id    = $this->Comancontroler_model->get_id_by_column_value('companies', 'company', $customerName);
							$warehouse_id   = $this->Comancontroler_model->get_id_by_column_value('warehouse', 'warehouse', $warehouseName);
							$sublocation_id = $this->Comancontroler_model->get_id_by_column_value('warehouse_sublocations', 'name', $sublocationName);

							if (!$customer_id || !$warehouse_id || !$sublocation_id) continue;

							$total_pallets = 0;
							$total_pieces = 0;

							foreach ($entries as $entry) {
								$total_pallets += (int)$entry['Pallet Quantity'];
								$total_pieces  += (int)$entry['Pieces Quantity'];
							}

							$parent_data = [
								'added_by' => $userid,
								'customer_id' => $customer_id,
								'warehouse_id' => $warehouse_id,
								'sublocation_id' => $sublocation_id,
								'total_pallets' => $total_pallets,
								'total_pieces'  => $total_pieces,
								'dated' => $dateIn,
								'type' => 'inbound'
							];

							$this->db->insert('warehouse_bounds', $parent_data);
							$bound_id = $this->db->insert_id();

							foreach ($entries as $entry) {
								$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', trim($entry['Material Number']));
								if (!$material_id) continue;

								$detail_data = [
									'bound_id'        => $bound_id,
									'type'            => 'inbound',
									'customer_id'     => $customer_id,
									'warehouse_id'    => $warehouse_id,
									'sublocation_id'  => $sublocation_id,
									'material_id'     => $material_id,
									'lot_number'      => $entry['Lot Number'],
									'pallet_number'   => $entry['Pallet Number'],
									'pallet_position' => $entry['Pallet Position'],
									'pallet_quantity' => $entry['Pallet Quantity'],
									'pieces_quantity' => $entry['Pieces Quantity']
								];

								$this->db->insert('warehouse_bound_details', $detail_data);

								$this->updateStock($customer_id, $warehouse_id, $sublocation_id, $material_id, $entry['Pallet Quantity'], $entry['Pieces Quantity'], $userid);
							}
						}
						// ✅ 2. UPDATE existing records
						foreach ($existingRows as $entry) {
							$inbound_id = trim($entry['inbound_id']);
							$bound_id = trim($entry['bound_id']);
							$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', trim($entry['Material Number']));
							if (!$material_id) continue;

							$newPallets = (int)$entry['Pallet Quantity'];
							$newPieces = (int)$entry['Pieces Quantity'];

							$existingDetail = $this->Comancontroler_model->get_data_by_id($inbound_id, 'warehouse_bound_details');

							if ($existingDetail) {
								$oldPallets = (int) $existingDetail[0]['pallet_quantity'];
								$oldPieces  = (int) $existingDetail[0]['pieces_quantity'];
								$customer_id = $existingDetail[0]['customer_id'];
								$warehouse_id = $existingDetail[0]['warehouse_id'];
								$sublocation_id = $existingDetail[0]['sublocation_id'];

								$updateData = [
									'lot_number'      => $entry['Lot Number'],
									'pallet_number'   => $entry['Pallet Number'],
									'pallet_position' => $entry['Pallet Position'],
									'pallet_quantity' => $newPallets,
									'pieces_quantity' => $newPieces
								];
								$this->Comancontroler_model->update_table_by_id($inbound_id, 'warehouse_bound_details', $updateData);

								// Recalculate warehouse_bounds totals
								$this->db->select('SUM(pallet_quantity) AS total_pallets, SUM(pieces_quantity) AS total_pieces');
								$this->db->where('bound_id', $bound_id);
								$this->db->where('type', 'inbound');
								$this->db->where('deleted', 'N');
								$sums = $this->db->get('warehouse_bound_details')->row_array();

								$this->Comancontroler_model->update_table_by_id($bound_id, 'warehouse_bounds', [
									'total_pallets' => $sums['total_pallets'],
									'total_pieces'  => $sums['total_pieces']
								]);

								// Update stock difference
								$diffPallets = $newPallets - $oldPallets;
								$diffPieces  = $newPieces - $oldPieces;

								$stockWhere = [
									'warehouse_id'   => $warehouse_id,
									'sublocation_id' => $sublocation_id,
									'customer_id'    => $customer_id,
									'material_id'    => $material_id
								];

								$existingStock = $this->Comancontroler_model->get_data_by_multiple_column($stockWhere, 'warehouse_stock');

								if (!empty($existingStock)) {
									$updatedStock = [
										'total_pallets' => max(0, $existingStock[0]['total_pallets'] + $diffPallets),
										'total_pieces'  => max(0, $existingStock[0]['total_pieces'] + $diffPieces),
										'updated_by'    => $userid
									];
									$this->Comancontroler_model->update_table_by_multiple_column($stockWhere, 'warehouse_stock', $updatedStock);
								}
							}
						}

						$this->db->trans_complete();

						if ($this->db->trans_status() === FALSE) {
							$this->session->set_flashdata('error', 'CSV import failed.');
							$data['upload'] = 'done';
						} else {
							$this->session->set_flashdata('item', 'CSV imported successfully.');
						}
						// redirect(base_url('admin/warehouse/uploadInbounds'));
					}
				}
			}
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/upload_warehouse_inbounds',$data);
			$this->load->view('admin/layout/footer');
		}
		public function editInbound()
		{
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			$bound_id      = $this->input->get('bound_id');
			$dated         = $this->input->get('dated');
			$customerId    = $this->input->get('customerId');
			$warehouseId   = $this->input->get('warehouseId');
			$sublocationId = $this->input->get('sublocationId');
			$detailIdsStr  = $this->input->get('detailIds');
			$detailIds     = !empty($detailIdsStr) ? json_decode($detailIdsStr, true) : [];

			$data['bound_id']        = $bound_id;
			$data['dated']           = $dated;
			$data['customer_id']     = $customerId;
			$data['warehouse_id']    = $warehouseId;
			$data['sublocation_id']  = $sublocationId;
			$data['detail_ids']      = $detailIds;
			$data['materialDetails'] = $this->Warehouse_model->getMaterialDetailsForTransfer($detailIds);
			$data['companies']       = $this->Comancontroler_model->get_data_by_column('paymenTerms !=', 'Deleted', 'companies', '*', 'company', 'asc', '', 'Yes');
			$data['warehouse_address']     = $this->Comancontroler_model->get_data_by_table('warehouse');
			$data['warehouse_sublocations'] = $this->Comancontroler_model->get_data_by_table('warehouse_sublocations');

			if ($this->input->post('save')) {

				$inboundId       = $this->input->post('inbound_id');
				$customerId      = $this->input->post('customerId');
				$warehouseId     = $this->input->post('warehouse_id');
				$sublocationId   = $this->input->post('sublocationId');
				$dateIn          = $this->input->post('dated');
				$detailIds       = $this->input->post('detail_id');
				$materialIds     = $this->input->post('materialId');
				$lotNumbers      = $this->input->post('lotNumber');
				$palletNumbers   = $this->input->post('palletNumber');
				$palletPositions = $this->input->post('palletPosition');
				$palletQuantities = $this->input->post('palletQuantity');
				$piecesQuantities = $this->input->post('piecesQuantity');
				$notes           = $this->input->post('notes');
				$userid          = $this->session->userdata('user_id');

				$this->db->trans_start();

				/** ================== MAIN INBOUND FILE UPLOAD ================== **/
				$inboundFiles = '';
				if (!empty($_FILES['inboundFile']['name'][0])) {
					$oldParent = $this->db->select('file')->where('id', $inboundId)->get('warehouse_bounds')->row_array();
					if (!empty($oldParent['file'])) {
						$oldFiles = explode(',', $oldParent['file']);
						foreach ($oldFiles as $oldFile) {
							$path = FCPATH . 'assets/warehouse-inbounds/inbound-files/' . $oldFile;
							if (is_file($path)) {
								unlink($path);
							}
						}
					}

					$uploadedFiles = [];
					$filesCount = count($_FILES['inboundFile']['name']);
					$config['upload_path']   = 'assets/warehouse-inbounds/inbound-files/';
					$config['allowed_types'] = '*';
					$this->load->library('upload');

					for ($i = 0; $i < $filesCount; $i++) {
						$_FILES['tempFile']['name']     = $_FILES['inboundFile']['name'][$i];
						$_FILES['tempFile']['type']     = $_FILES['inboundFile']['type'][$i];
						$_FILES['tempFile']['tmp_name'] = $_FILES['inboundFile']['tmp_name'][$i];
						$_FILES['tempFile']['error']    = $_FILES['inboundFile']['error'][$i];
						$_FILES['tempFile']['size']     = $_FILES['inboundFile']['size'][$i];

						$this->upload->initialize($config);
						if ($this->upload->do_upload('tempFile')) {
							$fileData = $this->upload->data();
							$unique_name = pathinfo($fileData['orig_name'], PATHINFO_FILENAME) . '_' . time() . $fileData['file_ext'];
							rename($fileData['full_path'], $fileData['file_path'] . $unique_name);
							$uploadedFiles[] = $unique_name;
						}
					}
					$inboundFiles = implode(',', $uploadedFiles);
				}

				$parentData = [
					'customer_id'    => $customerId,
					'warehouse_id'   => $warehouseId,
					'sublocation_id' => $sublocationId,
					'dated'          => $dateIn,
					'added_by'     => $userid
				];
				if ($inboundFiles) {
					$parentData['file'] = $inboundFiles;
				}
				$this->db->where('id', $inboundId)->update('warehouse_bounds', $parentData);

				/** ================== MARK REMOVED DETAILS AS DELETED & UPDATE STOCK ================== **/
				$existingDetails = $this->db->select('*')
					->where('bound_id', $inboundId)
					->where('deleted', 'N')
					->get('warehouse_bound_details')->result_array();

				$postedIds = array_filter($detailIds);
				// print_r($postedIds);exit;
				// print_r($existingDetails);exit;
				foreach ($existingDetails as $oldRow) {
					if (!in_array($oldRow['id'], $postedIds)) {
						// Mark as deleted
						$this->db->where('id', $oldRow['id'])->update('warehouse_bound_details', ['deleted' => 'Y']);

						// Reduce stock
						$stock_where = [
							'warehouse_id'   => $oldRow['warehouse_id'],
							'sublocation_id' => $oldRow['sublocation_id'],
							'customer_id'    => $customerId,
							'material_id'    => $oldRow['material_id']
						];
						$existing_stock = $this->Comancontroler_model->get_data_by_multiple_column($stock_where, 'warehouse_stock');
						if (!empty($existing_stock)) {
							$updated_stock = [
								'total_pallets' => max(0, $existing_stock[0]['total_pallets'] - $oldRow['pallet_quantity']),
								'total_pieces'  => max(0, $existing_stock[0]['total_pieces'] - $oldRow['pieces_quantity']),
								'updated_by'    => $userid
							];
							$this->Comancontroler_model->update_table_by_multiple_column($stock_where, 'warehouse_stock', $updated_stock);
						}
					}
				}

				/** ================== LOOP MATERIALS ================== **/
				for ($i = 0; $i < count($materialIds); $i++) {
					$currentDetailId = isset($detailIds[$i]) && $detailIds[$i] !== '' ? $detailIds[$i] : null;

					/** ===== MATERIAL FILE UPLOAD ===== **/
					$inboundMaterialFile = '';
					if (!empty($_FILES['inboundMaterialFile']['name'][$i][0])) {
						if ($currentDetailId) {
							$oldDetail = $this->db->select('file')->where('id', $currentDetailId)->get('warehouse_bound_details')->row_array();
							if (!empty($oldDetail['file'])) {
								$oldFiles = explode(',', $oldDetail['file']);
								foreach ($oldFiles as $oldFile) {
									$path = FCPATH . 'assets/warehouse-inbounds/inbound-materials/' . $oldFile;
									if (is_file($path)) {
										unlink($path);
									}
								}
							}
						}
						
						$uploadedInboundMaterialFiles = [];
						$materialFilesCount = count($_FILES['inboundMaterialFile']['name'][$i]);
						$config['upload_path']   = 'assets/warehouse-inbounds/inbound-materials/';
						$config['allowed_types'] = '*';
						$this->load->library('upload');

						for ($j = 0; $j < $materialFilesCount; $j++) {
							$_FILES['tempFile']['name']     = $_FILES['inboundMaterialFile']['name'][$i][$j];
							$_FILES['tempFile']['type']     = $_FILES['inboundMaterialFile']['type'][$i][$j];
							$_FILES['tempFile']['tmp_name'] = $_FILES['inboundMaterialFile']['tmp_name'][$i][$j];
							$_FILES['tempFile']['error']    = $_FILES['inboundMaterialFile']['error'][$i][$j];
							$_FILES['tempFile']['size']     = $_FILES['inboundMaterialFile']['size'][$i][$j];

							$this->upload->initialize($config);
							if ($this->upload->do_upload('tempFile')) {
								$fileData = $this->upload->data();
								$unique_name = pathinfo($fileData['orig_name'], PATHINFO_FILENAME) . '_' . time() . $fileData['file_ext'];
								rename($fileData['full_path'], $fileData['file_path'] . $unique_name);
								$uploadedInboundMaterialFiles[] = $unique_name;
							}
						}
						$inboundMaterialFile = implode(',', $uploadedInboundMaterialFiles);
					}

					$detailData = [
						'bound_id'        => $inboundId,
						'type'            => 'inbound',
						'customer_id'     => $customerId,
						'warehouse_id'    => $warehouseId,
						'sublocation_id'  => $sublocationId,
						'material_id'     => $materialIds[$i],
						'lot_number'      => $lotNumbers[$i],
						'pallet_number'   => $palletNumbers[$i],
						'pallet_position' => $palletPositions[$i],
						'pallet_quantity' => $palletQuantities[$i],
						'pieces_quantity' => $piecesQuantities[$i],
						'notes'           => $notes[$i],
						'deleted'         => 'N'
					];
					if ($inboundMaterialFile) {
						$detailData['file'] = $inboundMaterialFile;
					}

					/** ===== UPDATE OR INSERT ===== **/
					if ($currentDetailId) {
						$old = $this->db->where('id', $currentDetailId)->get('warehouse_bound_details')->row_array();
						$this->db->where('id', $currentDetailId)->update('warehouse_bound_details', $detailData);

						// Adjust stock if warehouse/sublocation/material changed
						$oldStockWhere = [
							'warehouse_id'   => $old['warehouse_id'],
							'sublocation_id' => $old['sublocation_id'],
							'customer_id'    => $customerId,
							'material_id'    => $old['material_id']
						];
						$oldStock = $this->Comancontroler_model->get_data_by_multiple_column($oldStockWhere, 'warehouse_stock');
						if (!empty($oldStock)) {
							$this->Comancontroler_model->update_table_by_multiple_column($oldStockWhere, 'warehouse_stock', [
								'total_pallets' => max(0, $oldStock[0]['total_pallets'] - $old['pallet_quantity']),
								'total_pieces'  => max(0, $oldStock[0]['total_pieces'] - $old['pieces_quantity']),
								'updated_by'    => $userid
							]);
						}

						// Add to new stock
						$newStockWhere = [
							'warehouse_id'   => $warehouseId,
							'sublocation_id' => $sublocationId,
							'customer_id'    => $customerId,
							'material_id'    => $materialIds[$i]
						];
						$newStock = $this->Comancontroler_model->get_data_by_multiple_column($newStockWhere, 'warehouse_stock');
						if (!empty($newStock)) {
							$this->Comancontroler_model->update_table_by_multiple_column($newStockWhere, 'warehouse_stock', [
								'total_pallets' => $newStock[0]['total_pallets'] + $palletQuantities[$i],
								'total_pieces'  => $newStock[0]['total_pieces'] + $piecesQuantities[$i],
								'updated_by'    => $userid
							]);
						} else {
							$this->Comancontroler_model->add_data_in_table($newStockWhere + [
								'total_pallets' => $palletQuantities[$i],
								'total_pieces'  => $piecesQuantities[$i],
								'updated_by'    => $userid
							], 'warehouse_stock');
						}
					} else {
						$this->db->insert('warehouse_bound_details', $detailData);

						$stock_where = [
							'warehouse_id'   => $warehouseId,
							'sublocation_id' => $sublocationId,
							'customer_id'    => $customerId,
							'material_id'    => $materialIds[$i]
						];
						$existing_stock = $this->Comancontroler_model->get_data_by_multiple_column($stock_where, 'warehouse_stock');
						if (!empty($existing_stock)) {
							$this->Comancontroler_model->update_table_by_multiple_column($stock_where, 'warehouse_stock', [
								'total_pallets' => $existing_stock[0]['total_pallets'] + $palletQuantities[$i],
								'total_pieces'  => $existing_stock[0]['total_pieces'] + $piecesQuantities[$i],
								'updated_by'    => $userid
							]);
						} else {
							$this->Comancontroler_model->add_data_in_table($stock_where + [
								'total_pallets' => $palletQuantities[$i],
								'total_pieces'  => $piecesQuantities[$i],
								'updated_by'    => $userid
							], 'warehouse_stock');
						}
					}
				}

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					$this->session->set_flashdata('error', 'Inbound update failed.');
				} else {
					$this->session->set_flashdata('item', 'Inbound updated successfully.');
				}

				redirect(base_url('admin/warehouseInbounds'));
			}

			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_edit_inbound', $data);
			$this->load->view('admin/layout/footer');
		}
		/*******************Warehouse Outbounds ************************ */
		function outbounds() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			$company = $sdate = $edate = $materialId = $warehouse_id = $sublocationId = '';
        
			if($this->input->post('generateCSV')){
            	$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				$warehouse_id = $this->input->post('warehouse_id');
				$sublocationId = $this->input->post('sublocationId');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$warehouseInbounds = $this->Warehouse_model->downloadWarehouseBounds('outbound',$sdate,$edate,$company,$materialId,$warehouse_id,$sublocationId);

				$heading = array('outbound_id', 'bound_id', 'Date Out','Customer', 'Warehouse', 'Sublocation', 'Material Number','Lot Number','Pallet Number', 'Pallet Position', 'Pallet Quantity', 'Pieces Quantity');
				
				$data = array($heading);
			
				if(!empty($warehouseInbounds)) {
					foreach($warehouseInbounds as $row){
					$dataRow = array(
						$row['id'],$row['bound_id'],$row['dated'],$row['customer'],$row['warehouse'],$row['sublocation'],$row['material_number'],$row['lot_number'],$row['pallet_number'],$row['pallet_position'],$row['pallet_quantity'],$row['pieces_quantity']);
						$data[] = $dataRow;
					}
				}
            
			
			if($this->input->post('generateCSV')){
				$fileName = "warehouseOutbounds.csv";   
				$file = fopen($fileName, 'w');
				foreach ($data as $row) {
					fputcsv($file, $row);
				}
				
				fclose($file); 

				header('Content-Type: application/csv');
				header("Content-Disposition: attachment; filename=\"$fileName\"");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fileName));

				readfile($fileName);
			}

			unlink($fileName);
			exit;
			die('csv');
        	}

			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$warehouse_id = $this->input->post('warehouse_id');
				$sublocationId = $this->input->post('sublocationId');
				$data['warehouse'] = $this->Warehouse_model->outbounds($sdate,$edate,$company, $materialId,$warehouse_id,$sublocationId);
			} else {
				$data['warehouse'] = $this->Warehouse_model->outbounds($sdate,$edate,$company, $materialId,$warehouse_id,$sublocationId);
			}
	        $data['materials'] = $this->getMaterials();
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$data['warehouse_sublocations'] = $this->Comancontroler_model->get_data_by_table('warehouse_sublocations');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds',$data);
			$this->load->view('admin/layout/footer');
		}
		function addOutbounds_old() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			if($this->input->post('save'))	{
				$customerId = $this->input->post('customerId');
				$warehouseAddressId = $this->input->post('warehouseAddressId');
				$sublocationId = $this->input->post('sublocationId');
				$materialId = $this->input->post('materialId');
				$batch = $this->input->post('batch');	
				$lotNumber = $this->input->post('lotNumber');													
				$palletNumber = $this->input->post('palletNumber');
				$palletPosition = $this->input->post('palletPosition');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$dateOut = $this->input->post('dateOut');
				// echo count($materialId);exit;
				$allInserted = true;
				$this->db->trans_start();
				for ($i = 0; $i < count($materialId); $i++) {
					if (empty($materialId[$i])) continue;
					$insert_data = array(
						'dispatchedBy'        => $userid,
						'customerId'     => $customerId,
						'warehouseAddressId'=>$warehouseAddressId,
						'sublocationId'=>$sublocationId,
						'sublocationId'=>$sublocationId,
						'materialId'     => $materialId[$i],
						'lotNumber' => $lotNumber[$i],
						'palletNumber'   => $palletNumber[$i],
						'palletPosition'   => $palletPosition[$i],
						'palletQuantity' => $palletQuantity[$i],
						'piecesQuantity' => $piecesQuantity[$i],
						'dateOut'         => $dateOut
					);
					$res = $this->Comancontroler_model->add_data_in_table($insert_data, 'warehouseOutbounds');
					if ($res) {
						$where = array(
							'customerId' => $customerId,
							'materialId' => $materialId[$i]
						);
						$existingWarehouse = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse');
						if (!empty($existingWarehouse)) {
							$currentQty = $existingWarehouse[0]['quantity'];
							$newQty = $currentQty - $piecesQuantity[$i];
							$sql="SELECT materialNumber FROM warehouseMaterials WHERE id=$materialId[$i]";
							$materialNumber=$this->db->query($sql)->row()->materialNumber;
							if ($newQty < 0) {
								$this->Comancontroler_model->delete($res,'warehouseOutbounds','id');
								$this->session->set_flashdata('error', 'Not enough stock for material number: ' . $materialNumber);
								redirect(base_url('admin/warehouse/addOutbounds'));
								return;
							}

							$update_data = array(
								'quantity'  => $newQty,
								'updatedBy' => $userid,
								'date'      => date('Y-m-d')
							);
							$this->Comancontroler_model->update_table_by_multiple_column($where,'warehouse', $update_data);
							// echo $res; exit;
							$log_data = array(
								'userId' => $userid,
								'customerId' => $customerId,
								'materialId' => $materialId[$i],
								'action' => 'Inserted',
								'table' => 'warehouseOutbounds',
								'recordId' => $res
							);
							$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');
						} else {
							$this->session->set_flashdata('error', 'Material not found in warehouse stock.');
							redirect(base_url('admin/warehouse/addOutbounds'));
							return;
						}
					}else{
						$allInserted = false;
						break;
					}
				}
				$this->db->trans_complete();
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Addition failed. No data was inserted.');
				} else {
					$this->db->trans_commit();
					if ($allInserted) {
					$this->session->set_flashdata('item', 'Item(s) inserted successfully.');
					} else {
						$this->session->set_flashdata('error', 'Failed to insert all items.');
					}                   
				}
				redirect(base_url('admin/warehouse/addOutbounds'));
			}
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds_add',$data);
			$this->load->view('admin/layout/footer');
		}
		public function addOutbounds() {
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			if ($this->input->post('save')) {
				$customerId       = $this->input->post('customerId');
				$warehouseId      = $this->input->post('warehouse_id');
				$sublocationId    = $this->input->post('sublocationId');
				$materialId       = $this->input->post('materialId');
				$lotNumber        = $this->input->post('lotNumber');
				$palletNumber     = $this->input->post('palletNumber');
				$palletPosition   = $this->input->post('palletPosition');
				$palletQuantity   = $this->input->post('palletQuantity');
				$piecesQuantity   = $this->input->post('piecesQuantity');
				$dateOut          = $this->input->post('dateOut');

				$totalPallets = 0;
				$totalPieces  = 0;
				$allInserted  = true;

				$this->db->trans_start();
				$parentData = array(
					'added_by'        => $userid,
					'customer_id'     => $customerId,
					'warehouse_id'    => $warehouseId,
					'sublocation_id'  => $sublocationId,
					'dated'           => $dateOut,
					'type'            => 'outbound',
					'total_pallets'   => 0,
					'total_pieces'    => 0
				);
				$bound_id = $this->Comancontroler_model->add_data_in_table($parentData, 'warehouse_bounds');
				if (!$bound_id) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Failed to create outbound entry.');
					redirect(base_url('admin/warehouse/addOutbounds'));
					return;
				}
				for ($i = 0; $i < count($materialId); $i++) {
					if (empty($materialId[$i])) continue;

					$check = array(
						'warehouse_id'    => $warehouseId,
						'sublocation_id'  => $sublocationId,
						'customer_id'     => $customerId,
						'material_id'     => $materialId[$i]
					);
					$existingStock = $this->Comancontroler_model->get_data_by_multiple_column($check, 'warehouse_stock');

					if (!empty($existingStock)) {
						$currentStock = $existingStock[0];
						$newPieces  = $currentStock['total_pieces'] - $piecesQuantity[$i];
						$newPallets = $currentStock['total_pallets'] - $palletQuantity[$i];

						// Check stock limits
						if ($newPieces < 0 || $newPallets < 0) {
							$materialNumber = $this->db->query("SELECT materialNumber FROM warehouseMaterials WHERE id = " . $materialId[$i])->row()->materialNumber;
							$this->session->set_flashdata('error', 'Not enough stock for material number: ' . $materialNumber);
							redirect(base_url('admin/warehouse/addOutbounds'));
							return;
						}

						// Insert child row
						$detailData = array(
							'bound_id'       => $bound_id,
							'type'           => 'outbound',
							'customer_id'    => $customerId,
							'warehouse_id'   => $warehouseId,
							'sublocation_id' => $sublocationId,
							'material_id'    => $materialId[$i],
							'lot_number'     => $lotNumber[$i],
							'pallet_number'  => $palletNumber[$i],
							'pallet_position'=> $palletPosition[$i],
							'pallet_quantity'=> $palletQuantity[$i],
							'pieces_quantity'=> $piecesQuantity[$i]
						);
						$res = $this->Comancontroler_model->add_data_in_table($detailData, 'warehouse_bound_details');

						// Update stock
						$updateStock = array(
							'total_pieces'  => $newPieces,
							'total_pallets' => $newPallets,
							'updated_by'    => $userid
						);
						$this->Comancontroler_model->update_table_by_multiple_column($check, 'warehouse_stock', $updateStock);

						// Log it
						$log = array(
							'userId'     => $userid,
							'customer_id' => $customerId,
							'material_id' => $materialId[$i],
							'action'     => 'Inserted',
							'type'      => 'outbound',
							'detail_id'   => $res
						);
						$this->Comancontroler_model->add_data_in_table($log, 'warehouseLogs');

						$totalPallets += $palletQuantity[$i];
						$totalPieces  += $piecesQuantity[$i];
					} else {
						$this->session->set_flashdata('error', 'Material not found in warehouse stock.');
						redirect(base_url('admin/warehouse/addOutbounds'));
						return;
					}
				}

				// Update parent totals
				$this->Comancontroler_model->update_table_by_id($bound_id, 'warehouse_bounds', array(
					'total_pallets' => $totalPallets,
					'total_pieces'  => $totalPieces
				));

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Addition failed. No data was inserted.');
				} else {
					$this->db->trans_commit();
					if ($allInserted) {
						$this->session->set_flashdata('item', 'Item(s) inserted successfully.');
					} else {
						$this->session->set_flashdata('error', 'Some items failed to insert.');
					}
				}

				redirect(base_url('admin/warehouse/addOutbounds'));
			}

			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds_add', $data);
			$this->load->view('admin/layout/footer');
		}
		function updateOutbounds_old() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			$id = $this->uri->segment(4);
			if($this->input->post('save'))	{
				$customerId = $this->input->post('customerId');
				$warehouseAddressId = $this->input->post('warehouseAddressId');
				$sublocationId = $this->input->post('sublocationId');
				$materialId = $this->input->post('materialId');
				$batch = $this->input->post('batch');	
				$lotNumber = $this->input->post('lotNumber');				
				$palletNumber = $this->input->post('palletNumber');
				$palletPosition = $this->input->post('palletPosition');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$dateOut = $this->input->post('dateOut');
				$insert_data=array(
					'dispatchedBy'=>$userid,
					'customerId'=>$customerId,
					'warehouseAddressId'=>$warehouseAddressId,
					'sublocationId'=>$sublocationId,
					'materialId'=>$materialId,
					'lotNumber'=>$lotNumber,
					'palletNumber'=>$palletNumber,
					'palletPosition'   => $palletPosition,
					'palletQuantity'=>$palletQuantity,
					'piecesQuantity'=>$piecesQuantity,
					'dateOut'=>$dateOut
				);
				$existingOutbound = $this->Comancontroler_model->get_data_by_id($id, 'warehouseOutbounds');
				$oldQty = $existingOutbound[0]['piecesQuantity'];

				$where = array(
					'customerId' => $customerId,
					'materialId' => $materialId
				);
				$existingWarehouse = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse');

				if (!empty($existingWarehouse)) {
					$currentQty = $existingWarehouse[0]['quantity'];
					$newQty = $currentQty + $oldQty - $piecesQuantity;
					$sql="SELECT materialNumber FROM warehouseMaterials WHERE id=$materialId";
					$materialNumber=$this->db->query($sql)->row()->materialNumber;
					if ($newQty < 0) {
						$this->session->set_flashdata('error', 'Error: Not enough stock for the material number: '.$materialNumber);
						redirect(base_url('admin/warehouse/updateOutbounds/'.$id));
						return;
					}
					$res = $this->Comancontroler_model->update_table_by_id($id,'warehouseOutbounds',$insert_data); 
					if ($res) {
						$update_data = array(
							'quantity'  => $newQty,
							'updatedBy' => $userid,
							'date'      => date('Y-m-d')
						);
						$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse', $update_data);
						$log_data = array(
							'userId' => $userid,
							'customerId' => $customerId,
							'materialId' => $materialId,
							'action' => 'Updated',
							'table' => 'warehouseOutbounds',
							'recordId' => $id
						);
						$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');
						$this->session->set_flashdata('item', 'Item updated successfully.');
						redirect(base_url('admin/warehouse/updateOutbounds/'.$id));
					}
				} else {
					$this->session->set_flashdata('error', 'Warehouse record not found for this material.');
					redirect(base_url('admin/warehouse/updateOutbounds/'.$id));
				}
			}
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($id,'warehouseOutbounds');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds_update',$data);
			$this->load->view('admin/layout/footer');
		}
		public function updateOutbounds() {
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			$id = $this->uri->segment(4); 
			$dated = $this->input->get('dated');

			if ($this->input->post('save')) {
				$customerId     = $this->input->post('customerId');
				$warehouseId    = $this->input->post('warehouse_id');
				$sublocationId  = $this->input->post('sublocationId');
				$materialId     = $this->input->post('materialId');
				$lotNumber      = $this->input->post('lotNumber');
				$palletNumber   = $this->input->post('palletNumber');
				$palletPosition = $this->input->post('palletPosition');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$dateOut        = $this->input->post('dateOut');

				$existingDetail = $this->Comancontroler_model->get_data_by_id($id, 'warehouse_bound_details');
				if (empty($existingDetail)) {
					$this->session->set_flashdata('error', 'Outbound record not found.');
					redirect('admin/warehouseOutbounds');
					return;
				}

				$oldDetail   = $existingDetail[0];
				$oldQty      = $oldDetail['pieces_quantity'];
				$oldPallets  = $oldDetail['pallet_quantity'];
				$boundId     = $oldDetail['bound_id'];

				$this->db->trans_start();

				// Restore stock to old material (if stock exists)
				$oldStockWhere = array(
					'warehouse_id'   => $oldDetail['warehouse_id'],
					'sublocation_id' => $oldDetail['sublocation_id'],
					'customer_id'    => $oldDetail['customer_id'],
					'material_id'    => $oldDetail['material_id']
				);

				$oldStock = $this->Comancontroler_model->get_data_by_multiple_column($oldStockWhere, 'warehouse_stock');
				if (!empty($oldStock)) {
					$this->Comancontroler_model->update_table_by_multiple_column($oldStockWhere, 'warehouse_stock', array(
						'total_pieces'  => $oldStock[0]['total_pieces'] + $oldQty,
						'total_pallets' => $oldStock[0]['total_pallets'] + $oldPallets,
						'updated_by'    => $userid
					));
				}

				// Validate and subtract stock from new material
				$newStockWhere = array(
					'warehouse_id'   => $warehouseId,
					'sublocation_id' => $sublocationId,
					'customer_id'    => $customerId,
					'material_id'    => $materialId
				);

				$newStock = $this->Comancontroler_model->get_data_by_multiple_column($newStockWhere, 'warehouse_stock');
				if (empty($newStock)) {
					$this->session->set_flashdata('error', 'Warehouse stock not found for the selected material.');
					redirect(base_url('admin/warehouse/updateOutbounds/' . $id));
					return;
				}

				$stock = $newStock[0];
				$newPieces  = $stock['total_pieces'] - $piecesQuantity;
				$newPallets = $stock['total_pallets'] - $palletQuantity;

				if ($newPieces < 0 || $newPallets < 0) {
					$materialNumber = $this->db->select('materialNumber')->get_where('warehouseMaterials', ['id' => $materialId])->row()->materialNumber;
					$this->session->set_flashdata('error', 'Error: Not enough stock for material number: ' . $materialNumber);
					redirect(base_url('admin/warehouse/updateOutbounds/' . $id));
					return;
				}

				$this->Comancontroler_model->update_table_by_multiple_column($newStockWhere, 'warehouse_stock', array(
					'total_pieces'  => $newPieces,
					'total_pallets' => $newPallets,
					'updated_by'    => $userid
				));

				// Update outbound detail
				$updateData = array(
					'customer_id'     => $customerId,
					'warehouse_id'    => $warehouseId,
					'sublocation_id'  => $sublocationId,
					'material_id'     => $materialId,
					'lot_number'      => $lotNumber,
					'pallet_number'   => $palletNumber,
					'pallet_position' => $palletPosition,
					'pallet_quantity' => $palletQuantity,
					'pieces_quantity' => $piecesQuantity
				);
				$this->Comancontroler_model->update_table_by_id($id, 'warehouse_bound_details', $updateData);

				// Step 5: Log
				$this->Comancontroler_model->add_data_in_table(array(
					'userId'      => $userid,
					'customer_id' => $customerId,
					'material_id' => $materialId,
					'action'      => 'Updated',
					'type'        => 'outbound',
					'detail_id'   => $id
				), 'warehouseLogs');

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					$this->session->set_flashdata('error', 'Outbound update failed.');
				} else {
					$this->session->set_flashdata('item', 'Outbound updated successfully.');
				}

				redirect(base_url('admin/warehouse/updateOutbounds/' . $id));
			}

			// Load view
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($id, 'warehouse_bound_details');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$data['dated'] = $dated;
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds_update', $data);
			$this->load->view('admin/layout/footer');
		}
		public function deleteOutbounds_old() {
			$id = $this->uri->segment(4);
			$record = $this->Comancontroler_model->get_data_by_id($id, 'warehouseOutbounds');

			if (empty($record)) {
				$this->session->set_flashdata('error', 'Record not found.');
				redirect('admin/warehouseOutbounds');
				return;
			}

			$record = $record[0];
			$customerId = $record['customerId'];
			$materialId = $record['materialId'];
			$piecesQuantity = $record['piecesQuantity'];

			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
		
			$where = array(
				'customerId' => $customerId,
				'materialId' => $materialId
			);
			$existingWarehouse = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse');

			if (!empty($existingWarehouse)) {
				$currentQty = $existingWarehouse[0]['quantity'];
				$newQty = $currentQty + $piecesQuantity;
				$update_data = array(
					'quantity'  => $newQty,
					'updatedBy' => $userid,
					'date'      => date('Y-m-d')
				);
				$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse', $update_data);
			}			
			$updateInbound = array(
				'deleted' => 'Y',
				'dispatchedBy' => $userid,
				'date' => date('Y-m-d H:i:s')
			);
			$this->Comancontroler_model->update_table_by_id($id, 'warehouseOutbounds', $updateInbound);	

			$log_data = array(
					'userId'     => $userid,
					'customerId' => $customerId,
					'materialId' => $materialId,
					'action'     => 'Deleted',
					'table'      => 'warehouseOutbounds',
					'recordId'   => $id,
					'date'       => date('Y-m-d H:i:s')
				);
				$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');
				$this->session->set_flashdata('item', 'Outbound record deleted and stock updated.');
			
			redirect('admin/warehouseOutbounds');
		}
		public function deleteOutbounds() {
			$id = $this->uri->segment(4); 
			$record = $this->Comancontroler_model->get_data_by_id($id, 'warehouse_bound_details');
			if (empty($record)) {
				$this->session->set_flashdata('error', 'Outbound detail not found.');
				redirect('admin/warehouseOutbounds');
				return;
			}

			$record = $record[0];
			$customerId     = $record['customer_id'];
			$materialId     = $record['material_id'];
			$piecesQuantity = $record['pieces_quantity'];
			$palletQuantity = $record['pallet_quantity'];
			$warehouseId    = $record['warehouse_id'];
			$sublocationId  = $record['sublocation_id'];

			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			$where = array(
				'warehouse_id'    => $warehouseId,
				'sublocation_id'  => $sublocationId,
				'customer_id'     => $customerId,
				'material_id'     => $materialId
			);
			
			$this->db->trans_start();
			
			$existingStock = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse_stock');

			if (!empty($existingStock)) {
				$currentStock = $existingStock[0];
				$newQty = $currentStock['total_pieces'] + $piecesQuantity;
				$newPallets = $currentStock['total_pallets'] + $palletQuantity;

				$update_data = array(
					'total_pieces'  => $newQty,
					'total_pallets' => $newPallets,
					'updated_by'    => $userid,
					'date'          => date('Y-m-d H:i:s')
				);
				$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse_stock', $update_data);
			}

			// Soft delete detail row
			$updateDetail = array(
				'deleted'      => 'Y',
				'date'    => date('Y-m-d H:i:s')
			);
			$this->Comancontroler_model->update_table_by_id($id, 'warehouse_bound_details', $updateDetail);

			// Log entry
			$log_data = array(
				'userId'     => $userid,
				'customer_id' => $customerId,
				'material_id' => $materialId,
				'action'     => 'Deleted',
				'type'      => 'outbound',
				'detail_id'   => $id,
				'date'       => date('Y-m-d H:i:s')
			);
			$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');
			
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'Failed to delete outbound. Operation rolled back.');
			} else {
				$this->db->trans_commit();
				$this->session->set_flashdata('item', 'Outbound record deleted and stock updated.');
			}

			redirect('admin/warehouseOutbounds');			
		}
		public function outboundAll_old() {
			// echo 'test';exit;
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
				$customerId = $this->input->post('customerId');
				$warehouseAddressId = $this->input->post('warehouseAddressId');
				$sublocationId = $this->input->post('sublocationId');
				$materialIds = $this->input->post('materialId');
				$dateOut = $this->input->post('dateOut');
				$this->db->trans_start();
				$allInserted = true;
				foreach ($materialIds as $materialId) {
					if (empty($materialId)) continue;
					$inboundSql = "SELECT * FROM warehouseInbounds 
						WHERE customerId = '$customerId' AND warehouseAddressId = '$warehouseAddressId' AND sublocationId = '$sublocationId' AND materialId = '$materialId' AND deleted = 'N' ORDER BY dateIn ASC";
						// echo  $inboundSql; exit;
					$inbounds = $this->db->query($inboundSql)->result_array();
					foreach ($inbounds as $inb) {
						$palletNumber = $inb['palletNumber'];
						$outboundSql = "SELECT piecesQuantity as outPiecesQuantity, palletQuantity as outPalletQuantity 
							FROM warehouseOutbounds 
							WHERE customerId = $customerId AND warehouseAddressId = '$warehouseAddressId' AND sublocationId = '$sublocationId' AND materialId = '$materialId' AND palletNumber = '$palletNumber'  AND deleted = 'N'";
						$outRes = $this->db->query($outboundSql)->row_array();
						$outQty = $outRes['outPiecesQuantity'] ?? 0;
						$outPalletQty = $outRes['outPalletQuantity'] ?? 0;
						$availableQty = $inb['piecesQuantity'] - $outQty;
						$availablePalletQty = $inb['palletQuantity'] - $outPalletQty;
						if ($availableQty > 0) {
							$insertData = [
								'dispatchedBy' => $userid,
								'customerId' => $customerId,
								'warehouseAddressId' => $warehouseAddressId,
								'sublocationId' => $sublocationId,
								'materialId' => $materialId,
								'lotNumber' => $inb['lotNumber'],
								'palletNumber' => $inb['palletNumber'],
								'palletPosition' => $inb['palletPosition'],
								'palletQuantity' => $availablePalletQty,
								'piecesQuantity' => $availableQty,
								'dateOut' => $dateOut
							];
							$res = $this->Comancontroler_model->add_data_in_table($insertData, 'warehouseOutbounds');
							if (!$res) {
								$allInserted = false;
								break;
							}
							$where = [
								'customerId' => $customerId,
								'materialId' => $materialId
							];
							$warehouse = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse');
							if (!empty($warehouse)) {
								$newQty = $warehouse[0]['quantity'] - $availableQty;
								if ($newQty < 0) $newQty = 0;

								$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse', [
									'quantity' => $newQty,
									'updatedBy' => $userid,
									'date' => date('Y-m-d')
								]);
							}
							$this->Comancontroler_model->add_data_in_table([
								'userId' => $userid,
								'customerId' => $customerId,
								'materialId' => $materialId,
								'action' => 'Bulk Outbound',
								'table' => 'warehouseOutbounds',
								'recordId' => $res
							], 'warehouseLogs');
						}
					}
				}
				$this->db->trans_complete();
				if ($this->db->trans_status() === FALSE || !$allInserted) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Bulk outbound failed.');
				} else {
					$this->db->trans_commit();
					$this->session->set_flashdata('item', 'Bulk outbound completed successfully.');
				}
				redirect(base_url('admin/warehouse/addOutbounds'));
			
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds_add', $data);
			$this->load->view('admin/layout/footer');
		}
		public function outboundAll() {
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			$customerId = $this->input->post('customerId');
			$warehouse_id = $this->input->post('warehouse_id');
			$sublocationId = $this->input->post('sublocationId');
			$materialIds = $this->input->post('materialId');
			$dateOut = $this->input->post('dateOut');

			$this->db->trans_start();
			$allInserted = true;

			// Step 1: Create parent outbound record
			$parent_data = [
				'type' => 'outbound',
				'customer_id' => $customerId,
				'warehouse_id' => $warehouse_id,
				'sublocation_id' => $sublocationId,
				'dated' => $dateOut,
				'added_by' => $userid,
			];
			$parent_id = $this->Comancontroler_model->add_data_in_table($parent_data, 'warehouse_bounds');

			if (!$parent_id) {
				$this->session->set_flashdata('error', 'Failed to create outbound record.');
				redirect(base_url('admin/warehouse/addOutbounds'));
			}

			foreach ($materialIds as $materialId) {
				if (empty($materialId)) continue;

				// Get inbounds for this material (FIFO order)
				$inboundSql = "
					SELECT * FROM warehouse_bound_details 
					WHERE customer_id = '$customerId' 
					AND warehouse_id = '$warehouse_id' 
					AND sublocation_id = '$sublocationId' 
					AND material_id = '$materialId' 
					AND type = 'inbound' AND deleted = 'N' 
					ORDER BY date ASC
				";
				$inbounds = $this->db->query($inboundSql)->result_array();

				foreach ($inbounds as $inb) {
					$palletNumber = $inb['pallet_number'];

					// Check how much already outbounded from this pallet
					$outboundSql = "
						SELECT 
							SUM(pieces_quantity) AS outPiecesQuantity, 
							SUM(pallet_quantity) AS outPalletQuantity 
						FROM warehouse_bound_details 
						WHERE type = 'outbound' 
						AND customer_id = '$customerId' 
						AND warehouse_id = '$warehouse_id' 
						AND sublocation_id = '$sublocationId' 
						AND material_id = '$materialId' 
						AND pallet_number = '$palletNumber' 
						AND deleted = 'N'
					";
					$outRes = $this->db->query($outboundSql)->row_array();

					$outQty = $outRes['outPiecesQuantity'] ?? 0;
					$outPalletQty = $outRes['outPalletQuantity'] ?? 0;

					$availableQty = $inb['pieces_quantity'] - $outQty;
					$availablePalletQty = $inb['pallet_quantity'] - $outPalletQty;

					if ($availableQty > 0) {
						// Insert child detail for outbound
						$detail_data = [
							'bound_id' => $parent_id,
							'type' => 'outbound',
							'warehouse_id' => $warehouse_id,
							'sublocation_id' => $sublocationId,
							'customer_id' => $customerId,
							'material_id' => $materialId,
							'lot_number' => $inb['lot_number'],
							'pallet_number' => $inb['pallet_number'],
							'pallet_position' => $inb['pallet_position'],
							'pallet_quantity' => $availablePalletQty,
							'pieces_quantity' => $availableQty
						];
						$detail_id = $this->Comancontroler_model->add_data_in_table($detail_data, 'warehouse_bound_details');

						if (!$detail_id) {
							$allInserted = false;
							break 2; 
						}

						// Update warehouse_stock
						$where = [
							'warehouse_id' => $warehouse_id,
							'sublocation_id' => $sublocationId,
							'customer_id' => $customerId,
							'material_id' => $materialId
						];
						$stock = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse_stock');
						if (!empty($stock)) {
							$currentStock = $stock[0];
							$newQty = $currentStock['total_pieces'] - $availableQty;
							$newPallets = $currentStock['total_pallets'] - $availablePalletQty;

							if ($newQty < 0) $newQty = 0;
							if ($newPallets < 0) $newPallets = 0;

							$update_data = [
								'total_pieces' => $newQty,
								'total_pallets' => $newPallets,
								'updated_by' => $userid,
								'date' => date('Y-m-d H:i:s')
							];
							$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse_stock', $update_data);
						}

						// Add log
						$this->Comancontroler_model->add_data_in_table([
							'userId' => $userid,
							'customer_id' => $customerId,
							'material_id' => $materialId,
							'action' => 'Bulk Outbound',
							'type' => 'outbound',
							'detail_id' => $detail_id,
							'date' => date('Y-m-d H:i:s')
						], 'warehouseLogs');
					}
				}
			}

			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE || !$allInserted) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'Bulk outbound failed.');
			} else {
				$this->db->trans_commit();
				$this->session->set_flashdata('item', 'Bulk outbound completed successfully.');
			}

			redirect(base_url('admin/warehouse/addOutbounds'));
		}
		private function updateStockOutbound($customer_id, $warehouse_id, $sublocation_id, $material_id, $palletQty, $piecesQty, $userid) {
			$where = [
				'warehouse_id' => $warehouse_id,
				'sublocation_id' => $sublocation_id,
				'customer_id' => $customer_id,
				'material_id' => $material_id
			];

			$existing = $this->Comancontroler_model->get_data_by_multiple_column($where, 'warehouse_stock');
			
			$palletQty = (int)($palletQty ?? 0);
    		$piecesQty = (int)($piecesQty ?? 0);
			if ($existing) {
				$existingPallets = (int)($existing[0]['total_pallets'] ?? 0);
		        $existingPieces  = (int)($existing[0]['total_pieces'] ?? 0);
				$update = [
					'total_pallets' => max(0, $existingPallets - $palletQty),
            		'total_pieces'  => max(0, $existingPieces - $piecesQty),
					'updated_by' => $userid
				];
				$this->Comancontroler_model->update_table_by_multiple_column($where, 'warehouse_stock', $update);
			}
		}
		public function uploadOutbounds()
		{
			if (!checkPermission($this->session->userdata('permission'), 'odispatch')) {
				redirect(base_url('AdminDashboard'));
			}

			$data['error'] = [];
			$data['upload'] = '';
			$data['skipped_rows'] = [];
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];

			if ($this->input->post('uploadcsv')) {
				$this->form_validation->set_rules('csvfile1', 'CSV file', 'required');
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

				if ($this->form_validation->run() !== FALSE) {
					if ($_FILES['csvfile']['name']) {
						$file = $_FILES['csvfile']['tmp_name'];
						$handle = fopen($file, "r");

						if (!$handle) {
							$data['error'][] = "Failed to open uploaded file.";
						} else {
							$rowIndex = 0;
							$headers = [];
							$groupedNew = [];
							$existingRows = [];

							while (($dataRow = fgetcsv($handle, 1000, ",")) !== FALSE) {
								if ($rowIndex == 0) {
									$headers = $dataRow;
									$normalizedHeaders = array_map('strtolower', $headers);
									if (in_array('inbound_id', $normalizedHeaders) && in_array('date in', $normalizedHeaders)) {
										$this->session->set_flashdata('error', 'You are trying to upload an Inbound file in the Outbound importer.');
										redirect(base_url('admin/warehouse/uploadOutbounds'));
										return;
									}
									$rowIndex++;
									continue;
								}

								$row = array_combine($headers, $dataRow);
								$rowNumber = $rowIndex + 1;

								$material = trim($row['Material Number']);
								$customer = trim($row['Customer']);
								$warehouse = trim($row['Warehouse']);
								$sublocation = trim($row['Sublocation']);
								$palletQty = is_numeric(trim($row['Pallet Quantity'])) ? trim($row['Pallet Quantity']) : 0;
								$piecesQty = is_numeric(trim($row['Pieces Quantity'])) ? trim($row['Pieces Quantity']) : 0;
								$dateOutRaw = str_replace('-', '/', trim($row['Date Out']));
								$dateOutObj = DateTime::createFromFormat('n/j/Y', $dateOutRaw);
								$dateOut = $dateOutObj ? $dateOutObj->format('Y-m-d') : null;

								if (!$material || !$customer || !$warehouse || !$sublocation || !$dateOut) {
									$data['error'][] = "Row #$rowNumber | Missing required fields.";
									$rowIndex++;
									continue;
								}

								$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', $material);
								if (!$material_id) {
									$data['error'][] = "Row #$rowNumber | Material: $material — Material not found.";
									$rowIndex++;
									continue;
								}

								$outbound_id = trim($row['outbound_id'] ?? '');
								$bound_id = trim($row['bound_id'] ?? '');

								if ($outbound_id && $bound_id) {
									$row['rowNumber'] = $rowNumber;
									$existingRows[] = $row;
								} else {
									$row['rowNumber'] = $rowNumber;
									$key = $dateOut . '|' . $customer . '|' . $warehouse . '|' . $sublocation;
									$groupedNew[$key][] = $row;
								}

								$rowIndex++;
							}

							fclose($handle);

							$this->db->trans_start();

							// Handle new entries
							foreach ($groupedNew as $key => $entries) {
								[$dateOut, $customerName, $warehouseName, $sublocationName] = explode('|', $key);

								$customer_id = $this->Comancontroler_model->get_id_by_column_value('companies', 'company', $customerName);
								$warehouse_id = $this->Comancontroler_model->get_id_by_column_value('warehouse', 'warehouse', $warehouseName);
								$sublocation_id = $this->Comancontroler_model->get_id_by_column_value('warehouse_sublocations', 'name', $sublocationName);

								$parent_data = [
									'added_by' => $userid,
									'customer_id' => $customer_id,
									'warehouse_id' => $warehouse_id,
									'sublocation_id' => $sublocation_id,
									'total_pallets' => 0,
									'total_pieces' => 0,
									'dated' => $dateOut,
									'type' => 'outbound'
								];

								$this->db->insert('warehouse_bounds', $parent_data);
								$bound_id = $this->db->insert_id();

								foreach ($entries as $entry) {
									$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', trim($entry['Material Number']));

									$stockWhere = [
										'warehouse_id' => $warehouse_id,
										'sublocation_id' => $sublocation_id,
										'customer_id' => $customer_id,
										'material_id' => $material_id
									];

									$stock = $this->Comancontroler_model->get_data_by_multiple_column($stockWhere, 'warehouse_stock');
									$availablePallets = $stock[0]['total_pallets'] ?? 0;
									$availablePieces = $stock[0]['total_pieces'] ?? 0;

									if ($entry['Pallet Quantity'] > $availablePallets || $entry['Pieces Quantity'] > $availablePieces) {
										$rowNumber = $entry['rowNumber'] ?? 'N/A';
										$data['skipped_rows'][] = "Row #$rowNumber | Material: {$entry['Material Number']} — Not enough stock (Pallets/Pieces).";
										continue;
									}

									$this->db->insert('warehouse_bound_details', [
										'bound_id' => $bound_id,
										'type' => 'outbound',
										'customer_id' => $customer_id,
										'warehouse_id' => $warehouse_id,
										'sublocation_id' => $sublocation_id,
										'material_id' => $material_id,
										'lot_number' => $entry['Lot Number'],
										'pallet_number' => $entry['Pallet Number'],
										'pallet_position' => $entry['Pallet Position'],
										'pallet_quantity' => $entry['Pallet Quantity'],
										'pieces_quantity' => $entry['Pieces Quantity']
									]);

									$this->updateStockOutbound($customer_id, $warehouse_id, $sublocation_id, $material_id, $entry['Pallet Quantity'], $entry['Pieces Quantity'], $userid);
								}
							}

							// Handle updates to existing
							foreach ($existingRows as $entry) {
								$rowNumber = $entry['rowNumber'];
								$outbound_id = trim($entry['outbound_id']);
								$bound_id = trim($entry['bound_id']);
								$material_id = $this->Comancontroler_model->get_id_by_column_value('warehouseMaterials', 'materialNumber', trim($entry['Material Number']));

								$existingDetail = $this->Comancontroler_model->get_data_by_id($outbound_id, 'warehouse_bound_details');
								if (!$existingDetail) continue;

								$oldPallets = (int)$existingDetail[0]['pallet_quantity'];
								$oldPieces = (int)$existingDetail[0]['pieces_quantity'];

								$diffPallets = (int)$entry['Pallet Quantity'] - $oldPallets;
								$diffPieces = (int)$entry['Pieces Quantity'] - $oldPieces;

								$stockWhere = [
									'warehouse_id' => $existingDetail[0]['warehouse_id'],
									'sublocation_id' => $existingDetail[0]['sublocation_id'],
									'customer_id' => $existingDetail[0]['customer_id'],
									'material_id' => $material_id
								];

								$stock = $this->Comancontroler_model->get_data_by_multiple_column($stockWhere, 'warehouse_stock');
								$availablePallets = $stock[0]['total_pallets'] ?? 0;
								$availablePieces = $stock[0]['total_pieces'] ?? 0;

								if ($diffPallets > $availablePallets || $diffPieces > $availablePieces) {
									$data['skipped_rows'][] = "Row #$rowNumber | Material: {$entry['Material Number']} — Not enough stock (Pallets/Pieces).";
									continue;
								}

								$this->Comancontroler_model->update_table_by_id($outbound_id, 'warehouse_bound_details', [
									'lot_number' => $entry['Lot Number'],
									'pallet_number' => $entry['Pallet Number'],
									'pallet_position' => $entry['Pallet Position'],
									'pallet_quantity' => $entry['Pallet Quantity'],
									'pieces_quantity' => $entry['Pieces Quantity']
								]);

								$this->db->select('SUM(pallet_quantity) AS total_pallets, SUM(pieces_quantity) AS total_pieces');
								$this->db->where('bound_id', $bound_id);
								$this->db->where('type', 'outbound');
								$this->db->where('deleted', 'N');
								$sums = $this->db->get('warehouse_bound_details')->row_array();

								$this->Comancontroler_model->update_table_by_id($bound_id, 'warehouse_bounds', [
									'total_pallets' => $sums['total_pallets'],
									'total_pieces' => $sums['total_pieces']
								]);

								$updatedStock = [
									'total_pallets' => $availablePallets - $diffPallets,
									'total_pieces' => $availablePieces - $diffPieces,
									'updated_by' => $userid
								];
								$this->Comancontroler_model->update_table_by_multiple_column($stockWhere, 'warehouse_stock', $updatedStock);
							}

							$this->db->trans_complete();

							if ($this->db->trans_status() === FALSE) {
								$data['error'][] = 'Transaction failed. Please check data.';
							} else {
								$this->session->set_flashdata('item', 'Outbound CSV imported successfully.');
							}
						}
					}
				}
			}

			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/upload_warehouse_outbounds', $data);
			$this->load->view('admin/layout/footer');
		}
		public function directOutbound() {
			$bound_id = $this->input->get('bound_id');
			$dated = $this->input->get('dated');
			$customerId = $this->input->get('customerId');
			$warehouseId = $this->input->get('warehouseId');
			$sublocationId = $this->input->get('sublocationId');
			$detailIdsStr = $this->input->get('detailIds');
			$detailIds = !empty($detailIdsStr) ? json_decode($detailIdsStr, true) : [];

			$data['bound_id'] = $bound_id;
			$data['dated'] = $dated;
			$data['customer_id'] = $customerId;
			$data['warehouse_id'] = $warehouseId;
			$data['sublocation_id'] = $sublocationId;
			$data['detail_ids'] = $detailIds;

			$data['materialDetails'] = $this->Warehouse_model->getMaterialDetailsForTransfer($detailIds);
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=', 'Deleted', 'companies', '*', 'company', 'asc', '', 'Yes');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$data['warehouse_sublocations'] = $this->Comancontroler_model->get_data_by_table('warehouse_sublocations');

			if ($this->input->post('save')) {
				$user = $this->session->userdata('logged');
				$userid = $user['adminid'];

				// Outbound data
				$customerId     = $this->input->post('customer_id');
				$warehouseId    = $this->input->post('warehouse_id');
				$sublocationId  = $this->input->post('sublocation_id');
				$materialId     = $this->input->post('material_id');
				$lotNumber      = $this->input->post('lotNumber');
				$palletNumber   = $this->input->post('palletNumber');
				$palletPosition = $this->input->post('palletPosition');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$dateOut        = $this->input->post('dated');

				$this->db->trans_begin();

				// Insert outbound parent
				$parentData = array(
					'added_by'       => $userid,
					'customer_id'    => $customerId,
					'warehouse_id'   => $warehouseId,
					'sublocation_id' => $sublocationId,
					'dated'          => $dateOut,
					'type'           => 'outbound',
					'total_pallets'  => 0,
					'total_pieces'   => 0
				);
				$outbound_id = $this->Comancontroler_model->add_data_in_table($parentData, 'warehouse_bounds');
				if (!$outbound_id) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Failed to create outbound entry.');
					redirect(base_url('admin/warehouseInbounds'));
					return;
				}

				$totalPallets = 0;
				$totalPieces  = 0;
				for ($i = 0; $i < count($materialId); $i++) {
					if (empty($materialId[$i])) continue;

					$check = array(
						'warehouse_id'   => $warehouseId,
						'sublocation_id' => $sublocationId,
						'customer_id'    => $customerId,
						'material_id'    => $materialId[$i]
					);
					$existingStock = $this->Comancontroler_model->get_data_by_multiple_column($check, 'warehouse_stock');

					if (!empty($existingStock)) {
						$currentStock = $existingStock[0];
						$newPieces  = $currentStock['total_pieces'] - $piecesQuantity[$i];
						$newPallets = $currentStock['total_pallets'] - $palletQuantity[$i];

						if ($newPieces < 0 || $newPallets < 0) {
							$materialNumber = $this->db->query("SELECT materialNumber FROM warehouseMaterials WHERE id = " . $materialId[$i])->row()->materialNumber;
							$this->db->trans_rollback();
							$this->session->set_flashdata('error', 'Not enough stock for material number: ' . $materialNumber);
							redirect(base_url('admin/warehouseInbounds'));
							return;
						}

						$detailData = array(
							'bound_id'        => $outbound_id,
							'type'            => 'outbound',
							'customer_id'     => $customerId,
							'warehouse_id'    => $warehouseId,
							'sublocation_id'  => $sublocationId,
							'material_id'     => $materialId[$i],
							'lot_number'      => $lotNumber[$i],
							'pallet_number'   => $palletNumber[$i],
							'pallet_position' => $palletPosition[$i],
							'pallet_quantity' => $palletQuantity[$i],
							'pieces_quantity' => $piecesQuantity[$i]
						);
						$detail_id = $this->Comancontroler_model->add_data_in_table($detailData, 'warehouse_bound_details');

						$updateStock = array(
							'total_pieces'  => $newPieces,
							'total_pallets' => $newPallets,
							'updated_by'    => $userid
						);
						$this->Comancontroler_model->update_table_by_multiple_column($check, 'warehouse_stock', $updateStock);

						$log = array(
							'userId'      => $userid,
							'customer_id' => $customerId,
							'material_id' => $materialId[$i],
							'action'      => 'Inserted',
							'type'        => 'outbound',
							'detail_id'   => $detail_id
						);
						$this->Comancontroler_model->add_data_in_table($log, 'warehouseLogs');

						$totalPallets += $palletQuantity[$i];
						$totalPieces  += $piecesQuantity[$i];
					} else {
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'Material not found in warehouse stock.');
						redirect(base_url('admin/warehouseInbounds'));
						return;
					}
				}

				$this->Comancontroler_model->update_table_by_id($outbound_id, 'warehouse_bounds', array(
					'total_pallets' => $totalPallets,
					'total_pieces'  => $totalPieces
				));

				// Complete the transaction
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Outbound transaction failed.');
				} else {
					$this->db->trans_commit();
					$this->session->set_flashdata('item', 'Outbound transaction completed successfully.');
				}

				redirect(base_url('admin/warehouseInbounds'));
			}

			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');			
			$this->load->view('warehouse/warehouse_direct_outbound', $data);
			$this->load->view('admin/layout/footer');
		}
		/****************** Material history ***************/
		public function materialHistory(){
			$company = $sdate = $edate = $materialId = $warehouse_id = $sublocationId = '';
			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				// if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				// if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				// $warehouse_id = $this->input->post('warehouse_id');
				// $sublocationId = $this->input->post('sublocationId');
				$data['materialHistory'] = $this->Warehouse_model->materialHistory($company, $materialId);
			} else {
				$data['materialHistory'] = $this->Warehouse_model->materialHistory($company, $materialId);
			}
	        $data['materials'] = $this->getMaterials();
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			// $data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			// $data['warehouse_sublocations'] = $this->Comancontroler_model->get_data_by_table('warehouse_sublocations');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/material_history',$data);
			$this->load->view('admin/layout/footer');
		}
		/****************** Internal warehouse Transfer ****/
		public function internalTransfer() {
			$bound_id = $this->input->get('bound_id');
			$dated = $this->input->get('dated');
			$customerId = $this->input->get('customerId');
			$warehouseId = $this->input->get('warehouseId');
			$sublocationId = $this->input->get('sublocationId');
			$detailIdsStr = $this->input->get('detailIds');
			$detailIds = !empty($detailIdsStr) ? json_decode($detailIdsStr, true) : [];

			$data['bound_id'] = $bound_id;
			$data['dated'] = $dated;
			$data['customer_id'] = $customerId;
			$data['warehouse_id'] = $warehouseId;
			$data['sublocation_id'] = $sublocationId;
			$data['detail_ids'] = $detailIds;

			$data['materialDetails'] = $this->Warehouse_model->getMaterialDetailsForTransfer($detailIds);
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=', 'Deleted', 'companies', '*', 'company', 'asc', '', 'Yes');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse');
			$data['warehouse_sublocations'] = $this->Comancontroler_model->get_data_by_table('warehouse_sublocations');

			if ($this->input->post('save')) {
				$user = $this->session->userdata('logged');
				$userid = $user['adminid'];

				// Outbound data
				$customerId     = $this->input->post('customer_id');
				$warehouseId    = $this->input->post('warehouse_id');
				$sublocationId  = $this->input->post('sublocation_id');
				$materialId     = $this->input->post('material_id');
				$lotNumber      = $this->input->post('lotNumber');
				$palletNumber   = $this->input->post('palletNumber');
				$palletPosition = $this->input->post('palletPosition');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$notes        = $this->input->post('notes');

				$dateOut        = $this->input->post('dated_new');

				// Inbound data
				$newWarehouseId    = $this->input->post('warehouse_id_new');
				$newSublocationId  = $this->input->post('sublocation_id_new');
				$lotNumberNew      = $this->input->post('lotNumberNew');
				$palletPositionNew = $this->input->post('palletPositionNew');
				$dateIn            = $this->input->post('dated_new');

				$this->db->trans_begin();

				// Insert outbound parent
				$parentData = array(
					'added_by'       => $userid,
					'customer_id'    => $customerId,
					'warehouse_id'   => $warehouseId,
					'sublocation_id' => $sublocationId,
					'dated'          => $dateOut,
					'type'           => 'outbound',
					'total_pallets'  => 0,
					'total_pieces'   => 0
				);
				$outbound_id = $this->Comancontroler_model->add_data_in_table($parentData, 'warehouse_bounds');
				if (!$outbound_id) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Failed to create outbound entry.');
					redirect(base_url('admin/warehouse/addOutbounds'));
					return;
				}

				$totalPallets = 0;
				$totalPieces  = 0;

				// Insert outbound child details
				for ($i = 0; $i < count($materialId); $i++) {
					if (empty($materialId[$i])) continue;

					$check = array(
						'warehouse_id'   => $warehouseId,
						'sublocation_id' => $sublocationId,
						'customer_id'    => $customerId,
						'material_id'    => $materialId[$i]
					);
					$existingStock = $this->Comancontroler_model->get_data_by_multiple_column($check, 'warehouse_stock');

					if (!empty($existingStock)) {
						$currentStock = $existingStock[0];
						$newPieces  = $currentStock['total_pieces'] - $piecesQuantity[$i];
						$newPallets = $currentStock['total_pallets'] - $palletQuantity[$i];

						if ($newPieces < 0 || $newPallets < 0) {
							$materialNumber = $this->db->query("SELECT materialNumber FROM warehouseMaterials WHERE id = " . $materialId[$i])->row()->materialNumber;
							$this->db->trans_rollback();
							$this->session->set_flashdata('error', 'Not enough stock for material number: ' . $materialNumber);
							redirect(base_url('admin/warehouse/addOutbounds'));
							return;
						}

						$detailData = array(
							'bound_id'        => $outbound_id,
							'type'            => 'outbound',
							'customer_id'     => $customerId,
							'warehouse_id'    => $warehouseId,
							'sublocation_id'  => $sublocationId,
							'material_id'     => $materialId[$i],
							'lot_number'      => $lotNumber[$i],
							'pallet_number'   => $palletNumber[$i],
							'pallet_position' => $palletPosition[$i],
							'pallet_quantity' => $palletQuantity[$i],
							'pieces_quantity' => $piecesQuantity[$i],
							'notes' => $notes[$i]
						);
						$detail_id = $this->Comancontroler_model->add_data_in_table($detailData, 'warehouse_bound_details');

						$updateStock = array(
							'total_pieces'  => $newPieces,
							'total_pallets' => $newPallets,
							'updated_by'    => $userid
						);
						$this->Comancontroler_model->update_table_by_multiple_column($check, 'warehouse_stock', $updateStock);

						$log = array(
							'userId'      => $userid,
							'customer_id' => $customerId,
							'material_id' => $materialId[$i],
							'action'      => 'Inserted',
							'type'        => 'outbound',
							'detail_id'   => $detail_id
						);
						$this->Comancontroler_model->add_data_in_table($log, 'warehouseLogs');

						$totalPallets += $palletQuantity[$i];
						$totalPieces  += $piecesQuantity[$i];
					} else {
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'Material not found in warehouse stock.');
						redirect(base_url('admin/warehouse/addOutbounds'));
						return;
					}
				}

				$this->Comancontroler_model->update_table_by_id($outbound_id, 'warehouse_bounds', array(
					'total_pallets' => $totalPallets,
					'total_pieces'  => $totalPieces
				));

				// Insert inbound parent
				$inbound_parent = array(
					'added_by'       => $userid,
					'customer_id'    => $customerId,
					'warehouse_id'   => $newWarehouseId,
					'sublocation_id' => $newSublocationId,
					'total_pallets'  => $totalPallets,
					'total_pieces'   => $totalPieces,
					'dated'          => $dateIn,
					'type'           => 'inbound'
				);
				$inbound_id = $this->Comancontroler_model->add_data_in_table($inbound_parent, 'warehouse_bounds');

				for ($i = 0; $i < count($materialId); $i++) {
					if (empty($materialId[$i])) continue;

					$detail_data = array(
						'bound_id'        => $inbound_id,
						'type'            => 'inbound',
						'customer_id'     => $customerId,
						'warehouse_id'    => $newWarehouseId,
						'sublocation_id'  => $newSublocationId,
						'material_id'     => $materialId[$i],
						'lot_number'      => $lotNumberNew[$i],
						'pallet_number'   => $palletNumber[$i],
						'pallet_position' => $palletPositionNew[$i],
						'pallet_quantity' => $palletQuantity[$i],
						'pieces_quantity' => $piecesQuantity[$i],
						'notes' => $notes[$i]
					);
					$this->db->insert('warehouse_bound_details', $detail_data);

					$log_data = array(
						'userId'      => $userid,
						'customer_id' => $customerId,
						'material_id' => $materialId[$i],
						'action'      => 'Inserted',
						'type'        => 'inbound',
						'detail_id'   => $inbound_id
					);
					$this->Comancontroler_model->add_data_in_table($log_data, 'warehouseLogs');

					$stock_where = array(
						'warehouse_id'   => $newWarehouseId,
						'sublocation_id' => $newSublocationId,
						'customer_id'    => $customerId,
						'material_id'    => $materialId[$i]
					);
					$existing_stock = $this->Comancontroler_model->get_data_by_multiple_column($stock_where, 'warehouse_stock');

					if (!empty($existing_stock)) {
						$updated_stock = array(
							'total_pallets' => $existing_stock[0]['total_pallets'] + $palletQuantity[$i],
							'total_pieces'  => $existing_stock[0]['total_pieces'] + $piecesQuantity[$i],
							'updated_by'    => $userid
						);
						$this->Comancontroler_model->update_table_by_multiple_column($stock_where, 'warehouse_stock', $updated_stock);
					} else {
						$new_stock = $stock_where + array(
							'total_pallets' => $palletQuantity[$i],
							'total_pieces'  => $piecesQuantity[$i],
							'updated_by'    => $userid
						);
						$this->Comancontroler_model->add_data_in_table($new_stock, 'warehouse_stock');
					}
				}

				// Complete the transaction
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Inbound/Outbound transaction failed.');
				} else {
					$this->db->trans_commit();
					$this->session->set_flashdata('item', 'Internal transfer completed successfully.');
				}

				redirect(base_url('admin/warehouseInbounds'));
			}

			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');			
			$this->load->view('warehouse/warehouse_internal_transfer', $data);
			$this->load->view('admin/layout/footer');
		}

		/****************** Warehouse Logs *****************/
		function warehouseLogs() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			 $company = $sdate = $edate = $material_id = '';
        
			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$material_id = $this->input->post('material_id');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$data['warehouse'] = $this->Warehouse_model->warehouseLogs($sdate,$edate,$company,$material_id);
			} else {
				$data['warehouse'] = $this->Warehouse_model->warehouseLogs($sdate,$edate,$company,$material_id);
			}
			$data['materials'] = $this->getMaterials();
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc','','Yes');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_logs',$data);
			$this->load->view('admin/layout/footer');
		}
		/****************** download bound report *****************/
		public function downloadBoundPDF(){
			if (!checkPermission($this->session->userdata('permission'), 'statementAcc')) {
				redirect(base_url('AdminDashboard'));
			}
			$this->load->library('pdf');
			$pdf = $this->pdf->load();
			$id = $this->uri->segment(3);
			$date = $_GET['date'];
			$customerId = $_GET['customerId'];
			$warehouseId = $_GET['warehouseId'];
			$sublocationId = $_GET['sublocationId'];
			$type = $_GET['type'];
			// $data['bounds'] = $this->Comancontroler_model->get_data_by_id($id, $table);
			$data['company'] = $this->Comancontroler_model->get_data_by_id($customerId, 'companies');
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($warehouseId, 'warehouse');
			$data['sublocation'] = $this->Comancontroler_model->get_data_by_id($sublocationId, 'warehouse_sublocations');
			// print_r($data['warehouseInbounds'][0]['warehouseAddressId']);exit;
			$data['invoice'] = array();			
			$data['type'] = '';
			$data['invoice'] = $this->Warehouse_model->getDocumentRecord($id);
			$today = date('mdY');
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			
			$receiptType = ($type == 'inbound') ? 'Inward' : 'Outward';
			$receiptDate = $date;
			$prefix = date('mdY', strtotime($receiptDate));

			$this->db->where([
				'bound_id' => $id
			]);
			$existing = $this->db->get('warehouse_receipts')->row();
			if ($existing) {
				$receipt_no = $existing->receipt_no;
			} else {
				$count = $this->getTodayReceiptCount($prefix, $receiptType);
				$try = 1;
				do {
					$receipt_no = $prefix . '-' . ($count + $try);
					$this->db->where('receipt_no', $receipt_no);
					$this->db->where('type', $receiptType);
					$exists = $this->db->get('warehouse_receipts')->row();
					$try++;
				} while ($exists);
				$this->db->insert('warehouse_receipts', [
					'receipt_no' => $receipt_no,
					'type' => $receiptType,
					'user_id' => $userid,
					'bound_id' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]);
			}
			$data['date'] = $receiptDate;
			$data['receipt_no'] = $receipt_no;
			if($type == 'inbound'){
				$file = 'inwardRDPDF';
				$html = $this->load->view('warehouse/' . $file, $data, true);
				$pdf->WriteHTML($html);
				$output = 'Inward RD - ' . $data['company'][0]['company'] . ' ' . date('m-d-Y') . '.pdf';
			}else{
				$file = 'outwardDDPDF';
				$html = $this->load->view('warehouse/' . $file, $data, true);
				$pdf->WriteHTML($html);
				$output = 'Outward DD - ' . $data['company'][0]['company'] . ' ' . date('m-d-Y') . '.pdf';
			}
			$pdf->Output($output, "D");
			exit;
		}		
		/****************** Essentials *****************/
		public function getMaterials(){			
			$sql="SELECT * FROM warehouseMaterials WHERE deleted='N'";
			$materials = $this->db->query($sql)->result_array();
			return $materials;
		}
		public function getMaterialsByCustomer(){
			$customerId = $this->input->post('customerId');
			$sql="SELECT * FROM warehouseMaterials where customerId=$customerId AND deleted='N'";
			$materials = $this->db->query($sql)->result_array();
			echo json_encode($materials);
		}
		public function getOutboundMaterialsByCustomer(){
			$customerId = $this->input->post('customerId');
			$sql="SELECT warehouseMaterials.* 
			FROM warehouseMaterials 
			JOIN warehouse_stock warehouse ON  warehouseMaterials.id=warehouse.material_id
			where warehouseMaterials.customerId=$customerId AND deleted='N' AND warehouse.`total_pieces`>0 
			GROUP BY warehouseMaterials.id";
			// echo $sql;exit;
			$materials = $this->db->query($sql)->result_array();
			echo json_encode($materials);
		}
		// public function getBatchesByMaterial(){
		// 	$materialId = $this->input->post('materialId');
		// 	$materialSql="SELECT materialNumber FROM warehouseMaterials where id=$materialId";
		// 	$materialNumber=$this->db->query($materialSql)->row()->materialNumber;
		// 	$sql="SELECT batch FROM warehouseMaterials where materialNumber=$materialNumber";
		// 	$batches = $this->db->query($sql)->result_array();
		// 	if (!empty($batches)) {
		// 		echo json_encode(['status' => 'success', 'batches' => $batches]);
		// 	} else {
		// 		echo json_encode(['status' => 'error', 'batches' => []]);
		// 	}
		// }

		public function getBatchesByMaterial() {
			$materialId = $this->input->post('materialId');
			$sql = "SELECT batch,`description` FROM warehouseMaterials WHERE id = ?";
			$query = $this->db->query($sql, [$materialId]);
			$result = $query->row();
			$batch = $result->batch;
			$description = $result->description;
			echo json_encode(['status' => 'success', 'batch' => $batch,  'description' => $description]);
		}
		public function getBatchesAndPQtyByMaterial() {
			$materialId = $this->input->post('materialId');
			$customerId = $this->input->post('customerId');
			$warehouseId = $this->input->post('warehouse_id');
			$sublocationId = $this->input->post('sublocationId');

			// Step 1: Get batch and description
			$sql = "SELECT batch, `description` FROM warehouseMaterials WHERE id = ?";
			$query = $this->db->query($sql, [$materialId]);
			$result = $query->row();

			$batch = $result->batch ?? '';
			$description = $result->description ?? '';

			// Step 2: Calculate total available (inbound - outbound)
			$totalPieces = 0;
			$totalPallets = 0;

			// Get all inbound records for the material
			$inboundSql = "
				SELECT * FROM warehouse_bound_details 
				WHERE customer_id = ? 
				AND warehouse_id = ? 
				AND sublocation_id = ? 
				AND material_id = ? 
				AND type = 'inbound' 
				AND deleted = 'N'
				ORDER BY date ASC
			";
			$inbounds = $this->db->query($inboundSql, [$customerId, $warehouseId, $sublocationId, $materialId])->result_array();

			foreach ($inbounds as $inb) {
				$palletNumber = $inb['pallet_number'];

				// Get already outbound quantities for this pallet
				$outboundSql = "
					SELECT 
						SUM(pieces_quantity) AS outPieces, 
						SUM(pallet_quantity) AS outPallets 
					FROM warehouse_bound_details 
					WHERE type = 'outbound' 
					AND customer_id = ? 
					AND warehouse_id = ? 
					AND sublocation_id = ? 
					AND material_id = ? 
					AND pallet_number = ? 
					AND deleted = 'N'
				";
				$outbound = $this->db->query($outboundSql, [$customerId, $warehouseId, $sublocationId, $materialId, $palletNumber])->row_array();

				$outPieces = $outbound['outPieces'] ?? 0;
				$outPallets = $outbound['outPallets'] ?? 0;

				$availablePieces = $inb['pieces_quantity'] - $outPieces;
				$availablePallets = $inb['pallet_quantity'] - $outPallets;

				if ($availablePieces > 0) {
					$totalPieces += $availablePieces;
					$totalPallets += $availablePallets;
				}
			}

			echo json_encode([
				'status' => 'success',
				'batch' => $batch,
				'description' => $description,
				'total_pieces' => $totalPieces,
				'total_pallets' => $totalPallets
			]);
		}
		public function getTodayReceiptCount($prefix, $type) {
			$sql = "SELECT COUNT(*) AS total FROM warehouse_receipts WHERE receipt_no LIKE '$prefix%'";
			$query = $this->db->query($sql);
			$row = $query->row();
			return $row->total;
		}
		public function getCustomerId($customer) {
			$sql = "SELECT id FROM companies WHERE `company` LIKE '%$customer%'"; 
			$query = $this->db->query($sql);
			$company_data = $query->result_array();
			// print_r($customer);exit;
			if(empty($company_data)) { 
				return false;
			} else {
				return $company_data[0]['id']; 
			}
		}
		public function getPalletsByMaterialAndBatch() {
			$materialId = $this->input->post('materialId');
			$warehouse_id = $this->input->post('warehouse_id');
			$sublocation_id = $this->input->post('sublocation_id');

			$where = " 1=1 AND deleted='N' AND `type`='inbound'";
			if($materialId){
				$where .=" AND material_id=$materialId";
			}
			if($warehouse_id){
				$where .=" AND warehouse_id=$warehouse_id";
			}
			if($sublocation_id){
				$where .=" AND sublocation_id=$sublocation_id";
			}
			$sql = "SELECT pallet_number palletNumber, pallet_quantity palletQuantity, pieces_quantity piecesQuantity, lot_number lotNumber, pallet_position palletPosition FROM warehouse_bound_details
			WHERE $where ";
			$result = $this->db->query($sql)->result_array();
			// echo $sql; exit;

			if (!empty($result)) {
				echo json_encode(['status' => 'success', 'pallets' => $result]);
			} else {
				echo json_encode(['status' => 'error', 'pallets' => []]);
			}
		}
		public function getSublocationsByWarehouse() {
			$warehouse_id = $this->input->post('warehouse_id');
			$data = [];
			if ($warehouse_id) {
				$data = $this->db->get_where('warehouse_sublocations', ['warehouse_id' => $warehouse_id])->result_array();
			}
			echo json_encode($data);
		}
	}
?>