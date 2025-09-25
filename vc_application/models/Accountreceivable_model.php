<?php
class Accountreceivable_model extends CI_Model
{
	
    public function __construct() {
        parent::__construct(); 
        $this->load->database(); 
    }
	public function getAccountReceivable($table, $sdate='', $edate='', $company=[], $agingSearch, $invoiceType, $customerStatus, $invoiceNo, $agingFrom, $agingTo) {
		$where='1=1';
		$extraSelect = '';
		if($sdate!='') { 
			$where .= " AND pudate >= '$sdate'";
		}
		if($edate!='') { 
			$where .= " AND pudate <= '$edate'"; 
		}
		if($company!='') { 
			$where .= " AND a.company in  (" . implode(",", $company) . ")"; 
		}

		if($agingSearch == ''){
			if ($agingFrom !== '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN $agingFrom AND $agingTo";
			} elseif ($agingFrom !== '' && $agingTo === '') {
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) >= $agingFrom";
			} elseif ($agingFrom === '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) <= $agingTo";
			}
		}
		

		if($table=='dispatchOutside'){
			$documentTable='documentsOutside';
			$type='logistics';
			$extraSelect = ' ,a.carrierPayoutDate';
		}elseif($table=='warehouse_dispatch'){
			$documentTable='warehouse_documents';
			$type='warehouse';
			$extraSelect = ' ,a.carrierPayoutDate';
		}else{
			$type='fleet';
			$documentTable='documents';
		}

		if ($agingSearch != '') {  
			if($agingSearch == 'zero'){
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 0 AND 15";
			}elseif($agingSearch == 'thirty'){
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 16 AND 30";
			}elseif($agingSearch == 'thirtyfive'){
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 35";
			}elseif($agingSearch == 'fortyfive'){
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 36 AND 45";
			}elseif($agingSearch == 'sixty'){
				$where .= " AND DATEDIFF(CURDATE(),  a.invoiceDate) BETWEEN 46 AND 60";
			}elseif($agingSearch == 'sixtyplus'){
				$where .= " AND DATEDIFF(CURDATE(),  a.invoiceDate) > 60";
			}
		}
		if($invoiceType != ''){
				$where .= " AND a.invoiceType='$invoiceType'";
		}else{
			$where .=" AND a.invoiceType IN ('Direct Bill', 'Quick Pay')";
		}

		if($customerStatus != ''){
			$where .= " AND c.status='$customerStatus'";
		}else{
			$where .= " AND c.status='Active'";
		}
		if($invoiceNo !=''){
			$where .= " AND a.invoice LIKE '%$invoiceNo%'";
		}
			
