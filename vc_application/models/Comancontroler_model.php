<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comancontroler_model extends CI_model {
    
    public function __construct() {
        parent::__construct(); 
        $this->load->database(); 
    }
 
    public function get_distint_address($column='paddress'){
        $this->db->select('DISTINCT '.$column);
		$this->db->from('dispatch');
		$query = $this->db->get();
		return $query->result_array();
    }
    public function get_data_by_name($city){
        $this->db->select('id');
		$this->db->from('companies');
		$this->db->where('company',$city);
		$query = $this->db->get();
		return $query->result_array();
    }
    public function get_city_by_name($city){
        $this->db->select('id');
		$this->db->from('cities');
		$this->db->where('city',$city);
		$query = $this->db->get();
		return $query->result_array();
    }
    public function get_location_by_name($location){
        $this->db->select('id');
		$this->db->from('locations');
		$this->db->where('location',$location);
		$query = $this->db->get();
		return $query->result_array();
    }
    public function check_finance_entry($fweek,$driver,$fdate,$unitid){
        $this->db->select('id');
		$this->db->from('finance');
		$this->db->where('fweek',$fweek);
		$this->db->where('driver',$driver);
		$this->db->where('fdate',$fdate);
		$this->db->where('unit_id',$unitid);
		$query = $this->db->get();
		return $query->result_array();
    }
    public function check_value_in_table($where,$value,$table,$columns='id'){
        $this->db->select($columns);
		$this->db->from($table);
		$this->db->where($where,$value);
		$query = $this->db->get();
		return $query->result_array();
    }
    public function get_data_by_table($table,$columns='*',$orderby='',$order='desc',$status=''){
		$this->db->select($columns);
		$this->db->from($table);
		if($table == 'drivers' && $status == 'All') {
		    //$this->db->where('status',$status);
		} elseif($table == 'drivers' && $status == '') {
		    $this->db->where('status','Active');
		}
		// if(($table == 'truckingCompanies' && $status == 'Active')){
		// 	$this->db->where('status',$status);
		// }
		if($orderby != '') {
		    $this->db->order_by($orderby,$order);
		}
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_data_by_where_in($where,$value,$table,$column='*'){
		$this->db->select($column);
		$this->db->from($table);
		$this->db->where_in($where,$value);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_data_by_id($id,$table,$column='*'){
		$this->db->select($column);
		$this->db->from($table);
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_id_by_column_value($table, $column, $value, $useLike = false) {
		$this->db->select('id');
		if ($useLike) {
			$this->db->like($column, $value);
		} else {
			$this->db->where($column, $value);
		}	
		$row = $this->db->get($table)->row_array();
		// echo $this->db->last_query();exit;
		return $row ? $row['id'] : null;
	}

	public function get_data_by_column($column,$value,$table,$columns='*',$orderby='',$order='desc',$limit='', $warehouseCustomer='No'){
		$this->db->select($columns);
		$this->db->from($table);
		$this->db->where($column,$value);
		if ($warehouseCustomer == 'Yes') {
			$this->db->where('warehouseCustomer', 'Yes'); 
		}
		if($orderby != '') {
		    $this->db->order_by($orderby,$order);
		}
		if($limit != '') {
		    $this->db->limit($limit);
		}
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
	}
	public function get_data_by_multiple_column($where,$table,$columns='*',$orderby='',$order='desc',$limit=''){
		$this->db->select($columns);
		$this->db->from($table);
		$this->db->where($where);
		if($orderby != '') {
		    $this->db->order_by($orderby,$order);
		}
		if($limit != '') {
		    $this->db->limit($limit);
		}
		$query = $this->db->get();
		return $query->result_array();
	}
	public function update_table_by_id($id,$table,$data){
	   $this->db->where('id', $id);
       $this->db->update($table, $data);
	//    if($id==2216){
	// 	echo $this->db->last_query();exit;

	//    }
       return true;
	}
	public function update_table_by_column($column,$value,$table,$data){
	   $this->db->where($column, $value);
       $this->db->update($table, $data);
       return true;
	}
	public function update_table_by_multiple_column($where, $table, $data){
    $this->db->where($where); // this now accepts an array
    $this->db->update($table, $data);
    return $this->db->affected_rows() > 0; // optional: return true only if updated
}

	function delete($id,$table,$field) {
      $this->db->where($field, $id);
      $this->db->delete($table); 
      return true;
    }
	public function delete_by_multiple_columns($conditions = [],$table) {
    if (!empty($conditions) && is_array($conditions)) {
        $this->db->where($conditions);
        $this->db->delete($table);
        return true;
    }
    return false; // No conditions provided
}

    function add_data_in_table($data,$table) {
      $this->db->insert($table, $data);
      return $this->db->insert_id();
     }
    function getDataByDate($where,$sdate,$edate,$table,$column='*'){
        $this->db->select($column);
		$this->db->from($table);
		$this->db->where($where.' >=',$sdate);
		$this->db->where($where.' <=',$edate);
		$query = $this->db->get();
		return $query->result_array();
    }
    public function getDataWithLike($where,$value,$table,$columns='*',$limit='0',$orderby='',$order='asc'){
        $this->db->select($columns);
		$this->db->from($table);
		if($table == 'companies'){
		    $this->db->where('paymenTerms !=','Deleted');
		}
		$this->db->like($where,$value);
		if($orderby != '') { $this->db->order_by($orderby,$order); }
		if($limit > 0) { $this->db->limit($limit); }
		$query = $this->db->get();
		return $query->result_array();
    }
    function get_dispatchinfo_by_id($id,$columns='*',$table='dispatchExtraInfo'){
        $this->db->select($columns);
		$this->db->from($table);
		$this->db->where('dispatchid',$id); 
		$this->db->order_by('pd_order','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->result_array();
    }
	function get_extradispatchinfo_by_id($id,$columns='*',$table='dispatchExtraInfo',  $type='pickup', $order='ASC'){
		$where = " 1=1 AND dispatchid=$id ";
		$sql="SELECT $columns FROM $table";
		if($type=='pickup'){	
			$sql .=" LEFT join companyAddress ON companyAddress.id=$table.pd_addressid";	
			$where .= " AND pd_type = 'pickup'";
		}else{
			$sql .=" LEFT join companyAddress ON companyAddress.id=$table.pd_addressid";
			$where .= " AND pd_type = 'dropoff'";
		}
		$sql .= " WHERE $where ORDER BY pd_order $order";
		// echo $sql;exit;
		$query = $this->db->query($sql);
		return $query->result_array();
    }
    function get_dispachLog($table,$did){
        $this->db->select('d.*,u.uname');
		$this->db->from($table.' as d');
		$this->db->join('admin_login as u','d.userid=u.id','left');
		$this->db->where('d.did',$did); 
		$this->db->order_by('d.id','desc');
		$query = $this->db->get();
		return $query->result_array();
    }
	function get_reminderLog($type,$did){
        $this->db->select('d.*,u.uname');
		$this->db->from('receivable_statment_history as d');
		$this->db->join('admin_login as u','d.user_id=u.id','left');
		$this->db->where('d.did',$did); 
		$this->db->where('d.dispatch_type',$type); 
		$this->db->order_by('d.id','desc');
		$query = $this->db->get();
		return $query->result_array();
    }
    function getCurrentReimbursement($date){
        $this->db->select('r.*,d.dname');
		$this->db->from('reimbursement as r');
		$this->db->join('drivers as d','d.id=r.driver_id','left'); 
		//$this->db->like('r.fdate',$date); 
		$this->db->where('r.rembursCheck','0'); 
		$query = $this->db->get();
		return $query->result_array();
    }
    function getServicesByDate($sdate,$edate){
        $this->db->select('s.nextServiceDate,e.trailer,p.title');
		$this->db->from('services as s');
		$this->db->join('equipment as e','e.id = s.equipment','left');
		$this->db->join('preService as p','p.id = s.repair','left');
		$this->db->where('s.nextServiceDate >=',$sdate);
		$this->db->where('s.nextServiceDate <=',$edate);
		$query = $this->db->get();
		return $query->result_array();
    }
    function update_driver_trips($driver_trip,$driver,$pudate){
        $this->db->where('driver', $driver);
        $this->db->where('tripdate', $pudate);
       $this->db->update('driver_trips', $driver_trip);
       return true;
    }
    function check_dirver_dispatch_by_date($driver,$pudate,$table='dispatch'){
        $this->db->select('id,invoice');
		$this->db->from($table);
		$this->db->where('driver',$driver);
		$this->db->where('pudate',$pudate);
		$query = $this->db->get();
		return $query->result_array();
    }
    function check_dirver_trip($driver,$pudate){
        $this->db->select('id,trip1,trip2,trip3,trip4');
		$this->db->from('driver_trips');
		$this->db->where('driver',$driver);
		$this->db->where('tripdate',$pudate);
		$query = $this->db->get();
		return $query->result_array();
    }
    function get_driver_trip_by_filter($sdate='',$edate='',$driver=''){
        $this->db->select('*');
		$this->db->from('driver_trips');
		if($driver!='') { $this->db->where('driver',$driver); }
		if($sdate!='') { $this->db->where('tripdate >=',$sdate); }
		if($edate!='') { $this->db->where('tripdate <=',$edate); }
		$query = $this->db->get();
		return $query->result_array();
    }
    function getServicesFilter($startDate='',$endDate='',$equipment='',$service=''){
        $this->db->select('s.id, s.serviceDate, s.nextServiceDate, s.coast, s.vendor, e.trailer as equipment, p.title as repair');
		$this->db->from('services as s');
		$this->db->join('equipment as e','e.id=s.equipment','left');
		$this->db->join('preService as p','p.id=s.repair','left'); 
		if($startDate != '') { $this->db->where('s.serviceDate >=',$startDate); }
		if($endDate != '') { $this->db->where('s.serviceDate <=',$endDate); }
		if($equipment != '') { $this->db->where('s.equipment',$equipment); }
		if($service != '') { $this->db->where('s.repair',$service); }
		$query = $this->db->get();
		return $query->result_array();
    }
    function getExtraDispatchInfo_old($id){
        $this->db->select('d.id,d.pudate1,d.dodate1,d.paddress1,d.daddress1,d.pcode1,d.dcode1, d.ptime1,d.dtime1,d.pnotes1,d.dnotes1,pl.location as plocation1, dl.location as dlocation1,pc.city as pcity1, dc.city as dcity1');
		$this->db->from('dispatchExtra as d');
		$this->db->join('locations as pl','pl.id=d.plocation1','left');
		$this->db->join('locations as dl','dl.id=d.dlocation1','left');
		$this->db->join('cities as pc','pc.id=d.pcity1','left');
		$this->db->join('cities as dc','dc.id=d.dcity1','left');
		$this->db->where('d.dispatchid',$id);
		$query = $this->db->get();
		return $query->result_array();
    }
    function getExtraDispatchInfo($id){
        $this->db->select('d.*,pl.location as pd_location, pl.location as ppd_location, pc.city as pd_city, pc.city as ppd_city');
		$this->db->from('dispatchExtraInfo as d');
		$this->db->join('locations as pl','pl.id=d.pd_location','left');
		$this->db->join('cities as pc','pc.id=d.pd_city','left');
		$this->db->where('d.dispatchid',$id);
		$this->db->order_by('d.pd_order','asc');
		$query = $this->db->get();
		return $query->result_array();
    }
    function getExtraOutsideDispatchInfo($id){
        $this->db->select('d.id, d.pd_type, d.pd_date,d.pd_address,d.pd_code, d.pd_time,d.pd_notes,d.pd_title,d.pd_port,d.pd_portaddress,d.pd_meta,pl.location as pd_location,pl.location as ppd_location, pc.city as pd_city, pc.city as ppd_city, d.pd_addressid');
		$this->db->from('dispatchOutsideExtraInfo as d');
		$this->db->join('locations as pl','pl.id=d.pd_location','left');
		$this->db->join('cities as pc','pc.id=d.pd_city','left');
		$this->db->where('d.dispatchid',$id);
		$this->db->order_by('d.pd_order','asc');
		$query = $this->db->get();
		return $query->result_array();
    }
	 function getExtraWarehouseDispatchInfo($id){
        $this->db->select('d.id, d.pd_type, d.pd_date,d.pd_address,d.pd_code, d.pd_time,d.pd_notes,d.pd_title,d.pd_port,d.pd_portaddress,d.pd_meta,pl.location as pd_location,pl.location as ppd_location, pc.city as pd_city, pc.city as ppd_city, d.pd_addressid,d.appointmentType,d.quantity,d.metaDescription,d.weight,d.commodity,d.receivingHours');
		$this->db->from('warehouse_dispatch_extra_info as d');
		$this->db->join('locations as pl','pl.id=d.pd_location','left');
		$this->db->join('cities as pc','pc.id=d.pd_city','left');
		$this->db->where('d.dispatchid',$id);
		$this->db->order_by('d.pd_order','asc');
		$query = $this->db->get();
		return $query->result_array();
    }
     function get_driver_tips_list($id=''){
        $this->db->select('t.*,d.pudate,d.dodate,d.vehicle,d.rate,dc.city as dcity,pc.city as pcity,pl.location as plocation,dl.location as dlocation');
		$this->db->from('driver_trips as t');
		$this->db->join('dispatch as d','d.id=t.dispatch','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		if($id!='') { $this->db->where('t.id',$id); }
		$query = $this->db->get();
		return $query->result_array(); 
     }
     function get_driver_trip_by_id($id){
         $this->db->select('t.*,d.pudate,d.dodate,d.vehicle,d.dcity,d.pcity,d.dlocation,d.plocation');
		$this->db->from('driver_trips as t');
		$this->db->join('dispatch as d','d.id=t.dispatch','left');
		$this->db->where('t.id',$id);
		$query = $this->db->get();
		return $query->result_array();
     }
     function get_driver_pay_by_week($sdate,$edate){
        $this->db->select('d.rate,d.driver,dr.dcode');
		$this->db->from('driver_trips as d');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->where('d.tripdate >=',$sdate); 
		$this->db->where('d.tripdate <=',$edate);  
		$query = $this->db->get();
		return $query->result_array();
     }
     function getDriverShift($sdate,$edate,$driver=''){
        $this->db->select('s.*,d.dname');
		$this->db->from('driver_shift as s');
		$this->db->join('drivers as d','d.id=s.driver_id','left');
		$this->db->where('s.start_date >=',$sdate); 
		$this->db->where('s.end_date <=',$edate);  
		if($driver != '') { $this->db->where('s.driver_id',$driver);  }
		$query = $this->db->get();
		return $query->result_array();
     }
     function get_driver_pay_for_finance($sdate,$edate,$driver){
        $this->db->select('d.rate,d.driver,dr.dcode');
		$this->db->from('driver_trips as d');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->where('d.tripdate >=',$sdate); 
		$this->db->where('d.tripdate <=',$edate); 
		$this->db->where('d.driver',$driver);
		$query = $this->db->get();
		return $query->result_array();
     }
     function get_search_for_finance_m_view($sdate='',$edate='',$unit=''){
        $this->db->select('f.*,v.vname,d.dname');
		$this->db->from('finance as f');
		$this->db->join('vehicles as v','v.id=f.unit_id','left');
		$this->db->join('drivers as d','d.id=f.driver','left');
		if($sdate!='') { $this->db->where('f.fdate >=',$sdate); }
		if($edate!='') { $this->db->where('f.fdate <=',$edate); }
		if($unit!='') { $this->db->where('f.unit_id',$unit);  }
		$this->db->limit(8);
		$query = $this->db->get();
		return $query->result_array();  
     }
     function get_search_for_finance($sdate='',$edate='',$unit=''){
        $this->db->select('f.id,f.fweek,f.fdate,f.total_expense,f.total_income,f.total_amt,v.vname');
		$this->db->from('finance as f');
		$this->db->join('vehicles as v','v.id=f.unit_id','left');
		if($sdate!='') { $this->db->where('f.fdate >=',$sdate); }
		if($edate!='') { $this->db->where('f.fdate <=',$edate); }
		if($unit!='') { $this->db->where('f.unit_id',$unit);  }
		$query = $this->db->get();
		return $query->result_array();   
     }
     function getCurrentTrip($date){
        $this->db->select('d.id as dispatchid,d.dispatchMeta,d.delivered,d.vehicle,d.driver,d.rate,d.pudate,d.ptime,d.dodate,d.dtime,d.tracking,d.driver_status,d.status,dr.dname,v.vname,v.vnumber,c.city as pcity,l.location as plocation,cd.city as dcity,ld.location as dlocation,com.company');
		$this->db->from('dispatch as d');
		$this->db->join('vehicles as v','v.id=d.vehicle','left');
		$this->db->join('companies as com','com.id=d.company','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->join('locations as l','l.id=d.plocation','left');
		$this->db->join('cities as c','c.id=d.pcity','left');
		$this->db->join('locations as ld','ld.id=d.dlocation','left');
		$this->db->join('cities as cd','cd.id=d.dcity','left');
		//$this->db->where('d.pudate',$date); 
		$this->db->where('d.delivered !=','yes'); 
		$this->db->or_where('d.delivered',NULL); 
		$query = $this->db->get();
		return $query->result_array(); 
     }
     function get_dispatch_for_finance($sdate,$edate,$unit){
        $this->db->select('d.vehicle,d.driver,d.rate,v.vname,dr.dcode');
		$this->db->from('dispatch as d');
		$this->db->join('vehicles as v','v.id=d.vehicle','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->where('d.pudate >=',$sdate); 
		$this->db->where('d.pudate <=',$edate); 
		$this->db->where('d.vehicle',$unit);  
		$query = $this->db->get();
		return $query->result_array(); 
     }
     function get_dispatch_by_week($sdate,$edate){
        $this->db->select('d.vehicle,d.driver,d.rate,v.vname,dr.dcode');
		$this->db->from('dispatch as d');
		$this->db->join('vehicles as v','v.id=d.vehicle','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->where('d.pudate >=',$sdate); 
		$this->db->where('d.pudate <=',$edate); 
		//$this->db->group_by('d.vehicle');  
		$query = $this->db->get();
		return $query->result_array(); 
     }
     function get_dispatch_for_trip($sdate='',$edate='',$unit=''){
        $this->db->select('d.id,d.pudate,d.dodate,d.vehicle,c.city,l.location,dr.dname');
		$this->db->from('dispatch as d');
		$this->db->join('locations as l','l.id=d.plocation','left');
		$this->db->join('cities as c','c.id=d.pcity','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		
		if($sdate!='') { $this->db->where('d.pudate >=',$sdate); }
		if($edate!='') { $this->db->where('d.pudate <=',$edate); }
		$query = $this->db->get();
		return $query->result_array();
    }
    
     function get_dispatch_for_calendar($sdate='',$edate='',$unit=''){
        $this->db->select('d.id,d.pudate,d.dodate,d.vehicle,c.city,l.location,dr.dname as driver,dr.dcode');
		$this->db->from('dispatch as d');
		$this->db->join('locations as l','l.id=d.plocation','left');
		$this->db->join('cities as c','c.id=d.pcity','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		
		if($sdate!='') { $this->db->where('d.pudate >=',$sdate); }
		if($edate!='') { $this->db->where('d.pudate <=',$edate); }
		if($unit!='') { $this->db->where('d.vehicle',$unit); }
		$query = $this->db->get();
		return $query->result_array();
    }
    
    function downloadDispatchCSV($sdate='',$edate='',$unit='',$driver='',$status='',$invoice='',$tracking='',$parent=''){
        $this->db->select('d.*,v.vname,v.vnumber,c.company as ccompany,dr.dname,pc.city as ppcity, dc.city as ddcity,pl.location as pplocation,dl.location as ddlocation');
		$this->db->from('dispatch as d'); 
		$this->db->join('vehicles as v','v.id=d.vehicle','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('companies as c','c.id=d.company','left');
		
		//if($sdate!='') { $this->db->where('d.pudate >=',$sdate); }
		//if($edate!='') { $this->db->where('d.pudate <=',$edate); }
		if ($sdate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('d.pudate >=', $sdate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('d.dodate >=', $sdate);
            $this->db->where('d.dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
        if ($edate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('d.pudate <=', $edate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('d.dodate <=', $edate);
            $this->db->where('d.dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
		if($unit!='') { $this->db->where('d.vehicle',$unit); }
		if($driver!='') { $this->db->where('d.driver',$driver); }
		if($status!='') { $this->db->where('d.status',$status); }
		if($invoice!='') { $this->db->like('d.invoice',$invoice); }
		if($tracking!='') { $this->db->like('d.tracking',$tracking); }
		if(is_array($parent)) { $this->db->where_in('parentInvoice',$parent); }
		$query = $this->db->get();
		return $query->result_array();
    }
    function downloadDispatchInvoice($id,$table='dispatch'){
        //$this->db->select('d.*,v.vname,v.vnumber,c.company as ccompany,c.address as caddress,c.email as cemail,c.phone as cphone,c.contactPerson,dr.dname,pc.city as ppcity, dc.city as ddcity,pl.location as pplocation,dl.location as ddlocation');
		$this->db->select('d.*,shipping_contacts.department as cdepartment,shipping_contacts.designation as cdesignation,c.company as ccompany,c.address as caddress, shipping_contacts.email as cemail, c.email as invoice_email, c.email2 as cemail2,shipping_contacts.phone as cphone,shipping_contacts.contact_person as contactPerson, pc.city as ppcity, dc.city as ddcity,pl.location as pplocation,dl.location as ddlocation');
		$this->db->from($table.' as d'); 
		//$this->db->join('vehicles as v','v.id=d.vehicle','left'); 
		//$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('companies as c','c.id=d.company','left');
		$this->db->join('company_shipping_contacts as shipping_contacts','shipping_contacts.id=d.shipping_contact','left');
		$this->db->where('d.id',$id);
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
    }
    
    function get_dispatch_by_filter_new($sdate='',$edate='',$unit='',$driver='',$status='',$invoice='',$tracking=''){
        $this->db->select('d*');
		$this->db->from('dispatch as d');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		
		if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		if($edate!='') { $this->db->where('pudate <=',$edate); }
		if($unit!='') { $this->db->where('vehicle',$unit); }
		if($driver!='') { $this->db->where('driver',$driver); }
		if($status!='') { $this->db->where('status',$status); }
		if($invoice!='') { $this->db->like('invoice',$invoice); }
		if($tracking!='') { $this->db->like('tracking',$tracking); }
		$query = $this->db->get();
		return $query->result_array();
    }
    
    function get_dispatch_by_filter000($sdate='',$edate='',$unit='',$driver='',$status='',$invoice='',$tracking=''){
        $date = date('Y-m-d');
        $sdate = isset($sdate) && $sdate != '' ? $sdate : $date;  // or some default value
        $edate = isset($edate) && $edate != '' ? $edate : $date;  // or some default value
        $unit = isset($unit) && $unit != '' ? $unit : NULL;
        $driver = isset($driver) && $driver != '' ? $driver : NULL;
        $status = isset($status) && $status != '' ? $status : NULL;
        $invoice = isset($invoice) && $invoice != '' ? $invoice : NULL;
        $tracking = isset($tracking) && $tracking != '' ? $tracking : NULL;
        
        echo $query = "
        SELECT 
            d.*, 
            de.pd_city, 
            de.pd_location
        FROM 
            dispatch AS d
        LEFT JOIN 
            dispatchExtraInfo AS de 
            ON de.dispatchid = d.id
        WHERE 
            (d.pudate >= IFNULL('$sdate', d.pudate)) AND
            (d.pudate <= IFNULL('$edate', d.pudate)) AND
            (d.vehicle = IFNULL('$unit', d.vehicle)) AND
            (d.driver = IFNULL('$driver', d.driver)) AND
            (d.status = IFNULL('$status', d.status)) AND
            (d.invoice LIKE IFNULL(CONCAT('%', '$invoice', '%'), d.invoice)) AND
            (d.tracking LIKE IFNULL(CONCAT('%', '$tracking', '%'), d.tracking))
        GROUP BY 
            de.dispatchid, d.id
        ORDER BY 
            d.pudate ASC, 
            d.driver ASC, 
            de.id DESC;
        ";
        
        // Execute the query using your preferred method
        $query = $this->db->query($query);
        print_r($query);die();
		//$query = $this->db->get();
		return $query->result_array();
    }
    function get_dispatch_by_filternnnnnnn($sdate='',$edate='',$unit='',$driver='',$status='',$invoice='',$tracking=''){
        $this->db->select('d.*');
		$this->db->from('dispatch as d');
		$this->db->join('dispatch as d2', 'd.invoice = d2.parentInvoice', 'left');
		if($sdate!='') { $this->db->where('d.pudate >=',$sdate); }
		if($edate!='') { $this->db->where('d.pudate <=',$edate); }
		if($unit!='') { $this->db->where('d.vehicle',$unit); }
		if($driver!='') { $this->db->where('d.driver',$driver); }
		if($status!='') { $this->db->where('d.status',$status); }
		if($invoice!='') { $this->db->where('d.invoiceType',$invoice); }
		//if($invoice!='') { $this->db->like('d.invoice',$invoice); }
		if($tracking!='') { $this->db->like('d.tracking',$tracking); }
		//$this->db->where('d.parentInvoice','');
		$this->db->order_by('d.pudate','asc');
		$this->db->order_by('d.driver','asc');
		$this->db->order_by('d.invoice','asc');
		$query = $this->db->get();
		return $query->result_array();
    }
    function get_dispatch_by_filter($sdate='',$edate='',$unit='',$driver='',$status='',$invoice='',$tracking='',$parent='no'){
        /*$query = "SELECT d.*, de.pd_city, de.pd_location, de.pd_date, de.pd_time FROM dispatch AS d LEFT JOIN dispatchExtraInfo AS de ON de.dispatchid = d.id WHERE (d.pudate >= IFNULL('$sdate', d.pudate)) AND (d.pudate <= IFNULL('$edate', d.pudate)) ";
            if($unit != '') { $query .= " AND (d.vehicle = IFNULL('$unit', d.vehicle)) "; }
            if($driver != '') { $query .= " AND (d.driver = IFNULL('$driver', d.driver)) "; }
            if($status != '') { $query .= " AND (d.status = IFNULL('$status', d.status)) "; }
            if($invoice != '') { $query .= " AND (d.invoice LIKE IFNULL(CONCAT('%', '$invoice', '%'), d.invoice)) "; }
            if($tracking != '') { $query .= " AND (d.tracking LIKE IFNULL(CONCAT('%', '$tracking', '%'), d.tracking)) "; }
            
			$query .= " GROUP BY de.dispatchid, d.id ORDER BY d.pudate DESC, de.pd_order DESC;";
            $result = $this->db->query($query);
            return $result->result_array();*/
        $this->db->select('*');
		$this->db->from('dispatch');
		//if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		//if($edate!='') { $this->db->where('pudate <=',$edate); }
		
		if ($sdate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('pudate >=', $sdate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('dodate >=', $sdate);
            $this->db->where('dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
        if ($edate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('pudate <=', $edate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('dodate <=', $edate);
            $this->db->where('dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }

		if($unit!='') { $this->db->where('vehicle',$unit); }
		if($driver!='') { $this->db->where('driver',$driver); }
		if($status!='') { $this->db->where('status',$status); }
		if($invoice!='') { $this->db->where('invoiceType',$invoice); }
		//if($invoice!='') { $this->db->like('invoice',$invoice); }
		if($tracking!='') { $this->db->like('tracking',$tracking); }
		if($parent=='no') { $this->db->where('parentInvoice',''); }
		if(is_array($parent)) { $this->db->where_in('parentInvoice',$parent); }
		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		$this->db->order_by('invoice','asc');
		$query = $this->db->get();
		return $query->result_array();
    }
    function downloadDispatchOutsideCSV_backup($sdate='',$edate='',$truckingCompany='',$driver='',$status='',$invoice='',$tracking='',$parent=''){
        $this->db->select('d.*,tc.company as ttruckingCompany,bc.company as bbookedUnder,c.company as ccompany,dr.dname,pc.city as ppcity, dc.city as ddcity,pl.location as pplocation,dl.location as ddlocation');
		$this->db->from('dispatchOutside as d'); 
		$this->db->join('truckingCompanies as tc','tc.id=d.truckingCompany','left');
		$this->db->join('truckingCompanies as bc','bc.id=d.bookedUnder','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('companies as c','c.id=d.company','left');

		//if($sdate!='') { $this->db->where('d.pudate >=',$sdate); }
		//if($edate!='') { $this->db->where('d.pudate <=',$edate); }
		
		if ($sdate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('d.pudate >=', $sdate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('d.dodate >=', $sdate);
            $this->db->where('d.dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
        if ($edate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('d.pudate <=', $edate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('d.dodate <=', $edate);
            $this->db->where('d.dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
		if($truckingCompany!='') { $this->db->where('d.truckingCompany',$truckingCompany); }
		if($driver!='') { $this->db->where('d.driver',$driver); }
		if($status!='') { $this->db->where('d.status',$status); }
		if($invoice!='') { $this->db->like('d.invoice',$invoice); }
		if($tracking!='') { $this->db->like('d.tracking',$tracking); }
		if(is_array($parent)) { $this->db->where_in('parentInvoice',$parent); }
		//$this->db->order_by('d.pudate','asc');
		//$this->db->order_by('d.driver','asc');
		//$this->db->order_by('d.invoice','asc');
		$query = $this->db->get();

		/* 
		//////////////////////////
		Shipments awaiting Closure
		/////////////////////////
		$sql="SELECT d.*,tc.company as ttruckingCompany,bc.company as bbookedUnder,c.company as ccompany,dr.dname,pc.city as ppcity, dc.city as ddcity,pl.location as pplocation,dl.location as ddlocation
        FROM `dispatchOutside` AS `d`
        LEFT JOIN truckingCompanies as tc ON tc.id=d.truckingCompany
        LEFT join truckingCompanies as bc ON bc.id=d.bookedUnder
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join locations as pl  ON pl.id=d.plocation
        LEFT join locations as dl  ON dl.id=d.dlocation
        LEFT join cities as pc  ON pc.id=d.pcity
        LEFT join cities as dc  ON dc.id=d.dcity
		LEFT join companies as c  ON c.id=d.company
        WHERE `d`.`delivered` != 'yes' OR `d`.`delivered` IS NULL";
		$query=$this->db->query($sql);
		*/
		return $query->result_array();
    }
	function downloadDispatchOutsideCSV($sdate='',$edate='',$truckingCompany='',$driver='',$status='',$invoice='',$tracking='',$parent=''){
        $this->db->select('d.*,tc.company as ttruckingCompany,bc.company as bbookedUnder,book_under_new.company as bookUnderNew,c.company as ccompany,dr.dname,pc.city as ppcity, dc.city as ddcity,pl.location as pplocation,dl.location as ddlocation');
		$this->db->from('dispatchOutside as d'); 
		$this->db->join('truckingCompanies as tc','tc.id=d.truckingCompany','left');
		$this->db->join('truckingCompanies as bc','bc.id=d.bookedUnder','left');
		$this->db->join('booked_under as book_under_new','book_under_new.id=d.bookedUnderNew','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('companies as c','c.id=d.company','left');

		//if($sdate!='') { $this->db->where('d.pudate >=',$sdate); }
		//if($edate!='') { $this->db->where('d.pudate <=',$edate); }
		
		if ($sdate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('d.pudate >=', $sdate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('d.dodate >=', $sdate);
            $this->db->where('d.dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
        if ($edate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('d.pudate <=', $edate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('d.dodate <=', $edate);
            $this->db->where('d.dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
		if($truckingCompany!='') { $this->db->where('d.truckingCompany',$truckingCompany); }
		if($driver!='') { $this->db->where('d.driver',$driver); }
		if($status!='') { $this->db->where('d.status',$status); }
		if($invoice!='') { $this->db->like('d.invoice',$invoice); }
		if($tracking!='') { $this->db->like('d.tracking',$tracking); }
		if(is_array($parent)) { $this->db->where_in('parentInvoice',$parent); }
		//$this->db->order_by('d.pudate','asc');
		//$this->db->order_by('d.driver','asc');
		//$this->db->order_by('d.invoice','asc');
		$query = $this->db->get();

		/* 
		//////////////////////////
		Shipments awaiting Closure
		/////////////////////////
		$sql="SELECT d.*,tc.company as ttruckingCompany,bc.company as bbookedUnder,c.company as ccompany,dr.dname,pc.city as ppcity, dc.city as ddcity,pl.location as pplocation,dl.location as ddlocation
        FROM `dispatchOutside` AS `d`
        LEFT JOIN truckingCompanies as tc ON tc.id=d.truckingCompany
        LEFT join truckingCompanies as bc ON bc.id=d.bookedUnder
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join locations as pl  ON pl.id=d.plocation
        LEFT join locations as dl  ON dl.id=d.dlocation
        LEFT join cities as pc  ON pc.id=d.pcity
        LEFT join cities as dc  ON dc.id=d.dcity
		LEFT join companies as c  ON c.id=d.company
        WHERE `d`.`delivered` != 'yes' OR `d`.`delivered` IS NULL";
		$query=$this->db->query($sql);
		*/
		return $query->result_array();
    }
    function get_dispatchOutside_by_filter($sdate='',$edate='',$company='',$truckingCompany='',$driver='',$status='',$invoice='',$tracking='',$dispatchInfoValue='',$dispatchInfo='',$parent='no'){
        $this->db->select('*');
		$this->db->from('dispatchOutside');
		//if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		//if($edate!='') { $this->db->where('pudate <=',$edate); }
		
		if ($sdate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('pudate >=', $sdate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('dodate >=', $sdate);
            $this->db->where('dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
        if ($edate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('pudate <=', $edate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('dodate <=', $edate);
            $this->db->where('dodate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
		if($company!='') { $this->db->where('company',$company); }        
		if($truckingCompany!='') { $this->db->where('truckingCompany',$truckingCompany); }
		if($driver!='') { $this->db->where('driver',$driver); }
		if($status!='') { $this->db->where('status',$status); }
		if($invoice!='') { $this->db->like('invoice',$invoice); }
		if($tracking!='') { $this->db->like('tracking',$tracking); }
		if($dispatchInfoValue!='' && $dispatchInfo != '') { 
			$dispatchMeta = '"'.$dispatchInfo.'","'.$dispatchInfoValue;
			$this->db->like('dispatchMeta',$dispatchMeta); 
		} elseif($dispatchInfo!='') { 
			$dispatchMeta = '"'.$dispatchInfo.'"';
			$this->db->like('dispatchMeta',$dispatchMeta); 
		} elseif($dispatchInfoValue != '') { 
			$dispatchMeta = '"'.$dispatchInfoValue;
			$this->db->like('dispatchMeta',$dispatchMeta); 
		}
		if($parent=='no') { $this->db->where('parentInvoice',''); }
		if(is_array($parent)) { $this->db->where_in('parentInvoice',$parent); }
		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		$this->db->order_by('invoice','asc');
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
    }
	function get_dispatchWarehouse_by_filter($sdate='',$edate='',$company='',$truckingCompany='',$driver='',$status='',$invoice='',$tracking='',$dispatchInfoValue='',$dispatchInfo='',$parent='no'){
        $this->db->select('*');
		$this->db->from('warehouse_dispatch');
		
		if ($sdate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('pudate >=', $sdate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('edate >=', $sdate);
            $this->db->where('edate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
        if ($edate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('pudate <=', $edate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('edate <=', $edate);
            $this->db->where('edate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
		if($company!='') { $this->db->where('company',$company); }        
		if($truckingCompany!='') { $this->db->where('truckingCompany',$truckingCompany); }
		if($driver!='') { $this->db->where('driver',$driver); }
		if($status!='') { $this->db->where('status',$status); }
		if($invoice!='') { $this->db->like('invoice',$invoice); }
		if($tracking!='') { $this->db->like('tracking',$tracking); }
		
		if($parent=='no') { 
			$this->db->group_start()
	        ->where('parentInvoice', '')
    		->or_where('parentInvoice IS NULL', null, false)
        	->group_end(); 
		}

		if(is_array($parent)) { $this->db->where_in('parentInvoice',$parent); }
		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		$this->db->order_by('invoice','asc');
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
    }
	  function downloadWarehouseDispatchCSV($sdate='',$edate='', $company='', $truckingCompany='',$driver='',$status='',$invoice='',$tracking='',$parent='no'){
        $this->db->select('d.*,tc.company as ttruckingCompany,bc.company as bbookedUnder,c.company as ccompany,dr.dname,pc.city as ppcity, dc.city as ddcity,pl.location as pplocation,dl.location as ddlocation');
		$this->db->from('warehouse_dispatch as d'); 
		$this->db->join('truckingCompanies as tc','tc.id=d.truckingCompany','left');
		$this->db->join('truckingCompanies as bc','bc.id=d.bookedUnder','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('companies as c','c.id=d.company','left');
		
		if ($sdate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('d.pudate >=', $sdate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('d.edate >=', $sdate);
            $this->db->where('d.edate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        
        if ($edate != '') {
            $this->db->group_start(); // Start a group for date conditions
            $this->db->where('d.pudate <=', $edate);
            $this->db->or_group_start(); // Start a subgroup for dodate
            $this->db->where('d.edate <=', $edate);
            $this->db->where('d.edate !=', '0000-00-00'); // dodate should not be '0000-00-00'
            $this->db->group_end(); // End the subgroup
            $this->db->group_end(); // End the main group
        }
        if($company!='') { $this->db->where('d.company',$company); }        
		if($truckingCompany!='') { $this->db->where('d.truckingCompany',$truckingCompany); }
		if($driver!='') { $this->db->where('d.driver',$driver); }
		if($status!='') { $this->db->where('d.status',$status); }
		if($invoice!='') { $this->db->like('d.invoice',$invoice); }
		if($tracking!='') { $this->db->like('d.tracking',$tracking); }
		if($parent=='no') { 
			$this->db->group_start()
	        ->where('parentInvoice', '')
    		->or_where('parentInvoice IS NULL', null, false)
        	->group_end(); 
		}
		if(is_array($parent)) { $this->db->where_in('parentInvoice',$parent); }
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
    }
	function getDispatchDataForBol($id,$table){
		$sql = "SELECT pickup_address.company as pickup_company, pickup_address.address as pickup_address, pickup_address.city as pickup_city,pickup_address.state as pickup_state, d.invoice, drop_address.company as drop_company, drop_address.address as drop_address, drop_address.city as drop_city,drop_address.state as drop_state, tc.company as carrier, d.trailer, d.tracking, d.dispatchMeta FROM $table as d
		JOIN truckingCompanies tc ON d.truckingCompany=tc.id
		JOIN booked_under ON d.bookedUnderNew=booked_under.id
		JOIN companies company ON d.company=company.id
		LEFT JOIN companyAddress pickup_address ON d.paddressid=pickup_address.id
		LEFT JOIN companyAddress drop_address ON d.daddressid=drop_address.id
		WHERE d.id=$id";
		// echo $sql;exit;
		$query = $this->db->query($sql);
		return $query->result_array();
	}
    function get_invoice_by_filter($table,$sdate='',$edate='',$company='',$driver='',$invoiceType='',$invoiceStatus=''){
        $this->db->select('*');
		$this->db->from($table);
		if($sdate==''  && $edate=='' && $company=='' && $driver=='' && $invoiceType=='' && $invoiceStatus==''){
			if (empty($sdate)) {
				$sdate = date('Y-m-01');
			}
			if (empty($edate)) {
				$edate = date('Y-m-t');
			}
					
			$this->db->where('pudate >=', $sdate);
			$this->db->where('pudate <=', $edate);
			$this->db->where('driver_status', 'Shipment Delivered'); 
			if($table == 'warehouse_dispatch'){
				$this->db->group_start();
					$this->db->where("invoiceReady = '0'");
					$this->db->or_where("invoiceReady = ''");
				$this->db->group_end();			
			}else{
				$this->db->group_start();
					$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceReady') = '0'");
					$this->db->or_where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceReady') = ''");
				$this->db->group_end();
			}
		}else{
			if($sdate!='') { $this->db->where('pudate >=',$sdate); }
			if($edate!='') { $this->db->where('pudate <=',$edate); }
			if($driver!='') { $this->db->where('driver',$driver); }
			if($company!='') { $this->db->where('company',$company); }
			if($invoiceType == ''){
				$this->db->where_in('invoiceType',array('Direct Bill','Quick Pay' ,'RTS'));
			} else {
				$this->db->where('invoiceType',$invoiceType);
			}
			if($invoiceStatus!='') {
				if($table == 'warehouse_dispatch'){
					if($invoiceStatus == 'invoiceReady'){
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
					}
					if($invoiceStatus == 'invoiced'){
						$this->db->where("invoiceReady = '1'");
						$this->db->where("invoiced = '1'");
						$this->db->group_start();
							$this->db->where("invoicePaid = '0'");
							$this->db->or_where("invoicePaid = ''");
						$this->db->group_end();
						$this->db->group_start();
							$this->db->where("invoiceClose = '0'");
							$this->db->or_where("invoiceClose = ''");
						$this->db->group_end();
					}
					if($invoiceStatus == 'invoicePaid'){
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceReady') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiced') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoicePaid') = '1'");
						$this->db->group_start();
							$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = '0'");
							$this->db->or_where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = ''");
						$this->db->group_end();
					}
					if($invoiceStatus == 'invoiceClose'){
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceReady') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiced') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoicePaid') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = '1'");
					}
				}else{
					if($invoiceStatus == 'invoiceReady'){
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
					if($invoiceStatus == 'invoiced'){
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceReady') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiced') = '1'");
						$this->db->group_start();
							$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoicePaid') = '0'");
							$this->db->or_where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoicePaid') = ''");
						$this->db->group_end();
						$this->db->group_start();
							$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = '0'");
							$this->db->or_where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = ''");
						$this->db->group_end();
					}
					if($invoiceStatus == 'invoicePaid'){
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceReady') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiced') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoicePaid') = '1'");
						$this->db->group_start();
							$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = '0'");
							$this->db->or_where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = ''");
						$this->db->group_end();
					}
					if($invoiceStatus == 'invoiceClose'){
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceReady') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiced') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoicePaid') = '1'");
						$this->db->where("JSON_EXTRACT(IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), '$.invoiceClose') = '1'");
					}
				}
			}
		}
		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		//$this->db->order_by('invoice','asc');
		$this->db->limit(1000);
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
    }
    public function getStatementOfAccount($table,$sdate='',$edate='',$company='',$groupby='yes') {
        if($groupby == 'yes') {
            //$this->db->select('id,pudate,dodate,pcity,dcity,rate,parate,company,dlocation,plocation,paddressid,daddressid,dWeek,trailer,tracking,invoice,childInvoice,parentInvoice,invoiceDate,invoiceType,expectPayDate,payoutAmount,status,rdate, SUM(parate) AS total_parate');
            $this->db->select('company, SUM(rate) AS total_rate, COUNT(id) AS total_id, SUM(payableAmt) as total_payableAmt, SUM(parate) AS total_parate, SUM(payoutAmount) AS total_payoutAmount, COUNT(id) AS record_count, MAX(pudate) AS last_pudate');
        } else {
            $this->db->select('id,pudate,dodate,pcity,dcity,rate,parate,company,dlocation,plocation,paddressid,daddressid,dWeek,trailer,tracking,invoice,childInvoice,parentInvoice,invoiceDate,invoiceType,expectPayDate,payableAmt,payoutAmount,status,rdate,dispatchMeta, parate AS total_parate');
        }
        
        $this->db->from($table);
        if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		if($edate!='') { $this->db->where('pudate <=',$edate); }
		if($company!='') { $this->db->where('company',$company); }
		$this->db->where_in('invoiceType',array('Direct Bill','Quick Pay'));
		$this->db->where("JSON_EXTRACT(
			 		IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), 
			 		'$.invoiced') = '1'");
        $this->db->where("JSON_EXTRACT(
			 		IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), 
			 		'$.invoiceClose') = '0'");
		$this->db->order_by('pudate','asc');
        if($groupby == 'yes') { $this->db->group_by('company'); }
        $this->db->limit(1000);
        $query = $this->db->get();
		// echo $this->db->last_query();
		// exit;

        return $query->result_array();
    }
	
	public function getReceivableStatement($table,$sdate='',$edate='',$company='',$agingSearch) {
        
     $this->db->select('id,pudate,dodate,pcity,dcity,rate,parate,company,dlocation,plocation,paddressid,daddressid,dWeek,trailer,tracking,invoice,childInvoice,parentInvoice,invoiceDate,invoiceType,expectPayDate,payableAmt,payoutAmount,status,rdate,dispatchMeta, parate AS total_parate');
        
        
        $this->db->from($table);
        if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		if($edate!='') { $this->db->where('pudate <=',$edate); }
		if($company!='') { $this->db->where('company',$company); }
		$this->db->where_in('invoiceType',array('Direct Bill','Quick Pay'));
		$this->db->where("JSON_EXTRACT(
			 		IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), 
			 		'$.invoiced') = '1'");
        $this->db->where("JSON_EXTRACT(
			 		IF(dispatchMeta = '' OR dispatchMeta IS NULL, '{}', dispatchMeta), 
			 		'$.invoiceClose') = '0'");
		if ($agingSearch != '') {  
			if($agingSearch == 'fortyfive'){
				$this->db->where('DATEDIFF(CURDATE(), invoiceDate) BETWEEN 31 AND 45', null, false);
			}elseif($agingSearch == 'sixty'){
				$this->db->where('DATEDIFF(CURDATE(), invoiceDate) BETWEEN 46 AND 60', null, false);
			}elseif($agingSearch == 'sixtyplus'){
				$this->db->where('DATEDIFF(CURDATE(), invoiceDate) > 60', null, false);
			}
		}
					

		$this->db->order_by('pudate','asc');
        if($groupby == 'yes') { $this->db->group_by('company'); }
        $this->db->limit(1000);
        $query = $this->db->get();
		// echo $this->db->last_query();
		// exit;

        return $query->result_array();
    }

	function get_invoice_pending(){
        $date = date('Y-m-d');
        $this->db->select('*');
		$this->db->from('dispatch');
		$this->db->where_in('invoiceType',array('Direct Bill','Quick Pay'));
		$this->db->like('dispatchMeta','"invoicePaid":"0"');
		$this->db->where('expectPayDate <=',$date);
		$this->db->where('expectPayDate !=','0000-00-00');
		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		$this->db->limit(1000);
		$query = $this->db->get();
		return $query->result_array();
    }
    function get_paysheet_data($sdate='',$edate='',$unit='',$driver='',$company=''){
        $this->db->select('*');
		$this->db->from('dispatch');
		if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		if($edate!='') { $this->db->where('pudate <=',$edate); }
		if($unit!='') { $this->db->where('vehicle',$unit); }
		if($driver!='') { $this->db->where('driver',$driver); }
		if($company!='') { $this->db->where('company',$company); }
		$query = $this->db->get();
		return $query->result_array();
    }
    function get_events_by_filter($sdate='',$edate=''){
         $this->db->select('*');
		$this->db->from('events');
		if($sdate!='') { $this->db->where('cdate >=',$sdate); }
		if($edate!='') { $this->db->where('cdate <=',$edate); }
		$query = $this->db->get();
		return $query->result_array();
    }
    function get_document_by_dispach($id,$table='documents'){
        $this->db->select('*');
		$this->db->from($table);
		$this->db->where('did',$id);
		$query = $this->db->get();
		return $query->result_array();
    }
	function get_documents_by_ids($id,$table='documents'){
        $this->db->select('*');
		$this->db->from($table);
		if (is_array($id)) {
			$this->db->where_in('id', $id);
		} else {
			$this->db->where('id', $id);
		}
		$query = $this->db->get();
		return $query->result_array();
    }
    function get_driver_document($id,$doc_for=''){
        $this->db->select('*');
		$this->db->from('driver_document');
		$this->db->where('did',$id);
		if($doc_for!='') { $this->db->where('docs_for',$doc_for); }
		$query = $this->db->get();
		return $query->result_array();
    }
    function getOtherInfo($table,$sdate='',$edate='',$truck='',$driver=''){
        $this->db->select('t.*,d.dname');
		$this->db->from($table.' as t');
		//$this->db->join('vehicles as v','v.id=t.truck','left');
		$this->db->join('drivers as d','d.id=t.driver_id','left');
		if($sdate != '') { $this->db->where('t.fdate >=',$sdate); }
		if($edate != '') { $this->db->where('t.fdate <=',$edate); }
		if($driver != '') { $this->db->where('t.driver_id',$driver); }
		if($truck != '') { $this->db->like('t.truck',$truck); }
		$query = $this->db->get();
		return $query->result_array(); 
     }
}
?>