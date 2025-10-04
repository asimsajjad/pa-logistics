<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AccountPayableController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->model('Comancontroler_model');
		$this->load->model('Accountpayable_model');

		if (empty($this->session->userdata('logged'))) {
			redirect(base_url('AdminLogin'));
		}
		// error_reporting(-1);
		// ini_set('display_errors', 1);
		// error_reporting(E_ERROR);
	}

	public function accountPayable()
	{
		if (!checkPermission($this->session->userdata('permission'), 'statementAcc')) {
			redirect(base_url('AdminDashboard'));
		}

		//$sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
		//$edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));

		$company = $truckingCompany = $factoringCompany = $driver = $sdate = $edate = $agingSearch = $invoiceNo = $carrierInvoiceRefNo = $agingFrom = $agingTo= '';

		$data['dispatchURL'] = 'dispatch';
		if ($this->input->post('search')) {
			$company = $this->input->post('company');
			$truckingCompany = $this->input->post('truckingCompany');
			$factoringCompany = $this->input->post('factoringCompany');
			$agingSearch = $this->input->post('agingSearch');
			$dispatchType = $this->input->post('dispatchType');
			$invoiceNo = $this->input->post('invoiceNo');
			$carrierInvoiceRefNo = $this->input->post('carrierInvoiceRefNo');
			$agingFrom = $this->input->post('aging_from');
			$agingTo = $this->input->post('aging_to');

			
			if ($dispatchType == 'warehouse_dispatch') {
				$table = 'warehouse_dispatch';
			}else {
				$table = 'dispatchOutside';
				$data['dispatchURL'] = 'outside-dispatch';
			} 

			$invoiceType = $this->input->post('invoiceType');

			$week = $this->input->post('week');
			if ($week != '' && $week != 'all') {
				$weeks = explode(',', $week);
				$sdate = $weeks[0];
				$edate = $weeks[1];
			}
			if ($this->input->post('sdate')) {
				$sdate = $this->input->post('sdate');
			}
			if ($this->input->post('edate')) {
				$edate = $this->input->post('edate');
			}
			$data['dispatch'] = $this->Accountpayable_model->getAccountPayable($table, $sdate, $edate, $truckingCompany, $factoringCompany, $agingSearch, $invoiceType, $invoiceNo, $carrierInvoiceRefNo, $agingFrom, $agingTo);
		} else {
			$data['dispatch'] = array();
		}


		$data['drivers'] = array(); //$this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		//$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=', 'Deleted', 'companies', '*', 'company', 'asc');
		$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies');
		$data['locations'] = array(); //$this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = array(); //$this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = array(); //$this->Comancontroler_model->get_data_by_table('cities');
		$data['factoringCompanies'] = $this->Comancontroler_model->get_data_by_table('factoringCompanies');

		$this->load->view('admin/layout/header');
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/account_payable', $data);
		$this->load->view('admin/layout/footer');
	}

	public function updateInvoice(){
		$id = $this->input->post('invoice_id');
		$carrierPayoutDate = $this->input->post('proofdate');
		$table=$this->input->post('distpatchType');

		$userId=$this->session->userdata('adminid');
		
		if($table == 'dispatchOutside'){
			$dispatchType = 'dispatchOutside';
		}elseif($table == 'warehouse_dispatch'){
			$dispatchType = 'warehouse_dispatch';
		}
		$batchSql = "SELECT MAX(id) as id, MAX(batchNo) as batchNo FROM payableBatches";
		$lastBatch = $this->db->query($batchSql)->row();

		if (($lastBatch)) {
			$batchNo = sprintf('%04d', intval($lastBatch->batchNo) + 1);
			$batchId = (!empty($lastBatch->id)) ? $lastBatch->id + 1 : 1;
		}

		$sql_carrierPayoutDate = $this->db->select('carrierPayoutDate')
                                    ->from($table)
                                    ->where('id', $id)
                                    ->get();
        $existingCarrierPayoutDate = $sql_carrierPayoutDate->row()->carrierPayoutDate;

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
				
				if($table == 'dispatchOutside'){
					$config['upload_path'] = 'assets/outside-dispatch/gd/';
				}elseif($table == 'warehouse_dispatch'){
					$config['upload_path'] = 'assets/warehouse/gd/';
				}
				$config['file_name'] = time() . '-CARRIER-GD-' . uniqid();

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('file')) {
					$uploadData = $this->upload->data();
					$paymentProof = $uploadData['file_name'];
					$addfile = array(
						'did' => $id,
						'type' => 'carrierGd',
						'fileurl' => $paymentProof,
						'rdate' => date('Y-m-d H:i:s')
					);

					if ($table == 'dispatchOutside') {
						$this->Comancontroler_model->add_data_in_table($addfile, 'documentsOutside');
					} elseif($table == 'warehouse_dispatch'){
						$this->Comancontroler_model->add_data_in_table($addfile, 'warehouse_documents');
					}
					// Log each uploaded file
					$changeField[] = array("Carrier Payment proof file","gdfile", "Upload", $paymentProof);
				} else {
					echo $this->upload->display_errors();
				}		
			}
			if($existingCarrierPayoutDate != $carrierPayoutDate){
				$changeField[] = array('Carrier Payout Date','carrierPayoutDate',$existingCarrierPayoutDate,$carrierPayoutDate);
			}
		}
        if ($id){
			// $sql = "UPDATE dispatchOutside SET `carrierPayoutDate` = '$carrierPayoutDate', carrierPayoutCheck=1, carrierGd='AK', batchId='$batchId' WHERE id=$id";
			// $this->db->query($sql);
			$totalAmount_sql="SELECT CASE WHEN a.bookedUnderNew = 4 THEN a.rate + a.agentRate ELSE a.rate END AS rate FROM $table a WHERE `id`=$id";
			// echo $totalAmount_sql;exit;
			$totalAmount=$this->db->query($totalAmount_sql)->row()->rate;
			// $totalAmount = $this->db->select('rate')->where('id', $id)->get('dispatchOutside')->row()->rate;

			$paymentType = 'payable';
			$insertBatch = "INSERT INTO payableBatches (addedBy, batchNo, dispatchType, totalAmount, paymentType) 
						VALUES ('$userId', '$batchNo', '$dispatchType', '$totalAmount', '$paymentType')";
				// echo $insertBatch;exit;		
			$batchResult=$this->db->query($insertBatch);

			$sql = "UPDATE $table SET `carrierPayoutDate` = '$carrierPayoutDate', carrierPayoutCheck=1, carrierGd='AK', payableBatchId='$batchId' WHERE id=$id";
			$this->db->query($sql);
			// echo $sql;exit;
            $result = $this->db->query($sql);
			if($changeField) {
				$userid = $this->session->userdata('logged');
				$changeFieldJson = json_encode($changeField);
				$aplog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
				if($table == 'warehouse_dispatch'){
					$this->Comancontroler_model->add_data_in_table($aplog,'warehouse_dispatch_log');
				}else{
					$this->Comancontroler_model->add_data_in_table($aplog,'dispatchOutsideLog');
				} 
			}
			if($result ){
				
				$this->session->set_flashdata('item', '	Updated successfully.');
			}
        } else {
			$this->session->set_flashdata('searchError', 'Something went wrong');
        }	
	}
	public function updateBulkInvoices() {
		$invoiceIds = json_decode($this->input->post('invoice_ids'), true);
		$carrierPayoutDate = $this->input->post('proofdate');
		$dispatchType=$this->input->post('distpatchType');

		$userId=$this->session->userdata('adminid');
	
		if($dispatchType == 'outsideDispatch'){
			$table = 'dispatchOutside';
		}elseif($dispatchType == 'warehouse_dispatch'){
			$table = 'warehouse_dispatch';
		}

		$batchNo = $this->input->post('batchNo');
		$batchId = $this->input->post('batchId');
		$total_Amount = $this->input->post('total_Amount');
		$uploadedFiles = [];

		// echo $total_Amount;exit;
		if (!empty($_FILES['gd_d']['name'][0])) {
			$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|xlsm';
			$config['max_size'] = '5000';
			if($table == 'dispatchOutside'){
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
		
				$fileName = time() . '-CARRIER-GD-' . uniqid();
				$config['file_name'] = $fileName;
				$this->upload->initialize($config);
		
				if ($this->upload->do_upload('file')) {
					$uploadData = $this->upload->data();
					$fileUrl = $uploadData['file_name'];
					$uploadedFiles[] = $uploadData['file_name'];
				}
			}
		}
	
		if (!empty($invoiceIds)) {
			// $this->db->select_sum('rate');  
			// $this->db->where_in('id', $invoiceIds);
			// $totalAmount = $this->db->get($table)->row()->rate;
			$paymentType = 'payable';
				$insertBatch = "INSERT INTO payableBatches (addedBy, dispatchType, batchNo, totalAmount, paymentType) 
                    VALUES ('$userId', '$table', '$batchNo', '$total_Amount', '$paymentType')";
				$this->db->query($insertBatch);	

			foreach ($invoiceIds as $id) {
				$changeField = [];
				// $sql_carrierPayoutDate = $this->db->select('carrierPayoutDate')
                //                     ->from($table)
                //                     ->where('id', $id)
                //                     ->get();
				$sql_carrierPayoutDate = "SELECT carrierPayoutDate FROM $table WHERE id=$id";
				$query_carrierPayoutDate = $this->db->query($sql_carrierPayoutDate);
        		$existingCarrierPayoutDate = $query_carrierPayoutDate->row()->carrierPayoutDate;
				// Update dispatchOutside table for each invoice
				$sql = "UPDATE $table SET `carrierPayoutDate` = '$carrierPayoutDate', carrierPayoutCheck=1, carrierGd='AK', payableBatchId='$batchId' WHERE id=$id";
				$this->db->query($sql);
	
				if (!empty($uploadedFiles)) {
					foreach ($uploadedFiles as $fileUrl) {
						$addfile = array(
							'did' => $id,
							'type' => 'carrierGd',
							'fileurl' => $fileUrl,
							'rdate' => date('Y-m-d H:i:s')
						);
						if ($table == 'dispatchOutside') {
							$this->Comancontroler_model->add_data_in_table($addfile, 'documentsOutside');
						} elseif($table == 'warehouse_dispatch'){
							$this->Comancontroler_model->add_data_in_table($addfile, 'warehouse_documents');
						}
						// Log each file upload
						$changeField[] = array("Carrier Payment proof file", "gdfile", "Upload", $fileUrl);
					}
				}
				// $changeField[] = array("Payment proof file","gdfile","Upload", $fileUrl); 
				if($existingCarrierPayoutDate != $carrierPayoutDate){
					$changeField[] = array('Carrier Payout Date','carrierPayoutDate',$existingCarrierPayoutDate,$carrierPayoutDate);
				}				
				$userid = $this->session->userdata('logged');
			   if($changeField) {
				   $changeFieldJson = json_encode($changeField);
				   $aplog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
					if($table == 'warehouse_dispatch'){
						$this->Comancontroler_model->add_data_in_table($aplog,'warehouse_dispatch_log');
					}else{
						$this->Comancontroler_model->add_data_in_table($aplog,'dispatchOutsideLog');
					} 
			    }
			   // To here log entry
			}
	
			$this->session->set_flashdata('item', 'Invoices updated successfully.');
		} else {
			$this->session->set_flashdata('searchError', 'No invoices selected.');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	public function downloadPayableStatementPDF()
	{
		if (!checkPermission($this->session->userdata('permission'), 'statementAcc')) {
			redirect(base_url('AdminDashboard'));
		}
		$this->load->library('pdf');
		$pdf = $this->pdf->load();
		$id = $this->uri->segment(3);
		$data['company'] = $this->Comancontroler_model->get_data_by_id($id, 'truckingCompanies');
		// print_r($data);exit;
		$data['invoice'] = array();
		$invoice = date('Y-m-d-H-i');
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$agingSearch = $_GET['agingSearch'];
		$invoiceType = $_GET['invoiceType'];
		$invoiceNo = $_GET['invoiceNo'];
		$carrierInvoiceRefNo = $_GET['carrierInvoiceRefNo'];
		$agingFrom = $_GET['agingFrom'];
		$agingTo = $_GET['agingTo'];

		$table = 'dispatch';
		$extraTable = 'dispatchExtraInfo';
		$data['type'] = '';
		$data['table'] = '';

		if (isset($_GET['dTable']) && $_GET['dTable'] == 'dispatchOutside') {
			$table = 'dispatchOutside';
			$extraTable = 'dispatchOutsideExtraInfo';
			$data['table'] = $table;

		}
		if (isset($_GET['dTable']) && $_GET['dTable'] == 'warehouse_dispatch') {
			$table = 'warehouse_dispatch';
			$extraTable = 'warehouse_dispatch_extra_info';
			$data['table'] = $table;
		}
		if (isset($_GET['type']) && $_GET['type'] != '') {
			$data['type'] = $_GET['type'];
		}

		$data['invoice'] = $this->Accountpayable_model->getPayableStatementOfAccount($table, $sdate, $edate, $id, $agingSearch, $invoiceType, $invoiceNo, $carrierInvoiceRefNo, $agingFrom, $agingTo);
		// print_r($data['invoice']);exit; 
		if ($data['invoice']) {
			for ($i = 0; count($data['invoice']) > $i; $i++) {
				$dispatchInfo = $this->Comancontroler_model->get_dispatchinfo_by_id($data['invoice'][$i]['id'], 'pd_date', $extraTable);
				if ($dispatchInfo) {
					foreach ($dispatchInfo as $dis) {
						$data['invoice'][$i]['pd_date'] = $dis['pd_date'];
					}
				} else {
					$data['invoice'][$i]['pd_date'] = '';
				}
			}
		}

		////// generate csv 

		if (isset($_GET['generateCSV']) || isset($_GET['generateXls'])) {
			$colspan = 5;

			if ($data['type'] == 'Drayage') {
				$colspan = 6;
				if($table == 'warehouse_dispatch'){
					$heading = array('End Date', 'Invoice No.', 'Cust Ref. No.', 'Container No. / Trailer', 'Invoice Date', 'Inv. Aging (Days)', 'Due Date', 'Amount');
				}else{
					$heading = array('Dilevery Date', 'Invoice No.', 'Cust Ref. No.', 'Container No. / Trailer', 'Invoice Date', 'Inv. Aging (Days)', 'Due Date', 'Amount');
				}
			} else {
				if($table == 'warehouse_dispatch'){
					$heading = array('End Date', 'Invoice No.', 'Cust Ref. No.', 'Invoice Date', 'Inv. Aging (Days)', 'Due Date', 'Amount');
				}else{
					$heading = array('End Date', 'Invoice No.', 'Cust Ref. No.', 'Invoice Date', 'Inv. Aging (Days)', 'Due Date', 'Amount');
				}
			}
			$csvExcel = array($heading);

			if ($data['invoice']) {
				$i = 1;
				$partialAmt = $amount = 0;
				foreach ($data['invoice'] as $dis) {
					$dispatchMeta = json_decode($dis['dispatchMeta'], true);
					if($table == 'warehouse_dispatch'){
						if (is_numeric($dis['partialAmount'])) {
							$partialAmt = $partialAmt + $dis['partialAmount'];
						}
					}else{
						if (is_numeric($dispatchMeta['partialAmount'])) {
							$partialAmt = $partialAmt + $dispatchMeta['partialAmount'];
						}					
					}
					
					if($table == 'warehouse_dispatch'){
						if ($data['type'] != '' && $dis['invoicePDF'] != $data['type']) {
							continue;
						}
					}else{
						if ($data['type'] != '' && $dispatchMeta['invoicePDF'] != $data['type']) {
							continue;
						}
					}

					
					if ($dis['pd_date'] != '' && (!strstr($dis['pd_date'], '0000'))) {
						$dis['pudate'] = $dis['pd_date'];
					}
					$amount = $amount + $dis['rate'];
					$rowArr = array(date('m-d-Y', strtotime($dis['pudate'])), $dis['invoice'], $dis['tracking']);
					if ($data['type'] == 'Drayage') {
						$rowArr[] = str_replace('TBA', 'N/A', $dis['trailer']);
					}
					// $rowArr[] = date('m-d-Y', strtotime($dis['invoiceDate']));
					if($table == 'warehouse_dispatch'){
						$rowArr[] = date('m-d-Y', strtotime($dis['custInvDate']));
					}else{
						$rowArr[] = date('m-d-Y', strtotime($dispatchMeta['custInvDate']));
					}
					

					$invoiceType = $agingTxt = '';
					$showAging = 'false';
					$aDays = 0;
					if ($dis['invoiceType'] == 'Direct Bill') {
						$aDays = 30;
					} elseif ($dis['invoiceType'] == 'Quick Pay') {
						$aDays = 7;
					} elseif ($dis['invoiceType'] == 'RTS') {
						$aDays = 3;
					}

					// if ($dis['invoiceDate'] != '0000-00-00') {
					// 	$showAging = 'true';
					// }
					if($table == 'warehouse_dispatch'){
						if (!empty($dis['custInvDate']) && $dis['custInvDate'] != '0000-00-00') {
							$showAging = 'true';
						}
						if ($dis['invoicePaidDate'] != '') {
							$showAging = 'false';
						}
						if ($dis['invoiceCloseDate'] != '') {
							$showAging = 'false';
						}				
					}else{
						if (!empty($dispatchMeta['custInvDate']) && $dispatchMeta['custInvDate'] != '0000-00-00') {
							$showAging = 'true';
						}

						if ($dispatchMeta['invoicePaidDate'] != '') {
							$showAging = 'false';
						}
						if ($dispatchMeta['invoiceCloseDate'] != '') {
							$showAging = 'false';
						}
					}
					
					if ($showAging == 'true') {
						// $date1 = new DateTime($dis['invoiceDate']);
						if($table == 'warehouse_dispatch'){
							$date1 = new DateTime($dis['custInvDate']);
						}else{
							$date1 = new DateTime($dispatchMeta['custInvDate']);
						}
						$date2 = new DateTime(date('Y-m-d'));
						$diff = $date1->diff($date2);
						$aging = $diff->days;

						if ($aging > $aDays && $aDays > 0) {
							$agingTxt = '' . $aging . ' Days';
						} else {
							$agingTxt = '' . $aging . ' Days';
						}
					}

					$rowArr[] = $agingTxt;
					if($table == 'warehouse_dispatch'){
						if (!empty($dis['custDueDate'])) {
							$rowArr[] = date('m-d-Y', strtotime($dis['custDueDate']));
						} else {
							$rowArr[] = '';
						}
					}else{
						if (!empty($dispatchMeta['custDueDate'])) {
							$rowArr[] = date('m-d-Y', strtotime($dispatchMeta['custDueDate']));
						} else {
							$rowArr[] = '';
						}
					}
					

					$rowArr[] = '$ ' . $dis['rate'];

					$csvExcel[] = $rowArr;
				}
				$rowArr = array('', '', '');
				if ($data['type'] == 'Drayage') {
					$rowArr[] = '';
				}
				$rowArr[] = '';
				$rowArr[] = '';
				$csvExcel[] = $rowArr;

				$subTotalRow = array('', '', '','','');
				if ($data['type'] == 'Drayage') {
					$subTotalRow[] = '';
				}
				$subTotalRow[] = 'Subtotal';
				$subTotalRow[] = number_format($amount, 2);
				$csvExcel[] = $subTotalRow;

				$totalAmt = $amount;
				if ($partialAmt > 0) {
					$partialAmtRow = array('', '', '','','');
					if ($data['type'] == 'Drayage') {
						$partialAmtRow[] = '';
					}
					$partialAmtRow[] = 'Partial Amount';
					$partialAmtRow[] = number_format($partialAmt, 2);
					$csvExcel[] = $partialAmtRow;
					$totalAmt = $totalAmt - $partialAmt;
				}

				$totalRow = array('', '', '','','');
				if ($data['type'] == 'Drayage') {
					$totalRow[] = '';
				}
				$totalRow[] = 'Total Amount Due';
				$totalRow[] = number_format($totalAmt, 2);
				$csvExcel[] = $totalRow;
			}


			if (isset($_GET['generateCSV'])) {
				$fileName = 'Payable Statement - ' . $data['company'][0]['company'] . ' ' . date('m-d-Y') . '.csv';
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
			if (isset($_GET['generateXls'])) {
				$this->load->library('excel_generator');
				$fileName = 'Payable Statement - ' . $data['company'][0]['company'] . ' ' . date('m-d-Y') . ".xlsx";   //"data_$date.xlsx";
				// Generate Excel file using the library
				$this->excel_generator->generateExcel($csvExcel, $fileName);
			}

			// Delete the file from the server
			unlink($fileName);
			exit;
			die('csv');
		}


		$file = 'payableStatementPDF';

		$html = $this->load->view('admin/' . $file, $data, true);
		//echo $html;die();

		$stylesheet = "";
		//$pdf->WriteHTML($stylesheet, 1);
		//$pdf->SetAutoPageBreak(true, 10);
		$pdf->WriteHTML($html);
		// write the HTML into the PDF
		$output = 'Payable Statement - ' . $data['company'][0]['company'] . ' ' . date('m-d-Y') . '.pdf';
		$pdf->Output($output, "D");
		exit;
	}
	public function payableBatches()
	{
		if (!checkPermission($this->session->userdata('permission'), 'statementAcc')) {
			redirect(base_url('AdminDashboard'));
		}

		$company = $truckingCompany = $driver = $sdate = $edate = $agingFrom = $agingTo = '';
		// $table = 'dispatchOutside';
		$dispatchType = $this->input->post('dispatchType');
        if($dispatchType == 'outsideDispatch'){ 
            $table = 'dispatchOutside'; 
            $data['dispatchURL'] = 'outside-dispatch';
        }else if($dispatchType == 'warehouse_dispatch'){
			$table = 'warehouse_dispatch'; 
            $data['dispatchURL'] = 'paWarehouse';
		}
		// $sdate = date('Y-m-d');
		// $edate = date('Y-m-d');
		$company = null;
	
		if($this->input->post('generateCSV') || $this->input->post('generateXls')){
            $truckingCompany = $this->input->post('truckingCompany');
			if ($this->input->post('sdate')) {
				$sdate = $this->input->post('sdate');
			}
			if ($this->input->post('edate')) {
				$edate = $this->input->post('edate');
			}
			$agingFrom = $this->input->post('aging_from');
			$agingTo = $this->input->post('aging_to');
      
            $dispatch = $this->Accountpayable_model->downloadPayableBatches($table,$sdate,$edate,$truckingCompany, $agingFrom, $agingTo);
			if($table=='warehouse_dispatch'){
				$heading = array('Added By', 'Booked Under','Carrier','Carrier Payout Date','Batch No', 'Batch Date','Invoice','Carrier Ref #','Carrier Inv. Ref #','End Date','Carrier Invoice Date','Service Provider Rate','Payment Days');
			}else{
				$heading = array('Added By', 'Booked Under','Carrier','Carrier Payout Date','Batch No', 'Batch Date','Invoice','Carrier Ref #','Carrier Inv. Ref #','Delivery Date','Carrier Invoice Date','Carrier Rate','Payment Days');
			}
			
			$data = array($heading);
			// print_r($dispatch);exit;
			$totalRate = 0;
			if(!empty($dispatch)) {	
				foreach($dispatch as $row){
					$dispatchMeta = json_decode($row['dispatchMeta'],true);
				   	$dodate = $carrierInvoiceDate = $expectPayDate = $carrierPayoutDate = '0000-00-00';
					if($table=='warehouse_dispatch'){
						if($row['edate']!='0000-00-00') {
							$dodate = '="' . date('m-d-Y', strtotime($row['edate'])) . '"';
						}
					}else{
						if($row['dodate']!='0000-00-00') {
							$dodate = '="' . date('m-d-Y', strtotime($row['dodate'])) . '"';
						}
					}				   
				   	if($row['carrierPayoutDate']!='0000-00-00') {
					   $carrierPayoutDate = '="' . date('m-d-Y', strtotime($row['carrierPayoutDate'])) . '"';
				   	}
				   	$batchDateBatchNo = date('mdY', strtotime($row['date'])) . $row['batchNo'];
				   	$batcDate = date('m-d-Y h:i:s A', strtotime($row['date']));
					
				    $CarrierRefNo = '';
					if($table=='warehouse_dispatch'){
						$CarrierRefNo = $row['dispatchValue'];
						$carrierInvDate = $row['custInvDate'];
					}else{
						if($dispatchMeta['dispatchInfo'][0][0] == 'Carrier Ref No'){
							$CarrierRefNo = $dispatchMeta['dispatchInfo'][0][1];
						}
					    $carrierInvDate = $dispatchMeta['custInvDate'];
					}
					
					if(trim($carrierInvDate) != ''){ 
						$carrierInvDate = '="' . date('m-d-Y', strtotime($carrierInvDate)) . '"';
					}
					$rate = (float)$row['rate']; // cast to number
        			$totalRate += $rate;
				   	$dataRow = array($row['added_by'], $row['bookedUnder'], $row['carrier'], $carrierPayoutDate, $batchDateBatchNo, $batcDate, $row['invoice'] , $CarrierRefNo, $row['carrierInvoiceRefNo'], $dodate, $carrierInvDate, $rate, $row['pay_days']);

					$data[] = $dataRow;
				}
				$totalRow = array('', '', '', '', '', '', '', '', '', '', 'Total:', number_format($totalRate,2), '');
			    $data[] = $totalRow;
			}
            
			if($this->input->post('generateCSV')){
				$fileName = "Paybale_Batches_Report.csv"; 
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
				$fileName = "Payable_Batches_Report.xlsx";   //"data_$date.xlsx";
				$this->excel_generator->generateExcel($data, $fileName);
			}

			unlink($fileName);
			exit;
			die('csv');
        }

		if ($this->input->post('search')) {
			$truckingCompany = $this->input->post('truckingCompany');
			if ($this->input->post('sdate')) {
				$sdate = $this->input->post('sdate');
			}
			if ($this->input->post('edate')) {
				$edate = $this->input->post('edate');
			}
			$agingFrom = $this->input->post('aging_from');
			$agingTo = $this->input->post('aging_to');
			$data['customersPayables'] = $this->Accountpayable_model->getPayableBatches($table,$sdate,$edate,$truckingCompany, $agingFrom, $agingTo);
		}else {
            $data['customersPayables'] = array();
        }
		
		$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies');
		$this->load->view('admin/layout/header');
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/payable_batches', $data);
		$this->load->view('admin/layout/footer');
	}
	public function nextBatchNo()
	{
		$batchSql = "SELECT MAX(id) as id, MAX(batchNo) as batchNo FROM payableBatches";
		$lastBatch = $this->db->query($batchSql)->row();

		$output = "";
		if (($lastBatch)) {
			$batchNo = sprintf('%04d', intval($lastBatch->batchNo) + 1);
			$batchId = (!empty($lastBatch->id)) ? $lastBatch->id + 1 : 1;

			$msg = array(
				'batchNo' => $batchNo,
				'batchId' => $batchId
			);
			$error = 0;
		} else {
			$msg = 'OPERATION FAILED';
			$error = 1;
		}
		$output = array(
			'error' => $error,
			'msg' => $msg
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
		$company = $truckingCompany = $factoringCompany = $driver = $sdate = $edate = $invoiceNo = $carrierInvoiceRefNo='';
		$dispatchType = $this->input->post('dispatchType');
		$truckingCompany = $this->input->post('truckingCompany');
		$factoringCompany = $this->input->post('factoringCompany');
		$invoiceType = $this->input->post('invoiceType');
		$invoiceNo = $this->input->post('invoiceNo');
		$carrierInvoiceRefNo = $this->input->post('carrierInvoiceRefNo');

		$where='1=1  AND a.bookedUnder !=4';
		if ($truckingCompany != '') {
			$company_list = implode(",", $truckingCompany);
			if (in_array(4, $truckingCompany)) {
				$where .= " AND (
					a.truckingcompany IN ($company_list)
					OR a.bookedUnderNew = 4
				)";
			} else {
				$where .= " AND a.truckingcompany IN ($company_list)";
			}
		}
		if ($factoringCompany != '') {
			$factoringCompany_list = implode(",", $factoringCompany);
			$where .= " AND a.factoringCompany IN ($factoringCompany_list)";			
		}
		if($invoiceType != ''){
			if($invoiceType == 'Quick Pay' || $invoiceType == 'Zelle'){
				$where .= " AND a.carrierPaymentType='$invoiceType'";
			}elseif($invoiceType == 'Standard Billing'){
				$where .= " AND a.carrierPaymentType !='Quick Pay' AND a.carrierPaymentType != 'Zelle'";
			}
		}
		if($invoiceNo != ''){
			$where .= " AND a.invoice LIKE '%$invoiceNo%'";
		}
		if($carrierInvoiceRefNo != ''){
			$where .= " AND a.carrierInvoiceRefNo LIKE '%$carrierInvoiceRefNo%'";
		}
		if($dispatchType == 'warehouse_dispatch'){
			$sql = "SELECT 
				SUM(CASE WHEN (a.invoicePaidDate !='' AND a.invoicePaidDate != '0000-00-00') THEN 1 ELSE 0 END) AS `pending_count`,
				SUM(CASE WHEN (a.invoicePaidDate !='' AND a.invoicePaidDate != '0000-00-00') THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0))  END) ELSE 0 END) AS `pending_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 0 AND 15 THEN 1 ELSE 0 END) AS `zero_fifteen_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 0 AND 15 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0))  END) ELSE 0 END) AS `zero_fifteen_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `fifteen_thirty_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 16 AND 30 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END) ELSE 0 END) AS `fifteen_thirty_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 31 AND 35 THEN 1 ELSE 0 END) AS `thirty_thirty_five_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 31 AND 35 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END) ELSE 0 END) AS `thirty_thirty_five_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 36 AND 45 THEN 1 ELSE 0 END) AS `thirty_five_forty_five_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 36 AND 45 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END) ELSE 0 END) AS `thirty_five_forty_five_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) AS `forty_five_sixty_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 46 AND 60 THEN (CASE WHEN  a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END) ELSE 0 END) AS `forty_five_sixty_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) >60 THEN 1 ELSE 0 END) AS `sixty_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) > 60 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)	ELSE 0 END) AS `sixty_days_amount`
				FROM warehouse_dispatch a
				WHERE $where
				AND a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
				AND (a.custInvDate != '' || a.custInvDate != '0000-00-00')
				ORDER BY a.custInvDate ASC
				LIMIT 1000";	
		}else{
			$sql = "SELECT 
				SUM(CASE WHEN ((JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')) IS NOT NULL)  AND (JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate'))) != '') THEN 1 ELSE 0 END) AS `pending_count`,
				SUM(CASE WHEN ((JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')) IS NOT NULL)  AND (JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate'))) != '') THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) ELSE (a.rate - IFNULL(a.carrierPartialAmt,0))  END) ELSE 0 END) AS `pending_amount`,			
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 0 AND 15 THEN 1 ELSE 0 END) AS `zero_fifteen_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 0 AND 15 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0))  END) ELSE 0 END) AS `zero_fifteen_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `fifteen_thirty_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 16 AND 30 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END) ELSE 0 END) AS `fifteen_thirty_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 31 AND 35 THEN 1 ELSE 0 END) AS `thirty_thirty_five_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 31 AND 35 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END) ELSE 0 END) AS `thirty_thirty_five_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 36 AND 45 THEN 1 ELSE 0 END) AS `thirty_five_forty_five_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 36 AND 45 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END) ELSE 0 END) AS `thirty_five_forty_five_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) AS `forty_five_sixty_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 46 AND 60 THEN (CASE WHEN  a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END) ELSE 0 END) AS `forty_five_sixty_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) >60 THEN 1 ELSE 0 END) AS `sixty_days_count`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) > 60 THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)	ELSE 0 END) AS `sixty_days_amount`
				FROM dispatchOutside a
				WHERE $where
				AND a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
				AND JSON_VALID(a.dispatchMeta)
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
				ORDER BY JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) ASC
				LIMIT 1000";			
		}
		// echo $sql;exit;
		$query = $this->db->query($sql);
		return $query->row(); 
	}
	public function exportPayables(){
		$invoiceIds = json_decode($this->input->post('export_invoice_ids'), true);
		$table = $this->input->post('dispatchTable');
		$dispatch = $this->downloadPayableCSV($invoiceIds, $table);
		if($table == 'warehouse_dispatch'){
			$heading = array('Trucking Company', 'Invoice', 'Factoring Company', 'End Date', 'Carrier Invoice Date', 'Carrier Invoice Ref NO', 'Rate', 'Aging Days', 'Customer Recieved Date');	
		}else{
			$heading = array('Trucking Company', 'Invoice', 'Factoring Company', 'Delivery Date', 'Carrier Invoice Date', 'Carrier Invoice Ref NO', 'Rate', 'Aging Days', 'Customer Recieved Date');	
		}
				
			$data = array($heading);
			if(!empty($dispatch)) {
				$rate = 0;
				$totalRate = 0;
				foreach($dispatch as $row){
					$dispatchMeta = json_decode($row['dispatchMeta'],true);
					$dodate ='0000-00-00';
				    if($row['dodate']!='0000-00-00') {
   					   	$dodate = '="' . date('m-d-Y', strtotime($row['dodate'])) . '"';
				    }
				    if($table == 'warehouse_dispatch'){
						if($row['edate']!='0000-00-00') {
							$dodate = '="' . date('m-d-Y', strtotime($row['edate'])) . '"';
						}
						$invReady = $row['invoiceReadyDate'];
						$invPaid = $row['invoicePaidDate'];		
						$carrierInvDate = $row['custInvDate'];
				    }else{
						$invReady = $dispatchMeta['invoiceReadyDate'];
						$invPaid = $dispatchMeta['invoicePaidDate'];
						$carrierInvDate = $dispatchMeta['custInvDate'];	
				    }
					if(trim($invReady) != '' && trim($invReady) != '0000-00-00'){ 
						$invReady = '="' . date('m-d-Y', strtotime($invReady)) . '"';
					}
					if(trim($invPaid) != '' && trim($invPaid) != '0000-00-00'){ 
						$invPaid = '="' . date('m-d-Y', strtotime($invPaid)) . '"';
					}
					if(trim($carrierInvDate) != '' && trim($carrierInvDate) != '0000-00-00'){ 
						$carrierInvDate = '="' . date('m-d-Y', strtotime($carrierInvDate)) . '"';
					}
						
					if($row['bookedUnderNew'] == 4){
						$rate = $row['rate'] + $row['agentRate'];
					}else{
						$rate = $row['rate'];
					}
					$totalRate += $rate;
					$rateFormatted = round($rate, 2);

					$carrierPaymentType = $row['carrierPaymentType'];
					$factoringType = $row['factoringType'];
					$factoringCompany = $row['factoringCompany'];
					if($factoringCompany == ''){	
						if($factoringType == ''){
							$factoringCompany = $row['carrierPaymentType'];
						}else{
							$factoringCompany = $row['factoringType'];
						}
					}else{
						$factoringCompany = $row['factoringCompany'];
					}
				   $dataRow = array($row['ttruckingCompany'], $row['invoice'], $factoringCompany, $dodate, $carrierInvDate, $row['carrierInvoiceRefNo'], $rateFormatted, $row['days_diff'], $invPaid);
					$data[] = $dataRow;
				}
				$data[] = array('', '', '', '', '', 'Total', round($totalRate,2), '', '');
			}
			$this->load->library('excel_generator');
			$fileName = "AccountPayables.xlsx";   
			$this->excel_generator->generateExcel($data, $fileName);
			unlink($fileName);
			exit;
			die('csv');
	}
	function downloadPayableCSV($invoiceIds, $table){
		$invoiceIdsString = implode(',', array_map('intval', $invoiceIds));
		if($table == 'warehouse_dispatch'){
			$sql = "SELECT a.id, a.truckingcompany AS company_id, c.company as ttruckingCompany, IFNULL(NULLIF(a.carrierInvoiceRefNo, ''), '-') AS carrierInvoiceRefNo, c.company, bookedUnderOld.company as bookedUnderOld, a.invoice, a.pudate, a.dodate, a.edate, a.invoiceDate, (a.rate - IFNULL(a.carrierPartialAmt,0))  as rate, a.agentRate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), a.custInvDate) AS days_diff, a.carrierPayoutDate, d.fileurl AS doc_fileurl, carrierInvoice.fileurl AS carrierInvoice, a.dispatchMeta,bookedUnder, bookedUnderNew, booked_under.company as bookedUnder, a.carrierPaymentType, fact.company AS factoringCompany, a.factoringType, a.invoiceReadyDate, a.invoicePaidDate, a.custInvDate
			FROM warehouse_dispatch a
			LEFT JOIN truckingCompanies c ON c.id = a.truckingcompany
			LEFT JOIN booked_under ON booked_under.id=a.bookedUnderNew
			LEFT JOIN truckingCompanies bookedUnderOld ON bookedUnderOld.id = a.bookedUnder
			LEFT JOIN documentsOutside d ON d.did = a.id AND d.type = 'carrierGd'
			LEFT JOIN factoringCompanies fact ON fact.id = a.factoringCompany
			LEFT JOIN documentsOutside carrierInvoice ON carrierInvoice.did = a.id AND carrierInvoice.type = 'carrierInvoice'
			WHERE a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
			AND (a.custInvDate != '' && a.custInvDate != '0000-00-00' )
			AND a.id IN ($invoiceIdsString)
			GROUP BY a.id
			ORDER BY a.custInvDate ASC
			LIMIT 1000";
		}else{
			$sql = "SELECT a.id, a.truckingcompany AS company_id, c.company as ttruckingCompany, IFNULL(NULLIF(a.carrierInvoiceRefNo, ''), '-') AS carrierInvoiceRefNo, c.company, bookedUnderOld.company as bookedUnderOld, a.invoice, a.pudate, a.dodate, a.invoiceDate, (a.rate - IFNULL(a.carrierPartialAmt,0))  as rate, a.agentRate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) AS days_diff, a.carrierPayoutDate, d.fileurl AS doc_fileurl, carrierInvoice.fileurl AS carrierInvoice, a.dispatchMeta,bookedUnder, bookedUnderNew, booked_under.company as bookedUnder, a.carrierPaymentType, fact.company AS factoringCompany, a.factoringType
			FROM dispatchOutside a
			LEFT JOIN truckingCompanies c ON c.id = a.truckingcompany
			LEFT JOIN booked_under ON booked_under.id=a.bookedUnderNew
			LEFT JOIN truckingCompanies bookedUnderOld ON bookedUnderOld.id = a.bookedUnder
			LEFT JOIN documentsOutside d ON d.did = a.id AND d.type = 'carrierGd'
			LEFT JOIN factoringCompanies fact ON fact.id = a.factoringCompany
			LEFT JOIN documentsOutside carrierInvoice ON carrierInvoice.did = a.id AND carrierInvoice.type = 'carrierInvoice'
			WHERE a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
			AND JSON_VALID(a.dispatchMeta)
			AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
			AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
			AND a.id IN ($invoiceIdsString)
			GROUP BY a.id
			ORDER BY JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) ASC
			LIMIT 1000";
		}
		$query = $this->db->query($sql);
		return $query->result_array();
    }
	public function exportPdfPayables(){
		if(!checkPermission($this->session->userdata('permission'),'statementAcc')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $this->load->library('pdf');
        $pdf = $this->pdf->load(); 
		$table = $this->input->post('dispatchTable');
		$invoiceIds = json_decode($this->input->post('export_pdf_invoice_ids'), true);
		$dispatch = $this->downloadPayableCSV($invoiceIds, $table);
		$groupedData = [];

		foreach ($dispatch as $row) {
			$company = $row['ttruckingCompany'];
			$carrierPaymentType = $row['carrierPaymentType'];
			$factoringType = $row['factoringType'];
			$factoringCompany = $row['factoringCompany'];
			if($factoringCompany == ''){	
				if($factoringType == ''){
					$factoringCompany = $row['carrierPaymentType'];
				}else{
					$factoringCompany = $row['factoringType'];
				}
			}else{
				$factoringCompany = $row['factoringCompany'];
			}
			$invoice = $row['invoice'];
			$aging = (int)$row['days_diff'];
			$rate = (float)$row['rate'];

			if (!isset($groupedData[$company])) {
				$groupedData[$company] = [
					'company' => $company,
					'factoringCompany' => $factoringCompany,
					'invoices' => [],
					'aging_values' => [],
					'rate_total' => 0,
				];
			}
			$groupedData[$company]['invoices'][] = $invoice;
			$groupedData[$company]['aging_values'][] = $aging;
			$groupedData[$company]['rate_total'] += $rate;
		}
		// print_r($groupedData);exit;
		// $data['company'] = $this->Comancontroler_model->get_data_by_id($id,'companies');

		$data['invoice'] = array();
		$data['invoice'] = $groupedData;
		$data['table'] = $table;
    	////// generate csv 	
		$file = 'accountPayablesPDF';
		
		$html = $this->load->view('admin/'.$file, $data, true);
		//echo $html;die();
		
		$stylesheet = "";
		//$pdf->WriteHTML($stylesheet, 1);
		//$pdf->SetAutoPageBreak(true, 10);
		$pdf->WriteHTML($html);
        // write the HTML into the PDF
        $output = 'AccountPayables '.date('m-d-Y').'.pdf';
		$pdf->Output($output, "D");
		exit;
	}
}
?>