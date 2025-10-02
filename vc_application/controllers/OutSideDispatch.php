<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OutSideDispatch extends CI_Controller {
    
    private $dispatchInfo = array('Container Number','Booking Number','Chassis Number','Shipping Line','Vessel / Voyage','POD Number','Seal Number','BOL #','PO #','SO #','Carrier Reference #','Others');
    private $expenses = array('Line Haul','FSC (Fuel Surcharge)','Pre-Pull','Lumper','Detention at Shipper','Detention at Receiver','Detention at Port','Drivers Assist','Gate Fee','Overweight Charges','Delivery Order Charges','Chassis Rental','Demurrage','Layover','Yard Storage','Customs Clearance','Chassis Gate Fee','Chassis Split Fee','Others','Toll','TONU','Discount','Dry Run','ISF Filing','Customs Clearance','Clean Truck Fund (CTF)','Pier Pass','Stop-Off');
    
    private $truckingArr = array("Flatbed","Dry Van","Power Only","Box Truck","53' Dry Van","48' Dry Van","28' Pup Trailer","53' Reefer Trailer","48' Reefer Trailer","28' Reefer Pup Trailer","Multi-temp Reefers (varies by configuration)","48' Flatbed","53' Flatbed","48' Step Deck (Drop Deck)","53' Step Deck (Drop Deck)","48' Double Drop Deck","53' Double Drop Deck","48' Removable Gooseneck (RGN)","53' Removable Gooseneck (RGN)","48' Stretch Flatbed","53' Stretch Flatbed","10' Box Truck","16' Box Truck","24' Box Truck","26' Box Truck","42' Food-grade Tanker","48' Food-grade Tanker","42' Fuel Tanker","48' Fuel Tanker","42' Chemical Tanker","48' Chemical Tanker","42' Pneumatic Tanker","48' Pneumatic Tanker","Single-Vehicle Hauler (varies)","50' Multi-Car Hauler (Enclosed or Open)","80' Stinger Steer Car Carrier","48' Conestoga Trailer","53' Conestoga Trailer","48' Lowboy Trailer","53' Lowboy Trailer","40' Hotshot Trailer","48' Hotshot Trailer");
    
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->model('Comancontroler_model');
	 
    	if( empty($this->session->userdata('logged') )) {
    		redirect(base_url('AdminLogin'));
    	}
		// error_reporting(-1);
		// ini_set('display_errors', 1);
		// error_reporting(E_ERROR);
	}
	public function outsideDispatchUpdate() {

		//echo "<pre>"; print_r($_POST); die();
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $data['truckingArr'] = $this->truckingArr;
		$data['truckingEquipments'] = $this->Comancontroler_model->get_data_by_table('truckingEquipments');

        $data['dispatchInfo'] = $this->Comancontroler_model->get_data_by_column('status','Active','dispatchInfo','title','title','asc');
        //$data['expenses'] = $this->expenses;
        $data['expenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','title,type,days_input','title','asc');
        $data['carrierExpenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','title,type','title','asc');
		$data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title,`order`','order','asc');
	    
		$id = $this->uri->segment(4);
		
		$disInfo = $this->Comancontroler_model->get_data_by_id($id,'dispatchOutside');
		$oldOtherChildInvoice=json_decode($disInfo[0]['dispatchMeta'])->otherChildInvoice;
		$oldFatoringCompanyId= $disInfo[0]['factoringCompany'];
		if($oldFatoringCompanyId>0){
			$factoringCompany=$this->Comancontroler_model->get_data_by_id($oldFatoringCompanyId,'factoringCompanies','company');
			if(isset($factoringCompany)){
				$oldfactoringCompany=	$factoringCompany[0]['company'];
			}else{
				$oldfactoringCompany = 'NA';
			}
		}else{
			$oldfactoringCompany = 'NA';
		}
		// $oldDispatchInfo= $disInfo[0]['dispatchInfo'];
		// print_r($oldDispatchInfo);exit;
		if(empty($disInfo)){ redirect(base_url('admin/outside-dispatch'));  }
		$changeField = array();
		
		if($this->input->post('save'))	{
				
			$this->form_validation->set_rules('pudate', 'PU date','required|min_length[9]');
			$this->form_validation->set_rules('driver', 'driver','required');
			$this->form_validation->set_rules('pcity', 'pickup city','required|min_length[1]');
			$this->form_validation->set_rules('dcity', 'drop off city','required|min_length[1]');
			$this->form_validation->set_rules('company', 'company','required|min_length[1]'); 
			$this->form_validation->set_rules('dlocation', 'drop off location','required|min_length[1]'); 
			$this->form_validation->set_rules('plocation', 'pick up location','required|min_length[1]');
			//$this->form_validation->set_rules('invoice', 'invoice','required|min_length[6]'); 
			
			$pudate1 = $this->input->post('pudate1');
			if(!is_array($pudate1)){ $pudate1 = array(); }
			$dodate1 = $this->input->post('dodate1');
			if(!is_array($dodate1)){ $dodate1 = array(); }
			$pudate = $this->input->post('pudate');
			$driver = $this->input->post('driver');
			$invoiceInput = $this->input->post('invoice');
			
			if(strtotime($pudate) < strtotime('2025-01-17')){
			    $this->form_validation->set_rules('bookedUnder', 'booked under','required|min_length[1]');
			} else {
			    $this->form_validation->set_rules('bookedUnderNew', 'booked under new','required|min_length[1]');
			}
			$inv_first = '';
			$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'dispatchOutside');
			if(empty($driver_trip)) { $inv_last = '1'; }
			else { $inv_last = count($driver_trip) + 1; }
			if($inv_last < 10) { $inv_last = '0'.$inv_last; }
			
			$driver_info = $this->Comancontroler_model->get_data_by_id($driver,'drivers');
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
			    $invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatchOutside','id');
			    if(count($invoiceInfo) > 1 || (count($invoiceInfo) == 1 && $invoiceInfo[0]['id']!=$id)){
			        $this->form_validation->set_rules('invoiceooo', 'invoice','required'); 
				    $this->form_validation->set_message('required','This invoice number is already exist.'); 
			    }
			}
			elseif($invoice == '' || $inv_first==''){
				$this->form_validation->set_rules('invoiceooo', 'invoice','required'); 
				$set_message = 'Invoice number must not empty.';
				if($inv_first == ''){ $set_message = 'Driver code is empty.'; }
				$this->form_validation->set_message('required',$set_message); 
			}
			else {
			    $invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
			}
				
			
			$childInvoiceInfo = $this->input->post('childInvoice');
			if(is_array($childInvoiceInfo)){
			    foreach($childInvoiceInfo as $ciID){
			        $ciInfo = $this->Comancontroler_model->get_data_by_column('invoice',$ciID,'dispatchOutside','id,childInvoice');
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
			        $ciInfo = $this->Comancontroler_model->get_data_by_column('invoice',$ciID,'dispatch','id,childInvoice');
			        if(empty($ciInfo)) {
			            $this->form_validation->set_rules('childInvoicesss', 'child invoice','required');
			            $this->form_validation->set_message('required','PA invoice "'.$ciID.'" is not exist.');
			        } 
			    }
			    $otherChildInvoice = implode(',',$otherChildInvoiceInfo);
			} else {
			    $otherChildInvoice = '';
			}
			
			
			
			$bolCheck = $this->input->post('bol');
			$rcCheck = $this->input->post('rc');
			$gdCheck = $this->input->post('gd');
			$invoiceType = $this->input->post('invoiceType');
			$carrierPaymentType = $this->input->post('carrierPaymentType');
			if (empty($carrierPaymentType)) {
				$carrierPaymentType = ''; 
			}
			$factoringType = $this->input->post('factoringType');
			if (empty($factoringType)) {
				$factoringType = ''; 
			}
			
			$invoicePaid = $this->input->post('invoicePaid');
			$invoiceClose = $this->input->post('invoiceClose');
			$invoiceReady = $this->input->post('invoiceReady');
			$invoicedCheckbox = $this->input->post('invoiced');
			
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
			    if(empty($_FILES['gd_d']['name'])  && $invoiceType != 'RTS'){
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
				// $this->form_validation->set_message('required','If you checked invoice paid checkbox than invoice closed should be checked aswell.');

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
                    $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm|csv';
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
					$config['upload_path'] = 'assets/outside-dispatch/bol/';
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
							$addfile = array('did'=>$id,'type'=>'bol','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
							$changeField[] = array('BOL File','bolfile','Upload',$bol);
						}
					}
				}
				
                /*if(!empty($_FILES['bol_d']['name'])){
					$config['upload_path'] = 'assets/outside-dispatch/bol/';
                    $config['file_name'] = $fileName1.'-BOL-'.$fileName2; //$_FILES['bol_d']['name'];  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('bol_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$id,'type'=>'bol','fileurl'=>$bol);
						$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
                    }
                }*/ 
                
                $rcFilesCount = count($_FILES['rc_d']['name']);
				if($rcFilesCount > 0) {  
					$rcFiles = $_FILES['rc_d'];
					$config['upload_path'] = 'assets/outside-dispatch/rc/';
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
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
							$changeField[] = array('RC File','rcfile','Upload',$bol);
						}
					}
				}
				
				/*if(!empty($_FILES['rc_d']['name'])){
					$config['upload_path'] = 'assets/outside-dispatch/rc/';
                    $config['file_name'] = $fileName1.'-RC-'.$fileName2; //$_FILES['rc_d']['name']; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('rc_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$id,'type'=>'rc','fileurl'=>$bol);
						$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
                    }
                } */
				if(!empty($_FILES['gd_d']['name'])){
					$config['upload_path'] = 'assets/outside-dispatch/gd/';
                    $config['file_name'] = $fileName1.'-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('gd_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$id,'type'=>'gd','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
						$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
						$changeField[] = array('Payment proof file','gdfile','Upload',$bol);
                    }
                } 
				// if(!empty($_FILES['carrier_gd_d']['name'])){
				// 	$config['upload_path'] = 'assets/outside-dispatch/gd/';
                //     $config['file_name'] = $fileName1.'-CARRIER-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                //     $this->load->library('upload',$config);
                //     $this->upload->initialize($config); 
                //     if($this->upload->do_upload('carrier_gd_d')){
                //         $uploadData = $this->upload->data();
                //         $bol = $uploadData['file_name'];
				// 		$addfile = array('did'=>$id,'type'=>'carrierGd','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
				// 		$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
				// 		$changeField[] = array('Carrier Payment proof file','gdfile','Upload',$bol);
                //     }
                // }
				$carrierGdFilesCount = count($_FILES['carrier_gd_d']['name']);
				// echo $carrierGdFilesCount; exit;
				if($carrierGdFilesCount > 0) {  
					$carrierGdFiles = $_FILES['carrier_gd_d'];
					$config['upload_path'] = 'assets/outside-dispatch/gd/';
					$config['file_name'] = $fileName1.'-CARRIER-GD-'.$fileName2; //$_FILES['carrierInvoice']['name'];  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
					for($i = 0; $i < $carrierGdFilesCount; $i++){ 
						$_FILES['carrier_gd_d']['name']     = $carrierGdFiles['name'][$i];
						$_FILES['carrier_gd_d']['type']     = $carrierGdFiles['type'][$i];
						$_FILES['carrier_gd_d']['tmp_name'] = $carrierGdFiles['tmp_name'][$i];
						$_FILES['carrier_gd_d']['error']     = $carrierGdFiles['error'][$i];
						$_FILES['carrier_gd_d']['size']     = $carrierGdFiles['size'][$i]; 
				
						if ($this->upload->do_upload('carrier_gd_d'))  { 
							$dataCarrier_gd_d = $this->upload->data(); 
							$Carrier_gd_d = $dataCarrier_gd_d['file_name'];
							$addfile = array('did'=>$id,'type'=>'carrierGd','fileurl'=>$Carrier_gd_d,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
							$changeField[] = array('Carrier Payment proof file','gdfile','Upload',$Carrier_gd_d);
						}
					}
				}
                
                /*if(!empty($_FILES['carrierInvoice']['name'])){
					$config['upload_path'] = 'assets/outside-dispatch/carrierInvoice/';
                    $config['file_name'] = $fileName1.'-Carrier-Invoice-'.$fileName2; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('carrierInvoice')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$id,'type'=>'carrierInvoice','fileurl'=>$bol);
						$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
                    }
                } */
                $ciFilesCount = count($_FILES['carrierInvoice']['name']);
				if($ciFilesCount > 0) {  
					$ciFiles = $_FILES['carrierInvoice'];
					$config['upload_path'] = 'assets/outside-dispatch/carrierInvoice/';
					$config['file_name'] = $fileName1.'-Carrier-Invoice-'.$fileName2; //$_FILES['carrierInvoice']['name'];  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
					for($i = 0; $i < $ciFilesCount; $i++){ 
						$_FILES['carrierInvoice']['name']     = $ciFiles['name'][$i];
						$_FILES['carrierInvoice']['type']     = $ciFiles['type'][$i];
						$_FILES['carrierInvoice']['tmp_name'] = $ciFiles['tmp_name'][$i];
						$_FILES['carrierInvoice']['error']     = $ciFiles['error'][$i];
						$_FILES['carrierInvoice']['size']     = $ciFiles['size'][$i]; 
				
						if ($this->upload->do_upload('carrierInvoice'))  { 
							$dataRc = $this->upload->data(); 
							$bol = $dataRc['file_name'];
							$addfile = array('did'=>$id,'type'=>'carrierInvoice','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
							$changeField[] = array('Carrier invoice file','carrierInvoice','Upload',$bol);
						}
					}
				}
				
				$paInvoiceCount = count($_FILES['paInvoice']['name']);
				if($paInvoiceCount > 0) {  
					$paInvoiceFiles = $_FILES['paInvoice'];
					$config['file_name'] = $fileName1.'-Customer-Inv-'.$fileName2;   
					$config['upload_path'] = 'assets/outside-dispatch/invoice/';
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
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
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
				
				$parate = $this->input->post('parate');
				/*if(!is_numeric($parate)) { $parate = 0; }
				$payoutRate = $companyInfo[0]['payoutRate'];
				if(!is_numeric($payoutRate)) { $payoutRate = 0; }
				$payoutAmount = $payoutRate * $parate;
				$payoutAmount = round($payoutAmount,2);*/
				$payoutAmount  = $this->input->post('payoutAmount');
				
				if($invoiceType == '' || $parate < 1 || strtotime($pudate) < strtotime('2024-09-01')) {  }
				elseif($invoiceType == 'RTS') { $payoutAmount = $parate - ($parate * 0.0115); }
				elseif($invoiceType == 'Direct Bill') { $payoutAmount = $parate * 1; }
				elseif($invoiceType == 'Quick Pay') { $payoutAmount = $parate - ($parate * 0.02); }
				if($payoutAmount > 0){ $payoutAmount = round($payoutAmount,2); }
				
				
				$expectPayDate = $this->input->post('expectPayDate');
				
				$invoiceDate = $this->input->post('invoiceDate');
				$invoiceType = $this->input->post('invoiceType');
				
				//$invoiceType = $companyInfo[0]['paymenTerms'];
				/*$dayToPay = $companyInfo[0]['dayToPay'];
				if($dayToPay < 2) {$pDay = '+ '.$dayToPay.' day'; }
				elseif($dayToPay > 1) {$pDay = '+ '.$dayToPay.' days'; }
				else { $pDay = "+ 0 day"; }
				*/
				
				
				$bol = $this->input->post('bol');
				$rc = $this->input->post('rc');
				$gd = $this->input->post('gd');
				$carrier_gd = $this->input->post('carrier_gd');

				$status = $this->input->post('status');
				
				$dispatchMeta = array('expense'=>array(),'dispatchInfo'=>array(),'carrierExpense'=>array());
				$dispatchMeta['carrierInvoiceCheck'] = $this->input->post('carrierInvoiceCheck');
				$expenseName = $this->input->post('expenseName');
				$expensePrice = $this->input->post('expensePrice');
				// $expenseDays = $this->input->post('expenseDays');
				$expenseStart = $this->input->post('expenseStart');
				$expenseEnd = $this->input->post('expenseEnd');
				

				$carrierExpenseName = $this->input->post('carrierExpenseName');
				$carrierExpensePrice = $this->input->post('carrierExpensePrice');
				
				$dispatchMeta['invoiced'] = $this->input->post('invoiced');
				$dispatchMeta['invoicePaid'] = $this->input->post('invoicePaid');
				$dispatchMeta['invoiceClose'] = $this->input->post('invoiceClose');
				$dispatchMeta['invoiceReady'] = $this->input->post('invoiceReady');
				$dispatchMeta['invoiceCloseDate'] = $this->input->post('invoiceCloseDate');
				$dispatchMeta['invoicePaidDate'] = $this->input->post('invoicePaidDate');
				$dispatchMeta['invoiceReadyDate'] = $this->input->post('invoiceReadyDate');
				$dispatchMeta['pickup'] = $this->input->post('pickup');
				$dispatchMeta['pPort'] = $this->input->post('pPort')??'0';
				$dispatchMeta['pPortAddress'] = $this->input->post('pPortAddress')??'0';
				$dispatchMeta['dropoff'] = $this->input->post('dropoff');
				$dispatchMeta['dPort'] = $this->input->post('dPort')??'0';
				$dispatchMeta['dPortAddress'] = $this->input->post('dPortAddress')??'0';
				$dispatchMeta['invoicePDF'] = $this->input->post('invoicePDF');
				$dispatchMeta['custInvDate'] = $this->input->post('custInvDate');
				$custDueDate = '';
				if($dispatchMeta['custInvDate'] != '' && $dispatchMeta['custInvDate'] != '0000-00-00'){
				    $custDueDate = date('Y-m-d',strtotime("+ 30 days", strtotime($dispatchMeta['custInvDate'])));
				}
				$dispatchMeta['custDueDate'] = $custDueDate;
				$dispatchMeta['otherChildInvoice'] = $otherChildInvoice;
				
				$dispatchMeta['drayageType'] = $this->input->post('drayageType');
				$dispatchMeta['invoiceDrayage'] = $this->input->post('invoiceDrayage');
				$dispatchMeta['invoiceTrucking'] = $this->input->post('invoiceTrucking');
				$dispatchMeta['appointmentTypeP'] = $this->input->post('appointmentTypeP');
				$dispatchMeta['appointmentTypeD'] = $this->input->post('appointmentTypeD');
				
				$dispatchMeta['quantityP'] = $this->input->post('quantityP');
				$dispatchMeta['commodityP'] = $this->input->post('commodityP');
				$dispatchMeta['metaDescriptionP'] = $this->input->post('metaDescriptionP');
				$dispatchMeta['weightP'] = $this->input->post('weightP');
				
				$dispatchMeta['quantityD'] = $this->input->post('quantityD');
				$dispatchMeta['metaDescriptionD'] = $this->input->post('metaDescriptionD');
				$dispatchMeta['weightD'] = $this->input->post('weightD');
				$dispatchMeta['erInformation'] = $this->input->post('erInformation');
				
				$dispatchMeta['driver_name'] = $this->input->post('driver_name');
				$dispatchMeta['driver_contact'] = $this->input->post('driver_contact');

				if(is_array($expenseName)) {
				    for($i=0;$i<count($expenseName);$i++){
				        $dispatchMeta['expense'][] = array($expenseName[$i],$expensePrice[$i],$expenseStart[$i],$expenseEnd[$i]);
				    }
				}
				if(is_array($carrierExpenseName)) {
				    for($i=0;$i<count($carrierExpenseName);$i++){
				        $dispatchMeta['carrierExpense'][] = array($carrierExpenseName[$i],$carrierExpensePrice[$i]);
				    }
				}
				$dispatchInfoName = $this->input->post('dispatchInfoName');
				$dispatchInfoValue = $this->input->post('dispatchInfoValue');
				if(is_array($dispatchInfoName)) {
				    for($i=0;$i<count($dispatchInfoName);$i++){
				        $dispatchMeta['dispatchInfo'][] = array($dispatchInfoName[$i],$dispatchInfoValue[$i]);

				    }
				}
				$dispatchMeta['partialAmount'] = $this->input->post('partialAmount');
				$payableAmt = $payoutAmount - $dispatchMeta['partialAmount'];
				if(!is_numeric($payableAmt)) { $payableAmt = 0; }
				
				// if($bol=='AK' && $rc=='AK' && $gd=='AK' && strtotime($pudate) > strtotime('2024-06-06')){ 
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
				//         //if($dispatchMeta['invoiceReady']=='1' && $dispatchMeta['invoiceReadyOld']!='1'){  $status = 'Ready to submit QP '.date('m/d/Y'); }
				//         $dispatchMeta['invoiceClose'] = $dispatchMeta['invoicePaid'] = $dispatchMeta['invoiced'] = '0';
				//     }
				// } 
				
				$carrierPlusAgentRate = 0;

				$dispatchMetaJson = json_encode($dispatchMeta);
				$bookedUnder=$this->input->post('bookedUnder') ;
				$bookedUnderNew=$this->input->post('bookedUnderNew') ;
				$agentPercentRate=$this->input->post('agentPercentRate') ;
				$pamargin = (float) $this->input->post('parate') - (float) $this->input->post('rate');
				
				if (($bookedUnder == 4) || ($bookedUnderNew == 4 || $bookedUnderNew == 5)) {
					$agentPercentValue = ((float) $this->input->post('parate') * $agentPercentRate)/100;

					$pamargin = (float) $this->input->post('parate') - (float) $this->input->post('rate') - (float) $this->input->post('agentRate');
					$carrierPlusAgentRate = (float) $this->input->post('rate') + (float) $this->input->post('agentRate');
				}

				$fatoringCompanyId= $this->input->post('factoringCompany');
				if($fatoringCompanyId>0){
					$factoringCompany=$this->Comancontroler_model->get_data_by_id($fatoringCompanyId,'factoringCompanies','company');
					if(isset($factoringCompany)){
						$newfactoringCompany =	$factoringCompany[0]['company'];
					}else{
						$newfactoringCompany = 'NA';
					}
				}else{
					$newfactoringCompany = 'NA';
				}
				
				if($this->input->post('delivered') == 'yes'){
					$driver_status = 'Shipment Delivered';
				}else{
					$driver_status = $this->input->post('driver_status');
				}
				$insert_data=array(
				    'driver'=>$driverId,
				    'truckingCompany'=>$this->input->post('truckingCompany'),
					'userid'=>$this->input->post('userid'),
				    'bookedUnder'=>$this->input->post('bookedUnder'),
				    'bookedUnderNew'=>$this->input->post('bookedUnderNew'),
				    'pudate'=>$pudate,
				    'dodate'=>$this->input->post('dodate'),
				    'trip'=>$this->input->post('trip'),
				    'pcity'=>$pcity,
				    'dcity'=>$dcity,
				    'rate'=>$this->input->post('rate'),
					'carrierPartialAmt'=>$this->input->post('carrierPartialAmt'),
				    'parate'=>$this->input->post('parate'),
					'agentRate'=>$this->input->post('agentRate'),
					'agentPercentRate'=>$this->input->post('agentPercentRate'),
					'carrierPlusAgentRate'=>$carrierPlusAgentRate,
				    //'rateLumper'=>$this->input->post('rateLumper'),
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
					'carrierPaymentType'=>$carrierPaymentType,
					'factoringType' => $factoringType,
					'factoringCompany' => $this->input->post('factoringCompany'),
				    'payoutAmount'=>$payoutAmount,
				    'payableAmt'=>$payableAmt,
				    'dWeek'=>$week,
				    'bol'=>$bol,
				    'rc'=>$rc,
				    'gd'=>$gd,
					'carrierGd'=>$carrier_gd,
				    'delivered'=>$this->input->post('delivered'),
				    'ptime'=>$this->input->post('ptime'),
				    'dtime'=>$this->input->post('dtime'),
				    'notes'=>$this->input->post('notes'),
				    'pnotes'=>$this->input->post('pnotes'),
				    'dnotes'=>$this->input->post('dnotes'),
				    'invoiceNotes'=>$this->input->post('invoiceNotes'),
				    'pamargin'=>$pamargin,
				    'carrierPayoutDate'=>$this->input->post('carrierPayoutDate'),
				    'carrierPayoutCheck'=>$this->input->post('carrierPayoutCheck'),
					'carrierInvoiceRefNo'=>$this->input->post('carrierInvoiceRefNo'),
				    //'detention'=>$this->input->post('detention'),
				    //'detention_check'=>$this->input->post('detention_check'),
				    //'dassist'=>$this->input->post('dassist'),
				    //'dassist_check'=>$this->input->post('dassist_check'),
				    'dispatchMeta'=>$dispatchMetaJson,
				    'lockDispatch'=>$this->input->post('lockDispatch'),
				    'driver_status'=>$driver_status,
				    'status'=>$status
				);
				if($invoiceDate != 'TBD' && $invoiceDate != ''){
				    $insert_data['invoiceDate'] = $invoiceDate;
				    if($invoiceType == 'RTS'){ $iDays = "+ 3 days"; }
				    elseif($invoiceType == 'Direct Bill'){ $iDays = "+ 30 days"; }
				    elseif($invoiceType == 'Quick Pay'){ $iDays = "+ 7 days"; }
				    else { $iDays = "+ 30 days"; }
				    $expectPayDate = date('Y-m-d',strtotime($iDays,strtotime($invoiceDate)));
				    $insert_data['expectPayDate'] = $expectPayDate;
				} else {
				    $insert_data['invoiceDate'] = $insert_data['expectPayDate'] = '0000-00-00';
				}
			
				$res = $this->Comancontroler_model->update_table_by_id($id,'dispatchOutside',$insert_data); 
				if($res){
				    
				    /************* update history **************/
					
					if($disInfo){
						foreach($disInfo as $di){
							//// Update child and parent invoice info /////
							if($di['childInvoice'] != $insert_data['childInvoice']) { 
						        $changeField[] = array('Child Invoice','childInvoice',$di['childInvoice'],$insert_data['childInvoice']); 
						        if($insert_data['childInvoice'] == ''){ $ciNewArray = array(); }
						        else { $ciNewArray = explode(',',$insert_data['childInvoice']); }
						        
							    if($di['childInvoice'] != '') {
							        $ciOldArray = explode(',',$di['childInvoice']);
							        foreach($ciOldArray as $val){
							            if (!in_array($val, $ciNewArray)){
							                $updateInvArr = array('parentInvoice'=>'');
							                $this->Comancontroler_model->update_table_by_column('invoice',$val,'dispatchOutside',$updateInvArr);
							            }
							        }
							    }
							    if($ciNewArray){
							        foreach($ciNewArray as $val){
							            $updateInvArr = array('parentInvoice'=>$invoice);
							            $this->Comancontroler_model->update_table_by_column('invoice',$val,'dispatchOutside',$updateInvArr);
							        }
							    }
							}
							//// update data in sub invoice 
							if($insert_data['childInvoice'] != '') {
								$ciNewArray = explode(',',$insert_data['childInvoice']);
								foreach($ciNewArray as $subInv){
									if(trim($subInv) == ''){ continue; }
									$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice',$subInv,'dispatchOutside','id,dispatchMeta');
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
									
									$currentDiMeta['custInvDate'] = $dispatchMeta['custInvDate'];
									// $currentDiMeta['carrierInvoiceCheck'] = $dispatchMeta['carrierInvoiceCheck'];
									$currentDiMeta['custDueDate'] = $dispatchMeta['custDueDate'];
									$subInvArr['carrierPayoutCheck'] = $insert_data['carrierPayoutCheck'];
									$subInvArr['carrierPayoutDate'] = $insert_data['carrierPayoutDate'];
									
									
									
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

									$subInvArr['status'] = $firstPart.' - Linked to '.$insert_data['invoice'];

									if($getSubDispatch[0]['id'] > 0) {
										$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'],'dispatchOutside',$subInvArr);
									}
								}
							}
							// update data in pa fleet dispatch  
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
										$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice', $subInv, 'dispatch', 'id,dispatchMeta');
							
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
									// $currentDiMeta['carrierInvoiceCheck'] = $dispatchMeta['carrierInvoiceCheck'];
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
									// print_r($$getSubDispatch);exit;
									$getPaentCheckboxes = $this->Comancontroler_model->get_data_by_column('invoice', $invoice, 'dispatchOutside', 'bol,rc,gd,delivered,shipping_contact,driver_status');

									$subInvArr['bol'] =$getPaentCheckboxes[0]['bol'];
									$subInvArr['rc'] = $getPaentCheckboxes[0]['rc'];
									$subInvArr['gd'] = $getPaentCheckboxes[0]['gd'];
									$subInvArr['delivered'] = $getPaentCheckboxes[0]['delivered'];
									$subInvArr['shipping_contact'] = $getPaentCheckboxes[0]['shipping_contact'];
									$subInvArr['driver_status'] = $insert_data['driver_status'];

									if ($getSubDispatch[0]['id'] > 0) {
										// echo $getSubDispatch[0]['id'];exit;
										$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'], 'dispatch', $subInvArr);
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
									$this->Comancontroler_model->update_table_by_column('invoice', $removedInv, 'dispatch', $updateData);
								}
							}

							if($di['driver'] != $insert_data['driver']) { 
								$changeField[] = array('Driver','driver',$di['driver'],$insert_data['driver']);
							 }
							if($di['truckingCompany'] != $insert_data['truckingCompany']) { $changeField[] = array('Trucking Company','truckingCompany',$di['truckingCompany'],$insert_data['truckingCompany']); }
							if($di['bookedUnder'] != $insert_data['bookedUnder']) { $changeField[] = array('Booked Under','bookedUnder',$di['bookedUnder'],$insert_data['bookedUnder']); }
							if($di['bookedUnderNew'] != $insert_data['bookedUnderNew']) { $changeField[] = array('Booked Under New','bookedUnderNew',$di['bookedUnderNew'],$insert_data['bookedUnderNew']); }
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

							if($di['invoiceNotes'] != $insert_data['invoiceNotes']) { 
								if($di['invoiceNotes'] == ''){
									$changeField[] = array('Invoice Description','invoiceNotes','No Notes',$insert_data['invoiceNotes']); 
								}else{
									$changeField[] = array('Invoice Description','invoiceNotes',$di['invoiceNotes'],$insert_data['invoiceNotes']); 
								}
								
							}
							if($di['company'] != $insert_data['company']) { $changeField[] = array('Company','company',$di['company'],$insert_data['company']); }
							
							$shippingContactsNew=$this->Comancontroler_model->get_data_by_id($insert_data['shipping_contact'],'company_shipping_contacts','contact_person');
							if(isset($shippingContactsNew)){
								$contactNew =	$shippingContactsNew[0]['contact_person'];
							}else{
								$contactNew = 'NA';
							}
							if($di['shipping_contact'] > 0){
								$shippingContactsOld=$this->Comancontroler_model->get_data_by_id($di['shipping_contact'],'company_shipping_contacts','contact_person');
								if(isset($shippingContactsOld)){
									$contactOld =	$shippingContactsOld[0]['contact_person'];
								}else{
									$contactOld = 'NA';
								}	
							}else{
								$contactOld = 'NA';
							}
							if($di['shipping_contact'] != $insert_data['shipping_contact']) { $changeField[] = array('Shipping Contact','shipping_contact', $contactOld, $contactNew); }

							if($di['rate'] != $insert_data['rate']) { $changeField[] = array('Rate','rate',$di['rate'],$insert_data['rate']); }
							if($di['carrierPartialAmt'] != $insert_data['carrierPartialAmt']) { $changeField[] = array('Carrier Partial Amount','carrierPartialAmt',$di['carrierPartialAmt'],$insert_data['carrierPartialAmt']); }
							if($di['agentRate'] != $insert_data['agentRate']) { $changeField[] = array('Brooker Rate','agentRate',$di['agentRate'],$insert_data['agentRate']); }

							if($di['agentPercentRate'] != $insert_data['agentPercentRate']) { $changeField[] = array('Agent Percent Rate','agentPercentRate',$di['agentPercentRate'],$insert_data['agentPercentRate']); }

							if($di['carrierPlusAgentRate'] != $insert_data['carrierPlusAgentRate']) { $changeField[] = array('Total Amount','carrierPlusAgentRate',$di['carrierPlusAgentRate'],$insert_data['carrierPlusAgentRate']); }

							if($di['pamargin'] != $insert_data['pamargin']) { $changeField[] = array('Margin','pamargin',$di['pamargin'],$insert_data['pamargin']); }

							if($di['parate'] != $insert_data['parate']) { $changeField[] = array('PA Rate','parate',$di['parate'],$insert_data['parate']); }
							if($di['trailer'] != $insert_data['trailer']) { $changeField[] = array('Trailer','trailer',$di['trailer'],$insert_data['trailer']); }
							if($di['tracking'] != $insert_data['tracking']) { $changeField[] = array('Tracking','tracking',$di['tracking'],$insert_data['tracking']); }
							if($di['invoice'] != $insert_data['invoice']) { $changeField[] = array('Invoice','invoice',$di['invoice'],$insert_data['invoice']); }
							if($di['invoiceType'] != $insert_data['invoiceType']) { $changeField[] = array('Invoice Type','invoiceType',$di['invoiceType'],$insert_data['invoiceType']); }

							if($di['carrierPaymentType'] != $insert_data['carrierPaymentType']) { $changeField[] = array('Carrier Payment Type','carrierPaymentType',$di['carrierPaymentType'],$insert_data['carrierPaymentType']); }

							if($di['factoringType'] != $insert_data['factoringType']) { $changeField[] = array('Factoring Type','factoringType',$di['factoringType'],$insert_data['factoringType']); }

							if($oldfactoringCompany != $newfactoringCompany) { 
								// echo $newfactoringCompany;exit;
								$changeField[] = array('Factoring Company','factoringCompany',$oldfactoringCompany,$newfactoringCompany); }

							
							if($di['carrierInvoiceRefNo'] != $insert_data['carrierInvoiceRefNo']) { 
								if($di['carrierInvoiceRefNo']==''){
									$changeField[] = array('Carrier invoice Ref No','carrierInvoiceRefNo','No value',$insert_data['carrierInvoiceRefNo']);
								}else{
									$changeField[] = array('Carrier invoice Ref No','carrierInvoiceRefNo',$di['carrierInvoiceRefNo'],$insert_data['carrierInvoiceRefNo']);
								}
							}
								
							if($di['payoutAmount'] != $insert_data['payoutAmount']) { $changeField[] = array('Payout Amount','payoutAmount',$di['payoutAmount'],$insert_data['payoutAmount']); }
							if($di['dWeek'] != $insert_data['dWeek']) { $changeField[] = array('Week','dWeek',$di['dWeek'],$insert_data['dWeek']); }
							if($di['bol'] != $insert_data['bol']) { $changeField[] = array('BOL','bol',$di['bol'],$insert_data['bol']); }
							if($di['rc'] != $insert_data['rc']) { $changeField[] = array('RC','rc',$di['rc'],$insert_data['rc']); }
							if($di['gd'] != $insert_data['gd']) { $changeField[] = array('$','gd',$di['gd'],$insert_data['gd']); }

							if($di['carrier_gd'] != $insert_data['carrier_gd']) { $changeField[] = array('Carrier Payment Proof Check box','carrier_gd',$di['carrier_gd'],$insert_data['carrier_gd']); }

							if($di['delivered'] != $insert_data['delivered']) { $changeField[] = array('Delivered','delivered',$di['delivered'],$insert_data['delivered']); }
							if($di['notes'] != $insert_data['notes']) { $changeField[] = array('Notes','notes',$di['notes'],$insert_data['notes']); }
							if($di['driver_status'] != $insert_data['driver_status']) { $changeField[] = array('Driver Status','driver_status',$di['driver_status'],$insert_data['driver_status']); }
							if($di['lockDispatch'] != $insert_data['lockDispatch']) { $changeField[] = array('Lock Dispatch','lockDispatch',$di['lockDispatch'],$insert_data['lockDispatch']); }
							if($di['status'] != $insert_data['status']) { $changeField[] = array('Status','status',$di['status'],$insert_data['status']); }
							if($di['dispatchMeta'] != ''){
								$diMeta = json_decode($di['dispatchMeta'],true);
								
								if(empty($diMeta['invoiced'])){ $diMeta['invoiced']=0; }
								if(empty($diMeta['invoicePaid'])){ $diMeta['invoicePaid']=0; }
								if(empty($diMeta['invoiceClose'])){ $diMeta['invoiceClose']=0; }
								if(empty($diMeta['invoiceReady'])){ $diMeta['invoiceReady']=0; }

								if(empty($dispatchMeta['invoiced'])){ $dispatchMeta['invoiced']=0; }
								if(empty($dispatchMeta['invoicePaid'])){ $dispatchMeta['invoicePaid']=0; }
								if(empty($dispatchMeta['invoiceClose'])){ $dispatchMeta['invoiceClose']=0; }
								if(empty($dispatchMeta['invoiceReady'])){ $dispatchMeta['invoiceReady']=0; }

								
								if($diMeta['invoiced'] != $dispatchMeta['invoiced']) { $changeField[] = array('Invoiced checkbox','invoiced',$diMeta['invoiced'],$dispatchMeta['invoiced']); }

								if($diMeta['invoicePaid'] != $dispatchMeta['invoicePaid']) { $changeField[] = array('Invoice Paid checkbox','invoicePaid',$diMeta['invoicePaid'],$dispatchMeta['invoicePaid']); }

								if($diMeta['invoicePaidDate'] != $dispatchMeta['invoicePaidDate']) { $changeField[] = array('Invoice Paid Date','invoicePaidDate',$diMeta['invoicePaidDate'],$dispatchMeta['invoicePaidDate']); }

								if($diMeta['invoiceClose'] != $dispatchMeta['invoiceClose']) { $changeField[] = array('Invoice Closed checkbox','invoiceClose',$diMeta['invoiceClose'],$dispatchMeta['invoiceClose']); }

								if($diMeta['invoiceCloseDate'] != $dispatchMeta['invoiceCloseDate']) { $changeField[] = array('Invoice Closed Date','invoiceCloseDate',$diMeta['invoiceCloseDate'],$dispatchMeta['invoiceCloseDate']); }

								if($diMeta['invoiceReady'] != $dispatchMeta['invoiceReady']) { $changeField[] = array('Ready to submit checkbox','invoiceReady',$diMeta['invoiceReady'],$dispatchMeta['invoiceReady']); }
								
								if($diMeta['invoiceReadyDate'] != $dispatchMeta['invoiceReadyDate']) { $changeField[] = array('Ready To Submit Date','invoiceReadyDate',$diMeta['invoiceReadyDate'],$dispatchMeta['invoiceReadyDate']); }
								
								if($diMeta['invoicePDF'] != $dispatchMeta['invoicePDF']) { $changeField[] = array('Invoice PDF','invoicePDF',$diMeta['invoicePDF'],$dispatchMeta['invoicePDF']); }
		
								if($diMeta['driver_name'] != $dispatchMeta['driver_name']) { $changeField[] = array('Driver name','driver_name',$diMeta['driver_name'],$dispatchMeta['driver_name']); }
								if($diMeta['driver_contact'] != $dispatchMeta['driver_contact']) { $changeField[] = array('Driver contact','driver_contact',$diMeta['driver_contact'],$dispatchMeta['driver_contact']); }
							}
							if($invoiceDate != 'TBD' && $invoiceDate != ''){
								if($di['invoiceDate'] != $insert_data['invoiceDate']) { 
									$changeField[] = array('Invoice Date','invoiceDate',$di['invoiceDate'],$insert_data['invoiceDate']);
									$changeField[] = array('Expect Pay Date','expectPayDate',$di['expectPayDate'],$insert_data['expectPayDate']);
								}
							}


							$dispatchInfo = json_decode($disInfo[0]['dispatchMeta'], true);
							// print_r($dispatchInfo);exit;
							if (is_array($dispatchInfoName)) {
								for ($i = 0; $i < count($dispatchInfoName); $i++) {
									if (isset($dispatchInfo['dispatchInfo'])) {
										$currentName = isset($dispatchInfo['dispatchInfo'][$i][0]) ? $dispatchInfo['dispatchInfo'][$i][0] : 'N/A';
										$currentValue = isset($dispatchInfo['dispatchInfo'][$i][1]) ? $dispatchInfo['dispatchInfo'][$i][1] : 'N/A';
									}
									
									// echo $dispatchInfoName[$i];exit;
									if ($currentName != $dispatchInfoName[$i] ) {
										$changeField[] = array(
											'Dispatch Info value',
											'dispatchInfoName',
											$currentName,
											$dispatchInfoName[$i]
										);
									}
									if ($currentValue != $dispatchInfoValue[$i]){
										$changeField[] = array(
										'Dispatch Info',
										'dispatchInfoValue',								
										$currentValue,
										$dispatchInfoValue[$i]
									);}
								}
							}
							if(is_array($expenseName)) {
								for($i=0;$i<count($expenseName);$i++){
									$dispatchMeta['expense'][] = array($expenseName[$i],$expensePrice[$i]);

									if (isset($dispatchInfo['expense'])) {
										$currentExpenseName = isset($dispatchInfo['expense'][$i][0]) ? $dispatchInfo['expense'][$i][0] : 'N/A';
										$currentExpenseValue = isset($dispatchInfo['expense'][$i][1]) ? $dispatchInfo['expense'][$i][1] : 'N/A';
									}
									if ($currentExpenseName != $expenseName[$i] ) {
										$changeField[] = array(
											'Expense name',
											'expenseName',
											$currentExpenseName,
											$expenseName[$i]
										);
									}
									if ($currentExpenseValue != $expensePrice[$i]){
										$changeField[] = array(
										'Expense prices',
										'expensePrice',								
										$currentExpenseValue,
										$expensePrice[$i]
									);}
								}
							}
							if(is_array($carrierExpenseName)) {
								for($i=0;$i<count($carrierExpenseName);$i++){
									$dispatchMeta['carrierExpense'][] = array($carrierExpenseName[$i],$carrierExpensePrice[$i]);

									if (isset($dispatchInfo['carrierExpense'])) {
										$currentCarrierExpenseName = isset($dispatchInfo['carrierExpense'][$i][0]) ? $dispatchInfo['carrierExpense'][$i][0] : 'N/A';
										$currentCarrierExpenseValue = isset($dispatchInfo['carrierExpense'][$i][1]) ? $dispatchInfo['carrierExpense'][$i][1] : 'N/A';
									}
									if ($currentCarrierExpenseName != $carrierExpenseName[$i] ) {
										$changeField[] = array(
											'Carrier Expense',
											'carrierExpenseName',
											$currentCarrierExpenseName,
											$carrierExpenseName[$i]
										);
									}
									if ($currentCarrierExpenseValue != $carrierExpensePrice[$i]){
										$changeField[] = array(
										'Carrier Expense prices',
										'carrierExpensePrice',								
										$currentCarrierExpenseValue,
										$carrierExpensePrice[$i]
									);}
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
    					$pnotes1 = $this->input->post('pnotes1');
    					$dnotes1 = $this->input->post('dnotes1');
    					$pcodename = $this->input->post('pcodename');
    					$dcodename = $this->input->post('dcodename');
    					$extrdispatchid1 = $this->input->post('extrdispatchid1');
    					$extrdispatchid2 = $this->input->post('extrdispatchid2');
    					$pd_type1 = $this->input->post('pd_type1');
    					$pd_type2 = $this->input->post('pd_type2');
    					
    					$pickup1 = $this->input->post('pickup1');
    					$pPort1 = $this->input->post('pPort1');

    					$pPortAddress1 = $this->input->post('pPortAddress1');


    					$dropoff1 = $this->input->post('dropoff1');
    					$dPort1 = $this->input->post('dPort1');

						
    					$dPortAddress1 = $this->input->post('dPortAddress1')??'0';
						
    					$appointmentTypeP1 = $this->input->post('appointmentTypeP1');
    					$appointmentTypeD1 = $this->input->post('appointmentTypeD1');
    					
    					$quantityP1 = $this->input->post('quantityP1');
    					$metaDescriptionP1 = $this->input->post('metaDescriptionP1');
    					$weightP1 = $this->input->post('weightP1');
						$commodityP1 = $this->input->post('commodityP1');

    					$quantityD1 = $this->input->post('quantityD1');
    					$metaDescriptionD1 = $this->input->post('metaDescriptionD1');
    					$weightD1 = $this->input->post('weightD1');
						
    					
    					for($i=0;$i<count($pudate1);$i++){
				          if($pudate1[$i]!='' && $pd_type1[$i]=='pickup') {
				            $pcodeVal1 = implode('~-~',$pcode1[$pcodename[$i]]);  
				            $pcity1 = $this->check_city($check_pcity1[$i]);
				            $plocation1 = $this->check_location($check_plocation1[$i]); 
							
							if($paddressid1[$i] == ''){
							    $dAddress = $this->isAddressExist('2025-01-25',$check_pcity1[$i],$check_plocation1[$i],$paddress1[$i],'yes');
							    if(is_numeric($dAddress)){ $paddressid1[$i] = $dAddress; }
							}
				            
							$pd_meta = array();
							$pd_meta['appointmentType'] = $appointmentTypeP1[$i];
							
							$pd_meta['quantityP'] = $quantityP1[$i];
							$pd_meta['metaDescriptionP'] = $metaDescriptionP1[$i];
							$pd_meta['weightP'] = $weightP1[$i];
							$pd_meta['commodityP'] = $commodityP1[$i];

							
				            $pdMetaJson = json_encode($pd_meta);
							$pPort1[$i] = $pPort1[$i] ?? '0';
							$pPortAddress1[$i] = $pPortAddress1[$i] ?? '0';

    					    $extraData = array(
    					    'dispatchid'=>$id,
    					    'pd_title'=>$pickup1[$i],
    					    'pd_port'=>$pPort1[$i],
    					    'pd_portaddress'=>$pPortAddress1[$i],
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
    					        $this->Comancontroler_model->update_table_by_id($extrdispatchid1[$i],'dispatchOutsideExtraInfo',$extraData); 
    					    } else {
    					        $this->Comancontroler_model->add_data_in_table($extraData,'dispatchOutsideExtraInfo');
    					    }
				          }
    					}
						
						for($i=0;$i<count($dodate1);$i++){
				          if($dodate1[$i]!='' && $pd_type2[$i]=='dropoff') { 
				            $dcodeVal1 = implode('~-~',$dcode1[$dcodename[$i]]);
				            $dcity1 = $this->check_city($check_dcity1[$i]);  
				            $dlocation1 = $this->check_location($check_dlocation1[$i]);
				             
				            if($daddressid1[$i] == ''){
							    $dAddress = $this->isAddressExist('2025-01-25',$check_dcity1[$i],$check_dlocation1[$i],$daddress1[$i],'yes');
							    if(is_numeric($dAddress)){ $daddressid1[$i] = $dAddress; }
				            }
							 
				            
							$pd_meta = array();
							$pd_meta['appointmentType'] = $appointmentTypeD1[$i];
							
							$pd_meta['quantityD'] = $quantityD1[$i];
							$pd_meta['metaDescriptionD'] = $metaDescriptionD1[$i];
							$pd_meta['weightD'] = $weightD1[$i];
							
				            $pdMetaJson = json_encode($pd_meta);
							
    					    $extraData = array(
    					    'dispatchid'=>$id,
    					    'pd_location'=>$dlocation1, 
    					    'pd_title'=>$dropoff1[$i],
    					    'pd_port'=>$dPort1[$i],
    					    'pd_portaddress'=>$dPortAddress1[$i],
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
    					        $this->Comancontroler_model->update_table_by_id($extrdispatchid2[$i],'dispatchOutsideExtraInfo',$extraData); 
    					    } else {
    					        $this->Comancontroler_model->add_data_in_table($extraData,'dispatchOutsideExtraInfo');
    					    }
				          }
    					}
				    }
				    
				    $userid = $this->session->userdata('logged');
				    if($changeField) {
				        $changeFieldJson = json_encode($changeField);
				        $dispatchLog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
				        $this->Comancontroler_model->add_data_in_table($dispatchLog,'dispatchOutsideLog'); 
				    }
				    
					$this->session->set_flashdata('item', '	PA Logistics updated successfully.');
                    redirect(base_url('admin/outside-dispatch/update/'.$id));
				}
 			   
			}
	    }
		//
		$user = $this->session->userdata('logged');
		$data['user'] = $user['adminid'];
		$data['extraDispatch'] = $this->Comancontroler_model->getExtraOutsideDispatchInfo($id); 
		$data['userinfo'] = $this->Comancontroler_model->get_data_by_column('id',$disInfo[0]['userid'],'admin_login','uname');
		$data['users'] = $this->Comancontroler_model->get_data_by_table('admin_login');
		$data['dispatch'] = $this->Comancontroler_model->get_data_by_id($id,'dispatchOutside');
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
	    $data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
		$data['drayageEquipments'] = $this->Comancontroler_model->get_data_by_table('drayageEquipments');
	    $data['erInformation'] = $this->Comancontroler_model->get_data_by_table('erInformation');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		//$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
		// echo $disInfo[0]['userid'];exit;
		$data['documents'] = array();

		
		$documents = $this->Comancontroler_model->get_document_by_dispach($id,'documentsOutside');
		if($documents){
	        foreach($documents as $doc){
	            $doc['parent'] = 'no'; 
	            $data['documents'][] = $doc;
	        }
	    }
	    if($data['dispatch'][0]['parentInvoice'] != ''){
	        $parentID = $this->Comancontroler_model->get_data_by_column('invoice',$data['dispatch'][0]['parentInvoice'],'dispatchOutside','id');
	        if($parentID){
	            if($parentID[0]['id'] > 0) {
	                $documentsParent = $this->Comancontroler_model->get_document_by_dispach($parentID[0]['id'],'documentsOutside');
	                if($documentsParent){
            	        foreach($documentsParent as $doc){
            	            $doc['parent'] = 'yes'; 
            	            $data['documents'][] = $doc;
            	        }
            	    }
	            }
	        }
	    }
		if($data['dispatch'][0]['otherParentInvoice'] != ''){
	        $parentID = $this->Comancontroler_model->get_data_by_column('invoice',$data['dispatch'][0]['otherParentInvoice'],'dispatch','id');
	        if($parentID){
	            if($parentID[0]['id'] > 0) {
	                $documentsParent = $this->Comancontroler_model->get_document_by_dispach($parentID[0]['id'],'documents');
	                if($documentsParent){
            	        foreach($documentsParent as $doc){
            	            $doc['otherParent'] = 'yes'; 
							$doc['parentType'] ='fleet';
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
		// $columns='*',$orderby='',$order='desc',$status=''
		$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies','*','','desc','Active');
		$data['booked_under'] = $this->Comancontroler_model->get_data_by_table('booked_under');
	    $data['dispatchLog'] = $this->Comancontroler_model->get_dispachLog('dispatchOutsideLog',$id);
		$data['reminderLog'] = $this->Comancontroler_model->get_reminderLog('logistics',$id);
		
		$data['factoringCompanies'] = $this->Comancontroler_model->get_data_by_table('factoringCompanies');

       
	    $dispatchMeta = json_decode($data['dispatch'][0]['dispatchMeta'],true);
		if(empty($dispatchMeta['invoiced'])){ $dispatchMeta['invoiced']=0; }
		if(empty($dispatchMeta['invoicePaid'])){ $dispatchMeta['invoicePaid']=0; }
		if(empty($dispatchMeta['invoiceClose'])){ $dispatchMeta['invoiceClose']=0; }
		if(empty($dispatchMeta['invoiceReady'])){ $dispatchMeta['invoiceReady']=0; }

		//echo "<pre>"; print_r($dispatchMeta); die(); 
		//echo $dispatchMeta['invoiced']; die();
		
	    
	    $data['otherChildInvoice'] = array();
	    if($dispatchMeta['otherChildInvoice'] != ''){
	        $oInvoice = explode(',',$dispatchMeta['otherChildInvoice']);
	        $data['otherChildInvoice'] = $this->Comancontroler_model->get_data_by_where_in('invoice',$oInvoice,'dispatch','id,invoice,rate,parate,trailer');
	    }
	    
	    $data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['dispatch'][0]['invoice'],'dispatchOutside','id,invoice,rate,parate,trailer');
        
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/outside_dispatch_update',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function index_backup() {
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    //$data['dispatchInfo'] = $this->dispatchInfo;
	    $data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');
        /********* update status *********/
        if($this->input->post('statusonly') && $this->input->post('statusid'))	{
            $statusonly = $this->input->post('statusonly');
            $statusid = $this->input->post('statusid');
            if($statusonly!='' && $statusid > 0){
                $updatedata = array('status'=>$statusonly);
                $this->Comancontroler_model->update_table_by_id($statusid,'dispatchOutside',$updatedata);
                die('updated');
            }
        }
        
        $sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        $edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
		$company = $truckingCompany = $driver = $status = $invoice = $tracking = $dispatchInfoValue = $dispatchInfo = '';
        
        ////// generate csv
		if($this->input->post('generateCSV') || $this->input->post('generateXls')){
            $truckingCompany = $this->input->post('truckingCompanies');
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
            
            $dispatch = $this->Comancontroler_model->downloadDispatchOutsideCSV($sdate,$edate,$truckingCompany,$driver,$status,$invoice,$tracking);
            $dispatchInfo = $this->Comancontroler_model->get_data_by_column('status','Active','dispatchInfo','title','title','asc');
            $expenses = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','title,type','title','asc');
			
			// Data to be written to the CSV file (example data)
			$heading = array('Dispatch ID','Trucking Company','Driver','Booked Under','Trucking Equipment','Drayage Equipment','Pick Up Date','Pick Up Time','Pick Up City','Pick Up Company','Pick Up Address','Pick Up','Pickup Notes','Drop Off Date','Drop Off Time','Drop Off City','Drop Off Company','Drop Off Address','Drop Off','Driver Notes','Rate','PA Rate','Rate Lumper','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Driver Assist','Shipment Notes','Shipment Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Carrier Invoice Date','Carrier Payout Date','Sub Invoice','Delivered', 'Customer BOL', 'Customer RC', 'Customer Payment Proof', 'Carrier Payment Type', 'Factoring Type', 'Factoring companies', 'Carrier Invoice', 'Invoice Ref No');
			foreach($expenses as $ex){ $heading[] = $ex['title']; }
			foreach($dispatchInfo as $di){ $heading[] = $di['title']; }
			$data = array($heading);
			
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
					$childDispatch = $this->Comancontroler_model->downloadDispatchOutsideCSV('','','','','','','',$subInvoice);
					if($childDispatch) {
						foreach($childDispatch as $row){
							if(in_array($row['id'], $dispatchArr)) { continue; }
							$dispatchArr[] = $row['id'];
							$dispatch[] = $row;
						}
					}
				}
				
				foreach($dispatch as $row){
					$dispatchMeta = json_decode($row['dispatchMeta'],true);
				    $pudate = date('m/d/Y',strtotime($row['pudate']));
					  if($row['pudate']!='0000-00-00') {
				    //    $dodate = date('m/d/Y',strtotime($row['dodate']));
   					   	$pudate = '="' . date('m-d-Y', strtotime($row['pudate'])) . '"';
				   }
				   $dodate = $invoiceDate = $expectPayDate = $carrierPayoutDate = '0000-00-00';
				   if($row['dodate']!='0000-00-00') {
				    //    $dodate = date('m/d/Y',strtotime($row['dodate']));
   					   	$dodate = '="' . date('m-d-Y', strtotime($row['dodate'])) . '"';
				   }
				   if($row['invoiceDate']!='0000-00-00') {
				    //    $invoiceDate = date('m/d/Y',strtotime($row['invoiceDate']));
					   	$invoiceDate = '="' . date('m-d-Y', strtotime($row['invoiceDate'])) . '"';
				   }
				   if($row['expectPayDate']!='0000-00-00') {
				    //    $expectPayDate = date('m/d/Y',strtotime($row['expectPayDate']));
					   $expectPayDate = '="' . date('m-d-Y', strtotime($row['expectPayDate'])) . '"';
				   }
				   if($row['carrierPayoutDate']!='0000-00-00') {
				    //    $carrierPayoutDate = date('m/d/Y',strtotime($row['carrierPayoutDate']));
					   $carrierPayoutDate = '="' . date('m-d-Y', strtotime($row['carrierPayoutDate'])) . '"';
				   }
				   
				   $invReady = $dispatchMeta['invoiceReadyDate'];
				   if(trim($invReady) != ''){ 
						// $invReady = date('m/d/Y',strtotime($invReady)); 
						$invReady = '="' . date('m-d-Y', strtotime($invReady)) . '"';
					}
				   $invPaid = $dispatchMeta['invoicePaidDate'];
				   if(trim($invPaid) != ''){ 
					// $invPaid = date('m/d/Y',strtotime($invPaid)); 
					$invPaid = '="' . date('m-d-Y', strtotime($invPaid)) . '"';
					}
				   $invClosed = $dispatchMeta['invoiceCloseDate'];
				   if(trim($invClosed) != ''){ 
					// $invClosed = date('m/d/Y',strtotime($invClosed));
					$invClosed = '="' . date('m-d-Y', strtotime($invClosed)) . '"';
				 }
				    $carrierInvDate = $dispatchMeta['custInvDate'];
				   if(trim($carrierInvDate) != ''){ 
					// $invClosed = date('m/d/Y',strtotime($invClosed));
					$carrierInvDate = '="' . date('m-d-Y', strtotime($carrierInvDate)) . '"';
				 }
				 $invoiceTrucking = $dispatchMeta['invoiceTrucking'];
				 $invoiceDrayage = $dispatchMeta['invoiceDrayage'];
				   
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
					
					$fatoringCompanyId= $row['factoringCompany'];
					if($fatoringCompanyId>0){
						$factoringCompany=$this->Comancontroler_model->get_data_by_id($fatoringCompanyId,'factoringCompanies','company');
						if(isset($factoringCompany)){
							$factoringCompany=	$factoringCompany[0]['company'];
						}else{
							$factoringCompany = '';
						}
					}else{
						$factoringCompany = '';
					}

					if($dispatchMeta['carrierInvoiceCheck'] == 1){
						$carrierInvoiceCheck = 'AK';
					}else{
						$carrierInvoiceCheck = '';
					}
					// $otherChildInvoice = $dispatchMeta['otherChildInvoice'];
					$childInvoices = !empty($row['childInvoice']) ? explode(',', $row['childInvoice']) : [];
					$otherInvoices = !empty($dispatchMeta['otherChildInvoice']) ? explode(',', $dispatchMeta['otherChildInvoice']) : [];
					$allInvoices = array_filter(array_unique(array_merge($childInvoices, $otherInvoices)));
					// $allChildInvoices = implode(',', $allInvoices);
					$allChildInvoices = '' . implode(', ', $allInvoices) . '';

				   $dataRow = array($row['id'],$row['ttruckingCompany'],$row['dname'],$row['bbookedUnder'],$invoiceTrucking,$invoiceDrayage,$pudate,$row['ptime'],$row['ppcity'],$row['pplocation'],$row['paddress'],$row['pcode'],cleanSpace($row['pnotes']),$dodate,$row['dtime'],$row['ddcity'],$row['ddlocation'],$row['daddress'],$row['dcode'],cleanSpace($row['dnotes']),$row['rate'],$row['parate'],$row['rateLumper'],$row['ccompany'],$trailer,$row['tracking'],$row['invoice'],$row['dWeek'],$row['payoutAmount'],$invoiceDate,$row['invoiceType'],$expectPayDate,$row['dassist'],$row['status'],$row['driver_status'],cleanSpace($row['notes']),$invReady,$invPaid,$invClosed,cleanSpace($row['invoiceNotes']),$carrierInvDate,$carrierPayoutDate,$allChildInvoices, $row['delivered'], $row['bol'], $row['rc'], $row['gd'], $row['carrierPaymentType'], $row['factoringType'], $factoringCompany, $carrierInvoiceCheck, $row['carrierInvoiceRefNo']);
				
				    
					foreach($expenses as $ex){ 
						$exInfo = '';
						if($dispatchMeta['expense']) { 
							foreach($dispatchMeta['expense'] as $diVal) {
								if($diVal[0] == $ex['title']){ $exInfo = $diVal[1]; }
							}
						}
						$dataRow[] = $exInfo;
					}
					foreach($dispatchInfo as $di){ 
						$disInfo = '';
						if($dispatchMeta['dispatchInfo']) { 
							foreach($dispatchMeta['dispatchInfo'] as $diVal) {
								if($diVal[0] == $di['title']){ $disInfo = $diVal[1]; }
							}
						}
						$dataRow[] = $disInfo;
					}
					$data[] = $dataRow;
				}
			}
            
			if($this->input->post('generateCSV')){
				$fileName = "OutsideDispatch_".$sdate."_".$edate.".csv"; 
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
				$fileName = "OutsideDispatch_".$sdate."_".$edate.".xlsx";   //"data_$date.xlsx";
				// Generate Excel file using the library
				$this->excel_generator->generateExcel($data, $fileName);
			}

			// Delete the file from the server
			unlink($fileName);
			exit;
			die('csv');
        }
		
		
        if($this->input->post('search'))	{
			$company = $this->input->post('company');    
            $truckingCompany = $this->input->post('truckingCompanies');
            $driver = $this->input->post('driver'); 
            $dispatchInfo = $this->input->post('dispatchInfo'); 
            $dispatchInfoValue = $this->input->post('dispatchInfoValue'); 
            
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){ 
                if(date('Y-m',strtotime($sdate)) != date('Y-m',strtotime($edate))){
                    $this->session->set_flashdata('searchError', 'If you want to filter with week than both date must be same month.');
                    redirect(base_url('admin/outside-dispatch'));
                } else {
                    $weeks = explode(',',$week);
                    //$sdate = $weeks[0];
                    //$edate = $weeks[1];
                    $sdate = date('Y-m',strtotime($sdate)).$weeks[0];
                    $edate = date('Y-m',strtotime($edate)).$weeks[1];
                }
            } 
        } else {
            $data['dispatchOutside'] = array();
        }
        
    	$data['dispatchOutside'] = $this->Comancontroler_model->get_dispatchOutside_by_filter($sdate,$edate,$company,$truckingCompany,$driver,$status,$invoice,$tracking,$dispatchInfoValue,$dispatchInfo,'');

    	$subInvoice = $dispatchArr = array();
    	if($data['dispatchOutside']){
    	    for($i=0;count($data['dispatchOutside']) > $i;$i++){
    	        $data['dispatchOutside'][$i]['sortcolumn'] = str_replace('-','',$data['dispatchOutside'][$i]['pudate']);
    	        if(in_array($data['dispatchOutside'][$i]['id'], $dispatchArr)) { continue; }
    	        $dispatchArr[] = $data['dispatchOutside'][$i]['id'];
				// print_r($data['dispatchOutside'][$i]['invoice']);exit;
    	        if($data['dispatchOutside'][$i]['childInvoice'] != '') { $subInvoice[] = $data['dispatchOutside'][$i]['invoice']; }
    	        
    	        // $dispatchInfo = $this->Comancontroler_model->get_data_by_column('dispatchid',$data['dispatchOutside'][$i]['id'],'dispatchOutsideExtraInfo','pd_date,pd_city,pd_location,pd_time,pd_addressid','pd_order','desc','1');
				
				$dispatchInfo = $this->Comancontroler_model->get_data_by_column('dispatchid',$data['dispatchOutside'][$i]['id'],'dispatchOutsideExtraInfo','pd_date,pd_city,pd_location,pd_time,pd_addressid,pd_type','pd_order','ASC','');
				
				if($dispatchInfo){
					foreach($dispatchInfo as $dis){
						// $data['dispatchOutside'][$i]['pd_date'] = $dis['pd_date'];
						// $data['dispatchOutside'][$i]['pd_city'] = $dis['pd_city'];
						// $data['dispatchOutside'][$i]['pd_location'] = $dis['pd_location'];
						// $data['dispatchOutside'][$i]['pd_time'] = $dis['pd_time'];

						//naveed added
						$data['dispatchOutside'][$i]['dispatchInfo'][] = [
							'pd_date' => $dis['pd_date'],
							'pd_city' => $dis['pd_city'],
							'pd_location' => $dis['pd_location'],
							'pd_time' => $dis['pd_time'],
							'pd_addressid' => $dis['pd_addressid'],
							'pd_type' => $dis['pd_type']

						];
						//naveed added
		
					}
				} else {
				    $data['dispatchOutside'][$i]['pd_date'] = $data['dispatchOutside'][$i]['pd_city'] = $data['dispatchOutside'][$i]['pd_location'] = $data['dispatchOutside'][$i]['pd_time'] = $data['dispatchOutside'][$i]['pd_addressid'] = '';
					
					//naveed added
					$data['dispatchOutside'][$i]['dispatchInfo'] = [
						[
							'pd_date' => '',
							'pd_city' => '',
							'pd_location' => '',
							'pd_time' => '',
							'pd_addressid' => ''
						]
					];
					//naveed added
		
				}
    	    }
    	}
    	if($subInvoice){
			$subDis = $this->Comancontroler_model->get_dispatchOutside_by_filter('','','','','','','','','','',$subInvoice);
						// print_r($subDis);exit;

			if($subDis){
				foreach($subDis as $sd){
				    $sd['sortcolumn'] = str_replace('-','',$sd['pudate']);
				    if(in_array($sd['id'], $dispatchArr)) { continue; }
    	            $dispatchArr[] = $sd['id'];
    	        
					$sd['pd_date'] = $sd['pd_city'] = $sd['pd_location'] = $sd['pd_time'] = '';
					$data['dispatchOutside'][] = $sd;
				}
			}
		}
		$invoiceWiseTotal = [];
		foreach ($data['dispatchOutside'] as $key) {
			$groupKey = $key['parentInvoice'] ?: $key['invoice'];

			if (!isset($invoiceWiseTotal[$groupKey])) {
				$invoiceWiseTotal[$groupKey] = ['rate' => 0, 'parate' => 0];
			}

			$invoiceWiseTotal[$groupKey]['rate'] += $key['rate'];
			$invoiceWiseTotal[$groupKey]['parate'] += $key['parate'];
		}
		$data['invoiceWiseTotal'] = $invoiceWiseTotal;

    	$data['startDate'] = $sdate;
    	$data['endDate'] = $edate;
    	
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
		$data['companiesForSelect'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
	    $data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/outsideDispatch',$data);
    	$this->load->view('admin/layout/footer');
	}
	public function index() {
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    //$data['dispatchInfo'] = $this->dispatchInfo;
	    $data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');
        /********* update status *********/
        if($this->input->post('statusonly') && $this->input->post('statusid'))	{
            $statusonly = $this->input->post('statusonly');
            $statusid = $this->input->post('statusid');
            if($statusonly!='' && $statusid > 0){
                $updatedata = array('status'=>$statusonly);
                $this->Comancontroler_model->update_table_by_id($statusid,'dispatchOutside',$updatedata);
                die('updated');
            }
        }
        
        $sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        $edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
		$company = $truckingCompany = $driver = $status = $invoice = $tracking = $dispatchInfoValue = $dispatchInfo = '';
        
        ////// generate csv
		if($this->input->post('generateCSV') || $this->input->post('generateXls')){
            $truckingCompany = $this->input->post('truckingCompanies');
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
            
            $dispatch = $this->Comancontroler_model->downloadDispatchOutsideCSV($sdate,$edate,$truckingCompany,$driver,$status,$invoice,$tracking);
            $dispatchInfo = $this->Comancontroler_model->get_data_by_column('status','Active','dispatchInfo','title','title','asc');
            $expenses = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','title,type','title','asc');
			
			// Data to be written to the CSV file (example data)
			$heading = array('Dispatch ID','Trucking Company','Driver','Booked Under','Booked Under New', 'Shipment Type','Trucking Equipment','Drayage Equipment','Pick Up Date','Pick Up Appointment Type','Pick Up Time','Pick Up City','Pick Up Company','Pick Up Address','Pick Up Description','Pick Up Quantity','Pick Up Weight','Pick Up Commodity','Pick Up','Pickup Notes','Drop Off Date','Drop Off Appointment Type','Drop Off Time','Drop Off Quantity','Drop Off City','Drop Off Company','Drop Off Address','Drop Off Weight', 'Drop Off Description','Drop Off','Driver Notes','Rate','PA Rate','Rate Lumper','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Driver Assist','Shipment Notes','Shipment Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Carrier Invoice Date','Carrier Payout Date','Sub Invoice','Delivered', 'Customer BOL', 'Customer RC', 'Customer Payment Proof', 'Carrier Payment Type', 'Factoring Type', 'Factoring companies', 'Carrier Invoice', 'Invoice Ref No','Expenses');
			foreach($expenses as $ex){ $heading[] = $ex['title']; }
			$heading[] = 'Dispatch Infos';
			foreach($dispatchInfo as $di){ $heading[] = $di['title']; }
			$data = array($heading);
			
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
					$childDispatch = $this->Comancontroler_model->downloadDispatchOutsideCSV('','','','','','','',$subInvoice);
					if($childDispatch) {
						foreach($childDispatch as $row){
							if(in_array($row['id'], $dispatchArr)) { continue; }
							$dispatchArr[] = $row['id'];
							$dispatch[] = $row;
						}
					}
				}
				
				foreach($dispatch as $row){
					$dispatchMeta = json_decode($row['dispatchMeta'],true);
				    $pudate = date('m/d/Y',strtotime($row['pudate']));
					  if($row['pudate']!='0000-00-00') {
				    //    $dodate = date('m/d/Y',strtotime($row['dodate']));
   					   	$pudate = '="' . date('m-d-Y', strtotime($row['pudate'])) . '"';
				   }
				   $dodate = $invoiceDate = $expectPayDate = $carrierPayoutDate = '0000-00-00';
				   if($row['dodate']!='0000-00-00') {
				    //    $dodate = date('m/d/Y',strtotime($row['dodate']));
   					   	$dodate = '="' . date('m-d-Y', strtotime($row['dodate'])) . '"';
				   }
				   if($row['invoiceDate']!='0000-00-00') {
				    //    $invoiceDate = date('m/d/Y',strtotime($row['invoiceDate']));
					   	$invoiceDate = '="' . date('m-d-Y', strtotime($row['invoiceDate'])) . '"';
				   }
				   if($row['expectPayDate']!='0000-00-00') {
				    //    $expectPayDate = date('m/d/Y',strtotime($row['expectPayDate']));
					   $expectPayDate = '="' . date('m-d-Y', strtotime($row['expectPayDate'])) . '"';
				   }
				   if($row['carrierPayoutDate']!='0000-00-00') {
				    //    $carrierPayoutDate = date('m/d/Y',strtotime($row['carrierPayoutDate']));
					   $carrierPayoutDate = '="' . date('m-d-Y', strtotime($row['carrierPayoutDate'])) . '"';
				   }
				   
				   $invReady = $dispatchMeta['invoiceReadyDate'];
				   if(trim($invReady) != ''){ 
						// $invReady = date('m/d/Y',strtotime($invReady)); 
						$invReady = '="' . date('m-d-Y', strtotime($invReady)) . '"';
					}
				   $invPaid = $dispatchMeta['invoicePaidDate'];
				   if(trim($invPaid) != ''){ 
					// $invPaid = date('m/d/Y',strtotime($invPaid)); 
					$invPaid = '="' . date('m-d-Y', strtotime($invPaid)) . '"';
					}
				   $invClosed = $dispatchMeta['invoiceCloseDate'];
				   if(trim($invClosed) != ''){ 
					// $invClosed = date('m/d/Y',strtotime($invClosed));
					$invClosed = '="' . date('m-d-Y', strtotime($invClosed)) . '"';
				 }
				    $carrierInvDate = $dispatchMeta['custInvDate'];
				   if(trim($carrierInvDate) != ''){ 
					// $invClosed = date('m/d/Y',strtotime($invClosed));
					$carrierInvDate = '="' . date('m-d-Y', strtotime($carrierInvDate)) . '"';
				 }
				$shipmentType = $dispatchMeta['invoicePDF'];
				$invoiceTrucking = $dispatchMeta['invoiceTrucking'];
				$invoiceDrayage = $dispatchMeta['invoiceDrayage'];
				$appointmentTypeP = $dispatchMeta['appointmentTypeP'];

				$metaDescriptionP = $dispatchMeta['metaDescriptionP'];
				$quantityP = $dispatchMeta['quantityP'];
				$weightP = $dispatchMeta['weightP'];
				$commodityP = $dispatchMeta['commodityP'];

				$appointmentTypeD = $dispatchMeta['appointmentTypeD'];
				$quantityD = $dispatchMeta['quantityD'];
				$metaDescriptionD = $dispatchMeta['metaDescriptionD'];
				$weightD = $dispatchMeta['weightD'];

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
					
					$fatoringCompanyId= $row['factoringCompany'];
					if($fatoringCompanyId>0){
						$factoringCompany=$this->Comancontroler_model->get_data_by_id($fatoringCompanyId,'factoringCompanies','company');
						if(isset($factoringCompany)){
							$factoringCompany=	$factoringCompany[0]['company'];
						}else{
							$factoringCompany = '';
						}
					}else{
						$factoringCompany = '';
					}

					if($dispatchMeta['carrierInvoiceCheck'] == 1){
						$carrierInvoiceCheck = 'AK';
					}else{
						$carrierInvoiceCheck = '';
					}
					// $otherChildInvoice = $dispatchMeta['otherChildInvoice'];
					$childInvoices = !empty($row['childInvoice']) ? explode(',', $row['childInvoice']) : [];
					$otherInvoices = !empty($dispatchMeta['otherChildInvoice']) ? explode(',', $dispatchMeta['otherChildInvoice']) : [];
					$allInvoices = array_filter(array_unique(array_merge($childInvoices, $otherInvoices)));
					// $allChildInvoices = implode(',', $allInvoices);
					$allChildInvoices = '' . implode(', ', $allInvoices) . '';

				   $dataRow = array($row['id'],$row['ttruckingCompany'],$row['dname'],$row['bbookedUnder'],$row['bookUnderNew'], $shipmentType, $invoiceTrucking,$invoiceDrayage,$pudate, $appointmentTypeP, $row['ptime'],$row['ppcity'],$row['pplocation'],$row['paddress'], $metaDescriptionP, $quantityP, $weightP, $commodityP, $row['pcode'],cleanSpace($row['pnotes']),$dodate,$appointmentTypeD,$row['dtime'],$quantityD,$row['ddcity'],$row['ddlocation'],$row['daddress'],$weightD,$metaDescriptionD,$row['dcode'],cleanSpace($row['dnotes']),$row['rate'],$row['parate'],$row['rateLumper'],$row['ccompany'],$trailer,$row['tracking'],$row['invoice'],$row['dWeek'],$row['payoutAmount'],$invoiceDate,$row['invoiceType'],$expectPayDate,$row['dassist'],$row['status'],$row['driver_status'],cleanSpace($row['notes']),$invReady,$invPaid,$invClosed,cleanSpace($row['invoiceNotes']),$carrierInvDate,$carrierPayoutDate,$allChildInvoices, $row['delivered'], $row['bol'], $row['rc'], $row['gd'], $row['carrierPaymentType'], $row['factoringType'], $factoringCompany, $carrierInvoiceCheck, $row['carrierInvoiceRefNo']);
					$dataRow[] = 'Expenses';    
					foreach ($expenses as $ex) {
						$value = '';
						if (!empty($dispatchMeta['expense'])) {
							foreach ($dispatchMeta['expense'] as $diVal) {
								if (trim($diVal[0]) === trim($ex['title'])) {
									$value = $diVal[1];
									break;
								}
							}
						}
						$dataRow[] = $value; 
					}

					$dataRow[] = 'Dispatch Infos';
					foreach($dispatchInfo as $di){ 
						$disInfo = '';
						if($dispatchMeta['dispatchInfo']) { 
							foreach($dispatchMeta['dispatchInfo'] as $diVal) {
								if($diVal[0] == $di['title']){ $disInfo = $diVal[1]; }
							}
						}
						$dataRow[] = $disInfo;
					}
					$data[] = $dataRow;
				}
			}
            // echo "<pre>";print_r($data);exit;
			if($this->input->post('generateCSV')){
				$fileName = "OutsideDispatch_".$sdate."_".$edate.".csv"; 
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
				$fileName = "OutsideDispatch_".$sdate."_".$edate.".xlsx";   //"data_$date.xlsx";
				// Generate Excel file using the library
				$this->excel_generator->generateExcel($data, $fileName);
			}

			// Delete the file from the server
			unlink($fileName);
			exit;
			die('csv');
        }
		
        if($this->input->post('search'))	{
			$company = $this->input->post('company');    
            $truckingCompany = $this->input->post('truckingCompanies');
            $driver = $this->input->post('driver'); 
            $dispatchInfo = $this->input->post('dispatchInfo'); 
            $dispatchInfoValue = $this->input->post('dispatchInfoValue'); 
            
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){ 
                if(date('Y-m',strtotime($sdate)) != date('Y-m',strtotime($edate))){
                    $this->session->set_flashdata('searchError', 'If you want to filter with week than both date must be same month.');
                    redirect(base_url('admin/outside-dispatch'));
                } else {
                    $weeks = explode(',',$week);
                    //$sdate = $weeks[0];
                    //$edate = $weeks[1];
                    $sdate = date('Y-m',strtotime($sdate)).$weeks[0];
                    $edate = date('Y-m',strtotime($edate)).$weeks[1];
                }
            } 
        } else {
            $data['dispatchOutside'] = array();
        }
        
    	$data['dispatchOutside'] = $this->Comancontroler_model->get_dispatchOutside_by_filter($sdate,$edate,$company,$truckingCompany,$driver,$status,$invoice,$tracking,$dispatchInfoValue,$dispatchInfo,'');

    	$subInvoice = $subChildInvoice = $subDispatch = $dispatchArr = array();
    	if($data['dispatchOutside']){
    	    for($i=0;count($data['dispatchOutside']) > $i;$i++){
    	        $data['dispatchOutside'][$i]['sortcolumn'] = str_replace('-','',$data['dispatchOutside'][$i]['pudate']);
    	        if(in_array($data['dispatchOutside'][$i]['id'], $dispatchArr)) { continue; }
    	        $dispatchArr[] = $data['dispatchOutside'][$i]['id'];
				$dispatchMeta = json_decode($data['dispatchOutside'][$i]['dispatchMeta'],true);
				// print_r($dispatchMeta['otherChildInvoice']);exit;
    	        if($data['dispatchOutside'][$i]['childInvoice'] != '') { $subInvoice[] = $data['dispatchOutside'][$i]['invoice']; }
    	        
				// if(!empty($data['dispatchOutside'][$i]['childInvoice'])){
				// 	$childInvoices = explode(',', $data['dispatchOutside'][$i]['childInvoice']);
				// 	$subChildInvoice = array_merge($subChildInvoice ?? [], $childInvoices);
				// }

				// if(!empty($dispatchMeta['otherChildInvoice'])){
				// 	$otherChildInvoices = explode(',', $dispatchMeta['otherChildInvoice']);
				// 	$subDispatch = array_merge($subDispatch ?? [], $otherChildInvoices);
				// }
				
    	        // $dispatchInfo = $this->Comancontroler_model->get_data_by_column('dispatchid',$data['dispatchOutside'][$i]['id'],'dispatchOutsideExtraInfo','pd_date,pd_city,pd_location,pd_time,pd_addressid','pd_order','desc','1');
				
				$dispatchInfo = $this->Comancontroler_model->get_data_by_column('dispatchid',$data['dispatchOutside'][$i]['id'],'dispatchOutsideExtraInfo','pd_date,pd_city,pd_location,pd_time,pd_addressid,pd_type','pd_order','ASC','');
				
				if($dispatchInfo){
					foreach($dispatchInfo as $dis){
						// $data['dispatchOutside'][$i]['pd_date'] = $dis['pd_date'];
						// $data['dispatchOutside'][$i]['pd_city'] = $dis['pd_city'];
						// $data['dispatchOutside'][$i]['pd_location'] = $dis['pd_location'];
						// $data['dispatchOutside'][$i]['pd_time'] = $dis['pd_time'];

						//naveed added
						$data['dispatchOutside'][$i]['dispatchInfo'][] = [
							'pd_date' => $dis['pd_date'],
							'pd_city' => $dis['pd_city'],
							'pd_location' => $dis['pd_location'],
							'pd_time' => $dis['pd_time'],
							'pd_addressid' => $dis['pd_addressid'],
							'pd_type' => $dis['pd_type']

						];
						//naveed added
		
					}
				} else {
				    $data['dispatchOutside'][$i]['pd_date'] = $data['dispatchOutside'][$i]['pd_city'] = $data['dispatchOutside'][$i]['pd_location'] = $data['dispatchOutside'][$i]['pd_time'] = $data['dispatchOutside'][$i]['pd_addressid'] = '';
					
					//naveed added
					$data['dispatchOutside'][$i]['dispatchInfo'] = [
						[
							'pd_date' => '',
							'pd_city' => '',
							'pd_location' => '',
							'pd_time' => '',
							'pd_addressid' => ''
						]
					];
					//naveed added
		
				}
    	    }
    	}
    	if($subInvoice){
			$subDis = $this->Comancontroler_model->get_dispatchOutside_by_filter('','','','','','','','','','',$subInvoice);
			if($subDis){
				foreach($subDis as $sd){
				    $sd['sortcolumn'] = str_replace('-','',$sd['pudate']);
				    if(in_array($sd['id'], $dispatchArr)) { continue; }
    	            $dispatchArr[] = $sd['id'];
    	        
					$sd['pd_date'] = $sd['pd_city'] = $sd['pd_location'] = $sd['pd_time'] = '';
					$data['dispatchOutside'][] = $sd;
				}
			}
		}

		// $allChildren = [];
		// if(!empty($subChildInvoice)){
		// 	$subDis = $this->Comancontroler_model->get_dispatchOutside_by_filter('', '', '', '', '', '', '', '', '', '', $subChildInvoice
		// 	);
		// 	if($subDis){ $allChildren = array_merge($allChildren, $subDis); }
		// }
		// if(!empty($subDispatch)){
		// 	$subDis2 = $this->Comancontroler_model->get_data_by_where_in ('invoice',$subDispatch,'dispatch','*');
		// 	if($subDis2){ $allChildren = array_merge($allChildren, $subDis2); }
		// }
		// $allChildren = array_map("unserialize", array_unique(array_map("serialize", $allChildren)));
		// $data['childInvoices'] = $allChildren;

		$invoiceWiseTotal = [];
		foreach ($data['dispatchOutside'] as $key) {
			$groupKey = $key['parentInvoice'] ?: $key['invoice'];

			if (!isset($invoiceWiseTotal[$groupKey])) {
				$invoiceWiseTotal[$groupKey] = ['rate' => 0, 'parate' => 0];
			}

			$invoiceWiseTotal[$groupKey]['rate'] += $key['rate'];
			$invoiceWiseTotal[$groupKey]['parate'] += $key['parate'];
		}
		$data['invoiceWiseTotal'] = $invoiceWiseTotal;

    	$data['startDate'] = $sdate;
    	$data['endDate'] = $edate;
    	
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
		$data['companiesForSelect'] = $this->Comancontroler_model->get_data_by_column('paymenTerms !=','Deleted','companies','*','company','asc');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
	    $data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/outsideDispatch',$data);
    	$this->load->view('admin/layout/footer');
	}

	public function getChildInvoices(){
		// echo 'childinvoices';exit;
		$id = $this->input->post('id');
		$sql="SELECT * FROM dispatchOutside WHERE id ='$id'";
		$result = $this->db->query($sql)->row();
		$subInvoice = $subChildInvoice = $subDispatch = $dispatchArr = array();
		if(!empty($result->childInvoice)){
			$childInvoices = explode(',', $result->childInvoice);
			$subChildInvoice = array_merge($subChildInvoice ?? [], $childInvoices);
		}
		
		$dispatchMeta = json_decode($result->dispatchMeta,true);
		if(!empty($dispatchMeta['otherChildInvoice'])){
			$otherChildInvoices = explode(',', $dispatchMeta['otherChildInvoice']);
			$subDispatch = array_merge($subDispatch ?? [], $otherChildInvoices);
		}
		
		$allChildren = [];
		if(!empty($subChildInvoice)){
			$subDis = $this->Comancontroler_model->get_data_by_where_in ('invoice',$subChildInvoice,'dispatchOutside','*');
			if($subDis){ $allChildren = array_merge($allChildren, $subDis); }
		}
		// print_r($allChildren);exit;
		if(!empty($subDispatch)){
			$subDis2 = $this->Comancontroler_model->get_data_by_where_in ('invoice',$subDispatch,'dispatch','*');
			if($subDis2){ $allChildren = array_merge($allChildren, $subDis2); }
		}
		$allChildren = array_map("unserialize", array_unique(array_map("serialize", $allChildren)));
		$this->output->set_content_type('application/json')->set_output(json_encode(['childInvoices' => $allChildren]));
	}

	public function outsideDispatchAdd() {
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $userid = $this->session->userdata('adminid');
	    
        $data['truckingArr'] = $this->truckingArr;
		$data['truckingEquipments'] = $this->Comancontroler_model->get_data_by_table('truckingEquipments');

        $data['dispatchInfo'] = $this->Comancontroler_model->get_data_by_column('status','Active','dispatchInfo','title','title','asc');
        //$data['expenses'] = $this->expenses;
        $data['expenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','title,type','title','asc');
	    $data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');
		
		if($this->input->post('save'))	{ 
			$this->form_validation->set_rules('pudate', 'PU date','required|min_length[9]');
			$this->form_validation->set_rules('pcity', 'pickup city','required|min_length[1]');
			$this->form_validation->set_rules('dcity', 'drop off city','required|min_length[1]');
			$this->form_validation->set_rules('company', 'company','required|min_length[1]'); 
			$this->form_validation->set_rules('dlocation', 'drop off location','required|min_length[1]'); 
			$this->form_validation->set_rules('plocation', 'pick up location','required|min_length[1]');  
			
			$pudate1 = $this->input->post('pudate1');
			if(!is_array($pudate1)){ $pudate1 = array(); }
			$dodate1 = $this->input->post('dodate1'); 
			if(!is_array($dodate1)){ $dodate1 = array(); }
			
			$pudate = $this->input->post('pudate');
			$driver = $this->input->post('driver');
				// echo $driver;exit;
			$inv_first = '';
			$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'dispatchOutside');
			if(empty($driver_trip)) { $inv_last = 1; }
			else { $inv_last = count($driver_trip) + 1; }
			if($inv_last < 10) {  $inv_last = '0'.$inv_last; }
			
			$driver_info = $this->Comancontroler_model->get_data_by_id($driver,'drivers');
			if(!empty($driver_info)) {
				$inv_first = $driver_info[0]['dcode'];
			}
			$inv_middel = date('mdy',strtotime($pudate));
			$invoice = $inv_first.''.$inv_middel.'-'.$inv_last;
			
			if($invoice == '' || $inv_first==''){
				$this->form_validation->set_rules('invoice', 'invoice','required'); 
				$set_message = 'Invoice number must not blank.';
				if($inv_first == ''){ $set_message = 'Driver code is empty.'; }
				$this->form_validation->set_message('required',$set_message); 
			}
			
			$invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
			/*
			$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatchOutside','id');
			if($invoice == '' || $inv_first==''){
				$this->form_validation->set_rules('invoice', 'invoice','required'); 
				$set_message = 'Invoice number must not blank.';
				if($inv_first == ''){ $set_message = 'Driver code is empty.'; }
				$this->form_validation->set_message('required',$set_message); 
			}
			elseif($invoiceInfo){
				$checkInvoice = 'false'; $invoiceCount = count($driver_trip) + 2;
				while($checkInvoice == 'false'){
					$invoiceCountTxt = $invoiceCount;
					if($invoiceCount < 10) { $invoiceCountTxt = '0'.$invoiceCount; }
					$invoice = $inv_first.''.$inv_middel.'-'.$invoiceCountTxt;
					$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatchOutside','id');
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
            else { 
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
				
				
				$week = date('M',strtotime($pudate)).' W';
				$day = date('d',strtotime($pudate));
				if($day < 9) { $w = '1'; }
				elseif($day < 16){ $w = '2'; }
				elseif($day < 24){ $w = '3'; }
				else { $w = '4'; }
				$week .= $w; 
				
				$parate = $this->input->post('parate');
				if(!is_numeric($parate)) { $parate = 0; }
				
				$invoiceType = ''; //$companyInfo[0]['paymenTerms'];
				/*
				$payoutRate = $companyInfo[0]['payoutRate'];
				if(!is_numeric($payoutRate)) { $payoutRate = 0; }
				$payoutAmount = $payoutRate * $parate;
				$payoutAmount = round($payoutAmount,2);
				*/
				$payoutAmount = 0;
				
				
				$dispatchMeta = array('expense'=>array(),'dispatchInfo'=>array());
				$dispatchMeta['carrierInvoiceCheck'] = $this->input->post('carrierInvoiceCheck');
				$expenseName = $this->input->post('expenseName');
				$expensePrice = $this->input->post('expensePrice');
				if(is_array($expenseName)) {
				    for($i=0;$i<count($expenseName);$i++){
				        $dispatchMeta['expense'][] = array($expenseName[$i],$expensePrice[$i]);
				    }
				}
				$dispatchInfoName = $this->input->post('dispatchInfoName');
				$dispatchInfoValue = $this->input->post('dispatchInfoValue');
				if(is_array($dispatchInfoName)) {
				    for($i=0;$i<count($dispatchInfoName);$i++){
						if (!empty($dispatchInfoValue[$i])) {
				        	$dispatchMeta['dispatchInfo'][] = array($dispatchInfoName[$i],$dispatchInfoValue[$i]);
						}
				    }
				}
				
				$dispatchMeta['pickup'] = $this->input->post('pickup');
				$dispatchMeta['pPort'] = $this->input->post('pPort');
				$dispatchMeta['pPortAddress'] = $this->input->post('pPortAddress');
				$dispatchMeta['dropoff'] = $this->input->post('dropoff');
				$dispatchMeta['dPort'] = $this->input->post('dPort');
				$dispatchMeta['dPortAddress'] = $this->input->post('dPortAddress');
				
				$dispatchMeta['invoicePDF'] = $this->input->post('invoicePDF');
				$dispatchMeta['drayageType'] = $this->input->post('drayageType');
				$dispatchMeta['invoiceDrayage'] = $this->input->post('invoiceDrayage');
				$dispatchMeta['invoiceTrucking'] = $this->input->post('invoiceTrucking');
				
				$dispatchMeta['appointmentTypeP'] = $this->input->post('appointmentTypeP');
				$dispatchMeta['quantityP'] = $this->input->post('quantityP');
				$dispatchMeta['commodityP'] = $this->input->post('commodityP');
				$dispatchMeta['metaDescriptionP'] = $this->input->post('metaDescriptionP');
				$dispatchMeta['weightP'] = $this->input->post('weightP');
				
				$dispatchMeta['appointmentTypeD'] = $this->input->post('appointmentTypeD');
				$dispatchMeta['quantityD'] = $this->input->post('quantityD');
				$dispatchMeta['metaDescriptionD'] = $this->input->post('metaDescriptionD');
				$dispatchMeta['weightD'] = $this->input->post('weightD');
				$dispatchMeta['erInformation'] = $this->input->post('erInformation');
				$dispatchMeta['driver_name'] = $this->input->post('driver_name');
				$dispatchMeta['driver_contact'] = $this->input->post('driver_contact');
				
				$dispatchMetaJson = json_encode($dispatchMeta);
				
				$pamargin = (float) $this->input->post('parate') - (float) $this->input->post('rate');
				
				$insert_data=array(
				    'driver'=>$driver,
				    'userid'=>$userid,
				    'pudate'=>$pudate,
				    'bookedUnderNew'=>$this->input->post('bookedUnderNew'),
				    'truckingCompany'=>$this->input->post('truckingCompany'),
				    'trip'=>$this->input->post('trip'),
				    'delivered'=>$this->input->post('delivered'),
					'pcity'=>$pcity,
				    'dcity'=>$dcity,
				    'dodate'=>$this->input->post('dodate'),
				    'rate'=>$this->input->post('rate'),
				    'parate'=>$this->input->post('parate'),
				    //'rateLumper'=>$this->input->post('rateLumper'),
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
					'invoiceNotes'=>$this->input->post('invoiceNotes'),
				    'pnotes'=>$this->input->post('pnotes'),
				    'dnotes'=>$this->input->post('dnotes'),
				    'pamargin'=>$pamargin,
				    'carrierPayoutDate'=>$this->input->post('carrierPayoutDate'),
				    'carrierPayoutCheck'=>$this->input->post('carrierPayoutCheck'),
				    //'detention'=>$this->input->post('detention'),
				    //'dassist'=>$this->input->post('dassist'),
				    'driver_status'=>$this->input->post('driver_status'),
				    'dispatchMeta'=>$dispatchMetaJson,
				    'status'=>$this->input->post('status'),
				    'rdate'=>date('Y-m-d H:i:s')
				);
			
				$res = $this->Comancontroler_model->add_data_in_table($insert_data,'dispatchOutside'); 
				if($res){
				//     $reminder = array(
				// 	'dispatch_id'=>$res,
				// 	'dispatch_type'=>'outside',
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
						
    					$pickup1 = $this->input->post('pickup1');
    					$pPort1 = $this->input->post('pPort1')?? '0';
    					$pPortAddress1 = $this->input->post('pPortAddress1')?? '0';
    					$dropoff1 = $this->input->post('dropoff1');
    					$dPort1 = $this->input->post('dPort1');
    					$dPortAddress1 = $this->input->post('dPortAddress1');
						
						
    					$appointmentTypeP1 = $this->input->post('appointmentTypeP1');
    					$quantityP1 = $this->input->post('quantityP1');
    					$metaDescriptionP1 = $this->input->post('metaDescriptionP1');
    					$weightP1 = $this->input->post('weightP1');
						$commodityP1 = $this->input->post('commodityP1');

    					$appointmentTypeD1 = $this->input->post('appointmentTypeD1');
    					$quantityD1 = $this->input->post('quantityD1');
    					$metaDescriptionD1 = $this->input->post('metaDescriptionD1');
    					$weightD1 = $this->input->post('weightD1');
						
    					
    					for($i=0;$i<count($pudate1);$i++){
				          if($pudate1[$i]!='') {
				            $pcodeVal1 = implode('~-~',$pcode1[$pcodename[$i]]); 
				            $pcity1 = $this->check_city($check_pcity1[$i]);
				            $plocation1 = $this->check_location($check_plocation1[$i]); 
				            
							$pd_meta = array();
							$pd_meta['appointmentType'] = $appointmentTypeP1[$i];
							$pd_meta['quantityP'] = $quantityP1[$i];
							$pd_meta['metaDescriptionP'] = $metaDescriptionP1[$i];
							$pd_meta['weightP'] = $weightP1[$i];
							$pd_meta['commodityP'] = $commodityP1[$i];

				            $pdMetaJson = json_encode($pd_meta);
							
    					    $extraData = array(
    					    'dispatchid'=>$res,
    					    'pd_location'=>$plocation1,
    					    'pd_title'=>$pickup1[$i],
    					    'pd_port'=>$pPort1[$i],
    					    'pd_portaddress'=>$pPortAddress1[$i],
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
    					    $this->Comancontroler_model->add_data_in_table($extraData,'dispatchOutsideExtraInfo');
				          }
    					}
						for($i=0;$i<count($dodate1);$i++){
				          if($dodate1[$i]!='') { 
				            $dcodeVal1 = implode('~-~',$dcode1[$dcodename[$i]]);
				            $dcity1 = $this->check_city($check_dcity1[$i]);  
				            $dlocation1 = $this->check_location($check_dlocation1[$i]);
				            
							$pd_meta = array();
							$pd_meta['appointmentType'] = $appointmentTypeD1[$i];
							$pd_meta['quantityD'] = $quantityD1[$i];
							$pd_meta['metaDescriptionD'] = $metaDescriptionD1[$i];
							$pd_meta['weightD'] = $weightD1[$i];
							
				            $pdMetaJson = json_encode($pd_meta);
							
    					    $extraData = array(
    					    'dispatchid'=>$res,
    					    'pd_location'=>$dlocation1,  
    					    'pd_title'=>$dropoff1[$i],
    					    'pd_port'=>$dPort1[$i],
    					    'pd_portaddress'=>$dPortAddress1[$i],
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
    					    $this->Comancontroler_model->add_data_in_table($extraData,'dispatchOutsideExtraInfo');
				          }
    					}
				    }
				    
				    
				    /*********** upload documents *********/
				    //$config['upload_path'] = 'assets/outside-dispatch/';
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
						$config['upload_path'] = 'assets/outside-dispatch/bol/';
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
								$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
							}
						}
					}
                    /*if(!empty($_FILES['bol_d']['name'])){
						$config['upload_path'] = 'assets/outside-dispatch/bol/';
                        $config['file_name'] = $fileName1.'-BOL-'.$fileName2; //$_FILES['bol_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('bol_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$res,'type'=>'bol','fileurl'=>$bol);
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
                        }
                    } */
					
					$rcFilesCount = count($_FILES['rc_d']['name']);
					if($rcFilesCount > 0) {  
						$rcFiles = $_FILES['rc_d'];
						$config['upload_path'] = 'assets/outside-dispatch/rc/';
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
								$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
								}
						}
					}
					/*if(!empty($_FILES['rc_d']['name'])){
						$config['upload_path'] = 'assets/outside-dispatch/rc/';
                        $config['file_name'] = $fileName1.'-RC-'.$fileName2; //$_FILES['rc_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('rc_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$res,'type'=>'rc','fileurl'=>$bol);
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
                        }
                    } */
					if(!empty($_FILES['gd_d']['name'])){
						$config['upload_path'] = 'assets/outside-dispatch/gd/';
                        $config['file_name'] = $fileName1.'-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('gd_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$res,'type'=>'gd','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
                        }
                    }
					if(!empty($_FILES['carrier_gd_d']['name'])){
						$config['upload_path'] = 'assets/outside-dispatch/gd/';
						$config['file_name'] = $fileName1.'-CARRIER-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
						$this->load->library('upload',$config);
						$this->upload->initialize($config); 
						if($this->upload->do_upload('carrier_gd_d')){
							$uploadData = $this->upload->data();
							$bol = $uploadData['file_name'];
							$addfile = array('did'=>$id,'type'=>'carrierGd','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
							$changeField[] = array('Carrier Payment proof file','gdfile','Upload',$bol);
						}
					}
                
                    /*if(!empty($_FILES['carrierInvoice']['name'])){
						$config['upload_path'] = 'assets/outside-dispatch/carrierInvoice/';
                        $config['file_name'] = $fileName1.'-Carrier-Invoice-'.$fileName2; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('carrierInvoice')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$res,'type'=>'carrierInvoice','fileurl'=>$bol);
							$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
                        }
                    }*/
                    
                    $ciFilesCount = count($_FILES['carrierInvoice']['name']);
					if($ciFilesCount > 0) {  
						$ciFiles = $_FILES['carrierInvoice'];
						$config['upload_path'] = 'assets/outside-dispatch/carrierInvoice/';
						$config['file_name'] = $fileName1.'-Carrier-Invoice-'.$fileName2; //$_FILES['carrierInvoice']['name'];  
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
						for($i = 0; $i < $ciFilesCount; $i++){
							$_FILES['carrierInvoice']['name']     = $ciFiles['name'][$i];
							$_FILES['carrierInvoice']['type']     = $ciFiles['type'][$i];
							$_FILES['carrierInvoice']['tmp_name'] = $ciFiles['tmp_name'][$i];
							$_FILES['carrierInvoice']['error']     = $ciFiles['error'][$i];
							$_FILES['carrierInvoice']['size']     = $ciFiles['size'][$i]; 
					
							if ($this->upload->do_upload('carrierInvoice'))  { 
								$dataRc = $this->upload->data(); 
								$bol = $dataRc['file_name'];
								$addfile = array('did'=>$res,'type'=>'carrierInvoice','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($addfile,'documentsOutside');
							}
						}
					}
					
                
					$this->session->set_flashdata('item', '	PA Logistics  add successfully.');
                    //redirect(base_url('admin/outside-dispatch/add'));
                    redirect(base_url('admin/outside-dispatch/update/'.$res.'#submit'));
				}
 			   
			}
	    }

		
        $id = $this->uri->segment(4);
        if($id > 0){
          $data['duplicate'] = $this->Comancontroler_model->get_data_by_id($id,'dispatchOutside');
        } else {
          $data['duplicate'] = array();
        }
	    $data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
	    $data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
	    $data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
	    //$data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
	    $data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
	    $data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies');
		$data['drayageEquipments'] = $this->Comancontroler_model->get_data_by_table('drayageEquipments');
		// $data['truckingEquipments'] = $this->Comancontroler_model->get_data_by_table('truckingEquipments');
	    $data['erInformation'] = $this->Comancontroler_model->get_data_by_table('erInformation');
	    $data['booked_under'] = $this->Comancontroler_model->get_data_by_table('booked_under');
	  
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/outside_dispatch_add',$data);
    	$this->load->view('admin/layout/footer');
	}
	function uploadcsv_backup(){
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $data['error'] = array();
        $data['upload'] = '';
	    
	    if(isset($_GET['dummy']) && $_GET['dummy']=='csv'){
	        $data = array(
				array('Dispatch ID','Trucking Company','Driver','Booked Under','Pick Up Date','Pick Up Time','Pick Up City','Pick Up Company','Pick Up Address','Pick Up','Pickup Notes','Drop Off Date','Drop Off Time','Drop Off City','Drop Off Company','Drop Off Address','Drop Off','Driver Notes','Rate','PA Rate','Rate Lumper','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Driver Assist','Shipment Notes','Shipment Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Carrier Invoice Date', 'Carrier Payout Date','Sub Invoice', 'Delivered', 'Customer BOL', 'Customer RC', 'Customer Payment Proof', 'Carrier Payment Type', 'Factoring Type', 'Factoring companies', 'Carrier Invoice', 'Invoice Ref No')
			);
			
			$fileName = "OutsideDispatch_".date('Y-m-d').".csv"; 
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
	    
	    if(isset($_GET['driver-company']) && $_GET['driver-company']=='csv'){
	        $data = array(
				array('Driver','Trucking Company')
			);
			$drivers = $vehicles = array();
			$driverInfo = $this->Comancontroler_model->get_data_by_column('status','Active','drivers','dname');
			foreach($driverInfo as $val){
			    $drivers[] = $val['dname'];
			}
			$vehicleInfo = $this->Comancontroler_model->get_data_by_column('company !=','','truckingCompanies','company,owner');
			foreach($vehicleInfo as $val){
			    //$vehicles[] = $val['company'].' ('.$val['owner'].')';
			    $vehicles[] = $val['company'];
			}
			
			$maxCount = max(count($drivers), count($vehicles));

            for ($i = 0; $i < $maxCount; $i++) {
                $driver = isset($drivers[$i]) ? $drivers[$i] : '';
                $vehicle = isset($vehicles[$i]) ? $vehicles[$i] : '';
                $data[] = array($driver, $vehicle);
            }

			$fileName = "Driver-Trucking-Company-List.csv"; 
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
                    
					foreach ($csv as $row) { 
						//echo '<pre>';print_r($row);echo '</pre>';
						if($row[0]=='Dispatch ID' || count($row) < 34) {
							continue;
						}
						
						$did = $row[0];
						$trucking = $row[1];
						$bookedUnder = $row[3];
						$truckingEquipment = $row[4];
						$drayageEqipment = $row[5];
						$driver = $row[2];
						$pudate = str_replace('-','/',$row[6]);
                        $pdate = DateTime::createFromFormat('m/d/Y', $pudate);
                        if ($pdate !== false) { $pudate = $pdate->format('Y-m-d'); } 
                        else { $pudate = ''; }
                        
						$dodate = $row[13];
						if($dodate != '') {
							$ddate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$dodate));
                            if ($ddate !== false) { $dodate = $ddate->format('Y-m-d'); } 
                            else { $dodate = ''; }
						}
						$check_company = $row[23];
						$check_pcity = $row[8];
						$check_plocation = $row[9];
						$check_paddress = $row[10];
						$check_dcity = $row[15];
						$check_dlocation = $row[16];
						$check_daddress = $row[17];
						$tracking = $row[25];
						$paddressid = $daddressid = 0;
						
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
							$data['error'][] = 'Dispatch ID "'.$did.'" driver should not blank.';
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
						
						$invoiceType = trim($row[30]);
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }
						
						$pattern = '/^\d{2}[\/-]\d{2}[\/-]\d{4}$/';

						if($invoiceType != '' && (!preg_match($pattern, trim($row[36])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice ready date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if(trim($row[37]) != '' && (!preg_match($pattern, trim($row[37])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice paid date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if(trim($row[38]) != '' && (!preg_match($pattern, trim($row[38])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice close date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if($invoiceType != '' && $invoiceType != 'RTS' && trim($row[39]) == ''){
        			        $data['error'][] = 'Dispatch ID '.$did.' invoice description is required.';
							continue;
        			    }
						
						// check trucking company
						$truckingArray = explode('(',$trucking);
						$truckingName = trim($truckingArray[0]);
						$truckingInfo = $this->Comancontroler_model->check_value_in_table('company',$truckingName,'truckingCompanies');
						if(count($truckingInfo) != '1'){ $trucking = ''; }
						else { $trucking = $truckingInfo[0]['id']; }
						
						// check bookedUnder 
						$bookedUnderArray = explode('(',$bookedUnder);
						$bookedUnderName = trim($bookedUnderArray[0]);
						$bookedUnderInfo = $this->Comancontroler_model->check_value_in_table('company',$bookedUnderName,'truckingCompanies');
						if(count($bookedUnderInfo) != '1'){ $bookedUnder = ''; }
						else { $bookedUnder = $bookedUnderInfo[0]['id']; }
						
						
						if($check_company=='' || $check_pcity=='' || $check_dcity=='' || $check_plocation=='' || $check_dlocation=='' || $tracking=='' || $trucking=='') {
							$data['error'][] = 'Dispatch ID '.$did.' please fill all required fields.';
							continue;
						}
						
						// generate invoice
						$inv_first = '';
						$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'dispatchOutside');
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
							
							if(stristr($row[26],$inv_first.''.$inv_middel)) {
								$invoice = $row[26];
							}
							elseif(strtotime($pudate) < strtotime('2024-04-25')){
								$invoice = $row[26];
								$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatchOutside','id');
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
						
						$invoiceType = $row[30]; //$companyInfo[0]['paymenTerms'];
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }
						
						$dcity = $this->check_city($check_dcity);
						$pcity = $this->check_city($check_pcity);
						$plocation = $this->check_location($check_plocation);
						$dlocation = $this->check_location($check_dlocation);
						
						$parate = str_replace('$','',$row[21]);
						if(!is_numeric($parate)) { $parate = 0; }
						$rate = str_replace('$','',$row[20]);
						if(!is_numeric($rate)) { $rate = 0; }
						
						$payoutAmount = $row[28];
						
						$dispatchMeta = array('expense'=>array(),'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoiceCloseDate'=>'','invoicePaidDate'=>'');
						$dispatchMetaJson = json_encode($dispatchMeta);
						
						$week = date('M',strtotime($pudate)).' W';
    					$day = date('d',strtotime($pudate));
    					if($day < 9) { $w = '1'; }
    					elseif($day < 16){ $w = '2'; }
    					elseif($day < 24){ $w = '3'; }
    					else { $w = '4'; }
    					$week .= $w; 

						$fatoringCompany= $row['49'];
						if($fatoringCompany != ''){
							$factoringCompany=$this->Comancontroler_model->get_data_by_column('company',$fatoringCompany,'factoringCompanies');
							if(isset($factoringCompany[0]['id'])){
								$factoringCompanyId = $factoringCompany[0]['id'];
							}else{
								$factoringCompanyId = '0';
							}
						}else{
							$factoringCompanyId = '0';
						}

						$carrierPaymentType = $row[47];
						if (empty($carrierPaymentType)) {
							$carrierPaymentType = ''; 
						}
						$factoringType = $row[48];
						if (empty($factoringType)) {
							$factoringType = ''; 
						}
						
						$insert_data = array(
							'driver'=>$driver,
							//'bookedUnder'=>$bookedUnder,
							'truckingCompany'=>$trucking,
							'pudate'=>$pudate,
							'pcity'=>$pcity,
							'dcity'=>$dcity,
							'dodate'=>$dodate,
							'rate'=>$rate,
							'parate'=>$parate,
							'company'=>$company,
							'dlocation'=>$dlocation,
							'plocation'=>$plocation,
							'dcode'=>$row[18],
							'pcode'=>$row[11],
							'paddress'=>$row[10],
							'daddress'=>$row[17],
							'trailer'=>$row[24],
							'tracking'=>$row[25],
							'invoice'=>$invoice,
							'payoutAmount'=>$payoutAmount,
							'invoiceType'=>$invoiceType,
							'dWeek'=>$week, //$row[25],
							'ptime'=>$row[7],
							'dtime'=>$row[14],
							'notes'=>$row[35],
							'pnotes'=>$row[12],
							'dnotes'=>$row[19],
							'invoiceNotes'=>$row[39],
							'driver_status'=>$row[34],
							'status'=>$row[33],
							'delivered'=>$row[43],
							'bol'=>$row[44],
							'rc'=>$row[45],
							'gd'=>$row[46],
							'carrierPaymentType'=>$carrierPaymentType,
							'factoringType'=>$factoringType,
							'factoringCompany'=>$factoringCompanyId,
							'carrierInvoiceRefNo'=>$row[51]
						);
						//array('0.Dispatch ID','1.Trucking Company','2.Driver','3.Booked Under','4.Pick Up Date','5.Pick Up Time','6.Pick Up City','7.Pick Up Company','8.Pick Up Address','9.Pick Up','10.Pickup Notes','11.Drop Off Date','12.Drop Off Time','13.Drop Off City','14.Drop Off Company','15.Drop Off Address','16.Drop Off','17.Driver Notes','18.Rate','19.PA Rate','20.Rate Lumper','21.Company','22.Trailer','23.Tracking','24.Invoice','25.Week','26.Payout Amount','27.Invoice Date','28.Invoice Type','29.Expected Pay Date','30.Driver Assist','31.Status','32.Driver Status','33.Notes','34.Invoice Ready','35.Invoice Paid','36.Invoice Closed','37.Invoice Description','38.Carrier Payout Date','39.Sub Invoice')
						if(is_numeric($row[0]) && $row[0] > 1) { // update
							if($row[29] != 'TBD' && $row[29] != ''){
								$idate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[29]));
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
							/*if($row[29] != 'TBD' && $row[29] != ''){
								$epdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[29]));
                                if ($epdate !== false) { 
                                    $expectPayDate = $epdate->format('Y-m-d'); 
                                    $insert_data['expectPayDate'] = $expectPayDate;
                                }
							}*/
							if($row[41] != '0000-00-00' && $row[41] != ''){
								$cpdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[41]));
                                if ($cpdate !== false) { 
                                    $carrierPayoutDate = $cpdate->format('Y-m-d'); 
                                    $insert_data['carrierPayoutDate'] = $carrierPayoutDate;
                                    $insert_data['carrierPayoutCheck'] = '1';
                                }
							}
							
							
							$changeField = array();
							$getDispatch = $this->Comancontroler_model->get_data_by_column('id',$row[0],'dispatchOutside');
							if($getDispatch[0]['dispatchMeta'] != '') {
								$currentDiMeta = json_decode($getDispatch[0]['dispatchMeta'],true);
								$currentDiMeta['invoiceReadyDate'] = $currentDiMeta['invoicePaidDate'] = $currentDiMeta['invoiceCloseDate'] = '';
								$currentDiMeta['invoiceReady'] = $currentDiMeta['invoicePaid'] = $currentDiMeta['invoiceClose'] = $currentDiMeta['invoiced'] = '0';
								if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){ // invoice date 
									$currentDiMeta['invoiced'] = '1';
								}
								if(trim($row[36]) != ''){ // invoice ready date 
									$irdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[36]));
									if ($irdate !== false) { 
										$invoiceReadyDate = $irdate->format('Y-m-d'); 
										$currentDiMeta['invoiceReadyDate'] = $invoiceReadyDate;
										$currentDiMeta['invoiceReady'] = '1';
									}
								}
								if(trim($row[37]) != ''){ // invoice paid date 
									$ipdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[37]));
									if ($ipdate !== false) { 
										$invoicePaidDate = $ipdate->format('Y-m-d'); 
										$currentDiMeta['invoicePaidDate'] = $invoicePaidDate;
										$currentDiMeta['invoicePaid'] = '1';
									}
								}
								if(trim($row[38]) != ''){ // invoice closed date 
									$icdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[38]));
									if ($icdate !== false) { 
										$invoiceCloseDate = $icdate->format('Y-m-d'); 
										$currentDiMeta['invoiceCloseDate'] = $invoiceCloseDate;
										$currentDiMeta['invoiceClose'] = '1';
									}
								}
								if(trim($row[40]) != ''){ // invoice closed date 
									$carrierinvdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[40]));
									if ($carrierinvdate !== false) { 
										$carrierInvoiceDate = $carrierinvdate->format('Y-m-d'); 
										$currentDiMeta['custInvDate'] = $carrierInvoiceDate;
									}
								}
								if($row['50'] == 'AK'){
									$currentDiMeta['carrierInvoiceCheck'] = '1';
								}else{
									$currentDiMeta['carrierInvoiceCheck']  = '0';
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
							
							/************* update history **************/
						
							if($getDispatch){
								foreach($getDispatch as $di){
									//// update data in sub invoice 
									if($di['childInvoice'] != '') {
										$ciNewArray = explode(',',$di['childInvoice']);
										foreach($ciNewArray as $subInv){
											if(trim($subInv) == ''){ continue; }
											$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice',$subInv,'dispatchOutside','id,dispatchMeta');
											if(empty($getSubDispatch)){ continue; }
											$subInvArr = array();
											if($getSubDispatch[0]['dispatchMeta'] == '') {
												$subDiMeta = array('expense'=>array(),'dispatchInfo'=>array(), 'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoicePaidDate'=>'','invoiceCloseDate'=>'');
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
												$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'],'dispatchOutside',$subInvArr);
											}
										}
									}
								
								
									if($di['driver'] != $insert_data['driver']) { $changeField[] = array('Driver','driver',$di['driver'],$insert_data['driver']); }
									if($di['truckingCompany'] != $insert_data['truckingCompany']) { $changeField[] = array('Trucking Company','truckingCompany',$di['truckingCompany'],$insert_data['truckingCompany']); }
									//if($di['bookedUnder'] != $insert_data['bookedUnder']) { $changeField[] = array('Booked Under','bookedUnder',$di['bookedUnder'],$insert_data['bookedUnder']); }
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
									if($di['carrierPayoutDate'] != $insert_data['carrierPayoutDate']) { $changeField[] = array('Carrier Payout Date','carrierPayoutDate',$di['carrierPayoutDate'],$insert_data['carrierPayoutDate']); }
									if($row[29] != 'TBD' && $row[29] != ''){
										if($di['invoiceDate'] != $insert_data['invoiceDate']) { 
											$changeField[] = array('Invoice Date','invoiceDate',$di['invoiceDate'],$insert_data['invoiceDate']);
											$changeField[] = array('Expect Pay Date','expectPayDate',$di['expectPayDate'],$insert_data['expectPayDate']);
										}
									}
									if($di['delivered'] != $insert_data['delivered']) { $changeField[] = array('Delivered','delivered',$di['delivered'],$insert_data['delivered']); }
									if($di['bol'] != $insert_data['bol']) { $changeField[] = array('BOL','bol',$di['bol'],$insert_data['bol']); }
									if($di['rc'] != $insert_data['rc']) { $changeField[] = array('RC','rc',$di['rc'],$insert_data['rc']); }
									if($di['gd'] != $insert_data['gd']) { $changeField[] = array('Cutomer Payment Proof Check Box','gd',$di['gd'],$insert_data['gd']); }
									if($di['carrierPaymentType'] != $insert_data['carrierPaymentType']) { $changeField[] = array('Carrier Payment Type','carrierPaymentType',$di['carrierPaymentType'],$insert_data['carrierPaymentType']); }
									if($di['factoringType'] != $insert_data['factoringType']) { $changeField[] = array('Factoring Type','factoringType',$di['factoringType'],$insert_data['factoringType']); }

									if($di['carrierInvoiceRefNo'] != $insert_data['carrierInvoiceRefNo']) { 
										if($di['carrierInvoiceRefNo']==''){
											$changeField[] = array('Carrier invoice Ref No','carrierInvoiceRefNo','No value',$insert_data['carrierInvoiceRefNo']);
										}else{
											$changeField[] = array('Carrier invoice Ref No','carrierInvoiceRefNo',$di['carrierInvoiceRefNo'],$insert_data['carrierInvoiceRefNo']);
										}
									}

								}
							}
							
							if($changeField) {
								$userid = $this->session->userdata('logged');
								$changeFieldJson = json_encode($changeField);
								$dispatchOutsideLog = array('did'=>$row[0],'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($dispatchOutsideLog,'dispatchOutsideLog'); 
							}
							
							
							$res = $this->Comancontroler_model->update_table_by_id($row[0],'dispatchOutside',$insert_data); 
							
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
							
							$res = $this->Comancontroler_model->add_data_in_table($insert_data,'dispatchOutside');
							if($res){
								
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
    	$this->load->view('admin/upload-outside-dispatch-csv',$data);
    	$this->load->view('admin/layout/footer');
    }
	function uploadcsv(){
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $data['error'] = array();
        $data['upload'] = '';
	    $userid = $this->session->userdata('adminid');
	    if(isset($_GET['dummy']) && $_GET['dummy']=='csv'){
	        $data = array(
				array('Dispatch ID','Trucking Company','Driver','Booked Under','Pick Up Date','Pick Up Time','Pick Up City','Pick Up Company','Pick Up Address','Pick Up','Pickup Notes','Drop Off Date','Drop Off Time','Drop Off City','Drop Off Company','Drop Off Address','Drop Off','Driver Notes','Rate','PA Rate','Rate Lumper','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Driver Assist','Shipment Notes','Shipment Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Carrier Invoice Date', 'Carrier Payout Date','Sub Invoice', 'Delivered', 'Customer BOL', 'Customer RC', 'Customer Payment Proof', 'Carrier Payment Type', 'Factoring Type', 'Factoring companies', 'Carrier Invoice', 'Invoice Ref No')
			);
			
			$fileName = "OutsideDispatch_".date('Y-m-d').".csv"; 
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
	    
	    if(isset($_GET['driver-company']) && $_GET['driver-company']=='csv'){
	        $data = array(
				array('Driver','Trucking Company')
			);
			$drivers = $vehicles = array();
			$driverInfo = $this->Comancontroler_model->get_data_by_column('status','Active','drivers','dname');
			foreach($driverInfo as $val){
			    $drivers[] = $val['dname'];
			}
			$vehicleInfo = $this->Comancontroler_model->get_data_by_column('company !=','','truckingCompanies','company,owner');
			foreach($vehicleInfo as $val){
			    //$vehicles[] = $val['company'].' ('.$val['owner'].')';
			    $vehicles[] = $val['company'];
			}
			
			$maxCount = max(count($drivers), count($vehicles));

            for ($i = 0; $i < $maxCount; $i++) {
                $driver = isset($drivers[$i]) ? $drivers[$i] : '';
                $vehicle = isset($vehicles[$i]) ? $vehicles[$i] : '';
                $data[] = array($driver, $vehicle);
            }

			$fileName = "Driver-Trucking-Company-List.csv"; 
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

					$headers = $csv[0]; 
					// echo '<pre>';print_r($csv);exit;

                    // take bkp before upload
                    $this->downloadDbBackup();
					$status = '';
					$invoice_type = '';
					foreach ($csv as $row) { 
						// echo '<pre>';print_r($row);exit;
						if($row[0]=='Dispatch ID' || count($row) < 34) {
							continue;
						}
						$expenses = [];
					    $dispatchInfos = [];
						$mode = ''; 
						foreach ($headers as $i => $header) {
							$header = trim($header);
							$value  = isset($row[$i]) ? trim($row[$i]) : '';

							if ($header == 'Expenses') {
								$mode = 'expense';
								continue; 
							}
							if ($header == 'Dispatch Infos') {
								$mode = 'dispatch';
								continue;
							}

							if ($mode == 'expense' && $header != '' && $value != '') {
								$expenses[] = [$header, $value];
							}

							if ($mode == 'dispatch' && $header != '' && $value != '') {
								$dispatchInfos[] = [$header, $value];
							}
						}
						// echo '<pre>';print_r($dispatchInfos);exit;

						$did = $row[0];
						$trucking = $row[1];
						$driver = $row[2];
						$bookedUnder = $row[3];
						$bookedUnderNew = $row[4];
						$shipmentType = $row[5];
						$truckingEquipment = $row[6];
						$drayageEqipment = $row[7];

						$appointmentTypeP = $row[9];
						$metaDescriptionP = $row[14];
						$quantityP = $row[15];
						$weightP = $row[16];
						$commodityP = $row[17];
						
						$appointmentTypeD = $row[21];
						$quantityD = $row[23];
						$weightD = $row[27];
						$metaDescriptionD = $row[28];						
						
						$pudate = str_replace('-','/',$row[8]);
                        $pdate = DateTime::createFromFormat('m/d/Y', $pudate);
                        if ($pdate !== false) { $pudate = $pdate->format('Y-m-d'); } 
                        else { $pudate = ''; }
                        
						$dodate = $row[20];
						if($dodate != '') {
							$ddate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$dodate));
                            if ($ddate !== false) { $dodate = $ddate->format('Y-m-d'); } 
                            else { $dodate = ''; }
						}
						$check_company = $row[34];
						$check_pcity = $row[11];
						$check_plocation = $row[12];
						$check_paddress = $row[13];
						
						$check_dcity = $row[24];
						$check_dlocation = $row[25];
						$check_daddress = $row[26];

						$tracking = $row[36];
						$paddressid = $daddressid = 0;
						
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
							$data['error'][] = 'Dispatch ID "'.$did.'" driver should not blank.';
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
						
						$invoiceType = trim($row[41]);
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }
						
						$pattern = '/^\d{2}[\/-]\d{2}[\/-]\d{4}$/';

						if($invoiceType != '' && (!preg_match($pattern, trim($row[47])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice ready date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if(trim($row[48]) != '' && (!preg_match($pattern, trim($row[48])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice paid date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if(trim($row[49]) != '' && (!preg_match($pattern, trim($row[49])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice close date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if($invoiceType != '' && $invoiceType != 'RTS' && trim($row[50]) == ''){
        			        $data['error'][] = 'Dispatch ID '.$did.' invoice description is required.';
							continue;
        			    }
						
						// check trucking company
						$truckingArray = explode('(',$trucking);
						$truckingName = trim($truckingArray[0]);
						$truckingInfo = $this->Comancontroler_model->check_value_in_table('company',$truckingName,'truckingCompanies');
						if(count($truckingInfo) != '1'){ $trucking = ''; }
						else { $trucking = $truckingInfo[0]['id']; }
						
						// check bookedUnder 
						$bookedUnderArray = explode('(',$bookedUnder);
						$bookedUnderName = trim($bookedUnderArray[0]);
						$bookedUnderInfo = $this->Comancontroler_model->check_value_in_table('company',$bookedUnderName,'truckingCompanies');
						if(count($bookedUnderInfo) != '1'){ $bookedUnder = ''; }
						else { $bookedUnder = $bookedUnderInfo[0]['id']; }

						// check bookedUnder New
						$bookedUnderNewArray = explode('(',$bookedUnderNew);
						$bookedUnderNewName = trim($bookedUnderNewArray[0]);
						$bookedUnderNewInfo = $this->Comancontroler_model->check_value_in_table('company',$bookedUnderNewName,'booked_under');
						if(count($bookedUnderNewInfo) != '1'){ $bookedUnderNew = ''; }
						else { $bookedUnderNew = $bookedUnderNewInfo[0]['id']; }
						
						if($check_company=='' || $check_pcity=='' || $check_dcity=='' || $check_plocation=='' || $check_dlocation=='' || $tracking=='' || $trucking=='') {
							$data['error'][] = 'Dispatch ID '.$did.' please fill all required fields.';
							continue;
						}
						
						// generate invoice
						$inv_first = '';
						$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'dispatchOutside');
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
							
							if(stristr($row[37],$inv_first.''.$inv_middel)) {
								$invoice = $row[37];
							}
							elseif(strtotime($pudate) < strtotime('2024-04-25')){
								$invoice = $row[37];
								$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatchOutside','id');
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
						
						$invoiceType = $row[41]; //$companyInfo[0]['paymenTerms'];
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill';
						 $invoice_type = 'DB'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; 
						$invoice_type = 'QP'; }
						
						$dcity = $this->check_city($check_dcity);
						$pcity = $this->check_city($check_pcity);
						$plocation = $this->check_location($check_plocation);
						$dlocation = $this->check_location($check_dlocation);
						
						$parate = str_replace('$','',$row[32]);
						if(!is_numeric($parate)) { $parate = 0; }
						$rate = str_replace('$','',$row[31]);
						if(!is_numeric($rate)) { $rate = 0; }
						
						$payoutAmount = $row[39];
						
						$dispatchMeta = array('expense'=>array(),'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoiceCloseDate'=>'','invoicePaidDate'=>'');
						$dispatchMetaJson = json_encode($dispatchMeta);
						
						$week = date('M',strtotime($pudate)).' W';
    					$day = date('d',strtotime($pudate));
    					if($day < 9) { $w = '1'; }
    					elseif($day < 16){ $w = '2'; }
    					elseif($day < 24){ $w = '3'; }
    					else { $w = '4'; }
    					$week .= $w; 

						$fatoringCompany= $row['60'];
						if($fatoringCompany != ''){
							$factoringCompany=$this->Comancontroler_model->get_data_by_column('company',$fatoringCompany,'factoringCompanies');
							if(isset($factoringCompany[0]['id'])){
								$factoringCompanyId = $factoringCompany[0]['id'];
							}else{
								$factoringCompanyId = '0';
							}
						}else{
							$factoringCompanyId = '0';
						}

						$carrierPaymentType = $row[58];
						if (empty($carrierPaymentType)) {
							$carrierPaymentType = ''; 
						}
						$factoringType = $row[59];
						if (empty($factoringType)) {
							$factoringType = ''; 
						}
						
						// $firstExpnense = $row[];
						// $lastExpnense = $row[119];
						// $firsDispInfo = $row[120];
						// $lastDispInfo = $row[];

						$insert_data = array(
							'driver'=>$driver,
							'bookedUnder'=>$bookedUnder,
							'bookedUnderNew'=>$bookedUnderNew,
							'truckingCompany'=>$trucking,
							'pudate'=>$pudate,
							'pcity'=>$pcity,
							'dcity'=>$dcity,
							'dodate'=>$dodate,
							'rate'=>$rate,
							'parate'=>$parate,
							'company'=>$company,
							'dlocation'=>$dlocation,
							'plocation'=>$plocation,
							'dcode'=>$row[29],
							'pcode'=>$row[18],
							'paddress'=>$row[17],
							'daddress'=>$row[26],
							'trailer'=>$row[35],
							'tracking'=>$row[36],
							'invoice'=>$invoice,
							'payoutAmount'=>$payoutAmount,
							'invoiceType'=>$invoiceType,
							'dWeek'=>$week, 
							'ptime'=>$row[10],
							'dtime'=>$row[22],
							'notes'=>$row[46],
							'pnotes'=>$row[19],
							'dnotes'=>$row[30],
							'invoiceNotes'=>$row[50],
							'driver_status'=>$row[45],
							'delivered'=>$row[54],
							'bol'=>$row[55],
							'rc'=>$row[56],
							'gd'=>$row[57],
							'carrierPaymentType'=>$carrierPaymentType,
							'factoringType'=>$factoringType,
							'factoringCompany'=>$factoringCompanyId,
							'carrierInvoiceRefNo'=>$row[62]
						);
						if(is_numeric($row[0]) && $row[0] > 1) { // update
							if($row[40] != 'TBD' && $row[40] != ''){
								$idate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[40]));
                                if ($idate !== false) { 
                                    $invoiceDate = $idate->format('Y-m-d'); 
                                    $insert_data['invoiceDate'] = $invoiceDate;
                                    
                                    // update expect pay date
                                    if($invoiceType == 'RTS'){ $iDays = '+ 3 days'; 
									$invoice_type = 'RTS'; }
            					    elseif($invoiceType == 'Direct Bill'){ $iDays = "+ 30 days"; 
									$invoice_type = 'DB'; }
            					    elseif($invoiceType == 'Quick Pay'){ $iDays = "+ 7 days"; 
									$invoice_type = 'QP'; }
                                    else { $iDays = '+ 1 month'; }
                                    $expectPayDate = date('Y-m-d',strtotime($iDays,strtotime($invoiceDate)));
                                    $insert_data['expectPayDate'] = $expectPayDate;
									$status = $invoice_type ." invoiced " . $idate->format('m/d/Y');
                                }
							}
							if($row[52] != '0000-00-00' && $row[52] != ''){
								$cpdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[52]));
                                if ($cpdate !== false) { 
                                    $carrierPayoutDate = $cpdate->format('Y-m-d'); 
                                    $insert_data['carrierPayoutDate'] = $carrierPayoutDate;
                                    $insert_data['carrierPayoutCheck'] = '1';
                                }
							}
							
							
							$changeField = array();
							$getDispatch = $this->Comancontroler_model->get_data_by_column('id',$row[0],'dispatchOutside');
							if($getDispatch[0]['dispatchMeta'] != '') {
								$currentDiMeta = json_decode($getDispatch[0]['dispatchMeta'],true);
								$currentDiMeta['expense'] = $expenses;
								$currentDiMeta['dispatchInfo'] =$dispatchInfos;
								$currentDiMeta['invoiceReadyDate'] = $currentDiMeta['invoicePaidDate'] = $currentDiMeta['invoiceCloseDate'] = '';
								$currentDiMeta['invoiceReady'] = $currentDiMeta['invoicePaid'] = $currentDiMeta['invoiceClose'] = $currentDiMeta['invoiced'] = '0';

								$currentDiMeta['invoicePDF'] = $shipmentType;
								$currentDiMeta['invoiceTrucking'] =$truckingEquipment;
								$currentDiMeta['invoiceDrayage'] =  $drayageEqipment;
								$currentDiMeta['appointmentTypeP'] =  $appointmentTypeP;
								$currentDiMeta['appointmentTypeD'] =  $appointmentTypeD;
								$currentDiMeta['quantityP'] =  $quantityP;
								$currentDiMeta['commodityP'] =  $commodityP;
								$currentDiMeta['metaDescriptionP'] =  $metaDescriptionP;
								$currentDiMeta['weightP'] =  $weightP;
								$currentDiMeta['quantityD'] =  $quantityD;
								$currentDiMeta['metaDescriptionD'] =  $metaDescriptionD;
								$currentDiMeta['weightD'] =  $weightD;
								
								if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){ // invoice date 
									$currentDiMeta['invoiced'] = '1';
								}
								if(trim($row[47]) != ''){ // invoice ready date 
									$irdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[47]));
									if ($irdate !== false) { 
										$invoiceReadyDate = $irdate->format('Y-m-d'); 
										$currentDiMeta['invoiceReadyDate'] = $invoiceReadyDate;
										$currentDiMeta['invoiceReady'] = '1';
										$status = $invoice_type ." Ready " . $irdate->format('m/d/Y');
									}
								}
								if($row[40] != 'TBD' && $row[40] != ''){
									$idate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[40]));
									if ($idate !== false) { 
										$status = $invoice_type ." invoiced " . $idate->format('m/d/Y');
									}
								}
								if(trim($row[48]) != ''){ // invoice paid date 
									$ipdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[48]));
									if ($ipdate !== false) { 
										$invoicePaidDate = $ipdate->format('Y-m-d'); 
										$currentDiMeta['invoicePaidDate'] = $invoicePaidDate;
										$currentDiMeta['invoicePaid'] = '1';
										$status = $invoice_type ." Paid " . $ipdate->format('m/d/Y');

									}
								}
								if(trim($row[49]) != ''){ // invoice closed date 
									$icdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[49]));
									if ($icdate !== false) { 
										$invoiceCloseDate = $icdate->format('Y-m-d'); 
										$currentDiMeta['invoiceCloseDate'] = $invoiceCloseDate;
										$currentDiMeta['invoiceClose'] = '1';
										$status = $invoice_type ." Closed " . $icdate->format('m/d/Y');
										
									}
								}

								if(trim($row[51]) != ''){ // invoice closed date 
									$carrierinvdate = DateTime::createFromFrmat('m/d/Y', str_replace('-','/',$row[51]));
									if ($carrierinvdate !== false) { 
										$carrierInvoiceDate = $carrierinvdate->format('Y-m-d'); 
										$currentDiMeta['custInvDate'] = $carrierInvoiceDate;
									}
								}
								if($row['61'] == 'AK'){
									$currentDiMeta['carrierInvoiceCheck'] = '1';
								}else{
									$currentDiMeta['carrierInvoiceCheck']  = '0';
								}
								if($status != ''){
									$insert_data['status']=$status;
								}else{
									$insert_data['status']=$row[44];
								}
								// $dispatchMetaJson = json_encode($currentDiMeta);
								// $insert_data['dispatchMeta'] = $dispatchMetaJson;
							 
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
							/************* update history **************/
						
							if($getDispatch){
								foreach($getDispatch as $di){

									$sub_invoices = $row[53];
									$osdInvoices = [];
									$otherInvoices = [];
									
									if ($sub_invoices) {
										$subInvArray = explode(',', $sub_invoices);
										foreach ($subInvArray as $subInv) {
											$subInv = trim($subInv);
											if ($subInv == '') { continue; }
											if (strpos($subInv, 'OSD') === 0) {
												// ---------- ChildInvoice logic ----------
												$osdInvoices[] = $subInv;
												$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice', $subInv, 'dispatchOutside', 'id,dispatchMeta');
												if (empty($getSubDispatch)) { continue; }

												$subInvArr = [];
												if ($getSubDispatch[0]['dispatchMeta'] == '') {
													$subDiMeta = [
														'expense' => [],
														'dispatchInfo' => [],
														'invoiced' => '0',
														'invoicePaid' => '0',
														'invoiceClose' => '0',
														'invoiceReady' => '0',
														'invoiceReadyDate' => '',
														'invoicePaidDate' => '',
														'invoiceCloseDate' => ''
													];
												} else {
													$subDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'], true);
												}

												// Copy over meta fields
												$subDiMeta['invoiceReadyDate'] = $currentDiMeta['invoiceReadyDate'];
												$subDiMeta['invoicePaidDate']  = $currentDiMeta['invoicePaidDate'];
												$subDiMeta['invoiceCloseDate'] = $currentDiMeta['invoiceCloseDate'];
												$subDiMeta['invoiceReady']     = $currentDiMeta['invoiceReady'];
												$subDiMeta['invoicePaid']      = $currentDiMeta['invoicePaid'];
												$subDiMeta['invoiceClose']     = $currentDiMeta['invoiceClose'];
												$subDiMeta['invoiced']         = $currentDiMeta['invoiced'];

												$subInvArr['dispatchMeta'] = json_encode($subDiMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

												if (array_key_exists("invoiceDate", $insert_data) && trim($insert_data['invoiceDate']) != '') {
													$subInvArr['invoiceDate']   = $insert_data['invoiceDate'];
													$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
												}

												$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
												$subInvArr['invoiceType']  = $insert_data['invoiceType'];
												$subInvArr['bol']          = $insert_data['bol'];
												$subInvArr['rc']           = $insert_data['rc'];
												$subInvArr['gd']           = $insert_data['gd'];
												$subInvArr['delivered']    = $insert_data['delivered'];
												$subInvArr['driver_status']= $insert_data['driver_status'];
												$subInvArr['parentInvoice'] = $invoice;
												
												$fullStatus = $insert_data['status'];
												$firstPart  = explode(' - Linked to', $fullStatus)[0];
												$subInvArr['status'] = $firstPart . ' - Linked to ' . $insert_data['invoice'];

												if ($getSubDispatch[0]['id'] > 0) {
													$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'], 'dispatchOutside', $subInvArr);
												}

											} else {
												// ---------- OtherChildInvoice logic ----------
												$otherInvoices[] = $subInv;
												$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice', $subInv, 'dispatch', 'id,dispatchMeta');
												if (empty($getSubDispatch)) { continue; }

												$subInvArr = [];
												if ($getSubDispatch[0]['dispatchMeta'] == '') {
													$subInvDiMeta = [
														'expense' => [],
														'invoiced' => '0',
														'invoicePaid' => '0',
														'invoiceClose' => '0',
														'invoiceReady' => '0',
														'invoiceReadyDate' => '',
														'invoicePaidDate' => '',
														'invoiceCloseDate' => ''
													];
												} else {
													$subInvDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'], true);
												}

												$subInvDiMeta['invoiceReadyDate'] = $currentDiMeta['invoiceReadyDate'];
												$subInvDiMeta['invoicePaidDate']  = $currentDiMeta['invoicePaidDate'];
												$subInvDiMeta['invoiceCloseDate'] = $currentDiMeta['invoiceCloseDate'];
												$subInvDiMeta['invoiceReady']     = $currentDiMeta['invoiceReady'];
												$subInvDiMeta['invoicePaid']      = $currentDiMeta['invoicePaid'];
												$subInvDiMeta['invoiceClose']     = $currentDiMeta['invoiceClose'];
												$subInvDiMeta['invoiced']         = $currentDiMeta['invoiced'];
												$subInvDiMeta['custInvDate']      = $currentDiMeta['custInvDate'];
												$subInvDiMeta['custDueDate']      = $currentDiMeta['custDueDate'];

												$subInvArr['dispatchMeta'] = json_encode($subInvDiMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

												if (array_key_exists("invoiceDate", $insert_data) && trim($insert_data['invoiceDate']) != '') {
													$subInvArr['invoiceDate']   = $insert_data['invoiceDate'];
													$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
												}

												$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
												$subInvArr['invoiceType']  = $insert_data['invoiceType'];
												$fullStatus = $insert_data['status'];
												$firstPart  = explode(' - Linked to', $fullStatus)[0];
												$subInvArr['status']       = $firstPart . ' - Linked to ' . $insert_data['invoice'];
												$subInvArr['otherParentInvoice'] = $invoice;

												$getPaentCheckboxes = $this->Comancontroler_model->get_data_by_column('invoice', $invoice, 'dispatchOutside', 'bol,rc,gd,delivered,shipping_contact,driver_status');

												$subInvArr['bol']             = $getPaentCheckboxes[0]['bol'];
												$subInvArr['rc']              = $getPaentCheckboxes[0]['rc'];
												$subInvArr['gd']              = $getPaentCheckboxes[0]['gd'];
												$subInvArr['delivered']       = $getPaentCheckboxes[0]['delivered'];
												$subInvArr['shipping_contact']= $getPaentCheckboxes[0]['shipping_contact'];
												$subInvArr['driver_status']   = $insert_data['driver_status'];

												if ($getSubDispatch[0]['id'] > 0) {
													$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'], 'dispatch', $subInvArr);
												}
											}
										}
									}
									$osdInvoiceStr   = implode(',', $osdInvoices);
									$otherInvoiceStr = implode(',', $otherInvoices);
							
									$insert_data['childInvoice']      = $osdInvoiceStr;  
									$currentDiMeta['otherChildInvoice'] = $otherInvoiceStr;

									$dispatchMetaJson = json_encode($currentDiMeta);
									$insert_data['dispatchMeta'] = $dispatchMetaJson; 

									// if($di['childInvoice'] != '') {
									// 	$ciNewArray = explode(',',$di['childInvoice']);
									// 	foreach($ciNewArray as $subInv){
									// 		if(trim($subInv) == ''){ continue; }
									// 		$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice',$subInv,'dispatchOutside','id,dispatchMeta');
											
									// 		if(empty($getSubDispatch)){ continue; }
									// 		$subInvArr = array();
									// 		if($getSubDispatch[0]['dispatchMeta'] == '') {
									// 			$subDiMeta = array('expense'=>array(),'dispatchInfo'=>array(), 'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoicePaidDate'=>'','invoiceCloseDate'=>'');
									// 		} else {
									// 			$subDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'],true);
									// 		}
									// 			$subDiMeta['invoiceReadyDate'] = $currentDiMeta['invoiceReadyDate'];
									// 			$subDiMeta['invoicePaidDate'] = $currentDiMeta['invoicePaidDate'];
									// 			$subDiMeta['invoiceCloseDate'] = $currentDiMeta['invoiceCloseDate'];
									// 			$subDiMeta['invoiceReady'] = $currentDiMeta['invoiceReady'];
									// 			$subDiMeta['invoicePaid'] = $currentDiMeta['invoicePaid'];
									// 			$subDiMeta['invoiceClose'] = $currentDiMeta['invoiceClose'];
									// 			$subDiMeta['invoiced'] = $currentDiMeta['invoiced'];
										
									// 		$subDiMetaJson = json_encode($subDiMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
									// 		$subInvArr['dispatchMeta'] = $subDiMetaJson;

									// 		if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){
									// 			$subInvArr['invoiceDate'] = $insert_data['invoiceDate'];
									// 			$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
									// 		}
									// 		$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
									// 		$subInvArr['invoiceType'] = $insert_data['invoiceType'];
									// 		$subInvArr['bol'] =$insert_data['bol'];
									// 		$subInvArr['rc'] = $insert_data['rc'];
									// 		$subInvArr['gd'] = $insert_data['gd'];
									// 		$subInvArr['delivered'] = $insert_data['delivered'];
									// 		$subInvArr['driver_status'] = $insert_data['driver_status'];
									// 		$fullStatus = $insert_data['status'];
									// 		$firstPart = explode(' - Linked to', $fullStatus)[0];  
									// 		$subInvArr['status'] = $firstPart . ' - Linked to ' . $insert_data['invoice'];

									// 		if($getSubDispatch[0]['id'] > 0) {
									// 			$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'],'dispatchOutside',$subInvArr);

									// 		}
									// 	}
									// }

									// $newDispatchMeta = json_decode($di['dispatchMeta'], true);
									// $ociNewArray = array(); 
									// if (!empty($newDispatchMeta['otherChildInvoice'])) {
									// 	$ociNewArray = explode(',', $newDispatchMeta['otherChildInvoice']);
									// }
									// if (!empty($ociNewArray)) {
									// 	foreach ($ociNewArray as $subInv) {
									// 		if (trim($subInv) == '') { continue; }
									// 			$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice', $subInv, 'dispatch', 'id,dispatchMeta');
									
									// 			if (empty($getSubDispatch)) { continue; }
									
									// 			$subInvArr = array();
									// 			if ($getSubDispatch[0]['dispatchMeta'] == '') {
									// 				$subInvDiMeta = array(
									// 					'expense' => array(),
									// 					'invoiced' => '0',
									// 					'invoicePaid' => '0',
									// 					'invoiceClose' => '0',
									// 					'invoiceReady' => '0',
									// 					'invoiceReadyDate' => '',
									// 					'invoicePaidDate' => '',
									// 					'invoiceCloseDate' => ''
									// 				);
									// 			} else {
									// 				$subInvDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'], true);
									// 			}
									
									// 		$subInvDiMeta['invoiceReadyDate'] = $currentDiMeta['invoiceReadyDate'];
									// 		$subInvDiMeta['invoicePaidDate'] = $currentDiMeta['invoicePaidDate'];
									// 		$subInvDiMeta['invoiceCloseDate'] = $currentDiMeta['invoiceCloseDate'];
									// 		$subInvDiMeta['invoiceReady'] = $currentDiMeta['invoiceReady'];
									// 		$subInvDiMeta['invoicePaid'] = $currentDiMeta['invoicePaid'];
									// 		$subInvDiMeta['invoiceClose'] = $currentDiMeta['invoiceClose'];
									// 		$subInvDiMeta['invoiced'] = $currentDiMeta['invoiced'];
									
									// 		$subInvDiMeta['custInvDate'] = $currentDiMeta['custInvDate'];
									// 		$subInvDiMeta['custDueDate'] = $currentDiMeta['custDueDate'];


									// 		$dispatchMetaJson = json_encode($subInvDiMeta);
									// 		$subInvArr['dispatchMeta'] = $dispatchMetaJson;
									
									// 		if (array_key_exists("invoiceDate", $insert_data) && trim($insert_data['invoiceDate']) != '') {
									// 			$subInvArr['invoiceDate'] = $insert_data['invoiceDate'];
									// 			$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
									// 		}
									
									// 		$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
									// 		$subInvArr['invoiceType'] = $insert_data['invoiceType'];
									// 		$fullStatus = $insert_data['status'];
									// 		$firstPart = explode(' - Linked to', $fullStatus)[0]; 
											
									// 		$subInvArr['status'] = $firstPart . ' - Linked to ' . $insert_data['invoice'];
									// 		$subInvArr['otherParentInvoice'] = $invoice;
									// 		$getPaentCheckboxes = $this->Comancontroler_model->get_data_by_column('invoice', $invoice, 'dispatchOutside', 'bol,rc,gd,delivered,shipping_contact,driver_status');

									// 		$subInvArr['bol'] =$getPaentCheckboxes[0]['bol'];
									// 		$subInvArr['rc'] = $getPaentCheckboxes[0]['rc'];
									// 		$subInvArr['gd'] = $getPaentCheckboxes[0]['gd'];
									// 		$subInvArr['delivered'] = $getPaentCheckboxes[0]['delivered'];
									// 		$subInvArr['shipping_contact'] = $getPaentCheckboxes[0]['shipping_contact'];
									// 		$subInvArr['driver_status'] = $insert_data['driver_status'];

									// 		if ($getSubDispatch[0]['id'] > 0) {
									// 			$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'], 'dispatch', $subInvArr);
									// 		}
									// 	}
									// }
									$res = $this->Comancontroler_model->update_table_by_id($row[0],'dispatchOutside',$insert_data);
								
									// echo 'wait'; exit;
									if($di['driver'] != $insert_data['driver']) { $changeField[] = array('Driver','driver',$di['driver'],$insert_data['driver']); }
									if($di['truckingCompany'] != $insert_data['truckingCompany']) { $changeField[] = array('Trucking Company','truckingCompany',$di['truckingCompany'],$insert_data['truckingCompany']); }
									//if($di['bookedUnder'] != $insert_data['bookedUnder']) { $changeField[] = array('Booked Under','bookedUnder',$di['bookedUnder'],$insert_data['bookedUnder']); }
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
									if($di['carrierPayoutDate'] != $insert_data['carrierPayoutDate']) { $changeField[] = array('Carrier Payout Date','carrierPayoutDate',$di['carrierPayoutDate'],$insert_data['carrierPayoutDate']); }
									if($row[29] != 'TBD' && $row[29] != ''){
										if($di['invoiceDate'] != $insert_data['invoiceDate']) { 
											$changeField[] = array('Invoice Date','invoiceDate',$di['invoiceDate'],$insert_data['invoiceDate']);
											$changeField[] = array('Expect Pay Date','expectPayDate',$di['expectPayDate'],$insert_data['expectPayDate']);
										}
									}
									if($di['delivered'] != $insert_data['delivered']) { $changeField[] = array('Delivered','delivered',$di['delivered'],$insert_data['delivered']); }
									if($di['bol'] != $insert_data['bol']) { $changeField[] = array('BOL','bol',$di['bol'],$insert_data['bol']); }
									if($di['rc'] != $insert_data['rc']) { $changeField[] = array('RC','rc',$di['rc'],$insert_data['rc']); }
									if($di['gd'] != $insert_data['gd']) { $changeField[] = array('Cutomer Payment Proof Check Box','gd',$di['gd'],$insert_data['gd']); }
									if($di['carrierPaymentType'] != $insert_data['carrierPaymentType']) { $changeField[] = array('Carrier Payment Type','carrierPaymentType',$di['carrierPaymentType'],$insert_data['carrierPaymentType']); }
									if($di['factoringType'] != $insert_data['factoringType']) { $changeField[] = array('Factoring Type','factoringType',$di['factoringType'],$insert_data['factoringType']); }

									if($di['carrierInvoiceRefNo'] != $insert_data['carrierInvoiceRefNo']) { 
										if($di['carrierInvoiceRefNo']==''){
											$changeField[] = array('Carrier invoice Ref No','carrierInvoiceRefNo','No value',$insert_data['carrierInvoiceRefNo']);
										}else{
											$changeField[] = array('Carrier invoice Ref No','carrierInvoiceRefNo',$di['carrierInvoiceRefNo'],$insert_data['carrierInvoiceRefNo']);
										}
									}

								}
							}
							
							if($changeField) {
								$userid = $this->session->userdata('logged');
								$changeFieldJson = json_encode($changeField);
								$dispatchOutsideLog = array('did'=>$row[0],'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($dispatchOutsideLog,'dispatchOutsideLog'); 
							}						
						} else { 
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
							$insert_data['userid'] = $userid;

							$currentDiMeta = [
								"expense" => $expenses,
								"dispatchInfo" => $dispatchInfos,
								"invoiceDate"       => isset($insert_data['invoiceDate']) ? $insert_data['invoiceDate'] : '',
								"expectPayDate"     => isset($insert_data['expectPayDate']) ? $insert_data['expectPayDate'] : '',
								"invoiceReadyDate"  => '',
								"invoicePaidDate"   => '',
								"invoiceCloseDate"  => '',
								"custInvDate"       => '',
								"invoiced"          => isset($insert_data['invoiceDate']) && trim($insert_data['invoiceDate']) != '' ? '1' : '0',
								"invoiceReady"      => '0',
								"invoicePaid"       => '0',
								"invoiceClose"      => '0',
								"carrierInvoiceCheck" => ($row[61] == 'AK') ? '1' : '0',
								"invoicePDF" => $shipmentType,
								"invoiceTrucking" => $truckingEquipment,
								"invoiceDrayage" => $drayageEqipment,
								"appointmentTypeP" => $appointmentTypeP,
								"appointmentTypeD" => $appointmentTypeD,
								"quantityP" => $quantityP,
								"commodityP" => $commodityP,
								"metaDescriptionP" => $metaDescriptionP,
								"weightP" => $weightP,
								"quantityD" => $quantityD,
								"metaDescriptionD" => $metaDescriptionD,
								"weightD" => $weightD
							];

							
							if($row[40] != 'TBD' && $row[40] != ''){
								$idate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[40]));
                                if ($idate !== false) { 
                                    $invoiceDate = $idate->format('Y-m-d'); 
                                    $insert_data['invoiceDate'] = $invoiceDate;
                                    
                                    // update expect pay date
                                    if($invoiceType == 'RTS'){ $iDays = '+ 3 days'; 
									$invoice_type = 'RTS'; }
            					    elseif($invoiceType == 'Direct Bill'){ $iDays = "+ 30 days"; 
									$invoice_type = 'DB'; }
            					    elseif($invoiceType == 'Quick Pay'){ $iDays = "+ 7 days"; 
									$invoice_type = 'QP'; }
                                    else { $iDays = '+ 1 month'; }
                                    $expectPayDate = date('Y-m-d',strtotime($iDays,strtotime($invoiceDate)));
                                    $insert_data['expectPayDate'] = $expectPayDate;
									$status = $invoice_type ." invoiced " . $idate->format('m/d/Y');
                                }
							}
								if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){ // invoice date 
									$currentDiMeta['invoiced'] = '1';
								}
								if(trim($row[47]) != ''){ // invoice ready date 
									$irdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[47]));
									if ($irdate !== false) { 
										$invoiceReadyDate = $irdate->format('Y-m-d'); 
										$currentDiMeta['invoiceReadyDate'] = $invoiceReadyDate;
										$currentDiMeta['invoiceReady'] = '1';
										$status = $invoice_type ." Ready " . $irdate->format('m/d/Y');
									}
								}
								if($row[40] != 'TBD' && $row[40] != ''){
									$idate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[40]));
									if ($idate !== false) { 
										$status = $invoice_type ." invoiced " . $idate->format('m/d/Y');
									}
								}
								if(trim($row[48]) != ''){ // invoice paid date 
									$ipdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[48]));
									if ($ipdate !== false) { 
										$invoicePaidDate = $ipdate->format('Y-m-d'); 
										$currentDiMeta['invoicePaidDate'] = $invoicePaidDate;
										$currentDiMeta['invoicePaid'] = '1';
										$status = $invoice_type ." Paid " . $ipdate->format('m/d/Y');

									}
								}
								if(trim($row[49]) != ''){ // invoice closed date 
									$icdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[49]));
									if ($icdate !== false) { 
										$invoiceCloseDate = $icdate->format('Y-m-d'); 
										$currentDiMeta['invoiceCloseDate'] = $invoiceCloseDate;
										$currentDiMeta['invoiceClose'] = '1';
										$status = $invoice_type ." Closed " . $icdate->format('m/d/Y');
										
									}
								}
								if($row['61'] == 'AK'){
									$currentDiMeta['carrierInvoiceCheck'] = '1';
								}else{
									$currentDiMeta['carrierInvoiceCheck']  = '0';
								}
								if($status != ''){
									$insert_data['status']=$status;
								}else{
									$insert_data['status']=$row[44];
								}

							$sub_invoices = $row[53];
							$osdInvoices = [];
							$otherInvoices = [];
							
							if ($sub_invoices) {
								$subInvArray = explode(',', $sub_invoices);

								foreach ($subInvArray as $subInv) {
									$subInv = trim($subInv);
									if ($subInv == '') { continue; }
									if (strpos($subInv, 'OSD') === 0) {
										// ---------- ChildInvoice logic ----------
										$osdInvoices[] = $subInv;
										$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice', $subInv, 'dispatchOutside', 'id,dispatchMeta');
										if (empty($getSubDispatch)) { continue; }

										$subInvArr = [];
										if ($getSubDispatch[0]['dispatchMeta'] == '') {
											$subDiMeta = [
												'expense' => [],
												'dispatchInfo' => [],
												'invoiced' => '0',
												'invoicePaid' => '0',
												'invoiceClose' => '0',
												'invoiceReady' => '0',
												'invoiceReadyDate' => '',
												'invoicePaidDate' => '',
												'invoiceCloseDate' => ''
											];
										} else {
											$subDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'], true);
										}

										// Copy over meta fields
										$subDiMeta['invoiceReadyDate'] = $currentDiMeta['invoiceReadyDate'];
										$subDiMeta['invoicePaidDate']  = $currentDiMeta['invoicePaidDate'];
										$subDiMeta['invoiceCloseDate'] = $currentDiMeta['invoiceCloseDate'];
										$subDiMeta['invoiceReady']     = $currentDiMeta['invoiceReady'];
										$subDiMeta['invoicePaid']      = $currentDiMeta['invoicePaid'];
										$subDiMeta['invoiceClose']     = $currentDiMeta['invoiceClose'];
										$subDiMeta['invoiced']         = $currentDiMeta['invoiced'];

										$subInvArr['dispatchMeta'] = json_encode($subDiMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

										if (array_key_exists("invoiceDate", $insert_data) && trim($insert_data['invoiceDate']) != '') {
											$subInvArr['invoiceDate']   = $insert_data['invoiceDate'];
											$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
										}

										$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
										$subInvArr['invoiceType']  = $insert_data['invoiceType'];
										$subInvArr['bol']          = $insert_data['bol'];
										$subInvArr['rc']           = $insert_data['rc'];
										$subInvArr['gd']           = $insert_data['gd'];
										$subInvArr['delivered']    = $insert_data['delivered'];
										$subInvArr['driver_status']= $insert_data['driver_status'];
										$subInvArr['parentInvoice'] = $invoice;
										
										$fullStatus = $insert_data['status'];
										$firstPart  = explode(' - Linked to', $fullStatus)[0];
										$subInvArr['status'] = $firstPart . ' - Linked to ' . $insert_data['invoice'];

										if ($getSubDispatch[0]['id'] > 0) {
											$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'], 'dispatchOutside', $subInvArr);
										}

									} else {
										// ---------- OtherChildInvoice logic ----------
										$otherInvoices[] = $subInv;
										$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice', $subInv, 'dispatch', 'id,dispatchMeta');
										if (empty($getSubDispatch)) { continue; }

										$subInvArr = [];
										if ($getSubDispatch[0]['dispatchMeta'] == '') {
											$subInvDiMeta = [
												'expense' => [],
												'invoiced' => '0',
												'invoicePaid' => '0',
												'invoiceClose' => '0',
												'invoiceReady' => '0',
												'invoiceReadyDate' => '',
												'invoicePaidDate' => '',
												'invoiceCloseDate' => ''
											];
										} else {
											$subInvDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'], true);
										}

										$subInvDiMeta['invoiceReadyDate'] = $currentDiMeta['invoiceReadyDate'];
										$subInvDiMeta['invoicePaidDate']  = $currentDiMeta['invoicePaidDate'];
										$subInvDiMeta['invoiceCloseDate'] = $currentDiMeta['invoiceCloseDate'];
										$subInvDiMeta['invoiceReady']     = $currentDiMeta['invoiceReady'];
										$subInvDiMeta['invoicePaid']      = $currentDiMeta['invoicePaid'];
										$subInvDiMeta['invoiceClose']     = $currentDiMeta['invoiceClose'];
										$subInvDiMeta['invoiced']         = $currentDiMeta['invoiced'];
										$subInvDiMeta['custInvDate']      = $currentDiMeta['custInvDate'];
										$subInvDiMeta['custDueDate']      = $currentDiMeta['custDueDate'];

										$subInvArr['dispatchMeta'] = json_encode($subInvDiMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

										if (array_key_exists("invoiceDate", $insert_data) && trim($insert_data['invoiceDate']) != '') {
											$subInvArr['invoiceDate']   = $insert_data['invoiceDate'];
											$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
										}

										$subInvArr['invoiceNotes'] = $insert_data['invoiceNotes'];
										$subInvArr['invoiceType']  = $insert_data['invoiceType'];
										$fullStatus = $insert_data['status'];
										$firstPart  = explode(' - Linked to', $fullStatus)[0];
										$subInvArr['status']       = $firstPart . ' - Linked to ' . $insert_data['invoice'];
										$subInvArr['otherParentInvoice'] = $invoice;

										$getPaentCheckboxes = $this->Comancontroler_model->get_data_by_column('invoice', $invoice, 'dispatchOutside', 'bol,rc,gd,delivered,shipping_contact,driver_status');

										$subInvArr['bol']             = $getPaentCheckboxes[0]['bol'];
										$subInvArr['rc']              = $getPaentCheckboxes[0]['rc'];
										$subInvArr['gd']              = $getPaentCheckboxes[0]['gd'];
										$subInvArr['delivered']       = $getPaentCheckboxes[0]['delivered'];
										$subInvArr['shipping_contact']= $getPaentCheckboxes[0]['shipping_contact'];
										$subInvArr['driver_status']   = $insert_data['driver_status'];

										if ($getSubDispatch[0]['id'] > 0) {
											$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'], 'dispatch', $subInvArr);
										}
									}
								}
							}

							$osdInvoiceStr   = implode(',', $osdInvoices);
							$otherInvoiceStr = implode(',', $otherInvoices);
							
							$insert_data['childInvoice']      = $osdInvoiceStr;  
							$currentDiMeta['otherChildInvoice'] = $otherInvoiceStr;

							$dispatchMetaJson = json_encode($currentDiMeta);
							$insert_data['dispatchMeta'] = $dispatchMetaJson; 
							
							$insert_data['paddressid'] = $paddressid; 
							$insert_data['daddressid'] = $daddressid; 
							
							$res = $this->Comancontroler_model->add_data_in_table($insert_data,'dispatchOutside');
							if($res){
								
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
    	$this->load->view('admin/upload-outside-dispatch-csv',$data);
    	$this->load->view('admin/layout/footer');
    }
	function uploadcsv_back_up(){
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $data['error'] = array();
        $data['upload'] = '';
	    
	    if(isset($_GET['dummy']) && $_GET['dummy']=='csv'){
	        $data = array(
				array('Dispatch ID','Trucking Company','Driver','Booked Under','Pick Up Date','Pick Up Time','Pick Up City','Pick Up Company','Pick Up Address','Pick Up','Pickup Notes','Drop Off Date','Drop Off Time','Drop Off City','Drop Off Company','Drop Off Address','Drop Off','Driver Notes','Rate','PA Rate','Rate Lumper','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Driver Assist','Shipment Notes','Shipment Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Carrier Invoice Date', 'Carrier Payout Date','Sub Invoice', 'Delivered', 'Customer BOL', 'Customer RC', 'Customer Payment Proof', 'Carrier Payment Type', 'Factoring Type', 'Factoring companies', 'Carrier Invoice', 'Invoice Ref No')
			);
			
			$fileName = "OutsideDispatch_".date('Y-m-d').".csv"; 
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
	    
	    if(isset($_GET['driver-company']) && $_GET['driver-company']=='csv'){
	        $data = array(
				array('Driver','Trucking Company')
			);
			$drivers = $vehicles = array();
			$driverInfo = $this->Comancontroler_model->get_data_by_column('status','Active','drivers','dname');
			foreach($driverInfo as $val){
			    $drivers[] = $val['dname'];
			}
			$vehicleInfo = $this->Comancontroler_model->get_data_by_column('company !=','','truckingCompanies','company,owner');
			foreach($vehicleInfo as $val){
			    //$vehicles[] = $val['company'].' ('.$val['owner'].')';
			    $vehicles[] = $val['company'];
			}
			
			$maxCount = max(count($drivers), count($vehicles));

            for ($i = 0; $i < $maxCount; $i++) {
                $driver = isset($drivers[$i]) ? $drivers[$i] : '';
                $vehicle = isset($vehicles[$i]) ? $vehicles[$i] : '';
                $data[] = array($driver, $vehicle);
            }

			$fileName = "Driver-Trucking-Company-List.csv"; 
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
                    
					foreach ($csv as $row) { 
						//echo '<pre>';print_r($row);echo '</pre>';
						if($row[0]=='Dispatch ID' || count($row) < 34) {
							continue;
						}
						$paddressid = $daddressid = 0;

						$did = $row[0];
						$trucking = $row[1];
						// check trucking company
						$truckingArray = explode('(',$trucking);
						$truckingName = trim($truckingArray[0]);
						$truckingInfo = $this->Comancontroler_model->check_value_in_table('company',$truckingName,'truckingCompanies');
						if(count($truckingInfo) != '1'){ $trucking = ''; }
						else { $trucking = $truckingInfo[0]['id']; }
						
						$user = $this->session->userdata('logged');
						$userid = $user['adminid'];

						$driver = $row[2];
						if($driver=='') {
							$data['error'][] = 'Dispatch ID "'.$did.'" driver should not blank.';
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

						$bookedUnder = $row[3];
						// check bookedUnder 
						$bookedUnderArray = explode('(',$bookedUnder);
						$bookedUnderName = trim($bookedUnderArray[0]);
						$bookedUnderInfo = $this->Comancontroler_model->check_value_in_table('company',$bookedUnderName,'truckingCompanies');
						if(count($bookedUnderInfo) != '1'){ $bookedUnder = ''; }
						else { $bookedUnder = $bookedUnderInfo[0]['id']; }
						
						$shipmentType = $row[4];
						$drayageType = $row[5];
						$drayageEquipment = $row[6];
						$emptyPickUpInformation = $row[7];
						$truckingEquipment = $row[8];

						$pudate = str_replace('-','/',$row[9]);
                        $pdate = DateTime::createFromFormat('m/d/Y', $pudate);
                        if ($pdate !== false) { $pudate = $pdate->format('Y-m-d'); } 
                        else { $pudate = ''; }

						if($pudate=='') {
							$data['error'][] = 'Dispatch ID '.$did.' pickup date should not blank.';
							continue;
						}

						$pAppointType = $row[10];
						$pTime = $row[11];
						$check_pcity = $row[12];
						$check_plocation = $row[13];
						$check_paddress = $row[14];
						$pDescription = $row[15];
						$pQuantity = $row[16];
						$pWeight = $row[17];
						$pCommodity = $row[18];
                        $pickUp = $row[19];
						$pNotes = $row[20];
						$isPickAddress = $this->isAddressExist($pudate,$check_pcity,$check_plocation,$check_paddress,'yes');
						if(is_numeric($isPickAddress)){ $paddressid = $isPickAddress; }
						elseif($isPickAddress){
							$addr = $check_plocation.' '.$check_paddress.' '.$check_pcity;
							$data['error'][] = 'Dispatch ID '.$did.' pick up address ('.$addr.') not exist.';
							continue;
						}
						$pcity = $this->check_city($check_pcity);
						$plocation = $this->check_location($check_plocation);
						
						$dodate = $row[21];
						if($dodate != '') {
							$ddate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$dodate));
                            if ($ddate !== false) { $dodate = $ddate->format('Y-m-d'); } 
                            else { $dodate = ''; }
						}
						$dAppointType = $row[22];
						$dTime = $row[23];
						$check_dcity = $row[24];
						$check_dlocation = $row[25];
						$check_daddress = $row[26];
						$dDescription = $row[27];
						$dQuantity = $row[28];
						$dWeight = $row[29];
                        $dropOff = $row[30];
						$dNotes = $row[31];
						$isDropAddress = $this->isAddressExist($pudate,$check_dcity,$check_dlocation,$check_daddress,'yes');
						if(is_numeric($isDropAddress)){ $daddressid = $isDropAddress; }
						elseif($isDropAddress){
							$addr = $check_dlocation.' '.$check_daddress.' '.$check_dcity;
							$data['error'][] = 'Dispatch ID '.$did.' drop off address ('.$addr.') not exist.';
							continue;
						}
						$dcity = $this->check_city($check_dcity);
						$dlocation = $this->check_location($check_dlocation);

						$rate = str_replace('$','',$row[32]);
						if(!is_numeric($rate)) { $rate = 0; }
						$carrierPartialAmount = $row[33];

						$parate = str_replace('$','',$row[34]);
						if(!is_numeric($parate)) { $parate = 0; }
						
						$rateLumper = str_replace('$','',$row[35]);
						if(!is_numeric($rateLumper)) { $rateLumper = 0; }

						$check_company = $row[36];
						$company = $this->check_company($check_company);
						$companyInfo = $this->Comancontroler_model->get_data_by_id($company,'companies','paymenTerms,payoutRate,dayToPay');
						$shippingContact = $row[37];
						$tracking = $row[38];
						$trailer = $row[39];
						
						// generate invoice
						$inv_first = '';
						$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'dispatchOutside');
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
							
							if(stristr($row[40],$inv_first.''.$inv_middel)) {
								$invoice = $row[40];
							}
							elseif(strtotime($pudate) < strtotime('2024-04-25')){
								$invoice = $row[40];
								$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'dispatchOutside','id');
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
						
						$customerPartialAmount = str_replace('$','',$row[41]);
						if(!is_numeric($customerPartialAmount)) { $customerPartialAmount = 0; }

						$customerPayoutAmount = str_replace('$','',$row[42]);
						if(!is_numeric($customerPayoutAmount)) { $customerPayoutAmount = 0; }

						$expectedPayDate = $row[43];
						if($expectedPayDate != '') {
							$ddate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$expectedPayDate));
                            if ($ddate !== false) { $expectedPayDate = $ddate->format('Y-m-d'); } 
                            else { $expectedPayDate = ''; }
						}


						$invoiceType = trim($row[44]);
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }
												
						$pattern = '/^\d{2}[\/-]\d{2}[\/-]\d{4}$/';

						if($invoiceType != '' && (!preg_match($pattern, trim($row[47])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice ready date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						
						if(trim($row[51]) != '' && (!preg_match($pattern, trim($row[51])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice paid date (MM-DD-YYYY) format is wrong.';
							continue;
						}

						if(trim($row[53]) != '' && (!preg_match($pattern, trim($row[53])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice close date (MM-DD-YYYY) format is wrong.';
							continue;
						}

						if($invoiceType != '' && $invoiceType != 'RTS' && trim($row[71]) == ''){
        			        $data['error'][] = 'Dispatch ID '.$did.' invoice description is required.';
							continue;
        			    }
						
						if($check_company=='' || $check_pcity=='' || $check_dcity=='' || $check_plocation=='' || $check_dlocation=='' || $tracking=='' || $trucking=='') {
							$data['error'][] = 'Dispatch ID '.$did.' please fill all required fields.';
							continue;
						}
						
						$delivered = trim($row[55]);
						$bol = trim($row[56]);
						$rc = trim($row[57]);
						$gd = trim($row[58]);

						$carrierPaymentType = $row[59];
						if (empty($carrierPaymentType)) {
							$carrierPaymentType = ''; 
						}
						$factoringType = $row[60];
						if (empty($factoringType)) {
							$factoringType = ''; 
						}
						$fatoringCompany= $row[61];
						if($fatoringCompany != ''){
							$factoringCompany=$this->Comancontroler_model->get_data_by_column('company',$fatoringCompany,'factoringCompanies');
							if(isset($factoringCompany[0]['id'])){
								$factoringCompanyId = $factoringCompany[0]['id'];
							}else{
								$factoringCompanyId = '0';
							}
						}else{
							$factoringCompanyId = '0';
						}
						$carrierInvoiceRefNo= $row[63];
						$shipmentNotes= $row[68];
						$shipmentStatus= $row[69];
						$shipmentRemarks= $row[70];
						$invoiceDescription= $row[71];

						$dispatchMeta = array('expense'=>array(),'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoiceCloseDate'=>'','invoicePaidDate'=>'');
						$dispatchMetaJson = json_encode($dispatchMeta);
						
						$week = date('M',strtotime($pudate)).' W';
    					$day = date('d',strtotime($pudate));
    					if($day < 9) { $w = '1'; }
    					elseif($day < 16){ $w = '2'; }
    					elseif($day < 24){ $w = '3'; }
    					else { $w = '4'; }
    					$week .= $w; 					
						
						$insert_data = array(
							'truckingCompany'=>$trucking,
							'userid'=>$userid,
							'driver'=>$driver,
							'bookedUnderNew'=>$bookedUnder,
							'pudate'=>$pudate,
							'ptime'=>$pTime,
							'pcity'=>$pcity,
							'plocation'=>$plocation,
							'paddress'=>$check_paddress,
							'pcode'=>$pickUp,
							'pnotes'=>$pNotes,
							'dodate'=>$dodate,
							'dtime'=>$dTime,
							'dcity'=>$dcity,
							'dlocation'=>$dlocation,
							'daddress'=>$check_daddress,
							'dcode'=>$dropOff,
							'dnotes'=>$dNotes,
							'rate'=>$rate,
							'carrierPartialAmt'=>$carrierPartialAmount,
							'parate'=>$parate,
							'company'=>$company,	
							'shipping_contact'=>$shippingContact,	
							'tracking'=>$tracking,	
							'trailer'=>$trailer,
							'invoice'=>$invoice,
							'dWeek'=>$week,
							'payoutAmount'=>$customerPayoutAmount,
							'invoiceType'=>$invoiceType,
							'delivered'=>$delivered,
							'bol'=>$bol,
							'rc'=>$rc,
							'gd'=>$gd,
							'carrierPaymentType'=>$carrierPaymentType,
							'factoringType'=>$factoringType,
							'factoringCompany'=>$factoringCompanyId,
							'carrierInvoiceRefNo'=>$carrierInvoiceRefNo,
							'status'=>$shipmentNotes,
							'driver_status'=>$shipmentStatus,
							'notes'=>$shipmentRemarks,
							'invoiceNotes'=>$invoiceDescription				
						);
						//array('0.Dispatch ID','1.Trucking Company','2.Driver','3.Booked Under','4.Pick Up Date','5.Pick Up Time','6.Pick Up City','7.Pick Up Company','8.Pick Up Address','9.Pick Up','10.Pickup Notes','11.Drop Off Date','12.Drop Off Time','13.Drop Off City','14.Drop Off Company','15.Drop Off Address','16.Drop Off','17.Driver Notes','18.Rate','19.PA Rate','20.Rate Lumper','21.Company','22.Trailer','23.Tracking','24.Invoice','25.Week','26.Payout Amount','27.Invoice Date','28.Invoice Type','29.Expected Pay Date','30.Driver Assist','31.Status','32.Driver Status','33.Notes','34.Invoice Ready','35.Invoice Paid','36.Invoice Closed','37.Invoice Description','38.Carrier Payout Date','39.Sub Invoice')
						if(is_numeric($row[0]) && $row[0] > 1) { // update
							if($row[49] != 'TBD' && $row[49] != ''){
								$idate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[49]));
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

							if($row[64] != '0000-00-00' && $row[64] != ''){
								$cpdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[64]));
                                if ($cpdate !== false) { 
                                    $carrierPayoutDate = $cpdate->format('Y-m-d'); 
                                    $insert_data['carrierPayoutDate'] = $carrierPayoutDate;
                                    $insert_data['carrierPayoutCheck'] = '1';
                                }
							}
							
							
							$changeField = array();
							$getDispatch = $this->Comancontroler_model->get_data_by_column('id',$row[0],'dispatchOutside');
							if($getDispatch[0]['dispatchMeta'] != '') {
								$currentDiMeta = json_decode($getDispatch[0]['dispatchMeta'],true);
								$currentDiMeta['invoiceReadyDate'] = $currentDiMeta['invoicePaidDate'] = $currentDiMeta['invoiceCloseDate'] = '';
								$currentDiMeta['invoiceReady'] = $currentDiMeta['invoicePaid'] = $currentDiMeta['invoiceClose'] = $currentDiMeta['invoiced'] = '0';
								if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){ // invoice date 
									$currentDiMeta['invoiced'] = '1';
								}
								if(trim($row[47]) != ''){ // invoice ready date 
									$irdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[47]));
									if ($irdate !== false) { 
										$invoiceReadyDate = $irdate->format('Y-m-d'); 
										$currentDiMeta['invoiceReadyDate'] = $invoiceReadyDate;
										$currentDiMeta['invoiceReady'] = '1';
									}
								}
								if(trim($row[51]) != ''){ // invoice paid date 
									$ipdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[51]));
									if ($ipdate !== false) { 
										$invoicePaidDate = $ipdate->format('Y-m-d'); 
										$currentDiMeta['invoicePaidDate'] = $invoicePaidDate;
										$currentDiMeta['invoicePaid'] = '1';
									}
								}
								if(trim($row[53]) != ''){ // invoice closed date 
									$icdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[53]));
									if ($icdate !== false) { 
										$invoiceCloseDate = $icdate->format('Y-m-d'); 
										$currentDiMeta['invoiceCloseDate'] = $invoiceCloseDate;
										$currentDiMeta['invoiceClose'] = '1';
									}
								}
								if(trim($row[64]) != ''){ // carrier invoice closed date 
									$carrierinvdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[64]));
									if ($carrierinvdate !== false) { 
										$carrierInvoiceDate = $carrierinvdate->format('Y-m-d'); 
										$currentDiMeta['custInvDate'] = $carrierInvoiceDate;
									}
								}
								if($row['62'] == 'AK'){
									$currentDiMeta['carrierInvoiceCheck'] = '1';
								}else{
									$currentDiMeta['carrierInvoiceCheck']  = '0';
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
							
							/************* update history **************/
						
							if($getDispatch){
								foreach($getDispatch as $di){
									//// update data in sub invoice 
									if($di['childInvoice'] != '') {
										$ciNewArray = explode(',',$di['childInvoice']);
										foreach($ciNewArray as $subInv){
											if(trim($subInv) == ''){ continue; }
											$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice',$subInv,'dispatchOutside','id,dispatchMeta');
											if(empty($getSubDispatch)){ continue; }
											$subInvArr = array();
											if($getSubDispatch[0]['dispatchMeta'] == '') {
												$subDiMeta = array('expense'=>array(),'dispatchInfo'=>array(), 'invoiced'=>'0','invoicePaid'=>'0','invoiceClose'=>'0','invoiceReady'=>'0','invoiceReadyDate'=>'','invoicePaidDate'=>'','invoiceCloseDate'=>'');
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
												$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'],'dispatchOutside',$subInvArr);
											}
										}
									}
								
								
									if($di['driver'] != $insert_data['driver']) { $changeField[] = array('Driver','driver',$di['driver'],$insert_data['driver']); }
									if($di['truckingCompany'] != $insert_data['truckingCompany']) { $changeField[] = array('Trucking Company','truckingCompany',$di['truckingCompany'],$insert_data['truckingCompany']); }
									//if($di['bookedUnder'] != $insert_data['bookedUnder']) { $changeField[] = array('Booked Under','bookedUnder',$di['bookedUnder'],$insert_data['bookedUnder']); }
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
									if($di['carrierPayoutDate'] != $insert_data['carrierPayoutDate']) { $changeField[] = array('Carrier Payout Date','carrierPayoutDate',$di['carrierPayoutDate'],$insert_data['carrierPayoutDate']); }
									if($row[49] != 'TBD' && $row[49] != ''){
										if($di['invoiceDate'] != $insert_data['invoiceDate']) { 
											$changeField[] = array('Invoice Date','invoiceDate',$di['invoiceDate'],$insert_data['invoiceDate']);
											$changeField[] = array('Expect Pay Date','expectPayDate',$di['expectPayDate'],$insert_data['expectPayDate']);
										}
									}
									if($di['delivered'] != $insert_data['delivered']) { $changeField[] = array('Delivered','delivered',$di['delivered'],$insert_data['delivered']); }
									if($di['bol'] != $insert_data['bol']) { $changeField[] = array('BOL','bol',$di['bol'],$insert_data['bol']); }
									if($di['rc'] != $insert_data['rc']) { $changeField[] = array('RC','rc',$di['rc'],$insert_data['rc']); }
									if($di['gd'] != $insert_data['gd']) { $changeField[] = array('Cutomer Payment Proof Check Box','gd',$di['gd'],$insert_data['gd']); }
									if($di['carrierPaymentType'] != $insert_data['carrierPaymentType']) { $changeField[] = array('Carrier Payment Type','carrierPaymentType',$di['carrierPaymentType'],$insert_data['carrierPaymentType']); }
									if($di['factoringType'] != $insert_data['factoringType']) { $changeField[] = array('Factoring Type','factoringType',$di['factoringType'],$insert_data['factoringType']); }

									if($di['carrierInvoiceRefNo'] != $insert_data['carrierInvoiceRefNo']) { 
										if($di['carrierInvoiceRefNo']==''){
											$changeField[] = array('Carrier invoice Ref No','carrierInvoiceRefNo','No value',$insert_data['carrierInvoiceRefNo']);
										}else{
											$changeField[] = array('Carrier invoice Ref No','carrierInvoiceRefNo',$di['carrierInvoiceRefNo'],$insert_data['carrierInvoiceRefNo']);
										}
									}

								}
							}
							
							if($changeField) {
								$userid = $this->session->userdata('logged');
								$changeFieldJson = json_encode($changeField);
								$dispatchOutsideLog = array('did'=>$row[0],'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($dispatchOutsideLog,'dispatchOutsideLog'); 
							}
							
							
							$res = $this->Comancontroler_model->update_table_by_id($row[0],'dispatchOutside',$insert_data); 
							
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
							
							$res = $this->Comancontroler_model->add_data_in_table($insert_data,'dispatchOutside');
							if($res){
								
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
    	$this->load->view('admin/upload-outside-dispatch-csv',$data);
    	$this->load->view('admin/layout/footer');
    }	
	public function indexOld() {
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    //$data['dispatchInfo'] = $this->dispatchInfo;
	    $data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');
        /********* update status *********/
        if($this->input->post('statusonly') && $this->input->post('statusid'))	{
            $statusonly = $this->input->post('statusonly');
            $statusid = $this->input->post('statusid');
            if($statusonly!='' && $statusid > 0){
                $updatedata = array('status'=>$statusonly);
                $this->Comancontroler_model->update_table_by_id($statusid,'dispatchOutside',$updatedata);
                die('updated');
            }
        }
        
        $sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        $edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
        $truckingCompany = $driver = $status = $invoice = $tracking = $dispatchInfoValue = $dispatchInfo = '';
        
        ////// generate csv
		if($this->input->post('generateCSV') || $this->input->post('generateXls')){
            $truckingCompany = $this->input->post('truckingCompanies');
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
            
            $dispatch = $this->Comancontroler_model->downloadDispatchOutsideCSV($sdate,$edate,$truckingCompany,$driver,$status,$invoice,$tracking);
            $dispatchInfo = $this->Comancontroler_model->get_data_by_column('status','Active','dispatchInfo','title','title','asc');
            $expenses = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','title,type','title','asc');
			// Data to be written to the CSV file (example data)
			$heading = array('Dispatch ID','Trucking Company','Driver','Booked Under','Pick Up Date','Pick Up Time','Pick Up City','Pick Up Company','Pick Up Address','Pick Up','Pickup Notes','Drop Off Date','Drop Off Time','Drop Off City','Drop Off Company','Drop Off Address','Drop Off','Driver Notes','Rate','PA Rate','Rate Lumper','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Driver Assist','Shipment Notes','Shipment Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Sub Invoice');
			foreach($expenses as $ex){ $heading[] = $ex['title']; }
			foreach($dispatchInfo as $di){ $heading[] = $di['title']; }
			$data = array($heading);
			
			if(!empty($dispatch)) {
			    $comAddArr = array();
			    $companyAddress = $this->Comancontroler_model->get_data_by_table('companyAddress');
				if(!empty($companyAddress)){
					foreach($companyAddress as $val){
						$comAddArr[$val['id']] = array($val['company'],$val['city'].', '.$val['state'],$val['address'].' '.$val['city'].', '.$val['state'].' '.$val['zip']);
					}
				}
				
				foreach($dispatch as $row){
					$dispatchMeta = json_decode($row['dispatchMeta'],true);
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
					
				   $dataRow = array($row['id'],$row['ttruckingCompany'],$row['dname'],$row['bbookedUnder'],$pudate,$row['ptime'],$row['ppcity'],$row['pplocation'],$row['paddress'],$row['pcode'],cleanSpace($row['pnotes']),$dodate,$row['dtime'],$row['ddcity'],$row['ddlocation'],$row['daddress'],$row['dcode'],cleanSpace($row['dnotes']),$row['rate'],$row['parate'],$row['rateLumper'],$row['ccompany'],$trailer,$row['tracking'],$row['invoice'],$row['dWeek'],$row['payoutAmount'],$invoiceDate,$row['invoiceType'],$expectPayDate,$row['dassist'],$row['status'],$row['driver_status'],cleanSpace($row['notes']),$invReady,$invPaid,$invClosed,cleanSpace($row['invoiceNotes']),$row['childInvoice']);
				
				    
					foreach($expenses as $ex){ 
						$exInfo = '';
						if($dispatchMeta['expense']) { 
							foreach($dispatchMeta['expense'] as $diVal) {
								if($diVal[0] == $ex['title']){ $exInfo = $diVal[1]; }
							}
						}
						$dataRow[] = $exInfo;
					}
					foreach($dispatchInfo as $di){ 
						$disInfo = '';
						if($dispatchMeta['dispatchInfo']) { 
							foreach($dispatchMeta['dispatchInfo'] as $diVal) {
								if($diVal[0] == $di['title']){ $disInfo = $diVal[1]; }
							}
						}
						$dataRow[] = $disInfo;
					}
					$data[] = $dataRow;
				}
			}
            
			if($this->input->post('generateCSV')){
				$fileName = "OutsideDispatch_".$sdate."_".$edate.".csv"; 
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
				$fileName = "OutsideDispatch_".$sdate."_".$edate.".xlsx";   //"data_$date.xlsx";
				// Generate Excel file using the library
				$this->excel_generator->generateExcel($data, $fileName);
			}

			// Delete the file from the server
			unlink($fileName);
			exit;
			die('csv');
        }
		
		
        if($this->input->post('search'))	{
            
            $truckingCompany = $this->input->post('truckingCompanies');
            $driver = $this->input->post('driver'); 
            $dispatchInfo = $this->input->post('dispatchInfo'); 
            $dispatchInfoValue = $this->input->post('dispatchInfoValue'); 
            
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){ 
                if(date('Y-m',strtotime($sdate)) != date('Y-m',strtotime($edate))){
                    $this->session->set_flashdata('searchError', 'If you want to filter with week than both date must be same month.');
                    redirect(base_url('admin/outside-dispatch'));
                } else {
                    $weeks = explode(',',$week);
                    //$sdate = $weeks[0];
                    //$edate = $weeks[1];
                    $sdate = date('Y-m',strtotime($sdate)).$weeks[0];
                    $edate = date('Y-m',strtotime($edate)).$weeks[1];
                }
            } 
        } else {
            $data['dispatchOutside'] = array();
        }
        
    	$data['dispatchOutside'] = $this->Comancontroler_model->get_dispatchOutside_by_filter($sdate,$edate,$truckingCompany,$driver,$status,$invoice,$tracking,$dispatchInfoValue,$dispatchInfo);
    	$subInvoice = $dispatchArr = array();
    	if($data['dispatchOutside']){
    	    for($i=0;count($data['dispatchOutside']) > $i;$i++){
    	        
    	        if(in_array($data['dispatchOutside'][$i]['id'], $dispatchArr)) { continue; }
    	        $dispatchArr[] = $data['dispatchOutside'][$i]['id'];
    	        if($data['dispatchOutside'][$i]['childInvoice'] != '') { $subInvoice[] = $data['dispatchOutside'][$i]['invoice']; }
    	        
    	        $dispatchInfo = $this->Comancontroler_model->get_data_by_column('dispatchid',$data['dispatchOutside'][$i]['id'],'dispatchOutsideExtraInfo','pd_date,pd_city,pd_location,pd_time,pd_addressid','pd_order','desc','1');
				if($dispatchInfo){
					foreach($dispatchInfo as $dis){
						$data['dispatchOutside'][$i]['pd_date'] = $dis['pd_date'];
						$data['dispatchOutside'][$i]['pd_city'] = $dis['pd_city'];
						$data['dispatchOutside'][$i]['pd_location'] = $dis['pd_location'];
						$data['dispatchOutside'][$i]['pd_time'] = $dis['pd_time'];
					}
				} else {
				    $data['dispatchOutside'][$i]['pd_date'] = $data['dispatchOutside'][$i]['pd_city'] = $data['dispatchOutside'][$i]['pd_location'] = $data['dispatchOutside'][$i]['pd_time'] = $data['dispatchOutside'][$i]['pd_addressid'] = '';
				}
    	    }
    	}
    	if($subInvoice){
			$subDis = $this->Comancontroler_model->get_dispatchOutside_by_filter('','','','','','','','','',$subInvoice);
			if($subDis){
				foreach($subDis as $sd){
				    if(in_array($sd['id'], $dispatchArr)) { continue; }
    	            $dispatchArr[] = $sd['id'];
    	        
					$sd['pd_date'] = $sd['pd_city'] = $sd['pd_location'] = $sd['pd_time'] = '';
					$data['dispatchOutside'][] = $sd;
				}
			}
		}
    	
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
		$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies');
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
	    $data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/outsideDispatch',$data);
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
    
	public function getNextInvoice(){
		$pudate=$this->input->post('pudate');
		$driver=$this->input->post('driver');
		$inv_first = '';
			$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'dispatchOutside');
			if(empty($driver_trip)) { $inv_last = 1; }
			else { $inv_last = count($driver_trip) + 1; }
			if($inv_last < 10) {  $inv_last = '0'.$inv_last; }
			
			$driver_info = $this->Comancontroler_model->get_data_by_id($driver,'drivers');
			if(!empty($driver_info)) {
				$inv_first = $driver_info[0]['dcode'];
			}
			$inv_middel = date('mdy',strtotime($pudate));
			$invoice = $inv_first.''.$inv_middel.'-'.$inv_last;
			
			if($invoice == '' || $inv_first==''){
				$this->form_validation->set_rules('invoice', 'invoice','required'); 
				$set_message = 'Invoice number must not blank.';
				if($inv_first == ''){ $set_message = 'Driver code is empty.'; }
				$this->form_validation->set_message('required',$set_message); 
			}
			
			$invoice = $this->generateInvoice($driver_trip,$inv_first.''.$inv_middel.'-');
		echo json_encode(['invoice' => $invoice]);
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
 
    public function ajaxdelete(){
        if($this->input->post('ajaxdelete'))	{
            $did = $this->input->post('deleteid'); 
            $this->Comancontroler_model->delete($did,'dispatchOutside','id');
            $this->Comancontroler_model->delete($did,'dispatchOutsideExtraInfo','dispatchid');
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
				    //'gd'=>$this->input->post('gd_input'),
				    'driver_status'=>$this->input->post('driver_status_input'),
				    'status'=>$this->input->post('status_input')
				);
			
				$res = $this->Comancontroler_model->update_table_by_id($id,'dispatchOutside',$insert_data); 
				if($res){
					echo 'done';
				}
			}
	    }
    }
 
    public function extradispatchdelete(){
        $id = $this->uri->segment(4);
     	$result = $this->Comancontroler_model->delete($id,'dispatchOutsideExtraInfo','id');
     }
	public function removefile(){
		$folder = $this->uri->segment(4);
		$did = $this->uri->segment(6);
		$id = $this->uri->segment(5);
		$file = $this->Comancontroler_model->get_data_by_id($id,'documentsOutside');
		if(empty($file)) {
			$this->session->set_flashdata('item', 'File not exist.'); 
		} else {
			if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/outside-dispatch/'.$folder.'/'.$file[0]['fileurl'])) {
				unlink(FCPATH.'assets/outside-dispatch/'.$folder.'/'.$file[0]['fileurl']);  
				
				$this->session->set_flashdata('item', 'Document removed successfully.'); 
			}
			$this->Comancontroler_model->delete($id,'documentsOutside','id');
		}
		redirect(base_url('admin/outside-dispatch/update/'.$did));
	}
 
	public function check_city($city) {
		$city_data = $this->Comancontroler_model->get_city_by_name($city);
		if(empty($city_data)) {
			$insert_data = array('city'=>$city);
			$res = $this->Comancontroler_model->add_data_in_table($insert_data,'cities'); 
			return $res;
		} else {
			return $city_data[0]['id']; 
		}
	}
	public function check_location($location) {
		$company_data = $this->Comancontroler_model->get_location_by_name($location);
		if(empty($company_data)) {
			$insert_data = array('location'=>$location);
			$res = $this->Comancontroler_model->add_data_in_table($insert_data,'locations'); 
			return $res;
		} else {
			return $company_data[0]['id']; 
		}
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
 
 
	
	public function truckingCompanies() {
        if(!checkPermission($this->session->userdata('permission'),'companyt')){
	        redirect(base_url('AdminDashboard'));   
		}
		
		if ($this->input->post('search')) {
			$truckingCompany = $this->input->post('truckingCompany');
			$data['truckingCompanies'] = $this->getSearchedTruckingCompany($truckingCompany);
		} else {
			$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies','*','company','asc','All');
		}
		$data['truckingCompaniesForSelect'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies','*','company','asc','All');

    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/trucking-companies',$data);
    	$this->load->view('admin/layout/footer');
	}
	Public function getSearchedTruckingCompany($truckingCompany=[]){
		$where = '1=1 ';
		if ($truckingCompany != '') {
			$where .= " AND a.id IN (" . implode(",", $truckingCompany) . ")";
		}
		$sql="SELECT * FROM truckingCompanies a WHERE $where ORDER BY a.company ASC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function truckingCompaniesAdd(){
	    if(!checkPermission($this->session->userdata('permission'),'companyt')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    if($this->input->post('save'))	{
				
				$this->form_validation->set_rules('company', 'company','required|min_length[3]|max_length[50]');
				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[50]');
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else {
					$mc = $this->input->post('mc');
					$duplicate = $this->db->get_where('truckingCompanies', ['mc' => $mc])->row();
					if ($duplicate) {
						$existing_company = $duplicate->company;
						$this->session->set_flashdata('item', '<div class="alert alert-danger">MC number already exists and is assigned to ' . $existing_company . '.</div>');
						redirect(base_url('admin/trucking-company/add'));
					}
					
					$insert_data=array(
					    'company'=>$this->input->post('company'),
					    'mc'=>$mc,
					    'dot'=>$this->input->post('dot'),
					    'ein'=>$this->input->post('ein'),
					    'email'=>$this->input->post('email'),
						'email2' => implode(',', $this->input->post('email2')),
					    'password'=>$this->input->post('password'),
					    'status'=>'Active',
					    'owner'=>$this->input->post('owner')
					);
				
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'truckingCompanies'); 
					if($res){
						$this->session->set_flashdata('item', 'Carrier insert successfully.');
                        redirect(base_url('admin/trucking-company/add'));
					}
				}
		}
      
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/trucking-company-add',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	public function truckingCompaniesUpdate(){
	    if(!checkPermission($this->session->userdata('permission'),'companyt')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    
		$id = $this->uri->segment(4);
		if($this->input->post('save'))	{
				
				$this->form_validation->set_rules('company', 'company','required|min_length[3]|max_length[50]');
				$this->form_validation->set_rules('email', 'email','valid_email|min_length[3]|max_length[50]');
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {
					$mc = $this->input->post('mc');
					if(!empty($mc)){
						$duplicate = $this->db->where('mc', $mc)->where('id !=', $id)->get('truckingCompanies')->row();
						if ($duplicate) {
							$existing_company = $duplicate->company;
							$this->session->set_flashdata('item', '<div class="alert alert-danger">MC number already exists and is assigned to <strong>' . htmlspecialchars($existing_company) . '</strong>.</div>');
							redirect(base_url('admin/trucking-company/update/' . $id));
						}
					}
					
                    $config['upload_path'] = 'assets/truckingCompanies/';
                        $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm';
                        $config['max_size']= '5000';
                         
					
                    if(!empty($_FILES['tc_mc']['name'])){
                        $config['file_name'] = 'MC-'.$id.'-'.date('YmdHis').'-'.rand(1,9); //$_FILES['tc_mc']['name'];
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('tc_mc')){ 
                            $uploadData = $this->upload->data();
                            $tc_mc = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'tc_mc','docs_for'=>'truckingCompanies','fileurl'=>$tc_mc,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
                        }
                    } 
					if(!empty($_FILES['tc_ein']['name'])){
                        $config['file_name'] = 'EIN-'.$id.'-'.date('YmdHis').'-'.rand(1,9); //$_FILES['tc_ein']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('tc_ein')){ 
                            $uploadData = $this->upload->data();
                            $tc_ein = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'tc_ein','docs_for'=>'truckingCompanies','fileurl'=>$tc_ein,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
                        }
                    } 
					if(!empty($_FILES['tc_ownerID']['name'])){
                        $config['file_name'] = 'OwnerID-'.$id.'-'.date('YmdHis').'-'.rand(1,9); //$_FILES['tc_ownerID']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('tc_ownerID')){ 
                            $uploadData = $this->upload->data();
                            $tc_ownerID = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'tc_ownerID','docs_for'=>'truckingCompanies','fileurl'=>$tc_ownerID,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
                        }
                    } 
					if(!empty($_FILES['tc_w9']['name'])){
                        $config['file_name'] = 'W9-'.$id.'-'.date('YmdHis').'-'.rand(1,9); //$_FILES['tc_w9']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('tc_w9')){ 
                            $uploadData = $this->upload->data();
                            $tc_w9 = $uploadData['file_name'];
                            $addfile = array('did'=>$id,'type'=>'tc_w9','docs_for'=>'truckingCompanies','fileurl'=>$tc_w9,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'driver_document');
                        }
                    } 
                     
                    
					$insert_data=array(
					    'company'=>$this->input->post('company'),
					    'mc'=>$this->input->post('mc'),
					    'dot'=>$this->input->post('dot'),
					    'ein'=>$this->input->post('ein'),
					    'email'=>$this->input->post('email'),
						'email2' => implode(',', $this->input->post('email2')),
					    'password'=>$this->input->post('password'),
					    'status'=>$this->input->post('status'),
					    'owner'=>$this->input->post('owner')
					);
				
					$res = $this->Comancontroler_model->update_table_by_id($id,'truckingCompanies',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Carrier update successfully.');
                        redirect(base_url('admin/trucking-company/update/'.$id));
					}
				}
		}
     
		$data['truckingCompany'] = $this->Comancontroler_model->get_data_by_id($id,'truckingCompanies');
		$data['documents'] = $this->Comancontroler_model->get_driver_document($id,'truckingCompanies');
     
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/trucking-company-update',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	public function truckingCompaniesDelete(){
	    if(!checkPermission($this->session->userdata('permission'),'companyt')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $id = $this->uri->segment(4);
 		$result = $this->Comancontroler_model->delete($id,'truckingCompanies','id');
 		if($result){
 		    $documents = $this->Comancontroler_model->get_driver_document($id,'truckingCompanies');
 		    if($documents){
 		        foreach($documents as $file){
 		            if($file['fileurl']!='' && file_exists(FCPATH.'assets/truckingCompanies/'.$file['fileurl'])) {
                        unlink(FCPATH.'assets/truckingCompanies/'.$file['fileurl']);
                    }
                    $this->Comancontroler_model->delete($file['id'],'driver_document','id');
 		        }
 		        
 		    }
 		}
 		redirect('admin/trucking-companies');
    }
 
	public function truckingCompaniesRemoveFile(){ 
	    if(!checkPermission($this->session->userdata('permission'),'companyt')){
	        redirect(base_url('AdminDashboard'));   
	    }
     $did = $this->uri->segment(5);
     $id = $this->uri->segment(4);
     $file = $this->Comancontroler_model->get_data_by_id($id,'driver_document');
     if(empty($file)) {
         $this->session->set_flashdata('item', 'File not exist.'); 
     } else {
	    if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/truckingCompanies/'.$file[0]['fileurl'])) {
            unlink(FCPATH.'assets/truckingCompanies/'.$file[0]['fileurl']);  
            $this->session->set_flashdata('item', 'Document removed successfully.'); 
        }
        $this->Comancontroler_model->delete($id,'driver_document','id');
        redirect(base_url('admin/trucking-company/update/'.$did));
     }
     redirect(base_url('admin/trucking-company/update/'.$did)); 
 }
		
}