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
    min-height: 46px !important;
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
</style>
<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3>Account Receivable</h3> 
		<div class="add_page" style="float: right;">
		</div>
		<div class="d-flex align-items-center flex-wrap" style="gap: 15px; float: right;">
			<a class="invoice-tab" id="pendingInvoicesLink" href="javascript:void(0);" onclick="submitReceivableSearch('pending')" title="pending invoices" style="text-decoration:none">
				<div class="invoice-top">
					<span>Pending Invoices <span id="pending_count" style="color: #6f42c1;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell bell-icon" style="font-size: 25px; margin-top: 0px; color: #6f42c1;"></i>
					</div>        
				</div>
				<div class="invoice-amount text-success" id=""><span id="pending_amount" style="color: #6f42c1 ;">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="javascript:void(0);" onclick="submitReceivableSearch('zero')" title="0 to 15 Aging days" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 0-15 <span id="0_15_days_count" style="color: #5cb85c;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-success bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>        
				</div>
				<div class="invoice-amount text-success" id=""><span id="0_15_days_amount" style="color: #5cb85c ;">$ 0.00</span></div>
			</a>	
		
			<a class="invoice-tab" href="javascript:void(0);" onclick="submitReceivableSearch('thirty')" title="15 to 30 Aging days" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 15-30 <span id="15_30_days_count" style="color: #5bc0de;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-info bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>        
				</div>
				<div class="invoice-amount text-info" id=""><span id="15_30_days_amount" style="color: #5bc0de;">$ 0.00</span></div>
			</a>	

			<a class="invoice-tab" href="javascript:void(0);" onclick="submitReceivableSearch('thirtyfive')" title="" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 30-35 <span id="30_35_days_count" style="color: #ff00eaff;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell bell-icon" style="font-size: 25px; margin-top: 0px; color: #ff00eaff;"></i>
					</div>        
				</div>
				<div class="invoice-amount" id=""><span id="30_35_days_amount" style="color: #ff00eaff;">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="javascript:void(0);" onclick="submitReceivableSearch('fortyfive')" title="DB PA Fleet Report" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 35-45 <span id="35_45_days_count" style="color: #007bff;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-primary bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>        
				</div>
				<div class="invoice-amount text-primary" id=""><span id="35_45_days_amount" style="color: #007bff;">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="javascript:void(0);" onclick="submitReceivableSearch('sixty')" title="DB PA Logistics Report" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 45-60 <span id="45_60_days_count" style="color: #ffc107;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-warning bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>       
				</div>
				<div class="invoice-amount text-warning" id=""><span id="45_60_days_amount" style="color: #ffc107;">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="javascript:void(0);" onclick="submitReceivableSearch('sixtyplus')" title="QP PA Fleet Report" style="text-decoration:none">
				<div class="invoice-top">
					<span>Aging 60+ <span id="60_days_count" style="color: #dc3545;">(0)</span></span>
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
			if($this->input->post('sdate')) { 
				$sdate = $this->input->post('sdate'); 
			} 
			else {
				$sdate =''; 
			}
			if($this->input->post('edate')) {
				$edate = $this->input->post('edate'); 
			}
			else {
				$edate =''; 
			}
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
			if($this->input->post('status')){
				$customerStatus = $this->input->post('status');
			}else{
				$customerStatus='';
			}
			if($this->input->post('invoiceNo')){
				$invoiceNo = $this->input->post('invoiceNo');
			}else{
				$invoiceNo = '';
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
			<form id="exportForm" action="<?= base_url('AccountReceivableController/exportReceivables') ?>" method="post">
				<input type="hidden" name="export_invoice_ids" id="export_invoice_ids">
				<input type="hidden" name="table" id="export_dispatch_table"> 
			</form>
			<form id="exportPdfForm" action="<?= base_url('AccountReceivableController/exportPdfReceivables') ?>" method="post">
				<input type="hidden" name="export_pdf_invoice_ids" id="export_pdf_invoice_ids">
				<input type="hidden" name="table" id="export_pdf_dispatch_table"> 
			</form>

			<form class="form form-inline" method="post" id="receivableSearchForm" action="">
				<div class="col-sm-12">
					<?php 
						if($this->session->flashdata('item')){ ?>
							<div class="alert alert-success">
								<h4><?php echo $this->session->flashdata('item');?></h4> 
							</div>
							<div class="msg-div">
								<?php 
								// echo $this->session->flashdata('item');
								$this->session->set_flashdata('item',''); 
								?>
							</div>
							<?php 
							$showMsgDiv = 'true';
						} else if($this->session->flashdata('error')){ ?>
							<div class="alert alert-danger">
								<h4><?php echo $this->session->flashdata('error');?></h4> 
							</div>
							<div class="msg-div error"><?php 
							// echo $this->session->flashdata('error'); 
							$this->session->set_flashdata('error',''); ?></div>
							<?php 
							$showMsgDiv = 'true';
						}
					?>
				</div>	
				
			<input type="hidden" name="agingSearch" id="agingSearch" value="<?php echo $agingSearch; ?>">
			<input type="hidden" name="search" id="agingSearch" value="Search">

			<button type="button" id="uploadReceivablebtn" class="btn btn-primary pt-cta" style="display: none;" data-bs-toggle="modal" data-bs-target="#uploadReceivableModal">
				Upload Document
			</button>
			<button type="button" id="exportAllbtn" class="btn btn-success pt-cta ml-1" style="display: none;" >
				Export
			</button>
			<button type="button" id="exportAllPdfbtn" class="btn btn-danger pt-cta ml-1" style="display: none;" >
				Export PDF
			</button>
			&nbsp;	
			<input type="text" required readonly placeholder="Start Date" value="<?php echo $sdate; ?>" name="sdate" style="width: 120px;" class="form-control datepicker"> &nbsp;
				<input type="text" required style="width: 120px;" readonly placeholder="End Date" value="<?php echo $edate; ?>" name="edate" class="form-control datepicker"> &nbsp;

				<select name="dispatchType" id="dispatchType" required class="form-control" style="max-width: 150px;">
					<option value="">Select Division</option>
					<option value="paDispatch" <?php if($this->input->post('dispatchType') == 'paDispatch') { echo 'selected'; } ?>>PA Fleet Dispatch</option>
					<option value="outsideDispatch" <?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo 'selected'; } ?>>PA Logistics</option>
					<option value="warehouse_dispatch" <?php if($this->input->post('dispatchType') == 'warehouse_dispatch') { echo 'selected'; } ?>>PA Warehousing</option>
				</select> 
				&nbsp;
				<select name="invoiceType"  class="form-control" style="max-width: 150px;max-height: 40px;border: 1px solid #a19a9a;">
					<option value="">Invoice Type</option>
					<option value="Direct Bill" <?php if($invoiceType == 'Direct Bill') { echo 'selected'; } ?>>Direct Bill</option>
					<option value="Quick Pay" <?php if($invoiceType =='Quick Pay') { echo 'selected'; } ?>>Quick Pay</option>
				</select>					
				 &nbsp;
				 <select name="status"  class="form-control" style="max-width: 161px;max-height: 40px;border: 1px solid #a19a9a;">
					<option value="Active" <?php if($customerStatus == 'Active') { echo 'selected'; } ?>>Active</option>
					<option value="In-Active" <?php if($customerStatus =='In-Active') { echo 'selected'; } ?>>In-Active</option>
					<option value="Write-Off" <?php if($customerStatus =='Write-Off') { echo 'selected'; } ?>>Write-Off</option>
				</select>					
				 &nbsp;
				<select name="company[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Customer" style="max-width: 250px;">
					<option value="">All Company</option>
					<?php 
						$selected_companies = $this->input->post('company');
						$companyArr = array();
						if(!empty($companies)){
							foreach($companies as $val){
								$companyArr[$val['id']] = $val['company'];
								echo '<option value="'.$val['id'].'"';
								if(!empty($selected_companies) && in_array($val['id'], $selected_companies)) { echo ' selected '; }
								echo '>'.$val['company'].'</option>';
							}
						}
						
					?>
					</select>
					 &nbsp; 
					 <input class="form-control p-cta ml-1" style="margin: 3px;" name="invoiceNo" value="<?php echo $invoiceNo; ?>" placeholder="Invoice No"/>
					&nbsp;
					<div class="form-inline">
						<input type="number" id="aging_from" name="aging_from" class="form-control" placeholder="Aging From"
							value="<?= $agingFromVal ?>" style="width: 125px;"> &nbsp;
						<input type="number" id="aging_to" name="aging_to" class="form-control" placeholder="Aging To"
							value="<?= $agingToVal ?>" style="width: 120px;">
					</div>
					&nbsp;
				<input type="submit" id="submitBtn" class="btn btn-success pt-cta">
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
						<th>Company</th>
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
									<?php if($this->input->post('dispatchType') == 'paDispatch') {  ?>
										<div class="dropdown" style="margin-left:10px;">
											<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-company-id="<?php echo $company['company_id']; ?>">
												Action
											</button>
											<div class="dropdown-menu" aria-labelledby="dropdownMenu2" style="padding: 10px;text-align: center;">
											<div class="d-flex align-items-center mb-2">
												<select id="" name="shippingContact" class="form-control form-control-sm shipping_contact" style="width:auto;min-height: 47px;">
													<option value="">-- Select Shipping Contact --</option>
												</select>

												<a class="btn btn-sm btn-danger statement-all-btn m-1"
												data-company-id="<?php echo $company['id']; ?>"
												href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?dTable=dispatch&sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>">
													Statement All
												</a>
												
												<a class="btn btn-sm btn-success statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateCSV&dTable=dispatch">Download CSV</a> 
											
												<a class="btn btn-sm btn-success statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateXls&dTable=dispatch">Download Excel</a> 

												<a class="btn btn-sm btn-info get-all-invoices" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadCustomerInvoices/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateXls&dTable=dispatch">Get Customer Invoices</a>

												<a class="btn btn-sm btn-warning add-notes m-1" data-row-id="<?php echo $company['id']; ?>" data-company-id="<?php echo $company['company_id']; ?>" data-cemail="<?php echo $company['email']; ?>" data-dispatch-type="<?php if($this->input->post('dispatchType') == 'paDispatch'){ echo 'dispatch'; } ?>"
												 href="">Reminder Email</a>
											</div>
											</div>
										</div>
									<?php  } 
									if($this->input->post('dispatchType') == 'outsideDispatch') {  ?>
									<div class="dropdown" style="margin-left:10px;">
                    					<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-company-id="<?php echo $company['company_id']; ?>">
                                            Action
                    					</button>
										<div class="dropdown-menu" aria-labelledby="dropdownMenu2" style="padding: 10px;text-align: center;">
											<div class="d-flex align-items-center mb-2">
												<select id="" name="shippingContact" class="form-control form-control-sm shipping_contact" style="width:auto;min-height: 47px;">
														<option value="">-- Select Shipping Contact --</option>
													</select>
									
												<a class="btn btn-sm btn-danger statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?dTable=dispatchOutside&sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>">Statement All</a> 

												<a class="btn btn-sm btn-danger statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?dTable=dispatchOutside&sdate=<?=$sdate?>&edate=<?=$edate?>&type=Trucking&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>">Statement (Trucking)</a> 

												<a class="btn btn-sm btn-danger statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?dTable=dispatchOutside&sdate=<?=$sdate?>&edate=<?=$edate?>&type=Drayage&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>">Statement (Drayage)</a>

												<a class="btn btn-sm btn-success statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateCSV<?php if($this->input->post('dispatchType') == 'outsideDispatch'){ echo '&dTable=dispatchOutside'; } ?>">Download CSV</a> 

												<a class="btn btn-sm btn-success statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateXls<?php if($this->input->post('dispatchType') == 'outsideDispatch'){ echo '&dTable=dispatchOutside'; } ?>">Download Excel</a> 

												<a class="btn btn-sm btn-info get-all-invoices m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadCustomerInvoices/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateXls<?php if($this->input->post('dispatchType') == 'outsideDispatch'){ echo '&dTable=dispatchOutside'; } ?>">Get Customer Invoices</a>

												<a class="btn btn-sm btn-warning add-notes m-1" data-row-id="<?php echo $company['id']; ?>" data-company-id="<?php echo $company['company_id']; ?>" data-cemail="<?php echo $company['email']; ?>"  data-dispatch-type="<?php if($this->input->post('dispatchType') == 'outsideDispatch'){ echo 'dispatchOutside'; } ?>"
												 href="">Reminder Email</a>
											</div> 
                    					</div>
										
                    				</div>
									<?php  } ?>		
									<?php   
									if($this->input->post('dispatchType') == 'warehouse_dispatch') {  ?>
									<div class="dropdown" style="margin-left:10px;">
                    					<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-company-id="<?php echo $company['company_id']; ?>">
                                            Action
                    					</button>
										<div class="dropdown-menu" aria-labelledby="dropdownMenu2" style="padding: 10px;text-align: center;">
											<div class="d-flex align-items-center mb-2">
												<select id="" name="shippingContact" class="form-control form-control-sm shipping_contact" style="width:auto;min-height: 47px;">
														<option value="">-- Select Shipping Contact --</option>
													</select>
									
												<a class="btn btn-sm btn-danger statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?dTable=warehouse_dispatch&sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>">Statement All</a> 

												<a class="btn btn-sm btn-success statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateCSV&dTable=warehouse_dispatch">Download CSV</a> 

												<a class="btn btn-sm btn-success statement-all-btn m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadStatementPDF/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateXls&dTable=warehouse_dispatch">Download Excel</a> 

												<a class="btn btn-sm btn-info get-all-invoices m-1" data-company-id="<?php echo $company['id']; ?>" href="<?php echo base_url('AccountReceivableController/downloadCustomerInvoices/'.$company['company_id']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&agingSearch=<?=$agingSearch?>&invoiceType=<?=$invoiceType?>&status=<?=$customerStatus?>&invoiceNo=<?=$invoiceNo?>&agingFrom=<?=$agingFromVal?>&agingTo=<?=$agingToVal?>&generateXls&dTable=warehouse_dispatch">Get Customer Invoices</a>

												<a class="btn btn-sm btn-warning add-notes m-1" data-row-id="<?php echo $company['id']; ?>" data-company-id="<?php echo $company['company_id']; ?>" data-cemail="<?php echo $company['email']; ?>"  data-dispatch-type="warehouse_dispatch"
												 href="">Reminder Email</a>
											</div> 
                    					</div>
										
                    				</div>
									<?php  } ?>				
								</td> 			
					</td>
            	</tr>
            	<?php foreach ($company['invoices'] as $range => $invoices) {
					 ?>

                	<tr class="invoice-details-<?php echo $company['id']; ?>-<?php echo $range; ?>" style="display:none;">
						<td colspan="1"></td> 
						<td colspan="12"> 
                        	<table class="">
                            	<tr>
									<th>
										<input type="checkbox" class="toggle-all-checkbox" data-range="<?php echo $company['id']; ?>-<?php echo $range; ?>" style="transform: scale(1.5); cursor: pointer;">
									</th> 
									<th>INVOICE</th>
									<?php if($this->input->post('dispatchType') == 'warehouse_dispatch') { ?>
										<th>END DATE</th>
									<?php }else{ ?>
										<th>DELIVERY DATE</th>
									<?php } ?>
									<th>INVOICE DATE</th>
									<?php if($this->input->post('dispatchType') == 'outsideDispatch') { ?>
										<th>CARRIER RATE</th>
									<?php }elseif($this->input->post('dispatchType') == 'warehouse_dispatch'){ ?>
										<th>S.P RATE</th>
									<?php } ?>
									<th>INVOICE AMT</th>
									<?php if($this->input->post('dispatchType') == 'outsideDispatch') { ?>
										<th>Carrier Paid Date</th>
									<?php }elseif($this->input->post('dispatchType') == 'warehouse_dispatch'){ ?>
										<th>S.P Paid Date</th>
									<?php } ?>
									<th>AGING DAYS</th>
									<th>INVOICE PAID DATE</th>
									<th>CUSTOMER PAYMENT PROOF</th>
									<th>ACTION</th>
									<th>NOTES</th>
								</tr>
                            	<?php 
								$invoice_n = 1;							
								foreach ($invoices as $invoice) { 
									if($this->input->post('dispatchType') == 'warehouse_dispatch') {
										$partialAmt = $invoice['partialAmount'];
									}else{
										$dispatchMeta = json_decode($invoice['dispatchMeta'],true);
										if(is_numeric($dispatchMeta['partialAmount'])) {
											$partialAmt = $dispatchMeta['partialAmount'];
										}
									}
									
								if($this->input->post('dispatchType') == 'outsideDispatch') { 
									$dispatchURL = base_url('admin/outside-dispatch/update/').$invoice['id'];
									$table = 'dispatchOutside';
									$filePath='assets/outside-dispatch/gd/';
								}elseif($this->input->post('dispatchType') == 'warehouse_dispatch'){
									$dispatchURL = base_url('admin/paWarehouse/update/').$invoice['id'];
									$table = 'warehouse_dispatch';
									$filePath='assets/warehouse/gd/';
								}else {	
									$dispatchURL = base_url('admin/dispatch/update/').$invoice['id'];
									$table = 'dispatch';
									$filePath='assets/upload/';
								}
								?>
                                	<tr>
										<td style="text-align: center; vertical-align: middle;">
											<input type="checkbox" class="invoice-checkbox invoice-checkbox-<?php echo $company['id']; ?>-<?php echo $range; ?>" value="<?php echo $invoice['id'] ?>" data-amount="<?= $invoice['parate']?>"  style="transform: scale(1.5); cursor: pointer;">
										</td>
										
                                    	<td style="text-align: center; vertical-align: middle;"><a target="_blank" href="<?=$dispatchURL?>"><?php echo $invoice['invoice']; ?></td>

									<?php if($this->input->post('dispatchType') == 'warehouse_dispatch') { ?>
										<td style="text-align: center; vertical-align: middle;"><?php echo date('m-d-Y',strtotime( $invoice['edate'])); ?></td>
									<?php }else{ ?>
										<td style="text-align: center; vertical-align: middle;"><?php echo date('m-d-Y',strtotime( $invoice['dodate'])); ?></td>
									<?php } ?>
                                    	<td style="text-align: center; vertical-align: middle;"><?php echo date('m-d-Y',strtotime( $invoice['invoiceDate'])); ?></td>
										<td style="text-align: center; vertical-align: middle;">$<?php echo number_format($invoice['rate'],2); ?></td>
                                    	<td style="text-align: center; vertical-align: middle;">$<?php echo number_format($invoice['parate'],2); ?></td>
										<?php if(($this->input->post('dispatchType') == 'outsideDispatch') || ($this->input->post('dispatchType') == 'warehouse_dispatch')) { 
											if($invoice['carrierPayoutDate'] =='' || $invoice['carrierPayoutDate'] == '0000-00-00'){ ?>
												<td></td>
											<?php } else{ ?>
												<td style="text-align: center; vertical-align: middle;"><?php echo date('m-d-Y',strtotime( $invoice['carrierPayoutDate'])); ?></td>
											<?php } ?>
											
										<?php } ?>
										<?php if($invoice['invoiceType'] == 'Direct Bill' && $invoice['days_diff']>30){ ?>
											<td style="text-align: center; vertical-align: middle;"><strong style="color:red"><?php echo $invoice['days_diff']; ?> Days</strong></td>
										<?php } else if($invoice['invoiceType'] == 'Quick Pay' && $invoice['days_diff']>3){ ?>
											<td style="text-align: center; vertical-align: middle;"><strong style="color:red"><?php echo $invoice['days_diff']; ?> Days</strong></td>
										<?php }  
										else{ ?>
											<td style="text-align: center; vertical-align: middle;"><strong style=""><?php echo $invoice['days_diff']; ?> Days</strong></td> <?php }?>
										
										 <td class="">
											<input type="hidden" id="table_<?php echo $invoice['id']; ?>" name="table" class="" value="<?php echo $table; ?>">

											<?php if($this->input->post('dispatchType') == 'warehouse_dispatch') { ?>
												<input type="date" id="invoicePaidDate_<?php echo $invoice['id']; ?>" name="invoicePaidDate" class="form-control datepicker" value="<?php echo $invoice['invoicePaidDate']; ?>">
											 <?php } else { ?>
												<input type="date" id="invoicePaidDate_<?php echo $invoice['id']; ?>" name="invoicePaidDate" class="form-control datepicker" value="<?php echo $dispatchMeta['invoicePaidDate']; ?>">
											<?php } ?>
										<td class="d-flex align-items-center gap-2" style="width:222px">
											<input type="file" id="gd_d<?php echo $invoice['id']; ?>" name="gd_d[]" class="form-control" multiple>
											
											<?php if (!empty($invoice['documents'])) { ?>
												<?php foreach ($invoice['documents'] as $document) { ?>
													<a href="<?php echo base_url($filePath . $invoice['fileurl']); ?>" 
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
										 <td class="">
											<a href="javascript:void(0);" class="btn btn-sm btn-primary updateInvoiceBtn" data-invoice-id="<?php echo $invoice['id']; ?>">Submit</a>
										</td>
										<td>
											<?php if (!empty($invoice['notes'])): ?>
												<ul id="notesList_<?php echo $invoice['id']; ?>" style="padding-left: 15px; margin:0;">
													<?php foreach ($invoice['notes'] as $i => $history): ?>
														<li class="note-item <?php echo $i > 0 ? 'd-none' : ''; ?>">
															<small><em>
																(<?php echo date('m-d-Y h:i A', strtotime($history['date'])); ?>)
															</em></small> 
															<strong><?php echo htmlspecialchars($history['subject']); ?></strong>
															<small><em>
																(<?php echo 'Reminded By : '. $history['uname']; ?>)
															</em></small> 
														</li>
													<?php endforeach; ?>
												</ul>

												<?php if (count($invoice['notes']) > 1): ?>
													<button type="button" style="text-decoration: none;"
															class="btn btn-link p-0 see-more-btn" 
															data-target="notesList_<?php echo $invoice['id']; ?>">
														Show more
													</button>
													
												<?php endif; ?>
												<button type="button" class="btn btn-sm btn-info go-reminders-btn" style="float:right;" data-dispatch-url="<?=$dispatchURL?>">
													Reminders History
												</button>
											<?php else: ?>
												<span>No notes</span>
											<?php endif; ?>
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
<div class="modal fade" id="uploadReceivableModal" tabindex="-1" aria-labelledby="uploadReceivableModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadReceivableModalLabel">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
			<div id="modalSelectedInfo" style="margin-bottom: 15px; font-weight: bold;">
				Total Invoices: <span id="modalSelectedCount">0</span> |
				Total Amount: $<span id="modalSelectedAmount">0</span>
			</div>
			<form id="uploadReceivableForm" method="post" enctype="multipart/form-data">
				<input type="hidden" name="invoice_ids" id="invoice_ids"> 
				<input type="hidden" name="table" id="dispatchTable"> 
				<input type="hidden" name="total_Amount" id="total_Amount"> 
				<input type="hidden" id="batchId" name="batchId" class="form-control">

				<div class="mb-3">
					<label for="invoicePaidDate" class="form-label">Select Date</label>
					<input type="date" id="invoicePaidDate" name="invoicePaidDate" class="form-control" value="<?= date('Y-m-d'); ?>" required>
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

<div id="statusModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Status</h4>
			</div>
			<div class="modal-body">
				<form class="form" method="post" action="" id="editstatusform">
					<div class="alert alert-success status-success-msg" style="display:none">Please wait updating....</div>
					<p><input type="text" name="statusonly" id="status-input" class="form-control" value="" required>
					<input type="hidden" name="statusid" id="dstatus-input" value="" required></p>
					<p><input type="submit" name="sstatus" id="sstatus-input" class="btn btn-primary" value="Update"></p>
				</form>
			</div> 
		</div>
		
	</div>
</div>

<div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 900px;" role="document">
    <div class="modal-content">
      <form id="addNoteForm" method="POST" action="<?= base_url('AccountReceivableController/addNotes') ?>">
        <div class="modal-header">
          <h5 class="modal-title">Send Note</h5>
        </div>

        <div class="modal-body">
			<div class="form-group">
				<label for="email_subject"><strong>Email Header</strong></label>
				<textarea class="form-control" rows="1" name="email_subject" id="email_subject" required></textarea>
			</div>
          	<div class="form-group">
            	<label for="note">Note</label>
            	<textarea id="note" name="note" class="form-control" required></textarea>
          	</div>
			<div class="form-group">
				<label for="email" class="form-label">Email</label>
				<input type="text" id="email" name="email" class="form-control" readonly>
			</div>
			<div class="form-group cc-email-list">
			</div>
			<input type="hidden" name="company_id" id="company_id">
			<input type="hidden" name="shipping_contact" id="shipping_contact">
          	<input type="hidden" name="invoice_ids" id="modalInvoiceIds">
         	 <input type="hidden" name="dispatch_type" id="modalDispatchType">
			 <input type="hidden" name="agingFrom" id="agingFrom">
         	 <input type="hidden" name="agingTo" id="agingTo">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" id="sendEmailBtn">Send</button>
			<button type="button" id="previewPdfBtn" class="btn btn-primary">Preview Statement</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="emailLoaderModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="max-width: 400px;">
    <div class="modal-content">
      <div class="modal-body text-center">
        <p>Sending email... Please wait.</p>
        <img src="<?php echo base_url('assets/images/Spin-loader.gif'); ?>" alt="Loading..." style="width: 50px;">
        <br><br>
        <button id="cancelEmailBtn" class="btn btn-danger">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery first -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- jQuery UI -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<link href="<?php echo base_url().'assets/css/5.3.0bootstrap.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/5.3.0bootstrap.bundle.min.js'; ?>"></script>

<link href="<?php echo base_url('assets/sweet_alert/sweetalert2.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/sweet_alert/sweetalert2@11.js'); ?>"></script>

<script src="<?php echo base_url('assets/ckeditor/ckeditor.js'); ?>"></script>
<script>
	$(document).ready(function() {
		$( ".datepicker" ).datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			autoclose: true
		});
		$(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
});

	$( function() {
		
	
		$('#dataTable').DataTable({
			"lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]]
		});		
	});
</script>

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
        var uploadButton = document.getElementById("uploadReceivablebtn");
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
			// amountSpan.textContent = totalAmount.toLocaleString('en-PK');
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

$(document).on("click", "#uploadReceivablebtn", function() {
	var dispatchType = document.getElementById("dispatchType");
    var selectedDispatch = dispatchType.value;
	if(selectedDispatch=='outsideDispatch'){
		var dispatchTable = 'dispatchOutside';
	}else if(selectedDispatch=='warehouse_dispatch'){
		var dispatchTable = 'warehouse_dispatch';
	}
	else{
		var dispatchTable = 'dispatch';

	}
	$('#dispatchTable').val(dispatchTable);
});



$(document).on("click", ".updateInvoiceBtn", function() {
    var invoiceId = $(this).data("invoice-id");
    var invoicePaidDate = $("#invoicePaidDate_" + invoiceId).val();
    var table = $("#table_" + invoiceId).val();
    var fileInput = $("#gd_d" + invoiceId)[0];

    if (!invoicePaidDate || fileInput.files.length === 0) {
        alert("Please select a date and at least one file.");
        return;
    }

    var formData = new FormData();
    formData.append("invoice_id", invoiceId);
    formData.append("invoicePaidDate", invoicePaidDate);
    formData.append("table", table);

    // Append all selected files
    for (var i = 0; i < fileInput.files.length; i++) {
        formData.append("gd_d[]", fileInput.files[i]);
    }

    $.ajax({
        url: "<?php echo base_url('AccountReceivableController/updateInvoice'); ?>",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert("Invoice updated successfully!");
            location.reload();
        },
        error: function(xhr, status, error) {
            alert("Error updating invoice: " + error);
        }
    });
});

var selectedInvoices = [];
document.addEventListener("DOMContentLoaded", function() {
    var uploadButton = document.getElementById("uploadReceivablebtn");
	var exportAllbtn = document.getElementById("exportAllbtn");
	var exportAllPdfbtn = document.getElementById("exportAllPdfbtn");
	var exportInput = document.getElementById("export_invoice_ids");
	var exportPdfInput = document.getElementById("export_pdf_invoice_ids");	
	var exportDispatchTable = document.getElementById("export_dispatch_table");
	var exportPdfDispatchTable = document.getElementById("export_pdf_dispatch_table");
    var checkboxes = document.querySelectorAll(".invoice-checkbox");
    var invoiceInput = document.getElementById("invoice_ids");

	// checkboxes.forEach(function(checkbox) {
    //     checkbox.addEventListener("change", function() {
    //         var anyChecked = Array.from(checkboxes).some(cb => cb.checked);
    //         uploadButton.style.display = anyChecked ? "inline-block" : "none";
    //     });
    // });

    uploadButton.addEventListener("click", function() {
        selectedInvoices = [];
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                selectedInvoices.push(checkbox.value);
            }
        });

        invoiceInput.value = JSON.stringify(selectedInvoices); 
        var modal = new bootstrap.Modal(document.getElementById("uploadReceivableModal"));
        modal.show();
    });

	exportAllbtn.addEventListener("click", function() {
		var dispatchType = document.getElementById("dispatchType");
		var selectedDispatch = dispatchType.value;
		
		if(selectedDispatch=='outsideDispatch'){
			var dispatchTable = 'dispatchOutside';
		}else if(selectedDispatch=='warehouse_dispatch'){
			var dispatchTable = 'warehouse_dispatch';
		}
		else{
			var dispatchTable = 'dispatch';

		}
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
		exportDispatchTable.value = JSON.stringify(dispatchTable);
        document.getElementById("exportForm").submit();
    });
	exportAllPdfbtn.addEventListener("click", function() {
		var dispatchType = document.getElementById("dispatchType");
		var selectedDispatch = dispatchType.value;
		if(selectedDispatch=='outsideDispatch'){
			var dispatchTable = 'dispatchOutside';
		}else if(selectedDispatch=='warehouse_dispatch'){
			var dispatchTable = 'warehouse_dispatch';
		}else{
			var dispatchTable = 'dispatch';

		}
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
		exportPdfDispatchTable.value = JSON.stringify(dispatchTable);
        document.getElementById("exportPdfForm").submit();
    });
	
});
</script>
<script>
$(document).ready(function() {
    $("#uploadReceivableForm").on("submit", function(e) {
        e.preventDefault(); 
        var formData = new FormData(this);
        formData.append("invoice_ids", JSON.stringify(selectedInvoices));

        $.ajax({
			url: "<?php echo base_url('AccountReceivableController/updateBulkInvoices'); ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
				$("#uploadReceivableModal").modal("hide"); 
				location.reload();
				alert("All invoices updated successfully!");
            },
            error: function(xhr, status, error) {
				console.error("Status:", status);
				console.error("Error:", error);
				console.error("Response:", xhr.responseText);
				alert("Error updating invoice. Check console for details.");
			}

        });
    });
});

