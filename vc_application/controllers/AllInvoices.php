<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AllInvoices extends CI_Controller {
    
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
	}
	
	public function dbPAFleetInvoices() {
	    if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
		$table = 'dispatch';
	    
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
					 else { return false; }
					 
					$insert_data = array(
					    'rate'=>$this->input->post('rate_input'),
					    'parate'=>$this->input->post('parate_input'), 
					    'payoutAmount'=>$this->input->post('payoutAmount_input'),
					    'invoiceDate'=>$this->input->post('invoiceDate_input'),
					    //'invoice'=>$this->input->post('invoice_input'),
					    'expectPayDate'=>$this->input->post('expectPayDate_input'),
					    'invoiceType'=>$this->input->post('invoiceType_input'),
					    'status'=>$this->input->post('status_input')
					);
				
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
            // $dispatchType = $this->input->post('dispatchType');
            // $invoiceType = $this->input->post('invoiceType');
            $table = 'dispatch';
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			$data['dispatch'] = $this->AllInvoices_model->get_db_pa_invoices($table,$sdate,$edate,$company,$driver);
        } else {
            $data['dispatch'] = array();
        }
        
		$data['dispatch'] = $this->AllInvoices_model->get_db_pa_invoices($table,$sdate,$edate,$company,$driver);

		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/dbPAFleetInvoice',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function dbPALogisticsInvoices() {
	    if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
		$table = 'dispatchOutside'; 

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
					 else { return false; }
					 
					$insert_data = array(
					    'rate'=>$this->input->post('rate_input'),
					    'parate'=>$this->input->post('parate_input'), 
					    'payoutAmount'=>$this->input->post('payoutAmount_input'),
					    'invoiceDate'=>$this->input->post('invoiceDate_input'),
					    //'invoice'=>$this->input->post('invoice_input'),
					    'expectPayDate'=>$this->input->post('expectPayDate_input'),
					    'invoiceType'=>$this->input->post('invoiceType_input'),
					    'status'=>$this->input->post('status_input')
					);
				
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
		$data['dispatchURL'] = 'outside-dispatch';       
        if($this->input->post('search'))	{
            $company = $this->input->post('company');
            $driver = $this->input->post('driver');
            // $dispatchType = $this->input->post('dispatchType');
            // $invoiceType = $this->input->post('invoiceType');
			$table = 'dispatchOutside'; 
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			$data['dispatch'] = $this->AllInvoices_model->get_db_pa_logistics_invoices($table,$sdate,$edate,$company,$driver);
        } else {
            $data['dispatch'] = array();
        }
		$data['dispatch'] = $this->AllInvoices_model->get_db_pa_logistics_invoices($table,$sdate,$edate,$company,$driver);
    	
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/dbPALogisticsInvoice',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function qpPAFleetInvoices() {
	    if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
		$table = 'dispatch';

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
					 else { return false; }
					 
					$insert_data = array(
					    'rate'=>$this->input->post('rate_input'),
					    'parate'=>$this->input->post('parate_input'), 
					    'payoutAmount'=>$this->input->post('payoutAmount_input'),
					    'invoiceDate'=>$this->input->post('invoiceDate_input'),
					    //'invoice'=>$this->input->post('invoice_input'),
					    'expectPayDate'=>$this->input->post('expectPayDate_input'),
					    'invoiceType'=>$this->input->post('invoiceType_input'),
					    'status'=>$this->input->post('status_input')
					);
				
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
            // $dispatchType = $this->input->post('dispatchType');
            // $invoiceType = $this->input->post('invoiceType');
            $table = 'dispatch';
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			$data['dispatch'] = $this->AllInvoices_model->get_qp_pa_invoices($table,$sdate,$edate,$company,$driver);
        } else {
            $data['dispatch'] = array();
        }
		$data['dispatch'] = $this->AllInvoices_model->get_qp_pa_invoices($table,$sdate,$edate,$company,$driver);

    	
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/qpPAFleetInvoice',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function rtsPAFleetInvoices() {
	    if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
		$table = 'dispatch';

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
					 else { return false; }
					 
					$insert_data = array(
					    'rate'=>$this->input->post('rate_input'),
					    'parate'=>$this->input->post('parate_input'), 
					    'payoutAmount'=>$this->input->post('payoutAmount_input'),
					    'invoiceDate'=>$this->input->post('invoiceDate_input'),
					    //'invoice'=>$this->input->post('invoice_input'),
					    'expectPayDate'=>$this->input->post('expectPayDate_input'),
					    'invoiceType'=>$this->input->post('invoiceType_input'),
					    'status'=>$this->input->post('status_input')
					);
				
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
            // $dispatchType = $this->input->post('dispatchType');
            // $invoiceType = $this->input->post('invoiceType');
            $table = 'dispatch';
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			$data['dispatch'] = $this->AllInvoices_model->get_rts_pa_invoices($table,$sdate,$edate,$company,$driver);
        } else {
            $data['dispatch'] = array();
        }
		$data['dispatch'] = $this->AllInvoices_model->get_rts_pa_invoices($table,$sdate,$edate,$company,$driver);

    	
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/rtsPAFleetInvoice',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function dbPAWarehousingInvoices()
	{
	    if(!checkPermission($this->session->userdata('permission'),'invoice')){
	        redirect(base_url('AdminDashboard'));   
	    }
		$table = 'warehouse_dispatch'; 
		     
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
					 elseif($type == 'warehouse_dispatch'){ $table = 'warehouse_dispatch'; }
					 else { return false; }
					 
					$insert_data = array(
					    'rate'=>$this->input->post('rate_input'),
					    'parate'=>$this->input->post('parate_input'), 
					    'payoutAmount'=>$this->input->post('payoutAmount_input'),
					    'invoiceDate'=>$this->input->post('invoiceDate_input'),
					    //'invoice'=>$this->input->post('invoice_input'),
					    'expectPayDate'=>$this->input->post('expectPayDate_input'),
					    'invoiceType'=>$this->input->post('invoiceType_input'),
					    'status'=>$this->input->post('status_input')
					);
				
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
		$data['dispatchURL'] = 'paWarehouse';       
        if($this->input->post('search'))	{
            $company = $this->input->post('company');
            $driver = $this->input->post('driver');
            // $dispatchType = $this->input->post('dispatchType');
            // $invoiceType = $this->input->post('invoiceType');
			$table = 'warehouse_dispatch'; 
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
			$data['dispatch'] = $this->AllInvoices_model->get_db_pa_warehousing_invoices($table,$sdate,$edate,$company,$driver);
        } else {
            $data['dispatch'] = array();
        }
		$data['dispatch'] = $this->AllInvoices_model->get_db_pa_warehousing_invoices($table,$sdate,$edate,$company,$driver);
    	
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers','id,dname','dname','asc');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/dbPAWarehousingInvoice',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function getInvoiceCountsAjax()
	{
		$data = [
			'dbPAFleet'     => $this->getInvoiceCount('Direct Bill','dispatch'),
			'dbPALogistics' => $this->getInvoiceCount('Direct Bill','dispatchOutside'),
			'qpPAFleet'     => $this->getInvoiceCount('Quick Pay','dispatch'),
			'rtsPAFleet'    => $this->getInvoiceCount('RTS','dispatch'),
			'dbPAWarehousing' => $this->getInvoiceCount('Direct Bill','warehouse_dispatch')
		];
		echo json_encode($data);
	}
	
	public function getInvoiceCount($type, $table)
	{
		$this->db->select('COUNT(*) as total_count, 
						COALESCE(SUM(rate),0) as total_rate, 
						COALESCE(SUM(parate),0) as total_parate', false);
		$this->db->where('invoiceType', $type);
		if($table == 'warehouse_dispatch'){
			$this->db->where("invoiceReady = '1'");
			$this->db->group_start();
				$this->db->where("invoiced = '0'");
				$this->db->or_where("invoiced = ''");
			$this->db->group_end();
			$this->db->group_start();
				$this->db->where("invoicePaid = '0'");
				$this->db->or_where("invoicePaid = ''");
			$this->db->group_end();
			$this->db->group_start();
				$this->db->where("invoiceClose = '0'");
				$this->db->or_where("invoiceClose = ''");
			$this->db->group_end();
		}else{
			$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceReady') = '1'");
			$this->db->group_start();
				$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiced') = '0'");
				$this->db->or_where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiced') = ''");
			$this->db->group_end();
			$this->db->group_start();
				$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoicePaid') = '0'");
				$this->db->or_where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoicePaid') = ''");
			$this->db->group_end();
			$this->db->group_start();
				$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = '0'");
				$this->db->or_where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = ''");
			$this->db->group_end();
		}
		$query = $this->db->get($table);
		return $query->row_array(); 
	}

	public function getInvoicesCounts()
	{
		$data = [
			'InvoicesCount'     => $this->getInvoicesCount()
		];
		echo json_encode($data);
	}
	public function getInvoicesCount()
	{
		$where='1=1';
		$dispatchType = $this->input->post('dispatchType');
		$invoiceSearch = $this->input->post('invoiceSearch');
	
		if($dispatchType == 'paDispatch'){ 
			$table = 'dispatch'; 
		}
		elseif($dispatchType == 'outsideDispatch') { $table = 'dispatchOutside'; }
		elseif($dispatchType == 'warehouseDispatch') { $table = 'warehouse_dispatch'; }

		
		if($invoiceSearch=='pendingInvoices'){
			if($dispatchType == 'warehouseDispatch'){
				$where = "d.`driver_status`='Shipment Delivered' AND (invoiceReady = '0' OR invoiceReady = '')";
			}else{
				$where = "d.`driver_status`='Shipment Delivered' AND ((d.dispatchMeta IS NOT NULL AND d.dispatchMeta != '' AND (JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.invoiceReady')) = '0' OR JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.invoiceReady')) = '')))";
			}
			
		}
		$sql="SELECT COUNT(*) AS `pending_invoices_count`, SUM(d.parate) AS `pending_invoices_amount` FROM $table d WHERE $where";
		// echo $sql;exit;
		$query = $this->db->query($sql);
		return $query->row(); 
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
		} else {
			if(is_numeric($id)) {
				$data['invoice'] = $this->Comancontroler_model->downloadDispatchInvoice($id);
				$data['extraDispatch'] = $this->Comancontroler_model->getExtraDispatchInfo($id);
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
		} else {
		    $data['trackingLabel'] = 'Customer Ref No.';
		    $data['bookingnoLabel'] = 'Booking No.';
		    $data['trailerValLabel'] = '';
		    $data['trailerVal'] = $data['bookingno'] = $data['tracking'] = $data['expPrice'] = $data['expName'] = $data['invoiceDate'] = $data['dropoffExtra'] = $data['dropoff'] = $data['pickup'] = $data['contactPerson'] = $data['cdepartment'] = $data['cemail'] = $data['cphone'] = $data['invoiceNotes'] = '';
		}
		
		$html = $this->load->view('admin/'.$file.'New', $data, true);
		//echo $html;die();
		
		$stylesheet = "";
		//$pdf->WriteHTML($stylesheet, 1);
		
		$pdf->WriteHTML($html);
        // write the HTML into the PDF
        
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
    				if(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'dispatch' && stristr($doc['fileurl'],'.pdf')) { 
    				    $pdfArray[] = FCPATH.'assets/upload/'.$doc['fileurl']; 
    				}
    				elseif(($doc['type']=='bol' || $doc['type']=='rc') && $type == 'outside' && stristr($doc['fileurl'],'.pdf')){
    				    $pdfArray[] = FCPATH.'assets/outside-dispatch/'.$doc['type'].'/'.$doc['fileurl']; 
    				}
    			}
	        }
			
	        $this->load->library('PdfMerger');
	        
            // Merge the PDFs and download the result
            try {
                $this->pdfmerger->merge_pdfs($pdfArray, 'Invoice # '.$invoice.'.pdf', 'D');
            } catch(Exception $e) {
				// combaine with image 
				$this->load->library('PdfMergerNewNew');
				// Merge the PDFs and download the result
				try {
					//$this->pdfmergernewnew->merge_pdfs_with_images($pdfArray, 'Invoice # '.$invoice.'.pdf','D'); // 'D' to force download
					$this->pdfmergernewnew->addPdf($pdfArray);
                    $this->pdfmergernewnew->download('Invoice # '.$invoice.'.pdf');
				} catch(Exception $e) {
					echo 'Message: Some pdf files are not proper format'; // .$e->getMessage();
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
		} else {
			$invoice = $this->Comancontroler_model->downloadDispatchInvoice($id);
			$extraDispatch = $this->Comancontroler_model->getExtraDispatchInfo($id);
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
			<input required type="text" name="invoiceName" value="<?=$invoiceName?>" class="form-control">
		</div>
		<div class="col-sm-6 form-group">
			<label>Invoice Date</label>
			<input required type="text" name="invoiceDate" value="<?php if($invoice[0]['invoiceDate'] != '0000-00-00') { echo date('Y-m-d',strtotime($invoice[0]['invoiceDate'])); } ?>" class="form-control datepicker">
		</div>
		<div class="col-sm-6 form-group">
			<label>Shipping Contact Person</label>
			<input type="text" name="contactPerson" value="<?=$invoice[0]['contactPerson']?>" class="form-control">
		</div>
		<div class="col-sm-6 form-group">
			<label>Shipping Department</label>
			<input type="text" name="cdepartment" value="<?=$invoice[0]['cdepartment']?>" class="form-control">
		</div>
		<div class="col-sm-6 form-group">
			<label>Shipping Email</label>
			<input type="email" name="cemail" value="<?=$invoice[0]['cemail']?>" class="form-control">
		</div>
		<div class="col-sm-6 form-group">
			<label>Shipping Phone</label>
			<input type="text" name="cphone" value="<?=$invoice[0]['cphone']?>" class="form-control">
		</div>
		<div class="col-sm-6 form-group">
			<label>PickUp</label>
			<input type="text" name="pickup" value="<?=$invoice[0]['pplocation']?> [<?=$invoice[0]['ppcity']?>]" class="form-control">
		</div>
		<div class="col-sm-6 form-group">
			<label>Drop Off</label>
			<input type="text" name="dropoff" value="<?=$invoice[0]['ddlocation']?> [<?=$invoice[0]['ddcity']?>]" class="form-control">
		</div>
		
		<?php
		if(isset($_GET['dTable']) && $_GET['dTable'] == 'dispatchOutside'){}
		else {
		    ?>
		    <div class="col-sm-6 form-group">
    			<label>Unit</label>
    			<input type="number" name="unit" value="0" min="0" class="form-control">
    		</div>
		    <div class="col-sm-6 form-group">
    			<label>Unit Price</label>
    			<input type="number" name="unitprice" value="0" min="0" step="0.01" class="form-control">
    		</div>
		    <?php
		}
		if($extraDispatch) { 
            $d = 1;
            foreach($extraDispatch as $ex){
                if($ex['pd_type']=='dropoff'){
                    $d++;
                    echo '<div class="col-sm-6 form-group">
            			<label>Drop Off #'.$d.'</label>
            			<input type="text" name="dropoffExtra[]" value="'.$ex['ppd_location'].' ['.$ex['ppd_city'].']'.'" class="form-control">
            		</div>'; 
                }
            }
        }
        
        $dispatchMeta = json_decode($invoice[0]['dispatchMeta'],true);
        if($dispatchMeta['expense']) { 
            echo '<div class="col-sm-12"><label>Expenses</label></div>';
			foreach($dispatchMeta['expense'] as $expVal) {
			    echo '<div class="col-sm-4 form-group"><input required type="text" name="expName[]" value="'.$expVal[0].'" class="form-control"></div>';
			    echo '<div class="col-sm-2 form-group"><input required type="number" step="0.01" name="expPrice[]" value="'.$expVal[1].'" class="form-control"></div>';
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
        			    echo '<div class="col-sm-4 form-group"><input required type="text" name="expName[]" value="'.$expVal[0].'" class="form-control"></div>';
        			    echo '<div class="col-sm-2 form-group"><input required type="number" step="0.01" name="expPrice[]" value="'.$expVal[1].'" class="form-control"></div>';
        			}
                }
            }
        }
        ?>
        
        <div class="col-sm-12"><label>Dispatch Info</label></div>
        <div class="col-sm-6 form-group">
			<input type="text" name="trackingLabel" value="Customer Ref No." class="form-control">
			<input type="text" name="tracking" value="<?=$invoice[0]['tracking']?>" class="form-control">
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
    			<input type="text" name="bookingnoLabel" value="Booking No." class="form-control">
    			<input type="text" name="bookingno" value="<?=$bookingNo?>" class="form-control">
    		</div>
    		<?php
		}
        ?>
        <div class="col-sm-12 form-group">
			<label><?php if(isset($_GET['dTable']) && $_GET['dTable'] == 'dispatchOutside' && $dispatchMeta['invoicePDF'] != 'Trucking'){ $tLabel = 'Container No.: '; }
			else { $tLabel = 'Trailer No.: '; } ?></label>
			<?php
			if(count($trailer) == 0) { $trailerVal = 'N/A'; } 
            else { 
				$trailerss = implode(', ',$trailer); 
				$trailerVal = str_replace(' ,',',',str_replace('TBA','N/A',$trailerss));
			} 
			?>
			<input type="text" name="trailerLabel" value="<?=$tLabel?>" class="form-control">
			<input type="text" name="trailer" value="<?=$trailerVal?>" class="form-control">
		</div>
		<div class="col-sm-12 form-group">
			<label>Invoice Description</label>
			<textarea name="invoiceNotes" class="form-control"><?=$invoice[0]['invoiceNotes']?></textarea>
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
					if($data['dispatch'][0]['userid'] > 0){
						$data['userinfo'] = $this->Comancontroler_model->get_data_by_id($data['dispatch'][0]['userid'],'admin_login');
						$data['truckCompany'] = $this->Comancontroler_model->get_data_by_id($data['dispatch'][0]['truckingCompany'],'truckingCompanies');
					}
					$data['extraDispatch'] = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id);
					$data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
					//$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
					//$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities'); 
				}
		}

		$file = 'rateLoadConfirmationPDF';
		$html = $this->load->view('admin/' . $file, $data, true);

		// Generate PDF
		$pdf->WriteHTML($html);
		$pdfOutput = $pdf->Output('', 'S'); // Get PDF content as string
		$filename = 'Rate and Load Confirmation' . date('m-d-Y') . '.pdf';

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

}