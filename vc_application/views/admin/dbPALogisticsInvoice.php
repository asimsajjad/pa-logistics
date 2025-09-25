<style>
	form.form {margin-bottom:25px;}
	.td-input{display:none;}
	.fas {cursor: pointer;}
	.fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	a.btn {margin-bottom: 5px;}

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
	<h3 class="m-0">DB PA Logistics Invoices</h3>
		<div class="add_page" style="float: right;">
		</div>
		<div class="d-flex align-items-center flex-wrap" style="gap: 15px; float: right;">
			<form class="form hide d-none" method="post" id="invoiceSearchForm" action="<?php echo base_url('Invoice/index') ?>">
				<input type="hidden" name="invoiceSearch" id="invoiceSearch" value="pendingInvoices">
				<select name="dispatchType" id="dispatchType" required class="form-control" style="max-width: 150px;">
					<option value="outsideDispatch" selected>PA Logistics</option>
				</select>				
			</form>

			<a class="invoice-tab" title="Pending Invoices" style="text-decoration: none; color: inherit;" href="#"
			onclick="submitInvoicesSearch('pendingInvoices')">
				<div class="invoice-top">
					<span>Pending Customer's invoices
						<span id="pendingInvoices_count" class="bell-number" style="color: #5bc0de;">(0)</span>
					</span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell bell-icon" style="font-size: 25px; margin-top: 0px; color: #5bc0de;"></i>
					</div>
				</div>
				<div class="invoice-amount text-danger">
					<span id="pendingInvoices_total_parate" style="color: #5bc0de;">$ 0.00</span>
				</div>
			</a>

			<a class="invoice-tab"  title="DB PA Fleet Report" style="text-decoration: none; color: inherit;"  href="<?php echo base_url().'admin/dbPAFleet';?>">
				<div class="invoice-top">
				<span>DB PA Fleet<span id="dbPAFleet_count" class="bell-number" style=" color: #dc3545!important;">(0)</span></span>
					<div class="bell-icon-wrapper">
					<i class="fa fa-bell text-danger bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>
				</div>
				<div class="invoice-amount text-danger" id=""><span id="dbPAFleet_total_parate" style=" color: #dc3545!important;">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="<?php echo base_url().'admin/dbPALogistics';?>" title="DB PA Logistics Report"  style="text-decoration: none; color: inherit;">
				<div class="invoice-top">
					<span>DB PA Logistics<span id="dbPALogistics_count" class="bell-number" style=" color: #007bff!important">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-primary bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>
				</div>
				<div class="invoice-amount text-primary" id=""><span id="dbPALogistics_total_parate" style="color: #007bff;">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="<?php echo base_url().'admin/qpPAFleet';?>" title="QP PA Fleet Report"  style="text-decoration: none; color: inherit;">
				<div class="invoice-top">
					<span>QP PA Fleet<span id="qpPAFleet_count" class="bell-number" style=" color: #ffc107!important">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-warning bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>
				</div>
				<div class="invoice-amount text-warning" id=""><span id="qpPAFleet_total_parate" style=" color: #ffc107!important">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="<?php echo base_url().'admin/rtsPAFleet';?>" title="RTS PA Fleet Report"  style="text-decoration: none; color: inherit;">
				<div class="invoice-top">
					<span>RTS PA Fleet<span id="rtsPAFleet_count" class="bell-number" style=" color: #28a745!important">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell text-success bell-icon" style="font-size: 25px; margin-top: 0px;"></i>
					</div>
				</div>
				<div class="invoice-amount text-success" id=""><span id="rtsPAFleet_total_parate" style=" color: #28a745!important">$ 0.00</span></div>
			</a>

			<a class="invoice-tab" href="<?php echo base_url().'admin/dbPAWarehousing';?>" title="DB PA Warehousing Report"  style="text-decoration: none; color: inherit;">
				<div class="invoice-top">
					<span>DB PA Warehousing<span id="dbPAWarehousing_count" class="bell-number" style="color: #ff00eaff;">(0)</span></span>
					<div class="bell-icon-wrapper">
						<i class="fa fa-bell bell-icon" style="color: #ff00eaff; font-size: 25px; margin-top: 0px;"></i>
					</div>
				</div>
				<div class="invoice-amount text-primary" id=""><span id="dbPAWarehousing_total_parate" style="color: #ff00eaff;">$ 0.00</span></div>
			</a>
		</div>
	</div>
	
	
	<div class="pt-card-body">
		
		<div class="d-block text-center">
			<form class="form form-inline pt-gap-15" id="dbLogisticSearchForm" method="post" action="">
				<input type="text" readonly placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 120px;" class="form-control datepicker">
				<input type="text"  style="width: 120px;" readonly placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker">
				
				<?php 
					$week1 = date('Y-m').'-01,'.date('Y-m').'-08';
					$week2 = date('Y-m').'-09,'.date('Y-m').'-15';
					$week3 = date('Y-m').'-16,'.date('Y-m').'-23';
					$week4 = date('Y-m').'-24,'.date('Y-m-t').'';
					$week5 = date('Y-m').'-01,'.date('Y-m').'-15';
					$week6 = date('Y-m').'-01,'.date('Y-m').'-23';
					$week7 = date('Y-m').'-09,'.date('Y-m').'-23';
					$week8 = date('Y-m').'-09,'.date('Y-m-t').'';
					$week9 = date('Y-m').'-16,'.date('Y-m-t').'';
					$week10 = date('Y-m').'-01,'.date('Y-m-t').'';
					
					if($this->input->post('week')=='all') { $curernt_w = '0'; }
					elseif($this->input->post('week')==$week1) { $curernt_w = '1'; }
					elseif($this->input->post('week')==$week2) { $curernt_w = '2'; }
					elseif($this->input->post('week')==$week3) { $curernt_w = '3'; }
					elseif($this->input->post('week')==$week4) { $curernt_w = '4'; }
					elseif($this->input->post('week')==$week5) { $curernt_w = '5'; }
					elseif($this->input->post('week')==$week6) { $curernt_w = '6'; }
					elseif($this->input->post('week')==$week7) { $curernt_w = '7'; }
					elseif($this->input->post('week')==$week8) { $curernt_w = '8'; }
					elseif($this->input->post('week')==$week9) { $curernt_w = '9'; }
					elseif($this->input->post('week')==$week10) { $curernt_w = '10'; }
					/*elseif(date('d') < 9) { $curernt_w = '1';}
                    elseif(date('d') < 16) { $curernt_w = '2'; }
                    elseif(date('d') < 24) { $curernt_w = '3'; }
                    else { $curernt_w = '4'; }*/
                    else { $curernt_w = '0'; }
                    
				?>
				<select name="week" class="form-control">
					<option value="all" <?php if($curernt_w == '0') { echo 'selected'; } ?>>All Week</option>
					<option value="<?php echo $week1;?>" <?php if($curernt_w == '1') { echo 'selected'; } ?>>Week 1</option>
					<option value="<?php echo $week2;?>" <?php if($curernt_w == '2') { echo 'selected'; } ?>>Week 2</option>
					<option value="<?php echo $week3;?>" <?php if($curernt_w == '3') { echo 'selected'; } ?>>Week 3</option>
					<option value="<?php echo $week4;?>" <?php if($curernt_w == '4') { echo 'selected'; } ?>>Week 4</option>
					<option value="<?php echo $week5;?>" <?php if($curernt_w == '5') { echo 'selected'; } ?>>Week 1 & Week 2</option>
					<option value="<?php echo $week6;?>" <?php if($curernt_w == '6') { echo 'selected'; } ?>>Week 1 to Week 3</option>
					<option value="<?php echo $week7;?>" <?php if($curernt_w == '7') { echo 'selected'; } ?>>Week 2 & Week 3</option>
					<option value="<?php echo $week8;?>" <?php if($curernt_w == '8') { echo 'selected'; } ?>>Week 2 to Week 4</option>
					<option value="<?php echo $week9;?>" <?php if($curernt_w == '9') { echo 'selected'; } ?>>Week 3 & Week 4</option>
					<option value="<?php echo $week10;?>" <?php if($curernt_w == '10') { echo 'selected'; } ?>>All 4 Week</option>
				</select>
				<!-- <select name="dispatchType" required class="form-control" style="max-width: 150px;">
					<option value="">Select Dispatch</option>
					<option value="paDispatch" <?php if($this->input->post('dispatchType') == 'paDispatch') { echo 'selected'; } ?>>PA Fleet Dispatch</option>
					<option value="outsideDispatch" <?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo 'selected'; } ?>>Outside Dispatch</option>
				</select> -->
				<!-- <select name="invoiceType"  class="form-control" style="max-width: 150px;">
					<option value="">Invoice Type</option>
					<option value="Direct Bill" <?php if($this->input->post('invoiceType') == 'Direct Bill') { echo 'selected'; } ?>>Direct Bill</option>
					<option value="Quick Pay" <?php if($this->input->post('invoiceType') == 'Quick Pay') { echo 'selected'; } ?>>Quick Pay</option>
				</select> -->
				<select name="driver[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Driver" style="max-width: 250px;">
					<option value="">All Drivers</option>
					<?php 
						$selected_drivers = $this->input->post('driver');
						if(!empty($drivers)){
							foreach($drivers as $val){
								echo '<option value="'.$val['id'].'"';
								if(is_array($selected_drivers) && in_array($val['id'], $selected_drivers)) { 
									echo ' selected '; 
								}
								echo '>'.$val['dname'].'</option>';
							}
						}
					?>
				</select>
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
				<input type="submit" value="Search" name="search" class="btn btn-success">
			</form>
		</div>
		
		
		
		<form class="form hide d-none" method="post" action="" id="editrowform">
			<input type="text" name="type_input" id="type_input" value="<?=$dispatchURL?>" required>
			<input type="text" name="did_input" placeholder="ID" id="did_input" value="" required>
			<input type="text" name="rate_input" placeholder="rate" id="rate_input" value="" required>
			<input type="text" name="parate_input" placeholder="pa rate" id="parate_input" value="" required>
			<input type="text" name="payoutAmount_input" placeholder="payout Amount" id="payoutAmount_input" value="">
			<input type="text" name="invoiceDate_input" placeholder="invoice Date" id="invoiceDate_input" value="">
			<input type="text" name="expectPayDate_input" placeholder="expect Pay Date" id="expectPayDate_input" value="">
			<input type="text" name="invoiceType_input" placeholder="invoiceType" id="invoiceType_input" value="">
			<input type="text" name="invoice_input" placeholder="invoice" id="invoice_input" value="">
			<input type="text" name="status_input" placeholder="status" id="status_input" value="">
		</form>
		
		<div class="table-responsive pt-datatbl" style="overflow-y: auto;max-height: 90vh;">
			<table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead style="position:sticky;top:0">
                    <tr>
						<th>Sr no.</th>
						<th>PU Date</th>
						<th>Invoice #</th>
						<th>Company</th>
						<th>Shipment Type</th>
						<th>Tracking #</th>
						<th>Rate</th>
						<th>Invoice Amt</th>
						<!-- <th>Payout Amt</th> -->
						<!-- <th>Week</th> -->
						<th class="d-none">Invoice Date</th>
						<!-- <th>Invoice Type</th> -->
						<th class="d-none">BOL</th>
						<th class="d-none">RC</th>
						<th class="d-none">$</th>
						<th>Status</th>
						<th>Expected Pay Date</th>
                        <th>Action</th>
					</tr> 
				</thead>
				
				<tbody>
					<?php
						if(!empty($dispatch)){
							$n=1; $rate = $parate = 0;
							foreach($dispatch as $key) {
								if($n < 16) {
									$rate = $rate + $key['rate'];
									$parate = $parate + $key['parate'];
								}
								$dispatchMeta = json_decode($key['dispatchMeta'],true);
								
								$bgcolor = '';
								if(stristr($key['dispatchMeta'],'"invoicePaid":"0"') && $key['expectPayDate']!='0000-00-00' && strtotime($key['expectPayDate']) < strtotime(date('Y-m-d'))){
								    //$bgcolor = 'bgcolor="#CC4B4B"';
								}
								//echo 'stristr('.$key['dispatchMeta'].',"invoicePaid":"0") && '.$key['expectPayDate']."!='0000-00-00' && strtotime(".$key['expectPayDate'].') > strtotime('.date('Y-m-d').')';
							 
								$invoiceType = $agingTxt = '';
								$showAging = 'false';
								$aDays = 0;
								if($key['invoiceType']=='Direct Bill'){ $invoiceType = 'DB'; $aDays = 30; }
								elseif($key['invoiceType']=='Quick Pay'){ $invoiceType = 'QP'; $aDays = 7; }
								elseif($key['invoiceType']=='RTS'){ $invoiceType = 'RTS'; $aDays = 3; }
								
								if($key['invoiceDate'] != '0000-00-00') { $showAging = 'true'; }
								if($dispatchMeta['invoicePaidDate'] != '') { $showAging = 'false'; }
								if($dispatchMeta['invoiceCloseDate'] != '') { $showAging = 'false'; }
								
								if($showAging == 'true'){
									$date1 = new DateTime($key['invoiceDate']);
									$date2 = new DateTime(date('Y-m-d'));
									$diff = $date1->diff($date2);
									$aging = $diff->days;
									
									if($aging  > $aDays && $aDays > 0) {
										$agingTxt = '<br><strong style="color:red">Inv. Aging: '.$aging.' Days</strong>';
									} else {
										$agingTxt = '<br>Inv. Aging: '.$aging.' Days';
									}
								}
									?>
							<tr class="tr-<?php echo $key['id'];?>" <?=$bgcolor?>>
								<td><?php echo $n;?></td>
								<td><?php echo date('m-d-Y',strtotime($key['pudate']));?></td>
								<td><?php //echo $key['invoice'];?>
									<a href="<?php echo base_url().'admin/'.$dispatchURL.'/update/'.$key['id'];?>"><span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_invoice_txt_<?php echo $key['id'];?>"><?php echo $key['invoice'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span></a>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_invoice_input_<?php echo $key['id'];?> current_input" data-id="#invoice_input" value="<?php echo $key['invoice'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								<td><?php //echo $key['company'];
									if(array_key_exists($key['company'],$companyArr)){ 
										echo $companyArr[$key['company']]; 
									}
									
									/*$payoutRate = 0;
									if(!empty($companies)){
										foreach($companies as $val){
											if($key['company']==$val['id']) { 
												echo $val['company']; 
												$payoutRate = $val['payoutRate'];
											}
										}
									}
									if(!is_numeric($payoutRate)) { $payoutRate = 0; }*/
								?></td> 
								<td><?php echo $dispatchMeta['invoicePDF'];?></td> 
								<td><?php echo $key['tracking'];?></td> 
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['rate'] > 0) { echo '$'; } echo '<span class="c_rate_txt_'.$key['id'].' rateTxt">'.number_format($key['rate'],2).'</span>';?> &nbsp; <i class="fas fa-edit d-none" data-id="<?php echo $key['id'];?>" title="Edit" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_rate_input_<?php echo $key['id'];?> current_input" data-id="#rate_input" value="<?php echo $key['rate'];?>" onkeyup="this.value=this.value.replace(/[^\d.]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['parate'] > 0) { echo '$'; } echo '<span class="c_parate_txt_'.$key['id'].' paRateTxt">'.number_format($key['parate'],2).'</span>';?> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_parate_input_<?php echo $key['id'];?> current_input" data-id="#parate_input" value="<?php echo $key['parate'];?>" onkeyup="this.value=this.value.replace(/[^\d.]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td>
								
								<!-- <td><?php ?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['payoutAmount'] > 0) { echo '$'; } echo '<span class="c_payoutAmount_txt_'.$key['id'].' payoutAmount">'.$key['payoutAmount'].'</span>';?> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_payoutAmount_input_<?php echo $key['id'];?> current_input" data-id="#payoutAmount_input" value="<?php echo $key['payoutAmount'];?>" onkeyup="this.value=this.value.replace(/[^\d.]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> -->
								
								<!-- <td><?=$key['dWeek']?></td> -->
								
								<td class="d-none"><?php //$key['invoiceDate']?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php echo '<span class="c_invoiceDate_txt_'.$key['id'].' invoiceDate">'; if($key['invoiceDate']=='0000-00-00') { echo 'TBD'; } else { echo 'Inv:&nbsp;'.$key['invoiceDate']; } echo '</span>';?> 
									
									<?php 
									if($dispatchMeta['invoicePaidDate'] != '') { echo '<br>Paid:&nbsp;'.$dispatchMeta['invoicePaidDate']; }
									if($dispatchMeta['invoiceCloseDate'] != '') { echo '<br>Closed:&nbsp;'.$dispatchMeta['invoiceCloseDate']; }
									?>
									</span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_invoiceDate_input_<?php echo $key['id'];?> current_select datepicker" data-id="#invoiceDate_input" value="<?php echo $key['invoiceDate'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td>
								
								<!-- <td><?php ?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php echo '<span class="c_invoiceType_txt_'.$key['id'].' invoiceType">'.$key['invoiceType'].'</span>';?> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<select class="c_invoiceType_input_<?php echo $key['id'];?> current_select" name="invoiceType" data-id="#invoiceType_input">
											<option value="">Select Invoice Type</option>
											<option <?php if($key['invoiceType']=='RTS') { echo 'selected'; } ?> value="RTS">RTS</option>
											<option <?php if($key['invoiceType']=='Direct Bill') { echo 'selected'; } ?> value="Direct Bill">Direct Bill</option>
											<option <?php if($key['invoiceType']=='Quick Pay') { echo 'selected'; } ?> value="Quick Pay">Quick Pay</option>
										</select>
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
									<?=$agingTxt?>
								</td> -->
								
								<td class="d-none" bgcolor="<?php if($key['bol']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php if($key['bol'] !='') { echo 'Yes'; }?></td> 
								
								<td class="d-none" bgcolor="<?php if($key['rc']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php echo $key['rc'];?></td> 
								
								<td class="d-none" bgcolor="<?php if($key['gd']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php echo $key['gd'];?></td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_status_txt_<?php echo $key['id'];?> "><?php echo $key['status'];?></span> &nbsp; <i class="fas fa-edit" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="text" class="c_status_input_<?php echo $key['id'];?> current_input" data-id="#status_input" value="<?php echo $key['status'];?>">
										<i class="fa fa-paper-plane" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td><?php //if($key['expectPayDate']=='0000-00-00') { echo 'TBD'; }?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php echo '<span class="c_expectPayDate_txt_'.$key['id'].' expectPayDate">';if($key['expectPayDate']=='0000-00-00') { echo 'TBD'; } else { echo $key['expectPayDate']; } echo '</span>';?> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_expectPayDate_input_<?php echo $key['id'];?> current_select datepicker" data-id="#expectPayDate_input" value="<?php echo $key['expectPayDate'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td>
								 
								<td>
									<a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/'.$dispatchURL.'/update/'.$key['id'];?>?invoice">Generate Invoice <i class="fas fa-edit" title="Edit" alt="Edit"></i></a>  
									<br>
									<!-- <a class="btn btn-sm btn-primary editInvoice" data-dTable="dispatchOutside" data-id="<?=$key['id']?>" href="<?php echo base_url('Invoice/downloadInvoicePDF/'.$key['id']);?>?dTable=dispatchOutside" data-toggle="modal" data-target="#editInvoiceModal">Generate Invoice</a> -->
										
								
								</td>
							</tr> 
							
							<?php
								$n++;
							}
						}
					?>
					
					<tfoot>  
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><strong>Total</strong></td>
							<td><strong>$<span class="rateTotal"><?php echo $rate; ?></span></strong></td>
							<td><strong>$<span class="paRateTotal"><?php echo $parate; ?></span></strong></td>
							<td></td>
							<td></td>
							<td></td>
							<td class="d-none"></td>
							<td class="d-none"></td>
							<td class="d-none"></td>
							<td class="d-none"></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tfoot>
				</tbody>
			</table>
		</div>
	</div>
	
