<?php
class Warehouse_model extends CI_Model
{
    public function __construct() {
        parent::__construct(); 
        $this->load->database(); 
    }
    public function warehouse($sdate,$edate,$company,$materialId,$warehouseAddressId,$sublocationId){
        $where = "1=1";
		if($company!='') { 
			$where .= " AND a.customerId in  (" . implode(",", $company) . ")"; 
		}
        if($materialId!='') { 
			$where .= " AND a.materialId in  (" . implode(",", $materialId) . ")"; 
		}
        if($warehouseAddressId!='') { 
			$where .= " AND inbound.warehouseAddressId in  (" . implode(",", $warehouseAddressId) . ")"; 
		}
        if($sublocationId!='') { 
			$where .= " AND inbound.sublocationId in  (" . implode(",", $sublocationId) . ")"; 
		}
		$sql="SELECT a.id, com.company as customer, a.customerId, user.uname as user, materials.materialNumber,  materials.batch, a.quantity, a.date, materials.expirationDate, materials.description,
        IFNULL(inbound.total_inbound, 0) AS totalInboundPallets,
        IFNULL(outbound.total_outbound, 0) AS totalOutboundPallets,
        (IFNULL(inbound.total_inbound, 0) - IFNULL(outbound.total_outbound, 0)) AS palletQuantity,
        warehouse.warehouse, warehouse.address AS warehouseAddress, sublocation.name AS sublocation
        FROM `warehouse` AS `a`
        join warehouseMaterials as materials  ON materials.id=a.materialId
        join companies as com  ON com.id=a.customerId
        join admin_login as user  ON user.id=a.updatedBy
        LEFT JOIN (
            SELECT materialId,  warehouseAddressId, sublocationId, SUM(palletQuantity) AS total_inbound
            FROM warehouseInbounds WHERE deleted='N'
            GROUP BY materialId
        ) AS inbound ON inbound.materialId = a.materialId
        LEFT JOIN (
            SELECT materialId, SUM(palletQuantity) AS total_outbound
            FROM warehouseOutbounds WHERE deleted='N'
            GROUP BY materialId
        ) AS outbound ON outbound.materialId = a.materialId
        JOIN warehouse_address AS warehouse ON warehouse.id = inbound.warehouseAddressId
        JOIN warehouse_sublocations AS sublocation ON sublocation.id = inbound.sublocationId
        WHERE $where  
        GROUP BY a.id 
        HAVING palletQuantity > 0 OR a.quantity > 0
        ORDER BY a.id DESC";
        // echo $sql;exit;
		$sql_result =  $this->db->query($sql)->result_array();
        return $sql_result;
	}
    public function materials($company,$materialId){
        $where = "1=1 AND a.deleted ='N'";
        if($company!='') { 
			$where .= " AND a.customerId in  (" . implode(",", $company) . ")"; 
		}
        if($materialId!='') { 
			$where .= " AND a.id in  (" . implode(",", $materialId) . ")"; 
		}
		$sql="SELECT a.id, com.company as customer, a.customerId, user.uname as user, a.materialNumber, a.description, a.batch, date(a.expirationDate) as expirationDate, a.date
        FROM `warehouseMaterials` AS `a`
        join companies as com  ON com.id=a.customerId
        join admin_login as user  ON user.id=a.addedBy
        WHERE $where  
        GROUP BY a.id 
        ORDER BY a.id DESC";
        // echo $sql; exit;
		$sql_result =  $this->db->query($sql)->result_array();
        return $sql_result;
	}

