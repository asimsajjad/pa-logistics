<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WarehouseDispatch extends CI_Controller {
    
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
		 error_reporting(-1);
        ini_set('display_errors', 1);
        error_reporting(E_ERROR);
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
                $this->Comancontroler_model->update_table_by_id($statusid,'warehouse_dispatch',$updatedata);
                die('updated');
            }
        }
        
        $sdate = date('Y-m-d'); //date('Y-m-d',strtotime('last monday'));
        $edate = date('Y-m-d'); //date('Y-m-d',strtotime('+7 days',strtotime($sdate)));
        
		$company = $truckingCompany = $driver = $status = $invoice = $tracking = $dispatchInfoValue = $dispatchInfo = '';
        
        ////// generate csv
		if($this->input->post('generateCSV') || $this->input->post('generateXls')){
			$company = $this->input->post('company');    
            $truckingCompany = $this->input->post('truckingCompanies');
            $driver = $this->input->post('driver'); 
            
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); }
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); }
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){ 
                if(date('Y-m',strtotime($sdate)) != date('Y-m',strtotime($edate))){
                } else {
                    $weeks = explode(',',$week);
                    $sdate = date('Y-m',strtotime($sdate)).$weeks[0];
                    $edate = date('Y-m',strtotime($edate)).$weeks[1];
                }
            }
            
            $dispatch = $this->Comancontroler_model->downloadWarehouseDispatchCSV($sdate,$edate,$company,$truckingCompany,$driver,$status,$invoice,$tracking);
            $dispatchInfo = $this->Comancontroler_model->get_data_by_column('status','Active','dispatchInfo','id,title','title','asc');
            $expenses = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','id,title,type','title','asc');
			
			$dispatchIds = array_column($dispatch, 'id');
			$expenseDetails = [];
			if (!empty($dispatchIds)) {
				$expenseDetails = $this->db
					->select('ded.*')
					->from('dispatch_expense_details ded')
					->where_in('ded.did', $dispatchIds)
					->where('ded.dispatchType', 'warehouse')
					->get()
					->result_array();
			}

			$customExpenseDetails = [];
			if (!empty($dispatchIds)) {
				$customExpenseDetails = $this->db
					->select('dced.*')
					->from('dispatch_custom_expense_details dced')
					->where_in('dced.did', $dispatchIds)
					->where('dced.dispatchType', 'warehouse')
					->get()
					->result_array();
			}

			$infoDetails = [];
			if (!empty($dispatchIds)) {
				$infoDetails = $this->db
					->select('did.*')
					->from('dispatch_info_details did')
					->where_in('did.did', $dispatchIds)
					->where('did.dispatchType', 'warehouse')
					->get()
					->result_array();
			}


			$groupedExpenseDetails = [];
			foreach ($expenseDetails as $row) {
				$groupedExpenseDetails[$row['did']][$row['expenseInfoId']] = $row['expenseInfoValue'];
			}

			$groupedCustomExpenses = [];
			$customExpenseTitles = [];
			foreach ($customExpenseDetails as $row) {
				$groupedCustomExpenses[$row['did']][] = [
					'title' => $row['title'],
					'value' => $row['value']
				];
				$customExpenseTitles[$row['title']] = true;
			}
			$customExpenseTitles = array_keys($customExpenseTitles); 


			$groupedInfoDetails = [];
			foreach ($infoDetails as $row) {
				$groupedInfoDetails[$row['did']][$row['dispatchInfoId']] = $row['dispatchValue'];
			}
			
			$heading = array('Dispatch ID','Service Provider','Driver','Booked Under','Start Date', 'End Date','City','Company Location','Address','#','Notes','Service Provider Rate','Customer Rate','PA Rate','Rate Lumper','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Driver Assist','Shipment Notes','Shipment Status','Notes','Invoice Ready Date','Invoice Paid Date','Invoice Closed Date','Invoice Description','Carrier Invoice Date','Carrier Payout Date','Sub Invoice');
			foreach($expenses as $ex){ $heading[] = $ex['title']; }
			foreach ($customExpenseTitles as $ceTitle) {$heading[] = 'Custom: ' . $ceTitle;}
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
					$childDispatch = $this->Comancontroler_model->downloadWarehouseDispatchCSV('','','','','','','',$subInvoice);
					if($childDispatch) {
						foreach($childDispatch as $row){
							if(in_array($row['id'], $dispatchArr)) { continue; }
							$dispatchArr[] = $row['id'];
							$dispatch[] = $row;
						}
					}
				}
				
				foreach($dispatch as $row){
					$dispatchId = $row['id'];
				    $pudate = date('m/d/Y',strtotime($row['pudate']));
					  if($row['pudate']!='0000-00-00') {
   					   	$pudate = '="' . date('m-d-Y', strtotime($row['pudate'])) . '"';
				   }
				   $invoiceDate = $expectPayDate = $carrierPayoutDate = '0000-00-00';
					$enddate = date('m/d/Y',strtotime($row['edate']));
				   if($row['edate']!='0000-00-00') {
   					   	$enddate = '="' . date('m-d-Y', strtotime($row['edate'])) . '"';
				   }

				   if($row['invoiceDate']!='0000-00-00' && $row['invoiceDate']!='') {
					   	$invoiceDate = '="' . date('m-d-Y', strtotime($row['invoiceDate'])) . '"';
				   }
				   if($row['expectPayDate']!='0000-00-00' && $row['expectPayDate']!='') {
					   $expectPayDate = '="' . date('m-d-Y', strtotime($row['expectPayDate'])) . '"';
				   }
				   if($row['carrierPayoutDate']!='0000-00-00' && $row['carrierPayoutDate']!='') {
					   $carrierPayoutDate = '="' . date('m-d-Y', strtotime($row['carrierPayoutDate'])) . '"';
				   }
				   
				   $invReady = $row['invoiceReadyDate'];
				   if(trim($invReady) != '' && $invReady != '0000-00-00'){ 
						$invReady = '="' . date('m-d-Y', strtotime($invReady)) . '"';
					}
				   $invPaid = $row['invoicePaidDate'];
				   if(trim($invPaid) != '' && $invPaid != '0000-00-00'){ 
					$invPaid = '="' . date('m-d-Y', strtotime($invPaid)) . '"';
					}
				   $invClosed = $row['invoiceCloseDate'];
				   if(trim($invClosed) != '' && $invClosed != '0000-00-00'){ 
					$invClosed = '="' . date('m-d-Y', strtotime($invClosed)) . '"';
				 }
				    $carrierInvDate = $row['custInvDate'];
				   if(trim($carrierInvDate) != '' && $carrierInvDate != '0000-00-00'){ 
					$carrierInvDate = '="' . date('m-d-Y', strtotime($carrierInvDate)) . '"';
				 }
				   
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
					
				   $dataRow = array($row['id'],$row['ttruckingCompany'],$row['dname'],$row['bbookedUnder'],$pudate,$enddate,$row['ppcity'],$row['pplocation'],$row['paddress'],$row['pcode'],cleanSpace($row['pnotes']),$row['rate'],$row['customer_rate'],$row['parate'],$row['rateLumper'],$row['ccompany'],$trailer,$row['tracking'],$row['invoice'],$row['dWeek'],$row['payoutAmount'],$invoiceDate,$row['invoiceType'],$expectPayDate,$row['dassist'],$row['status'],$row['driver_status'],cleanSpace($row['notes']),$invReady,$invPaid,$invClosed,cleanSpace($row['invoiceNotes']),$carrierInvDate,$carrierPayoutDate,$row['childInvoice']);
				
			     	foreach($expenses as $ex){
						$val = isset($groupedExpenseDetails[$dispatchId][$ex['id']]) ? $groupedExpenseDetails[$dispatchId][$ex['id']] : '';
						$dataRow[] = $val;
					}

					foreach ($customExpenseTitles as $ceTitle) {
						$value = '';
						if (!empty($groupedCustomExpenses[$dispatchId])) {
							foreach ($groupedCustomExpenses[$dispatchId] as $ce) {
								if ($ce['title'] == $ceTitle) {$value = $ce['value'];break;}
							}
						}
						$dataRow[] = $value;
					}

					foreach($dispatchInfo as $di){
						$val = isset($groupedInfoDetails[$dispatchId][$di['id']]) ? $groupedInfoDetails[$dispatchId][$di['id']] : '';
						$dataRow[] = $val;
					}
					$data[] = $dataRow;
				}
			}
            
			if($this->input->post('generateCSV')){
				$fileName = "WarehouseDispatch_".$sdate."_".$edate.".csv"; 
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
				$fileName = "WarehouseDispatch_".$sdate."_".$edate.".xlsx";
				$this->excel_generator->generateExcel($data, $fileName);
			}

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
                    redirect(base_url('admin/paWarehouse'));
                } else {
                    $weeks = explode(',',$week);
                    //$sdate = $weeks[0];
                    //$edate = $weeks[1];
                    $sdate = date('Y-m',strtotime($sdate)).$weeks[0];
                    $edate = date('Y-m',strtotime($edate)).$weeks[1];
                }
            } 
        } else {
            $data['dispatchWarehouse'] = array();
        }
        
    	$data['dispatchWarehouse'] = $this->Comancontroler_model->get_dispatchWarehouse_by_filter($sdate,$edate,$company,$truckingCompany,$driver,$status,$invoice,$tracking,$dispatchInfoValue,$dispatchInfo,'');

    	$subInvoice = $dispatchArr = array();
    	if($data['dispatchWarehouse']){
    	    for($i=0;count($data['dispatchWarehouse']) > $i;$i++){
    	        $data['dispatchWarehouse'][$i]['sortcolumn'] = str_replace('-','',$data['dispatchWarehouse'][$i]['pudate']);
    	        if(in_array($data['dispatchWarehouse'][$i]['id'], $dispatchArr)) { continue; }
    	        $dispatchArr[] = $data['dispatchWarehouse'][$i]['id'];
    	        if($data['dispatchWarehouse'][$i]['childInvoice'] != '') { $subInvoice[] = $data['dispatchWarehouse'][$i]['invoice']; }
    	      
				$dispatchInfo = $this->Comancontroler_model->get_data_by_column('dispatchid',$data['dispatchWarehouse'][$i]['id'],'warehouse_dispatch_extra_info','pd_date,pd_city,pd_location,pd_time,pd_addressid,pd_type','pd_order','ASC','');
				
				// $where = array('did'=>$data['dispatchWarehouse'][$i]['id'],'dispatchType'=>'warehouse');
				$whereDispInfo = array('did'=>$data['dispatchWarehouse'][$i]['id'],'dispatchType'=>'warehouse');
				$where = array('did'=>$data['dispatchWarehouse'][$i]['id'],'dispatchType'=>'warehouse', 'expenseType'=>'customer');
				$whereServiceProvider = array('did'=>$data['dispatchWarehouse'][$i]['id'],'dispatchType'=>'warehouse', 'expenseType'=>'serviceProvider');

				$dispatchInfoDetails = $this->Comancontroler_model->get_data_by_multiple_column($whereDispInfo,'dispatch_info_details','*','','','');
				$dispatchExpenseDetails = $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_expense_details','*','','','');
				$dispatchCustomExpenseDetails = $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_custom_expense_details','*','','','');

				if($dispatchInfo){
					foreach($dispatchInfo as $dis){
						$data['dispatchWarehouse'][$i]['dispatchInfo'][] = [
							'pd_date' => $dis['pd_date'],
							'pd_city' => $dis['pd_city'],
							'pd_location' => $dis['pd_location'],
							'pd_time' => $dis['pd_time'],
							'pd_addressid' => $dis['pd_addressid'],
							'pd_type' => $dis['pd_type']

						];
					}
				} else {
				    $data['dispatchWarehouse'][$i]['pd_date'] = $data['dispatchWarehouse'][$i]['pd_city'] = $data['dispatchWarehouse'][$i]['pd_location'] = $data['dispatchWarehouse'][$i]['pd_time'] = $data['dispatchWarehouse'][$i]['pd_addressid'] = '';
					$data['dispatchWarehouse'][$i]['dispatchInfo'] = [
						[
							'pd_date' => '',
							'pd_city' => '',
							'pd_location' => '',
							'pd_time' => '',
							'pd_addressid' => ''
						]
					];
				}

				if($dispatchInfoDetails){
					foreach($dispatchInfoDetails as $dis){
						$dispatchInfoTitle=$this->Comancontroler_model->get_data_by_column('id',$dis['dispatchInfoId'],'dispatchInfo','title','','','');
						$data['dispatchWarehouse'][$i]['dispatchInfoDetails'][] = [
							'dispatchInfoId' => $dis['dispatchInfoId'],
							'dispatchValue' => $dis['dispatchValue'],
							'dispatchInfoTitle' => $dispatchInfoTitle[0]['title']
						];
					}
				} else {
					$data['dispatchWarehouse'][$i]['dispatchInfoDetails'] = [
						[
							'dispatchInfoId' => '',
							'dispatchValue' => '',
							'dispatchInfoTitle' =>''
						]
					];		
				}
				if($dispatchExpenseDetails){
					foreach($dispatchExpenseDetails as $dis){
						$dispatchExpenseTitle=$this->Comancontroler_model->get_data_by_column('id',$dis['expenseInfoId'],'expenses','title','','','');
						$data['dispatchWarehouse'][$i]['dispatchExpenseDetails'][] = [
							'expenseInfoId' => $dis['expenseInfoId'],
							'expenseInfoValue' => $dis['expenseInfoValue'],
							'expenseInfoTitle' => $dispatchExpenseTitle[0]['title']
						];
					}
				} else {
					$data['dispatchWarehouse'][$i]['dispatchExpenseDetails'] = [
						[
							'expenseInfoId' => '',
							'expenseInfoValue' => '',
							'expenseInfoTitle' => ''
						]
					];		
				}
				if ($dispatchCustomExpenseDetails) {
					foreach ($dispatchCustomExpenseDetails as $dis) {
						$data['dispatchWarehouse'][$i]['dispatchCustomExpenseDetails'][] = [
							'customExpenseId' => $dis['id'],
							'customExpenseValue' => $dis['value'],
							'customeExpenseTitle' => $dis['title']
						];
					}
				} else {
					$data['dispatchWarehouse'][$i]['dispatchCustomExpenseDetails'] = [
						[
							'customExpenseId' => '',
							'customExpenseValue' => '',
							'customeExpenseTitle' => ''
						]
					];
				}
    	    }
    	}
    	if($subInvoice){
			$subDis = $this->Comancontroler_model->get_dispatchWarehouse_by_filter('','','','','','','','','','',$subInvoice);
			if($subDis){
				foreach($subDis as $sd){
				    $sd['sortcolumn'] = str_replace('-','',$sd['pudate']);
				    if(in_array($sd['id'], $dispatchArr)) { continue; }
    	            $dispatchArr[] = $sd['id'];
    	        
					$sd['pd_date'] = $sd['pd_city'] = $sd['pd_location'] = $sd['pd_time'] = '';
					$data['dispatchWarehouse'][] = $sd;
				}
			}
		}
		$invoiceWiseTotal = [];
		foreach ($data['dispatchWarehouse'] as $key) {
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
    	$this->load->view('warehouseDispatch/warehouseDispatch',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	public function paWarehouseAdd_backup() {
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $userid = $this->session->userdata('adminid');
	    
        $data['truckingArr'] = $this->truckingArr;
		$data['truckingEquipments'] = $this->Comancontroler_model->get_data_by_table('truckingEquipments');

        $data['dispatchInfo'] = $this->Comancontroler_model->get_data_by_column('status','Active','dispatchInfo','id,title','title','asc');
        //$data['expenses'] = $this->expenses;
        $data['expenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','id,title,type','title','asc');
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
			$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'warehouse_dispatch');
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
				
				$invoiceType = ''; 
				$payoutAmount = 0;
				$pamargin = (float) $this->input->post('parate') - (float) $this->input->post('rate');
				
				$insert_data=array(
				    'driver'=>$driver,
				    'userid'=>$userid,
				    'pudate'=>$pudate,
				    'bookedUnderNew'=>$this->input->post('bookedUnderNew'),
				    'truckingCompany'=>$this->input->post('truckingCompany'),
					'warehouseServices'=>$this->input->post('warehouseServices'),
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
				    'inwrd'=>$this->input->post('inwrd'),
					'outwrd'=>$this->input->post('outwrd'),
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
				    'driver_status'=>$this->input->post('driver_status'),
				    'status'=>$this->input->post('status'),
					'carrierInvoiceCheck' => $this->input->post('carrierInvoiceCheck'),
					'pickup' => $this->input->post('pickup'),
					'pPort' => $this->input->post('pPort'),
					'pPortAddress' => $this->input->post('pPortAddress'),
					'dropoff' => $this->input->post('dropoff'),
					'dPort' => $this->input->post('dPort'),
					'dPortAddress' => $this->input->post('dPortAddress'),
					'invoicePDF' => $this->input->post('invoicePDF'),
					'drayageType' => $this->input->post('drayageType'),
					'invoiceDrayage' => $this->input->post('invoiceDrayage'),
					'invoiceTrucking' => $this->input->post('invoiceTrucking'),
					'appointmentTypeP' => $this->input->post('appointmentTypeP'),
					'quantityP' => $this->input->post('quantityP'),
					'commodityP' => $this->input->post('commodityP'),
					'metaDescriptionP' => $this->input->post('metaDescriptionP'),
					'weightP' => $this->input->post('weightP'),
					'appointmentTypeD' => $this->input->post('appointmentTypeD'),
					'quantityD' => $this->input->post('quantityD'),
					'metaDescriptionD' => $this->input->post('metaDescriptionD'),
					'weightD' => $this->input->post('weightD'),
					'erInformation' => $this->input->post('erInformation'),
					'driver_name' => $this->input->post('driver_name'),
					'driver_contact' => $this->input->post('driver_contact'),
				    'rdate'=>date('Y-m-d H:i:s')
				);
			
				$res = $this->Comancontroler_model->add_data_in_table($insert_data,'warehouse_dispatch'); 
				if($res){
					$expenseName = $this->input->post('expenseName');
					$expensePrice = $this->input->post('expensePrice');

					$dispatchInfoName = $this->input->post('dispatchInfoName');
					$dispatchInfoValue = $this->input->post('dispatchInfoValue');

					if (is_array($expenseName) && is_array($expensePrice)) {
						foreach ($expenseName as $key => $name) {
							if (!empty($name)) {
								$this->db->insert('dispatch_expense_details', [
									'did' => $res,
									'expenseInfoId' => $name,
									'expenseInfoValue' => $expensePrice[$key],
									'dispatchType' => 'warehouse'
								]);
							}
						}
					}

					if (is_array($dispatchInfoName) && is_array($dispatchInfoValue)) {
						foreach ($dispatchInfoName as $key => $infoName) {
							if (!empty($dispatchInfoValue[$key])) {
								$this->db->insert('dispatch_info_details', [
									'did' => $res,
									'dispatchInfoId' => $infoName,
									'dispatchValue' => $dispatchInfoValue[$key],
									'dispatchType' => 'warehouse'
								]);
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
							'appointmentType'=>$appointmentTypeP1[$i],
							'quantity' => $quantityP1[$i],
							'metaDescription' => $metaDescriptionP1[$i],
							'weight' => $weightP1[$i],
							'commodity' => $commodityP1[$i],
							'pd_type'=>'pickup'
    					    );
    					    $this->Comancontroler_model->add_data_in_table($extraData,'warehouse_dispatch_extra_info');
				          }
    					}
						for($i=0;$i<count($dodate1);$i++){
				          if($dodate1[$i]!='') { 
				            $dcodeVal1 = implode('~-~',$dcode1[$dcodename[$i]]);
				            $dcity1 = $this->check_city($check_dcity1[$i]);  
				            $dlocation1 = $this->check_location($check_dlocation1[$i]);
							
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
							'appointmentType'=>$appointmentTypeD1[$i],
							'quantity' => $quantityD1[$i],
							'metaDescription' => $metaDescriptionD1[$i],
							'weight' => $weightD1[$i],
							'pd_type'=>'dropoff'
    					    );
    					    $this->Comancontroler_model->add_data_in_table($extraData,'warehouse_dispatch_extra_info');
				          }
    					}
				    }
				    
				    
				    /*********** upload documents *********/
				
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
				
					$inwrdFilesCount = count($_FILES['inwrd_d']['name']);
					if($inwrdFilesCount > 0) {  
						$inwrdFiles = $_FILES['inwrd_d'];
						$config['upload_path'] = 'assets/warehouse/inwrd/';
						$config['file_name'] = $fileName1.'-INWRD-'.$fileName2; //$_FILES['bol_d']['name'];  
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
						//echo '<pre>';print_r($bolFiles);
						for($i = 0; $i < $inwrdFilesCount; $i++){
							$_FILES['inwrd_d']['name']     = $inwrdFiles['name'][$i];
							$_FILES['inwrd_d']['type']     = $inwrdFiles['type'][$i];
							$_FILES['inwrd_d']['tmp_name'] = $inwrdFiles['tmp_name'][$i];
							$_FILES['inwrd_d']['error']     = $inwrdFiles['error'][$i];
							$_FILES['inwrd_d']['size']     = $inwrdFiles['size'][$i]; 
					
							if ($this->upload->do_upload('inwrd_d'))  { 
								$dataInwrd = $this->upload->data(); 
								$inwrd = $dataInwrd['file_name'];
								$addinwrdfile = array('did'=>$res,'type'=>'inwrd','fileurl'=>$inwrd,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($addinwrdfile,'warehouse_documents');
							}
						}
					}

					$outwrdFilesCount = count($_FILES['outwrd_d']['name']);
					if($outwrdFilesCount > 0) {  
						$outwrdFiles = $_FILES['outwrd_d'];
						$config['upload_path'] = 'assets/warehouse/outwrd/';
						$config['file_name'] = $fileName1.'-OUTWRD-'.$fileName2; //$_FILES['bol_d']['name'];  
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
						//echo '<pre>';print_r($bolFiles);
						for($i = 0; $i < $outwrdFilesCount; $i++){
							$_FILES['outwrd_d']['name']     = $outwrdFiles['name'][$i];
							$_FILES['outwrd_d']['type']     = $outwrdFiles['type'][$i];
							$_FILES['outwrd_d']['tmp_name'] = $outwrdFiles['tmp_name'][$i];
							$_FILES['outwrd_d']['error']     = $outwrdFiles['error'][$i];
							$_FILES['outwrd_d']['size']     = $outwrdFiles['size'][$i]; 
					
							if ($this->upload->do_upload('outwrd_d'))  { 
								$dataOutwrd = $this->upload->data(); 
								$outwrd = $dataOutwrd['file_name'];
								$addOutwrdfile = array('did'=>$res,'type'=>'outwrd','fileurl'=>$outwrd,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($addOutwrdfile,'warehouse_documents');
							}
						}
					}
										
					if(!empty($_FILES['gd_d']['name'])){
						$config['upload_path'] = 'assets/warehouse/gd/';
                        $config['file_name'] = $fileName1.'-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('gd_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$res,'type'=>'gd','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
                        }
                    }
                    
                    $ciFilesCount = count($_FILES['carrierInvoice']['name']);
					if($ciFilesCount > 0) {  
						$ciFiles = $_FILES['carrierInvoice'];
						$config['upload_path'] = 'assets/warehouse/carrierInvoice/';
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
								$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
							}
						}
					}
					
                
					$this->session->set_flashdata('item', '	PA Warehousing add successfully.');
                    //redirect(base_url('admin/outside-dispatch/add'));
                    redirect(base_url('admin/paWarehouse/update'.$res.'#submit'));
				}
 			   
			}
	    }

		
        $id = $this->uri->segment(4);
        if($id > 0){
          $data['duplicate'] = $this->Comancontroler_model->get_data_by_id($id,'warehouse_dispatch');
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
		 $data['warehouseServices'] = $this->Comancontroler_model->get_data_by_column('status','Active','warehouseServices','id,title','title','asc');
	    $data['erInformation'] = $this->Comancontroler_model->get_data_by_table('erInformation');
	    $data['booked_under'] = $this->Comancontroler_model->get_data_by_table('booked_under');
	  
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('warehouseDispatch/warehouse_dispatch_add',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	function uploadcsv(){
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $data['error'] = array();
        $data['upload'] = '';
	    
	    if(isset($_GET['dummy']) && $_GET['dummy']=='csv'){
	        $data = array(
				array('Dispatch ID','Service Provider','Driver','Booked Under','Start Date', 'End Date','City','Company Location','Address','#','Notes','Service Provider Rate', 'Customer Rate', 'PA Rate','Rate Lumper','Company','Trailer','Tracking','Invoice','Week','Payout Amount','Invoice Date','Invoice Type','Expected Pay Date','Driver Assist','Shipment Notes','Shipment Status','Notes','Invoice Ready','Invoice Paid','Invoice Closed','Invoice Description','Carrier Invoice Date', 'Carrier Payout Date','Sub Invoice')
			);
			
			$fileName = "WarehouseDispatch_".date('Y-m-d').".csv"; 
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
				$this->load->library('upload',$config);
				$this->upload->initialize($config); 
				if($this->upload->do_upload('csvfile')){
					$uploadData = $this->upload->data();
					$bol = $uploadData['file_name'];
					$csv_file = $uploadData['full_path'];
					$csv = array_map('str_getcsv', file($csv_file));
                    $this->downloadDbBackup();
                    
					foreach ($csv as $row) { 
						if($row[0]=='Dispatch ID' || count($row) < 34) {
							continue;
						}
						
						$did = $row[0];
						$trucking = $row[1];
						$truckingArray = explode('(',$trucking);
						$truckingName = trim($truckingArray[0]);
						$truckingInfo = $this->Comancontroler_model->check_value_in_table('company',$truckingName,'truckingCompanies');
						if(count($truckingInfo) != '1'){ $trucking = ''; }
						else { $trucking = $truckingInfo[0]['id']; }
						
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
						$bookedUnderArray = explode('(',$bookedUnder);
						$bookedUnderName = trim($bookedUnderArray[0]);
						$bookedUnderInfo = $this->Comancontroler_model->check_value_in_table('company',$bookedUnderName,'truckingCompanies');
						if(count($bookedUnderInfo) != '1'){ $bookedUnder = ''; }
						else { $bookedUnder = $bookedUnderInfo[0]['id']; }

						$pudate = str_replace('-','/',$row[4]);
                        $pdate = DateTime::createFromFormat('m/d/Y', $pudate);
                        if ($pdate !== false) { $pudate = $pdate->format('Y-m-d'); } 
                        else { $pudate = ''; }
						if($pudate=='') {
							$data['error'][] = 'Dispatch ID '.$did.' start date should not blank.';
							continue;
						}
						
						$enddate = str_replace('-','/',$row[5]);
                        $edate = DateTime::createFromFormat('m/d/Y', $enddate);
                        if ($edate !== false) { $enddate = $edate->format('Y-m-d'); } 
                        else { $enddate = ''; }

                        $check_pcity = $row[6];
						$check_plocation = $row[7];
						$check_paddress = $row[8];
						
						$check_company = $row[15];
						$tracking = $row[17];
						$paddressid = $daddressid = 0;
						
						$isPickAddress = $this->isAddressExist($pudate,$check_pcity,$check_plocation,$check_paddress,'yes');
						if(is_numeric($isPickAddress)){ $paddressid = $isPickAddress; }
						elseif($isPickAddress){
							$addr = $check_plocation.' '.$check_paddress.' '.$check_pcity;
							$data['error'][] = 'Dispatch ID '.$did.' address ('.$addr.') not exist.';
							continue;
						}
						
						$invoiceType = trim($row[22]);
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }
						
						$pattern = '/^\d{2}[\/-]\d{2}[\/-]\d{4}$/';

						if($invoiceType != '' && (!preg_match($pattern, trim($row[28])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice ready date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						// if(trim($row[35]) != '' && (!preg_match($pattern, trim($row[35])))) {
						// 	$data['error'][] = 'Dispatch ID '.$did.' invoice paid date (MM-DD-YYYY) format is wrong.';
						// 	continue;
						// }
						if(trim($row[29]) != '' && trim($row[29]) != '0000-00-00' && !preg_match($pattern, trim($row[29]))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice paid date (MM-DD-YYYY) format is wrong.';
							continue;
						}

						if(trim($row[30]) != '' && trim($row[30]) != '0000-00-00' && (!preg_match($pattern, trim($row[30])))) {
							$data['error'][] = 'Dispatch ID '.$did.' invoice close date (MM-DD-YYYY) format is wrong.';
							continue;
						}
						if($invoiceType != '' && $invoiceType != 'RTS' && trim($row[31]) == ''){
        			        $data['error'][] = 'Dispatch ID '.$did.' invoice description is required.';
							continue;
        			    }
						
						
						
						
						if($check_company=='' || $check_pcity==''  || $check_plocation=='' || $tracking=='' || $trucking=='') {
							$data['error'][] = 'Dispatch ID '.$did.' please fill all required fields.';
							continue;
						}
						
						// generate invoice
						$inv_first = '';
						$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'warehouse_dispatch');
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
							
							if(stristr($row[18],$inv_first.''.$inv_middel)) {
								$invoice = $row[18];
							}
							elseif(strtotime($pudate) < strtotime('2024-04-25')){
								$invoice = $row[18];
								$invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'warehouse_dispatch','id');
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
						
						$pcity = $this->check_city($check_pcity);
						$plocation = $this->check_location($check_plocation);
						
						$rate = str_replace('$','',$row[11]);
						if(!is_numeric($rate)) { $rate = 0; }

						$customer_rate = str_replace('$','',$row[12]);
						if(!is_numeric($customer_rate)) { $customer_rate = 0; }

						$parate = str_replace('$','',$row[13]);
						if(!is_numeric($parate)) { $parate = 0; }
						
						$payoutAmount = $row[20];
						$invoiceType = $row[22];
						if($invoiceType=='DB'){ $invoiceType = 'Direct Bill'; }
						elseif($invoiceType=='QP'){ $invoiceType = 'Quick Pay'; }

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
							'driver'=>$driver,
							//'bookedUnder'=>$bookedUnder,
							'truckingCompany'=>$trucking,
							'pudate'=>$pudate,
							'edate'=>$enddate,
							'pcity'=>$pcity,
							'rate'=>$rate,
							'customer_rate'=>$customer_rate,
							'parate'=>$parate,
							'company'=>$company,
							'plocation'=>$plocation,
							'paddress'=>$row[8],
							'pcode'=>$row[9],
							'pnotes'=>$row[10],
							'trailer'=>$row[16],
							'tracking'=>$row[17],
							'invoice'=>$invoice,
							'payoutAmount'=>$payoutAmount,
							'invoiceType'=>$invoiceType,
							'dWeek'=>$week,
							'status'=>$row[25],
							'driver_status'=>$row[26],
							'notes'=>$row[27],
							'invoiceNotes'=>$row[31]
						);
						if(is_numeric($row[0]) && $row[0] > 1) {
							if($row[21] != 'TBD' && $row[21] != ''){
								$idate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[21]));
                                if ($idate !== false) { 
                                    $invoiceDate = $idate->format('Y-m-d'); 
                                    $insert_data['invoiceDate'] = $invoiceDate;
                                    
                                    if($invoiceType == 'RTS'){ $iDays = '+ 3 days'; }
            					    elseif($invoiceType == 'Direct Bill'){ $iDays = "+ 30 days"; }
            					    elseif($invoiceType == 'Quick Pay'){ $iDays = "+ 7 days"; }
                                    else { $iDays = '+ 1 month'; }
                                    $expectPayDate = date('Y-m-d',strtotime($iDays,strtotime($invoiceDate)));
                                    $insert_data['expectPayDate'] = $expectPayDate;
                                }
							}
							
							if($row[33] != '0000-00-00' && $row[33] != ''){
								$cpdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[33]));
                                if ($cpdate !== false) { 
                                    $carrierPayoutDate = $cpdate->format('Y-m-d'); 
                                    $insert_data['carrierPayoutDate'] = $carrierPayoutDate;
                                    $insert_data['carrierPayoutCheck'] = '1';
                                }
							}
							
							
							$changeField = array();
							$getDispatch = $this->Comancontroler_model->get_data_by_column('id',$row[0],'warehouse_dispatch');
							if($getDispatch) {
								$current= $getDispatch[0];
								// $current['invoiceReadyDate'] = $current['invoicePaidDate'] = $current['invoiceCloseDate'] = '';
								// $current['invoiceReady'] = $current['invoicePaid'] = $current['invoiceClose'] = $current['invoiced'] = '0';
								if(array_key_exists("invoiceDate",$insert_data) && trim($insert_data['invoiceDate']) != ''){
									$insert_data['invoiced'] = '1';
								}
								if(trim($row[28]) != ''){ 
									$irdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[28]));
									if ($irdate !== false) { 
										$invoiceReadyDate = $irdate->format('Y-m-d'); 
										$insert_data['invoiceReadyDate'] = $invoiceReadyDate;
										$insert_data['invoiceReady'] = '1';
									}
								}
								if(trim($row[29]) != ''){
									$ipdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[29]));
									if ($ipdate !== false) { 
										$invoicePaidDate = $ipdate->format('Y-m-d'); 
										$insert_data['invoicePaidDate'] = $invoicePaidDate;
										$insert_data['invoicePaid'] = '1';
									}
								}
								if(trim($row[30]) != ''){  
									$icdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[30]));
									if ($icdate !== false) { 
										$invoiceCloseDate = $icdate->format('Y-m-d'); 
										$insert_data['invoiceCloseDate'] = $invoiceCloseDate;
										$insert_data['invoiceClose'] = '1';
									}
								}
								if(trim($row[32]) != ''){  
									$carrierinvdate = DateTime::createFromFormat('m/d/Y', str_replace('-','/',$row[32]));
									if ($carrierinvdate !== false) { 
										$carrierInvoiceDate = $carrierinvdate->format('Y-m-d'); 
										$insert_data['custInvDate'] = $carrierInvoiceDate;
									}
								}
								// $dispatchMetaJson = json_encode($currentDiMeta);
								// $insert_data['dispatchMeta'] = $dispatchMetaJson;
								
								if($getDispatch){
									// $diMeta = json_decode($getDispatch[0]['dispatchMeta'],true);
									if($insert_data['invoiced'] != $current['invoiced']) { $changeField[] = array('Invoiced','invoiced',$insert_data['invoiced'],$current['invoiced']); }
									if($insert_data['invoicePaid'] != $current['invoicePaid']) { $changeField[] = array('Invoice Paid','invoicePaid',$insert_data['invoicePaid'],$current['invoicePaid']); }
									if($insert_data['invoicePaidDate'] != $current['invoicePaidDate']) { $changeField[] = array('Invoice Paid Date','invoicePaidDate',$insert_data['invoicePaidDate'],$current['invoicePaidDate']); }
									if($insert_data['invoiceClose'] != $current['invoiceClose']) { $changeField[] = array('Invoice Closed','invoiceClose',$insert_data['invoiceClose'],$current['invoiceClose']); }
									if($insert_data['invoiceCloseDate'] != $current['invoiceCloseDate']) { $changeField[] = array('Invoice Closed Date','invoiceCloseDate',$insert_data['invoiceCloseDate'],$current['invoiceCloseDate']); }
									if($insert_data['invoiceReady'] != $current['invoiceReady']) { $changeField[] = array('Ready to submit','invoiceReady',$insert_data['invoiceReady'],$current['invoiceReady']); }
									if($insert_data['invoiceReadyDate'] != $current['invoiceReadyDate']) { $changeField[] = array('Ready To Submit Date','invoiceReadyDate',$insert_data['invoiceReadyDate'],$current['invoiceReadyDate']); }
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
											$getSubDispatch = $this->Comancontroler_model->get_data_by_column('invoice',$subInv,'warehouse_dispatch','*');
											if(empty($getSubDispatch)){ continue; }
											$subInvArr = array();
											
											if(is_array($insert_data)){
												$subInvArr['invoiceReadyDate'] = $insert_data['invoiceReadyDate'];
												$subInvArr['invoicePaidDate'] = $insert_data['invoicePaidDate'];
												$subInvArr['invoiceCloseDate'] = $insert_data['invoiceCloseDate'];
												$subInvArr['invoiceReady'] = $insert_data['invoiceReady'];
												$subInvArr['invoicePaid'] = $insert_data['invoicePaid'];
												$subInvArr['invoiceClose'] = $insert_data['invoiceClose'];
												$subInvArr['invoiced'] = $insert_data['invoiced'];
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
												$this->Comancontroler_model->update_table_by_id($getSubDispatch[0]['id'],'warehouse_dispatch',$subInvArr);
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
									if($row[27] != 'TBD' && $row[27] != ''){
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
								$warehouseDispatchLog = array('did'=>$row[0],'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($warehouseDispatchLog,'warehouse_dispatch_log'); 
							}	
							$res = $this->Comancontroler_model->update_table_by_id($row[0],'warehouse_dispatch',$insert_data); 
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
							
							$insert_data['dispatchMeta'] = $dispatchMetaJson; 
							
							$insert_data['paddressid'] = $paddressid; 
							$insert_data['daddressid'] = $daddressid; 
							
							$res = $this->Comancontroler_model->add_data_in_table($insert_data,'warehouse_dispatch');
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
    	$this->load->view('warehouseDispatch/upload_warehouse_dispatch_csv',$data);
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
			$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'warehouse_dispatch');
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
			// echo $did;exit;
            $this->Comancontroler_model->delete($did,'warehouse_dispatch','id');
            $this->Comancontroler_model->delete($did,'warehouse_dispatch_extra_info','dispatchid');
			$where = array('did'=>$did,'dispatchType'=>'warehouse');
			$this->Comancontroler_model->delete_by_multiple_columns($where,'dispatch_info_details');
			$this->Comancontroler_model->delete_by_multiple_columns($where,'dispatch_expense_details');
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
				    // 'bol'=>$this->input->post('bol_input'),
				    // 'rc'=>$this->input->post('rc_input'),
				    'inwrd'=>$this->input->post('inwrd_input'),
					'outwrd'=>$this->input->post('outwrd_input'),
				    'driver_status'=>$this->input->post('driver_status_input'),
				    'status'=>$this->input->post('status_input')
				);
			
				$res = $this->Comancontroler_model->update_table_by_id($id,'warehouse_dispatch',$insert_data); 
				if($res){
					echo 'done';
				}
			}
	    }
    }
 
    public function extradispatchdelete(){
        $id = $this->uri->segment(4);
     	$result = $this->Comancontroler_model->delete($id,'warehouse_dispatch_extra_info','id');
     }
	public function removefile(){
		$folder = $this->uri->segment(4);
		$did = $this->uri->segment(6);
		$id = $this->uri->segment(5);
		$file = $this->Comancontroler_model->get_data_by_id($id,'warehouse_documents');
		if(empty($file)) {
			$this->session->set_flashdata('item', 'File not exist.'); 
		} else {
			if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/warehouse/'.$folder.'/'.$file[0]['fileurl'])) {
				unlink(FCPATH.'assets/warehouse/'.$folder.'/'.$file[0]['fileurl']);  
				
				$this->session->set_flashdata('item', 'Document removed successfully.'); 
			}
			$this->Comancontroler_model->delete($id,'warehouse_documents','id');
		}
		redirect(base_url('admin/paWarehouse/update/'.$did));
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
	public function paWarehouseUpdate() {
		//echo "<pre>"; print_r($_POST); die();
	    if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $data['truckingArr'] = $this->truckingArr;
		$data['truckingEquipments'] = $this->Comancontroler_model->get_data_by_table('truckingEquipments');

        $data['dispatchInfo'] = $this->Comancontroler_model->get_data_by_column('status','Active','dispatchInfo','id,title','title','asc');
		
        //$data['expenses'] = $this->expenses;
        $data['expenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','id,title,type','title','asc');
		$data['carrierExpenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','id,title,type','title','asc');
	    $data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');
	    
		$id = $this->uri->segment(4);
		$disInfo = $this->Comancontroler_model->get_data_by_id($id,'warehouse_dispatch');
		$oldChilds = $this->db->where('parent_id', $id)->where('parent_type', 'warehousing')->get('sub_invoices')->result_array();
		$oldChildMap = [];
		foreach ($oldChilds as $oc) {
			$oldChildMap[$oc['child_id'].'-'.$oc['child_type']] = $oc;
		}

		// print_r($disInfo);exit;

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

		$oldCustomerExpenses = $this->db->where('did', $id)->where('dispatchType', 'warehouse')->where('expenseType', 'customer')->get('dispatch_expense_details')->result_array();

		$oldSPExpenses = $this->db->where('did', $id)->where('dispatchType', 'warehouse')->where('expenseType', 'serviceProvider')->get('dispatch_expense_details')->result_array();

		$oldCustomExpenses = $this->db->where('did', $id)->where('dispatchType', 'warehouse')->get('dispatch_custom_expense_details')->result_array();

		$oldDispatchInfo = $this->db->where('did', $id)->where('dispatchType', 'warehouse')->get('dispatch_info_details')->result_array();

		if(empty($disInfo)){ redirect(base_url('admin/paWarehouse'));  }
		$changeField = array();
		
		if($this->input->post('save'))	{
				
			$this->form_validation->set_rules('pudate', 'PU date','required|min_length[9]');
			$this->form_validation->set_rules('driver', 'driver','required');
			$this->form_validation->set_rules('pcity', 'pickup city','required|min_length[1]');
			$this->form_validation->set_rules('company', 'company','required|min_length[1]'); 
			$this->form_validation->set_rules('plocation', 'pick up location','required|min_length[1]');
			
			$pudate = $this->input->post('pudate');
			$driver = $this->input->post('driver');
			$invoiceInput = $this->input->post('invoice');
		
			$inv_first = '';
			$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'warehouse_dispatch');
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
			    $invoiceInfo = $this->Comancontroler_model->get_data_by_column('invoice',$invoice,'warehouse_dispatch','id');
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

			$childInvoices = [];
			$warehouseChildInvoiceInfo = $this->input->post('warehouseChildInvoice');
			if (is_array($warehouseChildInvoiceInfo)) {
				foreach ($warehouseChildInvoiceInfo as $ciInvoiceNo) {
					$ciInfo = $this->Comancontroler_model->get_data_by_column('invoice', trim($ciInvoiceNo), 'warehouse_dispatch', 'id');
					if (empty($ciInfo)) {
						$this->form_validation->set_rules('childInvoicesss', 'child invoice','required');
						$this->form_validation->set_message('required', 'Warehousing invoice "'.$ciInvoiceNo.'" does not exist.');
					} else {
						$ciID = $ciInfo[0]['id'];
						$alreadyChild = $this->db
						->where('child_id', $ciID)
						->where('child_type', 'warehousing')
						->where('parent_id !=', $id)
						->get('sub_invoices')
						->row();
						if ($alreadyChild) {
							$this->form_validation->set_rules('childInvoicesss', 'child invoice','required');
							$this->form_validation->set_message('required', 'Warehousing invoice "'.$ciInvoiceNo.'" is already assigned as a child.');
						}

						$childInvoices[] = [
							'child_id'   => $ciID,
							'child_type' => 'warehousing',
							'child_invoice_no' => $ciInvoiceNo
						];
					}
				}
			}

			$fleetChildInvoiceInfo = $this->input->post('fleetChildInvoice');
			if (is_array($fleetChildInvoiceInfo)) {
				foreach ($fleetChildInvoiceInfo as $ciInvoiceNo) {
					$ciInfo = $this->Comancontroler_model->get_data_by_column('invoice', trim($ciInvoiceNo), 'dispatch', 'id,childInvoice');
					if (empty($ciInfo)) {
						$this->form_validation->set_rules('childInvoicesss', 'child invoice','required');
						$this->form_validation->set_message('required','Fleet invoice "'.$ciInvoiceNo.'" does not exist.');
					} else {
						$ciID = $ciInfo[0]['id'];
						$childInvoices[] = [
							'child_id'   => $ciID,
							'child_type' => 'fleet',
							'child_invoice_no' => $ciInvoiceNo
						];
					}
				}
			}

			$logisticsChildInvoiceInfo = $this->input->post('logisticsChildInvoice');
			if (is_array($logisticsChildInvoiceInfo)) {
				foreach ($logisticsChildInvoiceInfo as $ciInvoiceNo) {
					$ciInfo = $this->Comancontroler_model->get_data_by_column('invoice', trim($ciInvoiceNo), 'dispatchOutside', 'id,childInvoice');
					if (empty($ciInfo)) {
						$this->form_validation->set_rules('childInvoicesss', 'child invoice','required');
						$this->form_validation->set_message('required','Logistics invoice "'.$ciInvoiceNo.'" does not exist.');
					} else {
						$ciID = $ciInfo[0]['id'];
						$childInvoices[] = [
							'child_id'   => $ciID,
							'child_type' => 'logistics',
							'child_invoice_no' => $ciInvoiceNo
						];
					}
				}
			}
			$newChildMap = [];
			foreach ($childInvoices as $child) {
				$newChildMap[$child['child_id'].'-'.$child['child_type']] = $child;
			}

			$inwrdCheck = $this->input->post('inwrd');
			$outwrdCheck = $this->input->post('outwrd');
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
			
			if($gdCheck=='AK' && $invoiceType == ''){ 
			    $this->form_validation->set_rules('invoiceType', 'invoice type','required');
			    $this->form_validation->set_rules('invoiceReady', 'invoice ready checkbox','required');
			    $this->form_validation->set_rules('invoiceReadyDate', 'invoice ready date','required');
			}
			elseif($gdCheck=='AK' && $invoiceReady == '0'){ 
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
					
				$inwrdFilesCount = count($_FILES['inwrd_d']['name']);
				if($inwrdFilesCount > 0) {  
					$inwrdFiles = $_FILES['inwrd_d'];
					$config['upload_path'] = 'assets/warehouse/inwrd/';
					$config['file_name'] = $fileName1.'-INWRD-'.$fileName2;  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
					for($i = 0; $i < $inwrdFilesCount; $i++){
						$_FILES['inwrd_d']['name']     = $inwrdFiles['name'][$i];
						$_FILES['inwrd_d']['type']     = $inwrdFiles['type'][$i];
						$_FILES['inwrd_d']['tmp_name'] = $inwrdFiles['tmp_name'][$i];
						$_FILES['inwrd_d']['error']     = $inwrdFiles['error'][$i];
						$_FILES['inwrd_d']['size']     = $inwrdFiles['size'][$i]; 
				
						if ($this->upload->do_upload('inwrd_d'))  { 
							$dataInwrd = $this->upload->data(); 
							$inwrd = $dataInwrd['file_name'];
							$addfile = array('did'=>$id,'type'=>'inwrd','fileurl'=>$inwrd,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
							$changeField[] = array('Inward RD File','inwrdfile','Upload',$inwrd);
						}
					}
				}

				$outwrdFilesCount = count($_FILES['outwrd_d']['name']);
				if($outwrdFilesCount > 0) {  
					$outwrdFiles = $_FILES['outwrd_d'];
					$config['upload_path'] = 'assets/warehouse/outwrd/';
					$config['file_name'] = $fileName1.'-OUTWRD-'.$fileName2;  
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
					for($i = 0; $i < $outwrdFilesCount; $i++){
						$_FILES['outwrd_d']['name']     = $outwrdFiles['name'][$i];
						$_FILES['outwrd_d']['type']     = $outwrdFiles['type'][$i];
						$_FILES['outwrd_d']['tmp_name'] = $outwrdFiles['tmp_name'][$i];
						$_FILES['outwrd_d']['error']     = $outwrdFiles['error'][$i];
						$_FILES['outwrd_d']['size']     = $outwrdFiles['size'][$i]; 
				
						if ($this->upload->do_upload('outwrd_d'))  { 
							$dataOutwrd = $this->upload->data(); 
							$outwrd = $dataOutwrd['file_name'];
							$addfile = array('did'=>$id,'type'=>'outwrd','fileurl'=>$outwrd,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
							$changeField[] = array('Outward DD File','outwrdfile','Upload',$outwrd);
						}
					}
				}
				
				if(!empty($_FILES['gd_d']['name'])){
					$config['upload_path'] = 'assets/warehouse/gd/';
                    $config['file_name'] = $fileName1.'-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('gd_d')){
                        $uploadData = $this->upload->data();
                        $bol = $uploadData['file_name'];
						$addfile = array('did'=>$id,'type'=>'gd','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
						$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
						$changeField[] = array('Payment proof file','gdfile','Upload',$bol);
                    }
                } 
				
				$carrierGdFilesCount = count($_FILES['carrier_gd_d']['name']);
				if($carrierGdFilesCount > 0) {  
					$carrierGdFiles = $_FILES['carrier_gd_d'];
					$config['upload_path'] = 'assets/warehouse/gd/';
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
							$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
							$changeField[] = array('Carrier Payment proof file','gdfile','Upload',$Carrier_gd_d);
						}
					}
				}
                
               
                $ciFilesCount = count($_FILES['carrierInvoice']['name']);
				if($ciFilesCount > 0) {  
					$ciFiles = $_FILES['carrierInvoice'];
					$config['upload_path'] = 'assets/warehouse/carrierInvoice/';
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
							$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
							$changeField[] = array('Carrier invoice file','carrierInvoice','Upload',$bol);
						}
					}
				}
				
				$paInvoiceCount = count($_FILES['paInvoice']['name']);
				if($paInvoiceCount > 0) {  
					$paInvoiceFiles = $_FILES['paInvoice'];
					$config['file_name'] = $fileName1.'-Customer-Inv-'.$fileName2;   
					$config['upload_path'] = 'assets/warehouse/invoice/';
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
							$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
							$changeField[] = array('Customer Invoice File','paInvoice','Upload',$bol);
						}
					}
				}
                
				/******************* check companies ****************/
				$check_company = $this->input->post('company');
				$company = $this->check_company($check_company);
				$companyInfo = $this->Comancontroler_model->get_data_by_id($company,'companies','paymenTerms,payoutRate,dayToPay');
								
				$check_pcity = $this->input->post('pcity');
				$pcity = $this->check_city($check_pcity);
				
				$check_plocation = $this->input->post('plocation');
				$plocation = $this->check_location($check_plocation);
				
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
				$payoutAmount  = $this->input->post('payoutAmount');
				
				if($invoiceType == '' || $parate < 1 || strtotime($pudate) < strtotime('2024-09-01')) {  }
				elseif($invoiceType == 'RTS') { $payoutAmount = $parate - ($parate * 0.0115); }
				elseif($invoiceType == 'Direct Bill') { $payoutAmount = $parate * 1; }
				elseif($invoiceType == 'Quick Pay') { $payoutAmount = $parate - ($parate * 0.02); }
				if($payoutAmount > 0){ $payoutAmount = round($payoutAmount,2); }
				
				
				$expectPayDate = $this->input->post('expectPayDate');
				
				$invoiceDate = $this->input->post('invoiceDate');
				$invoiceType = $this->input->post('invoiceType');
				
				$inwrd = $this->input->post('inwrd');
				$outwrd = $this->input->post('outwrd');
				$bol = $this->input->post('bol');
				$rc = $this->input->post('rc');
				$gd = $this->input->post('gd');
				$carrier_gd = $this->input->post('carrier_gd');

				$status = $this->input->post('status');
				
				$dispatchMeta = array();
				
				$custInvDate = $this->input->post('custInvDate');
				$custDueDate = '';
				if($custInvDate != '' && $custInvDate != '0000-00-00'){
				    $custDueDate = date('Y-m-d',strtotime("+ 30 days", strtotime($custInvDate)));
				}
				
			
				$partialAmount = $this->input->post('partialAmount');
				$payableAmt = $payoutAmount - $partialAmount;
				if(!is_numeric($payableAmt)) { $payableAmt = 0; }
					
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
				    'bookedUnder'=>$this->input->post('bookedUnder'),
				    'bookedUnderNew'=>$this->input->post('bookedUnderNew'),
				    'warehouseServices'=>$this->input->post('warehouseServices'),
				    'pudate'=>$pudate,
					'edate'=>$this->input->post('edate'),		
				    'trip'=>$this->input->post('trip'),
				    'pcity'=>$pcity,
				    'rate'=>$this->input->post('rate'),
					'customer_rate'=>$this->input->post('customer_rate'),
				    'parate'=>$this->input->post('parate'),
					'agentRate'=>$this->input->post('agentRate'),
					'agentPercentRate'=>$this->input->post('agentPercentRate'),
					'carrierPlusAgentRate'=>$carrierPlusAgentRate,
				    'company'=>$company,
					'shipping_contact' => $this->input->post('shipping_contact'),
				    'plocation'=>$plocation,
				    'pcode'=>$pcodeVal,
				    'trailer'=>$this->input->post('trailer'),
				    'tracking'=>$this->input->post('tracking'),
				    'paddress'=>$this->input->post('paddress'),
				    'paddressid'=>$this->input->post('paddressid'),
				    'invoice'=>$invoice,
				    'invoiceType'=>$invoiceType,
					'carrierPaymentType'=>$carrierPaymentType,
					'factoringType' => $factoringType,
					'factoringCompany' => $this->input->post('factoringCompany'),
				    'payoutAmount'=>$payoutAmount,
				    'payableAmt'=>$payableAmt,
					'partialAmount' => $partialAmount,
				    'dWeek'=>$week,
				    'inwrd'=>$inwrd,
					'outwrd'=>$outwrd,
				    'gd'=>$gd,
					'carrierGd'=>$carrier_gd,
				    'delivered'=>$this->input->post('delivered'),
				    'notes'=>$this->input->post('notes'),
				    'pnotes'=>$this->input->post('pnotes'),
				    'dnotes'=>$this->input->post('dnotes'),
				    'invoiceNotes'=>$this->input->post('invoiceNotes'),
				    'pamargin'=>$pamargin,
				    'carrierPayoutDate'=>$this->input->post('carrierPayoutDate'),
				    'carrierPayoutCheck'=>$this->input->post('carrierPayoutCheck'),
					'carrierInvoiceRefNo'=>$this->input->post('carrierInvoiceRefNo'),
				    'lockDispatch'=>$this->input->post('lockDispatch'),
				    'driver_status'=>$driver_status,
				    'status'=>$status,
					'carrierInvoiceCheck' => $this->input->post('carrierInvoiceCheck'),
					'invoiced' => $this->input->post('invoiced'),
					'invoicePaid' => $this->input->post('invoicePaid'),
					'invoiceClose' => $this->input->post('invoiceClose'),
					'invoiceReady' => $this->input->post('invoiceReady'),
					'invoiceCloseDate' => $this->input->post('invoiceCloseDate'),
					'invoicePaidDate' => $this->input->post('invoicePaidDate'),
					'invoiceReadyDate' => $this->input->post('invoiceReadyDate'),
					'pickup' => $this->input->post('pickup'),
					'pPort' => $this->input->post('pPort'),
					'pPortAddress' => $this->input->post('pPortAddress'),
					'dropoff' => $this->input->post('dropoff'),
					'dPort' => $this->input->post('dPort'),
					'dPortAddress' => $this->input->post('dPortAddress'),
					'custInvDate' => $custInvDate,
					'custDueDate' => $custDueDate,
					'invoicePDF' => $this->input->post('invoicePDF'),
					'drayageType' => $this->input->post('drayageType'),
					'invoiceDrayage' => $this->input->post('invoiceDrayage'),
					'invoiceTrucking' => $this->input->post('invoiceTrucking'),
					'quantityP' => $this->input->post('quantityP'),
					'commodityP' => $this->input->post('commodityP'),
					'metaDescriptionP' => $this->input->post('metaDescriptionP'),
					'weightP' => $this->input->post('weightP'),
					'erInformation' => $this->input->post('erInformation'),
					'driver_name' => $this->input->post('driver_name'),
					'driver_contact' => $this->input->post('driver_contact')
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
			
				$res = $this->Comancontroler_model->update_table_by_id($id,'warehouse_dispatch',$insert_data); 
				if($res){
					$this->db->where('did', $id);
					$this->db->where('dispatchType', 'warehouse');
					$this->db->delete('dispatch_expense_details');

					$expenseName = $this->input->post('expenseName');
					$expensePrice = $this->input->post('expensePrice');

					$carrierExpenseName = $this->input->post('carrierExpenseName');
					$carrierExpensePrice = $this->input->post('carrierExpensePrice');

					if (is_array($expenseName) && is_array($expensePrice)) {
						foreach ($expenseName as $key => $name) {
							if (!empty($name)) {
								$this->db->insert('dispatch_expense_details', [
									'did' => $id,
									'expenseInfoId' => $name,
									'expenseInfoValue' => $expensePrice[$key],
									'dispatchType' => 'warehouse',
									'expenseType' => 'customer'
								]);
							}
						}
					}

					if (is_array($carrierExpenseName) && is_array($carrierExpensePrice)) {
						foreach ($carrierExpenseName as $key => $name) {
							if (!empty($name)) {
								$this->db->insert('dispatch_expense_details', [
									'did' => $id,
									'expenseInfoId' => $name,
									'expenseInfoValue' => $carrierExpensePrice[$key],
									'dispatchType' => 'warehouse',
									'expenseType' => 'serviceProvider'
								]);
							}
						}
					}

					$this->db->where('did', $id);
					$this->db->where('dispatchType', 'warehouse');
					$this->db->delete('dispatch_custom_expense_details');

					$customExpenseTitles = $this->input->post('customExpenseName');
					$customExpensePrices = $this->input->post('customExpensePrice');

					if (!empty($customExpenseTitles) && is_array($customExpenseTitles)) {
						foreach ($customExpenseTitles as $key => $title) {
							$price = isset($customExpensePrices[$key]) ? $customExpensePrices[$key] : 0;
							$this->db->insert('dispatch_custom_expense_details', [
								'did' => $id,
								'title' => trim($title),
								'value' => (float) $price,
								'dispatchType' => 'warehouse'
							]);
						}
					}

					$this->db->where('did', $id);
					$this->db->where('dispatchType', 'warehouse');
					$this->db->delete('dispatch_info_details');

					$dispatchInfoName = $this->input->post('dispatchInfoName');
					$dispatchInfoValue = $this->input->post('dispatchInfoValue');

					if (is_array($dispatchInfoName) && is_array($dispatchInfoValue)) {
						foreach ($dispatchInfoName as $key => $infoName) {
							if (!empty($dispatchInfoValue[$key])) {
								$this->db->insert('dispatch_info_details', [
									'did' => $id,
									'dispatchInfoId' => $infoName,
									'dispatchValue' => $dispatchInfoValue[$key],
									'dispatchType' => 'warehouse'
								]);
							}
						}
					}
				    /************* update history **************/
					foreach ($oldChildMap as $key => $old) {
						if (!isset($newChildMap[$key])) {
							if ($old['child_type'] == 'warehousing') {
								$this->Comancontroler_model->update_table_by_id($old['child_id'], 'warehouse_dispatch', ['parentInvoice' => '']);
								$row = $this->Comancontroler_model->get_data_by_id($old['child_id'], 'warehouse_dispatch');
								// print_r($row[0]['status']);exit;
								if ($row && !empty($row[0]['status'])) {
									$firstPart = explode(' - Linked to', $row[0]['status']);
									$this->Comancontroler_model->update_table_by_id($old['child_id'], 'warehouse_dispatch', ['status' => $firstPart[0]]);
								}
							} elseif ($old['child_type'] == 'fleet') {
								$this->Comancontroler_model->update_table_by_id($old['child_id'], 'dispatch', ['otherParentInvoice' => '']);
								$row = $this->Comancontroler_model->get_data_by_id($old['child_id'], 'dispatch');
								if ($row && !empty($row[0]['status'])) {
									$firstPart = explode(' - Linked to', $row[0]['status']);
									$this->Comancontroler_model->update_table_by_id($old['child_id'], 'dispatch', ['status' => $firstPart[0]]);
								}
							} elseif ($old['child_type'] == 'logistics') {
								$this->Comancontroler_model->update_table_by_id($old['child_id'], 'dispatchOutside', ['otherParentInvoice' => '']);
								$row = $this->Comancontroler_model->get_data_by_id($old['child_id'], 'dispatchOutside');
								if ($row && !empty($row[0]['status'])) {
									$firstPart = explode(' - Linked to', $row[0]['status']);
									$this->Comancontroler_model->update_table_by_id($old['child_id'], 'dispatchOutside', ['status' => $firstPart[0]]);
								}
							}
							$this->db->where('id', $old['id'])->delete('sub_invoices');
						}
					}
					foreach ($newChildMap as $key => $new) {
						if (!isset($oldChildMap[$key])) {
							$this->db->insert('sub_invoices', [
								'parent_id'   => $id,
								'parent_type' => 'warehousing',
								'child_id'    => $new['child_id'],
								'child_type'  => $new['child_type'],
							]);
							$this->updateSubInvoices($new, $insert_data, $invoice);
						}else{
							$this->updateSubInvoices($new, $insert_data, $invoice);
						}
					}


					if($disInfo){
						
						foreach($disInfo as $di){
							
							if($di['driver'] != $insert_data['driver']) { 
								$changeField[] = array('Driver','driver',$di['driver'],$insert_data['driver']);
							 }
							if($di['truckingCompany'] != $insert_data['truckingCompany']) { $changeField[] = array('Service Provider','truckingCompany',$di['truckingCompany'],$insert_data['truckingCompany']); }
							
							if($di['bookedUnder'] != $insert_data['bookedUnder']) { $changeField[] = array('Booked Under','bookedUnder',$di['bookedUnder'],$insert_data['bookedUnder']); }

							if($di['bookedUnderNew'] != $insert_data['bookedUnderNew']) { $changeField[] = array('Booked Under New','bookedUnderNew',$di['bookedUnderNew'],$insert_data['bookedUnderNew']); }

							if($di['warehouseServices'] != $insert_data['warehouseServices']) { $changeField[] = array('Type Of Services','warehouseServices',$di['warehouseServices'],$insert_data['warehouseServices']); }

							if($di['trip'] != $insert_data['trip']) { $changeField[] = array('Trip','trip',$di['trip'],$insert_data['trip']); }

							if($di['pudate'] != $insert_data['pudate']) { $changeField[] = array('Start Date','pudate',$di['pudate'],$insert_data['pudate']); }

							if($di['edate'] != $insert_data['edate']) { $changeField[] = array('End Date','edate',$di['edate'],$insert_data['edate']); }

							if($di['plocation'] != $insert_data['plocation']) { $changeField[] = array('Warehouse Location','plocation',$di['plocation'],$insert_data['plocation']); }

							if($di['pcity'] != $insert_data['pcity']) { $changeField[] = array('Warehouse City','pcity',$di['pcity'],$insert_data['pcity']); }

							if($di['paddress'] != $insert_data['paddress']) { $changeField[] = array('Warehouse Address','paddress',$di['paddress'],$insert_data['paddress']); }

							if($di['pcode'] != $insert_data['pcode']) { $changeField[] = array('#','pcode',$di['pcode'],$insert_data['pcode']); }

							if($di['pnotes'] != $insert_data['pnotes']) { $changeField[] = array('Warehouse Notes','pnotes',$di['pnotes'],$insert_data['pnotes']); }

							if($di['quantityP'] != $insert_data['quantityP']) { $changeField[] = array('Quantity','quantity',$di['quantityP'],$insert_data['quantityP']); }

							if($di['weightP'] != $insert_data['weightP']) { $changeField[] = array('Weight','weight',$di['weightP'],$insert_data['weightP']); }

							if($di['commodityP'] != $insert_data['commodityP']) { $changeField[] = array('Commodity','commodity',$di['commodityP'],$insert_data['commodityP']); }

							if($di['rate'] != $insert_data['rate']) { $changeField[] = array('Service Provider Rate','rate',$di['rate'],$insert_data['rate']); }

							if($di['agentRate'] != $insert_data['agentRate']) { $changeField[] = array('Brooker Rate','agentRate',$di['agentRate'],$insert_data['agentRate']); }

							if($di['agentPercentRate'] != $insert_data['agentPercentRate']) { $changeField[] = array('Agent Percent Rate','agentPercentRate',$di['agentPercentRate'],$insert_data['agentPercentRate']); }

							if($di['carrierPlusAgentRate'] != $insert_data['carrierPlusAgentRate']) { $changeField[] = array('Total Amount','carrierPlusAgentRate',$di['carrierPlusAgentRate'],$insert_data['carrierPlusAgentRate']); }

							if($di['parate'] != $insert_data['parate']) { $changeField[] = array('Invoice Amount','parate',$di['parate'],$insert_data['parate']); }

							if($di['pamargin'] != $insert_data['pamargin']) { $changeField[] = array('Margin','pamargin',$di['pamargin'],$insert_data['pamargin']); }

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

							if($di['tracking'] != $insert_data['tracking']) { $changeField[] = array('Tracking Number','tracking',$di['tracking'],$insert_data['tracking']); }

							if($di['invoice'] != $insert_data['invoice']) { $changeField[] = array('Invoice','invoice',$di['invoice'],$insert_data['invoice']); }

							if($di['dWeek'] != $insert_data['dWeek']) { $changeField[] = array('Week','dWeek',$di['dWeek'],$insert_data['dWeek']); }
			
							if($di['payoutAmount'] != $insert_data['payoutAmount']) { $changeField[] = array('Payout Amount','payoutAmount',$di['payoutAmount'],$insert_data['payoutAmount']); }

							if($di['partialAmount'] != $insert_data['partialAmount']) { $changeField[] = array('Partial Amount','partialAmount',$di['partialAmount'],$insert_data['partialAmount']); }
							
							if($invoiceDate != 'TBD' && $invoiceDate != ''){
								if($di['invoiceDate'] != $insert_data['invoiceDate']) { 
									$changeField[] = array('Invoice Date','invoiceDate',$di['invoiceDate'],$insert_data['invoiceDate']);
									$changeField[] = array('Expect Pay Date','expectPayDate',$di['expectPayDate'],$insert_data['expectPayDate']);
								}
							}

							if($di['invoiceType'] != $insert_data['invoiceType']) { $changeField[] = array('Invoice Type','invoiceType',$di['invoiceType'],$insert_data['invoiceType']); }

							if($di['invoiced'] != $insert_data['invoiced']) { $changeField[] = array('Invoiced checkbox','invoiced',$di['invoiced'],$insert_data['invoiced']); }

							if($di['invoicePaid'] != $insert_data['invoicePaid']) { $changeField[] = array('Invoice Paid checkbox','invoicePaid',$di['invoicePaid'],$insert_data['invoicePaid']); }

							if($di['invoicePaidDate'] != $insert_data['invoicePaidDate']) { $changeField[] = array('Invoice Paid Date','invoicePaidDate',$di['invoicePaidDate'],$insert_data['invoicePaidDate']); }

							if($di['invoiceClose'] != $insert_data['invoiceClose']) { $changeField[] = array('Invoice Closed checkbox','invoiceClose',$di['invoiceClose'],$insert_data['invoiceClose']); }

							if($di['invoiceCloseDate'] != $insert_data['invoiceCloseDate']) { $changeField[] = array('Invoice Closed Date','invoiceCloseDate',$di['invoiceCloseDate'],$insert_data['invoiceCloseDate']); }

							if($di['invoiceReady'] != $insert_data['invoiceReady']) { $changeField[] = array('Ready to submit checkbox','invoiceReady',$di['invoiceReady'],$insert_data['invoiceReady']); }
								
							if($di['invoiceReadyDate'] != $insert_data['invoiceReadyDate']) { $changeField[] = array('Ready To Submit Date','invoiceReadyDate',$di['invoiceReadyDate'],$insert_data['invoiceReadyDate']); }
								
							if($di['invoicePDF'] != $insert_data['invoicePDF']) { $changeField[] = array('Invoice PDF','invoicePDF',$di['invoicePDF'],$insert_data['invoicePDF']); }

							if($di['inwrd'] != $insert_data['inwrd']) { $changeField[] = array('Inward RD','inwrd',$di['inwrd'],$insert_data['inwrd']); }

							if($di['outwrd'] != $insert_data['outwrd']) { $changeField[] = array('Outward DD','outwrd',$di['outwrd'],$insert_data['outwrd']); }

							if($di['gd'] != $insert_data['gd']) { $changeField[] = array('$','gd',$di['gd'],$insert_data['gd']); }

							if($di['carrier_gd'] != $insert_data['carrier_gd']) { $changeField[] = array('Carrier Payment Proof Check box','carrier_gd',$di['carrier_gd'],$insert_data['carrier_gd']); }

							if($di['carrierPaymentType'] != $insert_data['carrierPaymentType']) { $changeField[] = array('Service Provider Payment Type','carrierPaymentType',$di['carrierPaymentType'],$insert_data['carrierPaymentType']); }

							if($di['factoringType'] != $insert_data['factoringType']) { $changeField[] = array('Factoring Type','factoringType',$di['factoringType'],$insert_data['factoringType']); }

							if($oldfactoringCompany != $newfactoringCompany) { 
								$changeField[] = array('Factoring Company','factoringCompany',$oldfactoringCompany,$newfactoringCompany);
							}

							if($di['driver_status'] != $insert_data['driver_status']) { $changeField[] = array('Driver Status','driver_status',$di['driver_status'],$insert_data['driver_status']); }

							if($di['status'] != $insert_data['status']) { $changeField[] = array('Status','status',$di['status'],$insert_data['status']); }

							if($di['delivered'] != $insert_data['delivered']) { $changeField[] = array('Delivered','delivered',$di['delivered'],$insert_data['delivered']); }
							
							if($di['lockDispatch'] != $insert_data['lockDispatch']) { $changeField[] = array('Lock Dispatch','lockDispatch',$di['lockDispatch'],$insert_data['lockDispatch']); }
							

							if($di['notes'] != $insert_data['notes']) { $changeField[] = array('Notes','notes',$di['notes'],$insert_data['notes']); }

							if($di['invoiceNotes'] != $insert_data['invoiceNotes']) { 
								if($di['invoiceNotes'] == ''){
									$changeField[] = array('Invoice Description','invoiceNotes','No Notes',$insert_data['invoiceNotes']); 
								}else{
									$changeField[] = array('Invoice Description','invoiceNotes',$di['invoiceNotes'],$insert_data['invoiceNotes']); 
								}
								
							}				
							
							if (is_array($dispatchInfoName)) {
								for ($i = 0; $i < count($dispatchInfoName); $i++) {
									$currentId    = isset($oldDispatchInfo[$i]['dispatchInfoId']) ? $oldDispatchInfo[$i]['dispatchInfoId'] : null;
									$currentValue = isset($oldDispatchInfo[$i]['dispatchValue']) ? $oldDispatchInfo[$i]['dispatchValue'] : 'N/A';

									$currentName = 'N/A';
									if ($currentId) {
										$row = $this->db->select('title')->where('id', $currentId)->get('dispatchInfo')->row_array();
										$currentName = $row ? $row['title'] : 'N/A';
									}

									$newId   = isset($dispatchInfoName[$i]) ? $dispatchInfoName[$i] : null;
									$newName = 'N/A';
									if ($newId) {
										$row = $this->db->select('title')->where('id', $newId)->get('dispatchInfo')->row_array();
										$newName = $row ? $row['title'] : 'N/A';
									}

									if ($currentName != $newName) {
										$changeField[] = ['Dispatch Info name', 'dispatchInfoName', $currentName, $newName];
									}
									if ($currentValue != $dispatchInfoValue[$i]) {
										$changeField[] = ['Dispatch Info value', 'dispatchInfoValue', $currentValue, $dispatchInfoValue[$i]];
									}
								}
							}


							if (is_array($expenseName)) {
								for ($i = 0; $i < count($expenseName); $i++) {
									$currentExpenseId    = isset($oldCustomerExpenses[$i]['expenseInfoId']) ? $oldCustomerExpenses[$i]['expenseInfoId'] : null;
									$currentExpenseValue = isset($oldCustomerExpenses[$i]['expenseInfoValue']) ? $oldCustomerExpenses[$i]['expenseInfoValue'] : 'N/A';

									$currentExpenseName = 'N/A';
									if ($currentExpenseId) {
										$row = $this->db->select('title')->where('id', $currentExpenseId)->get('expenses')->row_array();
										$currentExpenseName = $row ? $row['title'] : 'N/A';
									}

									$newExpenseId   = isset($expenseName[$i]) ? $expenseName[$i] : null;
									$newExpenseName = 'N/A';
									if ($newExpenseId) {
										$row = $this->db->select('title')->where('id', $newExpenseId)->get('expenses')->row_array();
										$newExpenseName = $row ? $row['title'] : 'N/A';
									}

									if ($currentExpenseName != $newExpenseName) {
										$changeField[] = ['Cutomer Expense name', 'expenseName', $currentExpenseName, $newExpenseName];
									}
									if ($currentExpenseValue != $expensePrice[$i]) {
										$changeField[] = ['Customer Expense price', 'expensePrice', $currentExpenseValue, $expensePrice[$i]];
									}
								}
							}

							if (is_array($customExpenseTitles)) {
								for ($i = 0; $i < count($customExpenseTitles); $i++) {
									$currentExpenseName  = isset($oldCustomExpenses[$i]['title']) ? $oldCustomExpenses[$i]['title'] : 'N/A';
									$currentExpenseValue = isset($oldCustomExpenses[$i]['value']) ? $oldCustomExpenses[$i]['value'] : 'N/A';

									if ($currentExpenseName != $customExpenseTitles[$i]) {
										$changeField[] = ['Custom Expense name', 'expenseName', $currentExpenseName, $customExpenseTitles[$i]];
									}
									if ($currentExpenseValue != $customExpensePrices[$i]) {
										$changeField[] = ['Custom Expense price', 'expensePrice', $currentExpenseValue, $customExpensePrices[$i]];
									}
								}
							}

							// if (is_array($carrierExpenseName)) {
							// 	for ($i = 0; $i < count($carrierExpenseName); $i++) {
							// 		$currentExpenseId    = isset($oldSPExpenses[$i]['expenseInfoId']) ? $oldSPExpenses[$i]['expenseInfoId'] : null;
							// 		$currentExpenseValue = isset($oldSPExpenses[$i]['expenseInfoValue']) ? $oldSPExpenses[$i]['expenseInfoValue'] : 'N/A';

							// 		$currentExpenseName = 'N/A';
							// 		if ($currentExpenseId) {
							// 			$row = $this->db->select('title')->where('id', $currentExpenseId)->get('expenses')->row_array();
							// 			$currentExpenseName = $row ? $row['title'] : 'N/A';
							// 		}

							// 		$newExpenseId   = isset($expenseName[$i]) ? $expenseName[$i] : null;
							// 		$newExpenseName = 'N/A';
							// 		if ($newExpenseId) {
							// 			$row = $this->db->select('title')->where('id', $newExpenseId)->get('expenses')->row_array();
							// 			$newExpenseName = $row ? $row['title'] : 'N/A';
							// 		}

							// 		if ($currentExpenseName != $newExpenseName) {
							// 			$changeField[] = ['Service Provider Expense name', 'expenseName', $currentExpenseName, $newExpenseName];
							// 		}
							// 		if ($currentExpenseValue != $expensePrice[$i]) {
							// 			$changeField[] = ['Service Provider Expense price', 'expensePrice', $currentExpenseValue, $expensePrice[$i]];
							// 		}
							// 	}
							// }
						}
					}
					
				    
				    $userid = $this->session->userdata('logged');
				    if($changeField) {
				        $changeFieldJson = json_encode($changeField);
				        $dispatchLog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
				        $this->Comancontroler_model->add_data_in_table($dispatchLog,'warehouse_dispatch_log'); 
				    }
				    
					$this->session->set_flashdata('item', '	PA Warehousing updated successfully.');
                    redirect(base_url('admin/paWarehouse/update/'.$id));
				}
 			   
			}
	    }
		//
		$data['extraDispatch'] = $this->Comancontroler_model->getExtraWarehouseDispatchInfo($id); 
		$data['userinfo'] = $this->Comancontroler_model->get_data_by_column('id',$disInfo[0]['userid'],'admin_login','uname');
		$data['dispatch'] = $this->Comancontroler_model->get_data_by_id($id,'warehouse_dispatch');
		$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
		$data['companies'] = $this->Comancontroler_model->get_data_by_table('companies');
	    $data['companyAddress'] = $this->Comancontroler_model->get_data_by_table('companyAddress');
		$data['drayageEquipments'] = $this->Comancontroler_model->get_data_by_table('drayageEquipments');
	    $data['erInformation'] = $this->Comancontroler_model->get_data_by_table('erInformation');
		$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');

		$whereDispInfo = array('did'=>$id,'dispatchType'=>'warehouse');
		$where = array('did'=>$id,'dispatchType'=>'warehouse', 'expenseType'=>'customer');
		$whereServiceProvider = array('did'=>$id,'dispatchType'=>'warehouse', 'expenseType'=>'serviceProvider');
		$data['dispatchInfoDetails'] = $this->Comancontroler_model->get_data_by_multiple_column($whereDispInfo,'dispatch_info_details','*','','','');
		$data['dispatchExpenseDetails'] = $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_expense_details','*','','','');
		$data['dispatchSPExpenseDetails'] = $this->Comancontroler_model->get_data_by_multiple_column($whereServiceProvider,'dispatch_expense_details','*','','','');
		$data['dispatchCustomExpenses'] = $this->Comancontroler_model->get_data_by_multiple_column($where,'dispatch_custom_expense_details','*','','','');
		// print_r($data['dispatchInfo']);exit;
		$data['documents'] = array();

		
		$documents = $this->Comancontroler_model->get_document_by_dispach($id,'warehouse_documents');
		if($documents){
	        foreach($documents as $doc){
	            $doc['parent'] = 'no'; 
	            $data['documents'][] = $doc;
	        }
	    }
	    // if($data['dispatch'][0]['parentInvoice'] != ''){
	    //     $parentID = $this->Comancontroler_model->get_data_by_column('invoice',$data['dispatch'][0]['parentInvoice'],'warehouse_dispatch','id');
	    //     if($parentID){
	    //         if($parentID[0]['id'] > 0) {
	    //             $documentsParent = $this->Comancontroler_model->get_document_by_dispach($parentID[0]['id'],'warehouse_documents');
	    //             if($documentsParent){
        //     	        foreach($documentsParent as $doc){
        //     	            $doc['parent'] = 'yes'; 
        //     	            $data['documents'][] = $doc;
        //     	        }
        //     	    }
	    //         }
	    //     }
	    // }

		$parentInfo = $this->Comancontroler_model->get_data_by_column('child_id', $data['dispatch'][0]['id'], 'sub_invoices','parent_id, parent_type');
		if (!empty($parentInfo)) {
			$parentID   = $parentInfo[0]['parent_id'];
			$parentType = $parentInfo[0]['parent_type'];

			if ($parentID > 0) {
				if ($parentType == 'fleet') {
					$documentsParent = $this->Comancontroler_model->get_document_by_dispach($parentID, 'documents');
				} elseif ($parentType == 'logistics') {
					$documentsParent = $this->Comancontroler_model->get_document_by_dispach($parentID, 'documentsOutside');
				} elseif ($parentType == 'warehousing') {
					$documentsParent = $this->Comancontroler_model->get_document_by_dispach($parentID, 'warehouse_documents');
				} else {
					$documentsParent = [];
				}

				if (!empty($documentsParent)) {
					foreach ($documentsParent as $doc) {
						$doc['otherParent'] = 'yes';
						$doc['parentType'] = $parentType;
						$data['otherDocuments'][] = $doc;
					}
				}

			}
		}

		
		$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities'); 
		$data['truckingCompanies'] = $this->Comancontroler_model->get_data_by_table('truckingCompanies','*','','desc','Active');
		$data['booked_under'] = $this->Comancontroler_model->get_data_by_table('booked_under');
		$data['dispatchLog'] = $this->Comancontroler_model->get_dispachLog('warehouse_dispatch_log',$id);
		$data['factoringCompanies'] = $this->Comancontroler_model->get_data_by_table('factoringCompanies');
		 $data['warehouseServices'] = $this->Comancontroler_model->get_data_by_column('status','Active','warehouseServices','id,title','title','asc');

       
	    $dispatchMeta = json_decode($data['dispatch'][0]['dispatchMeta'],true);
		if(empty($data['invoiced'])){ $data['invoiced']=0; }
		if(empty($data['invoicePaid'])){ $data['invoicePaid']=0; }
		if(empty($data['invoiceClose'])){ $data['invoiceClose']=0; }
		if(empty($data['invoiceReady'])){ $data['invoiceReady']=0; }

		//echo "<pre>"; print_r($dispatchMeta); die(); 
		$childMappings = $this->Comancontroler_model->get_data_by_column(
			'parent_id',
			$id,
			'sub_invoices',
			'child_id,child_type'
		);

		$data['children'] = [];
		if (!empty($childMappings)) {
			foreach ($childMappings as $map) {
				$table = '';
				if ($map['child_type'] == 'warehousing') {
					$table = 'warehouse_dispatch';
				} elseif ($map['child_type'] == 'fleet') {
					$table = 'dispatch';
				} elseif ($map['child_type'] == 'logistics') {
					$table = 'dispatchOutside';
				}

				if ($table != '') {
					$child = $this->Comancontroler_model->get_data_by_column(
						'id',
						$map['child_id'],
						$table,
						'id,invoice,rate,parate,trailer'
					);
					if ($child) {
						$child[0]['child_type'] = $map['child_type'];
						$data['children'][] = $child[0];
					}
				}
			}
		}
	    
	    $data['childTrailer'] = $this->Comancontroler_model->get_data_by_column('parentInvoice',$data['dispatch'][0]['invoice'],'warehouse_dispatch','id,invoice,rate,parate,trailer');
        
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('warehouseDispatch/warehouse_dispatch_update', $data);
    	$this->load->view('admin/layout/footer');
	}

	private function updateSubInvoices($child, $insert_data, $invoice) {
		if ($child['child_type'] == 'warehousing') {
			$updateArr = [
				'parentInvoice'       => $invoice,
				'invoiceReadyDate'    => $insert_data['invoiceReadyDate'],
				'invoicePaidDate'     => $insert_data['invoicePaidDate'],
				'invoiceCloseDate'    => $insert_data['invoiceCloseDate'],
				'invoiceReady'        => $insert_data['invoiceReady'],
				'invoicePaid'         => $insert_data['invoicePaid'],
				'invoiceClose'        => $insert_data['invoiceClose'],
				'invoiced'            => $insert_data['invoiced'],
				'custInvDate'         => $insert_data['custInvDate'],
				'carrierInvoiceCheck' => $insert_data['carrierInvoiceCheck'],
				'custDueDate'         => $insert_data['custDueDate'],
				'carrierPayoutCheck'  => $insert_data['carrierPayoutCheck'],
				'carrierPayoutDate'   => $insert_data['carrierPayoutDate'],
				'invoiceNotes'        => $insert_data['invoiceNotes'],
				'invoiceType'         => $insert_data['invoiceType'],
				'inwrd'       		  => $insert_data['inwrd'],
				'outwrd'       		  => $insert_data['outwrd'],
				'gd'                  => $insert_data['gd'],
				'delivered'           => $insert_data['delivered'],
				'shipping_contact'    => $insert_data['shipping_contact'],
				'driver_status'       => $insert_data['driver_status'],
			];

			if (array_key_exists("invoiceDate", $insert_data) && trim($insert_data['invoiceDate']) != '') {
				$updateArr['invoiceDate']   = $insert_data['invoiceDate'];
				$updateArr['expectPayDate'] = $insert_data['expectPayDate'];
			}

			$fullStatus = $insert_data['status'];
			$firstPart  = explode(' - Linked to', $fullStatus)[0]; 
			$updateArr['status'] = $firstPart . ' - Linked to ' . $insert_data['invoice'];

			$this->Comancontroler_model->update_table_by_id($child['child_id'], 'warehouse_dispatch', $updateArr);
		}
		elseif ($child['child_type'] == 'fleet') {
			$getSubDispatch = $this->Comancontroler_model->get_data_by_column(
				'id',
				$child['child_id'],
				'dispatch',
				'id,dispatchMeta'
			);

			if (empty($getSubDispatch)) {
				return; // nothing to update
			}

			// Decode or initialize dispatchMeta
			if ($getSubDispatch[0]['dispatchMeta'] == '') {
				$currentDiMeta = [
					'expense'          => [],
					'invoiced'         => '0',
					'invoicePaid'      => '0',
					'invoiceClose'     => '0',
					'invoiceReady'     => '0',
					'invoiceReadyDate' => '',
					'invoicePaidDate'  => '',
					'invoiceCloseDate' => ''
				];
			} else {
				$currentDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'], true);
			}

			// Update dispatchMeta fields
			$currentDiMeta['invoiceReadyDate']    = $insert_data['invoiceReadyDate'];
			$currentDiMeta['invoicePaidDate']     = $insert_data['invoicePaidDate'];
			$currentDiMeta['invoiceCloseDate']    = $insert_data['invoiceCloseDate'];
			$currentDiMeta['invoiceReady']        = $insert_data['invoiceReady'];
			$currentDiMeta['invoicePaid']         = $insert_data['invoicePaid'];
			$currentDiMeta['invoiceClose']        = $insert_data['invoiceClose'];
			$currentDiMeta['invoiced']            = $insert_data['invoiced'];
			$currentDiMeta['custInvDate']         = $insert_data['custInvDate'];
			$currentDiMeta['carrierInvoiceCheck'] = $insert_data['carrierInvoiceCheck'];
			$currentDiMeta['custDueDate']         = $insert_data['custDueDate'];

			// Prepare update array
			$subInvArr = [
				'invoiceNotes'       => $insert_data['invoiceNotes'],
				'invoiceType'        => $insert_data['invoiceType'],
				'otherParentInvoice' => $invoice,
				'gd'                 => $insert_data['gd'],
				'delivered'          => $insert_data['delivered'],
				'shipping_contact'   => $insert_data['shipping_contact'],
				'driver_status'      => $insert_data['driver_status'],
				'dispatchMeta'       => json_encode($currentDiMeta)
			];

			// Only add invoiceDate if provided
			if (array_key_exists("invoiceDate", $insert_data) && trim($insert_data['invoiceDate']) != '') {
				$subInvArr['invoiceDate']   = $insert_data['invoiceDate'];
				$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
			}

			// Update status string
			$fullStatus         = $insert_data['status'];
			$firstPart          = explode(' - Linked to', $fullStatus)[0]; 
			$subInvArr['status'] = $firstPart . ' - Linked to ' . $insert_data['invoice'];

			// Run update
			if ($getSubDispatch[0]['id'] > 0) {
				$this->Comancontroler_model->update_table_by_id(
					$getSubDispatch[0]['id'],
					'dispatch',
					$subInvArr
				);
			}
		}
		elseif ($child['child_type'] == 'logistics') {
			$getSubDispatch = $this->Comancontroler_model->get_data_by_column(
				'id',
				$child['child_id'],
				'dispatchOutside',
				'id,dispatchMeta,gd'
			);

			if (empty($getSubDispatch)) {
				return; // nothing to update
			}

			// Decode or initialize dispatchMeta
			if ($getSubDispatch[0]['dispatchMeta'] == '') {
				$currentDiMeta = [
					'expense'          => [],
					'invoiced'         => '0',
					'invoicePaid'      => '0',
					'invoiceClose'     => '0',
					'invoiceReady'     => '0',
					'invoiceReadyDate' => '',
					'invoicePaidDate'  => '',
					'invoiceCloseDate' => ''
				];
			} else {
				$currentDiMeta = json_decode($getSubDispatch[0]['dispatchMeta'], true);
			}

			// Update dispatchMeta fields
			$currentDiMeta['invoiceReadyDate']    = $insert_data['invoiceReadyDate'];
			$currentDiMeta['invoicePaidDate']     = $insert_data['invoicePaidDate'];
			$currentDiMeta['invoiceCloseDate']    = $insert_data['invoiceCloseDate'];
			$currentDiMeta['invoiceReady']        = $insert_data['invoiceReady'];
			$currentDiMeta['invoicePaid']         = $insert_data['invoicePaid'];
			$currentDiMeta['invoiceClose']        = $insert_data['invoiceClose'];
			$currentDiMeta['invoiced']            = $insert_data['invoiced'];
			$currentDiMeta['custInvDate']         = $insert_data['custInvDate'];
			$currentDiMeta['carrierInvoiceCheck'] = $insert_data['carrierInvoiceCheck'];
			$currentDiMeta['custDueDate']         = $insert_data['custDueDate'];

			// Prepare update array
			$subInvArr = [
				'gd'                 => $insert_data['gd'],
				'delivered'          => $insert_data['delivered'],
				'shipping_contact'   => $insert_data['shipping_contact'],
				'driver_status'      => $insert_data['driver_status'],
				'invoiceNotes'       => $insert_data['invoiceNotes'],
				'invoiceType'        => $insert_data['invoiceType'],
				'otherParentInvoice' => $invoice,
				'dispatchMeta'       => json_encode($currentDiMeta)
			];

			// Only add invoiceDate if provided
			if (array_key_exists("invoiceDate", $insert_data) && trim($insert_data['invoiceDate']) != '') {
				$subInvArr['invoiceDate']   = $insert_data['invoiceDate'];
				$subInvArr['expectPayDate'] = $insert_data['expectPayDate'];
			}

			// Update status string
			$fullStatus          = $insert_data['status'];
			$firstPart           = explode(' - Linked to', $fullStatus)[0]; 
			$subInvArr['status'] = $firstPart . ' - Linked to ' . $insert_data['invoice'];

			// Run update
			if ($getSubDispatch[0]['id'] > 0) {
				$this->Comancontroler_model->update_table_by_id(
					$getSubDispatch[0]['id'],
					'dispatchOutside',
					$subInvArr
				);
			}
		}

	}

	public function paWarehouseAdd() {
		if(!checkPermission($this->session->userdata('permission'),'odispatch')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $userid = $this->session->userdata('adminid');
	    
        $data['truckingArr'] = $this->truckingArr;
		$data['truckingEquipments'] = $this->Comancontroler_model->get_data_by_table('truckingEquipments');

        $data['dispatchInfo'] = $this->Comancontroler_model->get_data_by_column('status','Active','dispatchInfo','id,title','title','asc');
        //$data['expenses'] = $this->expenses;
        $data['expenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','id,title,type','title','asc');
		$data['carrierExpenses'] = $this->Comancontroler_model->get_data_by_column('status','Active','expenses','id,title,type','title','asc');
	    $data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');

		if($this->input->post('save'))	{ 
			$this->form_validation->set_rules('pudate', 'Start date','required|min_length[9]');
			$this->form_validation->set_rules('pcity', 'city','required|min_length[1]');
			// $this->form_validation->set_rules('dcity', 'drop off city','required|min_length[1]');
			$this->form_validation->set_rules('company', 'company','required|min_length[1]'); 
			// $this->form_validation->set_rules('dlocation', 'drop off location','required|min_length[1]'); 
			$this->form_validation->set_rules('plocation', 'location','required|min_length[1]');  
			
			// $pudate1 = $this->input->post('pudate1');
			// if(!is_array($pudate1)){ $pudate1 = array(); }
			// $dodate1 = $this->input->post('dodate1'); 
			// if(!is_array($dodate1)){ $dodate1 = array(); }
			
			$pudate = $this->input->post('pudate');
			$driver = $this->input->post('driver');
				// echo $driver;exit;
			$inv_first = '';
			$driver_trip = $this->Comancontroler_model->check_dirver_dispatch_by_date($driver,$pudate,'warehouse_dispatch');
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
			// $check_dcity = $this->input->post('dcity');
			$check_pcity = $this->input->post('pcity');
			$check_plocation = $this->input->post('plocation');
			// $check_dlocation = $this->input->post('dlocation');
			$check_paddress = $this->input->post('paddress');
			// $check_daddress = $this->input->post('daddress');
				
			if($this->isAddressExist($pudate,$check_pcity,$check_plocation,$check_paddress)){
				$addr = $check_plocation.' '.$check_paddress.' '.$check_pcity;
				$this->form_validation->set_rules('pickupaddressss', 'Address','required');
				$this->form_validation->set_message('required','Address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
			}
			
			// if($this->isAddressExist($pudate,$check_dcity,$check_dlocation,$check_daddress)){
			// 	$addr = $check_dlocation.' '.$check_daddress.' '.$check_dcity;
			// 	$this->form_validation->set_rules('dropoffaddressss', 'drop off address','required');
			// 	$this->form_validation->set_message('required','Drop off address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
			// }
				
			// if(count($pudate1) > 0) {
			// 	$check_pcity1 = $this->input->post('pcity1');
			// 	$check_plocation1 = $this->input->post('plocation1');
			// 	$check_paddress1 = $this->input->post('paddress1');
			// 	$check_pd_type1 = $this->input->post('pd_type1');
			// 	for($i=0;$i<count($pudate1);$i++){
			// 		if($pudate1[$i]!='' && $check_pd_type1[$i]=='pickup') {
			// 			if($this->isAddressExist($pudate,$check_pcity1[$i],$check_plocation1[$i],$check_paddress1[$i])){
			// 				$addr = $check_plocation1[$i].' '.$check_paddress1[$i].' '.$check_pcity1[$i];
			// 				$this->form_validation->set_rules('dropoffaddressss'.$i, 'drop off address','required');
			// 				$this->form_validation->set_message('required','Pickup address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
			// 			}
			// 		}
			// 	}
			// }
			// if(count($dodate1) > 0) {
			// 	$check_dcity1 = $this->input->post('dcity1');
			// 	$check_dlocation1 = $this->input->post('dlocation1');
			// 	$check_daddress1 = $this->input->post('daddress1');
			// 	$check_pd_type2 = $this->input->post('pd_type2');
			// 	for($i=0;$i<count($dodate1);$i++){
			// 		if($dodate1[$i]!='' && $check_pd_type2[$i]=='dropoff') {
			// 			if($this->isAddressExist($pudate,$check_dcity1[$i],$check_dlocation1[$i],$check_daddress1[$i])){
			// 				$addr = $check_dlocation1[$i].' '.$check_daddress1[$i].' '.$check_dcity1[$i];
			// 				$this->form_validation->set_rules('dropoffaddresss'.$i, 'drop off address','required');
			// 				$this->form_validation->set_message('required','Drop off address ('.$addr.') is not exist. <a href="/admin/company-address">Click here to add new address</a>.');
			// 			}
			// 		}
			// 	}
			// }
			
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
				
				// $check_dcity = $this->input->post('dcity');
				// $dcity = $this->check_city($check_dcity);
				
				$check_pcity = $this->input->post('pcity');
				$pcity = $this->check_city($check_pcity);
				
				$check_plocation = $this->input->post('plocation');
				$plocation = $this->check_location($check_plocation);
				
				// $check_dlocation = $this->input->post('dlocation');
				// $dlocation = $this->check_location($check_dlocation);
				
				// $dcode = $this->input->post('dcode');
				// $dcodeVal = implode('~-~',$dcode);
				$pcode = $this->input->post('pcode');
				if (!is_array($pcode)) {
					$pcode = array($pcode);
				}

				$pcodeVal = implode('~-~', $pcode);				
				
				$week = date('M',strtotime($pudate)).' W';
				$day = date('d',strtotime($pudate));
				if($day < 9) { $w = '1'; }
				elseif($day < 16){ $w = '2'; }
				elseif($day < 24){ $w = '3'; }
				else { $w = '4'; }
				$week .= $w; 
				
				$parate = $this->input->post('parate');
				if(!is_numeric($parate)) { $parate = 0; }
				
				$invoiceType = ''; 
				$payoutAmount = 0;
				$pamargin = (float) $this->input->post('parate') - (float) $this->input->post('rate');
				
				$insert_data=array(
				    'driver'=>$driver,
				    'userid'=>$userid,
				    'pudate'=>$pudate,
					'edate'=>$this->input->post('edate'),
				    'bookedUnderNew'=>$this->input->post('bookedUnderNew'),
				    'truckingCompany'=>$this->input->post('truckingCompany'),
					'warehouseServices'=>$this->input->post('warehouseServices'),
				    'trip'=>$this->input->post('trip'),
				    'delivered'=>$this->input->post('delivered'),
					'pcity'=>$pcity,
				    // 'dcity'=>$dcity,
				    // 'dodate'=>$this->input->post('dodate'),
				    'rate'=>$this->input->post('rate'),
					'customer_rate'=>$this->input->post('customer_rate'),
				    'parate'=>$this->input->post('parate'),
				    //'rateLumper'=>$this->input->post('rateLumper'),
				    'company'=>$company,
				    // 'dlocation'=>$dlocation,
				    'plocation'=>$plocation,
				    // 'dcode'=>$dcodeVal,
				    'pcode'=>$pcodeVal,
				    'paddress'=>$this->input->post('paddress'),
				    // 'daddress'=>$this->input->post('daddress'),
				    'paddressid'=>$this->input->post('paddressid'),
				    // 'daddressid'=>$this->input->post('daddressid'),
				    'trailer'=>$this->input->post('trailer'),
				    'tracking'=>$this->input->post('tracking'),
				    'invoice'=>$invoice,
				    'payoutAmount'=>$payoutAmount,
				    'invoiceType'=>$invoiceType,
				    'dWeek'=>$week,
				    'inwrd'=>$this->input->post('inwrd'),
					'outwrd'=>$this->input->post('outwrd'),
				    'gd'=>$this->input->post('gd'),
				    // 'ptime'=>$this->input->post('ptime'),
				    // 'dtime'=>$this->input->post('dtime'),
				    'notes'=>$this->input->post('notes'),
					'invoiceNotes'=>$this->input->post('invoiceNotes'),
				    'pnotes'=>$this->input->post('pnotes'),
				    // 'dnotes'=>$this->input->post('dnotes'),
				    'pamargin'=>$pamargin,
				    'carrierPayoutDate'=>$this->input->post('carrierPayoutDate'),
				    'carrierPayoutCheck'=>$this->input->post('carrierPayoutCheck'),
				    'driver_status'=>$this->input->post('driver_status'),
				    'status'=>$this->input->post('status'),
					'carrierInvoiceCheck' => $this->input->post('carrierInvoiceCheck'),
					'pickup' => $this->input->post('pickup'),
					'pPort' => $this->input->post('pPort'),
					'pPortAddress' => $this->input->post('pPortAddress'),
					// 'dropoff' => $this->input->post('dropoff'),
					// 'dPort' => $this->input->post('dPort'),
					// 'dPortAddress' => $this->input->post('dPortAddress'),
					'invoicePDF' => $this->input->post('invoicePDF'),
					'drayageType' => $this->input->post('drayageType'),
					'invoiceDrayage' => $this->input->post('invoiceDrayage'),
					'invoiceTrucking' => $this->input->post('invoiceTrucking'),
					// 'appointmentTypeP' => $this->input->post('appointmentTypeP'),
					'quantityP' => $this->input->post('quantityP'),
					'commodityP' => $this->input->post('commodityP'),
					'metaDescriptionP' => $this->input->post('metaDescriptionP'),
					'weightP' => $this->input->post('weightP'),
					// 'appointmentTypeD' => $this->input->post('appointmentTypeD'),
					// 'quantityD' => $this->input->post('quantityD'),
					// 'metaDescriptionD' => $this->input->post('metaDescriptionD'),
					// 'weightD' => $this->input->post('weightD'),
					'erInformation' => $this->input->post('erInformation'),
					'driver_name' => $this->input->post('driver_name'),
					'driver_contact' => $this->input->post('driver_contact'),
				    'rdate'=>date('Y-m-d H:i:s')
				);
			
				$res = $this->Comancontroler_model->add_data_in_table($insert_data,'warehouse_dispatch'); 
				if($res){
					$expenseName = $this->input->post('expenseName');
					$expensePrice = $this->input->post('expensePrice');
					if (is_array($expenseName) && is_array($expensePrice)) {
						foreach ($expenseName as $key => $name) {
							if (!empty($name)) {
								$this->db->insert('dispatch_expense_details', [
									'did' => $res,
									'expenseInfoId' => $name,
									'expenseInfoValue' => $expensePrice[$key],
									'dispatchType' => 'warehouse'
								]);
							}
						}
					}

					$carrierExpenseName = $this->input->post('carrierExpenseName');
					$carrierExpensePrice = $this->input->post('carrierExpensePrice');
					if (is_array($carrierExpenseName) && is_array($carrierExpensePrice)) {
						foreach ($carrierExpenseName as $key => $name) {
							if (!empty($name)) {
								$this->db->insert('dispatch_expense_details', [
									'did' => $res,
									'expenseInfoId' => $name,
									'expenseInfoValue' => $carrierExpensePrice[$key],
									'dispatchType' => 'warehouse',
									'expenseType' => 'serviceProvider'
								]);
							}
						}
					}

					$customExpenseTitles = $this->input->post('customExpenseName');
					$customExpensePrices = $this->input->post('customExpensePrice');
					if (!empty($customExpenseTitles) && is_array($customExpenseTitles)) {
						foreach ($customExpenseTitles as $key => $title) {
							$price = isset($customExpensePrices[$key]) ? $customExpensePrices[$key] : 0;
							$this->db->insert('dispatch_custom_expense_details', [
								'did' => $res,
								'title' => trim($title),
								'value' => (float) $price,
								'dispatchType' => 'warehouse'
							]);
						}
					}

					$dispatchInfoName = $this->input->post('dispatchInfoName');
					$dispatchInfoValue = $this->input->post('dispatchInfoValue');
					if (is_array($dispatchInfoName) && is_array($dispatchInfoValue)) {
						foreach ($dispatchInfoName as $key => $infoName) {
							if (!empty($dispatchInfoValue[$key])) {
								$this->db->insert('dispatch_info_details', [
									'did' => $res,
									'dispatchInfoId' => $infoName,
									'dispatchValue' => $dispatchInfoValue[$key],
									'dispatchType' => 'warehouse'
								]);
							}
						}
					}

				    
				    /*********** upload documents *********/
				
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
				
					$inwrdFilesCount = count($_FILES['inwrd_d']['name']);
					if($inwrdFilesCount > 0) {  
						$inwrdFiles = $_FILES['inwrd_d'];
						$config['upload_path'] = 'assets/warehouse/inwrd/';
						$config['file_name'] = $fileName1.'-INWRD-'.$fileName2; //$_FILES['bol_d']['name'];  
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
						//echo '<pre>';print_r($bolFiles);
						for($i = 0; $i < $inwrdFilesCount; $i++){
							$_FILES['inwrd_d']['name']     = $inwrdFiles['name'][$i];
							$_FILES['inwrd_d']['type']     = $inwrdFiles['type'][$i];
							$_FILES['inwrd_d']['tmp_name'] = $inwrdFiles['tmp_name'][$i];
							$_FILES['inwrd_d']['error']     = $inwrdFiles['error'][$i];
							$_FILES['inwrd_d']['size']     = $inwrdFiles['size'][$i]; 
					
							if ($this->upload->do_upload('inwrd_d'))  { 
								$dataInwrd = $this->upload->data(); 
								$inwrd = $dataInwrd['file_name'];
								$addinwrdfile = array('did'=>$res,'type'=>'inwrd','fileurl'=>$inwrd,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($addinwrdfile,'warehouse_documents');
							}
						}
					}

					$outwrdFilesCount = count($_FILES['outwrd_d']['name']);
					if($outwrdFilesCount > 0) {  
						$outwrdFiles = $_FILES['outwrd_d'];
						$config['upload_path'] = 'assets/warehouse/outwrd/';
						$config['file_name'] = $fileName1.'-OUTWRD-'.$fileName2; //$_FILES['bol_d']['name'];  
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
						//echo '<pre>';print_r($bolFiles);
						for($i = 0; $i < $outwrdFilesCount; $i++){
							$_FILES['outwrd_d']['name']     = $outwrdFiles['name'][$i];
							$_FILES['outwrd_d']['type']     = $outwrdFiles['type'][$i];
							$_FILES['outwrd_d']['tmp_name'] = $outwrdFiles['tmp_name'][$i];
							$_FILES['outwrd_d']['error']     = $outwrdFiles['error'][$i];
							$_FILES['outwrd_d']['size']     = $outwrdFiles['size'][$i]; 
					
							if ($this->upload->do_upload('outwrd_d'))  { 
								$dataOutwrd = $this->upload->data(); 
								$outwrd = $dataOutwrd['file_name'];
								$addOutwrdfile = array('did'=>$res,'type'=>'outwrd','fileurl'=>$outwrd,'rdate'=>date('Y-m-d H:i:s'));
								$this->Comancontroler_model->add_data_in_table($addOutwrdfile,'warehouse_documents');
							}
						}
					}
										
					if(!empty($_FILES['gd_d']['name'])){
						$config['upload_path'] = 'assets/warehouse/gd/';
                        $config['file_name'] = $fileName1.'-GD-'.$fileName2; //$_FILES['gd_d']['name']; 
                        $this->load->library('upload',$config);
                        $this->upload->initialize($config); 
                        if($this->upload->do_upload('gd_d')){
                            $uploadData = $this->upload->data();
                            $bol = $uploadData['file_name'];
							$addfile = array('did'=>$res,'type'=>'gd','fileurl'=>$bol,'rdate'=>date('Y-m-d H:i:s'));
							$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
                        }
                    }
                    
                    $ciFilesCount = count($_FILES['carrierInvoice']['name']);
					if($ciFilesCount > 0) {  
						$ciFiles = $_FILES['carrierInvoice'];
						$config['upload_path'] = 'assets/warehouse/carrierInvoice/';
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
								$this->Comancontroler_model->add_data_in_table($addfile,'warehouse_documents');
							}
						}
					}
					
                
					$this->session->set_flashdata('item', '	PA Warehousing add successfully.');
                    //redirect(base_url('admin/outside-dispatch/add'));
                    redirect(base_url('admin/paWarehouse/update/'.$res.'#submit'));
				}
 			   
			}
	    }
		$id = $this->uri->segment(4);
        if($id > 0){
          $data['duplicate'] = $this->Comancontroler_model->get_data_by_id($id,'warehouse_dispatch');
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
		 $data['warehouseServices'] = $this->Comancontroler_model->get_data_by_column('status','Active','warehouseServices','id,title','title','asc');
	    $data['erInformation'] = $this->Comancontroler_model->get_data_by_table('erInformation');
	    $data['booked_under'] = $this->Comancontroler_model->get_data_by_table('booked_under');
	  
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('warehouseDispatch/warehouse_dispatch_add',$data);
    	$this->load->view('admin/layout/footer');
	}
}