<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {
    
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->model('Comancontroler_model');
		$this->load->model('AllInvoices_model');

    	if( empty($this->session->userdata('logged') )) {
    		redirect(base_url('AdminLogin'));
    	}
		$this->load->library('PdfMergerNew');
		error_reporting(-1);
        ini_set('display_errors', 1);
        error_reporting(E_ERROR);
	}
	
	

    public function combine_html_with_pdfs() {
        $this->load->library('PdfMergerNew');
        // Example: Path to existing PDF files
        $files = array(
            FCPATH . 'assets/COMPARISON.pdf',
            FCPATH . 'assets/outside-dispatch/carrierInvoice/04-22-24-Trip--Carrier-Invoice-OSD-51242-Montpelier-Nut-Company.pdf'
        );

        // Combine HTML page with existing PDF files
        $this->pdfmergernew->merge_pdfs_with_images($files, 'combaine.pdf', 'D'); // 'D' to force download
    }
	public function index() {
	    if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    
	    if(isset($_GET['doccc']) && $_GET['doccc'] > 0){
	        $this->load->library('PdfMerger');
	        
	        $files = array(
                FCPATH . 'assets/InvoiceNSR100520B.pdf',
                FCPATH . 'assets/COMPARISON.pdf'
            );
    
            // Merge the PDFs and download the result
            $this->pdfmerger->merge_pdfs($files, 'combine.pdf', 'D');
            die('updated');
	    }
	    
	    
	    //////// download bol and rc doc //////// outside
	    if(isset($_GET['doc']) && $_GET['doc'] > 0){
	        $pdfArray = array();
	        $type = 'dispatch';
	        if(isset($_GET['type']) && $_GET['type'] == 'outside'){
	            $documents = $this->Comancontroler_model->get_document_by_dispach($_GET['doc'],'documentsOutside');
	            $type = 'outside';
	        } else {
	            $documents = $this->Comancontroler_model->get_document_by_dispach($_GET['doc']);
	        }
	        /*foreach($documents as $doc) {
				if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch') { 
				    $pdfArray[] = 'admin/download_pdf/upload/'.$doc['fileurl']; 
				}
				elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside'){
				    $pdfArray[] = 'admin/download_pdf/outside-dispatch--'.$doc['type'].'/'.$doc['fileurl']; 
				}
			}*/
			foreach($documents as $doc) {
				if($doc['type']=='rc' && $type == 'dispatch') { 
				    $pdfArray[] = 'admin/download_pdf/upload/'.$doc['fileurl']; 
				}
				elseif($doc['type']=='rc' && $type == 'outside'){
				    $pdfArray[] = 'admin/download_pdf/outside-dispatch--'.$doc['type'].'/'.$doc['fileurl']; 
				}
			}
	        foreach($documents as $doc) {
				if($doc['type']=='bol' && $type == 'dispatch') { 
				    $pdfArray[] = 'admin/download_pdf/upload/'.$doc['fileurl']; 
				}
				elseif($doc['type']=='bol' && $type == 'outside'){
				    $pdfArray[] = 'admin/download_pdf/outside-dispatch--'.$doc['type'].'/'.$doc['fileurl']; 
				}
			}
			
			
			header('Content-Type: application/json');
            echo json_encode($pdfArray);
	        die();
	    }
        /********* update status *********/
        if($this->input->post('statusonly') && $this->input->post('statusid'))	{
            $statusonly = $this->input->post('statusonly');
            $statusid = $this->input->post('statusid');
            if($statusonly!='' && $statusid > 0){
                $updatedata = array('status'=>$statusonly);
                $this->Comancontroler_model->update_table_by_id($statusid,'dispatch',$updatedata);
                die('updated');
            }
        }
        
        /********** quick update ************/
        if($this->input->post('did_input'))	{
				$this->form_validation->set_rules('did_input', 'dispatch id','required|min_length[1]');
				$this->form_validation->set_rules('type_input', 'dispatch id','required|min_length[1]');

				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else {
					 $id = $this->input->post('did_input');
					 
					 $type = $this->input->post('type_input');
					 if($type == 'dispatch') { $table = 'dispatch'; }
					 elseif($type == 'outside-dispatch'){ $table = 'dispatchOutside'; }
					 elseif($type == 'paWarehouse'){$table = 'warehouse_dispatch'; }
					 else { return false; }
					 
					$insert_data = array(
					    'rate'=>$this->input->post('rate_input'),
					    'parate'=>$this->input->post('parate_input'), 
					    // 'payoutAmount'=>$this->input->post('payoutAmount_input'),
					    'invoiceDate'=>$this->input->post('invoiceDate_input'),
					    //'invoice'=>$this->input->post('invoice_input'),
					    'expectPayDate'=>$this->input->post('expectPayDate_input'),
					    // 'invoiceType'=>$this->input->post('invoiceType_input'),
					    'status'=>$this->input->post('status_input')
					);
					if ($this->input->post('payoutAmount_input') !== '') {
						$insert_data['payoutAmount'] = $this->input->post('payoutAmount_input');
					}

					if ($this->input->post('invoiceType_input') !== '') {
						$insert_data['invoiceType'] = $this->input->post('invoiceType_input');
					}
				
					$res = $this->Comancontroler_model->update_table_by_id($id,$table,$insert_data); 
					if($res){
						echo 'done';
					}
 				   
				}
			die();
	    }
        
        //$sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        //$edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
        $company = $driver = $sdate = $edate = '';
        
        $data['dispatchURL'] = 'dispatch';
         
        if($this->input->post('search'))	{
            $company = $this->input->post('company');
            $driver = $this->input->post('driver');
            $dispatchType = $this->input->post('dispatchType');
            $invoiceType = $this->input->post('invoiceType');
			$invoiceStatus = $this->input->post('invoiceStatus');
            if($dispatchType == 'outsideDispatch'){ 
                $table = 'dispatchOutside'; 
                $data['dispatchURL'] = 'outside-dispatch';
            }else if($dispatchType == 'warehouseDispatch'){
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
			$data['dispatch'] = $this->Comancontroler_model->get_invoice_by_filter($table, $sdate, $edate, $company, $driver, $invoiceType,$invoiceStatus);
        } elseif($this->input->post('invoiceSearch')){
 			$company = $this->input->post('company');
            $driver = $this->input->post('driver');
            $dispatchType = $this->input->post('dispatchType');
            $invoiceType = $this->input->post('invoiceType');
            if($dispatchType == 'outsideDispatch'){ 
                $table = 'dispatchOutside'; 
                $data['dispatchURL'] = 'outside-dispatch';
            }else if($dispatchType == 'warehouseDispatch'){
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
			if($this->input->post('invoiceSearch') == 'pendingInvoices'){
				$data['dispatch'] = $this->AllInvoices_model->get_pending_invoices($table,$sdate,$edate,$company,$driver,$invoiceType);
			}
		}else {
            $data['dispatch'] = array();
        }
        // print_r($data['dispatchURL']);exit;
    	
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/invoice',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function downloadInvoicePDF(){
	    if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $this->load->library('pdf');
        $pdf = $this->pdf->load();
        
		$id = $this->uri->segment(3);
		$data['invoice'] = $data['childTrailer'] = array();
		$invoice = date('Y-m-d-H-i');

		if(isset($_GET['dTable']) && $_GET['dTable'] == 'dispatchOutside'){
			$file = 'invoiceOutsidePDF';
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'dispatchOutside');
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id);
				$redirectionAddress='admin/outside-dispatch/update/';
				$dispatchType='outside';
				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'dispatchOutside','rate,trailer,dispatchMeta');
					$dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
			} 
		}else if(isset($_GET['dTable']) && $_GET['dTable'] == 'warehouse_dispatch'){
			$file = 'invoiceWarehousePDF';
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'warehouse_dispatch');
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraWarehouseDispatchInfo($id);
				$where = array('did'=>$id,'dispatchType'=>'warehouse');
				$dispatchInfoDetails = $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_info_details','*','','','');
				if($dispatchInfoDetails){
					foreach($dispatchInfoDetails as $dis){
						$dispatchInfoTitle=$this->Comancontroler_model->get_data_by_column('id',$dis['dispatchInfoId'],'dispatchInfo','title','','','');
						$data['invoice']['dispatchInfoDetails'][] = [
							'dispatchInfoId' => $dis['dispatchInfoId'],
							'dispatchValue' => $dis['dispatchValue'],
							'dispatchInfoTitle' => $dispatchInfoTitle[0]['title']
						];
					}
				} else {
					$data['invoice']['dispatchInfoDetails'] = [
						[
							'dispatchInfoId' => '',
							'dispatchValue' => '',
							'dispatchInfoTitle' =>''
						]
					];		
				}
				$redirectionAddress='admin/paWarehouse/update/';
				$dispatchType='warehouse';
				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'warehouse_dispatch','*');
					$dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
			} 
		} else {
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id);
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraDispatchInfo($id);
				$unitPriceSql = "SELECT unit,unitPrice,unitDescription FROM unitPrice WHERE dispatchId='$id'";
				$unitPrice = $this->db->query($unitPriceSql)->result_array();

				$invoiceAmountSql = "SELECT parate FROM dispatch WHERE id='$id'";
				$invoiceAmount = $this->db->query($invoiceAmountSql)->row()->parate;

				$data['dynamicUnitPrice']=$unitPrice;
				$redirectionAddress='admin/dispatch/update/';
				$dispatchType='fleet';

				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'dispatch','rate,trailer,dispatchMeta');
				    $dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatchOutside','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
			} 
			$file = 'invoicePDF';
		}

		$data['unitprice'] = $data['unitTotal'] = '0';
		if($this->input->post('invoiceName'))	{ 
		    $data['invoiceDate'] = $this->input->post('invoiceDate'); 
		    $data['contactPerson'] = $this->input->post('contactPerson'); 
		    $data['cdepartment'] = $this->input->post('cdepartment'); 
		    $data['cemail'] = $this->input->post('cemail'); 
		    $data['cphone'] = $this->input->post('cphone'); 
		    $data['invoiceNotes'] = $this->input->post('invoiceNotes');
		    $data['dropoff'] = $this->input->post('dropoff');
		    $data['pickup'] = $this->input->post('pickup');
			$data['pickupExtra'] = $this->input->post('pickupExtra');
		    $data['dropoffExtra'] = $this->input->post('dropoffExtra');
		    $data['expPrice'] = $this->input->post('expPrice');
		    $data['expName'] = $this->input->post('expName');
		    $data['trackingLabel'] = $this->input->post('trackingLabel');
		    $data['tracking'] = $this->input->post('tracking');
		    $data['bookingnoLabel'] = $this->input->post('bookingnoLabel');
		    $data['bookingno'] = $this->input->post('bookingno');
		    $data['trailerValLabel'] = $this->input->post('trailerLabel');
		    $data['trailerVal'] = $this->input->post('trailer');
		    $data['unitTotal'] = $this->input->post('unit');
		    $data['unitprice'] = $this->input->post('unitprice');
			$data['unitDescription'] = $this->input->post('unitDescription');
			if($dispatchType=='fleet'){
				$data['dynamicUnits'] = [];
				$data['dynamicUnits'][] = [
					'unit' => $this->input->post('unit'),
					'unitprice' => $this->input->post('unitprice'),
					'unitDescription' => $this->input->post('unitDescription')
				];
				
				$units = $this->input->post('unitA');
				$unitPrices = $this->input->post('unitPriceA');
				$unitDescriptions = $this->input->post('unitDescriptionA');
				if (!empty($units) && !empty($unitPrices)) {
					foreach ($units as $index => $unit) {
						$data['dynamicUnits'][] = [
							'unit' => $unit,
							'unitprice' => $unitPrices[$index],
							'unitDescription'=>$unitDescriptions[$index]
						];
					}
				}		
				$totalCalculatedAmount = 0;
				foreach ($data['dynamicUnits'] as $dynamicUnit) {
					$totalCalculatedAmount += $dynamicUnit['unit'] * $dynamicUnit['unitprice'];
				}
				$isValid = false;
				foreach ($data['dynamicUnits'] as $item) {
					if (is_array($item) && isset($item['unit']) && $item['unit'] > 0) {
						$isValid = true;
						break; 
					}
				}
				if($isValid){
					$this->db->query("DELETE FROM unitPrice WHERE dispatchId = $id AND dispatchType='$dispatchType'");
					$insertSuccessful = true; 
					foreach ($data['dynamicUnits'] as $unitData) {
						$unit = $unitData['unit'];
						$unitPrice = $unitData['unitprice'];
						$unitDescription = $unitData['unitDescription'];
						if ($unit <= 0) {
							continue;
						}				
						$sql = "INSERT INTO unitPrice (dispatchId, dispatchType, unit, unitPrice, unitDescription) VALUES (?, ?, ?, ?, ?)";
						if (!$this->db->query($sql, [$id, $dispatchType, $unit, $unitPrice, $unitDescription])) {
							$insertSuccessful = false; 
							break; 
						}
					}
					if ($insertSuccessful) {
						$this->session->set_flashdata('item', 'Unit and unit price updated successfully.');
					} else {
						$this->session->set_flashdata('error', 'Failed to update unit and unit price.');
					}
				}else{
					$this->db->query("DELETE FROM unitPrice WHERE dispatchId = $id AND dispatchType='$dispatchType'");
					$this->session->set_flashdata('item', 'Unit and unit price updated successfully.');
				}
				if ($totalCalculatedAmount == $invoiceAmount) {
				} else {
					$this->session->set_flashdata('item', 'Sum of (unit * unit prices) not matching invoice amount .');
				} 
				redirect(base_url($redirectionAddress . $id . '?invoice'));				
					
			}
			} else {
		    $data['trackingLabel'] = 'Customer Ref No.';
		    $data['bookingnoLabel'] = 'Booking No.';
		    $data['trailerValLabel'] = '';
		    $data['trailerVal'] = $data['bookingno'] = $data['tracking'] = $data['expPrice'] = $data['expName'] = $data['invoiceDate'] = $data['dropoffExtra'] = $data['dropoff'] = $data['pickup'] = $data['contactPerson'] = $data['cdepartment'] = $data['cemail'] = $data['cphone'] = $data['invoiceNotes'] = '';
			if(isset($_GET['dTable']) && $_GET['dTable'] == 'warehouse_dispatch'){
				$data['trackingLabel'] = 'PO No.';
				$data['expense'] = [];
				if (!empty($id)) {
					$details = $this->db->select('expenseInfoId, expenseInfoValue')
						->from('dispatch_expense_details')
						->where('did', $id)
						->where('dispatchType', 'warehouse')
						->where('expenseType', 'customer')
						->get()
						->result_array();
					$expenseTitles = [];
					$titles = $this->Comancontroler_model->get_data_by_column('status', 'Active', 'expenses', 'id,title,type', 'id', 'asc');
					foreach ($titles as $t) {
						$expenseMeta[$t['id']] = [
							'title' => $t['title'],
							'type'  => $t['type'],
						];
					}
					foreach ($details as $row) {
						$meta = isset($expenseMeta[$row['expenseInfoId']]) ? $expenseMeta[$row['expenseInfoId']] : null;
						$title = $meta ? $meta['title'] : 'Unknown';
						$type  = $meta ? $meta['type'] : 'positive'; 
						$value = $row['expenseInfoValue'];

						$value = ($type === 'negative') ? -abs($value) : abs($value);

						if (!isset($data['expense'][$title])) {
							$data['expense'][$title] = [
								'price' => $value,
								'unit'  => [$value],
								'type'  => $type  
							];
						} else {
							$data['expense'][$title]['price'] += $value;
							$data['expense'][$title]['unit'][] = $value;
						}
					}
					$where = array('did'=>$id,'dispatchType'=>'warehouse');
					$data['customExpenseDetails'] =  $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_custom_expense_details','*','','','');
					$data['warehouse_expense']=$data['expense'];
				}
			}
		}
		
		$html = $this->load->view('admin/'.$file.'New', $data, true);
		//echo $html;die();
		$stylesheet = "";
		$pdf->WriteHTML($html);
        
        if(isset($_GET['invoiceWithPdf']) && $_GET['invoiceWithPdf'] == 'bol-rc' && 1==2){
			
			$pdfArray = array();
            
            $output = FCPATH.'Invoice-#-'.$invoice.'.pdf';
		    $pdf->Output($output, "F"); // I D F
		    $pdfArray[] = $output;
		    
	        $type = 'dispatch';
	        if(isset($_GET['type']) && $_GET['type'] == 'outside'){
	            $documents = $this->Comancontroler_model->get_document_by_dispach($id,'documentsOutside');
	            $type = 'outside';
	        } else {
	            $documents = $this->Comancontroler_model->get_document_by_dispach($id);
	        }
	        if($documents){
    	        foreach($documents as $doc) {
    				if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch' && stristr($doc['fileurl'],'.pdf')) { 
    				    $pdfArray[] = FCPATH.'assets/upload/'.$doc['fileurl']; 
    				}
    				elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside' && stristr($doc['fileurl'],'.pdf')){
    				    $pdfArray[] = FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl']; 
    				}
    			}
	        }
			
	        $this->load->library('PdfMergerNew');
    
            // Merge the PDFs and download the result
            try {
				$this->pdfmergernew->merge_pdfs_with_images($pdfArray, 'Invoice # '.$invoice.'.pdf','D'); // 'D' to force download
            } catch(Exception $e) {
                echo 'Message: Some pdf files are not proper format'; // .$e->getMessage();
            }
            
            
            if($output!='' && file_exists($output)) {
                unlink($output);  
            }
            exit;

		}
        elseif(isset($_GET['invoiceWithPdf']) && $_GET['invoiceWithPdf'] == 'bol-rc'){
            
            $pdfArray = array();
            
            $output = FCPATH.'Invoice-#-'.$invoice.'.pdf';
		    $pdf->Output($output, "F"); // I D F
		    $pdfArray[] = $output;
		    
	        $type = 'dispatch';
	        if(isset($_GET['type']) && $_GET['type'] == 'outside'){
	            $documents = $this->Comancontroler_model->get_document_by_dispach($id,'documentsOutside');
	            $type = 'outside';
	        } else {
	            $documents = $this->Comancontroler_model->get_document_by_dispach($id);
	        }
	        if($documents){
    	        foreach($documents as $doc) {
    				if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch') { 
    				    // $pdfArray[] = FCPATH.'assets/upload/'.$doc['fileurl']; 
						if ($doc['type'] == 'rc') {
							if (stristr($doc['fileurl'], '.pdf')) {
								$rcFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
							} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
								list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
								$rcImages[] = [
									'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
									'width' => $width,
									'height' => $height,
								];
							}
						} elseif ($doc['type'] == 'bol') {
							if (stristr($doc['fileurl'], '.pdf')) {
								$bolFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
							} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
								list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
								$bolImages[] = [
									'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
									'width' => $width,
									'height' => $height,
								];
							}
						}
    				}
    				elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside'){
						if ($doc['type'] == 'rc') {
							if (stristr($doc['fileurl'], '.pdf')) {
								$rcFiles[] = FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
							} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
								list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
								$rcImages[] = [
									'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
									'width' => $width,
									'height' => $height,
								];
							}
						} elseif ($doc['type'] == 'bol') {
							if (stristr($doc['fileurl'], '.pdf')) {
								$bolFiles[] =FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
							} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
								list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
								$bolImages[] = [
									'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
									'width' => $width,
									'height' => $height,
								];
							}
						}
					
    				}
    			}
	        }
			$this->PdfMergerNew = new PdfMergerNew();
			if (!empty($rcImages)) {
				$rcImagePdf = FCPATH . 'assets/temp/rc_combined_images.pdf';
				$this->PdfMergerNew->combine_images_to_pdf($rcImages, 'F', $rcImagePdf);
				$rcFiles[] = $rcImagePdf;
			}
			
			if (!empty($bolImages)) {
				$bolImagePdf = FCPATH . 'assets/temp/bol_combined_images.pdf';
				$this->PdfMergerNew->combine_images_to_pdf($bolImages, 'F', $bolImagePdf);
				$bolFiles[] = $bolImagePdf;
			}
			$pdfArray = array_merge($pdfArray, $bolFiles);
			$pdfArray = array_merge($pdfArray, $rcFiles);
			
	        $this->load->library('PdfMerger');
	        
            try {
                $this->pdfmerger->merge_pdfs($pdfArray, 'Invoice # '.$invoice.'.pdf', 'D');
            } catch(Exception $e) {
				$this->load->library('PdfMergerNewNew');
				try {
					$this->pdfmergernewnew->addPdf($pdfArray);
                    $this->pdfmergernewnew->download('Invoice # '.$invoice.'.pdf');
				} catch(Exception $e) {
					echo 'Message: Some pdf files are not proper format'; 
				}
				/*$this->load->library('PdfMergerNew');
				// Merge the PDFs and download the result
				try {
					$this->pdfmergernew->merge_pdfs_with_images($pdfArray, 'Invoice # '.$invoice.'.pdf','D'); // 'D' to force download
				} catch(Exception $e) {
					echo 'Message: Some pdf files are not proper format'; // .$e->getMessage();
				}*/
            }
            
            
            if($output!='' && file_exists($output)) {
                unlink($output);  
            }
            exit;
	    } 
	    else {
            $output = 'Invoice # '.$invoice.'.pdf';
            if($this->input->post('invoiceName'))	{ $output = $this->input->post('invoiceName'); }
		    $pdf->Output($output, "D"); // I D F
	    }
		exit;
	}
	public function getAvailableFiles($id) {
		$type = 'dispatch'; 
		if ($this->input->get('type') === 'dispatchOutside') {
			$documents = $this->Comancontroler_model->get_document_by_dispach($id, 'documentsOutside');
			$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'dispatchOutside');
			
			$parentInvoice = '';
			$parentID = '';
			$parentInvoiceSql="SELECT parentInvoice FROM dispatchOutside WHERE id=$id";
			$parentInvoiceResult = $this->db->query($parentInvoiceSql)->row();
			$parentInvoice=$parentInvoiceResult->parentInvoice;
			if(isset($parentInvoice) && $parentInvoice != ''){
				$parentID = $this->Comancontroler_model->get_data_by_column('invoice',$parentInvoice,'dispatchOutside','id');
			}

			if($parentID){
	            if($parentID[0]['id'] > 0) {
	                $parentDocuments = $this->Comancontroler_model->get_document_by_dispach($parentID[0]['id'],'documentsOutside');
	                if($parentDocuments){
            	        foreach($parentDocuments as $doc){
            	            $doc['parent'] = 'yes'; 
            	            $parentDocuments['parentDocuments'][] = $doc;
            	        }
            	    }

	            }
	        }
			
			$otherParentInvoice = '';	
			$otherParentID = '';
			$otherParentInvoiceSql="SELECT otherParentInvoice FROM dispatchOutside WHERE id=$id";
			$otherParentInvoiceResult = $this->db->query($otherParentInvoiceSql)->row();
			$otherParentInvoice=$otherParentInvoiceResult->otherParentInvoice;
			// echo $otherParentInvoiceSql;exit;
			if(isset($otherParentInvoice) && $otherParentInvoice != ''){
				$otherParentID = $this->Comancontroler_model->get_data_by_column('invoice',$otherParentInvoice,'dispatch','id');
			}
			if($otherParentID){
	            if($otherParentID[0]['id'] > 0) {
	                $otherParentDocuments = $this->Comancontroler_model->get_document_by_dispach($otherParentID[0]['id'],'documents');

	                if($otherParentDocuments){
            	        foreach($otherParentDocuments as $doc){
            	            $doc['otherParent'] = 'yes'; 
            	            $otherParentDocuments['otherDocuments'][] = $doc;
            	        }
            	    }

	            }
	        }

			$type = 'outside'; 
		} elseif ($this->input->get('type') === 'warehouse_dispatch') {
			$documents = $this->Comancontroler_model->get_document_by_dispach($id, 'warehouse_documents');
			$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'warehouse_dispatch');
			$type = 'warehouse'; 
		}
		 else {
			$documents = $this->Comancontroler_model->get_document_by_dispach($id);
			$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'dispatch');
			
			$parentInvoice = '';
			$parentID = '';
			$parentInvoiceSql="SELECT parentInvoice FROM dispatch WHERE id=$id";
			$parentInvoiceResult = $this->db->query($parentInvoiceSql)->row();
			$parentInvoice=$parentInvoiceResult->parentInvoice;
			if(isset($parentInvoice) && $parentInvoice != ''){
				$parentID = $this->Comancontroler_model->get_data_by_column('invoice',$parentInvoice,'dispatch','id');
			}
			if($parentID){
	            if($parentID[0]['id'] > 0) {
	                $parentDocuments = $this->Comancontroler_model->get_document_by_dispach($parentID[0]['id'],'documents');
	                if($parentDocuments){
            	        foreach($parentDocuments as $doc){
            	            $doc['parent'] = 'yes'; 
            	            $parentDocuments['parentDocuments'][] = $doc;
            	        }
            	    }

	            }
	        }

			$otherParentInvoice = '';	
			$otherParentID = '';
			$otherParentInvoiceSql="SELECT otherParentInvoice FROM dispatch WHERE id=$id";
			$otherParentInvoiceResult = $this->db->query($otherParentInvoiceSql)->row();
			$otherParentInvoice=$otherParentInvoiceResult->otherParentInvoice;
			// echo $otherParentInvoiceSql;exit;
			if(isset($otherParentInvoice) && $otherParentInvoice != ''){
				$otherParentID = $this->Comancontroler_model->get_data_by_column('invoice',$otherParentInvoice,'dispatchOutside','id');
			}
			if($otherParentID){
	            if($otherParentID[0]['id'] > 0) {
	                $otherParentDocuments = $this->Comancontroler_model->get_document_by_dispach($otherParentID[0]['id'],'documentsOutside');

	                if($otherParentDocuments){
            	        foreach($otherParentDocuments as $doc){
            	            $doc['otherParent'] = 'yes'; 
            	            $otherParentDocuments['otherDocuments'][] = $doc;
            	        }
            	    }

	            }
	        }
		}
	
		$rcFiles = [];
		$bolFiles = [];
		$rcImages = [];
		$bolImages = [];
		$inwrdFiles = [];
		$outwrdFiles = [];
		$inwrdImages = [];
		$outwrdImages = [];
		$parentRcFiles = [];
		$parentBolFiles = [];
		$parentRcImages = [];
		$parentBolImages = [];
		$otherParentRcFiles = [];
		$otherParentBolFiles = [];
		$otherParentRcImages = [];
		$otherParentBolImages = [];
		$cEmails=$data['invoice'][0]['cemail2'];
		if (!empty($documents)) {
			foreach ($documents as $doc) {
				if (($doc['type'] == 'bol' || $doc['type'] == 'rc') && $type == 'dispatch') { 
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$rcFiles[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {    
							$rcImages[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$bolFiles[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							$bolImages[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						}
					}
				} elseif (($doc['type'] == 'bol' || $doc['type'] == 'rc') && $type == 'outside') {
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$rcFiles[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {    
							$rcImages[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$bolFiles[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							$bolImages[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						}
					}
				} elseif (($doc['type'] == 'inwrd' || $doc['type'] == 'outwrd') && $type == 'warehouse') {
					if ($doc['type'] == 'inwrd') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$inwrdFiles[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {    
							$inwrdImages[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						}
					} elseif ($doc['type'] == 'outwrd') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$outwrdFiles[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							$outwrdImages[] = [
								'id' => $doc['id'], 
								'name' => $doc['fileurl']
							];
						}
					}
				}
			}
		}
		if (!empty($parentDocuments)) {
			foreach ($parentDocuments['parentDocuments'] as $parentdoc) {
				if (($parentdoc['type'] == 'bol' || $parentdoc['type'] == 'rc')) {
					if ($parentdoc['type'] == 'rc') {

						if (stristr($parentdoc['fileurl'], '.pdf')) {
							$parentRcFiles[] = [
								'id' => $parentdoc['id'], 
								'name' => $parentdoc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $parentdoc['fileurl'])) {    
							$parentRcImages[] = [
								'id' => $parentdoc['id'], 
								'name' => $parentdoc['fileurl']
							];
						}
					} elseif ($parentdoc['type'] == 'bol') {
						if (stristr($parentdoc['fileurl'], '.pdf')) {
							$parentBolFiles[] = [
								'id' => $parentdoc['id'], 
								'name' => $parentdoc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $parentdoc['fileurl'])) {
							$parentBolImages[] = [
								'id' => $parentdoc['id'], 
								'name' => $parentdoc['fileurl']
							];
						}
					}
				}
			}
		}
		if (!empty($otherParentDocuments)) {
			foreach ($otherParentDocuments['otherDocuments'] as $otherParentdoc) {
				if (($otherParentdoc['type'] == 'bol' || $otherParentdoc['type'] == 'rc')) {
					if ($otherParentdoc['type'] == 'rc') {

						if (stristr($otherParentdoc['fileurl'], '.pdf')) {
							$otherParentRcFiles[] = [
								'id' => $otherParentdoc['id'], 
								'name' => $otherParentdoc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $otherParentdoc['fileurl'])) {    
							$otherParentRcImages[] = [
								'id' => $otherParentdoc['id'], 
								'name' => $otherParentdoc['fileurl']
							];
						}
					} elseif ($otherParentdoc['type'] == 'bol') {
						if (stristr($otherParentdoc['fileurl'], '.pdf')) {
							$otherParentBolFiles[] = [
								'id' => $otherParentdoc['id'], 
								'name' => $otherParentdoc['fileurl']
							];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $otherParentdoc['fileurl'])) {
							$otherParentBolImages[] = [
								'id' => $otherParentdoc['id'], 
								'name' => $otherParentdoc['fileurl']
							];
						}
					}
				}
			}
		}
	
		echo json_encode([
			'rc_files' => $rcFiles,
			'bol_files' => $bolFiles,
			'rc_images' => $rcImages,
			'bol_images' => $bolImages,
			'inwrd_files' => $inwrdFiles,
			'outwrd_files' => $outwrdFiles,
			'inwrd_images' => $inwrdImages,
			'outwrd_images' => $outwrdImages,
			'parent_rc_files' => $parentRcFiles,
			'parent_bol_files' => $parentBolFiles,
			'parent_rc_images' => $parentRcImages,
			'parent_bol_images' => $parentBolImages,
			'other_parent_rc_files' => $otherParentRcFiles,
			'other_parent_bol_files' => $otherParentBolFiles,
			'other_parent_rc_images' => $otherParentRcImages,
			'other_parent_bol_images' => $otherParentBolImages,
			'type' => $type,
			'cEmails' => $cEmails
		]);
	}
	public function combineSelectedPDFs(){
		if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $this->load->library('pdf');
        $pdf = $this->pdf->load();
        
		// $id = $this->uri->segment(3);
		$id = $this->input->get('dispatch_id'); 
		$data['invoice'] = $data['childTrailer'] = array();
		$invoice = date('Y-m-d-H-i');
		$dispatch_type = $this->input->get('dispatch_type');
		$file_ids = $this->input->get('file_ids'); 
		$parent_file_ids = $this->input->get('parent_file_ids'); 
		$other_parent_file_ids = $this->input->get('other_parent_file_ids'); 
		if ($dispatch_type=='dispatch'){
			$dtable='dispatch';
		} elseif ($dispatch_type=='warehouse_dispatch'){
			$dtable='warehouse_dispatch';
		} else{
			$dtable='dispatchOutside';
		}

		if($dtable == 'dispatchOutside'){
			$file = 'invoiceOutsidePDF';
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'dispatchOutside');
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id);
				$redirectionAddress='admin/outside-dispatch/update/';
				$dispatchType='outside';
				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'dispatchOutside','rate,trailer,dispatchMeta');
					$dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
			} 
		} elseif($dtable == 'warehouse_dispatch'){
			$file = 'invoiceWarehousePDF';
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'warehouse_dispatch');
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraWarehouseDispatchInfo($id);
				$where = array('did'=>$id,'dispatchType'=>'warehouse');
				$dispatchInfoDetails = $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_info_details','*','','','');
				if($dispatchInfoDetails){
					foreach($dispatchInfoDetails as $dis){
						$dispatchInfoTitle=$this->Comancontroler_model->get_data_by_column('id',$dis['dispatchInfoId'],'dispatchInfo','title','','','');
						$data['invoice']['dispatchInfoDetails'][] = [
							'dispatchInfoId' => $dis['dispatchInfoId'],
							'dispatchValue' => $dis['dispatchValue'],
							'dispatchInfoTitle' => $dispatchInfoTitle[0]['title']
						];
					}
				} else {
					$data['invoice']['dispatchInfoDetails'] = [
						[
							'dispatchInfoId' => '',
							'dispatchValue' => '',
							'dispatchInfoTitle' =>''
						]
					];		
				}
				$redirectionAddress='admin/paWarehouse/update/';
				$dispatchType='warehouse';
				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'warehouse_dispatch','*');
					$dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
				$data['trackingLabel'] = 'PO No.';
				$data['expense'] = [];
				if (!empty($id)) {
					$details = $this->db->select('expenseInfoId, expenseInfoValue')
						->from('dispatch_expense_details')
						->where('did', $id)
						->where('dispatchType', 'warehouse')
						->where('expenseType', 'customer')
						->get()
						->result_array();
					$expenseTitles = [];
					$titles = $this->Comancontroler_model->get_data_by_column('status', 'Active', 'expenses', 'id,title,type', 'id', 'asc');
					foreach ($titles as $t) {
						$expenseMeta[$t['id']] = [
							'title' => $t['title'],
							'type'  => $t['type'],
						];
					}
					foreach ($details as $row) {
						$meta = isset($expenseMeta[$row['expenseInfoId']]) ? $expenseMeta[$row['expenseInfoId']] : null;
						$title = $meta ? $meta['title'] : 'Unknown';
						$type  = $meta ? $meta['type'] : 'positive'; 
						$value = $row['expenseInfoValue'];

						$value = ($type === 'negative') ? -abs($value) : abs($value);

						if (!isset($data['expense'][$title])) {
							$data['expense'][$title] = [
								'price' => $value,
								'unit'  => [$value],
								'type'  => $type  
							];
						} else {
							$data['expense'][$title]['price'] += $value;
							$data['expense'][$title]['unit'][] = $value;
						}
					}
					$where = array('did'=>$id,'dispatchType'=>'warehouse');
					$data['customExpenseDetails'] =  $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_custom_expense_details','*','','','');
					$data['warehouse_expense']=$data['expense'];
				}
			} 
		} else {
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id);
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraDispatchInfo($id);
				$unitPriceSql = "SELECT unit,unitPrice,unitDescription FROM unitPrice WHERE dispatchId='$id' AND dispatchType='fleet'";
				$unitPrice = $this->db->query($unitPriceSql)->result_array();
				$invoiceAmountSql = "SELECT parate FROM dispatch WHERE id='$id'";
				$invoiceAmount = $this->db->query($invoiceAmountSql)->row()->parate;

				$data['dynamicUnitPrice']=$unitPrice;
				$redirectionAddress='admin/dispatch/update/';
				$dispatchType='fleet';

				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'dispatch','rate,trailer,dispatchMeta');
				    $dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatchOutside','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
			} 
			$file = 'invoicePDF';
		}

		$data['unitprice'] = $data['unitTotal'] = '0';
		if($this->input->post('invoiceName')){ 
			} else {
		    $data['trackingLabel'] = 'Customer Ref No.';
		    $data['bookingnoLabel'] = 'Booking No.';
		    $data['trailerValLabel'] = '';
		    $data['trailerVal'] = $data['bookingno'] = $data['tracking'] = $data['expPrice'] = $data['expName'] = $data['invoiceDate'] = $data['dropoffExtra'] = $data['dropoff'] = $data['pickup'] = $data['contactPerson'] = $data['cdepartment'] = $data['cemail'] = $data['cphone'] = $data['invoiceNotes'] = '';
		}

		$html = $this->load->view('admin/'.$file.'New', $data, true);
		//echo $html;die();
		$stylesheet = "";		
		$pdf->WriteHTML($html);
		// echo $pdf; exit;
        $pdfArray = array();
        $output = FCPATH.'Invoice-#-'.$invoice.'.pdf';
	    $pdf->Output($output, "F"); // I D F
		$pdfArray[] = $output;
	    $rcFiles = [];
		$bolFiles = [];
		$rcImages = [];
		$bolImages = [];
		$inwrdFiles = [];
		$outwrdFiles = [];
		$inwrdImages = [];
		$outwrdImages = [];
		$parentRcFiles = [];
		$parentBolFiles = [];
		$parentRcImages = [];
		$parentBolImages = [];
		$otherParentRcFiles = [];
		$otherParentBolFiles = [];
		$otherParentRcImages = [];
		$otherParentBolImages = [];
        $type = 'dispatch';
	    if($dispatch_type == 'dispatch'){
			$documents = $this->Comancontroler_model->get_documents_by_ids($file_ids);
			$otherParentdocuments = $this->Comancontroler_model->get_documents_by_ids($other_parent_file_ids,'documentsOutside');
			$parentdocuments = $this->Comancontroler_model->get_documents_by_ids($parent_file_ids);
	    } elseif($dispatch_type == 'warehouse_dispatch'){
			$documents = $this->Comancontroler_model->get_documents_by_ids($file_ids, 'warehouse_documents');
	        $type = 'warehouse';
		}else {
			$documents = $this->Comancontroler_model->get_documents_by_ids($file_ids,'documentsOutside');
			$parentdocuments = $this->Comancontroler_model->get_documents_by_ids($parent_file_ids, 'documentsOutside');
			$otherParentdocuments = $this->Comancontroler_model->get_documents_by_ids($other_parent_file_ids);
	        $type = 'outside';
	    }
	   if($documents){
	        foreach($documents as $doc) {
    			if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch') { 
    			    // $pdfArray[] = FCPATH.'assets/upload/'.$doc['fileurl']; 
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$rcFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$rcImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$bolFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$bolImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}
    			}
    			elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside'){
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$rcFiles[] = FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$rcImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$bolFiles[] =FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$bolImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}				
    			} elseif (($doc['type'] == 'inwrd' || $doc['type'] == 'outwrd') && $type == 'warehouse') {
					if ($doc['type'] == 'inwrd') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$inwrdFiles[] = FCPATH.'assets/warehouse/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH . 'assets/warehouse/'.$doc['type'].'/'. $doc['fileurl']);
							$inwrdImages[] = [
								'file' => FCPATH . 'assets/warehouse/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'outwrd') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$outwrdFiles[] =FCPATH.'assets/warehouse/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH . 'assets/warehouse/'.$doc['type'].'/'. $doc['fileurl']);
							$outwrdImages[] = [
								'file' => FCPATH . 'assets/warehouse/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}
				}
    		}
	    }

		if($parentdocuments){
	        foreach($parentdocuments as $doc) {
    			if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch') { 
    			    // $pdfArray[] = FCPATH.'assets/upload/'.$doc['fileurl']; 
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$parentRcFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$parentRcImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$parentBolFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$parentBolImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}
    			}
    			elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside'){
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$parentRcFiles[] = FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$parentRcImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$parentBolFiles[] =FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$parentBolImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}				
    			}
    		}
	    }

		if($otherParentdocuments){
	        foreach($otherParentdocuments as $doc) {
    			if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside') { 
    			    // $pdfArray[] = FCPATH.'assets/upload/'.$doc['fileurl']; 
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$otherParentRcFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$otherParentRcImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$otherParentBolFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$otherParentBolImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}
    			}
    			elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch'){
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$otherParentRcFiles[] = FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$otherParentRcImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$otherParentBolFiles[] =FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$otherParentBolImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}				
    			}
    		}
	    }
		$this->PdfMergerNew = new PdfMergerNew();

		if (!empty($parentRcImages)) {
			$parentRcmagePdf = FCPATH . 'assets/temp/parent_rc_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($parentRcImages, 'F', $parentRcmagePdf);
			$rcFiles[] = $parentRcmagePdf;
		}
			
		if (!empty($parentBolImages)) {
			$parentBolImagePdf = FCPATH . 'assets/temp/parent_bol_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($parentBolImages, 'F', $parentBolImagePdf);
			$bolFiles[] = $parentBolImagePdf;
		}

		if (!empty($otherParentRcImages)) {
			$otherParentRcmagePdf = FCPATH . 'assets/temp/other_parent_rc_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($otherParentRcImages, 'F', $otherParentRcmagePdf);
			$rcFiles[] = $otherParentRcmagePdf;
		}
			
		if (!empty($otherParentBolImages)) {
			$otherParentBolImagePdf = FCPATH . 'assets/temp/other_parent_bol_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($otherParentBolImages, 'F', $otherParentBolImagePdf);
			$bolFiles[] = $otherParentBolImagePdf;
		}

		if (!empty($rcImages)) {
			$rcImagePdf = FCPATH . 'assets/temp/rc_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($rcImages, 'F', $rcImagePdf);
			$rcFiles[] = $rcImagePdf;
		}
			
		if (!empty($bolImages)) {
			$bolImagePdf = FCPATH . 'assets/temp/bol_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($bolImages, 'F', $bolImagePdf);
			$bolFiles[] = $bolImagePdf;
		}

		if (!empty($inwrdImages)) {
			$inwrdImagePdf = FCPATH . 'assets/temp/inwrd_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($inwrdImages, 'F', $inwrdImagePdf);
			$inwrdFiles[] = $inwrdImagePdf;
		}
			
		if (!empty($outwrdImages)) {
			$outwrdImagePdf = FCPATH . 'assets/temp/outwrd_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($outwrdImages, 'F', $outwrdImagePdf);
			$outwrdFiles[] = $outwrdImagePdf;
		}

		$pdfArray = array_merge($pdfArray, $parentBolFiles);
		$pdfArray = array_merge($pdfArray, $otherParentBolFiles);
		$pdfArray = array_merge($pdfArray, $bolFiles);

		$pdfArray = array_merge($pdfArray, $parentRcFiles);
		$pdfArray = array_merge($pdfArray, $otherParentRcFiles);
		$pdfArray = array_merge($pdfArray, $rcFiles);

		if( $type == 'warehouse'){
			$pdfArray = array_merge($pdfArray, $inwrdFiles);
			$pdfArray = array_merge($pdfArray, $outwrdFiles);
		}
			
		$cleanedFiles = [];
		foreach ($pdfArray as $file) {
			$cleanedFile = sys_get_temp_dir() . '/' . uniqid() . '_cleaned.pdf';
			$command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($cleanedFile) . " " . escapeshellarg($file);
			exec($command);
			$cleanedFiles[] = file_exists($cleanedFile) ? $cleanedFile : $file;
		}
		$pdfArray = $cleanedFiles;
	    $this->load->library('PdfMerger');    
        try {
            $this->pdfmerger->merge_pdfs($pdfArray, 'Invoice # '.$invoice.'.pdf', 'D');
			$this->pdfmerger->download('Invoice # '.$invoice.'.pdf');
        } catch(Exception $e) {
			$this->load->library('PdfMergerNewNew');
			try {
				set_time_limit(300); 
		        $this->pdfmergernewnew->addPdf($pdfArray, 100, 30);
				// $this->pdfmergernewnew->addPdf($pdfArray);
                $this->pdfmergernewnew->download('Invoice # '.$invoice.'.pdf');
			} catch(Exception $e) {
				echo 'Message: Some pdf files are not proper format'; 
			}
        }    
        if($output!='' && file_exists($output)) {
            unlink($output);  
        }
        exit;
	}
	public function emailInvoice(){
		if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
		// echo 'test';exit;
		$adminid = $this->session->userdata('adminid'); 
		if($adminid){
			$adminSql = "SELECT * FROM admin_login WHERE id='$adminid'";
			$adminResult = $this->db->query($adminSql)->row();
			$username = $adminResult->uname;
			$email = $adminResult->email;
			$phone = $adminResult->phone;
		}
		
		
	    $this->load->library('pdf');
        $pdf = $this->pdf->load();
        
		// $id = $this->uri->segment(3);
		$id = $this->input->get('dispatch_id'); 
		$data['invoice'] = $data['childTrailer'] = array();
		$invoice = date('Y-m-d-H-i');
		$dispatch_type = $this->input->get('dispatch_type');
		$file_ids = $this->input->get('file_ids');
		$parent_file_ids = $this->input->get('parent_file_ids'); 
		$other_parent_file_ids = $this->input->get('other_parent_file_ids');  
		$other_cEmails = $this->input->get('other_cEmails') ?? [];
		$other_cEmails_checkbox = $this->input->get('other_cEmails_checkbox') ?? [];
		// echo  $other_cEmails;exit;
		if($dispatch_type=='dispatch'){
			$dtable='dispatch';
		}elseif ($dispatch_type=='warehouse_dispatch'){
			$dtable='warehouse_dispatch';
		}else{
			$dtable='dispatchOutside';
		}

		if($dtable == 'dispatchOutside'){
			$file = 'invoiceOutsidePDF';
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'dispatchOutside');
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id);
				$redirectionAddress='admin/outside-dispatch/update/';
				$dispatchType='outside';
				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					// $tracking = str_replace(' ','-',$data['invoice'][0]['tracking']);
					$tracking = $data['invoice'][0]['tracking'];
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'dispatchOutside','rate,trailer,dispatchMeta');
					$dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
			} 
		}elseif($dtable == 'warehouse_dispatch'){
			$file = 'invoiceWarehousePDF';
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'warehouse_dispatch');
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraWarehouseDispatchInfo($id);
				$where = array('did'=>$id,'dispatchType'=>'warehouse');
				$dispatchInfoDetails = $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_info_details','*','','','');
				if($dispatchInfoDetails){
					foreach($dispatchInfoDetails as $dis){
						$dispatchInfoTitle=$this->Comancontroler_model->get_data_by_column('id',$dis['dispatchInfoId'],'dispatchInfo','title','','','');
						$data['invoice']['dispatchInfoDetails'][] = [
							'dispatchInfoId' => $dis['dispatchInfoId'],
							'dispatchValue' => $dis['dispatchValue'],
							'dispatchInfoTitle' => $dispatchInfoTitle[0]['title']
						];
					}
				} else {
					$data['invoice']['dispatchInfoDetails'] = [
						[
							'dispatchInfoId' => '',
							'dispatchValue' => '',
							'dispatchInfoTitle' =>''
						]
					];		
				}
				$redirectionAddress='admin/paWarehouse/update/';
				$dispatchType='warehouse';
				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'warehouse_dispatch','*');
					$dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
				$data['trackingLabel'] = 'PO No.';
				$data['expense'] = [];
				if (!empty($id)) {
					$details = $this->db->select('expenseInfoId, expenseInfoValue')
						->from('dispatch_expense_details')
						->where('did', $id)
						->where('dispatchType', 'warehouse')
						->where('expenseType', 'customer')
						->get()
						->result_array();
					$expenseTitles = [];
					$titles = $this->Comancontroler_model->get_data_by_column('status', 'Active', 'expenses', 'id,title,type', 'id', 'asc');
					foreach ($titles as $t) {
						$expenseMeta[$t['id']] = [
							'title' => $t['title'],
							'type'  => $t['type'],
						];
					}
					foreach ($details as $row) {
						$meta = isset($expenseMeta[$row['expenseInfoId']]) ? $expenseMeta[$row['expenseInfoId']] : null;
						$title = $meta ? $meta['title'] : 'Unknown';
						$type  = $meta ? $meta['type'] : 'positive'; 
						$value = $row['expenseInfoValue'];

						$value = ($type === 'negative') ? -abs($value) : abs($value);

						if (!isset($data['expense'][$title])) {
							$data['expense'][$title] = [
								'price' => $value,
								'unit'  => [$value],
								'type'  => $type  
							];
						} else {
							$data['expense'][$title]['price'] += $value;
							$data['expense'][$title]['unit'][] = $value;
						}
					}
					$where = array('did'=>$id,'dispatchType'=>'warehouse');
					$data['customExpenseDetails'] =  $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_custom_expense_details','*','','','');
					$data['warehouse_expense']=$data['expense'];
				}
			} 
		} else {
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id);
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraDispatchInfo($id);
				$unitPriceSql = "SELECT unit,unitPrice,unitDescription FROM unitPrice WHERE dispatchId='$id' AND dispatchType='fleet'";
				$unitPrice = $this->db->query($unitPriceSql)->result_array();

				$data['dynamicUnitPrice']=$unitPrice;
				$redirectionAddress='admin/dispatch/update/';
				$dispatchType='fleet';

				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					// $tracking = str_replace(' ','-',$data['invoice'][0]['tracking']);
					$tracking = $data['invoice'][0]['tracking'];
					// echo $tracking;exit;
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'dispatch','rate,trailer,dispatchMeta');
				    $dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatchOutside','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
			} 
			$file = 'invoicePDF';
		}
		if($data['invoice'][0]['invoice_email']==''){
				$this->session->set_flashdata('error', 'Please add an invoicing email for the shipping company.');
				redirect(base_url($redirectionAddress . $id . '?invoice'));
			exit;
		}
		
		$companyEmailaddress=$data['invoice'][0]['invoice_email'];
		$recipientName=$data['invoice'][0]['contactPerson'];
		$data['unitprice'] = $data['unitTotal'] = '0';
		$data['unitprice'] = $data['unitTotal'] = '0';
		if($this->input->post('invoiceName')){ 
			} else {
		    $data['trackingLabel'] = 'Customer Ref No.';
		    $data['bookingnoLabel'] = 'Booking No.';
		    $data['trailerValLabel'] = '';
		    $data['trailerVal'] = $data['bookingno'] = $data['tracking'] = $data['expPrice'] = $data['expName'] = $data['invoiceDate'] = $data['dropoffExtra'] = $data['dropoff'] = $data['pickup'] = $data['contactPerson'] = $data['cdepartment'] = $data['cemail'] = $data['cphone'] = $data['invoiceNotes'] = '';
		}

		$html = $this->load->view('admin/'.$file.'New', $data, true);
		// //echo $html;die();
		$stylesheet = "";
		//$pdf->WriteHTML($stylesheet, 1);		
		$pdf->WriteHTML($html);
        $pdfArray = array();
		$rcFiles = array();
		$bolFiles = array();
		$rcImages = array();
		$bolImages = array();
		$inwrdFiles = [];
		$outwrdFiles = [];
		$inwrdImages = [];
		$outwrdImages = [];
		$parentRcFiles = [];
		$parentBolFiles = [];
		$parentRcImages = [];
		$parentBolImages = [];
		$otherParentRcFiles = [];
		$otherParentBolFiles = [];
		$otherParentRcImages = [];
		$otherParentBolImages = [];
        $output = FCPATH.'Invoice-'.$invoice.'.pdf';
		$pdf->Output($output, "F");
	    $pdfArray[] = $output;
        $type = 'dispatch';
		if($dispatch_type == 'dispatch'){
			$documents = $this->Comancontroler_model->get_documents_by_ids($file_ids);
			$otherParentdocuments = $this->Comancontroler_model->get_documents_by_ids($other_parent_file_ids,'documentsOutside');
			$parentdocuments = $this->Comancontroler_model->get_documents_by_ids($parent_file_ids);
		} elseif($dispatch_type == 'warehouse_dispatch'){
			$documents = $this->Comancontroler_model->get_documents_by_ids($file_ids, 'warehouse_documents');
	        $type = 'warehouse';
		} else {
			$documents = $this->Comancontroler_model->get_documents_by_ids($file_ids,'documentsOutside');
			$otherParentdocuments = $this->Comancontroler_model->get_documents_by_ids($other_parent_file_ids);
			$parentdocuments = $this->Comancontroler_model->get_documents_by_ids($parent_file_ids, 'documentsOutside');
			$type = 'outside';
		}
		if($documents){
    	    foreach($documents as $doc) {
    			if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch') { 
    			    // $pdfArray[] = FCPATH.'assets/upload/'.$doc['fileurl']; 
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$rcFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
								$rcImages[] = [
							'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
							'width' => $width,
							'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$bolFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$bolImages[] = [
							'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
							'width' => $width,
							'height' => $height,
							];
						}
					}
    			} elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside'){
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$rcFiles[] = FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$rcImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$bolFiles[] =FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$bolImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}
	    		} elseif (($doc['type'] == 'inwrd' || $doc['type'] == 'outwrd') && $type == 'warehouse') {
					if ($doc['type'] == 'inwrd') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$inwrdFiles[] = FCPATH.'assets/warehouse/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH . 'assets/warehouse/'.$doc['type'].'/'. $doc['fileurl']);
							$inwrdImages[] = [
								'file' => FCPATH . 'assets/warehouse/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'outwrd') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$outwrdFiles[] =FCPATH.'assets/warehouse/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH . 'assets/warehouse/'.$doc['type'].'/'. $doc['fileurl']);
							$outwrdImages[] = [
								'file' => FCPATH . 'assets/warehouse/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}
				}
    		}
		}

		if($parentdocuments){
	        foreach($parentdocuments as $doc) {
    			if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch') { 
    			    // $pdfArray[] = FCPATH.'assets/upload/'.$doc['fileurl']; 
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$parentRcFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$parentRcImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$parentBolFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$parentBolImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}
    			}
    			elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside'){
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$parentRcFiles[] = FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$parentRcImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$parentBolFiles[] =FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$parentBolImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}				
    			}
    		}
	    }

		if($otherParentdocuments){
	        foreach($otherParentdocuments as $doc) {
    			if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside') { 
    			    // $pdfArray[] = FCPATH.'assets/upload/'.$doc['fileurl']; 
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$otherParentRcFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$otherParentRcImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$otherParentBolFiles[] = FCPATH.'assets/upload/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH.'assets/upload/'.$doc['fileurl']);
							$otherParentBolImages[] = [
								'file' => FCPATH.'assets/upload/'.$doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}
    			}
    			elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch'){
					if ($doc['type'] == 'rc') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$otherParentRcFiles[] = FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {	
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$otherParentRcImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					} elseif ($doc['type'] == 'bol') {
						if (stristr($doc['fileurl'], '.pdf')) {
							$otherParentBolFiles[] =FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl'];
						} elseif (preg_match('/\.(jpg|jpeg|png)$/i', $doc['fileurl'])) {
							list($width, $height) = getimagesize(FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl']);
							$otherParentBolImages[] = [
								'file' => FCPATH . 'assets/outside-dispatch/'.$doc['type'].'/'. $doc['fileurl'],
								'width' => $width,
								'height' => $height,
							];
						}
					}				
    			}
    		}
	    }
		$this->PdfMergerNew = new PdfMergerNew();
		if (!empty($parentRcImages)) {
			$parentRcmagePdf = FCPATH . 'assets/temp/parent_rc_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($parentRcImages, 'F', $parentRcmagePdf);
			$rcFiles[] = $parentRcmagePdf;
		}
			
		if (!empty($parentBolImages)) {
			$parentBolImagePdf = FCPATH . 'assets/temp/parent_bol_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($parentBolImages, 'F', $parentBolImagePdf);
			$bolFiles[] = $parentBolImagePdf;
		}

		if (!empty($otherParentRcImages)) {
			$otherParentRcmagePdf = FCPATH . 'assets/temp/other_parent_rc_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($otherParentRcImages, 'F', $otherParentRcmagePdf);
			$rcFiles[] = $otherParentRcmagePdf;
		}
			
		if (!empty($otherParentBolImages)) {
			$otherParentBolImagePdf = FCPATH . 'assets/temp/other_parent_bol_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($otherParentBolImages, 'F', $otherParentBolImagePdf);
			$bolFiles[] = $otherParentBolImagePdf;
		}
		if (!empty($rcImages)) {
			$rcImagePdf = FCPATH . 'assets/temp/rc_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($rcImages, 'F', $rcImagePdf);
			$rcFiles[] = $rcImagePdf;
		}
		if (!empty($bolImages)) {
			$bolImagePdf = FCPATH . 'assets/temp/bol_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($bolImages, 'F', $bolImagePdf);
			$bolFiles[] = $bolImagePdf;
		}

		if (!empty($inwrdImages)) {
			$inwrdImagePdf = FCPATH . 'assets/temp/inwrd_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($inwrdImages, 'F', $inwrdImagePdf);
			$inwrdFiles[] = $inwrdImagePdf;
		}
			
		if (!empty($outwrdImages)) {
			$outwrdImagePdf = FCPATH . 'assets/temp/outwrd_combined_images.pdf';
			$this->PdfMergerNew->combine_images_to_pdf($outwrdImages, 'F', $outwrdImagePdf);
			$outwrdFiles[] = $outwrdImagePdf;
		}

		$pdfArray = array_merge($pdfArray, $parentBolFiles);
		$pdfArray = array_merge($pdfArray, $otherParentBolFiles);
		$pdfArray = array_merge($pdfArray, $bolFiles);

		$pdfArray = array_merge($pdfArray, $parentRcFiles);
		$pdfArray = array_merge($pdfArray, $otherParentRcFiles);
		$pdfArray = array_merge($pdfArray, $rcFiles);

		if( $type == 'warehouse'){
			$pdfArray = array_merge($pdfArray, $inwrdFiles);
			$pdfArray = array_merge($pdfArray, $outwrdFiles);
		}

		$cleanedFiles = [];
		foreach ($pdfArray as $file) {
			$cleanedFile = sys_get_temp_dir() . '/' . uniqid() . '_cleaned.pdf';
			$command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($cleanedFile) . " " . escapeshellarg($file);
			exec($command);
			$cleanedFiles[] = file_exists($cleanedFile) ? $cleanedFile : $file;
		}
		$pdfArray = $cleanedFiles;

	    $this->load->library('PdfMerger');
		
		if($tracking == 'N/A'){
			$tracking = '';
		}
		$rawFileName = 'Invoice-'.$invoice.'_'.$tracking.'.pdf';
		$mergedOutput = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $rawFileName);
		$mergedPath = '';
		if ($type == 'dispatch') {
			$mergedPath = FCPATH . 'assets/paInvoice/' . $mergedOutput;
		} else if($type == 'warehouse'){
			$mergedPath = FCPATH . 'assets/warehouse/invoice/' . $mergedOutput;
		}
		else {
			$mergedPath = FCPATH . 'assets/outside-dispatch/invoice/' . $mergedOutput;
		}

        try {
			$this->pdfmerger->merge_pdfs($pdfArray, $mergedPath, 'F');
			$output = $mergedOutput;
        } catch(Exception $e) {
			$this->load->library('PdfMergerNewNew');
			try {
				$this->pdfmergernewnew->addPdf($pdfArray);
				$this->pdfmergernewnew->save($mergedPath);

				$output = $mergedOutput;
			} catch(Exception $e) {
				echo 'Message: Some pdf files are not proper format'; 
			}
        }
								

        // if($output!='' && file_exists($output)) {
        //     unlink($output);  
        // }

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
			
			//from email=$email
			$mail->setFrom($email, 'Accounts PA Logistics Group LLC');
			$mail->addAddress($companyEmailaddress, $recipientName);
			// $mail->addAddress('naveedullah968@gmail.com', $recipientName);

			$mail->addCC('accounts@palogisticsgroup.com');

			if (!empty($other_cEmails) && is_array($other_cEmails)) {
				foreach ($other_cEmails as $index => $email) {
					if (isset($other_cEmails_checkbox[$index]) && $other_cEmails_checkbox[$index] == '1' && !empty($email))
					{
						$mail->addCC(trim($email));
					}
				}
			}

			// $mail->addAddress('naveedullah968@gmail.com', $recipientName);
			$mail->isHTML(true);
			$mail->addAttachment($mergedPath, basename($mergedPath));
			if($tracking==''){
				$tracking='Empty Trailer';
			}
			$email_subject = $this->input->get('email_subject');
			$mail->Subject = $email_subject;
			$email_body = $this->input->get('email_body');
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
			$email_history_sql = "
			INSERT INTO invoiceEmailHistory (dispatchId, dispatchType, `file`)
			VALUES ('$id', '$dispatchType', '$output')
			ON DUPLICATE KEY UPDATE `file` = VALUES(`file`),
			`date` = NOW()
			";
			$this->db->query($email_history_sql);
			$userid = $this->session->userdata('logged');

			if ($type == 'dispatch') {
				$addfile = array('did'=>$id,'type'=>'paInvoice','fileurl'=>$mergedOutput,'rdate'=>date('Y-m-d H:i:s'));
				$this->Comancontroler_model->add_data_in_table($addfile,'documents');
				$changeField[] = array('Customer Invoice File','paInvoice','Upload',$mergedOutput);

				if($changeField) {
					$changeFieldJson = json_encode($changeField);
					$dispatchLog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
					$this->Comancontroler_model->add_data_in_table($dispatchLog,'dispatchLog'); 
				}
			}elseif($type == 'warehouse'){
				$addfile = array('did'=>$id,'type'=>'paInvoice','fileurl'=>$mergedOutput,'rdate'=>date('Y-m-d H:i:s'));
				$this->Comancontroler_model->add_data_in_table($addfile, 'warehouse_documents');
				$changeField[] = array('Customer Invoice File', 'paInvoice', 'Upload', $mergedOutput);
				if($changeField) {
					$changeFieldJson = json_encode($changeField);
					$dispatchLog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
					$this->Comancontroler_model->add_data_in_table($dispatchLog,'warehouse_dispatch_log'); 
				}
			}
			else{
				$addfile = array('did'=>$id,'type'=>'paInvoice','fileurl'=>$mergedOutput,'rdate'=>date('Y-m-d H:i:s'));
				$this->Comancontroler_model->add_data_in_table($addfile, 'documentsOutside');
				$changeField[] = array('Customer Invoice File', 'paInvoice', 'Upload', $mergedOutput);
				if($changeField) {
					$changeFieldJson = json_encode($changeField);
					$dispatchLog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
					$this->Comancontroler_model->add_data_in_table($dispatchLog,'dispatchOutsideLog'); 
				}
			}

			$this->session->set_flashdata('item', 'Email sent successfully!');
		} else {
			$this->session->set_flashdata('error', 'Email could not be sent. Please try again.');
		}
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Message could not be sent. Error: ' . $e->getMessage());
		}
		
		redirect(base_url($redirectionAddress . $id . '?invoice'));
		exit;
	}
	public function invoiceEmailHistory(){
		if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
	            
        //$sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        //$edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
        $company = $driver = $sdate = $edate = '';
        
        $data['dispatchURL'] = 'dispatch';
         
        if($this->input->post('search'))	{
            $company = $this->input->post('company');
            $driver = $this->input->post('driver');
            $dispatchType = $this->input->post('dispatchType');
            $invoiceType = $this->input->post('invoiceType');
            if($dispatchType == 'outsideDispatch'){ 
                $table = 'dispatchOutside'; 
				$dispatchType = 'outside'; 
                $data['dispatchURL'] = 'outside-dispatch';
            }
            else { $table = 'dispatch';
				$dispatchType = 'fleet'; 
			}
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			$data['dispatch'] = $this->AllInvoices_model->get_invoice_history($table,$sdate,$edate,$dispatchType);
        } else {
            $data['dispatch'] = array();
        }
        
    	
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/invoice_email_history',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function emailRemitanceProof(){
		if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
		// echo 'test';exit;
		$adminid = $this->session->userdata('adminid'); 
		if($adminid){
			$adminSql = "SELECT * FROM admin_login WHERE id='$adminid'";
			$adminResult = $this->db->query($adminSql)->row();
			$username = $adminResult->uname;
			$email = $adminResult->email;
			$phone = $adminResult->phone;
		}
		
       	$id = $this->input->get('dispatch_id');
		$dispatch="SELECT * FROM dispatchOutside WHERE id=$id";
		$result=$this->db->query($dispatch)->row();
		$carrierRate=number_format($result->rate,2);
		$invoiceNo=$result->invoice;
		$carrierInvoiceNo=$result->carrierInvoiceRefNo;
		$truckingCompanyId = $result->truckingCompany;

		$truckingCompany="SELECT * FROM truckingCompanies WHERE id=$truckingCompanyId";
		$carrierName=$this->db->query($truckingCompany)->row()->company;

		$carrier_files = $this->input->get('carrier_files');
		$cEmail = $this->input->get('cEmail');
		$other_cEmails = $this->input->get('other_cEmails') ?? [];
		$other_cEmails_checkbox = $this->input->get('other_cEmails_checkbox') ?? [];
		$emailSentTo = $this->input->get('emailSentTo');
		$emailRecieverId = $this->input->get('emailRecieverId');

		$documents = $this->Comancontroler_model->get_documents_by_ids($carrier_files,'documentsOutside');
		$carrierGdDocs = [];
		if($documents){
	        foreach($documents as $doc){
	        	if ($doc['type'] == 'carrierGd') {
					$carrierGdDocs[] = $doc;
				}
	        }
	    }
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
			
			//from email=$email
			$mail->setFrom($email, 'Accounts PA Logistics Group LLC');
			$mail->addAddress($cEmail);
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
			// $mail->addAddress('naveedullah968@gmail.com', $recipientName);
			$mail->isHTML(true);
			$fileNames = [];
			foreach ($carrierGdDocs as $doc) {
				$filePath = FCPATH . 'assets/outside-dispatch/gd/' . $doc['fileurl'];
				if (file_exists($filePath)) {
					$mail->addAttachment($filePath);
					$fileNames[] = basename($doc['fileurl']);
				}else{
					$this->session->set_flashdata('error', 'Proof file was not found in directory.');
					redirect(base_url('admin/outside-dispatch/update/'. $id . '?invoice'));
					exit;
				}
			}
			$carrierGdDocsStr = implode(',', $fileNames);
			$email_subject = $this->input->get('email_subject');
			$mail->Subject = $email_subject;
			$email_body = $this->input->get('email_body');
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
			$sendBy = $userid['adminid'];
			$carrier_email_history_sql = "INSERT INTO carrierEmailHistory (dispatchId, sender, emailSentTo, emailRecieverId,`file`)
			VALUES ('$id', '$sendBy', '$emailSentTo', '$emailRecieverId', '$carrierGdDocsStr')";
			// echo $carrier_email_history_sql;exit;
			$this->db->query($carrier_email_history_sql);
		
			$this->session->set_flashdata('item', 'Remittance proof sent successfully!');
		} else {
			$this->session->set_flashdata('error', 'Email could not be sent. Please try again.');
		}
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Message could not be sent. Error: ' . $e->getMessage());
		}
		
		redirect(base_url('admin/outside-dispatch/update/' . $id));
		exit;
	}
	public function getAvailableCarrierFiles($id) {
		$documents = $this->Comancontroler_model->get_document_by_dispach($id,'documentsOutside');
		$carrierGdDocs = [];
		if($documents){
	        foreach($documents as $doc){
	        	if ($doc['type'] == 'carrierGd') {
					$carrierGdDocs[] = $doc;
				}
	        }
	    }
		$cemail = '';
		$cother_Emails = '';
		$email_sent_to = '';
		$email_reciever_id = '';
		if ($id) {
			$factoringData = $this->Comancontroler_model->get_data_by_column('id', $id, 'dispatchOutside', 'truckingCompany,factoringType,factoringCompany');
			if (!empty($factoringData)) {
				$factoringType = $factoringData[0]['factoringType'];
				$factoringCompanyId = $factoringData[0]['factoringCompany'];
				$truckingCompanyId = $factoringData[0]['truckingCompany'];

				if ($factoringType == 'Factoring') {
					$factoringCompany = $this->Comancontroler_model->get_data_by_column('id', $factoringCompanyId, 'factoringCompanies', 'email,email2');
					if (!empty($factoringCompany)) {
						$cemail = $factoringCompany[0]['email'];
						$cother_Emails =  $factoringCompany[0]['email2'];
						$email_sent_to = 'factoringCompany';
						$email_reciever_id = $factoringCompanyId;
					}else{
						$this->session->set_flashdata('error', 'Please select a factoring company and update the load.');
						echo json_encode([
							'redirect_url' => base_url('admin/outside-dispatch/update/' . $id . '?invoice')
						]);
						return;
					}
					if($cemail==''){
						$this->session->set_flashdata('error', 'Please add an email for the factoring company.');
						echo json_encode([
							'redirect_url' => base_url('admin/outside-dispatch/update/' . $id . '?invoice')
						]);
						return;
					}
				} else {
					$truckingCompany = $this->Comancontroler_model->get_data_by_column('id', $truckingCompanyId, 'truckingCompanies', 'email,email2');
					if (!empty($truckingCompany)) {
						$cemail = $truckingCompany[0]['email'];
						$cother_Emails = $truckingCompany[0]['email2'];
						$email_sent_to = 'truckingCompany';
						$email_reciever_id = $truckingCompanyId;
					}
					if($cemail==''){
						$this->session->set_flashdata('error', 'Please add an email for the carrier.');
						echo json_encode([
							'redirect_url' => base_url('admin/outside-dispatch/update/' . $id . '?invoice')
						]);
						return;
					}
				}
			}
		}
		$cEmails=$cemail;
		$otherCEmails=$cother_Emails;
		echo json_encode([
			'carrier_files' => $carrierGdDocs,
			'email' => $cEmails,
			'other_emails' => $otherCEmails,
			'email_sent_to' => $email_sent_to,
			'email_reciever_id' => $email_reciever_id
		]);
	}
	public function getAvailableCarrierEmails($id) {
		$cemail = '';
		$cother_Emails = '';
		if ($id) {
			$truckingCompanyData = $this->Comancontroler_model->get_data_by_column('id', $id, 'dispatchOutside', 'truckingCompany');
			if (!empty($truckingCompanyData)) {
				$truckingCompanyId = $truckingCompanyData[0]['truckingCompany'];
				$truckingCompany = $this->Comancontroler_model->get_data_by_column('id', $truckingCompanyId, 'truckingCompanies', 'email,email2');
				if (!empty($truckingCompany)) {
					$cemail = $truckingCompany[0]['email'];
					$cother_Emails =  $truckingCompany[0]['email2'];
					$email_sent_to = 'truckingCompany';
					$email_reciever_id = $truckingCompanyId;
				}
				// if($cemail==''){
				// 	$this->session->set_flashdata('error', 'Please add an email for the carrier.');
				// 		echo json_encode([
				// 			'redirect_url' => base_url('admin/outside-dispatch/update/' . $id . '?invoice')
				// 		]);
				// 		return;
				// }
			}
		}
		$cEmails=$cemail;
		$otherCEmails=$cother_Emails;
		echo json_encode([
			'email' => $cEmails,
			'other_emails' => $otherCEmails,
			'email_sent_to' => $email_sent_to,
			'email_reciever_id' => $email_reciever_id
		]);
	}
	public function emailRateConfirmationfile(){
		if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
		// echo 'test';exit;
		$adminid = $this->session->userdata('adminid'); 
		if($adminid){
			$adminSql = "SELECT * FROM admin_login WHERE id='$adminid'";
			$adminResult = $this->db->query($adminSql)->row();
			$username = $adminResult->uname;
			$email = $adminResult->email;
			$phone = $adminResult->phone;
		}
		
		
	    $this->load->library('pdf');
        $pdf = $this->pdf->load();
		$id = $this->input->get('dispatch_id'); 
		$cEmail = $this->input->get('cEmail');
		$other_cEmails = $this->input->get('other_cEmails') ?? [];
		$other_cEmails_checkbox = $this->input->get('other_cEmails_checkbox') ?? [];
		$emailSentTo = $this->input->get('emailSentTo');
		$emailRecieverId = $this->input->get('emailRecieverId');
		// echo  $other_cEmails;exit;
		
		if($cEmail==''){
			$this->session->set_flashdata('error', 'Please add an email for the carrier.');
			redirect(base_url('admin/outside-dispatch/update/' . $id));
			return;
		}
		$data['dispatch'] = $data['extraDispatch'] = $data['truckCompany'] = $data['companyAddress'] = $data['locations'] = $data['cities'] = $data['userinfo'] = array();

		if ($id) {
				if(is_numeric($id)){
					$data['dispatch'] = $this->Comancontroler_model->get_data_by_id($id,'dispatchOutside');
					$dispatchMetaJson = $data['dispatch'][0]['dispatchMeta'];
					$dispatchMeta = json_decode($dispatchMetaJson, true); 
					$invoicePDF = $dispatchMeta['invoicePDF'];
					if($invoicePDF == 'Drayage'){
						$erInformation = $dispatchMeta['erInformation'];
						if(!empty($erInformation)){
							$data['erInformation'] = $this->Comancontroler_model->get_data_by_id($erInformation,'erInformation');
						}
					}
					if($data['dispatch'][0]['userid'] > 0){
						$data['userinfo'] = $this->Comancontroler_model->get_data_by_id($data['dispatch'][0]['userid'],'admin_login');
					}
					$data['truckCompany'] = $this->Comancontroler_model->get_data_by_id($data['dispatch'][0]['truckingCompany'],'truckingCompanies');
					$data['extraDispatch'] = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id);
					$data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
				}
		}

		$file = 'rateLoadConfirmationPDF';
		$html = $this->load->view('admin/' . $file, $data, true);
		$pdf->WriteHTML($html);
		$invoiceNo = $data['dispatch'][0]['invoice']; 
		$filename = "Load Confirmation - $invoiceNo.pdf";
		$pdfContent = $pdf->Output('', 'S');


		$this->load->library('PHPMailer_Lib');
		$mail = $this->phpmailer_lib->load();
		try {
			$mail->isSMTP();                                      
			$mail->Host = 'smtp.gmail.com';                       
			$mail->SMTPAuth = true;                             
			$mail->Username = 'ops@palogisticsgroup.com';             
			$mail->Password = 'qkmensdfgwzsxnmh';                   
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
			
			//from email=$email
			$mail->setFrom('info@palogisticsgroup.com', 'Operations PA Logistics Group LLC');
			$mail->addAddress($cEmail);
			// $mail->addAddress('naveedullah968@gmail.com');
			$mail->addCC('ops@palogisticsgroup.com');

			if (!empty($other_cEmails) && is_array($other_cEmails)) {
				foreach ($other_cEmails as $index => $email) {
					if (isset($other_cEmails_checkbox[$index]) && $other_cEmails_checkbox[$index] == '1' && !empty($email))
					{
						$mail->addCC(trim($email));
					}
				}
			}
			// // $mail->addAddress('naveedullah968@gmail.com', $recipientName);
			$mail->isHTML(true);

			$email_subject = $this->input->get('email_subject');
			$mail->Subject = $email_subject;
			$email_body = $this->input->get('email_body');
			$mail->addStringAttachment($pdfContent, $filename, 'base64', 'application/pdf');
			$mail->Body= $email_body . '
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
						<p style="margin: 2px 0 0; font-size: 13px; color: #555;"><strong>Operations</strong></p>
						<p style="margin: 2px 0; font-size: 12px; color: #555;">
							<a href="tel:(925) 430-5356" style="color: #555; text-decoration: none;">(925) 430-5356</a> | <a href="mailto:Accounts@palogisticsgroup.com" style="color: #555; text-decoration: none;">ops@palogisticsgroup.com</a>
						</p>
						<p style="margin: 2px 0; font-size: 12px; color: #555;">
							MC 956423 | DOT 3339378 | <a href="https://palogisticsgroup.com" style="color: #555; text-decoration: none;" target="_blank">www.palogisticsgroup.com </a>
						</p>
						<p style="margin: 2px 0; font-size: 12px; color: #555;">672 W 11th St, Suite 348 Tracy, CA 95376</p>
						<table cellpadding="0" cellspacing="0" border="0" style="max-width: 400px; margin: 0;width: 278px;">
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
			$sendBy = $userid['adminid'];
			$rate_confirmation_email_history_sql = "INSERT INTO rate_confirmation_email_history (dispatchId, sender, emailSentTo, emailRecieverId)
			VALUES ('$id', '$sendBy','$emailSentTo', '$emailRecieverId')";
			$this->db->query($rate_confirmation_email_history_sql);
		
			$this->session->set_flashdata('item', 'Rate Confirmation sent successfully!');
		} else {
			$this->session->set_flashdata('error', 'Email could not be sent. Please try again.');
		}
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Message could not be sent. Error: ' . $e->getMessage());
		}
		
		redirect(base_url('admin/outside-dispatch/update/' . $id));
		exit;
	}
	public function carrierEmailHistory(){
		if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
	            
    	$sdate = $edate = ''; 
        if($this->input->post('search'))	{
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			$data['dispatch'] = $this->AllInvoices_model->get_carrier_email_history($sdate,$edate);
        } else {
            $data['dispatch'] = array();
        }
        
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/carrier_email_history',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function previewInvoicePDF(){
	    if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $this->load->library('pdf');
        $pdf = $this->pdf->load();
        
		// $id = $this->uri->segment(3);
		$id = $this->input->get('dispatch_id'); 
		$data['invoice'] = $data['childTrailer'] = array();
		$invoice = date('Y-m-d-H-i');
		$dispatch_type = $this->input->get('dispatch_type');
		$file_ids = $this->input->get('file_ids'); 
		$parent_file_ids = $this->input->get('parent_file_ids'); 
		$other_parent_file_ids = $this->input->get('other_parent_file_ids'); 
		if($dispatch_type=='dispatch'){
			$dtable='dispatch';
		}elseif($dispatch_type=='warehouse_dispatch'){
			$dtable='warehouse_dispatch';
		}
		else{
			$dtable='dispatchOutside';
		}

		if($dtable == 'dispatchOutside'){
			$file = 'invoiceOutsidePDF';
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'dispatchOutside');
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id);
				$redirectionAddress='admin/outside-dispatch/update/';
				$dispatchType='outside';
				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'dispatchOutside','rate,trailer,dispatchMeta');
					$dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
			} 
		} elseif($dtable == 'warehouse_dispatch'){
			$file = 'invoiceWarehousePDF';
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id,'warehouse_dispatch');
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraWarehouseDispatchInfo($id);
				$where = array('did'=>$id,'dispatchType'=>'warehouse');
				$dispatchInfoDetails = $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_info_details','*','','','');
				if($dispatchInfoDetails){
					foreach($dispatchInfoDetails as $dis){
						$dispatchInfoTitle=$this->Comancontroler_model->get_data_by_column('id',$dis['dispatchInfoId'],'dispatchInfo','title','','','');
						$data['invoice']['dispatchInfoDetails'][] = [
							'dispatchInfoId' => $dis['dispatchInfoId'],
							'dispatchValue' => $dis['dispatchValue'],
							'dispatchInfoTitle' => $dispatchInfoTitle[0]['title']
						];
					}
				} else {
					$data['invoice']['dispatchInfoDetails'] = [
						[
							'dispatchInfoId' => '',
							'dispatchValue' => '',
							'dispatchInfoTitle' =>''
						]
					];		
				}
				$redirectionAddress='admin/paWarehouse/update/';
				$dispatchType='warehouse';
				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'warehouse_dispatch','*');
					$dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
				$data['trackingLabel'] = 'PO No.';
				$data['expense'] = [];
				if (!empty($id)) {
					$details = $this->db->select('expenseInfoId, expenseInfoValue')
						->from('dispatch_expense_details')
						->where('did', $id)
						->where('dispatchType', 'warehouse')
						->where('expenseType', 'customer')
						->get()
						->result_array();
					$expenseTitles = [];
					$titles = $this->Comancontroler_model->get_data_by_column('status', 'Active', 'expenses', 'id,title,type', 'id', 'asc');
					foreach ($titles as $t) {
						$expenseMeta[$t['id']] = [
							'title' => $t['title'],
							'type'  => $t['type'],
						];
					}
					foreach ($details as $row) {
						$meta = isset($expenseMeta[$row['expenseInfoId']]) ? $expenseMeta[$row['expenseInfoId']] : null;
						$title = $meta ? $meta['title'] : 'Unknown';
						$type  = $meta ? $meta['type'] : 'positive'; 
						$value = $row['expenseInfoValue'];

						$value = ($type === 'negative') ? -abs($value) : abs($value);

						if (!isset($data['expense'][$title])) {
							$data['expense'][$title] = [
								'price' => $value,
								'unit'  => [$value],
								'type'  => $type  
							];
						} else {
							$data['expense'][$title]['price'] += $value;
							$data['expense'][$title]['unit'][] = $value;
						}
					}
					$where = array('did'=>$id,'dispatchType'=>'warehouse');
					$data['customExpenseDetails'] =  $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_custom_expense_details','*','','','');
					$data['warehouse_expense']=$data['expense'];
				}
			} 
		}
		else {
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id);
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraDispatchInfo($id);
				$unitPriceSql = "SELECT unit,unitPrice,unitDescription FROM unitPrice WHERE dispatchId='$id' AND dispatchType='fleet'";
				$unitPrice = $this->db->query($unitPriceSql)->result_array();
				$invoiceAmountSql = "SELECT parate FROM dispatch WHERE id='$id'";
				$invoiceAmount = $this->db->query($invoiceAmountSql)->row()->parate;

				$data['dynamicUnitPrice']=$unitPrice;
				$redirectionAddress='admin/dispatch/update/';
				$dispatchType='fleet';

				if($data['invoice'][0]['invoice'] != ''){
					$invoice = str_replace(' ','-',$data['invoice'][0]['invoice']);
					$data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['invoice'][0]['invoice'],'dispatch','rate,trailer,dispatchMeta');
				    $dispatchMeta = json_decode($data['invoice'][0]['dispatchMeta'],true);
            	    if($dispatchMeta['otherChildInvoice'] != ''){
            	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
            	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatchOutside','rate,trailer,dispatchMeta');
            	        if($otherChildInvoice){
            	            $data['childTrailer'] = array_merge($data['childTrailer'], $otherChildInvoice);
            	        }
            	    }
				}
			} 
			$file = 'invoicePDF';
		}

		$data['unitprice'] = $data['unitTotal'] = '0';
		if($this->input->post('invoiceName')){ 
			} else {
		    $data['trackingLabel'] = 'Customer Ref No.';
		    $data['bookingnoLabel'] = 'Booking No.';
		    $data['trailerValLabel'] = '';
		    $data['trailerVal'] = $data['bookingno'] = $data['tracking'] = $data['expPrice'] = $data['expName'] = $data['invoiceDate'] = $data['dropoffExtra'] = $data['dropoff'] = $data['pickup'] = $data['contactPerson'] = $data['cdepartment'] = $data['cemail'] = $data['cphone'] = $data['invoiceNotes'] = '';
		}

		$html = $this->load->view('admin/'.$file.'New', $data, true);
		//echo $html;die();
		$stylesheet = "";		
		$pdf->WriteHTML($html);
        // write the HTML into the PDF
		// echo $pdf; exit;
        $output = 'Invoice # '.$invoice.'.pdf';
        if($this->input->post('invoiceName'))	{ $output = $this->input->post('invoiceName'); }
	    $pdf->Output($output, "I"); // I D F	    
		exit;
	}
	public function invoicePending(){
	    if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $data['dispatchURL'] = 'dispatch';
	    $data['dispatch'] = $this->Comancontroler_model->get_invoice_pending();
	    
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		//$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/invoice-pending',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	public function editInvoiceForm(){
	    if(!checkPermission($this->session->userdata('permission'),'invoice') || (!is_numeric($_GET['editInvoiceID']))){
	        redirect(base_url('AdminDashboard'));   
	    }
	    
		$id = $_GET['editInvoiceID'];
		$invoice = $childTrailer = array();
		$invoiceName = date('Y-m-d-H-i');
		if(isset($_GET['dTable']) && $_GET['dTable'] == 'dispatchOutside'){
			$invoice = $this->Comancontroler_model->downloadDispatchInvoice($id,'dispatchOutside');
			$extraDispatch = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id);
			if($invoice[0]['invoice'] != ''){
				$invoiceName = str_replace(' ','-',$invoice[0]['invoice']);
				$childTrailer = $this->Comancontroler_model->get_data_by_column('parentInvoice',$invoice[0]['invoice'],'dispatchOutside','rate,trailer,dispatchMeta');
			    $dispatchMeta = json_decode($invoice[0]['dispatchMeta'],true);
        	    if($dispatchMeta['otherChildInvoice'] != ''){
        	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
        	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
        	        if($otherChildInvoice){
        	            $childTrailer = array_merge($childTrailer, $otherChildInvoice);
        	        }
        	    }
			}
		} else if(isset($_GET['dTable']) && $_GET['dTable'] == 'warehouse_dispatch'){
			$invoice = $this->Comancontroler_model->downloadDispatchInvoice($id,'warehouse_dispatch');
			$extraDispatch = $this->Comancontroler_model->getExtraWarehouseDispatchInfo($id);
			// print_r($extraDispatch);exit;
			if($invoice[0]['invoice'] != ''){
				$invoiceName = str_replace(' ','-',$invoice[0]['invoice']);
				$childTrailer = $this->Comancontroler_model->get_data_by_column('parentInvoice',$invoice[0]['invoice'],'warehouse_dispatch','rate,trailer,dispatchMeta');
			    $dispatchMeta = json_decode($invoice[0]['dispatchMeta'],true);
        	    if($dispatchMeta['otherChildInvoice'] != ''){
        	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
        	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','rate,trailer,dispatchMeta');
        	        if($otherChildInvoice){
        	            $childTrailer = array_merge($childTrailer, $otherChildInvoice);
        	        }
        	    }
			}
			// Fetch expenses master list to map titles by ID
			$expensesMaster = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','id,title','id','asc');
			$expenseTitles = [];
			foreach ($expensesMaster as $ex) {
				$expenseTitles[$ex['id']] = $ex['title'];
			}
			$expenseDetails = $this->db
				->select('expenseInfoId, expenseInfoValue')
				->from('dispatch_expense_details')
				->where('did', $id)
				->where('dispatchType', 'warehouse')
				->get()
				->result_array();
		} else {
			$invoice = $this->Comancontroler_model->downloadDispatchInvoice($id);
			$extraDispatch = $this->Comancontroler_model->getExtraDispatchInfo($id);
			$unitPriceSql = "SELECT * FROM unitPrice WHERE dispatchId='$id'";
			$unitPrice = $this->db->query($unitPriceSql)->result();

			if($invoice[0]['invoice'] != ''){
				$invoiceName = str_replace(' ','-',$invoice[0]['invoice']);
				$childTrailer = $this->Comancontroler_model->get_data_by_column('parentInvoice',$invoice[0]['invoice'],'dispatch','rate,trailer,dispatchMeta');
			    $dispatchMeta = json_decode($invoice[0]['dispatchMeta'],true);
        	    if($dispatchMeta['otherChildInvoice'] != ''){
        	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
        	        $otherChildInvoice = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatchOutside','rate,trailer,dispatchMeta');
        	        if($otherChildInvoice){
        	            $childTrailer = array_merge($childTrailer, $otherChildInvoice);
        	        }
        	    }
			}
		}
		$invoiceName = 'Invoice # '.$invoiceName.'.pdf';
		
		?>
		
		<div class="col-sm-6 form-group">
			<label>Invoice Name</label>
			<input required readonly type="text" name="invoiceName" value="<?=$invoiceName?>" class="form-control" style="width:95% !important;">
		</div>
		<div class="col-sm-6 form-group">
			<label>Invoice Date</label>
			<input required type="text" name="invoiceDate" value="<?php if($invoice[0]['invoiceDate'] != '0000-00-00') { echo date('Y-m-d',strtotime($invoice[0]['invoiceDate'])); } ?>" class="form-control datepicker" style="width:95% !important;" readonly>
		</div>
		<div class="col-sm-6 form-group">
			<label>Shipping Contact Person</label>
			<input readonly type="text" name="contactPerson" value="<?=$invoice[0]['contactPerson']?>" class="form-control" style="width:95% !important;">
		</div>
		<div class="col-sm-6 form-group">
			<label>Shipping Department</label>
			<input readonly type="text" name="cdepartment" value="<?=$invoice[0]['cdepartment']?>" class="form-control" style="width:95% !important;">
		</div>
		<div class="col-sm-6 form-group">
			<label>Shipping Email</label>
			<input readonly type="email" name="cemail" value="<?=$invoice[0]['cemail']?>" class="form-control" style="width:95% !important;">
		</div>
		<div class="col-sm-6 form-group">
			<label>Shipping Phone</label>
			<input readonly type="text" name="cphone" value="<?=$invoice[0]['cphone']?>" class="form-control" style="width:95% !important;">
		</div>
		<div class="col-sm-6 form-group">
			<label>Pick Up</label>
			<input readonly type="text" name="pickup" value="<?=$invoice[0]['pplocation']?> [<?=$invoice[0]['ppcity']?>]" class="form-control" style="width:95% !important;">
		</div>
		<?php 
		if($extraDispatch) { 
            $p = 1;
            foreach($extraDispatch as $ex){
                if($ex['pd_type']=='pickup'){
                    $p++;
                    echo '<div class="col-sm-6 form-group">
            			<label>Pick Up #'.$p.'</label>
            			<input type="text" name="pickupExtra[]" value="'.$ex['ppd_location'].' ['.$ex['ppd_city'].']'.'" class="form-control"readonly>
            		</div>'; 
                }
            }
        }
		?>
		<div class="col-sm-6 form-group">
			<label>Drop Off</label>
			<input readonly type="text" name="dropoff" value="<?=$invoice[0]['ddlocation']?> [<?=$invoice[0]['ddcity']?>]" class="form-control" style="width:95% !important;">
		</div>
		<?php
		 if (!isset($_GET['dTable']) || $_GET['dTable'] != 'dispatchOutside' && $_GET['dTable'] != 'warehouse_dispatch') { ?>
			<div id="unitRows" class="col-sm-12">
				<?php if (empty($unitPrice)): ?>
					<div class="row unitRow">
						<div class="col-sm-2 form-group">
							<label>Unit</label>
							<input type="number" name="unit" value="0" min="0" class="form-control" style="width:95% !important;">
						</div>
						<div class="col-sm-2 form-group">
							<label>Unit Price</label>
							<input type="number" name="unitprice" value="0" min="0" step="0.01" class="form-control" style="width:95% !important;">
						</div>
						<div class="col-sm-7 form-group">
							<label>Unit Description</label>
							<textarea row="1" name="unitDescription" class="form-control" style="width:95% !important; height:50px !important;"></textarea>
						</div>
						<div class="col-sm-1 form-group">
							<button type="button" class="btn btn-success addRow" style="float: right; margin-right: 34px; margin-top: 36px;">+</button>
						</div>
					</div>
				<?php else: ?>
					<?php 
						$adjustedIndex = 0; 
						foreach ($unitPrice as $record): 
							if ($record->unit > 0): 
						?>
								<div class="row unitRow">
									<div class="col-sm-2 form-group">
										<label>Unit</label>
										<input type="number" name="<?= $adjustedIndex === 0 ? 'unit' : 'unitA[]' ?>" value="<?= htmlspecialchars($record->unit); ?>" min="0" class="form-control" style="width:95% !important;">
									</div>
									<div class="col-sm-2 form-group">
										<label>Unit Price</label>
										<input type="number" name="<?= $adjustedIndex === 0 ? 'unitprice' : 'unitPriceA[]' ?>" value="<?= htmlspecialchars($record->unitPrice); ?>" min="0" step="0.01" class="form-control" style="width:95% !important;">
									</div>
									<div class="col-sm-7 form-group">
										<label>Unit Description</label>
										<textarea row="1" name="<?= $adjustedIndex === 0 ? 'unitDescription' : 'unitDescriptionA[]' ?>" class="form-control" style="width:95% !important; height:50px !important;"><?= htmlspecialchars($record->unitDescription); ?></textarea>
									</div>
									<div class="col-sm-1 form-group">
										<button type="button" class="btn <?= $adjustedIndex === 0 ? 'btn-success addRow' : 'btn-danger removeRow'; ?>" 
												style="float: right; margin-right: 34px; margin-top: 36px;">
											<?= $adjustedIndex === 0 ? '+' : '-'; ?>
										</button>
									</div>
								</div>
						<?php 
								$adjustedIndex++; 
							endif; 
						endforeach; 
						?>
				<?php endif; ?>
			</div>
		<?php } 
		if($extraDispatch) { 
            $d = 1;
            foreach($extraDispatch as $ex){
                if($ex['pd_type']=='dropoff'){
                    $d++;
                    echo '<div class="col-sm-6 form-group">
            			<label>Drop Off #'.$d.'</label>
            			<input type="text" name="dropoffExtra[]" value="'.$ex['ppd_location'].' ['.$ex['ppd_city'].']'.'" class="form-control"readonly>
            		</div>'; 
                }
            }
        }
        if(isset($_GET['dTable']) && $_GET['dTable'] == 'warehouse_dispatch'){
			if (!empty($expenseDetails)) {
				echo '<div class="col-sm-12"><label>Expenses</label></div>';
				foreach ($expenseDetails as $exp) {
					$title = isset($expenseTitles[$exp['expenseInfoId']]) ? $expenseTitles[$exp['expenseInfoId']] : 'Unknown';
					$value = $exp['expenseInfoValue'];
					echo '<div class="col-sm-4 form-group"><input type="text" name="expName[]" value="'.$title.'" class="form-control" readonly></div>';
					echo '<div class="col-sm-2 form-group"><input type="number" step="0.01" name="expPrice[]" value="'.$value.'" class="form-control" readonly></div>';
				}
			}
		}else{
			$dispatchMeta = json_decode($invoice[0]['dispatchMeta'],true);
			if($dispatchMeta['expense']) { 
				echo '<div class="col-sm-12"><label>Expenses</label></div>';
				foreach($dispatchMeta['expense'] as $expVal) {
					echo '<div class="col-sm-4 form-group"><input required type="text" name="expName[]" value="'.$expVal[0].'" class="form-control" readonly></div>';
					echo '<div class="col-sm-2 form-group"><input required type="number" step="0.01" name="expPrice[]" value="'.$expVal[1].'" class="form-control" readonly></div>';
				}
			}
		}
        
        
        $trailer = array();
        if(strstr($invoice[0]['trailer'],',')) { $trailer = explode(',',$invoice[0]['trailer']); }
        elseif($invoice[0]['trailer'] != ''){ $trailer[] = $invoice[0]['trailer']; }
        if($childTrailer){
            foreach($childTrailer as $child){
                $trailer[] = $child['trailer'];
                $dispatchMeta = json_decode($child['dispatchMeta'],true);
                if($dispatchMeta['expense']) { 
                    echo '<div class="col-sm-12"><label>Expenses</label></div>';
        			foreach($dispatchMeta['expense'] as $expVal) {
        			    echo '<div class="col-sm-4 form-group"><input required type="text" name="expName[]" value="'.$expVal[0].'" class="form-control" readonly></div>';
        			    echo '<div class="col-sm-2 form-group"><input required type="number" step="0.01" name="expPrice[]" value="'.$expVal[1].'" class="form-control" readonly></div>';
        			}
                }
            }
        }
        ?>
        <hr/>
        <div class="col-sm-12"><label><b>Dispatch Info</b></label></div>
        <div class="col-sm-6 form-group">
			<input type="text" name="trackingLabel" value="Customer Ref No." class="form-control" style="width:95% !important;" readonly>
		</div>
		<div class="col-sm-6 form-group">
			<input type="text" name="tracking" value="<?=$invoice[0]['tracking']?>" class="form-control" style="width:95% !important;" readonly>
		</div>
		
		<?php
		if(isset($_GET['dTable']) && $_GET['dTable'] == 'dispatchOutside'){
		    $bookingNo = '';
    		if($dispatchMeta['dispatchInfo']) { 
    			foreach($dispatchMeta['dispatchInfo'] as $diVal) { 
    				if($diVal[0]=='Booking No') { $bookingNo = $diVal[1]; }
    			}
    		}
    		?>
    		<div class="col-sm-6 form-group">
    			<input type="text" name="bookingnoLabel" value="Booking No." class="form-control" style="width:95% !important;">
			</div>
			<div class="col-sm-6 form-group">
    			<input type="text" name="bookingno" value="<?=$bookingNo?>" class="form-control" style="width:95% !important;">
    		</div>
    		<?php
		}
        ?>
        <!-- <div class="col-sm-12 form-group"> -->
			<label><?php if(isset($_GET['dTable']) && $_GET['dTable'] == 'dispatchOutside' && $dispatchMeta['invoicePDF'] != 'Trucking'){ $tLabel = 'Container No.: '; }
			else { $tLabel = 'Trailer No.: '; } ?></label>
			<?php
			if(count($trailer) == 0) { $trailerVal = 'N/A'; } 
            else { 
				$trailerss = implode(', ',$trailer); 
				$trailerVal = str_replace(' ,',',',str_replace('TBA','N/A',$trailerss));
			} 
			?>
		<div class="col-sm-6 form-group">
			<input type="text" name="trailerLabel" value="<?=$tLabel?>" class="form-control" readonly>
		</div>
		<div class="col-sm-6 form-group">
			<input type="text" name="trailer" value="<?=$trailerVal?>" class="form-control" readonly>
		</div>
		<div class="col-sm-12 form-group">
			<label>Invoice Description</label>
			<textarea readonly name="invoiceNotes" class="form-control"  style="width:98% !important;"><?=$invoice[0]['invoiceNotes']?></textarea>
		</div>
		<?php 
		exit;
	}
	
	public function statementOfAccount(){
	    if(!checkPermission($this->session->userdata('permission'),'statementAcc')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    
        //$sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        //$edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
        $company = $driver = $sdate = $edate = '';
        
        $data['dispatchURL'] = 'dispatch';
         
        if($this->input->post('search'))	{
            $company = $this->input->post('company');
            $driver = $this->input->post('driver');
            $dispatchType = $this->input->post('dispatchType');
            if($dispatchType == 'outsideDispatch'){ 
                $table = 'dispatchOutside'; 
                $data['dispatchURL'] = 'outside-dispatch';
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
			$data['dispatch'] = $this->Comancontroler_model->getStatementOfAccount($table,$sdate,$edate,$company,'no');
        } else {
            $data['dispatch'] = array();
        }
        
    	
		$data['drivers'] = array(); //$this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		//$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
		$data['locations'] = array(); //$this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = array(); //$this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = array(); //$this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/statement-of-account',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	public function downloadStatementPDF(){ 
	    if(!checkPermission($this->session->userdata('permission'),'statementAcc')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $this->load->library('pdf');
        $pdf = $this->pdf->load(); 
		$id = $this->uri->segment(3);
		$data['company'] = $this->Comancontroler_model->get_data_by_id($id,'companies');
		
		$data['invoice'] = array();
		$invoice = date('Y-m-d-H-i');
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$table = 'dispatch';
		$extraTable = 'dispatchExtraInfo';
		$data['type'] = '';
		
		if(isset($_GET['dTable']) && $_GET['dTable'] == 'dispatchOutside'){ 
			$table = 'dispatchOutside'; $extraTable = 'dispatchOutsideExtraInfo';
		} 
		if(isset($_GET['type']) && $_GET['type'] != ''){ $data['type'] = $_GET['type']; }
		
		$data['invoice'] = $this->Comancontroler_model->getStatementOfAccount($table,$sdate,$edate,$id,'no');
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
    				if(is_numeric($dispatchMeta['partialAmount'])) {
    					$partialAmt = $partialAmt + $dispatchMeta['partialAmount'];
    				}
    				if($data['type'] != '' && $dispatchMeta['invoicePDF'] != $data['type']){
    					continue;
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
    				if($dispatchMeta['invoicePaidDate'] != ''){ $showAging = 'false';  }
    				if($dispatchMeta['invoiceCloseDate'] != ''){ $showAging='false';  }
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
    			$rowArr = array('','','');
    			if($data['type'] == 'Drayage') { $rowArr[] = ''; }
    			$rowArr[] = ''; $rowArr[] = '';
    			$csvExcel[] = $rowArr;
    			
    			$subTotalRow = array('','','');
    			if($data['type'] == 'Drayage') { $subTotalRow[] = ''; }
    			$subTotalRow[] = 'Subtotal'; $subTotalRow[] = number_format($amount,2);
    			$csvExcel[] = $subTotalRow;
    			
    			$totalAmt = $amount;
    			if($partialAmt > 0) {
    				$partialAmtRow = array('','','');
    				if($data['type'] == 'Drayage') { $partialAmtRow[] = ''; }
    				$partialAmtRow[] = 'Partial Amount'; $partialAmtRow[] = number_format($partialAmt,2);
    				$csvExcel[] = $partialAmtRow;
    				$totalAmt = $totalAmt - $partialAmt;
    			}
    			
    			$totalRow = array('','','');
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
	
	public function downloadRateLoadConfirmationPDF() {
		// ob_start();

		// Check for permissions
		if (!checkPermission($this->session->userdata('permission'), 'statementAcc')) {
			//redirect(base_url('AdminDashboard')); 
		}

		$this->load->library('pdf');

		$pdf = $this->pdf->load(); 
		
		$data['dispatch'] = $data['extraDispatch'] = $data['truckCompany'] = $data['companyAddress'] = $data['locations'] = $data['cities'] = $data['userinfo'] = array();

		if (isset($_GET['id'])) {
			$id = $_GET['id'];
				if(is_numeric($id)){
					$data['dispatch'] = $this->Comancontroler_model->get_data_by_id($id,'dispatchOutside');
					$dispatchMetaJson = $data['dispatch'][0]['dispatchMeta'];
					$dispatchMeta = json_decode($dispatchMetaJson, true); 
					$invoicePDF = $dispatchMeta['invoicePDF'];
					if($invoicePDF == 'Drayage'){
						$erInformation = $dispatchMeta['erInformation'];
						if(!empty($erInformation)){
							$data['erInformation'] = $this->Comancontroler_model->get_data_by_id($erInformation,'erInformation');
						}
					}

					if($data['dispatch'][0]['userid'] > 0){
						$data['userinfo'] = $this->Comancontroler_model->get_data_by_id($data['dispatch'][0]['userid'],'admin_login');
					}
					$data['truckCompany'] = $this->Comancontroler_model->get_data_by_id($data['dispatch'][0]['truckingCompany'],'truckingCompanies');
					$data['extraDispatch'] = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id);
					$data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
					//$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
					//$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities'); 
				}
		}
		// echo "<pre>";
		// print_r($data['truckCompany'] );exit;
		$file = 'rateLoadConfirmationPDF';
		$html = $this->load->view('admin/' . $file, $data, true);
        //echo $html; die();
		// Generate PDF
		$pdf->WriteHTML($html);
		$pdfOutput = $pdf->Output('', 'S'); // Get PDF content as string
		$filename = 'Load Confirmation - ' . $data['dispatch'][0]['invoice'] . '.pdf';

		// Email Configuration
		/* $to = 'sumitpundir0541@gmail.com,vivek.1391@gmail.com';
		$subject = 'Rate and Load Confirmation';
		$message = '<p>Please find the attached Rate and Load Confirmation PDF.</p>';

		// Mail headers
		$headers = 'From: sumitpundir0541@gmail.com' . "\r\n" .
				   'Reply-To: sumitpundir0541@gmail.com' . "\r\n" .
				   'MIME-Version: 1.0' . "\r\n" .
				   'Content-Type: multipart/mixed; boundary="boundary1"' . "\r\n";

		// Body of the email
		$emailBody = '--boundary1' . "\r\n" .
					 'Content-Type: text/html; charset=UTF-8' . "\r\n" .
					 'Content-Transfer-Encoding: 7bit' . "\r\n" .
					 "\r\n" . $message . "\r\n" .
					 '--boundary1' . "\r\n" .
					 'Content-Type: application/pdf; name="' . $filename . '"' . "\r\n" .
					 'Content-Disposition: attachment; filename="' . $filename . '"' . "\r\n" .
					 'Content-Transfer-Encoding: base64' . "\r\n" .
					 "\r\n" . chunk_split(base64_encode($pdfOutput)) . "\r\n" .
					 '--boundary1--';

		// Send the email
		if (mail($to, $subject, $emailBody, $headers)) {
			echo "Email sent successfully! PDF is ready for download.";
		} else {
			echo "Failed to send email.";
		}

		ob_end_clean(); */

		// Download the PDF
		$pdf->Output($filename, 'D');
		exit;
	}
	public function downloadBolPDF() {
		if (!checkPermission($this->session->userdata('permission'), 'statementAcc')) {
			//redirect(base_url('AdminDashboard')); 
		}

		$this->load->library('pdf');

		$pdf = $this->pdf->load(); 
		
		$data['dispatch'] = $data['extraDispatch'] = array();

		if (isset($_GET['id'])) {
			$id = $_GET['id'];
				if(is_numeric($id)){
					$data['dispatch'] = $this->Comancontroler_model->getDispatchDataForBol($id,'dispatchOutside');
					$dispatchMetaJson = $data['dispatch'][0]['dispatchMeta'];
					$dispatchMeta = json_decode($dispatchMetaJson, true); 
					$dispatchInfoArr = [];
					if (!empty($dispatchMeta['dispatchInfo'])) {
						foreach ($dispatchMeta['dispatchInfo'] as $info) {
							if (isset($info[0]) && isset($info[1])) {
								$dispatchInfoArr[$info[0]] = trim($info[1]);
							}
						}
					}

					$data['dispatch'][0] = array_merge(
						$data['dispatch'][0],
						[
							'dispatchInfo' => $dispatchInfoArr,
							'quantityP' => $dispatchMeta['quantityP'] ?? '',
							'commodityP' => $dispatchMeta['commodityP'] ?? '',
							'metaDescriptionP' => $dispatchMeta['metaDescriptionP'] ?? '',
							'weightP' => $dispatchMeta['weightP'] ?? '',
							'quantityD' => $dispatchMeta['quantityD'] ?? '',
							'metaDescriptionD' => $dispatchMeta['metaDescriptionD'] ?? '',
							'weightD' => $dispatchMeta['weightD'] ?? ''
						]
					);
					$data['extraDispatch'] = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id);
				}
		}
		// echo "<pre>";
		// print_r($data);exit;

		$file = 'bol';
		$html = $this->load->view('admin/' . $file, $data, true);
       
		$pdf->WriteHTML($html);
		$pdfOutput = $pdf->Output('', 'S'); 
		$filename = 'BOL - ' . $data['dispatch'][0]['invoice'] . '.pdf';
		$pdf->Output($filename, 'D');
		exit;
	}

	public function indexOld() {
        /********* update status *********/
        if($this->input->post('statusonly') && $this->input->post('statusid'))	{
            $statusonly = $this->input->post('statusonly');
            $statusid = $this->input->post('statusid');
            if($statusonly!='' && $statusid > 0){
                $updatedata = array('status'=>$statusonly);
                $this->Comancontroler_model->update_table_by_id($statusid,'invoice',$updatedata);
                die('updated');
            }
        }
        
        $sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        $edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
        $company = $status = $invoice = $tracking = '';
         
        if($this->input->post('search'))	{
            $company = $this->input->post('companies');
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            $sdate = $this->input->post('sdate'); 
            $edate = $this->input->post('edate');  
        } else {
            $data['invoice'] = array();
        }
        
    	$data['invoice'] = $this->Comancontroler_model->get_invoice_by_filter($sdate,$edate,$company,$status);
    	
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','*','company','asc');
		//$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		//$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/invoice',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	public function invoiceAdd() {
 
		if($this->input->post('save'))	{ 
				$this->form_validation->set_rules('puDate', 'Pickup date','required|min_length[9]');
				$this->form_validation->set_rules('company', 'company','required|min_length[1]'); 
				
			
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else { 
                    $replaceFileName = array(',',"'",'"','(',')','/','.','<');
                    
					/******************* check companies ****************/
					$check_company = $this->input->post('company');
					$company = $this->check_company($check_company);
					
					$companyInfo = $this->Comancontroler_model->get_data_by_id($company,'companies','paymenTerms,payoutRate,dayToPay');
					
					$puDate = $this->input->post('puDate'); 
					$paRate = $this->input->post('paRate'); 
					$invoiceDate = $this->input->post('invoiceDate');
					
					$week = date('M',strtotime($puDate)).' W';
					$day = date('d',strtotime($puDate));
					if($day < 9) { $w = '1'; }
					elseif($day < 16){ $w = '2'; }
					elseif($day < 24){ $w = '3'; }
					else { $w = '4'; }
					$week .= $w; 
					
					$payoutRate = $companyInfo[0]['payoutRate'];
					if(!is_numeric($payoutRate)) { $payoutRate = 0; }
					$payoutAmount = $payoutRate * $paRate;
					$payoutAmount = round($payoutAmount,2);
					
					$invoiceType = $companyInfo[0]['paymenTerms'];
					$dayToPay = $companyInfo[0]['dayToPay'];
					if($dayToPay < 2) {$pDay = '+ '.$dayToPay.' day'; }
					elseif($dayToPay > 1) {$pDay = '+ '.$dayToPay.' days'; }
					else { $pDay = "+ 0 day"; }
					
					
					$insert_data = array(
					    'puDate'=>$puDate,
					    'invoiceNo'=>$this->input->post('invoiceNo'),
					    'company'=>$company,
					    'tracking'=>$this->input->post('tracking'),
					    'rate'=>$this->input->post('rate'),
					    'paRate'=>$paRate,
					    'payoutAmount'=>$payoutAmount,
					    'week'=>$week,
					    'invoiceType'=>$invoiceType, 
					    'bol'=>$this->input->post('bol'),
					    'rc'=>$this->input->post('rc'),
					    'gd'=>$this->input->post('gd'),
					    'status'=>$this->input->post('status')
					);
					if($invoiceDate != 'TBD'){
					    $insert_data['invoiceDate'] = $invoiceDate;
					    $expectPayDate = date('Y-m-d',strtotime($pDay,strtotime($invoiceDate)));
					    $insert_data['expectPayDate'] = $expectPayDate;
					}
				
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'invoice'); 
					if($res){
					    
					    /*********** upload documents *********/
					    //$config['upload_path'] = 'assets/invoice/';
                        $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
                        $config['max_size']= '5000';
                        
                        /******** rename documents *************/                        
                        $fileName1 = date('m-d-y',strtotime($puDate)).'-Invoice-'.$res;
                        $fileName1 = str_replace(' ','-',$fileName1);
                        $fileName1 = str_replace($replaceFileName,'',$fileName1);
                        $fileName2 = rand(100,9999);
                        $fileName2 = str_replace(' ','-',$fileName2);
                        $fileName2 = str_replace($replaceFileName,'',$fileName2);
						
                    if(!empty($_FILES['bol_d']['name'])){
						$config['upload_path'] = 'assets/invoice/bol/';
                        $config['file_name'] = $fileName1.'-BOL-'.$fileName2; //$_FILES['bol_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('bol_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$res,'type'=>'bol','fileurl'=>$bol);
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsInvoice');
                        }
                    } 
					if(!empty($_FILES['rc_d']['name'])){
						$config['upload_path'] = 'assets/invoice/rc/';
                        $config['file_name'] = $fileName1.'-RC-'.$fileName2; //$_FILES['rc_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('rc_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$res,'type'=>'rc','fileurl'=>$bol);
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsInvoice');
                        }
                    } 
					if(!empty($_FILES['gd_d']['name'])){
						$config['upload_path'] = 'assets/invoice/gd/';
                        $config['file_name'] = $fileName1.'-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('gd_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$res,'type'=>'gd','fileurl'=>$bol);
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsInvoice');
                        }
                    } 
                    
						$this->session->set_flashdata('item', 'Invoice  added successfully.');
                        redirect(base_url('admin/invoice/add'));
					}
 				   
				}
	}
      
      $id = $this->uri->segment(4);
      if($id > 0){
          $data['duplicate'] = $this->Comancontroler_model->get_data_by_id($id,'invoice');
      } else {
          $data['duplicate'] = array();
      }
      
	  $data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
	  
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/invoice_add',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	public function invoiceUpdate() {
 
		$id = $this->uri->segment(4);
		if($this->input->post('save'))	{
				
				$this->form_validation->set_rules('puDate', 'PU date','required|min_length[9]');
				$this->form_validation->set_rules('company', 'company','required|min_length[1]'); 
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
                    $replaceFileName = array(',',"'",'"','(',')','/','.','<'); 
                    $puDate = $this->input->post('puDate');
                    
					/******************** upload files ****************/
                        $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
                        $config['max_size']= '5000';
                        
                        /******** rename documents *************/
                        
                        $fileName1 = date('m-d-y',strtotime($puDate)).'-Invoice-'.$id;
                        $fileName1 = str_replace(' ','-',$fileName1);
                        $fileName1 = str_replace($replaceFileName,'',$fileName1);
                        $fileName2 = rand(10,9999);
                        $fileName2 = str_replace(' ','-',$fileName2);
                        $fileName2 = str_replace($replaceFileName,'',$fileName2);
						
                    if(!empty($_FILES['bol_d']['name'])){
						$config['upload_path'] = 'assets/invoice/bol/';
                        $config['file_name'] = $fileName1.'-BOL-'.$fileName2; //$_FILES['bol_d']['name'];  
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('bol_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$id,'type'=>'bol','fileurl'=>$bol);
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsInvoice');
                        }
                    } 
					if(!empty($_FILES['rc_d']['name'])){
						$config['upload_path'] = 'assets/invoice/rc/';
                        $config['file_name'] = $fileName1.'-RC-'.$fileName2; //$_FILES['rc_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('rc_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$id,'type'=>'rc','fileurl'=>$bol);
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsInvoice');
                        }
                    } 
					if(!empty($_FILES['gd_d']['name'])){
						$config['upload_path'] = 'assets/invoice/gd/';
                        $config['file_name'] = $fileName1.'-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('gd_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$id,'type'=>'gd','fileurl'=>$bol);
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsInvoice');
                        }
                    } 
                    
					/******************* check companies ****************/
					$check_company = $this->input->post('company');
					$company = $this->check_company($check_company);
					
					$companyInfo = $this->Comancontroler_model->get_data_by_id($company,'companies','paymenTerms,payoutRate,dayToPay');
					
					$paRate = $this->input->post('paRate'); 
					$invoiceDate = $this->input->post('invoiceDate');
					
					$week = date('M',strtotime($puDate)).' W';
					$day = date('d',strtotime($puDate));
					if($day < 9) { $w = '1'; }
					elseif($day < 16){ $w = '2'; }
					elseif($day < 24){ $w = '3'; }
					else { $w = '4'; }
					$week .= $w; 
					
					$payoutRate = $companyInfo[0]['payoutRate'];
					if(!is_numeric($payoutRate)) { $payoutRate = 0; }
					$payoutAmount = $payoutRate * $paRate;
					$payoutAmount = round($payoutAmount,2);
					
					$invoiceType = $companyInfo[0]['paymenTerms'];
					$dayToPay = $companyInfo[0]['dayToPay'];
					if($dayToPay < 2) {$pDay = '+ '.$dayToPay.' day'; }
					elseif($dayToPay > 1) {$pDay = '+ '.$dayToPay.' days'; }
					else { $pDay = "+ 0 day"; }
					
					
					$insert_data = array(
					    'puDate'=>$puDate,
					    'invoiceNo'=>$this->input->post('invoiceNo'),
					    'company'=>$company,
					    'tracking'=>$this->input->post('tracking'),
					    'rate'=>$this->input->post('rate'),
					    'paRate'=>$paRate,
					    'payoutAmount'=>$payoutAmount,
					    'week'=>$week,
					    'invoiceType'=>$invoiceType, 
					    'bol'=>$this->input->post('bol'),
					    'rc'=>$this->input->post('rc'),
					    'gd'=>$this->input->post('gd'),
					    'status'=>$this->input->post('status')
					);
					if($invoiceDate != 'TBD'){
					    $insert_data['invoiceDate'] = $invoiceDate;
					    $expectPayDate = date('Y-m-d',strtotime($pDay,strtotime($invoiceDate)));
					    $insert_data['expectPayDate'] = $expectPayDate;
					}
				
					$res = $this->Comancontroler_model->update_table_by_id($id,'invoice',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Invoice updated successfully.');
                        redirect(base_url('admin/invoice/update/'.$id));
					}
 				   
				}
		}
     
		$data['invoices'] = $this->Comancontroler_model->get_data_by_id($id,'invoice'); 
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
		$data['documents'] = $this->Comancontroler_model->get_data_by_column('did',$id,'documentsInvoice');
		//$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities'); 
     
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/invoice_update',$data);
    	$this->load->view('admin/layout/footer');
	}
 
    public function ajaxdelete(){
        if($this->input->post('ajaxdelete'))	{
            $did = $this->input->post('deleteid'); 
            $this->Comancontroler_model->delete($did,'invoice','id');
        }
    }
 
	public function removefile(){
		$folder = $this->uri->segment(4);
		$did = $this->uri->segment(6);
		$id = $this->uri->segment(5);
		$file = $this->Comancontroler_model->get_data_by_id($id,'documentsInvoice');
		if(empty($file)) {
			$this->session->set_flashdata('item', 'File not exist.'); 
		} else {
			if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/invoice/'.$folder.'/'.$file[0]['fileurl'])) {
				unlink(FCPATH.'assets/invoice/'.$folder.'/'.$file[0]['fileurl']);  
				
				$this->session->set_flashdata('item', 'Document removed successfully.'); 
			}
			$this->Comancontroler_model->delete($id,'documentsInvoice','id');
		}
		redirect(base_url('admin/invoice/update/'.$did));
	}
   
	public function check_company($company) {
		$company_data = $this->Comancontroler_model->get_data_by_name($company);
		if(empty($company_data)) {
			$insert_data = array('company'=>$company);
			$res = $this->Comancontroler_model->add_data_in_table($insert_data,'companies'); 
			return $res;
		} else {
			return $company_data[0]['id']; 
		}
	}

	public function setSubInvoices() {
		$parentId = $this->input->post('parentInvoiceId');
		$childInvoiceIds = $this->input->post('childInvoiceIds'); 
		$dispatchType = $this->input->post('dispatchType'); 
		$table = '';
		if($dispatchType == 'paDispatch'){
			$table = 'dispatch'; 
		}elseif($dispatchType == 'outsideDispatch'){
			$table = 'dispatchOutside'; 
		}elseif($dispatchType == 'warehouseDispatch'){
			$table = 'warehouse_dispatch'; 
		}

		if ($table == '') {
			echo json_encode(['status' => 'error', 'msg' => 'Please set a division.']);
			return;
		}
		if (!$parentId || empty($childInvoiceIds)) {
			echo json_encode(['status' => 'error', 'msg' => 'Invalid selection.']);
			return;
		}

		


		$this->db->select('invoice,childInvoice');
		$this->db->where('id', $parentId);
		$parentRow = $this->db->get($table)->row();

		$this->db->select('id, invoice, parentInvoice');
		$this->db->from($table);
		$this->db->where_in('id', $childInvoiceIds);
		$this->db->where("(parentInvoice IS NOT NULL AND parentInvoice != '')", NULL, FALSE);
		$query = $this->db->get();
		$existingParents = $query->result();

		if (!empty($existingParents)) {
			foreach ($existingParents as $child) {
				if ($child->parentInvoice != $parentRow->invoice) { 
					$response = [
						'status' => 'error',
						'message' => "Sub-invoicing failed as the invoice <b>{$child->invoice}</b> is also a sub-invoice of <b>{$child->parentInvoice}</b>."
					];
					echo json_encode($response);
					return;
				}
			}
		
		}

		$existingInvoices = [];
		if ($parentRow && !empty($parentRow->childInvoice)) {
			$existingInvoices = array_map('trim', explode(',', $parentRow->childInvoice));
		}

		$this->db->select('id,invoice');
		$this->db->where_in('id', $childInvoiceIds);
		$childRows = $this->db->get($table)->result();

		$newInvoices = [];
		foreach ($childRows as $row) {
			$newInvoices[] = $row->invoice;
		}

		$allInvoices = array_unique(array_merge($existingInvoices, $newInvoices));

		$this->db->where('id', $parentId);
		$this->db->update($table, [
			'childInvoice' => implode(',', $allInvoices)
		]);

		if ($parentRow) {
			$this->db->select('*');
			$this->db->where('id', $parentId);
			$parentFullRow = $this->db->get($table)->row_array();

			if (empty($parentFullRow['dispatchMeta'])) {
				$parentMeta = [
					'expense'=>[],
					'invoiced'=>'0',
					'invoicePaid'=>'0',
					'invoiceClose'=>'0',
					'invoiceReady'=>'0',
					'invoiceReadyDate'=>'',
					'invoicePaidDate'=>'',
					'invoiceCloseDate'=>''
				];
			} else {
				$parentMeta = json_decode($parentFullRow['dispatchMeta'], true);
			}
			foreach ($childRows as $row) {
				$this->db->select('id, dispatchMeta');
				$this->db->where('id', $row->id);
				$childData = $this->db->get($table)->row_array();
				if (!$childData) continue;
				if (empty($childData['dispatchMeta'])) {
					$currentDiMeta = [
						'expense'=>[],
						'invoiced'=>'0',
						'invoicePaid'=>'0',
						'invoiceClose'=>'0',
						'invoiceReady'=>'0',
						'invoiceReadyDate'=>'',
						'invoicePaidDate'=>'',
						'invoiceCloseDate'=>''
					];
				} else {
					$currentDiMeta = json_decode($childData['dispatchMeta'], true);
				}

				$currentDiMeta['invoiceReadyDate']   = $parentMeta['invoiceReadyDate'];
				$currentDiMeta['invoicePaidDate']    = $parentMeta['invoicePaidDate'];
				$currentDiMeta['invoiceCloseDate']   = $parentMeta['invoiceCloseDate'];
				$currentDiMeta['invoiceReady']       = $parentMeta['invoiceReady'];
				$currentDiMeta['invoicePaid']        = $parentMeta['invoicePaid'];
				$currentDiMeta['invoiceClose']       = $parentMeta['invoiceClose'];
				$currentDiMeta['invoiced']           = $parentMeta['invoiced'];

				if($table == 'dispatchOutside'){
					$currentDiMeta['custInvDate']        = $parentMeta['custInvDate'];
					$currentDiMeta['carrierInvoiceCheck']= $parentMeta['carrierInvoiceCheck'];
					$currentDiMeta['custDueDate']        = $parentMeta['custDueDate'];
					$currentDiMeta['carrierPayoutCheck'] = $parentMeta['carrierPayoutCheck'];
					$currentDiMeta['carrierPayoutDate'] = $parentMeta['carrierPayoutDate'];
				}
				
				$updateArr = [];
				$updateArr['dispatchMeta'] = json_encode($currentDiMeta);

				if (!empty($parentFullRow['invoiceDate'])) {
					$updateArr['invoiceDate']   = $parentFullRow['invoiceDate'];
					$updateArr['expectPayDate'] = $parentFullRow['expectPayDate'];
				}
				$updateArr['invoiceNotes']   = $parentFullRow['invoiceNotes'];
				$updateArr['invoiceType']    = $parentFullRow['invoiceType'];
				$updateArr['bol']            = $parentFullRow['bol'];
				$updateArr['rc']             = $parentFullRow['rc'];
				$updateArr['gd']             = $parentFullRow['gd'];
				$updateArr['delivered']      = $parentFullRow['delivered'];
				$updateArr['shipping_contact'] = $parentFullRow['shipping_contact'];
				$updateArr['driver_status']  = $parentFullRow['driver_status'];

				$fullStatus = $parentFullRow['status'];
				$firstPart = explode(' - Linked to', $fullStatus)[0];
				$updateArr['status'] = $firstPart . ' - Linked to ' . $parentFullRow['invoice'];
				$updateArr['parentInvoice'] = $parentFullRow['invoice'];

				$this->db->where('id', $row->id);
				$this->db->update($table, $updateArr);
			}
		}

		echo json_encode([
			'status' => 'success',
			'message' =>'<b>' .implode(',', $newInvoices) . '</b> are now the sub-invoices of <b>' . $parentRow->invoice . '</b>'
		]);
	}
}