</div>

</div>

<!-- Modal -->


<div id="editInvoiceModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="max-width: 800px;">
		<div class="modal-content" style="max-width: 100%;margin: auto;width: 100%;">
			<div class="modal-header">
				<button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Generate Invoice</h4>
			</div>
			<div class="modal-body">
				<form class="form" method="post" action="" id="editinvoiceform">
					<div class="alert alert-success i-status-success-msg" style="display:none">Please wait....</div>
					<div class="row invoiceAjaxForm"></div>
					<p>
						<input type="submit" name="generatePDF" id="generatePdfID" class="btn btn-primary" value="Update Invoice"> &nbsp;
						<a href="#" class="btn btn-success generatePdfBtn">Generate Invoice</a>
						<a href="#" class="btn btn-primary combibePdfBtn" target="_blank">Combine PDF</a>
						<a href="#" class="btn btn-success downloadPDF" data-type="<?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo 'outside'; } else { echo 'dispatch'; } ?>" data-id="">Download All</a>
					</p>
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

<!-- jQuery UI -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<link href="<?php echo base_url().'assets/css/5.3.0bootstrap.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/5.3.0bootstrap.bundle.min.js'; ?>"></script>

<script>
	$(document).ready(function() {
    	$('.select2').select2();
	  	let dispatchType = 'outsideDispatch';
		let invoiceSearch ='pendingInvoices'; 
        $.ajax({
            url: "<?php echo base_url('AllInvoices/getInvoicesCounts'); ?>",
            type: "POST",
            dataType: "json",
			data : {dispatchType: dispatchType,
				invoiceSearch: invoiceSearch},
            success: function(response) {
				let pendingInvoicesCount = response.InvoicesCount.pending_invoices_count || 0;
				let pendingInvoicesAmount= response.InvoicesCount.pending_invoices_amount || 0;

				$("#pendingInvoices_count").text("(" + pendingInvoicesCount + ")");
				$("#pendingInvoices_total_parate").text("$" + pendingInvoicesAmount );
            },
            error: function(xhr, status, error) {
                console.error("Error fetching invoice counts:", error);
            }
        });
   });
   function submitInvoicesSearch(invoiceTypeLabel) {
		document.getElementById('invoiceSearch').value = invoiceTypeLabel;
		document.getElementById('invoiceSearchForm').submit();
	}
	$( function() {
		/*$( ".datepicker" ).datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});*/
		$(document).on('focus', '.datepicker', function() {
            $(this).datepicker({
    			dateFormat: 'yy-mm-dd',
    			changeMonth: true,
    			changeYear: true
    		});
        });

		$(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
		$('html, body').on('keyup','#dataTable_filter input',function(){
			calculateRate();
		});
		$('html, body').on('change','#dataTable_length select',function(){
			calculateRate();
		});
		/*$('html, body').on('click','#dataTable_paginate a',function(){
			calculateRate();alert('yes');
		});*/ 
		$('html, body').on('click','a.paginate_button',function(){
			//calculateRate();
		});
		$('#dataTable').on( 'page.dt', function () {
			beforeCalculateRate();
			setTimeout(function() { calculateRate(); }, 1000);
		});
		setTimeout(function() {
			calculateRate();
		}, 2500);
		
		$('.td-txt .fa-edit').click(function(){
			var tdid = $(this).attr('data-id');
			
			$('.td-txt').show();
			$('.td-input').hide();
			
			$('.td-txt-'+tdid).hide();
			$('.td-input-'+tdid).show();
			
			$('#did_input').val(tdid);
			
			var rate = $('.c_rate_input_'+tdid).val();
			$('#rate_input').val(rate);
			
			var parate = $('.c_parate_input_'+tdid).val();
			$('#parate_input').val(parate);
			
			var payoutAmount = $('.c_payoutAmount_input_'+tdid).val();
			$('#payoutAmount_input').val(payoutAmount);
			
			var invoiceDate = $('.c_invoiceDate_input_'+tdid).val();
			$('#invoiceDate_input').val(invoiceDate);
			
			var expectPayDate = $('.c_expectPayDate_input_'+tdid).val();
			$('#expectPayDate_input').val(expectPayDate);
			
			var invoice = $('.c_invoice_input_'+tdid).val();
			$('#invoice_input').val(invoice);
			
			var invoiceType = $('.c_invoiceType_input_'+tdid).val();
			$('#invoiceType_input').val(invoiceType);
			
			var status = $('.c_status_input_'+tdid).val();
			$('#status_input').val(status);
			
			var $scrollable = $('.table-responsive');
            $scrollable.animate({
                scrollLeft: $scrollable[0].scrollWidth - $scrollable.width()
            }, 800); // Adjust the duration as needed
			
		});
		$('.current_select').change(function(e){
			var id = jQuery(this).attr('data-id');
			var valu = jQuery(this).val();
			jQuery(id).val(valu);
		});
		$('.current_input').keyup(function(e){
			//var cls = jQuery(this).attr('data-cls');
			var id = jQuery(this).attr('data-id');
			var valu = jQuery(this).val();
			//jQuery(cls).html(valu);
			jQuery(id).val(valu);
		});
		$('.fa-paper-plane').click(function(e){
			var tdid = jQuery(this).attr('data-id'); 
			
			$('.c_rate_txt_'+tdid).html($('#rate_input').val());
			$('.c_parate_txt_'+tdid).html($('#parate_input').val());
			// $('.c_payoutAmount_txt_'+tdid).html($('#payoutAmount_input').val());
			$('.c_invoiceDate_txt_'+tdid).html($('#invoiceDate_input').val());
			$('.c_expectPayDate_txt_'+tdid).html($('#expectPayDate_input').val());
			// $('.c_invoiceType_txt_'+tdid).html($('#invoiceType_input').val());
			$('.c_invoice_txt_'+tdid).html($('#invoice_input').val());
			$('.c_status_txt_'+tdid).html($('#status_input').val());
			
			$('.td-txt').show();
			$('.td-input').hide();
			
			/*var invoiceDate = $('.c_invoiceDate_input_'+tdid).val();
				$('#invoiceDate_input').val(invoiceDate);
				
				var expectPayDate = $('.c_expectPayDate_input_'+tdid).val();
			$('#expectPayDate_input').val(expectPayDate);*/
			
			$('#editrowform').submit();
		});
		
		$('.downloadPDF').click(function(e){
			e.preventDefault();
			var did = $(this).attr('data-id');
			var type = $(this).attr('data-type');
			var href = $(this).attr('href');
			$.ajax({
				type: "GET",
				url: "<?php echo base_url('admin/invoice?doc=');?>"+did+"&type="+type,
				data: "",
				success: function(response) { 
					let pdfLinks = response;
                    pdfLinks.forEach(function(pdfUrl) {
                        let link = document.createElement('a');
						if(type == 'outside'){
							link.href = "<?=base_url()?>"+pdfUrl;
						} else {
							link.href = "<?=base_url()?>"+pdfUrl;
						}
                        link.download = pdfUrl.split('/').pop();  // Filename from URL
                        link.target = '_blank';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });
					window.location.href=href;
				}
			});
		});
		
		$('.editInvoice').click(function(e){
			e.preventDefault();
			$('.invoiceAjaxForm').html('.....'); 
			var dTable = $(this).attr('data-dTable');
			var did = $(this).attr('data-id');
			var href = $(this).attr('href');
			$('.generatePdfBtn').attr('href',href);
			$('#editinvoiceform').attr('action',href);
			let cBtn = '<?php echo base_url('Invoice/downloadInvoicePDF/');?>'+did+'?invoiceWithPdf=bol-rc<?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo '&type=outside&dTable=dispatchOutside'; } ?>';
			$('.combibePdfBtn').attr('href',cBtn);
			
			let dBtn = '<?php echo base_url('Invoice/downloadInvoicePDF/');?>'+did+'<?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo '?dTable=dispatchOutside'; } ?>';
			$('.downloadPDF').attr('data-id',did);
			$('.downloadPDF').attr('href',dBtn);
			$.ajax({
				type: "post",
				url: "<?php echo base_url('Invoice/editInvoiceForm?editInvoiceID=');?>"+did+"&dTable="+dTable,
				data: "editInvoiceID="+did,
				success: function(responseData) { 
					$('.invoiceAjaxForm').html(responseData);
				}
			});
		});
		$('.editInvoiceBtn').click(function(e){
			e.preventDefault();
			var form_data = $('#editinvoiceform').serialize();
			$.ajax({
				type: "post",
				url: "<?php echo base_url('admin/invoice');?>",
				data: form_data,
				success: function(responseData) { 
					alert(responseData);
				}
			});
		});
		
		$('#editrowform').submit(function(e){
			e.preventDefault();
			var form_data = $(this).serialize();
			$.ajax({
				type: "post",
				url: "<?php echo base_url('admin/invoice');?>",
				data: form_data,
				success: function(responseData) { 
					$('#editrowform input').val('');
				}
			});
		});
		$('.status-edit').click(function(e){
			e.preventDefault();
			var did = $(this).attr('data-id');
			var dstatus = $(this).attr('data-status');
			$('#dstatus-input').val(did);
			$('#status-input').val(dstatus);
		});
		$('#editstatusform').submit(function(e){
			e.preventDefault();
			var form_data = $(this).serialize();
			var did = $('#dstatus-input').val();
			var dstatus = $('#status-input').val();
			$('.status-success-msg').show();
			$.ajax({
				type: "post",
				url: "<?php echo base_url('admin/invoice');?>",
				data: form_data,
				success: function(responseData) {
					//alert("data saved")
					$('.status-success-msg').html('Status Updated Successfully.');
					$('.status-'+did).html(dstatus);
				}
			});
		});
		
		
		$('#dataTable').DataTable({
			"lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]]
		});
		
		function calculateRate(){
			var parate = 0;  var rate = 0;
			$( ".paRateTxt:visible" ).each(function( index ) {
				var currentPrice = $(this).html().replace(/,/g, '');
				parate = parate + parseFloat(currentPrice);
			});
			$( ".rateTxt:visible" ).each(function( index ) {
				var currentPrice = $(this).html().replace(/,/g, '');
				rate = rate + parseFloat(currentPrice); 
			});
			$('.paRateTotal').html(
				(parseFloat(parate) || 0).toLocaleString('en-US', {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				})
			);

			$('.rateTotal').html(
				(parseFloat(rate) || 0).toLocaleString('en-US', {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				})
			);
		}
		function beforeCalculateRate(){
			var parate = 0;  var rate = 0;
			
			$('.paRateTotal').html(parate);
			$('.rateTotal').html(rate);
		}
		
	});
</script>
