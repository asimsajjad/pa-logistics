<?php
class AllInvoices_model extends CI_Model
{
	
    public function __construct() {
        parent::__construct(); 
        $this->load->database(); 
    }
	function get_db_pa_invoices($table,$sdate='',$edate='',$company=[],$driver=[]){
		
        $this->db->select("$table.*, drivers.dname");
		$this->db->from($table);
		$this->db->join('drivers', "$table.driver = drivers.id", 'left');
		if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		if($edate!='') { $this->db->where('pudate <=',$edate); }
		if(!empty($driver)) { 
			$this->db->where_in('driver', $driver); 
		}
		if(!empty($company)) { 
			$this->db->where_in('company', $company); 
		}

		$this->db->where('invoiceType','Direct Bill');
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
		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		// $this->db->limit(1000);
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
    }

	function get_db_pa_logistics_invoices($table,$sdate='',$edate='',$company=[],$driver=[]){
		
        $this->db->select("$table.*, drivers.dname");
		$this->db->from($table);
		$this->db->join('drivers', "$table.driver = drivers.id", 'left');

		if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		if($edate!='') { $this->db->where('pudate <=',$edate); }
		// if($driver!='') { $this->db->where('driver',$driver); }
		if(!empty($driver)) { 
			$this->db->where_in('driver', $driver); 
		}
		if(!empty($company)) { 
			$this->db->where_in('company', $company); 
		}
		// if($company!='') { $this->db->where('company',$company); }
		$this->db->where('invoiceType','Direct Bill');
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
		$this->db->group_end();		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		// $this->db->limit(1000);
		$query = $this->db->get();
		return $query->result_array();
    }

