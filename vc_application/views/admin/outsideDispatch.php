<style>
	form.form {margin-bottom:25px;}
	.td-input{display:none;}
	.fas {cursor: pointer;}
	.fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	a.btn {margin-bottom: 5px;}
	#dataTable td{max-width:250px;white-space: normal;}
	#dataTable td.nowrap{white-space: nowrap;}
	#dataTable td a{color:blue;}
	#dataTable td a.btn{color:#fff;}
	table tr.showTr, #invoiceTable tr.showTr{display: table-row !important;}
	#invoiceTable thead tr {background: #1362b7;color: #fff;}
	#invoiceTable .srno{font-size:0px;}
	#invoiceTable .srno::before{content:">>";font-size:15px;}

	table tr.showTr, #subInvoicesTable tr.showTr{display: table-row !important;}
	#subInvoicesTable thead tr {background: #1362b7;color: #fff;}
	#subInvoicesTable .srno{font-size:0px;}
	#subInvoicesTable .srno::before{content:">>";font-size:15px;}

	.getAddressParent{position:relative;}

	
.addressList{position:absolute;top:99%;left:0px;}
.cRed{color:red;font-weight:bold;}
</style>
<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3 class="m-0">PA Logistics</h3>
		<div class="add_page" style="float: right;">
			<a class="nav-link p-0" style="display: inline;" title="Create Section" href="<?php echo  base_url().'admin/outside-dispatch/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success btn-sm pt-cta"/>
			</a>
			<a class="nav-link p-0" title="Upload CSV" href="<?php echo  base_url().'admin/outside-dispatch/upload-csv';?>" style="display: inline;"><input type="button" name="add" value="Upload CSV" class="btn btn-primary btn-sm pt-cta"/>
			</a>
		</div>
	</div>
	
	
	<div class="pt-card-body">
		
		<div class="d-block text-center">
			<form class="form form-inline pt-gap-15" method="post" action="">
				<input type="text"  placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 150px;" class="form-control datepicker pt-form-field">
				<input type="text"  style="width: 150px;" placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker pt-form-field">
				
				<?php 
				    if($this->input->post('sdate')) { 
						$sdate = $this->input->post('sdate');
						$lastDay = date('t',strtotime($sdate));
					} else {
						$lastDay = date('t');
					}
					$week1 = '-01,-08';
					$week2 = '-09,-15';
					$week3 = '-16,-23';
					$week4 = '-24,-'.$lastDay.'';
					$week5 = '-01,-15';
					$week6 = '-01,-23';
					$week7 = '-09,-23';
					$week8 = '-09,-'.$lastDay.'';
					$week9 = '-16,-'.$lastDay.'';
					$week10 = '-01,-'.$lastDay.'';
					/*
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
					*/
					
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
                    else { $curernt_w = 'all'; }
                    
				?>
				<select name="week" class="form-control pt-form-field" style="width: 120px;">
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
				
				<select name="truckingCompanies" class="form-control pt-form-field" style="width: 180px;">
					<option value="">Truck Company</option>
					<?php 
						if(!empty($truckingCompanies)){
							
							foreach($truckingCompanies as $val){
								echo '<option value="'.$val['id'].'"';
								if($this->input->post('truckingCompanies')==$val['id']) { echo ' selected '; }
								echo '>'.$val['company'].' ('.$val['owner'].')</option>';
							}
						}
					?>
				</select>
				<select name="company" class="form-control pt-form-field select2" style="width: 250px;">
					<option value="">Select A Customer</option>
					<?php 
						if(!empty($companies)){
							foreach($companies as $val){
								echo '<option value="'.$val['id'].'"';
								if($this->input->post('company')==$val['id']) { echo ' selected '; }
								echo '>'.$val['company'].'</option>';
							}
						}
					?>
				</select>
			
				<!-- <select name="driver" class="form-control pt-form-field" style="width: 180px;">
					<option value="">Select Driver</option>
					<?php 
						if(!empty($drivers)){
							foreach($drivers as $val){
								echo '<option value="'.$val['id'].'"';
								if($this->input->post('driver')==$val['id']) { echo ' selected '; }
								echo '>'.$val['dname'].'</option>';
							}
						}
					?>
				</select> -->
				<?php /*
					<select name="dispatchInfo" class="form-control" style="width: 140px;">
					<option value="">Dispatch Info</option>
					<?php 
					if(!empty($dispatchInfo)){
					foreach($dispatchInfo as $val){
					echo '<option value="'.$val.'"';
					if($this->input->post('dispatchInfo')==$val) { echo ' selected '; }
					echo '>'.$val.'</option>';
					}
					}
					?>
					</select>&nbsp;
					<input type="text" style="width: 180px;" placeholder="Dispatch Value" value="<?php if($this->input->post('dispatchInfoValue')) { echo $this->input->post('dispatchInfoValue'); } ?>" name="dispatchInfoValue" class="form-control"> &nbsp;
				*/ ?>
				
				<input type="hidden" value="" name="dispatchInfoValue"> 
				<input type="hidden" value="" name="dispatchInfo"> 
				
				<input type="submit" value="Search" name="search" class="btn btn-success pt-cta"> 
				
				
				<div class="dropdown" style="margin-left:10px;">
					<button class="btn btn-primary dropdown-toggle pt-cta" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Download
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <input type="submit" value="Download CSV" name="generateCSV" class="dropdown-item" >
                        <input type="submit" value="Download Excel" name="generateXls" class="dropdown-item" >
					</div>
				</div>
				
				<!--input type="submit" value="Download" name="generateCSV" class="btn btn-primary" style="margin-left:10px;"-->
			</form>
		</div>
		
		
		
		<form class="form hide d-none" method="post" action="" id="editrowform">
			<input type="text" name="did_input" placeholder="ID" id="did_input" value="" required>
			<input type="text" name="rate_input" placeholder="rate" id="rate_input" value="" required>
			<input type="text" name="parate_input" placeholder="pa rate" id="parate_input" value="" required>
			<input type="text" name="trailer_input" placeholder="trailer" id="trailer_input" value="">
			<input type="text" name="tracking_input" placeholder="tracking" id="tracking_input" value="">
			<input type="text" name="invoice_input" placeholder="invoice" id="invoice_input" value="">
			<input type="text" name="bol_input" placeholder="bol" id="bol_input" value="">
			<input type="text" name="rc_input" placeholder="rc" id="rc_input" value="">
			<input type="text" name="gd_input" placeholder="gd" id="gd_input" value="">
			<input type="text" name="driver_status_input" placeholder="driver_status" id="driver_status_input" value="">
			<input type="text" name="status_input" placeholder="status" id="status_input" value="">
		</form>
		
		<div class="table-responsive pt-datatbl" style="overflow-y: auto;max-height: 90vh;">
			<table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead style="position:sticky;top:0">
                    <tr  class="thead">
						<th>Sr #</th>
						<th>PU Date & Time&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th>PU Info&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th>Del Date & Time&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th>Del Info&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th>Carrier Rate</th>
						<th>Inv Amount</th>
						<th>Company&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th>Carrier&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th>Dispatch Info&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<!--th>Booked Under</th>
						<th>Container/<br>Trailer #</th-->
						<th>Tracking / PO #</th>
						<th>Invoice #</th>
						<th>BOL</th>
						<th>RC</th>
						<th>Carrier Inv Status</th>
						<th>Customer Inv Status</th>
						<th class="d-none">Shipment Status<!--Driver Status--></th> 
						<th>Shipment Notes<!--Status--></th>
                        <th>Action</th>
					</tr> 
				</thead>
				
				<tbody>
                    
					<?php
					
						$cityArr = $locationArr = $companyArr = $comAddArr = array();
					
					if(!empty($companies)){
						foreach($companies as $val){
							$companyArr[$val['id']] = $val['company'];
						}
					}
					if(!empty($cities)){
						foreach($cities as $val){
							$cityArr[$val['id']] = $val['city'];
						}
					}
					
					if(!empty($locations)){
						foreach($locations as $val){
							$locationArr[$val['id']] = $val['location'];
						}
					}
					if(!empty($companyAddress)){
						foreach($companyAddress as $val){
							$comAddArr[$val['id']] = array($val['company'],$val['city'].', '.$val['state'],$val['address'].' '.$val['city'].', '.$val['state'].' '.$val['zip']);
						}
					}
					
					
					$sort_column = 'sortcolumn'; 
                    usort($dispatchOutside, function ($a, $b) use ($sort_column) {
                        return strcmp($a[$sort_column], $b[$sort_column]);
                    });
					// echo "<pre>";
					// print_r($dispatchOutside);exit;
    
						if(!empty($dispatchOutside)){
							$n=1; $rate = $parate = $parentChildRate = $parentChildPaRate = 0;
							// echo "<pre><br>";
							// print_r($dispatchOutside);exit;
							foreach($dispatchOutside as $key) {
							    
							    // if(!strstr($key['dodate'],'0000') && strtotime($startDate) > strtotime($key['dodate'])) {
							    //if(strtotime($startDate) > strtotime($key['pudate'])) {
							        // continue; 
							    // }
							    
							    $rateTxt = $paRateTxt = '';
							    // if(strtotime($startDate) <= strtotime($key['pudate']) && strtotime($endDate) >= strtotime($key['pudate'])) {
							        $rateTxt = 'rateTxt'; $paRateTxt = 'paRateTxt';
							    // }

								if($n < 16 && strtotime($startDate) <= strtotime($key['pudate']) && strtotime($endDate) >= strtotime($key['pudate'])) {
									$rate = $rate + $key['rate'];
									$parate = $parate + $key['parate'];
									// $rate = $rate + $parentChildRate;
									// $parate = $parate + $parentChildPaRate;
								}
								$dispatchMeta = json_decode($key['dispatchMeta'],true);
								
								$cls = 'parInvCls';
								if($key['parentInvoice'] != ''){
								    $cls .= str_replace(' ','',$key['parentInvoice']).' childTR1';
								}
							
							// if($key['parentInvoice'] == ''){
							?>
							<tr class="tr-<?php echo $key['id'];?>">
								<td><?php echo $n;?></td> 
								<td>
									<?php 
										$pickupCount = 1; 
										if (isset($key['dispatchInfo']) && is_array($key['dispatchInfo'])) {
											foreach ($key['dispatchInfo'] as $dispatch) {
												if ($dispatch['pd_type'] == 'pickup' && !strstr($dispatch['pd_date'], '0000') && $dispatch['pd_date'] != '') {
												$pickupCount++;				
												}
											}
										}
									    if ($pickupCount > 1) {
        									echo '<strong>PU-1: </strong>' . date('m-d-Y', strtotime($key['pudate'])) . '<br> @ ' . $key['ptime'] . '<br>';
									    } else {
 									       echo '<strong>' . date('m-d-Y', strtotime($key['pudate'])) . '<br> @ ' . $key['ptime'] . '</strong><br>';
    									}
									?>

									
									<?php
									
									if (isset($key['dispatchInfo']) && is_array($key['dispatchInfo'])) {
										$pickOfno=2;
										foreach ($key['dispatchInfo'] as $index => $dispatch) {
											//  print_r($dispatch);exit;
											if($dispatch['pd_type']=='pickup'){
												if (!strstr($dispatch['pd_date'], '0000') && $dispatch['pd_date'] != '') {
													echo 'PU-' . ($pickOfno) . ': ' . date('m-d-Y', strtotime($dispatch['pd_date'])) . '<br> @ '. $dispatch['pd_time'] . '';
												} 
												echo '<br>';
												?>
												<?php
												$pickOfno++;
											}
										}
									}
									// if($key['childInvoice'] != '' || $dispatchMeta['otherChildInvoice'] != ''){
								    //     echo '<br><br><a href="#" class="showChildInv btn btn-sm btn-success pt-cta" data-trcls=".parInvCls'.str_replace(' ','',$key['invoice']).'" data-cls=".subInvTr'.str_replace(' ','',$key['invoice']).'" data-toggle="modal" data-target="#invoiceModal">Sub Inv.</a>';
								    // } 
								    if ($key['childInvoice'] != '' || $dispatchMeta['otherChildInvoice'] != ''): ?>
										<br><br>
										<a href="#" 
										class="btn btn-sm btn-success pt-cta" 
										onclick="loadChildInvoices('<?php echo $key['id']; ?>')">
										Sub Inv.
										</a>
									<?php endif; 
								?>
									
								</td> 
								<td>
								<a href="<?php echo base_url().'admin/outside-dispatch/update/'.$key['id'];?>"><?php
								$pickupCount = 1; 
								if (isset($key['dispatchInfo']) && is_array($key['dispatchInfo'])) {
									foreach ($key['dispatchInfo'] as $dispatch) {
										if ($dispatch['pd_type'] == 'pickup' && !strstr($dispatch['pd_date'], '0000') && $dispatch['pd_date'] != '') {
										$pickupCount++;				
										}
									}
								}
								if ($pickupCount > 1) {
									if(array_key_exists($key['paddressid'],$comAddArr)  && $key['paddressid'] > 0){ 
										echo 'PU-1: '. $comAddArr[$key['paddressid']][0].'<br> ['.$comAddArr[$key['paddressid']][1].']';
									} else {
										if(array_key_exists($key['plocation'],$locationArr)){ 
											echo 'PU-1: '. $locationArr[$key['plocation']]; 
										}
										if(array_key_exists($key['pcity'],$cityArr)){ 
											echo '<br> ['.$cityArr[$key['pcity']].']'; 
										}
									}
								} else {
									if(array_key_exists($key['paddressid'],$comAddArr)  && $key['paddressid'] > 0){ 
										
										echo ''. $comAddArr[$key['paddressid']][0].'<br> ['.$comAddArr[$key['paddressid']][1].']';
									} else {
										if(array_key_exists($key['plocation'],$locationArr)){ 
											
											echo ''. $locationArr[$key['plocation']]; 
										}
										if(array_key_exists($key['pcity'],$cityArr)){ 
											echo '<br> ['.$cityArr[$key['pcity']].']'; 
										}
									}
								}	
									echo '<br>';	
									if (isset($key['dispatchInfo']) && is_array($key['dispatchInfo'])) {
										$pickOfno=2;
										foreach ($key['dispatchInfo'] as $index => $dispatch) {
											//  print_r($dispatch);exit;
											if($dispatch['pd_type']=='pickup'){
											
												?>
												<a href="<?php echo base_url().'admin/outside-dispatch/update/'.$key['id'];?>"><?php
												if (array_key_exists($dispatch['pd_addressid'], $comAddArr) && $dispatch['pd_addressid'] > 0) {
													echo 'PU-' . ($pickOfno) . ': ' . $comAddArr[$dispatch['pd_addressid']][0] . '<br> [' . $comAddArr[$dispatch['pd_addressid']][1] . ']';
												}
												elseif (array_key_exists($dispatch['daddressid'], $comAddArr) && $dispatch['daddressid'] > 0) {
													echo 'PU-' . ($pickOfno) . ': ' . $comAddArr[$dispatch['daddressid']][0] . '<br>[' . $comAddArr[$dispatch['daddressid']][1] . ']';
												} 
												else {
													if (array_key_exists($dispatch['pd_location'], $locationArr) && $dispatch['pd_location'] != '') {
														echo 'PU-' . ($pickOfno) . ': ' . $locationArr[$dispatch['pd_location']];
													}
													elseif (array_key_exists($dispatch['dlocation'], $locationArr)) {
														echo 'PU-' . ($pickOfno) . ': ' . $locationArr[$dispatch['dlocation']];
													}
													if (array_key_exists($dispatch['pd_city'], $cityArr) && $dispatch['pd_city'] != '') {
														echo '<br> [' . $cityArr[$dispatch['pd_city']] . ']';
													}
													elseif (array_key_exists($dispatch['dcity'], $cityArr)) {
														echo '<br> [' . $cityArr[$dispatch['dcity']] . ']';
													} 
												}?></a><?php
												echo '<br>';
												$pickOfno++;
											}
										}
									}
								
									?></a>
								</td>
							
								<td><strong><?php 
								   $dropOffCount = 1; 
								   if (isset($key['dispatchInfo']) && is_array($key['dispatchInfo'])) {
									   foreach ($key['dispatchInfo'] as $dispatch) {
										   if ($dispatch['pd_type'] == 'dropoff' && !strstr($dispatch['pd_date'], '0000') && $dispatch['pd_date'] != '') {
											   $dropOffCount++; 
										   }
									   }
								   }
								if ($dropOffCount > 1) {
									if(!strstr($key['pd_date'],'0000') && $key['pd_date']!='') { echo 'D-1: ' .date('m-d-Y',strtotime($key['pd_date'])).'<br> @ '.$key['pd_time']; } 
									elseif(strstr($key['dodate'],'0000')) {} 
								    else { echo 'D-1: '.date('m-d-Y',strtotime($key['dodate'])).'<br> @ '.$key['dtime'];} 
								}else{
									if(!strstr($key['pd_date'],'0000') && $key['pd_date']!='') { echo '' .date('m-d-Y',strtotime($key['pd_date'])).'<br> @ '.$key['pd_time']; } 
									elseif(strstr($key['dodate'],'0000')) {} 
								    else { echo ''.date('m-d-Y',strtotime($key['dodate'])).'<br> @ '.$key['dtime'];} 

								}  								
									echo '<br>';


									if (isset($key['dispatchInfo']) && is_array($key['dispatchInfo'])) {
										$dropOfno=2;
										foreach ($key['dispatchInfo'] as $index => $dispatch) {
											//  print_r($dispatch);exit;
											if($dispatch['pd_type']=='dropoff'){
												if (!strstr($dispatch['pd_date'], '0000') && $dispatch['pd_date'] != '') {
													echo 'D-' . ($dropOfno) . ': ' . date('m-d-Y', strtotime($dispatch['pd_date'])) . '<br> @ '. $dispatch['pd_time'] . '';
												} 
												echo '<br>';
												$dropOfno++;
											}
										}
									}
									?>
									</strong>
								</td> 
								<td>
									<?php
									$dropOffCount = 1; 
									if (isset($key['dispatchInfo']) && is_array($key['dispatchInfo'])) {
										foreach ($key['dispatchInfo'] as $dispatch) {
											if ($dispatch['pd_type'] == 'dropoff' && !strstr($dispatch['pd_date'], '0000') && $dispatch['pd_date'] != '') {
												$dropOffCount++; 
											}
										}
									}
								 if ($dropOffCount > 1) {
									if(array_key_exists($key['pd_addressid'],$comAddArr)  && $key['pd_addressid'] > 0){ 
										echo 'D-1: '. $comAddArr[$key['pd_addressid']][0].'<br> ['.$comAddArr[$key['pd_addressid']][1].']';
									} elseif(array_key_exists($key['daddressid'],$comAddArr)  && $key['daddressid'] > 0){ 
										echo 'D-1: '. $comAddArr[$key['daddressid']][0].'<br> ['.$comAddArr[$key['daddressid']][1].']';
									} else {
										if(array_key_exists($key['pd_location'],$locationArr) && $key['pd_location']!=''){ 
											echo 'D-1: ' .$locationArr[$key['pd_location']]; 
										}
										elseif(array_key_exists($key['dlocation'],$locationArr)){ 
											echo 'D-1: ' . $locationArr[$key['dlocation']]; 
										}
										if(array_key_exists($key['pd_city'],$cityArr) && $key['pd_city']!=''){ 
											echo '<br> ['.$cityArr[$key['pd_city']].']'; 
										}
										elseif(array_key_exists($key['dcity'],$cityArr)){ 
											echo '<br> ['.$cityArr[$key['dcity']].']'; 
										}
									}
								 }else{
									if(array_key_exists($key['pd_addressid'],$comAddArr)  && $key['pd_addressid'] > 0){ 
										echo ''. $comAddArr[$key['pd_addressid']][0].'<br> ['.$comAddArr[$key['pd_addressid']][1].']';
									} elseif(array_key_exists($key['daddressid'],$comAddArr)  && $key['daddressid'] > 0){ 
										echo ''. $comAddArr[$key['daddressid']][0].'<br> ['.$comAddArr[$key['daddressid']][1].']';
									} else {
										if(array_key_exists($key['pd_location'],$locationArr) && $key['pd_location']!=''){ 
											echo '' .$locationArr[$key['pd_location']]; 
										}
										elseif(array_key_exists($key['dlocation'],$locationArr)){ 
											echo '' . $locationArr[$key['dlocation']]; 
										}
										if(array_key_exists($key['pd_city'],$cityArr) && $key['pd_city']!=''){ 
											echo '<br> ['.$cityArr[$key['pd_city']].']'; 
										}
										elseif(array_key_exists($key['dcity'],$cityArr)){ 
											echo '<br> ['.$cityArr[$key['dcity']].']'; 
										}
									}
								 }  	
									
									echo '<br>';
									
									if (isset($key['dispatchInfo']) && is_array($key['dispatchInfo'])) {
										$dropOfno=2;
										foreach ($key['dispatchInfo'] as $index => $dispatch) {
											//  print_r($dispatch);exit;
											if($dispatch['pd_type']=='dropoff'){
												
												if (array_key_exists($dispatch['pd_addressid'], $comAddArr) && $dispatch['pd_addressid'] > 0) {
													echo 'D-' . ($dropOfno) . ': ' . $comAddArr[$dispatch['pd_addressid']][0] . ' <br>[' . $comAddArr[$dispatch['pd_addressid']][1] . ']';
												}
												elseif (array_key_exists($dispatch['daddressid'], $comAddArr) && $dispatch['daddressid'] > 0) {
													echo 'D-' . ($dropOfno) . ': ' . $comAddArr[$dispatch['daddressid']][0] . '<br> [' . $comAddArr[$dispatch['daddressid']][1] . ']';
												} 
												else {
													if (array_key_exists($dispatch['pd_location'], $locationArr) && $dispatch['pd_location'] != '') {
														echo 'D-' . ($dropOfno) . ': ' . $locationArr[$dispatch['pd_location']];
													}
													elseif (array_key_exists($dispatch['dlocation'], $locationArr)) {
														echo 'D-' . ($dropOfno) . ': ' . $locationArr[$dispatch['dlocation']];
													}
													if (array_key_exists($dispatch['pd_city'], $cityArr) && $dispatch['pd_city'] != '') {
														echo '<br> [' . $cityArr[$dispatch['pd_city']] . ']';
													}
													elseif (array_key_exists($dispatch['dcity'], $cityArr)) {
														echo '<br> [' . $cityArr[$dispatch['dcity']] . ']';
													} 
												}
												echo '<br>';
												$dropOfno++;
											}
										}
									}
									?>
								</td>
						
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['rate'] > 0) { echo '$'; } echo '<span class="c_rate_txt_'.$key['id'].' '.$rateTxt.'">'.number_format($key['rate'],2).'</span>';?> &nbsp; <i class="fas fa-edit d-none" data-id="<?php echo $key['id'];?>" title="Edit" alt="Edit"></i> </span>

									<!-- total carrier rate of parent and child invoice -->
									<!-- <span class="td-txt td-txt-<?php echo $key['id']; ?>">
										<?php
										if ($parentChildRate > 0) echo '$';
										?>
										<span class="c_rate_txt_<?php echo $key['id']; echo $rateTxt; ?> <?php echo $rateTxt; ?>">
											<?php echo $parentChildRate; ?>
										</span> &nbsp;
										<i class="fas fa-edit d-none" data-id="<?php echo $key['id']; ?>" title="Edit"></i>
									</span> -->
									<!-- total carrier rate of parent and child invoice -->

									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_rate_input_<?php echo $key['id'];?> current_input" data-id="#rate_input" value="<?php echo $key['rate'];?>" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['parate'] > 0) { echo '$'; } echo '<span class="c_parate_txt_'.$key['id'].' '.$paRateTxt.'">'.number_format($key['parate'],2).'</span>';?> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>

									<!-- total invoice amount of parent and child invoice -->
									<!-- <span class="td-txt td-txt-<?php echo $key['id']; ?>">
										<?php
										if ($parentChildPaRate > 0) echo '$';
										?>
										<span class="c_parate_txt_<?php echo $key['id']; echo $paRateTxt; ?> <?php echo $paRateTxt; ?>">
											<?php echo $parentChildPaRate; ?>
										</span> &nbsp;
										<i class="fas fa-edit d-none" data-id="<?php echo $key['id']; ?>" title="Edit"></i>
									</span> -->
									<!-- total invoice amount of parent and child invoice -->
									
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_parate_input_<?php echo $key['id'];?> current_input" data-id="#parate_input" value="<?php echo $key['parate'];?>" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td>  
								
								<td><?php 
									/*if(!empty($companies)){
										foreach($companies as $val){
											if($key['company']==$val['id']) { echo $val['company']; }
										}
									}*/
									if(array_key_exists($key['company'],$companyArr)){ 
										echo $companyArr[$key['company']]; 
									}
								?></td>  
								<td><?php 
									$tCompany = $bookedUnder = '';
									if(!empty($truckingCompanies)){
										foreach($truckingCompanies as $val){
											if($key['truckingCompany']==$val['id']) { 
												echo $val['company'].'<br>'; 
												if(!empty($val['mc'])){
													echo '<a href="' . base_url('admin/trucking-company/update/' . $val['id']) . '" target="_blank">[MC # '  . $val['mc'] . ']</a>'; 
												}else{
													echo '<a href="' . base_url('admin/trucking-company/update/' . $val['id']) . '" target="_blank">[DOT # '  . $val['dot'] . ']</a>'; 
												}
												
											}
											if($key['bookedUnder']==$val['id']) { $bookedUnder = $val['company']; }
										}
									}
								?></td>
								<td class="">
									<?php 
									if($key['dispatchMeta'] != ''){
									$dispatchMeta = json_decode($key['dispatchMeta'],true);
									if($dispatchMeta['dispatchInfo']){
									    foreach($dispatchMeta['dispatchInfo'] as $disInfo){
									        echo ''.$disInfo[0].':';
									        if($disInfo[0]=='Vessel / Voyage'){ echo '<br>'; }
									        else { echo '&nbsp;'; }
									        echo ''.$disInfo[1].'<br>';
									    }
									}
									echo  '<span class="hide d-none">';print_r($dispatchMeta['dispatchInfo']);print_r($dispatchMeta['expense']); echo '</span>';
									} else {
									echo  '<span class="hide d-none">'.$key['dispatchMeta'].'</span>'; 
									} ?>
								</td>
								<!--/td>
								<td-->
								<div class="hide d-none">
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_trailer_txt_<?php echo $key['id'];?>"><?php echo str_replace(',',', ',$key['trailer']);?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_trailer_input_<?php echo $key['id'];?> current_input" data-id="#trailer_input" value="<?php echo $key['trailer'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span></div>
								</td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_tracking_txt_<?php echo $key['id'];?>"><?php echo $key['tracking'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_tracking_input_<?php echo $key['id'];?> current_input" data-id="#tracking_input" value="<?php echo $key['tracking'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td class="nowrap">
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_invoice_txt_<?php echo $key['id'];?>"><?php echo $key['invoice'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_invoice_input_<?php echo $key['id'];?> current_input" data-id="#invoice_input" value="<?php echo $key['invoice'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td align="center" bgcolor="<?php if($key['bol']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php //if($key['bol'] !='') { echo 'Yes'; }?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_bol_txt_<?php echo $key['id'];?>"><?php if($key['bol'] !='') { echo 'Yes'; }?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="checkbox" <?php if($key['bol']=='AK') { echo 'checked'; } ?> class="c_bol_input_<?php echo $key['id'];?> current_checkbox" data-id="#bol_input" value="AK">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td align="center" bgcolor="<?php if($key['rc']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php //echo $key['rc'];?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_rc_txt_<?php echo $key['id'];?>"><?php if($key['rc']=='AK'){ echo 'Yes'; } ?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="checkbox" <?php if($key['rc']=='AK') { echo 'checked'; } ?> class="c_rc_input_<?php echo $key['id'];?> current_checkbox" data-id="#rc_input" value="AK">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td class="nowrap" bgcolor="<?php //if($key['carrierPayoutCheck']=='1' || $dispatchMeta['carrierInvoiceCheck']=='1') { echo '#73ac4d'; } ?>">
									<?php 
									if($key['carrierPayoutCheck']=='1' || $dispatchMeta['carrierInvoiceCheck']=='1') { echo 'Yes<br>'; } 
									
									$invoiceType = '';
									$carrierInvoiceType = '';
									// if($key['invoiceType']=='Direct Bill'){ $invoiceType = 'DB'; $aDays = 30; }
									// elseif($key['invoiceType']=='Quick Pay'){ $invoiceType = 'QP'; $aDays = 7; }
									// elseif($key['invoiceType']=='RTS'){ $invoiceType = 'RTS'; $aDays = 3; }
									$caDays = 0;
									if($key['carrierPaymentType']==''){ $carrierInvoiceType = ''; $caDays = 30; }
									if($key['carrierPaymentType']=='Direct Bill'){ $carrierInvoiceType = 'DB'; $caDays = 30; }
									if($key['carrierPaymentType']=='Standard Billing'){ $carrierInvoiceType = 'SB'; $caDays = 30; }
									elseif($key['carrierPaymentType']=='Quick Pay'){ $carrierInvoiceType = 'QP'; $caDays = 3; }
									elseif($key['carrierPaymentType']=='Zelle'){ $carrierInvoiceType = 'Zelle'; $caDays = 1; }
									elseif($key['carrierPaymentType']=='Factoring'){ $carrierInvoiceType = 'Facto'; $caDays = 30; }

									$caging = '';
									if($dispatchMeta['custInvDate'] != '') {  
										echo ' Inv.&nbsp;Date:&nbsp; '.date('m-d-Y',strtotime($dispatchMeta['custInvDate'])).'<br>';
										// echo 'Inv.&nbsp;Date:&nbsp;'.date('m-d-Y',strtotime($dispatchMeta['custInvDate'])).'<br>'; 
										$cdate1 = new DateTime($dispatchMeta['custInvDate']);
										$cdate2 = new DateTime(date('Y-m-d'));
										$cdiff = $cdate1->diff($cdate2);
										$caging = $cdiff->days;
									}
									if($key['bookedUnder']!=4 && $key['bookedUnderNew']!=4){
										if($dispatchMeta['custDueDate'] != '') { 
											echo ' Due&nbsp;Date:&nbsp; '.date('m-d-Y',strtotime($dispatchMeta['custDueDate'])).'<br>';
											// echo 'Due&nbsp;Date:&nbsp;'.date('m-d-Y',strtotime($dispatchMeta['custDueDate'])).'<br>';
										 }
								
									
									
									if(!strstr($key['carrierPayoutDate'],'0000')) {
										echo ' Payment&nbsp;Date:&nbsp; '.date('m-d-Y',strtotime($key['carrierPayoutDate'])).'<br>';
									    // echo 'Payment&nbsp;Date:&nbsp;'.date('m-d-Y',strtotime($key['carrierPayoutDate'])).'<br>'; 
									    if($dispatchMeta['custInvDate'] != '') { 
									        $cdate11 = new DateTime($dispatchMeta['custInvDate']);
										    $cdate21 = new DateTime($key['carrierPayoutDate']);
										    $cdiff = $cdate11->diff($cdate21);
										    $cpDays = $cdiff->days;
										    echo '<strong>Payment&nbsp;Days:&nbsp;'.$cpDays.'  Days</strong><br>'; 
									    }
									}
									elseif($dispatchMeta['custInvDate'] != '') { 
										if($caging  > $caDays && $caDays > 0) {
											echo '<strong style="color:red; color:red;font-weight: 800;">Inv.&nbsp;Aging: '.$caging.' Days</strong><br>';

										} else {
											echo 'Inv. Aging: '.$caging.' Days<br>';
										}
									} 
								}
									?>
								</td>
								<td class="nowrap">
									<?php 
									$aDays = 0;
									$showAging = 'false';
									if($key['invoiceType']=='Direct Bill'){ $invoiceType = 'DB'; $aDays = 30; }
									elseif($key['invoiceType']=='Quick Pay'){ $invoiceType = 'QP'; $aDays = 7; }
									elseif($key['invoiceType']=='RTS'){ $invoiceType = 'RTS'; $aDays = 3; }
									if($dispatchMeta['invoiceReadyDate'] != '') { echo $invoiceType.' R&#8203;ea&#8203;dy: '.date('m-d-Y',strtotime($dispatchMeta['invoiceReadyDate'])); }
									if($key['invoiceDate'] != '0000-00-00') { $showAging = 'true'; echo '<br>'.$invoiceType.' I&#8203;nvo&#8203;iced: '.date('m-d-Y',strtotime($key['invoiceDate'])); }
									if($dispatchMeta['invoicePaidDate'] != '') { $showAging = 'false'; echo '<br>'.$invoiceType.' P&#8203;ai&#8203;d: '.date('m-d-Y',strtotime($dispatchMeta['invoicePaidDate'])); }
									if($dispatchMeta['invoiceCloseDate'] != '') { $showAging = 'close'; echo '<br>'.$invoiceType.' C&#8203;lo&#8203;se&#8203;d: '.date('m-d-Y',strtotime($dispatchMeta['invoiceCloseDate'])); }
									if($showAging == 'true'){
									    $date1 = new DateTime($key['invoiceDate']);
										$date2 = new DateTime(date('Y-m-d'));
										$diff = $date1->diff($date2);
										$aging = $diff->days;
									$invoiceType = '';
									
									    if($aging  > $aDays && $aDays > 0) {
											echo '<br><strong style="color:red; color:red;font-weight: 800;"">Inv. Aging: '.$aging.' Days</strong>';
										} else {
											echo '<br>Inv. Aging: '.$aging.' Days';
										}
									}
									if($showAging == 'close'){
									    $date1 = new DateTime($key['invoiceDate']);
										$date2 = new DateTime($dispatchMeta['invoicePaidDate']);
										$diff = $date1->diff($date2);
										$aging = $diff->days;
										echo '<br><strong>Payment&nbsp;Days: '.$aging.' Days</strong>';
									}
										
									?>
								</td>
								<td class="d-none">
									<?php //echo $key['driver_status']; ?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_driver_status_txt_<?php echo $key['id'];?>"><?php echo $key['driver_status'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<select name="driver_status" class="c_driver_status_input_<?php echo $key['id'];?> current_change" data-id="#driver_status_input">
											<?php
											foreach($shipmentStatus as $ds){
												echo '<option value="'.$ds['title'].'"';
												if($key['driver_status'] == $ds['title']) { echo ' selected'; }
												echo '>'.$ds['title'].'</option>';
											}
											?>
										</select>
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_status_txt_<?php echo $key['id'];?>"><?php echo $key['status'];?></span> &nbsp; <i class="fas fa-edit" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="text" class="c_status_input_<?php echo $key['id'];?> current_input" data-id="#status_input" value="<?php echo $key['status'];?>">
										<i class="fa fa-paper-plane" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/outside-dispatch/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
									
									<a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/outside-dispatch/add/'.$key['id'];?>">Duplicate</a>
									
									<a class="btn btn-sm btn-danger delete-tr pt-cta" href="#" data-cls=".tr-<?php echo $key['id'];?>" data-id="<?php echo $key['id'];?>" data-toggle="modal" data-target="#deleteModal">Delete</a>
									
								</td>
							</tr> 
							<?php
							$n++;
							// }
								
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
							<td></td>
							<td></td>
							<td></td>
							<td></td>
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
<div id="subInvoicesModal" class="modal fade" role="dialog" style="z-index: 9;">
	<div class="modal-dialog modal-lg" style="max-width: 1200px;">
		<div class="modal-content" style="max-width: 100%;width: 100%;">
			<div class="modal-header">
				<button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Sub Invoice</h4>
			</div>
			<div class="modal-body table-responsive">
				<table class="table table-bordered" id="subInvoicesTable" width="100%" cellspacing="0">
					<!-- <thead></thead> -->
					<thead style="position:sticky;top:0">
                    <tr  class="thead">
						<th style="min-width: 56px;">Sr #</th>
						<th style="min-width: 225px;">PU Date & Time</th>
						<th style="min-width: 300px;">PU Info</th>
						<th style="min-width: 225px;">Del Date & Time</th>
						<th style="min-width: 300px;">Del Info</th>
						<th style="min-width: 198px;">Carrier Rate</th>
						<th style="min-width: 198px;">Inv Amount</th>
						<th style="min-width: 300px;">Company</th>
						<th style="min-width: 300px;">Carrier</th>
						<th style="min-width: 262px;">Dispatch Info</th>
						<th style="min-width: 198px;">Tracking / PO #</th>
						<th style="min-width: 198px;">Invoice #</th>
						<th style="min-width: 250px;">Shipment Status</th> 
						<th style="min-width: 400px;">Shipment Notes</th>
                        <th style="min-width: 220px;">Action</th>
					</tr> 
				</thead>
					<tbody>
					
					</tbody>
				</table>
			</div> 
		</div>
	</div>
</div>

<div id="invoiceModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" style="max-width: 1200px;">
		<div class="modal-content" style="max-width: 100%;width: 100%;">
			<div class="modal-header">
				<button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Sub Invoice</h4>
			</div>
			<div class="modal-body table-responsive">
				<table class="table table-bordered" id="invoiceTable" width="100%" cellspacing="0">
					<thead></thead>
					<tbody>
						<?php
						$js = '';
						if(!empty($dispatchOutside)){
							$n=1; $rate = $parate = 0;
							foreach($dispatchOutside as $key) {
							
								if($key['parentInvoice'] == ''){ continue; }
								
								if($n < 16) {
									$rate = $rate + $key['rate'];
									$parate = $parate + $key['parate'];
								}
								$dispatchMeta = json_decode($key['dispatchMeta'],true);
								
								$cls = 'parInvCls';
								if($key['parentInvoice'] != ''){
								    $cls .= str_replace(' ','',$key['parentInvoice']).' childTR';
								}
							?>
							<tr class="tr-<?php echo $key['id'].' '.$cls;?>" <?php if($key['parentInvoice'] != ''){ echo 'style="display:none"'; } ?>> 
								<td class="srno"><?php echo $n; //if($key['parentInvoice'] == ''){ echo $n; } else { echo '>>'; } ?></td>
								<td>
									<strong><?php echo date('m-d-Y',strtotime($key['pudate']));?> @ <?=$key['ptime']?></strong>
									<?php if($key['childInvoice'] != ''){
								        echo '<br><br><a href="#" class="showChildInv btn btn-sm btn-success pt-cta" data-trcls=".parInvCls'.str_replace(' ','',$key['invoice']).'" data-cls=".subInvTr'.str_replace(' ','',$key['invoice']).'" data-toggle="modal" data-target="#invoiceModal">Sub Inv.</a>';
								    } ?>
								</td> 
								<td><a href="<?php echo base_url().'admin/outside-dispatch/update/'.$key['id'];?>"><?php
									if(array_key_exists($key['paddressid'],$comAddArr)  && $key['paddressid'] > 0){ 
										echo $comAddArr[$key['paddressid']][0].' ['.$comAddArr[$key['paddressid']][1].']';
									} else {
										if(array_key_exists($key['plocation'],$locationArr)){ 
											echo $locationArr[$key['plocation']]; 
										}
										if(array_key_exists($key['pcity'],$cityArr)){ 
											echo ' ['.$cityArr[$key['pcity']].']'; 
										}
									}
									/*if(!empty($locations)){
										foreach($locations as $val){
											if($key['plocation']==$val['id']) { echo $val['location']; }
										}
									}
									if(!empty($cities)){
										foreach($cities as $val){
											if($key['pcity']==$val['id']) { echo ' ['.$val['city'].']'; }
										}
									}*/
								?></a></td>
								<td><strong><?php 
								    if(!strstr($key['pd_date'],'0000') && $key['pd_date']!='') { echo date('m-d-Y',strtotime($key['pd_date'])).' @ '.$key['pd_time']; } 
									elseif(strstr($key['dodate'],'0000')) {} 
								    else { echo date('m-d-Y',strtotime($key['dodate'])).' @ '.$key['dtime']; } ?></strong>
								</td> 
								<td><?php
								if(array_key_exists($key['pd_addressid'],$comAddArr)  && $key['pd_addressid'] > 0){ 
									echo $comAddArr[$key['pd_addressid']][0].' ['.$comAddArr[$key['pd_addressid']][1].']';
								} elseif(array_key_exists($key['daddressid'],$comAddArr)  && $key['daddressid'] > 0){ 
									echo $comAddArr[$key['daddressid']][0].' ['.$comAddArr[$key['daddressid']][1].']';
								} else {
									if(array_key_exists($key['pd_location'],$locationArr) && $key['pd_location']!=''){ 
										echo $locationArr[$key['pd_location']]; 
									}
									elseif(array_key_exists($key['dlocation'],$locationArr)){ 
										echo $locationArr[$key['dlocation']]; 
									}
									if(array_key_exists($key['pd_city'],$cityArr) && $key['pd_city']!=''){ 
										echo ' ['.$cityArr[$key['pd_city']].']'; 
									}
									elseif(array_key_exists($key['dcity'],$cityArr)){ 
										echo ' ['.$cityArr[$key['dcity']].']'; 
									}
								}
									/*if(!empty($locations)){
										foreach($locations as $val){
											//if($key['dlocation']==$val['id']) { echo $val['location']; }
											if($key['dlocation']==$val['id'] && $key['pd_location']=='') { echo $val['location']; }
											elseif($key['pd_location']==$val['id'] && $key['pd_location']!='') { echo $val['location']; }
										}
									}
									
									if(!empty($cities)){
										foreach($cities as $val){
											//if($key['dcity']==$val['id']) { echo ' ['.$val['city'].']'; }
											if($key['dcity']==$val['id'] && $key['pd_city']=='') { echo ' ['.$val['city'].']'; }
											elseif($key['pd_city']==$val['id'] && $key['pd_city']!='') { echo ' ['.$val['city'].']'; }
										}
									}*/
								?></td>
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['rate'] > 0) { echo '$'; } echo '<span class="c_rate_txt_'.$key['id'].' rateTxt">'.$key['rate'].'</span>';?> &nbsp; <i class="fas fa-edit d-none" data-id="<?php echo $key['id'];?>" title="Edit" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_rate_input_<?php echo $key['id'];?> current_input" data-id="#rate_input" value="<?php echo $key['rate'];?>" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['parate'] > 0) { echo '$'; } echo '<span class="c_parate_txt_'.$key['id'].' paRateTxt">'.$key['parate'].'</span>';?> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_parate_input_<?php echo $key['id'];?> current_input" data-id="#parate_input" value="<?php echo $key['parate'];?>" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td>  
								
								<td><?php 
									/*if(!empty($companies)){
										foreach($companies as $val){
											if($key['company']==$val['id']) { echo $val['company']; }
										}
									}*/
									if(array_key_exists($key['company'],$companyArr)){ 
										echo $companyArr[$key['company']]; 
									}
								?></td>  
								<td><?php 
									$tCompany = $bookedUnder = '';
									if(!empty($truckingCompanies)){
										foreach($truckingCompanies as $val){
											if($key['truckingCompany']==$val['id']) { echo $val['company']; }
											if($key['bookedUnder']==$val['id']) { $bookedUnder = $val['company']; }
										}
									}
								?></td>
								<td>
									<?php 
									if($key['dispatchMeta'] != ''){
									$dispatchMeta = json_decode($key['dispatchMeta'],true);
									if($dispatchMeta['dispatchInfo']){
									    foreach($dispatchMeta['dispatchInfo'] as $disInfo){
									        echo ''.$disInfo[0].':&nbsp;'.$disInfo[1].'<br>';
									    }
									}
									echo  '<span class="hide d-none">';print_r($dispatchMeta['dispatchInfo']);print_r($dispatchMeta['expense']); echo '</span>';
									} else {
									echo  '<span class="hide d-none">'.$key['dispatchMeta'].'</span>'; 
									} ?>
								<!--/td>
								<td-->
								<div class="hide d-none">
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_trailer_txt_<?php echo $key['id'];?>"><?php echo str_replace(',',', ',$key['trailer']);?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_trailer_input_<?php echo $key['id'];?> current_input" data-id="#trailer_input" value="<?php echo $key['trailer'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span></div>
								</td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_tracking_txt_<?php echo $key['id'];?>"><?php echo $key['tracking'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_tracking_input_<?php echo $key['id'];?> current_input" data-id="#tracking_input" value="<?php echo $key['tracking'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><a href="<?php echo base_url().'admin/outside-dispatch/update/'.$key['id'];?>"><span class="c_invoice_txt_<?php echo $key['id'];?>"><?php echo $key['invoice'];?></span></a> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_invoice_input_<?php echo $key['id'];?> current_input" data-id="#invoice_input" readonly value="<?php echo $key['invoice'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td align="center" bgcolor="<?php if($key['carrierPayoutCheck']=='1' || $dispatchMeta['carrierInvoiceCheck']=='1') { echo '#73ac4d'; } ?>">
									<?php if($key['carrierPayoutCheck']=='1' || $dispatchMeta['carrierInvoiceCheck']=='1') { echo 'Yes<br>'; } ?>
									<?php if(!strstr($key['carrierPayoutDate'],'0000')) { echo date('m-d-Y',strtotime($key['carrierPayoutDate'])); } ?>
								</td>
								
								<td bgcolor="<?php if($key['bol']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php //if($key['bol'] !='') { echo 'Yes'; }?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_bol_txt_<?php echo $key['id'];?>"><?php if($key['bol'] !='') { echo 'Yes'; }?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="checkbox" <?php if($key['bol']=='AK') { echo 'checked'; } ?> class="c_bol_input_<?php echo $key['id'];?> current_checkbox" data-id="#bol_input" value="AK">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td bgcolor="<?php if($key['rc']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php //echo $key['rc'];?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_rc_txt_<?php echo $key['id'];?>"><?php if($key['rc']=='AK'){ echo 'Yes'; } ?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="checkbox" <?php if($key['rc']=='AK') { echo 'checked'; } ?> class="c_rc_input_<?php echo $key['id'];?> current_checkbox" data-id="#rc_input" value="AK">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<?php 
									
									$invoiceType = '';
									if($key['invoiceType']=='Direct Bill'){ $invoiceType = 'DB'; }
									elseif($key['invoiceType']=='Quick Pay'){ $invoiceType = 'QP'; }
									elseif($key['invoiceType']=='RTS'){ $invoiceType = 'RTS'; }
									
									if($dispatchMeta['invoiceReadyDate'] != '') { echo $invoiceType.' R&#8203;ea&#8203;dy:<br>'.date('m-d-Y',strtotime($dispatchMeta['invoiceReadyDate'])); }
									if($key['invoiceDate'] != '0000-00-00') { echo '<br>'.$invoiceType.' I&#8203;nvo&#8203;iced:<br>'.date('m-d-Y',strtotime($key['invoiceDate'])); }
									if($dispatchMeta['invoicePaidDate'] != '') { echo '<br>'.$invoiceType.' P&#8203;ai&#8203;d:<br>'.date('m-d-Y',strtotime($dispatchMeta['invoicePaidDate'])); }
									if($dispatchMeta['invoiceCloseDate'] != '') { echo '<br>'.$invoiceType.' C&#8203;lo&#8203;se&#8203;d:<br>'.date('m-d-Y',strtotime($dispatchMeta['invoiceCloseDate'])); }
									?>
								</td>
								<td>
									<?php //echo $key['driver_status']; ?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_driver_status_txt_<?php echo $key['id'];?>"><?php echo $key['driver_status'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<select name="driver_status" class="c_driver_status_input_<?php echo $key['id'];?> current_change" data-id="#driver_status_input">
											<?php
											foreach($shipmentStatus as $ds){
												echo '<option value="'.$ds['title'].'"';
												if($key['driver_status'] == $ds['title']) { echo ' selected'; }
												echo '>'.$ds['title'].'</option>';
											}
											?>
										</select>
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_status_txt_<?php echo $key['id'];?>"><?php echo $key['status'];?></span> &nbsp; <i class="fas fa-edit" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="text" class="c_status_input_<?php echo $key['id'];?> current_input" data-id="#status_input" value="<?php echo $key['status'];?>">
										<i class="fa fa-paper-plane  d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/outside-dispatch/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
									
									<a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/outside-dispatch/add/'.$key['id'];?>">Duplicate</a>
									
									<a class="btn btn-sm btn-danger delete-tr pt-cta" href="#" data-cls=".tr-<?php echo $key['id'];?>" data-id="<?php echo $key['id'];?>" data-toggle="modal" data-target="#deleteModal">Delete</a>
									
								</td>
							</tr> 
							<?php
								$n++;
							}
						}
						/*
						if(!empty($dispatch)){
							$n=1; $rate = $parate = 0;
							foreach($dispatch as $key) {
							
								if($key['parentInvoice'] == ''){ continue; }
								
								if($n < 16) {
									$rate = $rate + $key['rate'];
									$parate = $parate + $key['parate'];
								}
								$dispatchMeta = json_decode($key['dispatchMeta'],true);
								$cls = 'parInvCls';
								
								if($key['parentInvoice'] != ''){
								    $cls .= str_replace(' ','',$key['parentInvoice']).' childTR';
								}
							?>
							<tr class="tr-<?php echo $key['id'].' '.$cls;?>" <?php if($key['parentInvoice'] != ''){ echo 'style="display:none"'; } ?>>
								<td class="srno"><?php echo $n; //if($key['parentInvoice'] == ''){ echo $n; } else { echo '>>'; } ?></td>
								<td>
								    <strong><?php echo date('m-d-Y',strtotime($key['pudate']));?> @ <?=$key['ptime']?></strong>
								    <?php if($key['childInvoice'] != ''){
								        echo '<br><br><a href="#" class="showChildInv btn btn-sm btn-success" data-trcls=".parInvCls'.str_replace(' ','',$key['invoice']).'" data-cls=".subInvTr'.str_replace(' ','',$key['invoice']).'" data-toggle="modal" data-target="#invoiceModal">Sub Inv.</a>';
								    } ?>
							    </td> 
								<td><a href="<?php echo base_url().'admin/dispatch/update/'.$key['id'];?>"><?php
									if(!empty($locations)){
										foreach($locations as $val){
											if($key['plocation']==$val['id']) { echo $val['location']; }
										}
									}
									if(!empty($cities)){
										foreach($cities as $val){
											if($key['pcity']==$val['id']) { echo ' ['.$val['city'].']'; }
										}
									}
								?></a>
								</td>
								<td>
								<strong>
								<?php 
									if(!strstr($key['pd_date'],'0000') && $key['pd_date']!='') { echo date('m-d-Y',strtotime($key['pd_date'])).' @ '.$key['pd_time']; } 
									elseif(strstr($key['dodate'],'0000')) {} 
									else { echo date('m-d-Y',strtotime($key['dodate'])).' @ '.$key['dtime']; } 
									?></strong>
									</td> 
								<td><?php
									if(!empty($locations)){
										foreach($locations as $val){
											if($key['dlocation']==$val['id'] && $key['pd_location']=='') { echo $val['location']; }
											elseif($key['pd_location']==$val['id'] && $key['pd_location']!='') { echo $val['location']; }
										}
									}
									if(!empty($cities)){
										foreach($cities as $val){
											if($key['dcity']==$val['id'] && $key['pd_city']=='') { echo ' ['.$val['city'].']'; }
											elseif($key['pd_city']==$val['id'] && $key['pd_city']!='') { echo ' ['.$val['city'].']'; }
										}
									}
								?></td>
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['rate'] > 0) { echo '$'; } echo '<span class="c_rate_txt_'.$key['id'].' rateTxt">'.$key['rate'].'</span>';?> &nbsp; <i class="fas fa-edit d-none" data-id="<?php echo $key['id'];?>" title="Edit" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_rate_input_<?php echo $key['id'];?> current_input" data-id="#rate_input" value="<?php echo $key['rate'];?>" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['parate'] > 0) { echo '$'; } echo '<span class="c_parate_txt_'.$key['id'].' paRateTxt">'.$key['parate'].'</span>';?> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_parate_input_<?php echo $key['id'];?> current_input" data-id="#parate_input" value="<?php echo $key['parate'];?>" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td>  
								
								<td><?php //echo $key['company'];
									if(!empty($companies)){
										foreach($companies as $val){
											if($key['company']==$val['id']) { echo $val['company']; }
										}
									}
								?></td> 
								<td><?php //echo $key['trailer'];?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_trailer_txt_<?php echo $key['id'];?>"><?php echo str_replace(',',', ',$key['trailer']);?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_trailer_input_<?php echo $key['id'];?> current_input" data-id="#trailer_input" value="<?php echo $key['trailer'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td><?php //echo $key['tracking'];?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_tracking_txt_<?php echo $key['id'];?>"><?php echo $key['tracking'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_tracking_input_<?php echo $key['id'];?> current_input" data-id="#tracking_input" value="<?php echo $key['tracking'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td><?php //echo $key['invoice'];?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_invoice_txt_<?php echo $key['id'];?>"><?php echo $key['invoice'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_invoice_input_<?php echo $key['id'];?> current_input" data-id="#invoice_input" value="<?php echo $key['invoice'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td bgcolor="<?php if($key['bol']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php //if($key['bol'] !='') { echo 'Yes'; }?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_bol_txt_<?php echo $key['id'];?>"><?php if($key['bol'] !='') { echo 'Yes'; }?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="checkbox" <?php if($key['bol']=='AK') { echo 'checked'; } ?> class="c_bol_input_<?php echo $key['id'];?> current_checkbox" data-id="#bol_input" value="AK">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td bgcolor="<?php if($key['rc']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php //echo $key['rc'];?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_rc_txt_<?php echo $key['id'];?>"><?php if($key['rc']=='AK'){ echo 'Yes'; } ?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="checkbox" <?php if($key['rc']=='AK') { echo 'checked'; } ?> class="c_rc_input_<?php echo $key['id'];?> current_checkbox" data-id="#rc_input" value="AK">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<?php 
									$invoiceType = '';
									if($key['invoiceType']=='Direct Bill'){ $invoiceType = 'DB'; }
									elseif($key['invoiceType']=='Quick Pay'){ $invoiceType = 'QP'; }
									elseif($key['invoiceType']=='RTS'){ $invoiceType = 'RTS'; }
									
									if($dispatchMeta['invoiceReadyDate'] != '') { echo $invoiceType.' Ready:<br>'.date('m-d-Y',strtotime($dispatchMeta['invoiceReadyDate'])); }
									if($key['invoiceDate'] != '0000-00-00') { echo '<br>'.$invoiceType.' Invoiced:<br>'.date('m-d-Y',strtotime($key['invoiceDate'])); }
									if($dispatchMeta['invoicePaidDate'] != '') { echo '<br>'.$invoiceType.' Paid:<br>'.date('m-d-Y',strtotime($dispatchMeta['invoicePaidDate'])); }
									if($dispatchMeta['invoiceCloseDate'] != '') { echo '<br>'.$invoiceType.' Closed:<br>'.date('m-d-Y',strtotime($dispatchMeta['invoiceCloseDate'])); }
									?>
								</td>
								<td>
									<?php //echo $key['driver_status']; ?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_driver_status_txt_<?php echo $key['id'];?>"><?php echo $key['driver_status'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<select name="driver_status" class="c_driver_status_input_<?php echo $key['id'];?> current_change" data-id="#driver_status_input">
											<?php
											foreach($shipmentStatus as $ds){
												echo '<option value="'.$ds['title'].'"';
												if($key['driver_status'] == $ds['title']) { echo ' selected'; }
												echo '>'.$ds['title'].'</option>';
											}
											?>
										</select>
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_status_txt_<?php echo $key['id'];?>"><?php echo $key['status'];?></span> &nbsp; <i class="fas fa-edit <?php if($key['lockDispatch']=='1') { echo 'd-none'; } ?>" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>"> 
										<input type="text" class="c_status_input_<?php echo $key['id'];?> current_input" data-id="#status_input" value="<?php echo $key['status'];?>">
										<i class="fa fa-paper-plane" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/dispatch/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
									
									<a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/dispatch/add/'.$key['id'];?>">Duplicate</a>
									
									<a class="btn btn-sm btn-danger delete-tr" href="#" data-cls=".tr-<?php echo $key['id'];?>" data-id="<?php echo $key['id'];?>" data-toggle="modal" data-target="#deleteModal">Delete</a>
								</td>
							</tr> 
							<?php 
							$n++; 
							}
						}
						*/
					?>
					</tbody>
				</table>
			</div> 
		</div>
	</div>
</div>


<div id="deleteModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Dispatch</h4>
			</div>
			<div class="modal-body">
				<form class="form" method="post" action="" id="deleteform">
					<div class="alert alert-success status-success-msg" style="display:none">Please wait deleting....</div>
					<p><strong>Are you sure delete this dispatch ?</strong>
						<input type="hidden" name="ajaxdelete" class="form-control" value="true" required>
					<input type="hidden" name="deleteid" id="deleteid-input" value="" required></p>
					<p> 
						<input type="submit" name="cdelete" id="sdelete-input" class="btn btn-danger" value="Delete">
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

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<!-- <link href="<?php echo base_url().'assets/css/5.3.0bootstrap.min.css'; ?>" rel="stylesheet"> -->
<!-- <script src="<?php echo base_url().'assets/js/5.3.0bootstrap.bundle.min.js'; ?>"></script> -->
<script>
   $(document).ready(function() {
      $('.select2').select2();
   });
</script>
<script>
	$( function() {
		$( ".datepicker" ).datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
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
		
		var rowsHead = $('.thead').clone();
		// $('#invoiceTable thead').html(rowsHead);
		
		$('.showChildInv').click(function(e){
		   e.preventDefault();
		   let clstr = $(this).attr('data-trcls');
		   $('#invoiceTable tbody tr').removeClass('showTr');
		   $(clstr).addClass('showTr');
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
			
			var trailer = $('.c_trailer_input_'+tdid).val();
			$('#trailer_input').val(trailer);
			
			var tracking = $('.c_tracking_input_'+tdid).val();
			$('#tracking_input').val(tracking);
			
			var driver_status = $('.c_driver_status_input_'+tdid).val();
			$('#driver_status_input').val(driver_status);
			
			var invoice = $('.c_invoice_input_'+tdid).val();
			$('#invoice_input').val(invoice);
			
			
			if($('.c_bol_input_'+tdid).prop('checked')) { var bol = 'AK'; }
			else { var bol = ''; }
			$('#bol_input').val(bol);
			
			if($('.c_rc_input_'+tdid).prop('checked')) { var rc = 'AK'; }
			else { var rc = ''; }
			$('#rc_input').val(rc);
			
			//if($('.c_gd_input_'+tdid).prop('checked')) { var gd = 'AK'; }
			//else { var gd = ''; }
			//$('#gd_input').val(gd);
			
			var status = $('.c_status_input_'+tdid).val();
			$('#status_input').val(status);
			
			var $scrollable = $('.table-responsive');
            $scrollable.animate({
                scrollLeft: $scrollable[0].scrollWidth - $scrollable.width()
            }, 800); // Adjust the duration as needed
            
		});
		$('.current_checkbox').click(function(e){
			var id = jQuery(this).attr('data-id');
			if($(this).prop('checked')) {
				var valu = 'AK'; 
				} else {
				var valu = ''; 
			}
			jQuery(id).val(valu);
		});
		$('.current_input').keyup(function(e){
			//var cls = jQuery(this).attr('data-cls');
			var id = jQuery(this).attr('data-id');
			var valu = jQuery(this).val();
			//jQuery(cls).html(valu);
			jQuery(id).val(valu);
		});
		
		$('.current_change').change(function(e){
			var id = jQuery(this).attr('data-id');
			var valu = jQuery(this).val();
			jQuery(id).val(valu);
		});
		
		$('.fa-paper-plane').click(function(e){
			var tdid = jQuery(this).attr('data-id'); 
			
			$('.c_rate_txt_'+tdid).html($('#rate_input').val());
			$('.c_parate_txt_'+tdid).html($('#parate_input').val());
			$('.c_trailer_txt_'+tdid).html($('#trailer_input').val());
			$('.c_tracking_txt_'+tdid).html($('#tracking_input').val());
			$('.c_driver_status_txt_'+tdid).html($('#driver_status_input').val());
			$('.c_invoice_txt_'+tdid).html($('#invoice_input').val());
			$('.c_status_txt_'+tdid).html($('#status_input').val());
			
			if($('#bol_input').val()=='AK') { var bol = 'Yes'; }
			else { var bol = ''; }
			$('.c_bol_txt_'+tdid).html(bol);
			$('.c_rc_txt_'+tdid).html($('#rc_input').val());
			//$('.c_gd_txt_'+tdid).html($('#gd_input').val());
			
			$('.td-txt').show();
			$('.td-input').hide();
			
			$('#editrowform').submit();
			
			
			//if($('#bol_input').val() == 'AK') { $('.c_rc_input_'+tdid).prop('checked','true'); }
			//else { $('.c_rc_input_'+tdid).prop('checked','false'); }
			//$('#rc_input').val(rc);
		});
		$('#editrowform').submit(function(e){
			e.preventDefault();
			var form_data = $(this).serialize();
			$.ajax({
				type: "post",
				url: "<?php echo base_url('admin/outside-dispatch/ajaxedit');?>",
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
				url: "<?php echo base_url('admin/outside-dispatch');?>",
				data: form_data,
				success: function(responseData) {
					//alert("data saved")
					$('.status-success-msg').html('Status Updated Successfully.');
					$('.status-'+did).html(dstatus);
				}
			});
		});
		
		$('.delete-tr').click(function(e){
			e.preventDefault();
			var did = $(this).attr('data-id');  
			$('#deleteid-input').val(did); 
		});
		$('#deleteform').submit(function(e){
			e.preventDefault();
			var form_data = $(this).serialize();
			var did = $('#deleteid-input').val(); 
			$('.status-success-msg').show();
			$.ajax({
				type: "post",
				url: "<?php echo base_url('admin/outside-dispatch/ajaxdelete');?>",
				data: form_data,
				success: function(responseData) {
					//alert("data saved")
					$('.status-success-msg').html('PA Logistics removed successfully.');
					$('.tr-'+did).html('');
					$('.tr-'+did).remove();
				}
			});
		});
		
		$('#dataTable').DataTable({
			"lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]],
			/*"columns": [
			    { "title": "Sr no." },
                { "title": "PU Date & Time" },
                { "title": "PU Info" },
                { "title": "Del Date & Time" },
                { "title": "Del Info" },
                { "title": "PA Rate" },
                { "title": "Invoice Amount" },
                { "title": "Company" },
                { "title": "Trucking Company" },
                { "title": "Booked Under" },
                { "title": "Container/Trailer #" },
                { "title": "Booking/Tracking #" },
                { "title": "Invoice #" },
                { "title": "Carrier Invoice Status" },
                { "title": "BOL" },
                { "title": "RC" },
                { 
                    "title": "Invoice Status", 
                    "render": function (data, type, row) {
                        if (data === "Ready") {
                            return 'R&#8203;e&#8203;a&#8203;d&#8203;y';  // Insert zero-width spaces
                        }
                        return data;
                    },
                    "searchable": true // Optional: Can disable if you want for the entire column
                },
                { "title": "Shipment Status" },
                { "title": "Shipment Notes" },
                { "title": "Action" }
			 ]*/
			"columnDefs": [
                {
                    "searchable": false, // Disable search
                    "targets": [16] // Exclude the 4th column (index 3)
                }
            ]
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
		$('#dataTable').on('draw.dt', function () {
			calculateRate();
		});

		function beforeCalculateRate(){
			var parate = 0;  var rate = 0;
			
			$('.paRateTotal').html(parate);
			$('.rateTotal').html(rate);
		}
		
	});


	const base_url = "<?php echo base_url(); ?>";
	const comAddArr = <?php echo json_encode($comAddArr); ?>;
    const locationArr = <?php echo json_encode($locationArr); ?>;
    const cityArr = <?php echo json_encode($cityArr); ?>;
    const companyArr = <?php echo json_encode($companyArr); ?>;
    const truckingCompanies = <?php echo json_encode($truckingCompanies); ?>;
    const shipmentStatus = <?php echo json_encode($shipmentStatus); ?>;
	function loadChildInvoices(id) {
		$.ajax({
			url: "<?php echo base_url('admin/getChildInvoices');?>",
			type: 'POST',
			data: { id: id },
			success: function(response) {
			    const childInvoices = response.childInvoices;
				renderChildInvoiceTable(
					childInvoices,
					comAddArr,        
					locationArr,
					cityArr,
					companyArr,
					truckingCompanies,
					shipmentStatus
				);
				// $('#invoiceModal .modal-body').html(response);
				$('#subInvoicesModal').modal('show');
			},
			error: function(xhr, status, error) {
				alert('Failed to load child invoices: ' + error);
			}
		});
	}
	

	function renderChildInvoiceTable(childInvoices, comAddArr, locationArr, cityArr, companyArr, truckingCompanies, shipmentStatus) {
		const tbody = $('#subInvoicesTable tbody');
		tbody.empty();

		let n = 1;
		let rate = 0;
		let parate = 0;

		childInvoices.forEach(key => {
			rate += parseFloat(key.rate);
			parate += parseFloat(key.parate);

			const dispatchMeta = key.dispatchMeta ? JSON.parse(key.dispatchMeta) : {};
			const cls = key.parentInvoice ? `parInvCls${key.parentInvoice.replace(/\s/g, '')} childTR` : 'parInvCls';
			const href = base_url + (key.invoice.includes('OSD') ? 'admin/outside-dispatch/update/' : 'admin/dispatch/update/') + key.id;
			const hrefDuplicate = base_url + (key.invoice.includes('OSD') ? 'admin/outside-dispatch/add/' : 'admin/dispatch/add/') + key.id;

			const puDate = formatDate(key.pudate) + ' @ ' + (key.ptime || '');
			const pdDate = key.pd_date && !key.pd_date.includes('0000') ? formatDate(key.pd_date) + ' @ ' + key.pd_time :
						(!key.dodate.includes('0000') ? formatDate(key.dodate) + ' @ ' + key.dtime : '');

			const subInvBtn = key.childInvoice ? `<br><br><a href="#" class="showChildInv btn btn-sm btn-success pt-cta" data-trcls=".parInvCls${key.invoice.replace(/\s/g, '')}" data-cls=".subInvTr${key.invoice.replace(/\s/g, '')}" data-toggle="modal" data-target="#invoiceModal">Sub Inv.</a>` : '';

			const puInfo = resolveAddress(key.paddressid, key.plocation, key.pcity, comAddArr, locationArr, cityArr);
			const delInfo = resolveAddress(key.pd_addressid || key.daddressid, key.pd_location || key.dlocation, key.pd_city || key.dcity, comAddArr, locationArr, cityArr);

			const companyName = companyArr[key.company] || '';
			const carrierName = resolveCarrier(key.truckingCompany, key.bookedUnder, truckingCompanies);
			const dispatchInfo = dispatchMeta.dispatchInfo ? dispatchMeta.dispatchInfo.map(d => `${d[0]}: ${d[1]}`).join('<br>') : '';

			const statusOptions = shipmentStatus.map(ds => `<option value="${ds.title}" ${key.driver_status === ds.title ? 'selected' : ''}>${ds.title}</option>`).join('');

			const parentStyle = key.isParent ? 'style="background-color: #f8d7da;"' : '';
			const row = `
				<tr ${parentStyle}>
					<td>${n}</td>
					<td><strong>${puDate}</strong>${subInvBtn}</td>
					<td><a href="${href}">${puInfo}</a></td>
					<td><strong>${pdDate}</strong></td>
					<td>${delInfo}</td>
					<td>
						<span class="td-txt td-txt-${key.id}">${key.rate > 0 ? '$' : ''}<span class="c_rate_txt_${key.id} rateTxt">${key.rate}</span> <i class="fas fa-edit d-none" data-id="${key.id}" title="Edit"></i></span>
						<span class="td-input td-input-${key.id}">
							<input type="text" class="c_rate_input_${key.id} current_input" value="${key.rate}" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
							<i class="fa fa-paper-plane d-none" data-id="${key.id}"></i>
						</span>
					</td>
					<td>
						<span class="td-txt td-txt-${key.id}">${key.parate > 0 ? '$' : ''}<span class="c_parate_txt_${key.id} paRateTxt">${key.parate}</span> <i class="fas fa-edit d-none" data-id="${key.id}" title="Edit"></i></span>
						<span class="td-input td-input-${key.id}">
							<input type="text" class="c_parate_input_${key.id} current_input" value="${key.parate}" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
							<i class="fa fa-paper-plane d-none" data-id="${key.id}"></i>
						</span>
					</td>
					<td>${companyName}</td>
					<td>${carrierName}</td>
					<td>${dispatchInfo}</td>
					<td>
						<span class="td-txt td-txt-${key.id}"><span class="c_tracking_txt_${key.id}">${key.tracking}</span> <i class="fas fa-edit d-none" data-id="${key.id}" title="Edit"></i></span>
						<span class="td-input td-input-${key.id}">
							<input type="text" class="c_tracking_input_${key.id} current_input" value="${key.tracking}">
							<i class="fa fa-paper-plane d-none" data-id="${key.id}"></i>
						</span>
					</td>
					<td>
						<span class="td-txt td-txt-${key.id}"><a href="${href}"><span class="c_invoice_txt_${key.id}">${key.invoice}</span></a> <i class="fas fa-edit d-none" data-id="${key.id}" title="Edit"></i></span>
						<span class="td-input td-input-${key.id}">
							<input type="text" class="c_invoice_input_${key.id} current_input" readonly value="${key.invoice}">
							<i class="fa fa-paper-plane d-none" data-id="${key.id}"></i>
						</span>
					</td>
					<td>
						<span class="td-txt td-txt-${key.id}"><span class="c_driver_status_txt_${key.id}">${key.driver_status}</span> <i class="fas fa-edit d-none" data-id="${key.id}" title="Edit"></i></span>
						<span class="td-input td-input-${key.id}">
							<select class="c_driver_status_input_${key.id} current_change">${statusOptions}</select>
							<i class="fa fa-paper-plane d-none" data-id="${key.id}"></i>
						</span>
					</td>
					<td>
						<span class="td-txt td-txt-${key.id}"><span class="c_status_txt_${key.id}">${key.status}</span> <i class="fas fa-edit" data-id="${key.id}" title="Edit"></i></span>
						<span class="td-input td-input-${key.id}">
							<input type="text" class="c_status_input_${key.id} current_input" value="${key.status}">
							<i class="fa fa-paper-plane d-none" data-id="${key.id}"></i>
						</span>
					</td>
					<td>
						<a class="btn btn-sm btn-success pt-cta" href="${href}">Edit <i class="fas fa-edit" title="Edit"></i></a>
						<a class="btn btn-sm btn-success pt-cta" href="${hrefDuplicate}">Duplicate</a>
					</td>
				</tr>
			`;
			tbody.append(row);
			n++;
		});
		const tfoot = `
		<tfoot>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><strong>Total</strong></td>
				<td><strong>$<span>${rate.toFixed(2)}</span></strong></td>
				<td><strong>$<span>${parate.toFixed(2)}</span></strong></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tfoot>
		`;
		$('#subInvoicesTable tfoot').remove();
		$('#subInvoicesTable').append(tfoot);
	}

	function formatDate(dateStr) {
		const d = new Date(dateStr);
		return isNaN(d) ? '' : `${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}-${d.getFullYear()}`;
	}

	function resolveAddress(addressId, locationId, cityId, comAddArr, locationArr, cityArr) {
		if (comAddArr[addressId]) {
			return `${comAddArr[addressId][0]} [${comAddArr[addressId][1]}]`;
		}
		let loc = locationArr[locationId] || '';
		let city = cityArr[cityId] ? ` [${cityArr[cityId]}]` : '';
		return loc + city;
	}

	function resolveCarrier(truckingCompanyId, bookedUnderId, truckingCompanies) {
		let carrier = '';
		truckingCompanies.forEach(val => {
			if (val.id === truckingCompanyId) carrier = val.company;
			if (val.id === bookedUnderId) carrier += ` / ${val.company}`;
		});
		return carrier;
	}
</script>
<style>
		.select2-container--default .select2-selection--single {
    border-radius: 26px !important;
	border: 1px solid #E4E4E4;
	}
	.select2-container .select2-selection--single {
    min-height: 46px !important;
	}
	
	.select2-container--default .select2-selection--single .select2-selection__rendered {
		color: #495057 !important;
		line-height: 43px !important;
		font-size: 14px !important;
		padding-left: 0px !important;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 40px !important;
		right: 3px !important;
	}
	
</style>