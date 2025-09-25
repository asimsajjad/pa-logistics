<style>
	form.form {margin-bottom:25px;}
	.td-input, .subInvTr, #invoiceTable .fa-edit, #invoiceTable .btn-danger {display:none;}
	.fas {cursor: pointer;}
	.fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	a.btn {margin-bottom: 5px;}
	/*.table-responsive.stickyCls{overflow-y: auto;max-height: 90vh;}
	.table-responsive.stickyCls thead{position:sticky;top:0}*/
	#dataTable td{max-width:250px;white-space: normal;}
	#dataTable td.nowrap{white-space: nowrap;}
	#dataTable td a{color:blue;}
	#dataTable td a.btn{color:#fff;}
	table tr.showTr, #invoiceTable tr.showTr{display: table-row !important;}
	#invoiceTable thead tr {background: #1362b7;color: #fff;}
	#invoiceTable .srno{font-size:0px;}
	#invoiceTable .srno::before{content:">>";font-size:15px;}
	.getAddressParent{position:relative;}
.addressList{position:absolute;top:99%;left:0px;}
.cRed{color:red;font-weight:bold;}
</style>
<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3 class="m-0">PA Fleet Dispatch</h3>
		<div class="add_page" style="float: right;">
			<a class="nav-link-old" title="Create Section" href="<?php echo  base_url().'admin/dispatch/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success btn-sm pt-cta"/>
			</a> 
			<a class="nav-link-old" title="Upload CSV" href="<?php echo  base_url().'admin/dispatch/upload-csv';?>" style="display: inline;"><input type="button" name="add" value="Upload CSV" class="btn btn-primary btn-sm pt-cta"/>
			</a>  
		</div>
	</div>
	
	
	<div class="pt-card-body">
		
		<!--div class="col-sm-12 text-center">
		    <?php /*if($this->session->flashdata('searchError')){ ?>
					<div class="alert alert-danger">
						<strong><?php echo $this->session->flashdata('searchError'); ?></strong>
					</div>
				<?php }*/ ?>
		</div-->
		<div class="d-block text-center">
			<form class="form form-inline justify-content-between" method="post" action="">
				<input type="text" placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 125px;" class="form-control datepicker pt-form-field">
				<input type="text"  style="width: 125px;" placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker pt-form-field">
				
				<?php //echo $sdate.' '.$edate.' ';
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
					//elseif(date('d') < 9) { $curernt_w = '1';}
                    //elseif(date('d') < 16) { $curernt_w = '2'; }
                    //elseif(date('d') < 24) { $curernt_w = '3'; }
                    //else { $curernt_w = '4'; }
                    else { $curernt_w = 'all'; }
				?>
				<select name="week" class="form-control pt-form-field">
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
				
				<select name="invoiceType" class="form-control pt-form-field">
					<option value="">Invoice</option>
					<option value="RTS" <?php if($this->input->post('invoiceType') == 'RTS') { echo 'selected'; } ?>>RTS</option>
					<option value="Direct Bill" <?php if($this->input->post('invoiceType') == 'Direct Bill') { echo 'selected'; } ?>>Direct Bill</option>
					<option value="Quick Pay" <?php if($this->input->post('invoiceType') == 'Quick Pay') { echo 'selected'; } ?>>Quick Pay</option>
				</select>
				<!-- input type="text" placeholder="Status" value="<?php //if($this->input->post('status')) { echo $this->input->post('status'); } ?>" name="status" class="form-control"> &nbsp;
                    <input type="text" placeholder="Invoice" value="<?php //if($this->input->post('invoice')) { echo $this->input->post('invoice'); } ?>" name="invoice" class="form-control"> &nbsp;
				<input type="text" placeholder="Tracking" value="<?php //if($this->input->post('tracking')) { echo $this->input->post('tracking'); } ?>" name="tracking" class="form-control" -->
				<select name="unit" class="form-control pt-form-field">
					<option value="">Select Unit</option>
					<?php 
						if(!empty($vehicles)){
							foreach($vehicles as $val){
								echo '<option value="'.$val['id'].'"';
								if($this->input->post('unit')==$val['id']) { echo ' selected '; }
								echo '>'.$val['vname'].' ('.$val['vnumber'].')</option>';
							}
						}
					?>
				</select>
				<select name="driver" class="form-control pt-form-field">
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
				</select>
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
				
				<!--input type="submit" value="CSV" name="generateCSV" class="btn btn-primary" style="margin-left:10px;">
				<input type="submit" value="Excel" name="generateXls" class="btn btn-primary" style="margin-left:10px;"-->
				
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
		  <!---->
		<div class="table-responsive pt-datatbl"  style="overflow-y: auto;max-height: 90vh;">
			<table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead style="position:sticky;top:0">
                    <tr class="thead">
						<th>Sr.#</th>
						<th>PU Date & Time</th>
						<th>PU Info&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th>Del Date & Time</th>
						<th>Del Info&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th>PA Rate</th>
						<th>Invoice<br>Amount</th>
						<th>Company&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th>Trailer #</th>
						<th>Tracking / PO #</th>
						<th>Invoice #</th>
						<th>BOL</th>
						<th>RC</th>
						<th>Invoice Status</th>
						<th>Dispatch Status</th> 
						<th>Dispatch Notes</th>
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
						////////  dispatch tr have show also in modal ///////////
					    $js = '';
						if(!empty($dispatch)){
							$n=1; $rate = $parate = $parentChildRate = $parentChildPaRate = 0;
							foreach($dispatch as $key) {
							    if($this->input->post('driver') != '') {
									if($key['driver'] != $this->input->post('driver')){ continue; }
								}
								
								//if($key['parentInvoice'] != ''){ continue; }
								//if(strtotime($key['pudate']) < strtotime($sdate) || strtotime($key['pudate']) > strtotime($edate)){ continue; }
								
								$rateTxt = $paRateTxt = '';
							    // if(strtotime($startDate) <= strtotime($key['pudate']) && strtotime($endDate) >= strtotime($key['pudate'])) {
							        $rateTxt = 'rateTxt'; $paRateTxt = 'paRateTxt';
							    // }
							    
								// if($n < 16 && strtotime($startDate) <= strtotime($key['pudate']) && strtotime($endDate) >= strtotime($key['pudate'])) {
								// 	$rate = $rate + $key['rate'];
								// 	$parate = $parate + $key['parate'];
								// 	// $rate = $rate + $parentChildRate;
								// 	// $parate = $parate + $parentChildPaRate;
								// }
								$dispatchMeta = json_decode($key['dispatchMeta'],true);
								$cls = 'parInvCls';
								
								if($key['parentInvoice'] != ''){
								    $cls .= str_replace(' ','',$key['parentInvoice']).' childTR1';
								}
								$cRed = $nextdDate = '';
								if(!strstr($key['pd_date'],'0000') && $key['pd_date']!='') { $nextdDate = $key['pd_date']; }
								elseif(strstr($key['dodate'],'0000')) {} 
								else { $nextdDate = $key['dodate']; } 
								$nextPdate = strtotime("+ 5 days",strtotime($nextdDate));
								if($dispatchMeta['invoiceReady']!='1' && $nextPdate < strtotime(date('Y-m-d')) && $nextdDate != '') { $cRed = ' cRed '; }
							// if($key['parentInvoice'] == ''){
								?>
								<tr class="tr-<?php echo $key['id'].' '.$cls;?>"  <?php //if($key['parentInvoice'] != ''){ echo 'style="display:none"'; } ?>>
									<td class="srno"><?php echo $n; //if($key['parentInvoice'] == ''){ echo $n; } else { echo '>>'; } ?></td>
									<td>
										<strong><?php echo date('m-d-Y',strtotime($key['pudate']));?> <br>@ <?=$key['ptime']?></strong>
										<?php if($key['childInvoice'] != ''){
											echo '<br><br><a href="#" class="showChildInv btn btn-sm btn-success" data-trcls=".parInvCls'.str_replace(' ','',$key['invoice']).'" data-cls=".subInvTr'.str_replace(' ','',$key['invoice']).'" data-toggle="modal" data-target="#invoiceModal">Sub Inv.</a>';
										} ?>
									</td> 
									<td><a href="<?php echo base_url().'admin/dispatch/update/'.$key['id'];?>"><?php
										if(array_key_exists($key['paddressid'],$comAddArr)  && $key['paddressid'] > 0){ 
											echo $comAddArr[$key['paddressid']][0].' <br>['.$comAddArr[$key['paddressid']][1].']';
										} else {
											if(array_key_exists($key['plocation'],$locationArr)){ 
												echo $locationArr[$key['plocation']]; 
											}
											if(array_key_exists($key['pcity'],$cityArr)){ 
												echo ' <br>['.$cityArr[$key['pcity']].']'; 
											}
										}
										/*
										if(!empty($locations)){
											foreach($locations as $val){
												if($key['plocation']==$val['id']) { echo $val['location']; }
											}
										}
										if(!empty($cities)){
											foreach($cities as $val){
												if($key['pcity']==$val['id']) { echo ' ['.$val['city'].']'; }
											}
										}*/
									?></a>
									</td>
									<td>
									<strong>
									<?php 
										if(!strstr($key['pd_date'],'0000') && $key['pd_date']!='') { echo date('m-d-Y',strtotime($key['pd_date'])).' <br>@ '.$key['pd_time']; } 
										elseif(strstr($key['dodate'],'0000')) {} 
										else { echo date('m-d-Y',strtotime($key['dodate'])).' <br>@ '.$key['dtime']; } 
										?></strong>
										</td> 
									<td><?php
									if(array_key_exists($key['pd_addressid'],$comAddArr)  && $key['pd_addressid'] > 0){ 
										echo $comAddArr[$key['pd_addressid']][0].' <br>['.$comAddArr[$key['pd_addressid']][1].']';
									} elseif(array_key_exists($key['daddressid'],$comAddArr)  && $key['daddressid'] > 0){ 
										echo $comAddArr[$key['daddressid']][0].' <br>['.$comAddArr[$key['daddressid']][1].']';
									} else {
										if(array_key_exists($key['pd_location'],$locationArr) && $key['pd_location']!=''){ 
											echo $locationArr[$key['pd_location']]; 
										}
										elseif(array_key_exists($key['dlocation'],$locationArr)){ 
											echo $locationArr[$key['dlocation']]; 
										}
										if(array_key_exists($key['pd_city'],$cityArr) && $key['pd_city']!=''){ 
											echo ' <br>['.$cityArr[$key['pd_city']].']'; 
										}
										elseif(array_key_exists($key['dcity'],$cityArr)){ 
											echo ' <br>['.$cityArr[$key['dcity']].']'; 
										}
									}
										/*
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
										}*/
									?></td>
									
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
									
									<td><?php //echo $key['company'];
										/*if(!empty($companies)){
											foreach($companies as $val){
												if($key['company']==$val['id']) { echo $val['company']; }
											}
										}*/
										if(array_key_exists($key['company'],$companyArr)){ 
											echo $companyArr[$key['company']]; 
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
									
									<td class="nowrap"><?php //echo $key['invoice'];?>
										<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_invoice_txt_<?php echo $key['id'].' '.$cRed;?>"><?php echo $key['invoice'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
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
									
									<?php /*
									<td bgcolor="<?php if($key['gd']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php //echo $key['gd'];?>
										<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_gd_txt_<?php echo $key['id'];?>"><?php echo $key['gd'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
										<span class="td-input td-input-<?php echo $key['id'];?>"> 
											<input type="checkbox" <?php if($key['gd']=='AK') { echo 'checked'; } ?> class="c_gd_input_<?php echo $key['id'];?> current_checkbox" data-id="#gd_input" value="AK">
											<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
										</span>
									</td> 
									*/ ?>
									<td class="nowrap">
										<?php 
										$invoiceType = '';
										$showAging = 'false';
										$aDays = 0;
										if($key['invoiceType']=='Direct Bill'){ $invoiceType = 'DB'; $aDays = 30; }
										elseif($key['invoiceType']=='Quick Pay'){ $invoiceType = 'QP'; $aDays = 7; }
										elseif($key['invoiceType']=='RTS'){ $invoiceType = 'RTS'; $aDays = 3; }
										
										if($dispatchMeta['invoiceReadyDate'] != '') { echo $invoiceType.'&nbsp;Ready:'.date('m-d-Y',strtotime($dispatchMeta['invoiceReadyDate'])); }
										if($key['invoiceDate'] != '0000-00-00') { $showAging = 'true'; echo '<br>'.$invoiceType.'&nbsp;Invoiced:'.date('m-d-Y',strtotime($key['invoiceDate'])); }
										if($dispatchMeta['invoicePaidDate'] != '') { $showAging = 'false'; echo '<br>'.$invoiceType.'&nbsp;Paid:'.date('m-d-Y',strtotime($dispatchMeta['invoicePaidDate'])); }
										if($dispatchMeta['invoiceCloseDate'] != '') { $showAging = 'closed'; echo '<br>'.$invoiceType.'&nbsp;Closed:'.date('m-d-Y',strtotime($dispatchMeta['invoiceCloseDate'])); }
										if($showAging == 'true'){
											$date1 = new DateTime($key['invoiceDate']);
											$date2 = new DateTime(date('Y-m-d'));
											$diff = $date1->diff($date2);
											$aging = $diff->days;
											
											if($aging  > $aDays && $aDays > 0) {
												echo '<br><strong style="color:red">Inv. Aging: '.$aging.' Days</strong>';
											} else {
												echo '<br>Inv. Aging: '.$aging.' Days';
											}
										}
										if($showAging == 'closed'){
											$date1 = new DateTime($key['invoiceDate']);
											$date2 = new DateTime($dispatchMeta['invoicePaidDate']);
											$diff = $date1->diff($date2);
											$aging = $diff->days;
											echo '<br><strong>Payment Days: '.$aging.' Days</strong>';
										}
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
									<?php /*
									<!-- td><span class="status-<?php echo $key['id'];?>"><?php echo $key['status'];?></span><br>
										<a href="#" data-cls=".status-<?php echo $key['id'];?>" data-id="<?php echo $key['id'];?>" data-status="<?php echo $key['status'];?>" data-toggle="modal" data-target="#statusModal" class="status-edit">Edit</a>
									</td --> 
									*/ ?>
									<td>
										<a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/dispatch/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
										<br>
										<a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/dispatch/add/'.$key['id'];?>">Duplicate</a>
										<br>
										<a class="btn btn-sm btn-danger delete-tr" href="#" data-cls=".tr-<?php echo $key['id'];?>" data-id="<?php echo $key['id'];?>" data-toggle="modal" data-target="#deleteModal">Delete</a>
										
										<!-- a style="color:#ff0000;"  href="<?php echo base_url().'admin/dispatch/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this menu ?')"><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a -->
									</td>
								</tr> 
								<?php 
								$n++; //if($key['parentInvoice'] == ''){	$n++; }
								}
							// }
						}
					?>
				</tbody>
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
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tfoot>
			</table>
		</div>
	</div>
</div>

</div>

<!-- Modal -->

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
<script>
	$(document).ready(function() {
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
		
		//var rowsToCopy = $('.childTR').clone();
		//$('#invoiceTable tbody').html(rowsToCopy);
		var rowsHead = $('.thead').clone();
		$('#invoiceTable thead').html(rowsHead);
		
		//$('.childTR').html('').remove();
		
		//var table = $('#dataTable').DataTable();
        //table.rows('.childTR').remove().draw();
    
    
		$('.showChildInv').click(function(e){
		   e.preventDefault();
		   let clstr = $(this).attr('data-trcls');
		   $('#invoiceTable tbody tr').removeClass('showTr');
		   $(clstr).addClass('showTr');
		});
		
		/*$('body').on('keyup','.getAddress',function(){
    		let li = '<li data-city="Islip" data-address="41 street ave." data-company="PA Transport">Islip</li>\
    		<li data-city="Brooklyn" data-address="42 street ave." data-company="Pepsi Corp.">Brooklyn</li>\
    		<li data-city="Staten Island" data-address="43 street ave" data-company="Amazon">Staten Island</li>';
    		let address = '<div class="addressList"><ul>'+li+'</ul></div>';
    		$(this).parent('div').append(address);
    	});
    	$('body').on('click','.addressList li',function(){
    		let fieldset = $(this).closest('fieldset');
    		
    		fieldset.find('.addressI').val($(this).attr('data-address'));
    		fieldset.find('.companyI').val($(this).attr('data-company'));
    		fieldset.find('.cityI').val($(this).attr('data-city'));
    		fieldset.find('.addressList').html('').remove();
    	});*/
	
		$('.showChildInv11').click(function(e){
		   e.preventDefault();
		   $('#invoiceTable thead').html('');
		   $('#invoiceTable tbody').html('');
		   let clstr = $(this).attr('data-trcls');
		   var rowsToCopy = $(clstr).clone();
		   $('#invoiceTable tbody').html(rowsToCopy);
		   var rowsHead = $('.thead').clone();
		   $('#invoiceTable thead').html(rowsHead);
		   
		   /*let clstr = $(this).attr('data-trcls');
		   $(clstr).toggleClass('showTr'); 
		   var rowsToCopy = $(clstr).clone();
		   $(clstr).toggleClass('showTr'); 
		   let cls = $(this).attr('data-cls');
		   $(cls).addClass('showTr');
		   $(cls+' table').html(rowsToCopy);
		   
		   calculateRate()*/
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
				url: "<?php echo base_url('admin/dispatch/ajaxedit');?>",
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
				url: "<?php echo base_url('admin/dispatch');?>",
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
				url: "<?php echo base_url('admin/dispatch/ajaxdelete');?>",
				data: form_data,
				success: function(responseData) {
					//alert("data saved")
					$('.status-success-msg').html('Dispatch removed successfully.');
					$('.tr-'+did).html('');
					$('.tr-'+did).remove();
				}
			});
		});
		
		 console.log("Initializing DataTable with pageLength set to 25");
		$('#dataTable').DataTable({
			"responsive": true,
			"lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]],
			"columnDefs": [
                {
                    "searchable": false, // Disable search
                    "targets": [13] // Exclude the 4th column (index 3)
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
			// $('.paRateTotal').html(parseFloat(parate).toFixed(2));
			// $('.rateTotal').html(parseFloat(rate).toFixed(2));
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
	/*
	var $responsiveTable = $('.table-responsive');
        var offset = $responsiveTable.offset().top - 20;
    
        $(window).on('scroll', function() {
            if ($(window).scrollTop() > offset) {
                $responsiveTable.addClass('stickyCls');
            } else {
                $responsiveTable.removeClass('stickyCls');
            }
        });*/
</script>
