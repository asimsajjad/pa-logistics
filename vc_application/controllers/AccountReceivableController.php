<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AccountReceivableController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->model('Comancontroler_model');
		$this->load->model('Accountreceivable_model');

    	if( empty($this->session->userdata('logged') )) {
    		redirect(base_url('AdminLogin'));
    	}
		// error_reporting(-1);
		// ini_set('display_errors', 1);
		// error_reporting(E_ERROR);
	}
	
	public function accountReceivable(){
	    if(!checkPermission($this->session->userdata('permission'),'statementAcc')){
	        redirect(base_url('AdminDashboard'));   
	    }

        //$sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        //$edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
        $company = $truckingCompany = $driver = $sdate = $edate = $agingSearch = $invoiceNo =  $customerStatus= $agingFrom= $agingTo='';
        
        $data['dispatchURL'] = 'dispatch';
        if($this->input->post('search'))	{
            $company = $this->input->post('company');
			$truckingCompany = $this->input->post('truckingCompany');
			$agingSearch = $this->input->post('agingSearch');
			$invoiceNo = $this->input->post('invoiceNo');
			$agingFrom = $this->input->post('aging_from');
			$agingTo = $this->input->post('aging_to');
            $driver = $this->input->post('driver');
            $dispatchType = $this->input->post('dispatchType');
            if($dispatchType == 'outsideDispatch'){ 
                $table = 'dispatchOutside'; 
                $data['dispatchURL'] = 'outside-dispatch';
            }elseif($dispatchType == 'warehouse_dispatch'){
 				$table = 'warehouse_dispatch'; 
                $data['dispatchURL'] = 'warehouse';
			}else { 
				$table = 'dispatch'; 
			}
            		
			
			$invoiceType = $this->input->post('invoiceType');
			$customerStatus = $this->input->post('status');

            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			$data['dispatch'] = $this->Accountreceivable_model->getAccountReceivable($table,$sdate,$edate,$company,$agingSearch,$invoiceType, $customerStatus, $invoiceNo, $agingFrom, $agingTo);
        } else {
            $data['dispatch'] =array();
        }
        
    	
		$data['drivers'] = array(); //$this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		//$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
		$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies');
		$data['locations'] = array(); //$this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = array(); //$this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = array(); //$this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/account_receivable',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function updateInvoice(){
		$id = $this->input->post('invoice_id');
		$invoicePaidDate = $this->input->post('invoicePaidDate');
		$formatedPaidDate = date('n/j/Y', strtotime($invoicePaidDate));
		$userId=$this->session->userdata('adminid');
		$table = $this->input->post('table');
		if($table == 'dispatch'){
			$dispatchType = 'dispatch';
		}elseif($table == 'dispatchOutside'){
			$dispatchType = 'dispatchOutside';
		}elseif($table == 'warehouse_dispatch'){
			$dispatchType = 'warehouse_dispatch';
		}
		

		$batchSql = "SELECT MAX(id) as id, MAX(batchNo) as batchNo FROM receivableBatches";
		$lastBatch = $this->db->query($batchSql)->row();

		if (($lastBatch)) {
			$batchNo = sprintf('%04d', intval($lastBatch->batchNo) + 1);
			$batchId = (!empty($lastBatch->id)) ? $lastBatch->id + 1 : 1;
		}
		if($table == 'warehouse_dispatch'){
			$dispatchMeta_sql="SELECT invoicePaidDate, invoiceType, `status` FROM $table WHERE id=$id";
			$dispatchMeta_result = $this->db->query($dispatchMeta_sql)->row();
			$invoiceType=$dispatchMeta_result->invoiceType;
			$existingStatus=$dispatchMeta_result->status;
			$existingInvoicePaidDate=$dispatchMeta_result->invoicePaidDate;
			$existingInvoiceCloseDate=$dispatchMeta_result->invoiceCloseDate;
		}else{
			$dispatchMeta_sql="SELECT dispatchMeta, invoiceType, `status` FROM $table WHERE id=$id";
			$dispatchMeta_result = $this->db->query($dispatchMeta_sql)->row();
			$invoiceType=$dispatchMeta_result->invoiceType;
			$existingStatus=$dispatchMeta_result->status;
			$current_metaData=$dispatchMeta_result->dispatchMeta;
			$dispatchMeta = json_decode($current_metaData, true); 
			$existingInvoicePaidDate=$dispatchMeta['invoicePaidDate'];
			$existingInvoiceCloseDate=$dispatchMeta['invoiceCloseDate'];
		}

		if(!empty($_FILES['gd_d']['name'][0])){
			$countFiles = count($_FILES['gd_d']['name']);
			for ($i = 0; $i < $countFiles; $i++) {
        		$_FILES['file']['name']     = $_FILES['gd_d']['name'][$i];
				$_FILES['file']['type']     = $_FILES['gd_d']['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES['gd_d']['tmp_name'][$i];
				$_FILES['file']['error']    = $_FILES['gd_d']['error'][$i];
				$_FILES['file']['size']     = $_FILES['gd_d']['size'][$i];

				$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
				$config['max_size'] = '5000';

				if($table == 'dispatch'){
					$config['upload_path'] = 'assets/upload/';
				}elseif($table == 'dispatchOutside'){
					$config['upload_path'] = 'assets/outside-dispatch/gd/';
				}elseif($table == 'warehouse_dispatch'){
					$config['upload_path'] = 'assets/warehouse/gd/';
				}

				$config['file_name'] = time() . '-GD-' . uniqid();

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('file')) {
					$uploadData = $this->upload->data();
					$paymentProof = $uploadData['file_name'];

					$addfile = array(
						'did' => $id,
						'type' => 'gd',
						'fileurl' => $paymentProof,
						'rdate' => date('Y-m-d H:i:s')
					);

					if ($table == 'dispatchOutside') {
						$this->Comancontroler_model->add_data_in_table($addfile, 'documentsOutside');
					} elseif ($table == 'dispatch') {
						$this->Comancontroler_model->add_data_in_table($addfile, 'documents');
					}elseif($table == 'warehouse_dispatch'){
						$this->Comancontroler_model->add_data_in_table($addfile, 'warehouse_documents');
					}

					// Log each uploaded file
					$changeField[] = array("Payment proof file", "gdfile", "Upload", $paymentProof);
				} else {
					echo $this->upload->display_errors();
				}
			}
			if($existingInvoicePaidDate != $invoicePaidDate){
				$changeField[] = array('Invoice Paid Date','invoicePaidDate',$existingInvoicePaidDate,$invoicePaidDate);
			}	
			if($existingInvoiceCloseDate != $invoicePaidDate){
				$changeField[] = array('Invoice Closed Date','invoiceCloseDate',$existingInvoiceCloseDate,$invoicePaidDate);
			}	
			
			
		}

        if ($id){
			if($table == 'warehouse_dispatch'){
				$sql = "SELECT * FROM sub_invoices WHERE parent_id = $id"; 
				$parentResult = $this->db->query($sql)->result_array();

				$warehouseChild = [];
				$fleetChild     = [];
				$logisticsChild = [];

				foreach ($parentResult as $row) {
					switch (strtolower($row['child_type'])) {
						case 'warehousing':
							$warehouseChild[] = $row['child_id'];
							break;
						case 'fleet':
							$fleetChild[] = $row['child_id'];
							break;
						case 'logistics':
							$logisticsChild[] = $row['child_id'];
							break;
					}
				}

				$allChildGroups = [
					'warehousing' => $warehouseChild,
					'fleet'     => $fleetChild,
					'logistics' => $logisticsChild
				];

				foreach ($allChildGroups as $childType => $childIds) {
					if (empty($childIds)) continue; // skip if no children

					foreach ($childIds as $childId) {

						if ($childType === 'warehousing') {
							$otherChildTable = 'warehouse_dispatch';
						} elseif ($childType === 'fleet') {
							$otherChildTable = 'dispatch';
						} elseif ($childType === 'logistics') {
							$otherChildTable = 'dispatchOutside';
						}
						
						if ($childType === 'warehousing') {	
							$this->db->select('id, invoicePaidDate,invoiceCloseDate, status, invoiceType');
							$this->db->where('id', $childId);
							$otherChildRow = $this->db->get($otherChildTable)->row();
							if ($otherChildRow) {
								$otherChildId   = $otherChildRow->id;
								
								$oldPaidDate  = $otherChildRow->invoicePaidDate ?? '';
								$oldCloseDate = $otherChildRow->invoiceCloseDate ?? '';

								$existingOtherChildStatus = $otherChildRow->status;
								$otherChildInvoiceType    = $otherChildRow->invoiceType;

								if ($otherChildInvoiceType == 'Direct Bill') {
									$childStatus = 'DB Closed ' . $formatedPaidDate;
								} elseif ($otherChildInvoiceType == 'Quick Pay') {
									$childStatus = 'QP Closed ' . $formatedPaidDate;
								} else {
									$childStatus = 'Closed ' . $formatedPaidDate;
								}

								$data = [
									'gd' => 'AK',
									'invoicePaid' => "1",
									'invoicePaidDate'   => $invoicePaidDate,
									'invoiceClose'   => "1",
									'invoiceCloseDate'   => $invoicePaidDate,
									'status' => $childStatus,
									'receivableBatchId' => $batchId
								];

								$this->db->where('id', $otherChildId);
								$this->db->update($otherChildTable, $data);

								// log changes
								$otherChildChangeField = [];
								if ($oldPaidDate != $invoicePaidDate) {
									$otherChildChangeField[] = ['Invoice Paid Date','invoicePaidDate',$oldPaidDate,$invoicePaidDate];
								}
								if ($oldCloseDate != $invoicePaidDate) {
									$otherChildChangeField[] = ['Invoice Closed Date','invoiceCloseDate',$oldCloseDate,$invoicePaidDate];
								}
								if ($existingOtherChildStatus != $childStatus) {
									$otherChildChangeField[] = ['Status','status',$existingOtherChildStatus,$childStatus];
								}

								if ($otherChildChangeField) {
									$userid = $this->session->userdata('logged');
									$otherChildChangeFieldJson = json_encode($otherChildChangeField);
									$otherChildAplog = [
										'did'       => $otherChildId,
										'userid'    => $userid['adminid'],
										'ip_address'=> getIpAddress(),
										'history'   => $otherChildChangeFieldJson,
										'rDate'     => date('Y-m-d H:i:s')
									];
									$this->Comancontroler_model->add_data_in_table($otherChildAplog,'warehouse_dispatch_log'); 	
								}
							}
						}else{
							$sqlChildRow = "SELECT id, dispatchMeta, status, invoiceType FROM $otherChildTable WHERE id=$childId";
							$otherChildRow = $this->db->query($sqlChildRow)->row();
							
							if ($otherChildRow) {
								$otherChildId   = $otherChildRow->id;

								$otherChildMeta = json_decode($otherChildRow->dispatchMeta, true);
								if (!is_array($otherChildMeta)) $otherChildMeta = [];

								$oldPaidDate  = $otherChildMeta['invoicePaidDate'] ?? '';
								$oldCloseDate = $otherChildMeta['invoiceCloseDate'] ?? '';

								// update meta
								$otherChildMeta['invoicePaid']     = "1";
								$otherChildMeta['invoicePaidDate'] = $invoicePaidDate;
								$otherChildMeta['invoiceClose']    = "1";
								$otherChildMeta['invoiceCloseDate']= $invoicePaidDate;

								$existingOtherChildStatus = $otherChildRow->status;
								$updatedOtherChildMeta    = json_encode($otherChildMeta);
								$otherChildInvoiceType    = $otherChildRow->invoiceType;

								if ($otherChildInvoiceType == 'Direct Bill') {
									$childStatus = 'DB Closed ' . $formatedPaidDate;
								} elseif ($otherChildInvoiceType == 'Quick Pay') {
									$childStatus = 'QP Closed ' . $formatedPaidDate;
								} else {
									$childStatus = 'Closed ' . $formatedPaidDate;
								}

								$data = [
									'gd'             => 'AK',
									'dispatchMeta'   => $updatedOtherChildMeta,
									'status'         => $childStatus,
									'receivableBatchId' => $batchId
								];

								$this->db->where('id', $otherChildId);
								$this->db->update($otherChildTable, $data);

								// log changes
								$otherChildChangeField = [];
								if ($oldPaidDate != $invoicePaidDate) {
									$otherChildChangeField[] = ['Invoice Paid Date','invoicePaidDate',$oldPaidDate,$invoicePaidDate];
								}
								if ($oldCloseDate != $invoicePaidDate) {
									$otherChildChangeField[] = ['Invoice Closed Date','invoiceCloseDate',$oldCloseDate,$invoicePaidDate];
								}
								if ($existingOtherChildStatus != $childStatus) {
									$otherChildChangeField[] = ['Status','status',$existingOtherChildStatus,$childStatus];
								}

								if ($otherChildChangeField) {
									$userid = $this->session->userdata('logged');
									$otherChildChangeFieldJson = json_encode($otherChildChangeField);
									$otherChildAplog = [
										'did'       => $otherChildId,
										'userid'    => $userid['adminid'],
										'ip_address'=> getIpAddress(),
										'history'   => $otherChildChangeFieldJson,
										'rDate'     => date('Y-m-d H:i:s')
									];

									if ($childType === 'fleet') {
										$this->Comancontroler_model->add_data_in_table($otherChildAplog,'dispatchLog'); 
									} elseif ($childType === 'logistics') {
										$this->Comancontroler_model->add_data_in_table($otherChildAplog,'dispatchOutsideLog'); 
									}
								}
							}
						}
					}
				}

				if($invoiceType == 'Direct Bill'){
					$status = 'DB Closed '.$formatedPaidDate;
				}else if($invoiceType == 'Quick Pay'){
					$status = 'QP Closed '.$formatedPaidDate;
				}else{
					$status = 'Closed '.$formatedPaidDate;
				}
				if($existingStatus != $status){
					$changeField[] = array('Status','status',$existingStatus,$status);
				}
				$dispatchMetaJson = json_encode($dispatchMeta);
				$this->db->select('parate');
				$this->db->where('id', $id);
				$totalAmount = $this->db->get($table)->row()->parate;

			
				$data = [
					'gd' => 'AK',
					'invoicePaid' => "1",
					'invoicePaidDate'   => $invoicePaidDate,
					'invoiceClose'   => "1",
					'invoiceCloseDate'   => $invoicePaidDate,
					'status' => $status,
					'receivableBatchId' => $batchId
				];

				$this->db->where('id', $id);
				$result = $this->db->update($table, $data);

				$paymentType = 'receivable';
				$insertBatch = "INSERT INTO receivableBatches (addedBy, batchNo, dispatchType, totalAmount, `paymentType`) 
				VALUES ('$userId', '$batchNo', '$dispatchType', '$totalAmount', '$paymentType')";
				$this->db->query($insertBatch);
				if($changeField) {
					$userid = $this->session->userdata('logged');
					$changeFieldJson = json_encode($changeField);
					$aplog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
					$this->Comancontroler_model->add_data_in_table($aplog,'warehouse_dispatch_log'); 
				}
			}else{
				$sql="SELECT childInvoice,dispatchMeta FROM $table WHERE id=$id"; 
				$parentResult=$this->db->query($sql)->row();
				$childInvoices = $parentResult->childInvoice;
				$parentDispatchMeta = json_decode($parentResult->dispatchMeta, true);
				$otherChildInvoices = isset($parentDispatchMeta['otherChildInvoice']) ? $parentDispatchMeta['otherChildInvoice'] : '';
				if (!empty($childInvoices)) {
					$invoiceNumbers = explode(',', $childInvoices);
					foreach ($invoiceNumbers as $invoiceNumber) {
						$childChangeField = array();
						$invoiceNumber = trim($invoiceNumber); 
						if ($invoiceNumber != '') {
							$this->db->select('id, dispatchMeta, status, invoiceType');
							$this->db->where('invoice', $invoiceNumber);
							$childRow = $this->db->get($table)->row();
							if ($childRow) {
								$childId = $childRow->id;
								$childMeta = json_decode($childRow->dispatchMeta, true);
								if (!is_array($childMeta)) $childMeta = [];
								$oldPaidDate = $childMeta['invoicePaidDate'] ?? '';
								$oldCloseDate = $childMeta['invoiceCloseDate'] ?? '';
								$childMeta['invoicePaid'] = "1";
								$childMeta['invoicePaidDate'] = $invoicePaidDate;
								$childMeta['invoiceClose'] = "1";
								$childMeta['invoiceCloseDate'] = $invoicePaidDate;
								$existingChildStatus=$childRow->status;
								$updatedChildMeta = json_encode($childMeta);
								$childInvoiceType = $childRow->invoiceType;
								if ($childInvoiceType == 'Direct Bill') {
									$childStatus = 'DB Closed ' . $formatedPaidDate;
								} elseif ($childInvoiceType == 'Quick Pay') {
									$childStatus = 'QP Closed ' . $formatedPaidDate;
								} else {
									$childStatus = 'Closed ' . $formatedPaidDate;
								}
								$this->db->where('id', $childId);
								$this->db->update($table, [
									'gd' => 'AK',
									'dispatchMeta' => $updatedChildMeta,
									'status' => $childStatus,
									'receivableBatchId' => $batchId
								]);
								if($oldPaidDate != $invoicePaidDate){
									$otherChildChangeField[] = array('Invoice Paid Date','invoicePaidDate',$oldPaidDate,$invoicePaidDate);
								}
								if($oldCloseDate != $invoicePaidDate){
									$otherChildChangeField[] = array('Invoice Closed Date','invoiceCloseDate',$oldCloseDate,$invoicePaidDate);
								}
								if($existingChildStatus != $childStatus){
									$childChangeField[] = array('Status','status',$existingChildStatus,$childStatus);
								}
								if($childChangeField) {
								$userid = $this->session->userdata('logged');
								$childChangeFieldJson = json_encode($childChangeField);
								$childAplog = array('did'=>$childId,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$childChangeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
								if($table=='dispatchOutside'){
									$this->Comancontroler_model->add_data_in_table($childAplog,'dispatchOutsideLog'); 
								}else{
									$this->Comancontroler_model->add_data_in_table($childAplog,'dispatchLog'); 
								}
							}
							}
						}
					}
				}
				if (!empty($otherChildInvoices)) {
					$otherInvoiceNumbers = explode(',', $otherChildInvoices);
					foreach ($otherInvoiceNumbers as $invoiceNumber) {
						$otherChildChangeField = array();
						$invoiceNumber = trim($invoiceNumber); 
						if ($invoiceNumber != '') {
							if($table=='dispatchOutside'){
								$otherChildTable='dispatch';
							}else{
								$otherChildTable='dispatchOutside';
							}
							$this->db->select('id, dispatchMeta, status, invoiceType');
							$this->db->where('invoice', $invoiceNumber);
							$otherChildRow = $this->db->get($otherChildTable)->row();
							if ($otherChildRow) {
								$otherChildId = $otherChildRow->id;
								$otherChildMeta = json_decode($otherChildRow->dispatchMeta, true);
								if (!is_array($otherChildMeta)) $otherChildMeta = [];
								$oldPaidDate = $otherChildMeta['invoicePaidDate'] ?? '';
								$oldCloseDate = $otherChildMeta['invoiceCloseDate'] ?? '';
								$otherChildMeta['invoicePaid'] = "1";
								$otherChildMeta['invoicePaidDate'] = $invoicePaidDate;
								$otherChildMeta['invoiceClose'] = "1";
								$otherChildMeta['invoiceCloseDate'] = $invoicePaidDate;
								$existingOtherChildStatus=$otherChildRow->status;
								$updatedOtherChildMeta = json_encode($otherChildMeta);
								$otherChildInvoiceType = $otherChildRow->invoiceType;
								if ($otherChildInvoiceType == 'Direct Bill') {
									$childStatus = 'DB Closed ' . $formatedPaidDate;
								} elseif ($otherChildInvoiceType == 'Quick Pay') {
									$childStatus = 'QP Closed ' . $formatedPaidDate;
								} else {
									$childStatus = 'Closed ' . $formatedPaidDate;
								}
								$this->db->where('id', $otherChildId);
								$this->db->update($otherChildTable, [
									'gd' => 'AK',
									'dispatchMeta' => $updatedOtherChildMeta,
									'status' => $childStatus,
									'receivableBatchId' => $batchId
								]);
								if($oldPaidDate != $invoicePaidDate){
									$otherChildChangeField[] = array('Invoice Paid Date','invoicePaidDate',$oldPaidDate,$invoicePaidDate);
								}
								if($oldCloseDate != $invoicePaidDate){
									$otherChildChangeField[] = array('Invoice Closed Date','invoiceCloseDate',$oldCloseDate,$invoicePaidDate);
								}
								if($existingOtherChildStatus != $childStatus){
									$otherChildChangeField[] = array('Status','status',$existingChildStatus,$childStatus);
								}
								if($otherChildChangeField) {
									$userid = $this->session->userdata('logged');
									$otherChildChangeFieldJson = json_encode($otherChildChangeField);
									$otherChildAplog = array('did'=>$otherChildId,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$otherChildChangeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
									if($table=='dispatchOutside'){
										$this->Comancontroler_model->add_data_in_table($otherChildAplog,'dispatchLog'); 
									}else{
										$this->Comancontroler_model->add_data_in_table($otherChildAplog,'dispatchOutsideLog'); 
									}
								}
							}
						}
					}
				}

				$dispatchMeta['invoicePaid'] = "1";  
				$dispatchMeta['invoicePaidDate'] =$this->input->post('invoicePaidDate');
			
				$dispatchMeta['invoiceClose'] = "1";  
				$dispatchMeta['invoiceCloseDate'] =$this->input->post('invoicePaidDate');
				if($invoiceType == 'Direct Bill'){
					$status = 'DB Closed '.$formatedPaidDate;
				}else if($invoiceType == 'Quick Pay'){
					$status = 'QP Closed '.$formatedPaidDate;
				}else{
					$status = 'Closed '.$formatedPaidDate;
				}
				if($existingStatus != $status){
					$changeField[] = array('Status','status',$existingStatus,$status);
				}
				$dispatchMetaJson = json_encode($dispatchMeta);
				$this->db->select('parate');
				$this->db->where('id', $id);
				$totalAmount = $this->db->get($table)->row()->parate;

				// $sql = "UPDATE $table SET gd='AK', dispatchMeta='$dispatchMetaJson', receivableBatchId='$batchId' WHERE id=$id";
				// $result = $this->db->query($sql);
				$data = [
					'gd' => 'AK',
					'dispatchMeta' => $dispatchMetaJson,
					'status' => $status,
					'receivableBatchId' => $batchId
				];

				$this->db->where('id', $id);
				$result = $this->db->update($table, $data);

				$paymentType = 'receivable';
				$insertBatch = "INSERT INTO receivableBatches (addedBy, batchNo, dispatchType, totalAmount, `paymentType`) 
				VALUES ('$userId', '$batchNo', '$dispatchType', '$totalAmount', '$paymentType')";
				$this->db->query($insertBatch);
				if($changeField) {
					$userid = $this->session->userdata('logged');
					$changeFieldJson = json_encode($changeField);
					$aplog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
					if($table=='dispatchOutside'){
						$this->Comancontroler_model->add_data_in_table($aplog,'dispatchOutsideLog'); 
					}else{
						$this->Comancontroler_model->add_data_in_table($aplog,'dispatchLog'); 
					}
				}
			}
			
			// print_r($childInvoices);exit;
			
			if($result ){
				$this->session->set_flashdata('item', '	Updated successfully.');
			}
        } else {
			$this->session->set_flashdata('searchError', 'Something went wrong');
        }
	}
	public function updateBulkInvoices() {
		$invoiceIds = json_decode($this->input->post('invoice_ids'), true); 
		
		$invoicePaidDate = $this->input->post('invoicePaidDate');
		$formatedPaidDate = date('n/j/Y', strtotime($invoicePaidDate));
		$userId=$this->session->userdata('adminid');
		$table = $this->input->post('table');
		$fileUrl = '';
		if($table == 'dispatch'){
			$dispatchType = 'dispatch';
		}elseif($table == 'dispatchOutside'){
			$dispatchType = 'dispatchOutside';
		}elseif($table == 'warehouse_dispatch'){
			$dispatchType = 'warehouse_dispatch';
		}
		$batchNo = $this->input->post('batchNo');
		$batchId = $this->input->post('batchId');
		$total_Amount = $this->input->post('total_Amount');
		$uploadedFiles = [];
		
		if (!empty($_FILES['gd_d']['name'][0])) {
			$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|xlsm';
			$config['max_size'] = '5000';
			// $config['upload_path'] = 'assets/outside-dispatch/gd/';
			if($table == 'dispatch'){
				$config['upload_path'] = 'assets/upload/';
			}elseif($table == 'dispatchOutside'){
				$config['upload_path'] = 'assets/outside-dispatch/gd/';
			}elseif($table == 'warehouse_dispatch'){
				$config['upload_path'] = 'assets/warehouse/gd/';
			}
			
			$this->load->library('upload', $config);
		
			foreach ($_FILES['gd_d']['name'] as $key => $value) {
				$_FILES['file']['name'] = $_FILES['gd_d']['name'][$key];
				$_FILES['file']['type'] = $_FILES['gd_d']['type'][$key];
				$_FILES['file']['tmp_name'] = $_FILES['gd_d']['tmp_name'][$key];
				$_FILES['file']['error'] = $_FILES['gd_d']['error'][$key];
				$_FILES['file']['size'] = $_FILES['gd_d']['size'][$key];

				$fileName = time() . '-GD-' . uniqid();
				$config['file_name'] = $fileName;
				$this->upload->initialize($config);

				if ($this->upload->do_upload('file')) {
					$uploadData = $this->upload->data();
					$uploadedFiles[] = $uploadData['file_name'];
				}
			}
		}
	
		if (!empty($invoiceIds)) {
			// $this->db->select_sum('parate');
			// $this->db->where_in('id', $invoiceIds);
			// $totalAmount = $this->db->get($table)->row()->parate;
			$paymentType = 'receivable';
			$insertBatch = "INSERT INTO receivableBatches (addedBy, batchNo, dispatchType, totalAmount, `paymentType`) 
							VALUES ('$userId','$batchNo', '$dispatchType', '$total_Amount', '$paymentType')";
			$this->db->query($insertBatch);
			
			foreach ($invoiceIds as $id) {
				$changeField = [];
				if ($table == 'warehouse_dispatch') {
					$dispatchMeta_sql="SELECT invoicePaidDate, invoiceCloseDate, invoiceType, `status`  FROM $table WHERE id=$id";
					$dispatchMeta_result = $this->db->query($dispatchMeta_sql)->row();
					$invoiceType=$dispatchMeta_result->invoiceType;
					$existingStatus=$dispatchMeta_result->status;
					$existingInvoicePaidDate=$dispatchMeta_result->invoicePaidDate;
					$existingInvoiceCloseDate=$dispatchMeta_result->invoiceCloseDate;

					$sql = "SELECT * FROM sub_invoices WHERE parent_id = $id"; 
					$parentResult = $this->db->query($sql)->result_array();

					$warehouseChild = [];
					$fleetChild     = [];
					$logisticsChild = [];

					foreach ($parentResult as $row) {
						switch (strtolower($row['child_type'])) {
							case 'warehousing':
								$warehouseChild[] = $row['child_id'];
								break;
							case 'fleet':
								$fleetChild[] = $row['child_id'];
								break;
							case 'logistics':
								$logisticsChild[] = $row['child_id'];
								break;
						}
					}

					$allChildGroups = [
						'warehousing' => $warehouseChild,
						'fleet'       => $fleetChild,
						'logistics'   => $logisticsChild
					];

					foreach ($allChildGroups as $childType => $childIds) {
						if (empty($childIds)) continue;

						foreach ($childIds as $childId) {
							if ($childType === 'warehousing') {
								$otherChildTable = 'warehouse_dispatch';
							} elseif ($childType === 'fleet') {
								$otherChildTable = 'dispatch';
							} elseif ($childType === 'logistics') {
								$otherChildTable = 'dispatchOutside';
							}

							if ($childType === 'warehousing') {
								$this->db->select('id, invoicePaidDate, invoiceCloseDate, status, invoiceType');
								$this->db->where('id', $childId);
								$otherChildRow = $this->db->get($otherChildTable)->row();

								if ($otherChildRow) {
									$otherChildId = $otherChildRow->id;
									$oldPaidDate  = $otherChildRow->invoicePaidDate ?? '';
									$oldCloseDate = $otherChildRow->invoiceCloseDate ?? '';
									$existingStatus = $otherChildRow->status;
									$invoiceType    = $otherChildRow->invoiceType;

									if ($invoiceType == 'Direct Bill') {
										$childStatus = 'DB Closed ' . $formatedPaidDate;
									} elseif ($invoiceType == 'Quick Pay') {
										$childStatus = 'QP Closed ' . $formatedPaidDate;
									} else {
										$childStatus = 'Closed ' . $formatedPaidDate;
									}

									$data = [
										'gd'              => 'AK',
										'invoicePaid'     => "1",
										'invoicePaidDate' => $invoicePaidDate,
										'invoiceClose'    => "1",
										'invoiceCloseDate'=> $invoicePaidDate,
										'status'          => $childStatus,
										'receivableBatchId'=> $batchId
									];
									$this->db->where('id', $otherChildId);
									$this->db->update($otherChildTable, $data);

									$logChanges = [];
									if ($oldPaidDate != $invoicePaidDate) {
										$logChanges[] = ['Invoice Paid Date','invoicePaidDate',$oldPaidDate,$invoicePaidDate];
									}
									if ($oldCloseDate != $invoicePaidDate) {
										$logChanges[] = ['Invoice Closed Date','invoiceCloseDate',$oldCloseDate,$invoicePaidDate];
									}
									if ($existingStatus != $childStatus) {
										$logChanges[] = ['Status','status',$existingStatus,$childStatus];
									}
									if ($logChanges) {
										$userid = $this->session->userdata('logged');
										$aplog = [
											'did'       => $otherChildId,
											'userid'    => $userid['adminid'],
											'ip_address'=> getIpAddress(),
											'history'   => json_encode($logChanges),
											'rDate'     => date('Y-m-d H:i:s')
										];
										$this->Comancontroler_model->add_data_in_table($aplog,'warehouse_dispatch_log'); 
									}
								}
							} 
							else {
								$this->db->select('id, dispatchMeta, status, invoiceType');
								$this->db->where('id', $childId);
								$otherChildRow = $this->db->get($otherChildTable)->row();

								if ($otherChildRow) {
									$otherChildId   = $otherChildRow->id;
									$meta = json_decode($otherChildRow->dispatchMeta, true);
									if (!is_array($meta)) $meta = [];

									$oldPaidDate  = $meta['invoicePaidDate'] ?? '';
									$oldCloseDate = $meta['invoiceCloseDate'] ?? '';

									$meta['invoicePaid']     = "1";
									$meta['invoicePaidDate'] = $invoicePaidDate;
									$meta['invoiceClose']    = "1";
									$meta['invoiceCloseDate']= $invoicePaidDate;

									$invoiceType = $otherChildRow->invoiceType;
									if ($invoiceType == 'Direct Bill') {
										$childStatus = 'DB Closed ' . $formatedPaidDate;
									} elseif ($invoiceType == 'Quick Pay') {
										$childStatus = 'QP Closed ' . $formatedPaidDate;
									} else {
										$childStatus = 'Closed ' . $formatedPaidDate;
									}

									$this->db->where('id', $otherChildId);
									$this->db->update($otherChildTable, [
										'gd' => 'AK',
										'dispatchMeta' => json_encode($meta),
										'status' => $childStatus,
										'receivableBatchId' => $batchId
									]);

									$logChanges = [];
									if ($oldPaidDate != $invoicePaidDate) {
										$logChanges[] = ['Invoice Paid Date','invoicePaidDate',$oldPaidDate,$invoicePaidDate];
									}
									if ($oldCloseDate != $invoicePaidDate) {
										$logChanges[] = ['Invoice Closed Date','invoiceCloseDate',$oldCloseDate,$invoicePaidDate];
									}
									if ($otherChildRow->status != $childStatus) {
										$logChanges[] = ['Status','status',$otherChildRow->status,$childStatus];
									}
									if ($logChanges) {
										$userid = $this->session->userdata('logged');
										$aplog = [
											'did'       => $otherChildId,
											'userid'    => $userid['adminid'],
											'ip_address'=> getIpAddress(),
											'history'   => json_encode($logChanges),
											'rDate'     => date('Y-m-d H:i:s')
										];
										if ($childType === 'fleet') {
											$this->Comancontroler_model->add_data_in_table($aplog,'dispatchLog'); 
										} elseif ($childType === 'logistics') {
											$this->Comancontroler_model->add_data_in_table($aplog,'dispatchOutsideLog'); 
										}
									}
								}
							}
						}
					}

					if($invoiceType == 'Direct Bill'){
						$status = 'DB Closed '.$formatedPaidDate;
					}else if($invoiceType == 'Quick Pay'){
						$status = 'QP Closed '.$formatedPaidDate;
					}else{
						$status = 'Closed '.$formatedPaidDate;
					}
					if($existingStatus != $status){
						$changeField[] = array('Status','status',$existingStatus,$status);
					}
					$dispatchMetaJson = json_encode($dispatchMeta);
					$this->db->select('parate');
					$this->db->where('id', $id);
					$totalAmount = $this->db->get($table)->row()->parate;

					$data = [
						'gd' => 'AK',
						'invoicePaid' => "1",
						'invoicePaidDate'   => $invoicePaidDate,
						'invoiceClose'   => "1",
						'invoiceCloseDate'   => $invoicePaidDate,
						'status' => $status,
						'receivableBatchId' => $batchId
					];

					$this->db->where('id', $id);
					$result = $this->db->update($table, $data);

					if (!empty($uploadedFiles)) {
						foreach ($uploadedFiles as $fileUrl) {
							$addfile = array(
								'did' => $id,
								'type' => 'gd',
								'fileurl' => $fileUrl,
								'rdate' => date('Y-m-d H:i:s')
							);
							$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
							$changeField[] = array("Payment proof file", "gdfile", "Upload", $fileUrl);
						}
					}

					if($existingInvoicePaidDate != $invoicePaidDate){
						$changeField[] = array('Invoice Paid Date','invoicePaidDate',$existingInvoicePaidDate,$invoicePaidDate);
					}
					if($existingInvoiceCloseDate != $invoicePaidDate){
						$changeField[] = array('Invoice Closed Date','invoiceCloseDate',$existingInvoiceCloseDate,$invoicePaidDate);
					}				
					if($changeField) {
						$userid = $this->session->userdata('logged');
						$changeFieldJson = json_encode($changeField);
						$aplog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
						$this->Comancontroler_model->add_data_in_table($aplog,'warehouse_dispatch_log'); 
					}
				}else{
					$dispatchMeta_sql="SELECT childInvoice, dispatchMeta, invoiceType, `status`  FROM $table WHERE id=$id";
					$dispatchMeta_result = $this->db->query($dispatchMeta_sql)->row();
					$invoiceType=$dispatchMeta_result->invoiceType;
					$existingStatus=$dispatchMeta_result->status;

					$current_metaData=$dispatchMeta_result->dispatchMeta;
					$dispatchMeta = json_decode($current_metaData, true); 
					$existingInvoicePaidDate=$dispatchMeta['invoicePaidDate'];
					$existingInvoiceCloseDate=$dispatchMeta['invoiceCloseDate'];

					$childInvoices = $dispatchMeta_result->childInvoice;
					$otherChildInvoices = isset($dispatchMeta['otherChildInvoice']) ? $dispatchMeta['otherChildInvoice'] : '';
					// print_r($childInvoices);exit;
					if (!empty($childInvoices)) {
						$invoiceNumbers = explode(',', $childInvoices);
						foreach ($invoiceNumbers as $invoiceNumber) {
							$childChangeField = array();
							$invoiceNumber = trim($invoiceNumber); 
							if ($invoiceNumber != '') {
								$this->db->select('id, dispatchMeta, status, invoiceType');
								$this->db->where('invoice', $invoiceNumber);
								$childRow = $this->db->get($table)->row();
								if ($childRow) {
									$childId = $childRow->id;
									$childMeta = json_decode($childRow->dispatchMeta, true);
									if (!is_array($childMeta)) $childMeta = [];
									$oldPaidDate = $childMeta['invoicePaidDate'] ?? '';
									$oldCloseDate = $childMeta['invoiceCloseDate'] ?? '';
									$childMeta['invoicePaid'] = "1";
									$childMeta['invoicePaidDate'] = $invoicePaidDate;
									$childMeta['invoiceClose'] = "1";
									$childMeta['invoiceCloseDate'] = $invoicePaidDate;
									$existingChildStatus=$childRow->status;
									$updatedChildMeta = json_encode($childMeta);
									$childInvoiceType = $childRow->invoiceType;
									if ($childInvoiceType == 'Direct Bill') {
										$childStatus = 'DB Closed ' . $formatedPaidDate;
									} elseif ($childInvoiceType == 'Quick Pay') {
										$childStatus = 'QP Closed ' . $formatedPaidDate;
									} else {
										$childStatus = 'Closed ' . $formatedPaidDate;
									}
									$this->db->where('id', $childId);
									$this->db->update($table, [
										'gd' => 'AK',
										'dispatchMeta' => $updatedChildMeta,
										'status' => $childStatus,
										'receivableBatchId' => $batchId
									]);
									if($oldPaidDate != $invoicePaidDate){
										$childChangeField[] = array('Invoice Paid Date','invoicePaidDate',$oldPaidDate,$invoicePaidDate);
									}
									if($oldCloseDate != $invoicePaidDate){
										$childChangeField[] = array('Invoice Closed Date','invoiceCloseDate',$oldCloseDate,$invoicePaidDate);
									}
									if($existingChildStatus != $childStatus){
										$childChangeField[] = array('Status','status',$existingChildStatus,$childStatus);
									}
									if($childChangeField) {
									$userid = $this->session->userdata('logged');
									$childChangeFieldJson = json_encode($childChangeField);
									$childAplog = array('did'=>$childId,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$childChangeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
									if($table=='dispatchOutside'){
										$this->Comancontroler_model->add_data_in_table($childAplog,'dispatchOutsideLog'); 
									}else{
										$this->Comancontroler_model->add_data_in_table($childAplog,'dispatchLog'); 
									}
								}
								}
							}
						}
					}
					if (!empty($otherChildInvoices)) {
						$otherInvoiceNumbers = explode(',', $otherChildInvoices);
						foreach ($otherInvoiceNumbers as $invoiceNumber) {
							$otherChildChangeField = array();
							$invoiceNumber = trim($invoiceNumber); 
							if ($invoiceNumber != '') {
								if($table=='dispatchOutside'){
									$otherChildTable='dispatch';
								}else{
									$otherChildTable='dispatchOutside';
								}
								$this->db->select('id, dispatchMeta, status, invoiceType');
								$this->db->where('invoice', $invoiceNumber);
								$otherChildRow = $this->db->get($otherChildTable)->row();
								if ($otherChildRow) {
									$otherChildId = $otherChildRow->id;
									$otherChildMeta = json_decode($otherChildRow->dispatchMeta, true);
									if (!is_array($otherChildMeta)) $otherChildMeta = [];
									$oldPaidDate = $otherChildMeta['invoicePaidDate'] ?? '';
									$oldCloseDate = $otherChildMeta['invoiceCloseDate'] ?? '';
									$otherChildMeta['invoicePaid'] = "1";
									$otherChildMeta['invoicePaidDate'] = $invoicePaidDate;
									$otherChildMeta['invoiceClose'] = "1";
									$otherChildMeta['invoiceCloseDate'] = $invoicePaidDate;
									$existingOtherChildStatus=$otherChildRow->status;
									$updatedOtherChildMeta = json_encode($otherChildMeta);
									$otherChildInvoiceType = $otherChildRow->invoiceType;
									if ($otherChildInvoiceType == 'Direct Bill') {
										$childStatus = 'DB Closed ' . $formatedPaidDate;
									} elseif ($otherChildInvoiceType == 'Quick Pay') {
										$childStatus = 'QP Closed ' . $formatedPaidDate;
									} else {
										$childStatus = 'Closed ' . $formatedPaidDate;
									}
									$this->db->where('id', $otherChildId);
									$this->db->update($otherChildTable, [
										'gd' => 'AK',
										'dispatchMeta' => $updatedOtherChildMeta,
										'status' => $childStatus,
										'receivableBatchId' => $batchId
									]);
									if($oldPaidDate != $invoicePaidDate){
										$otherChildChangeField[] = array('Invoice Paid Date','invoicePaidDate',$oldPaidDate,$invoicePaidDate);
									}
									if($oldCloseDate != $invoicePaidDate){
										$otherChildChangeField[] = array('Invoice Closed Date','invoiceCloseDate',$oldCloseDate,$invoicePaidDate);
									}
									if($existingOtherChildStatus != $childStatus){
										$otherChildChangeField[] = array('Status','status',$existingChildStatus,$childStatus);
									}
									if($otherChildChangeField) {
										$userid = $this->session->userdata('logged');
										$otherChildChangeFieldJson = json_encode($otherChildChangeField);
										$otherChildAplog = array('did'=>$otherChildId,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$otherChildChangeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
										if($table=='dispatchOutside'){
											$this->Comancontroler_model->add_data_in_table($otherChildAplog,'dispatchLog'); 
										}else{
											$this->Comancontroler_model->add_data_in_table($otherChildAplog,'dispatchOutsideLog'); 
										}
									}
								}
							}
						}
					}


					$dispatchMeta['invoicePaid'] = "1";  
					$dispatchMeta['invoicePaidDate'] =$this->input->post('invoicePaidDate');
					
					$dispatchMeta['invoiceClose'] = "1";  
					$dispatchMeta['invoiceCloseDate'] =$this->input->post('invoicePaidDate');
					
					$dispatchMetaJson = json_encode($dispatchMeta);
					if($invoiceType == 'Direct Bill'){
						$status = 'DB Closed '.$formatedPaidDate;
					}else if($invoiceType == 'Quick Pay'){
						$status = 'QP Closed '.$formatedPaidDate;
					}else{
						$status = 'Closed '.$formatedPaidDate;
					}
					if($existingStatus != $status){
						$changeField[] = array('Status','status',$existingStatus,$status);
					}
					// $sql = "UPDATE $table SET gd='AK', dispatchMeta='$dispatchMetaJson', receivableBatchId='$batchId' WHERE id=$id";
					// $this->db->query($sql);
					$this->db->where('id', $id);
					$this->db->update($table, [
						'gd' => 'AK',
						'dispatchMeta' => $dispatchMetaJson,
						'status' => $status,
						'receivableBatchId' => $batchId
					]);


					if (!empty($uploadedFiles)) {
						foreach ($uploadedFiles as $fileUrl) {
							$addfile = array(
								'did' => $id,
								'type' => 'gd',
								'fileurl' => $fileUrl,
								'rdate' => date('Y-m-d H:i:s')
							);
							if($table=='dispatchOutside'){
								$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
							} else {
								$this->Comancontroler_model->add_data_in_table($addfile,'documents');
							}

							// Log each file upload
							$changeField[] = array("Payment proof file", "gdfile", "Upload", $fileUrl);
						}
					}

					if($existingInvoicePaidDate != $invoicePaidDate){
						$changeField[] = array('Invoice Paid Date','invoicePaidDate',$existingInvoicePaidDate,$invoicePaidDate);
					}
					if($existingInvoiceCloseDate != $invoicePaidDate){
						$changeField[] = array('Invoice Closed Date','invoiceCloseDate',$existingInvoiceCloseDate,$invoicePaidDate);
					}				
					if($changeField) {
						$userid = $this->session->userdata('logged');
						$changeFieldJson = json_encode($changeField);
						$aplog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
						if($table=='dispatchOutside'){
							$this->Comancontroler_model->add_data_in_table($aplog,'dispatchOutsideLog'); 
						}else{
							$this->Comancontroler_model->add_data_in_table($aplog,'dispatchLog'); 
						}
					}
				}
			}
	
			$this->session->set_flashdata('item', 'Invoices updated successfully.');
		} else {
			$this->session->set_flashdata('searchError', 'No invoices selected.');
		}
	
		redirect($_SERVER['HTTP_REFERER']);
		// echo json_encode($response);
		exit;
	}
	public function downloadStatementPDF(){ 
	    if(!checkPermission($this->session->userdata('permission'),'statementAcc')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $this->load->library('pdf');
        $pdf = $this->pdf->load(); 
		$id = $this->uri->segment(3);
		
		$data['invoice'] = array();
		$invoice = date('Y-m-d-H-i');
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$agingSearch = $_GET['agingSearch'];
		$invoiceType = $_GET['invoiceType'];
		$customerStatus = $_GET['status'];
		$invoiceNo = $_GET['invoiceNo'];
		$shippingContact = $_GET['shippingContact'];
		$invoiceIds = $this->input->get('invoice_ids'); 
		if ($invoiceIds) {
			$invoiceIds = explode(',', $invoiceIds); 
		}
		$agingFrom = $_GET['agingFrom'];
		$agingTo = $_GET['agingTo'];

		$table = 'dispatch';
		$extraTable = 'dispatchExtraInfo';
		$data['type'] = '';
		if(isset($_GET['shippingContact'])){
			$data['company'] = $this->db->query("SELECT companies.company, companies.address, contacts.contact_person  as contactPerson,  contacts.designation, contacts.department, contacts.email, contacts.phone 
			FROM companies LEFT JOIN company_shipping_contacts contacts ON companies.id=contacts.company_id AND contacts.id=$shippingContact
			WHERE companies.id=$id")->result_array();
		}else{
			$data['company'] = $this->Comancontroler_model->get_data_by_id($id,'companies');
		}
		if(isset($_GET['dTable']) && $_GET['dTable'] == 'dispatchOutside'){ 
			$table = 'dispatchOutside'; $extraTable = 'dispatchOutsideExtraInfo';
		}elseif(isset($_GET['dTable']) && $_GET['dTable'] == 'warehouse_dispatch'){
			$table = 'warehouse_dispatch'; $extraTable = 'warehouse_dispatch_extra_info';
		} 
		if(isset($_GET['type']) && $_GET['type'] != ''){ $data['type'] = $_GET['type']; }
		
		$data['invoice'] = $this->Accountreceivable_model->getReceivableStatement($table,$sdate,$edate,$id,$agingSearch,$invoiceType,$customerStatus, $invoiceNo,$agingFrom, $agingTo, $invoiceIds);
		if($data['invoice']){
    	    for($i=0;count($data['invoice']) > $i;$i++){
    	        $dispatchInfo = $this->Comancontroler_model->get_dispatchinfo_by_id($data['invoice'][$i]['id'],'pd_date',$extraTable);
				if($dispatchInfo){
					foreach($dispatchInfo as $dis){
						$data['invoice'][$i]['pd_date'] = $dis['pd_date'];
					}
				} else {
				    $data['invoice'][$i]['pd_date'] = '';
				}
    	    }
    	}
    	$data['table']=$table;
    	////// generate csv 
	    
    	if(isset($_GET['generateCSV']) || isset($_GET['generateXls'])){
    		$colspan = 5; 
    		
    		if($data['type'] == 'Drayage'){
    			$colspan = 6; 
    			$heading = array('Shipment Date','PA Invoice No.','Cust Ref. No.','Container No. / Trailer','Invoice Date','Inv. Aging (Days)','Amount');
    		} else {
    			$heading = array('Shipment Date','PA Invoice No.','Cust Ref. No.','Invoice Date','Inv. Aging (Days)','Amount');
    		}
    		$csvExcel = array($heading);
    		
    		if($data['invoice']){
    			$i = 1;
    			$partialAmt = $amount = 0;
    			foreach($data['invoice'] as $dis){

    				$dispatchMeta = json_decode($dis['dispatchMeta'],true);
					if($table == 'warehouse_dispatch'){
						$partialAmt = $partialAmt + $dis['partialAmount'];
					} else{
						if(is_numeric($dispatchMeta['partialAmount'])) {
							$partialAmt = $partialAmt + $dispatchMeta['partialAmount'];
						}
					}
					if($table == 'warehouse_dispatch'){
						if($data['type'] != '' && $dis['invoicePDF'] != $data['type']){
							continue;
						}
					} else{
						if($data['type'] != '' && $dispatchMeta['invoicePDF'] != $data['type']){
							continue;
						}
					}

    				
    				if($dis['pd_date'] != '' && (!strstr($dis['pd_date'],'0000'))) {
    					$dis['pudate'] = $dis['pd_date'];
    				}
    				$amount = $amount + $dis['parate'];
    				$rowArr = array(date('m-d-Y',strtotime($dis['pudate'])),$dis['invoice'],$dis['tracking']);
    				if($data['type'] == 'Drayage') { $rowArr[] = str_replace('TBA','N/A',$dis['trailer']); }
    				$rowArr[] = date('m-d-Y',strtotime($dis['invoiceDate']));
    				
    				$invoiceType = $agingTxt = '';
    				$showAging = 'false';
    				$aDays = 0;
    				if($dis['invoiceType']=='Direct Bill'){  $aDays = 30; }
    				elseif($dis['invoiceType']=='Quick Pay'){ $aDays = 7; }
    				elseif($dis['invoiceType']=='RTS'){ $aDays = 3; }
    				
    				if($dis['invoiceDate'] != '0000-00-00'){ $showAging = 'true'; }
    				

					if($table == 'warehouse_dispatch'){
						if($dis['invoicePaidDate'] != '0000-00-00'){ $showAging = 'false';  }
    					if($dis['invoiceCloseDate'] != '0000-00-00'){ $showAging='false';  }
						// echo $showAging;exit;
					} else{
						if($dispatchMeta['invoicePaidDate'] != ''){ $showAging = 'false';  }
    					if($dispatchMeta['invoiceCloseDate'] != ''){ $showAging='false';  }
					}

    				if($showAging == 'true'){
    					$date1 = new DateTime($dis['invoiceDate']);
    					$date2 = new DateTime(date('Y-m-d'));
    					$diff = $date1->diff($date2);
    					$aging = $diff->days;
    					
    					if($aging  > $aDays && $aDays > 0) { $agingTxt = ''.$aging.' Days'; }
    					else { $agingTxt = ''.$aging.' Days'; }
    				}
    
    				$rowArr[] = $agingTxt;
    				$rowArr[] = '$ '.$dis['parate'];
    				
    				$csvExcel[] = $rowArr;
    			}
    			$rowArr = array('','','','');
    			if($data['type'] == 'Drayage') { $rowArr[] = ''; }
    			$rowArr[] = ''; $rowArr[] = '';
    			$csvExcel[] = $rowArr;
    			
    			$subTotalRow = array('','','','');
    			if($data['type'] == 'Drayage') { $subTotalRow[] = ''; }
    			$subTotalRow[] = 'Subtotal'; $subTotalRow[] = number_format($amount,2);
    			$csvExcel[] = $subTotalRow;
    			
    			$totalAmt = $amount;
    			if($partialAmt > 0) {
    				$partialAmtRow = array('','','','');
    				if($data['type'] == 'Drayage') { $partialAmtRow[] = ''; }
    				$partialAmtRow[] = 'Partial Amount'; $partialAmtRow[] = number_format($partialAmt,2);
    				$csvExcel[] = $partialAmtRow;
    				$totalAmt = $totalAmt - $partialAmt;
    			}
    			
    			$totalRow = array('','','','');
    			if($data['type'] == 'Drayage') { $totalRow[] = ''; }
    			$totalRow[] = 'Total Amount Due'; $totalRow[] = number_format($totalAmt,2);
    			$csvExcel[] = $totalRow;
    		}
    		
    		
    		if(isset($_GET['generateCSV'])){
    			$fileName = 'Statement of Accounts - '.$data['company'][0]['company'].' '.date('m-d-Y').'.csv';
    			// Open the file for writing
    			$file = fopen($fileName, 'w');
    
    			// Write data to the CSV file
    			foreach ($csvExcel as $row) {
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
    		if(isset($_GET['generateXls'])){
    			$this->load->library('excel_generator');
    			$fileName = 'Statement of Accounts - '.$data['company'][0]['company'].' '.date('m-d-Y').".xlsx";   //"data_$date.xlsx";
    			// Generate Excel file using the library
    			$this->excel_generator->generateExcel($csvExcel, $fileName);
    		}
    
    		// Delete the file from the server
    		unlink($fileName);
    		exit;
    		die('csv');
    	}
		
		
		$file = 'statementPDF';
		
		$html = $this->load->view('admin/'.$file, $data, true);
		//echo $html;die();
		
		$stylesheet = "";
		//$pdf->WriteHTML($stylesheet, 1);
		//$pdf->SetAutoPageBreak(true, 10);
		$pdf->WriteHTML($html);
        // write the HTML into the PDF
        $output = 'Statement of Accounts - '.$data['company'][0]['company'].' '.date('m-d-Y').'.pdf';
		$pdf->Output($output, "D");
		exit;
	}

	public function getInvoiceFileUrls()
	{
		$id = $this->uri->segment(3);

		$invoiceIds = $this->input->get('invoice_ids');
		if ($invoiceIds) {
			$invoiceIds = explode(',', $invoiceIds);
		}

		$table         = 'dispatch';
		$documentTable = 'documents';
		$folderPath    = 'assets/paInvoice/';

		if ($this->input->get('dTable') === 'dispatchOutside') {
			$table         = 'dispatchOutside';
			$documentTable = 'documentsOutside';
			$folderPath    = 'assets/outside-dispatch/invoice/';
		}else if($this->input->get('dTable') === 'warehouse_dispatch'){
			$table         = 'warehouse_dispatch';
			$documentTable = 'warehouse_documents';
			$folderPath    = 'assets/warehouse/invoice/';
		}

		$data['invoice'] = $this->Accountreceivable_model->getReceivableStatement(
			$table,
			$this->input->get('sdate'),
			$this->input->get('edate'),
			$id,
			$this->input->get('agingSearch'),
			$this->input->get('invoiceType'),
			$this->input->get('status'),
			$this->input->get('invoiceNo'),
			$this->input->get('agingFrom'),
			$this->input->get('agingTo'),
			$invoiceIds
		);

		$idString = implode(',', array_column($data['invoice'], 'id'));

		$docs = $this->db->query("
			SELECT d.fileurl, inv.invoice as invoiceno
			FROM $documentTable d
			JOIN $table inv ON d.did = inv.id
			WHERE d.did IN ($idString) 
			AND d.type='paInvoice'
		")->result_array();
		$files = [];
		foreach ($docs as $doc) {
			$files[] = [
				'url'  => base_url($folderPath . $doc['fileurl']),
				'name' => 'Invoice # ' . $doc['invoiceno'] . '.pdf'
			];
		}
		echo json_encode($files);
	}	
	public function receivableBatches(){
	    if(!checkPermission($this->session->userdata('permission'),'statementAcc')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    
        //$sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        //$edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
        $company = $truckingCompany = $driver = $sdate = $edate = '';
        $data['dispatchURL'] = 'dispatch';
		$company = $this->input->post('company');
		
		$truckingCompany = $this->input->post('truckingCompany');
        $driver = $this->input->post('driver');
        $dispatchType = $this->input->post('dispatchType');
        if($dispatchType == 'outsideDispatch'){ 
            $table = 'dispatchOutside'; 
            $data['dispatchURL'] = 'outside-dispatch';
        }else if($dispatchType == 'warehouse_dispatch'){
			$table = 'warehouse_dispatch'; 
            $data['dispatchURL'] = 'paWarehouse';
		}
        else { $table = 'dispatch'; }        
        $week = $this->input->post('week');
        if($week!='' && $week!='all'){
            $weeks = explode(',',$week);
            $sdate = $weeks[0];
            $edate = $weeks[1];
        }
        if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
        if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 

		if($this->input->post('generateCSV') || $this->input->post('generateXls')){
			$agingFrom = $this->input->post('aging_from');
			$agingTo = $this->input->post('aging_to');

            $dispatch = $this->Accountreceivable_model->downloadReceivableBatches($table,$sdate,$edate,$company, $agingFrom,$agingTo,'no');
			if(($dispatchType == 'outsideDispatch')){
				$heading = array('Added By','Company','Received Date','Batch No', 'Batch Date','Invoice','Carrier Ref #','Carrier Inv. Ref #','Delivery Date','Invoice Date','Invoice Amount','Received Days');
			}else if($dispatchType == 'warehouse_dispatch'){
				$heading = array('Added By','Company','Received Date','Batch No', 'Batch Date','Invoice','Carrier Ref #','Carrier Inv. Ref #','End Date','Invoice Date','Invoice Amount','Received Days');
			}else{
				$heading = array('Added By','Company','Received Date','Batch No', 'Batch Date','Invoice','Carrier Ref #','Delivery Date','Invoice Date','Invoice Amount','Received Days');
			}
			
			$data = array($heading);
			// print_r($dispatch);exit;
			$totalPaRate = 0;
			if(!empty($dispatch)) {	
				foreach($dispatch as $row){
					$dispatchMeta = json_decode($row['dispatchMeta'],true);
				   	$edate = $dodate = $carrierInvoiceDate = $expectPayDate = $carrierPayoutDate = '0000-00-00';
				   
					if($dispatchType == 'warehouse_dispatch'){
						if(($row['edate']!='0000-00-00') || ($row['edate']!='')) {
							$dodate = '="' . date('m-d-Y', strtotime($row['edate'])) . '"';
						}
					}else{
						if($row['dodate']!='0000-00-00') {
							$dodate = '="' . date('m-d-Y', strtotime($row['dodate'])) . '"';
						}
					}
				   	if($row['paid_date']!='0000-00-00') {
					   $receivedDate = '="' . date('m-d-Y', strtotime($row['paid_date'])) . '"';
				   	}
				   	$batchDateBatchNo = date('mdY', strtotime($row['date'])) . $row['batchNo'];
				   	$batcDate = date('m-d-Y h:i:s A', strtotime($row['date']));
				    $CarrierRefNo = '';
					if($dispatchType == 'warehouse_dispatch'){
						$CarrierRefNo = $row['dispatchValue'];
					}else{
						if($dispatchMeta['dispatchInfo'][0][0] == 'Carrier Ref No'){
							$CarrierRefNo = $dispatchMeta['dispatchInfo'][0][1];
						}
					}
					
					if($row['invoiceDate']!='0000-00-00'){ 
						$invoiceDate = '="' . date('m-d-Y', strtotime($row['invoiceDate'])) . '"';
					}
					$parate = (float)$row['parate']; // cast to number
        			$totalPaRate += $parate;
					if(($dispatchType == 'outsideDispatch') || ($dispatchType == 'warehouse_dispatch')){
						$dataRow = array($row['added_by'], $row['company'], $receivedDate, $batchDateBatchNo, $batcDate, $row['invoice'] , $CarrierRefNo, $row['carrierInvoiceRefNo'], $dodate, $invoiceDate, $parate, $row['received_days']);
					}else{
						$dataRow = array($row['added_by'], $row['company'], $receivedDate, $batchDateBatchNo, $batcDate, $row['invoice'] , $CarrierRefNo, $dodate, $invoiceDate, $parate, $row['received_days']);
					}
				   	

					$data[] = $dataRow;
				}
				if(($dispatchType == 'outsideDispatch') || ($dispatchType == 'warehouse_dispatch')){
					$totalRow = array('', '', '', '', '', '', '', '', '', 'Total:', number_format($totalPaRate,2), '');
				}else{
					$totalRow = array('', '', '', '', '', '', '', '', 'Total:', number_format($totalPaRate,2), '');
				}
			    $data[] = $totalRow;
			}
            
			if($this->input->post('generateCSV')){
				$fileName = "Receivable_Batches_Report.csv"; 
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
			if($this->input->post('generateXls')){
				$this->load->library('excel_generator');
				$fileName = "Receivable_Batches_Report.xlsx";   //"data_$date.xlsx";
				$this->excel_generator->generateExcel($data, $fileName);
			}

			unlink($fileName);
			exit;
			die('csv');
        }

        if($this->input->post('search'))	{
			$agingFrom = $this->input->post('aging_from');
			$agingTo = $this->input->post('aging_to');

			$data['dispatch'] = $this->Accountreceivable_model->getReceivableBatches($table,$sdate,$edate,$company,$agingFrom,$agingTo,'no');
        } else {
            $data['dispatch'] = array();
        }
        
    	
		$data['drivers'] = array(); 
		$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
		$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies');
		$data['locations'] = array(); //$this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = array(); //$this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = array(); //$this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/receivable_batches',$data);
    	$this->load->view('admin/layout/footer');
	}

	public function nextBatchNo(){
		$batchSql = "SELECT MAX(id) as id, MAX(batchNo) as batchNo FROM receivableBatches";
		$lastBatch = $this->db->query($batchSql)->row();

		$output="";
		if (($lastBatch)) {
			$batchNo = sprintf('%04d', intval($lastBatch->batchNo) + 1);
			$batchId = (!empty($lastBatch->id)) ? $lastBatch->id + 1 : 1;

			$msg = array(
				'batchNo' => $batchNo,
				'batchId' => $batchId
			);			     
			$error = 0;  
            }else{
                $msg = 'OPERATION FAILED';
                $error = 1;  
            }
            $output = array(
                    'error' =>$error,
                    'msg' =>$msg
                );
            echo json_encode($output);
        die; 
	}

	public function getAgingDaysCounts()
	{
		$data = [
			'DaysCount'     => $this->getAgingDaysCount()
		];
		echo json_encode($data);
	}
	public function getAgingDaysCount()
	{
		
		$where='1=1';
		$company = $this->input->post('company');
		$dispatchType = $this->input->post('dispatchType');
		$invoiceType = $this->input->post('invoiceType');
		$customerStatus = $this->input->post('status');
		$invoiceNo = $this->input->post('invoiceNo');

		if($dispatchType == 'paDispatch'){ 
			$table = 'dispatch'; 
		}
		elseif($dispatchType == 'outsideDispatch') { 
			$table = 'dispatchOutside';
		}elseif($dispatchType == 'warehouse_dispatch') { 
			$table = 'warehouse_dispatch';
		}
		if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
		if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
		if($sdate!='') { 
			$where .= " AND pudate >= '$sdate'";
		}
		if($edate!='') { 
			$where .= " AND pudate <= '$edate'"; 
		}
		if($company!='') { 
			$where .= " AND a.company in  (" . implode(",", $company) . ")"; 
		}
		if($invoiceType != ''){
				$where .= " AND a.invoiceType='$invoiceType'";
		}else{
			$where .=" AND a.invoiceType IN ('Direct Bill', 'Quick Pay')";
		}
		if($customerStatus != ''){
			$where .= " AND c.status='$customerStatus'";
		}else{
			$where .=" AND c.status='Active'";
		}
		if($invoiceNo != ''){
			$where .= " AND a.invoice LIKE '%$invoiceNo%'";
		}
		if($dispatchType == 'warehouse_dispatch'){
			$sql="SELECT 
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 0 AND 15 THEN 1 ELSE 0 END) AS `zero_fifteen_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 0 AND 15 THEN a.parate ELSE 0 END) AS `zero_fifteen_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `fifteen_thirty_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 16 AND 30 THEN a.parate ELSE 0 END) AS `fifteen_thirty_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 35 THEN 1 ELSE 0 END) AS `thirty_thirty_five_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 35 THEN a.parate ELSE 0 END) AS `thirty_thirty_five_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 36 AND 45 THEN 1 ELSE 0 END) AS `thirty_five_forty_five_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 36 AND 45 THEN a.parate ELSE 0 END) AS `thirty_five_forty_five_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) AS `forty_five_sixty_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 46 AND 60 THEN a.parate ELSE 0 END) AS `forty_five_sixty_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) > 60 THEN 1 ELSE 0 END) AS `sixty_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) > 60 THEN a.parate ELSE 0 END) AS `sixty_days_amount`
			FROM 
			$table a
			JOIN companies c ON c.id = a.company
			WHERE $where  
			AND a.invoiced = '1'
			AND (a.invoiceClose = '0' || a.invoiceClose = '')
			AND (a.invoiceCloseDate = '' || a.invoiceCloseDate = '0000-00-00')
			AND (a.invoicePaid = '0' || a.invoicePaid = '')
			AND (a.invoicePaidDate = '' || a.invoicePaidDate = '0000-00-00')
			ORDER BY 
			a.pudate ASC
			LIMIT 1000";
		}else{
			$sql="SELECT 
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 0 AND 15 THEN 1 ELSE 0 END) AS `zero_fifteen_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 0 AND 15 THEN a.parate ELSE 0 END) AS `zero_fifteen_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `fifteen_thirty_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 16 AND 30 THEN a.parate ELSE 0 END) AS `fifteen_thirty_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 35 THEN 1 ELSE 0 END) AS `thirty_thirty_five_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 35 THEN a.parate ELSE 0 END) AS `thirty_thirty_five_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 36 AND 45 THEN 1 ELSE 0 END) AS `thirty_five_forty_five_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 36 AND 45 THEN a.parate ELSE 0 END) AS `thirty_five_forty_five_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) AS `forty_five_sixty_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 46 AND 60 THEN a.parate ELSE 0 END) AS `forty_five_sixty_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) > 60 THEN 1 ELSE 0 END) AS `sixty_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) > 60 THEN a.parate ELSE 0 END) AS `sixty_days_amount`
			FROM 
			$table a
			JOIN companies c ON c.id = a.company
			WHERE $where  
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiced') = '1'
			AND (JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceClose') = '0' ||
				JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceClose') = '')
			AND a.invoiceDate != '0000-00-00'  
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceCloseDate') = ''
			AND (JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '0' ||
				JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '')
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') = ''
			ORDER BY 
			a.pudate ASC
			LIMIT 1000";
		}
		// echo $sql;exit;
		$query = $this->db->query($sql);
		return $query->row(); 
	}

	public function exportReceivables(){
		$invoiceIds = json_decode($this->input->post('export_invoice_ids'), true);
		$table = str_replace('"', '', $this->input->post('table'));
		$dispatch = $this->getReceivableInvoiceData($invoiceIds, $table);
		if($table=='dispatchOutside'){
			$heading = array('Company', 'Invoice', 'Delivery Date', 'Carrier Invoice Date', 'Carrier Invoice Ref NO', 'Carrier Rate', 'Invoice Amount', 'Aging Days');
		}else if($table=='warehouse_dispatch'){
			$heading = array('Company', 'Invoice', 'End Date', 'SP Invoice Date', 'SP Invoice Ref NO', 'SP Rate', 'Invoice Amount', 'Aging Days');
		}
		else{
			$heading = array('Company', 'Invoice', 'Delivery Date', 'Carrier Invoice Date', 'Carrier Rate', 'Invoice Amount','Aging Days');
		}
						
			$data = array($heading);
			if(!empty($dispatch)) {
				$rate = 0;
				$parate = 0;
				$totalRate = 0;
				$totalPaRate = 0;
				foreach($dispatch as $row){
					$dispatchMeta = json_decode($row['dispatchMeta'],true);
					$dodate ='0000-00-00';
				   if($row['dodate']!='0000-00-00') {
   					   	$dodate = '="' . date('m-d-Y', strtotime($row['dodate'])) . '"';
				   }
				   if($table=='warehouse_dispatch'){
						if($row['edate']!='0000-00-00') {
							$dodate = '="' . date('m-d-Y', strtotime($row['edate'])) . '"';
						}
				   }
				   $invoiceDate ='0000-00-00';
				   if($row['invoiceDate']!='0000-00-00') {
   					   	$invoiceDate = '="' . date('m-d-Y', strtotime($row['invoiceDate'])) . '"';
				   }
					$rate = $row['rate'];
					$totalRate += $rate;
					$parate = $row['parate'];
					$totalPaRate += $parate;
					$rateFormatted = round($rate, 2);
					$parateFormatted = round($parate, 2);
				   if($table=='dispatchOutside' || $table=='warehouse_dispatch'){
						$dataRow = array($row['company'], $row['invoice'], $dodate, $invoiceDate, $row['carrierInvoiceRefNo'], $rateFormatted, $parateFormatted, $row['days_diff']);
					}else{
						$dataRow = array($row['company'], $row['invoice'], $dodate, $invoiceDate, $rateFormatted, $parateFormatted, $row['days_diff']);
					}
					$data[] = $dataRow;
				}
				if($table=='dispatchOutside' || $table=='warehouse_dispatch'){
					$data[] = array('', '', '', '', 'Total', number_format($totalRate,2), number_format($totalPaRate,2), '');
				}else{
					$data[] = array('', '', '', 'Total', number_format($totalRate,2), number_format($totalPaRate,2), '');
				}
			}
			$this->load->library('excel_generator');
			$fileName = "AccountReceivables.xlsx";   
			$this->excel_generator->generateExcel($data, $fileName);
			unlink($fileName);
			exit;
			die('csv');
	}
	function getReceivableInvoiceData($invoiceIds,$table){
		$carrierInvoiceRefNoSelect='';
		if($table=='dispatchOutside'){
			$documentTable='documentsOutside';
			$carrierInvoiceRefNoSelect=", IFNULL(NULLIF(a.carrierInvoiceRefNo, ''), '-') AS carrierInvoiceRefNo";
		}elseif($table=='warehouse_dispatch'){
			$documentTable='warehouse_documents';
			$carrierInvoiceRefNoSelect=", IFNULL(NULLIF(a.carrierInvoiceRefNo, ''), '-') AS carrierInvoiceRefNo";
		}else{
			$documentTable='documents';
		}
		$invoiceIdsString = implode(',', array_map('intval', $invoiceIds));
		if($table=='warehouse_dispatch'){
			$sql = "SELECT a.id, a.company AS company_id, c.company, a.invoice, a.pudate, a.dodate, a.invoiceDate, a.rate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.invoiceType, d.fileurl, carrierInvoice.`fileurl` AS carrierInvoice,edate, a.dispatchMeta $carrierInvoiceRefNoSelect
			FROM $table a
			JOIN companies c ON c.id = a.company
			LEFT JOIN $documentTable d ON d.did = a.id AND d.type = 'gd'
			LEFT JOIN $documentTable carrierInvoice ON carrierInvoice.did = a.id AND carrierInvoice.type = 'carrierInvoice'
			WHERE a.invoiced = '1'
			AND ( a.invoiceClose = '0' || a.invoiceClose = '')
			AND (a.invoiceDate != '0000-00-00' || a.invoiceDate != '')	
			AND (a.invoiceCloseDate = '0000-00-00' || a.invoiceCloseDate = '')	
			AND (a.invoicePaid = '0' || a.invoicePaid = '')
			AND (a.invoicePaidDate = '0000-00-00' || a.invoicePaidDate = '')	
			AND a.id IN ($invoiceIdsString)
			GROUP BY a.id
			LIMIT 1000";
		}else{
			$sql = "SELECT a.id, a.company AS company_id, c.company, a.invoice, a.pudate, a.dodate, a.invoiceDate, a.rate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.invoiceType, d.fileurl, carrierInvoice.`fileurl` AS carrierInvoice, a.dispatchMeta $carrierInvoiceRefNoSelect
			FROM $table a
			JOIN companies c ON c.id = a.company
			LEFT JOIN $documentTable d ON d.did = a.id AND d.type = 'gd'
			LEFT JOIN $documentTable carrierInvoice ON carrierInvoice.did = a.id AND carrierInvoice.type = 'carrierInvoice'
			WHERE JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta),'$.invoiced') = '1'
			AND ( JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta),'$.invoiceClose') = '0' || 
			JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta),'$.invoiceClose') = '')
			AND a.invoiceDate != '0000-00-00'	
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta),'$.invoiceCloseDate') = ''
			AND (JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '0' ||
			JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '')
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') = ''
			AND a.id IN ($invoiceIdsString)
			GROUP BY a.id
			LIMIT 1000";
		}
		
		// echo $sql;exit;
		$query = $this->db->query($sql);
		return $query->result_array();
    }
	public function exportPdfReceivables(){
		if(!checkPermission($this->session->userdata('permission'),'statementAcc')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $this->load->library('pdf');
        $pdf = $this->pdf->load(); 
		
		$invoiceIds = json_decode($this->input->post('export_pdf_invoice_ids'), true);
		$table = str_replace('"', '', $this->input->post('table'));
		$dispatch = $this->getReceivableInvoiceData($invoiceIds, $table);
		$groupedData = [];

		foreach ($dispatch as $row) {
			$company = $row['company'];
			$invoice = $row['invoice'];
			$aging = (int)$row['days_diff'];
			$parate = (float)$row['parate'];

			if (!isset($groupedData[$company])) {
				$groupedData[$company] = [
					'company' => $company,
					'invoices' => [],
					'aging_values' => [],
					'parate_total' => 0,
				];
			}

			$groupedData[$company]['invoices'][] = $invoice;
			$groupedData[$company]['aging_values'][] = $aging;
			$groupedData[$company]['parate_total'] += $parate;
		}
		// print_r($groupedData);exit;
		// $data['company'] = $this->Comancontroler_model->get_data_by_id($id,'companies');

		$data['invoice'] = array();
		$data['invoice'] = $groupedData;
	
    	////// generate csv 	
		$file = 'accountReceivablesPDF';
		
		$html = $this->load->view('admin/'.$file, $data, true);
		//echo $html;die();
		
		$stylesheet = "";
		//$pdf->WriteHTML($stylesheet, 1);
		//$pdf->SetAutoPageBreak(true, 10);
		$pdf->WriteHTML($html);
        // write the HTML into the PDF
        $output = 'AccountReceivables '.date('m-d-Y').'.pdf';
		$pdf->Output($output, "D");
		exit;
	}
	public function addNotes(){ 
	    if(!checkPermission($this->session->userdata('permission'),'statementAcc')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $this->load->library('pdf');
        $pdf = $this->pdf->load(); 
		
		$data['invoice'] = array();
		$sdate ='';
		$edate ='';
		$agingSearch = '';
		$invoiceType = '';
		$customerStatus = '';
		$invoiceNo = '';
		$agingFrom = '';
		$agingTo = '';
		$company_id = $this->input->post('company_id');
		$shippingContact = $this->input->post('shippingContact');
		$invoiceIds = $this->input->post('invoice_ids'); 
		if ($invoiceIds) {
			$invoiceIds = explode(',', $invoiceIds); 
		}
		$agingFrom =  $this->input->post('agingFrom');
		$agingTo =  $this->input->post('agingTo');
		$file_ids = $this->input->post('invoice_file_ids'); 
		
		$dispatch_type = $this->input->post('dispatch_type'); 
		$table = 'dispatch';
		$extraTable = 'dispatchExtraInfo';
		$type = 'fleet';
		$data['type'] = '';
		if(isset($shippingContact)){
			$data['company'] = $this->db->query("SELECT companies.company, companies.address, contacts.contact_person  as contactPerson,  contacts.designation, contacts.department, contacts.email, contacts.phone 
			FROM companies LEFT JOIN company_shipping_contacts contacts ON companies.id=contacts.company_id AND contacts.id=$shippingContact
			WHERE companies.id=$company_id")->result_array();
		}else{
			$data['company'] = $this->Comancontroler_model->get_data_by_id($company_id,'companies');
		}
		if(isset($dispatch_type) && $dispatch_type == 'dispatchOutside'){ 
			$table = 'dispatchOutside'; 
			$extraTable = 'dispatchOutsideExtraInfo';
			$type = 'logistics';
		}
		
		if(isset($dispatch_type) && $dispatch_type == 'warehouse_dispatch'){ 
			$table = 'warehouse_dispatch'; 
			$extraTable = 'warehouse_dispatch_extra_info';
			$type = 'warehouse';
		}

		$document_table = '';
		$file_url = '';
		if ($dispatch_type == 'dispatch') {
			$document_table = 'documents';
			$file_url = FCPATH.'assets/paInvoice/';
		} elseif ($dispatch_type == 'dispatchOutside') {
			$document_table = 'documentsOutside';
			$type = 'outside';
			$file_url = FCPATH . 'assets/outside-dispatch/invoice/';
		} elseif ($dispatch_type == 'warehouse_dispatch') {
			$document_table = 'warehouse_documents';
			$type = 'warehouse';
			$file_url = FCPATH . 'assets/warehouse/invoice/';
		}

		$invoices = [];
		if ($document_table && !empty($file_ids)) {
			$sql = "SELECT fileurl FROM $document_table WHERE id IN (" . implode(",", $file_ids) . ") AND `type`='paInvoice'";
			// echo $sql;exit;

			$invoices = $this->db->query($sql)->result_array();
		}
		$data['table'] = $table;
		$data['invoice'] = $this->Accountreceivable_model->getReceivableStatement($table,$sdate,$edate,$company_id,$agingSearch,$invoiceType,$customerStatus, $invoiceNo, $agingFrom, $agingTo, $invoiceIds);

		if($data['invoice']){
    	    for($i=0;count($data['invoice']) > $i;$i++){
    	        $dispatchInfo = $this->Comancontroler_model->get_dispatchinfo_by_id($data['invoice'][$i]['id'],'pd_date',$extraTable);
				if($dispatchInfo){
					foreach($dispatchInfo as $dis){
						$data['invoice'][$i]['pd_date'] = $dis['pd_date'];
					}
				} else {
				    $data['invoice'][$i]['pd_date'] = '';
				}
    	    }
    	}	
		
		$file = 'statementPDF';
		
		$html = $this->load->view('admin/'.$file, $data, true);
		//echo $html;die();
		$stylesheet = "";
		$pdf->WriteHTML($html);
		$email =  $this->input->post('email');
		$other_cEmails = $this->input->post('other_cEmails') ?? [];
		$other_cEmails_checkbox = $this->input->post('other_cEmails_checkbox') ?? [];
		
		$filename = 'Statement of Accounts - '.$data['company'][0]['company'].' '.date('m-d-Y').'.pdf';
		$pdfContent = $pdf->Output('', 'S');

		$this->load->library('PHPMailer_Lib');
		$mail = $this->phpmailer_lib->load();
		try {
			$mail->isSMTP();                                      
			$mail->Host = 'smtp.gmail.com';                      
			$mail->SMTPAuth = true;                               
			$mail->Username = 'accounts@palogisticsgroup.com';            
			$mail->Password = 'vbio zjfq bkop nkha';                    
			$mail->SMTPSecure = 'tls';   
			$mail->Port = 587;                              
		
			$mail->SMTPOptions=array('ssl'=>array(
					'verify_peer'=>false,
					'verify_peer_name'=>false,
					'allow_self_signed'=>false
					
					));
			$mail->CharSet = 'UTF-8';
			$mail->Encoding = 'base64';

			// $mail->Host = 'smtp.gmail.com';
			// $mail->SMTPAuth = true;
			// $mail->Username = 'naveedullah968@gmail.com';
			// $mail->Password = 'ksvm qcbp xfuc weby';
			// $mail->Port = 587;
			// $mail->CharSet = 'UTF-8';
			// $mail->Encoding = 'base64';
			
			//from email=$email
			$mail->setFrom('accounts@palogisticsgroup.com', 'Accounts PA Logistics Group LLC');
			$mail->addAddress($email);
			// $mail->addAddress('naveedullah968@gmail.com');

			$mail->addCC('accounts@palogisticsgroup.com');

			if (!empty($other_cEmails) && is_array($other_cEmails)) {
				foreach ($other_cEmails as $index => $email) {
					if (isset($other_cEmails_checkbox[$index]) && $other_cEmails_checkbox[$index] == '1' && !empty($email))
					{
						$mail->addCC(trim($email));
					}
				}
			}

			$mail->isHTML(true);
			$mail->addStringAttachment($pdfContent, $filename, 'base64', 'application/pdf');	
			if (!empty($invoices)) {
				foreach ($invoices as $inv) {
					$filePath = $file_url . $inv['fileurl']; // full path
					if (file_exists($filePath)) {
						$mail->addAttachment($filePath, basename($filePath));
					}
				}
			}

			$email_subject = $this->input->post('email_subject');
			$mail->Subject = $email_subject;
			$email_body = $this->input->post('note');
			$mail->Body=$email_body .'
			<table cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 444px; font-family: Arial, sans-serif; line-height: 1.4;">
				<tr>
					<!-- Logo Section -->
					<td style="width: 120px; vertical-align: middle; text-align: center; padding-right: 9px; border-right: 2px solid #888;">
						<a href="https://patransportca.com" target="_blank">
							<img src="https://dispatch.patransportca.com/html/logo.jpg" alt="PA LOGISTICS Logo" width="120" style="width: 120px; height: auto; display: block;">
						</a>
					</td>

					<!-- Details Section -->
					<td style="padding-left: 9px; vertical-align: top;">
						<h2 style="margin: 0; font-size: 15px; color: #E25858;line-height: 19px;">PA Logistics Group LLC</h2>
						<p style="margin: 2px 0 0; font-size: 13px; color: #555;"><strong>Accounts Department</strong></p>
						<p style="margin: 2px 0; font-size: 12px; color: #555;">
							<a href="tel:(925) 489-2332" style="color: #555; text-decoration: none;">(925) 489-2332</a> | <a href="mailto:accounts@palogisticsgroup.com" style="color: #555; text-decoration: none;">accounts@palogisticsgroup.com</a>
						</p>
						<p style="margin: 2px 0; font-size: 12px; color: #555;">
							MC 956423 | DOT 3339378 | <a href="https://palogisticsgroup.com" style="color: #555; text-decoration: none;" target="_blank">www.palogisticsgroup.com</a>
						</p>
						<p style="margin: 2px 0; font-size: 12px; color: #555;">672 W 11th St, Suite 348 Tracy, CA 95376</p>
						
						<table cellpadding="0" cellspacing="0" border="0" style=" max-width: 400px; margin: 0;width: 278px;">
							<tr>
								<td style="padding: 0; text-align: center;">
									<img src="https://dispatch.patransportca.com/html/nmsdc.jpg" width="70" alt="NMSDC MBE Certified" style="width: 70px; height: auto;">
								</td>
								<td style="padding: 0; text-align: center;">
									<img src="https://dispatch.patransportca.com/html/carb.jpg" width="80" alt="CARB COMPLIANT" style="width: 80px; height: auto;">
								</td>
								<td style="padding: 0; text-align: center;">
									<img src="https://dispatch.patransportca.com/html/smartway.jpg" width="138" alt="SmartWay Transport Partnership" style="width: 138px; height: auto;margin-left: -12px;">
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<!-- Confidentiality Disclosure -->
				<tr>
					<td colspan="2" style="padding-top: 10px; text-align: left;">
						<p style="font-size: 10px; color: #888; text-align: justify; margin: 0;">
							<strong>Confidentiality Disclosure:</strong>
							The information in this email and in attachments is confidential and intended solely for the attention and use of the named addressee(s). This information may be subject to legal, professional, or other privilege or may otherwise be protected by work product immunity or other legal rules. It must not be disclosed to any person without our authority. If you are not the intended recipient, or a person responsible for delivering it to the intended recipient, you are not authorized to and must not disclose, copy, distribute, or retain this message or any part of it.
						</p>
					</td>
				</tr>
			</table>';
			if ($mail->send()) {
				$userid = $this->session->userdata('logged');
				$user_id = $userid['adminid'];
				// print_r($invoiceIds);exit;
				foreach ($invoiceIds as $invoiceId) {
					$sql="INSERT INTO receivable_statment_history (`user_id`, `company_id`, `did`, `dispatch_type`, `subject`,	`note`) VALUES ($user_id, $company_id, trim($invoiceId), '$type', '$email_subject', '$email_body')";
					// echo $sql;exit;
					$this->db->query($sql);
				}

				$this->session->set_flashdata('item', 'Email sent successfully!');
				echo json_encode(['status' => 'success', 'message' => 'Email sent successfully']);
			} else {
				$this->session->set_flashdata('error', 'Email could not be sent. Please try again.');
				echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Message could not be sent. Error: ' . $e->getMessage());
			echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
		}
		// redirect(base_url('admin/accountReceivable'));
		exit;
	}
	public function getCCEmails($company_id) {
		$invoiceIds = $this->input->post('invoice_ids');
		$dispatchType = $this->input->post('dispatchType');

		$document_table = '';
		if ($dispatchType == 'dispatch') {
			$document_table = 'documents';
		} elseif ($dispatchType == 'dispatchOutside') {
			$document_table = 'documentsOutside';
		} elseif ($dispatchType == 'warehouse_dispatch') {
			$document_table = 'warehouse_documents';
		}

		$invoices = [];
		if ($document_table && !empty($invoiceIds)) {
			$sql = "SELECT * FROM $document_table WHERE did IN (" . implode(",", $invoiceIds) . ") AND `type`='paInvoice'";
			$invoices = $this->db->query($sql)->result_array();
		}
		
		$company = $this->Comancontroler_model->get_data_by_id($company_id,'companies');
		$invEmail=$company[0]['email'];
		$cEmails=$company[0]['email2'];
		echo json_encode([
			'invEmails' => $invEmail,
			'cEmails' => $cEmails,
			'invoices'  => $invoices
		]);
	}
	public function PreviewStatmentPDF(){ 
	    if(!checkPermission($this->session->userdata('permission'),'statementAcc')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $this->load->library('pdf');
        $pdf = $this->pdf->load(); 
		
		$data['invoice'] = array();
		$sdate = '';
		$edate ='';
		$agingSearch = '';
		$invoiceType = '';
		$customerStatus = '';
		$invoiceNo = '';
		$companyId = $_GET['company_id'];
		$shippingContact = $_GET['shipping_contact'];
		$invoiceIds = $this->input->get('invoice_ids'); 
		if ($invoiceIds) {
			$invoiceIds = explode(',', $invoiceIds); 
		}
		$agingFrom = $_GET['agingFrom'];
		$agingTo = $_GET['agingTo'];
		$table = 'dispatch';
		$extraTable = 'dispatchExtraInfo';
		$data['type'] = '';
		if(isset($_GET['shipping_contact'])){
			$data['company'] = $this->db->query("SELECT companies.company, companies.address, contacts.contact_person  as contactPerson,  contacts.designation, contacts.department, contacts.email, contacts.phone 
			FROM companies LEFT JOIN company_shipping_contacts contacts ON companies.id=contacts.company_id AND contacts.id=$shippingContact
			WHERE companies.id=$companyId")->result_array();
		}else{
			$data['company'] = $this->Comancontroler_model->get_data_by_id($companyId,'companies');
		}
		if(isset($_GET['dispatch_type']) && $_GET['dispatch_type'] == 'dispatchOutside'){ 
			$table = 'dispatchOutside'; $extraTable = 'dispatchOutsideExtraInfo';
		} 
		if(isset($_GET['dispatch_type']) && $_GET['dispatch_type'] == 'warehouse_dispatch'){ 
			$table = 'warehouse_dispatch'; $extraTable = 'warehouse_dispatch_extra_info';
		} 
		$data['table'] = $table;
		$data['invoice'] = $this->Accountreceivable_model->getReceivableStatement($table,$sdate,$edate,$companyId,$agingSearch,$invoiceType,$customerStatus, $invoiceNo, $agingFrom, $agingTo, $invoiceIds);
		if($data['invoice']){
    	    for($i=0;count($data['invoice']) > $i;$i++){
    	        $dispatchInfo = $this->Comancontroler_model->get_dispatchinfo_by_id($data['invoice'][$i]['id'],'pd_date',$extraTable);
				if($dispatchInfo){
					foreach($dispatchInfo as $dis){
						$data['invoice'][$i]['pd_date'] = $dis['pd_date'];
					}
				} else {
				    $data['invoice'][$i]['pd_date'] = '';
				}
    	    }
    	}
    	
		$file = 'statementPDF';
		
		$html = $this->load->view('admin/'.$file, $data, true);
		//echo $html;die();
		
		$stylesheet = "";
		//$pdf->WriteHTML($stylesheet, 1);
		//$pdf->SetAutoPageBreak(true, 10);
		$pdf->WriteHTML($html);
        // write the HTML into the PDF
        $output = 'Statement of Accounts - '.$data['company'][0]['company'].' '.date('m-d-Y').'.pdf';
		$pdf->Output($output, "I"); // I D F	    
		exit;
	}
}
?>
