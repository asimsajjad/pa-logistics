<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//require APPPATH . 'libraries/REST_Controller.php';
require(APPPATH.'/libraries/REST_Controller.php');
class Appapi extends REST_Controller {
    
    private $auth = 'authcode';
    
	public	 function __construct() {
		parent::__construct(); 
		$this->load->helper('url'); 
		$this->load->model('Appapi_model'); 
		date_default_timezone_set('America/Los_Angeles');
		//header('Content-Type: application/json');
	}
	
	public function generateToken($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function checkToken($token=''){
        
        if($token == ''){
            $this->response([
                    'status' => FALSE,
                    'message' => 'Token empty.'
                ],REST_Controller::HTTP_UNAUTHORIZED);
            return false;
        }
        
        $tokenInfo = $this->Appapi_model->get_data_by_column('token',$token,'drivers','id');
        
        if(empty($tokenInfo)) {
            $this->response([
                    'status' => FALSE,
                    'message' => 'Token has been expired please login again.'
                ],REST_Controller::HTTP_UNAUTHORIZED);
            return false;
        }
        else {
            return true;
        }
    }
    
	public function testheader_post(){
	    $data = array();
	    foreach (getallheaders() as $name => $value) {
            $data[] =  $name.' : '.$value;
        }
        $this->response([
                    'status' => TRUE,
                    'message' => 'Done',
                    'data' => $data
                ], REST_Controller::HTTP_OK);
	}
	
	// https://dispatch.patransportca.com/Appapi/driverlogin
	public function driverlogin_post(){ 
	    
	    $json = file_get_contents('php://input');
	    $data = json_decode($json,true);
	    
	   if($data) { 
	    
	    $phone = $data['phone']; //$this->post('phone');
        $dcode = $data['dcode']; //$this->post('dcode');
	        
	    //if($phone != '' && $dcode != '')	{  
	        
	          $getInfo = $this->Appapi_model->driverLogin($phone,$dcode);
	          
	          if(empty($getInfo)) {
	              $this->response([
                    'status' => FALSE,
                    'message' => 'Phone number or dcode is wrong',
                    'data' => array()
                ], REST_Controller::HTTP_OK);
	          } else {
	              $token = $this->generateToken(20).md5(md5(date('YmdHis'))).$this->generateToken(25);
	              $getInfo[0]['token'] = $token;
	              
	              $this->Appapi_model->update_table_by_id('id',$getInfo[0]['id'],'drivers',array('token'=>$token));
	              
	              $this->response([
                        'status' => TRUE,
                        'message' => 'Login successfully.',
                        'data' => $getInfo
                    ], REST_Controller::HTTP_OK);
	          }
	    }
	    else{
            // Set the response and exit
             $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
        }
	    
	}
	
	public function driverinfo_get() { 
	    $token = '';
	    $header = getallheaders(); //apache_request_headers();
        foreach ($header as $headers => $value) {
            //echo "$headers: $value <br />\n";
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	    //$json = file_get_contents('php://input');
	    //$data = json_decode($json,true);
	    
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	    
	    
	   if($token) {  
	    
	    
	    $columns = 'id,dname,phone,dimage,email,address';
        $driver = $this->Appapi_model->get_data_by_column('token',$token,'drivers',$columns);
        // Check if the user data exists
        if(!empty($driver)){
            $driver[0]['currentTrip'] = '';
            $currentDate = date('Y-m-d');
            $currentTrip = $this->Appapi_model->getCurrentTrip($driver[0]['id'],$currentDate); 
            if(!empty($currentTrip)) { $driver[0]['currentTrip'] = $currentTrip[0]['id']; }
             
            $this->response([
                        'status' => TRUE,
                        'message' => 'Driver info.',
                        'data' => $driver
                    ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                        'status' => FALSE,
                        'message' => 'Driver info not found.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        }
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function driverlogout_post() { 
	    
	    $token = '';
	    $header = getallheaders(); //apache_request_headers();
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
        
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	    
	    $columnsData = array('token'=>'');
        $driver = $this->Appapi_model->update_table_by_id('token',$token,'drivers',$columnsData); 
        
            $this->response([
                        'status' => TRUE,
                        'message' => 'Logout successfully.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
       
    }
    
    public function checkdrivershiftstatus_get() { 
	    $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	      
	    //son = file_get_contents('php://input');
	    //ata = json_decode($json,true);
	    
	    $currentUrl = $_SERVER['REQUEST_URI'];
	    
	    $currentUrlArray = explode('/',$currentUrl);
	    
	    $driverId = end($currentUrlArray);
	    
	   if($driverId > 0) {  
	    //$driverId = $data['id'];
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	    
	  if($driverId) {
        $driver = $this->Appapi_model->checkShiftStatus('true',$driverId);
        // Check if the user data exists
        if(!empty($driver)){
            $currentDate = $this->Appapi_model->get_data_by_column('id',$driver[0]['id'],'driver_shift','end_date, start_date');
            $this->response([
                        'status' => TRUE,
                        'message' => 'Shift started',
                        'data' => array('isShiftStarted' => true,
                        'startDate' => $currentDate[0]['start_date'])
                    ], REST_Controller::HTTP_OK);
        }else{
            
            $this->response([
                        'status' => FALSE,
                        'message' => 'Shift not start',
                        'data' => array('isShiftStarted' => false)
                    ], REST_Controller::HTTP_OK);
        }
	  }
	  } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function drivershiftstartend_put() { 
	    $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	      
	    $json = file_get_contents('php://input');
	    $data = json_decode($json,true);
	    
	   if($data) {  
	    $driverId = $data['id'];
	    $status = $data['isavailable'];
	    if($status) { $status = 'true'; }
	    else { $status = 'false'; }
	    
	    $latitude = $data['lat'];
	    $longitude = $data['long'];
	    
	   /* $geo = $data['geo'];
	    print_r($geo);
	    if($geo){
	        $latitude = $geo['lat'];
	        $longitude = $geo['long'];
	    }*/
	    // echo ' '.$latitude.' '.$longitude;
	     //die();
	    
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	    
	    if($driverId == '' || $status == '') {
	        $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
            return false;
	    }
	    
	  if($status == 'true') {
        $driver = $this->Appapi_model->checkShiftStatus('true',$driverId);
        // Check if the user data exists
        if(!empty($driver)){
            $currentDate = $this->Appapi_model->get_data_by_column('id',$driver[0]['id'],'driver_shift','end_date, start_date');
            $this->response([
                        'status' => TRUE,
                        'message' => 'Shift started already.',
                        'data' => array('startDate'=>$currentDate[0]['start_date'])
                    ], REST_Controller::HTTP_OK);
        }else{
            $insertData = array('driver_id'=>$driverId,'status'=>'true','start_latitude'=>$latitude,'start_longitude'=>$longitude,'end_date'=>date('Y-m-d H:i:s'),'start_date'=>date('Y-m-d H:i:s'));
            $insertId = $this->Appapi_model->add_data_in_table($insertData,'driver_shift');
            
            if($longitude && $latitude) {
                $updateGpsData = array('live_gps'=>$longitude.','.$latitude.','.date('Y-m-d H:i:s'));
                $this->Appapi_model->update_table_by_id('id',$driverId,'drivers',$updateGpsData);
            }
            
            $currentDate = $this->Appapi_model->get_data_by_column('id',$insertId,'driver_shift','end_date, start_date');
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Shift start successfully.',
                        'data' => array('startDate'=>$currentDate[0]['start_date'])
                    ], REST_Controller::HTTP_OK);
        }
	  }
	  elseif($status == 'false') {
	      $driver = $this->Appapi_model->checkShiftStatus('true',$driverId);
        // Check if the user data exists
        if(empty($driver)){
            $this->response([
                        'status' => TRUE,
                        'message' => 'Shift not started yet please first start the shift.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        }else{
            $end_date = date('Y-m-d H:i:s');
            $updateData = array('status'=>'closed','end_latitude'=>$latitude,'end_longitude'=>$longitude,'end_date'=>$end_date);
            $this->Appapi_model->updateShiftStatus($updateData,$driverId);
            
            if($longitude && $latitude) {
                $updateGpsData = array('live_gps'=>$longitude.','.$latitude.','.date('Y-m-d H:i:s'));
                $this->Appapi_model->update_table_by_id('id',$driverId,'drivers',$updateGpsData);
            }
            
            $currentDate = $this->Appapi_model->get_data_by_column('id',$driver[0]['id'],'driver_shift','end_date, start_date');
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Shift ended successfully.',
                        'data' => array('startDate'=>$currentDate[0]['end_date'])
                    ], REST_Controller::HTTP_OK);
        }
	  }
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function previousshiftupdate_put() { 
	    $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	      
	    $json = file_get_contents('php://input');
	    $data = json_decode($json,true);
	    
	   if($data) {  
	    $shiftid = $data['shiftId'];
	    $stime = $data['startTime']; 
	    $sdate = $data['startDate'];
	    $edate = $data['endDate'];
	    $etime = $data['endTime'];
	    
	    
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	    
	    if($shiftid == '' || $stime == '' || $sdate == '' || $etime == '' || $edate == '') {
	        $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
            return false;
	    }
	    
	    $nextDate = date('Y-m-d',strtotime("+1 day",strtotime($sdate)));
	    if($edate != $sdate && $edate != $nextDate) {
	        $this->response([
                    'status' => FALSE,
                    'message' => 'Please send valid end date.'
                ],REST_Controller::HTTP_OK);
            return false;
	    }
	    
	   
	      $shift = $this->Appapi_model->get_data_by_column('id',$shiftid,'driver_shift','id');
        // Check if the shift data exists
        if(empty($shift)){
            $this->response([
                        'status' => FALSE,
                        'message' => 'Shift id not exist please check shift id.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        }else{
            $end_date = date('Y-m-d H:i:s',strtotime($edate.' '.$etime));
            $start_date = date('Y-m-d H:i:s',strtotime($sdate.' '.$stime));
            $updateData = array('end_date'=>$end_date,'start_date'=>$start_date);
            $this->Appapi_model->update_table_by_id('id',$shiftid,'driver_shift',$updateData);
            
            $currentDate = $this->Appapi_model->get_data_by_column('id',$shiftid,'driver_shift','end_date, start_date');
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Shift update successfully.',
                        'data' => $currentDate
                    ], REST_Controller::HTTP_OK);
        }
	  
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function previousshift_post(){
        $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	    $json = file_get_contents('php://input');
	    $data = json_decode($json,true);
	    
	   if($data) { 
	    $driverId = $data['id'];
	    $date = $data['date'];
	    
	    $limit = $data['limit'];
	    if($limit == '' || $limit == 0) { $limit = 10; }
	    $pagno = $data['pageno'];
	    if($pagno == '' || $pagno == '1') { $start = 0; }
	    else { $start = ($pagno - 1) * $limit; }
	    
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	    
	    if($date == '') {
	        $startDate = '2016-01-01 00:00:01';
            //$endDate = date('Y-m-d',strtotime("-1 day",strtotime(date('Y-m-d')))).' 23:59:59';
            $endDate = date('Y-m-d').' 23:59:59';
	    } else {
	        $startDate = date('Y-m-d',strtotime($date)).' 00:00:01';
	        $endDate = date('Y-m-d',strtotime($date)).' 23:59:59';
	    }
	    
	    if($driverId == '') {
	        $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
            return false;
	    }
	    
	    $shiftInfo = $this->Appapi_model->getPreviousShift($driverId,$startDate,$endDate,$start,$limit);
	    
        if(!empty($shiftInfo)){
            $this->response([
                        'status' => TRUE,
                        'message' => 'Successfully.',
                        'data' => $shiftInfo
                    ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                        'status' => FALSE,
                        'message' => 'Records not found.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        }
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function triphistory_post(){
        $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	    $json = file_get_contents('php://input');
	    $data = json_decode($json,true);
	    
	   if($data) { 
	        
	    $driverId = $data['id'];
	    $type = $data['type'];
	    $sdate = $data['startdate'];
	    $edate = $data['enddate'];
	    
	    $limit = $data['limit'];
	    if($limit == '' || $limit == 0) { $limit = 10; }
	    $pagno = $data['pageno'];
	    if($pagno == '' || $pagno == '1') { $start = 0; }
	    else { $start = ($pagno - 1) * $limit; }
	     
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	    
	    $typeArray = array('today','week','month','calendar');
	    
	    if($type == 'week') {
	        $startDate = date('Y-m-d',strtotime('last monday'));
	        $endDate = date('Y-m-d',strtotime("+7 days",strtotime($startDate)));
	    }
	    elseif($type == 'month'){
	        $startDate = date('Y-m-01');
	        $endDate = date('Y-m-t');
	    }
	    elseif($type == 'calendar'){
	        $startDate = $sdate;
	        $endDate = $edate;
	    }
	    else {
	        $startDate = date('Y-m-d');
	        $endDate = date('Y-m-d');
	    }
	    
	    $shiftInfo = $this->Appapi_model->getTripHistory($driverId,$startDate,$endDate,$start,$limit);
	    
	    //echo $pagno.' - '.$driverId.' - '.$startDate.' - '.$endDate.' - '.$start.' - '.$limit;
	    //print_r($shiftInfo);die();
	    
        if(!empty($shiftInfo)  && in_array($type, $typeArray)){
            $this->response([
                        'status' => TRUE,
                        'message' => 'Successfully.',
                        'data' => $shiftInfo
                    ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                        'status' => FALSE,
                        'message' => 'Records not found.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        }
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function tripinfo_post(){
        $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
        $dstatus = array('Start Trip','Check in Pick Up','Loading Start','Loaded','Checked In','On the Way','Check in Drop Off','Unloading Start','Unloaded','Checked Out','Trip Done');
        
	    $json = file_get_contents('php://input');
	    $data = json_decode($json,true);
	    
	   if($data) { 
	    $driverId = $data['driverid'];
	    $tripId = $data['tripid'];
	     
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	     
	    
	    $tripInfo = $this->Appapi_model->getTripInfo($tripId);
	    $url = base_url('assets/upload/');
	    $documents = $this->Appapi_model->getTripDocuments($tripId,$url,'bol');
	    //$otherDispatch = $this->Appapi_model->getOtherDispatch($tripId);
	    $otherDispatch = $this->getOtherDispatch($tripId);
	    
        if(!empty($tripInfo) && $tripId){
            if (in_array($tripInfo[0]['driver_status'], $dstatus)){
                $loopEnd = 0;  
                foreach($dstatus as $val){
                    if($loopEnd < 1){  
                        $tripInfo[0]['allstatus'][] = $val;
                    }
                    if($val == $tripInfo[0]['driver_status']) { $loopEnd = 1; }
                }
            } else {  
                $tripInfo[0]['allstatus'][] = 'Pending'; 
            }
            
            /*if($tripInfo[0]['pcity1']=='0' && $tripInfo[0]['plocation1']=='0'){
	                $tripInfo[0]['otherDispatch'] = array();
	        } else {
	                $tripInfo[0]['otherDispatch'] = array(
	                    'dcity1'=>$tripInfo[0]['dcity1'],
	                    'dcompany1'=>$tripInfo[0]['dcompany1'],
	                    'daddress1'=>$tripInfo[0]['daddress1'],
	                    'dodate1'=>$tripInfo[0]['dodate1'],
	                    'dtime1'=>$tripInfo[0]['dtime1'],
	                    'dcode1'=>$tripInfo[0]['dcode1'],
	                    'pcity1'=>$tripInfo[0]['pcity1'],
	                    'pcompany1'=>$tripInfo[0]['pcompany1'],
	                    'paddress1'=>$tripInfo[0]['paddress1'],
	                    'pudate1'=>$tripInfo[0]['pudate1'],
	                    'ptime1'=>$tripInfo[0]['ptime1'],
	                    'pcode1'=>$tripInfo[0]['pcode1'],
	                    
	                    );
	        }*/
            $tripInfo[0]['otherdispatch'] = $otherDispatch;
            $tripInfo[0]['documents'] = $documents;
            $this->response([
                        'status' => TRUE,
                        'message' => 'Successfully.',
                        'data' => $tripInfo
                    ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                        'status' => FALSE,
                        'message' => 'Records not found.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        }
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function tripinfostatusupdate_post(){
        $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	    $json = file_get_contents('php://input');
	    $data = json_decode($json,true);
	    
	   if($data) { 
	    $driverId = $data['driverid'];
	    $tripId = $data['tripid'];
	    $status = $data['status'];
	    $notes = $data['notes'];
	     
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	      
        if($tripId && $status){
            $updateInfo = array('status'=>$status);
            if($notes != '') { $updateInfo['notes'] = $notes; }
            $tripInfo = $this->Appapi_model->update_table_by_id('id',$tripId,'dispatch',$updateInfo);
            $this->response([
                        'status' => TRUE,
                        'message' => 'Status update successfully.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                        'status' => FALSE,
                        'message' => 'Records not found.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        }
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function tripinfoupdate_post_old(){
        $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	    //$json = file_get_contents('php://input');
	    //$data = json_decode($json,true);
	    
	    $dstatus = array('Start Trip','Check in Pick Up','Loading Start','Loaded','Checked In','On the Way','Check in Drop Off','Unloading Start','Unloaded','Checked Out','Trip Done');
        
	    $tripId = $this->post('tripid'); //$data['driverid'];
	    $status = $this->post('driver_status');
	    
	   if($tripId) { 
	    //$driverId = $data['driverid'];
	    //$tripId = $data['tripid'];
	    //$status = $data['status'];
	    $notes = $this->post('notes');
	    $dnotes = $this->post('dnotes');
	    $pnotes = $this->post('pnotes');
	    $tracking = $this->post('tracking');
	    $dropoffdate = $this->post('dodate');
	    $dropofftime = $this->post('dtime');
	    $dcompany = $this->post('dcompany');
	    $otherDispatch = $this->post('otherdispatch'); 
	    /*echo '***** posst **************';
	     print_r($_POST);
	     echo '***** other dispatch **************';
	     print_r($otherDispatch);
	     echo '***** all ci post **************';
	     print_r($this->post());
	     echo '***** end data **************';*/
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	      
        if($tripId && $status){
            
            $this->load->helper(array('form', 'url'));
            $config = array(
                'upload_path' => "assets/upload/",
                'allowed_types' => "jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm", 
                'overwrite' => FALSE,
                'encrypt_name' => TRUE,
              //  'max_size' => "1000",
                //'max_height' => "768",
                //'max_width' => "1024"
            );
        
            $document = ''; $uploadFile = 'true';
    	    /*if(!empty($_FILES['document']['name'])){
                    $config['file_name'] = $_FILES['document']['name']; 
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config); 
                    if($this->upload->do_upload('document')){
                        $uploadData = $this->upload->data();
                        $document = $uploadData['file_name'];
                    } else {
                        $uploadFile = 'false';
                    }
            }
    	    if($uploadFile == 'false') {
    	        $this->response([
                            'status' => FALSE,
                            'message' => 'Document not upload please try again.',
                            'data' => array()
                        ], REST_Controller::HTTP_OK);
    	    } else {
    	        if($document != '') {
    	            $updateData = array('did'=>$tripId, 'type'=>'bol', 'fileurl'=>$document);
                    $driver = $this->Appapi_model->add_data_in_table($updateData,'documents');
    	        }
            */
                $updateInfo = array('driver_status'=>$status,'tracking'=>$tracking);
                if($notes != '') { $updateInfo['notes'] = $notes; }
                if($dnotes != '') { $updateInfo['dnotes'] = $dnotes; }
                if($pnotes != '') { $updateInfo['pnotes'] = $pnotes; }
                if($dropoffdate != '') { $updateInfo['dodate'] = $dropoffdate; }
                if($dropofftime != '') { $updateInfo['dtime'] = $dropofftime; }
                /*
                if(is_array($otherDispatch)) { $ddnotes = json_encode($otherDispatch); }
                else { $ddnotes = $otherDispatch; }
                $updateInfo['dnotes'] = $ddnotes;
                */
                if($otherDispatch) {
                    foreach($otherDispatch as $info){
                      if($info['id'] != '') {
                        $updateExtraInfo = array(
        					    'pd_time'=>$info['pd_time'],
        					    'pd_date'=>$info['pd_date'] 
                        );
                        if($info['pd_location'] != '') {
                            $dlocation1 = $this->check_location($info['pd_location']);
                            $updateExtraInfo['pd_location'] = $dlocation1;
                        }
                        $this->Appapi_model->update_table_by_id('id',$info['id'],'dispatchExtraInfo',$updateExtraInfo);
                      }
                    }
                }
                
                if($dcompany != '') {
                    $dlocation = $this->check_location($dcompany);
                    $updateInfo['dlocation'] = $dlocation;
                }
                
                $tripInfo = $this->Appapi_model->update_table_by_id('id',$tripId,'dispatch',$updateInfo);
                
                $filesCount = count($_FILES['document']['name']) + 0; 
                for($i = 0; $i < $filesCount; $i++){   
                    $_FILES['file']['name']     = $_FILES['document']['name'][$i]; 
                    $_FILES['file']['type']     = $_FILES['document']['type'][$i]; 
                    $_FILES['file']['tmp_name'] = $_FILES['document']['tmp_name'][$i]; 
                    $_FILES['file']['error']     = $_FILES['document']['error'][$i]; 
                    $_FILES['file']['size']     = $_FILES['document']['size'][$i]; 
                    
                    $config['file_name'] = $_FILES['file']['name']; 
                    $this->load->library('upload', $config); 
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('file')){
                        $uploadData = $this->upload->data();
                        $document = $uploadData['file_name'];
                        if($document != '') {
            	            $updateData = array('did'=>$tripId, 'type'=>'bol', 'fileurl'=>$document);
                            $driver = $this->Appapi_model->add_data_in_table($updateData,'documents');
            	        }
                    }
                }
                
                
                $tripInfo = $this->Appapi_model->getTripInfo($tripId);
                $url = base_url('assets/upload/');
	            $documents = $this->Appapi_model->getTripDocuments($tripId,$url,'bol');
	            $tripInfo[0]['documents'] = $documents;
	            
	            //$otherDispatchs = $this->Appapi_model->getOtherDispatch($tripId);
	            $otherDispatchs = $this->getOtherDispatch($tripId);
	            $tripInfo[0]['otherdispatch'] = $otherDispatchs;
	            /*
	            if($tripInfo[0]['pcity1']=='0' && $tripInfo[0]['plocation1']=='0'){
	                $tripInfo[0]['otherDispatch'] = array();
	            } else {
	                $tripInfo[0]['otherDispatch'] = array(
	                    'dcity1'=>$tripInfo[0]['dcity1'],
	                    'dcompany1'=>$tripInfo[0]['dcompany1'],
	                    'daddress1'=>$tripInfo[0]['daddress1'],
	                    'dodate1'=>$tripInfo[0]['dodate1'],
	                    'dtime1'=>$tripInfo[0]['dtime1'],
	                    'dcode1'=>$tripInfo[0]['dcode1'],
	                    'pcity1'=>$tripInfo[0]['pcity1'],
	                    'pcompany1'=>$tripInfo[0]['pcompany1'],
	                    'paddress1'=>$tripInfo[0]['paddress1'],
	                    'pudate1'=>$tripInfo[0]['pudate1'],
	                    'ptime1'=>$tripInfo[0]['ptime1'],
	                    'pcode1'=>$tripInfo[0]['pcode1']
	                    );
	            }*/
	            
	            if (in_array($tripInfo[0]['driver_status'], $dstatus)){
                    $loopEnd = 0;
                    foreach($dstatus as $val){
                        if($loopEnd < 1){
                            $tripInfo[0]['allstatus'][] = $val;
                        }
                        if($val == $tripInfo[0]['driver_status']) { $loopEnd = 1; }
                    }
                } else {
                    $tripInfo[0]['allstatus'][] = 'Pending'; 
                }
            
                $this->response([
                            'status' => TRUE,
                            'message' => 'Trip update successfully.',
                            'data' => $tripInfo
                        ], REST_Controller::HTTP_OK);
    	    //}
        }else{
            $this->response([
                        'status' => FALSE,
                        'message' => 'Please select status.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        }
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function tripinfoupdate_post(){
        $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	    //$json = file_get_contents('php://input');
	    //$data = json_decode($json,true);
	    
	    $dstatus = array('Start Trip','Check in Pick Up','Loading Start','Loaded','Checked In','On the Way','Check in Drop Off','Unloading Start','Unloaded','Checked Out','Trip Done');
        
	    $tripId = $this->post('tripid'); //$data['driverid'];
	    $status = $this->post('driver_status');
	    $formType = 'form';
	    /*if(!empty($data)) {
	        $tripId = $data['tripid'];
	        $status = $data['driver_status'];
	        $formType = 'rowform';
	    }
	    echo "**************** row data ****************'";
	    print_r($data);*/
	   if($tripId) {   
	            $notes = $this->post('notes');
        	    $dnotes = $this->post('dnotes');
        	    $pnotes = $this->post('pnotes');
        	    $tracking = $this->post('tracking');
        	    $dropoffdate = $this->post('dodate');
        	    $dropofftime = $this->post('dtime');
        	    $dcompany = $this->post('dcompany');
        	    //$otherDispatch = $this->post('otherdispatch');

	    /*echo '***** posst **************';
	     print_r($_POST);
	     echo $formType.'***** other dispatch **************';
	     print_r($otherDispatch);
	     echo '***** all ci post **************';
	     print_r($this->post());
	     echo '***** end data **************';*/
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	      
        if($tripId && $status){
            
            $this->load->helper(array('form', 'url'));
            $config = array(
                'upload_path' => "assets/upload/",
                'allowed_types' => "jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm", 
                'overwrite' => FALSE,
                'encrypt_name' => TRUE,
              //  'max_size' => "1000",
                //'max_height' => "768",
                //'max_width' => "1024"
            );
        
            $document = ''; $uploadFile = 'true';
    	    
                $updateInfo = array('driver_status'=>$status,'tracking'=>$tracking);
                if($notes != '') { $updateInfo['notes'] = $notes; }
                if($dnotes != '') { $updateInfo['dnotes'] = $dnotes; }
                if($pnotes != '') { $updateInfo['pnotes'] = $pnotes; }
                if($dropoffdate != '') { $updateInfo['dodate'] = $dropoffdate; }
                if($dropofftime != '') { $updateInfo['dtime'] = $dropofftime; }
                
                $otherDispatchInfo = $this->Appapi_model->getOtherDispatch($tripId);
                $otherDispatch = array();
                if($otherDispatchInfo){
                    foreach($otherDispatchInfo as $odi){
                        $inputName = 'otherdispatch_'.$odi['id'];
                        $odiSingle = $this->post($inputName);
                        if($odiSingle != '') {
                            $odiArray = explode('~-~',$odiSingle);
                            if(count($odiArray) > 2 && $odiArray[0] > 0){
                                if($odiArray[1] == '' && $odiArray[2] == '' && $odiArray[3] == ''){}
                                else {
                                    $otherDispatchSingle = array('id'=>$odiArray[0]);
                                    if($odiArray[1] != '') { $otherDispatchSingle['pd_time'] = $odiArray[1]; }
                                    if($odiArray[2] != '') { $otherDispatchSingle['pd_date'] = $odiArray[2]; }
                                    if($odiArray[3] != '') { $otherDispatchSingle['pd_location'] = $odiArray[3]; }
                                    $otherDispatch[] = $otherDispatchSingle;
                                }
                            }
                        }
                    }
                } 
                if($otherDispatch) {
                    foreach($otherDispatch as $info){
                      if($info['id'] != '') {
                        $updateExtraInfo = array(
        					    'pd_time'=>$info['pd_time'],
        					    'pd_date'=>$info['pd_date'] 
                        );
                        if($info['pd_location'] != '') {
                            $dlocation1 = $this->check_location($info['pd_location']);
                            $updateExtraInfo['pd_location'] = $dlocation1;
                        }
                        $this->Appapi_model->update_table_by_id('id',$info['id'],'dispatchExtraInfo',$updateExtraInfo);
                      }
                    }
                }
                
                if($dcompany != '') {
                    $dlocation = $this->check_location($dcompany);
                    $updateInfo['dlocation'] = $dlocation;
                }
                
                $tripInfo = $this->Appapi_model->update_table_by_id('id',$tripId,'dispatch',$updateInfo);
                
                $filesCount = count($_FILES['document']['name']) + 0; 
                for($i = 0; $i < $filesCount; $i++){   
                    $_FILES['file']['name']     = $_FILES['document']['name'][$i]; 
                    $_FILES['file']['type']     = $_FILES['document']['type'][$i]; 
                    $_FILES['file']['tmp_name'] = $_FILES['document']['tmp_name'][$i]; 
                    $_FILES['file']['error']     = $_FILES['document']['error'][$i]; 
                    $_FILES['file']['size']     = $_FILES['document']['size'][$i]; 
                    
                    $config['file_name'] = $_FILES['file']['name']; 
                    $this->load->library('upload', $config); 
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('file')){
                        $uploadData = $this->upload->data();
                        $document = $uploadData['file_name'];
                        if($document != '') {
            	            $updateData = array('did'=>$tripId, 'type'=>'bol', 'fileurl'=>$document);
                            $driver = $this->Appapi_model->add_data_in_table($updateData,'documents');
            	        }
                    }
                }
                
                
                $tripInfo = $this->Appapi_model->getTripInfo($tripId);
                $url = base_url('assets/upload/');
	            $documents = $this->Appapi_model->getTripDocuments($tripId,$url,'bol');
	            $tripInfo[0]['documents'] = $documents;
	            
	            //$otherDispatchs = $this->Appapi_model->getOtherDispatch($tripId);
	            $otherDispatchs = $this->getOtherDispatch($tripId);
	            $tripInfo[0]['otherdispatch'] = $otherDispatchs;
	              
	            if (in_array($tripInfo[0]['driver_status'], $dstatus)){
                    $loopEnd = 0;
                    foreach($dstatus as $val){
                        if($loopEnd < 1){
                            $tripInfo[0]['allstatus'][] = $val;
                        }
                        if($val == $tripInfo[0]['driver_status']) { $loopEnd = 1; }
                    }
                } else {
                    $tripInfo[0]['allstatus'][] = 'Pending'; 
                }
            
                $this->response([
                            'status' => TRUE,
                            'message' => 'Trip update successfully.',
                            'data' => $tripInfo
                        ], REST_Controller::HTTP_OK);
    	    //}
        }else{
            $this->response([
                        'status' => FALSE,
                        'message' => 'Please select status.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        }
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function getvehicles_get(){
        $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
          
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	    
	    
	    $vehicles = $this->Appapi_model->get_data_by_table('vehicles','id,vname,vnumber');
	    
        if(!empty($vehicles)){
            $this->response([
                        'status' => TRUE,
                        'message' => 'Successfully.',
                        'data' => $vehicles
                    ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                        'status' => FALSE,
                        'message' => 'Records not found.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        } 
    }
     
    public function addothers_post() { 
        
        $this->load->helper(array('form', 'url'));
        
    
	    $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        } 
	    
	  
	    $driverId = $this->post('driverid'); //$data['driverid'];
	    $date = $this->post('date');
	    $amount = $this->post('amount');
	    $truckid = $this->post('truckid');
	    $type = $this->post('type');
	    $notes = $this->post('notes');
	    
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	     
	     
	    $typeArray = array('fuel','reimbursement','truck_supplies_request');
	    
	    if($type == 'truck_supplies_request') { $amount = '1'; } 
	    
	  if($amount > 0 && $truckid != '' && $driverId > 0 && $date != '' && $type != '' && in_array($type, $typeArray)) {
	      
	      $config = array(
            'upload_path' => "assets/".$type."/",
            'allowed_types' => "jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm", 
            'overwrite' => FALSE,
            'encrypt_name' => TRUE,
          //  'max_size' => "1000",
            //'max_height' => "768",
            //'max_width' => "1024"
        );
        
	      
	    $document = ''; $uploadFile = 'true';
	    /*if(!empty($_FILES['document']['name'])){
                $config['file_name'] = $_FILES['document']['name']; 
                $this->load->library('upload',$config);
                $this->upload->initialize($config); 
                if($this->upload->do_upload('document')){
                    $uploadData = $this->upload->data();
                    $document = $uploadData['file_name'];
                    if($document != '') {
        	            $updateData = array('did'=>$tripId, 'type'=>'bol', 'fileurl'=>$document);
                        $driver = $this->Appapi_model->add_data_in_table($updateData,'documents');
        	        }
                } else {
                    $uploadFile = 'false';
                }
        }*/
	    /*if($uploadFile == 'false') {
	        $this->response([
                        'status' => FALSE,
                        'message' => 'Document not upload please try again.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
	    } else {*/
	        $updateData = array('driver_id'=>$driverId, 'amount'=>$amount, 'truck'=>$truckid,'document'=>$document,'fdate'=>$date);
	        if($notes != '') { $updateData['notes'] = $notes; } 
            $inserid = $this->Appapi_model->add_data_in_table($updateData,$type);
            if($inserid > 0) {
                $filesCount = count($_FILES['document']['name']) + 0; 
                for($i = 0; $i < $filesCount; $i++){   
                    $_FILES['file']['name']     = $_FILES['document']['name'][$i]; 
                    $_FILES['file']['type']     = $_FILES['document']['type'][$i]; 
                    $_FILES['file']['tmp_name'] = $_FILES['document']['tmp_name'][$i]; 
                    $_FILES['file']['error']     = $_FILES['document']['error'][$i]; 
                    $_FILES['file']['size']     = $_FILES['document']['size'][$i]; 
                    
                    $config['file_name'] = $_FILES['file']['name']; 
                    $this->load->library('upload', $config); 
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('file')){
                        $uploadData = $this->upload->data();
                        $document = $uploadData['file_name'];
                        if($document != '') {
            	            $updateData = array('did'=>$inserid, 'type'=>$type, 'fileurl'=>$document);
                            $driver = $this->Appapi_model->add_data_in_table($updateData,'documents');
            	        }
                    }
                }
            }
        
            $this->response([
                        'status' => TRUE,
                        'message' => 'Record added successfully.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
	    //}
	  
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
     
    public function tripdocument_post() { 
        
        $this->load->helper(array('form', 'url'));
        $config = array(
            'upload_path' => "assets/upload/",
            'allowed_types' => "jpg|jpeg|png|gif|pdf|doc|docx|docm|xls|xlsx|xlsm", 
            'overwrite' => FALSE,
            'encrypt_name' => TRUE,
          //  'max_size' => "1000",
            //'max_height' => "768",
            //'max_width' => "1024"
        );
    
	    $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        } 
	    
	  
	    $tripId = $this->post('tripid'); //$data['driverid'];
	    
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	    
	    
	  if($tripId > 0) {
	      
	    $document = ''; $uploadFile = 'true';
	    if(!empty($_FILES['document']['name'])){
                $config['file_name'] = $_FILES['document']['name']; 
                $this->load->library('upload',$config);
                $this->upload->initialize($config); 
                if($this->upload->do_upload('document')){
                    $uploadData = $this->upload->data();
                    $document = $uploadData['file_name'];
                } else {
                    $uploadFile = 'false';
                }
        }
	    if($uploadFile == 'false') {
	        $this->response([
                        'status' => FALSE,
                        'message' => 'Document not upload please try again.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
	    } else {
	        $updateData = array('did'=>$tripId, 'type'=>'bol', 'fileurl'=>$document);
            $driver = $this->Appapi_model->add_data_in_table($updateData,'documents');
        
            $this->response([
                        'status' => TRUE,
                        'message' => 'Record added successfully.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
	    }
	  
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function livelatlog_post(){
        $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	    $json = file_get_contents('php://input');
	    $data = json_decode($json,true);
	    
	   if($data) { 
	    $driverId = $data['id'];
	    $log = $data['log'];
	    $lat = $data['lat'];
	     
	    
	    $checkToken = $this->checkToken($token);
	    
	    if(!$checkToken) { return false; }
	     
	    
	    if($driverId == '' || $log == '' || $lat == '') {
	        $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
            return false;
	    }
	    
	    $live_gps = $log.','.$lat.','.date('Y-m-d H:i:s');
	    $updateInfo = array('live_gps'=>$live_gps);
	    $this->Appapi_model->update_table_by_id('id',$driverId,'drivers',$updateInfo);
	    
        $this->response([
                        'status' => TRUE,
                        'message' => 'Successfully.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    public function getOtherDispatch($tripId){
        $return = $pickup = $dropoff = array();
        $otherDispatch = $this->Appapi_model->getOtherDispatch($tripId);
        if($otherDispatch) {
            $p = $d = 2;
            foreach($otherDispatch as $val){
                if($val['pd_type']=='pickup'){
                    $val['pd_serialNo'] = $p;
                    $p++;
                }
                if($val['pd_type']=='dropoff'){
                    $val['pd_serialNo'] = $d;
                    $d++;
                }
                $return[] = $val;
            }
            
            //$return = array_merge($pickup,$dropoff);
        }
        return $return;
    }
    
    public function check_location($location) {
	 $company_data = $this->Appapi_model->get_data_by_column('location',$location,'locations','id');
	 if(empty($company_data)) {
		 $insert_data = array('location'=>$location);
		$res = $this->Appapi_model->add_data_in_table($insert_data,'locations'); 
		return $res;
	 } else {
		return $company_data[0]['id']; 
	 }
    }
    
    public function check_company($company) {
    	 $company_data = $this->Appapi_model->get_data_by_column('company',$company,'companies','id');
    	 if(empty($company_data)) {
    		 $insert_data = array('company'=>$company);
    		$res = $this->Appapi_model->add_data_in_table($insert_data,'companies'); 
    		return $res;
    	 } else {
    		return $company_data[0]['id']; 
    	 }
    }
    
    public function remove_document_delete(){
        $token = '';
	    $header = getallheaders();  
        foreach ($header as $headers => $value) { 
            if($headers == 'Token'){
                $token = $value;
            }
        }
                    
	    $json = file_get_contents('php://input');
	    $data = json_decode($json,true);
	    
	   if($data) { 
	    $documentId = $data['id'];
	    $type = $data['type']; 
	    
	    $checkToken = $this->checkToken($token);
	    if(!$checkToken) { return false; } 
	    
	    $typeArry = array('bol','fuel','reimbursement','truck_supplies_request');
	    
	    if($documentId == '' || $type == '' || (!in_array($type,$typeArry))) {
	        $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
            return false;
	    }
	    
	   $file = $this->Appapi_model->get_document_by_filter($documentId,$type);
        if(empty($file)) {
             $this->response([
                        'status' => FALSE,
                        'message' => 'Document not found.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        } else {
            if($type == 'bol') { $folder = 'upload'; }
            elseif($type == 'fuel') { $folder = 'fuel'; }
            elseif($type == 'fuel') { $folder = 'reimbursement'; }
            elseif($type == 'fuel') { $folder = 'truck_supplies_request'; }
            else { $folder = 'nofolder'; }
            
    	    if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/'.$folder.'/'.$file[0]['fileurl'])) {
                unlink(FCPATH.'assets/'.$folder.'/'.$file[0]['fileurl']);  
            }
            $this->Appapi_model->delete($documentId,'documents','id');
            $this->response([
                        'status' => TRUE,
                        'message' => 'Successfully.',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
        } 
	   } else {
	       $this->response([
                    'status' => FALSE,
                    'message' => 'Please send required parameters.'
                ],REST_Controller::HTTP_BAD_REQUEST);
	   }
    }
    
    function removefile(){
     $did = $this->uri->segment(5);
     $id = $this->uri->segment(4);
     $file = $this->Comancontroler_model->get_data_by_id($id,'documents');
     if(empty($file)) {
         $this->session->set_flashdata('item', 'File not exist.'); 
     } else {
	    if($file[0]['fileurl']!='' && file_exists(FCPATH.'assets/upload/'.$file[0]['fileurl'])) {
            unlink(FCPATH.'assets/upload/'.$file[0]['fileurl']);  
            
            $this->session->set_flashdata('item', 'Document removed successfully.'); 
        }
        $this->Comancontroler_model->delete($id,'documents','id');
     }
     redirect(base_url('admin/dispatch/update/'.$did));
 }
 
}
?>