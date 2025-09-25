<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billingcontroler extends CI_Controller {
    
	public	 function __construct()
		{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->model('Comancontroler_model');
	 
				 //$this->load->database();
 
	if( empty($this->session->userdata('logged') )) {
			  redirect(base_url('AdminLogin'));
	}
	
		}
		
	
 /****************** Drivers trip *****************/
	function driver_trip() {
        if(!checkPermission($this->session->userdata('permission'),'trip')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $driver = $sdate = $edate = '';
        
    	if($this->input->post('search'))	{ 
            $driver = $this->input->post('driver');
             
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
            
            $data['driver_trips'] = $this->Comancontroler_model->get_driver_trip_by_filter($sdate,$edate,$driver);
        } else {
            $data['driver_trips'] = array();
        }
         
    	$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
        $data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
     
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/driver_trip',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	function drivertripadd() {
        if(!checkPermission($this->session->userdata('permission'),'trip')){
	        redirect(base_url('AdminDashboard'));   
	    }
        //redirect(base_url('admin/driver_trip/'));
        if($this->input->post('save'))	{
				
				$this->form_validation->set_rules('pdate', 'pdate','required|min_length[10]|max_length[10]');
				$this->form_validation->set_rules('driver', 'driver','required|min_length[1]|max_length[10]|numeric');
				
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {  
			        $trip1 = $this->input->post('trip1pu').','.$this->input->post('trip1do');
			        $trip2 = $this->input->post('trip2pu').','.$this->input->post('trip2do');
			        $trip3 = $this->input->post('trip3pu').','.$this->input->post('trip3do');
			        $trip4 = $this->input->post('trip4pu').','.$this->input->post('trip4do');
			        
					$insert_data=array(
					    'tripdate'=>$this->input->post('pdate'),
					    'driver'=>$this->input->post('driver'),
					    'trip1'=>$trip1,
					    'trip2'=>$trip2,
					    'trip3'=>$trip3,
					    'trip4'=>$trip4,
					    'stime'=>$this->input->post('stime'),
					    'etime'=>$this->input->post('etime'),
					    'total_hour'=>$this->input->post('total_hour'),
					    'total_amt'=>$this->input->post('total_amt'),
					    'deduction'=>$this->input->post('deduction'),
					    'deduction_txt'=>$this->input->post('deduction_txt'),
					    'spend_amt'=>$this->input->post('spend_amt'),
					    'spendamt_txt'=>$this->input->post('spendamt_txt'),
					    'rate'=>$this->input->post('total_amt'),
					    'notes'=>$this->input->post('notes'),
					    'status'=>$this->input->post('status'),
					    'rdate'=>date('Y-m-d H:i:s')
					);
				
					$res = $this->Comancontroler_model->add_data_in_table($insert_data,'driver_trips'); 
					if($res){
						$this->session->set_flashdata('item', 'Driver trip added successfully.');
                        redirect(base_url('admin/driver_trip/add'));
					}
 				   
				}
		}
		
      //$data['dispatch'] = $this->Comancontroler_model->get_dispatch_for_trip();
      $data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
      
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/driver_tripadd',$data);
    	$this->load->view('admin/layout/footer');
	}
  
	function drivertripupdate() {
        if(!checkPermission($this->session->userdata('permission'),'trip')){
	        redirect(base_url('AdminDashboard'));   
	    }
		$id = $this->uri->segment(4);
		if($this->input->post('save'))	{
				
				//$this->form_validation->set_rules('dispatch', 'dispatch','required|min_length[1]|max_length[20]');
				$this->form_validation->set_rules('driver', 'driver','required|min_length[1]|max_length[30]');
				
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {  
			        $trip1 = $this->input->post('trip1pu').','.$this->input->post('trip1do');
			        $trip2 = $this->input->post('trip2pu').','.$this->input->post('trip2do');
			        $trip3 = $this->input->post('trip3pu').','.$this->input->post('trip3do');
			        $trip4 = $this->input->post('trip4pu').','.$this->input->post('trip4do');
			        
					$insert_data=array(
					    'trip1'=>$trip1,
					    'trip2'=>$trip2,
					    'trip3'=>$trip3,
					    'trip4'=>$trip4,
					    'stime'=>$this->input->post('stime'),
					    'etime'=>$this->input->post('etime'),
					    'total_hour'=>$this->input->post('total_hour'),
					    'total_amt'=>$this->input->post('total_amt'),
					    'deduction'=>$this->input->post('deduction'),
					    'deduction_txt'=>$this->input->post('deduction_txt'),
					    'spend_amt'=>$this->input->post('spend_amt'),
					    'spendamt_txt'=>$this->input->post('spendamt_txt'),
					    'rate'=>$this->input->post('total_amt'),
					    'notes'=>$this->input->post('notes'),
					    'status'=>$this->input->post('status')
					);
				
					$res = $this->Comancontroler_model->update_table_by_id($id,'driver_trips',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Driver trip update successfully.');
                        redirect(base_url('admin/driver_trip/update/'.$id));
					}
 				   
				}
		}
     
     $data['driver_trip'] = $this->Comancontroler_model->get_data_by_id($id,'driver_trips');
     //$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     $data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
     $data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
     
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/driver_tripupdate',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	function drivertripdelete(){
	    if(!checkPermission($this->session->userdata('permission'),'trip')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $id = $this->uri->segment(4);
 		$result = $this->Comancontroler_model->delete($id,'driver_trips','id');
 		if($result){
 			redirect('admin/driver_trip');
 		}
    } 
    
    function finance() {
        if(!checkPermission($this->session->userdata('permission'),'finance')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $unit = $driver = $sdate = $edate = $tracking = '';
         
        if($this->input->post('search'))	{
            $unit = $this->input->post('unit'); 
            
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
                if(date('Y-m',strtotime($sdate)) != date('Y-m',strtotime($edate))){
                    $this->session->set_flashdata('searchError', 'If you want to filter with week than both date must be same month.');
                    redirect(base_url('admin/finance'));
                } else {
                    $weeks = explode(',',$week);
                    //$sdate = $weeks[0];
                    //$edate = $weeks[1];
                    $sdate = date('Y-m',strtotime($sdate)).$weeks[0];
                    $edate = date('Y-m',strtotime($edate)).$weeks[1];
                }
            }
            
            $month = $this->input->post('month');
            if($month!='' && $month!='all'){
                $months = explode(',',$month);
                $sdate = $months[0];
                $edate = $months[1]; 
            }
        } else {
            if(date('d') < 9) { $sdate = date('Y-m-01'); $edate = date('Y-m-08'); }
            elseif(date('d') < 16) { $sdate = date('Y-m-09'); $edate = date('Y-m-15'); }
            elseif(date('d') < 24) { $sdate = date('Y-m-16'); $edate = date('Y-m-23'); }
            else { $sdate = date('Y-m-24'); $edate = date('Y-m-t'); }
        }
        
        $data['finance'] = $this->Comancontroler_model->get_search_for_finance($sdate,$edate,$unit);  
        $data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
        
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/finance',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	function financeupdate() {
        if(!checkPermission($this->session->userdata('permission'),'finance')){
	        redirect(base_url('AdminDashboard'));   
	    }
		$id = $this->uri->segment(4);
		if($this->input->post('save'))	{
				
				$this->form_validation->set_rules('total_amt', 'total amount','required|numeric|min_length[1]|max_length[30]');
				
				
				$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
				if ($this->form_validation->run() == FALSE){}
                else
                {  
                    
                    /*************** expence value and label **********/
                    $unit_driver = $unit_driverval = $unit_drivertxt = '';
			        $unit_driver_txt = $this->input->post('unit_driver_txt');
			        if(!empty($unit_driver_txt)){
			            $unit_drivertxt = implode('-~-',$unit_driver_txt);
			        }
			        $unit_driver_val = $this->input->post('unit_driver_val');
			        if(!empty($unit_driver_val)){
			            $unit_driverval = implode('-~-',$unit_driver_val);
			        }
			        $unit_driver = $unit_drivertxt.'--~~--'.$unit_driverval;
                    
                    /*************** expence value and label **********/
                    $expense = $expenseval = $expensetxt = '';
			        $expenses_txt = $this->input->post('expenses_txt');
			        if(!empty($expenses_txt)){
			            $expensetxt = implode('-~-',$expenses_txt);
			        }
			        $expenses_val = $this->input->post('expenses_val');
			        if(!empty($expenses_val)){
			            $expenseval = implode('-~-',$expenses_val);
			        }
			        $expense = $expensetxt.'--~~--'.$expenseval;
			        
			        /****** dispatch factoring value and label ******/
                    /*$dis_fact = $dis_factval = $dis_facttxt = '';
			        $dis_fact_txt = $this->input->post('dis_fact_txt');
			        if(!empty($dis_fact_txt)){
			            $dis_facttxt = implode('-~-',$dis_fact_txt);
			        }
			        $dis_fact_val = $this->input->post('dis_fact_val');
			        if(!empty($dis_fact_val)){
			            $dis_factval = implode('-~-',$dis_fact_val);
			        }
			        $dis_fact = $dis_facttxt.'--~~--'.$dis_factval;
			        */
			        /****** other option value and label ******/
                    $other_option = $other_optionval = $other_optiontxt = '';
			        $other_option_txt = $this->input->post('other_option_txt');
			        if(!empty($other_option_txt)){
			            $other_optiontxt = implode('-~-',$other_option_txt);
			        }
			        $other_option_val = $this->input->post('other_option_val');
			        if(!empty($other_option_val)){
			            $other_optionval = implode('-~-',$other_option_val);
			        }
			        $other_option = $other_optiontxt.'--~~--'.$other_optionval;
			        
			        
					$insert_data=array(
					    'expenses'=>$expense,
					    'unit_pay'=>$this->input->post('unit_pay'),
					    'driver_pay'=>$this->input->post('driver_pay'),
					    'total_expense'=>$this->input->post('total_expense'),
					    'total_income'=>$this->input->post('total_income'),
					    'total_amt'=>$this->input->post('total_amt'),
					    'other_option'=>$other_option
					);
				
					$res = $this->Comancontroler_model->update_table_by_id($id,'finance',$insert_data); 
					if($res){
						$this->session->set_flashdata('item', 'Updated successfully.');
                        redirect(base_url('admin/finance/update/'.$id));
					}
 				   
				}
		}
     
     $data['finance'] = $this->Comancontroler_model->get_data_by_id($id,'finance');
     
     $data['units'] = $data['driver_pay'] = array();
     
     if(!empty($data['finance'])) {
        $year_month = date('Y-m',strtotime($data['finance'][0]['fdate']));
        if($data['finance'][0]['fweek']=='01-08') { $sdate = $year_month.'-01'; }
        elseif($data['finance'][0]['fweek']=='09-15') { $sdate = $year_month.'-09'; }
        elseif($data['finance'][0]['fweek']=='16-23') { $sdate = $year_month.'-16'; }
        else { $sdate = $year_month.'-24'; }
        
        $edate = date('Y-m-d',strtotime("+7 days",strtotime($sdate)));
         
         $data['units'] = $this->Comancontroler_model->get_dispatch_for_finance($sdate,$edate,$data['finance'][0]['unit_id']);
         $data['driver_pay'] = $this->Comancontroler_model->get_driver_pay_for_finance($sdate,$edate,$data['finance'][0]['driver']);
     }
     //$data['cities'] = $this->Comancontroler_model->get_data_by_table('cities');
     //$data['locations'] = $this->Comancontroler_model->get_data_by_table('locations');
     //$data['drivers'] = $this->Comancontroler_model->get_data_by_table('drivers');
     
    	$this->load->view('admin/layout/header');
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/financeupdate',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	function multiple_view(){
	    if(!checkPermission($this->session->userdata('permission'),'finance')){
	        redirect(base_url('AdminDashboard'));   
	    }
	    $unit = $driver = $sdate = $edate = $tracking = '';
         
        if($this->input->post('search'))	{
            $unit = $this->input->post('unit'); 
            
            $week = $this->input->post('week');
            if($week!='' && $week!='all'){
                $weeks = explode(',',$week);
                $sdate = $weeks[0];
                $edate = $weeks[1];
            }
            if($this->input->post('sdate'))	{ $sdate = $this->input->post('sdate'); } 
            if($this->input->post('edate'))	{ $edate = $this->input->post('edate'); } 
            
            $month = $this->input->post('month');
            if($month!='' && $month!='all'){
                $months = explode(',',$month);
                $sdate = $months[0];
                $edate = $months[1]; 
            }
            $data['finance'] = $this->Comancontroler_model->get_search_for_finance_m_view($sdate,$edate,$unit);
        }
        else { $data['finance'] = array(); }
        
        $data['vehicles'] = $this->Comancontroler_model->get_data_by_table('vehicles');
        
    	$this->load->view('admin/layout/header'); 
    	$this->load->view('admin/layout/sidebar');
    	$this->load->view('admin/finance_multiple_view',$data);
    	$this->load->view('admin/layout/footer');
	}
	
	function financedelete(){
	    if(!checkPermission($this->session->userdata('permission'),'finance')){
	        redirect(base_url('AdminDashboard'));   
	    }
        $id = $this->uri->segment(4);
 		$result = $this->Comancontroler_model->delete($id,'finance','id');
 		if($result){
 			redirect('admin/finance');
 		}
    } 
    
    
}
?>