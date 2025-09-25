<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	class LogisticsDashboard extends CI_Controller {
		public	 function __construct()
		{
			
			parent::__construct();
			//$this->load->helper(array('form','url'));
			$this->load->library('form_validation');
			//$this->load->model('Login_model');
			$this->load->model('LogisticsDashboard_model');
			$this->load->model('Comancontroler_model');
			$this->load->helper(array('form','url'));
			$this->load->library('session');
			if( empty($this->session->userdata('logged') )) {
				redirect('adminlogin/');
			}
		}

        public function logisticsDashboard(){
            $data=[];
			$sdate = $edate = '';			 
			if($this->input->post('search')){
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            	if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); }	
			} 
			$data['shipmentStatusCounts'] = $this->LogisticsDashboard_model->shipmentStatusCounts($sdate,$edate);
			$data['bookingsCounts'] = $this->LogisticsDashboard_model->bookingsCounts($sdate,$edate);
			$data['receivableInvoicesCounts'] = $this->LogisticsDashboard_model->receivableInvoicesCounts($sdate,$edate);
			$data['payableInvoicesCounts'] = $this->LogisticsDashboard_model->payableInvoicesCounts($sdate,$edate);
			$data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');

			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/logisticsDashboard',$data);
			$this->load->view('admin/layout/footer');
        }
		public function getDetailsData()
		{
			$type = $this->input->post('type');
			$status = $this->input->post('status');
			$sdate = $this->input->post('sdate');
			$edate = $this->input->post('edate');
			$data = [];
			if ($type === "shipment" && $status === "delivery") {
				$data = $this->LogisticsDashboard_model->deliveryShipmentDetails($sdate, $edate);
				$today = date('Y-m-d');
				foreach ($data as &$row) {
					// echo $row['dispatchid'];exit;
					$dispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date, pd_city, CONCAT(city, ", ", state) as city, pd_location, pd_time, pd_addressid, pd_type', 'dispatchOutsideExtraInfo', 'delivery', 'ASC');
					$dispatchLog= $this->Comancontroler_model->get_dispachLog('dispatchOutsideLog',$row['dispatchid']);
					// print_r($dispatchLog[0]);exit;
					$allDatesPassed = true;
					if ($dispatchInfo) {
						foreach ($dispatchInfo as $dis) {
							$row['dispatchInfo'][] = [
								'pd_date' => $dis['pd_date'],
								'pd_city' => $dis['pd_city'],
								'pd_city_name' => $dis['city'],
								'pd_location' => $dis['pd_location'],
								'pd_time' => $dis['pd_time'],
								'pd_addressid' => $dis['pd_addressid'],
								'pd_type' => $dis['pd_type']
							];
							if (!empty($dis['pd_date']) && $dis['pd_date'] >= $today) {
								$allDatesPassed = false;
							}
						}
					} else {
						$row['dispatchInfo'][] = [
							'pd_date' => '',
							'pd_city' => '',
							'pd_city_name' => '',
							'pd_location' => '',
							'pd_time' => '',
							'pd_addressid' => '',
							'pd_type' => ''
						];
					}
					if (!empty($row['date']) && $row['date'] >= $today) {
						$allDatesPassed = false;
					}
					if ($allDatesPassed && strtolower($row['driver_status']) !== 'shipment Delivered') {
						$row['overdue_status'] = true;
					} else {
						$row['overdue_status'] = false;
					}
					if($dispatchLog[0]){
						$row['lastUpdateInfo'][] = [
							'uname' => $dispatchLog[0]['uname'],
							'rDate' => $dispatchLog[0]['rDate']
						];
					}
				}
			} elseif ($type === "shipment" && $status === "pickup") {
				$data = $this->LogisticsDashboard_model->pickupShipmentDetails($sdate,$edate);
				$today = date('Y-m-d');
				foreach ($data as &$row) {
					$dispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date, pd_city, CONCAT(city, ", ", state) as city, pd_location, pd_time, pd_addressid, pd_type', 'dispatchOutsideExtraInfo', 'pickup', 'ASC');
					$dispatchLog= $this->Comancontroler_model->get_dispachLog('dispatchOutsideLog',$row['dispatchid']);
					// print_r($dispatchLog);exit;
					$allDatesPassed = true;
					if ($dispatchInfo) {
						foreach ($dispatchInfo as $dis) {
							$row['dispatchInfo'][] = [
								'pd_date' => $dis['pd_date'],
								'pd_city' => $dis['pd_city'],
								'pd_city_name' => $dis['city'],
								'pd_location' => $dis['pd_location'],
								'pd_time' => $dis['pd_time'],
								'pd_addressid' => $dis['pd_addressid'],
								'pd_type' => $dis['pd_type']
							];
							if (!empty($dis['pd_date']) && $dis['pd_date'] >= $today) {
								$allDatesPassed = false;
							}
						}
					} else {
						$row['dispatchInfo'][] = [
							'pd_date' => '',
							'pd_city' => '',
							'pd_city_name' => '',
							'pd_location' => '',
							'pd_time' => '',
							'pd_addressid' => '',
							'pd_type' => ''
						];
					}
					if (!empty($row['date']) && $row['date'] >= $today) {
						$allDatesPassed = false;
					}
					if ($allDatesPassed && (strtolower($row['driver_status']) !== 'Pending' || strtolower($row['driver_status']) !== 'Shipment Scheduled')) {
						$row['overdue_status'] = true;
					} else {
						$row['overdue_status'] = false;
					}
					if($dispatchLog[0]){
						$row['lastUpdateInfo'][] = [
							'uname' => $dispatchLog[0]['uname'],
							'rDate' => $dispatchLog[0]['rDate']
						];
					}
				}
			} elseif ($type === "shipment" && $status === "pending") {
				$data = $this->LogisticsDashboard_model->pendingShipmentDetails($sdate,$edate);
				foreach ($data as &$row) {
					$dispatchInfo = $this->Comancontroler_model->get_dispatchinfo_by_id($row['id'], 'pd_date,pd_city,pd_location,pd_time,pd_addressid');
					if ($dispatchInfo) {
						foreach ($dispatchInfo as $dis) {
							$row['pd_date'] = $dis['pd_date'];
							$row['pd_city'] = $dis['pd_city'];
							// $row['pd_city_name'] = $dis['city'];
							$row['pd_location'] = $dis['pd_location'];
							$row['pd_time'] = $dis['pd_time'];
						}
					}
				}
			} elseif ($type === "shipment" && $status === "pendingInvoices") {
				$data = $this->LogisticsDashboard_model->pendingInvoicesDetails();
			} elseif ($type === "shipment" && $status === "pendingCarrierInvoices") {
				$data = $this->LogisticsDashboard_model->pendingCarrierInvoicesDetails();
			} elseif ($type === "bookings" && $status === "weeklyBookings") {
				$data = $this->LogisticsDashboard_model->weeklyBookingsDetails($sdate,$edate);
				foreach ($data as &$row) {
					// $dispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date, pd_city, CONCAT(city, ", ", state) as city, pd_location, pd_time, pd_addressid, pd_type', 'dispatchOutsideExtraInfo', 'pickup', 'ASC');
					$pdispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,companyAddress.company,pd_type', 'dispatchOutsideExtraInfo', 'pickup',  'ASC');
					
					$ddispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,companyAddress.company,pd_type', 'dispatchOutsideExtraInfo', 'dropoff',  'ASC');

					$dispatchLog= $this->Comancontroler_model->get_dispachLog('dispatchOutsideLog',$row['dispatchid']);
					// print_r($dispatchLog);exit;
					if ($pdispatchInfo) {
						foreach ($pdispatchInfo as $dis) {
							$row['pdispatchInfo'][] = [
								'pd_date' => $dis['pd_date'],
								'pd_city' => $dis['pd_city'],
								'pd_city_name' => $dis['city'],
								'pd_paddress'=>$dis['company'],
								'pd_location' => $dis['pd_location'],
								'pd_time' => $dis['pd_time'],
								'pd_addressid' => $dis['pd_addressid'],
								'pd_type' => $dis['pd_type']
							];
						}
					} else {
						$row['pdispatchInfo'][] = [
							'pd_date' => '',
							'pd_city' => '',
							'pd_city_name' => '',
							'pd_location' => '',
							'pd_time' => '',
							'pd_addressid' => '',
							'pd_type' => ''
						];
					}
					if ($ddispatchInfo) {
						foreach ($ddispatchInfo as $dis) {
							$row['ddispatchInfo'][] = [
								'pd_date' => $dis['pd_date'],
								'pd_city' => $dis['pd_city'],
								'pd_city_name' => $dis['city'],
								'pd_paddress'=>$dis['company'],
								'pd_location' => $dis['pd_location'],
								'pd_time' => $dis['pd_time'],
								'pd_addressid' => $dis['pd_addressid'],
								'pd_type' => $dis['pd_type']
							];
						}
					} else {
						$row['ddispatchInfo'][] = [
							'pd_date' => '',
							'pd_city' => '',
							'pd_city_name' => '',
							'pd_location' => '',
							'pd_time' => '',
							'pd_addressid' => '',
							'pd_type' => ''
						];
					}
				}
			} elseif ($type === "bookings" && $status === "unassigned") {
					// echo $row['dispatchid'];exit;
				$data = $this->LogisticsDashboard_model->unassignedBookingsDetails($sdate,$edate);
				foreach ($data as &$row) {
					$pdispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,companyAddress.company,pd_type', 'dispatchOutsideExtraInfo', 'pickup',  'ASC');
					
					$ddispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,companyAddress.company,pd_type', 'dispatchOutsideExtraInfo', 'dropoff',  'ASC');

					$dispatchLog= $this->Comancontroler_model->get_dispachLog('dispatchOutsideLog',$row['dispatchid']);
					// print_r($dispatchLog);exit;
					if ($pdispatchInfo) {
						foreach ($pdispatchInfo as $dis) {
							$row['pdispatchInfo'][] = [
								'pd_date' => $dis['pd_date'],
								'pd_city' => $dis['pd_city'],
								'pd_city_name' => $dis['city'],
								'pd_paddress'=>$dis['company'],
								'pd_location' => $dis['pd_location'],
								'pd_time' => $dis['pd_time'],
								'pd_addressid' => $dis['pd_addressid'],
								'pd_type' => $dis['pd_type']
							];
						}
					} else {
						$row['pdispatchInfo'][] = [
							'pd_date' => '',
							'pd_city' => '',
							'pd_city_name' => '',
							'pd_location' => '',
							'pd_time' => '',
							'pd_addressid' => '',
							'pd_type' => ''
						];
					}
					if ($ddispatchInfo) {
						foreach ($ddispatchInfo as $dis) {
							$row['ddispatchInfo'][] = [
								'pd_date' => $dis['pd_date'],
								'pd_city' => $dis['pd_city'],
								'pd_city_name' => $dis['city'],
								'pd_paddress'=>$dis['company'],
								'pd_location' => $dis['pd_location'],
								'pd_time' => $dis['pd_time'],
								'pd_addressid' => $dis['pd_addressid'],
								'pd_type' => $dis['pd_type']
							];
						}
					} else {
						$row['ddispatchInfo'][] = [
							'pd_date' => '',
							'pd_city' => '',
							'pd_city_name' => '',
							'pd_location' => '',
							'pd_time' => '',
							'pd_addressid' => '',
							'pd_type' => ''
						];
					}
				}
			} elseif ($type === "receivableInvoices" && $status === "received") {
				$data = $this->LogisticsDashboard_model->receivedInvoicesDetails($sdate,$edate);
			} elseif ($type === "receivableInvoices" && $status === "receivable") {
				$data = $this->LogisticsDashboard_model->notReceivedInvoicesDetails($sdate,$edate);
			}elseif ($type === "payableInvoices" && $status === "paid") {
				$data = $this->LogisticsDashboard_model->paidInvoicesDetails($sdate,$edate);
			} elseif ($type === "payableInvoices" && $status === "payable") {
				$data = $this->LogisticsDashboard_model->payableInvoicesDetails($sdate,$edate);
			}
			// print_r($data);exit;
			echo json_encode($data);
		}
		function shipementStatuAndNotesEdit(){
     
			if($this->input->post('did_input'))	{
					
				$this->form_validation->set_rules('did_input', 'dispatch id','required|min_length[1]');
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
				else
				{
					 $id = $this->input->post('did_input');
					 $sql = "SELECT driver_status, `status` FROM  dispatchOutside WHERE id=$id";
					 $old_data = $this->db->query($sql)->row();
					 $insert_data=array(
						'driver_status'=>$this->input->post('driver_status_input'),
						'status'=>$this->input->post('status_input')
					);
				
					$res = $this->Comancontroler_model->update_table_by_id($id,'dispatchOutside',$insert_data);  
					
					$changeField = [];
					if($old_data->driver_status != $this->input->post('driver_status_input')) { 
						$changeField[] = array('Shipment Status','driver_status',$old_data->driver_status,$this->input->post('driver_status_input')); 
					}
					if($old_data->status != $this->input->post('status_input')) { 
						$changeField[] = array('Shipment Notes','status',$old_data->status,$this->input->post('status_input')); 
					}
				
					$userid = $this->session->userdata('logged');
					if($changeField) {
						$changeFieldJson = json_encode($changeField);
						$aplog = array('did'=>$id,'userid'=>$userid['adminid'],'ip_address'=>getIpAddress(),'history'=>$changeFieldJson,'rDate'=>date('Y-m-d H:i:s'));
						$this->Comancontroler_model->add_data_in_table($aplog,'dispatchOutsideLog'); 
					}
					if($res){
						echo 'done';
					}
				}
			}
		}
	}
?>