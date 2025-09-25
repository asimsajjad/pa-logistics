<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	class AdminDashboard extends CI_Controller {
		public	 function __construct()
		{
			parent::__construct();
			//$this->load->helper(array('form','url'));
			$this->load->library('form_validation');
			//$this->load->model('Login_model');
			$this->load->model('Register_model');
			$this->load->model('Comancontroler_model');
			$this->load->helper(array('form','url'));
			$this->load->library('session');
			if( empty($this->session->userdata('logged') )) {
				redirect('adminlogin/');
			}
		}
		public function index(){
			$data['gps'] = $this->Register_model->getLiveGps();
			$sdate = date('Y-m-d');
			$edate = date('Y-m-d',strtotime("+30 days",strtotime($sdate)));
			$data['permits'] = $this->Comancontroler_model->getDataByDate('expDate',$sdate,$edate,'permits','expDate,title');
			$data['insurance'] = $this->Comancontroler_model->getDataByDate('endDate',$sdate,$edate,'insurance','endDate,title');
			$data['hireDate'] = $this->Comancontroler_model->getDataByDate('sdate',$sdate,$edate,'drivers','sdate,dname,status');
			$data['dob'] = $this->Comancontroler_model->getDataByDate('dob',$sdate,$edate,'drivers','dob,dname,status');
			$data['medicalExpDate'] = $this->Comancontroler_model->getDataByDate('medate',$sdate,$edate,'drivers','medate,dname,status');
			$data['licenseExpDate'] = $this->Comancontroler_model->getDataByDate('ledate',$sdate,$edate,'drivers','ledate,dname,status');
			$data['servicesExpDate'] = $this->Comancontroler_model->getServicesByDate($sdate,$edate);
			$data['reimbursement'] = $this->Comancontroler_model->getCurrentReimbursement($sdate);
			$date = date('Y-m-d');
			$data['currentTrip'] = $this->Comancontroler_model->getCurrentTrip($date);
			if($data['currentTrip']){
        	    for($i=0;count($data['currentTrip']) > $i;$i++){  
        	        $dispatchInfo = $this->Comancontroler_model->get_dispatchinfo_by_id($data['currentTrip'][$i]['id'],'pd_date,pd_city,pd_location,pd_time,pd_addressid');
    				if($dispatchInfo){
    					foreach($dispatchInfo as $dis){
    						$data['currentTrip'][$i]['pd_date'] = $dis['pd_date'];
    						$data['currentTrip'][$i]['pd_city'] = $dis['pd_city'];
    						$data['currentTrip'][$i]['pd_location'] = $dis['pd_location'];
    						$data['currentTrip'][$i]['pd_time'] = $dis['pd_time'];
    					}
    				} else {
    				    $data['currentTrip'][$i]['pd_date'] = $data['currentTrip'][$i]['pd_city'] = $data['currentTrip'][$i]['pd_location'] = $data['currentTrip'][$i]['pd_time'] = $data['currentTrip'][$i]['pd_addressid'] = '';
    				}
        	    }
        	}
			
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/dashboard',$data);
			$this->load->view('admin/layout/footer');
		}
		public function user(){
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/dashboard');
			$this->load->view('admin/layout/footer');
		}
		function alluser()
		{
			$data['user'] = $this->Register_model->get_user();
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/alluser',$data);
			$this->load->view('admin/layout/footer');
		}
		function view(){
			$id = $this->uri->segment(4);
			$data['donation'] = $this->Register_model->get_data_id($id);
			$this->load->view('admin/layout/header');
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/viewuser',$data);
			$this->load->view('admin/layout/footer');
		}	
		function userdelete(){
			redirect(base_url('admin/alluser'));
			$id = $this->uri->segment(4);
			$result = $this->Register_model->delete($id,'user_register','id');
			if($result){
				redirect(base_url('admin/alluser'));
			}
		}
		
		public function uploadcsv() {
			echo $csv_file_path = FCPATH.'/old/companies.csv'; // Update with the actual path to your CSV file

			// Step 1: Read the CSV file
			$csv_data = $this->read_csv($csv_file_path);
			print_r($csv_data);
			// Step 2 and 3: Upload files and insert entries into the database
			$this->upload_files_and_insert_into_db($csv_data);

			echo "File upload and database insertion completed.";
		}

		private function read_csv($file_path) {
			$csv_data = [];
			if (($handle = fopen($file_path, 'r')) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
					$csv_data[] = $data;
				}
				fclose($handle);
			}
			return $csv_data;
		}

		private function upload_files_and_insert_into_db($csv_data) {
			$this->load->database();
			$this->load->helper('file');
			$this->load->library('upload');

			foreach ($csv_data as $row) {
				$id = $row[0];
				$file_path = $row[1];

				if (file_exists($file_path)) {
					$new_file_name = uniqid() . '_' . basename($file_path);
					$config['upload_path'] = './old/';
					$config['allowed_types'] = 'pdf';
					$config['file_name'] = $new_file_name;

					$this->upload->initialize($config);

					if (copy($file_path, $config['upload_path'] . $new_file_name)) {
						$data = [
							'id' => $id,
							'original_file_path' => $file_path,
							'uploaded_file_path' => $config['upload_path'] . $new_file_name
						];
						print_r($data);
						//$this->db->insert('uploads', $data);
					} else {
						echo 'Failed to copy file: ' . $file_path;
					}
				} else {
					echo 'File does not exist: ' . $file_path;
				}
			}
		}
	}
?>