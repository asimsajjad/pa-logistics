<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appapi_model extends CI_model {
    
    public function __construct() {
        parent::__construct(); 
        $this->load->database(); 
    }
    
    public function getOtherDispatch($id){
        $this->db->select('d.id,CONCAT(d.pd_date, " 00:00:00") as pd_date,d.pd_type,d.pd_address,d.pd_code, d.pd_time,d.pd_notes,pl.location as pd_company, pc.city as pd_city');
		$this->db->from('dispatchExtraInfo as d');
		$this->db->join('locations as pl','pl.id=d.pd_location','left'); 
		$this->db->join('cities as pc','pc.id=d.pd_city','left'); 
		$this->db->where('d.dispatchid',$id);
		$this->db->order_by('d.pd_type','asc');
		$this->db->order_by('d.id','asc');
		$query = $this->db->get();
		return $query->result_array();
    }
    public function getOtherDispatch__old($id){
        $this->db->select('d.id,d.pudate1,d.dodate1,d.paddress1,d.daddress1,d.pcode1,d.dcode1, d.ptime1,d.dtime1,d.pnotes1,d.dnotes1,pl.location as pcompany1, dl.location as dcompany1,pc.city as pcity1, dc.city as dcity1');
		$this->db->from('dispatchExtra as d');
		$this->db->join('locations as pl','pl.id=d.plocation1','left');
		$this->db->join('locations as dl','dl.id=d.dlocation1','left');
		$this->db->join('cities as pc','pc.id=d.pcity1','left');
		$this->db->join('cities as dc','dc.id=d.dcity1','left');
		$this->db->where('d.dispatchid',$id);
		$query = $this->db->get();
		return $query->result_array();
    }
    public function getTripInfo__Old($tripId){
        $this->db->select('d.id,d.trip,CONCAT(d.pudate, " 00:00:00") as pudate,pl.location as paddress,pc.city as pcity,pco.company as pcompany,d.ptime,CONCAT(d.dodate," 00:00:00") as dodate,d.dtime,dl.location as daddress,dc.city as dcity,d.tracking,pco.company as dcompany,d.notes,d.dnotes,d.pnotes,d.trailer,d.tracking,dr.dname as driver_name,d.driver_status'); 
		$this->db->from('dispatch as d');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('companies as pco','pco.id=d.company','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		//$this->db->where('d.driver',$driverId);
		$this->db->where('d.id',$tripId);
		$query = $this->db->get();
		return $query->result_array(); 
    }
    public function getTripInfo_ooolldd($tripId){
		$this->db->select('d.id,d.trip, 
		CONCAT(d.pudate, " 00:00:00") as pudate, d.ptime, pc.city as pcity, pl.location as pcompany, d.paddress, d.pcode,
		CONCAT(d.dodate," 00:00:00") as dodate, d.dtime, dc.city as dcity, dl.location as dcompany, d.daddress, d.dcode,
		CONCAT(d.pudate1, " 00:00:00") as pudate1, d.ptime1, ppc.city as pcity1, ppl.location as pcompany1, d.paddress1, d.pcode1,
		CONCAT(d.dodate1," 00:00:00") as dodate1, d.dtime1, ddc.city as dcity1, ddl.location as dcompany1, d.daddress1, d.dcode1,
		   d.tracking,pco.company as company, d.notes,d.dnotes, d.pnotes, d.trailer, d.tracking, dr.dname as driver_name, d.driver_status');
		$this->db->from('dispatch as d');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->join('locations as ppl','ppl.id=d.plocation1','left');
		$this->db->join('locations as ddl','ddl.id=d.dlocation1','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('cities as ppc','ppc.id=d.pcity1','left');
		$this->db->join('cities as ddc','ddc.id=d.dcity1','left');
		$this->db->join('companies as pco','pco.id=d.company','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		//$this->db->where('d.driver',$driverId);
		$this->db->where('d.id',$tripId);
		$query = $this->db->get();
		return $query->result_array(); 
    }
    public function getTripInfo($tripId){
		$this->db->select('d.id,d.trip, 
		CONCAT(d.pudate, " 00:00:00") as pudate, d.ptime, pc.city as pcity, pl.location as pcompany, d.paddress, d.pcode,
		CONCAT(d.dodate," 00:00:00") as dodate, d.dtime, dc.city as dcity, dl.location as dcompany, d.daddress, d.dcode,
		   d.tracking,pco.company as company, d.notes,d.dnotes, d.pnotes, d.trailer, d.tracking, dr.dname as driver_name, d.driver_status');
		$this->db->from('dispatch as d');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->join('cities as pc','pc.id=d.pcity','left');
		$this->db->join('cities as dc','dc.id=d.dcity','left');
		$this->db->join('companies as pco','pco.id=d.company','left');
		$this->db->join('drivers as dr','dr.id=d.driver','left');
		//$this->db->where('d.driver',$driverId);
		$this->db->where('d.id',$tripId);
		$query = $this->db->get();
		return $query->result_array(); 
    }
    
    public function getTripDocuments($tripId,$url,$type=''){
        $this->db->select('id,CONCAT("'.$url.'", fileurl) AS document ');
		$this->db->from('documents');
		if($type != '') { $this->db->where('type',$type); }
		$this->db->where('did',$tripId);
		$query = $this->db->get();
		return $query->result_array();
    }
    
    public function getTripHistory($driverId,$startDate,$endDate,$start,$limit){
        $this->db->select('d.id,d.trip,CONCAT(d.pudate, " 00:00:00") as pudate,d.ptime,pl.location as plocation,CONCAT(d.dodate, " 00:00:00") as dodate,d.dtime,dl.location as dlocation,d.driver_status');
		$this->db->from('dispatch as d');
		$this->db->join('locations as pl','pl.id=d.plocation','left');
		$this->db->join('locations as dl','dl.id=d.dlocation','left');
		$this->db->where('d.pudate >=',$startDate);
		$this->db->where('d.pudate <=',$endDate);
		$this->db->where('d.driver',$driverId);
		$this->db->limit($limit,$start);
		$query = $this->db->get();
		return $query->result_array();
    }
    
    public function getCurrentTrip($driverId,$currentdate){
        $status = array('Start Trip','Check in Pick Up','Loading Start','Loaded','Checked In','On the Way','Check in Drop Off','Unloading Start','Unloaded','Checked Out');
        $this->db->select('id');
		$this->db->from('dispatch');
		$this->db->where('pudate', $currentdate);
		$this->db->where('driver',$driverId);
		$this->db->where_in('driver_status',$status);
		$this->db->order_by('trip','asc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->result_array();
    }
    
    public function getPreviousShift($driverId,$startDate,$endDate,$start,$limit){
        $this->db->select('*');
		$this->db->from('driver_shift');
		$this->db->where('start_date <=',$endDate);
		$this->db->where('start_date >=',$startDate);
		$this->db->where('driver_id',$driverId);
		$this->db->where('status','closed');
		$this->db->order_by('id','desc');
		$this->db->limit($limit,$start);
		$query = $this->db->get();
		return $query->result_array();
    }
    public function checkShiftStatus($status,$driverId){
        $this->db->select('id');
		$this->db->from('driver_shift');
		$this->db->where('status',$status);
		$this->db->where('driver_id',$driverId);
		$query = $this->db->get();
		return $query->result_array();
    }
    public function updateShiftStatus($data,$driverId){
       $this->db->where('status', 'true');
       $this->db->where('driver_id', $driverId);
       $this->db->update('driver_shift', $data);
       return true;
    }
    
    public function driverLogin($phone,$dcode){
        $this->db->select('id,dname,phone');
		$this->db->from('drivers');
		$this->db->where('phone',$phone);
		$this->db->where('dcode',$dcode);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->result_array();
    }
    
    public function get_document_by_filter($rowid,$type){
		$this->db->select('*');
		$this->db->from('documents');
		$this->db->where('id',$rowid);
		$this->db->where('type',$type);
		$query = $this->db->get();
		return $query->result_array();
	}
    
    public function get_data_by_table($table,$columns='*'){
		$this->db->select($columns);
		$this->db->from($table);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_data_by_column($column,$value,$table,$columns='*'){
		$this->db->select($columns);
		$this->db->from($table);
		$this->db->where($column,$value);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function update_table_by_id($column,$value,$table,$data){
	   $this->db->where($column, $value);
       $this->db->update($table, $data);
       return true;
	}
	function delete($id,$table,$field) {
      $this->db->where($field, $id);
      $this->db->delete($table); 
      return true;
    }
    function add_data_in_table($data,$table) {
      $this->db->insert($table, $data);
      return $this->db->insert_id();
    }
 
}
?>