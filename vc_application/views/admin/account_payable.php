<style>
	form.form {margin-bottom:25px;}
	.td-input{display:none;}
	.fas {cursor: pointer;}
	.fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	a.btn {margin-bottom: 5px;}

	.select2-container--default .select2-selection--multiple {
    border-radius: 20px !important;
	}
	.select2-container .select2-selection--multiple {
    min-height: 36px !important;
	}
	.form-control {
    	height: 39px !important;
	}
	.invoice-tab {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: inherit;
        text-align: center;
    }

    .invoice-top {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .bell-icon {
        font-size: 20px;
    }
</style>
<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
	<h3 class="m-0">Account Payable</h3>
		<div class="add_page" style="float: right;">
		</div>
		<div class="d-flex align-items-center flex-wrap" style="gap: 15px; float: right;">
			<a class="invoice-tab" href="javascript:void(0);" onclick="submitPayableSearch('zero')" title="0 to 15 Aging days" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 0-15 <span id="0_15_days_count" style="color: #5cb85c;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-success bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>        
				</div>
				<div class="invoice-amount text-success" id=""><span id="0_15_days_amount" style="color: #5cb85c ;">$ 0.00</span></div>
			</a>	
		
			<a class="invoice-tab" href="javascript:void(0);" onclick="submitPayableSearch('thirty')" title="15 to 30 Aging days" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 15-30 <span id="15_30_days_count" style="color: #5bc0de;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-info bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>        
				</div>
				<div class="invoice-amount text-info" id=""><span id="15_30_days_amount" style="color: #5bc0de;">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="javascript:void(0);" onclick="submitPayableSearch('thirtyfive')" title="" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 30-35 <span id="30_35_days_count" style="color: #ff00eaff;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell bell-icon" style="font-size: 25px; margin-top: 0px; color: #ff00eaff;"></i>
					</div>        
				</div>
				<div class="invoice-amount" id=""><span id="30_35_days_amount" style="color: #ff00eaff;">$ 0.00</span></div>
			</a>

    		<a class="invoice-tab" href="javascript:void(0);" onclick="submitPayableSearch('fortyfive')" title="DB PA Fleet Report" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 35-45 <span id="35_45_days_count" style="color: #007bff;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-primary bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>        
				</div>
				<div class="invoice-amount text-primary" id=""><span id="35_45_days_amount" style="color: #007bff;">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="javascript:void(0);" onclick="submitPayableSearch('sixty')" title="DB PA Logistics Report" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging Days 45-60 <span id="45_60_days_count" style="color: #ffc107;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-warning bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>       
				</div>
				<div class="invoice-amount text-warning" id=""><span id="45_60_days_amount" style="color: #ffc107;">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="javascript:void(0);" onclick="submitPayableSearch('sixtyplus')" title="QP PA Fleet Report" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging Days 60+ <span id="60_days_count" style="color: #dc3545;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-danger bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>
				</div>
				<div class="invoice-amount text-danger" id=""><span id="60_days_amount" style="color: #dc3545;">$ 0.00</span></div>
			</a>
		</div>
	</div>
	<div class="card-bodys table_style">
		<div class="d-block text-center">
			<?php
			if($this->input->post('agingSearch')) {
				$agingSearch = $this->input->post('agingSearch'); 
			}
			else {
				$agingSearch =''; 
			}
			if($this->input->post('invoiceType')){
				$invoiceType = $this->input->post('invoiceType');
			}else{
				$invoiceType='';
			}
			if($this->input->post('factoringCompany')){
				$factoringCompany = $this->input->post('factoringCompany');
			}else{
				$factoringCompany='';
			}
			if($this->input->post('invoiceNo')){
				$invoiceNo = $this->input->post('invoiceNo');
			}else{
				$invoiceNo = '';
			}
			if($this->input->post('carrierInvoiceRefNo')){
				$carrierInvoiceRefNo = $this->input->post('carrierInvoiceRefNo');
			}else{
				$carrierInvoiceRefNo = '';
			}
			if($agingSearch == ''){
				if($this->input->post('aging_from')){
					$agingFromVal = $this->input->post('aging_from');
				}else{
					$agingFromVal = '';
				}
				if($this->input->post('aging_to')){
					$agingToVal = $this->input->post('aging_to');
				}else{
					$agingToVal = '';
				}
			}else{
				$agingFromVal = '';
				$agingToVal = '';			
			}
			?>
			<form id="exportForm" action="<?= base_url('AccountPayableController/exportPayables') ?>" method="post">
				<input type="hidden" name="export_invoice_ids" id="export_invoice_ids">
			</form>
			<form id="exportPdfForm" action="<?= base_url('AccountPayableController/exportPdfPayables') ?>" method="post">
				<input type="hidden" name="export_pdf_invoice_ids" id="export_pdf_invoice_ids">
			</form>
			<form class="form form-inline" id="payableSearchForm" method="post" action="">
			<input type="hidden" name="agingSearch" id="agingSearch" value="<?php echo $agingSearch; ?>">
			<input type="hidden" name="search" id="agingSearch" value="Search">

			<button type="button" id="uploadAllbtn" class="btn btn-primary p-cta" style="margin: 3px; display: none;" data-bs-toggle="modal" data-bs-target="#uploadModal">
					Upload Document
			</button>
			<button type="button" id="exportAllbtn" class="btn btn-success p-cta ml-1" style="margin: 3px; display: none;" >
				Export
			</button>
			<button type="button" id="exportAllPdfbtn" class="btn btn-danger p-cta ml-1" style="margin: 3px; display: none;" >
				Export PDF
			</button>
			&nbsp;
			<select class="form-control select2 p-cta ml-1" name="truckingCompany[]" data-placeholder="Select Carrier" multiple="multiple" style="margin: 3px; max-width: 200px;">
						<option value="">Select Carrier</option>
						<?php 
						$selected_truckingCompanies = $this->input->post('truckingCompany');
						$truckingCompanyArr = array();
							if (!empty($truckingCompanies)) {
								foreach ($truckingCompanies as $val) {
									$truckingCompanyArr[$val['id']] = $val['company'];
									echo '<option value="'.$val['id'].'"';
									if(!empty($selected_truckingCompanies) && in_array($val['id'], $selected_truckingCompanies)) { echo ' selected '; }
									echo '>'.$val['company'].'</option>';
								}
							}
						?>
			</select>
			&nbsp;
			<select name="invoiceType"  class="form-control p-cta ml-1" style="margin: 3px; max-width: 250px;max-height: 40px;border: 1px solid #a19a9a;">
					<option value="">Carrier Payment Type</option>
					<option value="Standard Billing" <?php if($invoiceType == 'Standard Billing') { echo 'selected'; } ?>>Standard Billing</option>
					<option value="Quick Pay" <?php if($invoiceType =='Quick Pay') { echo 'selected'; } ?>>Quick Pay</option>
					<option value="Zelle" <?php if($invoiceType =='Zelle') { echo 'selected'; } ?>>Zelle</option>
			</select>
			&nbsp;
			<select class="form-control select2 p-cta ml-1" name="factoringCompany[]" id="factoringCompany" multiple="multiple"  data-placeholder="Select Factoring Company" style="margin: 3px; max-width: 200px;">
						<option value="">Select Factoring Company</option>
						<?php 
						$selectedFactoringCompanies= $this->input->post('factoringCompany');
						$factoringCompaniesArr = array();
							if (!empty($factoringCompanies)) {
								foreach ($factoringCompanies as $val) {
									$factoringCompaniesArr[$val['id']] = $val['id'];
									echo '<option value="'.$val['id'].'"';
									if(!empty($selectedFactoringCompanies) && in_array($val['id'], $selectedFactoringCompanies)) { echo ' selected '; }
									echo '>'.$val['company'].'</option>';
								}
							}
						?>
					    <!-- <?php foreach ($factoringCompanies as $company): ?> -->
							<!-- <option value="<?php echo $company['id']; ?>" -->
								<!-- <?php if ($factoringCompany == $company['id']) echo 'selected'; ?>> -->
								<!-- <?php echo $company['company']; ?> -->
							<!-- </option> -->
						<!-- <?php endforeach; ?> -->
			</select>			
			&nbsp;
			<input class="form-control p-cta ml-1" style="margin: 3px;" name="invoiceNo" value="<?php echo $invoiceNo; ?>" placeholder="Invoice No"/>
			&nbsp;
			<input class="form-control p-cta ml-1" name="carrierInvoiceRefNo" value="<?php echo $carrierInvoiceRefNo; ?>" placeholder="Carrier Invoice Ref No" style="margin: 3px;"/>
			&nbsp;
			<div class="form-inline">
				<input type="number" name="aging_from" class="form-control" placeholder="Aging From" value="<?= $agingFromVal ?>" style="width: 125px;"> &nbsp;
				<input type="number" name="aging_to" class="form-control" placeholder="Aging To" value="<?= $agingToVal ?>" style="width: 120px;">
			</div>
			&nbsp;
			<input type="submit" id="submitBtn" class="btn btn-success p-cta ml-1">
			</form>
		</div>

		<div id="selectedCountBlock" style="display: none; margin: 10px 0; font-weight: bold;">
			Total Invoices: <span id="selectedCount">0</span> |
			Total Amount: $<span id="selectedAmount">0</span>
		</div>


		<div class="table-responsive pt-datatbl" style="overflow-y: auto;max-height: 90vh;">
			<table class="table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead style="position:sticky;top:0">
        			<tr>
						<th>
							<?php
							if (!empty($dispatch)) { ?>
								<input class="mr-2" type="checkbox" id="toggle-company-master" style="transform: scale(1.2); cursor: pointer;" title="Toggle All">
							<?php } ?>
							Sr no.
						</th>
						<th>Carrier</th>
						<th>0-15</th>
						<th>15-30</th>
						<th>30-45</th>
						<th>45-60</th>
						<th>60-75</th>
						<th>75-90</th>
						<th>90+</th>
						<?php ?><th>Action</th> <?php ?>
					</tr> 
				</thead>
				<tbody>
					<?php
						if (!empty($dispatch)) {
							$n = 1;
							foreach ($dispatch as $company) {

								$totalInvoiceAmount = $company['0-15_days_amount'] + $company['16-30_days_amount'] + $company['31-45_days_amount'] + $company['46-60_days_amount'] + $company['61-75_days_amount'] + $company['76-90_days_amount'] + $company['90_days_amount'];
    							
								$totalInvoices = $company['0-15_days_count'] + $company['16-30_days_count'] + $company['31-45_days_count'] + $company['46-60_days_count'] + $company['61-75_days_count'] + $company['76-90_days_count'] + $company['90_days_count'];
    
            		?>
            		<tr style="" class="table-row">
                		<td style="text-align: center; vertical-align: middle;"><?php echo $n; ?></td>
                		<td><?php echo $company['company']; ?><br/> $<?php echo number_format($totalInvoiceAmount,2); ?><?php if($totalInvoices > 0): ?>&nbsp;( <a href="#" class="toggle-details company-toggle-link" data-range="all_days" data-company-id="<?php echo $company['id']; ?>" data-id="<?php echo $company['id']; ?>" style="color: blue;"><?php echo $totalInvoices; ?></a> )<?php else: ?>( 0 )<?php endif; ?></td> 

						<td>$<?php echo number_format($company['0-15_days_amount'],2); ?> <?php if($company['0-15_days_count'] > 0): ?>( <a href="#" class="toggle-details" data-range="0-15_days" data-id="<?php echo $company['id']; ?>" style="color: blue;"><?php echo $company['0-15_days_count']; ?></a> )<?php else: ?>( 0 )<?php endif; ?></td>

						<td>$<?php echo number_format($company['16-30_days_amount'],2); ?> <?php if($company['16-30_days_count'] > 0): ?>( <a href="#" class="toggle-details" data-range="16-30_days" data-id="<?php echo $company['id']; ?>" style="color: blue;"><?php echo $company['16-30_days_count']; ?></a> )<?php else: ?>( 0 )<?php endif; ?></td>

						<td>$<?php echo number_format($company['31-45_days_amount'],2); ?> <?php if($company['31-45_days_count'] > 0): ?>( <a href="#" class="toggle-details" data-range="31-45_days" data-id="<?php echo $company['id']; ?>" style="color: blue;"><?php echo $company['31-45_days_count']; ?></a> )<?php else: ?>( 0 )<?php endif; ?></td>

						<td>$<?php echo number_format($company['46-60_days_amount'],2); ?> <?php if($company['46-60_days_count'] > 0): ?>( <a href="#" class="toggle-details" data-range="46-60_days" data-id="<?php echo $company['id']; ?>" style="color: blue;"><?php echo $company['46-60_days_count']; ?></a> )<?php else: ?>( 0 )<?php endif; ?></td>

						<td>$<?php echo number_format($company['61-75_days_amount'],2); ?>  <?php if($company['61-75_days_count'] > 0): ?>( <a href="#" class="toggle-details" data-range="61-75_days" data-id="<?php echo $company['id']; ?>" style="color: blue;"><?php echo $company['61-75_days_count']; ?></a> ) <?php else: ?>( 0 )<?php endif; ?></td>	
									
						<td>$<?php echo number_format($company['76-90_days_amount'],2); ?><?php if($company['76-90_days_count'] > 0): ?>( <a href="#" class="toggle-details" data-range="76-90_days" data-id="<?php echo $company['id']; ?>" style="color: blue;"><?php echo $company['76-90_days_count']; ?></a> ) <?php else: ?>( 0 )<?php endif; ?></td>

						<td>$<?php echo number_format($company['90_days_amount'],2); ?> <?php if($company['90_days_count'] > 0): ?>( <a href="#" class="toggle-details" data-range="90_days" data-id="<?php echo $company['id']; ?>" style="color: blue;"><?php echo $company['90_days_count']; ?></a> ) <?php else: ?>( 0 )<?php endif; ?></td>

						<td>
							<div class="dropdown" style="margin-left:10px;">
                				<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Statement
                    			</button>
								<div class="dropdown-menu" aria-labelledby="dropdownMenu2" style="padding: 10px;text-align: center;">
									<a class="btn btn-sm btn-danger" href="<?php echo base_url('AccountPayableController/downloadPayableStatementPDF/'.$company['company_id']);?>?dTable=dispatchOutside&sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&invoiceNo=<?=$invoiceNo?>&carrierInvoiceRefNo=<?=$carrierInvoiceRefNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>">Statement All</a> 

									<a class="btn btn-sm btn-success" href="<?php echo base_url('AccountPayableController/downloadPayableStatementPDF/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&invoiceNo=<?=$invoiceNo?>&carrierInvoiceRefNo=<?=$carrierInvoiceRefNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateCSV&dTable=dispatchOutside">Download CSV</a> 

									<a class="btn btn-sm btn-success" href="<?php echo base_url('AccountPayableController/downloadPayableStatementPDF/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&invoiceNo=<?=$invoiceNo?>&carrierInvoiceRefNo=<?=$carrierInvoiceRefNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateXls&dTable=dispatchOutside">Download Excel</a> 
									
                    			</div>
										
                    		</div>
						</td>
            	</tr>
            	<?php foreach ($company['invoices'] as $range => $invoices) { ?>
                	<tr class="invoice-details-<?php echo $company['id']; ?>-<?php echo $range; ?>" style="display:none;">
						<td colspan="1"></td> 
						<td colspan="12"> 
                        	<table class="">
                            	<tr>
									<th>
										<input type="checkbox" class="toggle-all-checkbox" data-range="<?php echo $company['id']; ?>-<?php echo $range; ?>" style="transform: scale(1.5); cursor: pointer;">
									</th>
									<?php if($company['company_id']== 4){?>
									<th>CARRIER</th>
									<th>BOOKED UNDER</th>
									<?php } ?>
									<th>INVOICE</th>
									<th>FACTORING COMPANY</th>
									<th>DELIVERY DATE</th>
									<th>CARRIER INVOICE DATE</th>
									<th>CARRIER INVOICE REF NO </th>
									<th>CARRIER RATE</th>
									<th>AGING DAYS</th>
									<th>CUSTOMER PAYMENT DATE</th>
									<!-- <th>CARRIER DUE DATE</th> -->
									<th>CARRIER PAYOUT DATE</th>
									<th>CARRIER PAYMENT PROOF</th>
									<th>CARRIER INVOICE</th>
									<th>ACTION</th>
								</tr>
                            	<?php 
								$invoice_n = 1;							
								foreach ($invoices as $invoice) { 
									
									$dispatchMeta = json_decode($invoice['dispatchMeta'],true);
									if($invoice['bookedUnderNew']==4){
										$camount= $invoice['rate'] + $invoice['agentRate'];
									}else{
										$camount= $invoice['rate'];
									}
									// echo $amount;exit;
									if($invoice['bookedUnder']==''){
										$bookedUnder=$invoice['bookedUnderOld'];
									}else{
										$bookedUnder=$invoice['bookedUnder'];
									}

									$carrierPaymentType = $invoice['carrierPaymentType'];
									$factoringType = $invoice['factoringType'];
									$factoringCompany = $invoice['factoringCompany'];
									if($factoringCompany == ''){	
										if($factoringType == ''){
											$factoringCompany = $invoice['carrierPaymentType'];
										}else{
											$factoringCompany = $invoice['factoringType'];
										}
									}else{
										$factoringCompany = $invoice['factoringCompany'];
									}

									?>
                                	<tr>
										<td style="text-align: center; vertical-align: middle;">
											<input type="checkbox" class="invoice-checkbox invoice-checkbox-<?php echo $company['id']; ?>-<?php echo $range; ?>" value="<?php echo $invoice['id'] ?>" data-amount="<?= $camount ?>" style="transform: scale(1.5); cursor: pointer;">
										</td>
										<?php if($company['company_id']== 4){?>
											<td style="text-align: center; vertical-align: middle;"><?php echo $invoice['company']; ?></td>
											<td style="text-align: center; vertical-align: middle;"><?php echo $bookedUnder; ?></td>
										<?php } ?>
                                    	<td style="text-align: center; vertical-align: middle;"><a href="<?php echo base_url().'admin/outside-dispatch/update/'.$invoice['id'];?>"><?php echo $invoice['invoice']; ?></td>

										<td style="text-align: center; vertical-align: middle;"><?php echo $factoringCompany; ?></td>

                                    	<td style="text-align: center; vertical-align: middle;"><?php echo date('m-d-Y',strtotime( $invoice['dodate'])); ?></td>
										<td style="text-align: center; vertical-align: middle;">
											<?php
											if (isset($dispatchMeta['custInvDate']) && !empty($dispatchMeta['custInvDate'])) {
												echo date('m-d-Y', strtotime($dispatchMeta['custInvDate']));
											} else {
												echo ''; 
											}
											?>
										</td>
										<td style="text-align: center; vertical-align: middle;"><?php echo $invoice['carrierInvoiceRefNo'];?>
										</td>
<!-- 
                                    	<td style="text-align: center; vertical-align: middle;"><?php echo date('m-d-Y',strtotime( $invoice['invoiceDate'])); ?></td> -->

                                    	<td style="text-align: center; vertical-align: middle;">$<?php echo number_format($camount,2); ?></td>
										<?php if($invoice['carrierPaymentType'] != 'Quick Pay' && $invoice['carrierPaymentType'] != 'Zelle' && $invoice['days_diff']>30){ ?>
											<td style="text-align: center; vertical-align: middle;"><strong style="color:red"><?php echo $invoice['days_diff']; ?> Days</strong></td>
										<?php } else if($invoice['carrierPaymentType'] == 'Quick Pay' && $invoice['days_diff']>3){ ?>
											<td style="text-align: center; vertical-align: middle;"><strong style="color:red"><?php echo $invoice['days_diff']; ?> Days</strong></td>
										<?php }  else if($invoice['carrierPaymentType'] == 'Zelle' && $invoice['days_diff']>1){ ?>
											<td style="text-align: center; vertical-align: middle;"><strong style="color:red"><?php echo $invoice['days_diff']; ?> Days</strong></td>
										<?php }
										else{ ?>
											<td style="text-align: center; vertical-align: middle;"><strong style=""><?php echo $invoice['days_diff']; ?> Days</strong></td> <?php }?>
										
										<td style="text-align: center; vertical-align: middle;">
											<?php
											if (isset($dispatchMeta['invoicePaidDate']) && !empty($dispatchMeta['invoicePaidDate'])) {
												echo date('m-d-Y', strtotime($dispatchMeta['invoicePaidDate']));
											} else {
												echo ''; 
											}
											?>
										</td>
										
										<td class="">
											<input type="date" id="proofdate_<?php echo $invoice['id']; ?>" name="proofdate" class="form-control datepicker" value="<?php echo $invoice['carrierPayoutDate']; ?>">
										
											<td class="d-flex align-items-center gap-2" style="width:222px">
											<input type="file" id="gd_d<?php echo $invoice['id']; ?>" name="gd_d[]" class="form-control" multiple>
											<?php if (!empty($invoice['documents'])) { ?>
												<?php foreach ($invoice['documents'] as $document) { ?>
													<a href="<?php echo base_url('assets/outside-dispatch/gd/' . $document['fileurl']); ?>" 
													download="<?php echo $document['fileurl']; ?>" 
													class="document-link"
													data-invoice-id="<?php echo $invoice['id']; ?>" style="display:none;">
													</a>
												<?php } ?>
												<button class="btn btn-sm btn-info download-all-btn" data-invoice-id="<?php echo $invoice['id']; ?>">Download All</button>
											<?php } else { ?>
												<span>No files</span>
											<?php } ?>
										</td>
										<td style="word-break: break-all;">
											<a target="_blank" href="<?php echo base_url('assets/outside-dispatch/carrierInvoice/') . $invoice['carrierInvoice'] . '?id=' . rand(10,99); ?>">
												<span>
													<?php
														$fileName = $invoice['carrierInvoice'];
														$startChars = 10; 
														$endChars = 10; 
														if (strlen($fileName) > $startChars + $endChars) {
															$previewName = substr($fileName, 0, $startChars) . '...' . substr($fileName, -$endChars);
														} else {
															$previewName = $fileName;
														}
														echo $previewName;
													?>
												</span>
											</a>
										</td>
										<td class="">
											<a href="javascript:void(0);" class="btn btn-sm btn-primary updateInvoiceBtn" data-invoice-id="<?php echo $invoice['id']; ?>">Submit</a>
										</td>
                                	</tr>
                            	<?php
                             	$invoice_n++; 
                        	} ?>
                        </table>
                    </td>
                </tr>
            <?php } ?>
            <?php
                $n++;
            }
        }
        ?>
    </tbody>
		</table>
		</div>
	</div>
	
</div>

</div>

 <!-- Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
			<div id="modalSelectedInfo" style="margin-bottom: 15px; font-weight: bold;">
				Total Invoices: <span id="modalSelectedCount">0</span> |
				Total Amount: $<span id="modalSelectedAmount">0</span>
			</div>

			<form id="uploadForm" method="post" enctype="multipart/form-data">
				<input type="hidden" name="invoice_ids" id="invoice_ids"> 
				<input type="hidden" name="total_Amount" id="total_Amount"> 
				<input type="hidden" id="batchId" name="batchId" class="form-control">

				<div class="mb-3">
					<label for="proofdate" class="form-label">Select Date</label>
					<input type="date" id="proofdate" name="proofdate" class="form-control" value="<?= date('Y-m-d'); ?>" required>
				</div>

				<div class="mb-3">
					<label for="gd_d" class="form-label">Upload File</label>
					<input type="file" id="gd_d" name="gd_d[]" class="form-control" multiple>
				</div>

				<div class="mb-3">
					<label for="batchNo" class="form-label">Batch No</label>
					<input type="text" id="batchNo" name="batchNo" class="form-control" readonly>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Close</button>
					<button type="submit" class="btn btn-primary">Update Invoices</button>
				</div>
			</form>
            </div>
        </div>
    </div>
</div>


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<link href="<?php echo base_url().'assets/css/5.3.0bootstrap.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/5.3.0bootstrap.bundle.min.js'; ?>"></script>



<script>
   $(document).ready(function() {
      $('.select2').select2();
   });
</script>

<script>
	

	$(document).ready(function(){
		$(".toggle-details").on("click", function(e){
			e.preventDefault();
			var companyId = $(this).data("id");
			var range = $(this).data("range");
			$(".invoice-details-" + companyId + "-" + range).toggle();
		});
	});

	document.addEventListener("DOMContentLoaded", function () {
		function toggleUploadButton() {
			var uploadButton = document.getElementById("uploadAllbtn");
			var exportAllbtn = document.getElementById("exportAllbtn");
			var exportAllPdfbtn = document.getElementById("exportAllPdfbtn");
			
			var anyChecked = document.querySelectorAll(".invoice-checkbox:checked").length > 0;
			uploadButton.style.display = anyChecked ? "inline-block" : "none";
			exportAllbtn.style.display = anyChecked ? "inline-block" : "none";
			exportAllPdfbtn.style.display = anyChecked ? "inline-block" : "none";
		}
		
		function updateSelectedInvoiceCount() {
			const selectedCheckboxes = document.querySelectorAll(".invoice-checkbox:checked");
			const count = selectedCheckboxes.length;
			let totalAmount = 0;

			selectedCheckboxes.forEach(cb => {
				let amount = parseFloat(cb.getAttribute("data-amount")) || 0;
				totalAmount += amount;
			});

			const countBlock = document.getElementById("selectedCountBlock");
			const countSpan = document.getElementById("selectedCount");
			const amountSpan = document.getElementById("selectedAmount");

			if (count > 0) {
				countSpan.textContent = count;
				amountSpan.textContent = totalAmount.toLocaleString('en-PK', {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				});
				countBlock.style.display = "block";
			} else {
				countBlock.style.display = "none";
			}
		}

		document.querySelectorAll(".toggle-all-checkbox").forEach(function (masterCheckbox) {
			masterCheckbox.addEventListener("change", function () {
				let range = this.dataset.range;
				let checkboxes = document.querySelectorAll(".invoice-checkbox-" + range);

				checkboxes.forEach(function (checkbox) {
					checkbox.checked = masterCheckbox.checked;
				});

				toggleUploadButton();
				updateSelectedInvoiceCount();
			});
		});

		document.querySelectorAll(".invoice-checkbox").forEach(function (checkbox) {
			checkbox.addEventListener("change", function () {
				toggleUploadButton();
				updateSelectedInvoiceCount(); 
			});
		});

		$('#toggle-company-master').on('change', function () {
			let checked = $(this).is(':checked');

			$('.company-toggle-link').each(function () {
				let companyId = $(this).data('company-id');
				let detailRows = $(`.invoice-details-${companyId}-all_days`);
				if (checked) {
					if (detailRows.is(':hidden')) {
						$(this).trigger('click');
					}

					setTimeout(() => {
						$(`.invoice-checkbox-${companyId}-all_days`).prop('checked', true);
						$(`.toggle-all-checkbox[data-range="${companyId}-all_days"]`).prop('checked', true);
						toggleUploadButton();
						updateSelectedInvoiceCount();
					}, 300);
				} 
				else {
					if (detailRows.is(':visible')) {
						$(this).trigger('click');
					}

					setTimeout(() => {
						$(`.invoice-checkbox-${companyId}-all_days`).prop('checked', false);
						$(`.toggle-all-checkbox[data-range="${companyId}-all_days"]`).prop('checked', false);
						toggleUploadButton();
						updateSelectedInvoiceCount();
					}, 300);
				}
			});
		});

	});

	$(document).on("click", ".updateInvoiceBtn", function() {
		var invoiceId = $(this).data("invoice-id");
		
		var proofDate = $("#proofdate_" + invoiceId).val();
		var fileInput = $("#gd_d" + invoiceId)[0];

		if (!proofDate || fileInput.files.length === 0) {
			alert("Please select a date and at least one file.");
			return;
		}

		var formData = new FormData();
		formData.append("invoice_id", invoiceId);
		formData.append("proofdate", proofDate);	
		for (var i = 0; i < fileInput.files.length; i++) {
			formData.append("gd_d[]", fileInput.files[i]);
		}
		$.ajax({
			url: "<?php echo base_url('AccountPayableController/updateInvoice'); ?>",
			type: "POST",

			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				alert("Invoice updated successfully!");
				location.reload();        },
		error: function(xhr, status, error) {
				alert("Error updating invoice: " + error);
			}
		});
	});

	var selectedInvoices = [];
	document.addEventListener("DOMContentLoaded", function() {
		var uploadButton = document.getElementById("uploadAllbtn");
		var exportAllbtn = document.getElementById("exportAllbtn");
		var exportAllPdfbtn = document.getElementById("exportAllPdfbtn");
		var exportInput = document.getElementById("export_invoice_ids");
		var exportPdfInput = document.getElementById("export_pdf_invoice_ids");
		var checkboxes = document.querySelectorAll(".invoice-checkbox");
		var invoiceInput = document.getElementById("invoice_ids");
		uploadButton.addEventListener("click", function() {
			selectedInvoices = [];
			checkboxes.forEach(function(checkbox) {
				if (checkbox.checked) {
					selectedInvoices.push(checkbox.value);
				}
			});

			invoiceInput.value = JSON.stringify(selectedInvoices); 
			var modal = new bootstrap.Modal(document.getElementById("uploadModal"));
			modal.show();
		});

		exportAllbtn.addEventListener("click", function() {
			selectedInvoices = [];
			checkboxes.forEach(function(checkbox) {
				if (checkbox.checked) {
					selectedInvoices.push(checkbox.value);
				}
			});
			if (selectedInvoices.length === 0) {
				alert("Please select at least one invoice to export.");
				return;
			}
			exportInput.value = JSON.stringify(selectedInvoices);
			document.getElementById("exportForm").submit();
		});
		exportAllPdfbtn.addEventListener("click", function() {
			selectedInvoices = [];
			checkboxes.forEach(function(checkbox) {
				if (checkbox.checked) {
					selectedInvoices.push(checkbox.value);
				}
			});
			if (selectedInvoices.length === 0) {
				alert("Please select at least one invoice to export.");
				return;
			}
			exportPdfInput.value = JSON.stringify(selectedInvoices);
			document.getElementById("exportPdfForm").submit();
		});
	});

