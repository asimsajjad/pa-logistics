<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dispatch extends CI_Controller {
    
    //private $expenses = array('Line Haul','FSC (Fuel Surcharge)','Pre-Pull','Lumper','Detention at Shipper','Detention at Receiver','Detention at Port','Drivers Assist','Gate Fee','Overweight Charges','Delivery Order Charges','Chassis Rental','Demurrage','Layover','Yard Storage','Customs Clearance','Chassis Gate Fee','Chassis Split Fee','Others','TONU','Discount','Dry Run','ISF Filing','Customs Clearance');
	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->model('Comancontroler_model');
	 
		//$this->load->database();
 
	    if(empty($this->session->userdata('logged'))) {
			  redirect(base_url('AdminLogin'));
	    }
		error_reporting(-1);
		ini_set('display_errors', 1);
		error_reporting(E_ERROR);
	}
	
	
	function dispatchupdate() { 
	    //echo "Timezone: " . date_default_timezone_get();
	    //echo date('d m Y H:i:s');
	    if(!checkPermission($this->session->userdata('permission'),'dispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
        //$data['expenses'] = $this->expenses;
        $data['expenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','title,type','title','asc');
	    $data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');
	    
        /*if($this->input->post('companyInfo')=='yes' && $this->input->post('companyInfo')!='')	{
            $companies = array();
            $company_data = $this->Comancontroler_model->get_data_by_name($company);
			if(!empty($company_data)) {
				$companyInfo = $this->Comancontroler_model->get_data_by_column('id',$company_data[0]['id'],'companies');
				if($companyInfo){ foreach($companyInfo as $com){ $companies = $com; } }
			}
			header('Content-Type: application/json');
			echo json_encode($companies);
			die();
        }*/
        
		$id = $this->uri->segment(4);
		$disInfo = $this->Comancontroler_model->get_data_by_id($id,'dispatch');
		$oldOtherChildInvoice=json_decode($disInfo[0]['dispatchMeta'])->otherChildInvoice;
		// print_r(json_decode($disInfo[0]['dispatchMeta'])->otherChildInvoice);exit;
		if(empty($disInfo)){ redirect(base_url('admin/dispatch'));  }
		$changeField = array();
		
		if($this->input->post('save'))	{
			$this->form_validation->set_rules('pudate', 'PU date','required|min_length[9]');
			$this->form_validation->set_rules('driver', 'driver','required');
			$this->form_validation->set_rules('pcity', 'pickup city','required|min_length[1]');
			$this->form_validation->set_rules('dcity', 'drop off city','required|min_length[1]');
			$this->form_validation->set_rules('company', 'company','required|min_length[1]'); 
			$this->form_validation->set_rules('dlocation', 'drop off location','required|min_length[1]'); 
			$this->form_validation->set_rules('plocation', 'pick up location','required|min_length[1]'); 
			$this->form_validation->set_rules('tracking', 'tracking','required|min_length[2]'); 
			
			$pudate1 = $this->input->post('pudate1');
			if(!is_array($pudate1)){ $pudate1 = array(); }
			$dodate1 = $this->input->post('dodate1');
			if(!is_array($dodate1)){ $dodate1 = array(); }
			/*if(count($pudate1) != count($dodate1)) {
				$this->form_validation->set_rules('pudate1', 'extra dispatch info','required'); 
				$this->form_validation->set_message('required','Extra dispatch pickup and drop off count must be equal.'); 
			}*/
			
			$pudate = $this->input->post('pudate');
			$driver = $this->input->post('driver');
			$invoiceInput = $this->input->post('invoice');
			
			$inv_first = '';
			$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate);
			//print_r($driver_trip);
			if(empty($driver_trip)) { $inv_last = '1'; }
			else { $inv_last = count($driver_trip) + 1; }
			if($inv_last < 10) { $inv_last = '0'.$inv_last; }
			
			$driver_info = $this->Comancontroler_model->get_data_by_id($driver,'drivers','dcode');
			if(!empty($driver_info)) {
				$inv_first = $driver_info[0]['dcode'];
			}
			$inv_middel = date('mdy',strtotime($pudate));
			$invoice = $inv_first.''.$inv_middel.'-'.$inv_last;
			if(stristr($invoiceInput,$inv_first.''.$inv_middel)) {
				$invoice = $invoiceInput;
			}
			elseif(strtotime($pudate) < strtotime('2024-04-25')){
			    $invoice = $invoiceInput;
			    $invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatch','id');
			    if(count($invoiceInfo) > 1 || (count($invoiceInfo) == 1 && $invoiceInfo[0]['id']!=$id)){
			        $this->form_validation->set_rules('invoiceooo', 'invoice','required'); 
				    $this->form_validation->set_message('required','This invoice number is already exist.'); 
			    }
			}
			elseif($invoice == '' || $inv_first == ''){
				$this->form_validation->set_rules('invoiceooo', 'invoice','required'); 
				$set_message = 'Invoice number must not empty.';
				if($inv_first == ''){ $set_message = 'Driver code is empty.'; }
				$this->form_validation->set_message('required',$set_message); 
			}
			else {
			    $invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
			}
			
			
			$bolCheck = $this->input->post('bol');
			$rcCheck = $this->input->post('rc');
			$gdCheck = $this->input->post('gd');
			$invoiceType = $this->input->post('invoiceType');
			$invoicePaid = $this->input->post('invoicePaid');
			$invoiceClose = $this->input->post('invoiceClose');
			$invoiceReady = $this->input->post('invoiceReady');
			$invoicedCheckbox = $this->input->post('invoiced');
			
			$childInvoiceInfo = $this->input->post('childInvoice');
			if(is_array($childInvoiceInfo)){
			    foreach($childInvoiceInfo as $ciID){
			        $ciInfo = $this->Comancontroler_model->get_data_by_column('invoice',$ciID,'dispatch','id,childInvoice');
			        if(empty($ciInfo)) {
			            $this->form_validation->set_rules('childInvoicesss', 'child invoice','required');
			            $this->form_validation->set_message('required','Invoice id "'.$ciID.'" is not exist.');
			        } elseif($ciInfo[0]['childInvoice'] != ''){
			            $this->form_validation->set_rules('childInvoicesss', 'child invoice','required');
			            $this->form_validation->set_message('required','Invoice id "'.$ciID.'" is already parent invoice.');
			        }
			    }
			    $childInvoice = implode(',',$childInvoiceInfo);
			} else {
			    $childInvoice = '';
			}
			
			$otherChildInvoiceInfo = $this->input->post('otherChildInvoice');
			if(is_array($otherChildInvoiceInfo)){
			    foreach($otherChildInvoiceInfo as $ciID){
			        $ciInfo = $this->Comancontroler_model->get_data_by_column('invoice',$ciID,'dispatchOutside','id,childInvoice');
			        if(empty($ciInfo)) {
			            $this->form_validation->set_rules('childInvoicesss', 'child invoice','required');
			            $this->form_validation->set_message('required','Outside invoice id "'.$ciID.'" is not exist.');
			        } 
			    }
			    $otherChildInvoice = implode(',',$otherChildInvoiceInfo);
			} else {
			    $otherChildInvoice = '';
			}
			
			if($bolCheck=='AK' && $rcCheck=='AK' && $gdCheck=='AK' && $invoiceType == ''){ 
			    $this->form_validation->set_rules('invoiceType', 'invoice type','required');
			    $this->form_validation->set_rules('invoiceReady', 'invoice ready checkbox','required');
			    $this->form_validation->set_rules('invoiceReadyDate', 'invoice ready date','required');
			}
			elseif($bolCheck=='AK' && $rcCheck=='AK' && $gdCheck=='AK' && $invoiceReady == '0'){ 
			    $this->form_validation->set_rules('invoiceReady11', 'invoice ready checkbox','required');
			    $this->form_validation->set_rules('invoiceReadyDate', 'invoice ready date','required');
			}
			if($invoicePaid == '1'){ 
			    $this->form_validation->set_rules('invoicePaidDate', 'invoice paid date','required');
			}
			if($invoiceClose == '1'){ 
			    $this->form_validation->set_rules('invoiceCloseDate', 'invoice close date','required');
			    if(empty($_FILES['gd_d']['name']) && $invoiceType != 'RTS'){
			        $this->form_validation->set_rules('gdfile', 'payment proof','required');
			    }
			}
			
			if($invoiceType != '' && $invoiceType != 'RTS'){
		        $this->form_validation->set_rules('invoiceNotes', 'invoice description','required');
		    }
			if($invoiceReady == '1'){ 
			    $this->form_validation->set_rules('invoiceReadyDate', 'invoice ready date','required');
			}
			if($invoicedCheckbox == '1'){
			    $this->form_validation->set_rules('invoiceDate', 'invoice date','required');
			    if($this->input->post('invoiceDate')=='TBD'){
			        $this->form_validation->set_rules('invoiceReadyDatesss', 'invoice date','required');
			        $this->form_validation->set_message('required','If you checked invoiced checkbox than invoice date is required.');
			    }
			}
			if($invoicePaid == '1' && $invoiceClose == '0' && $invoiceType != 'RTS'){
			    $this->form_validation->set_rules('invoiceCloseDate', 'invoice closed date','required');
				$this->form_validation->set_message('required','If you checked invoice paid checkbox than invoice closed should be checked aswell.');
				
				if(empty($_FILES['gd_d']['name'])){
					$this->form_validation->set_rules('gdfile', 'payment proof','required');
			    }
			}
			
			$check_dcity = $this->input->post('dcity');
			$check_pcity = $this->input->post('pcity');
			$check_plocation = $this->input->post('plocation');
			$check_dlocation = $this->input->post('dlocation');
			$check_paddress = $this->input->post('paddress');
			$check_daddress = $this->input->post('daddress');
				
			if($this->isAddressExist($pudate,$check_pcity,$check_plocation,$check_paddress)){
				$addr = $check_plocation.' '.$check_paddress.' '.$check_pcity;
				$this->form_validation->set_rules('pickupaddressss', 'pickup address','required');
				$this->form_validation->set_message('required','Pick up address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
			}
			
			if($this->isAddressExist($pudate,$check_dcity,$check_dlocation,$check_daddress)){
				$addr = $check_dlocation.' '.$check_daddress.' '.$check_dcity;
				$this->form_validation->set_rules('dropoffaddressss', 'drop off address','required');
				$this->form_validation->set_message('required','Drop off address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
			}
			
				
			if(count($pudate1) > 0) {
				$check_pcity1 = $this->input->post('pcity1');
				$check_plocation1 = $this->input->post('plocation1');
				$check_paddress1 = $this->input->post('paddress1');
				$check_pd_type1 = $this->input->post('pd_type1');
				for($i=0;$i<count($pudate1);$i++){
					if($pudate1[$i]!='' && $check_pd_type1[$i]=='pickup') {
						if($this->isAddressExist($pudate,$check_pcity1[$i],$check_plocation1[$i],$check_paddress1[$i])){
							$addr = $check_plocation1[$i].' '.$check_paddress1[$i].' '.$check_pcity1[$i];
							$this->form_validation->set_rules('dropoffaddressss'.$i, 'drop off address','required');
							$this->form_validation->set_message('required','Pickup address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
						}
					}
				}
			}
			if(count($dodate1) > 0) {
				$check_dcity1 = $this->input->post('dcity1');
				$check_dlocation1 = $this->input->post('dlocation1');
				$check_daddress1 = $this->input->post('daddress1');
				$check_pd_type2 = $this->input->post('pd_type2');
				for($i=0;$i<count($dodate1);$i++){
					if($dodate1[$i]!='' && $check_pd_type2[$i]=='dropoff') {
						if($this->isAddressExist($pudate,$check_dcity1[$i],$check_dlocation1[$i],$check_daddress1[$i])){
							$addr = $check_dlocation1[$i].' '.$check_daddress1[$i].' '.$check_dcity1[$i];
							$this->form_validation->set_rules('dropoffaddresss'.$i, 'drop off address','required');
							$this->form_validation->set_message('required','Drop off address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
						}
					}
				}
			}
		
		    $check_company = $this->input->post('company');
		    if(strtotime($pudate) >= strtotime('2024-09-07')){
    			$checkCompany = $this->Comancontroler_model->get_data_by_column('company',$check_company,'companies','id,address');
    			if($invoiceType == 'RTS' || $invoiceType == ''){
    			    
    			} elseif($checkCompany && $checkCompany[0]['address'] == ''){
    			    $this->form_validation->set_rules('checkcompany', 'company','required');
    				$this->form_validation->set_message('required','Company ('.$check_company.') address is blank. <a href="/admin/company/update/'.$checkCompany[0]['id'].'" target="_blank">Click here to update company address</a>.');
    			} elseif(empty($checkCompany)){
    			    $this->form_validation->set_rules('checkcompany', 'company','required');
    				$this->form_validation->set_message('required','Company ('.$check_company.') is not exist. <a href="/admin/company/add" target="_blank">Click here to add new company</a>.');
    			}
		    }
			
			
			$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
			if ($this->form_validation->run() == FALSE){}
            else
            {
                
                $replaceFileName = array(',',"'",'"','(',')','/','.','<');
                $driverId = $this->input->post('driver');
                $pudate = $this->input->post('pudate');
                
				/******************** upload files ****************/
                    $config['upload_path'] = 'assets/upload/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
                    $config['max_size']= '5000';
                    
                    /******** rename documents *************/
                    $driverCode = '';
                    $driversCodeResult = $this->Comancontroler_model->get_data_by_table('drivers','id,dcode');
                    if($driversCodeResult){
                        foreach($driversCodeResult as $driverLoop){
                            if($driverLoop['id'] == $driverId) { $driverCode = $driverLoop['dcode']; }
                        }
                    }
                    
                    $fileName1 = date('m-d-y',strtotime($pudate)).'-Trip-'.$this->input->post('trip');
                    $fileName1 = str_replace(' ','-',$fileName1);
                    $fileName1 = str_replace($replaceFileName,'',$fileName1);
                    $fileName2 = $driverCode.'-'.$this->input->post('tracking').'-'.$this->input->post('company');
                    $fileName2 = str_replace(' ','-',$fileName2);
                    $fileName2 = str_replace($replaceFileName,'',$fileName2);
				
				$bolFilesCount = count($_FILES['bol_d']['name']);
				if($bolFilesCount > 0) {
					$bolFiles = $_FILES['bol_d'];
					$config['file_name'] = $fileName1.'-BOL-'.$fileName2; //$_FILES['bol_d']['name'];  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
					for($i = 0; $i < $bolFilesCount; $i++){
						$_FILES['bol_d']['name']     = $bolFiles['name'][$i];
						$_FILES['bol_d']['type']     = $bolFiles['type'][$i];
						$_FILES['bol_d']['tmp_name'] = $bolFiles['tmp_name'][$i];
						$_FILES['bol_d']['error']     = $bolFiles['error'][$i];
						$_FILES['bol_d']['size']     = $bolFiles['size'][$i]; 
				
						if ($this->upload->do_upload('bol_d'))  { 
							$dataBol = $this->upload->data();
							$bol = $dataBol['file_name'];
							$addfile = array('did'=>$id,'type'=>'bol','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documents');
							$changeField[] = array('BOL File','bolfile','Upload',$bol);
						}
					}
				}
				
                /*if(!empty($_FILES['bol_d']['name'])){
                    $config['file_name'] = $fileName1.'-BOL-'.$fileName2; //$_FILES['bol_d']['name'];  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('bol_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$id,'type'=>'bol','fileurl'=>$bol);
						$this->Comancontroler_model->add_data_in_table($addfile,'documents');
                    }
                } */
                
                $rcFilesCount = count($_FILES['rc_d']['name']);
				if($rcFilesCount > 0) {  
					$rcFiles = $_FILES['rc_d'];
					$config['file_name'] = $fileName1.'-RC-'.$fileName2; //$_FILES['rc_d']['name'];  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                   
					for($i = 0; $i < $rcFilesCount; $i++){
						$_FILES['rc_d']['name']     = $rcFiles['name'][$i];
						$_FILES['rc_d']['type']     = $rcFiles['type'][$i];
						$_FILES['rc_d']['tmp_name'] = $rcFiles['tmp_name'][$i];
						$_FILES['rc_d']['error']     = $rcFiles['error'][$i];
						$_FILES['rc_d']['size']     = $rcFiles['size'][$i]; 
				
						if ($this->upload->do_upload('rc_d'))  { 
							$dataRc = $this->upload->data(); 
							$bol = $dataRc['file_name'];
							$addfile = array('did'=>$id,'type'=>'rc','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documents');
							$changeField[] = array('RC File','rcfile','Upload',$bol);
						}
					}
				}
				
				/*if(!empty($_FILES['rc_d']['name'])){
                    $config['file_name'] = $fileName1.'-RC-'.$fileName2; //$_FILES['rc_d']['name']; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('rc_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$id,'type'=>'rc','fileurl'=>$bol);
						$this->Comancontroler_model->add_data_in_table($addfile,'documents');
                    }
                } */
                
                
				if(!empty($_FILES['gd_d']['name'])){
                    $config['file_name'] = $fileName1.'-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('gd_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$id,'type'=>'gd','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
						$this->Comancontroler_model->add_data_in_table($addfile,'documents');
						$changeField[] = array('Payment proof file','gdfile','Upload',$bol);
                    }
                } 
                
                $paInvoiceCount = count($_FILES['paInvoice']['name']);
				if($paInvoiceCount > 0) {  
					$paInvoiceFiles = $_FILES['paInvoice'];
					$config['file_name'] = $fileName1.'-Customer-Inv-'.$fileName2; //$_FILES['paInvoice']['name'];  
					$config['upload_path'] = 'assets/paInvoice/';
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                   
					for($i = 0; $i < $paInvoiceCount; $i++){
						$_FILES['paInvoice']['name']     = $paInvoiceFiles['name'][$i];
						$_FILES['paInvoice']['type']     = $paInvoiceFiles['type'][$i];
						$_FILES['paInvoice']['tmp_name'] = $paInvoiceFiles['tmp_name'][$i];
						$_FILES['paInvoice']['error']     = $paInvoiceFiles['error'][$i];
						$_FILES['paInvoice']['size']     = $paInvoiceFiles['size'][$i]; 
				
						if ($this->upload->do_upload('paInvoice'))  { 
							$dataInv = $this->upload->data(); 
							$bol = $dataInv['file_name'];
							$addfile = array('did'=>$id,'type'=>'paInvoice','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documents');
							$changeField[] = array('Customer Invoice File','paInvoice','Upload',$bol);
						}
					}
				}
                
				/******************* check companies ****************/
				$check_company = $this->input->post('company');
				$company = $this->check_company($check_company);
				$companyInfo = $this->Comancontroler_model->get_data_by_id($company,'companies','paymenTerms,payoutRate,dayToPay');
				
				$check_dcity = $this->input->post('dcity');
				$dcity = $this->check_city($check_dcity);
				
				$check_pcity = $this->input->post('pcity');
				$pcity = $this->check_city($check_pcity);
				
				$check_plocation = $this->input->post('plocation');
				$plocation = $this->check_location($check_plocation);
				
				$check_dlocation = $this->input->post('dlocation');
				$dlocation = $this->check_location($check_dlocation);
				
				$dcode = $this->input->post('dcode');
				$dcodeVal = implode('~-~',$dcode);
				$pcode = $this->input->post('pcode');
				$pcodeVal = implode('~-~',$pcode);
				
				$week = date('M',strtotime($pudate)).' W';
				$day = date('d',strtotime($pudate));
				if($day < 9) { $w = '1'; }
				elseif($day < 16){ $w = '2'; }
				elseif($day < 24){ $w = '3'; }
				else { $w = '4'; }
				$week .= $w; 
				//$week = $this->input->post('dWeek');
				
				$invoiceType = $this->input->post('invoiceType');
				$parate = $this->input->post('parate');
				
				/*if(!is_numeric($parate)) { $parate = 0; }
				$payoutRate = $companyInfo[0]['payoutRate'];
                if(!is_numeric($payoutRate)) { $payoutRate = 0; }
				$payoutAmount = $payoutRate * $parate;
				$payoutAmount = round($payoutAmount,2);*/
				
				$payoutAmount  = $this->input->post('payoutAmount');
				
				if($invoiceType == '' || $parate < 1 || strtotime($pudate) < strtotime('2024-09-01')) {  }
				//if($invoiceType == '' || $parate < 1) {  }
				elseif($invoiceType == 'RTS') { $payoutAmount = $parate - ($parate * 0.0115); }
				elseif($invoiceType == 'Direct Bill') { $payoutAmount = $parate * 1; }
				elseif($invoiceType == 'Quick Pay') { $payoutAmount = $parate - ($parate * 0.02); }
				if($payoutAmount > 0){ $payoutAmount = round($payoutAmount,2); }
				
				
				$expectPayDate = $this->input->post('expectPayDate');
				$invoiceDate = $this->input->post('invoiceDate'); 
				//if($invoiceType == '') { $invoiceType = $companyInfo[0]['paymenTerms']; }
				//$invoiceType = $companyInfo[0]['paymenTerms'];
				
				$dayToPay = $companyInfo[0]['dayToPay'];
				if($dayToPay < 2) {$pDay = '+ '.$dayToPay.' day'; }
				elseif($dayToPay > 1) {$pDay = '+ '.$dayToPay.' days'; }
				else { $pDay = "+ 0 day"; }
				
				$dispatchMeta = array('expense'=>array());
				$expenseName = $this->input->post('expenseName');
				$expensePrice = $this->input->post('expensePrice');
				if(is_array($expenseName)) {
				    for($i=0;$i<count($expenseName);$i++){
				        $dispatchMeta['expense'][] = array($expenseName[$i],$expensePrice[$i]);
				    }
				}
				$dispatchMeta['invoiced'] = $this->input->post('invoiced');
				$dispatchMeta['invoicePaid'] = $this->input->post('invoicePaid');
				$dispatchMeta['invoiceClose'] = $this->input->post('invoiceClose');
				$dispatchMeta['invoiceReady'] = $this->input->post('invoiceReady');
				$dispatchMeta['invoiceCloseDate'] = $this->input->post('invoiceCloseDate');
				$dispatchMeta['invoicePaidDate'] = $this->input->post('invoicePaidDate');
				$dispatchMeta['invoiceReadyDate'] = $this->input->post('invoiceReadyDate');
				$dispatchMeta['pickup'] = $this->input->post('pickup');
				$dispatchMeta['dropoff'] = $this->input->post('dropoff');
				$dispatchMeta['partialAmount'] = $this->input->post('partialAmount');
				$dispatchMeta['appointmentTypeP'] = $this->input->post('appointmentTypeP');
				$dispatchMeta['appointmentTypeD'] = $this->input->post('appointmentTypeD');

				$payableAmt = $payoutAmount - $dispatchMeta['partialAmount'];
				if(!is_numeric($payableAmt)) { $payableAmt = 0; }
				
				$dispatchMeta['otherChildInvoice'] = $otherChildInvoice;
				
				$bol = $this->input->post('bol');
				$rc = $this->input->post('rc');
				$gd = $this->input->post('gd');
				
				$status = $this->input->post('status');
				
				// if($bol=='AK' && $rc=='AK' && $gd=='AK' && strtotime($pudate) > strtotime('2024-05-18')){ 
				//     if($invoiceType == 'RTS' && $this->input->post('invoiceCloseOld')!='1'){
				//         /*if($dispatchMeta['invoiceClose']=='1'){  $status = 'Closed '.date('m/d/Y'); }
				//         elseif($dispatchMeta['invoicePaid']=='1'){  $status = 'RTS Paid '.date('m/d/Y'); }
				//         elseif($dispatchMeta['invoiced']=='1'){  $status = 'RTS Invoiced '.date('m/d/Y'); }
				//         elseif($dispatchMeta['invoiceReady']=='1'){  $status = 'Ready to submit RTS '.date('m/d/Y'); }*/
				//     }
				//     if($invoiceType == 'Direct Bill' && $this->input->post('invoiceCloseOld')!='1'){
				//         /*if($dispatchMeta['invoiceClose']=='1'){  $status = 'Closed '.date('m/d/Y'); }
				//         elseif($dispatchMeta['invoicePaid']=='1'){  $status = 'DB Paid '.date('m/d/Y'); }
				//         elseif($dispatchMeta['invoiced']=='1'){  $status = 'DB Invoiced '.date('m/d/Y'); }
				//         elseif($dispatchMeta['invoiceReady']=='1'){  $status = 'Ready to submit DB '.date('m/d/Y'); }*/
				//     }
				//     if($invoiceType == 'Quick Pay'){
				//         if($dispatchMeta['invoiceReady']=='1' && $dispatchMeta['invoiceReadyOld']!='1'){  /*$status = 'Ready to submit QP '.date('m/d/Y');*/ }
				//         $dispatchMeta['invoiceClose'] = $dispatchMeta['invoicePaid'] = $dispatchMeta['invoiced'] = '0';
				//     }
				// }
				
				$dispatchMetaJson = json_encode($dispatchMeta);
				
				if($this->input->post('delivered') == 'yes'){
					$driver_status = 'Shipment Delivered';
				}else{
					$driver_status = $this->input->post('driver_status');
				}
				$insert_data = array(
				    'driver'=>$driverId,
				    'vehicle'=>$this->input->post('vehicle'),
				    'pudate'=>$pudate,
				    'dodate'=>$this->input->post('dodate'),
				    'trip'=>$this->input->post('trip'),
				    'pcity'=>$pcity,
				    'dcity'=>$dcity,
				    'rate'=>$this->input->post('rate'),
				    'parate'=>$this->input->post('parate'),
				    'company'=>$company,
					'shipping_contact' => $this->input->post('shipping_contact'),
				    'dlocation'=>$dlocation,
				    'plocation'=>$plocation,
				    'dcode'=>$dcodeVal,
				    'pcode'=>$pcodeVal,
				    'trailer'=>$this->input->post('trailer'),
				    'tracking'=>$this->input->post('tracking'),
				    'paddress'=>$this->input->post('paddress'),
				    'daddress'=>$this->input->post('daddress'),
				    'paddressid'=>$this->input->post('paddressid'),
				    'daddressid'=>$this->input->post('daddressid'),
				    'invoice'=>$invoice,
				    'childInvoice'=>$childInvoice,
				    'invoiceType'=>$invoiceType,
				    'payoutAmount'=>$payoutAmount,
				    'payableAmt'=>$payableAmt,
				    'dWeek'=>$week,
				    'bol'=>$this->input->post('bol'),
				    'rc'=>$this->input->post('rc'),
				    'gd'=>$this->input->post('gd'),
				    'delivered'=>$this->input->post('delivered'),
				    'ptime'=>$this->input->post('ptime'),
				    'dtime'=>$this->input->post('dtime'),
				    'notes'=>$this->input->post('notes'),
				    'pnotes'=>$this->input->post('pnotes'),
				    'dnotes'=>$this->input->post('dnotes'),
				    'invoiceNotes'=>$this->input->post('invoiceNotes'),
				    //'detention'=>$this->input->post('detention'),
				    //'detention_check'=>$this->input->post('detention_check'),
				    //'dassist'=>$this->input->post('dassist'),
				    //'dassist_check'=>$this->input->post('dassist_check'),
				    'dispatchMeta'=>$dispatchMetaJson,
				    'driver_status'=>$driver_status,
				    'lockDispatch'=>$this->input->post('lockDispatch'),
				    'status'=>$status
				);
				if($invoiceDate != 'TBD' && $invoiceDate != ''){
				    $insert_data['invoiceDate'] = $invoiceDate; 
				    if($invoiceType == 'RTS'){ $iDays = "+ 3 days"; }
				    elseif($invoiceType == 'Direct Bill'){ $iDays = "+ 30 days"; }
				    elseif($invoiceType == 'Quick Pay'){ $iDays = "+ 7 days"; }
				    else { $iDays = "+1 month"; }
				    $expectPayDate = date('Y-m-d',strtotime($iDays,strtotime($invoiceDate)));
				    $insert_data['expectPayDate'] = $expectPayDate;
				} else {
				    $insert_data['invoiceDate'] = $insert_data['expectPayDate'] = '0000-00-00';
				}
			
				$res = $this->Comancontroler_model->update_table_by_id($id,'dispatch',$insert_data); 
				if($res){
				    /************* update history **************/
					
					if($disInfo){
						foreach($disInfo as $di){
							if($di['childInvoice'] != $insert_data['childInvoice']) { 
						        $changeField[] = array('Child Invoice','childInvoice',$di['childInvoice'],$insert_data['childInvoice']); 
						        if($insert_data['childInvoice'] == ''){ $ciNewArray = array(); }
						        else { $ciNewArray = explode(',',$insert_data['childInvoice']); }
						        
							    if($di['childInvoice'] != '') {
							        $ciOldArray = explode(',',$di['childInvoice']);
							        foreach($ciOldArray as $val){
							            if (!in_array($val, $ciNewArray)){
							              
											$fullStatus = $insert_data['status'];
											$firstPart = explode(' - Linked to', $fullStatus)[0]; 
											// echo ($firstPart);exit;

											$updateInvArr = array(
												'parentInvoice'=>'',
												'status' => $firstPart
											);

							                $this->Comancontroler_model->update_table_by_column('invoice',$val,'dispatch',$updateInvArr);
							            }
							        }
							    }
							    if($ciNewArray){
							        foreach($ciNewArray as $val){
							            $updateInvArr = array('parentInvoice'=>$invoice);
							            $this->Comancontroler_model->update_table_by_column('invoice',$val,'dispatch',$updateInvArr);
							        }
							    }
							}
							//// update data in sub invoice 
							if($insert_data['childInvoice'] != '') {
								$ciNewArray = explode(',',$insert_data['childInvoice']);
								foreach($ciNewArray as $subInv){
									if(trim($subInv) == ''){ continue; }
									$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice',$subInv,'dispatch','id,dispatchMeta');
									if(empty($getSubDispatch)){ continue; }
									$subInvArr = array();
									if($getSubDispatch[0]['dispatchMeta'] == '') {
									    $currentDiMeta = array('expense'=>array(),'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoicePaidDate'=>'','invoiceCloseDate'=>'');
									} else {
										$currentDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'],true);
									}
									$currentDiMeta['invoiceReadyDate'] = $dispatchMeta['invoiceReadyDate'];
									$currentDiMeta['invoicePaidDate'] = $dispatchMeta['invoicePaidDate'];
									$currentDiMeta['invoiceCloseDate'] = $dispatchMeta['invoiceCloseDate'];
									$currentDiMeta['invoiceReady'] = $dispatchMeta['invoiceReady'];
									$currentDiMeta['invoicePaid'] = $dispatchMeta['invoicePaid'];
									$currentDiMeta['invoiceClose'] = $dispatchMeta['invoiceClose'];
									$currentDiMeta['invoiced'] = $dispatchMeta['invoiced'];
									
									$dispatchMetaJson = json_encode($currentDiMeta);
									$subInvArr['dispatchMeta'] = $dispatchMetaJson;
									
									if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){
										$subInvArr['invoiceDate'] = $insert_data['invoiceDate'];
										$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
									}
									$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
									$subInvArr['invoiceType'] = $insert_data['invoiceType'];
									$subInvArr['bol'] =$insert_data['bol'];
									$subInvArr['rc'] = $insert_data['rc'];
									$subInvArr['gd'] = $insert_data['gd'];
									$subInvArr['delivered'] = $insert_data['delivered'];
									$subInvArr['shipping_contact'] = $insert_data['shipping_contact'];
									$subInvArr['driver_status'] = $insert_data['driver_status'];

									$fullStatus = $insert_data['status'];
									$firstPart = explode(' - Linked to', $fullStatus)[0];  
									$subInvArr['status'] = $firstPart . ' - Linked to ' . $insert_data['invoice'];

									// $subInvArr['status'] = $insert_data['status'].' - Linked to '.$insert_data['invoice'];
									
									if($getSubDispatch[0]['id'] > 0) {
										$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'],'dispatch',$subInvArr);
									}
								}
							}
							
							// update data in pa logistics dispatch  
							/*if(is_array($otherChildInvoiceInfo)){
			                    foreach($otherChildInvoiceInfo as $ciID){
			                        
			                    }
							}*/
							// Decode dispatchMeta first (if not already)
							
							$newDispatchMeta = json_decode($insert_data['dispatchMeta'], true);
							$ociNewArray = array(); 
							if (!empty($newDispatchMeta['otherChildInvoice'])) {
								$ociNewArray = explode(',', $newDispatchMeta['otherChildInvoice']);
							}

							if (!empty($ociNewArray)) {
								foreach ($ociNewArray as $subInv) {
									if (trim($subInv) == '') { continue; }
									$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice', $subInv, 'dispatchOutside', 'id,dispatchMeta,bol,rc,gd');
									if (empty($getSubDispatch)) { continue; }
									$subInvArr = array();
									if ($getSubDispatch[0]['dispatchMeta'] == '') {
										$currentDiMeta = array(
											'expense' => array(),
											'invoiced' => '0',
											'invoicePaid' => '0',
											'invoiceClose' => '0',
											'invoiceReady' => '0',
											'invoiceReadyDate' => '',
											'invoicePaidDate' => '',
											'invoiceCloseDate' => ''
										);
									} else {
										$currentDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'], true);
									}
							
									$currentDiMeta['invoiceReadyDate'] = $dispatchMeta['invoiceReadyDate'];
									$currentDiMeta['invoicePaidDate'] = $dispatchMeta['invoicePaidDate'];
									$currentDiMeta['invoiceCloseDate'] = $dispatchMeta['invoiceCloseDate'];
									$currentDiMeta['invoiceReady'] = $dispatchMeta['invoiceReady'];
									$currentDiMeta['invoicePaid'] = $dispatchMeta['invoicePaid'];
									$currentDiMeta['invoiceClose'] = $dispatchMeta['invoiceClose'];
									$currentDiMeta['invoiced'] = $dispatchMeta['invoiced'];
							
									$currentDiMeta['custInvDate'] = $dispatchMeta['custInvDate'];
									$currentDiMeta['carrierInvoiceCheck'] = $dispatchMeta['carrierInvoiceCheck'];
									$currentDiMeta['custDueDate'] = $dispatchMeta['custDueDate'];

									$dispatchMetaJson = json_encode($currentDiMeta);
									$subInvArr['dispatchMeta'] = $dispatchMetaJson;
							
									if (array_key_exists("invoiceDate", $insert_data) && trim($insert_data['invoiceDate']) != '') {
										$subInvArr['invoiceDate'] = $insert_data['invoiceDate'];
										$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
									}
							
									$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
									$subInvArr['invoiceType'] = $insert_data['invoiceType'];
									$fullStatus = $insert_data['status'];
									$firstPart = explode(' - Linked to', $fullStatus)[0]; 
									// $subInvArr['status'] = $firstPart; 
									// print_r($firstPart);exit;
									$subInvArr['status'] = $firstPart . ' - Linked to ' . $insert_data['invoice'];
									$subInvArr['otherParentInvoice'] = $invoice;
									$getPaentCheckboxes = $this->Comancontroler_model->get_data_by_column('invoice', $invoice, 'dispatch', 'bol,rc,gd,delivered,shipping_contact,driver_status');

									$subInvArr['bol'] =$getPaentCheckboxes[0]['bol'];
									$subInvArr['rc'] = $getPaentCheckboxes[0]['rc'];
									$subInvArr['gd'] = $getPaentCheckboxes[0]['gd'];
									$subInvArr['delivered'] = $getPaentCheckboxes[0]['delivered'];
									$subInvArr['shipping_contact'] = $insert_data['shipping_contact'];
									$subInvArr['driver_status'] = $insert_data['driver_status'];

									// print_r($getPaentCheckboxes[0]['delivered']);exit;

									if ($getSubDispatch[0]['id'] > 0) {
										// echo $getSubDispatch[0]['id'];exit;
										$sql=$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'], 'dispatchOutside', $subInvArr);
										// echo $sql;exit;
									}
								}
							}
							
							$ociOldArray = array();
							if (!empty($oldOtherChildInvoice)) {
								$ociOldArray = explode(',', $oldOtherChildInvoice);
							}

							$removedOtherChildInvoices = array_diff($ociOldArray, $ociNewArray);
							if (!empty($removedOtherChildInvoices)) {
								foreach ($removedOtherChildInvoices as $removedInv) {
									$removedInv = trim($removedInv);
									if ($removedInv == '') continue;
									$fullStatus = $insert_data['status'];
									$firstPart = explode(' - Linked to', $fullStatus)[0]; 

									$updateData = array(
										'status' => $firstPart,
										'otherParentInvoice' => null);
									$this->Comancontroler_model->update_table_by_column('invoice', $removedInv, 'dispatchOutside', $updateData);
								}
							}


							if($di['driver'] != $insert_data['driver']) { $changeField[] = array('Driver','driver',$di['driver'],$insert_data['driver']); }
							if($di['vehicle'] != $insert_data['vehicle']) { $changeField[] = array('Vehicle','vehicle',$di['vehicle'],$insert_data['vehicle']); }
							if($di['trip'] != $insert_data['trip']) { $changeField[] = array('Trip','trip',$di['trip'],$insert_data['trip']); }
							if($di['pudate'] != $insert_data['pudate']) { $changeField[] = array('Pickup Date','pudate',$di['pudate'],$insert_data['pudate']); }
							if($di['ptime'] != $insert_data['ptime']) { $changeField[] = array('Pickup Time','ptime',$di['ptime'],$insert_data['ptime']); }
							if($di['plocation'] != $insert_data['plocation']) { $changeField[] = array('Pickup Location','plocation',$di['plocation'],$insert_data['plocation']); }
							if($di['pcity'] != $insert_data['pcity']) { $changeField[] = array('Pickup City','pcity',$di['pcity'],$insert_data['pcity']); }
							if($di['paddress'] != $insert_data['paddress']) { $changeField[] = array('Pickup Address','paddress',$di['paddress'],$insert_data['paddress']); }
							if($di['pcode'] != $insert_data['pcode']) { $changeField[] = array('Pickup','pcode',$di['pcode'],$insert_data['pcode']); }
							if($di['pnotes'] != $insert_data['pnotes']) { $changeField[] = array('Pickup Notes','pnotes',$di['pnotes'],$insert_data['pnotes']); }
							if($di['dodate'] != $insert_data['dodate'] && $insert_data['dodate'] != '') { $changeField[] = array('Drop Off Date','dodate',$di['dodate'],$insert_data['dodate']); }
							if($di['dtime'] != $insert_data['dtime']) { $changeField[] = array('Drop Off Time','dtime',$di['dtime'],$insert_data['dtime']); }
							if($di['dlocation'] != $insert_data['dlocation']) { $changeField[] = array('Drop Off Location','dlocation',$di['dlocation'],$insert_data['dlocation']); }
							if($di['dcity'] != $insert_data['dcity']) { $changeField[] = array('Drop Off City','dcity',$di['dcity'],$insert_data['dcity']); }
							if($di['daddress'] != $insert_data['daddress']) { $changeField[] = array('Drop Off Address','daddress',$di['daddress'],$insert_data['daddress']); }
							if($di['dcode'] != $insert_data['dcode']) { $changeField[] = array('Drop Off','dcode',$di['dcode'],$insert_data['dcode']); }
							if($di['dnotes'] != $insert_data['dnotes']) { $changeField[] = array('Drop Off Notes','dnotes',$di['dnotes'],$insert_data['dnotes']); }
							if($di['company'] != $insert_data['company']) { $changeField[] = array('Company','company',$di['company'],$insert_data['company']); }
							if($di['shipping_contact'] != $insert_data['shipping_contact']) { $changeField[] = array('Shipping Contact','shipping_contact',$di['shipping_contact'],$insert_data['shipping_contact']); }
							if($di['rate'] != $insert_data['rate']) { $changeField[] = array('Rate','rate',$di['rate'],$insert_data['rate']); }
							if($di['parate'] != $insert_data['parate']) { $changeField[] = array('PA Rate','parate',$di['parate'],$insert_data['parate']); }
							if($di['payoutAmount'] != $insert_data['payoutAmount']) { $changeField[] = array('Payout Amount','payoutAmount',$di['payoutAmount'],$insert_data['payoutAmount']); }
							if($di['trailer'] != $insert_data['trailer']) { $changeField[] = array('Trailer','trailer',$di['trailer'],$insert_data['trailer']); }
							if($di['tracking'] != $insert_data['tracking']) { $changeField[] = array('Tracking','tracking',$di['tracking'],$insert_data['tracking']); }
							if($di['invoice'] != $insert_data['invoice']) { $changeField[] = array('Invoice','invoice',$di['invoice'],$insert_data['invoice']); }
							if($di['invoiceType'] != $insert_data['invoiceType']) { $changeField[] = array('Invoice Type','invoiceType',$di['invoiceType'],$insert_data['invoiceType']); }
							if($di['payoutAmount'] != $insert_data['payoutAmount']) { $changeField[] = array('Payout Amount','payoutAmount',$di['payoutAmount'],$insert_data['payoutAmount']); }
							if($di['dWeek'] != $insert_data['dWeek']) { $changeField[] = array('Week','dWeek',$di['dWeek'],$insert_data['dWeek']); }
							if($di['bol'] != $insert_data['bol']) { $changeField[] = array('BOL','bol',$di['bol'],$insert_data['bol']); }
							if($di['rc'] != $insert_data['rc']) { $changeField[] = array('RC','rc',$di['rc'],$insert_data['rc']); }
							if($di['gd'] != $insert_data['gd']) { $changeField[] = array('$','gd',$di['gd'],$insert_data['gd']); }
							if($di['delivered'] != $insert_data['delivered']) { $changeField[] = array('Delivered','delivered',$di['delivered'],$insert_data['delivered']); }
							if($di['notes'] != $insert_data['notes']) { $changeField[] = array('Notes','notes',$di['notes'],$insert_data['notes']); }
							if($di['driver_status'] != $insert_data['driver_status']) { $changeField[] = array('Driver Status','driver_status',$di['driver_status'],$insert_data['driver_status']); }
							if($di['lockDispatch'] != $insert_data['lockDispatch']) { $changeField[] = array('Lock Dispatch','lockDispatch',$di['lockDispatch'],$insert_data['lockDispatch']); }
							if($di['status'] != $insert_data['status']) { $changeField[] = array('Status','status',$di['status'],$insert_data['status']); }
							if($di['dispatchMeta'] != ''){
								$diMeta = json_decode($di['dispatchMeta'],true);
								if($diMeta['invoiced'] != $dispatchMeta['invoiced']) { $changeField[] = array('Invoiced','invoiced',$diMeta['invoiced'],$dispatchMeta['invoiced']); }
								if($diMeta['invoicePaid'] != $dispatchMeta['invoicePaid']) { $changeField[] = array('Invoice Paid','invoicePaid',$diMeta['invoicePaid'],$dispatchMeta['invoicePaid']); }
								if($diMeta['invoicePaidDate'] != $dispatchMeta['invoicePaidDate']) { $changeField[] = array('Invoice Paid Date','invoicePaidDate',$diMeta['invoicePaidDate'],$dispatchMeta['invoicePaidDate']); }
								if($diMeta['invoiceClose'] != $dispatchMeta['invoiceClose']) { $changeField[] = array('Invoice Closed','invoiceClose',$diMeta['invoiceClose'],$dispatchMeta['invoiceClose']); }
								if($diMeta['invoiceCloseDate'] != $dispatchMeta['invoiceCloseDate']) { $changeField[] = array('Invoice Closed Date','invoiceCloseDate',$diMeta['invoiceCloseDate'],$dispatchMeta['invoiceCloseDate']); }
								if($diMeta['invoiceReady'] != $dispatchMeta['invoiceReady']) { $changeField[] = array('Ready to submit','invoiceReady',$diMeta['invoiceReady'],$dispatchMeta['invoiceReady']); }
								if($diMeta['invoiceReadyDate'] != $dispatchMeta['invoiceReadyDate']) { $changeField[] = array('Ready To Submit Date','invoiceReadyDate',$diMeta['invoiceReadyDate'],$dispatchMeta['invoiceReadyDate']); }
							}
							if($invoiceDate != 'TBD' && $invoiceDate != ''){
								if($di['invoiceDate'] != $insert_data['invoiceDate']) { 
									$changeField[] = array('Invoice Date','invoiceDate',$di['invoiceDate'],$insert_data['invoiceDate']);
									$changeField[] = array('Expect Pay Date','expectPayDate',$di['expectPayDate'],$insert_data['expectPayDate']);
								}
							}
						}
					}
					
					
					
				    /*********** insert data in extra dispatch table *****/ 
				    if(count($pudate1) > 0 || count($dodate1) > 0) {
				        $pcode1 = $this->input->post('pcode1');
    					$dcode1 = $this->input->post('dcode1');
    					$check_dcity1 = $this->input->post('dcity1');
    					$check_pcity1 = $this->input->post('pcity1');
    					$check_plocation1 = $this->input->post('plocation1');
    					$check_dlocation1 = $this->input->post('dlocation1');
    					$ptime1 = $this->input->post('ptime1');
    					$dtime1 = $this->input->post('dtime1');
    					$paddress1 = $this->input->post('paddress1');
    					$daddress1 = $this->input->post('daddress1');
    					$paddressid1 = $this->input->post('paddressid1');
    					$daddressid1 = $this->input->post('daddressid1');
    					
    					$pickup1 = $this->input->post('pickup1');
    					$dropoff1 = $this->input->post('dropoff1');
    					
    					$pnotes1 = $this->input->post('pnotes1');
    					$dnotes1 = $this->input->post('dnotes1');
    					$pcodename = $this->input->post('pcodename');
    					$dcodename = $this->input->post('dcodename');
    					$extrdispatchid1 = $this->input->post('extrdispatchid1');
    					$extrdispatchid2 = $this->input->post('extrdispatchid2');
    					$pd_type1 = $this->input->post('pd_type1');
    					$pd_type2 = $this->input->post('pd_type2');
    					
						$appointmentTypeP1 = $this->input->post('appointmentTypeP1');
    					$appointmentTypeD1 = $this->input->post('appointmentTypeD1');

    					for($i=0;$i<count($pudate1);$i++){
				          if($pudate1[$i]!='' && $pd_type1[$i]=='pickup') {
				            $pcodeVal1 = implode('~-~',$pcode1[$pcodename[$i]]);  
				            $pcity1 = $this->check_city($check_pcity1[$i]);
				            $plocation1 = $this->check_location($check_plocation1[$i]); 
				            
							$pd_meta = array();
							$pd_meta['appointmentType'] = $appointmentTypeP1[$i];
				            $pdMetaJson = json_encode($pd_meta);

    					    $extraData = array(
    					    'dispatchid'=>$id,
    					    'pd_title'=>$pickup1[$i],
    					    'pd_location'=>$plocation1,
    					    'pd_time'=>$ptime1[$i], 
    					    'pd_address'=>$paddress1[$i], 
    					    'pd_addressid'=>$paddressid1[$i], 
    					    'pd_date'=>$pudate1[$i], 
    					    'pd_notes'=>$pnotes1[$i], 
    					    'pd_code'=>$pcodeVal1,
    					    'pd_city'=>$pcity1,
    					    'pd_order'=>$i,
							'pd_meta'=>$pdMetaJson,
							'pd_type'=>'pickup'
    					    );
    					    if($extrdispatchid1[$i] > 0) {
    					        $this->Comancontroler_model->update_table_by_id($extrdispatchid1[$i],'dispatchExtraInfo',$extraData); 
    					    } else {
    					        $this->Comancontroler_model->add_data_in_table($extraData,'dispatchExtraInfo');
    					    }
				          }
    					}
						
						for($i=0;$i<count($dodate1);$i++){
				          if($dodate1[$i]!='' && $pd_type2[$i]=='dropoff') { 
				            $dcodeVal1 = implode('~-~',$dcode1[$dcodename[$i]]);
				            $dcity1 = $this->check_city($check_dcity1[$i]);  
				            $dlocation1 = $this->check_location($check_dlocation1[$i]);
				           
							$pd_meta = array();
							$pd_meta['appointmentType'] = $appointmentTypeD1[$i];
							$pdMetaJson = json_encode($pd_meta);

    					    $extraData = array(
    					    'dispatchid'=>$id,
    					    'pd_title'=>$dropoff1[$i],
    					    'pd_location'=>$dlocation1, 
    					    'pd_time'=>$dtime1[$i], 
    					    'pd_address'=>$daddress1[$i], 
    					    'pd_addressid'=>$daddressid1[$i], 
    					    'pd_date'=>$dodate1[$i], 
    					    'pd_notes'=>$dnotes1[$i],
    					    'pd_code'=>$dcodeVal1, 
    					    'pd_city'=>$dcity1,
    					    'pd_order'=>$i,
							'pd_meta'=>$pdMetaJson,
							'pd_type'=>'dropoff'
    					    );
    					    if($extrdispatchid2[$i] > 0) {
    					        $this->Comancontroler_model->update_table_by_id($extrdispatchid2[$i],'dispatchExtraInfo',$extraData); 
    					    } else {
    					        $this->Comancontroler_model->add_data_in_table($extraData,'dispatchExtraInfo');
    					    }
				          }
    					}
				    }
				    
				    $userid = $this->session->userdata('logged');
				    //print_r($userid);
				    
				    if($changeField) {
				        $changeFieldJson = json_encode($changeField);
				        $dispatchLog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
				        $this->Comancontroler_model->add_data_in_table($dispatchLog,'dispatchLog'); 
				    }
				    
					$this->session->set_flashdata('item', 'Dispatch updated successfully.');
					$url = base_url('admin/dispatch/update/'.$id);
					if(isset($_GET['invoice'])){ $url .= '?invoice'; }
					//if($id == '15'){
					    //echo '<pre>';print_r($dispatchMeta);print_r($changeField);print_r($insert_data);echo '</pre>';
					//} else {
                        redirect($url);
					//}
				}
 			   
			}
	    }
     
        $data['extraDispatch'] = $this->Comancontroler_model->getExtraDispatchInfo($id);
        $data['dispatch'] = $this->Comancontroler_model->get_data_by_id($id,'dispatch');
        $data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers','*','','desc','All');
	    $data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
	    $data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
	    $data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
	    $data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
	    $data['documents'] = array();
	    $documents = $this->Comancontroler_model->get_document_by_dispach($id);
	    if($documents){
	        foreach($documents as $doc){
	            $doc['parent'] = 'no'; 
	            $data['documents'][] = $doc;
	        }
	    }
	    if($data['dispatch'][0]['parentInvoice'] != ''){
	        $parentID = $this->Comancontroler_model->get_data_by_column('invoice',$data['dispatch'][0]['parentInvoice'],'dispatch','id');
	        if($parentID){
	            if($parentID[0]['id'] > 0) {
	                $documentsParent = $this->Comancontroler_model->get_document_by_dispach($parentID[0]['id']);
	                if($documentsParent){
            	        foreach($documentsParent as $doc){
            	            $doc['parent'] = 'yes'; 
            	            $data['documents'][] = $doc;
            	        }
            	    }
	            }
	        }
	    }

		// print_r($data['dispatch'][0]['otherParentInvoice']);exit;
		// $dispatchMetaDecode = json_decode($data['dispatch'][0]['dispatchMeta'], true);
		// $otherParentInvoice = $dispatchMetaDecode['otherParentInvoice'];
		// // echo $otherParentInvoice; 
		if($data['dispatch'][0]['otherParentInvoice'] != ''){
	        $parentID = $this->Comancontroler_model->get_data_by_column('invoice',$data['dispatch'][0]['otherParentInvoice'],'dispatchOutside','id');
	        if($parentID){
	            if($parentID[0]['id'] > 0) {
	                $documentsParent = $this->Comancontroler_model->get_document_by_dispach($parentID[0]['id'],'documentsOutside');
	                if($documentsParent){
            	        foreach($documentsParent as $doc){
            	            $doc['otherParent'] = 'yes'; 
							$doc['parentType'] = 'logistics';
            	            $data['otherDocuments'][] = $doc;
            	        }
            	    }
	            }
	        }
	    }
		$parentInfo = $this->Comancontroler_model->get_data_by_column('child_id', $data['dispatch'][0]['id'], 'sub_invoices','parent_id, parent_type');
		if (!empty($parentInfo)) {
			$parentID   = $parentInfo[0]['parent_id'];
			$parentType = $parentInfo[0]['parent_type'];

			if ($parentID > 0) {
				if ($parentType == 'warehousing') {
					$documentsParent = $this->Comancontroler_model->get_document_by_dispach($parentID, 'warehouse_documents');
				} else {
					$documentsParent = [];
				}

				if (!empty($documentsParent)) {
					foreach ($documentsParent as $doc) {
						$doc['otherParent'] = 'yes';
						$doc['parentType'] = 'warehousing';
						$data['otherDocuments'][] = $doc;
					}
				}

			}
		}

	    $data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
	    $data['dispatchLog'] = $this->Comancontroler_model->get_dispachLog('dispatchLog',$id);
	    $data['reminderLog'] = $this->Comancontroler_model->get_reminderLog('fleet',$id);
	    $dispatchMeta = json_decode($data['dispatch'][0]['dispatchMeta'],true);
	    
	    $data['otherChildInvoice'] = array();
	    if($dispatchMeta['otherChildInvoice'] != ''){
	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
	        $data['otherChildInvoice'] = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatchOutside','id,invoice,rate,parate,trailer');
	    }
	    
	    $data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['dispatch'][0]['invoice'],'dispatch','id,invoice,rate,parate,trailer');
		$unitPriceSql = "SELECT * FROM unitPrice WHERE dispatchId='$id' AND dispatchType='fleet'";
		$unitPrice = $this->db->query($unitPriceSql)->result_array();
		$data['unitPrice']=$unitPrice;

    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/dispatch_update',$data);
    	$this->load->view('admin/layout/footer');
    }
    
	function index() {
	    if(!checkPermission($this->session->userdata('permission'),'dispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    
	    if(isset($_GET['updateInDb']) && $_GET['updateInDb'] == 'yes'){ 
	        $dispatchAll = $this->Comancontroler_model->get_data_by_column('pudate >=','2024-07-01','dispatch','id,pudate,invoiceType,parate,trailer,dispatchMeta');
            if($dispatchAll) {
                foreach($dispatchAll as $dispatch){
                    $parate = $dispatch['parate'];
                    $payoutAmount = 0;
                    if($dispatch['invoiceType'] == '' || $parate < 1) {  }
        			elseif($dispatch['invoiceType'] == 'RTS') { $payoutAmount = $parate - ($parate * 0.0115); }
        			elseif($dispatch['invoiceType'] == 'Direct Bill') { $payoutAmount = $parate * 1; }
        			elseif($dispatch['invoiceType'] == 'Quick Pay') { $payoutAmount = $parate - ($parate * 0.02); }
        			echo $dispatch['id'].' '.$dispatch['pudate'].' '.$parate.' ';
        			if($payoutAmount > 0){ echo ' yes ';
        			    $payoutAmount = round($payoutAmount,2); 
        			    $udata = array('payoutAmount'=>$payoutAmount);
        			    $this->Comancontroler_model->update_table_by_id($dispatch['id'],'dispatch',$udata);
        			}
        			echo '<br>';
                }
            }
		   die('df');
	    }
	    
	    
	    $data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');
	    
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
        
        $sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        $edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
            
        ////// generate csv
        if($this->input->post('generateCSV') || $this->input->post('generateXls')){
            $unit = $this->input->post('unit');
            $driver = $this->input->post('driver'); 
            
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                if(date('Y-m',strtotime($sdate)) != date('Y-m',strtotime($edate))){
                    //$this->session->set_flashdata('searchError', 'If you want to filter with week than both date must be same month.');
                    //redirect(base_url('admin/dispatch'));
                } else {
                    $weeks = explode(',',$week);
                    //$sdate = $weeks[0];
                    //$edate = $weeks[1];
                    $sdate = date('Y-m',strtotime($sdate)).$weeks[0];
                    $edate = date('Y-m',strtotime($edate)).$weeks[1];
                }
            }
            
            $dispatch = $this->Comancontroler_model->downloadDispatchCSV($sdate,$edate,$unit,$driver,$status,$invoice,$tracking); 
            $expenses = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','title,type','title','asc');

			// Data to be written to the CSV file (example data)
			$heading = array('Dispatch ID','Vehicle','Driver','Pick Up Date','Pick Up Time','Pick Up City','Pick Up Company','Pick Up Address','Pick Up','Pickup Notes','Drop Off Date','Drop Off Time','Drop Off City','Drop Off Company','Drop Off Address','Drop Off','Driver Notes','PA Rate','Invoice Amount','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Detention','Driver Assist','Dispatch Notes','Dispatch Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Sub Invoice');
			foreach($expenses as $ex){ $heading[] = $ex['title']; }
			$data = array($heading);
			
			// array('0.Dispatch ID','1.Vehicle','2.Driver','3.Pick Up Date','4.Pick Up Time','5.Pick Up City','6.Pick Up Company','7.Pick Up Address','8.Pick Up','9.Pickup Notes','10.Drop Off Date','11.Drop Off Time','12.Drop Off City','13.Drop Off Company','14.Drop Off Address','15.Drop Off','16.Driver Notes','17.Rate','18.PA Rate','19.Company','20.Trailer','21.Tracking','22.Invoice','23.Week','24.Payout Amount','25.Invoice Date','26.Invoice Type','27.Expected Pay Date','28.Detention','29.Driver Assist','30.Status','31.Driver Status','32.Notes','33.Invoice Ready','34.Invoice Paid','35.Invoice Closed')
			
			if(!empty($dispatch)) {
			    $subInvoice = $dispatchArr = $comAddArr = array();
				$companyAddress = $this->Comancontroler_model->get_data_by_table('companyAddress');
				if(!empty($companyAddress)){
					foreach($companyAddress as $val){
						$comAddArr[$val['id']] = array($val['company'],$val['city'].', '.$val['state'],$val['address'].' '.$val['city'].', '.$val['state'].' '.$val['zip']);
					}
				}
				foreach($dispatch as $row){
					if(in_array($row['id'], $dispatchArr)) { continue; }
					$dispatchArr[] = $row['id'];
					if($row['childInvoice'] != '') { $subInvoice[] = $row['invoice']; }
				}
				if($subInvoice){
					$childDispatch = $this->Comancontroler_model->downloadDispatchCSV('','','','','','','',$subInvoice);
					if($childDispatch) {
						foreach($childDispatch as $row){
							if(in_array($row['id'], $dispatchArr)) { continue; }
							$dispatchArr[] = $row['id'];
							$dispatch[] = $row;
						}
					}
				}
				
				foreach($dispatch as $row){
				   $pudate = date('m/d/Y',strtotime($row['pudate']));
				   $dodate = $invoiceDate = $expectPayDate = '0000-00-00';
				   if($row['dodate']!='0000-00-00') {
				       $dodate = date('m/d/Y',strtotime($row['dodate']));
				   }
				   if($row['invoiceDate']!='0000-00-00') {
				       $invoiceDate = date('m/d/Y',strtotime($row['invoiceDate']));
				   }
				   if($row['expectPayDate']!='0000-00-00') {
				       $expectPayDate = date('m/d/Y',strtotime($row['expectPayDate']));
				   }
				   $dispatchMeta = json_decode($row['dispatchMeta'],true);
				   $invStatus = $invPaid = $invClosed = '';
				   if($dispatchMeta['invoiceClose']=='1') { $invStatus = 'Closed'; }
				   elseif($dispatchMeta['invoicePaid']=='1') { $invStatus = 'Paid'; }
				   elseif($dispatchMeta['invoiced']=='1') { $invStatus = 'Invoiced'; }
				   elseif($dispatchMeta['invoiceReady']=='1') { $invStatus = 'Ready To Submit'; }
				   if($invStatus != '') { $invStatus = $row['invoiceType'].' '.$invStatus; }
				   
				   $invReady = $dispatchMeta['invoiceReadyDate'];
				   if(trim($invReady) != ''){ $invReady = date('m/d/Y',strtotime($invReady)); }
				   $invPaid = $dispatchMeta['invoicePaidDate'];
				   if(trim($invPaid) != ''){ $invPaid = date('m/d/Y',strtotime($invPaid)); }
				   $invClosed = $dispatchMeta['invoiceCloseDate'];
				   if(trim($invClosed) != ''){ $invClosed = date('m/d/Y',strtotime($invClosed)); }
				   
				   if(strstr($row['trailer'],',')) { $trailer = '="'.$row['trailer'].'"'; }
				   else { $trailer = $row['trailer']; }
				   
				   if(array_key_exists($row['paddressid'],$comAddArr)  && $row['paddressid'] > 0){
					   $row['ppcity'] = $comAddArr[$row['paddressid']][1];
					   $row['pplocation'] = $comAddArr[$row['paddressid']][0];
					   $row['paddress'] = $comAddArr[$row['paddressid']][2];
					}
					if(array_key_exists($row['daddressid'],$comAddArr)  && $row['daddressid'] > 0){
					   $row['ddcity'] = $comAddArr[$row['daddressid']][1];
					   $row['ddlocation'] = $comAddArr[$row['daddressid']][0];
					   $row['daddress'] = $comAddArr[$row['daddressid']][2];
					}
					
					$childInvoices = !empty($row['childInvoice']) ? explode(',', $row['childInvoice']) : [];
					$otherInvoices = !empty($dispatchMeta['otherChildInvoice']) ? explode(',', $dispatchMeta['otherChildInvoice']) : [];
					$allInvoices = array_filter(array_unique(array_merge($childInvoices, $otherInvoices)));
					// $allChildInvoices = implode(',', $allInvoices);
					$allChildInvoices = '' . implode(', ', $allInvoices) . '';

					$rowData = array($row['id'],$row['vname'].' ('.$row['vnumber'].')',$row['dname'],$pudate,$row['ptime'],$row['ppcity'],$row['pplocation'],$row['paddress'],$row['pcode'],cleanSpace($row['pnotes']),$dodate,$row['dtime'],$row['ddcity'],$row['ddlocation'],$row['daddress'],$row['dcode'],cleanSpace($row['dnotes']),$row['rate'],$row['parate'],$row['ccompany'],$trailer,$row['tracking'],$row['invoice'],$row['dWeek'],$row['payoutAmount'],$invoiceDate,$row['invoiceType'],$expectPayDate,$row['detention'],$row['dassist'],$row['status'],$row['driver_status'],cleanSpace($row['notes']),$invReady,$invPaid,$invClosed,cleanSpace($row['invoiceNotes']),$allChildInvoices);
				   
				   foreach($expenses as $ex){ 
						$exInfo = '';
						if($dispatchMeta['expense']) { 
							foreach($dispatchMeta['expense'] as $diVal) {
								if($diVal[0] == $ex['title']){ $exInfo = $diVal[1]; }
							}
						}
						$rowData[] = $exInfo;
					}
					
				    $data[] = $rowData;
				}
			}

			if($this->input->post('generateCSV')){
				$fileName = "FleetDispatch_".$sdate."_".$edate.".csv"; 
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
			if($this->input->post('generateXls')){
				$this->load->library('excel_generator');
				$fileName = "FleetDispatch_".$sdate."_".$edate.".xlsx";   //"data_$date.xlsx";
				// Generate Excel file using the library
				//echo "<pre>"; print_r($data); exit;
				$this->excel_generator->generateExcel($data, $fileName);
			}
			

			// Delete the file from the server
			unlink($fileName);
			exit;
			die('csv');
        }
		
        /*if(date('d') < 9) { 
            $sdate = date('Y-m').'-01'; $edate = date('Y-m').'-08'; 
        }
        elseif(date('d') < 16) { 
            $sdate = date('Y-m').'-09'; $edate = date('Y-m').'-15'; 
        }
        elseif(date('d') < 24) { 
            $sdate = date('Y-m').'-16'; $edate = date('Y-m').'-23'; 
        }
        else {
            $sdate = date('Y-m').'-24'; $edate = date('Y-m-t'); 
        }*/
        
        $unit = $driver = $status = $invoice = $tracking = '';
         
        if($this->input->post('search'))	{
            $unit = $this->input->post('unit');
            $driver = $this->input->post('driver');
            
            //$status = $this->input->post('status');
            $invoice = $this->input->post('invoiceType');
            //$tracking = $this->input->post('tracking');
            
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                if(date('Y-m',strtotime($sdate)) != date('Y-m',strtotime($edate))){
                    $this->session->set_flashdata('searchError', 'If you want to filter with week than both date must be same month.');
                    redirect(base_url('admin/dispatch'));
                } else {
                    $weeks = explode(',',$week);
                    //$sdate = $weeks[0];
                    //$edate = $weeks[1];
                    $sdate = date('Y-m',strtotime($sdate)).$weeks[0];
                    $edate = date('Y-m',strtotime($edate)).$weeks[1];
                }
            }
            
            
            //$data['dispatch'] = $this->Comancontroler_model->get_dispatch_by_filter($sdate,$edate,$unit,$driver,$status,$invoice,$tracking);
        } else {
            $data['dispatch'] = array();
        }
        
    	$data['dispatch'] = $this->Comancontroler_model->get_dispatch_by_filter($sdate,$edate,$unit,$driver,$status,$invoice,$tracking,'');
    	//print_r($data['dispatch']);
    	$subInvoice = $dispatchArr = array();
    	$allDates = '';
    	if($data['dispatch']){
    	    for($i=0;count($data['dispatch']) > $i;$i++){
    	        
    	        /*if($data['dispatch'][$i]['invoiceType'] != ''){
    	            
					if($data['dispatch'][$i]['invoiceType']=='RTS'){ $p = 0.0115; }
					//elseif($data['dispatch'][$i]['invoiceType']=='Direct Bill'){ $p = 1; }
					elseif($data['dispatch'][$i]['invoiceType']=='Quick Pay'){ $p = 0.02; }
					else{ $p = 0; }
					
					if($p > 0){
					    $allDates .= ' '.$data['dispatch'][$i]['id'].' -- '.$data['dispatch'][$i]['pudate'].' -- '.$data['dispatch'][$i]['invoiceType'].' -- '.$data['dispatch'][$i]['parate'].' -- '.$data['dispatch'][$i]['payoutAmount'];
    					$payoutAmount = $data['dispatch'][$i]['parate'] - ($data['dispatch'][$i]['parate'] * $p);
    					$payoutAmount = round($payoutAmount,2);
    					$allDates .= ' -- '.$payoutAmount.'<br>';
    					//$insert_data = array('payoutAmount'=>$payoutAmount);
    					//$this->Comancontroler_model->update_table_by_id($data['dispatch'][$i]['id'],'dispatch',$insert_data);
					}
    	        }*/
    	        
    	        if(in_array($data['dispatch'][$i]['id'], $dispatchArr)) { continue; }
    	        $dispatchArr[] = $data['dispatch'][$i]['id'];
    	        
				if($data['dispatch'][$i]['childInvoice'] != '') { $subInvoice[] = $data['dispatch'][$i]['invoice']; }
    	        $dispatchInfo = $this->Comancontroler_model->get_dispatchinfo_by_id($data['dispatch'][$i]['id'],'pd_date,pd_city,pd_location,pd_time,pd_addressid');
				if($dispatchInfo){
					foreach($dispatchInfo as $dis){
						$data['dispatch'][$i]['pd_date'] = $dis['pd_date'];
						$data['dispatch'][$i]['pd_city'] = $dis['pd_city'];
						$data['dispatch'][$i]['pd_location'] = $dis['pd_location'];
						$data['dispatch'][$i]['pd_time'] = $dis['pd_time'];
					}
				} else {
				    $data['dispatch'][$i]['pd_date'] = $data['dispatch'][$i]['pd_city'] = $data['dispatch'][$i]['pd_location'] = $data['dispatch'][$i]['pd_time'] = $data['dispatch'][$i]['pd_addressid'] = '';
				}
    	    }
    	}
    	if(isset($_GET['loop'])){
    	    echo $allDates;
    	    die();
    	}
		if($subInvoice){
			$subDis = $this->Comancontroler_model->get_dispatch_by_filter('','','','','','','',$subInvoice);
			if($subDis){
				foreach($subDis as $sd){
				    if(in_array($sd['id'], $dispatchArr)) { continue; }
    	            $dispatchArr[] = $sd['id'];
    	        
					$sd['pd_date'] = $sd['pd_city'] = $sd['pd_location'] = $sd['pd_time'] = '';
					$data['dispatch'][] = $sd;
				}
			}
		}

		$invoiceWiseTotal = [];
		foreach ($data['dispatch'] as $key) {
			$groupKey = $key['parentInvoice'] ?: $key['invoice'];

			if (!isset($invoiceWiseTotal[$groupKey])) {
				$invoiceWiseTotal[$groupKey] = ['rate' => 0, 'parate' => 0];
			}

			$invoiceWiseTotal[$groupKey]['rate'] += $key['rate'];
			$invoiceWiseTotal[$groupKey]['parate'] += $key['parate'];
		}
		$data['invoiceWiseTotal'] = $invoiceWiseTotal;
		
		$data['sdate'] = $data['startDate'] = $sdate;
		$data['edate'] = $data['endDate'] = $edate;
    	
	  $data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
	  $data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
	  $data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
	  $data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
	  $data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
	  $data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/dispatch',$data);
    	$this->load->view('admin/layout/footer');
	}
 
	function dispatchadd() {
	    if(!checkPermission($this->session->userdata('permission'),'dispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
        //$data['expenses'] = $this->expenses;
	    $userid = $this->session->userdata('adminid');

	    $data['expenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','title,type','title','asc');
	    $data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');
	    
		if($this->input->post('save'))	{ 
			$this->form_validation->set_rules('pudate', 'PU date','required|min_length[9]');
			$this->form_validation->set_rules('pcity', 'pickup city','required|min_length[1]');
			$this->form_validation->set_rules('dcity', 'drop off city','required|min_length[1]');
			$this->form_validation->set_rules('company', 'company','required|min_length[1]'); 
			$this->form_validation->set_rules('dlocation', 'drop off location','required|min_length[1]'); 
			$this->form_validation->set_rules('plocation', 'pick up location','required|min_length[1]'); 
			$this->form_validation->set_rules('tracking', 'tracking','required|min_length[1]'); 
			
			$pudate = $this->input->post('pudate');
			$driver = $this->input->post('driver');
			$pudate1 = $this->input->post('pudate1');
			if(!is_array($pudate1)){ $pudate1 = array(); }
			$dodate1 = $this->input->post('dodate1');
			if(!is_array($dodate1)){ $dodate1 = array(); }
			
			/*if(count($pudate1) != count($dodate1)) {
				$this->form_validation->set_rules('pudate1', 'extra dispatch info','required'); 
				$this->form_validation->set_message('required','Extra dispatch pickup and drop off count must be equal.'); 
			}*/
			
			$inv_first = '';
			$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate);
			if(empty($driver_trip)) { $inv_last = 1; }
			else { $inv_last = count($driver_trip) + 1; }
			if($inv_last < 10) {  $inv_last = '0'.$inv_last; }
			/*elseif(count($driver_trip)==1) { $inv_last = 'B'; }
			elseif(count($driver_trip)==2) { $inv_last = 'C'; }
			elseif(count($driver_trip)==3) { $inv_last = 'D'; }
			else { $inv_last = 'E'; }*/
			
			$driver_info = $this->Comancontroler_model->get_data_by_id($driver,'drivers','dcode');
			if(!empty($driver_info)) {
				$inv_first = $driver_info[0]['dcode'];
			}
			$inv_middel = date('mdy',strtotime($pudate));
			$invoice = $inv_first.''.$inv_middel.'-'.$inv_last;
			
			if($invoice == '' || $inv_first == ''){
				$this->form_validation->set_rules('invoice', 'invoice','required'); 
				$set_message = 'Invoice number must not empty.';
				if($inv_first == ''){ $set_message = 'Driver code is empty.'; }
				$this->form_validation->set_message('required',$set_message); 
			}
			
			$invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
			
			/*$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatch','id');
			if($invoice == '' || $inv_first == ''){
				$this->form_validation->set_rules('invoice', 'invoice','required'); 
				$set_message = 'Invoice number must not empty.';
				if($inv_first == ''){ $set_message = 'Driver code is empty.'; }
				$this->form_validation->set_message('required',$set_message); 
			}
			elseif($invoiceInfo){
				$invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
				$checkInvoice = 'false'; $invoiceCount = count($driver_trip) + 2;
				while($checkInvoice == 'false'){
					$invoiceCountTxt = $invoiceCount;
					if($invoiceCount < 10) { $invoiceCountTxt = '0'.$invoiceCount; }
					$invoice = $inv_first.''.$inv_middel.'-'.$invoiceCountTxt;
					$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatch','id');
					if($invoiceInfo){}
					else { $checkInvoice = 'true'; }
					$invoiceCount++;
				}
			}*/
			
			
			$check_dcity = $this->input->post('dcity');
			$check_pcity = $this->input->post('pcity');
			$check_plocation = $this->input->post('plocation');
			$check_dlocation = $this->input->post('dlocation');
			$check_paddress = $this->input->post('paddress');
			$check_daddress = $this->input->post('daddress');
				
			if($this->isAddressExist($pudate,$check_pcity,$check_plocation,$check_paddress)){
				$addr = $check_plocation.' '.$check_paddress.' '.$check_pcity;
				$this->form_validation->set_rules('pickupaddressss', 'pickup address','required');
				$this->form_validation->set_message('required','Pick up address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
			}
			
			if($this->isAddressExist($pudate,$check_dcity,$check_dlocation,$check_daddress)){
				$addr = $check_dlocation.' '.$check_daddress.' '.$check_dcity;
				$this->form_validation->set_rules('dropoffaddressss', 'drop off address','required');
				$this->form_validation->set_message('required','Drop off address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
			}
			
				
			if(count($pudate1) > 0) {
				$check_pcity1 = $this->input->post('pcity1');
				$check_plocation1 = $this->input->post('plocation1');
				$check_paddress1 = $this->input->post('paddress1');
				$check_pd_type1 = $this->input->post('pd_type1');
				for($i=0;$i<count($pudate1);$i++){
					if($pudate1[$i]!='' && $check_pd_type1[$i]=='pickup') {
						if($this->isAddressExist($pudate,$check_pcity1[$i],$check_plocation1[$i],$check_paddress1[$i])){
							$addr = $check_plocation1[$i].' '.$check_paddress1[$i].' '.$check_pcity1[$i];
							$this->form_validation->set_rules('dropoffaddressss'.$i, 'drop off address','required');
							$this->form_validation->set_message('required','Pickup address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
						}
					}
				}
			}
			if(count($dodate1) > 0) {
				$check_dcity1 = $this->input->post('dcity1');
				$check_dlocation1 = $this->input->post('dlocation1');
				$check_daddress1 = $this->input->post('daddress1');
				$check_pd_type2 = $this->input->post('pd_type2');
				for($i=0;$i<count($dodate1);$i++){
					if($dodate1[$i]!='' && $check_pd_type2[$i]=='dropoff') {
						if($this->isAddressExist($pudate,$check_dcity1[$i],$check_dlocation1[$i],$check_daddress1[$i])){
							$addr = $check_dlocation1[$i].' '.$check_daddress1[$i].' '.$check_dcity1[$i];
							$this->form_validation->set_rules('dropoffaddresss'.$i, 'drop off address','required');
							$this->form_validation->set_message('required','Drop off address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
						}
					}
				}
			}
			
			$check_company = $this->input->post('company');
			$invoiceType = $this->input->post('invoiceType');
			$checkCompany = $this->Comancontroler_model->get_data_by_column('company',$check_company,'companies','id,address');
			if($invoiceType == 'RTS' || $invoiceType == ''){
			    
			} elseif($checkCompany && $checkCompany[0]['address'] == ''){
			    $this->form_validation->set_rules('checkcompany', 'company','required');
				$this->form_validation->set_message('required','Company ('.$check_company.') address is blank. <a href="/admin/company/update/'.$checkCompany[0]['id'].'" target="_blank">Click here to update company address</a>.');
			} elseif(empty($checkCompany)){
			    $this->form_validation->set_rules('checkcompany', 'company','required');
				$this->form_validation->set_message('required','Company ('.$check_company.') is not exist. <a href="/admin/company/add" target="_blank">Click here to add new company</a>.');
			}
			
		
			$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
			if ($this->form_validation->run() == FALSE){}
            else
            {
				//$vname = $this->input->post('vname'); 
			    $replaceFileName = array(',',"'",'"','(',')','/','.','<');
		
				/******************* check companies ****************/
				$check_company = $this->input->post('company');
				$company = $this->check_company($check_company);
				$companyInfo = $this->Comancontroler_model->get_data_by_id($company,'companies','paymenTerms,payoutRate,dayToPay');
				
				$check_dcity = $this->input->post('dcity');
				$dcity = $this->check_city($check_dcity);
				
				$check_pcity = $this->input->post('pcity');
				$pcity = $this->check_city($check_pcity);
				
				$check_plocation = $this->input->post('plocation');
				$plocation = $this->check_location($check_plocation);
				
				$check_dlocation = $this->input->post('dlocation');
				$dlocation = $this->check_location($check_dlocation);
				
				$dcode = $this->input->post('dcode');
				$dcodeVal = implode('~-~',$dcode);
				$pcode = $this->input->post('pcode');
				$pcodeVal = implode('~-~',$pcode);
				
				
				$pudate = $this->input->post('pudate');
				$vehicle = $this->input->post('vehicle');
				
				
				$week = date('M',strtotime($pudate)).' W';
				$day = date('d',strtotime($pudate));
				if($day < 9) { $w = '1'; }
				elseif($day < 16){ $w = '2'; }
				elseif($day < 24){ $w = '3'; }
				else { $w = '4'; }
				$week .= $w; 
				
				$parate = $this->input->post('parate');
				if(!is_numeric($parate)) { $parate = 0; }
				
				//$invoiceType = $companyInfo[0]['paymenTerms'];
				$expectPayDate = $this->input->post('expectPayDate');
				$invoiceDate = $this->input->post('invoiceDate');
				$invoiceType = $this->input->post('invoiceType');
				
				$payoutRate = $companyInfo[0]['payoutRate'];
				if(!is_numeric($payoutRate)) { $payoutRate = 0; }
				$payoutAmount = $payoutRate * $parate;
				$payoutAmount = round($payoutAmount,2);
				
				if($invoiceType == '' || $parate < 1) {  }
				elseif($invoiceType == 'RTS') { $payoutAmount = $parate - ($parate * 0.0115); }
				elseif($invoiceType == 'Direct Bill') { $payoutAmount = $parate * 1; }
				elseif($invoiceType == 'Quick Pay') { $payoutAmount = $parate - ($parate * 0.02); }
				if($payoutAmount > 0){ $payoutAmount = round($payoutAmount,2); }
				
				
				$dispatchMeta = array('expense'=>array(),'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0');
				$expenseName = $this->input->post('expenseName');
				$expensePrice = $this->input->post('expensePrice');
				if(is_array($expenseName)) {
				    for($i=0;$i<count($expenseName);$i++){
				        $dispatchMeta['expense'][] = array($expenseName[$i],$expensePrice[$i]);
				    }
				}
				$dispatchMeta['invoiced'] = $this->input->post('invoiced');
				$dispatchMeta['invoicePaid'] = $this->input->post('invoicePaid');
				$dispatchMeta['invoiceClose'] = $this->input->post('invoiceClose');
				$dispatchMeta['invoiceReady'] = $this->input->post('invoiceReady');
				$dispatchMeta['appointmentTypeP'] = $this->input->post('appointmentTypeP');
				$dispatchMeta['appointmentTypeD'] = $this->input->post('appointmentTypeD');

				$bol = $this->input->post('bol');
				$rc = $this->input->post('rc');
				$gd = $this->input->post('gd');
				
				$status = $this->input->post('status');
				
				if($bol=='AK' && $rc=='AK' && $gd=='AK'){ 
				    if($invoiceType == 'RTS' && $this->input->post('invoiceCloseOld')!='1'){
				        /*if($dispatchMeta['invoiceClose']=='1'){  $status = 'Closed '.date('m/d/Y'); }
				        elseif($dispatchMeta['invoicePaid']=='1'){  $status = 'RTS Paid '.date('m/d/Y'); }
				        elseif($dispatchMeta['invoiced']=='1'){  $status = 'RTS Invoiced '.date('m/d/Y'); }
				        elseif($dispatchMeta['invoiceReady']=='1'){  $status = 'Ready to submit RTS '.date('m/d/Y'); }*/
				    }
				    if($invoiceType == 'Direct Bill' && $this->input->post('invoiceCloseOld')!='1'){
				        /*if($dispatchMeta['invoiceClose']=='1'){  $status = 'Closed '.date('m/d/Y'); }
				        elseif($dispatchMeta['invoicePaid']=='1'){  $status = 'DB Paid '.date('m/d/Y'); }
				        elseif($dispatchMeta['invoiced']=='1'){  $status = 'DB Invoiced '.date('m/d/Y'); }
				        elseif($dispatchMeta['invoiceReady']=='1'){  $status = 'Ready to submit DB '.date('m/d/Y'); }*/
				    }
				    if($invoiceType == 'Quick Pay'){
				        //if($dispatchMeta['invoiceReady']=='1' && $dispatchMeta['invoiceReadyOld']!='1'){  $status = 'Ready to submit QP '.date('m/d/Y'); }
				        $dispatchMeta['invoiceClose'] = $dispatchMeta['invoicePaid'] = $dispatchMeta['invoiced'] = '0';
				    }
				}
				
				$dispatchMetaJson = json_encode($dispatchMeta);
				
				$insert_data = array(
				    'driver'=>$driver,
				    'vehicle'=>$vehicle,
				    'pudate'=>$pudate,
				    'trip'=>$this->input->post('trip'),
				    'delivered'=>$this->input->post('delivered'),
					'pcity'=>$pcity,
				    'dcity'=>$dcity,
				    'dodate'=>$this->input->post('dodate'),
				    'rate'=>$this->input->post('rate'),
				    'parate'=>$this->input->post('parate'),
				    'company'=>$company,
				    'dlocation'=>$dlocation,
				    'plocation'=>$plocation,
				    'dcode'=>$dcodeVal,
				    'pcode'=>$pcodeVal,
				    'paddress'=>$this->input->post('paddress'),
				    'daddress'=>$this->input->post('daddress'),
				    'paddressid'=>$this->input->post('paddressid'),
				    'daddressid'=>$this->input->post('daddressid'),
				    'trailer'=>$this->input->post('trailer'),
				    'tracking'=>$this->input->post('tracking'),
				    'invoice'=>$invoice,
				    'payoutAmount'=>$payoutAmount,
				    'invoiceType'=>$invoiceType,
				    'dWeek'=>$week,
				    'bol'=>$this->input->post('bol'),
				    'rc'=>$this->input->post('rc'),
				    'gd'=>$this->input->post('gd'),
				    'ptime'=>$this->input->post('ptime'),
				    'dtime'=>$this->input->post('dtime'),
				    'notes'=>$this->input->post('notes'),
				    'pnotes'=>$this->input->post('pnotes'),
				    'dnotes'=>$this->input->post('dnotes'),
				    //'detention'=>$this->input->post('detention'),
				    //'dassist'=>$this->input->post('dassist'),
				    'driver_status'=>$this->input->post('driver_status'),
				    'dispatchMeta'=>$dispatchMetaJson,
				    'status'=>$this->input->post('status'),
				    'rdate'=>date('Y-m-d H:i:s')
				);
				if($invoiceDate != 'TBD' && $invoiceDate != ''){
				    $insert_data['invoiceDate'] = $invoiceDate; 
				    $expectPayDate = date('Y-m-d',strtotime("+1 month",strtotime($invoiceDate)));
				    $insert_data['expectPayDate'] = $expectPayDate;
				} else {
				    $insert_data['invoiceDate'] = $insert_data['expectPayDate'] = '0000-00-00';
				}
			
				$res = $this->Comancontroler_model->add_data_in_table($insert_data,'dispatch'); 
				if($res){
				    
				// 	$reminder = array(
				// 	'dispatch_id'=>$res,
				// 	'dispatch_type'=>'fleet',
				// 	'message'=>'dispatch',
				// 	'event_type'=>'dispatch',
				// 	'added_by'=>$userid
				// );
				// $this->Comancontroler_model->add_data_in_table($reminder,'reminders');

				    /*********** insert data in extra dispatch table *****/
				    if(count($pudate1) > 0 || count($dodate1) > 0) {
				        $pcode1 = $this->input->post('pcode1');
    					$dcode1 = $this->input->post('dcode1');
    					$check_dcity1 = $this->input->post('dcity1');
    					$check_pcity1 = $this->input->post('pcity1');
    					$check_plocation1 = $this->input->post('plocation1');
    					$check_dlocation1 = $this->input->post('dlocation1');
    					$ptime1 = $this->input->post('ptime1');
    					$dtime1 = $this->input->post('dtime1');
    					$paddress1 = $this->input->post('paddress1');
    					$daddress1 = $this->input->post('daddress1');
    					$paddressid1 = $this->input->post('paddressid1');
    					$daddressid1 = $this->input->post('daddressid1');
    					$pnotes1 = $this->input->post('pnotes1');
    					$dnotes1 = $this->input->post('dnotes1');
    					$pcodename = $this->input->post('pcodename');
    					$dcodename = $this->input->post('dcodename');
						$appointmentTypeP1 = $this->input->post('appointmentTypeP1');
    					$appointmentTypeD1 = $this->input->post('appointmentTypeD1');

    					for($i=0;$i<count($pudate1);$i++){
				          if($pudate1[$i]!='') {
				            $pcodeVal1 = implode('~-~',$pcode1[$pcodename[$i]]); 
				            $pcity1 = $this->check_city($check_pcity1[$i]);
				            $plocation1 = $this->check_location($check_plocation1[$i]); 
				            $pd_meta = array();
							$pd_meta['appointmentType'] = $appointmentTypeP1[$i];
							$pdMetaJson = json_encode($pd_meta);

    					    $extraData = array(
    					    'dispatchid'=>$res,
    					    'pd_location'=>$plocation1,
    					    'pd_time'=>$ptime1[$i],
    					    'pd_address'=>$paddress1[$i],
    					    'pd_addressid'=>$paddressid1[$i],
    					    'pd_date'=>$pudate1[$i],
    					    'pd_notes'=>$pnotes1[$i],
    					    'pd_code'=>$pcodeVal1,
    					    'pd_city'=>$pcity1,
    					    'pd_order'=>$i,
							'pd_meta'=>$pdMetaJson,
							'pd_type'=>'pickup'
    					    );
    					    $this->Comancontroler_model->add_data_in_table($extraData,'dispatchExtraInfo');
				          }
    					}
						for($i=0;$i<count($dodate1);$i++){
				          if($dodate1[$i]!='') { 
				            $dcodeVal1 = implode('~-~',$dcode1[$dcodename[$i]]);
				            $dcity1 = $this->check_city($check_dcity1[$i]);  
				            $dlocation1 = $this->check_location($check_dlocation1[$i]);
				            $pd_meta = array();
							$pd_meta['appointmentType'] = $appointmentTypeD1[$i];			
				            $pdMetaJson = json_encode($pd_meta);

    					    $extraData = array(
    					    'dispatchid'=>$res,
    					    'pd_location'=>$dlocation1,  
    					    'pd_time'=>$dtime1[$i], 
    					    'pd_address'=>$daddress1[$i], 
    					    'pd_addressid'=>$daddressid1[$i], 
    					    'pd_date'=>$dodate1[$i], 
    					    'pd_notes'=>$dnotes1[$i],
    					    'pd_code'=>$dcodeVal1,  
    					    'pd_city'=>$dcity1,
    					    'pd_order'=>$i,
							'pd_meta'=>$pdMetaJson,
							'pd_type'=>'dropoff'
    					    );
    					    $this->Comancontroler_model->add_data_in_table($extraData,'dispatchExtraInfo');
				          }
    					}
				    }
				    
				    /*********** insert data in driver trip table *****/
				    $check_trip = $this->Comancontroler_model->check_dirver_trip($driver,$pudate);
				    if(empty($check_trip)) {
						$trip1 = $res.','.$plocation.','.$dlocation.','.$pcity.','.$dcity;
				        $driver_trip = array('tripdate'=>$pudate,'rate'=>'250','trip1'=>$trip1,'driver'=>$driver,'spend_amt'=>'0','total_hour'=>'0','total_amt'=>'0','rdate'=>date('Y-m-d H:i:s'));
				        $this->Comancontroler_model->add_data_in_table($driver_trip,'driver_trips');
				    } else { 
						if($check_trip[0]['trip2']=='') { $trip = 'trip2'; }
						elseif($check_trip[0]['trip3']=='') { $trip = 'trip3'; }
						elseif($check_trip[0]['trip4']=='') { $trip = 'trip4'; }
						else { $trip = ''; }
						 
						if($trip != '') { 
							$newtrip = $res.','.$plocation.','.$dlocation.','.$pcity.','.$dcity;
							$driver_trip = array($trip=>$newtrip);
							$this->Comancontroler_model->update_driver_trips($driver_trip,$driver,$pudate);
						}
					}
				    
				    /********** add entry in finance table *********/
				    $fday = date('d',strtotime($pudate));
				    $fyearmonth = date('Y-m',strtotime($pudate)); 
				    $flday = date('t',strtotime($pudate));
				    
				    if($fday < 9) { $fdate = $fyearmonth.'-01'; $fweek = '01-08'; }
                    elseif($fday < 16) { $fdate = $fyearmonth.'-09'; $fweek = '09-15'; }
                    elseif($fday < 24) { $fdate = $fyearmonth.'-16'; $fweek = '16-23'; }
                    else { $fdate = $fyearmonth.'-24'; $fweek = '24-'.$flday;  }
                    $check_finance = $this->Comancontroler_model->check_finance_entry($fweek,$driver,$fdate,$vehicle);
                    if(empty($check_finance)) {
                        $add_finance = array('unit_pay'=>'0','driver_pay'=>'0','driver'=>$driver,'unit_id'=>$vehicle,'fweek'=>$fweek,'fdate'=>$fdate,'total_expense'=>'0','total_income'=>'0','total_amt'=>'0');
                        $this->Comancontroler_model->add_data_in_table($add_finance,'finance'); 
                    }
				    
				    
				    /*********** upload documents *********/
				    $config['upload_path'] = 'assets/upload/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
                    $config['max_size']= '5000';
                    
                    /******** rename documents *************/
                    $driverCode = '';
                    $driversCodeResult = $this->Comancontroler_model->get_data_by_table('drivers','id,dcode');
                    if($driversCodeResult){
                        foreach($driversCodeResult as $driverLoop){
                            if($driverLoop['id'] == $driver) { $driverCode = $driverLoop['dcode']; }
                        }
                    }
                    
                    $fileName1 = date('m-d-y',strtotime($pudate)).'-Trip-'.$this->input->post('trip');
                    $fileName1 = str_replace(' ','-',$fileName1);
                    $fileName1 = str_replace($replaceFileName,'',$fileName1);
                    $fileName2 = $driverCode.'-'.$this->input->post('tracking').'-'.$this->input->post('company');
                    $fileName2 = str_replace(' ','-',$fileName2);
                    $fileName2 = str_replace($replaceFileName,'',$fileName2);
				
				$bolFilesCount = count($_FILES['bol_d']['name']);
				if($bolFilesCount > 0) {  
					$bolFiles = $_FILES['bol_d'];
					$config['file_name'] = $fileName1.'-BOL-'.$fileName2; //$_FILES['bol_d']['name'];  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
					//echo '<pre>';print_r($bolFiles);
					for($i = 0; $i < $bolFilesCount; $i++){
						$_FILES['bol_d']['name']     = $bolFiles['name'][$i];
						$_FILES['bol_d']['type']     = $bolFiles['type'][$i];
						$_FILES['bol_d']['tmp_name'] = $bolFiles['tmp_name'][$i];
						$_FILES['bol_d']['error']     = $bolFiles['error'][$i];
						$_FILES['bol_d']['size']     = $bolFiles['size'][$i]; 
				
						if ($this->upload->do_upload('bol_d'))  { 
							$dataBol = $this->upload->data(); 
							$bol = $dataBol['file_name'];
							$addfile = array('did'=>$res,'type'=>'bol','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documents');
						}
					}
				}	
                /*if(!empty($_FILES['bol_d']['name'])){
                    $config['file_name'] = $fileName1.'-BOL-'.$fileName2; //$_FILES['bol_d']['name']; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('bol_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$res,'type'=>'bol','fileurl'=>$bol);
						$this->Comancontroler_model->add_data_in_table($addfile,'documents');
                    }
                } */
                $rcFilesCount = count($_FILES['rc_d']['name']);
				if($rcFilesCount > 0) {  
					$rcFiles = $_FILES['rc_d'];
					$config['file_name'] = $fileName1.'-RC-'.$fileName2; //$_FILES['rc_d']['name'];  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
					for($i = 0; $i < $rcFilesCount; $i++){
						$_FILES['rc_d']['name']     = $rcFiles['name'][$i];
						$_FILES['rc_d']['type']     = $rcFiles['type'][$i];
						$_FILES['rc_d']['tmp_name'] = $rcFiles['tmp_name'][$i];
						$_FILES['rc_d']['error']     = $rcFiles['error'][$i];
						$_FILES['rc_d']['size']     = $rcFiles['size'][$i]; 
				
						if ($this->upload->do_upload('rc_d'))  { 
							$dataRc = $this->upload->data(); 
							$bol = $dataRc['file_name'];
							$addfile = array('did'=>$res,'type'=>'rc','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documents');
						}
					}
				}
				/*if(!empty($_FILES['rc_d']['name'])){
                    $config['file_name'] = $fileName1.'-RC-'.$fileName2; //$_FILES['rc_d']['name']; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('rc_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$res,'type'=>'rc','fileurl'=>$bol);
						$this->Comancontroler_model->add_data_in_table($addfile,'documents');
                    }
                } */
				if(!empty($_FILES['gd_d']['name'])){
                    $config['file_name'] = $fileName1.'-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('gd_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$res,'type'=>'gd','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
						$this->Comancontroler_model->add_data_in_table($addfile,'documents');
                    }
                } 
                
					$this->session->set_flashdata('item', 'Dispatch add successfully.');
                    //redirect(base_url('admin/dispatch/add'));
                    redirect(base_url('admin/dispatch/update/'.$res.'#submit'));
				}
 			   
			}
	    }
      
		$id = $this->uri->segment(4);
		if($id > 0){
          $data['duplicate'] = $this->Comancontroler_model->get_data_by_id($id,'dispatch');
		} else {
          $data['duplicate'] = array();
		}
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		$data['pre_made_trips'] = $this->Comancontroler_model->get_data_by_table('pre_made_trips');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
	  
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/dispatch_add',$data);
    	$this->load->view('admin/layout/footer');
	}
 
	function uploadcsv(){
	    if(!checkPermission($this->session->userdata('permission'),'dispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    
        $data['error'] = array();
        $data['upload'] = '';
	    
	   

	    if(isset($_GET['dummy']) && $_GET['dummy']=='csv'){
	        $data = array(
				array('Dispatch ID','Vehicle','Driver','Pick Up Date','Pick Up Time','Pick Up City','Pick Up Company','Pick Up Address','Pick Up','Pickup Notes','Drop Off Date','Drop Off Time','Drop Off City','Drop Off Company','Drop Off Address','Drop Off','Driver Notes','Rate','PA Rate','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Detention','Driver Assist','Dispatch Notes','Dispatch Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Sub Invoice')
			);
			
			$fileName = "FleetDispatch_".date('Y-m-d').".csv"; 
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
	    
	    if(isset($_GET['driver-vehicle']) && $_GET['driver-vehicle']=='csv'){
	        $data = array(
				array('Driver','Vehicle')
			);
			$drivers = $vehicles = array();
			$driverInfo = $this->Comancontroler_model->get_data_by_column('status','Active','drivers','dname');
			foreach($driverInfo as $val){
			    $drivers[] = $val['dname'];
			}
			$vehicleInfo = $this->Comancontroler_model->get_data_by_column('vname !=','','vehicles','vname,vnumber');
			foreach($vehicleInfo as $val){
			    $vehicles[] = $val['vname'].' ('.$val['vnumber'].')';
			}
			
			$maxCount = max(count($drivers), count($vehicles));

            for ($i = 0; $i < $maxCount; $i++) {
                $driver = isset($drivers[$i]) ? $drivers[$i] : '';
                $vehicle = isset($vehicles[$i]) ? $vehicles[$i] : '';
                $data[] = array($driver, $vehicle);
            }

			$fileName = "Driver-Vehicle-List.csv"; 
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
			/*$csvfileCount = count($_FILES['csvfile']['name']);
			if($csvfileCount < 1){
				$this->form_validation->set_rules('csvfile', 'csv file','required');
			}*/
			
			$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
			if ($this->form_validation->run() == FALSE){}
			else
			{ 
				$config['upload_path'] = 'assets/csvfiles/';
				$config['allowed_types'] = 'csv';
				//$config['max_size']= '5000';
				$this->load->library('upload',$config);
				$this->upload->initialize($config); 
				if($this->upload->do_upload('csvfile')){ 
					$uploadData = $this->upload->data();
					$bol = $uploadData['file_name'];
					$csv_file = $uploadData['full_path'];
					$csv = array_map('str_getcsv', file($csv_file));
					
					// take bkp before upload
                    $this->downloadDbBackup();
                    //echo '<pre>';print_r($csv);die();
					foreach ($csv as $row) {
						//echo '<pre>';print_r($row);echo '</pre>';
						if($row[0]=='Dispatch ID'  || count($row) < 33) {
							continue;
						}
						
						$did = $row[0];
						$vehicle = $row[1];
						$driver = $row[2];
						$pudate = str_replace('-','/',$row[3]);
                        $pdate = DateTime::createFromFormat('m/d/Y', $pudate);
                        if ($pdate !== false) { $pudate = $pdate->format('Y-m-d'); } 
                        else { $pudate = ''; }

						$dodate = $row[10];
						if($dodate != '') {
							$ddate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$dodate));
                            if ($ddate !== false) { $dodate = $ddate->format('Y-m-d'); } 
                            else { $dodate = ''; }
						}
						$check_company = $row[19];
						$check_pcity = $row[5];
						$check_plocation = $row[6];
						$check_paddress = $row[7];
						$check_dcity = $row[12];
						$check_dlocation = $row[13];
						$check_daddress = $row[14];
						$paddressid = $daddressid = 0;
						$tracking = $row[21];
						
						$isPickAddress = $this->isAddressExist($pudate,$check_pcity,$check_plocation,$check_paddress,'yes');
						if(is_numeric($isPickAddress)){ $paddressid = $isPickAddress; }
						elseif($isPickAddress){
							$addr = $check_plocation.' '.$check_paddress.' '.$check_pcity;
							$data['error'][] = 'Dispatch ID '.$did.' pick up address ('.$addr.') not exist.';
							continue;
						}
						
						$isDropAddress = $this->isAddressExist($pudate,$check_dcity,$check_dlocation,$check_daddress,'yes');
						if(is_numeric($isDropAddress)){ $daddressid = $isDropAddress; }
						elseif($isDropAddress){
							$addr = $check_dlocation.' '.$check_daddress.' '.$check_dcity;
							$data['error'][] = 'Dispatch ID '.$did.' drop off address ('.$addr.') not exist.';
							continue;
						}
						
						if($driver=='') {
							$data['error'][] = 'Dispatch ID '.$did.' driver should not blank.';
							continue;
						}
						$driver = trim($driver);
						$driverInfo = $this->Comancontroler_model->check_value_in_table('dname',$driver,'drivers','id,status');
						if(empty($driverInfo) || count($driverInfo) == '0' || count($driverInfo) > 1){
							$data['error'][] = 'Dispatch ID '.$did.' driver ('.$driver.') is not exist or this name must match with existing driver name.';
							continue;
						}
						elseif($driverInfo[0]['status'] != 'Active') {
							$data['error'][] = 'Dispatch ID '.$did.' driver ('.$driver.') is deactive by admin.';
							continue;
						}
						$driver = $driverInfo[0]['id'];
						
						if($pudate=='') {
							$data['error'][] = 'Dispatch ID '.$did.' pickup date should not blank.';
							continue;
						}
						
						$invoiceType = trim($row[26]);
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }
						
						$pattern = '/^\d{2}[\/-]\d{2}[\/-]\d{4}$/';

						if($invoiceType != '' && (!preg_match($pattern, trim($row[33])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice ready date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if(trim($row[34]) != '' && (!preg_match($pattern, trim($row[34])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice paid date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if(trim($row[35]) != '' && (!preg_match($pattern, trim($row[35])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice close date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if($invoiceType != '' && $invoiceType != 'RTS' && trim($row[36]) == ''){
        			        $data['error'][] = 'Dispatch ID '.$did.' invoice description is required.';
							continue;
        			    }
						
						
						// check vehicle 
						$vehicleArray = explode('(',$vehicle);
						$vehicleName = trim($vehicleArray[0]);
						$vehicleInfo = $this->Comancontroler_model->check_value_in_table('vname',$vehicleName,'vehicles');
						if(count($vehicleInfo) != '1'){ $vehicle = ''; }
						else { $vehicle = $vehicleInfo[0]['id']; }
						
						if($check_company=='' || $check_pcity=='' || $check_dcity=='' || $check_plocation=='' || $check_dlocation=='' || $tracking=='' || $vehicle=='') {
							$data['error'][] = 'Dispatch ID '.$did.' please fill all required fields.';
							continue;
						}
						
						// generate invoice
						$inv_first = '';
						$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate);
						$driver_info = $this->Comancontroler_model->get_data_by_id($driver,'drivers','dcode');
						if(!empty($driver_info)) {
							$inv_first = $driver_info[0]['dcode'];
						}
						$inv_middel = date('mdy',strtotime($pudate));
						$invoice = $inv_first.''.$inv_middel.'';
						if($invoice == '' || $inv_first == ''){
							$set_message = 'Invoice number must not empty.';
							if($inv_first == ''){ $set_message = 'Driver code is empty.'; }
							$data['error'][] = 'Dispatch ID '.$did.' invoice issue: '.$set_message.'.';
							continue;
						}
						
						if(is_numeric($row[0]) && $row[0] > 1) {
							
							if(stristr($row[22],$inv_first.''.$inv_middel)) {
								$invoice = $row[22];
							}
							elseif(strtotime($pudate) < strtotime('2024-04-25')){
								$invoice = $row[22];
								$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatch','id');
								if(count($invoiceInfo) > 1 || (count($invoiceInfo) == 1 && $invoiceInfo[0]['id']!=$id)){
									$data['error'][] = 'Dispatch ID '.$did.' this invoice number is already exist.';
									continue;
								}
							}
							else {
								$invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
							}
						} else {
							$invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
						}
						// generate invoice end ymd
						
						$company = $this->check_company($check_company);
						$companyInfo = $this->Comancontroler_model->get_data_by_id($company,'companies','paymenTerms,payoutRate,dayToPay');
						$invoiceType = $row[26]; //$companyInfo[0]['paymenTerms'];
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }
						
						$dcity = $this->check_city($check_dcity);
						$pcity = $this->check_city($check_pcity);
						$plocation = $this->check_location($check_plocation);
						$dlocation = $this->check_location($check_dlocation);
						
						$parate = str_replace('$','',$row[18]);
						if(!is_numeric($parate)) { $parate = 0; }
						$rate = str_replace('$','',$row[17]);
						if(!is_numeric($rate)) { $rate = 0; }
						
						$payoutAmount = $row[24];
						
						$dispatchMeta = array('expense'=>array(),'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoicePaidDate'=>'','invoiceCloseDate'=>'');
						$dispatchMetaJson = json_encode($dispatchMeta);
						
						//array('0.Dispatch ID','1.Vehicle','2.Driver','3.Pick Up Date','4.Pick Up Time','5.Pick Up City','6.Pick Up Company','7.Pick Up Address','8.Pick Up','9.Pickup Notes','10.Drop Off Date','11.Drop Off Time','12.Drop Off City','13.Drop Off Company','14.Drop Off Address','15.Drop Off','16.Driver Notes','17.Rate','18.PA Rate','19.Company','20.Trailer','21.Tracking','22.Invoice','23.Week','24.Payout Amount','25.Invoice Date','26.Invoice Type','27.Expected Pay Date','28.Detention','29.Driver Assist','30.Status','31.Driver Status','32.Notes')
						
						$week = date('M',strtotime($pudate)).' W';
						$day = date('d',strtotime($pudate));
						if($day < 9) { $w = '1'; }
						elseif($day < 16){ $w = '2'; }
						elseif($day < 24){ $w = '3'; }
						else { $w = '4'; }
						$week .= $w; 
							
						$insert_data = array(
							'driver'=>$driver,
							'vehicle'=>$vehicle,
							'pudate'=>$pudate,
							'pcity'=>$pcity,
							'dcity'=>$dcity,
							'dodate'=>$dodate,
							'rate'=>$rate,
							'parate'=>$parate,
							'company'=>$company,
							'dlocation'=>$dlocation,
							'plocation'=>$plocation,
							'dcode'=>$row[15],
							'pcode'=>$row[8],
							'paddress'=>$row[7],
							'daddress'=>$row[14],
							'trailer'=>$row[20],
							'tracking'=>$row[21],
							'invoice'=>$invoice,
							'payoutAmount'=>$payoutAmount,
							'invoiceType'=>$invoiceType,
							'dWeek'=>$week, //$row[23],
							'ptime'=>$row[4],
							'dtime'=>$row[11],
							'notes'=>$row[32],
							'pnotes'=>$row[9],
							'dnotes'=>$row[16],
							'invoiceNotes'=>$row[36],
							'driver_status'=>$row[31],
							'status'=>$row[30]
						);
						
						if(is_numeric($row[0]) && $row[0] > 1) { // update
							if($row[25] != 'TBD' && $row[25] != ''){
								//$invoiceDate = date('Y-m-d',strtotime($row[25]));
								$idate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[25]));
                                if ($idate !== false) { 
                                    $invoiceDate = $idate->format('Y-m-d'); 
                                    $insert_data['invoiceDate'] = $invoiceDate;
                                    
                                    // update expect pay date
                                    if($invoiceType == 'RTS'){ $iDays = '+ 3 days'; }
            					    elseif($invoiceType == 'Direct Bill'){ $iDays = "+ 30 days"; }
            					    elseif($invoiceType == 'Quick Pay'){ $iDays = "+ 7 days"; }
                                    else { $iDays = '+ 1 month'; }
                                    $expectPayDate = date('Y-m-d',strtotime($iDays,strtotime($invoiceDate)));
                                    $insert_data['expectPayDate'] = $expectPayDate;
                                }
							}
							/*if($row[27] != 'TBD' && $row[27] != ''){
								//$expectPayDate = date('Y-m-d',strtotime($row[27]));
								$epdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[27]));
                                if ($epdate !== false) { 
                                    $expectPayDate = $epdate->format('Y-m-d'); 
                                    $insert_data['expectPayDate'] = $expectPayDate;
                                }
							}*/
							
							$changeField = array();
							$getDispatch = $this->Comancontroler_model->get_data_by_column('id',$row[0],'dispatch');
							if($getDispatch[0]['dispatchMeta'] != '') {
								$currentDiMeta = json_decode($getDispatch[0]['dispatchMeta'],true);
								$currentDiMeta['invoiceReadyDate'] = $currentDiMeta['invoicePaidDate'] = $currentDiMeta['invoiceCloseDate'] = '';
								$currentDiMeta['invoiceReady'] = $currentDiMeta['invoicePaid'] = $currentDiMeta['invoiceClose'] = $currentDiMeta['invoiced'] = '0';
								if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){ // invoice date 
									$currentDiMeta['invoiced'] = '1';
								}
								if(trim($row[33]) != ''){ // invoice ready date 
									$irdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[33]));
									if ($irdate !== false) { 
										$invoiceReadyDate = $irdate->format('Y-m-d'); 
										$currentDiMeta['invoiceReadyDate'] = $invoiceReadyDate;
										$currentDiMeta['invoiceReady'] = '1';
									}
								}
								if(trim($row[34]) != ''){ // invoice paid date 
									$ipdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[34]));
									if ($ipdate !== false) { 
										$invoicePaidDate = $ipdate->format('Y-m-d'); 
										$currentDiMeta['invoicePaidDate'] = $invoicePaidDate;
										$currentDiMeta['invoicePaid'] = '1';
									}
								}
								if(trim($row[35]) != ''){ // invoice closed date 
									$icdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[35]));
									if ($icdate !== false) { 
										$invoiceCloseDate = $icdate->format('Y-m-d'); 
										$currentDiMeta['invoiceCloseDate'] = $invoiceCloseDate;
										$currentDiMeta['invoiceClose'] = '1';
									}
								}
								$dispatchMetaJson = json_encode($currentDiMeta);
								$insert_data['dispatchMeta'] = $dispatchMetaJson;
								
								if($getDispatch[0]['dispatchMeta'] != ''){
									$diMeta = json_decode($getDispatch[0]['dispatchMeta'],true);
									if($diMeta['invoiced'] != $currentDiMeta['invoiced']) { $changeField[] = array('Invoiced','invoiced',$diMeta['invoiced'],$currentDiMeta['invoiced']); }
									if($diMeta['invoicePaid'] != $currentDiMeta['invoicePaid']) { $changeField[] = array('Invoice Paid','invoicePaid',$diMeta['invoicePaid'],$currentDiMeta['invoicePaid']); }
									if($diMeta['invoicePaidDate'] != $currentDiMeta['invoicePaidDate']) { $changeField[] = array('Invoice Paid Date','invoicePaidDate',$diMeta['invoicePaidDate'],$currentDiMeta['invoicePaidDate']); }
									if($diMeta['invoiceClose'] != $currentDiMeta['invoiceClose']) { $changeField[] = array('Invoice Closed','invoiceClose',$diMeta['invoiceClose'],$currentDiMeta['invoiceClose']); }
									if($diMeta['invoiceCloseDate'] != $currentDiMeta['invoiceCloseDate']) { $changeField[] = array('Invoice Closed Date','invoiceCloseDate',$diMeta['invoiceCloseDate'],$currentDiMeta['invoiceCloseDate']); }
									if($diMeta['invoiceReady'] != $currentDiMeta['invoiceReady']) { $changeField[] = array('Ready to submit','invoiceReady',$diMeta['invoiceReady'],$currentDiMeta['invoiceReady']); }
									if($diMeta['invoiceReadyDate'] != $currentDiMeta['invoiceReadyDate']) { $changeField[] = array('Ready To Submit Date','invoiceReadyDate',$diMeta['invoiceReadyDate'],$currentDiMeta['invoiceReadyDate']); }
								}
							}
							
								
							if($getDispatch){
								foreach($getDispatch as $di){
								    
									//// update data in sub invoice 
									if($di['childInvoice'] != '') {
										$ciNewArray = explode(',',$di['childInvoice']);
										foreach($ciNewArray as $subInv){
											if(trim($subInv) == ''){ continue; }
											$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice',$subInv,'dispatch','id,dispatchMeta');
											if(empty($getSubDispatch)){ continue; }
											$subInvArr = array();
											if($getSubDispatch[0]['dispatchMeta'] == '') {
												$subDiMeta = array('expense'=>array(),'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoicePaidDate'=>'','invoiceCloseDate'=>'');
											} else {
												$subDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'],true);
											}
											if(is_array($currentDiMeta)){
												$subDiMeta['invoiceReadyDate'] = $currentDiMeta['invoiceReadyDate'];
												$subDiMeta['invoicePaidDate'] = $currentDiMeta['invoicePaidDate'];
												$subDiMeta['invoiceCloseDate'] = $currentDiMeta['invoiceCloseDate'];
												$subDiMeta['invoiceReady'] = $currentDiMeta['invoiceReady'];
												$subDiMeta['invoicePaid'] = $currentDiMeta['invoicePaid'];
												$subDiMeta['invoiceClose'] = $currentDiMeta['invoiceClose'];
												$subDiMeta['invoiced'] = $currentDiMeta['invoiced'];
											}
											
											$subDiMetaJson = json_encode($subDiMeta);
											$subInvArr['dispatchMeta'] = $subDiMetaJson;
											
											if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){
												$subInvArr['invoiceDate'] = $insert_data['invoiceDate'];
												$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
											}
											$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
											$subInvArr['invoiceType'] = $insert_data['invoiceType'];
											$subInvArr['status'] = $insert_data['status'].' - Linked to '.$insert_data['invoice'];
											
											if($getSubDispatch[0]['id'] > 0) {
												$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'],'dispatch',$subInvArr);
											}
										}
									}
								
									if($di['driver'] != $insert_data['driver']) { $changeField[] = array('Driver','driver',$di['driver'],$insert_data['driver']); }
									if($di['vehicle'] != $insert_data['vehicle']) { $changeField[] = array('Vehicle','vehicle',$di['vehicle'],$insert_data['vehicle']); }
									if($di['pudate'] != $insert_data['pudate']) { $changeField[] = array('Pickup Date','pudate',$di['pudate'],$insert_data['pudate']); }
									if($di['ptime'] != $insert_data['ptime']) { $changeField[] = array('Pickup Time','ptime',$di['ptime'],$insert_data['ptime']); }
									if($di['plocation'] != $insert_data['plocation']) { $changeField[] = array('Pickup Location','plocation',$di['plocation'],$insert_data['plocation']); }
									if($di['pcity'] != $insert_data['pcity']) { $changeField[] = array('Pickup City','pcity',$di['pcity'],$insert_data['pcity']); }
									if($di['paddress'] != $insert_data['paddress']) { $changeField[] = array('Pickup Address','paddress',$di['paddress'],$insert_data['paddress']); }
									if($di['pcode'] != $insert_data['pcode']) { $changeField[] = array('Pickup','pcode',$di['pcode'],$insert_data['pcode']); }
									if($di['pnotes'] != $insert_data['pnotes']) { $changeField[] = array('Pickup Notes','pnotes',$di['pnotes'],$insert_data['pnotes']); }
									if($di['dodate'] != $insert_data['dodate'] && $insert_data['dodate'] != '') { $changeField[] = array('Drop Off Date','dodate',$di['dodate'],$insert_data['dodate']); }
									if($di['dtime'] != $insert_data['dtime']) { $changeField[] = array('Drop Off Time','dtime',$di['dtime'],$insert_data['dtime']); }
									if($di['dlocation'] != $insert_data['dlocation']) { $changeField[] = array('Drop Off Location','dlocation',$di['dlocation'],$insert_data['dlocation']); }
									if($di['dcity'] != $insert_data['dcity']) { $changeField[] = array('Drop Off City','dcity',$di['dcity'],$insert_data['dcity']); }
									if($di['daddress'] != $insert_data['daddress']) { $changeField[] = array('Drop Off Address','daddress',$di['daddress'],$insert_data['daddress']); }
									if($di['dcode'] != $insert_data['dcode']) { $changeField[] = array('Drop Off','dcode',$di['dcode'],$insert_data['dcode']); }
									if($di['dnotes'] != $insert_data['dnotes']) { $changeField[] = array('Drop Off Notes','dnotes',$di['dnotes'],$insert_data['dnotes']); }
									if($di['company'] != $insert_data['company']) { $changeField[] = array('Company','company',$di['company'],$insert_data['company']); }
									if($di['rate'] != $insert_data['rate']) { $changeField[] = array('Rate','rate',$di['rate'],$insert_data['rate']); }
									if($di['parate'] != $insert_data['parate']) { $changeField[] = array('PA Rate','parate',$di['parate'],$insert_data['parate']); }
									if($di['trailer'] != $insert_data['trailer']) { $changeField[] = array('Trailer','trailer',$di['trailer'],$insert_data['trailer']); }
									if($di['tracking'] != $insert_data['tracking']) { $changeField[] = array('Tracking','tracking',$di['tracking'],$insert_data['tracking']); }
									if($di['invoice'] != $insert_data['invoice']) { $changeField[] = array('Invoice','invoice',$di['invoice'],$insert_data['invoice']); }
									if($di['invoiceType'] != $insert_data['invoiceType']) { $changeField[] = array('Invoice Type','invoiceType',$di['invoiceType'],$insert_data['invoiceType']); }
									if($di['payoutAmount'] != $insert_data['payoutAmount']) { $changeField[] = array('Payout Amount','payoutAmount',$di['payoutAmount'],$insert_data['payoutAmount']); }
									if($di['dWeek'] != $insert_data['dWeek']) { $changeField[] = array('Week','dWeek',$di['dWeek'],$insert_data['dWeek']); }
									if($di['notes'] != $insert_data['notes']) { $changeField[] = array('Notes','notes',$di['notes'],$insert_data['notes']); }
									if($di['invoiceNotes'] != $insert_data['invoiceNotes']) { $changeField[] = array('Invoice Description','invoiceNotes',$di['invoiceNotes'],$insert_data['invoiceNotes']); }
									if($di['driver_status'] != $insert_data['driver_status']) { $changeField[] = array('Driver Status','driver_status',$di['driver_status'],$insert_data['driver_status']); }
									if($di['status'] != $insert_data['status']) { $changeField[] = array('Status','status',$di['status'],$insert_data['status']); }
									
									if($row[25] != 'TBD' && $row[25] != ''){
										if($di['invoiceDate'] != $insert_data['invoiceDate']) { 
											$changeField[] = array('Invoice Date','invoiceDate',$di['invoiceDate'],$insert_data['invoiceDate']);
											$changeField[] = array('Expect Pay Date','expectPayDate',$di['expectPayDate'],$insert_data['expectPayDate']);
										}
									}
								}
							}
							if($changeField) {
								$userid = $this->session->userdata('logged');
								$changeFieldJson = json_encode($changeField);
								$dispatchLog = array('did'=>$row[0],'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'type'=>'CSV ','rDate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($dispatchLog,'dispatchLog'); 
							}
							
							$res = $this->Comancontroler_model->update_table_by_id($row[0],'dispatch',$insert_data); 
							
						} else { // add new
							$week = date('M',strtotime($pudate)).' W';
							$day = date('d',strtotime($pudate));
							if($day < 9) { $w = '1'; }
							elseif($day < 16){ $w = '2'; }
							elseif($day < 24){ $w = '3'; }
							else { $w = '4'; }
							$week .= $w; 
							$insert_data['dWeek'] = $week; 
							
							$payoutRate = $companyInfo[0]['payoutRate'];
							if(!is_numeric($payoutRate)) { $payoutRate = 0; }
							$payoutAmount = $payoutRate * $parate;
							$payoutAmount = round($payoutAmount,2);
							$insert_data['payoutAmount'] = $payoutAmount;
							
							$insert_data['dispatchMeta'] = $dispatchMetaJson; 
							
							$insert_data['paddressid'] = $paddressid; 
							$insert_data['daddressid'] = $daddressid; 
							
							$res = $this->Comancontroler_model->add_data_in_table($insert_data,'dispatch'); 
							if($res){
								// *********** insert data in driver trip table *****
								$check_trip = $this->Comancontroler_model->check_dirver_trip($driver,$pudate);
								if(empty($check_trip)) {
									$trip1 = $res.','.$plocation.','.$dlocation.','.$pcity.','.$dcity;
									$driver_trip = array('tripdate'=>$pudate,'rate'=>'250','trip1'=>$trip1,'driver'=>$driver,'spend_amt'=>'0','total_hour'=>'0','total_amt'=>'0','rdate'=>date('Y-m-d H:i:s'));
									$this->Comancontroler_model->add_data_in_table($driver_trip,'driver_trips');
								} else { 
									if($check_trip[0]['trip2']=='') { $trip = 'trip2'; }
									elseif($check_trip[0]['trip3']=='') { $trip = 'trip3'; }
									elseif($check_trip[0]['trip4']=='') { $trip = 'trip4'; }
									else { $trip = ''; }
									 
									if($trip != '') { 
										$newtrip = $res.','.$plocation.','.$dlocation.','.$pcity.','.$dcity;
										$driver_trip = array($trip=>$newtrip);
										$this->Comancontroler_model->update_driver_trips($driver_trip,$driver,$pudate);
									}
								}
								
								// ********** add entry in finance table *********
								$fday = date('d',strtotime($pudate));
								$fyearmonth = date('Y-m',strtotime($pudate)); 
								$flday = date('t',strtotime($pudate));
								
								if($fday < 9) { $fdate = $fyearmonth.'-01'; $fweek = '01-08'; }
								elseif($fday < 16) { $fdate = $fyearmonth.'-09'; $fweek = '09-15'; }
								elseif($fday < 24) { $fdate = $fyearmonth.'-16'; $fweek = '16-23'; }
								else { $fdate = $fyearmonth.'-24'; $fweek = '24-'.$flday;  }
								$check_finance = $this->Comancontroler_model->check_finance_entry($fweek,$driver,$fdate,$vehicle);
								if(empty($check_finance)) {
									$add_finance = array('unit_pay'=>'0','driver_pay'=>'0','driver'=>$driver,'unit_id'=>$vehicle,'fweek'=>$fweek,'fdate'=>$fdate,'total_expense'=>'0','total_income'=>'0','total_amt'=>'0');
									$this->Comancontroler_model->add_data_in_table($add_finance,'finance'); 
								}
							}
						}
						$data['upload'] = 'done';
						//echo '<pre>'; print_r($insert_data);echo '</pre>';
					}
					
					unlink($csv_file);
				}
			}
		}
		
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/upload-dispatch-csv',$data);
    	$this->load->view('admin/layout/footer');
    }
    
	function uploadcsvOld(){
	    if(!checkPermission($this->session->userdata('permission'),'dispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    
        $data['error'] = array();
        $data['upload'] = '';
	    
	   

	    if(isset($_GET['dummy']) && $_GET['dummy']=='csv'){
	        $data = array(
				array('Dispatch ID','Vehicle','Driver','Pick Up Date','Pick Up Time','Pick Up City','Pick Up Company','Pick Up Address','Pick Up','Pickup Notes','Drop Off Date','Drop Off Time','Drop Off City','Drop Off Company','Drop Off Address','Drop Off','Driver Notes','Rate','PA Rate','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Detention','Driver Assist','Dispatch Notes','Dispatch Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Sub Invoice')
			);
			
			$fileName = "FleetDispatch_".date('Y-m-d').".csv"; 
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
	    
	    if(isset($_GET['driver-vehicle']) && $_GET['driver-vehicle']=='csv'){
	        $data = array(
				array('Driver','Vehicle')
			);
			$drivers = $vehicles = array();
			$driverInfo = $this->Comancontroler_model->get_data_by_column('status','Active','drivers','dname');
			foreach($driverInfo as $val){
			    $drivers[] = $val['dname'];
			}
			$vehicleInfo = $this->Comancontroler_model->get_data_by_column('vname !=','','vehicles','vname,vnumber');
			foreach($vehicleInfo as $val){
			    $vehicles[] = $val['vname'].' ('.$val['vnumber'].')';
			}
			
			$maxCount = max(count($drivers), count($vehicles));

            for ($i = 0; $i < $maxCount; $i++) {
                $driver = isset($drivers[$i]) ? $drivers[$i] : '';
                $vehicle = isset($vehicles[$i]) ? $vehicles[$i] : '';
                $data[] = array($driver, $vehicle);
            }

			$fileName = "Driver-Vehicle-List.csv"; 
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
			/*$csvfileCount = count($_FILES['csvfile']['name']);
			if($csvfileCount < 1){
				$this->form_validation->set_rules('csvfile', 'csv file','required');
			}*/
			
			$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
			if ($this->form_validation->run() == FALSE){}
			else
			{ 
				$config['upload_path'] = 'assets/csvfiles/';
				$config['allowed_types'] = 'csv';
				//$config['max_size']= '5000';
				$this->load->library('upload',$config);
				$this->upload->initialize($config); 
				if($this->upload->do_upload('csvfile')){ 
					$uploadData = $this->upload->data();
					$bol = $uploadData['file_name'];
					$csv_file = $uploadData['full_path'];
					$csv = array_map('str_getcsv', file($csv_file));
					
					// take bkp before upload
                    $this->downloadDbBackup();
                    //echo '<pre>';print_r($csv);die();
					foreach ($csv as $row) {
						//echo '<pre>';print_r($row);echo '</pre>';
						if($row[0]=='Dispatch ID'  || count($row) < 33) {
							continue;
						}
						
						$did = $row[0];
						$vehicle = $row[1];
						$driver = $row[2];
						$pudate = str_replace('-','/',$row[3]);
                        $pdate = DateTime::createFromFormat('m/d/Y', $pudate);
                        if ($pdate !== false) { $pudate = $pdate->format('Y-m-d'); } 
                        else { $pudate = ''; }

						$dodate = $row[10];
						if($dodate != '') {
							$ddate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$dodate));
                            if ($ddate !== false) { $dodate = $ddate->format('Y-m-d'); } 
                            else { $dodate = ''; }
						}
						$check_company = $row[19];
						$check_pcity = $row[5];
						$check_dcity = $row[12];
						$check_plocation = $row[6];
						$check_dlocation = $row[13];
						$tracking = $row[21];
						
						if($driver=='') {
							$data['error'][] = 'Dispatch ID '.$did.' driver should not blank.';
							continue;
						}
						$driver = trim($driver);
						$driverInfo = $this->Comancontroler_model->check_value_in_table('dname',$driver,'drivers','id,status');
						if(empty($driverInfo) || count($driverInfo) == '0' || count($driverInfo) > 1){
							$data['error'][] = 'Dispatch ID '.$did.' driver ('.$driver.') is not exist or this name must match with existing driver name.';
							continue;
						}
						elseif($driverInfo[0]['status'] != 'Active') {
							$data['error'][] = 'Dispatch ID '.$did.' driver ('.$driver.') is deactive by admin.';
							continue;
						}
						$driver = $driverInfo[0]['id'];
						
						if($pudate=='') {
							$data['error'][] = 'Dispatch ID '.$did.' pickup date should not blank.';
							continue;
						}
						
						$invoiceType = trim($row[26]);
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }
						
						$pattern = '/^\d{2}[\/-]\d{2}[\/-]\d{4}$/';

						if($invoiceType != '' && (!preg_match($pattern, trim($row[33])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice ready date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if(trim($row[34]) != '' && (!preg_match($pattern, trim($row[34])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice paid date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if(trim($row[35]) != '' && (!preg_match($pattern, trim($row[35])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice close date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if($invoiceType != '' && $invoiceType != 'RTS' && trim($row[36]) == ''){
        			        $data['error'][] = 'Dispatch ID '.$did.' invoice description is required.';
							continue;
        			    }
						
						
						// check vehicle 
						$vehicleArray = explode('(',$vehicle);
						$vehicleName = trim($vehicleArray[0]);
						$vehicleInfo = $this->Comancontroler_model->check_value_in_table('vname',$vehicleName,'vehicles');
						if(count($vehicleInfo) != '1'){ $vehicle = ''; }
						else { $vehicle = $vehicleInfo[0]['id']; }
						
						if($check_company=='' || $check_pcity=='' || $check_dcity=='' || $check_plocation=='' || $check_dlocation=='' || $tracking=='' || $vehicle=='') {
							$data['error'][] = 'Dispatch ID '.$did.' please fill all required fields.';
							continue;
						}
						
						// generate invoice
						$inv_first = '';
						$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate);
						$driver_info = $this->Comancontroler_model->get_data_by_id($driver,'drivers','dcode');
						if(!empty($driver_info)) {
							$inv_first = $driver_info[0]['dcode'];
						}
						$inv_middel = date('mdy',strtotime($pudate));
						$invoice = $inv_first.''.$inv_middel.'';
						if($invoice == '' || $inv_first == ''){
							$set_message = 'Invoice number must not empty.';
							if($inv_first == ''){ $set_message = 'Driver code is empty.'; }
							$data['error'][] = 'Dispatch ID '.$did.' invoice issue: '.$set_message.'.';
							continue;
						}
						
						if(is_numeric($row[0]) && $row[0] > 1) {
							
							if(stristr($row[22],$inv_first.''.$inv_middel)) {
								$invoice = $row[22];
							}
							elseif(strtotime($pudate) < strtotime('2024-04-25')){
								$invoice = $row[22];
								$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatch','id');
								if(count($invoiceInfo) > 1 || (count($invoiceInfo) == 1 && $invoiceInfo[0]['id']!=$id)){
									$data['error'][] = 'Dispatch ID '.$did.' this invoice number is already exist.';
									continue;
								}
							}
							else {
								$invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
							}
						} else {
							$invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
						}
						// generate invoice end ymd
						
						$company = $this->check_company($check_company);
						$companyInfo = $this->Comancontroler_model->get_data_by_id($company,'companies','paymenTerms,payoutRate,dayToPay');
						$invoiceType = $row[26]; //$companyInfo[0]['paymenTerms'];
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }
						
						$dcity = $this->check_city($check_dcity);
						$pcity = $this->check_city($check_pcity);
						$plocation = $this->check_location($check_plocation);
						$dlocation = $this->check_location($check_dlocation);
						
						$parate = str_replace('$','',$row[18]);
						if(!is_numeric($parate)) { $parate = 0; }
						$rate = str_replace('$','',$row[17]);
						if(!is_numeric($rate)) { $rate = 0; }
						
						$payoutAmount = $row[24];
						
						$dispatchMeta = array('expense'=>array(),'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoicePaidDate'=>'','invoiceCloseDate'=>'');
						$dispatchMetaJson = json_encode($dispatchMeta);
						
						//array('0.Dispatch ID','1.Vehicle','2.Driver','3.Pick Up Date','4.Pick Up Time','5.Pick Up City','6.Pick Up Company','7.Pick Up Address','8.Pick Up','9.Pickup Notes','10.Drop Off Date','11.Drop Off Time','12.Drop Off City','13.Drop Off Company','14.Drop Off Address','15.Drop Off','16.Driver Notes','17.Rate','18.PA Rate','19.Company','20.Trailer','21.Tracking','22.Invoice','23.Week','24.Payout Amount','25.Invoice Date','26.Invoice Type','27.Expected Pay Date','28.Detention','29.Driver Assist','30.Status','31.Driver Status','32.Notes')
						
						$week = date('M',strtotime($pudate)).' W';
						$day = date('d',strtotime($pudate));
						if($day < 9) { $w = '1'; }
						elseif($day < 16){ $w = '2'; }
						elseif($day < 24){ $w = '3'; }
						else { $w = '4'; }
						$week .= $w; 
							
						$insert_data = array(
							'driver'=>$driver,
							'vehicle'=>$vehicle,
							'pudate'=>$pudate,
							'pcity'=>$pcity,
							'dcity'=>$dcity,
							'dodate'=>$dodate,
							'rate'=>$rate,
							'parate'=>$parate,
							'company'=>$company,
							'dlocation'=>$dlocation,
							'plocation'=>$plocation,
							'dcode'=>$row[15],
							'pcode'=>$row[8],
							'paddress'=>$row[7],
							'daddress'=>$row[14],
							'trailer'=>$row[20],
							'tracking'=>$row[21],
							'invoice'=>$invoice,
							'payoutAmount'=>$payoutAmount,
							'invoiceType'=>$invoiceType,
							'dWeek'=>$week, //$row[23],
							'ptime'=>$row[4],
							'dtime'=>$row[11],
							'notes'=>$row[32],
							'pnotes'=>$row[9],
							'dnotes'=>$row[16],
							'invoiceNotes'=>$row[36],
							'driver_status'=>$row[31],
							'status'=>$row[30]
						);
						
						if(is_numeric($row[0]) && $row[0] > 1) { // update
							if($row[25] != 'TBD' && $row[25] != ''){
								//$invoiceDate = date('Y-m-d',strtotime($row[25]));
								$idate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[25]));
                                if ($idate !== false) { 
                                    $invoiceDate = $idate->format('Y-m-d'); 
                                    $insert_data['invoiceDate'] = $invoiceDate;
                                    
                                    // update expect pay date
                                    if($invoiceType == 'RTS'){ $iDays = '+ 3 days'; }
            					    elseif($invoiceType == 'Direct Bill'){ $iDays = "+ 30 days"; }
            					    elseif($invoiceType == 'Quick Pay'){ $iDays = "+ 7 days"; }
                                    else { $iDays = '+ 1 month'; }
                                    $expectPayDate = date('Y-m-d',strtotime($iDays,strtotime($invoiceDate)));
                                    $insert_data['expectPayDate'] = $expectPayDate;
                                }
							}
							/*if($row[27] != 'TBD' && $row[27] != ''){
								//$expectPayDate = date('Y-m-d',strtotime($row[27]));
								$epdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[27]));
                                if ($epdate !== false) { 
                                    $expectPayDate = $epdate->format('Y-m-d'); 
                                    $insert_data['expectPayDate'] = $expectPayDate;
                                }
							}*/
							
							$changeField = array();
							$getDispatch = $this->Comancontroler_model->get_data_by_column('id',$row[0],'dispatch');
							if($getDispatch[0]['dispatchMeta'] != '') {
								$currentDiMeta = json_decode($getDispatch[0]['dispatchMeta'],true);
								$currentDiMeta['invoiceReadyDate'] = $currentDiMeta['invoicePaidDate'] = $currentDiMeta['invoiceCloseDate'] = '';
								$currentDiMeta['invoiceReady'] = $currentDiMeta['invoicePaid'] = $currentDiMeta['invoiceClose'] = $currentDiMeta['invoiced'] = '0';
								if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){ // invoice date 
									$currentDiMeta['invoiced'] = '1';
								}
								if(trim($row[33]) != ''){ // invoice ready date 
									$irdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[33]));
									if ($irdate !== false) { 
										$invoiceReadyDate = $irdate->format('Y-m-d'); 
										$currentDiMeta['invoiceReadyDate'] = $invoiceReadyDate;
										$currentDiMeta['invoiceReady'] = '1';
									}
								}
								if(trim($row[34]) != ''){ // invoice paid date 
									$ipdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[34]));
									if ($ipdate !== false) { 
										$invoicePaidDate = $ipdate->format('Y-m-d'); 
										$currentDiMeta['invoicePaidDate'] = $invoicePaidDate;
										$currentDiMeta['invoicePaid'] = '1';
									}
								}
								if(trim($row[35]) != ''){ // invoice closed date 
									$icdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[35]));
									if ($icdate !== false) { 
										$invoiceCloseDate = $icdate->format('Y-m-d'); 
										$currentDiMeta['invoiceCloseDate'] = $invoiceCloseDate;
										$currentDiMeta['invoiceClose'] = '1';
									}
								}
								$dispatchMetaJson = json_encode($currentDiMeta);
								$insert_data['dispatchMeta'] = $dispatchMetaJson;
								
								if($getDispatch[0]['dispatchMeta'] != ''){
									$diMeta = json_decode($getDispatch[0]['dispatchMeta'],true);
									if($diMeta['invoiced'] != $currentDiMeta['invoiced']) { $changeField[] = array('Invoiced','invoiced',$diMeta['invoiced'],$currentDiMeta['invoiced']); }
									if($diMeta['invoicePaid'] != $currentDiMeta['invoicePaid']) { $changeField[] = array('Invoice Paid','invoicePaid',$diMeta['invoicePaid'],$currentDiMeta['invoicePaid']); }
									if($diMeta['invoicePaidDate'] != $currentDiMeta['invoicePaidDate']) { $changeField[] = array('Invoice Paid Date','invoicePaidDate',$diMeta['invoicePaidDate'],$currentDiMeta['invoicePaidDate']); }
									if($diMeta['invoiceClose'] != $currentDiMeta['invoiceClose']) { $changeField[] = array('Invoice Closed','invoiceClose',$diMeta['invoiceClose'],$currentDiMeta['invoiceClose']); }
									if($diMeta['invoiceCloseDate'] != $currentDiMeta['invoiceCloseDate']) { $changeField[] = array('Invoice Closed Date','invoiceCloseDate',$diMeta['invoiceCloseDate'],$currentDiMeta['invoiceCloseDate']); }
									if($diMeta['invoiceReady'] != $currentDiMeta['invoiceReady']) { $changeField[] = array('Ready to submit','invoiceReady',$diMeta['invoiceReady'],$currentDiMeta['invoiceReady']); }
									if($diMeta['invoiceReadyDate'] != $currentDiMeta['invoiceReadyDate']) { $changeField[] = array('Ready To Submit Date','invoiceReadyDate',$diMeta['invoiceReadyDate'],$currentDiMeta['invoiceReadyDate']); }
								}
							}
							
								
							if($getDispatch){
								foreach($getDispatch as $di){
								    
									//// update data in sub invoice 
									if($di['childInvoice'] != '') {
										$ciNewArray = explode(',',$di['childInvoice']);
										foreach($ciNewArray as $subInv){
											if(trim($subInv) == ''){ continue; }
											$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice',$subInv,'dispatch','id,dispatchMeta');
											if(empty($getSubDispatch)){ continue; }
											$subInvArr = array();
											if($getSubDispatch[0]['dispatchMeta'] == '') {
												$subDiMeta = array('expense'=>array(),'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoicePaidDate'=>'','invoiceCloseDate'=>'');
											} else {
												$subDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'],true);
											}
											if(is_array($currentDiMeta)){
												$subDiMeta['invoiceReadyDate'] = $currentDiMeta['invoiceReadyDate'];
												$subDiMeta['invoicePaidDate'] = $currentDiMeta['invoicePaidDate'];
												$subDiMeta['invoiceCloseDate'] = $currentDiMeta['invoiceCloseDate'];
												$subDiMeta['invoiceReady'] = $currentDiMeta['invoiceReady'];
												$subDiMeta['invoicePaid'] = $currentDiMeta['invoicePaid'];
												$subDiMeta['invoiceClose'] = $currentDiMeta['invoiceClose'];
												$subDiMeta['invoiced'] = $currentDiMeta['invoiced'];
											}
											
											$subDiMetaJson = json_encode($subDiMeta);
											$subInvArr['dispatchMeta'] = $subDiMetaJson;
											
											if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){
												$subInvArr['invoiceDate'] = $insert_data['invoiceDate'];
												$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
											}
											$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
											$subInvArr['invoiceType'] = $insert_data['invoiceType'];
											$subInvArr['status'] = $insert_data['status'].' - Linked to '.$insert_data['invoice'];
											
											if($getSubDispatch[0]['id'] > 0) {
												$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'],'dispatch',$subInvArr);
											}
										}
									}
								
									if($di['driver'] != $insert_data['driver']) { $changeField[] = array('Driver','driver',$di['driver'],$insert_data['driver']); }
									if($di['vehicle'] != $insert_data['vehicle']) { $changeField[] = array('Vehicle','vehicle',$di['vehicle'],$insert_data['vehicle']); }
									if($di['pudate'] != $insert_data['pudate']) { $changeField[] = array('Pickup Date','pudate',$di['pudate'],$insert_data['pudate']); }
									if($di['ptime'] != $insert_data['ptime']) { $changeField[] = array('Pickup Time','ptime',$di['ptime'],$insert_data['ptime']); }
									if($di['plocation'] != $insert_data['plocation']) { $changeField[] = array('Pickup Location','plocation',$di['plocation'],$insert_data['plocation']); }
									if($di['pcity'] != $insert_data['pcity']) { $changeField[] = array('Pickup City','pcity',$di['pcity'],$insert_data['pcity']); }
									if($di['paddress'] != $insert_data['paddress']) { $changeField[] = array('Pickup Address','paddress',$di['paddress'],$insert_data['paddress']); }
									if($di['pcode'] != $insert_data['pcode']) { $changeField[] = array('Pickup','pcode',$di['pcode'],$insert_data['pcode']); }
									if($di['pnotes'] != $insert_data['pnotes']) { $changeField[] = array('Pickup Notes','pnotes',$di['pnotes'],$insert_data['pnotes']); }
									if($di['dodate'] != $insert_data['dodate'] && $insert_data['dodate'] != '') { $changeField[] = array('Drop Off Date','dodate',$di['dodate'],$insert_data['dodate']); }
									if($di['dtime'] != $insert_data['dtime']) { $changeField[] = array('Drop Off Time','dtime',$di['dtime'],$insert_data['dtime']); }
									if($di['dlocation'] != $insert_data['dlocation']) { $changeField[] = array('Drop Off Location','dlocation',$di['dlocation'],$insert_data['dlocation']); }
									if($di['dcity'] != $insert_data['dcity']) { $changeField[] = array('Drop Off City','dcity',$di['dcity'],$insert_data['dcity']); }
									if($di['daddress'] != $insert_data['daddress']) { $changeField[] = array('Drop Off Address','daddress',$di['daddress'],$insert_data['daddress']); }
									if($di['dcode'] != $insert_data['dcode']) { $changeField[] = array('Drop Off','dcode',$di['dcode'],$insert_data['dcode']); }
									if($di['dnotes'] != $insert_data['dnotes']) { $changeField[] = array('Drop Off Notes','dnotes',$di['dnotes'],$insert_data['dnotes']); }
									if($di['company'] != $insert_data['company']) { $changeField[] = array('Company','company',$di['company'],$insert_data['company']); }
									if($di['rate'] != $insert_data['rate']) { $changeField[] = array('Rate','rate',$di['rate'],$insert_data['rate']); }
									if($di['parate'] != $insert_data['parate']) { $changeField[] = array('PA Rate','parate',$di['parate'],$insert_data['parate']); }
									if($di['trailer'] != $insert_data['trailer']) { $changeField[] = array('Trailer','trailer',$di['trailer'],$insert_data['trailer']); }
									if($di['tracking'] != $insert_data['tracking']) { $changeField[] = array('Tracking','tracking',$di['tracking'],$insert_data['tracking']); }
									if($di['invoice'] != $insert_data['invoice']) { $changeField[] = array('Invoice','invoice',$di['invoice'],$insert_data['invoice']); }
									if($di['invoiceType'] != $insert_data['invoiceType']) { $changeField[] = array('Invoice Type','invoiceType',$di['invoiceType'],$insert_data['invoiceType']); }
									if($di['payoutAmount'] != $insert_data['payoutAmount']) { $changeField[] = array('Payout Amount','payoutAmount',$di['payoutAmount'],$insert_data['payoutAmount']); }
									if($di['dWeek'] != $insert_data['dWeek']) { $changeField[] = array('Week','dWeek',$di['dWeek'],$insert_data['dWeek']); }
									if($di['notes'] != $insert_data['notes']) { $changeField[] = array('Notes','notes',$di['notes'],$insert_data['notes']); }
									if($di['invoiceNotes'] != $insert_data['invoiceNotes']) { $changeField[] = array('Invoice Description','invoiceNotes',$di['invoiceNotes'],$insert_data['invoiceNotes']); }
									if($di['driver_status'] != $insert_data['driver_status']) { $changeField[] = array('Driver Status','driver_status',$di['driver_status'],$insert_data['driver_status']); }
									if($di['status'] != $insert_data['status']) { $changeField[] = array('Status','status',$di['status'],$insert_data['status']); }
									
									if($row[25] != 'TBD' && $row[25] != ''){
										if($di['invoiceDate'] != $insert_data['invoiceDate']) { 
											$changeField[] = array('Invoice Date','invoiceDate',$di['invoiceDate'],$insert_data['invoiceDate']);
											$changeField[] = array('Expect Pay Date','expectPayDate',$di['expectPayDate'],$insert_data['expectPayDate']);
										}
									}
								}
							}
							if($changeField) {
								$userid = $this->session->userdata('logged');
								$changeFieldJson = json_encode($changeField);
								$dispatchLog = array('did'=>$row[0],'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'type'=>'CSV ','rDate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($dispatchLog,'dispatchLog'); 
							}
							
							$res = $this->Comancontroler_model->update_table_by_id($row[0],'dispatch',$insert_data); 
							
						} else { // add new
							$week = date('M',strtotime($pudate)).' W';
							$day = date('d',strtotime($pudate));
							if($day < 9) { $w = '1'; }
							elseif($day < 16){ $w = '2'; }
							elseif($day < 24){ $w = '3'; }
							else { $w = '4'; }
							$week .= $w; 
							$insert_data['dWeek'] = $week; 
							
							$payoutRate = $companyInfo[0]['payoutRate'];
							if(!is_numeric($payoutRate)) { $payoutRate = 0; }
							$payoutAmount = $payoutRate * $parate;
							$payoutAmount = round($payoutAmount,2);
							$insert_data['payoutAmount'] = $payoutAmount;
							
							$insert_data['dispatchMeta'] = $dispatchMetaJson; 
							
							$res = $this->Comancontroler_model->add_data_in_table($insert_data,'dispatch'); 
							if($res){
								// *********** insert data in driver trip table *****
								$check_trip = $this->Comancontroler_model->check_dirver_trip($driver,$pudate);
								if(empty($check_trip)) {
									$trip1 = $res.','.$plocation.','.$dlocation.','.$pcity.','.$dcity;
									$driver_trip = array('tripdate'=>$pudate,'rate'=>'250','trip1'=>$trip1,'driver'=>$driver,'spend_amt'=>'0','total_hour'=>'0','total_amt'=>'0','rdate'=>date('Y-m-d H:i:s'));
									$this->Comancontroler_model->add_data_in_table($driver_trip,'driver_trips');
								} else { 
									if($check_trip[0]['trip2']=='') { $trip = 'trip2'; }
									elseif($check_trip[0]['trip3']=='') { $trip = 'trip3'; }
									elseif($check_trip[0]['trip4']=='') { $trip = 'trip4'; }
									else { $trip = ''; }
									 
									if($trip != '') { 
										$newtrip = $res.','.$plocation.','.$dlocation.','.$pcity.','.$dcity;
										$driver_trip = array($trip=>$newtrip);
										$this->Comancontroler_model->update_driver_trips($driver_trip,$driver,$pudate);
									}
								}
								
								// ********** add entry in finance table *********
								$fday = date('d',strtotime($pudate));
								$fyearmonth = date('Y-m',strtotime($pudate)); 
								$flday = date('t',strtotime($pudate));
								
								if($fday < 9) { $fdate = $fyearmonth.'-01'; $fweek = '01-08'; }
								elseif($fday < 16) { $fdate = $fyearmonth.'-09'; $fweek = '09-15'; }
								elseif($fday < 24) { $fdate = $fyearmonth.'-16'; $fweek = '16-23'; }
								else { $fdate = $fyearmonth.'-24'; $fweek = '24-'.$flday;  }
								$check_finance = $this->Comancontroler_model->check_finance_entry($fweek,$driver,$fdate,$vehicle);
								if(empty($check_finance)) {
									$add_finance = array('unit_pay'=>'0','driver_pay'=>'0','driver'=>$driver,'unit_id'=>$vehicle,'fweek'=>$fweek,'fdate'=>$fdate,'total_expense'=>'0','total_income'=>'0','total_amt'=>'0');
									$this->Comancontroler_model->add_data_in_table($add_finance,'finance'); 
								}
							}
						}
						$data['upload'] = 'done';
						//echo '<pre>'; print_r($insert_data);echo '</pre>';
					}
					
					unlink($csv_file);
				}
			}
		}
		
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/upload-dispatch-csv',$data);
    	$this->load->view('admin/layout/footer');
    }
    
    function isAddressExist($pudate,$check_pcity,$check_plocation,$check_paddress,$returnID='no'){
		$date = '2024-08-24';
		$return = false;
		if(strtotime($pudate) >= strtotime($date) && ($check_pcity !='' || $check_plocation!='' || $check_paddress!='')){
			$picity = $pistate = '';
			if(stristr($check_pcity,', ')){
				$cityState = explode(', ',$check_pcity);
				$picity = $cityState[0];
				$pistate = $cityState[1];
			}
			if($check_pcity !='' && $check_paddress!=''){
			    $address = explode($check_pcity,$check_paddress);
			    $check_paddress = $address[0];
			}
			$pWhere = array('company'=>$check_plocation,'address'=>$check_paddress,'city'=>$picity,'state'=>$pistate);
			$pResult = $this->Comancontroler_model->get_data_by_multiple_column($pWhere,'companyAddress','id');
			if(empty($pResult)){ $return = true; }
			elseif($returnID == 'yes') { $return = $pResult[0]['id']; } 
		}
		return $return;
	}
	
    public function getAddress(){
        if($this->input->post('keyword')) {
            $keyword = $this->input->post('keyword');
            $type = $this->input->post('type');
            $address = $this->Comancontroler_model->getDataWithLike($type,$keyword,'companyAddress','*','20',$type,$order='asc');
            if($address){
                foreach($address as $val){
                    echo '<li data-id="'.$val['id'].'" data-time="'.$val['shippingHours'].'" data-city="'.$val['city'].', '.$val['state'].'" data-zip="'.$val['zip'].'" data-address="'.$val['address'].'" data-company="'.$val['company'].'">'.$val['company'].', '.$val['address'].' '.$val['city'].' '.$val['state'].' '.$val['zip'].'</li>';
                }
            } else {
                echo '<li data-city="" data-address="" data-company="">No address found</li>';
            }
        }
    }
    public function getCompanies(){
        if($this->input->post('keyword')) {
            $keyword = $this->input->post('keyword'); 
            $address = $this->Comancontroler_model->getDataWithLike('company',$keyword,'companies','*','50','company,id',$order='asc');
            if($address){
                foreach($address as $val){
                    echo '<li data-id="'.$val['id'].'" data-company="'.$val['company'].'">'.$val['company'];
                    if($val['address'] != '') { echo ' ('.$val['address'].')'; }
                    echo '</li>';
                }
            } else {
                echo '<li data-city="" data-address="" data-company="">No company found</li>';
            }
        }
    }
    
    public function downloadDbBackup() {
        
        $this->load->dbutil();
        $this->load->helper('file');
        //$this->load->helper('url');
        
        // Database backup preferences
        $prefs = array(
            'format'      => 'zip',
            'filename'    => 'backup-on-'.date('d-M-Y-h-i-A').'.sql'
        );

        // Create the database backup
        $backup = $this->dbutil->backup($prefs);

        // Set the backup file name with the current date
        $db_name = 'backup-on-'.date('d-M-Y-h-i-A').'.zip';

        // Set the path to save the backup file
        $save_path = FCPATH . 'backup/' . $db_name;

        // Write the backup file to the specified path
        if (write_file($save_path, $backup)) {
            //echo "Backup completed successfully! File saved to: " . $save_path;
        } else {
            //echo "Failed to write backup file.";
        }
    }
    
    public function download_pdf($folder, $file_name) {
        $this->load->helper('download');

        // Path to the PDF file
        $file_path = FCPATH.'assets/' . str_replace('--','/',$folder) . '/' . $file_name;

        // Force download the file
        if (file_exists($file_path)) {
            force_download($file_path, NULL);
        } else {
            show_404();
        }
    }
    
	function paysheet(){ 
	    if(!checkPermission($this->session->userdata('permission'),'paysheet')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $unit = $driver = $company = $sdate = $edate = '';
         
        if($this->input->post('search')) {
            $unit = $this->input->post('unit');
            $driver = $this->input->post('driver');
            $company = $this->input->post('company');
            
            
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
            
            
            $data['dispatch'] = $this->Comancontroler_model->get_paysheet_data($sdate,$edate,$unit,$driver,$company);
        } else {
            $data['dispatch'] = array();
        }
        
    	
	  $data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
	  $data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
	  $data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
	  $data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
	  $data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     
     $this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/paysheet',$data);
    	$this->load->view('admin/layout/footer');
 }
	
    function generateInvoice($driver_trip,$initial){
		$invoicesFromDatabase = array();
		if($driver_trip){
			foreach($driver_trip as $trip){ $invoicesFromDatabase[] = $trip['invoice']; }
		}
		$missingInvoiceNumbers = [];
		for ($i = 1; $i <= count($invoicesFromDatabase) + 1; $i++) {
			$expectedInvoiceNumber = sprintf($initial."%02d", $i);
			if (!in_array($expectedInvoiceNumber, $invoicesFromDatabase)) {
				$missingInvoiceNumbers[] = $expectedInvoiceNumber;
			}
		}
		return $missingInvoiceNumbers[0];
	}
    function check_city($city) {
    	 $city_data = $this->Comancontroler_model->get_city_by_name($city);
    	 if(empty($city_data)) {
    		 $insert_data = array('city'=>$city);
    		$res = $this->Comancontroler_model->add_data_in_table($insert_data,'cities'); 
    		return $res;
    	 } else {
    		return $city_data[0]['id']; 
    	 }
    }
    
    function check_location($location) {
    	 $company_data = $this->Comancontroler_model->get_location_by_name($location);
    	 if(empty($company_data)) {
    		 $insert_data = array('location'=>$location);
    		$res = $this->Comancontroler_model->add_data_in_table($insert_data,'locations'); 
    		return $res;
    	 } else {
    		return $company_data[0]['id']; 
    	 }
    }
     function check_company($company) {
    	 $company_data = $this->Comancontroler_model->get_data_by_name($company);
    	 if(empty($company_data)) {
    		 $insert_data = array('company'=>$company);
    		$res = $this->Comancontroler_model->add_data_in_table($insert_data,'companies'); 
    		return $res;
    	 } else {
    		return $company_data[0]['id']; 
    	 }
     }
     function removedriverfile(){
        $did = $this->uri->segment(5);
         $id = $this->uri->segment(4);
         $file = $this->Comancontroler_model->get_data_by_id($id,'driver_document');
         if(empty($file)) {
             $this->session->set_flashdata('item', 'File not exist.'); 
         } else {
    	    if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/driver/'.$file[0]['fileurl'])) {
                unlink(FCPATH.'assets/driver/'.$file[0]['fileurl']);  
                
                $this->session->set_flashdata('item', 'Document removed successfully.'); 
            }
            $this->Comancontroler_model->delete($id,'driver_document','id');
            
            if($file[0]['docs_for']=='driver') { 
                redirect(base_url('admin/driver/update/'.$did));
            } else {
                redirect(base_url('admin/vehicle/update/'.$did)); 
            }
         }
         redirect(base_url('admin/driver/update/'.$did)); 
     }
     function removefile(){
         $did = $this->uri->segment(5);
         $id = $this->uri->segment(4);
         $file = $this->Comancontroler_model->get_data_by_id($id,'documents');
         if(empty($file)) {
             $this->session->set_flashdata('item', 'File not exist.'); 
         } else {
             if($file[0]['type']=='paInvoice'){ $folder = 'paInvoice'; }
             else { $folder = 'upload'; }
    	    if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/'.$folder.'/'.$file[0]['fileurl'])) {
                unlink(FCPATH.'assets/'.$folder.'/'.$file[0]['fileurl']);  
                $this->session->set_flashdata('item', 'Document removed successfully.'); 
                
                // add history entry 
                $userid = $this->session->userdata('logged');
                $changeFieldJson = json_encode(array(array(strtoupper($file[0]['type']).' File',$file[0]['type'].'file','Removed',$file[0]['fileurl'])));
			    $dispatchLog = array('did'=>$did,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
				$this->Comancontroler_model->add_data_in_table($dispatchLog,'dispatchLog'); 
            }
            $this->Comancontroler_model->delete($id,'documents','id');
         }
         redirect(base_url('admin/dispatch/update/'.$did));
     }
     function extradispatchdelete(){
        $id = $this->uri->segment(4);
     	$result = $this->Comancontroler_model->delete($id,'dispatchExtraInfo','id'); 
     }
     function dispatchdelete(){
         $id = $this->uri->segment(4);
            // $this->Comancontroler_model->delete($id,'dispatchExtraInfo','dispatchid');
     		//$result = $this->Comancontroler_model->delete($id,'dispatch','id');
     		//if($result){
     			redirect('admin/dispatch');
     		//}
     }
     function ajaxdelete(){
          if($this->input->post('ajaxdelete'))	{
              $did = $this->input->post('deleteid'); 
              $result = $this->Comancontroler_model->delete($did,'dispatch','id');
          }
     }
    function ajaxedit(){
        if($this->input->post('did_input'))	{
		    $this->form_validation->set_rules('did_input', 'dispatch id','required|min_length[1]');
		
		    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
		    if ($this->form_validation->run() == FALSE){}
            else
            {
    			 $id = $this->input->post('did_input');
    			 $insert_data=array(
    			    'rate'=>$this->input->post('rate_input'),
    			    'parate'=>$this->input->post('parate_input'), 
    			    'trailer'=>$this->input->post('trailer_input'),
    			    'tracking'=>$this->input->post('tracking_input'),
    			    'invoice'=>$this->input->post('invoice_input'),
    			    'bol'=>$this->input->post('bol_input'),
    			    'rc'=>$this->input->post('rc_input'),
    			   // 'gd'=>$this->input->post('gd_input'),
    			    'driver_status'=>$this->input->post('driver_status_input'),
    			    'status'=>$this->input->post('status_input')
    			);
		
			    $res = $this->Comancontroler_model->update_table_by_id($id,'dispatch',$insert_data); 
    			if($res){
    				echo 'done';
    			}
		    }
	    }
    }
 
    function pre_made_trips(){ 
    	if(!checkPermission($this->session->userdata('permission'),'ptrip')){
	        redirect(base_url('AdminDashboard'));   
	    }
	  //$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
	  //$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
	  //$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
	  $data['pre_made_trips'] = $this->Comancontroler_model->get_data_by_table('pre_made_trips');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/pre_made_trips',$data);
    	$this->load->view('admin/layout/footer');
    }
 
    function pre_made_tripsadd(){ 
        if(!checkPermission($this->session->userdata('permission'),'ptrip')){
	        redirect(base_url('AdminDashboard'));   
	    }
        if($this->input->post('save'))	{
				
				$this->form_validation->set_rules('pname', 'trip name','required|min_length[3]');
				$this->form_validation->set_rules('pcity', 'pickup city','required|min_length[3]');
				$this->form_validation->set_rules('plocation', 'pickup location','required|min_length[3]');
				$this->form_validation->set_rules('dcity', 'drop off city','required|min_length[3]'); 
				$this->form_validation->set_rules('dlocation', 'drop off location','required|min_length[3]'); 
				$this->form_validation->set_rules('company', 'company','required|min_length[3]');  
				$this->form_validation->set_rules('rate', 'rate','required|min_length[1]');  
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                { 
					
					$insert_data=array(
					    'pname'=>$this->input->post('pname'),
					    'pcity'=>$this->input->post('pcity'),
					    'plocation'=>$this->input->post('plocation'), 
					    'dcity'=>$this->input->post('dcity'), 
					    'dlocation'=>$this->input->post('dlocation'), 
					    'ptime'=>$this->input->post('ptime'), 
					    'paddress'=>$this->input->post('paddress'), 
					    'dtime'=>$this->input->post('dtime'), 
					    'daddress'=>$this->input->post('daddress'), 
					    'company'=>$this->input->post('company'),
					    'rate'=>$this->input->post('rate'),
					    'parate'=>$this->input->post('parate')
					);
				
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'pre_made_trips'); 
					if($res){ 
						$this->session->set_flashdata('item', 'Pre made trip add successfully.');
                        redirect(base_url('admin/pre_made_trips/add'));
					}
 				   
				}
	}
      
	  $data['cities'] = $this->Comancontroler_model->get_data_by_table('cities','*','city','ASC');
	  $data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','ASC');
	  $data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
	  //$data['pre_made_trips'] = $this->Comancontroler_model->get_data_by_table('pre_made_trips');
	  
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/pre_made_tripsadd',$data);
    	$this->load->view('admin/layout/footer');
 }
 
    function pre_made_tripsupdate() {
        if(!checkPermission($this->session->userdata('permission'),'ptrip')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $id = $this->uri->segment(4);
        if($this->input->post('save'))	{
				
				$this->form_validation->set_rules('pname', 'trip name','required|min_length[3]');
				$this->form_validation->set_rules('pcity', 'pickup city','required|min_length[3]');
				$this->form_validation->set_rules('plocation', 'pickup location','required|min_length[3]');
				$this->form_validation->set_rules('dcity', 'drop off city','required|min_length[3]'); 
				$this->form_validation->set_rules('dlocation', 'drop off location','required|min_length[3]'); 
				$this->form_validation->set_rules('company', 'company','required|min_length[3]');  
				$this->form_validation->set_rules('rate', 'rate','required|min_length[1]');  
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					
					$insert_data=array(
					    'pname'=>$this->input->post('pname'),
					    'pcity'=>$this->input->post('pcity'),
					    'plocation'=>$this->input->post('plocation'), 
					    'ptime'=>$this->input->post('ptime'), 
					    'paddress'=>$this->input->post('paddress'), 
					    'dtime'=>$this->input->post('dtime'), 
					    'daddress'=>$this->input->post('daddress'), 
					    'dcity'=>$this->input->post('dcity'), 
					    'dlocation'=>$this->input->post('dlocation'), 
					    'company'=>$this->input->post('company'),
					    'rate'=>$this->input->post('rate'),
					    'parate'=>$this->input->post('parate')
					);
				
					$res = $this->Comancontroler_model->update_table_by_id($id,'pre_made_trips',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Pre made trip update successfully.');
                        redirect(base_url('admin/pre_made_trips/update/'.$id));
					}
 				   
				}
	}
     
     $data['pre_made_trips'] = $this->Comancontroler_model->get_data_by_id($id,'pre_made_trips');
     $data['cities'] = $this->Comancontroler_model->get_data_by_table('cities','*','city','ASC');
	  $data['companies'] = $this->Comancontroler_model->get_data_by_table('companies','id,company','company','ASC');
	  $data['locations'] = $this->Comancontroler_model->get_data_by_table('locations'); 
     
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/pre_made_tripsupdate',$data);
    	$this->load->view('admin/layout/footer');
 }
 
    function pre_made_tripsdelete(){
        if(!checkPermission($this->session->userdata('permission'),'ptrip')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $id = $this->uri->segment(4);
 		$result = $this->Comancontroler_model->delete($id,'pre_made_trips','id');
 		if($result){
 			redirect('admin/pre_made_trips');
 		}
    }
 
    function events(){  
    	if(!checkPermission($this->session->userdata('permission'),'calendar')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $data['events'] = $this->Comancontroler_model->get_data_by_table('events'); 
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/events',$data);
    	$this->load->view('admin/layout/footer');
    }
    function eventadd() {
        if(!checkPermission($this->session->userdata('permission'),'calendar')){
	        redirect(base_url('AdminDashboard'));   
	    }
        if($this->input->post('save'))	{
				
				$this->form_validation->set_rules('cdate', 'date','required|min_length[9]');
				$this->form_validation->set_rules('title', 'title','required|min_length[3]');
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					
					
					$insert_data=array(
					    'cdate'=>$this->input->post('cdate'),
					    'title'=>$this->input->post('title')
					);
				
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'events'); 
					if($res){ 
						$this->session->set_flashdata('item', 'Event add successfully.');
                        redirect(base_url('admin/event/add'));
					}
 				   
				}
	    } 
	  
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/event_add',$data);
    	$this->load->view('admin/layout/footer');
    }
 
    function eventupdate() {
        if(!checkPermission($this->session->userdata('permission'),'calendar')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $id = $this->uri->segment(4);
        if($this->input->post('save'))	{
				
				$this->form_validation->set_rules('cdate', 'date','required|min_length[9]');
				$this->form_validation->set_rules('title', 'title','required|min_length[3]');
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                { 
					$insert_data=array(
					    'cdate'=>$this->input->post('cdate'),
					    'title'=>$this->input->post('title')
					);
				
					$res = $this->Comancontroler_model->update_table_by_id($id,'events',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Event update successfully.');
                        redirect(base_url('admin/event/update/'.$id));
					}
 				   
				}
	} 
    	
		$data['events'] = $this->Comancontroler_model->get_data_by_id($id,'events');
		
		$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/event_update',$data);
    	$this->load->view('admin/layout/footer');
 }
    function eventdelete(){
        if(!checkPermission($this->session->userdata('permission'),'calendar')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $id = $this->uri->segment(4);
 		$result = $this->Comancontroler_model->delete($id,'events','id');
 		if($result){
 			redirect('admin/calendar_weekly');
 		}
    }
 
 function calendar(){
        if(!checkPermission($this->session->userdata('permission'),'calendar')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $unit = $type = $sdate = $edate = '';
         
        if($this->input->post('search'))	{
            $unit = $this->input->post('unit');
            $type = $this->input->post('type');
        }
        
		if(!empty($this->uri->segment(3))){
		       $ndate = $this->uri->segment(3).'-'.$this->uri->segment(4);
		       $sdate = $ndate.'-01';
		       $edate = $ndate.'-31';
		  }else{
              $sdate = date('Y-m-01');
	          $edate = date('Y-m-t');
		  }
		   
	  if($type != 'Events') {
		$data['dispatch'] = $this->Comancontroler_model->get_dispatch_for_calendar($sdate,$edate,$unit);
	  } else { $data['dispatch'] = array(); }
	  
	  if($type != 'Dispatch') {
		$data['events'] = $this->Comancontroler_model->get_events_by_filter($sdate,$edate);
	  } else { $data['events'] = array(); }
		
	  $data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
	  
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/calendar',$data);
    	$this->load->view('admin/layout/footer');
 }
 
 function calendar_weekly(){
        if(!checkPermission($this->session->userdata('permission'),'calendar')){
	        redirect(base_url('AdminDashboard'));   
	    }
		$unit = $type = $sdate = $edate = '';
         
        if($this->input->post('search'))	{
            $unit = $this->input->post('unit');
            $type = $this->input->post('type');
        }
        
		if(!empty($this->uri->segment(3))){
		       $ndate = $this->uri->segment(3).'-'.$this->uri->segment(4).'-'.$this->uri->segment(5);
		       $day = date('D',strtotime($ndate));
		       if($day == 'Mon') { $sdate = $ndate; }
		      else { $sdate = date('Y-m-d',strtotime('last monday',strtotime($ndate))); } 
		       $edate = date('Y-m-d',strtotime("+6 days",strtotime($sdate)));
		  } else {
		      if(date('D') == 'Mon') { $sdate = date('Y-m-d'); }
		      else { $sdate = date('Y-m-d',strtotime('last monday',strtotime(date('Y-m-d')))); } 
	          $edate = date('Y-m-d',strtotime("+6 days",strtotime($sdate)));
		  }
		  $data['sdate'] = $sdate;
		  $data['edate'] = $edate;
		   
		
		if($type != 'Events') {
		$data['dispatch'] = $this->Comancontroler_model->get_dispatch_for_calendar($sdate,$edate,$unit);
	  } else { $data['dispatch'] = array(); }
	  
	  if($type != 'Dispatch') {
		$data['events'] = $this->Comancontroler_model->get_events_by_filter($sdate,$edate);
	  } else { $data['events'] = array(); }
	  
	  
	    $data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/calendar_weekly',$data);
    	$this->load->view('admin/layout/footer');
 }
 
 function calendar_day_view(){
        if(!checkPermission($this->session->userdata('permission'),'calendar')){
	        redirect(base_url('AdminDashboard'));   
	    }
		if(!empty($this->uri->segment(3))){
		       $ndate = $this->uri->segment(3).'-'.$this->uri->segment(4).'-'.$this->uri->segment(5);
		       $sdate = $ndate;  
		       $edate = $ndate;
		  } else {
		      $sdate = date('Y-m-d'); 
	          $edate = date('Y-m-d');
		  }
		  $data['sdate'] = $sdate;
		  $data['edate'] = $edate;
		   
		$data['dispatch'] = $this->Comancontroler_model->get_dispatch_for_calendar($sdate,$edate);
		$data['events'] = $this->Comancontroler_model->get_events_by_filter($sdate,$edate);
	 //$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
	  //$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/calendar_day',$data);
    	$this->load->view('admin/layout/footer');
 }
 
    function driver_shift() {
        if(!checkPermission($this->session->userdata('permission'),'shift')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $sdate = date('Y-m-d').' 00:00:01';
        $edate = date('Y-m-d').' 23:59:59';
        $driver = '';
        if($this->input->post('search'))	{ 
            $driver = $this->input->post('driver'); 
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate').' 00:00:01'; } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate').' 23:59:59'; } 
        }
        
    	$data['driver_shift'] = $this->Comancontroler_model->getDriverShift($sdate,$edate,$driver);
    	$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/driver_shift',$data);
    	$this->load->view('admin/layout/footer');
	}
     function driver_shift_add() {
        if(!checkPermission($this->session->userdata('permission'),'shift')){
	        redirect(base_url('AdminDashboard'));   
	    }
        if($this->input->post('save'))	{
    				
    				$this->form_validation->set_rules('driver_id', 'driver id','required|min_length[1]|max_length[4]');
    				$this->form_validation->set_rules('sdate', 'start date','required');
    				$this->form_validation->set_rules('edate', 'end date','required'); 
    				
    				
    				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
    				if ($this->form_validation->run() == FALSE){}
                    else
                    { 
                        $startDate = $this->input->post('sdate').' '.$this->input->post('stime');
                        $endDate = $this->input->post('edate').' '.$this->input->post('etime');
                        
    					$insert_data=array(
    					    'driver_id'=>$this->input->post('driver_id'),
    					    'start_date'=>$startDate,
    					    'end_date'=>$endDate,
    					    'start_latitude'=>$this->input->post('slatitude'),
    					    'start_longitude'=>$this->input->post('slongitude'),
    					    'end_latitude'=>$this->input->post('elatitude'),
    					    'end_longitude'=>$this->input->post('elongitude'),
    					    'status'=>$this->input->post('status')
    					);
    				
    					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'driver_shift'); 
    					if($res){ 
    						$this->session->set_flashdata('item', 'Driver shift insert successfully.');
                            redirect(base_url('admin/driver_shift/add'));
    					}
     				   
    				}
    	}
    	
         $data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers'); 
	  
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/driver_shift_add',$data);
        	$this->load->view('admin/layout/footer');
     }
     function driver_shift_update() {
        if(!checkPermission($this->session->userdata('permission'),'shift')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $id = $this->uri->segment(4);
        if($this->input->post('save'))	{
    				
    				$this->form_validation->set_rules('driver_id', 'driver id','required|min_length[1]|max_length[4]');
    				$this->form_validation->set_rules('sdate', 'start date','required'); 
    				$this->form_validation->set_rules('edate', 'end date','required'); 
    				
    				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
    				if ($this->form_validation->run() == FALSE){}
                    else
                    {
    					$startDate = $this->input->post('sdate').' '.$this->input->post('stime');
                        $endDate = $this->input->post('edate').' '.$this->input->post('etime');
                        
    					$insert_data = array(
    					    'driver_id'=>$this->input->post('driver_id'),
    					    'start_date'=>$startDate,
    					    'end_date'=>$endDate,
    					    'start_latitude'=>$this->input->post('slatitude'),
    					    'start_longitude'=>$this->input->post('slongitude'),
    					    'end_latitude'=>$this->input->post('elatitude'),
    					    'end_longitude'=>$this->input->post('elongitude'),
    					    'status'=>$this->input->post('status')
    					); 
    				     
    					$res = $this->Comancontroler_model->update_table_by_id($id,'driver_shift',$insert_data); 
    					if($res){
    						$this->session->set_flashdata('item', 'Driver shift update successfully.');
                            redirect(base_url('admin/driver_shift/update/'.$id));
    					}
     				   
    				}
    	}
         
         $data['driver_shift'] = $this->Comancontroler_model->get_data_by_id($id,'driver_shift'); 
         
         $data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers'); 
	    
        	$this->load->view('admin/layout/header');
        	$this->load->view('admin/layout/sidebar');
        	$this->load->view('admin/driver_shift_update',$data);
        	$this->load->view('admin/layout/footer');
     }
     function driver_shift_delete(){
         if(!checkPermission($this->session->userdata('permission'),'shift')){
	        redirect(base_url('AdminDashboard'));   
	    }
         $id = $this->uri->segment(4); 
     		$result = $this->Comancontroler_model->delete($id,'driver_shift','id');
     		if($result){
     			redirect('admin/driver_shift');
     		}
     }

    
}
?>