	function get_qp_pa_invoices($table,$sdate='',$edate='',$company=[],$driver=[]){
		
        $this->db->select("$table.*, drivers.dname");
		$this->db->from($table);
		$this->db->join('drivers', "$table.driver = drivers.id", 'left');

		if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		if($edate!='') { $this->db->where('pudate <=',$edate); }
		// if($driver!='') { $this->db->where('driver',$driver); }
		if(!empty($driver)) { 
			$this->db->where_in('driver', $driver); 
		}
		if(!empty($company)) { 
			$this->db->where_in('company', $company); 
		}
		// if($company!='') { $this->db->where('company',$company); }
		$this->db->where('invoiceType','Quick Pay');
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
		$this->db->group_end();		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		// $this->db->limit(1000);
		$query = $this->db->get();
		return $query->result_array();
    }
	function get_rts_pa_invoices($table,$sdate='',$edate='',$company=[],$driver=[]){
		
        $this->db->select("$table.*, drivers.dname");
		$this->db->from($table);
		$this->db->join('drivers', "$table.driver = drivers.id", 'left');

		if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		if($edate!='') { $this->db->where('pudate <=',$edate); }
		// if($driver!='') { $this->db->where('driver',$driver); }
		if(!empty($driver)) { 
			$this->db->where_in('driver', $driver); 
		}
		if(!empty($company)) { 
			$this->db->where_in('company', $company); 
		}
		// if($company!='') { $this->db->where('company',$company); }
		$this->db->where('invoiceType','RTS');
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
		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		// $this->db->limit(1000);
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
    }
	function get_db_pa_warehousing_invoices($table,$sdate='',$edate='',$company=[],$driver=[]){
		
        $this->db->select("$table.*, drivers.dname");
		$this->db->from($table);
		$this->db->join('drivers', "$table.driver = drivers.id", 'left');

		if($sdate!='') { $this->db->where('pudate >=',$sdate); }
		if($edate!='') { $this->db->where('pudate <=',$edate); }
		// if($driver!='') { $this->db->where('driver',$driver); }
		if(!empty($driver)) { 
			$this->db->where_in('driver', $driver); 
		}
		if(!empty($company)) { 
			$this->db->where_in('company', $company); 
		}
		// if($company!='') { $this->db->where('company',$company); }
		$this->db->where('invoiceType','Direct Bill');
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
		$this->db->order_by('pudate','asc');
		$this->db->order_by('driver','asc');
		// $this->db->limit(1000);
		$query = $this->db->get();
		return $query->result_array();
    }
	function get_pending_invoices($table,$sdate='',$edate='',$company='',$driver='',$invoiceType=''){
		$where = "d.`driver_status`='Shipment Delivered' 
          AND ((d.dispatchMeta IS NOT NULL AND d.dispatchMeta != '' AND 
        (JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.invoiceReady')) = '0' 
         OR JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.invoiceReady')) = '')))";
		//  if($sdate!='') { 
        //     $where .= " AND d.pudate >= '$sdate'";
		// }
		// if($edate!='') { 
        //     $where .= " AND d.pudate <= '$edate'"; 
		// }
		// if($driver!='') { 
        //     $where .= " AND d.driver = '$driver'"; 
		// }
		// if($company!='') { 
        //     $where .= " AND d.company = '$company'"; 
		// }
		// if($invoiceType!='') { 
        //     $where .= " AND d.invoiceType = '$invoiceType'"; 
		// }
		if($table == 'dispatchOutside'){
			$sql="SELECT d.*, c.company as carrier FROM $table AS `d` JOIN truckingCompanies c ON c.id = d.truckingcompany WHERE $where";
		}else{
			$sql="SELECT * FROM $table AS `d` WHERE $where";
		}
		
        $result =  $this->db->query($sql)->result_array();
        return $result;
    }
	public function getPayableStatementOfAccount($table,$sdate='',$edate='',$company='',$groupby='yes') {
        $sql="Select id,pudate,dodate,pcity,dcity,rate,parate,company,dlocation,plocation,paddressid,daddressid,dWeek,trailer,tracking,invoice,childInvoice,parentInvoice,invoiceDate,invoiceType,expectPayDate,payableAmt,payoutAmount,status,rdate,dispatchMeta, parate AS total_parate FROM $table a
		WHERE a.invoiceType IN ('Direct Bill', 'Quick Pay')
	    AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiced') = '1'
	    AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceClose') = '0'
	    AND a.invoiceDate != '0000-00-00'
    	AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') = ''
   		AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceCloseDate') = ''
		AND a.truckingcompany='$company' ORDER BY a.pudate ASC limit 1000";
        $query =  $this->db->query($sql);
        return $query->result_array();
    }
	public function get_invoice_history($table,$sdate='',$edate='',$dispatchType){
		$where='1=1 ';
		if($sdate!='') { 
			$where .="AND DATE(a.date) >= '$sdate'";
		 }
		if($edate!='') {
			$where .="AND DATE(a.date) <= '$edate'";
		 }
		
		$sql="SELECT * FROM invoiceEmailHistory a 
		JOIN $table dispatchTable ON dispatchTable.id=a.dispatchId
		WHERE a.dispatchType='$dispatchType' AND $where";
		// echo $sql;exit;
		$query=$this->db->query($sql);
		return $query->result_array();
	}
	public function get_carrier_email_history($sdate='',$edate=''){
		$where='1=1 ';
		if($sdate!='') { 
			$where .="AND DATE(a.date) >= '$sdate'";
		 }
		if($edate!='') {
			$where .="AND DATE(a.date) <= '$edate'";
		 }
		
		$sql="SELECT dispatchTable.id,`admin`.uname as sender, c.company as customer, dispatchTable.invoice, tc.company as carrier, fc.company as factoringCompany, a.emailSentTo,
		CASE 
			WHEN a.emailSentTo = 'factoringCompany' THEN fc.email
			WHEN a.emailSentTo = 'truckingCompany' THEN tc.email
			ELSE NULL
		END AS sentToEmail, a.date, a.file
		FROM carrierEmailHistory a 
		JOIN dispatchOutside dispatchTable ON dispatchTable.id=a.dispatchId
		JOIN admin_login `admin`  ON  `admin`.id = a.sender
		JOIN companies c ON c.id=dispatchTable.company 
		JOIN truckingCompanies tc ON tc.id=dispatchTable.truckingCompany 
		LEFT JOIN factoringCompanies fc ON fc.id=dispatchTable.factoringCompany 
		WHERE $where";
		// echo $sql;exit;
		$query=$this->db->query($sql);
		return $query->result_array();
	}
}

?>