<div class="card mb-3">
	<div class="d-flex justify-content-between align-items-center pt-page-title pt-title-border flex-wrap mb-5">
		<h3 class="pt-page-title mb-0">Add PA Fleet Dispatch</h3>
		<div class="add_page">
			<a href="<?php echo base_url('admin/dispatch');?>" class="nav-link p-0"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm pt-cta"/></a>
		</div>
	</div>
	
	
		<div class="mobile_content">
				
			<form class="form form-inline mb-4" id="addPaFleetform" method="post" action="<?php echo base_url('admin/dispatch/add');?>">
				<select class="form-control " name="premadetrip" onchange="this.form.submit()">
					<option value="">Select Pre Made Trip</option>
					<?php 
						//$expenses = array('Line Haul','FSC (Fuel Surcharge)','Pre-Pull','Lumper','Detention at Shipper','Detention at Receiver','Detention at Port','Drivers Assist','Gate Fee','Overweight Charges','Delivery Order Charges','Chassis Rental','Demurrage','Layover','Yard Storage','Customs Clearance','Chassis Gate Fee','Chassis Split Fee','Others','TONU','Discount','Dry Run');
						$expenseN = array();
						foreach($expenses as $exp) {
							if($exp['type']=='Negative'){ $expenseN[] = $exp['title']; } 
						}
						$premadetrip = array('pudate'=>'','notes'=>'','status'=>'','dassist'=>'','detention'=>'','invoice'=>'','tracking'=>'','trailer'=>'','dodate'=>'','dtime'=>'','ptime'=>'','trip'=>'','driver'=>'','vehicle'=>'','pcity'=>'','plocation'=>'','paddress'=>'','paddressid'=>'','dcity'=>'','dlocation'=>'','daddress'=>'','daddressid'=>'','rate'=>'','parate'=>'','company'=>'','dispatchMeta'=>array());
						if(!empty($pre_made_trips)){
							foreach($pre_made_trips as $val){
								echo '<option value="'.$val['id'].'"';
								if($this->input->post('premadetrip')==$val['id']) { 
									echo ' selected="selected" '; 
									$premadetrip = array('ptime'=>$val['ptime'],'paddress'=>$val['paddress'],'paddressid'=>$val['paddressid'],'pcity'=>$val['pcity'],'plocation'=>$val['plocation'],'dtime'=>$val['dtime'],'daddress'=>$val['daddress'],'daddressid'=>$val['daddressid'],'dcity'=>$val['dcity'],'dlocation'=>$val['dlocation'],'rate'=>$val['rate'],'parate'=>$val['parate'],'company'=>$val['company']);
								}
								echo '>'.$val['pname'].'</option>';
							}
						}
					?>
				</select>
			</form>

			<?php 
				if(!empty($duplicate)) {
					$premadetrip = $duplicate[0];
					
					$premadetrip['pudate'] = '';
					$premadetrip['dodate'] = '';
					$premadetrip['invoice'] = '';
					$premadetrip['tracking'] = '';
					$premadetrip['trailer'] = '';
					$premadetrip['notes'] = '';
					$premadetrip['status'] = '';
					$premadetrip['driver'] = '';
					$premadetrip['vehicle'] = '';
				} 
			?>
			<!-- h3> Add PA Fleet Dispatch</h3 -->
			<?php if($this->session->flashdata('item')){ ?>
				<div class="alert alert-success">
					<?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?>
				</div>
				<?php } 
				
				$js_companies = $js_cities = $js_location = '';
				//$js_companies = $company = $dcity = $pcity = $dcity1 = $pcity1 = $js_location = $js_cities = $plocation = $dlocation = $plocation1 = $dlocation1 = '';
				if(!empty($companies)){
					$i = 1;
					foreach($companies as $val){
						if($i > 1) { $js_companies .= ','; }
						$js_companies .= '"'.$val['company'].'"';
						if($premadetrip['company']==$val['id']) { $premadetrip['company'] = $val['company']; } 
						$i++;
					}
				}
				if(!empty($locations)){
					$i = 1;
					foreach($locations as $val){
						if($i > 1) { $js_location .= ','; }
						$js_location .= '"'.$val['location'].'"'; 
						if($premadetrip['plocation']==$val['id']) { $premadetrip['plocation'] = $val['location']; }
						if($premadetrip['dlocation']==$val['id']) { $premadetrip['dlocation'] = $val['location']; }
						$i++;
					}
				}
				if(!empty($cities)){
					$i = 1;
					foreach($cities as $val){
						if($i > 1) { $js_cities .= ','; }
						$js_cities .= '"'.$val['city'].'"'; 
						if($premadetrip['pcity']==$val['id']) { $premadetrip['pcity'] = $val['city']; }
						if($premadetrip['dcity']==$val['id']) { $premadetrip['dcity'] = $val['city']; }
						$i++;
					}
				}
			?>
			<form class="form" method="post" action="<?php echo base_url('admin/dispatch/add');?>" enctype="multipart/form-data">
				<?php  echo validation_errors();?>
				<div class="clearfix"></div>
				<div class="row">	
					<div class="col-sm-6">
						<div class="form-group">
							<label for="contain">Vehicle</label>
							<select class="form-control" name="vehicle" required>
								<option value="">Select Vehicle</option>
								<?php 
									if(!empty($vehicles)){
										foreach($vehicles as $val){
											echo '<option value="'.$val['id'].'"';
											if($this->input->post('vehicle')==$val['id']) { echo ' selected="selected" '; }
											elseif($premadetrip['vehicle']==$val['id']) { echo ' selected="selected" '; }
											echo '>'.$val['vname'].' ('.$val['vnumber'].')</option>';
										}
									}
								?>
							</select>
						</div>
					</div>
					
					<div class="col-sm-6">	     
						<div class="form-group">
							<label for="contain">Driver</label>							
							<select class="form-control" name="driver" required>
								<option value="">Select Driver</option>
								<?php 
									if(!empty($drivers)){
										foreach($drivers as $val){
											echo '<option value="'.$val['id'].'"';
											if($this->input->post('driver')==$val['id']) { echo ' selected="selected" '; }
											elseif($premadetrip['driver']==$val['id']) { echo ' selected="selected" '; }
											echo '>'.$val['dname'].'</option>';
										}
									}
								?>
							</select>
						</div>
					</div>
				</div>
				
				<fieldset class="my-5">
					<legend>Pick Up:</legend>
					<button class="btn btn-success btn-sm pick-drop-btn pickup-btn-add" type="button">Add New +</button>
					
					<div class="row pickup-pcode-parent">
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain" class="d-flex align-items-center pt-labelslelectrip">Pick Up Date 
									<select name="trip" class="form-control">
										<option value="">Select Trip</option>
										<?php for($i=1;$i<16;$i++){ 
											echo '<option value="'.$i.'"';
											if($premadetrip['trip']==$i) { echo ' selected="selected" '; }
											echo '>'.$i.'</option>';
										} ?>
									</select>
								</label>
								<input name="pudate" type="text" class="form-control  datepicker" required value="<?php if($this->input->post('pudate')!='') { echo $this->input->post('pudate'); } else { echo $premadetrip['pudate']; } ?>">
							</div>
						</div>
						<div class="col-sm-4">
								<div class="form-group">
									<label for="contain">Appointment Type</label>
									<select class="form-control appointmentTypeP" name="appointmentTypeP" id="appointmentTypeP" required>
										<option value="">Select Appointment Type</option>
										<option value="Appointment">By Appointment</option>
										<option value="FCFS">First Come First Serve (FCFS)</option>
									</select>
								</div>
							</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Pick Up Time</label>
								
								<div class="input-group mb-2">
									<input name="ptime" type="text" class="timeInput form-control " value="<?php if($this->input->post('ptime')!='') { echo $this->input->post('ptime'); } else { echo $premadetrip['ptime']; } ?>" readonly>
									<div class="tDropdown"></div>
									<div class="input-group-append">
										<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Pick Up City</label>
								<div class="getAddressParent">
									<input type="text" id="pcity" data-type="city" class="form-control  cityI getAddress" name="pcity" required value="<?php if($this->input->post('pcity')!='') { echo $this->input->post('pcity'); } else { echo $premadetrip['pcity']; } ?>">
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="contain">Pick Up Company (Location)</label>
								<div class="getAddressParent">
									<input type="text" id="plocation" data-type="company" class="form-control  getAddress companyI" name="plocation" required value="<?php if($this->input->post('plocation')!='') { echo $this->input->post('plocation'); } else { echo $premadetrip['plocation']; } ?>">
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="contain">Pick Up Address</label>
								<div class="getAddressParent">
									<input type="hidden" name="paddressid" class="addressidI" value="<?php if($this->input->post('paddressid')!='') { echo $this->input->post('paddressid'); } else { echo $premadetrip['paddressid']; } ?>"> 
									<input type="text" id="paddress" data-type="address" class="form-control  getAddress addressI" name="paddress" value="<?php if($this->input->post('paddress')!='') { echo $this->input->post('paddress'); } else { echo $premadetrip['paddress']; } ?>"> 
								</div>
							</div>
						</div>
						<?php
							$pcodeValue = $this->input->post('pcode');
							if($pcodeValue=='') { $pcode = array(' '); }
							else { $pcode = explode('~-~',$pcodeValue); }
							for($i=0;$i<count($pcode);$i++){
								if($i > 0) {
									$class = ' pcode-id-'.$i;
									$dContent = '<div class="input-group-text"><i data-cls=".pcode-id-'.$i.'" class="fa fa-trash code-delete"></i></div>';
									} else {
									$class = '';
									$dContent = '<div class="input-group-text pcode-add"><strong>+</strong></div>';
								}
							?>
							<div class="col-sm-3 <?php echo $class;?>">
								<div class="form-group">
									<label for="contain">Pick Up#</label>
									<div class="input-group mb-2">
										<input name="pcode[]" type="text" class="form-control " value="<?php echo $pcode[$i]; ?>">
										<div class="input-group-append">
											<?php echo $dContent; ?>
										</div>
									</div>  
								</div>
							</div>
						<?php } ?>
						
						<div class="col-sm-12 pnotes">
							<div class="form-group">
								<label for="contain">Pickup Notes</label> 
								<textarea name="pnotes" class="form-control "><?php if($this->input->post('pnotes')!='') { echo $this->input->post('pnotes'); } else { echo $premadetrip['pnotes']; } ?></textarea>
							</div>
						</div>
					</div>
				</fieldset>	
				
				<div class="pickupExtra"></div>
				
				<fieldset class="my-5">
					<legend>Drop Off:</legend>
					<button class="btn btn-success btn-sm pick-drop-btn drop-btn-add" type="button">Add New +</button>
					
					<div class="row dropoff-pcode-parent">				
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Drop Off Date</label>
								<input name="dodate" type="text" class="form-control  datepicker" value="<?php if($this->input->post('dodate')!='') { echo $this->input->post('dodate'); } else { echo $premadetrip['dodate']; } ?>">
								
							</div>
						</div>
						<div class="col-sm-4">
								<div class="form-group">
									<label for="contain">Appointment Type</label>
									<select class="form-control appointmentTypeD" name="appointmentTypeD" id="appointmentTypeD" required>
										<option value="">Select Appointment Type</option>
										<option value="Appointment">By Appointment</option>
										<option value="FCFS">First Come First Serve (FCFS)</option>
									</select>
								</div>
							</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Drop Off Time</label>
								
								<div class="input-group mb-2">
									<input name="dtime" type="text" class="timeInput form-control " value="<?php if($this->input->post('dtime')!='') { echo $this->input->post('dtime'); } else { echo $premadetrip['dtime']; } ?>" readonly>
									<div class="tDropdown"></div>
									<div class="input-group-append">
										<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Drop Off City</label>
								<div class="getAddressParent">
									<input type="text" id="dcity" data-type="city" class="form-control  cityI getAddress" name="dcity" required value="<?php if($this->input->post('dcity')!='') { echo $this->input->post('dcity'); } else { echo $premadetrip['dcity']; } ?>">
								</div>
							</div>
						</div>
						<div class="col-sm-5">
							<div class="form-group">
								<label for="contain">Drop Off Company (Location)</label>
								&nbsp;&nbsp;
								<div class="custom-control custom-checkbox my-1 mr-sm-2" style="display: inline;color:#ff0047;">
									<input type="checkbox" class="custom-control-input" id="delivered" name="delivered" value="yes">
									<label class="custom-control-label" for="delivered">Delivered</label>
								</div>
								<div class="getAddressParent">
									<input type="text" id="dlocation" data-type="company" class="form-control  getAddress companyI" name="dlocation" required value="<?php if($this->input->post('dlocation')!='') { echo $this->input->post('dlocation'); } else { echo $premadetrip['dlocation']; } ?>">
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="contain">Drop Off Address</label>
								<div class="getAddressParent">
								    <input type="hidden" name="daddressid" class="addressidI" value="<?php if($this->input->post('daddressid')!='') { echo $this->input->post('daddressid'); } else { echo $premadetrip['daddressid']; } ?>"> 
									<input type="text" id="daddress" data-type="address" class="form-control  getAddress addressI" name="daddress" value="<?php if($this->input->post('daddress')!='') { echo $this->input->post('daddress'); } else { echo $premadetrip['daddress']; } ?>"> 
								</div>
							</div>
						</div>
						<?php
							$dcodeValue = $this->input->post('dcode');
							if($dcodeValue=='') { $dcode = array(' '); }
							else { $dcode = explode('~-~',$dcodeValue); }
							for($i=0;$i<count($dcode);$i++){
								if($i > 0) {
									$class = ' dcode-id-'.$i;
									$dContent = '<div class="input-group-text"><i data-cls=".dcode-id-'.$i.'" class="fa fa-trash code-delete"></i></div>';
									} else {
									$class = '';
									$dContent = '<div class="input-group-text dcode-add" data-cls=".driver-notes-code"><strong>+</strong></div>';
								}
							?>
							<div class="col-sm-2 <?php echo $class;?>">
								<div class="form-group">
									<label for="contain">Drop Off#</label>
									<div class="input-group mb-2">
										<input name="dcode[]" type="text" class="form-control " value="<?php echo $dcode[$i]; ?>">
										<div class="input-group-append">
											<?php echo $dContent; ?>
										</div>
									</div>  
								</div>
							</div>
						<?php } ?>
						
						
						<div class="col-sm-12 driver-notes-code">
							<div class="form-group">
								<label for="contain">Driver Notes</label> 
								<textarea name="dnotes" class="form-control "><?php if($this->input->post('dnotes')!='') { echo $this->input->post('dnotes'); } else { echo $premadetrip['dnotes']; } ?></textarea>
							</div>
						</div>
						
					</div>
				</fieldset>
				
				<div class="dropoffExtra" id="sortable"></div>
				
								
				<div class="row">					
					<div class="col-sm-4">
						<div class="form-group">
							<label for="contain">Rate</label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
									<div class="input-group-text">$</div>
								</div>
								<input name="rate" step="0.01" type="number" min="0" class="form-control " value="<?php if($this->input->post('rate')!='') { echo $this->input->post('rate'); } else { echo $premadetrip['rate']; } ?>">
							</div> 
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="contain">Invoice Amount</label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
									<div class="input-group-text">$</div>
								</div>
								<input name="parate" step="0.01" type="number" min="0" data-price="0" class="form-control  parate" value="<?php if($this->input->post('parate')!='') { echo $this->input->post('parate'); } else { echo $premadetrip['parate']; } ?>">
							</div> 
						</div>
					</div>
					<div class="col-sm-12">
						<fieldset class="my-5">
							<legend>Expense:</legend>
							<button class="btn btn-success btn-sm pick-drop-btn expense-btn" type="button">Add New +</button>
							<div class="row expense-cls">
								<?php
									$e = 1;
									$paRate = $premadetrip['parate'];
									
									if($premadetrip['dispatchMeta']) {
										$dispatchMeta = json_decode($premadetrip['dispatchMeta'],true);
										foreach($dispatchMeta['expense'] as $expVal) {
											$e++;
											//if($expVal[0] == 'Discount') { $paRate = $paRate + $expVal[1]; }
											if(in_array($expVal[0],$expenseN)){ $paRate = $paRate + $expVal[1]; }
											else { $paRate = $paRate - $expVal[1]; }
										?>
										<div class="col-sm-3 expense-div-<?=$e?>">
											<div class="form-group">
												<div style="display: inline;color:#ff0047;" class="custom-checkbox my-1 mr-sm-2">
													<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".expense-div-<?=$e?>" type="button" style="top:0px;">X</button>
													<select name="expenseName[]" class="expenseNameSelect expenseName-<?=$e?>" style="padding: 3px;margin: 3px;">
														<?php 
															foreach($expenses as $exp) {
																echo '<option value="'.$exp['title'].'"';
																if($exp['title'] == $expVal[0]){ echo ' selected '; }
																echo '>'.$exp['title'].'</option>';
															}
														?>
													</select>
												</div>
												<input name="expensePrice[]" data-cls=".expenseName-<?=$e?>" required type="number" min="1" class="form-control  expenseAmt" value="<?=$expVal[1]?>" step="0.01">
											</div>
										</div>
										<?php }
									}
								?>
							</div>
						</fieldset>
					</div>
				</div>  
				
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label for="contain">Company</label>
							<div class="getCompanyParent">
								<input type="text" id="companies" class="form-control  getCompany" name="company" required value="<?php if($this->input->post('company')!='') { echo $this->input->post('company'); } else { echo $premadetrip['company']; } ?>">
								<input type="hidden" class="companyID" name="companyid">
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="contain">Trailer #</label>
							<input name="trailer" type="text" class="form-control " value="<?php if($this->input->post('trailer')!='') { echo $this->input->post('trailer'); } else { echo $premadetrip['trailer']; } ?>">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="contain">Tracking #</label>
							<input required name="tracking" type="text" class="form-control " value="<?php if($this->input->post('tracking')!='') { echo $this->input->post('tracking'); } else { echo $premadetrip['tracking']; } ?>">
						</div>
					</div>
				</div>
				
				
				<fieldset class="my-5">
					<legend>Financial:</legend>
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Invoice #</label>
								<input readonly name="invoice" type="text" class="form-control " value="<?php if($this->input->post('invoice')!='') { echo $this->input->post('invoice'); } else { echo $premadetrip['invoice']; } ?>">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Payout Amount</label>
								<input name="payoutAmount" type="text" class="form-control " value="<?php if($this->input->post('payoutAmount')!='') { echo $this->input->post('payoutAmount'); } ?>">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Invoice Date</label>
								<input name="invoiceDate" type="text" class="form-control  datepicker" placeholder="TBD" value="<?php if($this->input->post('invoiceDate')!='') { echo $this->input->post('invoiceDate'); } else { echo 'TBD'; } ?>">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Expected Pay Date</label>
								<input name="expectPayDate" readonly type="text" class="form-control  datepicker" value="<?php if($this->input->post('expectPayDate')!='') { echo $this->input->post('expectPayDate'); } else { echo 'TBD'; } ?>">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Invoice Type</label>
								<input type="hidden" name="invoiceTypeOld" value="">
								<select class="form-control  invoiceTypeCls" name="invoiceType">
									<option value="">Select Invoice Type</option>
									<option value="RTS" <?php if($this->input->post('invoiceType')=='RTS') { echo 'selected'; }?>>RTS</option>
									<option value="Direct Bill" <?php if($this->input->post('invoiceType')=='Direct Bill') { echo 'selected'; }?>>Direct Bill</option>
									<option value="Quick Pay" <?php if($this->input->post('invoiceType')=='Quick Pay') { echo 'selected'; }?>>Quick Pay</option>
								</select>
							</div>
						</div>
						
						<?php 
							if($this->input->post('invoiceType')=='RTS') { $invoiceType = 'RTS'; }
							elseif($this->input->post('invoiceType')=='Direct Bill') { $invoiceType = 'DB'; }
							elseif($this->input->post('invoiceType')=='Quick Pay') { $invoiceType = 'QP'; }
							else { $invoiceType = ''; }
						?>
						
						<div class="col-sm-8 invoiceCheckboxCls">
							<label style="display:block">&nbsp;</label>
							<div class="form-group invoiceqp"  style="display:<?php if($this->input->post('invoiceType')=='') { echo 'none'; } else { echo 'inline'; } ?>">
								<div class="custom-control custom-checkbox my-1 mr-sm-2" style="display: inline;">
									<input type="hidden" name="invoiceReady" value="0">
									<input type="checkbox" class="custom-control-input invoiceCheckCls" id="invoiceTypeInvoiceReady" name="invoiceReady" <?php if($this->input->post('invoiceReady')=='1') { echo 'checked'; } ?> value="1">
									<label class="custom-control-label" for="invoiceTypeInvoiceReady"><span class="invoiceTitle"><?=$invoiceType?></span> Ready To Submit</label>
								</div>
							</div>
							<div class="form-group"  style="display:<?php if($this->input->post('invoiceType')!='RTS' && $this->input->post('invoiceType')!='Direct Bill') { echo 'none'; } else { echo 'inline'; } ?>">
								<div class="custom-control custom-checkbox my-1 mr-sm-2" >
									<input type="hidden" name="invoiced" value="0">
									<input type="checkbox" class="custom-control-input invoiceCheckCls" id="invoiceTypeInvoiced" name="invoiced" <?php if($this->input->post('invoiced')=='1') { echo 'checked'; } ?> value="1">
									<label class="custom-control-label" for="invoiceTypeInvoiced"><span class="invoiceTitle"><?=$invoiceType?></span> Invoiced</label>
								</div>
							</div>
							<div class="form-group"  style="display:<?php if($this->input->post('invoiceType')!='RTS' && $this->input->post('invoiceType')!='Direct Bill') { echo 'none'; } else { echo 'inline'; } ?>">
								<div class="custom-control custom-checkbox my-1 mr-sm-2">
									<input type="hidden" name="invoicePaid" value="0">
									<input type="checkbox" class="custom-control-input invoiceCheckCls" id="invoiceTypeInvoicePaid" name="invoicePaid" <?php if($this->input->post('invoiceType')=='1') { echo 'checked'; } ?> value="1">
									<label class="custom-control-label" for="invoiceTypeInvoicePaid"><span class="invoiceTitle"><?=$invoiceType?></span> Paid</label>
								</div>
							</div>
							<div class="form-group"  style="display:<?php if($this->input->post('invoiceType')!='RTS' && $this->input->post('invoiceType')!='Direct Bill') { echo 'none'; } else { echo 'inline'; } ?>">
								<div class="custom-control custom-checkbox my-1 mr-sm-2">
									<input type="hidden" name="invoiceClose" value="0">
									<input type="hidden" name="invoiceCloseOld" value="">
									<input type="checkbox" class="custom-control-input invoiceCheckCls" id="invoiceTypeInvoiceClose" name="invoiceClose" <?php if($this->input->post('invoiceClose')=='1') { echo 'checked'; } ?> value="1">
									<label class="custom-control-label" for="invoiceTypeInvoiceClose"><span class="invoiceTitle"><?=$invoiceType?></span> Closed</label>
								</div>
							</div>
						</div>
						
					</div>
				</fieldset>
				
				<p>&nbsp;</p>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<div class="custom-control custom-checkbox my-1 mr-sm-2">
								<input type="checkbox" class="custom-control-input bolrc" id="customControlInline" name="bol" value="AK" <?php if($this->input->post('bol')=='AK') { echo ' checked'; } ?>>
								<label class="custom-control-label" for="customControlInline">BOL</label>
							</div>
							<input name="bol_d[]" multiple type="file" class="form-control ">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group"> 
							<div class="custom-control custom-checkbox my-1 mr-sm-2">
								<input type="checkbox" name="rc" class="custom-control-input bolrc" id="customControlInlinerc" value="AK" <?php if($this->input->post('rc')=='AK') { echo ' checked'; } ?>>
								<label class="custom-control-label" for="customControlInlinerc">RC</label>
							</div>
							<input name="rc_d[]" multiple type="file" class="form-control ">
						</div>
					</div> 
					<div class="col-sm-4">
						<div class="form-group"> 
							<div class="custom-control custom-checkbox my-1 mr-sm-2">
								<input type="checkbox" name="gd" class="custom-control-input bolrc" id="customControlInlinegd" value="AK" <?php if($this->input->post('gd')=='AK') { echo ' checked'; } ?>>
								<label class="custom-control-label" for="customControlInlinegd">$</label>
							</div>
							<input name="gd_d" type="file" class="form-control ">
						</div>
					</div>
					
					
					<div class="col-sm-5">
						<div class="form-group">
							<label for="contain">Status</label> 
							<input name="status" type="text" class="form-control  statusCls" data="" value="<?php if($this->input->post('status')!='') { echo $this->input->post('status'); } else { echo $premadetrip['status']; } ?>">
						</div>
					</div> 
					<div class="col-sm-5">
						<div class="form-group">
							<label for="contain">Driver Status</label> 
							<select name="driver_status" class="form-control " required>
								<option value="Pending">Select Driver Status</option>
								<?php
									foreach($shipmentStatus as $ds){
										echo '<option value="'.$ds['title'].'">'.$ds['title'].'</option>';
									}
								?>
							</select>
						</div>
					</div>
					
					<div class="col-sm-12">    
						
					</div>
					
					<div class="col-sm-12">
						<div class="form-group">
							<label for="contain">Dispatch Remarks</label> 
							<textarea name="notes" class="form-control "><?php if($this->input->post('notes')!='') { echo $this->input->post('notes'); } else { echo $premadetrip['notes']; } ?></textarea>
						</div>
					</div>
					<div class="col-sm-12"> 
						<div class="form-group text-center mb-0">
							<input type="submit" name="save" value="Add Dispatch" class="btn btn-success btn-sm pt-cta"/>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
		
		

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<!--script src="https://code.jquery.com/jquery-1.12.4.js"></script-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!--script src="/assets/bootstrap-select.js"></script>
<link href="/assets/bootstrap-select.css" rel="stylesheet" /-->

