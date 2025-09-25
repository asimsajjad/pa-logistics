<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Update PA Warehousing 
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/paWarehouse');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			<div class="container">
				<h3> Update PA Warehousing</h3>
				<?php
					$disp = $dispatch[0]; 
					// print_r($dispatchInfo);exit;

					$dispatchMeta = json_decode($disp['dispatchMeta'],true);
					//$expenses = array('Line Haul','FSC (Fuel Surcharge)','Pre-Pull','Lumper','Detention at Shipper','Detention at Receiver','Detention at Port','Drivers Assist','Gate Fee','Overweight Charges','Delivery Order Charges','Chassis Rental','Demurrage','Layover','Yard Storage','Customs Clearance','Chassis Gate Fee','Chassis Split Fee','Others','Toll','TONU','Discount','Dry Run');
					$expenseN = array();
					foreach($expenses as $exp) {
						if($exp['type']=='Negative'){ $expenseN[] = $exp['id']; } 
					}
					//$dispatchInfo = array('Container Number','Booking Number','Chassis Number','Shipping Line','Vessel / Voyage','POD Number','Seal Number','BOL #','PO #','SO #','Others');
					
					$js_companies = $company = $dcity = $pcity = $dcity1 = $pcity1 = $js_location = $js_cities = $plocation = $dlocation = '';
				
					$cityArr = $locationArr = $companyArr = $vehicleArr = $driverArr = $comAddArr = $truckComArr = array();
					
					$paddress = $disp['paddress'];
					$daddress = $disp['daddress'];
					
					if(!empty($companies)){
						$i = 1;
						foreach($companies as $val){
							//if($i > 1) { $js_companies .= ','; }
							//$js_companies .= '"'.$val['company'].'"';
							$companyArr[$val['id']] = $val['company'];
							if($disp['company']==$val['id']) { $company = $val['company']; }
							$i++;
						}
					}
					
					if(!empty($cities)){
						$i = 1;
						foreach($cities as $val){
							//if($i > 1) { $js_cities .= ','; }
							//$js_cities .= '"'.$val['city'].'"';
							
							if($disp['dcity']==$val['id']) { $dcity = $val['city']; }
							if($disp['pcity']==$val['id']) { $pcity = $val['city']; }
							if($disp['dcity1']==$val['id']) { $dcity1 = $val['city']; }
							if($disp['pcity1']==$val['id']) { $pcity1 = $val['city']; }
							$cityArr[$val['id']] = $val['city'];
							$i++;
						}
					}
					
					if(!empty($locations)){
						$i = 1;
						foreach($locations as $val){
							//if($i > 1) { $js_location .= ','; }
							//$js_location .= '"'.$val['location'].'"'; 
							if($disp['plocation']==$val['id']) { $plocation = $val['location']; }
							if($disp['dlocation']==$val['id']) { $dlocation = $val['location']; }
							$locationArr[$val['id']] = $val['location'];
							$i++;
						}
					}
					
					if(!empty($companyAddress)){
						foreach($companyAddress as $val){
							if($disp['paddressid']==$val['id'] && $disp['paddressid'] > 0) { 
								$plocation = $val['company'];
								$pcity = $val['city'].', '.$val['state'];
								$paddress = $val['address'].' '.$val['city'].', '.$val['state'].' '.$val['zip'];
							}
							if($disp['daddressid']==$val['id'] && $disp['daddressid'] > 0) { 
								$dlocation = $val['company'];
								$dcity = $val['city'].', '.$val['state'];
								$daddress = $val['address'].' '.$val['city'].', '.$val['state'].' '.$val['zip'];
							}
							$comAddArr[$val['id']] = array($val['company'],$val['city'].', '.$val['state'],$val['address'].' '.$val['city'].', '.$val['state'].' '.$val['zip']);
						}
					}

					if (!empty($drivers)) {
						foreach ($drivers as $val) {
							$driverArr[$val['id']] = array(
								'dname' => $val['dname'],
								'phone' => $val['phone']
							);
						}
					
						if (isset($driverArr[$disp['driver']]) && $disp['driver'] > 0) {
							$driverName = $driverArr[$disp['driver']]['dname'];
							$driverPhone = $driverArr[$disp['driver']]['phone'];
						}
					}
					$pdfArray = array(); 
				?>
				
				
				
				<form class="form" id="updatePaLogisticform" method="post" action="<?php echo base_url('admin/paWarehouse/update/'.$this->uri->segment(4)); if(isset($_GET['invoice'])) { echo '?invoice'; }?>#submit" enctype="multipart/form-data">
					
					<div class="row lockDispatchCls invoiceEdit">
						<div class="col-sm-12">
						    <?php  
					        if($this->session->flashdata('item')){ ?>
            					<div class="alert alert-success">
            						<h4><?php echo $this->session->flashdata('item');?></h4> 
            					</div>
            					<div class="msg-div"><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></div>
            					<?php 
            					$showMsgDiv = 'true';
            				} else if($this->session->flashdata('error')){ ?>
								<div class="alert alert-danger">
            						<h4><?php echo $this->session->flashdata('error');?></h4> 
            					</div>
								<div class="msg-div error"><?php echo $this->session->flashdata('error'); $this->session->set_flashdata('error',''); ?></div>
            					<?php 
            					$showMsgDiv = 'true';
							}
            				echo $error = validation_errors();
            				if($error != ''){
            				    $showMsgDiv = 'true';
            				    echo '<div class="msg-div error">Found some errors</div>';
            				}
                			?>
                			<div class="msg-div flashMsgCls" style="display:none">Document removed successfully</div>
                		</div>
                		
						<div class="clearfix"></div>
						<div style="width:100%;clear:both;"></div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Carrier</label>
								<select class="form-control select2" name="truckingCompany" required >
									<option value="">Select Carrier</option>
									<?php 
										if(!empty($truckingCompanies)){
											foreach($truckingCompanies as $val){
											    $truckComArr[$val['id']] = $val['company'].' ('.$val['owner'].')';
												echo '<option value="'.$val['id'].'"';
												if($disp['truckingCompany']==$val['id']) { echo ' selected="selected" '; }
												echo '>'.$val['company'].' ('.$val['owner'].')</option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						
						
						<div class="col-sm-3 hide d-none">	     
							<div class="form-group">
								<label for="contain">Driver</label>
								<select class="form-control" name="driver" required readonly>
									<option value="<?=$disp['driver']?>">Select Driver</option>
									<?php 
										if(!empty($drivers)){
											foreach($drivers as $val){
												echo '<option value="'.$val['id'].'"';
												if('45'==$val['id']) { echo ' selected="selected" '; }
												echo '>'.$val['dname'].'</option>';
												$driverArr[$val['id']] = $val['dname'].'';
											}
										}
									?>
								</select>
							</div>
						</div>
					
						<div class="col-sm-3 <?php if(strtotime('2025-01-16') >= strtotime($disp['pudate'])) { echo 'hide d-none'; } ?>">	     
							<div class="form-group">
								<label for="contain">User</label> 
								<input readonly class="form-control" name="userid" value="<?=$userinfo[0]['uname']?>">
							</div>
						</div>
						<div class="col-sm-3 <?php if(strtotime('2025-01-17') <= strtotime($disp['pudate'])) { echo 'hide d-none'; } ?>">
							<div class="form-group">
								<label for="contain">Booked Under</label>
								<select class="form-control" name="bookedUnder" id="bookedUnder" required>
									<option value="0">Select Booked Under</option>
									<?php 
										if(!empty($truckingCompanies)){
											foreach($truckingCompanies as $val){
												echo '<option value="'.$val['id'].'" data-company="'.htmlspecialchars($val['company']).'"'; 
												if($disp['bookedUnder']==$val['id']) { echo ' selected="selected" '; }
												echo '>'.$val['company'].' ('.$val['owner'].')</option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-3 <?php if(strtotime('2025-01-16') >= strtotime($disp['pudate'])) { echo 'hide d-none'; } ?>">
							<div class="form-group">
								<label for="contain">Booked Under New</label>
								<select class="form-control" name="bookedUnderNew" id="bookedUnderNew" required>
									<option value="0">Select Booked Under</option>
									<?php 
										if(!empty($booked_under)){
											foreach($booked_under as $val){
												echo '<option value="'.$val['id'].'" data-company="'.htmlspecialchars($val['company']).'"';
												if($disp['bookedUnderNew']==$val['id']) { echo ' selected="selected" '; }
												echo '>'.$val['company'].' ('.$val['owner'].')</option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Shipment Type</label>
								<select class="form-control invoicePDF" required name="invoicePDF" id="shipmentType">
									<option value="">Select Invoice PDF</option>
									<!-- <option value="Drayage" <?php if($dispatchMeta['invoicePDF']=='Drayage') { echo 'selected'; }?>>Drayage (Import / Export)</option>
									<option value="Trucking" <?php if($dispatchMeta['invoicePDF']=='Trucking') { echo 'selected'; }?>>Trucking</option>
									<option value="Freight" <?php if($dispatchMeta['invoicePDF']=='Freight') { echo 'selected'; }?>>Freight Forwarding</option> -->
									<option value="Warehousing" <?php if($disp['invoicePDF']=='Warehousing') { echo 'selected'; }?>>Warehousing</option> 
								</select>
							</div>
						</div>
						<div class="col-sm-3 warehouseService" style="">
							<div class="form-group">
								<label for="contain">Type of Service</label>
								<select class="form-control" name="warehouseServices" id="warehouseServices">
									<option value="">Select Service</option>
								<?php 
										if(!empty($warehouseServices)){
											foreach($warehouseServices as $val){
												echo '<option value="'.$val['id'].'"';
												if($disp['warehouseServices']==$val['id']) { echo ' selected="selected" '; }
												echo '>'.$val['title'].' </option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-3  Drayage" <?php if($dispatchMeta['invoicePDF']!='Drayage') { echo 'style="display:none"'; }?>>
							<div class="form-group">
								<label for="contain">Drayage Type</label>
								<select class="form-control" name="drayageType"  id="drayageType">
									<option value="">Select Drayage Type</option>
									<option value="Export" <?php if($dispatchMeta['drayageType']=='Export') { echo 'selected'; }?>>Drayage Export</option>
									<option value="Import" <?php if($dispatchMeta['drayageType']=='Import') { echo 'selected'; }?>>Drayage Import</option>
								</select>
							</div>
						</div>
						<div class="col-sm-3 Drayage" <?php if($dispatchMeta['invoicePDF']!='Drayage') { echo 'style="display:none"'; }?>>
							<div class="form-group">
								<label for="contain">Equipment</label>
								<select class="form-control" name="invoiceDrayage" id="invoiceDrayage">
									<option value="">Select Equipment</option>
								<?php 
										if(!empty($drayageEquipments)){
											foreach($drayageEquipments as $val){
												echo '<option value="'.$val['name'].'"';
												if($dispatchMeta['invoiceDrayage']==$val['name']) { echo ' selected="selected" '; }
												echo '>'.$val['name'].' </option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-3 Trucking" <?php if($dispatchMeta['invoicePDF']!='Trucking') { echo 'style="display:none"'; }?>>
							<div class="form-group">
								<label for="contain">Equipment</label>
								<!-- <select class="form-control" name="invoiceTrucking" id="invoiceTrucking">
									<option value="">Select Equipment</option>
									<?php foreach($truckingArr as $trck){
									    echo '<option value="'.$trck.'"';
									    if($dispatchMeta['invoiceTrucking']==$trck) { echo 'selected'; }
									    echo '>'.$trck.'</option>';
									} ?>
								</select> -->
								<select class="form-control" name="invoiceTrucking" id="invoiceTrucking">
									<option value="">Select Equipment</option>
								<?php 
										if(!empty($truckingEquipments)){
											foreach($truckingEquipments as $val){
												echo '<option value="'.$val['name'].'"';
												if($dispatchMeta['invoiceTrucking']==$val['name']) { echo ' selected="selected" '; }
												echo '>'.$val['name'].' </option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-sm-3 erInformation" <?php if($dispatchMeta['invoicePDF']!='Drayage') { echo 'style="display:none"'; }?>>
							<div class="form-group">
								<label for="contain">Empty Pick-up Information</label>
								<select class="form-control" name="erInformation" id="erInformation" >
									<option value="">Select Empty Pick-up Information</option>
									<?php 
										if(!empty($erInformation)){
											foreach($erInformation as $val){
												echo '<option value="'.$val['id'].'"';
												if($dispatchMeta['erInformation']==$val['id']) { echo ' selected="selected" '; }
												echo '>'.$val['company'].' ('.$val['address'].', '.$val['city'].' '.$val['state'].')</option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						
					</div>
					
					<fieldset class="lockDispatchCls invoiceEdit">
						<legend>
							<input type="text" class="form-control" name="pickup" value="<?php if($disp['pickup']) { echo $disp['pickup']; } else { echo 'Pick Up'; } ?>">
						</legend>
						<button class="btn btn-success btn-sm pick-drop-btn pickup-btn-add" type="button">Add New +</button>
						
						<div class="row pickup-pcode-parent">
							<div class="col-sm-3">
								<div class="form-group">
									<label for="contain">Pick Up Date 
										<select name="trip">
											<option value="0">Select Trip</option>
											<?php for($i=1;$i<16;$i++){ 
												echo '<option value="'.$i.'"';
												if($disp['trip']==$i) { echo ' selected="selected" '; }
												echo '>'.$i.'</option>';
											} ?>
										</select>
									</label>
									<input name="pudate" type="text" class="form-control datepicker" required value="<?php echo $disp['pudate'];  ?>">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="contain">Appointment Type</label>
									<select class="form-control appointmentTypeP" name="appointmentTypeP" id="appointmentTypeP" required>
										<option value="">Select Appointment Type</option>
										<option value="Appointment" <?php if($disp['appointmentTypeP']=='Appointment') { echo 'selected'; }?>>By Appointment</option>
										<option value="FCFS" <?php if($disp['appointmentTypeP']=='FCFS') { echo 'selected'; }?>>First Come First Serve (FCFS)</option>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="contain">Pick Up Time</label>
									<div class="input-group mb-2">
										<input name="ptime" id="ptime" type="text" class="timeInput form-control" value="<?php echo $disp['ptime'];  ?>" readonly>
        							    <div class="tDropdown"></div>
										<div class="input-group-append">
											<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
										</div>
        							</div>
								</div>
							</div> 
							
							<div class="col-sm-4">
								<div class="form-group">
									<label for="contain">Pick Up Company (Location)</label>
									<div class="getAddressParent"><input type="text" id="plocation" class="form-control getAddress companyI" data-type="company" name="plocation" required value="<?php echo $plocation;?>"> </div>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="contain">Pick Up Address</label>
									<div class="getAddressParent">
										<input type="hidden" name="paddressid" class="addressidI" value="<?php echo $disp['paddressid'];?>">
										<input type="text" id="paddress" class="form-control getAddress addressI" data-type="address" name="paddress" value="<?php echo $paddress;?>"> 
									</div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="contain">Pick Up City</label>
									<div class="getAddressParent"><input type="text" id="pcity" class="form-control getAddress cityI" data-type="city" name="pcity" required value="<?php echo $pcity;?>"> </div> 
								</div>
							</div>
							<div class="col-sm-4 hide d-none">
								<div class="form-group">
									<label for="contain">Port of Loading / Export</label>
									<input type="text" class="form-control" name="pPort" value="<?php echo $disp['pPort'];?>">
								</div>
							</div>
							<div class="col-sm-4 hide d-none">
								<div class="form-group">
									<label for="contain">Port Address</label>
									<input type="text" class="form-control" name="pPortAddress" value="<?php echo $disp['pPortAddress'];?>">
								</div>
							</div>
							
							<div class="col-sm-5">
								<div class="form-group">
									<label for="contain">Description</label>
									<textarea class="form-control" name="metaDescriptionP"><?php echo $disp['metaDescriptionP'];?></textarea>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label for="contain">Quantity</label>
									<input type="text" class="form-control" name="quantityP" value="<?php echo $disp['quantityP'];?>">
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label for="contain">Weight</label>
									<input required type="text" class="form-control weight" name="weightP" value="<?php echo $disp['weightP'];?>">
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="contain">Commodity</label>
									<input required type="text" class="form-control" name="commodityP" value="<?php echo $disp['commodityP'];?>">
								</div>
							</div>
							
							<?php
								if($disp['pcode']=='') { $pcode = array(' '); }
								else { $pcode = explode('~-~',$disp['pcode']); }
								for($i=0;$i<count($pcode);$i++){
									if($i > 0) {
										$class = ' pcode-id-'.$i;
										$dContent = '<div class="input-group-text"><i data-cls=".pcode-id-'.$i.'" class="fa fa-trash code-delete"></i></div>';
									} else {
										$class = '';
										$dContent = '<div class="input-group-text pcode-add" data-cls=".pickup-notes-code"><strong>+</strong></div>';
									}
								?>
								<div class="col-sm-2 <?php echo $class;?>">
									<div class="form-group">
										<label for="contain">Pick Up#</label>
										<div class="input-group mb-2">
											<input name="pcode[]" type="text" class="form-control" value="<?php echo $pcode[$i]; ?>">
											<div class="input-group-append">
												<?php echo $dContent; ?>
											</div>
										</div>  
									</div>
								</div>
							<?php } ?>
							
							<div class="col-sm-12 pickup-notes-code">
								<div class="form-group">
									<label for="contain">Pickup Notes</label> 
									<textarea name="pnotes" class="form-control"><?php echo $disp['pnotes'] ?></textarea>
								</div>
							</div>
							
						</div>
					</fieldset>	
					
					<?php
						$extraCount = 2;
						$pextraCount = 2;
						$PDCode = 1;
						if($extraDispatch) {
							foreach($extraDispatch as $info){
								if($info['pd_type']=='pickup') {
									if($info['pd_meta'] != '') {
										$pdMeta = json_decode($info['pd_meta'],true);
									} else { $pdMeta = array(); }
								?>
								<fieldset class="pick-drop-both-<?php echo $info['id'];?> lockDispatchCls invoiceEdit">
									<legend>
										<input type="text" class="form-control" name="pickup1[]" value="<?php if($info['pd_title']) { echo $info['pd_title']; } else { echo 'Pick Up'; } ?>">
									</legend>
									<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-both-remove-btn" data-id="<?php echo $info['id'];?>" data-removeCls=".pick-drop-both-<?php echo $info['id'];?>" type="button">Remove</button>
									<div class="row pickup-pcode1<?php echo $info['id'];?>-parent">
										<div class="col-sm-3">
											<div class="form-group">
												<label for="contain">Pick Up Date</label>
												<input name="pudate1[]" type="text" class="form-control datepicker" required value="<?php if(!strstr($info['pd_date'],'0000')) { echo $info['pd_date']; } ?>">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="contain">Appointment Type</label>
												<select class="form-control appointmentTypeP1" name="appointmentTypeP1[]" required>
													<option value="">Select Appointment Type</option>
													<option value="Appointment" <?php if($info['appointmentType']=='Appointment') { echo 'selected'; }?>>By Appointment</option>
													<option value="FCFS" <?php if($info['appointmentType']=='FCFS') { echo 'selected'; }?>>First Come First Serve (FCFS)</option>
												</select>
											</div>
										</div>
										<!-- <div class="col-sm-3">
											<div class="form-group">
												<label for="contain">Pick Up Time</label>
												<input readonly name="ptime1[]" type="text" class="form-control timeDropdown" value="<?php echo $info['pd_time'];  ?>">
												
											</div>
										</div> -->
										<div class="col-sm-3">
												<div class="form-group">
												<label for="contain">Pick Up Time</label>
													<div class="input-group mb-2">
														<input readonly name="ptime1[]" type="text" class="timeInput form-control" value="<?php echo $info['pd_time'];  ?>">
														<div class="tDropdown"></div>
														<div class="input-group-append">
															<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
														</div>
													</div>
												</div>
											</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="contain">Pick Up Company (Location)</label>
												<div class="getAddressParent"><input type="text" id="plocation1" class="form-control location1 getAddress companyI" required data-type="company" name="plocation1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][0]; } else { echo $info['pd_location']; }?>"> </div>
											</div>
										</div>
										<div class="col-sm-5">
											<div class="form-group">
												<label for="contain">Pick Up Address</label>
												<div class="getAddressParent">
													<input type="hidden" name="paddressid1[]" class="addressidI" value="<?php echo $info['pd_addressid'];?>"> 
													<input type="text" id="paddress1" class="form-control paddress1 getAddress addressI" data-type="address" name="paddress1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][2]; } else { echo $info['pd_address']; } ?>"> 
												</div>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label for="contain">Pick Up City</label>
												<div class="getAddressParent"><input type="text" id="pcity1" class="form-control city1 getAddress cityI" required data-type="city" name="pcity1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][1]; } else { echo $info['pd_city']; } ?>"></div>  
											</div>
										</div>
										<div class="col-sm-5">
											<div class="form-group">
												<label for="contain">Description</label>
												<textarea class="form-control" name="metaDescriptionP1[]"><?php echo $info['metaDescription'];?></textarea>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label for="contain">Quantity</label>
												<input type="text" class="form-control" name="quantityP1[]" value="<?php echo $info['quantity'];?>">
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label for="contain">Weight</label>
												<input required type="text" class="form-control weight" name="weightP1[]" value="<?php echo $info['weight'];?>">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label for="contain">Commodity</label>
												<input required type="text" class="form-control" name="commodityP1[]" value="<?php echo $info['commodity'];?>">
											</div>
										</div>
										<div class="col-sm-4 hide d-none">
											<div class="form-group">
												<label for="contain">Port of Loading / Export</label>
												<input type="text" class="form-control" name="pPort1[]" value="<?php echo $info['pd_port'];?>">
											</div>
										</div>
										<div class="col-sm-4 hide d-none">
											<div class="form-group">
												<label for="contain">Port Address</label>
												<input type="text" class="form-control" name="pPortAddress1[]" value="<?php echo $info['pd_portaddress'];?>">
											</div>
										</div>
										
										
							
										<input name="pd_type1[]" type="hidden" value="pickup">
										<input name="pcodename[]" type="hidden" value="pc<?php echo $info['id'];?>">
										<input name="extrdispatchid1[]" type="hidden" value="<?php echo $info['id'];  ?>">
										
										<?php
											if($info['pd_code']=='') { $pcode1 = array(' '); }
											else { $pcode1 = explode('~-~',$info['pd_code']); }
											for($i=0;$i<count($pcode1);$i++){
												if($i > 0) {
													$class = ' pcode1-id-'.$PDCode;
													$dContent = '<div class="input-group-text"><i data-cls=".pcode1-id-'.$PDCode.'" class="fa fa-trash code-delete"></i></div>';
													} else {
													$class = '';
													$dContent = '<div class="input-group-text pcode1-add" data-name="pc'.$info['id'].'" data-cls=".pnotes1-'.$info['id'].'"><strong>+</strong></div>';
												}
												
											?>
											<div class="col-sm-2 <?php echo $class;?>">
												<div class="form-group">
													<label for="contain">Pick Up#</label>
													<div class="input-group mb-2">
														<input name="pcode1[pc<?php echo $info['id'];?>][]" type="text" class="form-control" value="<?php echo $pcode1[$i];?>">
														<div class="input-group-append">
															<?php echo $dContent; ?>
														</div>
													</div>  
												</div>
											</div>
											<?php 
												$PDCode++;
											} ?>
											<div class="col-sm-12 pnotes1-<?php echo $info['id'];?>">
												<div class="form-group">
													<label for="contain">Pickup Notes</label> 
													<textarea name="pnotes1[]" class="form-control"><?php echo $info['pd_notes'];  ?></textarea>
												</div>
											</div>
									</div>
								</fieldset>
								<?php
									$extraCount++;
									$pextraCount++;

								}
							}
						} ?> 
						
						<div class="sortable pickupExtra"></div>
						
						
						<fieldset class="lockDispatchCls invoiceEdit">
							<legend><input type="text" class="form-control" name="dropoff" value="<?php if($dispatchMeta['dropoff']) { echo $dispatchMeta['dropoff']; } else { echo 'Drop Off'; } ?>"></legend>
							<button class="btn btn-success btn-sm pick-drop-btn drop-btn-add" type="button">Add New +</button>
							
							<div class="row dropoff-pcode-parent">
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Drop Off Date</label>
										<input name="dodate" type="text" class="form-control datepicker" value="<?php if(!strstr($disp['dodate'],'0000')) { echo $disp['dodate']; } ?>">
										
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="contain">Appointment Type</label>
										<select class="form-control appointmentTypeD" name="appointmentTypeD" id="appointmentTypeD" required>
											<option value="">Select Appointment Type</option>
											<option value="Appointment" <?php if($disp['appointmentTypeD']=='Appointment') { echo 'selected'; }?>>By Appointment</option>
											<option value="FCFS" <?php if($disp['appointmentTypeD']=='FCFS') { echo 'selected'; }?>>First Come First Serve (FCFS)</option>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Drop Off Time</label>
										<div class="input-group mb-2">
											<input name="dtime" id="dtime" type="text" class="timeInput form-control" value="<?php echo $disp['dtime'];  ?>"  readonly>
											<div class="tDropdown"></div>
											<div class="input-group-append">
												<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="contain">Quantity</label>
										<input type="text" class="form-control" name="quantityD" value="<?php echo $disp['quantityD'];?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Drop Off Company (Location) &nbsp;</label> 
										<div class="getAddressParent"><input type="text" id="dlocation" class="form-control getAddress companyI" data-type="company" name="dlocation" required value="<?php echo $dlocation;?>"> </div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="contain">Drop Off Address</label>
										<div class="getAddressParent">
											<input type="hidden" name="daddressid" class="addressidI" value="<?php echo $disp['daddressid'];?>"> 
											<input type="text" id="daddress" class="form-control getAddress addressI" data-type="address" name="daddress" value="<?php echo $daddress;?>"> 
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Drop Off City</label>
										<div class="getAddressParent"><input type="text" id="dcity" class="form-control getAddress cityI" data-type="city" name="dcity" required value="<?php echo $dcity;?>"> </div>  
									</div>
								</div>
								<div class="col-sm-4 hide d-none">
									<div class="form-group">
										<label for="contain">Port of Discharge / Import</label>
										<input type="text" class="form-control" name="dPort" value="<?php echo $disp['dPort'];?>">
									</div>
								</div>
								<div class="col-sm-4 hide d-none">
									<div class="form-group">
										<label for="contain">Port Address</label>
										<input type="text" class="form-control" name="dPortAddress" value="<?php echo $disp['dPortAddress'];?>">
									</div>
								</div>
								
								
								
								
								<div class="col-sm-2">
									<div class="form-group">
										<label for="contain">Weight</label>
										<input required type="text" class="form-control weight" name="weightD" value="<?php echo $disp['weightD'];?>">
									</div>
								</div>
								
								<div class="col-sm-6">
									<div class="form-group">
										<label for="contain">Description</label>
										<textarea class="form-control" name="metaDescriptionD"><?php echo $disp['metaDescriptionD'];?></textarea>
									</div>
								</div>
									
								<?php
									if($disp['dcode']=='') { $dcode = array(' '); }
									else { $dcode = explode('~-~',$disp['dcode']); }
									for($i=0;$i<count($dcode);$i++){
										if($i > 0) {
											$class = ' dcode-id-'.$i;
											$dContent = '<div class="input-group-text"><i data-cls=".dcode-id-'.$i.'" class="fa fa-trash code-delete"></i></div>';
											} else {
											$class = '';
											$dContent = '<div class="input-group-text dcode-add" data-cls=".driver-note-code"><strong>+</strong></div>';
										}
									?>
									<div class="col-sm-2 <?php echo $class;?>">
										<div class="form-group">
											<label for="contain">Drop Off#</label>
											<div class="input-group mb-2">
												<input name="dcode[]" type="text" class="form-control" value="<?php echo $dcode[$i]; ?>">
												<div class="input-group-append">
													<?php echo $dContent; ?>
												</div>
											</div>  
										</div>
									</div>
								<?php } ?>
								
								<div class="col-sm-12 driver-note-code">
									<div class="form-group">
										<label for="contain">Drop Off Notes</label> 
										<textarea name="dnotes" class="form-control"><?php echo $disp['dnotes'] ?></textarea>
									</div>
								</div>
								
							</div>
						</fieldset>
						
						<div  id="sortable" class="sortable dropoffExtra lockDispatchCls">
						<?php
							$extraCount = 2; 
							if($extraDispatch) {
								foreach($extraDispatch as $info){ 
									if($info['pd_type']=='dropoff') {
										if($info['pd_meta'] != '') {
											$pdMeta = json_decode($info['pd_meta'],true);
										} else { $pdMeta = array(); }
									?>
									<fieldset class="ui-state-default pick-drop-both-<?php echo $info['id'];?> invoiceEdit">
										<legend>
											<input type="text" class="form-control" name="dropoff1[]" value="<?php if($info['pd_title']) { echo $info['pd_title']; } else { echo 'Drop Off'; } ?>">
										</legend>
										<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-both-remove-btn"  data-id="<?php echo $info['id'];?>" data-removeCls=".pick-drop-both-<?php echo $info['id'];?>" type="button">Remove</button>
										<div class="row dropoff1<?php echo $info['id'];?>-pcode-parent">
											<div class="col-sm-3">
												<div class="form-group">
													<label for="contain">Drop Off Date</label>
													<input name="dodate1[]" type="text" class="form-control datepicker" value="<?php if(!strstr($info['pd_date'],'0000')) { echo $info['pd_date']; } ?>">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label for="contain">Appointment Type</label>
													<select class="form-control appointmentTypeD1" name="appointmentTypeD1[]" required>
														<option value="">Select Appointment Type</option>
														<option value="Appointment" <?php if($info['appointmentType']=='Appointment') { echo 'selected'; }?>>By Appointment</option>
														<option value="FCFS" <?php if($info['appointmentType']=='FCFS') { echo 'selected'; }?>>First Come First Serve (FCFS)</option>
													</select>
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label for="contain">Drop Off Time</label>
													<div class="input-group mb-2">
														<input readonly name="dtime1[]" type="text" class="timeInput form-control" value="<?php echo $info['pd_time'];  ?>">
														<div class="tDropdown"></div>
														<div class="input-group-append">
															<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<label for="contain">Quantity</label>
													<input type="text" class="form-control" name="quantityD1[]" value="<?php echo $info['quantity'];?>">
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label for="contain">Drop Off Company (Location) &nbsp;</label>
													<div class="getAddressParent"><input type="text" class="form-control location1 getAddress companyI" data-type="company" name="dlocation1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][0]; } else { echo $info['pd_location']; }?>"> </div>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label for="contain">Drop Off Address</label>
													<div class="getAddressParent">
														<input type="hidden" name="daddressid1[]" class="addressidI" value="<?php echo $info['pd_addressid'];?>"> 
														<input type="text" id="daddress1" class="form-control getAddress addressI" data-type="address" name="daddress1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][2]; } else { echo $info['pd_address']; }?>"> 
													</div>
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label for="contain">Drop Off City</label>
													<div class="getAddressParent"><input type="text" class="form-control city1 getAddress cityI" data-type="city" name="dcity1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][1]; } else { echo $info['pd_city']; }?>"></div>
												</div>
											</div>
											<div class="col-sm-4 hide d-none">
												<div class="form-group">
													<label for="contain">Port of Discharge / Import</label>
													<input type="text" class="form-control" name="dPort1[]" value="<?php echo $info['pd_port'];?>">
												</div>
											</div>
											<div class="col-sm-4 hide d-none">
												<div class="form-group">
													<label for="contain">Port Address</label>
													<input type="text" class="form-control" name="dPortAddress1[]" value="<?php echo $info['pd_portaddress'];?>">
												</div>
											</div>
											
											
											
											<div class="col-sm-2">
												<div class="form-group">
													<label for="contain">Weight</label>
													<input required type="text" class="form-control weight" name="weightD1[]" value="<?php echo $info['weight'];?>">
												</div>
											</div>
								            <div class="col-sm-6">
												<div class="form-group">
													<label for="contain">Description</label>
													<textarea class="form-control" name="metaDescriptionD1[]"><?php echo $info['metaDescription'];?></textarea>
												</div>
											</div>
										
							
											<input name="dcodename[]" type="hidden" value="dc<?php echo $info['id'];?>">
											<input name="pd_type2[]" type="hidden" value="dropoff">
											<input name="extrdispatchid2[]" type="hidden" value="<?php echo $info['id'];  ?>">
											
											<?php
												if($info['pd_code']=='') { $dcode1 = array(' '); }
												else { $dcode1 = explode('~-~',$info['pd_code']); }
												for($i=0;$i<count($dcode1);$i++){
													if($i > 0) {
														$class = ' dcode1-id-'.$PDCode;
														$dContent = '<div class="input-group-text"><i data-cls=".dcode1-id-'.$PDCode.'" class="fa fa-trash code-delete"></i></div>';
														} else {
														$class = '';
														$dContent = '<div class="input-group-text dcode1-add" data-name="dc'.$info['id'].'" data-cls=".dropoff1'.$info['id'].'-pcode-parent"><strong>+</strong></div>';
													}
												?>
												<div class="col-sm-2 <?php echo $class;?>">
													<div class="form-group">
														<label for="contain">Drop Off#</label>
														<div class="input-group mb-2">
															<input name="dcode1[dc<?php echo $info['id'];?>][]" type="text" class="form-control" value="<?php echo $dcode1[$i];?>">
															<div class="input-group-append">
																<?php echo $dContent; ?>
															</div>
														</div> 
													</div>
												</div>
												<?php 
													$PDCode++;
												} ?>
											
											<div class="col-sm-12">
												<div class="form-group">
													<label for="contain">Drop Off Notes</label> 
													<textarea name="dnotes1[]" class="form-control"><?php echo $info['pd_notes']; ?></textarea>
												</div>
											</div>
										</div>
									</fieldset>
									<?php
										$extraCount++;
									}
								}
							} ?> 
							
							</div>
							
							
							<div class="row lockDispatchCls">
								<div class="col-sm-12"><p>&nbsp;</p></div>
							</div>
							
							
							<fieldset class="lockDispatchCls invoiceEdit">
								<legend>Add Dispatch Info:</legend>
								<button class="btn btn-success btn-sm pick-drop-btn dispatchInfo-btn" type="button">Add New +</button>
								<div class="row dispatchInfo-cls sortable">
									<?php
										$e = 1;
										if($dispatchInfoDetails) { 
											foreach($dispatchInfoDetails as $diVal) {
													// print_r($diVal['dispatchInfoId']);exit;

												$e++;
											?>
											<div class="ui-state-default col-sm-3 dispatchInfo-div-<?=$e?>">
												<div class="form-group">
													<div style="display: inline;color:#ff0047;" class="custom-checkbox my-1 mr-sm-2">
														<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".dispatchInfo-div-<?=$e?>" type="button" style="top:0px;">X</button>
														<select required name="dispatchInfoName[]" style="padding: 3px;margin: 3px;">
															<?php 
															    echo '<option value="">'.$diVal[0].'</option>';
																foreach($dispatchInfo as $di) {
																	echo '<option value="'.$di['id'].'"';
																	if($di['id'] == $diVal['dispatchInfoId']){ echo ' selected '; }
																	echo '>'.$di['title'].'</option>';
																}
															?>
														</select>
													</div>
													<input name="dispatchInfoValue[]" required type="text" class="form-control" value="<?=$diVal['dispatchValue']?>">
												</div>
											</div>
											<?php }
										}
									?>
								</div>
							</fieldset>
							
							<p>&nbsp;</p>
							
							<div class="row lockDispatchCls">
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Storage Rate</label>
										<div class="input-group mb-2">
											<div class="input-group-prepend">
												<div class="input-group-text">$</div>
											</div>
											<input required name="rate"  <?php if($disp['carrierPayoutCheck']=='1') { echo ' readonly'; } ?> type="number" min="0" step="0.01" class="form-control rateInput rate-cls" value="<?php echo $disp['rate']; ?>">
										</div> 
									</div>
								</div>
								<div class="col-sm-3" id="grizzlyRate" style="display: none;">
									<div class="form-group">
										<label for="contain">Brooker Rate</label>
										<div class="input-group mb-2">
											<div class="input-group-prepend">
												<div class="input-group-text">$</div>
											</div>
											<input name="agentRate"  type="number" min="0" step="0.01" id="agentRate" class="form-control agentRate rate-cls" value="<?php echo $disp['agentRate']; ?>">
											<span id="brookerPercentDisplay" style="position: absolute; right: 35px; top: 14px; color: #888; pointer-events: none;">(0)</span>
										</div> 
									</div>
								</div>
								<div class="col-sm-3" id="grizzlyPercentRate" style="display: none;">
									<div class="form-group">
										<label for="agentPercentRate">Agent Rate</label>
										<div class="input-group mb-2">
											<div class="input-group-prepend">
												<div class="input-group-text">%</div>
											</div>
											<input  name="agentPercentRate" type="number" min="0" step="0.01" id="agentPercentRate" class="form-control agentPercentRate rate-cls" 	value="<?php echo $disp['agentPercentRate']; ?>"  style="border-top-right-radius: 50px; border-bottom-right-radius: 50px;">
												<span id="agentRateDisplay" style="position: absolute; right: 35px; top: 14px; color: #888; pointer-events: none;">(0)</span>
										</div> 
									</div>
								</div>
								<div class="col-sm-3" id="grizzlyTotalAmt" style="display: none;">
									<div class="form-group">
										<label for="contain">Total Amount</label>
										<div class="input-group mb-2">
											<div class="input-group-prepend">
												<div class="input-group-text">$</div>
											</div>
											<input name="carrierPlusAgentRate" id="carrierPlusAgentRate" readonly type="number" step="0.01" class="form-control carrierPlusAgentRate" value="<?php echo $disp['carrierPlusAgentRate']; ?>">
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Invoice Amount</label>
										<div class="input-group mb-2">
											<div class="input-group-prepend">
												<div class="input-group-text">$</div>
											</div>
											<input name="parate"  <?php if($disp['carrierPayoutCheck']=='1') { echo ' readonly'; } ?> type="number" step="0.01" class="form-control parate rate-cls" required value="<?php echo $disp['parate']; ?>" data-price="<?php echo $disp['parate']; ?>">
										</div> 
										
									</div>
								</div>
								<?php 
							$otherRate = $otherPaRate = 0;
							if($otherChildInvoice){
								foreach($otherChildInvoice as $val){
									$otherRate = $otherRate + $val['rate']; 
									$otherPaRate = $otherPaRate + $val['parate']; 
								}
								?>
								<div class="col-sm-2">
    								<div class="form-group">
    									<label for="contain">PA Rate</label>
    									<div class="input-group mb-2">
    										<div class="input-group-prepend">
    											<div class="input-group-text">$</div>
    										</div>
    										<input name="otherrate" type="number"step="0.01" class="form-control" value="<?=$otherRate?>">
    									</div>
    								</div>
    							</div>
								<div class="col-sm-3">
    								<div class="form-group">
    									<label for="contain">PA Invoice Amount</label>
    									<div class="input-group mb-2">
    										<div class="input-group-prepend">
    											<div class="input-group-text">$</div>
    										</div>
    										<input name="otherparate" type="number"step="0.01" class="form-control" value="<?=$otherPaRate?>">
    									</div>
    								</div>
    							</div>
								<?php
							}
							?>
							
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Margin</label>
										<div class="input-group mb-2">
											<div class="input-group-prepend">
												<div class="input-group-text">$</div>
											</div>
											<input name="pamargin" readonly type="number" step="0.01" class="form-control pamargin" value="<?php echo $disp['pamargin']; ?>">
										</div>
									</div>
								</div> 
								
								<?php if($disp['rateLumper'] > 0){ ?>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="contain">Rate Lumper</label>
											<div class="input-group mb-2">
												<div class="input-group-prepend">
													<div class="input-group-text">$</div>
												</div>
												<input readonly name="rateLumper" step="0.01" type="number" min="0" class="form-control" value="<?php echo $disp['rateLumper']; ?>">
											</div> 
										</div>
									</div>
								<?php } ?>
								
								<div class="col-sm-12">
									<fieldset>
										<legend>Expense:</legend>
										<?php if($disp['carrierPayoutCheck']=='0') { ?>
											<button class="btn btn-success btn-sm pick-drop-btn expense-btn" type="button">Add New +</button>
										<?php } ?>
										<div class="row expense-cls">
											<?php
												$e = 1;
												$paRate = $disp['parate'];
												if($dispatchExpenseDetails) { 
													foreach($dispatchExpenseDetails as $expVal) {
														$e++;
														//if($expVal[0] == 'Discount') { $paRate = $paRate + $expVal[1]; }
														if(in_array($expVal['expenseInfoId'],$expenseN)){ $paRate = $paRate + $expVal['expenseInfoValue']; }
														else { $paRate = $paRate - $expVal['expenseInfoValue']; }
													?>
													<div class="col-sm-3 expense-div-<?=$e?>">
														<div class="form-group">
															<div style="display: inline;color:#ff0047;" class="custom-checkbox my-1 mr-sm-2">
																<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".expense-div-<?=$e?>" type="button" style="top:0px;">X</button>
																<select <?php if($disp['carrierPayoutCheck']=='1') { echo ' readonly'; }?> name="expenseName[]" class="expenseNameSelect expenseName-<?=$e?>" style="padding: 3px;margin: 3px;">
																	<?php 
																		foreach($expenses as $exp) {
																			echo '<option value="'.$exp['id'].'"';
																			if($exp['id'] == $expVal['expenseInfoId']){ echo ' selected '; }
																			echo '>'.$exp['title'].'</option>';
																		}
																	?>
																</select>
															</div>
															<input <?php if($disp['carrierPayoutCheck']=='1') { echo ' readonly'; }?> name="expensePrice[]" data-cls=".expenseName-<?=$e?>" required type="number" min="1" class="form-control expenseAmt" value="<?=$expVal['expenseInfoValue']?>" step="0.01">
														</div>
													</div>
													<?php }
												}
											?>
										</div>
									</fieldset>
								</div>
							</div>
							<p>&nbsp;</p>
							<div class="row lockDispatchCls">
								<div class="col-sm-4">
									<div class="form-group">
										<label for="contain">Company</label>
										<div class="getCompanyParent">
										    <input type="text" id="companies" class="form-control getCompany" name="company" required value="<?php echo $company;?>">
									    </div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="contain">Tracking #</label>
										<input required name="tracking" type="text" class="form-control" value="<?php echo $disp['tracking']; ?>">
									</div>
								</div>
								<!-- <div class="col-sm-4">
									<div class="form-group">
										<label for="contain"><?php if($dispatchMeta['invoicePDF']=='Drayage') { echo 'Container'; } else { echo 'Trailer'; }?> #</label>
										<input required name="trailer" type="text" class="form-control" value="<?php echo $disp['trailer']; ?>">
									</div>
								</div> -->
								<!-- <?php if($childTrailer) { ?>
    							<div class="col-sm-12">
    								<div class="form-group">
    									<label for="contain">Container / Trailers #</label>
    									<input name="childtrailer" readonly type="text" class="form-control" value="<?php 
    									$t = 1;
    									echo $disp['trailer'];
    									if($disp['trailer']!=''){ $t++; }
    									foreach($childTrailer as $val){
    									    if($t > 1){ echo ', '; }
    									    echo $val['trailer'];
    									    $t++;
    									}
    									if($otherChildInvoice){
											foreach($otherChildInvoice as $val){
												echo ', '.$val['trailer'];
											}
										}
    									?>">
    								</div>
    							</div>
    							<?php } ?> -->
								
							
								<!-- <div class="col-sm-12 invoiceEdit">
									<fieldset>
										<legend>Sub Invoice:</legend>
										<button class="btn btn-success btn-sm pick-drop-btn childInvoice-btn" type="button">Add New +</button>
										<button style="right:120px" class="btn btn-success btn-sm pick-drop-btn otherChildInvoice-btn" type="button">PA Add New +</button>
										<div class="row childInvoice-cls">
											<?php 
												$e = 1;
												if($disp['childInvoice'] != '') { 
													$childInvoice = explode(',',$disp['childInvoice']);
													foreach($childInvoice as $expVal) {
														$e++;
														$cPaRate = 0;
														$invoiceId = '';
															foreach($childTrailer as $val){ 
																if(trim($val['invoice']) == trim($expVal)){
																	$invoiceId = $val['id'];
																	$cPaRate = $val['parate'];
																	$cRate = $val['rate'];

																}
															}
													?>
													<div class="col-sm-3 childInvoice-div-<?=$e?>">
														<div class="form-group">
															<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".childInvoice-div-<?=$e?>" type="button" style="top:0px;">X</button> 
															<button class="btn btn-primary btn-sm" onclick="window.open('<?=base_url().'admin/outside-dispatch/update/'.$invoiceId?>', '_blank');" type="button">Go To</button>

															<input name="childInvoice[]" required type="text" class="form-control" value="<?=$expVal?>" placeholder="Invoice">
															<?php 
															
															?>
															<input name="childInvoiceRate[]" readonly type="text" class="form-control" value="Inv. Amt: $<?=$cPaRate?>" placeholder="Invoice Amount">
															<input name="" readonly type="text" class="form-control" value="Carrier Rate: $<?=$cRate?>" placeholder="Invoice Amount">
														</div>
													</div>
													<?php }
												}
												
												
											if($dispatchMeta['otherChildInvoice'] != '') { 
											    $otherChildInvoiceInfo = explode(',',$dispatchMeta['otherChildInvoice']);
												foreach($otherChildInvoiceInfo as $expVal) {
													
													$e++;
												
													$cPaRate = 0;
													$invoiceId = '';

													if ($otherChildInvoice) {
														foreach ($otherChildInvoice as $val) {
															if (trim($val['invoice']) == trim($expVal)) {
																$invoiceId = $val['id'];
																if ($val['parate'] > 0) { 
																	$cPaRate = 'Inv. Amt.: $' . $val['parate']; 
																} else { 
																	$cPaRate = 'Rate: $' . $val['rate']; 
																}
																$cRate = $val['rate'];
																break; 
															}
														}
													}

												?>
												<div class="col-sm-3 pa-invoice childInvoice-div-<?=$e?>">
													<div class="form-group">
														<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".childInvoice-div-<?=$e?>" type="button" style="top:0px;">X</button>
													
														<button class="btn btn-primary btn-sm" onclick="window.open('<?=base_url().'admin/dispatch/update/'.$invoiceId?>', '_blank');" type="button">Go To</button>


														<input name="otherChildInvoice[]" required type="text" class="form-control" value="<?=$expVal?>" placeholder="PA Invoice">
														<?php 
															
															?>
															<input name="otherChildInvoiceRate[]" readonly type="text" class="form-control" value="<?=$cPaRate?>" placeholder="PA Invoice Amount">
															<input name="" readonly type="text" class="form-control" value="Carrier Rate: $<?=$cRate?>" placeholder="Invoice Amount">
													</div>
												</div>
												<?php }
											}
											?>
										</div>
									</fieldset>
								</div> -->
							</div>
							
							
							<fieldset class="lockDispatchCls">
								<legend>Financial:</legend>
								<div class="row"> 
									
									<div class="col-sm-2">
										<div class="form-group">
											<label for="contain">Invoice #</label>
											<input <?php if(strtotime($disp['pudate']) > strtotime('2024-04-24')) { echo 'readonly'; } ?> name="invoice" type="text" class="form-control" value="<?php echo $disp['invoice']; ?>">
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="contain">Week</label>
											<input name="dWeek" type="text" readonly class="form-control" value="<?php echo $disp['dWeek']; ?>">
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="contain">Payout Amount</label>
											<input name="payoutAmount" type="text" class="form-control" value="<?php echo $disp['payoutAmount']; ?>">
										</div>
									</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="contain">Partial Amount</label>
										<input name="partialAmount" type="number" min="0" step="0.01" class="form-control" value="<?php echo ($disp['partialAmount']) ? $disp['partialAmount']: 0; ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="contain">Payable Amount</label>
										<input name="payableAmt" readonly type="number" min="0" step="0.01" class="form-control" value="<?php echo ($disp['payableAmt']) ? $disp['payableAmt']: 0; ?>">
									</div>
								</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="contain">Expected Pay Date</label>
											<input readonly name="expectPayDate" type="text" class="form-control datepicker" value="<?php if($disp['expectPayDate']=='0000-00-00') { echo 'TBD'; } else { echo $disp['expectPayDate']; } ?>">
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="contain">Invoice Type</label>
											<select class="form-control invoiceTypeCls" name="invoiceType">
												<option value="">Select Invoice Type</option>
												<option value="RTS" <?php if($disp['invoiceType']=='RTS') { echo 'selected'; }?>>RTS</option>
												<option value="Direct Bill" <?php if($disp['invoiceType']=='Direct Bill') { echo 'selected'; }?>>Direct Bill</option>
												<option value="Quick Pay" <?php if($disp['invoiceType']=='Quick Pay') { echo 'selected'; }?>>Quick Pay</option>
											</select>
										</div>
									</div>
									<?php 
										if($disp['invoiceType']=='RTS') { $invoiceType = 'RTS'; }
										elseif($disp['invoiceType']=='Direct Bill') { $invoiceType = 'DB'; }
										elseif($disp['invoiceType']=='Quick Pay') { $invoiceType = 'QP'; }
										else { $invoiceType = ''; }
									?>
									
									<div class="col-sm-8 invoiceCheckboxCls">
										<label style="display:block">&nbsp;</label>
										
										<div class="form-group invoiceqp"  style="display:<?php if($disp['invoiceType']=='') { echo 'none'; } else { echo 'inline'; } ?>">
											<div class="custom-control custom-checkbox my-1 mr-sm-2" style="display: inline;">
												<input type="hidden" name="invoiceReady" value="<?=$disp['invoiceReady']?>" id="invoiceReadyhidden">
												<input type="checkbox" class="custom-control-input invoiceCheckCls" onclick="updateValueOnRuntime('invoiceTypeInvoiceReady','invoiceReadyhidden')" id="invoiceTypeInvoiceReady" name="invoiceReady" <?php if($disp['invoiceReady']=='1') { echo 'checked'; } ?> value="<?=$disp['invoiceReady']?>">
												<label class="custom-control-label" for="invoiceTypeInvoiceReady"><span class="invoiceTitle"><?=$invoiceType?></span> Ready To Submit</label>
											</div>
										</div>
										<div class="form-group"  style="display:<?php if($disp['invoiceType']=='') { echo 'none'; } else { echo 'inline'; } ?>">
											<div class="custom-control custom-checkbox my-1 mr-sm-2" >
												 <input type="hidden" name="invoiced" value="<?=$disp['invoiced']?>" id="invoicedHidden">
												<input type="checkbox" class="custom-control-input invoiceCheckCls" onclick="updateValueOnRuntime('invoiceTypeInvoiced','invoicedHidden')" id="invoiceTypeInvoiced" name="invoiced" <?php if($disp['invoiced']=='1') { echo 'checked'; } ?> value="<?=$disp['invoiced']?>">
												<label class="custom-control-label" for="invoiceTypeInvoiced"><span class="invoiceTitle"><?=$invoiceType?></span> Invoiced</label>
											</div>
										</div>
										<div class="form-group"  style="display:<?php if($disp['invoiceType']=='') { echo 'none'; } else { echo 'inline'; } ?>">
											<div class="custom-control custom-checkbox my-1 mr-sm-2">
												<input type="hidden" name="invoicePaid" value="<?=$disp['invoicePaid']?>" id="invoicePaidHidden">
												<input type="checkbox" class="custom-control-input invoiceCheckCls" onclick="updateValueOnRuntime('invoiceTypeInvoicePaid','invoicePaidHidden')" id="invoiceTypeInvoicePaid" name="invoicePaid" <?php if($disp['invoicePaid']=='1') { echo 'checked'; } ?> value="<?=$disp['invoicePaid']?>">
												<label class="custom-control-label" for="invoiceTypeInvoicePaid"><span class="invoiceTitle"><?=$invoiceType?></span> Paid</label>
											</div>
										</div>
										<div class="form-group"  style="display:<?php if($disp['invoiceType']=='') { echo 'none'; } else { echo 'inline'; } ?>">
											<div class="custom-control custom-checkbox my-1 mr-sm-2">
												 <input type="hidden" name="invoiceClose" value="<?=$disp['invoiceClose']?>" id="invoiceCloseHidden"> 
												<input type="hidden" name="invoiceCloseOld" value="<?= $disp['invoiceClose'] ?? 0 ?>">
												<input type="checkbox" class="custom-control-input invoiceCheckCls"  onclick="updateValueOnRuntime('invoiceTypeInvoiceClose','invoiceCloseHidden')" id="invoiceTypeInvoiceClose" name="invoiceClose" <?php if($disp['invoiceClose']=='1') { echo 'checked'; } ?>  value="<?=$disp['invoiceClose']?>">
												<label class="custom-control-label" for="invoiceTypeInvoiceClose"><span class="invoiceTitle"><?=$invoiceType?></span> Closed</label>
											</div>
										</div>
									</div>
								</div>	
    							<div class="row paid-close-date" <?php //if(!isset($_GET['invoice']) && $disp['invoiceType']!='RTS'){ echo 'style="display:none"'; } ?>>
    								<div class="col-sm-3">
    									<div class="form-group">
    										<label for="contain">Ready Submit Date</label>
    										<input name="invoiceReadyDate" type="text" class="form-control datepicker invoiceReadyDate" value="<?php echo $disp['invoiceReadyDate']; ?>">
    									</div>
    								</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="contain">Invoice Date</label>
											<input name="invoiceDate" type="text" class="form-control datepicker invoiceDate" value="<?php if($disp['invoiceDate']=='0000-00-00') { echo 'TBD'; } else { echo $disp['invoiceDate']; } ?>">
										</div>
									</div>
    								<div class="col-sm-3">
    									<div class="form-group">
    										<label for="contain">Invoice Paid Date</label>
    										<input name="invoicePaidDate" type="text" class="form-control datepicker invoicePaidDate" value="<?php echo $disp['invoicePaidDate']; ?>">
    									</div>
    								</div>
    								<div class="col-sm-3">
    									<div class="form-group">
    										<label for="contain">Invoice Close Date</label>
    										<input name="invoiceCloseDate" type="text" class="form-control datepicker invoiceCloseDate" value="<?php echo $disp['invoiceCloseDate']; ?>">
    									</div>
    								</div>
    							</div>
								
							</fieldset>
							
							<p>&nbsp;</p>
							
						
							<p>&nbsp;</p>
							
							<div class="row lockDispatchCls">
								<div class="col-sm-12 invoiceEdit">
									<div class="row">
									    <div class="col-sm-5">
									        <div class="custom-control custom-checkbox my-1 mr-sm-2" style="display: inline;color:#ff0047;">
    											<input type="checkbox" class="custom-control-input" id="delivered" name="delivered" value="yes" <?php if($disp['delivered']=='yes') { echo ' checked'; } ?>>
    											<label class="custom-control-label" for="delivered">Delivered</label>
    										</div>
    										<p>&nbsp;</p>
									    </div>
										<div class="col-sm-2"></div>
										<div class="col-sm-5" style="display: flex; gap: 10px;">
											<div class="form-group" style="flex: 1;">
											<label for="contain">Carrier Payment Type</label>
											<select class="form-control carrierPaymentTypeCls" name="carrierPaymentType" id="carrierPaymentType">
												<option value="">Select Payment Type</option>
												<!-- <option value="Direct Bill" <?php if($disp['carrierPaymentType']=='Direct Bill') { echo 'selected'; }?>>Direct Bill</option> -->
												<option value="Standard Billing" <?php if($disp['carrierPaymentType']=='Standard Billing') { echo 'selected'; }?>>Standard Billing</option>
												<option value="Quick Pay" <?php if($disp['carrierPaymentType']=='Quick Pay') { echo 'selected'; }?>>Quick Pay</option>
												<option value="Zelle" <?php if($disp['carrierPaymentType']=='Zelle') { echo 'selected'; }?>>Zelle</option>
											</select>
											</div>
											<div class="form-group d-none" id="factoringTypeDev" style="flex: 1;">
											<label for="contain">Factoring Type</label>
											<select class="form-control factoringTypeCls" name="factoringType" id="factoringType">
												<option value="">Select a Type</option>
												<option value="Direct Payment" <?php if($disp['factoringType']=='Direct Payment') { echo 'selected'; }?>>Direct Payment</option>
												<option value="Factoring" <?php if($disp['factoringType']=='Factoring') { echo 'selected'; }?>>Factoring</option>
											</select>
											</div>
											<div class="form-group d-none" id="factoringCompanyDev" style="flex: 1;">
											<label for="contain">Factoring companies</label>
											<select class="form-control" name="factoringCompany" id="factoringCompany">
												<option value="">Select a company</option>
												    <?php foreach ($factoringCompanies as $company): ?>
													<option value="<?php echo $company['id']; ?>"
														<?php if ($disp['factoringCompany'] == $company['id']) echo 'selected'; ?>>
														<?php echo $company['company']; ?>
													</option>
												<?php endforeach; ?>
											</select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row lockDispatchCls">
								<div class="col-sm-12 invoiceEdit">
									<div class="row">
										<div class="col-sm-5">
											<div class="form-group">
												<div class="custom-control custom-checkbox my-1 mr-sm-2">
													<input type="checkbox" class="custom-control-input bolrc" id="customControlInline" name="inwrd" value="AK" <?php if($disp['inwrd']=='AK') { echo ' checked'; } ?>>
													<label class="custom-control-label" for="customControlInline">Inward RD</label>  
													<a data-cls=".d-bol" href="#" class="download-pdf">Download All</a>
												</div>
												
												<input name="inwrd_d[]" multiple type="file" class="form-control">
											</div>
											<label for="contain">&nbsp;</label><br>
											<?php if(!empty($documents)) { 
												foreach($documents as $doc) {
													if($doc['type']=='inwrd') { 
													    $pdfArray[] = array('warehouse--inwrd',$doc['fileurl']);
													    echo '<a class="d-pdf d-bol" href="'.base_url('admin/download_pdf/warehouse--inwrd/'.$doc['fileurl']).'">download</a>';
														echo '<span class="doc-file">
													    <a target="_blank" download href="'.base_url().'assets/warehouse/inwrd/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
														if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/paWarehouse/removefile/inwrd/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
														echo '<a target="_blank" href="'.base_url('assets/warehouse/inwrd/').''.$doc['fileurl'].'?id='.rand(10,99).'">
														<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
													}
												}
											}
											// if(!empty($otherDocuments)) { 
											// foreach($otherDocuments as $doc) {
											// 	if($doc['type']=='bol') { 
											// 		$pdfArray[] = array('upload',$doc['fileurl']);
											// 		    echo '<a class="d-pdf d-bol" href="'.base_url('admin/download_pdf/upload/'.$doc['fileurl']).'">download</a>';
											// 			echo '<span class="doc-file">
											// 		    <a target="_blank" download href="'.base_url().'admin/download_pdf/upload/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
											// 			if($doc['otherParent'] != 'yes') { echo '<a href="'.base_url().'admin/dispatch/removefile/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
											// 			echo '<a target="_blank" href="'.base_url('assets/upload/').''.$doc['fileurl'].'?id='.rand(10,99).'">
											// 			<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
											// 	}
											// }
											// }
											?>
										</div>												
										<div class="col-sm-2"></div>
										<div class="col-sm-5">
											<div class="form-group">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<div class="custom-control custom-checkbox">
														<input type="hidden" value="0" name="carrierInvoiceCheck">
														<input type="checkbox" class="custom-control-input" id="carrierInvoiceCheck" name="carrierInvoiceCheck" value="1" <?php if($disp['carrierInvoiceCheck']=='1') { echo 'checked'; } ?>>
														<label for="carrierInvoiceCheck" class="custom-control-label">Carrier Invoice</label>
														<a data-cls=".d-carrierInvoice" href="#" class="download-pdf ml-2">Download All</a>
													</div>
												</div>

												<div class="d-flex gap-2" id="carrierInvoiceDev">
													<div style="">
														<input name="carrierInvoice[]" multiple type="file" class="form-control">
													</div>
													<div style="width: 40%; margin-top: -32px;" class="ml-2" id="carrierInvoiceRefNoDiv">
														<label for="carrierInvoiceRefNo" class="mb-2">Invoice Ref No</label>
														<input name="carrierInvoiceRefNo" type="text" class="form-control" value="<?php echo $disp['carrierInvoiceRefNo']; ?>">
													</div>
												</div>

												<?php if(!empty($documents)) { 
													foreach($documents as $doc) {
														if($doc['type']=='carrierInvoice') { 
														    $pdfArray[] = array('warehouse--carrierInvoice',$doc['fileurl']);
														    echo '<a class="d-pdf d-carrierInvoice" href="'.base_url('admin/download_pdf/warehouse--carrierInvoice/'.$doc['fileurl']).'">download</a>';
															echo '<span class="doc-file">
													        <a target="_blank" download href="'.base_url().'assets/warehouse/carrierInvoice/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
															if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/paWarehouse/removefile/carrierInvoice/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
															echo '<a target="_blank" href="'.base_url('assets/warehouse/carrierInvoice/').''.$doc['fileurl'].'?id='.rand(10,99).'">
															<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
														}
													}
												}
												?>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12 invoiceEdit">
									<div class="row">
										<div class="col-sm-5">
											<div class="form-group">
												<div class="custom-control custom-checkbox my-1 mr-sm-2">
													<input type="checkbox" class="custom-control-input outwrd" id="customControlInlineOutwrd" name="outwrd" value="AK" <?php if($disp['outwrd']=='AK') { echo ' checked'; } ?>>
													<label class="custom-control-label" for="customControlInlineOutwrd">Outward DD</label>  
													<a data-cls=".d-outwrd" href="#" class="download-pdf">Download All</a>
												</div>
												
												<input name="outwrd_d[]" multiple type="file" class="form-control">
											</div>
											<label for="contain">&nbsp;</label><br>
											<?php if(!empty($documents)) { 
												foreach($documents as $doc) {
													if($doc['type']=='outwrd') { 
													    $pdfArray[] = array('warehouse--outwrd',$doc['fileurl']);
													    echo '<a class="d-pdf d-outwrd" href="'.base_url('admin/download_pdf/warehouse--outwrd/'.$doc['fileurl']).'">download</a>';
														echo '<span class="doc-file">
													    <a target="_blank" download href="'.base_url().'assets/warehouse/outwrd/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
														if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/paWarehouse/removefile/outwrd/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
														echo '<a target="_blank" href="'.base_url('assets/warehouse/outwrd/').''.$doc['fileurl'].'?id='.rand(10,99).'">
														<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
													}
												}
											}
											// if(!empty($otherDocuments)) { 
											// foreach($otherDocuments as $doc) {
											// 	if($doc['type']=='bol') { 
											// 		$pdfArray[] = array('upload',$doc['fileurl']);
											// 		    echo '<a class="d-pdf d-bol" href="'.base_url('admin/download_pdf/upload/'.$doc['fileurl']).'">download</a>';
											// 			echo '<span class="doc-file">
											// 		    <a target="_blank" download href="'.base_url().'admin/download_pdf/upload/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
											// 			if($doc['otherParent'] != 'yes') { echo '<a href="'.base_url().'admin/dispatch/removefile/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
											// 			echo '<a target="_blank" href="'.base_url('assets/upload/').''.$doc['fileurl'].'?id='.rand(10,99).'">
											// 			<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
											// 	}
											// }
											// }
											?>
										</div>
										<div class="col-sm-2"></div>
										<div class="col-sm-5">
											<div class="form-group">
												<label>Carrier Invoice Date</label>				
												<input name="custInvDate" type="text" class="form-control datepicker" value="<?php if($disp['custInvDate']!='') { echo $disp['custInvDate']; } ?>">
											</div>
											
										</div>
									</div>
								</div>
								
								<div class="col-sm-12 invoiceEdit">
									<div class="row">
										<div class="col-sm-5">
											<div class="form-group"> 
												<div class="custom-control custom-checkbox my-1 mr-sm-2">
													<input type="checkbox" name="gd" class="custom-control-input bolrc" id="customControlInlinegd" value="AK" <?php if($disp['gd']=='AK') { echo ' checked'; } ?>>
													<label class="custom-control-label" for="customControlInlinegd">Customer Payment Proof</label> 
													<a data-cls=".d-gd" href="#" class="download-pdf">Download All</a>
												</div>
												
												<input name="gd_d" type="file" class="form-control">
											</div>
											
											<label for="contain">&nbsp;</label><br>
											<?php 
											$gdfile = '';
											if(!empty($documents)) { 
												foreach($documents as $doc) {
													if($doc['type']=='gd') { 
													    $gdfile = 'yes';
													    $pdfArray[] = array('warehouse--gd',$doc['fileurl']);
													    echo '<a class="d-pdf d-gd" href="'.base_url('admin/download_pdf/warehouse--gd/'.$doc['fileurl']).'">download</a>';
														echo '<span class="doc-file">
													    <a target="_blank" download href="'.base_url().'assets/warehouse/gd/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
														if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/paWarehouse/removefile/gd/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
														echo '<a target="_blank" href="'.base_url('assets/warehouse/gd/').''.$doc['fileurl'].'?id='.rand(10,99).'">
														<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
													}
												}
											}
											// if(!empty($otherDocuments)) { 
											// 	foreach($otherDocuments as $doc) {
											// 		if($doc['type']=='gd') { 
											// 			$gdfile = 'yes';
											// 			$pdfArray[] = array('upload',$doc['fileurl']);
											// 			echo '<a class="d-pdf d-gd" href="'.base_url('admin/download_pdf/upload/'.$doc['fileurl']).'">download</a>';
											// 			echo '<span class="doc-file">
											// 			<a target="_blank" download href="'.base_url().'admin/download_pdf/upload/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
											// 			if($doc['otherParent'] != 'yes') { echo '<a href="'.base_url().'admin/dispatch/removefile/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
											// 			echo '<a target="_blank" href="'.base_url('assets/upload/').''.$doc['fileurl'].'?id='.rand(10,99).'">
											// 			<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
											// 		}
											// 	}
											// }
											?>
											<input type="hidden" name="gdfile" value="<?=$gdfile?>">
										</div>
										<div class="col-sm-2"></div>
										<div class="col-sm-5" id="carrierPaymentProof">
											<div class="form-group" id="custDueDate">
												<label>Carrier Due Date</label>
												<input readonly name="custDueDate" type="text" class="form-control datepicker" value="<?php if($disp['custDueDate']!='') { echo $disp['custDueDate']; } ?>">
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12 invoiceEdit">
									<div class="row">
										<div class="col-sm-5">
											<div class="form-group"> 
												<label>Customer Invoice</label> 
													<a data-cls=".d-paInvoice" href="#" class="download-pdf">Download All</a>
													<input name="paInvoice[]" type="file" class="form-control">
											</div>
											
											<label for="contain">&nbsp;</label><br>
											<?php 
											if(!empty($documents)) { 
												foreach($documents as $doc) {
													if($doc['type']=='paInvoice') { 
													    $pdfArray[] = array('warehouse--invoice',$doc['fileurl']);
													    echo '<a class="d-pdf d-paInvoice" href="'.base_url('admin/download_pdf/warehouse--invoice/'.$doc['fileurl']).'">download</a>';
														echo '<span class="doc-file">
													    <a target="_blank" download href="'.base_url().'assets/warehouse/invoice/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
														if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/paWarehouse/removefile/invoice/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
														echo '<a target="_blank" href="'.base_url('assets/warehouse/invoice/').''.$doc['fileurl'].'?id='.rand(10,99).'">
														<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
													}
												}
											}
											?>
										</div>
										<div class="col-sm-2">					
										</div>
										<div class="col-sm-5">
											<div class="form-group" id="carrierPayoutCheckboxDate">
												<div class="custom-control custom-checkbox my-1 mr-sm-2">
													<input type="hidden" value="0" name="carrierPayoutCheck">
													<input type="checkbox" class="custom-control-input" id="carrierPayoutCheck" name="carrierPayoutCheck" value="1" <?php if($disp['carrierPayoutCheck']=='1') { echo ' checked'; } ?>>
													<label for="carrierPayoutCheck" class="custom-control-label carrierPayoutCheck">Carrier Payout Date</label>
												</div>
												<input name="carrierPayoutDate" id="carrierPayoutDate" type="text" class="form-control datepicker" value="<?php if($disp['carrierPayoutDate']=='0000-00-00') { echo ''; } else { echo $disp['carrierPayoutDate']; } ?>">
											</div>
											<div class="form-group"> 
												<div class="carrier-control carrier-checkbox my-1 mr-sm-2">
													<input type="checkbox" name="carrier_gd" class="carrier-control-input" id="carrierControlInlinegd" value="AK" <?php if($disp['carrierGd']=='AK') { echo ' checked'; } ?>>
													<label class="carrier-control-label" for="carrierControlInlinegd">Carrier Payment Proof</label> 
													<a data-cls=".carrier-d-gd" href="#" class="download-pdf">Download All</a>
												</div>
												
												<input name="carrier_gd_d[]" type="file" class="form-control" multiple>
											</div>
											
											<label for="contain">&nbsp;</label><br>
											<?php 
											$carriergdfile = '';
											if(!empty($documents)) { 
												foreach($documents as $doc) {
													if($doc['type']=='carrierGd') { 
													    $carriergdfile = 'yes';
													    $pdfArray[] = array('warehouse--gd',$doc['fileurl']);
													    echo '<a class="d-pdf carrier-d-gd" href="'.base_url('admin/download_pdf/warehouse--gd/'.$doc['fileurl']).'">download</a>';
														echo '<span class="doc-file">
													    <a target="_blank" download href="'.base_url().'assets/warehouse/gd/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
														if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/paWarehouse/removefile/gd/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
														echo '<a target="_blank" href="'.base_url('assets/warehouse/gd/').''.$doc['fileurl'].'?id='.rand(10,99).'">
														<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
													}
												}
											}
											?>
											<br>
											<input type="hidden" name="carriergdfile" value="<?=$carriergdfile?>">
											<?php 
											$showRemittanceButton = false;
											if (!empty($documents)) {
												foreach ($documents as $doc) {
													if ($doc['type'] == 'carrierGd') {
														$showRemittanceButton = true;
														break;
													}
												}
											}
											if ($showRemittanceButton) { 
											?>
												<!-- <a class="btn btn-success openCarrierEmailModal" 
												data-id="<?= $disp['id'] ?>" 
												data-dTable="dispatchWarehouse"
												href="#"
												data-toggle="modal"
												data-target="#emailCarrierPaymenteModal">
												Send Remittance Proof
												</a> -->
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
								
								<?php if($pdfArray){ ?>
    							<div class="col-sm-12">
    								<div class="form-group"><button id="download-pdfs" class="btn btn-success btn-sm download-all-pdf" type="button">Download All Files</button><br><br></div>
    							</div>
    							<?php } ?>
								
							
								<div class="col-sm-6">
									<div class="form-group">
										<label for="contain">Shipment Notes</label>
										<input name="status" type="text" class="form-control statusCls" data="<?=$disp['status']?>" value="<?php echo $disp['status']; ?>">
									</div>
								</div>
								<div class="col-sm-12 row">	
									<div class="col-sm-6">
										<div class="form-group">
											<label for="contain">Shipment Status</label> 
											<select name="driver_status" class="form-control">
												<?php
													foreach($shipmentStatus as $ds){
														echo '<option value="'.$ds['title'].'"';
														if($disp['driver_status'] == $ds['title']) { echo ' selected'; }
														echo '>'.$ds['title'].'</option>';
													}
												?>
											</select>
										</div>
									</div>
								</div>								
								<div class="col-sm-12"  id="submit">
									<div class="form-group">
										<label for="contain">Shipment Remarks</label> 
										<textarea name="notes" class="form-control"><?php echo $disp['notes'] ?></textarea>
									</div>
								</div>
								
								<div class="col-sm-12">
									<div class="form-group">
										<label for="contain">Invoice Description</label> 
										<textarea <?php if($invoiceType=='DB' || $invoiceType=='QP'){ echo 'required'; } ?> name="invoiceNotes" id="invoiceNotes" class="form-control invoiceNotes"><?php echo $disp['invoiceNotes'] ?></textarea>
									</div>
								</div>
							
							<div class="row">
								<div class="col-sm-3"> 
									<div class="form-group">
										<input type="submit" name="save" value="Update PA Warehousing" class="btn btn-primary"/>
									</div>
								</div>
								<!-- <?php if(isset($_GET['invoice'])){ ?> -->
									<!-- <?php } ?>		 -->
								<?php if(checkPermission($this->session->userdata('permission'),'invoice')){ ?>
									<div class="col-sm-3" id="generateInvButton"> 
    								<div class="form-group">
    									<a class="btn btn-success editInvoice" data-id="<?=$disp['id']?>" data-dTable="warehouse_dispatch" 
    									href="<?php echo base_url('Invoice/downloadInvoicePDF/'.$disp['id']);?>?dTable=warehouse_dispatch" data-toggle="modal" 
    									data-target="#editInvoiceModal">Generate Invoice</a>
    								</div>
    								</div>	
								<?php } ?>	
    							
    							<div class="col-sm-3 text-center"> 
    								<div class="form-group">
    									<div class="custom-control custom-checkbox my-1 mr-sm-2">
    									    <input type="hidden" value="0" name="lockDispatch">
    										<input type="checkbox" name="lockDispatch" id="customLockDispatch" class="custom-control-input" value="1" <?php if($disp['lockDispatch']=='1') { echo ' checked'; } ?>>
    										<label class="custom-control-label" for="customLockDispatch">Lock Dispatch</label>
    									</div>
    								</div>
    							</div>
								<?php if(checkPermission($this->session->userdata('permission'),'odispatch')){ ?> 
									<div class="col-sm-3" id="rateButton"> 
											<div class="form-group">
											<!-- <a class="btn btn-success" href="<?php echo base_url('Invoice/downloadRateLoadConfirmationPDF?id='.$disp['id']);?>">Generate Rate Confirmation</a> -->
											</div>
										</div>
										<?php } ?>
							</div>
							<div class="row">
						    <div class="col-sm-12">
						       
						    </div>
						</div>
				</form>
			
			
			    <?php if($dispatchLog){ ?>
			        
			        <div class="row">
			            <div class="col-sm-12">
			                <p>&nbsp;</p>
			                <h3>Update History</h3>
    			            <div class="table-responsive">
                    			<table class="table table-bordered" id="dataTable11" width="100%" cellspacing="0">
                    				<thead style="position:sticky;top:0">
                                        <tr>
                    						<th>Sr no.</th>
                    						<th>User</th>
                    						<th>IP</th>
                    						<th>Date</th>
                    						<th>Columns</th>
                    					</tr> 
                    				</thead>
                    				<tbody>
                    				    <?php 
                    				    $i = 1;
                    				    foreach($dispatchLog as $log){
                    				        echo '<tr><td>'.$i.'</td><td>'.$log['uname'].'</td><td>'.$log['ip_address'].'</td><td>'.date('d M Y h:iA',strtotime($log['rDate'])).'</td><td>';
                    				        if($log['history'] != ''){
                    				            $history = json_decode($log['history'],true);
                    				            foreach($history as $val){
                    				                //echo $val[0].', ';
                    				                
													$old = $val[2];
													$new = $val[3];
													if($val[1]=='invoiceReady' || $val[1]=='invoiceClose' || $val[1]=='invoicePaid' || $val[1]=='invoiced' || $val[1]=='lockDispatch'){
														if($old=='0'){ $old = 'Uncheck'; }
														if($old=='1'){ $old = 'Checked'; }
														if($new=='0'){ $new = 'Uncheck'; }
														if($new=='1'){ $new = 'Checked'; }
													}
													// print_r($val[1]);exit;
													if($val[1]=='bol' || $val[1]=='rc' || $val[1]=='gd'){
														if($old=='AK'){ $old = 'Checked'; }
														else{ $old = 'Uncheck'; }
														if($new=='AK'){ $new = 'Checked'; }
														else{ $new = 'Uncheck'; }
													}
													if($val[1]=='driver'){
														if(array_key_exists($old,$driverArr)){ $old = $driverArr[$old]; }
														if(array_key_exists($new,$driverArr)){ $new = $driverArr[$new]; }
													}
													if($val[1]=='vehicle'){
														if(array_key_exists($old,$vehicleArr)){ $old = $vehicleArr[$old]; }
														if(array_key_exists($new,$vehicleArr)){ $new = $vehicleArr[$new]; }
													} 
													if($val[1]=='truckingCompany'){
														if(array_key_exists($old,$truckComArr)){ $old = $truckComArr[$old]; }
														if(array_key_exists($new,$truckComArr)){ $new = $truckComArr[$new]; }
													}
													if($val[1]=='company'){
														if(array_key_exists($old,$companyArr)){ $old = $companyArr[$old]; }
														if(array_key_exists($new,$companyArr)){ $new = $companyArr[$new]; }
													}
													if($val[1]=='dlocation' || $val[1]=='plocation'){
														if(array_key_exists($old,$locationArr)){ $old = $locationArr[$old]; }
														if(array_key_exists($new,$locationArr)){ $new = $locationArr[$new]; }
													}
													if($val[1]=='dcity' || $val[1]=='pcity'){
														if(array_key_exists($old,$cityArr)){ $old = $cityArr[$old]; }
														if(array_key_exists($new,$cityArr)){ $new = $cityArr[$new]; }
													}
													
													echo 'Changed the '.$val[0].' value from <strong>"'.$old.'"</strong> to <strong>"'.$new.'"</strong><br>';
                    				            }
                    				        }
                    				        echo '</td></tr>';
                    				        $i++;
                    				    } ?>
                    				</tbody>
                    			</table>
                    			<?php if(count($dispatchLog) > 5){ ?>
                    			<a href="#" id="showMore" class="btn btn-sm">Show More</a>
                    			<?php } ?>
                    		</div>
                		</div>
			        </div>
			    <?php } ?>
			
			</div>
		</div>
	</div>	
</div>	


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
						<!-- <a href="#" class="btn btn-primary combibePdfBtn" data-toggle="modal" data-target="#combinePdfModal">Combine PDF / Email</a> -->
						<!-- <a href="#" class="btn btn-primary combibePdfBtn" target="_blank">Combine PDF</a> -->
						<!-- <a href="#" class="btn btn-success downloadPDF" data-type="outside" data-id="">Download All</a> -->
					</p>
				</form>
			</div> 
		</div>
		
	</div>
</div>

<div id="combinePdfModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Select Files to Combine/Email</h4>
      </div>
      <div class="modal-body">
        <form id="combinePdfForm" action="<?php echo base_url('Invoice/combineSelectedPDFs'); ?>">
		<input type="hidden" name="dispatch_id" id="dispatch_id">
		<input type="hidden" name="dispatch_type" id="dispatch_type">
          <div class="form-group file-list">
            <!-- Dynamically load checkboxes here -->
          </div>
          <button type="submit" class="btn btn-danger">Combine PDF</button>
		  <button type="submit" class="btn btn-primary" id="emailPdfBtn">Email</button>
		  <button type="submit" class="btn btn-success" id="previewPdfBtn">Preview Invoice</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="emailCarrierPaymenteModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Select Carrier Files to Email</h4>
      </div>
      <div class="modal-body">
        <form id="combinePdfForm" action="<?php echo base_url('Invoice/emailRemitanceProof'); ?>">
		<input type="hidden" name="dispatch_id" id="carrier_dispatch_id">
          <div class="form-group carrier-file-list">
            <!-- Dynamically load checkboxes here -->
          </div>
		  <button type="submit" class="btn btn-primary" id="emailPdfBtn">Send Email</button>
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

<!--script src="/assets/bootstrap-select.js"></script>
<link href="/assets/bootstrap-select.css" rel="stylesheet" /-->

<script>
	$(document).ready(function() {
      $('.select2').select2();
   	});
	$(document).ready(function() {
	    <?php if($showMsgDiv == 'true') { ?>
	    setTimeout(function(){
	        $('.msg-div').hide();
	    }, 5000);
	    $('html, body').animate({
                scrollTop: $("#submit").offset().top
            }, 'slow');
	    <?php } ?>
	    
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
    		fieldset.find('.shippingHoursI').val($(this).attr('data-time'));
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
			$(this).datepicker({dateFormat: 'yy-mm-dd',changeMonth: true, changeYear: true});
		});
		$(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
        
		$( ".sortable" ).sortable();
		
		<?php /*if(isset($_GET['invoice'])){ ?>
			$('.invoiceEdit input, .invoiceEdit select, .invoiceEdit textarea').attr('readonly','');
		<?php }*/ ?>
		<?php if($disp['lockDispatch']=='1') { ?>
		    $('.lockDispatchCls input, .lockDispatchCls select, .lockDispatchCls textarea').attr('readonly','');
		<?php } ?>
		
		/*var companies = [<?php //echo $js_companies; ?>];
		$( "#companies" ).autocomplete({ source: companies }); 
		
		var cities = [<?php //echo $js_cities; ?>];
		$( "#dcity" ).autocomplete({ source: cities });
		$( "#pcity" ).autocomplete({ source: cities });
		$('body').on('focus',".city1", function(){
			$(this).autocomplete({ source: cities });
		});
		
		var locations = [<?php //echo $js_location; ?>];
		$( "#dlocation" ).autocomplete({ source: locations });
		$( "#plocation" ).autocomplete({ source: locations });
		$('body').on('focus',".location1", function(){
			$(this).autocomplete({ source: locations });
		});*/
		
		$('#dataTable11 tbody tr').slice(5).hide();
        $('#showMore').click(function(e) {
            e.preventDefault();
            $('#dataTable11 tbody tr').show();
            $(this).hide(); // Hide the "Show More" button
        });
		
		<?php if($pdfArray){ ?>
		$('#download-pdfs').click(function() {
            var files = [
                <?php for($i=0;$i<count($pdfArray);$i++){
                    if($i > 0){ echo ','; }
                    echo '{ folder: "'.$pdfArray[$i][0].'", file: "'.$pdfArray[$i][1].'" }';
                } ?>
            ];

            files.forEach(function(file) {
                let href = "<?php echo base_url('admin/download_pdf/'); ?>" + file.folder + '/' + file.file;
				console.log(href);
				downloadpdf(href);
            });
        });
        
		$('.download-pdf').click(function(e){
			e.preventDefault();
			let cls = $(this).attr('data-cls');
			$(cls).each(function(index) {
				let href = $(this).attr('href');
				downloadpdf(href);
			});
		});
		
		function downloadpdf(href){
			var link = document.createElement('a');
			link.href = href
			link.target = '_blank';
			link.download = '';
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
        <?php } ?>
        if (!$('.d-bol').length) { $('a[data-cls=".d-bol"]').hide(); }
		if (!$('.d-outwrd').length) { $('a[data-cls=".d-outwrd"]').hide(); }
		if (!$('.d-gd').length) { $('a[data-cls=".d-gd"]').hide(); }
		if (!$('.carrier-d-gd').length) { $('a[data-cls=".carrier-d-gd"]').hide(); }
		if (!$('.d-carrierInvoice').length) { $('a[data-cls=".d-carrierInvoice"]').hide(); }
		if (!$('.d-paInvoice').length) { $('a[data-cls=".d-paInvoice"]').hide(); }
		
        
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
							//surcharge.val(result.toFixed(2));
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
		
		$('.rate-cls').keyup(function(){
			$('.rate-cls').each(function(index) {
				var rateInput = $('.rateInput').val();
				if(rateInput=='' || rateInput=='NaN'){ rateInput = 0; }

				var agentRateInput = $('#agentRate').val();
				if(agentRateInput=='' || agentRateInput=='NaN'){ agentRateInput = 0; }
				
				var parate = $('.parate').val();
				if(parate=='' || parate=='NaN'){ parate = 0; }
			

				let carrierPlusAgentRate = parseFloat(rateInput) + parseFloat(agentRateInput); 
				$('#carrierPlusAgentRate').val(carrierPlusAgentRate.toFixed(2));
        
			

				let pamargin = parseFloat(parate) - parseFloat(rateInput) - parseFloat(agentRateInput); 
				$('.pamargin').val(pamargin.toFixed(2));

				var agentPercentRate = $('#agentPercentRate').val();
				if(agentPercentRate === '' || agentPercentRate === 'NaN'){ agentPercentRate = 0; }
				var agentRateCalc = (parseFloat(pamargin) * parseFloat(agentPercentRate)) / 100;
				$('#agentRateDisplay').text(`(${agentRateCalc.toFixed(2)})`);

				var brookerPercent = (parseFloat(agentRateInput)/parseFloat(rateInput) ) *100;
				$('#brookerPercentDisplay').text(`(${brookerPercent.toFixed(2)}%)`);

				
			});
		});
		
		
		var dcid = 9999;
		
		$('.childInvoice-btn').click(function(){
			var expenseDiv = '<div class="col-sm-3 childInvoice-div-'+dcid+'">\
			<div class="form-group">\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".childInvoice-div-'+dcid+'" type="button" style="top:0px;">X</button>\
			<input name="childInvoice[]" required type="text" class="form-control" placeholder="Invoice" value="">\
			</div>\
			</div>';
			dcid++;
			$('.childInvoice-cls').append(expenseDiv);
		});
		
		$('.otherChildInvoice-btn').click(function(){
			var expenseDiv = '<div class="col-sm-3 pa-invoice childInvoice-div-'+dcid+'">\
			<div class="form-group">\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".childInvoice-div-'+dcid+'" type="button" style="top:0px;">X</button>\
			<input name="otherChildInvoice[]" required type="text" class="form-control" placeholder="PA Invoice" value="">\
			</div>\
			</div>';
			dcid++;
			$('.childInvoice-cls').append(expenseDiv);
		});
		
		$('.expense-btn').click(function(){
			var expenseDiv = '<div class="col-sm-3 expense-div-'+dcid+'">\
			<div class="form-group">\
			<div style="display: inline;color:#ff0047;" class="custom-checkbox my-1 mr-sm-2">\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".expense-div-'+dcid+'" type="button" style="top:0px;">X</button>\
			<select name="expenseName[]" class="expenseNameSelect expenseName-'+dcid+'" style="padding: 3px;margin: 3px;">\
			<?php foreach($expenses as $exp) { echo '<option value="'.$exp['title'].'">'.$exp['title'].'</option>'; } ?>\
			</select>\
			</div>\
			<input name="expensePrice[]" data-cls=".expenseName-'+dcid+'" required type="number" min="0" class="form-control expenseAmt" value="0" step="0.01">\
			</div>\
			</div>';
			dcid++;
			$('.expense-cls').append(expenseDiv);
		});
		
		
		$('.dispatchInfo-btn').click(function(){
			var dispatchInfoDiv = '<div class="col-sm-3 dispatchInfo-div-'+dcid+'">\
			<div class="form-group">\
			<div style="display: inline;color:#ff0047;" class="custom-checkbox my-1 mr-sm-2">\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".dispatchInfo-div-'+dcid+'" type="button" style="top:0px;">X</button>\
			<select name="dispatchInfoName[]" style="padding: 3px;margin: 3px;">\
			<?php foreach($dispatchInfo as $di) { echo '<option value="'.$di['title'].'">'.$di['title'].'</option>'; } ?>\
			</select>\
			</div>\
			<input name="dispatchInfoValue[]" required type="text" class="form-control" value="">\
			</div>\
			</div>';
			dcid++;
			$('.dispatchInfo-cls').append(dispatchInfoDiv);
		});
		
		$('.pcode-add').click(function(){
			var cls = $(this).attr('data-cls'); 
			var pickup = '<div class="col-sm-2 pcode-id-'+dcid+'"><div class="form-group"><label for="contain">Pick Up#</label><div class="input-group mb-2"><input name="pcode[]" type="text" required class="form-control" value=""><div class="input-group-append"><div class="input-group-text"><i data-cls=".pcode-id-'+dcid+'" class="fa fa-trash code-delete"></i></div></div></div></div></div>';
			$(cls).before(pickup);
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
				calculatePaRate();
			}
		});
		$('body').on('click','.pick-drop-both-remove-btn',function(){
			var cls = $(this).attr('data-removeCls');
			var rowid = $(this).attr('data-id');
			var result = window.confirm('Are you sure it will remove both pickup and dropoff?');
			if (result == true) {
				$.ajax({
					url: "<?php echo base_url('admin/outside-dispatch-extra/delete/');?>"+rowid,
					type: "post",
					data: "rowid="+rowid,
					success: function(d) {
						//alert(d);
					}
				});
				
				$(cls).html('').remove();
			}
		});
		
		var pid = <?php echo $pextraCount;?>;
		$('.pickup-btn-add').click(function(){
			var pfieldset = '<fieldset class="ui-state-default pickup'+pid+'">\
			<legend><input type="text" class="form-control" name="pickup1[]" value="Pick Up # '+pid+'"></legend>\
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
					<select class="form-control appointmentType" name="appointmentTypeP1[] required">\
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
							<input name="ptime1[]" type="text" class="timeInput form-control" value="" readonly>\
							<div class="tDropdown"></div>';
							pfieldset += '\
							<div class="input-group-append">\
								<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white; appointmentType"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>\
						</div>\
					</div>\
    			</div>\
			</div>\
			<div class="col-sm-4">\
			<div class="form-group">\
			<label for="contain">Pick Up Company (Location)</label>\
			<div class="getAddressParent"><input type="text" id="plocation1" class="form-control location1 getAddress companyI" required data-type="company" name="plocation1[]" value=""></div> \
			</div>\
			</div>\
			<div class="col-sm-4">\
				<div class="form-group">\
					<label for="contain">Pick Up Address</label>\
					<div class="getAddressParent"><input type="hidden" name="paddressid1[]" class="addressidI" value=""><input type="text" id="paddress1" class="form-control paddress1 getAddress addressI" data-type="address" name="paddress1[]" value=""></div> \
				</div>\
			</div>\
			<div class="col-sm-3">\
				<div class="form-group">\
					<label for="contain">Pick Up City</label>\
					<div class="getAddressParent"><input type="text" id="pcity1" class="form-control city1 getAddress cityI" required data-type="city" name="pcity1[]" value=""></div>  \
				</div>\
			</div>\
			<div class="col-sm-5"><div class="form-group">\
				<label for="contain">Description</label>\
				<textarea class="form-control" name="metaDescriptionP1[]"></textarea>\
				</div>\
			</div>\
			<div class="col-sm-2"><div class="form-group"><label for="contain">Quantity</label><input type="text" class="form-control" name="quantityP1[]" value=""></div></div>\
			<div class="col-sm-2"><div class="form-group"><label for="contain">Weight</label><input required type="text" class="form-control weight" name="weightP1[]" value=""></div></div>\
			<div class="col-sm-3">\
				<div class="form-group">\
					<label for="contain">Commodity</label>\
					<input required type="text" class="form-control" name="commodityP1[]" value="">\
				</div>\
			</div>\
			<div class="col-sm-2 pcode1-id-'+pid+'">\
			<div class="form-group">\
			<label for="contain">Pick Up#</label>\
			<div class="input-group mb-2">\
			<input name="pcodename[]" type="hidden" value="pc'+pid+'">\
			<input name="extrdispatchid[]" type="hidden" value="0">\
			<input name="pcode1[pc'+pid+'][]" type="text" class="form-control" value="">\
			<div class="input-group-append">\
			<div class="input-group-text pcode1-add" data-name="pc'+pid+'" data-cls=".pnotes1-'+pid+'"><strong>+</strong></div>\
			</div>\
			</div>  \
			</div>\
			</div>\
			<input name="pd_type1[]" type="hidden" value="pickup">\
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
			$('#invoiceTrucking').trigger('change');
		});
		
		var did = <?php echo $extraCount;?>;
		$('.drop-btn-add').click(function() {
			var dfieldset = '<fieldset class="ui-state-default dropoff1'+did+'">\
			<legend><input type="text" class="form-control" name="dropoff1[]" value="Drop Off #'+did+'"></legend>\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removeCls=".dropoff1'+did+'" type="button">Remove</button>\
			<div class="row dropoff1'+did+'-pcode-parent">\
			<div class="col-sm-3">\
			<div class="form-group">\
			<label for="contain">Drop Off Date</label>\
			<input name="dodate1[]" type="text" class="form-control datepicker" required value="">\
			</div>\
			</div>\
			<div class="col-sm-3">\
				<div class="form-group">\
					<label for="contain">Appointment Type</label>\
					<select class="form-control appointmentType" name="appointmentTypeD1[]" required>\
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
							dfieldset += '\
							<div class="input-group-append">\
								<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white; appointmentType"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>\
						</div>\
					</div>\
    			</div>\
			</div>\
			<div class="col-sm-2"><div class="form-group"><label for="contain">Quantity</label><input type="text" class="form-control" name="quantityD1[]" value=""></div></div>\
			<div class="col-sm-3">\
			<div class="form-group">\
			<label for="contain">Drop Off Company (Location) &nbsp;</label>\
			<div class="getAddressParent"><input type="text" class="form-control location1 getAddress companyI" data-type="company" name="dlocation1[]" value=""></div> \
			</div>\
			</div>\
			<div class="col-sm-4">\
			<div class="form-group">\
			<label for="contain">Drop Off Address</label>\
			<div class="getAddressParent"><input type="hidden" name="daddressid1[]" class="addressidI" value=""><input type="text" id="daddress1" class="form-control getAddress addressI" data-type="address" name="daddress1[]" value=""></div> \
			</div>\
			</div>\
			<div class="col-sm-3">\
			<div class="form-group">\
			<label for="contain">Drop Off City</label>\
			<div class="getAddressParent"><input type="text" class="form-control city1 getAddress cityI" data-type="city" name="dcity1[]" value=""></div>\
			</div>\
			</div>\
			<div class="col-sm-4 hide d-none">\
				<div class="form-group">\
					<label for="contain">Port of Discharge / Import</label>\
					<input type="text" class="form-control" name="dPort1[]" value="">\
				</div>\
			</div>\
			<div class="col-sm-4 hide d-none">\
				<div class="form-group">\
					<label for="contain">Port Address</label>\
					<input type="text" class="form-control" name="dPortAddress1[]" value="">\
				</div>\
			</div>\
			<div class="col-sm-2"><div class="form-group"><label for="contain">Weight</label><input required type="text" class="form-control weight" name="weightD1[]" value=""></div></div>\
			<div class="col-sm-6"><div class="form-group"><label for="contain">Description</label><textarea class="form-control" name="metaDescriptionD1[]"></textarea></div></div>\
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
			<input name="pd_type2[]" type="hidden" value="dropoff">\
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
			$('#invoiceTrucking').trigger('change');
		});
		
		$(document).on('click', '.pick-drop-remove-btn', function() {
			var removeCls = $(this).data('removecls');
			$(removeCls).remove(); 
			if (removeCls.startsWith('.pickup')) {
				if (pid > 1) {
					pid--;
				}
			} else if (removeCls.startsWith('.dropoff')) {
				if (did > 1) {
					did--;
				}
			}
			$('#invoiceTrucking').trigger('change');
		});

		$('.parate').attr('data-price','<?=$paRate?>');
		$('.parate').keyup(function(){
			let valu = $(this).val();
			$(this).attr('data-price',valu);
			setTimeout(function(){
				calculatePaRate();
			}, 3000);
		});
		
		$('#customControlInlinegd, #customControlInlinerc, #customControlInline').click(function(){
			if($('#customControlInlinegd').prop('checked') && $('#customControlInlinerc').prop('checked') && $('#customControlInline').prop('checked')) { 
				$('.invoiceTypeCls').attr('required','');
			}
			else { $('.invoiceTypeCls').removeAttr('required'); }
		});
		
		$('select.invoicePDF').each(function () {
			$(this).trigger('change');
		});

		$('select.invoicePDF').change(function(){
			let valu = $(this).val();
			console.log(valu);
			if(valu == 'Drayage'){
				$('.Trucking').hide();
				$('.Drayage').show();
				$('.erInformation').show();

				$('#drayageType').prop('required', true);
				$('#invoiceDrayage').prop('required', true);
				$('#erInformation').prop('required', true);
				$('#invoiceTrucking').prop('required', false);
				
				$('#carrierInvoiceRefNoDiv').show();
				$('#carrierInvoiceDev').addClass('d-flex');
			}
			else if(valu == 'Trucking'){
				$('.Trucking').show();
				$('.Drayage').hide();
				$('.erInformation').hide();
				$('#invoiceTrucking').prop('required', true);

				$('#drayageType').prop('required', false);
				$('#invoiceDrayage').prop('required', false);
				$('#erInformation').prop('required', false);
				$('#carrierInvoiceRefNoDiv').show();
				$('#carrierInvoiceDev').addClass('d-flex');
			}
			else {
				$('.Trucking').hide();
				$('.Drayage').hide();
				$('.erInformation').hide();
				$('#drayageType').prop('required', false);
				$('#invoiceDrayage').prop('required', false);
				$('#erInformation').prop('required', false);
				$('#invoiceTrucking').prop('required', false);
				$('#carrierInvoiceRefNoDiv').hide();
				$('#carrierInvoiceDev').removeClass('d-flex');
			}
		});
		$('#shipmentType').trigger('change');
		var invoiceTypeTxt = '<?=$invoiceType?>';
		$('.invoiceTypeCls').change(function(){
		    $('.invoiceNotes').removeAttr('required','');
			let valu = $(this).val();
			if(valu == 'RTS'){
				$('.invoiceTitle').html('RTS'); invoiceTypeTxt = 'RTS';
				$('.invoiceCheckboxCls > div').show();
			} else if(valu == 'Direct Bill'){
				$('.invoiceTitle').html('DB'); invoiceTypeTxt = 'DB';
				<?php /*if(!isset($_GET['invoice'])){ ?>
				    $('.invoiceCheckboxCls > div').hide();
				    $('.invoiceCheckboxCls .invoiceqp').show();
				<?php } else { ?>
				    $('.invoiceCheckboxCls > div').show();
				<?php }*/ ?>
				$('.invoiceCheckboxCls > div').show();
				$('.invoiceNotes').attr('required','');
			} else if(valu == 'Quick Pay'){
				$('.invoiceTitle').html('QP'); invoiceTypeTxt = 'QP';
				<?php /*if(!isset($_GET['invoice'])){ ?>
				    $('.invoiceCheckboxCls > div').hide();
				    $('.invoiceCheckboxCls .invoiceqp').show();
				<?php } else { ?>
				    $('.invoiceCheckboxCls > div').show();
				<?php }*/ ?>
				$('.invoiceCheckboxCls > div').show();
				$('.invoiceNotes').attr('required','');
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
		
		$('.remove-file').click(function(e){
		    e.preventDefault();
		    var href = $(this).attr('href');
		    // return confirm(\'Are you sure delete this file ?\')
		    if(confirm('Are you sure delete this file ?')) {
    		    $(this).parent('span').hide();
    		    $.ajax({
    				type: "GET",
    				url: href,
    				data: "",
    				success: function(response) { 
    				    $('.flashMsgCls').show();
    				    setTimeout(function(){
                	        $('.flashMsgCls').hide();
                	    }, 5000);
    				}
    			});
		    }
		});
		$('.downloadPDF').click(function(e){
			e.preventDefault();
			var disid = $(this).attr('data-id');
			var type = $(this).attr('data-type');
			var href = $(this).attr('href');
			$.ajax({
				type: "GET",
				url: "<?php echo base_url('admin/invoice?doc=');?>"+disid+"&type="+type,
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
			var disid = $(this).attr('data-id');
			var href = $(this).attr('href');
			$('.generatePdfBtn').attr('href',href);
			$('#editinvoiceform').attr('action',href);
			let cBtn = '<?php echo base_url('Invoice/downloadInvoicePDF/');?>'+disid+'?invoiceWithPdf=bol-rc&type=warehouse&dTable=warehouse_dispatch';
			$('.combibePdfBtn').attr('href',cBtn);
			
			let dBtn = '<?php echo base_url('Invoice/downloadInvoicePDF/');?>'+disid+'?dTable=warehouse_dispatch';
			$('.downloadPDF').attr('data-id',disid);
			$('.downloadPDF').attr('href',dBtn);

			$('#combinePdfForm').data('id', disid);
			$('#combinePdfForm').data('type', dTable);
			$.ajax({
				type: "post",
				url: "<?php echo base_url('Invoice/editInvoiceForm?editInvoiceID=');?>"+disid+"&dTable="+dTable,
				data: "editInvoiceID="+disid,
				success: function(responseData) { 
					$('.invoiceAjaxForm').html(responseData);
				}
			});
		});	
		
		$('#combinePdfModal').on('show.bs.modal', function () {
			const dispatchId = $('#combinePdfForm').data('id');
			const type = $('#combinePdfForm').data('type');

			$.ajax({
				url: `<?php echo base_url('Invoice/getAvailableFiles/'); ?>${dispatchId}?type=${type}`,
				method: 'GET',
				success: function(response) {
					const data = typeof response === "string" ? JSON.parse(response) : response;
					$('#dispatch_id').val(dispatchId);
					$('#dispatch_type').val(type);
					let fileList = '';
					const baseUrl = '<?php echo base_url(); ?>';

					function constructUrl(fileName, fileType) {
						if (type === 'dispatch') {
							return `${baseUrl}assets/upload/${fileName}`;
						} else {
							return `${baseUrl}assets/outside-dispatch/${fileType}/${fileName}`;
						}
					}
					
					function constructOtherParentUrl(fileName, fileType) {
						return `${baseUrl}assets/upload/${fileName}`;
					}

					if ((data.parent_bol_files && data.parent_bol_files.length > 0) || (data.parent_bol_images && data.parent_bol_images.length > 0)) {
						fileList += `<h4 class="section-heading">Parent BOL Files</h4><hr>`;
					}
					if (data.parent_bol_files && data.parent_bol_files.length > 0) {
						data.parent_bol_files.forEach(function(file) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="parent_file_ids[]" value="${file.id}" checked> 
								<a href="${constructUrl(file.name, 'bol')}" target="_blank">${file.name}</a>
								</label>
							</div>
							`;
						});
					}
					if (data.parent_bol_images && data.parent_bol_images.length > 0) {
						data.parent_bol_images.forEach(function(image) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="parent_file_ids[]" value="${image.id}" checked> 
								<a href="${constructUrl(image.name, 'bol')}" target="_blank">${image.name}</a>
								</label>
							</div>
							`;
						});
					}


					if ((data.bol_files && data.bol_files.length > 0) || (data.bol_images && data.bol_images.length > 0)) {
						fileList += `<h4 class="section-heading">BOL Files</h4><hr>`;
					}
					if (data.bol_files && data.bol_files.length > 0) {
						data.bol_files.forEach(function(file) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="file_ids[]" value="${file.id}" checked> 
								<a href="${constructUrl(file.name, 'bol')}" target="_blank">${file.name}</a>
								</label>
							</div>
							`;
						});
					}
					if (data.bol_images && data.bol_images.length > 0) {
						data.bol_images.forEach(function(image) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="file_ids[]" value="${image.id}" checked> 
								<a href="${constructUrl(image.name, 'bol')}" target="_blank">${image.name}</a>
								</label>
							</div>
							`;
						});
					}
					
					if ((data.other_parent_bol_files && data.other_parent_bol_files.length > 0) || (data.other_parent_bol_images && data.other_parent_bol_images.length > 0)) {
						fileList += `<h4 class="section-heading">Fleet BOL Files</h4><hr>`;
					}
					if (data.other_parent_bol_files && data.other_parent_bol_files.length > 0) {
						data.other_parent_bol_files.forEach(function(file) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="other_parent_file_ids[]" value="${file.id}" checked> 
								<a href="${constructOtherParentUrl(file.name, 'bol')}" target="_blank">${file.name}</a>
								</label>
							</div>
							`;
						});
					}
					if (data.other_parent_bol_images && data.other_parent_bol_images.length > 0) {
						data.other_parent_bol_images.forEach(function(image) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="other_parent_file_ids[]" value="${image.id}" checked> 
								<a href="${constructOtherParentUrl(image.name, 'bol')}" target="_blank">${image.name}</a>
								</label>
							</div>
							`;
						});
					}

					if ((data.parent_rc_files && data.parent_rc_files.length > 0) || (data.parent_rc_images && data.parent_rc_images.length > 0)) {
						fileList += `<h4 class="section-heading">Parent RC Files</h4><hr>`;
					}					
					if (data.parent_rc_files && data.parent_rc_files.length > 0) {
						data.parent_rc_files.forEach(function(file) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="parent_file_ids[]" value="${file.id}" checked> 
								<a href="${constructUrl(file.name, 'rc')}" target="_blank">${file.name}</a>
								</label>
							</div>
							`;
						});
					}
					if (data.parent_rc_images && data.parent_rc_images.length > 0) {
						data.parent_rc_images.forEach(function(image) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="parent_file_ids[]" value="${image.id}" checked> 
								<a href="${constructUrl(image.name, 'rc')}" target="_blank">${image.name}</a>
								</label>
							</div>
							`;
						});
					}

					if ((data.rc_files && data.rc_files.length > 0) || (data.rc_images && data.rc_images.length > 0)) {
						fileList += `<h4 class="section-heading">RC Files</h4><hr>`;
					}
					if (data.rc_files && data.rc_files.length > 0) {
						data.rc_files.forEach(function(file) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="file_ids[]" value="${file.id}" checked> 
								<a href="${constructUrl(file.name, 'rc')}" target="_blank">${file.name}</a>
								</label>
							</div>
							`;
						});
					}
					if (data.rc_images && data.rc_images.length > 0) {
						data.rc_images.forEach(function(image) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="file_ids[]" value="${image.id}" checked> 
								<a href="${constructUrl(image.name, 'rc')}" target="_blank">${image.name}</a>
								</label>
							</div>
							`;
						});
					}

					if ((data.other_parent_rc_files && data.other_parent_rc_files.length > 0) || (data.other_parent_rc_images && data.other_parent_rc_images.length > 0)) {
						fileList += `<h4 class="section-heading">Fleet RC Files</h4><hr>`;
					}					
					if (data.other_parent_rc_files && data.other_parent_rc_files.length > 0) {
						data.other_parent_rc_files.forEach(function(file) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="other_parent_file_ids[]" value="${file.id}" checked> 
								<a href="${constructOtherParentUrl(file.name, 'rc')}" target="_blank">${file.name}</a>
								</label>
							</div>
							`;
						});
					}
					if (data.other_parent_rc_images && data.other_parent_rc_images.length > 0) {
						data.other_parent_rc_images.forEach(function(image) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="other_parent_file_ids[]" value="${image.id}" checked> 
								<a href="${constructOtherParentUrl(image.name, 'rc')}" target="_blank">${image.name}</a>
								</label>
							</div>
							`;
						});
					}

					if (data.cEmails && data.cEmails.length > 0) {
						fileList += `<h4 class="section-heading">Add CC Email</h4><hr>`;
						const emailArray = data.cEmails.split(',');
						emailArray.forEach(function(email) {
							const trimmedEmail = email.trim();
							if (trimmedEmail !== '') {
								fileList += `
									<div class="file-entry d-flex align-items-center mb-2">
										<input type="checkbox" class="other_cEmails_checkbox me-2" checked> 
										<input type="hidden" name="other_cEmails_checkbox[]" value="1">
										<input type="text" class="form-control form-control-sm ml-1" name="other_cEmails[]" value="${trimmedEmail}" readonly style="max-width: 300px;">
									</div>
								`;
							}
						});
					}

					if (fileList === '') {
						fileList = '<p>No files available to combine.</p>';
					}

					$('.file-list').html(fileList);
				},
				error: function() {
					$('.file-list').html('<p>Error loading files. Please try again.</p>');
				}
			});
		});
		$(document).on('change', '.other_cEmails_checkbox', function() {
			var isChecked = $(this).is(':checked');
			$(this).siblings('input[type="hidden"]').val(isChecked ? '1' : '0');
			
		});

		document.getElementById('emailPdfBtn').addEventListener('click', function (e) {
			const form = document.getElementById('combinePdfForm');
			form.action = "<?php echo base_url('Invoice/emailInvoice'); ?>";
		});
		document.getElementById('previewPdfBtn').addEventListener('click', function (e) {
			e.preventDefault(); 
			const dispatchId = document.getElementById('dispatch_id').value;
			const dispatchType = document.getElementById('dispatch_type').value;
			let url = "<?php echo base_url('Invoice/PreviewInvoicePDF'); ?>";
			url += `?dispatch_id=${dispatchId}&dispatch_type=${dispatchType}`;
			window.open(url, '_blank');
		});

		document.querySelector('form#combinePdfForm .btn-success').addEventListener('click', function () {
			document.getElementById('combinePdfForm').action = "<?php echo base_url('Invoice/combineSelectedPDFs'); ?>";
		});


		$('.openCarrierEmailModal').on('click', function (e) {
			e.preventDefault();			
			const dispatchId = $(this).data('id');
			$('#emailCarrierPaymenteModal').data('dispatch-id', dispatchId);
		});


		$('#emailCarrierPaymenteModal').on('show.bs.modal', function () {
		    const dispatchId = $(this).data('dispatch-id');
    		$('#carrier_dispatch_id').val(dispatchId);
			$.ajax({
				url: `<?php echo base_url('Invoice/getAvailableCarrierFiles/'); ?>${dispatchId}`,
				method: 'GET',
				success: function(response) {
					const data = typeof response === "string" ? JSON.parse(response) : response;
					if (data.redirect_url) {
						$('#emailCarrierPaymenteModal').modal('hide');
						window.location.href = data.redirect_url;
						return;
					}
					let fileList = '';
					const baseUrl = '<?php echo base_url(); ?>';
					if (data.carrier_files && data.carrier_files.length > 0) {
						data.carrier_files.forEach(function(file) {
							const fileName = file.fileurl.split('/').pop(); // Just in case it's a full path
							const fileLink = '<?php echo base_url('assets/outside-dispatch/gd/'); ?>' + file.fileurl;

							fileList += `
								<div class="file-entry">
									<label>
										<input type="checkbox" name="carrier_files[]" value="${file.id}" checked> 
										<a href="${fileLink}" target="_blank">${fileName}</a>
									</label>
								</div>
							`;
						});
					}

					if (data.email && data.email.length > 0) {
						fileList += `<input type="hidden" class="form-control form-control-sm ml-1" name="cEmail" value="${data.email}">`
					}
					fileList += `<input type="hidden" class="form-control form-control-sm ml-1" name="emailSentTo" value="${data.email_sent_to}">`
					fileList += `<input type="hidden" class="form-control form-control-sm ml-1" name="emailRecieverId" value="${data.email_reciever_id}">`

					if (data.other_emails && data.other_emails.length > 0) {
						fileList += `<h4 class="section-heading">Add CC Email</h4><hr>`;
						const emailArray = data.other_emails.split(',');
						emailArray.forEach(function(email) {
							const trimmedEmail = email.trim();
							if (trimmedEmail !== '') {
								fileList += `
									<div class="file-entry d-flex align-items-center mb-2">
										<input type="checkbox" class="other_cEmails_checkbox me-2" checked> 
										<input type="hidden" name="other_cEmails_checkbox[]" value="1">
										<input type="text" class="form-control form-control-sm ml-1" name="other_cEmails[]" value="${trimmedEmail}" readonly style="max-width: 300px;">
									</div>
								`;
							}
						});
					}
					if(fileList === '') {
						fileList = '<p>No files available to combine.</p>';
					}
					$('.carrier-file-list').html(fileList);
				},
				error: function() {
					$('.carrier-file-list').html('<p>Error loading files. Please try again.</p>');
				}
			});
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

		// 	dropdown.append('<input type="text" class="search-time" placeholder="Search time..." style="width:100%; padding:5px; margin-bottom:5px;">');

		// 	timeOptions.forEach(time => {
		// 	dropdown.append('<div class="time-option">'+time+'</div>');
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

		//Select time when clicking an option (Replaces only the second time)
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
		
		// Select time when clicking an option
		// $(document).on("click", ".tDropdown div", function () {
		// 	let dropdown = $(this).parent("div");
		// 	let selectedTime = $(this).text();
		// 	let parentGroup = $(this).closest(".input-group");
		// 	let appointmentType = parentGroup.closest(".row").find("select").val();

		// 	if (activeInput) {
		// 		let currentValue = activeInput.val().trim();
		// 		let times = currentValue.split(" - ");

		// 		if (appointmentType === "Appointment") {
		// 			// Single time input for "Appointment"
		// 			activeInput.val(selectedTime);
		// 		} else if (appointmentType === "FCFS") {
		// 			// Time range input for "FCFS"
		// 			if (times.length === 1 && times[0] !== "") {
		// 				activeInput.val(times[0] + " - " + selectedTime); // Add second time
		// 			} else if (times.length === 2) {
		// 				activeInput.val(times[0] + " - " + selectedTime); // Replace second time
		// 			} else {
		// 				activeInput.val(selectedTime); // Insert first time
		// 			}
		// 		}
		// 	}

		// 	dropdown.hide();
		// });

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
	
		//adding lbs to weight entery
		$(document).on('blur', '.weight', function() {
			let val = $(this).val().trim();
			if (val !== '') {
				val = val.replace(/lbs$/i, '').replace(/,/g, '').trim();
				if (!isNaN(val)) {
					val = Number(val).toLocaleString('en-US');
				}
				$(this).val(val + ' lbs');
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
		if (event.target.matches('[name="carrier_gd_d"]')) {
			const checkbox = document.getElementById('carrierControlInlinegd');
			if (event.target.value) {
				checkbox.setAttribute('required', 'required');
			} else {
				checkbox.removeAttribute('required');
			}
		}
		
		if (event.target.matches('[name="carrierInvoice[]"]')) {
			const checkbox = document.getElementById('carrierInvoiceCheck');
			if (event.target.value) {
				checkbox.setAttribute('required', 'required');
			} else {
				checkbox.removeAttribute('required');
			}
		}
	});
	
	//adding required attribute to pick/drop time inputs
	document.querySelector('#updatePaLogisticform').addEventListener('submit', function (e) {
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
		function changeStatus(invoiceTypeTxt) { 
			var statusText = ''; 
			var currentDate = '<?=date('m/d/Y')?>';
			let invDate = ''; 
			let invoiceDate = '';

			let currentNotes = $('.statusCls').val();
			let parts = currentNotes.split('-');
			let existingFirstPart = parts[0].trim();
			let remainingPart = parts.slice(1).join('-').trim();

			let allInvoices = [];
			$('input[name="childInvoice[]"], input[name="otherChildInvoice[]"]').each(function () {
				if ($(this).val().trim() !== '') {
					allInvoices.push($(this).val().trim());
				}
			});
			let linkedInvoicesText = allInvoices.length > 0 ? allInvoices.map(invoice => `Linked to ${invoice}`).join(' - ') : '';

			if ($('.invoiceCheckCls[name="invoiceClose"]').is(':checked')) {
				statusText = invoiceTypeTxt+' Closed ' + currentDate;
				$('.invoiceCheckCls[name="invoicePaid"], .invoiceCheckCls[name="invoiced"], .invoiceCheckCls[name="invoiceReady"]').prop('checked', true);
				invoiceDate = 'yes';
				$('input.invoiceCloseDate, input.invoicePaidDate, input.invoiceReadyDate').attr('required', '');
				invDate = $('input.invoiceCloseDate').val();
				remainingPart = '';
			} else if ($('.invoiceCheckCls[name="invoicePaid"]').is(':checked')) {
				statusText = invoiceTypeTxt+' Paid ' + currentDate;
				$('.invoiceCheckCls[name="invoiced"], .invoiceCheckCls[name="invoiceReady"]').prop('checked', true);
				invoiceDate = 'yes';
				$('input.invoicePaidDate, input.invoiceReadyDate').attr('required', '');
				$('input.invoiceCloseDate').removeAttr('required');
				invDate = $('input.invoicePaidDate').val();
			} else if ($('.invoiceCheckCls[name="invoiced"]').is(':checked')) {
				statusText = invoiceTypeTxt+' Invoiced ' + currentDate;
				$('.invoiceCheckCls[name="invoiceReady"]').prop('checked', true);
				invoiceDate = 'yes';
				$('input.invoiceCloseDate, input.invoicePaidDate').removeAttr('required');
				$('input.invoiceReadyDate').attr('required', '');
				invDate = $('input.invoiceDate').val();
			} else if ($('.invoiceCheckCls[name="invoiceReady"]').is(':checked')) {
				statusText = invoiceTypeTxt+' Ready to submit '+currentDate;
				invoiceDate = 'no';
				$('input.invoiceReadyDate').attr('required', '');
				invDate = $('input.invoiceReadyDate').val();
			} else {
				$('input.invoiceReadyDate').removeAttr('required');
			}

			if (invoiceTypeTxt == 'RTS') { 
				$('.paid-close-date').show(); 
			}

			if (invoiceDate == 'yes') {
				$('input.invoiceDate').attr('required', ''); 
				if ($('input.invoiceDate').val() == 'TBD') { 
					$('input.invoiceDate').val(''); 
				}
			} else {
				if ($('#customControlInlinegd').prop('checked') && $('#customControlInlinerc').prop('checked') && $('#customControlInline').prop('checked')) { 
					// Do nothing
				} else { 
					$('.invoiceTypeCls').removeAttr('required'); 
				}
			}

			if (statusText != '' && invoiceTypeTxt != '') {
				if (invDate != '') {
					let [year, month, day] = invDate.split('-');
					month = month.padStart(2, '0');
					day = day.padStart(2, '0');
					let cDate = `${month}/${day}/${year}`;
					statusText = statusText.replace(currentDate, cDate);
				}

				let finalStatus = statusText;

				// Append linked invoices only if it’s not already present
				// if ((statusText.includes('Paid') || statusText.includes('Invoiced') || statusText.includes('Ready to submit')) && !remainingPart.includes('Linked to') && linkedInvoicesText) {
				// 	finalStatus += ` - ${linkedInvoicesText}`;
				// } else if (remainingPart !== '') {
				// 	finalStatus += ` - ${remainingPart}`;
				// }

				$('.statusCls').val(finalStatus.trim());
			} else {
				let statusTextOld = $('.statusCls').attr('data');
				$('.statusCls').val(statusTextOld);
			}
		}

	// $(document).on('input', 'input[name="childInvoice[]"], input[name="otherChildInvoice[]"]', function () {
	// 		let currentNotes = $('.statusCls').val();
	// 		let allInvoices = [];
	// 		$('input[name="childInvoice[]"], input[name="otherChildInvoice[]"]').each(function () {
	// 			if ($(this).val().trim() !== '') {
	// 				allInvoices.push($(this).val().trim());
	// 			}
	// 		});
	// 		let linkedInvoicesText = allInvoices.length > 0 ? allInvoices.map(invoice => `Linked to ${invoice}`).join(' - ') : '';
	// 		if (currentNotes.includes('Linked to ')) {
	// 			if (linkedInvoicesText) {
	// 				currentNotes = currentNotes.replace(/- Linked to .+/, '- ' + linkedInvoicesText);
	// 			} else {
	// 				currentNotes = currentNotes.replace(/- Linked to .+/, '').trim();
	// 			}
	// 		} else if (linkedInvoicesText !== '') {
	// 			currentNotes = currentNotes.includes('-')
	// 				? `${currentNotes} - ${linkedInvoicesText}`
	// 				: `${currentNotes} - ${linkedInvoicesText}`;
	// 		}

	// 		$('.statusCls').val(currentNotes.trim());
	// });

	$(document).on('click', '.pick-drop-remove-btn', function () {
		let removeClass = $(this).data('removecls');
		$(removeClass).remove();
		// let currentNotes = $('.statusCls').val();
		// let allInvoices = [];
		// $('input[name="childInvoice[]"], input[name="otherChildInvoice[]"]').each(function () {
		// 	if ($(this).val().trim() !== '') {
		// 		allInvoices.push($(this).val().trim());
		// 	}
		// });
		// let linkedInvoicesText = allInvoices.length > 0 ? allInvoices.map(invoice => `Linked to ${invoice}`).join(' - ') : '';
		// if (currentNotes.includes('Linked to ')) {
		// 	if (linkedInvoicesText) {
		// 		currentNotes = currentNotes.replace(/- Linked to .+/, '- ' + linkedInvoicesText);
		// 	} else {
		// 		currentNotes = currentNotes.replace(/- Linked to .+/, '').trim();
		// 	}
		// } else if (linkedInvoicesText !== '') {
		// 	currentNotes = currentNotes.includes('-')
		// 		? `${currentNotes} - ${linkedInvoicesText}`
		// 		: `${currentNotes} - ${linkedInvoicesText}`;
		// }
		// $('.statusCls').val(currentNotes.trim());
	});
		
	function checkCheckboxes() {
			if ($('.bolrc:checked').length === $('.bolrc').length) {
				$('.invoiceTypeCls').attr('required', 'required');
			} else {
				$('.invoiceTypeCls').removeAttr('required');
			}
		}
		checkCheckboxes();
		
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
			
			var rateInput = $('.rateInput').val();

			var agentRateInput = $('#agentRate').val();
			if(agentRateInput=='' || agentRateInput=='NaN'){ agentRateInput = 0; }

			let pamargin = parseFloat(finalAmt) - parseFloat(rateInput)-parseFloat(agentRateInput); 
			$('.pamargin').val(parseFloat(pamargin).toFixed(2));
			//$('.pamargin').val(Math.round(pamargin));


			var agentPercentRate = $('#agentPercentRate').val();
				if(agentPercentRate === '' || agentPercentRate === 'NaN'){ agentPercentRate = 0; }
				var agentRateCalc = (parseFloat(pamargin) * parseFloat(agentPercentRate)) / 100;
				$('#agentRateDisplay').text(`(${agentRateCalc.toFixed(2)})`);

				var brookerPercent = (parseFloat(agentRateInput)/parseFloat(rateInput) ) *100;
				$('#brookerPercentDisplay').text(`(${brookerPercent.toFixed(2)}%)`);
		}
	
		function updateInvoiceNotes() {
			var selectedOption = $('#invoiceTrucking').find('option:selected').text();
			if (selectedOption === 'Dry Van' || selectedOption === 'Power Only' || selectedOption === 'Flatbed' || selectedOption === 'Box Truck') {
				var cityText = '';
				var mainCity = $('input[name="dcity"]').val();
				if (mainCity) {
					cityText += '[' + mainCity + ']';
				}
				$('input[name="dcity1[]"]').each(function() {
					var val = $(this).val();
					if (val) {
						cityText += '[' + val + ']';
					}
				});

				if (selectedOption === 'Dry Van') {
					if ($('input[name="dcity1[]"]').filter(function() { return $(this).val(); }).length > 0) {
						var concatenatedValue = 'FTL Shipment ' + (pid - 1) + ' Pick ' + (did - 1) + ' Drop ';
						$('#invoiceNotes').val(concatenatedValue);
					} else {
						var concatenatedValue = 'FTL Shipment ' + cityText;
						$('#invoiceNotes').val(concatenatedValue);
					}
				} else if (selectedOption === 'Power Only') {
					if ($('input[name="dcity1[]"]').filter(function() { return $(this).val(); }).length > 0) {
						var concatenatedValue = 'Power Only Shipment ' + (pid - 1) + ' Pick ' + (did - 1) + ' Drop ';
						$('#invoiceNotes').val(concatenatedValue);
					} else {
						var concatenatedValue = 'Power Only Shipment ' + cityText;
						$('#invoiceNotes').val(concatenatedValue);
					}
				} else {
					var concatenatedValue = selectedOption + ' ' + (pid - 1) + ' Pick ' + (did - 1) + ' Drop';
					$('#invoiceNotes').val(concatenatedValue);
				}	
			}
		
		}

		$('#invoiceTrucking').change(function() {
			updateInvoiceNotes();
		});
	});

  function updateValueOnRuntime(myCheckbox,myHiddencheckbox) {
	//alert(myCheckbox);
    let checkbox = document.getElementById(myCheckbox);

    if (checkbox.checked) {
      checkbox.value = "1";
    } else {
      checkbox.value = "0";
    }
	let hiddenInput = document.getElementById(myHiddencheckbox);

    hiddenInput.value = checkbox.checked ? "1" : "0";
    
  }


  	const query = window.location.search; 
	const fragment = window.location.hash; 
	const generateInvButton = document.getElementById("generateInvButton");
	const rateButton = document.getElementById("rateButton");

	if (query === "?invoice") {
		generateInvButton.style.display = "block";
		rateButton.style.display = "none";
	}
	else if (fragment === "#submit") {
		generateInvButton.style.display = "block";
		rateButton.style.display = "block";
	}
	else {
		generateInvButton.style.display = "none";
		rateButton.style.display = "block";
	}

	document.addEventListener('DOMContentLoaded', function() {

		const bookedUnderDropdown = document.getElementById('bookedUnder');
		const bookedUnderNewDropdown = document.getElementById('bookedUnderNew');
		
		const grizzlyRate = document.getElementById('grizzlyRate');
		const grizzlyTotalAmt = document.getElementById('grizzlyTotalAmt');
		const grizzlyPercentRate = document.getElementById('grizzlyPercentRate');

		const custDueDate = document.getElementById('custDueDate');
		const carrierPayoutCheckboxDate = document.getElementById('carrierPayoutCheckboxDate');
		const carrierPaymentProof = document.getElementById('carrierPaymentProof');

		const carrierPaymentType = document.getElementById('carrierPaymentType');

		function toggleGrizzlyFields() {
			let activeDropdown = null;

			if (bookedUnderDropdown && bookedUnderDropdown.offsetParent !== null) {
				activeDropdown = bookedUnderDropdown;
			} else if (bookedUnderNewDropdown && bookedUnderNewDropdown.offsetParent !== null) {
				activeDropdown = bookedUnderNewDropdown;
			}

			if (!activeDropdown) {
				console.log('No active dropdown found');
				return;
			}

			const selectedOption = activeDropdown.options[activeDropdown.selectedIndex];
			const companyName = selectedOption.getAttribute('data-company');
			if (companyName === 'Grizzly Freight') {
				carrierPaymentType.value = 'Standard Billing';
				grizzlyRate.style.display = 'block';
				grizzlyTotalAmt.style.display = 'block';
				grizzlyPercentRate.style.display = 'block';

				var rateInput = $('.rateInput').val();
				if(rateInput=='' || rateInput=='NaN'){ rateInput = 0; }

				var agentRateInput = $('#agentRate').val();
				if(agentRateInput=='' || agentRateInput=='NaN'){ agentRateInput = 0; }
				
				var parate = $('.parate').val();
				if(parate=='' || parate=='NaN'){ parate = 0; }

				let carrierPlusAgentRate = parseFloat(rateInput) + parseFloat(agentRateInput); 
				$('#carrierPlusAgentRate').val(carrierPlusAgentRate.toFixed(2));

				let pamargin = parseFloat(parate) - parseFloat(rateInput) - parseFloat(agentRateInput); 
				$('.pamargin').val(pamargin.toFixed(2));
				
				var agentPercentRate = $('#agentPercentRate').val();
				if(agentPercentRate === '' || agentPercentRate === 'NaN'){ agentPercentRate = 0; }
				var agentRateCalc = (parseFloat(pamargin) * parseFloat(agentPercentRate)) / 100;
				$('#agentRateDisplay').text(`(${agentRateCalc.toFixed(2)})`);

				var brookerPercent = (parseFloat(agentRateInput)/parseFloat(rateInput) ) *100;
				$('#brookerPercentDisplay').text(`(${brookerPercent.toFixed(2)}%)`);

				// custDueDate.style.display = 'none';
				carrierPayoutCheckboxDate.style.display = 'none';
				carrierPaymentProof.style.display = 'none';
			} else if (companyName === 'PA Logistics Group LLC'){
				grizzlyRate.style.display = 'block';
				grizzlyTotalAmt.style.display = 'block';
				grizzlyPercentRate.style.display = 'block';

				var rateInput = $('.rateInput').val();
				if(rateInput=='' || rateInput=='NaN'){ rateInput = 0; }

				var agentRateInput = $('#agentRate').val();
				if(agentRateInput=='' || agentRateInput=='NaN'){ agentRateInput = 0; }
				
				var parate = $('.parate').val();
				if(parate=='' || parate=='NaN'){ parate = 0; }

				var agentPercentRate = $('#agentPercentRate').val();
				if(agentPercentRate === '' || agentPercentRate === 'NaN'){ agentPercentRate = 0; }
				var agentRateCalc = (parseFloat(parate) * parseFloat(agentPercentRate)) / 100;
				$('#agentRateDisplay').text(`(${agentRateCalc.toFixed(2)})`);

				let carrierPlusAgentRate = parseFloat(rateInput) + parseFloat(agentRateInput); 
				$('#carrierPlusAgentRate').val(carrierPlusAgentRate.toFixed(2));

				var brookerPercent = (parseFloat(agentRateInput)/parseFloat(rateInput) ) *100;
				$('#brookerPercentDisplay').text(`(${brookerPercent.toFixed(2)}%)`);

				// custDueDate.style.display = 'block';
				carrierPayoutCheckboxDate.style.display = 'block';
				carrierPaymentProof.style.display = 'block';
			}
			else {
				$('#carrierPlusAgentRate').val(0.00);
				$('#agentRate').val(0.00);
				$('#agentPercentRate').val(0.00);
				$('#agentRateDisplay').val(0.00);
				$('#carrierPlusAgentRate').val(0.00);

				grizzlyRate.style.display = 'none';
				grizzlyTotalAmt.style.display = 'none';
				grizzlyPercentRate.style.display = 'none';


				custDueDate.style.display = 'block';
				carrierPayoutCheckboxDate.style.display = 'block';
				carrierPaymentProof.style.display = 'block';
			}
		}

		toggleGrizzlyFields();

		bookedUnderDropdown.addEventListener('change', toggleGrizzlyFields);
		bookedUnderNewDropdown.addEventListener('change', toggleGrizzlyFields);

	});


	$(document).ready(function() {
    $('input[name="pudate"]').on('change', function() {
        let selectedDate = $(this).val();
        let driverId = $('select[name="driver"]').val(); 
        let shipmentType = $('select[name="invoicePDF"]').val();

        if (selectedDate && driverId) {
            $.ajax({
                url: "<?php echo base_url('OutSideDispatch/getNextInvoice'); ?>",
                type: 'POST',
                data: {
                    pudate: selectedDate,
                    driver: driverId
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    $('select[name="dispatchInfoName[]"]').each(function() {
                        if ($(this).val() === 'Carrier Ref No') {
                            var carrierRefInput = $(this).closest('.form-group').find('input[name="dispatchInfoValue[]"]');
                            if (shipmentType == 'Drayage') {
                                // carrierRefInput.val('');
                            } else {
                                carrierRefInput.val(data.invoice);
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        } else {
            console.warn("Please select both a date and a driver.");
        }
    });
});

$(document).ready(function () {
    // Hide both by default
	$('#factoringTypeDev').addClass('d-none');
	$('#factoringCompanyDev').addClass('d-none');
    // When Carrier Payment Type changes
    $('#carrierPaymentType').on('change', function () {
        let val = $(this).val();
        if (val === 'Standard Billing') {
			$('#factoringTypeDev').removeClass('d-none');
			
			$('#factoringType').on('change', function () {
				let val = $(this).val();
				if (val === 'Factoring') {
					$('#factoringCompanyDev').removeClass('d-none');
				} else {
					$('#factoringCompanyDev').addClass('d-none');  
					$('#factoringCompany').val('');  
				}
			});
			$('#factoringType').trigger('change');
        } else {
			$('#factoringTypeDev').addClass('d-none');
			$('#factoringCompanyDev').addClass('d-none');
			$('#factoringCompany').val('');  
			$('#factoringType').val('');  
        }
    });

    // Trigger change on load if values are pre-selected (for edit forms)
    $('#carrierPaymentType').trigger('change');
});
</script>

<style>
	.input-group{position:relative;}
  .tDropdown {position:absolute;width:calc(100% - 30px);max-height:240px;overflow-y:auto;background:white;border:1px solid #ccc;display:none;z-index:1000;top:98%;left:0;}
  .tDropdown div {padding: 5px;cursor: pointer;}
  .tDropdown div:hover {background: #f0f0f0;}
  
.msg-div.error {background:red;}
.msg-div {position: fixed;width: 380px;font-size: 19px;max-width: 98%;background: #28a745;top:20px;right: 0;left:0px;margin:auto;z-index: 9999;padding: 10px;border: 2px solid #28a745; color: #fff;    box-shadow: 0px 0px 7px 10px #aaa;
     display: flex; justify-content: center;align-content: center; flex-wrap: wrap;}
	.pa-invoice .btn-danger{background-color:#000;}
	<?php /*if(isset($_GET['invoice'])){ ?>
		.invoiceEdit::before {  content: "";  position: absolute;  width: 100%;  height: 100%;  z-index: 99;}
		.invoiceEdit {  position: relative;}
		.drop-btn-add, .pickup-btn-add, .dispatchInfo-btn{display:none;}
	<?php } */
	
	if($disp['lockDispatch']=='1') { ?>
		.lockDispatchCls::before {  content: "";  position: absolute;  width: 100%;  height: 100%;  z-index: 99;}
		.lockDispatchCls {  position: relative;}
		.drop-btn-add, .pickup-btn-add, .pick-drop-btn{display:none;}
	<?php } ?>
	
	.download-pdf {color: #fff;border: 1px solid green;border-radius: 7px;padding: 1px 7px;background: #28a745;font-size: 13px;}
	.download-pdf:hover{color:#fff;background:green;text-decoration:none;}
	.fileDownload img{width: 20px;position: absolute;top:0;left: 0;z-index: 99;}
	.d-pdf{display:none;}
	#dataTable11.table td{font-size:15px;}
	#ui-datepicker-div {z-index: 99999 !important;}
	.invoiceCheckboxCls div{display:inline;}
	legend{font-weight:bold;}
	.custom-control-label::before {width: 20px;height: 20px;}
	.custom-control-label::after {width: 20px;height: 20px;}
	.doc-file {display: inline-block;text-align: center;margin: 5px;font-size: 14px;}
	.doc-file span {display: block;}
	.doc-file {display: inline-block;text-align: center;max-width: 145px;position: relative;}
	.doc-file .remove-file {position: absolute;right: 0;top: 0px;padding: 3px;color: red;font-weight: bold;border: 1px solid red;line-height: 13px;}
	form fieldset{position:relative;padding:15px;background: #f6f6f6; border: 1px solid #c5c5c5;}
	fieldset .pick-drop-btn{position:absolute;right:15px;top:-34px;}
	.card .container-fluid, .card .mobile_content, .card .container{padding-left:0;padding-right:0;}
	  .card, .pt-content-body > .container-fluid {    padding: 10px;  }
	  .dispatchInfo-cls.sortable .ui-state-default {border: 0px solid;}
	
	.getAddressParent, .getCompanyParent{position:relative;}
	.addressList, .companyList{position:absolute;top:99%;left:0px;background: #eee;z-index: 999;width: 100%;border: 1px solid #aaa;}
	.addressList li, .companyList li {list-style: none;line-height: 23px;padding: 4px;cursor: pointer;}
	.addressList li:hover, .companyList li:hover {background:#fff;}

	.time-option.focused {
		background-color: #007bff;
		color: white;
	}
	
	.select2-container--default .select2-selection--single {
    border-radius: 26px !important;
	}
	.select2-container .select2-selection--single {
    min-height: 46px !important;
	}
	
	.select2-container--default .select2-selection--single .select2-selection__rendered {
		color: #495057 !important;
		line-height: 43px !important;
		font-size: 14px !important;
		padding-left: 11px !important;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 40px !important;
		right: 3px !important;
	}
</style>