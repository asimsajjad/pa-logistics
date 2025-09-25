<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Update PA Fleet Dispatch 
		<div class="add_page" style="float: right;">
			<?php 
			if(isset($_GET['invoice'])){ 
				$backURL = base_url('admin/invoice');
			} else {
				$backURL = base_url('admin/dispatch');
			}
			?>
			<a href="<?php echo $backURL;?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			<div class="container">
				<h3> Update PA Fleet Dispatch</h3>
				<?php 
					
					
					$disp = $dispatch[0]; 
					
					$cityArr = $locationArr = $companyArr = $vehicleArr = $driverArr = $comAddArr = array();
					
					$dispatchMeta = json_decode($disp['dispatchMeta'],true);
					//$expenses = array('Line Haul','FSC (Fuel Surcharge)','Pre-Pull','Lumper','Detention at Shipper','Detention at Receiver','Detention at Port','Drivers Assist','Gate Fee','Overweight Charges','Delivery Order Charges','Chassis Rental','Demurrage','Layover','Yard Storage','Customs Clearance','Chassis Gate Fee','Chassis Split Fee','Others','TONU','Discount','Dry Run');
					$expenseN = array();
					foreach($expenses as $exp) {
						if($exp['type']=='Negative'){ $expenseN[] = $exp['title']; } 
					}
							
					$js_companies = $company = $dcity = $pcity = $js_location = $js_cities = $plocation = $dlocation = '';
					$paddress = $disp['paddress'];
					$daddress = $disp['daddress'];
					if(!empty($companies)){
						$i = 1;
						foreach($companies as $val){
							//if($i > 1) { $js_companies .= ','; }
							//$js_companies .= '"'.$val['company'].'"';
							
							if($disp['company']==$val['id']) { $company = $val['company']; }
							$companyArr[$val['id']] = $val['company'];
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
						$i = 1;
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
							$i++;
						}
					}
					
					$dClass = $showMsgDiv = '';
				?>
				
				
				<form class="form" id="updatePaFleetform" method="post" action="<?php echo base_url('admin/dispatch/update/'.$this->uri->segment(4)); if(isset($_GET['invoice'])) { echo '?invoice'; } ?>#submit" enctype="multipart/form-data">
					
					<div class="row invoiceEdit lockDispatchCls">
					    <div class="col-sm-12">
						    <?php  
					        if($this->session->flashdata('item')){ ?>
            					<div class="alert alert-success">
            						<h4><?php echo $this->session->flashdata('item');?></h4> 
            					</div>
            					<div class="msg-div"><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></div>
            					<?php 
            					$showMsgDiv = 'true';
            				} else if ($this->session->flashdata('error')){
								$showMsgDiv = 'true';?>
								<div class="alert alert-danger">
            						<h4><?php echo $this->session->flashdata('error');?></h4> 
            					</div>
            					<div class="msg-div error"><?php echo $this->session->flashdata('error'); $this->session->set_flashdata('error',''); ?></div>
            					<?php 
            				    // echo '<div class="msg-div error">Found some errors</div>';
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
						<div class="col-sm-6">
							<div class="form-group">
								<label for="contain">Vehicle</label>
								<select class="form-control" name="vehicle" required>
									<option value="">Select Vehicle</option>
									<?php 
										if(!empty($vehicles)){
											foreach($vehicles as $val){
												echo '<option value="'.$val['id'].'"';
												if($disp['vehicle']==$val['id']) { echo ' selected="selected" '; }
												echo '>'.$val['vname'].' ('.$val['vnumber'].')</option>';
												$vehicleArr[$val['id']] = $val['vname'].' ('.$val['vnumber'].')';
											}
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-sm-6">	     
							<div class="form-group">
								<label for="contain">Driver</label>
								<select class="form-control" id="driver_select" name="driver" required>
									<option value="">Select Driver</option>
									<?php 
										// if(!empty($drivers)){
										// 	foreach($drivers as $val){
										// 		echo '<option value="'.$val['id'].'"';
										// 		if($disp['driver']==$val['id']) { echo ' selected="selected" '; }
										// 		echo '>'.$val['dname'].'</option>';
										// 		$driverArr[$val['id']] = $val['dname'].'';
										// 	}
										// }
									?> 
									<?php 
										$selectedDriverId = $disp['driver'];
										$selectedDriverIsInactive = false;
										if (!empty($drivers)) {
											foreach ($drivers as $val) {
												$isSelected = ($val['id'] == $selectedDriverId);
												$isActive = ($val['status'] === 'Active');
												if ($isSelected && !$isActive) {
													$selectedDriverIsInactive = true;
												}
												if ($isActive || $isSelected) {
													echo '<option value="' . $val['id'] . '"';
													if ($isSelected) echo ' selected';
													echo '>' . $val['dname'];
													if (!$isActive) echo ' (Inactive)';
													echo '</option>';
													$driverArr[$val['id']] = $val['dname'];
												}
											}
										}
									?>
								</select>
								<?php if($selectedDriverIsInactive){ ?>
									<input type="hidden" name="driver" value="<?= $selectedDriverId ?>"/>
								<?php } ?>
							</div>
						</div>
					</div>
					
					<ul class="nav nav-tabs" id="myTab" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link btn-success active" id="general-tab" data-toggle="tab" data-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">General</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link btn-success" id="history-tab" data-toggle="tab" data-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">History</button>
                      </li>
					  <li class="nav-item" role="presentation">
                        <button class="nav-link btn-info" id="reminders-tab" data-toggle="tab" data-target="#reminders" type="button" role="tab" aria-controls="reminders" aria-selected="false">Reminders</button>
                      </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                       <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
						  
						<fieldset class="invoiceEdit lockDispatchCls">
							<legend><input type="text" class="form-control" name="pickup" value="<?php if($dispatchMeta['pickup']) { echo $dispatchMeta['pickup']; } else { echo 'Pick Up'; } ?>"></legend>
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
										<option value="Appointment" <?php if($dispatchMeta['appointmentTypeP']=='Appointment') { echo 'selected'; }?>>By Appointment</option>
										<option value="FCFS" <?php if($dispatchMeta['appointmentTypeP']=='FCFS') { echo 'selected'; }?>>First Come First Serve (FCFS)</option>
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
										<div class="getAddressParent">
											<input type="text" id="plocation" class="form-control getAddress companyI" data-type="company" name="plocation" required value="<?php echo $plocation;?>"> 
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="contain">Pick Up Address</label>
										<div class="getAddressParent">
											<input type="text" id="paddress" class="form-control getAddress addressI" data-type="address" name="paddress" value="<?php echo $paddress;?>"> 
											<input type="hidden" name="paddressid" class="addressidI" value="<?php echo $disp['paddressid'];?>"> 
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Pick Up City</label>
										<div class="getAddressParent">
											<input type="text" id="pcity" class="form-control getAddress cityI" name="pcity" data-type="city" required value="<?php echo $pcity;?>">
										</div>
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
							$pdfArray = array();
							$PDCode = 1;
							if($extraDispatch) {
								foreach($extraDispatch as $info){
									if($info['pd_type']=='pickup') {
										if($info['pd_meta'] != '') {
											$pdMeta = json_decode($info['pd_meta'],true);
										} else { $pdMeta = array(); }
									?>
									<fieldset class="pick-drop-both-<?php echo $info['id'];?> invoiceEdit lockDispatchCls">
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
													<option value="Appointment" <?php if($pdMeta['appointmentType']=='Appointment') { echo 'selected'; }?>>By Appointment</option>
													<option value="FCFS" <?php if($pdMeta['appointmentType']=='FCFS') { echo 'selected'; }?>>First Come First Serve (FCFS)</option>
												</select>
											</div>
										</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label for="contain">Pick Up Time</label>
													<div class="input-group mb-2">
														<input name="ptime1[]" type="text" class="timeInput form-control" value="<?php echo $info['pd_time'];  ?>" readonly>
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
													<div class="getAddressParent">
														<input type="text" id="plocation1" class="form-control location1 getAddress companyI" required data-type="company" name="plocation1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][0]; } else { echo $info['pd_location']; }?>"> 
													</div>
												</div>
											</div>
											<div class="col-sm-4">
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
													<div class="getAddressParent">
														<input type="text" id="pcity1" class="form-control city1 getAddress cityI" data-type="city" required name="pcity1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][1]; } else { echo $info['pd_city']; } ?>">  
													</div>
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
									}
								}
							} ?> 
							
							<div class="pickupExtra"></div>
							
							<fieldset class="invoiceEdit lockDispatchCls">
								<legend><input type="text" class="form-control" name="dropoff" value="<?php if($dispatchMeta['dropoff']) { echo $dispatchMeta['dropoff']; } else { echo 'Drop Off'; } ?>"></legend>
								<button class="btn btn-success btn-sm pick-drop-btn drop-btn-add" type="button">Add New +</button>
								
								<div class="row dropoff-pcode-parent">
									<div class="col-sm-2">
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
											<option value="Appointment" <?php if($dispatchMeta['appointmentTypeD']=='Appointment') { echo 'selected'; }?>>By Appointment</option>
											<option value="FCFS" <?php if($dispatchMeta['appointmentTypeD']=='FCFS') { echo 'selected'; }?>>First Come First Serve (FCFS)</option>
										</select>
									</div>
								</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="contain">Drop Off Time</label>
											<div class="input-group mb-2">
												<input name="dtime" id="dtime" type="text" class="timeInput form-control" value="<?php echo $disp['dtime'];  ?>" readonly>
												<div class="tDropdown"></div>
												<div class="input-group-append">
													<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-5">
										<div class="form-group">
											<label for="contain">Drop Off Company (Location) &nbsp;</label>
											&nbsp;
											<div class="getAddressParent">
												<input type="text" id="dlocation" class="form-control getAddress companyI" data-type="company" name="dlocation" required value="<?php echo $dlocation;?>"> 
											</div>
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
											<div class="getAddressParent">
												<input type="text" id="dcity" class="form-control getAddress cityI" data-type="city" name="dcity" required value="<?php echo $dcity;?>">  
											</div>
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
											<label for="contain">Driver Notes</label> 
											<textarea name="dnotes" class="form-control"><?php echo $disp['dnotes'] ?></textarea>
										</div>
									</div>
									
								</div>
							</fieldset>
							
							<div id="sortable" class="dropoffExtra lockDispatchCls">
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
													<div class="col-sm-2">
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
														<option value="Appointment" <?php if($pdMeta['appointmentType']=='Appointment') { echo 'selected'; }?>>By Appointment</option>
														<option value="FCFS" <?php if($pdMeta['appointmentType']=='FCFS') { echo 'selected'; }?>>First Come First Serve (FCFS)</option>
													</select>
												</div>
											</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label for="contain">Drop Off Time</label>
															
															<div class="input-group mb-2">
																<input name="dtime1[]" type="text" class="timeInput form-control" value="<?php echo $info['pd_time'];?>" readonly>
																<div class="tDropdown"></div>
																<div class="input-group-append">
																	<div class="input-group-text timedd" style="width: 32px;padding: 2px;background: white;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-5">
														<div class="form-group">
															<label for="contain">Drop Off Company (Location) &nbsp;</label>
															<div class="getAddressParent">
															  <input type="text" class="form-control location1 getAddress companyI" data-type="company" name="dlocation1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][0]; } else { echo $info['pd_location']; }?>"> 
															</div>
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label for="contain">Drop Off Address</label>
															<div class="getAddressParent">
																<input type="hidden" name="daddressid1[]" class="addressidI" value="<?php echo $info['pd_addressid'];?>"> 
															  <input type="text" id="daddress1" data-type="address" class="form-control getAddress addressI" name="daddress1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][2]; } else { echo $info['pd_address']; }?>"> 
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label for="contain">Drop Off City</label>
															<div class="getAddressParent">
															  <input type="text" class="form-control city1 getAddress cityI" data-type="city" name="dcity1[]" value="<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][1]; } else { echo $info['pd_city']; }?>">
															</div>
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
									} 
								?> 
							</div>
							
					  
						<div class="row lockDispatchCls">
							<div class="col-sm-2">
								<div class="form-group">
									<label for="contain">Rate</label>
									<div class="input-group mb-2">
										<div class="input-group-prepend">
											<div class="input-group-text">$</div>
										</div>
										<input name="rate" type="number" min="0" step="0.01" class="form-control" value="<?php echo $disp['rate']; ?>">
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
										<input name="parate" type="number"step="0.01" class="form-control parate" required value="<?php echo $disp['parate']; ?>" data-price="<?php echo $disp['parate']; ?>">
									</div>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label for="contain">Margin</label>
									<div class="input-group mb-2">
										<div class="input-group-prepend">
											<div class="input-group-text">$</div>
										</div>
										<input name="pamargin" readonly type="number"step="0.01" class="form-control" value="<?php echo ($disp['parate'] - $disp['rate']); ?>">
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
    									<label for="contain">Logistics Rate</label>
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
    									<label for="contain">Logistics Invoice Amount</label>
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
							<?php 
							if ($unitPrice) { 
								$toggleColor = false; 
							?>
								<div class="col-sm-12 row">
									<?php 
									foreach ($unitPrice as $val) { 
										$units = $val['unit']; 
										$price = $val['unitPrice']; 

										$backgroundColor = $toggleColor ? '#f9f9f9' : '##ffffff'; 
									?>
										<div class="col-sm-2" style="background-color: <?= $backgroundColor ?>;">
											<div class="form-group">
												<label for="contain">Units</label>
												<div class="input-group mb-2">
													<div class="input-group-prepend">
														<div class="input-group-text"></div>
													</div>
													<input readonly name="" type="number" step="0.01" class="form-control" value="<?= $units ?>">
												</div>
											</div>
										</div>
										<div class="col-sm-2" style="background-color: <?= $backgroundColor ?>;">
											<div class="form-group">
												<label for="contain">Unit Price</label>
												<div class="input-group mb-2">
													<div class="input-group-prepend">
														<div class="input-group-text">$</div>
													</div>
													<input readonly name="" type="number" step="0.01" class="form-control" value="<?= $price ?>">
												</div>
											</div>
										</div>
									<?php 
										$toggleColor = !$toggleColor;
									} 
									?>
								</div>
							<?php 
							} 
							?>
							<div class="col-sm-12">
								<fieldset>
									<legend>Expense:</legend>
									<button class="btn btn-success btn-sm pick-drop-btn expense-btn" type="button">Add New +</button>
									<div class="row expense-cls">
										<?php
											$e = 1;
											$paRate = $disp['parate'];
											if($dispatchMeta['expense']) { 
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
														<input name="expensePrice[]" data-cls=".expenseName-<?=$e?>" required type="number" min="1" class="form-control expenseAmt" value="<?=$expVal[1]?>" step="0.01">
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
							<div class="col-sm-3">
								<div class="form-group">
									<label for="contain">Company</label>
									<div class="getCompanyParent">
									    <input type="text" id="companies" class="form-control getCompany" name="company" required value="<?php echo $company;?>">
								    </div>
								</div>
							</div>
							<input type="hidden" id="company_id" name="company_id" value="<?php echo $disp['company']; ?>">
								<div class="col-sm-3">
									<div class="form-group">
										<label for="shipping_contact">Shipping Contact</label>
										<select id="shipping_contact" name="shipping_contact" class="form-control">
											<option value="">-- Select Shipping Contact --</option>
										</select>
									</div>
								</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="contain">Tracking #</label>
									<input required name="tracking" id="tracking" type="text" class="form-control" value="<?php echo $disp['tracking']; ?>">
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="contain">Trailer #</label>
									<input required name="trailer" type="text" class="form-control" value="<?php echo $disp['trailer']; ?>">
								</div>
							</div> 
							<?php if($childTrailer) { ?>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="contain">Trailers #</label>
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
							<?php } ?>
							
							
							<div class="col-sm-12">
								<fieldset>
									<legend>Sub Invoice:</legend>
									<button class="btn btn-success btn-sm pick-drop-btn childInvoice-btn" type="button">Add New +</button>
									<button style="right:120px" class="btn btn-success btn-sm pick-drop-btn otherChildInvoice-btn" type="button">Outside Add New +</button>
									<div class="row childInvoice-cls">
										<?php
											$e = 1;
											if($disp['childInvoice'] != '') { 
											    $childInvoice = explode(',',$disp['childInvoice']);
												foreach($childInvoice as $expVal) {
													$e++;
													$cPaRate = 0;
															foreach($childTrailer as $val){
																if(trim($val['invoice']) == trim($expVal)){
																	if($val['parate'] > 0) { $cPaRate = 'Inv. Amt.: $'.$val['parate']; }
																	else { $cPaRate = 'Rate: $'.$val['rate']; }
																	$invoiceId = $val['id'];
																}
															}
												?>
												<div class="col-sm-3 childInvoice-div-<?=$e?>">
													<div class="form-group">
														<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".childInvoice-div-<?=$e?>" type="button" style="top:0px;">X</button>
														<button class="btn btn-primary btn-sm" onclick="window.open('<?=base_url().'admin/dispatch/update/'.$invoiceId?>', '_blank');" type="button">Go To</button>
														<input name="childInvoice[]" required type="text" class="form-control" value="<?=$expVal?>" placeholder="PA Invoice">
															<input name="childInvoiceRate[]" readonly type="text" class="form-control" value="<?=$cPaRate?>" placeholder="Invoice Amount">
													</div>
												</div>
												<?php }
											}
											
											if($dispatchMeta['otherChildInvoice'] != '') { 
											    $otherChildInvoiceInfo = explode(',',$dispatchMeta['otherChildInvoice']);
												foreach($otherChildInvoiceInfo as $expVal) {
													$e++;
													$cPaRate = 0;
													if($otherChildInvoice){
														foreach($otherChildInvoice as $val){
															if(trim($val['invoice']) == trim($expVal)){
																if($val['parate'] > 0) { $cPaRate = 'Inv. Amt.: $'.$val['parate']; }
																else { $cPaRate = 'Rate: $'.$val['rate']; }
																$invoiceId = $val['id'];
															}
														}
													}
													
												?>
												<div class="col-sm-3 out-invoice childInvoice-div-<?=$e?>">
													<div class="form-group">
														<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".childInvoice-div-<?=$e?>" type="button" style="top:0px;">X</button>
														<button class="btn btn-primary btn-sm" onclick="window.open('<?=base_url().'admin/outside-dispatch/update/'.$invoiceId?>', '_blank');" type="button">Go To</button>
														<input name="otherChildInvoice[]" required type="text" class="form-control" value="<?=$expVal?>" placeholder="Outside Invoice">
														
															<input name="otherChildInvoiceRate[]" readonly type="text" class="form-control" value="<?=$cPaRate?>" placeholder="Invoice Amount">
													</div>
												</div>
												<?php }
											}
										?>
									</div>
								</fieldset>
							</div>
							
						</div>
						
						<fieldset class="lockDispatchCls">
							<legend>Financial:</legend>
							<div class="row">
								
								<div class="col-sm-2">
									<div class="form-group">
										<label for="contain">Invoice #</label>
										<input <?php if(strtotime($disp['pudate']) > strtotime('2024-04-24')) { echo 'readonly'; } ?> name="invoice" id="invoice_no" type="text" class="form-control" value="<?php echo $disp['invoice']; ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="contain">Week</label>
										<input name="dWeek" readonly type="text" class="form-control" value="<?php echo $disp['dWeek']; ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="contain">Payout Amount</label>
										<input name="payoutAmount" readonly type="text" class="form-control" value="<?php echo $disp['payoutAmount']; ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="contain">Partial Amount</label>
										<input name="partialAmount" type="number" min="0" step="0.01" class="form-control" value="<?php echo ($dispatchMeta['partialAmount']) ? $dispatchMeta['partialAmount']: 0; ?>">
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
										<input name="expectPayDate" readonly type="text" class="form-control datepicker" value="<?php if($disp['expectPayDate']=='0000-00-00' || $disp['invoiceDate']=='0000-00-00') { echo 'TBD'; } else { echo $disp['expectPayDate']; } ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="contain">Invoice Type</label>
										<input type="hidden" name="invoiceTypeOld" value="<?=$disp['invoiceType']?>">
										<select class="form-control invoiceTypeCls" name="invoiceType" <?php if(isset($_GET['invoice'])){ echo 'required'; } ?>>
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
											<input type="hidden" name="invoiceReady" value="0">
											<input type="checkbox" class="custom-control-input invoiceCheckCls" id="invoiceTypeInvoiceReady" name="invoiceReady" <?php if($dispatchMeta['invoiceReady']=='1') { echo 'checked'; } ?> value="1">
											<label class="custom-control-label" for="invoiceTypeInvoiceReady"><span class="invoiceTitle"><?=$invoiceType?></span> Ready To Submit</label>
										</div>
									</div>
									<div class="form-group <?php //if(!isset($_GET['invoice'])){ echo 'hide d-none'; } ?>"  style="display:<?php if($disp['invoiceType']=='') { echo 'none'; } else { echo 'inline'; } ?>">
										<div class="custom-control custom-checkbox my-1 mr-sm-2" >
											<input type="hidden" name="invoiced" value="0">
											<input type="checkbox" class="custom-control-input invoiceCheckCls" id="invoiceTypeInvoiced" name="invoiced" <?php if($dispatchMeta['invoiced']=='1') { echo 'checked'; } ?> value="1">
											<label class="custom-control-label" for="invoiceTypeInvoiced"><span class="invoiceTitle"><?=$invoiceType?></span> Invoiced</label>
										</div>
									</div>
									<div class="form-group <?php //if(!isset($_GET['invoice'])){ echo 'hide d-none'; } ?>"  style="display:<?php if($disp['invoiceType']=='') { echo 'none'; } else { echo 'inline'; } ?>">
										<div class="custom-control custom-checkbox my-1 mr-sm-2">
											<input type="hidden" name="invoicePaid" value="0">
											<input type="checkbox" class="custom-control-input invoiceCheckCls" id="invoiceTypeInvoicePaid" name="invoicePaid" <?php if($dispatchMeta['invoicePaid']=='1') { echo 'checked'; } ?> value="1">
											<label class="custom-control-label" for="invoiceTypeInvoicePaid"><span class="invoiceTitle"><?=$invoiceType?></span> Paid</label>
										</div>
									</div>
									<div class="form-group <?php //if(!isset($_GET['invoice'])){ echo 'hide d-none'; } ?>"  style="display:<?php if($disp['invoiceType']=='') { echo 'none'; } else { echo 'inline'; } ?>">
										<div class="custom-control custom-checkbox my-1 mr-sm-2">
											<input type="hidden" name="invoiceClose" value="0">
											<input type="hidden" name="invoiceCloseOld" value="<?=$dispatchMeta['invoiceClose']?>">
											<input type="checkbox" class="custom-control-input invoiceCheckCls" id="invoiceTypeInvoiceClose" name="invoiceClose" <?php if($dispatchMeta['invoiceClose']=='1') { echo 'checked'; } ?> value="1">
											<label class="custom-control-label" for="invoiceTypeInvoiceClose"><span class="invoiceTitle"><?=$invoiceType?></span> Closed</label>
										</div>
									</div>
								</div>
								
							</div>
							<div class="row paid-close-date" <?php //if(!isset($_GET['invoice']) && $disp['invoiceType']!='RTS'){ echo 'style="display:none"'; } ?>>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Ready Submit Date</label>
										<input name="invoiceReadyDate" type="text" class="form-control datepicker invoiceReadyDate" value="<?php echo $dispatchMeta['invoiceReadyDate']; ?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Invoice Date</label>
										<input <?php if(isset($_GET['invoice'])){ echo 'required'; } ?> name="invoiceDate" type="text" class="form-control datepicker invoiceDate" placeholder="TBD" value="<?php if($disp['invoiceDate']=='0000-00-00') { echo 'TBD'; } else { echo $disp['invoiceDate']; } ?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Invoice Paid Date</label>
										<input name="invoicePaidDate" type="text" class="form-control datepicker invoicePaidDate" value="<?php echo $dispatchMeta['invoicePaidDate']; ?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Invoice Close Date</label>
										<input name="invoiceCloseDate" type="text" class="form-control datepicker invoiceCloseDate" value="<?php echo $dispatchMeta['invoiceCloseDate']; ?>">
									</div>
								</div>
							</div>
						</fieldset>
						<p>&nbsp;</p>
						<div class="row">
						    <div class="col-sm-5">
								<div class="custom-control custom-checkbox my-1 mr-sm-2" style="display: inline;color:#ff0047;">
									<input type="checkbox" class="custom-control-input" id="delivered" name="delivered" value="yes" <?php if($disp['delivered']=='yes') { echo ' checked'; } ?>>
									<label class="custom-control-label" for="delivered">Delivered</label>
								</div>
    							<p>&nbsp;</p>
						    </div>
						</div>
						<div class="row lockDispatchCls">
							<div class="col-sm-12 invoiceEdit">
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<div class="custom-control custom-checkbox my-1 mr-sm-2">
												<input type="checkbox" class="custom-control-input bolrc" id="customControlInline" name="bol" value="AK" <?php if($disp['bol']=='AK') { echo ' checked'; } ?>>
												<label class="custom-control-label" for="customControlInline">BOL</label> <a data-cls=".d-bol" href="#" class="download-pdf">Download All</a>
											</div>
											<input name="bol_d[]" multiple type="file" class="form-control">
										</div>
									</div>
									<div class="col-sm-8">
										<label for="contain">&nbsp;</label><br>
										<?php 
										if(!empty($documents)) { 
											foreach($documents as $doc) {
												if($doc['type']=='bol') { 
												    $pdfArray[] = array('upload',$doc['fileurl']);
													echo '<a class="d-pdf d-bol" href="'.base_url('admin/download_pdf/upload/'.$doc['fileurl']).'">download</a>';
													echo '<span class="doc-file">
													<a target="_blank" download href="'.base_url().'admin/download_pdf/upload/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
													if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/dispatch/removefile/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
													echo '<a target="_blank" href="'.base_url('assets/upload/').''.$doc['fileurl'].'?id='.rand(10,99).'">
													<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
												}
											}
										}

										if(!empty($otherDocuments)) { 
											foreach($otherDocuments as $doc) {
												if($doc['type']=='bol') { 
													$pdfArray[] = array('outside-dispatch--bol',$doc['fileurl']);
													    echo '<a class="d-pdf d-bol" href="'.base_url('admin/download_pdf/outside-dispatch--bol/'.$doc['fileurl']).'">download</a>';
														echo '<span class="doc-file">
													    <a target="_blank" download href="'.base_url().'assets/outside-dispatch/bol/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
														if($doc['otherParent'] != 'yes') { echo '<a href="'.base_url().'admin/outside-dispatch/removefile/bol/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
														echo '<a target="_blank" href="'.base_url('assets/outside-dispatch/bol/').''.$doc['fileurl'].'?id='.rand(10,99).'">
														<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
												}
											}
										}
										?>
									</div>
								</div>
							</div>
							<div class="col-sm-12 invoiceEdit">
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group"> 
											<div class="custom-control custom-checkbox my-1 mr-sm-2">
												<input type="checkbox" name="rc" class="custom-control-input bolrc" id="customControlInlinerc" value="AK" <?php if($disp['rc']=='AK') { echo ' checked'; } ?>>
												<label class="custom-control-label" for="customControlInlinerc">RC</label>  <a data-cls=".d-rc" href="#" class="download-pdf">Download All</a>
											</div>
											<input name="rc_d[]" multiple type="file" class="form-control">
										</div>
									</div>
									<div class="col-sm-8">
										<label for="contain">&nbsp;</label><br>
										<?php if(!empty($documents)) { 
											foreach($documents as $doc) {
												if($doc['type']=='rc') { 
												    $pdfArray[] = array('upload',$doc['fileurl']);
													echo '<a class="d-pdf d-rc" href="'.base_url('admin/download_pdf/upload/'.$doc['fileurl']).'">download</a>';
													echo '<span class="doc-file">
													<a target="_blank" download href="'.base_url().'admin/download_pdf/upload/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
													if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/dispatch/removefile/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
													echo '<a target="_blank" href="'.base_url('assets/upload/').''.$doc['fileurl'].'?id='.rand(10,99).'">
													<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
												}
											}
										}
										if(!empty($otherDocuments)) { 
											foreach($otherDocuments as $doc) {
												if($doc['type']=='rc') { 
													$pdfArray[] = array('outside-dispatch--rc',$doc['fileurl']);
													echo '<a class="d-pdf d-rc" href="'.base_url('admin/download_pdf/outside-dispatch--rc/'.$doc['fileurl']).'">download</a>';
													echo '<span class="doc-file">
													<a target="_blank" download href="'.base_url().'assets/outside-dispatch/rc/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
													if($doc['otherParent'] != 'yes') { echo '<a href="'.base_url().'admin/outside-dispatch/removefile/rc/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
													echo '<a target="_blank" href="'.base_url('assets/outside-dispatch/rc/').''.$doc['fileurl'].'?id='.rand(10,99).'">
													<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
												}
											}
										}
									?>
									</div>
								</div>
							</div>
							
							<div class="col-sm-12 invoiceEdit">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group"> 
											<div class="custom-control custom-checkbox my-1 mr-sm-2">
												<input type="checkbox" name="gd" class="custom-control-input bolrc" id="customControlInlinegd" value="AK" <?php if($disp['gd']=='AK') { echo ' checked'; } ?>>
												<label class="custom-control-label" for="customControlInlinegd">Payment Proof</label>  <a data-cls=".d-gd" href="#" class="download-pdf">Download All</a>
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
												    $pdfArray[] = array('upload',$doc['fileurl']);
													echo '<a class="d-pdf d-gd" href="'.base_url('admin/download_pdf/upload/'.$doc['fileurl']).'">download</a>';
													echo '<span class="doc-file">
													<a target="_blank" download href="'.base_url().'admin/download_pdf/upload/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
													if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/dispatch/removefile/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
													echo '<a target="_blank" href="'.base_url('assets/upload/').''.$doc['fileurl'].'?id='.rand(10,99).'">
													<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
												}
											}
										}
											if(!empty($otherDocuments)) { 
												foreach($otherDocuments as $doc) {
													if($doc['type']=='gd') { 
													    $gdfile = 'yes';
														if($doc['parentType'] == 'logistics'){
													  	 	$pdfArray[] = array('outside-dispatch--gd',$doc['fileurl']);
															$downloadUrl = base_url('admin/download_pdf/outside-dispatch--gd/'.$doc['fileurl']);
															$fileDownloadUrl = base_url().'assets/outside-dispatch/gd/'.$doc['fileurl'];
															$fileUrl =base_url('assets/outside-dispatch/gd/').''.$doc['fileurl'].'?id='.rand(10,99);
															$removeUrl = base_url().'admin/outside-dispatch/removefile/gd/'.$doc['id'].'/'.$disp['id'];
														}elseif($doc['parentType'] == 'warehousing'){
															$pdfArray[] = array('warehouse--gd', $doc['fileurl']);
															$downloadUrl = base_url('admin/download_pdf/warehouse--gd/' . $doc['fileurl']);
															$fileDownloadUrl = base_url().'assets/warehouse/gd/'.$doc['fileurl'];
															$fileUrl = base_url('assets/warehouse/gd/' . $doc['fileurl']);
															$removeUrl = base_url('admin/paWarehouse/removefile/gd/' . $doc['id'] . '/' . $disp['id']);
														}
													    echo '<a class="d-pdf d-gd" href="' . $downloadUrl . '">download</a>';
														echo '<span class="doc-file">
													    <a target="_blank" download href="' . $fileDownloadUrl . '" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
														if($doc['otherParent'] != 'yes') { echo '<a href="' . $removeUrl . '" class="remove-file">X</a>'; }
														echo '<a target="_blank" href="' . $fileUrl . '">
														<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
													}
												}
											}
										?>
										<input type="hidden" name="gdfile" value="<?=$gdfile?>">
									</div>
									
									<div class="col-sm-6">
										<div class="form-group"> 
											<label>Customer Invoice</label>  <a data-cls=".d-paInvoice" href="#" class="download-pdf">Download All</a>
											<input name="paInvoice[]" type="file" class="form-control">
										</div>
										<label for="contain">&nbsp;</label><br>
										<?php
										if(!empty($documents)) { 
											foreach($documents as $doc) {
												if($doc['type']=='paInvoice') { 
												    $pdfArray[] = array('upload',$doc['fileurl']);
													echo '<a class="d-pdf d-paInvoice" href="'.base_url('admin/download_pdf/paInvoice/'.$doc['fileurl']).'">download</a>';
													echo '<span class="doc-file">
													<a target="_blank" download href="'.base_url().'admin/download_pdf/paInvoice/'.$doc['fileurl'].'" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
													if($doc['parent'] != 'yes') { echo '<a href="'.base_url().'admin/dispatch/removefile/'.$doc['id'].'/'.$disp['id'].'" class="remove-file">X</a>'; }
													echo '<a target="_blank" href="'.base_url('assets/paInvoice/').''.$doc['fileurl'].'?id='.rand(10,99).'">
													<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
												}
											}
										}
										if(!empty($otherDocuments)) { 
											foreach($otherDocuments as $doc) {
												if($doc['type']=='paInvoice') { 
													if($doc['parentType'] == 'fleet'){
														$pdfArray[] = array('outside-dispatch--invoice',$doc['fileurl']);
														$downloadUrl = base_url('admin/download_pdf/outside-dispatch--invoice/'.$doc['fileurl']);
														$fileDownloadUrl = base_url().'assets/outside-dispatch/invoice/'.$doc['fileurl'];
														$fileUrl =base_url('assets/outside-dispatch/invoice/').''.$doc['fileurl'].'?id='.rand(10,99);
														$removeUrl = base_url().'admin/outside-dispatch/removefile/invoice/'.$doc['id'].'/'.$disp['id'];
													}elseif($doc['parentType'] == 'warehousing'){
														$pdfArray[] = array('warehouse--invoice', $doc['fileurl']);
														$downloadUrl = base_url('admin/download_pdf/warehouse--invoice/' . $doc['fileurl']);
														$fileDownloadUrl = base_url().'assets/warehouse/invoice/'.$doc['fileurl'];
														$fileUrl = base_url('assets/warehouse/invoice/' . $doc['fileurl']);
														$removeUrl = base_url('admin/paWarehouse/removefile/invoice/' . $doc['id'] . '/' . $disp['id']);
													}
													echo '<a class="d-pdf d-paInvoice" href="' . $downloadUrl . '">download</a>';
													echo '<span class="doc-file">
													<a target="_blank" download href="' . $fileDownloadUrl . '" class="fileDownload"><img src="/assets/images/download_icon.png"></a>';
													if($doc['otherParent'] != 'yes') { echo '<a href="' . $removeUrl . '" class="remove-file">X</a>'; }
													echo '<a target="_blank" href="' . $fileUrl . '">
													<i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
												}
											}
										}
										?>
									</div>
								</div>
							</div>
							
							<?php if($pdfArray){ ?>
							<div class="col-sm-12">
								<div class="form-group"><button id="download-pdfs" class="btn btn-success btn-sm download-all-pdf" type="button">Download All Files</button><br></div>
							</div>
							<?php } ?>
							
							<?php if($disp['detention'] > 0){ ?>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Detention</label> 
										&nbsp;
										<div class="custom-control custom-checkbox my-1 mr-sm-2" style="display: inline;color:#ff0047;">
											<input type="checkbox" readonly class="custom-control-input" id="detention_check" name="detention_check" value="yes" <?php if($disp['detention_check']=='yes') { echo ' checked'; } ?>>
											<label class="custom-control-label" for="detention_check"> &nbsp;&nbsp;</label>
										</div>
										<input name="detention" readonly type="number" min="0" class="form-control" value="<?php echo $disp['detention']; ?>" step="0.01">
									</div>
								</div>
							<?php } ?>
							<?php if($disp['dassist'] > 0){ ?>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="contain">Driver Assist</label> 
										&nbsp;
										<div class="custom-control custom-checkbox my-1 mr-sm-2" style="display: inline;color:#ff0047;">
											<input type="checkbox" readonly class="custom-control-input" id="dassist_check" name="dassist_check" value="yes" <?php if($disp['dassist_check']=='yes') { echo ' checked'; } ?>>
											<label class="custom-control-label" for="dassist_check"> &nbsp;&nbsp;</label>
										</div>
										<input name="dassist" readonly type="number" min="0" class="form-control" value="<?php echo $disp['dassist']; ?>">
									</div>
								</div>
							<?php } ?>
							
							<div class="col-sm-6">
								<div class="form-group">
									<label for="contain">Dispatch Notes</label>
									<input name="status" type="text" class="form-control statusCls" data="<?=$disp['status']?>" value="<?=$disp['status']?>">
								</div>
							</div>
							
							<div class="col-sm-6">
								<div class="form-group">
									<label for="contain">Dispatch Status</label> 
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
						
						<div class="row lockDispatchCls">
							<div class="col-sm-12">
								<div class="form-group">
									<label for="contain">Dispatch Remarks</label> 
									<textarea  name="notes" class="form-control notesCls"><?php echo $disp['notes'] ?></textarea>
								</div>
							</div>
							
							<div class="col-sm-12 <?php //if(isset($_GET['invoice'])){} else { echo 'hide d-none'; } ?>">
								<div class="form-group">
									<label for="contain">Invoice Description</label> 
									<textarea <?php if($invoiceType=='DB' || $invoiceType=='QP'){ echo 'required'; } ?> name="invoiceNotes" class="form-control invoiceNotes"><?php echo $disp['invoiceNotes'] ?></textarea>
								</div>
							</div>
						</div>
						
						<div class="row" id="submit">
							<div class="col-sm-4"> 
								<div class="form-group">
									<input type="submit" name="save" value="<?php if(isset($_GET['invoice'])){ echo 'Update Invoice'; } else { echo 'Update Dispatch'; } ?>" class="btn btn-primary"/>
								</div>
							</div>
							<?php if(isset($_GET['invoice'])){ ?>
							<div class="col-sm-4"> 
								<div class="form-group">
									<a class="btn btn-success editInvoice" data-id="<?=$disp['id']?>" data-dTable="dispatch" 
									href="<?php echo base_url('Invoice/downloadInvoicePDF/'.$disp['id']);?>" data-toggle="modal" 
									data-target="#editInvoiceModal">Generate Invoice</a>
								</div>
							</div>	
							<?php } ?>
							<div class="col-sm-4"> 
								<div class="form-group">
									<div class="custom-control custom-checkbox my-1 mr-sm-2">
									    <input type="hidden" value="0" name="lockDispatch">
										<input type="checkbox" name="lockDispatch" id="customLockDispatch" class="custom-control-input" value="1" <?php if($disp['lockDispatch']=='1') { echo ' checked'; } ?>>
										<label class="custom-control-label" for="customLockDispatch">Lock Dispatch</label>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
						    <div class="col-sm-12">
						        
						    </div>
						</div>
						
					  </div>
					  
                      <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
						  
					<?php if($dispatchLog){ ?>
						
						<div class="row">
							<div class="col-sm-12">
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
														
														echo $log['type'].'Changed the '.$val[0].' value from <strong>"'.$old.'"</strong> to <strong>"'.$new.'"</strong><br>';
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

					    <div class="tab-pane fade" id="reminders" role="tabpanel" aria-labelledby="reminders-tab">
							<?php if (!empty($reminderLog)) { ?>
								<div class="row">
									<div class="col-sm-12">
										<h3>Reminder History</h3>
										<div class="table-responsive">
											<table class="table table-bordered" id="reminder_datatable" width="100%" cellspacing="0">
												<thead style="position:sticky;top:0">
													<tr>
														<th>Sr no.</th>
														<th>User</th>
														<th>Date</th>
														<th>Subject</th>
														<th>Note</th>
													</tr> 
												</thead>
												<tbody>
													<?php 
													$i = 1;
													foreach ($reminderLog as $log) { ?>
														<tr>
															<td><?= $i; ?></td>
															<td><?= htmlspecialchars($log['uname']); ?></td>
															<td style="">
																<span style="white-space:nowrap;"><?= date('d M Y', strtotime($log['date'])); ?></span><br>
																<?= date('h:i A', strtotime($log['date'])); ?>
															</td>
															<td><?= htmlspecialchars($log['subject']); ?></td>
															<td><?= $log['note']; ?></td>
														</tr>
													<?php $i++; } ?>
												</tbody>
											</table>

											<?php if (count($reminderLog) > 5) { ?>
												<a href="#" id="showMoreReminders" class="btn btn-sm">Show More</a>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php } else { ?>
								<p>No reminders found.</p>
							<?php } ?>
						</div>
						<ul class="nav nav-tabs mb-2" id="myTabBottom" role="tablist" style="float:right; margin-top: -1px;">
							<li class="nav-item" role="presentation">
								<button style="border-radius:unset;" class="nav-link btn-success active" id="general-tab-bottom" data-toggle="tab" data-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">General</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link btn-success" id="history-tab-bottom" data-toggle="tab" data-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">History</button>
							</li>
							<li class="nav-item" role="presentation">
								<button style="border-radius:unset;" class="nav-link btn-info" id="reminders-tab-bottom" data-toggle="tab" data-target="#reminders" type="button" role="tab" aria-controls="reminders" aria-selected="false">Reminders</button>
							</li>
						</ul>
                    </div>
					
				</form>
			
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
						<!-- <a href="#" class="btn btn-primary combibePdfBtn" target="_blank">Combine PDF</a> -->
						<a href="#" class="btn btn-primary combibePdfBtn" data-toggle="modal" data-target="#combinePdfModal">Combine PDF / Email</a>
						<!-- <a href="#" class="btn btn-success downloadPDF" data-type="dispatch" data-id="">Download All</a> -->
						<!-- <a href="#" class="btn btn-success emailBtn" data-type="" data-id="">Email</a> -->
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
		<div class="form-group">
			<label for="email_subject"><strong>Email Header</strong></label>
			<textarea class="form-control" rows="1" name="email_subject" id="email_subject"></textarea>
		</div>
		<div class="form-group">
			<label for="email_body"><strong>Email Body</strong></label>
			<textarea class="form-control" rows="1" name="email_body" id="email_body"></textarea>
		</div>
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

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<!--script src="https://code.jquery.com/jquery-3.7.1.js"></script-->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!--script src="/assets/bootstrap-select.js"></script>
<link href="/assets/bootstrap-select.css" rel="stylesheet" /-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo base_url('assets/ckeditor/ckeditor.js'); ?>"></script>

<script>
	$(document).ready(function() {
	    <?php if($showMsgDiv == 'true') { ?>
	    setTimeout(function(){
	        $('.msg-div').hide();
	    }, 5000);
	    $('html, body').animate({
                scrollTop: $("#submit").offset().top
            }, 'slow');
	    <?php } ?>
	   /* $(document).scroll(function(){
          $('.msg-div').hide();
        });*/

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
			let companyId   = $(this).attr('data-id');

    		fieldset.find('.getCompany').val($(this).attr('data-company'));
    		fieldset.find('.companyList').html('').remove();

			$('#company_id').val(companyId);

			$.ajax({
				url: "<?= base_url('Comancontroler/getShippingContacts') ?>",
				type: "POST",
				data: { company_id: companyId },
				dataType: "json",
				success: function(data) {
					let $dropdown = $("#shipping_contact");
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
		$('body').on('keydown', '.getCompany', function () {
			clearTimeout(typingTimer);
		});
		
		//$( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
		$('body').on('focus',".datepicker", function(){
			$(this).datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
		});
		$(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
		
		$( "#sortable" ).sortable();
		 
		<?php /*if(isset($_GET['invoice'])){ ?>
			$('.invoiceEdit input, .invoiceEdit select, .invoiceEdit textarea').attr('readonly','');
		<?php }*/ ?>
		<?php if($disp['lockDispatch']=='1') { ?>
		    $('.lockDispatchCls input, .lockDispatchCls select, .lockDispatchCls textarea').attr('readonly','');
		<?php } ?>
		
		//var companies = [<?php //echo $js_companies; ?>];
		//$( "#companies" ).autocomplete({ source: companies }); 
		
		$('#dataTable11 tbody tr').slice(5).hide();
        $('#showMore').click(function(e) {
            e.preventDefault();
            $('#dataTable11 tbody tr').show();
            $(this).hide(); // Hide the "Show More" button
        });
        
		$('#reminder_datatable tbody tr').slice(5).hide();
        $('#showMoreReminders').click(function(e) {
            e.preventDefault();
            $('#reminder_datatable tbody tr').show();
            $(this).hide(); 
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
				downloadpdf(href);
                /*var link = document.createElement('a');
                link.href = "<?php echo base_url('admin/download_pdf/'); ?>" + file.folder + '/' + file.file;
                link.target = '_blank';
                link.download = file.file;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);*/
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
		if (!$('.d-rc').length) { $('a[data-cls=".d-rc"]').hide(); }
		if (!$('.d-gd').length) { $('a[data-cls=".d-gd"]').hide(); } 
		if (!$('.d-paInvoice').length) { $('a[data-cls=".d-paInvoice"]').hide(); }
        
		
		/*var cities = [<?php //echo $js_cities; ?>];
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
		
		$('.childInvoice-btn').click(function(){
			var expenseDiv = '<div class="col-sm-3 childInvoice-div-'+dcid+'">\
			<div class="form-group">\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".childInvoice-div-'+dcid+'" type="button" style="top:0px;">X</button>\
			<input name="childInvoice[]" required type="text" class="form-control" placeholder="PA Invoice" value="">\
			</div>\
			</div>';
			dcid++;
			$('.childInvoice-cls').append(expenseDiv);
		});
		$('.otherChildInvoice-btn').click(function(){
			var expenseDiv = '<div class="col-sm-3 out-invoice childInvoice-div-'+dcid+'">\
			<div class="form-group">\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removecls=".childInvoice-div-'+dcid+'" type="button" style="top:0px;">X</button>\
			<input name="otherChildInvoice[]" required type="text" class="form-control" placeholder="Outside Invoice" value="">\
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
			<input name="expensePrice[]" data-cls=".expenseName-'+dcid+'" required type="number" min="1" class="form-control expenseAmt" value="0" step="0.01">\
			</div>\
			</div>';
			dcid++;
			$('.expense-cls').append(expenseDiv);
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
			}
			calculatePaRate();
		});
		$('body').on('click','.pick-drop-both-remove-btn',function(){
			var cls = $(this).attr('data-removeCls');
			var rowid = $(this).attr('data-id');
			var result = window.confirm('Are you sure it will remove both pickup and dropoff?');
			if (result == true) {
				$.ajax({
					url: "<?php echo base_url('admin/dispatch-extra/delete/');?>"+rowid,
					type: "post",
					data: "rowid="+rowid,
					success: function(d) {
						//alert(d);
					}
				});
				
				$(cls).html('').remove();
			}
		});
		
		var pid = <?php echo $extraCount;?>;
		$('.pickup-btn-add').click(function(){
			var pfieldset = '<fieldset class="pickup'+pid+'">\
			<legend><input type="text" class="form-control" name="pickup1[]" value="Pick Up '+pid+'"></legend>\
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
						<input name="ptime1[]" id="ptime1-' + pid + '" type="text" class="timeInput form-control" value="" required readonly>\
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
			<input type="text" id="pcity1" data-type="city" class="form-control city1 getAddress companyI" required name="pcity1[]" value="">  \
			</div></div>\
			</div>\
			<div class="col-sm-4">\
			<div class="form-group">\
			<label for="contain">Pick Up Company (Location)</label>\
			<div class="getAddressParent"><input type="text" id="plocation1" class="form-control location1 getAddress companyI" data-type="company" required name="plocation1[]" value=""></div> \
			</div>\
			</div>\
			<div class="col-sm-4">\
			<div class="form-group">\
			<label for="contain">Pick Up Address</label>\
			<input type="hidden" name="paddressid1[]" class="addressidI" value="">\
			<div class="getAddressParent"><input type="text" id="paddress1" class="form-control paddress1 getAddress addressI" data-type="address" name="paddress1[]" value=""></div> \
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
		});
		
		// <div class="col-sm-2">\
		// 	<div class="form-group">\
		// 	<label for="contain">Drop Off Time</label>\
		// 	<input name="dtime1[]" type="text" class="form-control" value="">\
		// 	</div>\
		// 	</div>\

		var did = <?php echo $extraCount;?>;
		$('.drop-btn-add').click(function() {
			var dfieldset = '<fieldset class="ui-state-default dropoff1'+did+'">\
			<legend><input type="text" class="form-control" name="dropoff1[]" value="Drop Off #'+did+'"></legend>\
			<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" data-removeCls=".dropoff1'+did+'" type="button">Remove</button>\
			<div class="row dropoff1'+did+'-pcode-parent">\
			<div class="col-sm-2">\
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
			<div class="getAddressParent"><input type="text" data-type="city" class="form-control city1 getAddress cityI" name="dcity1[]" value=""></div>\
			</div>\
			</div>\
			<div class="col-sm-5">\
			<div class="form-group">\
			<label for="contain">Drop Off Company (Location) &nbsp;</label>\
			<div class="getAddressParent"><input type="text" data-type="company" class="form-control location1 getAddress companyI" name="dlocation1[]" value=""></div> \
			</div>\
			</div>\
			<div class="col-sm-4">\
			<div class="form-group">\
			<label for="contain">Drop Off Address</label>\
			<input type="hidden" name="daddressid1[]" class="addressidI" value=""> \
			<div class="getAddressParent"><input type="text" id="daddress1" class="form-control getAddress addressI" data-type="address" name="daddress1[]" value=""></div> \
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
		
		var invoiceTypeTxt = '<?=$invoiceType?>';
		$('.invoiceTypeCls').change(function(){
		    $('.invoiceNotes').removeAttr('required','');
			let valu = $(this).val();
			if(valu == 'RTS'){
				$('.invoiceTitle').html('RTS'); invoiceTypeTxt = 'RTS';
				$('.invoiceCheckboxCls > div').show();
				$("#shipping_contact").prop("required", false);
			} else if(valu == 'Direct Bill'){
				$('.invoiceTitle').html('DB'); invoiceTypeTxt = 'DB';
				$("#shipping_contact").prop("required", true);
				<?php //if(!isset($_GET['invoice'])){ ?>
				$('.invoiceCheckboxCls > div').hide();
				$('.invoiceCheckboxCls .invoiceqp').show();
				<?php /*} else { ?>
				$('.invoiceCheckboxCls > div').show();
				<?php }*/ ?>
				$('.invoiceNotes').attr('required','');
			} else if(valu == 'Quick Pay'){
				$('.invoiceTitle').html('QP'); invoiceTypeTxt = 'QP';
				$("#shipping_contact").prop("required", true);
				$('.invoiceCheckboxCls > div').show();
				//$('.invoiceCheckboxCls .invoiceqp').show();
				<?php /*//if(!isset($_GET['invoice'])){ ?>
				$('.invoiceCheckboxCls > div').hide();
				$('.invoiceCheckboxCls .invoiceqp').show();
				<?php } else { ?>
				$('.invoiceCheckboxCls > div').show();
				<?php }*/ ?>
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
			let cBtn = '<?php echo base_url('Invoice/downloadInvoicePDF/');?>'+disid+'?invoiceWithPdf=bol-rc';
			$('.combibePdfBtn').attr('href',cBtn);
			
			let dBtn = '<?php echo base_url('Invoice/downloadInvoicePDF/');?>'+disid+'';
			$('.downloadPDF').attr('data-id',disid);
			$('.downloadPDF').attr('href',dBtn);

			let emailBtn = '<?php echo base_url('Invoice/emailInvoice/');?>'+disid+'';
			$('.emailBtn').attr('data-id',disid);
			$('.emailBtn').attr('data-dTable',dTable);
			$('.emailBtn').attr('href',emailBtn);

			$('#combinePdfForm').data('id', disid);
			$('#combinePdfForm').data('type', dTable);
			$.ajax({
				type: "post",
				url: "<?php echo base_url('Invoice/editInvoiceForm?editInvoiceID=');?>"+disid+"&dTable="+dTable,
				data: "editInvoiceID="+disid,
				success: function(responseData) { 
					$('.invoiceAjaxForm').html(responseData);
					bindDynamicRowHandlers();
				}
			});
		});
		
		function bindDynamicRowHandlers() {
			$('#unitRows').on('click', '.addRow', function () {
				const parentRow = $(this).closest('.unitRow');
				const clonedRow = parentRow.clone();
				clonedRow.find('input').val('');
				clonedRow.find('.addRow').remove(); 
				clonedRow.find('.form-group:last-child').html(
					'<button type="button" class="btn btn-danger removeRow" style="float: right; margin-right: 34px; margin-top: 36px;">-</button>'
				);
				$('#unitRows').append(clonedRow);
				reindexRows();
			});

			$('#unitRows').on('click', '.removeRow', function () {
				$(this).closest('.unitRow').remove(); 
				reindexRows();
			});
		}
		function reindexRows() {
			$('#unitRows .unitRow').each(function (index) {
				if (index === 0) {
					$(this).find('input[name="unit"]').attr('name', 'unit');
					$(this).find('input[name="unitprice"]').attr('name', 'unitprice');
					$(this).find('textarea[name="unitDescription"]').attr('name', 'unitDescription');
				} else {
					$(this).find('input[name="unit"]').attr('name', `unitA[]`);
					$(this).find('input[name="unitprice"]').attr('name', `unitPriceA[]`);
					$(this).find('textarea[name="unitDescription"]').attr('name', `unitDescriptionA[]`);
				}
			});
		}

		$('#combinePdfModal').on('show.bs.modal', function () {
			const dispatchId = $('#combinePdfForm').data('id');
			const type = $('#combinePdfForm').data('type');

			const tracking = $('#tracking').val() || 'N/A';
			const invoiceNo = $('#invoice_no').val() || 'N/A';

			const defaultEmailSubject = `Invoice No.: ${invoiceNo} / Customer Ref No.: ${tracking}`;
			document.getElementById('email_subject').value = defaultEmailSubject;
			
			const defaultEmailBody = `<strong>Hey Team,</strong>
			<p>Please find the attached invoice as captioned above.</p>
			<p>Please acknowledge receipt.</p>`;

			setTimeout(function () {
				if (CKEDITOR.instances.email_body) {
					CKEDITOR.instances.email_body.setData(defaultEmailBody);
					CKEDITOR.instances.email_body.resize('100%', '120', true);
				} else {
					CKEDITOR.replace('email_body', {
						height: 120,
						extraPlugins: 'lineheight',
						line_height: "1;1.2;1.5;1.75;2;2.5;3"
						
					});
					CKEDITOR.instances.email_body.on('instanceReady', function () {
						CKEDITOR.instances.email_body.setData(defaultEmailBody);
						CKEDITOR.instances.email_body.resize('100%', '120', true);
					});
				}
			}, 300);
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
						if (type === 'dispatch') {
							return `${baseUrl}assets/outside-dispatch/${fileType}/${fileName}`;
						}
						
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
						fileList += `<h4 class="section-heading">Logistics BOL Files</h4><hr>`;
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
						fileList += `<h4 class="section-heading">Logistics RC Files</h4><hr>`;
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
		// document.getElementById('emailPdfBtn').addEventListener('click', function (e) {
		// 	const form = document.getElementById('combinePdfForm');
		// 	form.action = "<?php echo base_url('Invoice/emailInvoice'); ?>";
		// });
		let emailTimeout = null; 
		let currentForm = null;  
		function setupEmailButton(buttonId, formId, actionUrl) {
			document.getElementById(buttonId).addEventListener('click', function (e) {
				e.preventDefault();
				currentForm = document.getElementById(formId);
				currentForm.action = actionUrl;

				Swal.fire({
					title: 'Are you sure?',
					text: "Do you want to send the email?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes, send it!',
					cancelButtonText: 'No, cancel'
				}).then((result) => {
					if (result.isConfirmed) {
						$('#emailLoaderModal').modal('show');

						emailTimeout = setTimeout(() => {
							$('#emailLoaderModal').modal('hide');
							currentForm.submit();
						}, 30000); 
					}
				});
			});
		}
		document.getElementById('cancelEmailBtn').addEventListener('click', function () {
			clearTimeout(emailTimeout);
			emailTimeout = null;
			currentForm = null;
			$('#emailLoaderModal').modal('hide');
			Swal.fire('Cancelled', 'Email sending was cancelled.', 'info');
		});
		setupEmailButton('emailPdfBtn', 'combinePdfForm', "<?php echo base_url('Invoice/emailInvoice'); ?>");
		
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
			const fileInput = event.target;
			const existingLinks = document.querySelectorAll('.d-bol');
			
			const driverSelect = document.getElementById('driver_select');
			const selectedDriver = driverSelect ? driverSelect.value : null;
			if (selectedDriver === '15') {
				checkbox.removeAttribute('required');
				return;
			}
			if (fileInput.files.length > 0 || existingLinks.length > 0) {
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
	
	//adding required attribute to pick/drop time inputs
	// document.querySelector('#updatePaFleetform').addEventListener('submit', function (e) {
    //     const ptimeInput = document.querySelector('#ptime');
	// 	const ptimeChildInputs = document.querySelectorAll('input[name="ptime1[]"]'); 
	// 	const dtimeInput = document.querySelector('#dtime');
	// 	const dtimeChildInputs = document.querySelectorAll('input[name="dtime1[]"]'); 

    //     if (ptimeInput.value.trim() === '') {
    //         e.preventDefault(); 
    //         alert('Pick Up Time is required!');
	// 		return;
    //     }
	// 	for (let i = 0; i < ptimeChildInputs.length; i++) {
	// 		if (ptimeChildInputs[i].value.trim() === '') {
	// 			e.preventDefault(); 
	// 			alert('Child Pick Up Time ' + (i + 1) + ' is required!');
	// 			return;
	// 		}
	// 	}
	// 	if(dtimeInput.value.trim() === ''){
	// 		e.preventDefault(); 
    //         alert('Drop Off Time is required!');
	// 		return;
	// 	}

	// 	for (let i = 0; i < dtimeChildInputs.length; i++) {
	// 		if (dtimeChildInputs[i].value.trim() === '') {
	// 			e.preventDefault(); 
	// 			alert('Child Drop Off Time ' + (i + 1) + ' is required!');
	// 			return;
	// 		}
	// 	}
    // });

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
			let invoiceDate = '';
			let invDate = '';
			
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
				$('.invoiceCheckCls[name="invoicePaid"]').prop('checked', true);
				$('.invoiceCheckCls[name="invoiced"]').prop('checked', true);
				$('.invoiceCheckCls[name="invoiceReady"]').prop('checked', true);
				invoiceDate = 'yes';
				$('input.invoiceCloseDate, input.invoicePaidDate, input.invoiceReadyDate').attr('required','');
				invDate = $('input.invoiceCloseDate').val();
				remainingPart = '';
			} else if ($('.invoiceCheckCls[name="invoicePaid"]').is(':checked')) {
				statusText = invoiceTypeTxt+' Paid ' + currentDate;
				$('.invoiceCheckCls[name="invoiced"]').prop('checked', true);
				$('.invoiceCheckCls[name="invoiceReady"]').prop('checked', true);
				invoiceDate = 'yes';
				$('input.invoicePaidDate, input.invoiceReadyDate').attr('required','');
				$('input.invoiceCloseDate').removeAttr('required');
				invDate = $('input.invoicePaidDate').val();
			} else if ($('.invoiceCheckCls[name="invoiced"]').is(':checked')) {
				statusText = invoiceTypeTxt+' Invoiced ' + currentDate;
				$('.invoiceCheckCls[name="invoiceReady"]').prop('checked', true);
				invoiceDate = 'yes';
				$('input.invoiceCloseDate, input.invoicePaidDate').removeAttr('required');
				$('input.invoiceReadyDate').attr('required','');
				invDate = $('input.invoiceDate').val();
			} else if ($('.invoiceCheckCls[name="invoiceReady"]').is(':checked')) {
				statusText = invoiceTypeTxt +' Ready to submit '+currentDate;
				invoiceDate = 'no';
				$('input.invoiceReadyDate').attr('required','');
				invDate = $('input.invoiceReadyDate').val();
			} else {
			    $('input.invoiceReadyDate').removeAttr('required');
			}
			
			if (invoiceTypeTxt=='RTS') { $('.paid-close-date').show(); }
			
			if(invoiceDate == 'yes'){
				$('input.invoiceDate').attr('required',''); 
				if($('input.invoiceDate').val() == 'TBD') { $('input.invoiceDate').val(''); }
			} else {
			    if($('#customControlInlinegd').prop('checked') && $('#customControlInlinerc').prop('checked') && $('#customControlInline').prop('checked')) { }
				else { $('.invoiceTypeCls').removeAttr('required'); }
			}
			
			if(statusText != '' && invoiceTypeTxt != ''){
			    if(invDate != '') {
			        let [year, month, day] = invDate.split('-');
                    month = month.padStart(2, '0');
                    day = day.padStart(2, '0');
                    let cDate = `${month}/${day}/${year}`;
                    let statusTextVal = statusText;
					statusText = statusTextVal.replace(currentDate, cDate);
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
		// 	let currentNotes = $('.statusCls').val();
		// 	let allInvoices = [];
		// 	$('input[name="childInvoice[]"], input[name="otherChildInvoice[]"]').each(function () {
		// 		if ($(this).val().trim() !== '') {
		// 			allInvoices.push($(this).val().trim());
		// 		}
		// 	});
		// 	let linkedInvoicesText = allInvoices.length > 0 ? allInvoices.map(invoice => `Linked to ${invoice}`).join(' - ') : '';
		// 	if (currentNotes.includes('Linked to ')) {
		// 		if (linkedInvoicesText) {
		// 			currentNotes = currentNotes.replace(/- Linked to .+/, '- ' + linkedInvoicesText);
		// 		} else {
		// 			currentNotes = currentNotes.replace(/- Linked to .+/, '').trim();
		// 		}
		// 	} else if (linkedInvoicesText !== '') {
		// 		currentNotes = currentNotes.includes('-')
		// 			? `${currentNotes} - ${linkedInvoicesText}`
		// 			: `${currentNotes} - ${linkedInvoicesText}`;
		// 	}

		// 	$('.statusCls').val(currentNotes.trim());
		// });

		
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
			$('.parate').val(finalAmt.toFixed(2));
		}
		
	});
	$(document).ready(function () {
		function validateBOL() {
			const isBolChecked = $('#customControlInline').is(':checked');
			const bolFileInput = $('input[name="bol_d[]"]');
			const bolUploadedDocs = $('.d-bol').length;

			const driverSelect = document.getElementById('driver_select'); 
			const selectedDriver = driverSelect ? driverSelect.value : null;

			if (selectedDriver === '15') {
				$('#customControlInline').removeAttr('required');
				$('input[name="bol_d[]"]').prop('required', false);
				return;
			}


			if (isBolChecked && bolUploadedDocs === 0 && bolFileInput.get(0).files.length === 0) {
				bolFileInput.prop('required', true);
			} else {
				bolFileInput.prop('required', false);
			}
		}
		$('#customControlInline').on('change', validateBOL);
		$('input[name="bol_d[]"]').on('change', validateBOL);

		function validateRC() {
			const isRcChecked = $('#customControlInlinerc').is(':checked');
			const rcFileInput = $('input[name="rc_d[]"]');
			const rcUploadedDocs = $('.d-rc').length
		}
		$('#customControlInlinerc').on('change', validateRC);
		$('input[name="rc_d[]"]').on('change', validateRC);
		$('#driver_select').on('change', validateBOL);
		validateBOL();
		validateRC();
	});

	document.addEventListener('DOMContentLoaded', function () {
		<?php if (!empty($selectedDriverIsInactive) && $selectedDriverIsInactive): ?>
			document.getElementById('driver_select').setAttribute('disabled', 'disabled');
		<?php endif; ?>
	});

	$(document).ready(function () {
		var invoiceTypeTxt = '<?= $invoiceType ?>';
		if (invoiceTypeTxt === "RTS") {
			$("#shipping_contact").prop("required", false);
		} else {
			$("#shipping_contact").prop("required", true);
		}
		function loadShippingContacts(companyId, selectedContact = "") {
			if (companyId) {
				$.ajax({
					url: "<?= base_url('Comancontroler/getShippingContacts'); ?>",
					type: "POST",
					data: { company_id: companyId },
					dataType: "json",
					success: function (response) {
						let $dropdown = $("#shipping_contact");
						$dropdown.empty();
						if (response.length === 1) {
							let contact = response[0];
							let designation = contact.designation ? ` (${contact.designation})` : "";
							$dropdown.append(
								$("<option>", {
									value: contact.id,
									text: contact.contact_person + designation,
									selected: true
								})
							);
						} else {
							$dropdown.append('<option value="">-- Select Shipping Contact --</option>');
							$.each(response, function (i, contact) {
								let designation = contact.designation ? ` (${contact.designation})` : "";
								$dropdown.append(
									$("<option>", {
										value: contact.id,
										text: contact.contact_person + designation,
										selected: (selectedContact == contact.id) 
									})
								);
							});
						}
					}
				});
			}
		}
		let companyId = $("#company_id").val();
		let selectedContact = "<?= $disp['shipping_contact'] ?? '' ?>"; 
		if (companyId) {
			loadShippingContacts(companyId, selectedContact);
		}
		$(document).on("change", "#company_id", function () {
			loadShippingContacts($(this).val());
		});
	});

	$(document).ready(function () {
		if (window.location.hash === "#reminders") {
			$('#reminders-tab').tab('show');
			history.replaceState(null, null, window.location.pathname);
		}
	});
	$(function () {
		var syncing = false;
		$(document).on('shown.bs.tab', 'a[data-toggle="tab"], button[data-toggle="tab"], a[data-bs-toggle="tab"], button[data-bs-toggle="tab"]', function (e) {
			if (syncing) return;        
			syncing = true;

			var target = $(e.target).attr('data-target') || $(e.target).attr('href') || $(e.target).data('bsTarget') || $(e.target).data('target');

			$('a[data-toggle="tab"], button[data-toggle="tab"], a[data-bs-toggle="tab"], button[data-bs-toggle="tab"]').each(function () {
			var t = $(this).attr('data-target') || $(this).attr('href') || $(this).data('bsTarget') || $(this).data('target');
			if (t == target && this !== e.target) {
				$(this).tab('show');
			}
			});
	 		syncing = false;
	 	});
	});

</script>

<style>
    .timeInput {padding:13px 15px;}
    .input-group{position:relative;}
  .tDropdown {position:absolute;width:calc(100% - 30px);max-height:240px;overflow-y:auto;background:white;border:1px solid #ccc;display:none;z-index:1000;top:98%;left:0;}
  .tDropdown div {padding: 5px;cursor: pointer;}
  .tDropdown div:hover {background: #f0f0f0;}
  
.modal{z-index:999;}
.msg-div.error {background:red;}
.msg-div {position: fixed;width: 380px;font-size: 19px;max-width: 98%;background: #28a745;top:20px;right: 0;left:0px;margin:auto;z-index: 9999;padding: 10px;border: 2px solid #28a745; color: #fff;    box-shadow: 0px 0px 7px 10px #aaa;
    display: flex; justify-content: center;align-content: center; flex-wrap: wrap;}
    .out-invoice .btn-danger{background-color:#000;}
	<?php /*if(isset($_GET['invoice'])){ ?>
		.invoiceEdit::before {  content: "";  position: absolute;  width: 100%;  height: 100%;  z-index: 99;}
		.invoiceEdit {  position: relative;}
		.drop-btn-add, .pickup-btn-add, .otherChildInvoice-btn, .childInvoice-btn{display:none;}
	<?php }*/ ?>
	 
	 <?php if($disp['lockDispatch']=='1') { ?>
		.lockDispatchCls::before {  content: "";  position: absolute;  width: 100%;  height: 100%;  z-index: 99;}
		.lockDispatchCls {  position: relative;}
		.drop-btn-add, .pickup-btn-add{display:none;}
	<?php } ?>
	.d-pdf{display:none;}
	.download-pdf {color: #fff;border: 1px solid green;border-radius: 7px;padding: 1px 7px;background: #28a745;font-size: 13px;}
	.download-pdf:hover{color:#fff;background:green;text-decoration:none;}
	#dataTable11.table td{font-size:15px;}
	#reminder_datatable.table td{font-size:15px;}
	.fileDownload img{width: 20px;position: absolute;top:0;left: 0;z-index: 99;}
	#ui-datepicker-div {z-index: 99999 !important;}
	.invoiceCheckboxCls div{display:inline;}
	.custom-control-label::before {width: 20px;height: 20px;}
	.custom-control-label::after {width: 20px;height: 20px;}
	.doc-file {display: inline-block;text-align: center;}
	.doc-file span {display: block;font-size:12px;}
	.doc-file {display: inline-block;text-align: center;max-width: 145px;position: relative;}
	.doc-file .remove-file {position: absolute;right: 0;top:0px;padding: 3px;color: red;font-weight: bold;border: 1px solid red;line-height: 13px;}
	fieldset{position:relative;padding:15px;background: #f6f6f6; border: 1px solid #c5c5c5;}
	fieldset .pick-drop-btn{position:absolute;right:15px;top:-34px;}
	.card .container-fluid, .card .mobile_content, .card .container{padding-left:0;padding-right:0;}
	  .card, .pt-content-body > .container-fluid {    padding: 10px;  }
	
	.getAddressParent, .getCompanyParent{position:relative;}
	.addressList, .companyList{position:absolute;top:99%;left:0px;background: #eee;z-index: 999;width: 100%;border: 1px solid #aaa;}
	.addressList li, .companyList li {list-style: none;line-height: 23px;padding: 4px;cursor: pointer;}
	.addressList li:hover, .companyList li:hover {background:#fff;}
	.tab-pane.active {  padding: 15px;  border: 1px solid #ddd;margin-top: -1px;}
	#myTab .btn-success.active{color: #495057;  background-color: #fff;border-color: #dee2e6 #dee2e6 #fff;box-shadow: 0 0 0 0rem rgba(40,167,69,.5);}
	#myTab .btn-info.active{
		color: #495057;  
		background-color: #fff;
		border-color: #dee2e6 #dee2e6 #fff;
		box-shadow: 0 0 0 0rem rgba(40,167,69,.5);
	}
	#myTabBottom .btn-success.active{
		color: #495057;  
		background-color: #fff;
		border-color: #fff #dee2e6 #dee2e6;
		box-shadow: 0 0 0 0rem rgba(40,167,69,.5);
	}
	#myTabBottom .btn-info.active{
		color: #495057;  
		background-color: #fff;
		border-color: #fff #dee2e6 #dee2e6;
		box-shadow: 0 0 0 0rem rgba(40,167,69,.5);
	}
	

hr {
    border: 1px solid #ddd;
    margin: 10px 0;
}

.file-entry {
    margin-bottom: 10px;
}

.file-entry label {
    display: flex;
    align-items: center;
    gap: 10px;
}

.file-entry input[type="checkbox"] {
    transform: scale(1.2); 
    margin-right: 10px;
}

.file-list {
    padding: 10px;
}
.time-option.focused {
		background-color: #007bff;
		color: white;
	}
	#email_subject{
		width: 100% !important; 
		height: 60px !important;
	}
</style>