    public function downloadWarehouseMaterials($company,$materialId){
        $where = "1=1 AND a.deleted ='N'";
        if($company!='') { 
			$where .= " AND a.customerId in  (" . implode(",", $company) . ")"; 
		}
        if($materialId!='') { 
			$where .= " AND a.id in  (" . implode(",", $materialId) . ")"; 
		}
		$sql="SELECT a.id, com.company as customer, a.customerId, user.uname as user, a.materialNumber, a.description, a.batch, a.expirationDate as expirationDate, a.date
        FROM `warehouseMaterials` AS `a`
        join companies as com  ON com.id=a.customerId
        join admin_login as user  ON user.id=a.addedBy
        WHERE $where  
        GROUP BY a.id 
        ORDER BY a.id DESC";
        // echo $sql; exit;
		$sql_result =  $this->db->query($sql)->result_array();
        return $sql_result;
    }
    public function inbounds($sdate,$edate,$company,$materialId){
        $where = "1=1 AND a.deleted ='N' ";
        if ($sdate == '' && $edate == '') {
            $today = date('Y-m-d');
            $where .= " AND DATE(`a`.`dateIn`) = '$today'";
        } else {
            if ($sdate != '') {
                $where .= " AND DATE(`a`.`dateIn`) >= '$sdate'";
            }
            if ($edate != '') {
                $where .= " AND DATE(`a`.`dateIn`) <= '$edate'";
            }
        }

		if($company!='') { 
			$where .= " AND a.customerId in  (" . implode(",", $company) . ")"; 
		}
        if($materialId!='') { 
			$where .= " AND a.materialId in  (" . implode(",", $materialId) . ")"; 
		}
       
        $sql="SELECT a.id, com.company as customer, a.customerId, a.warehouseAddressId, a.sublocationId, SUM(a.palletQuantity) palletQuantity, SUM(a.piecesQuantity) piecesQuantity, a.dateIn, warehouseAdd.warehouse as warehouse, warehouseAdd.address as warehouseAddress, sublocations.name as sublocation
        FROM `warehouseInbounds` AS `a`
        join warehouseMaterials as materials  ON materials.id=a.materialId
        join companies as com  ON com.id=a.customerId
        LEFT JOIN warehouse_address as warehouseAdd  ON warehouseAdd.id=a.warehouseAddressId
        LEFT JOIN warehouse_sublocations as sublocations ON sublocations.id=a.sublocationId
        join admin_login as user  ON user.id=a.addedBy
        WHERE $where
        GROUP BY a.dateIn,a.customerId,a.warehouseAddressId,a.sublocationId
        ORDER BY a.id DESC";
        // echo $sql; exit;
        $summaryResults = $this->db->query($sql)->result_array();
        
        foreach ($summaryResults as &$summary) {
            $dateIn = $summary['dateIn'];
            $customerId = $summary['customerId'];
            $warehouseAddressId = $summary['warehouseAddressId'];
            $sublocationId = $summary['sublocationId'];

            $detail_sql = "SELECT a.id, com.company as customer, a.customerId, user.uname as user, materials.materialNumber, materials.batch, a.palletNumber,  a.palletPosition, a.palletQuantity, a.piecesQuantity, a.dateIn, a.date, a.lotNumber
                        FROM `warehouseInbounds` AS `a`
                        JOIN warehouseMaterials as materials ON materials.id = a.materialId
                        JOIN companies as com ON com.id = a.customerId
                        JOIN admin_login as user ON user.id = a.addedBy
                        WHERE $where AND a.dateIn = '$dateIn' AND a.customerId=$customerId AND a.warehouseAddressId=$warehouseAddressId AND a.sublocationId=$sublocationId
                        ORDER BY a.id DESC";
                        

            $details = $this->db->query($detail_sql)->result_array();
            $summary['details'] = $details; 
        }
        return $summaryResults;
	}
    public function outbounds($sdate,$edate,$company,$materialId){
        $where = "1=1 AND a.deleted ='N'";

       if ($sdate == '' && $edate == '') {
            $today = date('Y-m-d');
            $where .= " AND DATE(`a`.`dateOut`) = '$today'";
        } else {
            if ($sdate != '') {
                $where .= " AND DATE(`a`.`dateOut`) >= '$sdate'";
            }
            if ($edate != '') {
                $where .= " AND DATE(`a`.`dateOut`) <= '$edate'";
            }
        }

		if($company!='') { 
			$where .= " AND a.customerId in  (" . implode(",", $company) . ")"; 
		}
        if($materialId!='') { 
			$where .= " AND a.materialId in  (" . implode(",", $materialId) . ")"; 
		}
       
        $sql="SELECT a.id, com.company as customer, a.customerId,a.warehouseAddressId, a.sublocationId, SUM(a.palletQuantity) palletQuantity, SUM(a.piecesQuantity) piecesQuantity, a.dateOut, warehouseAdd.warehouse as warehouse, warehouseAdd.address as warehouseAddress, sublocations.name as sublocation
        FROM `warehouseOutbounds` AS `a`
        JOIN warehouseMaterials as materials  ON materials.id=a.materialId
        JOIN companies as com  ON com.id=a.customerId
        LEFT JOIN warehouse_address as warehouseAdd  ON warehouseAdd.id=a.warehouseAddressId
        LEFT JOIN warehouse_sublocations as sublocations ON sublocations.id=a.sublocationId
        JOIN admin_login as user  ON user.id=a.dispatchedBy
        WHERE $where
        GROUP BY a.dateOut, a.customerId, a.warehouseAddressId, a.sublocationId
        ORDER BY a.id DESC";
        $summaryResults = $this->db->query($sql)->result_array();
        
        foreach ($summaryResults as &$summary) {
            $dateOut = $summary['dateOut'];
            $customerId = $summary['customerId'];
            $warehouseId = $summary['warehouseAddressId'];
            $sublocationId = $summary['sublocationId'];

            $detail_sql = "SELECT a.id, com.company as customer, a.customerId, user.uname as user, materials.materialNumber, materials.batch, a.palletNumber, a.palletPosition, a.palletQuantity, a.piecesQuantity, a.dateOut, a.date, a.lotNumber
                        FROM `warehouseOutbounds` AS `a`
                        JOIN warehouseMaterials as materials ON materials.id = a.materialId
                        JOIN companies as com ON com.id = a.customerId
                        JOIN admin_login as user  ON user.id=a.dispatchedBy
                        WHERE $where AND a.dateOut = '$dateOut' AND a.customerId=$customerId AND a.warehouseAddressId=$warehouseId AND a.sublocationId = $sublocationId
                        ORDER BY a.id DESC";

            $details = $this->db->query($detail_sql)->result_array();
            $summary['details'] = $details; 
        }
        return $summaryResults;
	}
    public function warehouseLogs($sdate,$edate,$company,$materialId){
        $where = "1=1";
        if ($sdate == '' && $edate == '') {
            $today = date('Y-m-d');
            $where .= " AND DATE(`a`.`date`) = '$today'";
        } else {
            if ($sdate != '') {
                $where .= " AND DATE(`a`.`date`) >= '$sdate'";
            }
            if ($edate != '') {
                $where .= " AND DATE(`a`.`date`) <= '$edate'";
            }
        }

		if($company!='') { 
			$where .= " AND a.customerId in  (" . implode(",", $company) . ")"; 
		}
        if($materialId!='') { 
			$where .= " AND a.materialId in  (" . implode(",", $materialId) . ")"; 
		}
        $sql = "SELECT 
                    a.*, 
                    com.company AS customer, 
                    user.uname AS user, 
                    materials.materialNumber,  
                    materials.batch
                FROM warehouseLogs AS a
                JOIN warehouseMaterials AS materials ON materials.id = a.materialId
                JOIN companies AS com ON com.id = a.customerId
                JOIN admin_login AS user ON user.id = a.userId
                WHERE $where
                ORDER BY a.id DESC";

        $logs = $this->db->query($sql)->result();

        $finalResult = [];

        foreach ($logs as $log) {
            $piecesQuantity = null;

            if ($log->table === 'warehouseOutbounds') {
                $record = $this->db->get_where('warehouseOutbounds', ['id' => $log->recordId])->row();
                $piecesQuantity = $record ? $record->piecesQuantity : null;
            } elseif ($log->table === 'warehouseInbounds') {
                $record = $this->db->get_where('warehouseInbounds', ['id' => $log->recordId])->row();
                $piecesQuantity = $record ? $record->piecesQuantity : null;
            }

            $finalResult[] = [
                'id'              => $log->id,
                'customer'        => $log->customer,
                'customerId'      => $log->customerId,
                'user'            => $log->user,
                'materialNumber'  => $log->materialNumber,
                'batch'           => $log->batch,
                'action' => $log->action,
                'date'            => $log->date,
                'table'           => $log->table,
                'recordId'        => $log->recordId,
                'piecesQuantity'  => $piecesQuantity
            ];
        }
        return $finalResult;
	}