$('#uploadReceivableModal').on('hidden.bs.modal', function () {
    $('.modal-backdrop').remove();
});
$('#uploadReceivableModal').on('show.bs.modal', function () {
    $.ajax({
        url: "<?php echo base_url('AccountReceivableController/nextBatchNo'); ?>",
        type: "POST",
        dataType: 'json',
        success: function(data) {
            if (data.error == '1') {  
                alert('Something went wrong');    
                return false;
            }
            if (data.error == '0') {  
                $('#batchNo').val(data.msg.batchNo);
				$('#batchId').val(data.msg.batchId);

            }
        },
        error: function() {
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
	
    // 3. Update hidden input and modal display
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
$(document).ready(function() {
	let dispatchType = $('select[name="dispatchType"]').val();
	let selectedInvoiceType = $('select[name="invoiceType"]').val(); 
	let selectedCustomerStatus = $('select[name="status"]').val(); 
	let company = $('select[name="company[]"]').val(); 
	let sdate = $('input[name="sdate"]').val();
	let edate = $('input[name="edate"]').val();
	let invoice = $('input[name="invoiceNo"]').val();

        $.ajax({
            url: "<?php echo base_url('AccountReceivableController/getAgingDaysCounts'); ?>",
            type: "POST",
            dataType: "json",
			data : {dispatchType: dispatchType,
				invoiceType: selectedInvoiceType,
				status: selectedCustomerStatus,
				company: company,
				invoiceNo:invoice,
				sdate: sdate,
				edate: edate},
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
				let pending_count = response.DaysCount.pending_count || 0;
				let pending_amount = response.DaysCount.pending_amount || 0;

				const formatAmount = (val) => (parseFloat(val) || 0).toLocaleString('en-US', {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				});

				$("#pending_count").text("(" + pending_count + ")");
				$("#pending_amount").text("$" + formatAmount(pending_amount));
				
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

	function togglePendingLink() {
        const dispatchType = document.getElementById("dispatchType").value;
        const link = document.getElementById("pendingInvoicesLink");

        if (dispatchType === "paDispatch") {
            link.style.display = "none";   // hide
        } else {
            link.style.display = "block";  // show
        }
    }

    // Run on page load
    togglePendingLink();

    // Run when dropdown changes
    document.getElementById("dispatchType").addEventListener("change", togglePendingLink);
	
	function submitReceivableSearch(agingLabel) {
		document.getElementById('agingSearch').value = agingLabel;
		document.getElementById('receivableSearchForm').submit();
	}
	$(document).ready(function() {
		$('#submitBtn').on('click', function() {
			$('#agingSearch').val('');
		});
	});

	$(document).on('click', '.statement-all-btn', function (e) {
		e.preventDefault();
		var companyId = $(this).data('company-id');
		var selectedIds = [];
		var baseUrl = $(this).attr('href');
		var newUrl = baseUrl;
	
		$(`.invoice-checkbox-${companyId}-all_days:checked`).each(function () {
			selectedIds.push($(this).val());
		});
		var shippingContact = $(this).closest('.dropdown-menu').find('.shipping_contact').val();
		
		if (selectedIds.length > 0) {
			if (selectedIds.length > 0) {
				newUrl += '&invoice_ids=' + encodeURIComponent(selectedIds.join(','));
			}
		}
		if (shippingContact) {
			newUrl += '&shippingContact=' + encodeURIComponent(shippingContact);
		}
		window.location.href = newUrl;
	});


	$(document).on('click', '.get-all-invoices', function (e) {
		e.preventDefault();
		var companyId = $(this).data('company-id');
		var selectedIds = $(`.invoice-checkbox-${companyId}-all_days:checked`)
			.map(function () { return $(this).val(); })
			.get();
		var baseUrl = $(this).attr('href');
		$.get(baseUrl.replace('downloadCustomerInvoices', 'getInvoiceFileUrls'), {
			invoice_ids: selectedIds.join(',')
		}, function (files) {
			files = JSON.parse(files);
			files.forEach(function (file) {
				var a = document.createElement('a');
				a.href = file.url;
				a.download = file.name; 
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
			});
		});
	});

	$(document).on('click', '.add-notes', function (e) {
		e.preventDefault();
		var rowId = $(this).data('row-id');
		var companyId = $(this).data('company-id');
		var cEmail = $(this).data('cemail');
		if (!cEmail || cEmail.trim() === "") {
			Swal.fire({
				title: "Missing Email",
				text: "Please add an invoicing email for the company.",
				icon: "warning",
				confirmButtonText: "OK"
			});
			return;
		}
		var shippingContact = $(this).closest('.dropdown-menu').find('.shipping_contact').val();
		var dispatchType = $(this).data('dispatch-type');
		var selectedIds = [];
		var baseUrl = $(this).attr('href');
		var newUrl = baseUrl;
	
		$(`.invoice-checkbox-${rowId}-all_days:checked`).each(function () {
			selectedIds.push($(this).val());
		});
		
		if (selectedIds.length === 0) {
			Swal.fire({
				title: "No Invoice Selected",
				text: "Please select at least one invoice.",
				icon: "warning",
				confirmButtonText: "OK"
			});
			return;
		}	
		$('#company_id').val(companyId);
		$('#email').val(cEmail);
		$('#shipping_contact').val(shippingContact);
		$('#modalInvoiceIds').val(selectedIds.join(','));
		$('#modalDispatchType').val(dispatchType);
		const agingFrom = document.getElementById('aging_from').value;
		const agingTo = document.getElementById('aging_to').value;
		$('#agingFrom').val(agingFrom);
		$('#agingTo').val(agingTo);
		setTimeout(function () {
			if (CKEDITOR.instances.note) {
				CKEDITOR.instances.note.resize('100%', '120', true);
			} else {
				CKEDITOR.replace('note', {
					height: 120,
					extraPlugins: 'lineheight',
					line_height: "1;1.2;1.5;1.75;2;2.5;3"
					
				});
				CKEDITOR.instances.note.on('instanceReady', function () {
					CKEDITOR.instances.note.resize('100%', '120', true);
				});
			}
		}, 300);
		$.ajax({
			url: `<?php echo base_url('AccountReceivableController/getCCEmails/'); ?>${companyId}`,
			method: 'POST',
			data: { invoice_ids: selectedIds,
					dispatchType: dispatchType },
			success: function(response) {
				const data = typeof response === "string" ? JSON.parse(response) : response;
				let ccEmailsList = '';
				const baseUrl = '<?php echo base_url(); ?>';
				let fileList = '';
				function constructUrl(fileName) {
					if (dispatchType === 'dispatch') {
						return `${baseUrl}assets/paInvoice/${fileName}`;
					} else if(dispatchType === 'dispatchOutside') {
						return `${baseUrl}assets/outside-dispatch/invoice/${fileName}`;
					} else if(dispatchType === 'warehouse_dispatch') {
						return `${baseUrl}assets/warehouse/invoice/${fileName}`;
					}
				}

				if (data.cEmails && data.cEmails.length > 0) {
					ccEmailsList += `<h4 class="section-heading">Add CC Email</h4><hr>`;
					const emailArray = data.cEmails.split(',');
					emailArray.forEach(function(email) {
						const trimmedEmail = email.trim();
						if (trimmedEmail !== '') {
							ccEmailsList += `
								<div class="file-entry d-flex align-items-center mb-2">
									<input type="checkbox" class="other_cEmails_checkbox me-2" checked> 
									<input type="hidden" name="other_cEmails_checkbox[]" value="1">
									<input type="text" class="form-control form-control-sm ml-1" name="other_cEmails[]" value="${trimmedEmail}" readonly style="max-width: 300px;">
								</div>
							`;
						}
					});
				}

				if (data.invoices && data.invoices.length > 0) {
					ccEmailsList += `<h4 class="section-heading">Invoice Files</h4><hr>`;
				}
				if (data.invoices && data.invoices.length > 0) {
					data.invoices.forEach(function(file) {
						ccEmailsList += `
						<div class="file-entry">
							<label>
								<input type="checkbox" name="invoice_file_ids[]" value="${file.id}" checked> 
								<a href="${constructUrl(file.fileurl)}" target="_blank">${file.fileurl}</a>
							</label>
						</div>
						`;
					});
				}
				$('.cc-email-list').html(ccEmailsList);
				},
				error: function() {
					$('.cc-email-list').html('<p>Error getting CC Emails files.</p>');
				}
			});
		$('#addNoteModal').modal('show');
	});
	// $('#addNoteForm').on('submit', function (e) {
	// 	var noteContent = CKEDITOR.instances.note.getData().trim();

	// 	if (noteContent === "") {
	// 		e.preventDefault();
	// 		Swal.fire({
	// 			title: "Note Required",
	// 			text: "Please add some text in note  section before sending.",
	// 			icon: "warning",
	// 			confirmButtonText: "OK"
	// 		});
	// 		return false;
	// 	}
	// });

	document.getElementById('previewPdfBtn').addEventListener('click', function (e) {
		e.preventDefault();

		const company_id = document.getElementById('company_id').value;
		const shipping_contact = document.getElementById('shipping_contact').value;
		const modalInvoiceIds = document.getElementById('modalInvoiceIds').value;
		const dispatchType = document.getElementById('modalDispatchType').value;
		const agingFrom = document.getElementById('aging_from').value;
		const agingTo = document.getElementById('aging_to').value;

		let url = "<?php echo base_url('AccountReceivableController/PreviewStatmentPDF'); ?>";
		let params = [];

		if (company_id) params.push(`company_id=${encodeURIComponent(company_id)}`);
		if (shipping_contact) params.push(`shipping_contact=${encodeURIComponent(shipping_contact)}`);
		if (modalInvoiceIds) params.push(`invoice_ids=${encodeURIComponent(modalInvoiceIds)}`);
		if (dispatchType) params.push(`dispatch_type=${encodeURIComponent(dispatchType)}`);
		if (agingFrom) params.push(`dispatch_type=${encodeURIComponent(agingFrom)}`);
		if (agingTo) params.push(`dispatch_type=${encodeURIComponent(agingTo)}`);
		if (params.length > 0) {
			url += "?" + params.join("&");
		}

		window.open(url, '_blank');
	});



	$(document).on('click', '.dropdown-menu', function (e) {
		e.stopPropagation();
	});
	$(document).on('show.bs.dropdown', '.dropdown', function (e) {
		let button = $(this).find('.dropdown-toggle'); 
		let companyId = button.data('company-id');     
		let $dropdown  = $(this).find('.shipping_contact'); 
		$.ajax({
			url: "<?= base_url('Comancontroler/getShippingContacts') ?>",
				type: "POST",
				data: { company_id: companyId },
				dataType: "json",
			success: function (data) {
					$dropdown.empty();
					if (data.length === 1) {
						let contact = data[0];
						let designation = contact.designation ? " (" + contact.designation + ")" : "";
						$dropdown.append(
							$("<option>", {
								value: contact.id,
								text: contact.contact_person + designation,
								selected: true
							})
						);
					} else {
						$dropdown.append('<option value="">-- Select Shipping Contact --</option>');
						$.each(data, function(index, contact) {
							let designation = contact.designation ? " (" + contact.designation + ")" : "";
							$dropdown.append(
								$("<option>", {
									value: contact.id,
									text: contact.contact_person + designation
								})
							);
						});
					}
				}
		});
	});

</script>


<script>
$(document).ready(function() {
    let emailRequest; 
    let currentForm = $('#addNoteForm');

	
    $('#sendEmailBtn').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to send the email? You will have 30 seconds to cancel.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, schedule it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#emailLoaderModal').modal('show');
                emailTimeout = setTimeout(function() {
                    for (var instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                    emailRequest = $.ajax({
                        url: currentForm.attr('action'),
                        type: "POST",
                        data: currentForm.serialize(),
                        dataType: "json",
                        success: function(response) {
                            $('#emailLoaderModal').modal('hide');
                            if (response.status === 'success') {
                                Swal.fire("Success", response.message, "success");
                                $("#receivableSearchForm").submit();
                                $('#addNoteModal').modal('hide');
                            } else {
                                Swal.fire("Error", response.message, "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            $('#emailLoaderModal').modal('hide');
                            Swal.fire("Error", "Something went wrong: " + error, "error");
                        }
                    });
                }, 30000); 
            }
        });
    });

    $('#cancelEmailBtn').on('click', function() {
        if (emailTimeout) {
            clearTimeout(emailTimeout);
            emailTimeout = null;
        }
        if (emailRequest) {
            emailRequest.abort(); 
            emailRequest = null;
        }
        $('#emailLoaderModal').modal('hide');
        Swal.fire('Cancelled', 'Email sending cancelled.', 'info');
    });

	$(document).on('click', '.see-more-btn', function() {
		const targetId = $(this).data('target');
		const list = $('#' + targetId);
		const hiddenItems = list.find('.note-item.d-none');

		if (hiddenItems.length > 0) {
			hiddenItems.removeClass('d-none');
			$(this).text('Show less');
		} else {
			list.find('.note-item').not(':first').addClass('d-none');
			$(this).text('Show more');
		}
	});

});

$(document).on('click', '.go-reminders-btn', function () {
    // let invoiceId = $(this).data('invoice-id');
	let url = $(this).data('dispatch-url');
	
    window.location.href = url + "#reminders";
});


</script>