</script>

<script>
$(document).ready(function() {
    $("#uploadForm").on("submit", function(e) {
        e.preventDefault(); 
        var formData = new FormData(this);
        formData.append("invoice_ids", JSON.stringify(selectedInvoices));
        $.ajax({
			url: "<?php echo base_url('AccountPayableController/updateBulkInvoices'); ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
				$("#uploadModal").modal("hide"); 
				location.reload();
				alert("All invoices updated successfully!");
            },
            error: function(xhr, status, error) {
            alert("Error updating invoice: " + error);
        }
        });
    });
});

$('#uploadModal').on('hidden.bs.modal', function () {
    $('.modal-backdrop').remove();
});

$('#uploadModal').on('show.bs.modal', function () {
    $.ajax({
        url: "<?php echo base_url('AccountPayableController/nextBatchNo'); ?>",
		type: "POST",
		dataType: 'json',
		success: function (data) {
			if (data.error == '1') {
				alert('Something went wrong');
				return false;
			}
			if (data.error == '0') {
				$('#batchNo').val(data.msg.batchNo);
				$('#batchId').val(data.msg.batchId);			
			}
		},
		error: function () {
			alert('Failed to fetch batch number. Please try again.');
		}
	});
	var selectedInvoices = [];
    var totalAmount = 0;

    document.querySelectorAll(".invoice-checkbox:checked").forEach(function (checkbox) {
        selectedInvoices.push(checkbox.value);
        totalAmount += parseFloat(checkbox.getAttribute("data-amount")) || 0;
    });

	$('#total_Amount').val(totalAmount);			
	
    document.getElementById("invoice_ids").value = JSON.stringify(selectedInvoices);
    $('#modalSelectedCount').text(selectedInvoices.length);
    $('#modalSelectedAmount').text(totalAmount.toLocaleString('en-PK', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			}));
	
});

