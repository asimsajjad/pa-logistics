<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	class FleetDashboard extends CI_Controller {
		public	 function __construct()
		{
			
			parent::__construct();
			//$this->load->helper(array('form','url'));
			$this->load->library('form_validation');
			//$this->load->model('Login_model');
			$this->load->model('Register_model');
			$this->load->model('FleetDashboard_model');
			$this->load->model('Comancontroler_model');
			$this->load->helper(array('form','url'));
			$this->load->library('session');
			if( empty($this->session->userdata('logged') )) {
				redirect('adminlogin/');
			}
		}

        public function fleetDashboard(){
            $data=[];
			$sdate = $edate = '';			 
			if($this->input->post('search')){
				if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            	if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); }	
			} 
            $data['shipmentStatusCounts'] = $this->FleetDashboard_model->shipmentStatusCounts($sdate,$edate);
			$data['bookingsCounts'] = $this->FleetDashboard_model->bookingsCounts($sdate,$edate);
			$data['receivableInvoicesCounts'] = $this->FleetDashboard_model->receivableInvoicesCounts($sdate,$edate);
			$data['shipmentStatus'] = $this->Comancontroler_model->get_data_by_column('status','Active','shipmentStatus','title','order','asc');
			$data['reimbursementCounts'] = $this->FleetDashboard_model->reimbursementCounts();
			$startdate = date('Y-m-d');
			$enddate = date('Y-m-d',strtotime("+30 days",strtotime($startdate)));
			$data['permits'] = $this->Comancontroler_model->getDataByDate('expDate',$startdate,$enddate,'permits','expDate,title');
			$data['insurance'] = $this->Comancontroler_model->getDataByDate('endDate',$startdate,$enddate,'insurance','endDate,title');
			$data['hireDate'] = $this->Comancontroler_model->getDataByDate('sdate',$startdate,$enddate,'drivers','sdate,dname,status');
			$data['dob'] = $this->Comancontroler_model->getDataByDate('dob',$startdate,$enddate,'drivers','dob,dname,status');
			$data['medicalExpDate'] = $this->Comancontroler_model->getDataByDate('medate',$startdate,$enddate,'drivers','medate,dname,status');
			$data['licenseExpDate'] = $this->Comancontroler_model->getDataByDate('ledate',$startdate,$enddate,'drivers','ledate,dname,status');
			$data['servicesExpDate'] = $this->Comancontroler_model->getServicesByDate($startdate,$enddate);
			$data['gps'] = $this->Register_model->getLiveGps();

			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/fleetDashboard',$data);
			$this->load->view('admin/layout/footer');
        }

	public function getDetailsData()
	{
		$type = $this->input->post('type');
		$status = $this->input->post('status');
		$sdate = $this->input->post('sdate');
		$edate = $this->input->post('edate');
		$data = [];
		if ($type === "shipment" && $status === "Delivery") {
			$data = $this->FleetDashboard_model->deliveryShipmentDetails($sdate,$edate);
			$today = date('Y-m-d');
			foreach ($data as &$row) {
				// echo $row['dispatchid'];exit;
				$dispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,pd_type', 'dispatchExtraInfo', 'delivery','ASC');
				$dispatchLog = $this->Comancontroler_model->get_dispachLog('dispatchLog',$row['dispatchid']);
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
		} elseif ($type === "shipment" && $status === "Pickup") {
			$today = date('Y-m-d');
			$data = $this->FleetDashboard_model->pickupShipmentDetails($sdate,$edate);
			foreach ($data as &$row) {
				// echo $row['dispatchid'];exit;
				$allDatesPassed = true;
				$dispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,pd_type', 'dispatchExtraInfo', 'pickup',  'ASC');
				$dispatchLog = $this->Comancontroler_model->get_dispachLog('dispatchLog',$row['dispatchid']);
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
		} elseif ($type === "shipment" && $status === "Pending") {
			$data = $this->FleetDashboard_model->pendingShipmentDetails($sdate,$edate);
			foreach ($data as &$row) {
				$dispatchInfo = $this->Comancontroler_model->get_dispatchinfo_by_id($row['id'], 'pd_date,pd_city,pd_location,pd_time,pd_addressid');
				if ($dispatchInfo) {
					foreach ($dispatchInfo as $dis) {
						$row['pd_date'] = $dis['pd_date'];
						$row['pd_city'] = $dis['pd_city'];
						$row['pd_location'] = $dis['pd_location'];
						$row['pd_time'] = $dis['pd_time'];
					}
				}
			}
		} elseif ($type === "shipment" && $status === "pendingInvoices") {
				$data = $this->FleetDashboard_model->pendingInvoicesDetails();
		} elseif ($type === "bookings" && $status === "weeklyBookings") {
			$data = $this->FleetDashboard_model->weeklyBookingsDetails($sdate,$edate);
				foreach ($data as &$row) {
				// echo $row['dispatchid'];exit;
				$pdispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,companyAddress.company,pd_type', 'dispatchExtraInfo', 'pickup',  'ASC');
				$ddispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,companyAddress.company,pd_type', 'dispatchExtraInfo', 'dropoff',  'ASC');
				$dispatchLog = $this->Comancontroler_model->get_dispachLog('dispatchLog',$row['dispatchid']);
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
				if($dispatchLog[0]){
					$row['lastUpdateInfo'][] = [
						'uname' => $dispatchLog[0]['uname'],
						'rDate' => $dispatchLog[0]['rDate']
					];
				}
			}
		} elseif ($type === "bookings" && $status === "Unassigned") {
			$data = $this->FleetDashboard_model->unassignedBookingsDetails($sdate,$edate);
			foreach ($data as &$row) {
				// echo $row['dispatchid'];exit;
				$pdispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,companyAddress.company,pd_type', 'dispatchExtraInfo', 'pickup',  'ASC');
				$ddispatchInfo = $this->Comancontroler_model->get_extradispatchinfo_by_id($row['dispatchid'],'pd_date,pd_city,CONCAT(city, ", ", state) as city,pd_location,pd_time,pd_addressid,companyAddress.company,pd_type', 'dispatchExtraInfo', 'dropoff',  'ASC');
				$dispatchLog = $this->Comancontroler_model->get_dispachLog('dispatchLog',$row['dispatchid']);
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
				if($dispatchLog[0]){
					$row['lastUpdateInfo'][] = [
						'uname' => $dispatchLog[0]['uname'],
						'rDate' => $dispatchLog[0]['rDate']
					];
				}
			}
		} elseif ($type === "receivableInvoices" && $status === "Received") {
			$data = $this->FleetDashboard_model->receivedInvoicesDetails($sdate,$edate);
		} elseif ($type === "receivableInvoices" && $status === "Receivable") {
			$data = $this->FleetDashboard_model->notReceivedInvoicesDetails($sdate,$edate);
		} elseif ($type === "reimbursements" && $status === "Reimbursed") {
			$data = $this->FleetDashboard_model->reimbursedDetails();
		} elseif ($type === "reimbursements" && $status === "Reimbursable") {
			$data = $this->FleetDashboard_model->reimbursableDetails();
		}
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
				 $sql = "SELECT driver_status, `status` FROM  dispatch WHERE id=$id";
				 $old_data = $this->db->query($sql)->row();
				 $insert_data=array(
					'driver_status'=>$this->input->post('driver_status_input'),
					'status'=>$this->input->post('status_input')
				);
			
				$res = $this->Comancontroler_model->update_table_by_id($id,'dispatch',$insert_data); 

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
					$this->Comancontroler_model->add_data_in_table($aplog,'dispatchLog'); 
				}

				if($res){
					echo 'done';
				}
			}
		}
	}
}
?>