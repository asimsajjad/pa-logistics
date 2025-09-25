<style>
    .card {
        padding: 0;
    }

    .form-control {
        border-radius: 6px;
        border: 1px solid #A6A6A6;
    }

    .container-fluid {
        padding: 20px !important;
    }

    .card-header-floating {
        position: absolute;
        top: -14px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        padding: 0 20px;
        z-index: 1;
    }

    .rounded {
        border-radius: 16px;
    }

    .card-title-floating {
        background: white;
        padding: 0 10px;
        font-weight: bold;
    }

    .card-button-floating {
        padding: 0 10px;
        font-weight: bold;
    }

    .floating-wrapper {
        position: relative;
        margin-bottom: 2rem;
    }
</style>

<div class="card">
    <div class="bg-light rounded p-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fas fa-th-list mr-2"></i> Update PA warehousing
        </h6>
        <div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/paWarehouse');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
    </div>
		<div class="container-fluid">
			<div class="col-sm-12 mobile_content">
				<div class="container">
					<?php
						$disp = $dispatch[0]; 
						// echo $disp['status']; exit;
						// print_r($dispatchInfo);exit;
						$dispatchMeta = json_decode($disp['dispatchMeta'],true);			
						$expenseN = array();
						foreach($expenses as $exp) {
							if($exp['type']=='Negative'){ $expenseN[] = $exp['id']; } 
						}
						$carrierExpenseN = array();
						foreach($carrierExpenses as $exp) {
							if($exp['type']=='Negative'){ $carrierExpenseN[] = $exp['id']; } 
						}
						$js_companies = $company = $dcity = $pcity = $dcity1 = $pcity1 = $js_location = $js_cities = $plocation = $dlocation = '';
						$cityArr = $locationArr = $companyArr = $vehicleArr = $driverArr = $comAddArr = $truckComArr = $bookUnderArr =  $typeOfServiceArr = array();				
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
						</div>
						<div class="container-fluid">
							<!-- shipment-->
							<div class="floating-wrapper ">
								<div class="card-header-floating">
									<div class="card-title-floating">Shipment</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">

										<div class="row">
											<div class="col-sm-3 ">
												<div class="form-group">
													<label>Service Provider</label>
													<select class="form-control select2" name="truckingCompany" required >
														<option value="">Select Service Provider</option>
														<?php 
															if(!empty($truckingCompanies)){
																foreach($truckingCompanies as $val){
																	$truckComArr[$val['id']] = $val['company'];
																	echo '<option value="'.$val['id'].'"';
																	if($disp['truckingCompany']==$val['id']) { echo ' selected="selected" '; }
																	echo '>'.$val['company'].'</option>';
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
											<div class="col-sm-3 ">
												<div class="form-group">
													<label>User</label>
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
																echo '>'.$val['company'].'</option>';
															}
														}
													?>
												</select>
											</div>
										</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label>Booked Under New</label>
													<select class="form-control" name="bookedUnderNew" id="bookedUnderNew" required>
														<option value="0">Select Booked Under</option>
														<?php 
															if(!empty($booked_under)){
																foreach($booked_under as $val){
																	$bookUnderArr[$val['id']] = $val['company'];
																	echo '<option value="'.$val['id'].'" data-company="'.htmlspecialchars($val['company']).'"';
																	if($disp['bookedUnderNew']==$val['id']) { echo ' selected="selected" '; }
																	echo '>'.$val['company'].'</option>';
																}
															}
														?>
													</select>
												</div>
											</div>
											<div class="col-sm-3 ">
												<div class="form-group">
													<label>Shipment Type</label>
													<select class="form-control invoicePDF" required name="invoicePDF" id="shipmentType">
														<option value="">Select Shipment Type</option>
														<option value="Warehousing" <?php if($disp['invoicePDF']=='Warehousing') { echo 'selected'; }?>>Warehousing</option> 
													</select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-3 warehouseService">
												<div class="form-group">
													<label>Type Of Services</label>
													<select class="form-control" name="warehouseServices" id="warehouseServices">
														<option value="">Select Service</option>
														<?php 
															if(!empty($warehouseServices)){
																foreach($warehouseServices as $val){
																	$typeOfServiceArr[$val['id']] = $val['title'];
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
										</div>
									</div>
								</div>
							</div>
							<!-- Pick  up  -->
							<fieldset>
								<div class="floating-wrapper">
									<div class="card-header-floating">
										<legend>
											<input type="text" class="form-control" style="height: 35px;" name="pickup" value="<?php if($this->input->post('pickup')!='') { echo $this->input->post('pickup'); } else { echo 'Warehouse Location'; } ?>">
										</legend>
										<div class="card-button-floating">
											<!-- <button class="btn btn-primary btn-sm pick-drop-btn pickup-btn-add" type="button">Add New +</button> -->
										</div>
									</div>
									<div class="card border shadow no-overflow">
										<div class="card-body">
											<div class="row pickup-pcode-parent">
												<div class="col-sm-4 ">
													<div class="form-group">
														<label>Start Date</label>
														<select name="trip">
															<option value="0">Select Trip</option>
															<?php for($i=1;$i<16;$i++){ 
																echo '<option value="'.$i.'"';
																if($disp['trip']==$i) { echo ' selected="selected" '; }
																echo '>'.$i.'</option>';
															} ?>
														</select>
														<input name="pudate" type="date" class="form-control datepicker" required value="<?php echo $disp['pudate'];  ?>">
													</div>
												</div>
												<div class="col-sm-4 ">
													<div class="form-group">
														<label>End Date</label>
														<input name="edate" type="date" class="form-control datepicker" required value="<?php echo $disp['edate'];  ?>">
													</div>
												</div>
												<!-- <div class="col-sm-4">
													<div class="form-group">
														<label for="contain">Appointment Type</label>
															<select class="form-control appointmentTypeP" name="appointmentTypeP" id="appointmentTypeP" required>
															<option value="">Select Appointment Type</option>
															<option value="Appointment" <?php if($disp['appointmentTypeP']=='Appointment') { echo 'selected'; }?>>By Appointment</option>
															<option value="FCFS" <?php if($disp['appointmentTypeP']=='FCFS') { echo 'selected'; }?>>First Come First Serve (FCFS)</option>
														</select>
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group">
														<label for="contain">Pick Up Time</label>	
														<div class="input-group mb-2">
															<input name="ptime" id="ptime" type="text" class="timeInput form-control" value="<?php echo $disp['ptime'];  ?>" readonly>
															<div class="tDropdown"></div>
															<div class="input-group-append">
																<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
															</div>
														</div>
													</div>
												</div> -->
												<div class="col-sm-4"></div>
												<div class="col-sm-4">
													<div class="form-group">
														<label for="contain">Company (Location)</label>
														<div class="getAddressParent"><input type="text" id="plocation" class="form-control getAddress companyI" data-type="company" name="plocation" required value="<?php echo $plocation;?>"> </div>
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group">
														<label for="contain">City</label>
														<div class="getAddressParent"><input type="text" id="pcity" class="form-control getAddress cityI" data-type="city" name="pcity" required value="<?php echo $pcity;?>"> </div>
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group">
														<label for="contain">Address</label>
														<div class="getAddressParent">
															<input type="hidden" name="paddressid" class="addressidI" value="<?php echo $disp['paddressid'];?>">
															<input type="text" id="paddress" class="form-control getAddress addressI" data-type="address" name="paddress" value="<?php echo $paddress;?>"> 
														</div>
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
												<div class="col-sm-4">
													<div class="form-group">
														<label for="contain">Commodity</label>
														<input required type="text" class="form-control" name="commodityP" value="<?php echo $disp['commodityP'];?>">
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group">
														<label for="contain">Description</label>
														<textarea class="form-control" style="height: 49px; border-radius: 6px;"  name="metaDescriptionP"><?php echo $disp['metaDescriptionP'];?></textarea>
													</div>
												</div>
												<div class="col-sm-12 pnotes">
													<div class="form-group">
														<label for="contain">Notes</label> 
														<textarea name="pnotes" style="height: 49px; border-radius: 6px;" class="form-control"><?php echo $disp['pnotes'] ?></textarea>
													</div>
												</div>								
													
											<?php
												if($disp['pcode']=='') { $pcode = array(' '); }
												else { $pcode = explode('~-~',$disp['pcode']); }

												for($i=0;$i<count($pcode);$i++){
													if($i > 0) {
														$class = ' pcode-id-'.$i;
														$dContent = '<div class=""><button class="btn btn-danger code-delete" type="button" data-cls=".pcode-id-'.$i.'" style="height: 100%;"><i class="fa fa-trash "></i></button></div>';
														} else {
														$class = '';
														$dContent = '<div class=""><button type="button" class="btn btn-success pcode-add" style="height: 100%;"><i class="fas fa-plus"></i></button></div>';
													}
												?>
												<div class="col-sm-2 <?php echo $class;?>">
													<div class="form-group">
														<label for="contain">#</label>
														<div class="input-group mb-2">
															<input name="pcode[]" type="text" class="form-control" value="<?php echo $pcode[$i]; ?>">
															<div class="input-group-append">
																<?php echo $dContent; ?>
															</div>
														</div>  
													</div>
												</div>
											<?php } ?>
											<div class="pickup-no"></div>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
							<?php
								$extraCount = 2;
								$pextraCount = 2;
								$PDCode = 1;
								
							?>
						
							<div  id="sortable" class="sortable dropoffExtra lockDispatchCls">
								<?php
								$extraCount = 2; 
								?> 		
							</div>           
							<!-- Dispatch Info  -->
							<div class="floating-wrapper ">
								<div class="card-header-floating">
									<div class="card-title-floating">Dispatch Info</div>
									<div class="card-button-floating">
										<button class="btn btn-primary btn-sm dispatchInfo-btn" type="button">Add Dispatch Info+</button>
									</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">
										<div class="row dispatchInfo-cls sortable">
											<?php
												$e = 1;
												if($dispatchInfoDetails) { 
													foreach($dispatchInfoDetails as $diVal) {
														// print_r($diVal['dispatchInfoId']);exit;
														$e++;
														?>
														<div class="ui-state-default col-sm-3 dispatchInfo-div-<?=$e?>">
															<div class="form-group d-flex m-0">
																<select name="dispatchInfoName[]" class="form-control">
																<?php 
																	echo '<option value="">'.$diVal[0].'</option>';
																	foreach($dispatchInfo as $di) {
																		echo '<option value="'.$di['id'].'"';
																		if($di['id'] == $diVal['dispatchInfoId']){ echo ' selected '; }
																		echo '>'.$di['title'].'</option>';
																	}
																?>
																</select>
																<div class="input-group-append ml-2">
																	<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".dispatchInfo-div-<?=$e?>">
																		<i class="fas fa-trash-alt"></i>
																	</button>
																</div>
															</div>
															<div class="input-group">
																<input name="dispatchInfoValue[]" type="text" class="form-control" value="<?=$diVal['dispatchValue']?>" required>
															</div>
														</div>
														<?php 
													}
												}
											?>
										</div>
									</div>
								</div>
							</div>
							<!-- Invoice details -->
							<div class="floating-wrapper">
								<div class="card-header-floating">
									<div class="card-title-floating">Service Provider Expenses</div>
									<div class="card-button-floating">
										<button class="btn btn-primary btn-sm carrier-expense-btn" type="button">Add Expenses +</button>
									</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">
										<div class="row">
											<div class="col-sm-3 ">
												<div class="form-group">
													<label>Service Provider Rate</label>
													<div class="input-group mb-2">
														<div class="input-group-prepend">
															<div class="input-group-text">$</div>
														</div>
														<input required name="rate" readonly type="number" min="0" step="0.01" class="form-control rateInput rate-cls" value="<?php echo $disp['rate']; ?>">
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
														<input  name="agentPercentRate" type="number" min="0" step="0.01" id="agentPercentRate" class="form-control agentPercentRate rate-cls" 	value="<?php echo $disp['agentPercentRate']; ?>"  style="">
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
										</div>
										<div class="row carrier-expense-cls">
											<?php
												$e = 1;
												$rate = $disp['rate'];
												if($dispatchSPExpenseDetails) { 
													foreach($dispatchSPExpenseDetails as $expVal) {
															$e++;
															//if($expVal[0] == 'Discount') { $paRate = $paRate + $expVal[1]; }
															if(in_array($expVal['expenseInfoId'],$carrierExpenseN)){ $rate = $rate + $expVal['expenseInfoValue']; }
															else { $rate = $rate - $expVal['expenseInfoValue']; }
														?>
														<div class="col-sm-3 mb-1 carrier-expense-div-<?= $e ?>">
															<div class="form-group d-flex m-0">
																<select <?php if($disp['carrierPayoutCheck']=='1') { echo ' readonly'; }?> name="carrierExpenseName[]" class="form-control carrierExpenseNameSelect carrierExpenseName-<?=$e?>" >
																<?php 
																	foreach($carrierExpenses as $exp) {
																		echo '<option value="'.$exp['id'].'"';
																		if($exp['id'] == $expVal['expenseInfoId']){ echo ' selected '; }
																		echo '>'.$exp['title'].'</option>';
																	}
																?>
																</select>
															<div class="input-group-append ml-2">
																<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".carrier-expense-div-<?= $e ?>">
																	<i class="fas fa-trash-alt"></i>
																</button>
															</div>
														</div>
														<div class="input-group">
															<input <?php if($disp['carrierPayoutCheck']=='1') { echo ' readonly'; }?> name="carrierExpensePrice[]" data-cls=".carrierExpenseName-<?=$e?>" required type="number" min="1" step="0.01" class="form-control carrierExpenseAmt" value="<?=$expVal['expenseInfoValue']?>">
														</div>
													</div>
													<?php
													}
												}
											?>
										</div>
									</div>
								</div>
							</div>
							<!-- Expenses  -->
							<div class="floating-wrapper">
								<div class="card-header-floating">
									<div class="card-title-floating">Customer Expenses</div>
									<div class="card-button-floating">
										<button class="btn btn-primary btn-sm expense-btn" type="button">Add Expenses +</button>
										<button class="btn btn-secondary btn-sm expense-custom-btn" type="button">Add Custom Expense +</button>
									</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">
										<div  class="row">
											<div class="col-sm-3 ">
												<div class="form-group">
													<label>Invoice Amount</label>
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
											<div class="col-sm-3 ">
												<div class="form-group">
													<label>Margin</label>
													<div class="input-group mb-2">
														<div class="input-group-prepend">
															<div class="input-group-text">$</div>
														</div>
														<input name="pamargin" readonly type="number" step="0.01" class="form-control pamargin" value="<?php echo $disp['pamargin']; ?>">
													</div>
												</div>
											</div>
										</div>
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
														<div class="col-sm-3 mb-1 expense-div-<?= $e ?>">
															<div class="form-group d-flex m-0">
																<select <?php if($disp['carrierPayoutCheck']=='1') { echo ' readonly'; }?> name="expenseName[]" class="form-control expenseNameSelect expenseName-<?=$e?>" >
																<?php 
																	foreach($expenses as $exp) {
																		echo '<option value="'.$exp['id'].'"';
																		if($exp['id'] == $expVal['expenseInfoId']){ echo ' selected '; }
																		echo '>'.$exp['title'].'</option>';
																	}
																?>
																</select>
															<div class="input-group-append ml-2">
																<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".expense-div-<?= $e ?>">
																	<i class="fas fa-trash-alt"></i>
																</button>
															</div>
														</div>
														<div class="input-group">
															<input <?php if($disp['carrierPayoutCheck']=='1') { echo ' readonly'; }?> name="expensePrice[]" data-cls=".expenseName-<?=$e?>" required type="number" min="1" step="0.01" class="form-control expenseAmt" value="<?=$expVal['expenseInfoValue']?>">
														</div>
													</div>
													<?php
													}
												}
											?>
											<?php
												if (!empty($dispatchCustomExpenses)) {
													foreach ($dispatchCustomExpenses as $custExp) {
														$paRate = $paRate - $custExp['value'];
													}
												}

												$c = 1;
												if (!empty($dispatchCustomExpenses)) {
													foreach ($dispatchCustomExpenses as $custExp) {
														$c++;
												?>
													<div class="col-sm-3 mb-1 custom-expense-div-<?= $c ?>">
														<div class="form-group d-flex m-0">
															<input type="text" name="customExpenseName[]" class="form-control customExpenseName-<?=$c?>" placeholder="Custom Title" value="<?= htmlspecialchars($custExp['title']) ?>" required>
															<div class="input-group-append ml-2">
																<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".custom-expense-div-<?= $c ?>">
																	<i class="fas fa-trash-alt"></i>
																</button>
															</div>
														</div>
														<div class="input-group">
															<input type="number" step="0.01" name="customExpensePrice[]" data-cls=".expenseName-<?=$c?>" class="form-control expenseAmt" value="<?= $custExp['value'] ?>" required>
														</div>
													</div>
												<?php
													}
												}
											?>
										</div>
										
									</div>
								</div>
							</div>

							<div class="floating-wrapper">
								<div class="card-header-floating">
									<div class="card-title-floating">Sub Invoices</div>
									<div class="card-button-floating">
										<button class="btn btn-success btn-sm pick-drop-btn childInvoice-btn" type="button">Warhousing +</button>
										<button style="right:120px" class="btn btn-success btn-sm pick-drop-btn fleetChildInvoice-btn" type="button">Fleet +</button>
										<button style="right:120px" class="btn btn-success btn-sm pick-drop-btn logisticsChildInvoice-btn" type="button">Logistics +</button>
									</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">
										<div class="row childInvoice-cls">
											
										</div>	
										<div class="row">
											<?php if (!empty($children)): ?>
												<?php $e = 1; ?>
												<?php foreach ($children as $child): ?>
													<?php 
														$e++;
														$invoiceId = $child['id'];
														$invoiceNo = $child['invoice'];
														$cPaRate   = $child['parate'];
														$cRate     = $child['rate'];

														if ($child['child_type'] == 'warehousing') {
															$updateUrl = base_url().'admin/paWarehouse/update/'.$invoiceId;
															$Carrier_rate = 'SP Rate:';
															$childInvoice = 'warehouseChildInvoice[]';
														} elseif ($child['child_type'] == 'fleet') {
															$updateUrl = base_url().'admin/dispatch/update/'.$invoiceId;
															$Carrier_rate = 'Carrier Rate:';
															$childInvoice = 'fleetChildInvoice[]';
														} else {
															$updateUrl = base_url().'admin/outside-dispatch/update/'.$invoiceId;
															$Carrier_rate = 'Carrier Rate:';
															$childInvoice = 'logisticsChildInvoice[]';
														}
													?>
													<div class="col-sm-3 childInvoice-div-<?=$e?>">
														<div class="form-group">
															<button class="btn btn-danger btn-sm pick-drop-btn pick-drop-remove-btn" 
																	data-removecls=".childInvoice-div-<?=$e?>" 
																	type="button">X</button> 

															<button class="btn btn-primary btn-sm" 
																	onclick="window.open('<?=$updateUrl?>', '_blank');" 
																	type="button">Go To</button>

															<input name=<?=$childInvoice?> required type="text" 
																class="form-control" 
																value="<?=$invoiceNo?>" 
																placeholder="Invoice">

															<input name="childInvoiceRate[]" readonly type="text" 
																class="form-control" 
																value="Inv. Amt: $<?=$cPaRate?>" 
																placeholder="Invoice Amount">

															<input name="" readonly type="text" 
																class="form-control" 
																value="<?= $Carrier_rate ?> $<?=$cRate?>" 
																placeholder="Carrier Amount">
														</div>
													</div>
												<?php endforeach; ?>
											<?php endif; ?>
										</div>									
									</div>
								</div>
							</div>

							<!-- Company Tracking -->
							<div class="floating-wrapper mt-4">
								<div class="card-header-floating">
									<div class="card-title-floating">Company Tracking</div>
								</div>
								<div class="card border shadow no-overflow">
									<div class="card-body">
										<div class="row">
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Company</label>
													<div class="getCompanyParent">
														<input type="text" id="companies" class="form-control getCompany" name="company" required value="<?php echo $company;?>">
													</div>
												</div>
											</div>
											<input type="hidden" id="company_id" name="company_id" value="<?php echo $disp['company']; ?>">
											<div class="col-sm-4">
												<div class="form-group">
													<label for="shipping_contact">Shipping Contact</label>
													<select id="shipping_contact" name="shipping_contact" class="form-control" required>
														<option value="">-- Select Shipping Contact --</option>
													</select>
												</div>
											</div>
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Tracking Number</label>
													<input required id="tracking" name="tracking" type="text" class="form-control" value="<?php echo $disp['tracking']; ?>">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- Financial-->
							<div class="floating-wrapper">
								<div class="card-header-floating">
									<div class="card-title-floating">Financials</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">
										<div class="row">
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Invoice Number</label>
													<input <?php if(strtotime($disp['pudate']) > strtotime('2024-04-24')) { echo 'readonly'; } ?> id="invoice_no" name="invoice" type="text" class="form-control" value="<?php echo $disp['invoice']; ?>">
												</div>
											</div>
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Week</label>
													<input name="dWeek" type="text" readonly class="form-control" value="<?php echo $disp['dWeek']; ?>">
												</div>
											</div>
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Payout Amount</label>
													<input name="payoutAmount" type="text" class="form-control" value="<?php echo $disp['payoutAmount']; ?>">
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<label for="contain">Partial Amount</label>
													<input name="partialAmount" type="number" min="0" step="0.01" class="form-control" value="<?php echo ($disp['partialAmount']) ? $disp['partialAmount']: 0; ?>">
												</div>
											</div>
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Payable Amount</label>
													<input name="payableAmt" readonly type="number" min="0" step="0.01" class="form-control" value="<?php echo ($disp['payableAmt']) ? $disp['payableAmt']: 0; ?>">
												</div>
											</div>
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Expected Pay Date</label>
													<input readonly name="expectPayDate" type="text" class="form-control datepicker" value="<?php if($disp['expectPayDate']=='0000-00-00') { echo 'TBD'; } else { echo $disp['expectPayDate']; } ?>">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- Customer Details-->
							<div class="floating-wrapper">
								<div class="card-header-floating">
									<div class="card-title-floating">Customer Details</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">
										<div class="row">
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Invoice Type</label>
													<select class="form-control invoiceTypeCls" name="invoiceType">
														<option value="">Select Invoice Type</option>
														<option value="RTS" <?php if($disp['invoiceType']=='RTS') { echo 'selected'; }?>>RTS</option>
														<option value="Direct Bill" <?php if($disp['invoiceType']=='Direct Bill') { echo 'selected'; }?>>Direct Bill</option>
														<option value="Quick Pay" <?php if($disp['invoiceType']=='Quick Pay') { echo 'selected'; }?>>Quick Pay</option>
													</select>
												</div>
											</div>
										</div>
										<?php 
											if($disp['invoiceType']=='RTS') { $invoiceType = 'RTS'; }
											elseif($disp['invoiceType']=='Direct Bill') { $invoiceType = 'DB'; }
											elseif($disp['invoiceType']=='Quick Pay') { $invoiceType = 'QP'; }
											else { $invoiceType = ''; }
											?>
										<div class="row">
											<div class="col-sm-3 invoiceCheckboxCls" >
												<div class="form-check mb-2">
													<input type="hidden" name="invoiceReady" value="<?=$disp['invoiceReady']?>" id="invoiceReadyhidden">
													<input type="checkbox" class="form-check-input invoiceCheckCls" onclick="updateValueOnRuntime('invoiceTypeInvoiceReady','invoiceReadyhidden')" id="invoiceTypeInvoiceReady" name="invoiceReady" <?php if($disp['invoiceReady']=='1') { echo 'checked'; } ?> value="<?=$disp['invoiceReady']?>">
													<label class="form-check-label" for="invoiceTypeInvoiceReady"><span class="invoiceTitle"><?=$invoiceType?></span> Ready To Submit</label>
												</div>
												<div class="form-group">
													<label>Ready Submit Date</label>
													<input name="invoiceReadyDate" type="text" class="form-control datepicker invoiceReadyDate" value="<?php echo $disp['invoiceReadyDate']; ?>">
												</div>
											</div>
											<div class="col-sm-3 invoiceCheckboxCls">
											<div class="form-check mb-2">
													<input type="hidden" name="invoiced" value="<?=$disp['invoiced']?>" id="invoicedHidden">
													<input type="checkbox" class="form-check-input invoiceCheckCls" onclick="updateValueOnRuntime('invoiceTypeInvoiced','invoicedHidden')" id="invoiceTypeInvoiced" name="invoiced" <?php if($disp['invoiced']=='1') { echo 'checked'; } ?> value="<?=$disp['invoiced']?>">
													<label class="form-check-label" for="invoiceTypeInvoiced"><span class="invoiceTitle"><?=$invoiceType?></span> Invoiced</label>
												</div>
												<div class="form-group">
													<label>Invoice Date</label>
													<input name="invoiceDate" type="text" class="form-control datepicker invoiceDate" value="<?php if($disp['invoiceDate']=='0000-00-00') { echo 'TBD'; } else { echo $disp['invoiceDate']; } ?>">
												</div>
											</div>
											<div class="col-sm-3 invoiceCheckboxCls">
												<div class="form-check mb-2">
													<input type="hidden" name="invoicePaid" value="<?=$disp['invoicePaid']?>" id="invoicePaidHidden">
													<input type="checkbox" class="form-check-input invoiceCheckCls" onclick="updateValueOnRuntime('invoiceTypeInvoicePaid','invoicePaidHidden')" id="invoiceTypeInvoicePaid" name="invoicePaid" <?php if($disp['invoicePaid']=='1') { echo 'checked'; } ?> value="<?=$disp['invoicePaid']?>">
													<label class="form-check-label" for="invoiceTypeInvoicePaid"><span class="invoiceTitle"><?=$invoiceType?></span> Paid</label>
												</div>
												<div class="form-group">
													<label>Invoice Paid Date</label>
													<input name="invoicePaidDate" type="text" class="form-control datepicker invoicePaidDate" value="<?php echo $disp['invoicePaidDate']; ?>">
												</div>
											</div>
											<div class="col-sm-3 invoiceCheckboxCls">
												<div class="form-check mb-2">
													<input type="hidden" name="invoiceClose" value="<?=$disp['invoiceClose']?>" id="invoiceCloseHidden">
													<input type="checkbox" class="form-check-input invoiceCheckCls" onclick="updateValueOnRuntime('invoiceTypeInvoiceClose','invoiceCloseHidden')" id="invoiceTypeInvoiceClose" name="invoiceClose" <?php if($disp['invoiceClose']=='1') { echo 'checked'; } ?>  value="<?=$disp['invoiceClose']?>">
													<label class="form-check-label" for="invoiceTypeInvoiceClose"><span class="invoiceTitle"><?=$invoiceType?></span> Closed</label>
												</div>
												<div class="form-group">
													<label>Invoice Closed Date</label>
													<input name="invoiceCloseDate" type="text" class="form-control datepicker invoiceCloseDate" value="<?php echo $disp['invoiceCloseDate']; ?>">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-3 ">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<div class="form-check mb-0">
														<input type="checkbox" class="form-check-input bolrc" id="customControlInline" name="inwrd" value="AK" <?php if($disp['inwrd']=='AK') { echo ' checked'; } ?>>
														<label class="form-check-label" for="customControlInline">Inward RD</label>
													</div>
													<?php
														$hasInwrd = false;
														if (!empty($documents)) {
															foreach ($documents as $doc) {
																if ($doc['type'] == 'inwrd') {
																	$hasInwrd = true;
																	break;
																}
															}
														}
														if ($hasInwrd) {
															echo '<a data-cls=".d-inwrd" href="#" class="btn btn-primary btn-sm download-pdf">Download All</a>';
														}
													?>
												</div>
												<div class="custom-file mb-2">
													<input type="file" class="custom-file-input" name="inwrd_d[]" multiple>
													
													<label class="custom-file-label" for="fileInward">Choose File</label>

												</div>
												<?php 
													if (!empty($documents)) {
														foreach ($documents as $doc) {
															if ($doc['type'] == 'inwrd') {
																$pdfArray[] = array('warehouse--inwrd', $doc['fileurl']);
																$fileUrl = base_url('assets/warehouse/inwrd/' . $doc['fileurl']);
																$downloadUrl = base_url('admin/download_pdf/warehouse--inwrd/' . $doc['fileurl']);
																$removeUrl = base_url('admin/paWarehouse/removefile/inwrd/' . $doc['id'] . '/' . $disp['id']);
																echo '<a class="d-pdf d-inwrd" href="'.base_url('admin/download_pdf/warehouse--inwrd/'.$doc['fileurl']).'">download</a>';
																$filename = htmlspecialchars($doc['fileurl']);
																$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;
																echo '<div class="d-flex justify-content-between align-items-center">';
																echo '<div class="text-truncate" style="max-width: 60%;">';
																echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
																echo '</div>';
																echo '<div>';
																echo '<a href="' . $fileUrl . '?id=' . rand(10, 99) . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
																echo '<a href="' . $downloadUrl . '" class="fileDownload" download><i class="fas fa-download text-primary mx-1"></i></a>';
																if ($doc['parent'] != 'yes') {
																	echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
																}
																echo '</div>';
																echo '</div>';
															}
														}
													} 
													if (!empty($otherDocuments)) {
														foreach ($otherDocuments as $doc) {
															if ($doc['type'] == 'inwrd') {
																if ($doc['parentType'] == 'warehousing') {
																	$pdfArray[] = ['warehouse--inwrd', $doc['fileurl']];
																	$fileUrl = base_url('assets/warehouse/inwrd/' . $doc['fileurl']);
																	$downloadUrl = base_url('admin/download_pdf/warehouse--inwrd/' . $doc['fileurl']);
																	$removeUrl = base_url('admin/paWarehouse/removefile/inwrd/' . $doc['id'] . '/' . $disp['id']);
																} else {
																	$fileUrl = $downloadUrl = $removeUrl = '';
																}

																$filename = htmlspecialchars($doc['fileurl']);
																$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;

																echo '<div class="d-flex justify-content-between align-items-center">';
																echo '<div class="text-truncate" style="max-width: 60%;">';
																echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
																echo '</div><div>';
																echo '<a href="' . $fileUrl . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
																echo '<a href="' . $downloadUrl . '" download><i class="fas fa-download text-primary mx-1"></i></a>';
																if ($doc['otherParent'] != 'yes' && $removeUrl) {
																	echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
																}
																echo '</div></div>';
															}
														}
													}
												?>
											</div>
											<div class="col-sm-3 ">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<div class="form-check mb-0">
														<input class="form-check-input outwrd" id="customControlInlineOutwrd" name="outwrd" value="AK" <?php if($disp['outwrd']=='AK') { echo ' checked'; } ?> type="checkbox">
														<label class="form-check-label" for="customControlInlineOutwrd">Outward DD</label>
													</div>
													<?php
														$hasOutwrd = false;
														if (!empty($documents)) {
															foreach ($documents as $doc) {
																if ($doc['type'] == 'outwrd') {
																	$hasOutwrd = true;
																	break;
																}
															}
														}
														if ($hasOutwrd) {?>
															<a data-cls=".d-outwrd" href="#" class="download-pdf ml-2">Download All</a>
														<?php }
													?>
												</div>
												<div class="custom-file mb-2">
													<input type="file" class="custom-file-input" name="outwrd_d[]" multiple>
													<label class="custom-file-label" for="fileInward">Choose File</label>
												</div>
												<?php 
													if (!empty($documents)) {
														foreach ($documents as $doc) {
															if ($doc['type'] == 'outwrd') {
																$pdfArray[] = array('warehouse--outwrd', $doc['fileurl']);
																$fileUrl = base_url('assets/warehouse/outwrd/' . $doc['fileurl']);
																$downloadUrl = base_url('admin/download_pdf/warehouse--outwrd/' . $doc['fileurl']);
																$removeUrl = base_url('admin/paWarehouse/removefile/outwrd/' . $doc['id'] . '/' . $disp['id']);
																echo '<a class="d-pdf d-outwrd" href="'.base_url('admin/download_pdf/warehouse--outwrd/'.$doc['fileurl']).'">download</a>';
																$filename = htmlspecialchars($doc['fileurl']);
																$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;
																echo '<div class="d-flex justify-content-between align-items-center">';
																echo '<div class="text-truncate" style="max-width: 60%;">';
																echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
																echo '</div>';
																echo '<div>';
																echo '<a href="' . $fileUrl . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
																echo '<a href="' . $downloadUrl . '" download><i class="fas fa-download text-primary mx-1"></i></a>';
																if ($doc['parent'] != 'yes') {
																	echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
																}
																echo '</div>';
																echo '</div>';
															}
														}
													} 
													if (!empty($otherDocuments)) {
														foreach ($otherDocuments as $doc) {
															if ($doc['type'] == 'outwrd') {
																if ($doc['parentType'] == 'warehousing') {
																	$pdfArray[] = ['warehouse--outwrd', $doc['fileurl']];
																	$fileUrl = base_url('assets/warehouse/outwrd/' . $doc['fileurl']);
																	$downloadUrl = base_url('admin/download_pdf/warehouse--outwrd/' . $doc['fileurl']);
																	$removeUrl = base_url('admin/paWarehouse/removefile/outwrd/' . $doc['id'] . '/' . $disp['id']);
																} else {
																	$fileUrl = $downloadUrl = $removeUrl = '';
																}

																$filename = htmlspecialchars($doc['fileurl']);
																$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;

																echo '<div class="d-flex justify-content-between align-items-center">';
																echo '<div class="text-truncate" style="max-width: 60%;">';
																echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
																echo '</div><div>';
																echo '<a href="' . $fileUrl . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
																echo '<a href="' . $downloadUrl . '" download><i class="fas fa-download text-primary mx-1"></i></a>';
																if ($doc['otherParent'] != 'yes' && $removeUrl) {
																	echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
																}
																echo '</div></div>';
															}
														}
													}
												?>
											</div>
											<div class="col-sm-3 ">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<div class="form-check mb-0">
														<input class="form-check-input bolrc" name="gd" type="checkbox" id="customControlInlinegd" value="AK" <?php if($disp['gd']=='AK') { echo ' checked'; } ?>>
														<label class="form-check-label" for="customControlInlinegd">Payment Proof</label>
													</div>
													<?php
														$hasGd = false;
														if (!empty($documents)) {
															foreach ($documents as $doc) {
																if ($doc['type'] == 'gd') {
																	$hasGd = true;
																	break;
																}
															}
														}
														if ($hasGd) {
															echo '<a data-cls=".d-gd" href="#" class="btn btn-primary btn-sm download-pdf">Download All</a>';
														}
													?>
												</div>
												<div class="custom-file mb-2">
													<input type="file" class="custom-file-input" name="gd_d">
													<label class="custom-file-label" for="fileInward">Choose File</label>
												</div>
												<?php 
												$gdfile = '';
													if (!empty($documents)) {
														
														foreach ($documents as $doc) {
															if ($doc['type'] == 'gd') {
																$gdfile = 'yes';
																$pdfArray[] = array('warehouse--gd', $doc['fileurl']);
																$fileUrl = base_url('assets/warehouse/gd/' . $doc['fileurl']);
																$downloadUrl = base_url('admin/download_pdf/warehouse--gd/' . $doc['fileurl']);
																$removeUrl = base_url('admin/paWarehouse/removefile/gd/' . $doc['id'] . '/' . $disp['id']);
																echo '<a class="d-pdf d-gd" href="'.base_url('admin/download_pdf/warehouse--gd/'.$doc['fileurl']).'">download</a>';
																$filename = htmlspecialchars($doc['fileurl']);
																$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;
																echo '<div class="d-flex justify-content-between align-items-center">';
																echo '<div class="text-truncate" style="max-width: 60%;">';
																echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
																echo '</div>';
																echo '<div>';
																echo '<a href="' . $fileUrl . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
																echo '<a href="' . $downloadUrl . '" download><i class="fas fa-download text-primary mx-1"></i></a>';
																if ($doc['otherParent'] != 'yes' && $removeUrl) {
																	echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
																}
																echo '</div>';
																echo '</div>';
															}
														}
													}

													if (!empty($otherDocuments)) {
														foreach ($otherDocuments as $doc) {
															if ($doc['type'] == 'gd') {
																$gdfile = 'yes';
																$pdfArray[] = ['upload', $doc['fileurl']];
																$downloadUrl = base_url('admin/download_pdf/upload/' . $doc['fileurl']);
																if ($doc['parentType'] == 'fleet') {
																	$pdfArray[] = array('upload',$doc['fileurl']);
																	$downloadUrl = base_url('admin/download_pdf/upload/' . $doc['fileurl']);
																	$fileUrl = base_url('assets/upload/gd/' . $doc['fileurl']);
																	$removeUrl = base_url('admin/dispatch/removefile/gd/' . $doc['id'] . '/' . $disp['id']);
																}elseif ($doc['parentType'] == 'logistics'){ 
																	$pdfArray[] = array('outside-dispatch--gd',$doc['fileurl']);				
																	$downloadUrl = base_url('admin/download_pdf/outside-dispatch--gd/' . $doc['fileurl']);			
																	$fileUrl = base_url('assets/outside-dispatch/gd/' . $doc['fileurl']);
																	$removeUrl = base_url('admin/outside-dispatch/removefile/gd/' . $doc['id'] . '/' . $disp['id']);
																} elseif ($doc['parentType'] == 'warehousing') {
																	$pdfArray[] = array('warehouse--gd', $doc['fileurl']);
																	$downloadUrl = base_url('admin/download_pdf/warehouse--gd/' . $doc['fileurl']);
																	$fileUrl = base_url('assets/warehouse/gd/' . $doc['fileurl']);
																	$removeUrl = base_url('admin/paWarehouse/removefile/gd/' . $doc['id'] . '/' . $disp['id']);
																} else {
																	$removeUrl = ''; 
																}
																$filename = htmlspecialchars($doc['fileurl']);
																$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;

																echo '<div class="d-flex justify-content-between align-items-center">';
																echo '<div class="text-truncate" style="max-width: 60%;">';
																echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
																echo '</div>';
																echo '<div>';
																echo '<a href="' . $fileUrl . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
																echo '<a href="' . $downloadUrl . '" download><i class="fas fa-download text-primary mx-1"></i></a>';
																if ($doc['otherParent'] != 'yes' && $removeUrl) {
																	echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
																}
																echo '</div>';
																echo '</div>';
															}
														}
													}
												?>
												<input type="hidden" name="gdfile" value="<?=$gdfile?>">
											</div>
											<div class="col-sm-3 ">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<div class="form-check mb-0">
														<label class="form-check-label" for="Invoice">Invoice</label>
													</div>
													<?php
														$hasPaInvoice = false;
														if (!empty($documents)) {
															foreach ($documents as $doc) {
																if ($doc['type'] == 'paInvoice') {
																	$hasPaInvoice = true;
																	break;
																}
															}
														}
														if ($hasPaInvoice) {
															echo '<a data-cls=".d-paInvoice" href="#" class="btn btn-primary btn-sm download-pdf">Download All</a>';
														}
													?>
												</div>
												<div class="custom-file mb-2">
													<input type="file" class="custom-file-input" name="paInvoice[]">
													<label class="custom-file-label" for="paInvoice">Choose File</label>
												</div>
												<?php 
													if (!empty($documents)) { 
														foreach ($documents as $doc) {
															if ($doc['type'] == 'paInvoice') {
																$pdfArray[] = array('warehouse--invoice', $doc['fileurl']);
																$fileUrl = base_url('assets/warehouse/invoice/' . $doc['fileurl']);
																$downloadUrl = base_url('admin/download_pdf/warehouse--invoice/' . $doc['fileurl']);
																$removeUrl = base_url('admin/paWarehouse/removefile/invoice/' . $doc['id'] . '/' . $disp['id']);
																echo '<a class="d-pdf d-paInvoice" href="'.base_url('admin/download_pdf/warehouse--invoice/'.$doc['fileurl']).'">download</a>';
																$filename = htmlspecialchars($doc['fileurl']);
																$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;
																echo '<div class="d-flex justify-content-between align-items-center">';
																echo '<div class="text-truncate" style="max-width: 60%;">';
																echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
																echo '</div>';
																echo '<div>';
																echo '<a href="' . $fileUrl . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
																echo '<a href="' . $downloadUrl . '" download><i class="fas fa-download text-primary mx-1"></i></a>';
																if ($doc['parent'] != 'yes') {
																	echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
																}
																echo '</div>';
																echo '</div>';
															}
														}
													}

													if (!empty($otherDocuments)) {
														foreach ($otherDocuments as $doc) {
															if ($doc['type'] == 'paInvoice') {
																if ($doc['parentType'] == 'fleet') {
																	$pdfArray[] = array('upload', $doc['fileurl']);
																	$downloadUrl = base_url('admin/download_pdf/upload/' . $doc['fileurl']);
																	$fileUrl = base_url('assets/upload/invoice/' . $doc['fileurl']);
																	$removeUrl = base_url('admin/dispatch/removefile/invoice/' . $doc['id'] . '/' . $disp['id']);

																} elseif ($doc['parentType'] == 'logistics') {
																	$pdfArray[] = array('outside-dispatch--invoice', $doc['fileurl']);
																	$downloadUrl = base_url('admin/download_pdf/outside-dispatch--invoice/' . $doc['fileurl']);
																	$fileUrl = base_url('assets/outside-dispatch/invoice/' . $doc['fileurl']);
																	$removeUrl = base_url('admin/outside-dispatch/removefile/invoice/' . $doc['id'] . '/' . $disp['id']);

																} elseif ($doc['parentType'] == 'warehousing') {
																	$pdfArray[] = array('warehouse--invoice', $doc['fileurl']);
																	$downloadUrl = base_url('admin/download_pdf/warehouse--invoice/' . $doc['fileurl']);
																	$fileUrl = base_url('assets/warehouse/invoice/' . $doc['fileurl']);
																	$removeUrl = base_url('admin/paWarehouse/removefile/invoice/' . $doc['id'] . '/' . $disp['id']);
																} else {
																	$removeUrl = '';
																}

																$filename = htmlspecialchars($doc['fileurl']);
																$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;

																echo '<div class="d-flex justify-content-between align-items-center">';
																echo '<div class="text-truncate" style="max-width: 60%;">';
																echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
																echo '</div>';
																echo '<div>';
																echo '<a href="' . $fileUrl . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
																echo '<a href="' . $downloadUrl . '" download><i class="fas fa-download text-primary mx-1"></i></a>';
																if ($doc['otherParent'] != 'yes' && $removeUrl) {
																	echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
																}
																echo '</div>';
																echo '</div>';
															}
														}
													}
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- carrier  -->
							<div class="floating-wrapper">
								<div class="card-header-floating">
									<div class="card-title-floating">Carrier Details</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">
										<div class="row">
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Payment Type</label>
													<select class="form-control carrierPaymentTypeCls" name="carrierPaymentType" id="carrierPaymentType">
														<option value="">Select Payment Type</option>
														<option value="Standard Billing" <?php if($disp['carrierPaymentType']=='Standard Billing') { echo 'selected'; }?>>Standard Billing</option>
														<option value="Quick Pay" <?php if($disp['carrierPaymentType']=='Quick Pay') { echo 'selected'; }?>>Quick Pay</option>
														<option value="Zelle" <?php if($disp['carrierPaymentType']=='Zelle') { echo 'selected'; }?>>Zelle</option>
													</select>
												</div>
											</div>
											<div class="col-sm-4 d-none" id="factoringTypeDev">
												<div class="form-group">
													<label>Factoring Type</label>
													<select class="form-control factoringTypeCls" name="factoringType" id="factoringType">
														<option value="">Select a Type</option>
														<option value="Direct Payment" <?php if($disp['factoringType']=='Direct Payment') { echo 'selected'; }?>>Direct Payment</option>
														<option value="Factoring" <?php if($disp['factoringType']=='Factoring') { echo 'selected'; }?>>Factoring</option>
													</select>
												</div>
											</div>
											<div class="col-sm-4 d-none" id="factoringCompanyDev">
												<div class="form-group">
													<label>Factoring Companies</label>
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
										<div class="row">
											<div class="col-sm-4 ">
												<div class="form-group">
													<label for="readyDate">Invoice Date</label>
													<input name="custInvDate" type="text" class="form-control datepicker" value="<?php if($disp['custInvDate']!='') { echo $disp['custInvDate']; } ?>">
												</div>
											</div>
											<div class="col-sm-4" >
												<div class="form-group" id="custDueDate">
													<label for="invoiceDate">Due Date</label>
													<input readonly name="custDueDate" type="text" class="form-control datepicker" value="<?php if($disp['custDueDate']!='') { echo $disp['custDueDate']; } ?>">
												</div>
											</div>
											<div class="col-sm-4"  id="carrierPayoutCheckboxDate">
												<div class="form-check mb-2">
													<input type="hidden" value="0" name="carrierPayoutCheck">
													<input type="checkbox" class="form-check-input" id="carrierPayoutCheck" name="carrierPayoutCheck" value="1" <?php if($disp['carrierPayoutCheck']=='1') { echo ' checked'; } ?>>
													<label class="form-check-label carrierPayoutCheck" for="carrierPayoutCheck">Payout Date</label>
												</div>
												<div class="form-group">
													<input name="carrierPayoutDate" id="carrierPayoutDate" type="text" class="form-control datepicker" value="<?php if($disp['carrierPayoutDate']=='0000-00-00') { echo ''; } else { echo $disp['carrierPayoutDate']; } ?>">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-4 ">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<div class="form-check mb-0">
														<input type="hidden" value="0" name="carrierInvoiceCheck">
														<input type="checkbox" class="form-check-input" id="carrierInvoiceCheck" name="carrierInvoiceCheck" value="1" <?php if($disp['carrierInvoiceCheck']=='1') { echo 'checked'; } ?>>
														<label class="form-check-label" for="carrierInvoiceCheck"> Invoice</label>
													</div>
													<!-- <button  data-cls=".d-carrierInvoice" href="#" class="btn btn-primary btn-sm download-pdf">Download All</button> -->
													<?php
														$hasCarrierInvoice = false;
														if (!empty($documents)) {
															foreach ($documents as $doc) {
																if ($doc['type'] == 'carrierInvoice') {
																	$hasCarrierInvoice = true;
																	break;
																}
															}
														}
														if ($hasCarrierInvoice) {
															echo '<a data-cls=".d-carrierInvoice" href="#" class="download-pdf ml-2">Download All</a>';
														}
													?>
												</div>
												<div class="custom-file mb-2">
													<input name="carrierInvoice[]" multiple type="file" class="custom-file-input">
													<label class="custom-file-label" for="carrierInvoice">Choose File</label>
												</div>
												<?php if(!empty($documents)) { 
													foreach($documents as $doc) {
														if($doc['type']=='carrierInvoice') {     
															$pdfArray[] = array('warehouse--carrierInvoice', $doc['fileurl']);
															$fileUrl = base_url('assets/warehouse/carrierInvoice/' . $doc['fileurl']);
															$downloadUrl = base_url('admin/download_pdf/warehouse--carrierInvoice/' . $doc['fileurl']);
															$removeUrl = base_url('admin/paWarehouse/removefile/carrierInvoice/' . $doc['id'] . '/' . $disp['id']);
															echo '<a class="d-pdf d-carrierInvoice" href="'.base_url('admin/download_pdf/warehouse--carrierInvoice/'.$doc['fileurl']).'">download</a>';
															$filename = htmlspecialchars($doc['fileurl']);
															$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;
																
															echo '<div class="d-flex justify-content-between align-items-center">';
															echo '<div class="text-truncate" style="max-width: 60%;">';
															echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
															echo '</div>';
															echo '<div>';
															echo '<a href="' . $fileUrl . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
															echo '<a href="' . $downloadUrl . '" download><i class="fas fa-download text-primary mx-1"></i></a>';
															if ($doc['parent'] != 'yes') {
																echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
															}
															echo '</div>';
															echo '</div>';
														}
													}
												} ?>
											</div>
											<div class="col-sm-4 " id="carrierPaymentProof">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<div class="form-check mb-0">
														<input type="checkbox" name="carrier_gd" class="carrier-control-input form-check-input" id="carrierControlInlinegd" value="AK" <?php if($disp['carrierGd']=='AK') { echo ' checked'; } ?>>
														<label class="form-check-label" for="carrierControlInlinegd">Payment Proof</label>
													</div>
													<!-- <button data-cls=".carrier-d-gd" href="#" class="btn btn-primary btn-sm download-pdf">Download All</button> -->
													<?php
														$hasCarrierGd = false;
														if (!empty($documents)) {
															foreach ($documents as $doc) {
																if ($doc['type'] == 'carrierGd') {
																	$hasCarrierGd = true;
																	break;
																}
															}
														}
														if ($hasCarrierGd) {
															echo '<a data-cls=".carrier-d-gd" href="#" class="download-pdf">Download All</a>';
														}
													?>
												</div>
												<div class="custom-file mb-2">
													<input type="file" class="custom-file-input" name="carrier_gd_d[]" multiple>
													<label class="custom-file-label" for="carrier_gd_d" >Choose File</label>
												</div>
												<?php 
													$carriergdfile = '';
													if (!empty($documents)) { 
														foreach ($documents as $doc) {
															if ($doc['type'] == 'carrierGd') { 
																$carriergdfile = 'yes';
																$pdfArray[] = array('warehouse--gd', $doc['fileurl']);
																$fileUrl = base_url('assets/warehouse/gd/' . $doc['fileurl']);
																$downloadUrl = base_url('admin/download_pdf/warehouse--gd/' . $doc['fileurl']);
																$removeUrl = base_url('admin/paWarehouse/removefile/gd/' . $doc['id'] . '/' . $disp['id']);
																echo '<a class="d-pdf carrier-d-gd" href="'.base_url('admin/download_pdf/warehouse--gd/'.$doc['fileurl']).'">download</a>';
																$filename = htmlspecialchars($doc['fileurl']);
																$shortName = (strlen($filename) > 30) ? substr($filename, 0, 30) . '...' : $filename;
																echo '<div class="d-flex justify-content-between align-items-center">';
																echo '<div class="text-truncate" style="max-width: 60%;">';
																echo '<a href="' . $fileUrl . '" target="_blank" title="' . $filename . '">' . $shortName . '</a>';	
																echo '</div>';
																echo '<div>';
																echo '<a href="' . $fileUrl . '" target="_blank"><i class="fas fa-eye text-info mx-1"></i></a>';
																echo '<a href="' . $downloadUrl . '" download><i class="fas fa-download text-primary mx-1"></i></a>';
																if ($doc['parent'] != 'yes') {
																	echo '<a href="' . $removeUrl . '" class="remove-file"><i class="fas fa-trash text-danger mx-1"></i></a>';
																}
																echo '</div>';
																echo '</div>';
															}
														}
													}
												?>
												<input type="hidden" name="carriergdfile" value="<?=$carriergdfile?>">
											</div>

										</div>
										<!-- <button class="btn btn-primary btn-sm mt-3">Download All Files</button> -->
									</div>
								</div>
							</div>
							<!-- Shipment details -->
							<div class="floating-wrapper ">
								<div class="card-header-floating">
									<div class="card-title-floating">Shipment Notes</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">
										<div class="row">
											<div class="col-sm-4 ">
												<div class="form-group">
													<label>Status</label>
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
											<div class="col-sm-6">
												<div class="form-group">
													<label>Remarks</label>
												<input name="status" type="text" class="form-control statusCls" data="<?=$disp['status']?>" value="<?php echo $disp['status']; ?>">
												</div>
											</div>
											<div class="col-sm-2 mt-3">
												<div class="form-check mb-2 mt-3">
													<input type="hidden" value="0" name="lockDispatch">
													<input type="checkbox" name="lockDispatch" id="customLockDispatch" class="form-check-input" value="1" 
														<?php if($disp['lockDispatch']=='1') { echo ' checked'; } ?>>
													<label class="form-check-label" for="customLockDispatch">Lock Dispatch</label>
												</div>

												<div class="form-check mb-2">
													<input type="checkbox" class="form-check-input" id="delivered" name="delivered" value="yes" 
														<?php if($disp['delivered']=='yes') { echo ' checked'; } ?>>
													<label class="form-check-label" for="delivered">Delivered</label>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label>Notes</label>
													<textarea name="notes" class="form-control"><?php echo $disp['notes'] ?></textarea>
												</div>
											</div>
										<div class="col-sm-6">
												<div class="form-group">
													<label>Invoice Description</label>
													<textarea <?php if($invoiceType=='DB' || $invoiceType=='QP'){ echo 'required'; } ?> name="invoiceNotes" id="invoiceNotes" class="form-control invoiceNotes"><?php echo $disp['invoiceNotes'] ?></textarea>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-4">
												<!-- <button class="btn btn-success btn-sm p-2 mt-2" type="submit" name="save" value="Update PA Warehousing" ></button> -->
												<input type="submit" name="save" value="Update PA Warehousing" class="btn btn-primary"/>
											</div>
											<?php if(checkPermission($this->session->userdata('permission'),'invoice')){ ?>
											<div class="col-sm-4" id="generateInvButton"> 
												<div class="form-group">
													<a class="btn btn-success editInvoice" data-id="<?=$disp['id']?>" data-dTable="warehouse_dispatch" 
														href="<?php echo base_url('Invoice/downloadInvoicePDF/'.$disp['id']);?>?dTable=warehouse_dispatch" data-toggle="modal" data-target="#editInvoiceModal">Generate Invoice
													</a>
												</div>
											</div>	
										<?php } ?>
										<?php if($pdfArray){ ?>
											<div class="col-sm-4">
												<button class="btn btn-success download-all-pdf" id="download-pdfs">Download All Files</button>				
											</div>
										<?php } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>	

					<?php if($dispatchLog){ ?>   
						<div class="container-fluid" style="margin-top: -40px"> 
							<div class="floating-wrapper ">
								<div class="card-header-floating">
									<div class="card-title-floating">Update History</div>
								</div>
								<div class="card border shadow">
									<div class="card-body">
										<div class="row" style="margin-top: -45px;">
											<div class="col-sm-12">
												<p>&nbsp;</p>
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
																					if($val[1]=='truckingCompany'){
																						if(array_key_exists($old,$truckComArr)){ $old = $truckComArr[$old]; }
																						if(array_key_exists($new,$truckComArr)){ $new = $truckComArr[$new]; }
																					}
																					if($val[1]=='bookedUnderNew'){
																						if(array_key_exists($old,$bookUnderArr)){ $old = $bookUnderArr[$old]; }
																						if(array_key_exists($new,$bookUnderArr)){ $new = $bookUnderArr[$new]; }
																					}
																					if($val[1]=='warehouseServices'){
																						if(array_key_exists($old,$typeOfServiceArr)){ $old = $typeOfServiceArr[$old]; }
																						if(array_key_exists($new,$typeOfServiceArr)){ $new = $typeOfServiceArr[$new]; }
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
																} 
															?>
														</tbody>
													</table>
													<?php if(count($dispatchLog) > 5){ ?>
														<a href="#" id="showMore" class="btn btn-sm">Show More</a>
													<?php } ?>
												</div>
											</div>
										</div>
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
						<a href="#" class="btn btn-primary combibePdfBtn" data-toggle="modal" data-target="#combinePdfModal">Combine PDF / Email </a>
					</p>
				</form>
			</div> 
		</div>
		
	</div>
</div>

<div id="combinePdfModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="max-width: 900px;">
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

<div id="emailCarrierPaymenteModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Select Carrier Files to Email</h4>
      </div>
      <div class="modal-body">
        <form id="carrierPdfForm" action="<?php echo base_url('Invoice/emailRemitanceProof'); ?>">
		<input type="hidden" name="dispatch_id" id="carrier_dispatch_id">
          <div class="form-group carrier-file-list">
            <!-- Dynamically load checkboxes here -->
          </div>
		  <button type="submit" class="btn btn-primary" id="sendCarrierEmailBtn">Send Email</button>
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
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo base_url('assets/ckeditor/ckeditor.js'); ?>"></script>

<script>
	$(document).ready(function() {
      $('.select2').select2();
   	});
	$(document).ready(function() {
	    <?php if($showMsgDiv == 'true') { ?>
	    setTimeout(function(){
	        $('.msg-div').hide();
	    }, 5000);
        
	    var submitElement = $("#submit");
        if (submitElement.length) {
            $('html, body').animate({
                scrollTop: submitElement.offset().top
            }, 'slow');
        }

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
    		
    		fieldset.find('.addressI').val($(this).attr('data-address')+' '+$(this).attr('data-city')+' '+$(this).attr('data-zip'));
    		fieldset.find('.companyI').val($(this).attr('data-company'));
    		fieldset.find('.cityI').val($(this).attr('data-city'));
    		fieldset.find('.addressidI').val($(this).attr('data-id'));
    		fieldset.find('.shippingHoursI').val($(this).attr('data-time'));
    		fieldset.find('.addressList').html('').remove();
		});
		
		$('body').on('keyup', '.getCompany', function () {
			clearTimeout(typingTimer);
			const $this = $(this); 
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
		
		
		$('body').on('focus',".datepicker", function(){
			$(this).datepicker({dateFormat: 'yy-mm-dd',changeMonth: true, changeYear: true});
		});
		$(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
        });
        
		$( ".sortable" ).sortable();
		<?php if($disp['lockDispatch']=='1') { ?>
		    $('.lockDispatchCls input, .lockDispatchCls select, .lockDispatchCls textarea').attr('readonly','');
		<?php } ?>
		$('#dataTable11 tbody tr').slice(5).hide();
        $('#showMore').click(function(e) {
            e.preventDefault();
            $('#dataTable11 tbody tr').show();
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
				// console.log(href);
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
			console.log(href);
			var link = document.createElement('a');
			link.href = href
			link.target = '_blank';
			link.download = '';
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
        <?php } ?>
        if (!$('.d-inwrd').length) { $('a[data-cls=".d-inwrd"]').hide(); }
		if (!$('.d-outwrd').length) { $('a[data-cls=".d-outwrd"]').hide(); }
		if (!$('.d-gd').length) { $('a[data-cls=".d-gd"]').hide(); }
		if (!$('.carrier-d-gd').length) { $('a[data-cls=".carrier-d-gd"]').hide(); }
		if (!$('.d-carrierInvoice').length) { $('a[data-cls=".d-carrierInvoice"]').hide(); }
		if (!$('.d-paInvoice').length) { $('a[data-cls=".d-paInvoice"]').hide(); }
		
        
		var timeoutID;
		
		$('body').on('keyup', '.expenseAmt', function(){
			let cls = $(this).attr('data-cls');
			if($(cls).val() == 'FSC (Fuel Surcharge)'){
				let $this = $(this);
				clearTimeout(timeoutID); 
				timeoutID = setTimeout(function(){
					let surcharge = $this; 
					let samt = surcharge.val();
					if(samt == '' || samt == 'undefined' || samt == 'NaN' || isNaN(samt)) { samt = 0; }
					
					$(".expenseAmt").each(function(index) {
						let insideCls = $(this).attr('data-cls');
						if($(insideCls).val() == 'Line Haul') {
							let amt = $(this).val();
							if(amt == '' || amt == 'undefined' || amt == 'NaN' || isNaN(amt)) { amt = 0; }
							let result = (samt / 100) * amt;
							surcharge.val(parseFloat(result).toFixed(2));
						}
					});
					calculatePaRate();
				}, 2000);
				} else {
				calculatePaRate();
			}
		});

			$('body').on('keyup', '.carrierExpenseAmt', function(){
			let cls = $(this).attr('data-cls');
			if($(cls).val() == 'FSC (Fuel Surcharge)'){
				let $this = $(this); // Capture $(this) to use inside setTimeout
				
				clearTimeout(timeoutID); // Clear any existing timer
				
				timeoutID = setTimeout(function(){
					let surcharge = $this; // Use the captured value of $(this)
					let samt = surcharge.val();
					if(samt == '' || samt == 'undefined' || samt == 'NaN' || isNaN(samt)) { samt = 0; }
					
					$(".carrierExpenseAmt").each(function(index) {
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
					calculateCarrierRate();
				}, 2000);
				} else {
				calculateCarrierRate();
			}
		});
		
		
		$('body').on('click','.expenseAmt',function(){
			calculatePaRate();
		});

		$('body').on('click','.carrierExpenseAmt',function(){
			calculateCarrierRate();
		})

		$('body').on('change','.expenseNameSelect',function(){
			calculatePaRate();
		});
		
		$('body').on('change','.carrierExpenseNameSelect',function(){
			calculateCarrierRate();
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
			let expenseDiv = `
			<div class="col-sm-3 mb-1 childInvoice-div-${dcid}">
				<div class="form-group d-flex m-0">
					<input name="warehouseChildInvoice[]" required type="text" class="form-control" placeholder="Warehousing Invoice" value="">
					<div class="input-group-append ml-2">
						<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".childInvoice-div-${dcid}">
							<i class="fas fa-trash-alt"></i>
						</button>
					</div>
				</div>
			</div>`;
			dcid++;
			$('.childInvoice-cls').append(expenseDiv);
		});

		$('.fleetChildInvoice-btn').click(function(){
			let expenseDiv = `
			<div class="col-sm-3 mb-1 pa-invoice childInvoice-div-${dcid}">
				<div class="form-group d-flex m-0">
					<input name="fleetChildInvoice[]" required type="text" class="form-control" placeholder="Fleet Invoice" value="">
					<div class="input-group-append ml-2">
						<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".childInvoice-div-${dcid}">
							<i class="fas fa-trash-alt"></i>
						</button>
					</div>
				</div>
			</div>`;
			dcid++;
			$('.childInvoice-cls').append(expenseDiv);
		});

		$('.logisticsChildInvoice-btn').click(function(){
			let expenseDiv = `
			<div class="col-sm-3 mb-1 pa-invoice childInvoice-div-${dcid}">
				<div class="form-group d-flex m-0">
					<input name="logisticsChildInvoice[]" required type="text" class="form-control" placeholder="Logistics Invoice" value="">
					<div class="input-group-append ml-2">
						<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".childInvoice-div-${dcid}">
							<i class="fas fa-trash-alt"></i>
						</button>
					</div>
				</div>
			</div>`;
			dcid++;
			$('.childInvoice-cls').append(expenseDiv);
		});

	
        $('.expense-btn').click(function () {
			let expenseDiv = `
			<div class="col-sm-3 mb-1 expense-div-${dcid}">
				<div class="form-group d-flex m-0">
					<select name="expenseName[]" class="form-control expenseNameSelect expenseName-${dcid}">
						<?php foreach($expenses as $exp) { echo '<option value="'.$exp['id'].'">'.$exp['title'].'</option>'; } ?>
					</select>
					<div class="input-group-append ml-2">
						<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".expense-div-${dcid}">
							<i class="fas fa-trash-alt"></i>
						</button>
					</div>
				</div>
				<div class="input-group">
					<input name="expensePrice[]" data-cls=".expenseName-${dcid}" required type="number" step="0.01" class="form-control expenseAmt" value="0">
				</div>
			</div>`;
			dcid++;
			$('.expense-cls').append(expenseDiv);
		});

		$('.carrier-expense-btn').click(function(){
			let carrierExpenseDiv = `
			<div class="col-sm-3 mb-1 carrier-expense-div-${dcid}">
				<div class="form-group d-flex m-0">
					<select name="carrierExpenseName[]" class="form-control carrierExpenseNameSelect carrierExpenseName-${dcid}">
						<?php foreach($carrierExpenses as $exp) { echo '<option value="'.$exp['id'].'">'.$exp['title'].'</option>'; } ?>
					</select>
					<div class="input-group-append ml-2">
						<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".carrier-expense-div-${dcid}">
							<i class="fas fa-trash-alt"></i>
						</button>
					</div>
				</div>
				<div class="input-group">
					<input name="carrierExpensePrice[]" data-cls=".carrierExpenseName-${dcid}" required type="number" min="1" step="0.01" class="form-control carrierExpenseAmt" value="0">
				</div>
			</div>`;
			dcid++;
			$('.carrier-expense-cls').append(carrierExpenseDiv);
		});

		let expenseCount = <?= $e ?>;
		$(document).on('click', '.expense-custom-btn', function () {
			expenseCount++;
			const newInput = `
				<div class="col-sm-3 mb-1 expense-div-${expenseCount}">
					<div class="form-group d-flex m-0">
						<input name="customExpenseName[]" type="text" placeholder="Enter Title" class="form-control expenseName-${expenseCount}" required />
						<div class="input-group-append ml-2">
							<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".expense-div-${expenseCount}">
								<i class="fas fa-trash-alt"></i>
							</button>
						</div>
					</div>
					<div class="input-group">
						<input name="customExpensePrice[]" data-cls=".expenseName-${expenseCount}" type="number" step="0.01" class="form-control expenseAmt" required />
					</div>
				</div>
			`;
			$('.expense-cls').append(newInput);
		});

		$('.dispatchInfo-btn').click(function () {
			let dispatchInfoDiv = `
				<div class="col-sm-3 mb-1 dispatchInfo-div-${dcid}">
					<div class="form-group d-flex m-0">
						<select name="dispatchInfoName[]" class="form-control">
							<?php foreach($dispatchInfo as $di) { echo '<option value="'.$di['id'].'">'.$di['title'].'</option>'; } ?>
						</select>
						<div class="input-group-append ml-2">
							<button class="btn btn-danger pick-drop-remove-btn" type="button" data-removecls=".dispatchInfo-div-${dcid}">
								<i class="fas fa-trash-alt"></i>
							</button>
						</div>
					</div>
					<div class="input-group">
						<input name="dispatchInfoValue[]" type="text" class="form-control" required>
					</div>
				</div>
			`;
			dcid++;
			$('.dispatchInfo-cls').append(dispatchInfoDiv);
		});
		
	    $('.pcode-add').click(function(){
			var pickup = '<div class="col-sm-2 pcode-id-'+dcid+'"><div class="form-group"><label for="contain">#</label><div class="input-group mb-2"><input name="pcode[]" type="text" required class="form-control" value=""><div class="input-group-append"><div class=""><button class="btn btn-danger code-delete" type="button" data-cls=".pcode-id-'+dcid+'" style="height:100%;"><i class="fa fa-trash"></i></button></div></div></div></div></div>';
			$('.pickup-no').before(pickup);
			dcid++;
		});

		$(document).on('click', '.remove-pcode', function () {
			$(this).closest('.pcode-field').remove();
		});
		
		$(document).on('click', '.dropoff-remove', function () {
			$(this).closest('.dropoff-field').remove();
		});
		
		$('body').on('click','.code-delete',function(){
			var cls = $(this).attr('data-cls');
			$(cls).html('').remove();
		}); 
		$('body').on('click', '.pick-drop-remove-btn', function () {
			var cls = $(this).data('removecls'); 
			if (window.confirm('Are you sure?')) {
				$(cls).remove();
				calculatePaRate();
				calculateCarrierRate();
			}
		});
		
		// var pid = <?php echo $pextraCount;?>;

		// var did = <?php echo $extraCount;?>;
		
		// $(document).on('click', '.pick-drop-remove-btn', function() {
		// 	var removeCls = $(this).data('removecls');
		// 	$(removeCls).remove(); 
		// 	if (removeCls.startsWith('.pickup')) {
		// 		if (pid > 1) {
		// 			pid--;
		// 		}
		// 	} else if (removeCls.startsWith('.dropoff')) {
		// 		if (did > 1) {
		// 			did--;
		// 		}
		// 	}
		// 	$('#invoiceTrucking').trigger('change');
		// });

		$('.parate').attr('data-price','<?=$paRate?>');
		$('.parate').keyup(function(){
			let valu = $(this).val();
			$(this).attr('data-price',valu);
			setTimeout(function(){
				calculatePaRate();
			}, 3000);
		});
		$('.rateInput').attr('data-price','<?=$rate?>');
		$('.rateInput').keyup(function(){
			let valu = $(this).val();
			$(this).attr('data-price',valu);
			setTimeout(function(){
				calculateCarrierRate();
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
				$('.invoiceCheckboxCls').show();
				$("#shipping_contact").prop("required", false);
			} else if(valu == 'Direct Bill'){
				$('.invoiceTitle').html('DB'); invoiceTypeTxt = 'DB';
				$('.invoiceCheckboxCls').show();
				$('.invoiceNotes').attr('required','');
				$("#shipping_contact").prop("required", true);
			} else if(valu == 'Quick Pay'){
				$('.invoiceTitle').html('QP'); invoiceTypeTxt = 'QP';
				$('.invoiceCheckboxCls').show();
				$('.invoiceNotes').attr('required','');
				$("#shipping_contact").prop("required", true);
			} else {
				$('.invoiceCheckboxCls').hide(); invoiceTypeTxt = '';
			}
			changeStatus(invoiceTypeTxt);
		});
		
		// $(document).ready(function() {
		// 	$('.invoiceTypeCls').trigger('change');
		// });

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
				var fileContainer = $(this).closest('.d-flex.justify-content-between.align-items-center');
    		    $.ajax({
    				type: "GET",
    				url: href,
    				data: "",
    				success: function(response) { 
						fileContainer.remove();
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

			const tracking = $('#tracking').val() || 'N/A';
			const invoiceNo = $('#invoice_no').val() || 'N/A';

			const defaultEmailSubject = `Invoice No.: ${invoiceNo} / Customer Ref No.: ${tracking}`;
			document.getElementById('email_subject').value = defaultEmailSubject;

			const defaultEmailBody = `<p><strong>Hey Team,</strong><br>
			Please find the attached invoice as captioned above.<br>Please acknowledge receipt.</p>`;

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
						return `${baseUrl}assets/warehouse/${fileType}/${fileName}`;
					}
					
				
					if ((data.inwrd_files && data.inwrd_files.length > 0) || (data.inwrd_images && data.inwrd_images.length > 0)) {
						fileList += `<h4 class="section-heading">Inward Files</h4><hr>`;
					}
					if (data.inwrd_files && data.inwrd_files.length > 0) {
						data.inwrd_files.forEach(function(file) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="file_ids[]" value="${file.id}" checked> 
								<a href="${constructUrl(file.name, 'inwrd')}" target="_blank">${file.name}</a>
								</label>
							</div>
							`;
						});
					}
					if (data.inwrd_images && data.inwrd_images.length > 0) {
						data.inwrd_images.forEach(function(image) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="file_ids[]" value="${image.id}" checked> 
								<a href="${constructUrl(image.name, 'inwrd')}" target="_blank">${image.name}</a>
								</label>
							</div>
							`;
						});
					}

					if ((data.outwrd_files && data.outwrd_files.length > 0) || (data.outwrd_images && data.outwrd_images.length > 0)) {
						fileList += `<h4 class="section-heading">Outward Files</h4><hr>`;
					}
					if (data.outwrd_files && data.outwrd_files.length > 0) {
						data.outwrd_files.forEach(function(file) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="file_ids[]" value="${file.id}" checked> 
								<a href="${constructUrl(file.name, 'outwrd')}" target="_blank">${file.name}</a>
								</label>
							</div>
							`;
						});
					}
					if (data.outwrd_images && data.outwrd_images.length > 0) {
						data.outwrd_images.forEach(function(image) {
							fileList += `
							<div class="file-entry">
								<label>
								<input type="checkbox" name="file_ids[]" value="${image.id}" checked> 
								<a href="${constructUrl(image.name, 'outwrd')}" target="_blank">${image.name}</a>
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
		// setupEmailButton('sendCarrierEmailBtn', 'carrierPdfForm', "<?php echo base_url('Invoice/emailRemitanceProof'); ?>");
		// setupEmailButton('sendRateConfirmationEmailBtn', 'rateConfirmationPdfForm', "<?php echo base_url('Invoice/emailRateConfirmationfile'); ?>");

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
							const fileLink = '<?php echo base_url('assets/warehouse/gd/'); ?>' + file.fileurl;

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

			dropdown.find('.time-option').removeClass('focused'); 
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

		$(document).on("click", ".timedd", function () {
			let parentGroup = $(this).closest(".input-group");
			activeInput = parentGroup.find(".timeInput"); 
			let dropdown = parentGroup.find(".tDropdown");

			dropdown.toggle().css({
			});
		});

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

		// $(document).on("change", "#appointmentTypeP", function () {
		// 	const appointmentType = $(this).val(); 
		// 	const ptimeInput = $("#ptime"); 
		// 	if (appointmentType !== "") {
		// 		ptimeInput.val(""); 
		// 	}
		// });
		// $(document).on("change", "#appointmentTypeD", function () {
		// 	const appointmentType = $(this).val(); 
		// 	const ptimeInput = $("#dtime"); 
		// 	if (appointmentType !== "") {
		// 		ptimeInput.val(""); 
		// 	}
		// });
		// $(document).on("change", ".appointmentType", function () {
		// 	const appointmentType = $(this).val();
		// 	const ptimeInput = $(this).closest(".col-sm-3").next(".col-sm-3").find(".timeInput");
		// 	if (appointmentType !== "") {
		// 		ptimeInput.val(""); 
		// 	}
		// });
		// $(document).on("change", ".appointmentTypeP1", function () {
		// 	const appointmentType = $(this).val();
		// 	const ptimeInput = $(this).closest(".col-sm-4").next(".col-sm-3").find(".timeInput");
		// 	if (appointmentType !== "") {
		// 		ptimeInput.val(""); 
		// 	}
		// });
		// $(document).on("change", ".appointmentTypeD1", function () {
		// 	const appointmentType = $(this).val();
		// 	const ptimeInput = $(this).closest(".col-sm-4").next(".col-sm-3").find(".timeInput");
		// 	if (appointmentType !== "") {
		// 		ptimeInput.val(""); 
		// 	}
		// });
	
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

		$(document).on("click", function (e) {
			if (!$(e.target).closest(".tDropdown, .timedd").length) {
				$(".tDropdown").hide();
			}
		});
		function changeStatus(invoiceTypeTxt){
			var statusText = ''; 
			var currentDate = '<?=date('m/d/Y')?>';
			let invoiceDate = '';
			let invDate = '';
			
			let currentNotes = $('.statusCls').val();
			let parts = currentNotes.split('-');
			let existingFirstPart = parts[0].trim();
			let remainingPart = parts.slice(1).join('-').trim();

			let allInvoices = [];
			$('input[name="warehouseChildInvoice[]"], input[name="fleetChildInvoice[]"], input[name="logisticsChildInvoice[]"]').each(function () {
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
				$('.statusCls').val(finalStatus.trim());
			} else {
				let statusTextOld = $('.statusCls').attr('data');
				$('.statusCls').val(statusTextOld);
			}
		}
	// $(document).on('click', '.pick-drop-remove-btn', function () {
	// 	let removeClass = $(this).data('removecls');
	// 	$(removeClass).remove();
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

		$(".expenseAmt").each(function() {
			let cls = $(this).attr('data-cls'); 
			let amt = $(this).val();
			let fieldName = $(this).attr('name'); // 👈 detect by name

			if(amt == '' || amt == 'undefined' || amt == 'NaN') { amt = 0; }
			amt = parseFloat(amt);

			if (fieldName === "customExpensePrice[]") {
				// 🔹 Custom expense → respect user’s + or -
				expenseAmt += amt;
			} else {
				// 🔹 Dropdown expense
				<?php 
				if($expenseN){ 
					for($e=0; count($expenseN) > $e; $e++){
						if($e > 0){ echo 'else '; }
						// dropdown negative type → always subtract ABS
						echo "if($(cls).val()=='".$expenseN[$e]."') { expenseAmt -= Math.abs(amt); }\n";
					}
				} else {
					echo 'if(1==2){}';
				}
				?>
				else {
					expenseAmt += amt; // normal dropdown expense
				}
			}
		});

		let paAmt = $('.parate').attr('data-price');
		if(paAmt == '' || paAmt == 'undefined' || paAmt == 'NaN') { paAmt = 0; }

		let finalAmt = parseFloat(paAmt) + parseFloat(expenseAmt); 
		$('.parate').val(parseFloat(finalAmt).toFixed(2));
		
		var rateInput = $('.rateInput').val();
		var agentRateInput = $('#agentRate').val();
		if(agentRateInput=='' || agentRateInput=='NaN'){ agentRateInput = 0; }

		let pamargin = parseFloat(finalAmt) - parseFloat(rateInput) - parseFloat(agentRateInput); 
		$('.pamargin').val(parseFloat(pamargin).toFixed(2));

		var agentPercentRate = $('#agentPercentRate').val();
		if(agentPercentRate === '' || agentPercentRate === 'NaN'){ agentPercentRate = 0; }
		var agentRateCalc = (parseFloat(pamargin) * parseFloat(agentPercentRate)) / 100;
		$('#agentRateDisplay').text(`(${agentRateCalc.toFixed(2)})`);

		var brookerPercent = (parseFloat(agentRateInput)/parseFloat(rateInput)) * 100;
		$('#brookerPercentDisplay').text(`(${brookerPercent.toFixed(2)}%)`);
	}

		function calculateCarrierRate(){
			var carrierExpenseAmt = 0;
			$(".carrierExpenseAmt").each(function(index) {
				let cls = $(this).attr('data-cls');
				let amt = $(this).val();
				if(amt == '' || amt == 'undefined' || amt == 'NaN') { amt = 0; }
				<?php 
				if($carrierExpenseN){ 
					for($e=0;count($carrierExpenseN) > $e;$e++){
						if($e > 0){ echo 'else '; }
						echo "if($(cls).val()=='".$carrierExpenseN[$e]."') { carrierExpenseAmt = carrierExpenseAmt - parseFloat(amt); }\n";
					}
				} else {
					echo 'if(1==2){}';
				}
				?>
				// if($(cls).val()=='Discount') { carrierExpenseAmt = carrierExpenseAmt - parseFloat(amt); }
				else { carrierExpenseAmt = carrierExpenseAmt + parseFloat(amt); }
			});
			let rateAmt = $('.rateInput').attr('data-price');
			if(rateAmt == '' || rateAmt == 'undefined' || rateAmt == 'NaN') { rateAmt = 0; }
			let finalRateAmt = parseFloat(rateAmt) + parseFloat(carrierExpenseAmt); 
			$('.rateInput').val(parseFloat(finalRateAmt).toFixed(2));
			//$('.parate').val(Math.round(finalAmt));
				
			var rateInput = $('.rateInput').val();
			var parateInput = $('.parate').val();

			var agentRateInput = $('#agentRate').val();
			if(agentRateInput=='' || agentRateInput=='NaN'){ agentRateInput = 0; }

			let pamargin = parseFloat(parateInput) - parseFloat(rateInput)-parseFloat(agentRateInput); 
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
	// const rateButton = document.getElementById("rateButton");

	if (query === "?invoice") {
		generateInvButton.style.display = "block";
		// rateButton.style.display = "none";
	}
	else if (fragment === "#submit") {
		generateInvButton.style.display = "block";
		// rateButton.style.display = "block";
	}
	else {
		generateInvButton.style.display = "none";
		// rateButton.style.display = "block";
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
                url: "<?php echo base_url('WarehouseDispatch/getNextInvoice'); ?>",
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
		$('#factoringTypeDev').addClass('d-none');
		$('#factoringCompanyDev').addClass('d-none');
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
		$('#carrierPaymentType').trigger('change');
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
	<?php 	
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
	legend{font-weight:bold;}
	.custom-control-label::before {width: 20px;height: 20px;}
	.custom-control-label::after {width: 20px;height: 20px;}
	.doc-file {display: inline-block;text-align: center;margin: 5px;font-size: 14px;}
	.doc-file span {display: block;}
	.doc-file {display: inline-block;text-align: center;max-width: 145px;position: relative;}
	.doc-file .remove-file {position: absolute;right: 0;top: 0px;padding: 3px;color: red;font-weight: bold;border: 1px solid red;line-height: 13px;}
	form fieldset{position:relative;}
	fieldset .pick-drop-btn{position:absolute;right:15px;}
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
	.no-overflow {
		overflow: visible !important;
	}

	.file-entry input[type="checkbox"] {
		transform: scale(1.5); 
		margin-right: 10px;
	}

	.form-check-input {
		transform: scale(1.5); 
	}
</style>