<script>
	$(document).ready(function() {
		let typingTimer;
		const doneTypingInterval = 500; // Time in ms (500ms = 0.5s)

		$('body').on('keyup', '.getAddress', function () {
			clearTimeout(typingTimer);
			const $this = $(this); // Store the reference to the current jQuery object
			typingTimer = setTimeout(() => {
				$('.addressList').html('').remove();
				let keyword = $this.val();
				let type = $this.attr('data-type');
				$.ajax({
					type: "post",
					url: "<?php echo base_url('admin/get-address');?>",
					data: { keyword: keyword, type: type },
					success: function (responseData) { 
						let address = '<div class="addressList"><ul>' + responseData + '</ul></div>';
						$this.parent('div').append(address);
					}
				});
			}, doneTypingInterval);
		});

		$('body').on('keydown', '.getAddress', function () {
			clearTimeout(typingTimer);
		});
		$(document).on('mousedown', function(event) {
			if (!$(event.target).closest('.getAddress, .addressList').length) {
				$('.addressList').remove();
			}
		});

    	$('body').on('click','.addressList li',function(){
    		let fieldset = $(this).closest('fieldset');
    		
    		//fieldset.find('.addressI').val($(this).attr('data-address'));
    		fieldset.find('.addressI').val($(this).attr('data-address')+' '+$(this).attr('data-city')+' '+$(this).attr('data-zip'));
    		fieldset.find('.companyI').val($(this).attr('data-company'));
    		fieldset.find('.cityI').val($(this).attr('data-city'));
    		fieldset.find('.addressidI').val($(this).attr('data-id'));
    		fieldset.find('.addressList').html('').remove();
		});
		
		//// company address
		$('body').on('keyup', '.getCompany', function () {
			clearTimeout(typingTimer);
			const $this = $(this); // Store the reference to the current jQuery object
			typingTimer = setTimeout(() => {
				$('.companyList').html('').remove();
				let keyword = $this.val();
				$.ajax({
					type: "post",
					url: "<?php echo base_url('admin/get-companies');?>",
					data: { keyword: keyword },
					success: function (responseData) { 
						let address = '<div class="companyList"><ul>' + responseData + '</ul></div>';
						$this.parent('div').append(address);
					}
				});
			}, doneTypingInterval);
		});
		$('body').on('click','.companyList li',function(){
    		let fieldset = $(this).closest('.getCompanyParent');
    		fieldset.find('.getCompany').val($(this).attr('data-company'));
    		fieldset.find('.companyList').html('').remove();
		});
		$('body').on('keydown', '.getCompany', function () {
			clearTimeout(typingTimer);
		});
		
    	
		//$( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
		$('body').on('focus',".datepicker", function(){
			$(this).datepicker({dateFormat: 'yy-mm-dd'});
		});
		$(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });

		$( "#sortable" ).sortable();
		<?php /*
		var companies = [<?php echo $js_companies; ?>];
		$( "#companies" ).autocomplete({ source: companies }); 
		
		var cities = [<?php echo $js_cities; ?>];
		$( "#dcity" ).autocomplete({ source: cities });
		$( "#pcity" ).autocomplete({ source: cities });
		$('body').on('focus',".city1", function(){
			$(this).autocomplete({ source: cities });
		});
		
		var locations = [<?php echo $js_location; ?>];
		$( "#dlocation" ).autocomplete({ source: locations });
		$( "#plocation" ).autocomplete({ source: locations });
		$('body').on('focus',".location1", function(){
			$(this).autocomplete({ source: locations });
		});
		*/ ?>
		/*$('body').on('keyup','.expenseAmt',function(){
			calculatePaRate();
		});*/
		var timeoutID;
		$('body').on('keyup', '.expenseAmt', function(){
			let cls = $(this).attr('data-cls');
			if($(cls).val() == 'FSC (Fuel Surcharge)'){
				let $this = $(this); // Capture $(this) to use inside setTimeout
				
				clearTimeout(timeoutID); // Clear any existing timer
				
				timeoutID = setTimeout(function(){
					let surcharge = $this; // Use the captured value of $(this)
					let samt = surcharge.val();
					if(samt == '' || samt == 'undefined' || samt == 'NaN' || isNaN(samt)) { samt = 0; }
					
					$(".expenseAmt").each(function(index) {
						let insideCls = $(this).attr('data-cls');
						if($(insideCls).val() == 'Line Haul') {
							let amt = $(this).val();
							if(amt == '' || amt == 'undefined' || amt == 'NaN' || isNaN(amt)) { amt = 0; }
							let result = (samt / 100) * amt;
							surcharge.val(parseFloat(result).toFixed(2));
							//surcharge.val(Math.round(result));
						}
					});
					calculatePaRate();
				}, 2000);
				} else {
				calculatePaRate();
			}
		});
		
		$('body').on('click','.expenseAmt',function(){
			calculatePaRate();
		});
		$('body').on('change','.expenseNameSelect',function(){
			calculatePaRate();
		});
		
		var dcid = 9999;
		
		$('.expense-btn').click(function(){
			var expenseDiv = '<div class="col-sm-3 expense-div-'+dcid+'">\
			<div class="form-group">\
			<div style="display: inline;color:#ff0047;" class="custom-checkbox my-1 mr-sm-2">\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".expense-div-'+dcid+'" type="button" style="top:0px;">X</button>\
			<select name="expenseName[]" class="expenseNameSelect expenseName-'+dcid+'" style="padding: 3px;margin: 3px;">\
			<?php foreach($expenses as $exp) { echo '<option value="'.$exp['title'].'">'.$exp['title'].'</option>'; } ?>\
			</select>\
			</div>\
			<input name="expensePrice[]" data-cls=".expenseName-'+dcid+'" required type="number" min="1" class="form-control expenseAmt" value="0" step="0.01">\
			</div>\
			</div>';
			dcid++;
			$('.expense-cls').append(expenseDiv);
		});
		
		
		$('.pcode-add').click(function(){
			var pickup = '<div class="col-sm-2 pcode-id-'+dcid+'"><div class="form-group"><label for="contain">Pick Up#</label><div class="input-group mb-2"><input name="pcode[]" type="text" required class="form-control" value=""><div class="input-group-append"><div class="input-group-text"><i data-cls=".pcode-id-'+dcid+'" class="fa fa-trash code-delete"></i></div></div></div></div></div>';
			$('.pnotes').before(pickup);
			dcid++;
		}); 
		
		$('body').on('click','.pcode1-add',function(){
			var cls = $(this).attr('data-cls'); 
			var name = $(this).attr('data-name'); 
			var pickup = '<div class="col-sm-2 pcode1-id-'+dcid+'"><div class="form-group"><label for="contain">Pick Up#</label><div class="input-group mb-2"><input name="pcode1['+name+'][]" type="text" required class="form-control" value=""><div class="input-group-append"><div class="input-group-text"><i data-cls=".pcode1-id-'+dcid+'" class="fa fa-trash code-delete"></i></div></div></div></div></div>';
			$(cls).before(pickup);
			dcid++;
		});
		
		$('.dcode-add').click(function(){
			var cls = $(this).attr('data-cls'); 
			var dropoff = '<div class="col-sm-2 dcode-id-'+dcid+'"><div class="form-group"><label for="contain">Pick Up#</label><div class="input-group mb-2"><input name="dcode[]" type="text" required class="form-control" value=""><div class="input-group-append"><div class="input-group-text"><i data-cls=".dcode-id-'+dcid+'" class="fa fa-trash code-delete"></i></div></div></div></div></div>';
			$(cls).before(dropoff);
			dcid++;
		});
		$('body').on('click','.dcode1-add',function(){
			var cls = $(this).attr('data-cls'); 
			var name = $(this).attr('data-name'); 
			var dropoff = '<div class="col-sm-2 dcode1-id-'+dcid+'"><div class="form-group"><label for="contain">Pick Up#</label><div class="input-group mb-2"><input name="dcode1['+name+'][]" type="text" required class="form-control" value=""><div class="input-group-append"><div class="input-group-text"><i data-cls=".dcode1-id-'+dcid+'" class="fa fa-trash code-delete"></i></div></div></div></div></div>';
			$(cls).before(dropoff);
			dcid++;
		});
		
		$('body').on('click','.code-delete',function(){
			var cls = $(this).attr('data-cls');
			$(cls).html('').remove();
		}); 
		$('body').on('click','.pick-drop-remove-btn',function(){
			var cls = $(this).attr('data-removeCls');
			var result = window.confirm('Are you sure?');
			if (result == true) {
				$(cls).html('').remove();
			}
			calculatePaRate();
		});
		// <div class="col-sm-2">\
			// 	<div class="form-group">\
			// 		<label for="contain">Pick Up Time</label>\
			// 		<input name="ptime1[]" type="text" class="form-control" value="">\
			// 	</div>\
			// </div>\
		var pid = 2;
		$('.pickup-btn-add').click(function(){
			var pfieldset = '<fieldset class="pickup'+pid+'">\
			<legend>Pick Up #'+pid+':</legend>\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removeCls=".pickup'+pid+'" type="button">Remove</button>\
			<div class="row pickup-pcode1'+pid+'-parent">\
			<div class="col-sm-3">\
			<div class="form-group">\
			<label for="contain">Pick Up Date</label>\
			<input name="pudate1[]" type="text" class="form-control datepicker" required value="">\
			</div>\
			</div>\
			<div class="col-sm-3">\
				<div class="form-group">\
					<label for="contain">Appointment Type</label>\
					<select class="form-control" id="appointmentTypeP-' + pid + '" name="appointmentTypeP1[]" required>\
						<option value="">Select Appointment Type</option>\
						<option value="Appointment">By Appointment</option>\
						<option value="FCFS">First Come First Serve (FCFS)</option>\
					</select>\
				</div>\
			</div>\
			 <div class="col-sm-3">\
			 	<div class="form-group">\
					<label for="contain">Pick Up Time</label>\
					<div class="input-group mb-2">\
						<input name="ptime1[]" id="ptime1-' + pid + '" type="text" class="timeInput form-control" value="" readonly>\
						<div class="tDropdown"></div>';
						// for (let h = 0; h < 24; h++) {
						// 	for (let m = 0; m < 60; m += 15) {
						// 		let hour = h % 12 === 0 ? 12 : h % 12;
						// 		let meridian = h < 12 ? "AM" : "PM";
						// 		let times = `${hour.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')} ${meridian}`;
						// 		pfieldset += '<div>'+times+'</div>';
						// 	}
						// }
						pfieldset += '\
						<div class="input-group-append">\
							<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>\
					</div>\
				</div>\
			</div>\
			</div>\
			<div class="col-sm-3">\
			<div class="form-group">\
			<label for="contain">Pick Up City</label>\
			<div class="getAddressParent">\
			<input type="text" id="pcity1" data-type="city" class="form-control city1 cityI getAddress" required name="pcity1[]" value="">  \
			</div>\
			</div>\
			</div>\
			<div class="col-sm-4">\
			<div class="form-group">\
			<label for="contain">Pick Up Company (Location)</label>\
			<div class="getAddressParent">\
			<input type="text" id="plocation1" data-type="company" class="form-control location1 getAddress companyI" required name="plocation1[]" value=""> \
			</div>\
			</div>\
			</div>\
			<div class="col-sm-4">\
			<div class="form-group">\
			<label for="contain">Pick Up Address</label>\
			<div class="getAddressParent">\
			<input type="hidden" name="paddressid1[]" class="addressidI" value="">\
			<input type="text" id="paddress1" data-type="address" class="form-control paddress1 getAddress addressI" name="paddress1[]" value=""> \
			</div>\
			</div>\
			</div>\
			<div class="col-sm-2 pcode1-id-'+pid+'">\
			<div class="form-group">\
			<label for="contain">Pick Up#</label>\
			<div class="input-group mb-2">\
			<input name="pcodename[]" type="hidden" value="pc'+pid+'">\
			<input name="pcode1[pc'+pid+'][]" type="text" class="form-control" value="">\
			<div class="input-group-append">\
			<div class="input-group-text pcode1-add" data-name="pc'+pid+'" data-cls=".pnotes1-'+pid+'"><strong>+</strong></div>\
			</div>\
			</div>  \
			</div>\
			</div>\
			<div class="col-sm-12 pnotes1-'+pid+'">\
			<div class="form-group">\
			<label for="contain">Pickup Notes</label> \
			<textarea name="pnotes1[]" class="form-control"></textarea>\
			</div>\
			</div>\
			</div>\
			</fieldset>';
			$('.pickupExtra').append(pfieldset);
			pid++;
		});
		
		var did = 2;
		$('.drop-btn-add').click(function() {
			var dfieldset = '<fieldset class="ui-state-default dropoff1'+did+'">\
			<legend>Drop Off #'+did+':</legend>\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removeCls=".dropoff1'+did+'" type="button">Remove</button>\
			<div class="row dropoff1'+did+'-pcode-parent">\
			<div class="col-sm-3">\
			<div class="form-group">\
			<label for="contain">Drop Off Date</label>\
			<input name="dodate1[]" type="text" class="form-control datepicker" required value="">\
			</div>\
			</div>\
			<div class="col-sm-4">\
				<div class="form-group">\
					<label for="contain">Appointment Type</label>\
					<select class="form-control" id="appointmentTypeD-' + did + '" name="appointmentTypeD1[]" required>\
						<option value="">Select Appointment Type</option>\
						<option value="Appointment">By Appointment</option>\
						<option value="FCFS">First Come First Serve (FCFS)</option>\
					</select>\
				</div>\
			</div>\
			<div class="col-sm-3">\
			<div class="form-group">\
			<label for="contain">Drop Off Time</label>\
			<div class="input-group mb-2">\
				<input name="dtime1[]" type="text" class="timeInput form-control" value="" readonly>\
				<div class="tDropdown"></div>';
				
				// for (let h = 0; h < 24; h++) {
    			// 	for (let m = 0; m < 60; m += 15) {
    			// 		let hour = h % 12 === 0 ? 12 : h % 12;
    			// 		let meridian = h < 12 ? "AM" : "PM";
    			// 		let times = `${hour.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')} ${meridian}`;
    			// 		dfieldset += '<div>'+times+'</div>';
    			// 	}
    			// }
				
				dfieldset += '\
				<div class="input-group-append">\
					<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>\
				</div>\
			</div>\
			</div>\
			</div>\
			<div class="col-sm-3">\
			<div class="form-group">\
			<label for="contain">Drop Off City</label>\
			<div class="getAddressParent">\
			<input type="text" data-type="city" class="form-control city1 cityI getAddress" name="dcity1[]" value="">\
			</div>\
			</div>\
			</div>\
			<div class="col-sm-5">\
			<div class="form-group">\
			<label for="contain">Drop Off Company (Location) &nbsp;</label>\
			<div class="getAddressParent">\
			<input type="text" data-type="company" class="form-control location1 getAddress companyI" name="dlocation1[]" value=""> \
			</div>\
			</div>\
			</div>\
			<div class="col-sm-4">\
			<div class="form-group">\
			<label for="contain">Drop Off Address</label>\
			<div class="getAddressParent">\
			<input type="hidden" name="daddressid1[]" class="addressidI" value="">\
			<input type="text" id="daddress1" data-type="address" class="form-control getAddress addressI" name="daddress1[]" value=""> \
			</div>\
			</div>\
			</div>\
			<div class="col-sm-2 dcode1-id-1'+did+'">\
			<div class="form-group">\
			<label for="contain">Drop Off#</label>\
			<div class="input-group mb-2">\
			<input name="dcodename[]" type="hidden" value="dc'+did+'">\
			<input name="dcode1[dc'+did+'][]" type="text" class="form-control" value="">\
			<div class="input-group-append">\
			<div class="input-group-text dcode1-add" data-name="dc'+did+'" data-cls=".dnotes1'+did+'"><strong>+</strong></div>\
			</div>\
			</div> \
			</div>\
			</div>\
			<div class="col-sm-12 dnotes1'+did+'">\
			<div class="form-group">\
			<label for="contain">Drop Off Notes</label>\
			<textarea name="dnotes1[]" class="form-control"></textarea>\
			</div>\
			</div>\
			</div>\
			</fieldset>';
			$('.dropoffExtra').append(dfieldset);
			did++;		
		});
		
		$('.parate').keyup(function(){
			let valu = $(this).val();
			$(this).attr('data-price',valu);
		});
		$('.parate').click(function(){
			let valu = $(this).val();
			$(this).attr('data-price',valu);
		});
		
		$('#customControlInlinegd, #customControlInlinerc, #customControlInline').click(function(){
			if($('#customControlInlinegd').prop('checked') && $('#customControlInlinerc').prop('checked') && $('#customControlInline').prop('checked')) { 
				$('.invoiceTypeCls').attr('required','');
			}
			else { $('.invoiceTypeCls').removeAttr('required'); }
		});
		
		var invoiceTypeTxt = '';
		$('.invoiceTypeCls').change(function(){
			let valu = $(this).val();
			if(valu == 'RTS'){
				$('.invoiceTitle').html('RTS'); invoiceTypeTxt = 'RTS';
				$('.invoiceCheckboxCls > div').show();
				} else if(valu == 'Direct Bill'){
				$('.invoiceTitle').html('DB'); invoiceTypeTxt = 'DB';
				$('.invoiceCheckboxCls > div').show();
				} else if(valu == 'Quick Pay'){
				$('.invoiceTitle').html('QP'); invoiceTypeTxt = 'QP';
				$('.invoiceCheckboxCls > div').hide();
				$('.invoiceCheckboxCls .invoiceqp').show();
				} else {
				$('.invoiceCheckboxCls > div').hide(); invoiceTypeTxt = '';
			}
			changeStatus(invoiceTypeTxt);
		});
		
		$('.invoiceCheckCls').on('click', function() {
			changeStatus(invoiceTypeTxt);
		});
		
        $('.bolrc').on('click', function() {
            checkCheckboxes();
		});
		
		
		/********* time dropdown ******/
		let activeInput = null; // Track currently active input field
		// Generate time options
		const generateTimeOptions = () => {
			let times = [];
			for (let h = 0; h < 24; h++) {
				for (let m = 0; m < 60; m += 15) {
					let hour = h % 12 === 0 ? 12 : h % 12;
					let meridian = h < 12 ? "AM" : "PM";
					times.push(`${hour.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')} ${meridian}`);
				}
			}
			return times;
		};

		// Add times to each tDropdown
		// $(".input-group").each(function () {
		// 	let timeOptions = generateTimeOptions();
		// 	let dropdown = $(this).find(".tDropdown");
		// 	timeOptions.forEach(time => {
		// 	dropdown.append('<div>'+time+'</div>');
		// 	});
		// });

			// Naveed time dropdown customization
			$(document).on('click', '.input-group', function () {
			let dropdown = $(this).find(".tDropdown");
			if (dropdown.children('.search-time').length === 0) {
				let input = $('<input type="text" class="search-time" placeholder="Search time..." style="width:100%; padding:5px; margin-bottom:5px;">');
				dropdown.prepend(input);
				let timeOptions = generateTimeOptions();
				timeOptions.forEach(time => {
					dropdown.append('<div class="time-option">' + time + '</div>');
				});
			}

			dropdown.find('.time-option').removeClass('focused'); // remove old highlight
			let firstOption = dropdown.find('.time-option:visible').first();
			if (firstOption.length) {
				currentFocusIndex = 0;
				firstOption.addClass('focused');
			}

			dropdown.show(); 
			setTimeout(function() {
				dropdown.find('.search-time').focus(); 
			}, 10);
		});
		let currentFocusIndex = -1;
		$(document).on('keyup', '.search-time', function(e) {
			if (e.key === "ArrowDown" || e.key === "ArrowUp" || e.key === "Enter") {
				return; 
			}
			let searchVal = $(this).val().toLowerCase();
			let dropdown = $(this).parent();
			let options = dropdown.find('.time-option');
			currentFocusIndex = -1;
			options.removeClass('focused');
			let firstMatchIndex = -1;
			options.each(function(index) {
				let timeText = $(this).text().toLowerCase();
				if (timeText.indexOf(searchVal) !== -1) {
					$(this).show();
					if (firstMatchIndex === -1) {
						firstMatchIndex = index;
					}
				} else {
					$(this).hide();
				}
			});

			if (firstMatchIndex !== -1) {
				let visibleOptions = options.filter(':visible');
				currentFocusIndex = 0;
				visibleOptions.removeClass('focused');
				visibleOptions.eq(0).addClass('focused');
			}
		});
		$(document).on('keydown', '.search-time', function(e) {
			let dropdown = $(this).parent();
			let options = dropdown.find('.time-option:visible');
			if (e.key === "ArrowDown") {
				currentFocusIndex++;
				if (currentFocusIndex >= options.length) currentFocusIndex = 0;
				updateFocusedOption(options);
				e.preventDefault();
			} 
			else if (e.key === "ArrowUp") {
				currentFocusIndex--;
				if (currentFocusIndex < 0) currentFocusIndex = options.length - 1;
				updateFocusedOption(options);
				e.preventDefault();
			} 
			else if (e.key === "Enter") {
				if (currentFocusIndex > -1 && options.length > 0) {
					options.eq(currentFocusIndex).trigger('click');
				}
				$(this).val('');
				dropdown.hide();
				e.preventDefault();
			}
		});
		function updateFocusedOption(options) {
			options.removeClass('focused'); 
			if (options.length > 0 && currentFocusIndex >= 0) {
				let focusedOption = options.eq(currentFocusIndex);
				focusedOption.addClass('focused');
				focusedOption[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
			}
		}
		$(document).on('click', '.time-option', function(e) {
			e.stopPropagation();
			let selectedTime = $(this).text();
			let dropdown = $(this).closest('.tDropdown');
			dropdown.find('.search-time').val(selectedTime);
			setTimeout(() => {
				dropdown.find('.search-time').val('');
				dropdown.find('.time-option').show(); 
			}, 50); 
			dropdown.hide();
		});
		$(document).on('click', function(e) {
			if (!$(e.target).closest('.input-group').length) {
				$('.tDropdown').hide(); 
				$('.search-time').val(''); 
				$('.time-option').show(); 
				$('.time-option').removeClass('focused');
				currentFocusIndex = -1;
			}
		});
		// Naveed time dropdown customization




		// Show dropdown when clicking the .timedd icon
		$(document).on("click", ".timedd", function () {
			let parentGroup = $(this).closest(".input-group");
			activeInput = parentGroup.find(".timeInput"); // Set active input
			let dropdown = parentGroup.find(".tDropdown");

			dropdown.toggle().css({
				//top: activeInput.offset().top + activeInput.outerHeight(),
				//left: activeInput.offset().left,
				//position: "absolute"
			});
		});

		// Select time when clicking an option (Replaces only the second time)
		// $(document).on("click", ".tDropdown div", function () {
		// 	let dropdown = $(this).parent("div");
		// 	let selectedTime = $(this).text();

		// 	if (activeInput) {
		// 		let currentValue = activeInput.val().trim();
		// 		let times = currentValue.split(" - ");

		// 		if(times.length === 1 && times[0].length < 7) {
		// 		    activeInput.val(selectedTime); 
		// 		} else if (times.length === 1 && times[0] !== "") {
		// 			activeInput.val(times[0] + " - " + selectedTime); // Add second time
		// 		} else if (times.length === 2) {
		// 			activeInput.val(times[0] + " - " + selectedTime); // Replace only the second time
		// 		} else {
		// 			activeInput.val(selectedTime); // If empty, insert first time
		// 		}
		// 	}

		// 	dropdown.hide();
		// });
		$(document).on("click", ".tDropdown div", function () {
			let dropdown = $(this).parent("div");
			let selectedTime = $(this).text();
		
			let parentGroup = $(this).closest(".input-group");
			let appointmentType = parentGroup.closest(".row").find("select").val();

			if (activeInput) {
				let currentValue = activeInput.val().trim();
				let times = currentValue.split(" - ");

				if (appointmentType === "Appointment") {
					activeInput.val(selectedTime);
					dropdown.hide();
				} else if (appointmentType === "FCFS") {
					if(times.length === 1 && times[0].length < 7) {
						activeInput.val(selectedTime); 
					} else if (times.length === 1 && times[0] !== "") {
						activeInput.val(times[0] + " - " + selectedTime); 
					} else if (times.length === 2) {
						activeInput.val(times[0] + " - " + selectedTime); 
					} else {
						activeInput.val(selectedTime); 
					}
				}else{
					let appointmentTypeP = $('select#appointmentTypeP').val();
					if (appointmentTypeP === "Appointment") {
						activeInput.val(selectedTime);
						dropdown.hide();
					} else if(appointmentTypeP === "FCFS") {
						if(times.length === 1 && times[0].length < 7) {
						activeInput.val(selectedTime); 
						} else if (times.length === 1 && times[0] !== "") {
							activeInput.val(times[0] + " - " + selectedTime); 
						} else if (times.length === 2) {
							activeInput.val(times[0] + " - " + selectedTime); 
						} else {
							activeInput.val(selectedTime); 
						}
					}
				}
			}		
			dropdown.hide();
		});
		$(document).on("change", "#appointmentTypeP", function () {
			const appointmentType = $(this).val(); 
			const ptimeInput = $("#ptime"); 
			if (appointmentType !== "") {
				ptimeInput.val(""); 
			}
		});
		$(document).on("change", "#appointmentTypeD", function () {
			const appointmentType = $(this).val(); 
			const ptimeInput = $("#dtime"); 
			if (appointmentType !== "") {
				ptimeInput.val(""); 
			}
		});
		$(document).on("change", ".appointmentType", function () {
			const appointmentType = $(this).val();
			const ptimeInput = $(this).closest(".col-sm-3").next(".col-sm-3").find(".timeInput");
			if (appointmentType !== "") {
				ptimeInput.val(""); 
			}
		});
		$(document).on("change", ".appointmentTypeP1", function () {
			const appointmentType = $(this).val();
			const ptimeInput = $(this).closest(".col-sm-4").next(".col-sm-3").find(".timeInput");
			if (appointmentType !== "") {
				ptimeInput.val(""); 
			}
		});
		$(document).on("change", ".appointmentTypeD1", function () {
			const appointmentType = $(this).val();
			const ptimeInput = $(this).closest(".col-sm-4").next(".col-sm-3").find(".timeInput");
			if (appointmentType !== "") {
				ptimeInput.val(""); 
			}
		});

		//adding required attribute to checkboxes
		document.body.addEventListener('change', function (event) {
		if (event.target.matches('[name="bol_d[]"]')) {
			const checkbox = document.getElementById('customControlInline');
			if (event.target.value) {
				checkbox.setAttribute('required', 'required');
			} else {
				checkbox.removeAttribute('required');
			}
		}
		
		if (event.target.matches('[name="rc_d[]"]')) {
			const checkbox = document.getElementById('customControlInlinerc');
			if (event.target.value) {
				checkbox.setAttribute('required', 'required');
			} else {
				checkbox.removeAttribute('required');
			}
		}
		if (event.target.matches('[name="gd_d"]')) {
			const checkbox = document.getElementById('customControlInlinegd');
			if (event.target.value) {
				checkbox.setAttribute('required', 'required');
			} else {
				checkbox.removeAttribute('required');
			}
		}
	});
	
		
		//adding required attribute to pick/drop time
		document.querySelector('#addPaFleetform').addEventListener('submit', function (e) {
        const ptimeInput = document.querySelector('#ptime');
		const dtimeInput = document.querySelector('#dtime');
		const dtimeChildInputs = document.querySelectorAll('input[name="dtime1[]"]'); 

        if (ptimeInput.value.trim() === '') {
            e.preventDefault(); 
            alert('Pick Up Time is required!');
			return;
        }
		if(dtimeInput.value.trim() === ''){
			e.preventDefault(); 
            alert('Drop Off Time is required!');
			return;
		}

		for (let i = 0; i < dtimeChildInputs.length; i++) {
			if (dtimeChildInputs[i].value.trim() === '') {
				e.preventDefault(); 
				alert('Child Drop Off Time ' + (i + 1) + ' is required!');
				return;
			}
		}
    });

		// Hide dropdown if clicking outside
		$(document).on("click", function (e) {
			if (!$(e.target).closest(".tDropdown, .timedd").length) {
				$(".tDropdown").hide();
			}
		});
		/********* time dropdown ******/
    
		function changeStatus(invoiceTypeTxt){
			var statusText = ''; // Variable to store the result based on priority
			var currentDate = '<?=date('m/d/Y')?>';
			
			if ($('.invoiceCheckCls[name="invoiceClose"]').is(':checked') && invoiceTypeTxt!='QP') {
				statusText = invoiceTypeTxt+' Closed ' + currentDate;
			} else if ($('.invoiceCheckCls[name="invoicePaid"]').is(':checked') && invoiceTypeTxt!='QP') {
				statusText = invoiceTypeTxt+' Paid ' + currentDate;
			} else if ($('.invoiceCheckCls[name="invoiced"]').is(':checked') && invoiceTypeTxt!='QP') {
				statusText = invoiceTypeTxt+' Invoiced ' + currentDate;
			} else if ($('.invoiceCheckCls[name="invoiceReady"]').is(':checked')) {
				statusText = invoiceTypeTxt +' Ready to submit '+currentDate;
			}
			
			if(statusText != '' && invoiceTypeTxt != ''){
				$('.statusCls').val(statusText);
			} else {
				let statusTextOld = $('.statusCls').attr('data');
				$('.statusCls').val(statusTextOld);
			}
		}
		
		function checkCheckboxes() {
            if ($('.bolrc:checked').length === $('.bolrc').length) {
                $('.invoiceTypeCls').attr('required', 'required');
				} else {
                $('.invoiceTypeCls').removeAttr('required');
			}
		}
		
		function calculatePaRate(){
			var expenseAmt = 0;
			$(".expenseAmt").each(function(index) {
				let cls = $(this).attr('data-cls');
				let amt = $(this).val();
				if(amt == '' || amt == 'undefined' || amt == 'NaN') { amt = 0; }
				<?php 
				if($expenseN){ 
					for($e=0;count($expenseN) > $e;$e++){
						if($e > 0){ echo 'else '; }
						echo "if($(cls).val()=='".$expenseN[$e]."') { expenseAmt = expenseAmt - parseFloat(amt); }\n";
					}
				} else {
					echo 'if(1==2){}';
				}
				?>
				//if($(cls).val()=='Discount') { expenseAmt = expenseAmt - parseFloat(amt); }
				else { expenseAmt = expenseAmt + parseFloat(amt); }
			});
			let paAmt = $('.parate').attr('data-price');
			if(paAmt == '' || paAmt == 'undefined' || paAmt == 'NaN') { paAmt = 0; }
			let finalAmt = parseFloat(paAmt) + parseFloat(expenseAmt); 
			$('.parate').val(parseFloat(finalAmt).toFixed(2));
			//$('.parate').val(Math.round(finalAmt));
		}
	});