		if($type=='warehouse'){
			$sql="SELECT a.id, c.company, a.company AS company_id, c.email,
    		SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 0 AND 15 THEN a.parate ELSE 0 END) AS `0-15_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 0 AND 15 THEN 1 ELSE 0 END) AS `0-15_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 16 AND 30 THEN a.parate ELSE 0 END) AS `16-30_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `16-30_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 45 THEN a.parate ELSE 0 END) AS `31-45_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 45 THEN 1 ELSE 0 END) AS `31-45_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 46 AND 60 THEN a.parate ELSE 0 END) AS `46-60_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) AS `46-60_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 61 AND 75 THEN a.parate ELSE 0 END) AS `61-75_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 61 AND 75 THEN 1 ELSE 0 END) AS `61-75_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 76 AND 90 THEN a.parate ELSE 0 END) AS `76-90_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 76 AND 90 THEN 1 ELSE 0 END) AS `76-90_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) > 90 THEN a.parate ELSE 0 END) AS `90_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) > 90 THEN 1 ELSE 0 END) AS `90_days_count`
			FROM 
				$table a
			JOIN 
				companies c ON c.id = a.company
			WHERE $where  
				AND a.invoiced = '1'
				AND (a.invoiceClose = '0' || a.invoiceClose = '')
				AND a.invoiceDate != '0000-00-00'  
				AND (a.invoiceCloseDate = '' || a.invoiceCloseDate = '0000-00-00')
				AND (a.invoicePaid = '0' || a.invoicePaid = '')
				AND (a.invoicePaidDate = '' || a.invoicePaidDate = '0000-00-00')
			GROUP BY 
				a.company
			ORDER BY 
				a.pudate ASC
			LIMIT 1000";
		}else{
			$sql="SELECT a.id, c.company, a.company AS company_id, c.email,
    		SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 0 AND 15 THEN a.parate ELSE 0 END) AS `0-15_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 0 AND 15 THEN 1 ELSE 0 END) AS `0-15_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 16 AND 30 THEN a.parate ELSE 0 END) AS `16-30_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `16-30_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 45 THEN a.parate ELSE 0 END) AS `31-45_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 45 THEN 1 ELSE 0 END) AS `31-45_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 46 AND 60 THEN a.parate ELSE 0 END) AS `46-60_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) AS `46-60_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 61 AND 75 THEN a.parate ELSE 0 END) AS `61-75_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 61 AND 75 THEN 1 ELSE 0 END) AS `61-75_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 76 AND 90 THEN a.parate ELSE 0 END) AS `76-90_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 76 AND 90 THEN 1 ELSE 0 END) AS `76-90_days_count`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) > 90 THEN a.parate ELSE 0 END) AS `90_days_amount`,
			SUM(CASE WHEN DATEDIFF(CURDATE(), a.invoiceDate) > 90 THEN 1 ELSE 0 END) AS `90_days_count`
			FROM 
				$table a
			JOIN 
				companies c ON c.id = a.company
			WHERE $where  
				AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiced') = '1'
				AND (JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceClose') = '0' ||
				JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceClose') = '')
				AND a.invoiceDate != '0000-00-00'  
				AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceCloseDate') = ''
				AND (JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '0' ||
				JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '')
				AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') = ''
			GROUP BY 
				a.company
			ORDER BY 
				a.pudate ASC
			LIMIT 1000";
		}
		
		// echo $sql;exit;
		$query =  $this->db->query($sql);
		$companies = $query->result_array();

		if($type=='warehouse'){
			$sql_invoices="SELECT a.id, a.company AS company_id, c.company, a.invoice, a.pudate, a.edate, a.invoiceDate, a.rate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.invoiceType, d.fileurl, carrierInvoice.`fileurl` AS carrierInvoice $extraSelect
			FROM $table a
			JOIN companies c ON c.id = a.company
			LEFT JOIN $documentTable d ON d.did = a.id AND d.type = 'gd'
			LEFT JOIN $documentTable carrierInvoice ON carrierInvoice.did = a.id AND carrierInvoice.type = 'carrierInvoice'
			WHERE $where
			AND a.invoiced = '1'
			AND ( a.invoiceClose = '0' || a.invoiceClose = '')
			AND a.invoiceDate != '0000-00-00'	
			AND ( a.invoiceCloseDate = '' || a.invoiceCloseDate = '0000-00-00')
			AND (a.invoicePaid = '0' || a.invoicePaid = '')
			AND ( a.invoicePaidDate = '' || a.invoicePaidDate = '0000-00-00')
			GROUP BY a.id
			ORDER BY a.pudate ASC
			LIMIT 1000";
		}else{
			$sql_invoices="SELECT a.id, a.company AS company_id, c.company, a.invoice, a.pudate, a.dodate, a.invoiceDate, a.rate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.invoiceType, d.fileurl, carrierInvoice.`fileurl` AS carrierInvoice, a.dispatchMeta $extraSelect
			FROM $table a
			JOIN companies c ON c.id = a.company
			LEFT JOIN $documentTable d ON d.did = a.id AND d.type = 'gd'
			LEFT JOIN $documentTable carrierInvoice ON carrierInvoice.did = a.id AND carrierInvoice.type = 'carrierInvoice'
			WHERE $where
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta),'$.invoiced') = '1'
			AND ( JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta),'$.invoiceClose') = '0' || 
			JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta),'$.invoiceClose') = '')
			AND a.invoiceDate != '0000-00-00'	
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta),'$.invoiceCloseDate') = ''
			AND (JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '0' ||
			JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '')
			AND JSON_EXTRACT(
					IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), 
						'$.invoicePaidDate'
					) = ''
			GROUP BY a.id
			ORDER BY a.pudate ASC
			LIMIT 1000";
		}
		//echo $sql_invoices;exit;
		$query_invoices =  $this->db->query($sql_invoices);
		$invoices = $query_invoices->result_array();

		$sql_documents = "SELECT did, `type`, fileurl FROM $documentTable WHERE did IN (SELECT id FROM $table) AND $documentTable.type = 'gd'";
		$query_documents = $this->db->query($sql_documents);
		$documents = $query_documents->result_array();
	
		$invoice_details = [
			'all_days' => [],
			'0-15_days' => [],
			'16-30_days' => [],
			'31-45_days' => [],
			'46-60_days' => [],
			'61-75_days' => [],
			'76-90_days' => [],
			'90+_days' => []
		];
	
		foreach ($invoices as $invoice) {
			$days_diff = $invoice['days_diff'];
			$invoice_details['all_days'][] = $invoice;
			if ($days_diff >= 0 && $days_diff <= 15) {
				$invoice_details['0-15_days'][] = $invoice;
			} elseif ($days_diff >= 16 && $days_diff <= 30) {
				$invoice_details['16-30_days'][] = $invoice;
			} elseif ($days_diff >= 31 && $days_diff <= 45) {
				$invoice_details['31-45_days'][] = $invoice;
			} elseif ($days_diff >= 46 && $days_diff <= 60) {
				$invoice_details['46-60_days'][] = $invoice;
			} elseif ($days_diff >= 61 && $days_diff <= 75) {
				$invoice_details['61-75_days'][] = $invoice;
			} elseif ($days_diff >= 76 && $days_diff <= 90) {
				$invoice_details['76-90_days'][] = $invoice;
			} else {
				$invoice_details['90_days'][] = $invoice;
			}
		}
	
		$grouped_documents = [];
		foreach ($documents as $doc) {
			$grouped_documents[$doc['did']][] = $doc;
		}

		$result = [];
		foreach ($companies as $company) {
			$company_id = $company['company_id'];
	
			$company_invoices = [
				'all_days' => [],
				'0-15_days' => [],
				'16-30_days' => [],
				'31-45_days' => [],
				'46-60_days' => [],
				'61-75_days' => [],
				'76-90_days' => [],
				'90_days' => []
			];
	
			foreach ($invoice_details as $key => $invoices) {
				$filtered_invoices = array_filter($invoices, function($invoice) use ($company_id) {
					return $invoice['company_id'] == $company_id;
				});

				// Add documents to each invoice
				foreach ($filtered_invoices as &$invoice) {
					$invoice_id = $invoice['id'];
					$invoice['documents'] = isset($grouped_documents[$invoice_id]) ? $grouped_documents[$invoice_id] : [];
					   
					$sql_notes = "SELECT `subject`, `note`, `date`, uname
					FROM receivable_statment_history 
					JOIN admin_login user ON user.id=receivable_statment_history.user_id
					WHERE did= $invoice_id AND dispatch_type='$type'";
					$historyQuery = $this->db->query($sql_notes)->result_array();

					$invoice['notes'] = $historyQuery ?: [];
				}
							
				$company_invoices[$key] = $filtered_invoices;
			}
	
			$company['invoices'] = $company_invoices;
			$result[] = $company;
		}
		// echo "<pre>"; print_r($result);exit;
	// echo "<pre>"; print_r($result);exit;
		return $result;
	}
	public function getReceivableBatches($table, $sdate='', $edate='', $company=[], $agingFrom, $agingTo, $groupby='yes') {
		$where="1=1 AND batches.paymentType='receivable'";
		if($sdate!='') { 
			$where .= " AND DATE(`batches`.`date`) >= '$sdate'";
		}
		if($edate!='') { 
			$where .= " AND DATE(`batches`.`date`) <= '$edate'"; 
		}
		if($company!='') { 
			$where .= " AND a.company in  (" . implode(",", $company) . ")"; 
		}
		if ($agingFrom !== '' && $agingTo !== '') {
			$where .= " AND DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')), a.invoiceDate) BETWEEN $agingFrom AND $agingTo";
		} elseif ($agingFrom !== '' && $agingTo === '') {
			$where .= " AND DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')), a.invoiceDate) >= $agingFrom";
		} elseif ($agingFrom === '' && $agingTo !== '') {
			$where .= " AND DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')), a.invoiceDate) <= $agingTo";
		}

		$select = '';
		if($table=='dispatchOutside'){
			$documentTable='documentsOutside';
			$select=', a.carrierInvoiceRefNo';
		}else if($table=='warehouse_dispatch'){
			$documentTable='warehouse_documents';
			$select=', a.carrierInvoiceRefNo';
		}
		else{
			$documentTable='documents';
		}

		if($table=='warehouse_dispatch'){
			$sql="SELECT batches.id, batches.date `date`, 	
			CASE WHEN COUNT(DISTINCT companies.company) > 1 THEN 'Multiple Companies' ELSE MAX(companies.company) END AS company,
			sum(a.parate) as totalAmount, batchNo, a.dispatchMeta, user.uname as added_by, invoicePaidDate
			FROM receivableBatches `batches`
			JOIN 
				$table a ON a.`receivableBatchId`=`batches`.`id`
			JOIN 
				companies ON companies.id = a.company
			LEFT JOIN admin_login user ON user.id = `batches`.addedBy
			WHERE $where 
			AND a.invoiceType IN ('Direct Bill', 'Quick Pay')
			AND a.invoiced = '1'
			AND (a.invoiceDate != '' AND a.invoiceDate != '0000-00-00')   
			AND a.invoicePaid = '1'
			AND (a.invoicePaidDate != '' || a.invoicePaidDate != '0000-00-00')
			GROUP BY `batches`.`id`";
		}else {
			$sql="SELECT batches.id, batches.date `date`, 	
			CASE WHEN COUNT(DISTINCT companies.company) > 1 THEN 'Multiple Companies' ELSE MAX(companies.company) END AS company,
			sum(a.parate) as totalAmount, batchNo, a.dispatchMeta, user.uname as added_by
			FROM receivableBatches `batches`
			JOIN 
				$table a ON a.`receivableBatchId`=`batches`.`id`
			JOIN 
				companies ON companies.id = a.company
			LEFT JOIN admin_login user ON user.id = `batches`.addedBy
			WHERE $where 
			AND a.invoiceType IN ('Direct Bill', 'Quick Pay')
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiced') = '1'
			AND a.invoiceDate != '0000-00-00'  
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '1'
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') != ''
			GROUP BY `batches`.`id`";
		}
		
		$query =  $this->db->query($sql);
		$result = $query->result_array();

		foreach ($result as &$batch) {
			$batchId = $batch['id'];
			
			if($table=='warehouse_dispatch'){
				$invoiceQuery = "SELECT a.id, a.invoice, a.pudate, a.dodate, a.edate, a.invoiceDate, a.rate, a.parate, a.payableAmt, 
				DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.dispatchMeta, companies.company, DATEDIFF(a.invoicePaidDate, a.invoiceDate) received_days, infoDetails.`dispatchValue`, info.`title` $select
				FROM $table a
				JOIN receivableBatches `batches` ON batches.id=a.receivableBatchId
				JOIN companies ON companies.id = a.company
				LEFT JOIN dispatch_info_details infoDetails ON infoDetails.`did`=a.id AND infoDetails.dispatchInfoId=12
				LEFT JOIN dispatchInfo info ON infoDetails.`dispatchInfoId`=info.`id`
				WHERE $where AND a.invoiceType IN ('Direct Bill', 'Quick Pay')
				AND a.invoiced = '1'
				AND (a.invoiceDate != '' AND a.invoiceDate != '0000-00-00')  
				AND a.invoicePaid = '1'
				AND (a.invoicePaidDate != '' || a.invoicePaidDate != '0000-00-00')
				AND a.receivableBatchId = $batchId";
			}else{
				$invoiceQuery = "SELECT a.id, a.invoice, a.pudate, a.dodate, a.invoiceDate, a.rate, a.parate, a.payableAmt, 
				DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.dispatchMeta, companies.company, DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')), a.invoiceDate) received_days $select
				FROM $table a
				JOIN receivableBatches `batches` ON batches.id=a.receivableBatchId
				JOIN companies ON companies.id = a.company
				WHERE $where AND a.invoiceType IN ('Direct Bill', 'Quick Pay')
				AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiced') = '1'
				AND a.invoiceDate != '0000-00-00'  
				AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '1'
				AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') != ''
				AND a.receivableBatchId = $batchId";
			}
			
					// echo $invoiceQuery;exit;

			$invoiceDetailsQuery = $this->db->query($invoiceQuery);
			$invoiceDetails = $invoiceDetailsQuery->result_array();

			// Now loop through each invoice and attach documents
			foreach ($invoiceDetails as &$invoice) {
				$invoiceId = $invoice['id'];
		
				$invoiceDocumentsQuery = "SELECT did, `type`, fileurl 
										  FROM $documentTable 
										  WHERE did = $invoiceId AND `type` = 'gd'";
		
				$invoiceDocumentsDetails = $this->db->query($invoiceDocumentsQuery);
				$invoice['documents'] = $invoiceDocumentsDetails->result_array();
			}
		
			// Attach invoice details with their documents to the batch
			$batch['invoiceDetails'] = $invoiceDetails;

		}
		return $result;
	}
	public function getReceivableStatement($table,$sdate='',$edate='',$company,$agingSearch,$invoiceType,$customerStatus, $invoiceNo,$agingFrom, $agingTo, $invoiceIds=[]) {
            // $this->db->from($table);
			$where="1=1 AND a.parate>0";
			if($sdate!='') { 
				$where .= " AND pudate >= '$sdate'";
			}
			if($edate!='') { 
				$where .= " AND pudate <= '$edate'"; 
			}
			if($company!='') { 
				$where .= " AND a.company = '$company' "; 
			}
		
		if ($agingSearch != '') {  
			if($agingSearch == 'fortyfive'){
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN 31 AND 45";
			}elseif($agingSearch == 'sixty'){
				$where .= " AND DATEDIFF(CURDATE(),  a.invoiceDate) BETWEEN 46 AND 60";
			}elseif($agingSearch == 'sixtyplus'){
				$where .= " AND DATEDIFF(CURDATE(),  a.invoiceDate) > 60";
			}
		}
		if($invoiceType != ''){
			$where .= " AND a.invoiceType='$invoiceType'";
		}else{
			$where .=" AND a.invoiceType IN ('Direct Bill', 'Quick Pay')";
		}
	
		if($customerStatus != ''){
			$where .= " AND c.status='$customerStatus'";
		}else{
			$where .= " AND c.status='Active'";
		}
		if($invoiceNo !=''){
			$where .= " AND a.invoice LIKE '%$invoiceNo%'";
		}
		if($invoiceIds!='') { 
			$where .= " AND a.id in  (" . implode(",", $invoiceIds) . ")"; 
		}

		if($agingSearch == ''){
			// if ($agingFrom !== '' && $agingTo !== '') {
			// 	$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) BETWEEN $agingFrom AND $agingTo";
			// }
			//  else
			if ($agingFrom != '') {
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) >= $agingFrom";
			} 
			// else
			if ($agingTo != '') {
				$where .= " AND DATEDIFF(CURDATE(), a.invoiceDate) <= $agingTo";
			}
		}
		
		if($table=='warehouse_dispatch'){
			$sql="SELECT a.id,pudate,dodate,pcity,dcity,rate,parate,a.company,dlocation,plocation,paddressid,daddressid,dWeek,trailer,tracking,invoice,childInvoice,parentInvoice,invoiceDate,invoiceType,expectPayDate,payableAmt,payoutAmount,a.status,rdate,dispatchMeta, parate AS total_parate, c.email, partialAmount,invoicePDF, invoicePaidDate, invoiceCloseDate
			FROM $table a
			JOIN companies c ON c.id = a.company
			WHERE $where 
			AND a.invoiced = '1'
			AND (a.invoiceClose = '0' || a.invoiceClose = '')
			AND a.invoiceDate != '0000-00-00'  
			AND (a.invoiceCloseDate = '' || a.invoiceCloseDate = '0000-00-00' )
			AND (a.invoicePaid = '0' || a.invoicePaid = '')
			AND (a.invoicePaidDate = '' || a.invoicePaidDate = '0000-00-00' )
			ORDER BY a.pudate ASC LIMIT 1000";  
		}else{
			$sql="SELECT a.id,pudate,dodate,pcity,dcity,rate,parate,a.company,dlocation,plocation,paddressid,daddressid,dWeek,trailer,tracking,invoice,childInvoice,parentInvoice,invoiceDate,invoiceType,expectPayDate,payableAmt,payoutAmount,a.status,rdate,dispatchMeta, parate AS total_parate, c.email 
			FROM $table a
			JOIN companies c ON c.id = a.company
			WHERE $where 
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiced') = '1'
			AND (JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceClose') = '0' ||
			JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceClose') = '')
			AND a.invoiceDate != '0000-00-00'  
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiceCloseDate') = ''
			AND (JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '0' ||
			JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '')
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') = ''
			ORDER BY a.pudate ASC LIMIT 1000";  
		}

     	  
        $query = $this->db->query($sql);
		// echo $this->db->last_query();
		// exit;

        return $query->result_array();
    }

	public function downloadReceivableBatches($table, $sdate = '', $edate = '', $company = [], $agingFrom, $agingTo){
		$where="1=1 AND batches.paymentType='receivable'";
		if($sdate!='') { 
			$where .= " AND DATE(`batches`.`date`) >= '$sdate'";
		}
		if($edate!='') { 
			$where .= " AND DATE(`batches`.`date`) <= '$edate'"; 
		}
		if($company!='') { 
			$where .= " AND a.company in  (" . implode(",", $company) . ")"; 
		}
		if($table=='warehouse_dispatch'){
			if ($agingFrom !== '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(a.invoicePaidDate, a.invoiceDate) BETWEEN $agingFrom AND $agingTo";
			} elseif ($agingFrom !== '' && $agingTo === '') {
				$where .= " AND DATEDIFF(a.invoicePaidDate, a.invoiceDate) >= $agingFrom";
			} elseif ($agingFrom === '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(a.invoicePaidDate, a.invoiceDate) <= $agingTo";
			}
		}else{
			if ($agingFrom !== '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')), a.invoiceDate) BETWEEN $agingFrom AND $agingTo";
			} elseif ($agingFrom !== '' && $agingTo === '') {
				$where .= " AND DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')), a.invoiceDate) >= $agingFrom";
			} elseif ($agingFrom === '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')), a.invoiceDate) <= $agingTo";
			}
		}

		
		$select = '';
		if(($table=='dispatchOutside') || ($table=='warehouse_dispatch')){
			$select=', a.carrierInvoiceRefNo';
		}

		if($table=='warehouse_dispatch'){
			$sql="SELECT batches.id, user.uname as added_by, c.company as company, a.invoicePaidDate as paid_date, batchNo, batches.date as `date`, a.invoice, a.dispatchMeta, a.dodate, a.edate, a.invoiceDate, a.parate, DATEDIFF(a.invoicePaidDate, a.invoiceDate) received_days, infoDetails.`dispatchValue`, info.`title` $select 
			FROM receivableBatches AS `batches`
			JOIN $table a ON a.`receivableBatchId`=`batches`.`id`
			LEFT JOIN admin_login user ON user.id = `batches`.addedBy
			LEFT JOIN dispatch_info_details infoDetails ON infoDetails.`did`=a.id AND infoDetails.dispatchInfoId=12
			LEFT JOIN dispatchInfo info ON infoDetails.`dispatchInfoId`=info.`id`
			JOIN companies c ON c.id = a.company
			WHERE $where
			AND a.invoiceType IN ('Direct Bill', 'Quick Pay')
			AND a.invoiced = '1'
			AND (a.invoiceDate != '0000-00-00'  || a.invoiceDate != '' ) 
			AND a.invoicePaid = '1'
			AND (a.invoicePaidDate != '0000-00-00' || a.invoicePaidDate != '')
			ORDER BY batches.date ASC";
		}
		else{
			$sql="SELECT batches.id, user.uname as added_by, c.company as company, JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')) as paid_date, batchNo, batches.date as `date`, a.invoice, a.dispatchMeta, a.dodate, a.invoiceDate, a.parate, DATEDIFF(JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate')), a.invoiceDate) received_days $select 
			FROM receivableBatches AS `batches`
			JOIN $table a ON a.`receivableBatchId`=`batches`.`id`
			LEFT JOIN admin_login user ON user.id = `batches`.addedBy
			JOIN companies c ON c.id = a.company
			WHERE $where
			AND a.invoiceType IN ('Direct Bill', 'Quick Pay')
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoiced') = '1'
			AND a.invoiceDate != '0000-00-00'  
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaid') = '1'
			AND JSON_EXTRACT(IF(a.dispatchMeta = '' OR a.dispatchMeta IS NULL, '{}', a.dispatchMeta), '$.invoicePaidDate') != ''
			ORDER BY batches.date ASC";
		}
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}
}

?>