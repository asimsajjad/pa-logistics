<?php
class Warehouse_model extends CI_Model
{
    public function __construct() {
        parent::__construct(); 
        $this->load->database(); 
    }
    public function warehouse($sdate,$edate,$company,$materialId,$warehouse_id,$sublocation_id){
        $where = "1=1";
		if($company!='') { 
			$where .= " AND a.customer_id in  (" . implode(",", $company) . ")"; 
		}
        if($materialId!='') { 
			$where .= " AND a.material_id in  (" . implode(",", $materialId) . ")"; 
		}
        if($warehouse_id!='') { 
			$where .= " AND a.warehouse_id in  (" . implode(",", $warehouse_id) . ")"; 
		}
        if($sublocation_id!='') { 
			$where .= " AND a.sublocation_id in  (" . implode(",", $sublocation_id) . ")"; 
		}
		$sql="SELECT a.id, com.company as customer, a.customer_id, materials.materialNumber,  materials.batch, a.total_pallets palletQuantity,a.total_pieces quantity, a.date, materials.expirationDate, materials.description,
        warehouse.warehouse, warehouse.address AS warehouseAddress, sublocation.name AS sublocation
        FROM `warehouse_stock` AS `a`
        join warehouseMaterials as materials  ON materials.id=a.material_id
        join companies as com  ON com.id=a.customer_id       
        JOIN warehouse ON warehouse.id = a.warehouse_Id
        JOIN warehouse_sublocations AS sublocation ON sublocation.id = a.sublocation_id
        WHERE $where  
        GROUP BY a.id 
        HAVING a.total_pallets > 0 OR a.total_pieces > 0
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
    public function inbounds($sdate,$edate,$company,$materialId,$warehouse_id,$sublocation_id){
        $where = "1=1 AND a.deleted ='N' AND a.type='inbound' ";
        $where1 = "";

        if ($sdate == '' && $edate == '') {
            $today = date('Y-m-d');
            $where .= " AND DATE(`a`.`dated`) = '$today'";
        } else {
            if ($sdate != '') {
                $where .= " AND DATE(`a`.`dated`) >= '$sdate'";
            }
            if ($edate != '') {
                $where .= " AND DATE(`a`.`dated`) <= '$edate'";
            } 
        }

		if($company!='') { 
			$where .= " AND a.customer_id in  (" . implode(",", $company) . ")"; 
		}
        if($warehouse_id!='') { 
			$where .= " AND a.warehouse_id in  (" . implode(",", $warehouse_id) . ")"; 
		}
        if($sublocation_id!='') { 
			$where .= " AND a.sublocation_id in  (" . implode(",", $sublocation_id) . ")"; 
		}
        if($materialId!='') { 
			$where1 .= " AND details.material_id in  (" . implode(",", $materialId) . ")"; 
		}
       
        $sql="SELECT a.id, com.company as customer, a.customer_id, a.warehouse_id, a.sublocation_id, SUM(details.pallet_quantity) AS palletQuantity, SUM(details.pieces_quantity) as piecesQuantity, a.dated, warehouse.warehouse as warehouse, warehouse.address as warehouseAddress, sublocations.name as sublocation, a.`file`
        FROM `warehouse_bounds` AS `a`
        JOIN warehouse_bound_details details ON a.id=details.bound_id AND details.`deleted`='N' AND details.`type`='inbound'
        join companies as com  ON com.id=a.customer_id
        JOIN warehouse ON warehouse.id=a.warehouse_id
        LEFT JOIN warehouse_sublocations as sublocations ON sublocations.id=a.sublocation_id
        WHERE $where $where1
        GROUP BY a.id
        ORDER BY a.id DESC";
        // echo $sql; exit;
        $summaryResults = $this->db->query($sql)->result_array();
        
        foreach ($summaryResults as &$summary) {
            $id = $summary['id'];
            $detail_sql = "SELECT details.id, com.company as customer, details.customer_id, materials.materialNumber, materials.batch, details.pallet_number palletNumber,  details.pallet_position palletPosition, details.pallet_quantity palletQuantity, details.pieces_quantity piecesQuantity, details.date, details.lot_number lotNumber, details.notes, details.`file`
                        FROM `warehouse_bound_details` AS `details`
                        JOIN warehouseMaterials as materials ON materials.id = details.material_id
                        JOIN companies as com ON com.id = details.customer_id
                        WHERE details.deleted ='N' AND details.type='inbound' AND details.bound_id=$id $where1 
                        ORDER BY details.id DESC";
            $details = $this->db->query($detail_sql)->result_array();
            $summary['details'] = $details; 
        }
        return $summaryResults;
	}
    public function downloadWarehouseBounds($type='inbound',$sdate,$edate,$company,$materialId,$warehouse_id,$sublocation_id){
        $where = "1=1 AND details.deleted ='N' AND details.type='$type' ";
         if ($sdate == '' && $edate == '') {
            $today = date('Y-m-d');
            $where .= " AND DATE(bound.`dated`) = '$today'";
        } else {
            if ($sdate != '') {
                $where .= " AND DATE(bound.`dated`) >= '$sdate'";
            }
            if ($edate != '') {
                $where .= " AND DATE(bound.`dated`) <= '$edate'";
            } 
        }

		if($company!='') { 
			$where .= " AND bound.customer_id in  (" . implode(",", $company) . ")"; 
		}
        if($warehouse_id!='') { 
			$where .= " AND bound.warehouse_id in  (" . implode(",", $warehouse_id) . ")"; 
		}
        if($sublocation_id!='') { 
			$where .= " AND bound.sublocation_id in  (" . implode(",", $sublocation_id) . ")"; 
		}
        if($materialId!='') { 
			$where .= " AND details.material_id in  (" . implode(",", $materialId) . ")"; 
		}
        $sql="SELECT details.id, details.bound_id, bound.dated, com.company as customer, materials.materialNumber as material_number,  details.pallet_number,  details.pallet_position, details.pallet_quantity, details.pieces_quantity, details.lot_number,warehouse, sublocations.name as sublocation
                        FROM `warehouse_bound_details` AS `details`
                        JOIN warehouseMaterials as materials ON materials.id = details.material_id
                        JOIN companies as com ON com.id = details.customer_id
                        JOIN warehouse_bounds bound ON bound.id=details.bound_id
                        JOIN warehouse ON warehouse.id=bound.warehouse_id
                        JOIN warehouse_sublocations as sublocations ON sublocations.id=bound.sublocation_id
                        WHERE $where  
                        ORDER BY details.id DESC";
                        // echo $sql;exit;
        $sql_result =  $this->db->query($sql)->result_array();
        return $sql_result;
    }
    public function outbounds($sdate,$edate,$company,$materialId,$warehouse_id,$sublocation_id){
        $where = "1=1 AND a.deleted ='N' AND `a`.`type`='outbound' ";
        $where1 = "";

       if ($sdate == '' && $edate == '') {
            $today = date('Y-m-d');
            $where .= " AND DATE(`a`.`dated`) = '$today'";
        } else {
            if ($sdate != '') {
                $where .= " AND DATE(`a`.`dated`) >= '$sdate'";
            }
            if ($edate != '') {
                $where .= " AND DATE(`a`.`dated`) <= '$edate'";
            }
        }

		if($company!='') { 
			$where .= " AND a.customer_id in  (" . implode(",", $company) . ")"; 
		}
        if($warehouse_id!='') { 
			$where .= " AND a.warehouse_id in  (" . implode(",", $warehouse_id) . ")"; 
		}
        if($sublocation_id!='') { 
			$where .= " AND a.sublocation_id in  (" . implode(",", $sublocation_id) . ")"; 
		}
        if($materialId!='') { 
			$where1 .= " AND details.material_id in  (" . implode(",", $materialId) . ")"; 
		}
       
        $sql="SELECT a.id, com.company as customer, a.customer_id, a.warehouse_id, a.sublocation_id, SUM(details.pallet_quantity) AS palletQuantity, SUM(details.pieces_quantity) as piecesQuantity, a.dated, warehouse.warehouse as warehouse, warehouse.address as warehouseAddress, sublocations.name as sublocation
        FROM `warehouse_bounds` AS `a`
        JOIN warehouse_bound_details details ON a.id=details.bound_id AND details.`deleted`='N' AND details.`type`='outbound'
        join companies as com  ON com.id=a.customer_id
        JOIN warehouse ON warehouse.id=a.warehouse_id
        LEFT JOIN warehouse_sublocations as sublocations ON sublocations.id=a.sublocation_id
        WHERE $where $where1
        GROUP BY a.id
        ORDER BY a.id DESC";
        $summaryResults = $this->db->query($sql)->result_array();
        
        foreach ($summaryResults as &$summary) {
            $id = $summary['id'];
            $detail_sql = "SELECT details.id, com.company as customer, details.customer_id, materials.materialNumber, materials.batch, details.pallet_number palletNumber, details.pallet_position palletPosition, details.pallet_quantity palletQuantity, details.pieces_quantity piecesQuantity, details.date, details.lot_number lotNumber
                        FROM `warehouse_bound_details` AS `details`
                        JOIN warehouseMaterials as materials ON materials.id = details.material_id
                        JOIN companies as com ON com.id = details.customer_id
                        WHERE details.deleted ='N' AND details.type='outbound' AND details.bound_id=$id $where1 
                        ORDER BY details.id DESC";

            $details = $this->db->query($detail_sql)->result_array();
            $summary['details'] = $details; 
        }
        return $summaryResults;
	}
    public function warehouseLogs($sdate,$edate,$customer_id,$material_id){
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

		if($customer_id!='') { 
			$where .= " AND a.customer_id in  (" . implode(",", $customer_id) . ")"; 
		}
        if($material_id!='') { 
			$where .= " AND a.material_id in  (" . implode(",", $material_id) . ")"; 
		}
        $sql = "SELECT 
                    a.*, 
                    com.company AS customer, 
                    user.uname AS user, 
                    materials.materialNumber,  
                    materials.batch
                FROM warehouseLogs AS a
                JOIN warehouseMaterials AS materials ON materials.id = a.material_id
                JOIN companies AS com ON com.id = a.customer_id
                JOIN admin_login AS user ON user.id = a.userId
                WHERE $where
                ORDER BY a.id DESC";

        $logs = $this->db->query($sql)->result();

        $finalResult = [];

        foreach ($logs as $log) {
            $piecesQuantity = null;

            // if ($log->type === 'warehouseOutbounds' || $log->type === 'outbound') {
            //     $record = $this->db->get_where('warehouse_bound_details', ['id' => $log->detail_id])->row();
            //     $piecesQuantity = $record ? $record->piecesQuantity : null;
            // } elseif ($log->tpye === 'warehouseInbounds' || $log->type === 'inbound') {
            //     $record = $this->db->get_where('warehouse_bound_details', ['id' => $log->detail_id])->row();
            //     $piecesQuantity = $record ? $record->piecesQuantity : null;
            // }

            $finalResult[] = [
                'id'              => $log->id,
                'customer'        => $log->customer,
                'customerId'      => $log->customerId,
                'user'            => $log->user,
                'materialNumber'  => $log->materialNumber,
                'batch'           => $log->batch,
                'action' => $log->action,
                'date'            => $log->date,
                'type'           => $log->type,
                'detail_id'        => $log->detail_id
                // 'piecesQuantity'  => $piecesQuantity
            ];
        }
        return $finalResult;
	}
    public function getDocumentRecord($id){
        $details_sql = "SELECT a.id, a.customer_id customerId, materials.materialNumber, materials.batch, a.pallet_number palletNumber, a.pallet_quantity palletQuantity, a.pieces_quantity piecesQuantity, a.date, a.lot_number lotNumber, a.pallet_position palletPosition, materials.description
            FROM warehouse_bound_details a
            JOIN warehouseMaterials as materials ON materials.id = a.material_id
            WHERE a.bound_id='$id'";
        $details_result =  $this->db->query($details_sql);
        return $details_result->result_array();
    }
    public function materialHistory($company,$materialId){
        $where = "1=1";
		if($company!='') { 
			$where .= " AND bounds.customer_id in  (" . implode(",", $company) . ")"; 
		}
        if($materialId!='') { 
			$where .= " AND details.material_id in  (" . implode(",", $materialId) . ")"; 
		}
        
		$sql="SELECT details.id, material_id, CONCAT(material.`materialNumber`,' ( ',material.`batch`,' )') material, companies.`company` AS customer, warehouse.`warehouse`, sublocation.`name` AS sublocation, details.pallet_number,  details.pallet_position, details.lot_number, details.`type` AS movement_type, details.`pallet_quantity`, details.`pieces_quantity`, bounds.`dated`
        FROM warehouse_bound_details details
        JOIN warehouseMaterials material ON material.id = details.material_id
        JOIN warehouse_bounds bounds ON bounds.`id`=details.`bound_id`
        JOIN companies ON companies.`id`=bounds.`customer_id`
        JOIN warehouse ON bounds.`warehouse_id`=warehouse.`id`
        JOIN warehouse_sublocations sublocation ON sublocation.`id`=bounds.`sublocation_id`
        WHERE $where  
        ORDER BY details.`id` DESC";
        // echo $sql;exit;
		$sql_result =  $this->db->query($sql)->result_array();
        return $sql_result;
	}
    public function getMaterialDetailsForTransfer($ids=[]) {
        // print_r($ids);exit;
        $this->db->select('*');
        $this->db->from('warehouse_bound_details');

        if (!empty($ids)) {
            $this->db->where_in('id', $ids);
        }
        $this->db->order_by('id','DESC');
        return $this->db->get()->result_array();
    }

}

?>