</script>

<style>
 .timeInput {padding:13px 15px;}
    .input-group{position:relative;}
  .tDropdown {position:absolute;width:calc(100% - 30px);max-height:240px;overflow-y:auto;background:white;border:1px solid #ccc;display:none;z-index:1000;top:98%;left:0;}
  .tDropdown div {padding: 5px;cursor: pointer;}
  .tDropdown div:hover {background: #f0f0f0;}
  
	#ui-datepicker-div {z-index: 99999 !important;}
	.invoiceCheckboxCls div{display:inline;}
	.custom-control-label::before {width: 20px;height: 20px;}
	.custom-control-label::after {width: 20px;height: 20px;}
	
	fieldset .pick-drop-btn{position:absolute;right:15px;top:-34px;}
	fieldset{position:relative;padding:15px;background: #f6f6f6; border: 1px solid #c5c5c5;}
	.card .container-fluid, .card .mobile_content, .card .container{padding-left:0;padding-right:0;}
	  .card, .pt-content-body > .container-fluid {    padding: 10px;  }
	  
	.getAddressParent, .getCompanyParent{position:relative;}
	.addressList, .companyList{position:absolute;top:99%;left:0px;background: #eee;z-index: 999;width: 100%;border: 1px solid #aaa;}
	.addressList li, .companyList li {list-style: none;line-height: 23px;padding: 4px;cursor: pointer;}
	.addressList li:hover, .companyList li:hover {background:#fff;}

	.time-option.focused {
		background-color: #007bff;
		color: white;
	}
</style>