$(document).off('click', '.download-all-btn').on('click', '.download-all-btn', function (e) {
    e.preventDefault();
    
    var invoiceId = $(this).data('invoice-id');
    var processedFiles = []; 

    $('.document-link[data-invoice-id="' + invoiceId + '"]').each(function () {
        var fileUrl = $(this).attr('href');
        var fileName = $(this).attr('download');

        if (!processedFiles.includes(fileUrl)) {
            processedFiles.push(fileUrl);

            var link = document.createElement('a');
            link.href = fileUrl;
            link.download = fileName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });
});
</script>
<script>
	$( function() {
		
		$(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
        });
		
		$('#dataTable').DataTable({
			"lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]]
		});
	});
	$(document).ready(function() {
		let selectedTruckingCompanies = $('select[name="truckingCompany[]"]').val(); 
		let selectedFactoringCompanies = $('select[name="factoringCompany[]"]').val(); 
		let selectedInvoiceType = $('select[name="invoiceType"]').val(); 
		let invoice = $('input[name="invoiceNo"]').val();
		let carrierInvoice = $('input[name="carrierInvoiceRefNo"]').val();

        $.ajax({
            url: "<?php echo base_url('AccountPayableController/getAgingDaysCounts'); ?>",
            type: "POST",
            dataType: "json",
			data : {truckingCompany: selectedTruckingCompanies,
				factoringCompany: selectedFactoringCompanies,
				invoiceType: selectedInvoiceType,
				invoiceNo:invoice,
				carrierInvoiceRefNo:carrierInvoice
			},
            success: function(response) {
				let count60 = response.DaysCount.sixty_days_count || 0;
				let amount60 = response.DaysCount.sixty_days_amount || 0;
				let count45_60 = response.DaysCount.forty_five_sixty_days_count || 0;
				let amount45_60 = response.DaysCount.forty_five_sixty_days_amount || 0;
				let count35_45 = response.DaysCount.thirty_five_forty_five_days_count || 0;
				let amount35_45 = response.DaysCount.thirty_five_forty_five_days_amount || 0;
				let count30_35 = response.DaysCount.thirty_thirty_five_days_count || 0;
				let amount30_35 = response.DaysCount.thirty_thirty_five_days_amount || 0;
				let count15_30 = response.DaysCount.fifteen_thirty_days_count || 0;
				let amount15_30 = response.DaysCount.fifteen_thirty_days_amount || 0;
				let count0_15 = response.DaysCount.zero_fifteen_days_count || 0;
				let amount0_15 = response.DaysCount.zero_fifteen_days_amount || 0;

				const formatAmount = (val) => (parseFloat(val) || 0).toLocaleString('en-US', {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				});

				$("#0_15_days_count").text("(" + count0_15 + ")");
				$("#0_15_days_amount").text("$" + formatAmount(amount0_15));

				$("#15_30_days_count").text("(" + count15_30 + ")");
				$("#15_30_days_amount").text("$" + formatAmount(amount15_30));

				$("#30_35_days_count").text("(" + count30_35 + ")");
				$("#30_35_days_amount").text("$" + formatAmount(amount30_35));

				$("#35_45_days_count").text("(" + count35_45 + ")");
				$("#35_45_days_amount").text("$" + formatAmount(amount35_45));

				$("#45_60_days_count").text("(" + count45_60 + ")");
				$("#45_60_days_amount").text("$" + formatAmount(amount45_60));

				$("#60_days_count").text("(" + count60 + ")");		
				$("#60_days_amount").text("$" + formatAmount(amount60));	
            },
            error: function(xhr, status, error) {
                console.error("Error fetching invoice counts:", error);
            }
        });
    });
	function submitPayableSearch(agingLabel) {
		document.getElementById('agingSearch').value = agingLabel;
		document.getElementById('payableSearchForm').submit();
	}
	$(document).ready(function() {
		$('#submitBtn').on('click', function() {
			$('#agingSearch').val('');
		});
	});
</script>

