<?php
/*
* Developer Name: Ravinder Singh Mehta
* Developed For : My Talnt Hunt
* Development Com : 33infotech
* Version 1.0
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

// Load the Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';

class Authentication extends REST_Controller {

    public function __construct() { 
        parent::__construct();
        //$this->load->library('session');
        // Load the user model
        $this->load->model('Appapi_model');
     
    }
    
  
    public function login_post() {
        // Get the post data
        $phone = $this->post('phone');
        $dcode = $this->post('dcode');
         
        // Validate the post data
        if(!empty($phone) && !empty($dcode)){
            $user = $this->Appapi_model->driverLogin($phone,$dcode);
            if($user){
                
                
                 $userimage = '';
                    // if($user['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$user['image']; }else{$userimage = 'http://majemeyfarms.com/images/user/31.png';}
                     
                     //$result = array('id'=>$user['id'],'d_name'=>$user['d_name'],'f_name'=>$user['f_name'],'l_name'=>$user['l_name'],'uid'=>$user['userid'],'customer_id'=>$user['customer_id'],'email'=>$user['email'],'phone'=>$user['phone'],'gender'=>$user['gender'],'dob'=>$user['dob'],'address'=>$user['address'],'city'=>$user['city'],'state'=>$user['state'],'pincode'=>$user['pincode'],'image'=>$userimage,'bliss_amount'=>$user['bliss_amount'],'status'=>$user['status'],'device_id'=>$user['device_id']); 
                 $result = $user; //array('id'=>$user['id'],'d_name'=>'vivek');
                
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'User login successful.',
                    'data' => $result
                ], REST_Controller::HTTP_OK);
                
        
            }
            else{
                // Set the response and exit
                //BAD_REQUEST (400) being the HTTP response code
                $this->response([
                    'status' => FALSE,
                    'message' => 'Wrong username or password.',
                    'data' => ''
                ],REST_Controller::HTTP_OK);
            }
        }
        else{
            // Set the response and exit
             $this->response([
                    'status' => FALSE,
                    'message' => 'Provide email and password.',
                    'data' => ''
                ],REST_Controller::HTTP_BAD_REQUEST);
        }
    }
 
    public function userforgot_post() {
        // Get the post data
        $email = $this->post('email');
     
        // Validate the post data
        if(!empty($email)){
            
            $user = $this->user->userforgot($email);
            
            if($user){
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'Password sent to registered Email or phone  Number',
                    'data' => $user
                ], REST_Controller::HTTP_OK);
            }else{
                // Set the response and exit
                //BAD_REQUEST (400) being the HTTP response code
                $this->response([
                    'status' => FALSE,
                    'message' => 'Wrong email or phone.',
                    'data' => $user
                ],REST_Controller::HTTP_OK);
                
            }
        }else{
            // Set the response and exit
            
             $this->response([
                    'status' => FALSE,
                    'message' => 'Provide email or phone.',
                    'data' => $user
                ],REST_Controller::HTTP_OK);
        }
    }
    public function registration_post() {
        // Get the post data
        $first_name = strip_tags($this->post('name'));
        $email = strip_tags($this->post('email'));
        $phone = strip_tags($this->post('phone'));
        $password = strip_tags($this->post('password'));
        $direct_customer_id = strip_tags($this->post('refer'));
        //$otp=$this->post('otp');
        
		
        
        // Validate the post data
        if(!empty($first_name) && !empty($phone)){
            
            // Check if the given email already exists
            $con['returnType'] = 'count';
            $con['conditions'] = array(
                'email' => $email,
            );
            
            $con1['returnType'] = 'count';
            $con1['conditions'] = array(
                'phone' => $phone,
            );
			$con2['returnType'] = 'count';
            $con2['conditions'] = array(
                'customer_id' => $direct_customer_id,
                'consume >' => 0,
            );
            
            $userCount = $this->user->getRows($con);
            $userCount1 = $this->user->getRows($con1);
			if(!empty($direct_customer_id)){
            $userCount2 = $this->user->getRows($con2);
			}else{
			$userCount2=1;	
				
			}
            
           /*  if($otp!=''){
                $otp_exist=$this->user->otp_veryfy($phone,$otp);
            } */ 
			if(is_numeric($phone)) {
              }	else{
				  $this->response([
                    'status' => FALSE,
                    'message' => 'Enter Correct Phone Number.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
			  }
            
            if($userCount > 0){
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'The given email already exists.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
                
            }
            elseif($userCount1 > 0){
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'The given Phone already exists please login.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
                
            }
           		elseif($userCount2==0){
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Referral Code Not exist.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
                
            }
            /*elseif ($otp=='') {
                $phone = $this->post('phone');
                //$this->session->set_userdata('no_veryfied','no');
                if($phone != '') {
                    $otp_crt = rand(1000,9999);
                    
                    $otpdata = array(
                     'phone' => $phone,
                     'otp' => $otp_crt,);
                    $this->user->insert_manual('otp_verification',$otpdata);
                    $sms_msg = urlencode("Your OTP is ".$otp_crt."\nThank you\nTeam My Talent Hunt");
                    //$smstext = "http://weberleads.in/http-api.php?username=".$this->config->item('sms_user')."&password=".$this->config->item('sms_pass')."&senderid=MTHUNT&route=2&number=".$phone."&message=".$sms_msg;
					
				
				$smstext = "http://103.16.101.52/sendsms/bulksms?username=bsz-talenthunt&password=".$this->config->item('sms_pass')."&type=0&dlr=1&destination=".$phone."&source=".$this->config->item('sms_sndrid')."&message=".$sms_msg;
                    file_get_contents($smstext);

                    $to = $this->post('email');
                    $subject ="OTP for My Talent Hunt Registration";
                    $txt = "Your OTP for My Talent Hunt registration is ".$otp_crt.""; 
                    $headers = "From: info@mytalenthunt.in"."\r\n";
                    $headers = "MIME-Version: 1.0" . "\r\n";     
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";  
                    //$headers .= 'From: <https://www.mytalenthunt.in>' . "\r\n"; 
                    mail($to,$subject,$txt,$headers);
                }
                
                $this->response([
                        'status' => TRUE,
                        'message' => 'otp',
                        'data' => $otp_crt,
                    ], REST_Controller::HTTP_OK);
                
                
            } 
            elseif ($otp_exist!=1) {

                $this->response([
                    'status' => FALSE,
                    'message' => 'The OTP entered is incorrect',
                    'data' => '',
                ],REST_Controller::HTTP_OK);

            }*/
            else{
                // Insert user data
                
                 
                
                $userData = array(
                'd_name' => $first_name,
                'f_name' => $first_name,
                'email' => $email,
                'phone' => $phone,
                'pass_word' => md5($password),
                'device_id' => $this->post('device_id'),
                'direct_customer_id' => str_replace(' ', '', $direct_customer_id),
                'status' => 'active',   
                );
                $insert = $this->user->insert($userData,$password);
                
                // Check if the user data is inserted
                if($insert){
                    // Set the response and exit
                    $result= "$insert";
                    
                    $this->response([
                        'status' => TRUE,
                        'message' => 'The user has been added successfully. Please login now',
                        'data' => $result,
                    ], REST_Controller::HTTP_OK);
                }else{
                    // Set the response and exit
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Some problems occurred, please try again.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);   
                }
            }
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
            
        }
    }
    public function registerwithgoogle_post() {
        // Get the post data
        $first_name = strip_tags($this->post('name'));
        $email = $this->post('email');
        $device_id = $this->post('device_id');
        
        // Validate the post data
        if(!empty($email)){
            $con1['returnType'] = 'count';
            $con1['conditions'] = array(
                'email' => $email,
            );
             $userCount = $this->user->getRows($con1);
            
         if($userCount > 0){
              $user = $this->user->loginwithemail($email);
              $userimage = '';
                     if($user['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$user['image']; }else{$userimage = 'http://majemeyfarms.com/images/user/31.png';}
                     
                     $result = array('id'=>$user['id'],'d_name'=>$user['d_name'],'f_name'=>$user['f_name'],'l_name'=>$user['l_name'],'customer_id'=>$user['customer_id'],'email'=>$user['email'],'phone'=>$user['phone'],'gender'=>$user['gender'],'dob'=>$user['dob'],'address'=>$user['address'],'city'=>$user['city'],'state'=>$user['state'],'pincode'=>$user['pincode'],'image'=>$userimage,'bliss_amount'=>$user['bliss_amount'],'status'=>$user['status'],'user_id'=>$user['userid'],'device_id'=>$user['device_id']); 
                $this->user->update_customerdeviceid($user['id'],$device_id);
                 
            //$this->response($result, REST_Controller::HTTP_OK);
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'User login successful.',
                    'data' => $result
                ], REST_Controller::HTTP_OK);
            }else{
                // Insert user data
                $userData = array(
                'd_name' => $first_name,
                'email' => $email,
                'phone' => '',
                'status' => 'active',
                );
                $insert = $this->user->insert($userData);
                 $this->user->update_customerdeviceid($insert,$device_id);
                // Check if the user data is inserted
                if($insert){
                   $user = $this->user->loginwithemail($email);
             $userimage = '';
                     if($user['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/'.$user['image']; }else{$userimage = 'http://majemeyfarms.com/images/user/31.png';}
                     $result = array('id'=>$user['id'],'d_name'=>$user['d_name'],'f_name'=>$user['f_name'],'l_name'=>$user['l_name'],'customer_id'=>$user['customer_id'],'email'=>$user['email'],'phone'=>$user['phone'],'gender'=>$user['gender'],'dob'=>$user['dob'],'address'=>$user['address'],'city'=>$user['city'],'state'=>$user['state'],'pincode'=>$user['pincode'],'image'=>$userimage,'wallet'=>$user['bliss_amount'],'status'=>$user['status'],'user_id'=>$user['userid'],'device_id'=>$user['device_id']); 
                $this->user->update_customerdeviceid($user['id'],$device_id);
                 
            //$this->response($result, REST_Controller::HTTP_OK);
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'User login successful.',
                    'data' => $result
                ], REST_Controller::HTTP_OK);
                }else{
                    // Set the response and exit
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Some problems occurred, please try again.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);   
                }
            }
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    public function category_get() {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.

        $users = $this->user->category_all();
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                     
                      if($val['image']!='') { $userimage = 'http://majemeyfarms.com/main-admin/images/category/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     $result[] = array('id'=>$val['id'],'name'=>$val['c_name'],'image'=>$userimage);
                 }
            $this->response($result, REST_Controller::HTTP_OK);
            
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
                $this->response([
                'status' => FALSE,
                'message' => 'No Category was found.'
            ], REST_Controller::HTTP_OK);
        }
    }

    public function getallvideossearch_post(){
        
        $user_id = strip_tags($this->post('search'));
        $type = strip_tags($this->post('type'));
        $id = strip_tags($this->post('userid'));
        $result = array();
    
    if($type=='video'){
        $videos = $this->user->get_all_video_by_search($user_id);
        if(!empty($videos)) { 
        foreach($videos as $val) {
            $video = '';
                     if($val['video']!='') { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; }
                     $video_thumb = '';
                     if($val['video_thumb']!='') { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
       $result[] = array('id'=>$val['id'],'title'=>mb_substr($val['title'],0,15),'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'date'=>$val['date'],'video'=>$video,'video_thumb'=>$video_thumb,'image'=>$userimage,'user_id'=>$val['user_id'],'uid'=>$val['custid'],'description'=>$val['description'],'contest_id'=>0);
        } 
        
            //$this->response($result, REST_Controller::HTTP_OK);
			$this->response([
                'status' => TRUE,
                'message' => 'Record found.',
                'data' => $result
            ], REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
                $this->response([
                'status' => FALSE,
                'message' => 'No record found.',
				'data' => ''
            ], REST_Controller::HTTP_OK);
        }
        
    }
    }
    public function contest_get($type = 'null') {
         $con = array('type'=>$type);
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.

        $users = $this->user->contest_all($con);
        // Check if the user data exists
        if(!empty($users)){

            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $thumb_image = '';
                     if($val['thum_image']!='') { $thumb_image = 'http://majemeyfarms.com/main-admin/images/contest/'.$val['thum_image']; }

                     $big_image = '';
                     if($val['image']!='') { $big_image = 'http://majemeyfarms.com/main-admin/images/contest/'.$val['image']; }
                     
                     $result[] = array('id'=>$val['id'],'name'=>$val['c_name'],'thumb_image'=>$thumb_image,'big_image'=>$big_image,'status'=>$val['status'],'type'=>$val['type'],'last_date'=>$val['last_date'],'contest_id'=>'0');
                 }
            $this->response($result, REST_Controller::HTTP_OK);
            
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No Contast found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function scoreboard_get($id = 0,$type='') {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        //
        $users = $this->user->getallscoreboardid($id,$type);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
             $this->response($users, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }
    public function scoreboardsearch_get($keyword='',$id = 0,$type='') {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        //
        $users = $this->user->live_scoreboard_all($id,$type);
        // Check if the user data exists
        if(!empty($users)){
            $result = array();
            $i = 1;
            foreach($users as $user) {
                
                if(strstr(strtolower($user['d_name']),strtolower($keyword)) !== false) {
                //if(strpos($user['d_name'],$keyword) !== false) {
                    $result[] = array('rank'=>$i,'id'=>$user['id'],'d_name'=>$user['d_name'],'customer_id'=>$user['customer_id'],'city'=>$user['city'],'state'=>$user['state'],'total_votes'=>$user['total_votes'],'total_points'=>$user['total_points'],'total_count'=>$user['total_count']);
                    
                }
                $i++;
            }
            // Set the response and exit
            //OK (200) being the HTTP response code
             $this->response($result, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }
    public function videobycategory_get($id = 0, $userid = 0, $limit = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        
        
        $con = $id?array('vid' => 0,'id' => $id,'userid'=>$userid,'loadmore'=>$limit):'';

        $users = $this->user->getvideo($con);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            
            $result = array();
                 foreach($users as $val) {
                      $video = '';
                     if($val['video']!='') { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; }
                     $video_thumb = '';
                     if($val['video_thumb']!='') { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     
                     $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     
                     $result[] = array('id'=>$val['id'],'vid'=>$val['v_id'],'title'=>mb_substr($val['title'],0,15),'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'date'=>$val['date'],'video'=>$video,'video_thumb'=>$video_thumb,'image'=>$userimage,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest_id'],'liked'=>$val['lid'],'voted'=>$val['gid']); 
                 }
            $this->response($result, REST_Controller::HTTP_OK);
            
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }
    public function videobycategorybyid_post($id = 0, $userid = 0, $limit = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        
        $userid = strip_tags($this->post('user_id'));
        $video_id = strip_tags($this->post('videoid'));
        $loadmore = strip_tags($this->post('loadmore'));
        //die();

        $con = $userid?array('id' => $video_id,'userid'=>$userid):'';
        $users = $this->user->getvideo_by_id($con);

        $con = $video_id?array('vid' => $video_id,'id' => $users[0]['category'],'userid'=>$userid,'loadmore'=>$loadmore):'';
        $users1 = $this->user->getvideo($con);
        if($limit==0){
        $users = array_merge($users,$users1);
        }else{
        $users = $users1;   
        }

        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            
            $result = array();
                 foreach($users as $val) {
                      $video = '';
                     //if($val['video']!='') { $video = 'https://s3.ap-south-1.amazonaws.com/mth.video.content/videos/'.$val['video']; }
                     if($val['video']!='') { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; }
                     $video_thumb = '';
                     if($val['video_thumb']!='') { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     
                     $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     
                     $result[] = array('id'=>$val['id'],'vid'=>$val['v_id'],'title'=>mb_substr($val['title'],0,15),'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'user_id'=>$val['user_id'],'uid'=>$val['custid'],'date'=>$val['date'],'video'=>$video,'video_thumb'=>$video_thumb,'image'=>$userimage,'description'=>$val['description'],'contest_id'=>0,'liked'=>$val['lid'],'voted'=>$val['gid']); 
                 }
            $this->response($result, REST_Controller::HTTP_OK);
            
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }
    public function imagesplash_get($id = 0, $userid = 0,$limit = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
    //  $con = $id?array('id' => $id,'userid'=>$userid):'';
       /* $userid = strip_tags($this->post('user_id'));
        $video_id = strip_tags($this->post('videoid'));
        $loadmore = strip_tags($this->post('loadmore'));*/
        $con=array('id' => $id,'userid'=>$userid,'loadmore'=>$limit);
        $users = $this->user->imagesplash($con);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            
            
            $result = array();
                 foreach($users as $val) {
                      $video = '';
                     if($val['image']!='') { $video = 'http://majemeyfarms.com/assets/gallery/'.$val['image']; }
                     $video_thumb = '';
                     if($val['image']!='') { $video_thumb = 'http://majemeyfarms.com/assets/gallery/thumbs/'.$val['image']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     
                     $userimage = '';
                     if($val['cimage']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['cimage']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     
                    // $result[] = array('id'=>$val['id'],'title'=>substr($val['title'],0,15),'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'date'=>$val['date'],'image'=>$video,'image_thumb'=>$video_thumb,'uimage'=>$userimage,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest_id'],'liked'=>$val['lid'],'voted'=>$val['gid']); 

                     $result[] = array('id'=>$val['id'],'title'=>$val['title'],'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'date'=>$val['date'],'image'=>$video,'image_thumb'=>$video_thumb,'uimage'=>$userimage,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest'],'liked'=>$val['lid'],'voted'=>$val['gid']); 
                 }
            $this->response($result, REST_Controller::HTTP_OK);
            
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }

    public function imagesplashbyid_post() {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
    //  $con = $id?array('id' => $id,'userid'=>$userid):'';
        $userid = strip_tags($this->post('user_id'));
        $video_id = strip_tags($this->post('videoid'));
        $loadmore = strip_tags($this->post('loadmore'));

         $con=array('id' => $video_id,'userid'=>$userid);
        $users = $this->user->imagesplash_by_id($con);
        //print_r($this->post()); die();
        $con=array('id' => $video_id,'userid'=>$userid,'loadmore'=>$loadmore);
        $users1 = $this->user->imagesplash($con);

        if(!empty($users)) {
            $users = array_merge($users,$users1);
        } else {
            $users = $users1; 
        }

        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            
            
                 $result = array();
                 foreach($users as $val) {
                      $video = '';
                     if($val['image']!='') { $video = 'http://majemeyfarms.com/assets/gallery/'.$val['image']; }
                     $video_thumb = '';
                     if($val['image']!='') { $video_thumb = 'http://majemeyfarms.com/assets/gallery/thumbs/'.$val['image']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     
                     $userimage = '';
                     if($val['cimage']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['cimage']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     
                    // $result[] = array('id'=>$val['id'],'title'=>substr($val['title'],0,15),'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'date'=>$val['date'],'image'=>$video,'image_thumb'=>$video_thumb,'uimage'=>$userimage,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest_id'],'liked'=>$val['lid'],'voted'=>$val['gid']); 

                     $result[] = array('id'=>$val['id'],'title'=>$val['title'],'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'date'=>$val['date'],'image'=>$video,'image_thumb'=>$video_thumb,'uimage'=>$userimage,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest'],'liked'=>$val['lid'],'voted'=>$val['gid']); 
                 }
            $this->response($result, REST_Controller::HTTP_OK);
            
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }

    public function becomepartner_post() {
        // Get the post data
       
        $customer_id = str_replace(' ','',$this->post('customer_id'));
        $id = strip_tags($this->post('id'));
        

       $users = $this->user->select_manual('customer',array('customer_id'=>$customer_id),array('consume >'=>0));



       if(!empty($users)) {

       if(!empty($this->post('customer_id'))){
                // Insert user data
                $userData = array(
                'direct_customer_id' => $customer_id
              
                );
                 $this->user->update_manual('customer',array('id'=>$id), $userData);
                
                
                // Check if the user data is inserted
            
                    
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Data added successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
                    
                    
                
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Add some data.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
            
        }
        } else {
            $this->response([
                    'status' => FALSE,
                    'message' => 'Sponsor ID is not exist.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
        }
    }


    public function contestbyid_get($id = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
    //  $con = $id?array('id' => $id,'userid'=>$userid):'';
        $con=array('id' => $id);
        $users = $this->user->get_contest_all($con);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
                $result = array();
                 foreach($users as $val) {
                      $thumb_image = '';
                     if($val['thum_image']!='') { $thumb_image = 'http://majemeyfarms.com/main-admin/images/contest/'.$val['thum_image']; }

                     $big_image = '';
                     if($val['image']!='') { $big_image = 'http://majemeyfarms.com/main-admin/images/contest/'.$val['image']; }
                     
                     $result[] = array('id'=>$val['id'],'name'=>$val['c_name'],'thumb_image'=>$thumb_image,'big_image'=>$big_image,'status'=>$val['status'],'type'=>$val['type'],'last_date'=>$val['last_date']);
                 }
            $this->response($result, REST_Controller::HTTP_OK);
            
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }
     public function primefee_get() {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
    //  $con = $id?array('id' => $id,'userid'=>$userid):'';
        
        $users = $this->user->select_manual('prime_fee');

        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
                $result = $users;
                 
            $this->response($result, REST_Controller::HTTP_OK);
            
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }

    public function imagesplashbyid_get_old($id = 0, $userid = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
    //  $con = $id?array('id' => $id,'userid'=>$userid):'';
        $con=array('id' => $id,'userid'=>$userid);
        $users = $this->user->imagesplash_by_id($con);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            
            
            $result = array();
                 foreach($users as $val) {
                      $video = '';
                     if($val['image']!='') { $video = 'http://majemeyfarms.com/assets/gallery/'.$val['image']; }
                     $video_thumb = '';
                     if($val['image']!='') { $video_thumb = 'http://majemeyfarms.com/assets/gallery/thumbs/'.$val['image']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     
                     $userimage = '';
                     if($val['cimage']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['cimage']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     
                     $result[] = array('id'=>$val['id'],'title'=>substr($val['title'],0,15),'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'date'=>$val['date'],'image'=>$video,'image_thumb'=>$video_thumb,'uimage'=>$userimage,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest_id'],'liked'=>$val['lid'],'voted'=>$val['gid']); 
                 }
            $this->response($result, REST_Controller::HTTP_OK);
            
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }
    public function user_get($id = 0, $userid = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id,'userid'=>$userid):'';
        $users = $this->user->getuserdata($con);
        $usersvideo = $this->user->uservideobyid($con);
        $total_video =0;
        $total_video = $this->user->count_manual('videos','id',array('user_id'=>$id));
		
       
        if(!empty($users)){
			
			
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                    if($val['frstatus']==1 && $val['req_status']==1) {
                        $status = 3;
                    } 
                    elseif($val['frstatus']==1 && $val['req_status']==0) {
                        if($val['user_id']==$userid) {
                            $status = 1;
                        }
                        else {
                            $status = 2;
                        }
                        


                    }  else {
                            $status = 0;
                     }
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                    
                     $result[] = array('id'=>$val['id'],'d_name'=>$val['d_name'],'f_name'=>$val['f_name'],'l_name'=>$val['l_name'],'email'=>$val['email'],'phone'=>$val['phone'],'direct_customer_id'=>$val['direct_customer_id'],'gender'=>$val['gender'],'dob'=>$val['dob'],'city'=>$val['city'],'state'=>$val['state'],'image'=>$userimage,'incentive'=>$val['bliss_amount'],'total_video'=>$total_video,'followings'=>$val['followings'],'followers'=>$val['followers'],'myfriends'=>$val['friends'],'friendrequest'=>$val['friends_request'],'link_count'=>$val['link_count'],'views'=>$val['views'],'is_follow'=>$val['is_follow'],'is_friend'=>$status); 
                 }
                 
                 $videos = array();
                 foreach($usersvideo as $val) {
                      $video = '';
                     if($val['video']!='') { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; }
                     
                      $video_thumb = '';
                     if($val['video_thumb']!='') { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                   
                     
                     $videos[] = array('id'=>$val['id'],'vid'=>$val['v_id'],'title'=>mb_substr($val['title'],0,15),'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'user_id'=>$val['user_id'],'uid'=>$val['usrid'],'date'=>$val['date'],'video'=>$video,'video_thumb'=>$video_thumb,'image'=>0,'description'=>$val['description'],'contest_id'=>0,'liked'=>$val['lid'],'voted'=>$val['gid']); 
                     
                     
                 }
                 
                   
                //print_r($videos);
                 
            $this->response(['userdata'=>$result,'uservideo'=>$videos],REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function video_get($id = 0, $userid = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id,'userid'=>$userid):'';
         $users = $this->user->getvideobyid($con);
         $usersvideo = $this->user->getrecomandedvideo();
         $userscomment = $this->user->getvideocomment($con);
         $result = array();
                 foreach($users as $val) {
                      $video = '';
                     if($val['video']!='') { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; }
                     
                     $video_thumb = '';
                     if($val['video_thumb']!='') { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     
                     $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                    
                     $result[] = array('id'=>$val['id'],'title'=>substr($val['title'],0,15),'views'=>$val['views'],'votes'=>$val['votes'],'likes'=>$val['likes'],'user_name'=>$val['d_name'],'user_id'=>$val['userid'],'date'=>$val['date'],'video'=>$video,'video_thumb'=>$video_thumb,'image'=>$userimage,'followers'=>$val['followers'],'user_id'=>$val['user_id'],'description'=>$val['description'],'liked'=>$val['lid'],'voted'=>$val['votid']); 
                 }
                 
                  $videos = array();
                 foreach($usersvideo as $val) {
                      $video = '';
                     if($val['video']!='') { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; }
                     
                      $video_thumb = '';
                     if($val['video_thumb']!='') { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     
                     $videos[] = array('id'=>$val['id'],'title'=>substr($val['title'],0,15),'status'=>$val['status'],'views'=>$val['views'],'votes'=>$val['votes'],'likes'=>$val['likes'],'user_name'=>$val['d_name'],'date'=>$val['date'],'video'=>$video,'video_thumb'=>$video_thumb,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest_id']); 
                 }
                 
                 
                   $comment = array();
                 foreach($userscomment as $val) {
                     
                     $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     
                     $comment[] = array('id'=>$val['u_id'],'comment'=>$val['comment'],'user_name'=>$val['d_name'],'c_date'=>$val['c_date'],'image'=>$userimage); 
                 }
                 
                 
            $this->response(['videodetail'=>$result,'othervideo'=>$videos,'comment'=>$comment], REST_Controller::HTTP_OK);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
           
            $this->response($users, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No Job was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function uservideos_get($id = 0, $type = 0, $prime=0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id,'type'=>$type,'prime'=>$prime):'';
        $users = $this->user->uservideobytype($con);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $video = '';
                     if($val['video']!='') { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; }
                     
                      $video_thumb = '';
                     if($val['video_thumb']!='') { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }$result[] = array('id'=>$val['id'],'title'=>substr($val['title'],0,25),'status'=>$val['status'],'views'=>$val['views'],'votes'=>$val['votes'],'likes'=>$val['likes'],'user_name'=>$val['d_name'],'date'=>$val['date'],'video'=>$video,'video_thumb'=>$video_thumb,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest_id']); 
                 }
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No Job was found.',
                'data' => array(),
            ], REST_Controller::HTTP_OK);
        }
    }
    public function rank_get($id = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $users = $this->user->getuserpdata($id);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            if($users[0]['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$users[0]['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
            $this->response([
                'status' => TRUE,
                'rank' => $users[0]['bsacode'],
                'name' => $users[0]['d_name'],
                'image' => $userimage,
                'data' => array(),
            ], REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'rank' => 'No data was found.',
                'data' => array(),
            ], REST_Controller::HTTP_OK);
        }
    }
    public function userimages_get($id = 0, $type = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id,'type'=>$type):'';
        $users = $this->user->userimagesbytype($con);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $video = '';
                     if($val['image']!='') { $video = 'http://majemeyfarms.com/assets/gallery/'.$val['image']; }
                     $video_thumb = '';
                     if($val['image']!='') { $video_thumb = 'http://majemeyfarms.com/assets/gallery/thumbs/'.$val['image']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     $result[] = array('id'=>$val['id'],'title'=>substr($val['title'],0,15),'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'date'=>$val['date'],'image'=>$video,'image_thumb'=>$video_thumb,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest_id'],'liked'=>'0','voted'=>'0'); 
                 }
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No images was found.',
                'data' => array(),
            ], REST_Controller::HTTP_OK);
        }
    }
    public function followers_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):'';
        $users = $this->user->getuserfollowers($con);
         
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                    
                     $result[] = array('id'=>$val['id'],'uid'=>$val['user_id'],'fid'=>$val['follow_id'],'fcount'=>$val['fcount'],'d_name'=>$val['d_name'],'image'=>$userimage); 
                 }
                 
            $this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No data found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function followings_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):'';
        $users = $this->user->getuserfollowings($con);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                    
                     $result[] = array('id'=>$val['id'],'uid'=>$val['user_id'],'fid'=>$val['follow_id'],'d_name'=>$val['d_name'],'image'=>$userimage,'fcount'=>'0'); 
                 }
                 
            $this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No data found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function myfriends_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
       $frnds = $this->user->my_friends($id);
       $frnds_second = $this->user->my_friends_second($id);
       $users = array_merge($frnds,$frnds_second);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     $result[] = array('id'=>$val['id'],'uid'=>$val['user_id'],'fid'=>$val['friend_id'],'d_name'=>$val['d_name'],'image'=>$userimage,'fcount'=>'0'); 
                 }
            $this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No data found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function friendrequest_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):'';
        $users = $this->user->my_friend_request($id);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                    
                     $result[] = array('id'=>$val['id'],'uid'=>$val['user_id'],'fid'=>$val['friend_id'],'d_name'=>$val['d_name'],'image'=>$userimage,'fcount'=>'0'); 
                 }
                 
            $this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No data found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function certificate_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):'';
        $users = $this->user->getusercertificate($con);
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      
                  $userimage = 'http://majemeyfarms.com/assets/front/images/certificate-small.jpg';
                    $url= 'http://majemeyfarms.com/images/user/certificate/'.$val['image'];
                     $result[] = array('id'=>$val['id'],'star'=>$val['star_rank'],'url'=>$url,'image'=>$userimage); 
                 }
                 
            $this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No data found.'
            ], REST_Controller::HTTP_OK);
        }
    }
    public function voteunvote_post() {
        // Get the post data
        $user_id = strip_tags($this->post('user_id'));
        $video_id = strip_tags($this->post('video_id'));
         $type = strip_tags($this->post('type'));
        
        $where['user_id'] = $user_id;
        $where2['vote_id'] = $video_id;
        
        // Validate the post data
        if(!empty($user_id) && !empty($video_id) && !empty($type) && $type=='video'){
             
             
            $vote_status = $this->user->select_manual('votes',$where,$where2);
        if(empty($vote_status)) {
            $this->user->insert_manual('votes',array('user_id'=>$user_id,'vote_id'=>$video_id));
            $this->user->update_vote_counter('videos',$video_id,1,'votes','+'); 
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Vote Added Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->user->delete_manual('votes',array('id'=>$vote_status[0]['id']));
            $this->user->update_vote_counter('videos',$video_id,1,'votes','-');
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Unvote Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        }
        }
        elseif(!empty($user_id) && !empty($video_id) && !empty($type) && $type=='image'){
             
             
            $vote_status = $this->user->select_manual('images_votes',$where,$where2);
        if(empty($vote_status)) {
            $this->user->insert_manual('images_votes',array('user_id'=>$user_id,'vote_id'=>$video_id));
            $this->user->update_vote_counter('gallery',$video_id,1,'votes','+'); 
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Vote Added Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->user->delete_manual('images_votes',array('id'=>$vote_status[0]['id']));
            $this->user->update_vote_counter('gallery',$video_id,1,'votes','-');
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Unvote Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        }
        }
        else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    public function likeunlike_post() {
        // Get the post data
        $user_id = strip_tags($this->post('user_id'));
        $video_id = strip_tags($this->post('like_id'));
        $type = strip_tags($this->post('type'));
        
        $where['user_id'] = $user_id;
        $where2['like_id'] = $video_id;
        
        // Validate the post data
        if(!empty($user_id) && !empty($video_id) && !empty($type) && $type=='video'){
             
             
            $like_status = $this->user->select_manual('likes',$where,$where2);
            $video_data = $this->user->select_manual('videos',array('id'=>$video_id));
        if(empty($like_status)) {
			
            $this->user->insert_manual('likes',array('user_id'=>$user_id,'like_id'=>$video_id));
            $this->user->update_vote_counter('videos',$video_id,1,'likes','+'); 
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Like Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->user->delete_manual('likes',array('id'=>$like_status[0]['id']));
            $this->user->update_vote_counter('videos',$video_id,1,'likes','-');
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Unlike Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        }
        }
        elseif(!empty($user_id) && !empty($video_id) && !empty($type) && $type=='image'){
             
             
            $like_status = $this->user->select_manual('images_likes',$where,$where2);
            $video_data = $this->user->select_manual('gallery',array('id'=>$video_id));
        if(empty($like_status)) {
            $this->user->insert_manual('images_likes',array('user_id'=>$user_id,'like_id'=>$video_id));
            $this->user->update_vote_counter('gallery',$video_id,1,'likes','+'); 
           
            $this->response([
                        'status' => TRUE,
                        'message' => 'Like Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->user->delete_manual('images_likes',array('id'=>$like_status[0]['id']));
            $this->user->update_vote_counter('gallery',$video_id,1,'likes','-');
            $this->response([
                        'status' => TRUE,
                        'message' => 'Unlike Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        }
        }
        elseif(!empty($user_id) && !empty($video_id) && !empty($type) && $type=='porch'){
             
             
            $like_status = $this->user->select_manual('post_likes',$where,$where2);
            $video_data = $this->user->select_manual('timeline_post',array('id'=>$video_id));
        if(empty($like_status)) {
            $this->user->insert_manual('post_likes',array('user_id'=>$user_id,'like_id'=>$video_id));
            $this->user->update_vote_counter('timeline_post',$video_id,1,'likes','+'); 
           
            $this->response([
                        'status' => TRUE,
                        'message' => 'Like Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->user->delete_manual('post_likes',array('id'=>$like_status[0]['id']));
            $this->user->update_vote_counter('timeline_post',$video_id,1,'likes','-');
         
            $this->response([
                        'status' => TRUE,
                        'message' => 'Unlike Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        }
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    public function followunfollow_post() {
        // Get the post data
        $user_id = strip_tags($this->post('user_id'));
        $follow_id = strip_tags($this->post('follow_id'));
        
        $where['user_id'] = $user_id;
        $where2['follow_id'] = $follow_id;
        
        // Validate the post data
        if(!empty($user_id) && !empty($follow_id)){
             
             
            $follow_status = $this->user->select_manual('followers',$where,$where2);
            if(empty($follow_status)) {
            $this->user->insert_manual('followers',array('user_id'=>$user_id,'follow_id'=>$follow_id));
            $this->user->update_counter($follow_id,1,'followers','+');
            $this->user->update_counter($user_id,1,'followings','+');
        
			$data_to_store = array(
            'user_id'=>$follow_id,
            'type'=>'Follow',
            'user_id_by'=>$user_id,
            );
            $this->user->insert_manual('notifications',$data_to_store);
                
            $this->response([
                        'status' => TRUE,
                        'message' => 'Follow Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->user->delete_manual('followers',array('id'=>$follow_status[0]['id']));
            $this->user->update_counter($follow_id,1,'followers','-');
            $this->user->update_counter($user_id,1,'followings','-');
			$this->user->delete_manual_all('notifications',$user_id,$follow_id,'Follow');
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Unfollow Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        }
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }

    public function followunfollowback_post() {
        // Get the post data
        $follow_id = strip_tags($this->post('user_id'));
        $user_id = strip_tags($this->post('follow_id'));
        
        $where['user_id'] = $user_id;
        $where2['follow_id'] = $follow_id;
        
        // Validate the post data
        if(!empty($user_id) && !empty($follow_id)){
             
             
            $follow_status = $this->user->select_manual('followers',$where,$where2);
            if(empty($follow_status)) {
            $this->user->insert_manual('followers',array('user_id'=>$user_id,'follow_id'=>$follow_id));
            $this->user->update_counter($follow_id,1,'followers','+');
            $this->user->update_counter($user_id,1,'followings','+');
			$data_to_store = array(
            'user_id'=>$follow_id,
            'type'=>'Follow',
            'user_id_by'=>$user_id,
            );
            $this->user->insert_manual('notifications',$data_to_store);
            $this->response([
                        'status' => TRUE,
                        'message' => 'Follow Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        }else{
            $this->user->delete_manual('followers',array('id'=>$follow_status[0]['id']));
            $this->user->update_counter($follow_id,1,'followers','-');
            $this->user->update_counter($user_id,1,'followings','-');
			$this->user->delete_manual_all('notifications',$user_id,$follow_id,'Follow');
            $this->response([
                        'status' => TRUE,
                        'message' => 'Unfollow Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        }
        }
		else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    public function friendunfriend_post() {
        // Get the post data
        $id = strip_tags($this->post('user_id'));
        $frnd_id = strip_tags($this->post('follow_id'));
        $type = strip_tags($this->post('type'));
        
        $where['user_id'] = $id;
        $where2['friend_id'] = $frnd_id;
        $where3['friend_id'] = $id;
        $where4['user_id'] = $frnd_id;
        
        $frnd_status = $this->user->get_frnds_list1($id,$frnd_id);
        //print_r($frnd_status); die();
        // Validate the post data
        if(empty($frnd_status)) { 
            $this->user->insert_manual('friends',array('user_id'=>$id,'friend_id'=>$frnd_id));
            $this->user->update_counter($frnd_id,1,'friends_request','+');
        $data_to_store = array(
            'user_id'=>$frnd_id,
            'type'=>'Friend Request',
            'user_id_by'=>$id,
            );
            $this->user->insert_manual('notifications',$data_to_store);
                
            $this->response([
                        'status' => TRUE,
                        'message' => 'Friend Request Sent',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        } 
        else{

            $this->user->delete_manual('friends',array('id'=>$frnd_status[0]['id']));
			$frqst='Friend Request';
			$this->user->delete_manual_all('notifications',$id,$frnd_id,$frqst);
            
            if($frnd_status[0]['status']==1) {
                $this->user->update_counter($frnd_status[0]['friend_id'],1,'friends','-');
                $this->user->update_counter($frnd_status[0]['user_id'],1,'friends','-');
            } else {
                $this->user->update_counter($frnd_status[0]['friend_id'],1,'friends_request','-');
            }
            
            $this->response([
                        'status' => TRUE,
                        'message' => 'Unfriend Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
            
        }
    }

    public function friendaccept_post() {
        // Get the post data
       /* $id = strip_tags($this->post('user_id'));
        $frnd_id = strip_tags($this->post('follow_id'));
        
        $where['id'] = $id;
        
        $frnd_status = $this->user->get_frnds_list1($id,$frnd_id);*/

         $id = strip_tags($this->post('id'));
        
        $where['id'] = $id;
        
        $frnd_status = $this->user->select_manual('friends',array('id'=>$id));

        // Validate the post data
        if($frnd_status[0]['status']==0) {
        /*print_r($frnd_status);
            die();*/
            $this->user->update_manual('friends',$where,array('status'=>1));
            $this->user->update_counter($frnd_status[0]['friend_id'],1,'friends','+');
            $this->user->update_counter($frnd_status[0]['user_id'],1,'friends','+');
            $this->user->update_counter($frnd_status[0]['friend_id'],1,'friends_request','-');
                
            $this->response([
                        'status' => TRUE,
                        'message' => 'Friend Request Accpeted',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
        } 
    }
    public function linkunlinkaccount_post(){
        $user_id = strip_tags($this->post('user_id'));
        $follow_id = strip_tags($this->post('link_id'));
        $status = $this->input->post('status'); 
        $where['user_id'] = $user_id;
        $where2['linked_id'] = $follow_id;
         if(!empty($user_id) && !empty($follow_id)){
        $follow_status = $this->user->select_manual('linked_account',$where,$where2);
        if(empty($follow_status)) {
            $this->user->insert_manual('linked_account',array('user_id'=>$user_id,'linked_id'=>$follow_id));
            $this->user->update_counter($follow_id,1,'link_count','+');
            $this->user->update_follow_wallet($follow_id,1,'bliss_amount','+');
            
            $data_to_store = array(
            'user_id'=>$follow_id,
            'type'=>'Link Profile',
            'user_id_by'=>$user_id,
            'amount'=>1,
            'status'=>'Credit'
            );
            $this->user->insert_manual('transaction_wallet',$data_to_store);
            $this->response([
                        'status' => TRUE,
                        'message' => 'Link Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
            
        } else {
            $this->user->delete_manual('linked_account',array('id'=>$follow_status[0]['id']));
            $this->user->update_counter($follow_id,1,'link_count','-');
            $this->user->update_follow_wallet($follow_id,1,'bliss_amount','-');
            $data_to_store = array(
            'user_id'=>$follow_id,
            'type'=>'Unlink Profile',
            'user_id_by'=>$user_id,
            'amount'=>1,
            'status'=>'Debit'
            );
            $this->user->insert_manual('transaction_wallet',$data_to_store);
            $this->response([
                        'status' => TRUE,
                        'message' => 'Unlink Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
            }
         } else {
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    public function videocomment_post(){    
    
     $user_id = strip_tags($this->post('user_id'));
    $video_id = strip_tags($this->post('video_id'));
    $comment = strip_tags($this->post('comment'));
    $type = strip_tags($this->post('type'));
    $user = $this->user->select_manual('customer',array('id'=>$user_id));
    if($user[0]['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$user[0]['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
    if(!empty($video_id) && !empty($user_id) && $type=='video'){
        $data = array(
                'v_id' => $video_id,
                'comment' => $comment,
                'u_id' => $user_id,  
                'c_date'=>time()                   
            );
          $insert_id = $this->user->insert_manual('comment',$data);
           $this->user->update_vote_counter('videos',$video_id,1,'comment','+'); 
           
          $this->response([
                        'status' => TRUE,
                        'message' => 'Add Successfully',
                        'data' => $userimage,
                        'name' => $user[0]['d_name']
                    ], REST_Controller::HTTP_OK);
    
    
     }
     elseif(!empty($video_id) && !empty($user_id) && $type=='porch'){
        $data = array(
                'post_id' => $video_id,
                'comment' => $comment,
                'u_id' => $user_id,  
                'commented_date'=>time()                 
            );
          $insert_id = $this->user->insert_manual('post_comments',$data);
           $this->user->update_vote_counter('timeline_post',$video_id,1,'comment','+'); 
          $this->response([
                        'status' => TRUE,
                        'message' => 'Add Successfully',
                        'data' => $userimage
                    ], REST_Controller::HTTP_OK);
    
    
     }
      elseif(!empty($video_id) && !empty($user_id) && $type=='image'){
        $data = array( 
                'v_id' => $video_id,
                'comment' => $comment,
                'u_id' => $user_id,
                'c_date'=>time()                   
            );
          $insert_id = $this->user->insert_manual('gallery_comment',$data);
           $this->user->update_vote_counter('gallery',$video_id,1,'comment','+'); 
          
          $this->response([
                        'status' => TRUE,
                        'message' => 'Add Successfully',
                        'data' => $userimage
                    ], REST_Controller::HTTP_OK);
    
    
     }
     else{
            // Set the response and exit
             $this->response([
                    'status' => FALSE,
                    'message' => 'Please provide all info',
                    'data' => ''
                ],REST_Controller::HTTP_BAD_REQUEST);
        }
        
    }
    public function videocomment_get($id = 0,$type='') {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):''; 
      
            $userscomment = $this->user->getvideocomment($con);
                   $comment = array();
                 foreach($userscomment as $val) {
                     
                     $userimage = '';
                    if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     
                     $comment[] = array('id'=>$val['id'],'comment'=>$val['comment'],'userid'=>$val['custid'],'user_name'=>$val['d_name'],'c_date'=>$val['c_date'],'image'=>$userimage); 
                 }
            
        // Check if the user data exists
        if(!empty($userscomment)){
            // Set the response and exit
            //OK (200) being the HTTP response code
           
            //$this->response($comment, REST_Controller::HTTP_OK);
            
            $this->response([
                'status' => TRUE,
                'message' => '',
                'data' => $comment
            ], REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No comment found.',
                'data' => ''
            ], REST_Controller::HTTP_OK);
        }
    }
    public function redeemrequest_post() {
        // Get the post data
        $user_id = strip_tags($this->post('user_id'));
        $redeem = strip_tags($this->post('redeem'));
        // Validate the post data
        if(!empty($user_id) && !empty($redeem)){
            
            $con['conditions'] = array(
                'id' => $user_id,
            );
            
           $profile = $this->user->getRows($con);
        
           if($profile[0]['bliss_amount'] < $this->input->post('redeem')) {
                $this->response([
                        'status' => TRUE,
                        'message' => 'Your redeem maximum Amount is '.$profile[0]['bliss_amount'].'',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
              
           }else{
              $data_to_store = array(
                    'balance' => $profile[0]['bliss_amount']-$this->input->post('redeem'),
                    'redeem' => $this->post('redeem'),
                    'my_bliss_req' => 'amount',
                    'user_id' => $user_id
                ); 
                
            $insert_id = $this->user->insert_manual('redeem_bliss ',$data_to_store);
             $this->user->bliss_amount_update($user_id,$this->input->post('redeem'),'bliss_amount');
             
              $this->response([
                        'status' => TRUE,
                        'message' => 'Redeem Request Added Successfully',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
             
           }
            
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    public function user_put() {
        $id = $this->put('id');
        // Get the post data
        $first_name = strip_tags($this->put('d_name'));
        $city = strip_tags($this->put('city'));
        $state = strip_tags($this->put('state'));
        $gender = strip_tags($this->put('gender'));
        $pincode = strip_tags($this->put('pincode'));
        $dob = strip_tags($this->put('dob'));
		$bio = strip_tags($this->put('bio'));
        // Validate the post data
        if(!empty($id)){
            // Update user's account data
            $userData = array();
            if(!empty($first_name)){
                $userData['d_name'] = $first_name;
            }
            if(!empty($city)){
                $userData['city'] = $city;
            }
            if(!empty($pincode)){
                $userData['pincode'] = $pincode;
            }
            if(!empty($dob)){
                $userData['dob'] = $dob;
            }
            if(!empty($gender)){
                $userData['gender'] = $gender;
            }
            
            if(!empty($state)){
                $userData['state'] = $state;
            }
			if(!empty($bio)){
                $userData['info'] = $bio;
            }
            
            $update = $this->user->update($userData, $id);
            
            // Check if the user data is updated
            if($update){
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'The user info has been updated successfully.'
                ], REST_Controller::HTTP_OK);
            }else{
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Some problems occurred, please try again.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            }
        }else{
            // Set the response and exit
            $this->response([
                    'status' => FALSE,
                    'message' => 'Provide at least one user info to update.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    public function uploadvideo_post() {
        // Get the post data
        $user_id = strip_tags($this->post('user_id')); 
         $title = trim($this->post('title'), '"');
         $category = trim($this->post('category'), '"');
         $description = trim($this->post('description'), '"');
         $type = $this->post('type');
		 if($type=='draft'){$status="Pending";}else{$status="Approved";}
        // Validate the post data
        if(!empty($user_id)){
        $this->load->helper(array('form', 'url'));

        $upload_path = 'assets/videos/';
        //$upload_path = 'images/video/';
        $config['upload_path'] = $upload_path;
        //$config['allowed_types'] = 'wmv|mp4|avi|mov|m4v|mov|hevc|qt';
        $config['allowed_types'] = '*';
        //$config['max_size'] = '40000';
       // $config['max_filename'] = '200';
		$config['encrypt_name'] = TRUE;
        
        $config['overwrite'] = FALSE;
        $video_data = array();
        $is_file_error = FALSE;
        
            if (!$is_file_error) {
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('video')) {
                   $this->response([
                        'status' => TRUE,
                        'message' => 'video not uploaded',
                        'data' => $this->upload->display_errors(),
                    ], REST_Controller::HTTP_OK);
                    $is_file_error = TRUE;
                } else {
                 $video_data = $this->upload->data();
                 
            $userData = array(
              'user_id' => $user_id,
              'title' => $title,
              'category' => $category,
              'video' => $video_data['file_name'],
              'description' => $description,
              'status' => $status,
                );
                $insert = $this->user->insert_video($userData);
                 //$update = $this->user->insert($userData, $user_id);
                 $path='http://majemeyfarms.com/assets/videos/'.$video_data['file_name'];
                  $this->response([
                        'status' => TRUE,
                        'message' => 'video uploaded successfully.',
                        'data' => $type,
                    ], REST_Controller::HTTP_OK);
                }
            }
            else
    {
        $error = array('error' => $this->upload->display_errors());
        $this->response([
                        'status' => TRUE,
                        'message' => 'video not uploaded',
                        'data' => $error,
                    ], REST_Controller::HTTP_OK);
    }
        
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    public function timelinepost_post() {
        // Get the post data
        $user_id = strip_tags($this->post('user_id'));
        // Validate the post data
        if(!empty($user_id)){
        $this->load->helper(array('form', 'url'));
        $userData = array(
              'user_id' => $user_id,
              'content' => $this->post('title')
                );
        if(!empty($_FILES)) {
        $filename = $_FILES['image']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if ($ext == 'wmv' || $ext == 'mp4' || $ext == 'avi'|| $ext == 'mov'|| $ext == 'm4v'|| $ext == 'hevc' || $ext == 'qt') {
        $upload_path = 'assets/videos/';
        //$upload_path = 'images/video/';
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'wmv|mp4|avi|mov|m4v|mov|hevc|qt|';
        $config['max_size'] = '40000';
        $config['max_filename'] = '200';
        $config['encrypt_name'] = TRUE;
        $config['overwrite'] = FALSE;
        $image_data = array();
        $is_file_error = FALSE;
        
            if (!$is_file_error) {
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('image')) {
                   $this->response([
                        'status' => TRUE,
                        'message' => 'video not uploaded',
                        'data' => $this->upload->display_errors(),
                    ], REST_Controller::HTTP_OK);
                    $is_file_error = TRUE;
                } else {
                 $image_data = $this->upload->data();
                 //$update = $this->user->insert($userData, $user_id);
                 $path='http://majemeyfarms.com/assets/videos/'.$image_data['file_name'];
                  $userData['video'] = $image_data['file_name'];
                }
            }

        }

       if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg') {


    $upload_path = 'assets/timeline/post/';
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['max_size'] = '4000';
        //$config['max_filename'] = '200';
        $config['encrypt_name'] = TRUE;
        $config['overwrite'] = FALSE;
        $video_data = array();
        $is_file_error = FALSE;
        
            if (!$is_file_error) {
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('image')) {
                   $error = array('error' => $this->upload->display_errors());
                  $this->response([
                        'status' => TRUE,
                        'message' => 'Image not uploaded',
                        'data' => $this->upload->display_errors(),
                    ], REST_Controller::HTTP_OK);
                    $is_file_error = TRUE;
                } else {
                 $video_data = $this->upload->data();
                 $path='http://majemeyfarms.com/assets/gallery/'.$video_data['file_name'];
                  $userData['image'] = $video_data['file_name'];
                  $userData['rdate'] = time();

                }
            }
         }
        }
        $this->user->insert_manual('timeline_post',$userData);
        $this->response([
                        'status' => TRUE,
                        'message' => 'Post Updated successfully.'
                    ], REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    public function uploadimagesplash_post() {
        // Get the post data
        $user_id = strip_tags($this->post('user_id'));
		 $title = trim($this->post('title'), '"');
         $description = trim($this->post('description'), '"');
        // Validate the post data
        if(!empty($user_id)){
        $this->load->helper(array('form', 'url'));
        
        $upload_path = 'assets/gallery/';
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|png|jpeg';
       // $config['max_size'] = '4000';
        //$config['max_filename'] = '200';
        $config['encrypt_name'] = TRUE;
        $config['overwrite'] = FALSE;
        $video_data = array();
        $is_file_error = FALSE;
        



            if (!$is_file_error) {
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('image')) {
                   $error = array('error' => $this->upload->display_errors());
                  $this->response([
                        'status' => TRUE,
                        'message' => 'Image not uploaded',
                        'data' => $this->upload->display_errors(),
                    ], REST_Controller::HTTP_OK);
                    $is_file_error = TRUE;
                } else {
                 $video_data = $this->upload->data();

                $data = array('upload_data' => $this->upload->data());
                $file_name = $data['upload_data']['file_name'];
                $target_path = 'assets/gallery/thumbs/';     
                /*$imgConfig = array();                       
                $imgConfig['image_library'] = 'GD2';                        
                $imgConfig['source_image']  = './assets/gallery/'.$file_name;
                $imgConfig['wm_type']   = 'overlay';                    
                $imgConfig['wm_overlay_path'] = './assets/front/images/Watermark.png';
                 $imgConfig['wm_vrt_alignment'] = 'bottom';
                $imgConfig['wm_hor_alignment'] = 'left';
                $imgConfig['wm_opacity'] = '50';
                
                $this->load->library('image_lib', $imgConfig);                      
                $this->image_lib->initialize($imgConfig);                       
                $this->image_lib->watermark();  */
                
                 $q['name']=$data['upload_data']['file_name'];
                 $configi['image_library'] = 'gd2';
                 $configi['source_image']   = './assets/gallery/'.$file_name;
                 $configi['new_image']   = $target_path;
                 $configi['maintain_ratio'] = TRUE;
                 $config['quality'] = '60%';
                 $configi['width']  = 500; // new size
                 $configi['height'] = 500;
                $this->load->library('image_lib');
                $this->image_lib->initialize($configi);    
                $this->image_lib->resize();
                



                $userData = array(
              'user_id' => $user_id,
              'title' => $title,
              'image' => $video_data['file_name'],
              'description' => $description,
                );
                $insert = $this->user->insert_imagesplash($userData);
                 $path='http://majemeyfarms.com/assets/gallery/'.$video_data['file_name'];
                  $this->response([
                        'status' => TRUE,
                        'message' => 'Image uploaded successfully.',
                        'data' => $path,
                    ], REST_Controller::HTTP_OK);
                }
            }
            else
    {
        $error = array('error' => $this->upload->display_errors());
       $this->response([
                        'status' => TRUE,
                        'message' => 'Image not uploaded',
                        'data' => $this->upload->display_errors(),
                    ], REST_Controller::HTTP_OK);
    }
        
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    } 
   
   public function uploadimage_post() {
        // Get the post data
        $user_id = strip_tags($this->post('user_id'));
        // Validate the post data
        if(!empty($user_id)){
    $this->load->helper(array('form', 'url'));
        $config = array(
        'upload_path' => "images/user/profile_pick/",
        //'allowed_types' => "jpg|png|jpeg",
        'allowed_types' => "*",
        'overwrite' => FALSE,
        'encrypt_name' => TRUE,
      //  'max_size' => "1000",
        //'max_height' => "768",
        //'max_width' => "1024"
    );
    $this->load->library('upload',$config);
	

    if($this->upload->do_upload('image'))
    {
        $data = array('upload_data' => $this->upload->data());
         $userData['image'] = $data['upload_data']['file_name'];
         $update = $this->user->update($userData, $user_id);
       
		 if(!empty($user_id)){
			 $imgname=$data['upload_data']['file_name'];
	   $resize= $this->_create_thumbs($imgname);
       }
		 
		 
         $path='https://www.majemeyfarms.com/images/user/profile_pick/'.$data['upload_data']['file_name'];
        
        //$this->set_response($data,'imagepath' => 'http://saraogroup.33demo.com/images/career-cv/'.$path.'', REST_Controller::HTTP_CREATED);
        
         $this->response([
                        'status' => TRUE,
                        'message' => 'Profile uploaded successfully.',
                        'resize' => $resize,
                        'data' => $path,
                    ], REST_Controller::HTTP_OK);
    }
    else
    {
        $error = array('error' => $this->upload->display_errors());
        $this->response($error, REST_Controller::HTTP_BAD_REQUEST);
    }
            
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
            
        }
    }
    
   public function uploadbanner_post() {
        // Get the post data
        //$user_id = '2';
        $user_id = $this->post('user_id');
        // Validate the post data
        if(!empty($user_id)){
            
         // echo 'rahul'; die();
    $this->load->helper(array('form', 'url'));
        $config = array(
        'upload_path' => "images/user/",
        'allowed_types' => "jpg|png|jpeg",
        'overwrite' => FALSE,
        'encrypt_name' => TRUE,
       // 'max_size' => "1000",
        //'max_height' => "768",
        //'max_width' => "1024"
    );
    $this->load->library('upload',$config);

    if($this->upload->do_upload('image'))
    {
        $data = array('upload_data' => $this->upload->data());
        
         $userData['cover_pic'] = $data['upload_data']['file_name'];
         $update = $this->user->update($userData, $user_id);
         
         $path='http://majemeyfarms.com/images/user/'.$data['upload_data']['file_name'];
        
        //$this->set_response($data,'imagepath' => 'http://saraogroup.33demo.com/images/career-cv/'.$path.'', REST_Controller::HTTP_CREATED);
        
         $this->response([
                        'status' => TRUE,
                        'message' => 'Banner uploaded successfully.',
                        'data' => $path,
                    ], REST_Controller::HTTP_OK);
    }
    else
    {
        $error = array('error' => $this->upload->display_errors());
        $this->response($error, REST_Controller::HTTP_OK);
    }
            
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
        }
    }   
    public function socialporch_get($id = 0, $userid = 0, $limit = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
         $con = $id?array('id' => $id,'userid'=>$userid,'limit'=>$limit):'';
        
         $friends = $this->user->get_user_friend_ids($userid); 
         $friends_second = $this->user->get_user_friend_ids_second($userid);
         $friends = array_merge($friends,$friends_second);
         $following = $this->user->my_following_list($userid);
         
         $user_ids = array($id);
         if(!empty($friends)) {
             foreach($friends as $friend){
                 $user_ids[] = $friend['friend_id'];
             }
         }
         if(!empty($following)) {
             foreach($following as $friend){
             $user_ids[] = $friend['follow_id'];
             }
         }
        $timeline = $this->user->get_timeline_post($user_ids,$userid,$limit);
        
        // Check if the user data exists
        if(!empty($timeline)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($timeline as $val) {
                      $userimage = '';
                     if($val['userimage']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['userimage']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     $image='';
                     $video='';
                     $type='';
                     
                     if(!empty($val['image'])) { $video = 'http://majemeyfarms.com/assets/timeline/post/'.$val['image'];$type='image'; }
                     
                     if(!empty($val['video'])) { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; $type='video';}
                     
                     if(!empty($val['video_thumb'])) { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{$video_thumb = '';}
                     
                    
                     $result[] = array('id'=>$val['id'],'user_id'=>$val['user_id'],'f_name'=>$val['f_name'],'l_name'=>$val['l_name'],'userimage'=>$userimage,'views'=>$val['views'],'comment'=>$val['comment'],'likes'=>$val['likes'],'postimage'=>$image,'video'=>$video,'video_thumb'=>$video_thumb,'content'=>$val['content'],'rdate'=>timelinetimeAgo($val['rdate']),'liked'=>$val['lid'],'type'=>$type); 
                 }
                  
                  
                 /*
                 
                    $images = array();
                 foreach($usersimages as $val) {
                      $largimage = '';
                     if($val['image']!='') { $largimage = 'http://majemeyfarms.com/assets/gallery/'.$val['image']; }
                     $video_thumb = '';
                     if($val['image']!='') { $video_thumb = 'http://majemeyfarms.com/assets/gallery/thumbs/'.$val['image']; }else{
                    $video_thumb = 'http://majemeyfarms.com/assets/front/images/vid-back.jpg';
                         }
                     
                     $userimage = '';
                     if($val['cimage']!='') { $userimage = 'http://majemeyfarms.com/images/user/small/'.$val['cimage']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     $images[] = array('id'=>$val['id'],'title'=>substr($val['title'],0,15),'status'=>$val['status'],'views'=>number_format_short($val['views']),'votes'=>number_format_short($val['votes']),'likes'=>number_format_short($val['likes']),'user_name'=>$val['d_name'],'date'=>$val['date'],'image'=>$largimage,'image_thumb'=>$video_thumb,'uimage'=>$userimage,'user_id'=>$val['user_id'],'description'=>$val['description'],'contest_id'=>$val['contest_id'],'liked'=>'0','voted'=>'0'); 
                 } */
                 
                //$this->response($timeline,REST_Controller::HTTP_OK); 
                 
            $this->response(['postdata'=>$result],REST_Controller::HTTP_OK);
            //$this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function kyc_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        
         $user = $this->user->select_manual('customer',array('id'=>$id));
        
        // Check if the user data exists
        if(!empty($user)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();

            $result[] = array('id'=>$user[0]['id'],'name'=>$user[0]['account_name'],'gpay'=>$user[0]['gpay'],'paytm'=>$user[0]['paytm'],'bank_name'=>$user[0]['bank_name'],'account_no'=>$user[0]['account_no'],'ifsc'=>$user[0]['ifsc'],'bank_image'=>$user[0]['bank_image']); 
                 
            $this->response($result,REST_Controller::HTTP_OK);
            //$this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function socialporchmedia_get($id = 0, $userid = 0, $limit = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
         $con = $id?array('id' => $id,'userid'=>$userid):'';
        $timeline = $this->user->get_my_timeline_post($userid,$id);
        
        // Check if the user data exists
        if(!empty($timeline)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($timeline as $val) {
                      $userimage = '';
                     if($val['userimage']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['userimage']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     $image='';
                     $video='';
                     $type='';
                     if(!empty($val['image'])) { $video = 'http://majemeyfarms.com/assets/timeline/post/'.$val['image'];$type='image'; }
                     
                     if(!empty($val['video'])) { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; $type='video';}
                     
                     if(!empty($val['video_thumb'])) { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{$video_thumb = '';}
                     
                    
                     $result[] = array('id'=>$val['id'],'user_id'=>$val['user_id'],'f_name'=>$val['f_name'],'l_name'=>$val['l_name'],'userimage'=>$userimage,'views'=>$val['views'],'comment'=>$val['comment'],'likes'=>$val['likes'],'postimage'=>$image,'video'=>$video,'video_thumb'=>$video_thumb,'content'=>$val['content'],'rdate'=>timelinetimeAgo($val['rdate']),'liked'=>$val['lid'],'type'=>$type); 
                 }
                 
            $this->response(['postdata'=>$result],REST_Controller::HTTP_OK);
            //$this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function socialporchpostbyid_get($id = 0, $userid = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
         $con = $id?array('id' => $id,'userid'=>$userid):'';
        
        $timeline = $this->user->get_timeline_post_by_id($id,$userid);
        
        // Check if the user data exists
        if(!empty($timeline)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($timeline as $val) {
                      $userimage = '';
                     if($val['userimage']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['userimage']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     
                     $image='';
                     $video='';
                     
                     if(!empty($val['image'])) { $video = 'http://majemeyfarms.com/assets/timeline/post/'.$val['image']; }
                     
                     if(!empty($val['video'])) { $video = 'http://majemeyfarms.com/assets/videos/'.$val['video']; }
                     
                     if(!empty($val['video_thumb'])) { $video_thumb = 'http://majemeyfarms.com/assets/videos/thumbnail/'.$val['video_thumb']; }else{$video_thumb = '';}
                     
                    
                     $result[] = array('id'=>$val['id'],'user_id'=>$val['user_id'],'f_name'=>$val['f_name'],'l_name'=>$val['l_name'],'userimage'=>$userimage,'views'=>$val['views'],'comment'=>$val['comment'],'likes'=>$val['likes'],'postimage'=>$image,'video'=>$video,'video_thumb'=>$video_thumb,'content'=>$val['content'],'rdate'=>$val['rdate'],'liked'=>$val['lid']); 
                 }
                 
            $this->response(['postdata'=>$result],REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
    public function topcreator_get($id = 0, $userid = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
         $con = $id?array('id' => $id,'userid'=>$userid):'';
        
         
        $topcreator = $this->user->topcreator_list($userid);
        
        // Check if the user data exists
        if(!empty($topcreator)){
            // Set the response and exit
            //OK (200) being the HTTP response code
          
                 $recomend = array();
                 foreach($topcreator as $val) {
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     $recomend[] = array('id'=>$val['id'],'d_name'=>$val['d_name'],'followers'=>$val['followers'],'image'=>$userimage); 
                 }
                 
                //$this->response($timeline,REST_Controller::HTTP_OK); 
                 
            //$this->response(['postdata'=>$result,'recomended'=>$recomend],REST_Controller::HTTP_OK);
            $this->response($recomend,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function smartbuzz_get($id = 0, $userid = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        
         $friends = $this->user->get_user_friend_ids($userid); 
         $friends_second = $this->user->get_user_friend_ids_second($userid);
         $friends = array_merge($friends,$friends_second);
         
          $user_ids = array();
         if(!empty($friends)) {
             foreach($friends as $friend){
                 $user_ids[] = $friend['friend_id'];
             }
         }
         //print_r($friends);
         
        $mylastchat = $this->user->get_mylastchat($user_ids,$userid);
        $recomended = $this->user->my_recomended_list($userid);
        // Check if the user data exists
        
         $result = array();
         $lastchat = array();
         $recomend = array();
        if(!empty($friends)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            
                 foreach($friends as $val) {
                       $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     $result[] = array('id'=>$val['cid'],'uid'=>$val['userid'],'d_name'=>$val['d_name'],'image'=>$userimage,'fstatus'=>friendstatus($val['cid'])); 
                 }
                 
                  
                 
                
                 foreach($mylastchat as $val) {
                      $userimage = '';
                     if($val['userimage']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['userimage']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     $time =    timeago($val['timestamp']);
                     $lastchat[] = array('id'=>$val['id'],'user_id'=>$val['custid'],'u_name'=>$val['d_name'],'userimage'=>$userimage,'user_msg'=>$val['chat_message'],'time'=>$time); 
                 }
                 }
                 
                  
                 foreach($recomended as $val) {
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     $recomend[] = array('id'=>$val['id'],'uid'=>$val['userid'],'d_name'=>$val['d_name'],'image'=>$userimage); 
                 }
                
                 
            $this->response(['friendsdata'=>$result,'lastchat'=>$lastchat,'recomended'=>$recomend],REST_Controller::HTTP_OK);
            //$this->response($result,REST_Controller::HTTP_OK);
        
    }
    
    
     public function hostcategory($id) {
        $users = $this->user->hostcategory($id);
        if(!empty($users)) {
            return $users[0]['c_name'];
        } else {
            return '';
        }
        
    }
    
    public function chitchat_get($limit = 0) {
        
        $chitchat = $this->user->chitchat_list($limit);
        // Check if the user data exists
        if(!empty($chitchat)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($chitchat as $val) {
                       $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/chitchat/user/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     $result[] = array('id'=>$val['id'],'uid'=>$val['merchant_id'],'d_name'=>$val['d_name'],'vanue1'=>$val['vanue1'],'city'=>$val['city'],'image'=>$userimage); 
                 }
                 
                 
            //$this->response(['chitchat'=>$result],REST_Controller::HTTP_OK);
            $this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
    public function chitchatuser_get($id = 0, $userid = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id,'userid'=>$userid):'';
        $users = $this->user->chitchatuserdata($con);
        
        $usersmultiimages = $this->user->chitchatuserprofile($con);
        $suggestion=$this->user->suggestion_fetch($users[0]['category'],$id);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/chitchat/user/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     $cover_pic = '';
                     $occupation=$this->hostcategory($val['category']);
                     $result[] = array('id'=>$val['id'],'d_name'=>$val['d_name'],'about'=>$val['about_us'],'charges'=>$val['charges'],'info'=>$val['p_info'],'state'=>$val['state'],'city'=>$val['city'],'website'=>$val['website'],'vanue1'=>$val['meet_at'],'image'=>$userimage,'youtube'=>$val['youtube'],'facebook'=>$val['facebook'],'occupation'=>$occupation); 
                 }
                 
                 
                 $multiimages = array();
                 foreach($usersmultiimages as $val) {
                      $userimage = '';
                     if($val['media']!='') { $usermultiimage = 'http://majemeyfarms.com/images/chitchat/user/portfolio/'.$val['media']; }
                    
                     $multiimages[] = array('images'=>$usermultiimage); 
                 }
                 
                 
                 $sugg = array();
                 foreach($suggestion as $val) {
                       $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/chitchat/user/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                     $sugg[] = array('id'=>$val['id'],'uid'=>$val['merchant_id'],'d_name'=>$val['d_name'],'city'=>$val['city'],'image'=>$userimage); 
                 }
                 
            $this->response(['userdata'=>$result,'usermultimages'=>$multiimages,'suggestion'=>$sugg],REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
    public function addorder_post() {
        // Get the post data
        $user_id =$this->post('user_id');
        $video_id = $this->post('video_id');  
        $image_id = $this->post('image_id'); 
        $contest_id =$this->post('contest_id');
        $coupon = $this->post('coupon');
        $order_type = $this->post('order_type');
		$round = $this->post('round');
        $total_amount = strip_tags($this->post('total_amount'));
        
        if(!empty($this->post('video_id'))){
            if(count($this->post('video_id')) > 2) {
            $this->response([
                        'status' => TRUE,
                        'message' => 'You can select only 2 videos at a time for the selected contest',
                        'data' => '',
                    ], REST_Controller::HTTP_OK);
            }
            elseif(count($this->post('video_id')) == 0) {
                $this->response([
                        'status' => TRUE,
                        'message' => 'Select any Video',
                        'data' => '',
                    ], REST_Controller::HTTP_OK);
            }
        }
        
        if(!empty($this->post('image_id'))){
            if(count($this->post('image_id')) > 2) {
            $this->response([
                        'status' => TRUE,
                        'message' => 'You can select only 2 Images at a time for the selected contest',
                        'data' => '',
                    ], REST_Controller::HTTP_OK);
            }
            elseif(count($this->post('image_id')) == 0) {
                $this->response([
                        'status' => TRUE,
                        'message' => 'Select any Image',
                        'data' => '',
                    ], REST_Controller::HTTP_OK);
            }
        }
            
            if(!empty($this->post('coupon'))){
                
        if(strtolower($this->post('coupon')) != 'fr55mth') {
            $this->response([
                        'status' => TRUE,
                        'message' => 'Invalid Coupon Code',
                        'data' => '',
                    ], REST_Controller::HTTP_OK);
        } 
                
                if($order_type=='video'){
            $payment = $this->user->select_manual('videos',array('user_id'=>$user_id),array('coupon'=>$this->post('coupon')));
                }elseif($order_type=='image'){
             $payment = $this->user->select_manual('gallery',array('user_id'=>$user_id),array('coupon'=>$this->post('coupon')));
                }
            if(count($payment)>=1) {
                $this->response([
                        'status' => TRUE,
                        'message' => 'Coupon Code already used',
                        'data' => '',
                    ], REST_Controller::HTTP_OK);
            }
            
            }
            
            if($order_type=='video'){
            $previouscontestvideo = $this->user->select_manual('videos',array('user_id'=>$user_id),array('contest_id'=>$this->post('contest_id')));
            $previouscontestvideo= count($previouscontestvideo) + count($this->post('video_id'));
            if($previouscontestvideo>10) {
                  $this->response([
                        'status' => TRUE,
                        'message' => 'you can participate with upto 10 videos',
                        'data' => '',
                    ], REST_Controller::HTTP_OK);
        }
            } 
            
            if($order_type=='image'){
            $previouscontestvideo = $this->user->select_manual('gallery',array('user_id'=>$user_id),array('contest_id'=>$this->post('contest_id')));
            $previouscontestvideo= count($previouscontestvideo) + count($this->post('image_id'));
            if($previouscontestvideo>10) {
                  $this->response([
                        'status' => TRUE,
                        'message' => 'you can participate with upto 10 images',
                        'data' => '',
                    ], REST_Controller::HTTP_OK);
        }
            }
        
        
        
        // Validate the post data
        if(!empty($user_id) && !empty($contest_id) && !empty($order_type)){
            $result=0;
			if($round > 1){
				
// Prepare new cURL resource
$ch = curl_init('http://majemeyfarms.com/razorpay/pay.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS,"auth=Codex@123&amount=".$total_amount."");
// Submit the POST request
$result = curl_exec($ch);
// Close cURL session handle
curl_close($ch);
		}
        
        if($order_type=='video'){ 
                // Insert user data
                $userData = array(
                'user_id' => $user_id,
                'order_id' => $result,
                'contest' => $contest_id,
                'cr' => $total_amount,
                'status' => 'Process',
                'dis' => 'Contest Handling Fee',
                'how_to_pay' => 'razorpay',
                'videos' => $video_id,
                );
        }
        
        if($order_type=='image'){ 
                // Insert user data
                $userData = array(
                'user_id' => $user_id,
                'order_id' => $result,
                'contest' => $contest_id,
                'cr' => $total_amount,
                'status' => 'Process',
                'dis' => 'Contest Handling Fee',
                'how_to_pay' => 'razorpay',
                'image' => $image_id,
                );
        }
        
		
		if($round > 1){
                $insert = $this->user->addorder($userData);
                // Check if the user data is inserted
                if($insert){
                    // Set the response and exit
                    $this->response([
                        'status' => TRUE,
                        'message' => 'order Added successfully.',
                        'data' => $result
                    ], REST_Controller::HTTP_OK);
                }else{
                    // Set the response and exit
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Some problems occurred, please try again.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);    
                }
        
		
		}else{
		  
		  
		  $insert = $this->user->addorder($userData);
                // Check if the user data is inserted
                if($insert){
					
                $tags = json_decode($video_id, true);
              foreach($tags as $key) {    
               $this->user->update_manual('videos',array('id'=>$key),array('contest_id'=>$contest_id)); 
			  }
                    // Set the response and exit
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Participated successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
                }else{
                    // Set the response and exit
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Some problems occurred, please try again.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);    
                }
		  
	  }
        }
      
	  
	  
	  if(!empty($user_id) && !empty($order_type) && $order_type=='prime'){
        
            // Prepare new cURL resource
        $ch = curl_init('http://majemeyfarms.com/razorpay/pay.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"auth=Codex@123&amount=".$total_amount."");
        // Submit the POST request
        $result = curl_exec($ch);
        // Close cURL session handle
        curl_close($ch);

        if($order_type=='prime'){ 
                // Insert user data
                $userData = array(
                'user_id' => $user_id,
                'order_id' => $result,
                'cr' => $total_amount,
                'status' => 'Process',
                'dis' => 'Prime Handling Fee',
                'how_to_pay' => 'razorpay'
                );
        }
                $insert = $this->user->addorder($userData);
                // Check if the user data is inserted
                if($insert){
                    // Set the response and exit
                    $this->response([
                        'status' => TRUE,
                        'message' => 'order Added successfully.',
                        'data' => $result
                    ], REST_Controller::HTTP_OK);
                }else{
                    // Set the response and exit
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Some problems occurred, please try again.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);    
                }

        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
        }
    
		
	
	}
    public function verifyorder_post() {
        // Get the post data
        if($this->post('user_id')){$user_id = strip_tags($this->post('user_id'));  }
        
        $order_id = strip_tags($this->post('order_id'));
        $status = strip_tags($this->post('status'));
        $amount = strip_tags($this->post('amount'));
        $payment_id = strip_tags($this->post('payment_id'));
        $type = $this->post('type');
        $contest_type = $this->post('contest_type');
        
        //type prime contest
        
        // Validate the post data
        if(!empty($user_id) && !empty($order_id)){
        if($this->post('user_id')){ 
        $insert = $this->user->verifycustomerorder($order_id,$status,$user_id,$payment_id,$amount);
        }

                // Check if the user data is inserted
                if($insert){

                    // Set the response and exit
                    if($status=='Failed'){

                        $this->response([
                        'status' => TRUE,
                        'message' => 'Payment Failed.',
                        'data' => 'Done'
                    ], REST_Controller::HTTP_OK);
                        
                        
                    }else{
                       
                if($type=='contest')  {
                     $summery = $this->user->select_manual('transaction_summery',array('order_id'=>$order_id));
                     $contest_ids = json_decode($summery[0]['videos'],true);
                    if(!empty($contest_ids)) {

            $contest_id = $summery[0]['contest'];
            
          foreach($contest_ids as $con_id) {
            if($con_id!='') {
               if($contest_type=='video'){

                 $this->user->update_manual('videos',array('id'=>$con_id),array('contest_id'=>$contest_id));

                  }else{

                   $this->user->update_manual('gallery',array('id'=>$con_id),array('contest_id'=>$contest_id)); 
                   

                  } 
            }
              

          }


      }
      $this->response([
                        'status' => TRUE,
                        'message' => 'Payment successfully.',
                        'data' => 'Done'
                    ], REST_Controller::HTTP_OK);
                    
                }

                /** prime member start**/
                if($type=='prime') {

                 $user = $this->user->profile($user_id);


                 $prime_limit = $this->user->select_manual('prime_limit',array('user_id'=>$user_id));
                if(empty($prime_limit)) {
                    $this->user->insert_manual('prime_limit',array('user_id'=>$user_id));
                    if($user[0]['parent_customer_id'] !='') {
                    $this->user->direct_update($user[0]['parent_customer_id'],1,'direct');
                        
                        if($user[0]['dprime']==1) {
                            $this->user->update_prime_eligibility_income($user[0]['did'],200);
                            $this->user->update_prime_eligibility_income($user[0]['id'],200);
                        }
                 }

                 $this->user->update_manual('customer',array('id'=>$user_id),array('prime'=>1));
                }
                 $last_prime_member = $this->user->get_prime_member();
                 $column = $last_prime_member[0]['column'];
                 $row = $last_prime_member[0]['row'];
                 if($column==3) {
                    $row = $row+1;
                    $column = 1;
                 } else {
                    $column = $column+1;
                 }

                 $data_to_store = array('user_id'=>$user_id,'column'=>$column,'row'=>$row);
                 $insert_id = $this->user->insert_prime_member($data_to_store);

                 /* Row Income Start */
                 if($column==3) {
                    $get_record = $this->user->get_prime_member_by_id($row);
                    if(!empty($get_record) && $get_record[0]['income'] < $get_record[0]['eligibility']) {
                        //  income
                        if($get_record[0]['income'] + $get_record[0]['eligibility'] > $get_record[0]['eligibility']) { 
                        $income = $get_record[0]['eligibility'] - $get_record[0]['income'];
                        } else { $income = 250; }
                        $data_to_store = array('user_id'=>$get_record[0]['user_id'],'amount'=>$income,'user_send_by'=>$insert_id,'type'=>'Row','status'=>'Active');
                        $this->user->insert_income($data_to_store);
                        $this->user->update_wallet($get_record[0]['user_id'],$income);
                        $this->user->update_prime_limit_income($get_record[0]['user_id'],$income);

                    } else {
                        $data_to_store = array('user_id'=>$get_record[0]['user_id'],'amount'=>250,'user_send_by'=>$insert_id,'type'=>'Row','status'=>'Hold');
                        $this->user->insert_income($data_to_store);
                    }
                    
                 }
                 /* Row Income End */

                  /* Column Income Start */
                 if(is_int($row/3) > 0) {
                    $get_column_income_id = $insert_id-(($row/3)*6);
                    $get_record = $this->user->get_prime_member_by_id($get_column_income_id);
                    if(!empty($get_record) && $get_record[0]['income'] < $get_record[0]['eligibility']) {
                        //  income
                        if($get_record[0]['income'] + $get_record[0]['eligibility'] > $get_record[0]['eligibility']) { 
                        $income = $get_record[0]['eligibility'] - $get_record[0]['income'];
                        } else { $income = 250; }
                        $data_to_store = array('user_id'=>$get_record[0]['user_id'],'amount'=>$income,'user_send_by'=>$insert_id,'type'=>'Column','status'=>'Active');
                        $this->user->insert_income($data_to_store);
                        $this->user->update_wallet($get_record[0]['user_id'],$income);
                        $this->user->update_prime_limit_income($get_record[0]['user_id'],$income);
                    } else {
                        $data_to_store = array('user_id'=>$get_record[0]['user_id'],'amount'=>250,'user_send_by'=>$insert_id,'type'=>'Column','status'=>'Hold');
                        $this->user->insert_income($data_to_store);
                    }

                    if(is_int($insert_id/9) > 0) {

                        $get_row_income = $insert_id/9;
                        $get_record = $this->user->get_prime_member_row($get_row_income);
                        if(!empty($get_record)) {
                            foreach ($get_record as $value) {
                                if($value['income'] < $value['eligibility']) {
                                    if($value['income'] + $value['eligibility'] > $value['eligibility']) { 
                                    $income = $value['eligibility'] - $value['income'];
                                    } else { $income = 300; }
                                    $data_to_store = array('user_id'=>$value['user_id'],'amount'=>$income,'user_send_by'=>$insert_id,'type'=>'Box Achiever','status'=>'Active');
                                    $this->user->insert_income($data_to_store);
                                    $this->user->update_wallet($value['user_id'],$income);
                                    $this->user->update_prime_limit_income($value['user_id'],$income);
                                } else {
                                    $data_to_store = array('user_id'=>$value['user_id'],'amount'=>300,'user_send_by'=>$insert_id,'type'=>'Box Achiever','status'=>'Hold');
                                    $this->user->insert_income($data_to_store);
                                }
                                
                                

                            }


                            $get_record = $this->user->get_prime_member_by_id($get_row_income);
                            $this->prime_memeber_matrix($get_record[0]['user_id']);

                        }


                    }

                 }

                 /* Column Income End */



          $this->session->unset_userdata('web_type');
          $this->session->unset_userdata('contest_type');
          $this->session->set_flashdata('flash_message', 'updated');
          

                    /** prime member end**/


                    $this->response([
                        'status' => TRUE,
                        'message' => 'Payment successfully.',
                        'data' => 'Done'
                    ], REST_Controller::HTTP_OK);
                    }
                }

                }else{
                    // Set the response and exit
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Some problems occurred, please try again.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);   
                }
        
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    public function primemember_post() {
        // Get the post data
        $user_id =$this->post('user_id');
        $order_type = $this->post('order_type');
        $total_amount = strip_tags($this->post('total_amount'));
        
        // Validate the post data
        if(!empty($user_id) && !empty($order_type)){
            
// Prepare new cURL resource
$ch = curl_init('http://majemeyfarms.com/razorpay/pay.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS,"auth=Codex@123&amount=".$total_amount."");
// Submit the POST request
$result = curl_exec($ch);
// Close cURL session handle
curl_close($ch);
        
        if($order_type=='prime'){ 
                // Insert user data
                $userData = array(
                'user_id' => $user_id,
                'order_id' => $result,
                'cr' => $total_amount,
                'status' => 'Process',
                'dis' => 'Prime membership Fee',
                'how_to_pay' => 'razorpay',
                );
           }
           $insert = $this->user->addorder($userData);
                // Check if the user data is inserted
                if($insert){
                    // Set the response and exit
                    $this->response([
                        'status' => TRUE,
                        'message' => 'order Added successfully.',
                        'data' => $result
                    ], REST_Controller::HTTP_OK);
                }else{
                    // Set the response and exit
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Some problems occurred, please try again.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);    
                }
        
        }else{
            // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide complete user info to add.',
                    'data' => '',
                ],REST_Controller::HTTP_OK);
        }
    }

    public function prime_memeber_matrix($id) {

            
                 $last_prime_member = $this->user->get_prime_member();
                 $column = $last_prime_member[0]['column'];
                 $row = $last_prime_member[0]['row'];
                 if($column==3) {
                    $row = $row+1;
                    $column = 1;
                 } else {
                    $column = $column+1;
                 }

                 $data_to_store = array('user_id'=>$id,'column'=>$column,'row'=>$row);
                 $insert_id = $this->user->insert_prime_member($data_to_store);

                 /* Row Income Start */
                 if($column==3) {
                    $get_record = $this->user->get_prime_member_by_id($row);
                    if(!empty($get_record) && $get_record[0]['income'] < $get_record[0]['eligibility']) {
                        //  income
                        if($get_record[0]['income'] + $get_record[0]['eligibility'] > $get_record[0]['eligibility']) { 
                        $income = $get_record[0]['eligibility'] - $get_record[0]['income'];
                        } else { $income = 250; }
                        $data_to_store = array('user_id'=>$get_record[0]['user_id'],'amount'=>$income,'user_send_by'=>$insert_id,'type'=>'Matrix Row','status'=>'Active');
                        $this->user->insert_income($data_to_store);
                        $this->user->update_wallet($get_record[0]['user_id'],$income);
                        $this->user->update_prime_limit_income($get_record[0]['user_id'],$income);
                    } else {
                        $data_to_store = array('user_id'=>$get_record[0]['user_id'],'amount'=>250,'user_send_by'=>$insert_id,'type'=>'Matrix Row','status'=>'Hold');
                        $this->user->insert_income($data_to_store);
                    }
                    
                 }
                 /* Row Income End */

                  /* Column Income Start */
                 if(is_int($row/3) > 0) {
                    $get_column_income_id = $insert_id-(($row/3)*6);
                    $get_record = $this->user->get_prime_member_by_id($get_column_income_id);
                    if(!empty($get_record) && $get_record[0]['income'] < $get_record[0]['eligibility']) {
                        //  income
                        if($get_record[0]['income'] + $get_record[0]['eligibility'] > $get_record[0]['eligibility']) { 
                        $income = $get_record[0]['eligibility'] - $get_record[0]['income'];
                        } else { $income = 250; }
                        $data_to_store = array('user_id'=>$get_record[0]['user_id'],'amount'=>$income,'user_send_by'=>$insert_id,'type'=>'Matrix Column','status'=>'Active');
                        $this->user->insert_income($data_to_store);
                        $this->user->update_wallet($get_record[0]['user_id'],$income);
                        $this->user->update_prime_limit_income($get_record[0]['user_id'],$income);
                    } else {
                        $data_to_store = array('user_id'=>$get_record[0]['user_id'],'amount'=>250,'user_send_by'=>$insert_id,'type'=>'Matrix Column','status'=>'Hold');
                        $this->user->insert_income($data_to_store);
                    }

                    if(is_int($insert_id/9) > 0) {


                        /*$get_record = $this->user->get_prime_member_matrix_by_id($get_row_income);
                        if(!empty($get_record)) {
                            //  income
                            $data_to_store = array('user_id'=>$get_record[0]['user_id'],'amount'=>800,'user_send_by'=>$insert_id,'type'=>'Matrix Box Income');
                            $this->user->insert_income($data_to_store);
                        }*/


                        $get_row_income = $insert_id/9;
                        $get_record = $this->user->get_prime_member_row($get_row_income);
                        if(!empty($get_record)) {
                            foreach ($get_record as $value) {
                                if($value['income'] < $value['eligibility']) {
                                    if($value['income'] + $value['eligibility'] > $value['eligibility']) { 
                                    $income = $value['eligibility'] - $value['income'];
                                    } else { $income = 300; }
                                    $data_to_store = array('user_id'=>$value['user_id'],'amount'=>$income,'user_send_by'=>$insert_id,'type'=>'Matrix Box Achiever','status'=>'Active');
                                    $this->user->insert_income($data_to_store);
                                    $this->user->update_wallet($value['user_id'],$income);
                                    $this->user->update_prime_limit_income($value['user_id'],$income);
                                } else {
                                    $data_to_store = array('user_id'=>$value['user_id'],'amount'=>300,'user_send_by'=>$insert_id,'type'=>'Matrix Box Achiever','status'=>'Hold');
                                    $this->user->insert_income($data_to_store);
                                }
                                
                                //$this->prime_memeber_matrix($value['user_id']);

                                
                            }

                            $get_record = $this->user->get_prime_member_by_id($get_row_income);
                            $this->prime_memeber_matrix($get_record[0]['user_id']);
                        }


                        


                    }

                 }

                 /* Column Income End */
                
                 
             

       }
    public function transferfund_post() {
        // Get the post data
        $user_id = $this->post('user_id');
        $redeem = $this->post('amount');
        
       $users = $this->user->getuserpdata($user_id);
       $wallet= $users[0]['bliss_amount'];
       $balance =$wallet-$redeem;
       
       $ciruserlimit = 500;
       
        if($wallet < $this->input->post('amount')) {
              $this->response([
                    'status' => FALSE,
                    'message' => 'Your maximum transfer Amount limit  is '.$wallet.'',
                    'data' => ''
                ], REST_Controller::HTTP_OK);
           }
           
           
          if($users[0]['var_status']=='') {
              $this->response([
                    'status' => FALSE,
                    'message' => 'Please update your profile',
                    'data' => ''
                ], REST_Controller::HTTP_OK);
           }
           
           if($users[0]['var_status']=='no') {
              $this->response([
                    'status' => FALSE,
                    'message' => 'Your profile is under review please wait 2 working days',
                    'data' => ''
                ], REST_Controller::HTTP_OK);
           }
           
       
        if(!empty($user_id)){
             $data_to_store = array(
                    'balance' => $balance,
                    'redeem' => $this->input->post('amount'),
                    'after_tds' => $balance,
                    'my_bliss_req' => 'bliss_amount',
                    'user_id' => $user_id
                ); 
               $this->user->redeem_bliss_request($data_to_store);
               
               $this->user->update_wallet($user_id,$balance);

                $this->response([
                    'status' => TRUE,
                    'message' => 'Request updated successfully.',
                    'data' => ''
                ], REST_Controller::HTTP_OK);
           
        }else{
            // Set the response and exit
             $this->response([
                    'status' => FALSE,
                    'message' => 'Something Went wrong',
                    'data' => ''
                ],REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function consultant_post() {
        // Get the post data
       
        
       if(!empty($this->post('user_id'))){
                // Insert user data
                $userData = array(
              'user_id' => $this->post('user_id'),
              'name' => $this->post('bcname'),
              'email' => $this->post('bcemail'),
              'phone' => $this->post('bcphone'),
              'address' => $this->post('bcadress'),
              'city' => $this->post('bccity'),
              'vanue1' => $this->post('bcvanue1'),
              'occup' => $this->post('bcoccup'),
              'vanue2' => $this->post('bcvanue2'),
              'status' => 'Pending',
                );
                $insert = $this->user->insert_manual('consultant_request', $userData);
                
                
                // Check if the user data is inserted
                if($insert){
                    
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Data added successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
                    
                    
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Add some data.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }

    public function bankupdate_post() {
        
       if(strip_tags($this->post('user_id'))!=''){
                // Insert user data
        $userData = array(
              'account_name' => $this->post('name'),
              'gpay' => $this->post('gpay'),
              'paytm' => $this->post('paytm'),
              'bank_name' => $this->post('bank_name'),
              'account_no' => $this->post('account_no'),
              'ifsc' => $this->post('ifsc'),
              'info' => $this->post('bio'),
              //'bank_image' => $this->post('bank_image')
            );
                $insert = $this->user->update_manual('customer',array('id'=>$this->post('user_id')), $userData);
                
                
                // Check if the user data is inserted
                if($insert){
                    
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Data updated successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
                    
                    
                }
            } else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Add some data.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }

    public function advertisement_post() {
        // Get the post data
       
        
       if(!empty($this->post('user_id'))){
                // Insert user data
                $userData = array(
              'user_id' => $this->post('user_id'),
              'brand_name' => $this->post('brand_name'),
              'email' => $this->post('email'),
              'phone' => $this->post('phone'),
              'company_name' => $this->post('company_name'),
              'service' => $this->post('service'),
              'concuran_person' => $this->post('concuran_person'),
              'advertise_budget' => $this->post('advertise_budget'),
              'advertise_type' => $this->post('advertise_type'),
              'status' => 'Pending',
                );
                $insert = $this->user->insert_manual('advertisement_request', $userData);
                
                
                // Check if the user data is inserted
                if($insert){
                    
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Data added successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
                    
                    
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Add some data.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    
     public function contact_post() {
        // Get the post data
       
        
       if(!empty($this->post('user_id'))){
                // Insert user data
                $userData = array(
              'user_id' => $this->post('user_id'),
              'name' => $this->post('name'),
              'email' => $this->post('email'),
              'phone' => $this->post('phone'),
              'description' => $this->post('description'),
              'status' => 'Pending',
                );
                $insert = $this->user->insert_manual('contact_request', $userData);
                
                $to = "goldroger9888@gmail.com";
                $subject ="contact_form :- mytalenthunt";
                $txt = "name :- ".$this->input->post('name')."<br/>email :- ".$this->input->post('email')."<br/>phone :- ".$this->input->post('phone')."<br/>message :- ".$this->input->post('description')."<br/>Customer name :- ".$this->session->userdata('full_name')."<br/>customer id :- ".$this->session->userdata('bliss_id'); 
                $headers = "From: mytalenthunt.in" . "\r\n";
                $headers = "MIME-Version: 1.0" . "\r\n";     
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";  
                $headers .= 'From: <mytalenthunt.in>' . "\r\n"; 
                mail($to,$subject,$txt,$headers);
                
                // Check if the user data is inserted
                if($insert){
                    
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Data added successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
                    
                    
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Add some data.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }

   

    public function prime_income_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
         $con = $id?array('id' => $id):'';
         $prime_member_row = $prime_member_column = $prime_member_box = array();
         $prime_member = $this->user->get_prime_member_by_userid($id);
         $profile = $this->user->profile($id);
         /*if(!empty($prime_member)) {
            $prime_member_row = $this->user->select_manual('prime_member',array('row'=>$prime_member[0]['id']));
            $row = ($prime_member[0]['row']-1)*3;
       $prime_member_column = $this->user->select_manual('prime_member',array('row >='=>$row+1,'row <'=>$row+4,'column'=>$prime_member[0]['column']));

            $row = $prime_member[0]['id']*3;
            $prime_member_box = $this->user->select_manual('prime_member',array('row'=>$row));
         }
         
          $count =  count($prime_member_row) + count($prime_member_column) + count($prime_member_box); $balance = 10-$count;  */
          $prime_member_inc = $this->user->select_manual('income',array('user_id'=>$id));
          
          $prime_limit = $this->user->select_manual('prime_limit',array('user_id'=>$id));

        // Check if the user data exists
        if(!empty($prime_member)){
            // Set the response and exit
            //OK (200) being the HTTP response code
          
        $result = array('royality_limit'=>$prime_limit[0]['eligibility'],'ledger_total'=>array_sum(array_column($prime_member_inc, 'amount')),'ledger_balance'=>$profile[0]['bliss_amount'],'royality'=>0,'pac'=>0,'monitization'=>0);
                 
                 
            //$this->response(['postdata'=>$result],REST_Controller::HTTP_OK);
            $this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
      public function processhandlingfees_get($type = 'null') {
         $con = array('type'=>$type);
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.

        $users = $this->user->process_fee($con);
        // Check if the user data exists
        if(!empty($users)){
            $this->response($users, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No data found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
   
     
    /**  Chat api **/
    
    public function chatpost_post() {
    
        $from = strip_tags($this->post('from'));
        $to = strip_tags($this->post('to'));
            if(!empty($from) && !empty($to)){
                $now = date('Y-m-d H:i:s');
                // Insert user data
                $userData = array(
                'from_user_id' => $this->post('from'),
                'to_user_id' => $this->post('to'),
                'chat_message' => $this->post('message'),
                'timestamp' => $now,
                );
                $insert = $this->user->add_chat_msg($userData);
                
                $get_from = $this->user->user_chat_data($from);
                $get_to = $this->user->user_chat_data1($to);
                if(!empty($get_to)){
                $this->sendNotification($from,$get_from[0]['f_name'],$get_to[0]['device_id']);
                }
            
                // Check if the user data is inserted
                if($insert){
                    
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Data added successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
                    
                    
                }else{
                    $this->response([
                    'status' => FALSE,
                    'message' => 'Some problems occurred, please try again.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);   
                }
                
        
                
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Add some data.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    
    public function onebyonchat_get($id = 0, $userid = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
         
         $users = $this->user->get_chat_msg($id,$userid);   
        // Check if the user data exists
        if(!empty($users) ){
            
        $result = array();
        $prev = NULL;
        foreach($users as $val) {
        $time = new DateTime($val['timestamp']);
        $date = $time->format('n.j.Y');
        $datee = $time->format('j F Y');
        $time = $time->format('H:i a');
        if ($datee != $prev) {
        $groupdate=$datee;
        $prev = $datee;
        }else{$groupdate='1';}

        $result[] = array('id'=>$val['id'],'from'=>$val['from_user_id'],'to'=>$val['to_user_id'],'message'=>$val['chat_message'],'sent'=>$time,'groupdate'=>$groupdate);  
        }
         $this->response($result, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }
       
    public function onebyonchatnew_get($id = 0, $userid = 0) {
       // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        
         $users = $this->user->get_chat_msg_new($id,$userid);
         
        // Check if the user data exists
        if(!empty($users) ){
            $this->user->update_chat($id,$userid);
        $result = array();
        foreach($users as $val) {
        $time = timeago($val['timestamp']);
        $result[] = array('id'=>$val['id'],'from'=>$val['from_user_id'],'to'=>$val['to_user_id'],'message'=>$val['chat_message'],'sent'=>$time);  
        }
         $this->response($result, REST_Controller::HTTP_OK);    
        }else{
            // Set the response and exit
            $this->response(array(), REST_Controller::HTTP_OK);
        }
    }
   
    
    public function sendNotification($uiid,$name,$device_id) {
        $token = $device_id; 
        $message = "Sent you a message";
        $pid = "0";
        $uid = $uiid;
        $this->load->library('fcm');
        $this->fcm->setTitle($name);
        $this->fcm->setMessage($message);
        $this->fcm->setUserid($uid);
        $this->fcm->setProductid($pid);
        /**
         * set to true if the notificaton is used to invoke a function
         * in the background
         */
        $this->fcm->setIsBackground(false);
        /**
         * payload is userd to send additional data in the notification
         * This is purticularly useful for invoking functions in background
         * -----------------------------------------------------------------
         * set payload as null if no custom data is passing in the notification
         */
        $payload = array('notification' => '');
        $this->fcm->setPayload($payload);
        /**
         * Send images in the notification
         */
        $this->fcm->setImage('http://majemeyfarms.com/assets/front/images/favicon.png');
        /**
         * Get the compiled notification data as an array
         */
        $json = $this->fcm->getPush();
        $p = $this->fcm->send($token, $json);
       // print_r($p);
    }
 
 
 
 /**  notification  ***/
 
 
  public function usernotificationcount_get($id = 0) {
        $user_id = $id;
        $users = newnotificationapi($user_id);
        $msgcount = newmsgnotificationapi($user_id);
        
        //if(!empty($users)){
        $this->response(['chat'=>$msgcount,'msg'=>$users],REST_Controller::HTTP_OK);
        /* }else{
        $this->response(['chat'=>'','msg'=>''],REST_Controller::HTTP_OK);
        } */
    }
   
  public function usernotification_get($id = 0) {
        $user_id = $id;
        $users = notificationapi($user_id);
        $result = array();
        foreach($users as $val) {
            
            if($val['type']=='Like Video'){$type="likes Your Video"; $screen="video"; }
            if($val['type']=='Like Image'){$type="likes Your Photo"; $screen="image";}
            if($val['type']=='Like Post'){$type="liked your post"; $screen="post";}
            if($val['type']=='Follow'){$type="started following you"; $screen="follow";}
            if($val['type']=='Friend Request'){$type="want to become friend."; $screen="friend";}
            if($val['type']=='Friend Request Accepted'){$type="has accepted your friend request."; $screen="friend accepted";}
            
                     $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
            
        $time = timeago($val['date']);
        $result[] = array('id'=>$val['id'],'user_id'=>$val['user_id'],'user_id_by'=>$val['user_id_by'],'v_id'=>$val['v_id'],'p_id'=>$val['p_id'],'type'=>$type,'message'=>$val['message'],'status'=>$val['status'],'newnoti'=>$val['newnoti'],'readstatus'=>$val['readstatus'],'videoid'=>$val['videoid'],'d_name'=>$val['d_name'],'image'=>$userimage,'postid'=>0,'screen'=>$screen,'date'=>$time);  
        }
         
        if(!empty($users)){
                  $this->response([
                'status' => TRUE,
                'data' => $result,
                'message' => 'Notification found'
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => FALSE,
                'data' => '',
                'message' => 'No Notification was found.'
            ], REST_Controller::HTTP_OK);
        }
    }
   
    
    /** chat notification  ***/
    
   
     public function chatnotification_get($id = 0) {
        $user_id = $id;
        $users = newmessagesapi($user_id);
        
        $result = array();
        foreach($users as $val) {
            
                     $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/profile_pick/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
            
        $time = timeago($val['timestamp']);
        $result[] = array('id'=>$val['id'],'name'=>$val['d_name'],'from_user_id'=>$val['from_user_id'],'type'=>'has sent you a message','message'=>$val['chat_message'],'image'=>$userimage,'date'=>$time);  
        }
        
        if(!empty($users)){
                  $this->response([
                'status' => TRUE,
                'data' => $result,
                'message' => 'Notification found'
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => FALSE,
                'data' => '',
                'message' => 'No Notification was found.'
            ], REST_Controller::HTTP_OK);
        }
    }
  
    
    /*logout*/
    
public function logout_get($id = 0) {
        $user_id = $id;
        $users = $this->user->update_logoutid($user_id);
    }   
    
    /*Banner*/
    
     public function sliderimage_get($id=0) {
                $userslider = $this->user->active_slider();
                $prime = 0;
                if($id>0) {
                	$users = $this->user->select_manual('customer',array('id'=>$id));
                	$prime = $users[0]['prime'];
                }
        if(!empty($userslider)){
                $result = array();
                 foreach($userslider as $val) {
                  $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/main-admin/images/banner/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/main-admin/images/product/default_img.png';}
            
                 $result[] = array('id'=>$val['id'],'status'=>$val['status'],'image'=>$userimage,'contest_id'=>$val['contest_id'],'prime'=>$prime); 
                 }
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No Slider was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
   
        public function report_post() {
    
            if(!empty($this->post('v_id')) && !empty($this->post('u_id'))){
                // Insert user data
                $userData = array(
                'v_id' => $this->post('v_id'),
              'user_id' => $this->post('u_id'),
              'report' => implode("~~",$this->post('report')),
              //'report' => $this->post('report'),
              'comment' => $this->post('comment'),
              'status' => 'Pending',
                );
                $insert = $this->user->insert_manual('video_report', $userData);
                
                
                // Check if the user data is inserted
                if($insert){
                    
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Data added successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
                    
                    
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Add some data.',
                    'data' => '',
                ],REST_Controller::HTTP_BAD_REQUEST);
            
        }
    }
    
	
	
	  public function delete_post() {
    
        $postid = strip_tags($this->post('postid'));
        $chatid = strip_tags($this->post('chatid'));
		
        if($postid > 0){
		$this->user->delete_manual('timeline_post',array('id'=>$this->post('postid')));
		$this->user->delete_manual('post_comments',array('post_id'=>$this->post('postid')));
		$this->user->delete_manual('post_likes',array('like_id'=>$this->post('postid')));
		$this->user->delete_manual('notifications',array('p_id'=>$this->post('postid')));
          $this->response([
                        'status' => TRUE,
                        'message' => 'Post Deleted successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
            }
			
			if($chatid > 0){
		$this->user->delete_manual('chat_message',array('id'=>$this->post('chatid')));
          $this->response([
                        'status' => TRUE,
                        'message' => 'Chat Deleted successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
            }
			
		
	
	
    }
   
   
    public function deletenotification_post() {
    
        $userid = strip_tags($this->post('uid'));
        $notificationid = strip_tags($this->post('notificationid'));
		
			if($notificationid > 0 && $userid == 0){
		$this->user->delete_manual('notifications',array('id'=>$this->post('notificationid')));
          $this->response([
                        'status' => TRUE,
                        'message' => 'Notification Deleted successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
            }
			
			if($notificationid == 0  && $userid > 0){
		$this->user->delete_manual('notifications',array('user_id'=>$this->post('uid')));
          $this->response([
                        'status' => TRUE,
                        'message' => 'Notification Deleted successfully.',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
            }
			
    }
	
	
			
	
	
	public function _create_thumbs($file_name){
// Image resizing config
$config = array(
// Large Image
array(
'image_library' => 'GD2',
'source_image'=> 'images/user/profile_pick/'.$file_name,
'maintain_ratio'=> FALSE,
'width'=> 300,
'height'=> 300,
'new_image'=> 'images/user/profile_pick/'.$file_name
),
// Medium Image
array(
'image_library' => 'GD2',
'source_image'=> 'images/user/profile_pick/'.$file_name,
'maintain_ratio'=> TRUE,
'width'=> 45,
'height'=> 45,
'new_image'=> 'images/user/medium/'.$file_name
),
// Small Image
array(
'image_library' => 'GD2',
'source_image'=> 'images/user/profile_pick/'.$file_name,
'maintain_ratio'=> TRUE,
'width'=> 35,
'height'=> 35,
'new_image'=> 'images/user/small/'.$file_name
));

$this->load->library('image_lib', $config[0]);
foreach ($config as $item){
$this->image_lib->initialize($item);
if(!$this->image_lib->resize()){
return  $this->image_lib->display_errors();;
}
$this->image_lib->clear();
}
}

 public function versioncheck_get() {
        
		              $this->response([
                        'status' => TRUE,
                        'message' => 'We have fixed some issues and added some cool features in this update',
                        'data' => '1.0.11.009'
                    ], REST_Controller::HTTP_OK);
    }

 public function postdraftvideo_post() {
        // Get the post data
        $user_id = strip_tags($this->post('userid'));
        $video_id = strip_tags($this->post('videoid'));
		
		 if(!empty($video_id)){
                // Insert user data
                $userData = array(
                'status' => 'Approved'
                );
                 $this->user->update_manual('videos',array('id'=>$video_id), $userData);
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Video Published',
                        'data' => ''
                    ], REST_Controller::HTTP_OK);
                    
                    
                
            }
    }
   

   
    
    /**  unusefull api **/
    public function incentive_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):'';
        $users = $this->user->my_incentive($con);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
          
            $this->response($users,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No data found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function mylinkes_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):'';
        $users = $this->user->select_mylinks($con);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                    
                     $result[] = array('id'=>$val['id'],'d_name'=>$val['d_name'],'image'=>$userimage); 
                 }
                 
            $this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No data found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function connectedlinks_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):'';
        $users = $this->user->connected_links($con);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $result = array();
                 foreach($users as $val) {
                      $userimage = '';
                     if($val['image']!='') { $userimage = 'http://majemeyfarms.com/images/user/'.$val['image']; }else{$userimage = 'http://majemeyfarms.com/assets/images/31.png';}
                    
                     $result[] = array('id'=>$val['id'],'uid'=>$val['linked_id'],'d_name'=>$val['d_name'],'image'=>$userimage); 
                 }
                 
            $this->response($result,REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No data found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function state_get() {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $users = $this->user->state();
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $this->response($users, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No state was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function city_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id;
        $users = $this->user->city_all($con);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            $this->response($users, REST_Controller::HTTP_OK);
        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'status' => FALSE,
                'message' => 'No city was found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
  public function sound_get() {      $users = $this->user->sound_all();        if(!empty($users)){            $this->response($users, REST_Controller::HTTP_OK);        }else{                $this->response([                'status' => FALSE,                'message' => 'No Category was found.'            ], REST_Controller::HTTP_OK);        }    }
} 