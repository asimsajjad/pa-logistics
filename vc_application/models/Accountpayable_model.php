<?php
class Accountpayable_model extends CI_Model
{
	
    public function __construct() {
        parent::__construct(); 
        $this->load->database(); 
    }
	public function getAccountPayable($table, $sdate = '', $edate = '', $company = [], $factoringCompany=[], $agingSearch, $invoiceType, $invoiceNo, $carrierInvoiceRefNo, $agingFrom, $agingTo) {
		$where = '1=1 AND a.bookedUnder !=4';
		if ($sdate != '') { 
			$where .= " AND pudate >= '$sdate'";
		}
		if ($edate != '') { 
			$where .= " AND pudate <= '$edate'"; 
		}
		if ($company != '') {
			$company_list = implode(",", $company);
		
			if (in_array(4, $company)) {
				$where .= " AND (
					a.truckingcompany IN ($company_list)
					OR a.bookedUnderNew = 4
				)";
			} else {
				$where .= " AND a.truckingcompany IN ($company_list)";
			}
		}
		if ($factoringCompany != '') {
			$factoringCompany_list = implode(",", $factoringCompany);
			$where .= " AND a.factoringCompany IN ($factoringCompany_list)";
		}
		if($invoiceType != ''){
			if($invoiceType == 'Quick Pay' || $invoiceType == 'Zelle'){
				$where .= " AND a.carrierPaymentType='$invoiceType'";
			}elseif($invoiceType == 'Standard Billing'){
				$where .= " AND a.carrierPaymentType !='Quick Pay' AND a.carrierPaymentType != 'Zelle'";
			}
		}
		if($table == 'warehouse_dispatch'){ 
			$documentTable = 'warehouse_documents';
		}else{
			$documentTable='documentsOutside';
		}
		if($table == 'warehouse_dispatch'){
			if ($agingSearch != '') {  
				if($agingSearch == 'pending'){
					$where .= " AND (a.invoicePaidDate IS NOT NULL AND a.invoicePaidDate != '0000-00-00')";
				}
				if($agingSearch == 'zero'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 0 AND 15";
				}elseif($agingSearch == 'thirty'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 16 AND 30";
				}elseif($agingSearch == 'thirtyfive'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 31 AND 35";
				}elseif($agingSearch == 'fortyfive'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 36 AND 45";
				}elseif($agingSearch == 'sixty'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 46 AND 60";
				}elseif($agingSearch == 'sixtyplus'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) > 60";
				}
			}
		}else{
			if ($agingSearch != '') {  
				if($agingSearch == 'pending'){
					$where .= " AND ((JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate'))) IS NOT NULL 
					AND (JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.invoicePaidDate'))) != '')";
				}
				if($agingSearch == 'zero'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 0 AND 15";
				}elseif($agingSearch == 'thirty'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 16 AND 30";
				}elseif($agingSearch == 'thirtyfive'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 31 AND 35";
				}elseif($agingSearch == 'fortyfive'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 36 AND 45";
				}elseif($agingSearch == 'sixty'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 46 AND 60";
				}elseif($agingSearch == 'sixtyplus'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) > 60";
				}
			}
		}
		
	
		if($invoiceNo !=''){
			$where .= " AND a.invoice LIKE '%$invoiceNo%'";
		}
		if($carrierInvoiceRefNo !=''){
			$where .= " AND a.carrierInvoiceRefNo LIKE '%$carrierInvoiceRefNo%'";
		}

		if($table == 'warehouse_dispatch'){
			if($agingSearch == ''){
				if ($agingFrom !== '' && $agingTo !== '') {
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN $agingFrom AND $agingTo";
				} elseif ($agingFrom !== '' && $agingTo === '') {
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) >= $agingFrom";
				} elseif ($agingFrom === '' && $agingTo !== '') {
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) <= $agingTo";
				}
			}
		}else {
			if($agingSearch == ''){
				if ($agingFrom !== '' && $agingTo !== '') {
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN $agingFrom AND $agingTo";
				} elseif ($agingFrom !== '' && $agingTo === '') {
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) >= $agingFrom";
				} elseif ($agingFrom === '' && $agingTo !== '') {
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) <= $agingTo";
				}
			}
		}
		

		if($table == 'warehouse_dispatch'){
			$sql = "SELECT a.id, truckingCompanies.company, CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END AS company_id,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 0 AND 15 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `0-15_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 0 AND 15 THEN 1 ELSE 0 END) AS `0-15_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 16 AND 30 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `16-30_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `16-30_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 31 AND 45 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `31-45_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 31 AND 45 THEN 1 ELSE 0 END) AS `31-45_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 46 AND 60 
						THEN (CASE WHEN  a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `46-60_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) AS `46-60_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 61 AND 75 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `61-75_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 61 AND 75 THEN 1 ELSE 0 END) AS `61-75_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 76 AND 90 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `76-90_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 76 AND 90 THEN 1 ELSE 0 END) AS `76-90_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), a.custInvDate) > 90 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `90_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), a.custInvDate) > 90 THEN 1 ELSE 0 END) AS `90_days_count`
				FROM warehouse_dispatch a
				LEFT JOIN truckingCompanies ON truckingCompanies.id = CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END
				WHERE a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
				AND ( a.custInvDate != '' &&  a.custInvDate != '0000-00-00')
				AND $where
				GROUP BY CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END
				ORDER BY a.custInvDate ASC
				LIMIT 1000";	
				// echo $sql;exit;
				$query = $this->db->query($sql);
				$companies = $query->result_array();

				$sql_invoices = "SELECT a.id, a.truckingcompany AS company_id, a.carrierInvoiceRefNo, c.company, bookedUnderOld.company as bookedUnderOld, a.invoice, a.pudate, a.dodate, a.edate, a.invoiceDate, (a.rate - IFNULL(a.carrierPartialAmt, 0)) AS rate, a.agentRate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), a.custInvDate) AS days_diff, a.carrierPayoutDate, d.fileurl AS doc_fileurl, carrierInvoice.fileurl AS carrierInvoice, a.dispatchMeta,bookedUnder, bookedUnderNew, booked_under.company as bookedUnder, a.carrierPaymentType, fact.company as factoringCompany, a.factoringType, carrierPartialAmt, a.custInvDate, a.invoicePaidDate
				FROM warehouse_dispatch a
				LEFT JOIN truckingCompanies c ON c.id = a.truckingcompany
				LEFT JOIN booked_under ON booked_under.id=a.bookedUnderNew
				LEFT JOIN truckingCompanies bookedUnderOld ON bookedUnderOld.id = a.bookedUnder
				LEFT JOIN documentsOutside d ON d.did = a.id AND d.type = 'carrierGd'
				LEFT JOIN factoringCompanies fact ON fact.id = a.factoringCompany
				LEFT JOIN documentsOutside carrierInvoice ON carrierInvoice.did = a.id AND carrierInvoice.type = 'carrierInvoice'
				WHERE a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
				AND ( a.custInvDate != '' ||  a.custInvDate != '0000-00-00')
				AND $where
				GROUP BY a.id
				ORDER BY a.custInvDate ASC
				LIMIT 1000";
				
		}
		else{
			$sql = "SELECT a.id, truckingCompanies.company, CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END AS company_id,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 0 AND 15 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `0-15_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 0 AND 15 THEN 1 ELSE 0 END) AS `0-15_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 16 AND 30 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `16-30_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `16-30_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 31 AND 45 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `31-45_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 31 AND 45 THEN 1 ELSE 0 END) AS `31-45_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 46 AND 60 
						THEN (CASE WHEN  a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `46-60_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) AS `46-60_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 61 AND 75 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `61-75_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 61 AND 75 THEN 1 ELSE 0 END) AS `61-75_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 76 AND 90 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `76-90_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 76 AND 90 THEN 1 ELSE 0 END) AS `76-90_days_count`,
				SUM(CASE 
					WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) > 90 
						THEN (CASE WHEN a.bookedUnderNew = 4 THEN (a.rate + a.agentRate - IFNULL(a.carrierPartialAmt,0)) 
				ELSE (a.rate - IFNULL(a.carrierPartialAmt,0)) END)
					ELSE 0 
				END) AS `90_days_amount`,
				SUM(CASE WHEN DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) > 90 THEN 1 ELSE 0 END) AS `90_days_count`
				FROM dispatchOutside a
				LEFT JOIN truckingCompanies ON truckingCompanies.id = CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END
				WHERE a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
				AND JSON_VALID(a.dispatchMeta)
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
				AND $where
				GROUP BY CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END
				ORDER BY JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) ASC
				LIMIT 1000";	
				// echo $sql;exit;
				$query = $this->db->query($sql);
				$companies = $query->result_array();

				$sql_invoices = "SELECT a.id, a.truckingcompany AS company_id, a.carrierInvoiceRefNo, c.company, bookedUnderOld.company as bookedUnderOld, a.invoice, a.pudate, a.dodate, a.invoiceDate, (a.rate - IFNULL(a.carrierPartialAmt, 0)) AS rate, a.agentRate, a.parate, a.payableAmt, DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) AS days_diff, a.carrierPayoutDate, d.fileurl AS doc_fileurl, carrierInvoice.fileurl AS carrierInvoice, a.dispatchMeta,bookedUnder, bookedUnderNew, booked_under.company as bookedUnder, a.carrierPaymentType, fact.company as factoringCompany, a.factoringType, carrierPartialAmt
				FROM dispatchOutside a
				LEFT JOIN truckingCompanies c ON c.id = a.truckingcompany
				LEFT JOIN booked_under ON booked_under.id=a.bookedUnderNew
				LEFT JOIN truckingCompanies bookedUnderOld ON bookedUnderOld.id = a.bookedUnder
				LEFT JOIN documentsOutside d ON d.did = a.id AND d.type = 'carrierGd'
				LEFT JOIN factoringCompanies fact ON fact.id = a.factoringCompany
				LEFT JOIN documentsOutside carrierInvoice ON carrierInvoice.did = a.id AND carrierInvoice.type = 'carrierInvoice'
				WHERE a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
				AND JSON_VALID(a.dispatchMeta)
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
				AND $where
				GROUP BY a.id
				ORDER BY JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) ASC
				LIMIT 1000";
				
		}	
		// echo $sql_invoices;exit;
		$query_invoices = $this->db->query($sql_invoices);
		$invoices = $query_invoices->result_array();
		
	
		$sql_documents = "SELECT did, `type`, fileurl FROM $documentTable WHERE did IN (SELECT id FROM $table) AND $documentTable.`type` = 'carrierGd'";
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
			if ($invoice['bookedUnderNew'] == 4) {
				$invoice['company_id'] = 4;
			}

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
	
		// Group documents by invoice ID
		$grouped_documents = [];
		foreach ($documents as $doc) {
			$grouped_documents[$doc['did']][] = $doc;
		}
	
		$result = [];
		// echo "<pre><br>";
		foreach ($companies as $company) {
			$company_id = $company['company_id'];
			
			$amounts = [
				$company['0-15_days_amount'],
				$company['16-30_days_amount'],
				$company['31-45_days_amount'],
				$company['46-60_days_amount'],
				$company['61-75_days_amount'],
				$company['76-90_days_amount'],
				$company['90_days_amount']
			];
			// echo "<pre><br>";
			// print_r(amounts);exit;
			
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
			if(array_sum($amounts)>0){
				foreach ($invoice_details as $key => $invoices) {
					$filtered_invoices = array_filter($invoices, function ($invoice) use ($company_id) {
						return $invoice['company_id'] == $company_id;
					});
		
					// Add documents to each invoice
					foreach ($filtered_invoices as &$invoice) {
						$invoice_id = $invoice['id'];
						$invoice['documents'] = isset($grouped_documents[$invoice_id]) ? $grouped_documents[$invoice_id] : [];
					}
					
		
					$company_invoices[$key] = $filtered_invoices;
				}
				$company['invoices'] = $company_invoices;
				$result[] = $company;
			}
		
		}
		// echo "<pre>"; print_r($result);exit;
		return $result;
	}
	public function getPayableStatementOfAccount($table,$sdate='',$edate='',$company='', $agingSearch, $invoiceType, $invoiceNo, $carrierInvoiceRefNo, $agingFrom, $agingTo) {
		$where = '1=1 AND a.bookedUnder !=4';
		if ($company == 4) {
			$where .= " AND (a.truckingcompany = '4' OR a.bookedUnderNew = 4)";
		} else {
			$where .= " AND a.truckingcompany = '$company'";
		}
		
		if($table == 'warehouse_dispatch'){
			if ($agingSearch != '') {  
				if($agingSearch == 'zero'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 0 AND 15";
				}elseif($agingSearch == 'thirty'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 16 AND 30";
				}elseif($agingSearch == 'thirtyfive'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 31 AND 35";
				}elseif($agingSearch == 'fortyfive'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 36 AND 45";
				}elseif($agingSearch == 'sixty'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN 46 AND 60";
				}elseif($agingSearch == 'sixtyplus'){
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) > 60";
				}
			}
		}else{
			if ($agingSearch != '') {  
				if($agingSearch == 'zero'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 0 AND 15";
				}elseif($agingSearch == 'thirty'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 16 AND 30";
				}elseif($agingSearch == 'thirtyfive'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 31 AND 35";
				}elseif($agingSearch == 'fortyfive'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 36 AND 45";
				}elseif($agingSearch == 'sixty'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN 46 AND 60";
				}elseif($agingSearch == 'sixtyplus'){
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) > 60";
				}
			}
		}

		if($invoiceType != ''){
			if($invoiceType == 'Quick Pay' || $invoiceType == 'Zelle'){
				$where .= " AND a.carrierPaymentType='$invoiceType'";
			}elseif($invoiceType == 'Standard Billing'){
				$where .= " AND a.carrierPaymentType !='Quick Pay' AND a.carrierPaymentType != 'Zelle'";
			}
		}
		if($invoiceNo !=''){
			$where .= " AND a.invoice LIKE '%$invoiceNo%'";
		}
		if($carrierInvoiceRefNo !=''){
			$where .= " AND a.carrierInvoiceRefNo LIKE '%$carrierInvoiceRefNo%'";
		}
		
		if($table == 'warehouse_dispatch'){
			if($agingSearch == ''){
				if ($agingFrom !== '' && $agingTo !== '') {
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) BETWEEN $agingFrom AND $agingTo";
				} elseif ($agingFrom !== '' && $agingTo === '') {
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) >= $agingFrom";
				} elseif ($agingFrom === '' && $agingTo !== '') {
					$where .= " AND DATEDIFF(CURDATE(), a.custInvDate) <= $agingTo";
				}
			}
		}else {
			if($agingSearch == ''){
				if ($agingFrom !== '' && $agingTo !== '') {
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN $agingFrom AND $agingTo";
				} elseif ($agingFrom !== '' && $agingTo === '') {
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) >= $agingFrom";
				} elseif ($agingFrom === '' && $agingTo !== '') {
					$where .= " AND DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) <= $agingTo";
				}
			}
		}

		if($table == 'warehouse_dispatch'){
			$sql="Select a.id,pudate,dodate,pcity,dcity,rate,agentRate,parate,company,dlocation,plocation,paddressid,daddressid,dWeek,trailer,tracking,invoice,childInvoice,parentInvoice,invoiceDate, a.edate,invoiceType,expectPayDate,payableAmt,payoutAmount,a.status,rdate,dispatchMeta, parate AS total_parate, DATEDIFF(CURDATE(), a.custInvDate) AS days_diff, bookedUnder, bookedUnderNew, custInvDate,  carrierInfoDetails.`dispatchValue` as carrier_ref_no,  carrierinfo.`title` as carrier_ref_no_title,  poInfoDetails.`dispatchValue` as po_no, pOinfo.`title` as po_no_title, a.custDueDate
			FROM $table a
			LEFT JOIN dispatch_info_details carrierInfoDetails ON carrierInfoDetails.`did`=a.id AND carrierInfoDetails.dispatchInfoId=12
			LEFT JOIN dispatchInfo carrierinfo ON carrierInfoDetails.`dispatchInfoId`=carrierinfo.`id`
			LEFT JOIN dispatch_info_details poInfoDetails ON poInfoDetails.`did`=a.id AND poInfoDetails.dispatchInfoId=10
			LEFT JOIN dispatchInfo pOinfo ON poInfoDetails.`dispatchInfoId`=pOinfo.`id`
			WHERE  a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
			AND ( a.custInvDate != '' &&  a.custInvDate != '0000-00-00')
			AND $where
			ORDER BY a.custInvDate ASC limit 1000";
		}
		else{
			$sql="Select id,pudate,dodate,pcity,dcity,rate,agentRate,parate,company,dlocation,plocation,paddressid,daddressid,dWeek,trailer,tracking,invoice,childInvoice,parentInvoice,invoiceDate,invoiceType,expectPayDate,payableAmt,payoutAmount,status,rdate,dispatchMeta, parate AS total_parate, DATEDIFF(CURDATE(), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) AS days_diff, bookedUnder, bookedUnderNew
			FROM $table a
			WHERE  a.carrierPayoutDate = '0000-00-00' AND a.carrierPayoutCheck = '0' 
				AND JSON_VALID(a.dispatchMeta)
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
				AND $where
			ORDER BY JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) ASC limit 1000";
		}
        
		// echo $sql;exit;
        $query =  $this->db->query($sql);
        return $query->result_array();
    }
	public function getPayableBatches($table, $sdate = '', $edate = '', $company = [], $agingFrom, $agingTo)
	{
		$where = "1=1 AND a.bookedUnder !=4 AND batches.paymentType='payable'";
		if($sdate!='') { 
			$where .= " AND DATE(`batches`.`date`) >= '$sdate'";
		}
		if($edate!='') { 
			$where .= " AND DATE(`batches`.`date`) <= '$edate'"; 
		}
		// if ($truckingCompany != '') {
		// 	$where .= " AND a.truckingcompany IN (" . implode(",", $truckingCompany) . ")";
		// }
		if ($company != '') {
			$company_list = implode(",", $company);
		
			if (in_array(4, $company)) {
				$where .= " AND (
					a.truckingcompany IN ($company_list)
					OR a.bookedUnderNew = 4
				)";
			} else {
				$where .= " AND a.truckingcompany IN ($company_list)";
			}
		}

		if($table=='warehouse_dispatch'){
			if ($agingFrom !== '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), a.custInvDate) BETWEEN $agingFrom AND $agingTo";
			} elseif ($agingFrom !== '' && $agingTo === '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), a.custInvDate) >= $agingFrom";
			} elseif ($agingFrom === '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), a.custInvDate) <= $agingTo";
			}
		}else{
			if ($agingFrom !== '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN $agingFrom AND $agingTo";
			} elseif ($agingFrom !== '' && $agingTo === '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) >= $agingFrom";
			} elseif ($agingFrom === '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) <= $agingTo";
			}
		}

		if($table=='warehouse_dispatch'){ 
			$sql = "SELECT batches.id, DATE(a.carrierPayoutDate) AS carrierPayoutDate, 
			CASE WHEN COUNT(DISTINCT c.company) > 1 THEN 'Multiple Carriers' ELSE MAX(c.company) END AS company, 
			batchNo , SUM(a.rate) as totalAmount, batches.date as `date`, CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END AS company_id, user.uname as added_by
                FROM payableBatches AS `batches`
                JOIN $table a ON a.payableBatchId = batches.id
				LEFT JOIN admin_login user ON user.id = `batches`.addedBy
				JOIN truckingCompanies c ON c.id = CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END
                WHERE $where
				AND a.carrierPayoutDate != '0000-00-00' AND a.carrierPayoutCheck = '1' 
				AND (a.custInvDate !='' && a.custInvDate != '0000-00-00')
                GROUP BY batches.id LIMIT 1000";
		}else{
			$sql = "SELECT batches.id, DATE(a.carrierPayoutDate) AS carrierPayoutDate, 
			CASE WHEN COUNT(DISTINCT c.company) > 1 THEN 'Multiple Carriers' ELSE MAX(c.company) END AS company, 
			batchNo , SUM(a.rate) as totalAmount, batches.date as `date`, CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END AS company_id, user.uname as added_by
                FROM payableBatches AS `batches`
                JOIN $table a ON a.payableBatchId = batches.id
				LEFT JOIN admin_login user ON user.id = `batches`.addedBy
				JOIN truckingCompanies c ON c.id = CASE WHEN a.bookedUnderNew = 4 THEN 4 ELSE a.truckingcompany END
                WHERE $where
				AND a.carrierPayoutDate != '0000-00-00' AND a.carrierPayoutCheck = '1' 
				AND JSON_VALID(a.dispatchMeta)
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
                GROUP BY batches.id LIMIT 1000";
		}
				// echo $sql; exit;
		$query = $this->db->query($sql);
		$result = $query->result_array();

		foreach ($result as &$batch) {
			$batchId = $batch['id'];
			if($table=='warehouse_dispatch'){ 
				$invoiceQuery = "SELECT a.id, a.invoice, a.pudate, a.dodate, a.edate, a.invoiceDate, a.rate, a.parate, a.payableAmt, 
				DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.dispatchMeta, booked_under.company as bookedUnder, bookedUnderOld.company as bookedUnderOld, c.company, carrierInvoiceRefNo, DATEDIFF(DATE(a.carrierPayoutDate), a.custInvDate) AS pay_days, a.custInvDate, infoDetails.`dispatchValue`, info.`title`
				FROM $table a
				JOIN payableBatches `batches` ON a.payableBatchId = batches.id
				LEFT JOIN truckingCompanies c ON c.id = a.truckingcompany
				LEFT JOIN booked_under ON booked_under.id=a.bookedUnderNew
				LEFT JOIN truckingCompanies bookedUnderOld ON bookedUnderOld.id = a.bookedUnder
				LEFT JOIN dispatch_info_details infoDetails ON infoDetails.`did`=a.id AND infoDetails.dispatchInfoId=12
				LEFT JOIN dispatchInfo info ON infoDetails.`dispatchInfoId`=info.`id`
				WHERE $where AND a.carrierPayoutDate != '0000-00-00' AND a.carrierPayoutCheck = '1' 
				AND (a.custInvDate !='' && a.custInvDate != '0000-00-00')
				AND a.payableBatchId = $batchId";
			}else{
				$invoiceQuery = "SELECT a.id, a.invoice, a.pudate, a.dodate, a.invoiceDate, a.rate, a.parate, a.payableAmt, 
				DATEDIFF(CURDATE(), a.invoiceDate) AS days_diff, a.dispatchMeta, booked_under.company as bookedUnder, bookedUnderOld.company as bookedUnderOld, c.company, carrierInvoiceRefNo, DATEDIFF(DATE(a.carrierPayoutDate), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) AS pay_days
				FROM $table a
				JOIN payableBatches `batches` ON a.payableBatchId = batches.id
				LEFT JOIN truckingCompanies c ON c.id = a.truckingcompany
				LEFT JOIN booked_under ON booked_under.id=a.bookedUnderNew
				LEFT JOIN truckingCompanies bookedUnderOld ON bookedUnderOld.id = a.bookedUnder
				WHERE $where AND a.carrierPayoutDate != '0000-00-00' AND a.carrierPayoutCheck = '1' 
				AND JSON_VALID(a.dispatchMeta)
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
				AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
				AND a.payableBatchId = $batchId";
			}
				// echo $invoiceQuery;exit;

			$invoiceDetailsQuery = $this->db->query($invoiceQuery);
			$invoiceDetails = $invoiceDetailsQuery->result_array();
			foreach ($invoiceDetails as &$invoice) {
				$invoiceId = $invoice['id'];
				if($table=='warehouse_dispatch'){ 
					$invoiceDocumentsQuery = "SELECT did, type, fileurl FROM warehouse_documents WHERE did = $invoiceId AND type = 'carrierGd'";
				}else{
					$invoiceDocumentsQuery = "SELECT did, type, fileurl FROM documentsOutside WHERE did = $invoiceId AND type = 'carrierGd'";
				}
				
				$invoiceDocumentsDetails = $this->db->query($invoiceDocumentsQuery);
				$invoice['documentsOutside'] = $invoiceDocumentsDetails->result_array();
			}
			$batch['invoiceDetails'] = $invoiceDetails;
		}

		return $result;
	}

	public function downloadPayableBatches($table, $sdate = '', $edate = '', $company = [], $agingFrom, $agingTo){

		$where = "1=1 AND a.bookedUnder !=4 AND batches.paymentType='payable'";
		if($sdate!='') { 
			$where .= " AND DATE(`batches`.`date`) >= '$sdate'";
		}
		if($edate!='') { 
			$where .= " AND DATE(`batches`.`date`) <= '$edate'"; 
		}
		if ($company != '') {
			$company_list = implode(",", $company);
		
			if (in_array(4, $company)) {
				$where .= " AND (
					a.truckingcompany IN ($company_list)
					OR a.bookedUnderNew = 4
				)";
			} else {
				$where .= " AND a.truckingcompany IN ($company_list)";
			}
		}

		if($table=='warehouse_dispatch'){ 
			if ($agingFrom !== '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), a.custInvDate) BETWEEN $agingFrom AND $agingTo";
			} elseif ($agingFrom !== '' && $agingTo === '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), a.custInvDate) >= $agingFrom";
			} elseif ($agingFrom === '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), a.custInvDate) <= $agingTo";
			}else{
				$where .= " AND 1=1";
			}
		}else{
			if ($agingFrom !== '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) BETWEEN $agingFrom AND $agingTo";
			} elseif ($agingFrom !== '' && $agingTo === '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) >= $agingFrom";
			} elseif ($agingFrom === '' && $agingTo !== '') {
				$where .= " AND DATEDIFF(DATE(a.carrierPayoutDate), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) <= $agingTo";
			}else{
				$where .= " AND 1=1";
			}
		}

		if($table=='warehouse_dispatch'){ 
			$sql="SELECT batches.id, DATE(a.carrierPayoutDate) AS carrierPayoutDate, c.company as carrier, a.invoice, carrierInvoiceRefNo, a.dodate, a.edate, a.invoiceDate, a.rate, batchNo, batches.date as `date`, user.uname as added_by,  DATEDIFF(DATE(a.carrierPayoutDate), a.custInvDate) AS pay_days, a.dispatchMeta, booked_under.company as bookedUnder, a.custInvDate, infoDetails.`dispatchValue`, info.`title`
			FROM payableBatches AS `batches`
			JOIN $table a ON a.payableBatchId = batches.id
			LEFT JOIN admin_login user ON user.id = `batches`.addedBy
			JOIN truckingCompanies c ON c.id = a.truckingcompany
			LEFT JOIN booked_under ON booked_under.id=a.bookedUnderNew 
			LEFT JOIN dispatch_info_details infoDetails ON infoDetails.`did`=a.id AND infoDetails.dispatchInfoId=12
			LEFT JOIN dispatchInfo info ON infoDetails.`dispatchInfoId`=info.`id`
			WHERE $where
			AND a.carrierPayoutDate != '0000-00-00' AND a.carrierPayoutCheck = '1' 
			AND (a.custInvDate != '0000-00-00' && a.custInvDate != '' )
			ORDER BY batches.date ASC";
		}else{
			$sql="SELECT batches.id, DATE(a.carrierPayoutDate) AS carrierPayoutDate, c.company as carrier, a.invoice, carrierInvoiceRefNo, a.dodate, a.invoiceDate, a.rate, batchNo, batches.date as `date`, user.uname as added_by,  DATEDIFF(DATE(a.carrierPayoutDate), JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate'))) AS pay_days, a.dispatchMeta, booked_under.company as bookedUnder
			FROM payableBatches AS `batches`
			JOIN $table a ON a.payableBatchId = batches.id
			LEFT JOIN admin_login user ON user.id = `batches`.addedBy
			JOIN truckingCompanies c ON c.id = a.truckingcompany
			LEFT JOIN booked_under ON booked_under.id=a.bookedUnderNew 
			WHERE $where
			AND a.carrierPayoutDate != '0000-00-00' AND a.carrierPayoutCheck = '1' 
			AND JSON_VALID(a.dispatchMeta)
			AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) IS NOT NULL
			AND JSON_UNQUOTE(JSON_EXTRACT(a.dispatchMeta, '$.custInvDate')) != ''
			ORDER BY batches.date ASC";
		}
		
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

}

?>