    public function getDocumentRecord($date, $customerId, $warehouseId, $table){
        // if($table=='warehouseInbounds'){
        //     $sql = "SELECT dateIn,customerId FROM $table WHERE id=$id";
        //     $result = $this->db->query($sql)->row();
        //     $date = $result->dateIn;
        // }else{
        //     $sql = "SELECT dateOut,customerId FROM $table WHERE id=$id";
        //     $result = $this->db->query($sql)->row();
        //     $date = $result->dateOut;
        // }
        // $customerId = $result->customerId;
        
         if($table=='warehouseInbounds'){
            $details_sql = "SELECT a.id, a.customerId, materials.materialNumber, materials.batch, a.palletNumber, a.palletQuantity, a.piecesQuantity, a.dateIn, a.date, a.lotNumber, a.palletPosition, materials.description
            FROM $table a
            JOIN warehouseMaterials as materials ON materials.id = a.materialId
            WHERE a.dateIn='$date' AND a.customerId='$customerId' AND a.warehouseAddressId='$warehouseId' AND a.deleted ='N'";
        }else{
            $details_sql = "SELECT a.id, a.customerId, materials.materialNumber, materials.batch, a.palletNumber, a.palletQuantity, a.piecesQuantity, a.dateOut, a.date, a.lotNumber, a.palletPosition,  materials.description
            FROM $table a
            JOIN warehouseMaterials as materials ON materials.id = a.materialId
            WHERE a.dateOut='$date' AND a.customerId='$customerId' AND a.warehouseAddressId='$warehouseId' AND a.deleted ='N'";
        }
        // echo $details_sql;exit;
        $details_result =  $this->db->query($details_sql);
        return $details_result->result_array();
    }
}

?>