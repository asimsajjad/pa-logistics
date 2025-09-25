<?php
class LogisticsDashboard_model extends CI_Model
{
    public function __construct() {
        parent::__construct(); 
        $this->load->database(); 
    }
	
    public function shipmentStatusCounts($sdate='', $edate=''){
        $whereDO = "1=1";
        $wherePU = "1=1 ";
      
        if($sdate!='') { 
            $whereDO .= " AND (dd.dodate >= '$sdate' OR extra.pd_date >= '$sdate')";
            $wherePU .= " AND (pd.pudate >= '$sdate' OR extra.pd_date >= '$sdate')";
		}
		if($edate!='') { 
            $whereDO .= " AND (dd.dodate <= '$edate' OR extra.pd_date <= '$edate')"; 
			$wherePU .= " AND (pd.pudate <= '$edate' OR extra.pd_date <= '$edate')"; 
		}
        if($sdate =='' && $edate ==''){
            $whereDO .= " AND (dd.dodate = CURDATE() OR extra.pd_date = CURDATE())";
            $wherePU .= " AND (pd.pudate = CURDATE() OR extra.pd_date = CURDATE())";
        }
		$shipmentStatusCounts_sql="SELECT 
        (SELECT COUNT(*) AS dispatchCount FROM 
        (SELECT dd.id AS dispatchid 
        FROM `dispatchOutside` AS `dd`
        LEFT JOIN `dispatchOutsideExtraInfo` AS `extra` ON dd.id = extra.dispatchid AND extra.pd_date != '0000-00-00' AND extra.pd_type = 'dropoff'
        WHERE (($whereDO AND dd.dodate != '0000-00-00') OR (dd.driver_status != 'Shipment Delivered' AND dd.driver_status != 'Delivery Completed' AND dd.driver_status != 'Checked Out' AND (dd.dodate <= CURDATE() OR extra.pd_date <= CURDATE()) AND dd.status NOT LIKE '%closed%')) AND dd.parentInvoice = '' GROUP BY dd.id) AS subquery) Delivery,

        ( SELECT COUNT(*) AS dispatchCount FROM (SELECT pd.id AS dispatchid FROM `dispatchOutside` AS `pd`
        LEFT JOIN `dispatchOutsideExtraInfo` AS `extra` ON pd.id = extra.dispatchid AND extra.pd_date != '0000-00-00' AND extra.pd_type = 'pickup' WHERE (($wherePU) OR (pd.driver_status IN ('Pending', 'Shipment Scheduled')  AND pd.status NOT LIKE '%closed%' AND (pd.pudate <= CURDATE() OR extra.pd_date <= CURDATE()))) AND `pd`.`pudate` != '0000-00-00' AND `pd`.`parentInvoice` = '' GROUP BY pd.id) AS subquery ) Pickup, 

        (SELECT COUNT(*) FROM `dispatchOutside` AS `d` 
        LEFT JOIN vehicles as v ON v.id=d.vehicle
        LEFT join companies as com  ON com.id=d.company
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join companyAddress as pca  ON pca.id=d.paddressid
        LEFT join companyAddress as dca  ON dca.id=d.daddressid
        LEFT JOIN cities as pc ON pc.id=d.pcity
        LEFT JOIN cities as dc ON dc.id=d.dcity
        LEFT JOIN truckingCompanies as tc ON tc.id=d.truckingCompany
        WHERE d.`driver_status`='Shipment Delivered' 
           AND ((d.dispatchMeta IS NOT NULL AND d.dispatchMeta != '' AND 
        (JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.invoiceReady')) = '0' 
         OR JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.invoiceReady')) = '')))
        ) PendingInvoices,

        (
            SELECT COUNT(*) FROM `dispatchOutside` AS `d` 
            LEFT JOIN vehicles AS v ON v.id=d.vehicle
            LEFT JOIN companies AS com  ON com.id=d.company
            LEFT JOIN drivers AS dr  ON dr.id=d.driver
            LEFT JOIN companyAddress AS pca  ON pca.id=d.paddressid
            LEFT JOIN companyAddress AS dca  ON dca.id=d.daddressid
            LEFT JOIN cities AS pc ON pc.id=d.pcity
            LEFT JOIN cities AS dc ON dc.id=d.dcity
            LEFT JOIN truckingCompanies AS tc ON tc.id=d.truckingCompany
            WHERE d.`driver_status`='Shipment Delivered' 
            AND ((d.dispatchMeta IS NOT NULL AND d.dispatchMeta != '' AND 
            (JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.carrierInvoiceCheck')) = '0' 
            OR JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.carrierInvoiceCheck')) = '')))
        ) PendingCarrierInvoices,

        COUNT(*) Pending FROM `dispatchOutside` AS `d` WHERE `d`.`delivered` != 'yes' OR `d`.`delivered` IS NULL";
        // echo $shipmentStatusCounts_sql;exit;
		$shipmentStatusCounts_result =  $this->db->query($shipmentStatusCounts_sql)->result_array();
        return $shipmentStatusCounts_result;
	}
    public function deliveryShipmentDetails($sdate='', $edate=''){
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND (d.dodate >= '$sdate' OR extra.pd_date >= '$sdate')";
		}
		if($edate!='') { 
            $where .= " AND (d.dodate <= '$edate' OR extra.pd_date <= '$edate')"; 
		}
        if($sdate =='' && $edate ==''){
            $where .= " AND (d.dodate = CURDATE() OR extra.pd_date = CURDATE())";
        }

		$deliveryShipmentDetails_sql="SELECT d.id as dispatchid,d.invoice as invoice, d.dispatchMeta, v.vname, v.vnumber,
        COALESCE(CONCAT('[', ca.city, ', ', ca.state, ']'), CONCAT('[',dc.city,']')) AS `city`,
        d.delivered,d.vehicle,d.driver,d.rate,d.dodate as `date`,d.dtime as `time`,d.tracking,d.driver_status,d.status,dr.dname,com.company, tc.company as carrier,
        STR_TO_DATE(CONCAT(d.dodate, ' ', 
        CASE 
            WHEN d.dtime LIKE '%-%' THEN SUBSTRING_INDEX(d.dtime, '-', -1)
            ELSE d.dtime
        END), '%Y-%m-%d %h:%i %p') AS sorted_datetime
        FROM `dispatchOutside` AS `d`
        LEFT JOIN dispatchOutsideExtraInfo extra ON d.id = extra.dispatchid AND extra.pd_date != '0000-00-00' AND extra.pd_type = 'dropoff'
        LEFT JOIN vehicles as v ON v.id=d.vehicle
        LEFT JOIN truckingCompanies as tc ON tc.id=d.truckingCompany
        LEFT join companies as com  ON com.id=d.company
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join companyAddress as ca  ON ca.id=d.daddressid
        LEFT JOIN cities as dc ON dc.id=d.dcity
        WHERE (
        (
            $where
        )
        OR (
            d.driver_status != 'Shipment Delivered'
            AND d.driver_status != 'Delivery Completed'
            AND d.driver_status != 'Checked Out'
            AND (
                d.dodate <= CURDATE()
                OR extra.pd_date <= CURDATE()
            )
            AND d.status NOT LIKE '%closed%'
        )
        ) AND `d`.`parentInvoice` = ''
        GROUP BY d.id ORDER BY sorted_datetime ASC";
        // echo $deliveryShipmentDetails_sql;exit;
		$deliveryShipmentDetails_sql_result =  $this->db->query($deliveryShipmentDetails_sql)->result_array();
        return $deliveryShipmentDetails_sql_result;
	}
    public function pickupShipmentDetails($sdate='', $edate=''){
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND (d.pudate >= '$sdate' OR extra.pd_date >= '$sdate')";

		}
		if($edate!='') { 
            $where .= " AND (d.pudate <= '$edate' OR extra.pd_date <= '$edate')"; 

		}
        if($sdate =='' && $edate ==''){
            $where .= " AND (d.pudate = CURDATE() OR extra.pd_date = CURDATE())";
        }
		$pickupShipmentDetails_sql="SELECT d.id as dispatchid, d.invoice as invoice,d.dispatchMeta, v.vname, v.vnumber,
        COALESCE(CONCAT('[', ca.city, ', ', ca.state, ']'), CONCAT('[',pc.city,']')) AS `city`,
        d.pudate as `date`, d.ptime as `time`, d.delivered,d.vehicle,d.driver,d.rate,d.dodate,d.tracking,d.driver_status,d.status,dr.dname,com.company, tc.company as carrier,
        STR_TO_DATE(CONCAT(d.pudate, ' ', 
        CASE 
            WHEN d.ptime LIKE '%-%' THEN SUBSTRING_INDEX(d.ptime, '-', -1)
            ELSE d.ptime
        END), '%Y-%m-%d %h:%i %p') AS sorted_datetime
        FROM `dispatchOutside` AS `d`
        LEFT JOIN dispatchOutsideExtraInfo extra ON d.id = extra.dispatchid AND extra.pd_date != '0000-00-00' AND extra.pd_type = 'pickup'
        LEFT JOIN vehicles as v ON v.id=d.vehicle
        LEFT JOIN truckingCompanies as tc ON tc.id=d.truckingCompany
        LEFT join companies as com  ON com.id=d.company
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join companyAddress as ca  ON ca.id=d.paddressid
        LEFT JOIN cities as pc ON pc.id=d.pcity
        WHERE (($where)
        OR (d.driver_status IN ('Pending', 'Shipment Scheduled')  AND d.status NOT LIKE '%closed%' AND (d.pudate <= CURDATE() OR extra.pd_date <= CURDATE())))
         AND `d`.`pudate` != '0000-00-00' AND `d`.`parentInvoice` = ''
        GROUP BY d.id ORDER BY sorted_datetime ASC";
        // echo $pickupShipmentDetails_sql;exit;
		$pickupShipmentDetails_sql_result =  $this->db->query($pickupShipmentDetails_sql)->result_array();
        return $pickupShipmentDetails_sql_result;
	}
    public function pendingShipmentDetails($sdate='', $edate=''){
		$pendingShipmentDetails_sql="SELECT d.id as dispatchid,d.dispatchMeta, v.vname, v.vnumber, c.city as pcity, l.location as plocation, d.pudate, d.ptime, cd.city as dcity,ld.location as dlocation, d.delivered,d.vehicle,d.driver,d.rate,d.dodate,d.dtime,d.tracking,d.driver_status,d.status,dr.dname,com.company 
        FROM `dispatchOutside` AS `d`
        LEFT JOIN vehicles as v ON v.id=d.vehicle
        LEFT join companies as com  ON com.id=d.company
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join locations as l  ON l.id=d.plocation
        LEFT join cities as c  ON c.id=d.pcity
        LEFT join locations as ld  ON ld.id=d.dlocation
        LEFT join cities as cd  ON cd.id=d.dcity
        WHERE `d`.`delivered` != 'yes' OR `d`.`delivered` IS NULL";
		$pendingShipmentDetails_sql_result =  $this->db->query($pendingShipmentDetails_sql)->result_array();
        return $pendingShipmentDetails_sql_result;
	}
    public function pendingInvoicesDetails(){
        $where = "1=1";
        // if($sdate!='') { 
        //     $where .= " AND d.pudate >= '$sdate'";
		// }
		// if($edate!='') { 
        //     $where .= " AND d.pudate <= '$edate'"; 
		// }
		$pendingInvoices_sql="SELECT d.id as dispatchid,d.dispatchMeta, v.vname, v.vnumber,  d.pudate `date`, d.ptime as `time`, d.delivered,d.vehicle,d.driver,d.rate,d.dodate,d.dtime,d.tracking,d.driver_status,d.status,dr.dname,com.company, d.invoice,
        pca.company as `paddress`, COALESCE(CONCAT('[', pca.city, ', ', pca.state, ']'), CONCAT('[',pc.city,']')) AS `city`, dca.company as `daddress`, COALESCE(CONCAT('[', dca.city, ', ', dca.state, ']'), CONCAT('[',dc.city,']')) AS `dcity`, 
        CASE WHEN d.bol IS NULL OR d.bol != 'AK' THEN 'BOL missing' ELSE '' END AS bol_status, tc.company as carrier,
        CASE WHEN d.rc IS NULL OR d.rc != 'AK' THEN 'RC missing' ELSE '' END AS rc_status
        FROM `dispatchOutside` AS `d`
        LEFT JOIN vehicles as v ON v.id=d.vehicle
        LEFT join companies as com  ON com.id=d.company
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join companyAddress as pca  ON pca.id=d.paddressid
        LEFT join companyAddress as dca  ON dca.id=d.daddressid
        LEFT JOIN cities as pc ON pc.id=d.pcity
        LEFT JOIN cities as dc ON dc.id=d.dcity
        LEFT JOIN truckingCompanies as tc ON tc.id=d.truckingCompany
        WHERE d.`driver_status`='Shipment Delivered' 
          AND ((d.dispatchMeta IS NOT NULL AND d.dispatchMeta != '' AND 
        (JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.invoiceReady')) = '0' 
         OR JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.invoiceReady')) = '')))";
        $pendingInvoices_result =  $this->db->query($pendingInvoices_sql)->result_array();
        return $pendingInvoices_result;
    }
    public function pendingCarrierInvoicesDetails(){
        $where = "1=1";
		$pendingCarrierInvoices_sql="SELECT d.id as dispatchid,d.dispatchMeta, v.vname, v.vnumber,  d.pudate `date`, d.ptime as `time`, d.delivered,d.vehicle,d.driver,d.rate,d.dodate,d.dtime,d.tracking,d.driver_status,d.status,dr.dname,com.company, d.invoice,
        pca.company as `paddress`, COALESCE(CONCAT('[', pca.city, ', ', pca.state, ']'), CONCAT('[',pc.city,']')) AS `city`, dca.company as `daddress`, COALESCE(CONCAT('[', dca.city, ', ', dca.state, ']'), CONCAT('[',dc.city,']')) AS `dcity`,tc.company as carrier
        FROM `dispatchOutside` AS `d`
        LEFT JOIN vehicles as v ON v.id=d.vehicle
        LEFT join companies as com  ON com.id=d.company
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join companyAddress as pca  ON pca.id=d.paddressid
        LEFT join companyAddress as dca  ON dca.id=d.daddressid
        LEFT JOIN cities as pc ON pc.id=d.pcity
        LEFT JOIN cities as dc ON dc.id=d.dcity
        LEFT JOIN truckingCompanies as tc ON tc.id=d.truckingCompany
        WHERE d.`driver_status`='Shipment Delivered' 
          AND ((d.dispatchMeta IS NOT NULL AND d.dispatchMeta != '' AND 
        (JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.carrierInvoiceCheck')) = '0' 
         OR JSON_UNQUOTE(JSON_EXTRACT(d.dispatchMeta, '$.carrierInvoiceCheck')) = '')))";
        $pendingCarrierInvoices_result =  $this->db->query($pendingCarrierInvoices_sql)->result_array();
        return $pendingCarrierInvoices_result;
    }
    public function bookingsCounts($sdate='', $edate=''){ 
        $whereWB = "1=1";
        $whereUAB = "1=1 ";
        if($sdate!='') { 
            $whereWB .= " AND d.pudate >= '$sdate'";
            $whereUAB .= " AND uad.pudate >= '$sdate'";
		}
		if($edate!='') { 
            $whereWB .= " AND d.pudate <= '$edate'"; 
			$whereUAB .= " AND uad.pudate <= '$edate'"; 
		}
        if($sdate =='' && $edate ==''){
            $whereWB .= " AND d.pudate BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) - 5 DAY)";
            $whereUAB .= " AND uad.pudate BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) - 5 DAY)";
        }

        $weeklyBookingsCounts_sql = "SELECT COUNT(*) AS weeklyBookings, 
        (SELECT COUNT(*) FROM `dispatchOutside` AS `uad` 
        WHERE $whereUAB AND truckingCompany=8) AS unassignedBookings 
        FROM `dispatchOutside` AS `d` WHERE $whereWB";
        // echo $weeklyBookingsCounts_sql;exit;
        $weeklyBookingsCounts_result = $this->db->query($weeklyBookingsCounts_sql)->result_array();
        return $weeklyBookingsCounts_result;
    }
    public function weeklyBookingsDetails($sdate='', $edate=''){
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND d.pudate >= '$sdate'";
		}
		if($edate!='') { 
            $where .= " AND d.pudate <= '$edate'"; 
		}
        if($sdate =='' && $edate ==''){
            $where .= " AND d.pudate BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) - 5 DAY)";
        }
		$weeklyBookings_sql="SELECT d.id as dispatchid,d.dispatchMeta, v.vname, v.vnumber,  d.pudate `date`, d.ptime as `time`, d.delivered,d.vehicle,d.driver,d.rate,d.dodate,d.dtime,d.tracking,d.driver_status,d.status,dr.dname,com.company, d.invoice,
        pca.company as `paddress`, COALESCE(CONCAT('[', pca.city, ', ', pca.state, ']'), CONCAT('[',pc.city,']')) AS `city`, dca.company as `daddress`, COALESCE(CONCAT('[', dca.city, ', ', dca.state, ']'), CONCAT('[',dc.city,']')) AS `dcity`
        FROM `dispatchOutside` AS `d`
        LEFT JOIN vehicles as v ON v.id=d.vehicle
        LEFT join companies as com  ON com.id=d.company
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join companyAddress as pca  ON pca.id=d.paddressid
        LEFT join companyAddress as dca  ON dca.id=d.daddressid
        LEFT JOIN cities as pc ON pc.id=d.pcity
        LEFT JOIN cities as dc ON dc.id=d.dcity
        LEFT JOIN truckingCompanies as tc ON tc.id=d.truckingCompany
        WHERE $where";
        $weeklyBookings_result =  $this->db->query($weeklyBookings_sql)->result_array();
        return $weeklyBookings_result;
	}
    public function unassignedBookingsDetails($sdate='', $edate=''){
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND d.pudate >= '$sdate'";
		}
		if($edate!='') { 
            $where .= " AND d.pudate <= '$edate'"; 
		}
        if($sdate =='' && $edate ==''){
            $where .= " AND d.pudate BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) - 5 DAY)";
        }
		$unassignedBookings_sql="SELECT d.id as dispatchid,d.dispatchMeta, v.vname, v.vnumber,  d.pudate `date`, d.ptime as `time`,d.delivered,d.vehicle,d.driver,d.rate,d.dodate,d.dtime,d.tracking,d.driver_status,d.status,dr.dname,com.company, d.invoice , pca.company as `paddress`, COALESCE(CONCAT('[', pca.city, ', ', pca.state, ']'), CONCAT('[',pc.city,']')) AS `city`, dca.company as `daddress`, COALESCE(CONCAT('[', dca.city, ', ', dca.state, ']'), CONCAT('[',dc.city,']')) AS `dcity`
        FROM `dispatchOutside` AS `d`
        LEFT JOIN vehicles as v ON v.id=d.vehicle
        LEFT join companies as com  ON com.id=d.company
        LEFT join drivers as dr  ON dr.id=d.driver
        LEFT join companyAddress as pca  ON pca.id=d.paddressid
        LEFT join companyAddress as dca  ON dca.id=d.daddressid
        LEFT JOIN cities as pc ON pc.id=d.pcity
        LEFT JOIN cities as dc ON dc.id=d.dcity
        LEFT JOIN truckingCompanies as tc ON tc.id=d.truckingCompany
        WHERE truckingCompany=8 AND $where";
        $unassignedBookings_result =  $this->db->query($unassignedBookings_sql)->result_array();
        return $unassignedBookings_result;
	}
    public function receivableInvoicesCounts($sdate='', $edate=''){ 
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND a.invoiceDate >= '$sdate'";
		}
		if($edate!='') { 
            $where .= " AND a.invoiceDate <= '$edate'"; 
		}
        
        $receivableInvoicesCounts_sql = "SELECT COUNT(*) AS notReceived, SUM(parate) as notReceivedAmt 
        FROM `dispatchOutside` AS `a` 
        WHERE a.invoiceType IN ('Direct Bill', 'Quick Pay')
        AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiced') = '1'
        AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceClose') = '0'
        AND a.invoiceDate != '0000-00-00'      
        AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceCloseDate') = ''
        AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '0'
        AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') = '' 
        AND $where";
        $notReceived = $this->db->query($receivableInvoicesCounts_sql)->row();

        $receivedInvoicesCounts_sql="SELECT COUNT(*) AS received , SUM(parate) as receivedAmt 
        FROM `dispatchOutside` AS `a` 
        WHERE a.invoiceType IN ('Direct Bill', 'Quick Pay')
        AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '1'
        AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') != '' 
        AND $where";
        $received = $this->db->query($receivedInvoicesCounts_sql)->row();

        $receivableArray=[];
        $receivableArray['notReceived']=$notReceived->notReceived;
        $receivableArray['notReceivedAmt']=$notReceived->notReceivedAmt;

        $receivableArray['received']=$received->received;
        $receivableArray['receivedAmt']=$received->receivedAmt;
        return $receivableArray;
    }
    public function receivedInvoicesDetails($sdate='', $edate=''){ 
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND a.invoiceDate >= '$sdate'";
		}
		if($edate!='') { 
            $where .= " AND a.invoiceDate <= '$edate'"; 
		}
        $receivaleInvoices_sql = "SELECT a.id dispatchid, a.company AS company_id, c.company, a.invoice, a.pudate, a.dodate, a.invoiceDate, a.rate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.dispatchMeta
            FROM `dispatchOutside` AS `a` 
            JOIN companies c ON c.id = a.company
            WHERE a.invoiceType IN ('Direct Bill', 'Quick Pay')
            AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '1'
            AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') != ''
            AND $where";
        // echo $weeklyBookingsCounts_sql;exit;
        $receivaleInvoices_result = $this->db->query($receivaleInvoices_sql)->result_array();
        return $receivaleInvoices_result;
    }
    public function notReceivedInvoicesDetails($sdate='', $edate=''){ 
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND a.invoiceDate >= '$sdate'";
		}
		if($edate!='') { 
            $where .= " AND a.invoiceDate <= '$edate'"; 
		}
        $notReceivaleInvoices_sql = "SELECT a.id dispatchid, a.company AS company_id, c.company, a.invoice, a.pudate, a.dodate, a.invoiceDate, a.rate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.dispatchMeta
            FROM `dispatchOutside` AS `a` 
            JOIN companies c ON c.id = a.company
            WHERE a.invoiceType IN ('Direct Bill', 'Quick Pay')
            AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiced') = '1'
            AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceClose') = '0'
            AND a.invoiceDate != '0000-00-00'      
            AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceCloseDate') = ''
            AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '0'
            AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') = ''
            AND $where";
        // echo $weeklyBookingsCounts_sql;exit;
        $notReceivaleInvoices_result = $this->db->query($notReceivaleInvoices_sql)->result_array();
        return $notReceivaleInvoices_result;
    }
	public function payableInvoicesCounts($sdate='', $edate=''){ 
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) >= '$sdate'";
		}
		if($edate!='') { 
            $where .= " AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) <= '$edate'"; 
		}

        $payableInvoicesCounts_sql = "SELECT COUNT(*) AS unPaid, SUM(parate) as unPaidAmt 
        FROM `dispatchOutside` AS `a`
             WHERE a.bookedUnder !=4 AND a.invoiceType IN ('Direct Bill', 'Quick Pay')
		    AND a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0'
            AND JSON_VALID(a.dispatchMeta)
            AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
            AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
            AND $where
            ";
        $payable = $this->db->query($payableInvoicesCounts_sql)->row();

        $paidInvoicesCounts_sql="SELECT COUNT(*) AS paid , SUM(parate) as paidAmt 
        FROM `dispatchOutside` AS `a` 
            WHERE a.bookedUnder !=4 AND a.invoiceType IN ('Direct Bill', 'Quick Pay')
		    AND a.carrierPayoutDate != '0000-00-00' AND a.carrierPayoutCheck = '1' 
            AND JSON_VALID(a.dispatchMeta)
            AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
            AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
            AND $where
            ";
        $paid = $this->db->query($paidInvoicesCounts_sql)->row();

        $payableArray=[];
        $payableArray['unPaid']=$payable->unPaid;
        $payableArray['unPaidAmt']=$payable->unPaidAmt;

        $payableArray['paid']=$paid->paid;
        $payableArray['paidAmt']=$paid->paidAmt;
        return $payableArray;
    }
    public function payableInvoicesDetails($sdate='', $edate=''){ 
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) >= '$sdate'";
		}
		if($edate!='') { 
            $where .= " AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) <= '$edate'"; 
		}
        $payableInvoices_sql = "SELECT a.id as dispatchid, a.truckingcompany AS company_id, c.company, a.invoice, a.pudate, a.dodate, JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) as invoiceDate, a.rate,a.agentRate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) AS days_diff, a.dispatchMeta,bookedUnder, bookedUnderNew
		FROM dispatchOutside a
		JOIN truckingCompanies c ON c.id = a.truckingcompany
		WHERE a.bookedUnder !=4 AND
		a.invoiceType IN ('Direct Bill', 'Quick Pay')
		AND a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
        AND JSON_VALID(a.dispatchMeta)
        AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
        AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
        AND $where";
        // echo $weeklyBookingsCounts_sql;exit;
        $payableInvoices_result = $this->db->query($payableInvoices_sql)->result_array();
        return $payableInvoices_result;
    }
    public function paidInvoicesDetails($sdate='', $edate=''){ 
        $where = "1=1";
        if($sdate!='') { 
            $where .= " AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) >= '$sdate'";
		}
		if($edate!='') { 
            $where .= " AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) <= '$edate'"; 
		}

        $paidInvoices_sql = "SELECT a.id as dispatchid, a.truckingcompany AS company_id, c.company, a.invoice, a.pudate, a.dodate, JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) as invoiceDate, a.rate,a.agentRate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) AS days_diff, a.dispatchMeta,bookedUnder, bookedUnderNew
		FROM dispatchOutside a
		JOIN truckingCompanies c ON c.id = a.truckingcompany
		WHERE a.bookedUnder !=4 AND
		a.invoiceType IN ('Direct Bill', 'Quick Pay')
		AND a.carrierPayoutDate != '0000-00-00' AND a.carrierPayoutCheck = '1'
        AND JSON_VALID(a.dispatchMeta)
        AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
        AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
        AND $where";
        // echo $weeklyBookingsCounts_sql;exit;
        $paidInvoices_result = $this->db->query($paidInvoices_sql)->result_array();
        return $paidInvoices_result;
    }

}

?>