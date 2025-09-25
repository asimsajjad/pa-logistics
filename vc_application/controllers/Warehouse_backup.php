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
		    $company = $sdate = $edate = $materialId = $warehouseAddressId = $sublocationId = '';
        
			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				$warehouseAddressId = $this->input->post('warehouseAddressId');
				$sublocationId = $this->input->post('sublocationId');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$data['warehouse'] = $this->Warehouse_model->warehouse($sdate,$edate,$company,$materialId,$warehouseAddressId,$sublocationId);
			} else {
				$data['warehouse'] = $this->Warehouse_model->warehouse($sdate,$edate,$company,$materialId,$warehouseAddressId,$sublocationId);
			}

			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
			$data['materials'] = $this->getMaterials();
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
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

			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');

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
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
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
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
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
			 $company = $sdate = $edate = $materialId = $warehouseAddressId = $sublocationId = '';
        
			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$data['warehouse'] = $this->Warehouse_model->inbounds($sdate,$edate,$company,$materialId);
			} else {
				$data['warehouse'] = $this->Warehouse_model->inbounds($sdate,$edate,$company,$materialId);
			}
			$data['materials'] = $this->getMaterials();
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
			// $data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			// $data['warehouse_sublocations'] = $this->Comancontroler_model->get_data_by_table('warehouse_sublocations');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_inbounds',$data);
			$this->load->view('admin/layout/footer');
		}
		function addInbounds() {
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
				$palletPosition = $this->input->post('palletPosition');
				$lotNumber = $this->input->post('lotNumber');					
				$palletNumber = $this->input->post('palletNumber');
				$palletQuantity = $this->input->post('palletQuantity');
				$piecesQuantity = $this->input->post('piecesQuantity');
				$dateIn = $this->input->post('dateIn');
				// echo count($materialId);exit;
				$allInserted = true;
				$this->db->trans_start();
				for ($i = 0; $i < count($materialId); $i++) {
					// Skip empty materials (if user added extra rows)
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
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
	    	$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_inbounds_add',$data);
			$this->load->view('admin/layout/footer');
		}
		function updateInbounds() {
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
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($id,'warehouseInbounds');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_inbounds_update',$data);
			$this->load->view('admin/layout/footer');
		}
		public function deleteInbounds() {
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
		/*******************Warehouse Outbounds ************************ */
		function outbounds() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			 $company = $sdate = $edate = $materialId = $warehouseAddressId = $sublocationId = '';
        
			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				// $warehouseAddressId = $this->input->post('warehouseAddressId');
				// $sublocationId = $this->input->post('sublocationId');
				$data['warehouse'] = $this->Warehouse_model->outbounds($sdate,$edate,$company, $materialId);
			} else {
				$data['warehouse'] = $this->Warehouse_model->outbounds($sdate,$edate,$company, $materialId);
			}
	        $data['materials'] = $this->getMaterials();
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
			// $data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			// $data['warehouse_sublocations'] = $this->Comancontroler_model->get_data_by_table('warehouse_sublocations');
			$this->load->view('admin/layout/header'); 
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds',$data);
			$this->load->view('admin/layout/footer');
		}
		function addOutbounds() {
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
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds_add',$data);
			$this->load->view('admin/layout/footer');
		}
		function updateOutbounds() {
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
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($id,'warehouseOutbounds');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds_update',$data);
			$this->load->view('admin/layout/footer');
		}
		public function deleteOutbounds() {
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
		public function outboundAll() {
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
			
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
			$data['warehouse_address'] = $this->Comancontroler_model->get_data_by_table('warehouse_address');
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('warehouse/warehouse_outbounds_add', $data);
			$this->load->view('admin/layout/footer');
		}

		/****************** Warehouse Logs *****************/
		function warehouseLogs() {
		    // if(!checkPermission($this->session->userdata('permission'),'company')){
    	    //     redirect(base_url('AdminDashboard'));   
    	    // }
			 $company = $sdate = $edate = $materialId = '';
        
			if($this->input->post('search'))	{
				$company = $this->input->post('company');
				$materialId = $this->input->post('materialId');
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
				if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
				$data['warehouse'] = $this->Warehouse_model->warehouseLogs($sdate,$edate,$company,$materialId);
			} else {
				$data['warehouse'] = $this->Warehouse_model->warehouseLogs($sdate,$edate,$company,$materialId);
			}
			$data['materials'] = $this->getMaterials();
			$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
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
			$table = $_GET['dTable'];
			$data['bounds'] = $this->Comancontroler_model->get_data_by_id($id, $table);
			$data['company'] = $this->Comancontroler_model->get_data_by_id($customerId, 'companies');
			$data['warehouse'] = $this->Comancontroler_model->get_data_by_id($warehouseId, 'warehouse_address');
			$data['sublocation'] = $this->Comancontroler_model->get_data_by_id($sublocationId, 'warehouse_sublocations');
			// print_r($data['warehouseInbounds'][0]['warehouseAddressId']);exit;
			$data['invoice'] = array();			
			$data['type'] = '';
			$data['invoice'] = $this->Warehouse_model->getDocumentRecord($date, $customerId, $warehouseId, $table);
				
			$today = date('mdY');
			$user = $this->session->userdata('logged');
			$userid = $user['adminid'];
			
			$receiptType = ($table == 'warehouseInbounds') ? 'Inward' : 'Outward';
			$receiptDate = $date;
			$prefix = date('mdY', strtotime($receiptDate));

			$this->db->where([
				'reference_id' => $id
			]);
			$existing = $this->db->get('warehouse_receipts')->row();

			// if ($existing) {
			// 	$receipt_no = $existing->receipt_no;
			// } else {
			// 	$count = $this->getTodayReceiptCount($prefix, $receiptType);
			// 	$receipt_no = $prefix . '-' . ($count + 1);
			// 	$this->db->insert('warehouse_receipts', [
			// 		'receipt_no' => $receipt_no,
			// 		'type' => $receiptType,
			// 		'user_id' => $userid,
			// 		'reference_id' => $id,
			// 		'created_at' => date('Y-m-d H:i:s')
			// 	]);
			// }
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
					'reference_id' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]);
			}

			$data['date'] = $receiptDate;
			$data['receipt_no'] = $receipt_no;
			if($table == 'warehouseInbounds'){
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
			$sql="SELECT warehouseMaterials.* FROM warehouseMaterials 
			JOIN warehouse ON  warehouseMaterials.id=warehouse.materialId
			where warehouseMaterials.customerId=$customerId AND deleted='N' AND warehouse.`quantity`>0";
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
			$warehouseAddressId = $this->input->post('warehouseAddressId');
			$sql = "SELECT palletNumber, palletQuantity, piecesQuantity, lotNumber, palletPosition FROM warehouseInbounds 
			WHERE materialId = $materialId AND warehouseAddressId = $warehouseAddressId AND deleted='N'";
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