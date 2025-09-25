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
            <i class="fas fa-th-list mr-2"></i> Add PA warehousing
        </h6>
 		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/paWarehouse');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div>     </div>
    <div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			<div class="container">
       			 <?php 
					$expenseN = array();
					foreach($expenses as $exp) {
						if($exp['type']=='Negative'){ $expenseN[] = $exp['title']; } 
					}
					// print_r($expenseN);exit;
					$carrierExpenseN = array();
					foreach($carrierExpenses as $exp) {
						if($exp['type']=='Negative'){ $carrierExpenseN[] = $exp['id']; } 
					}
					$premadetrip = array('pudate'=>'','notes'=>'','status'=>'','dassist'=>'','detention'=>'','invoice'=>'','tracking'=>'','trailer'=>'','dodate'=>'','dtime'=>'','ptime'=>'','trip'=>'','driver'=>'','vehicle'=>'','pcity'=>'','plocation'=>'','paddress'=>'','paddressid'=>'','dcity'=>'','dlocation'=>'','daddress'=>'','daddressid'=>'','rate'=>'','parate'=>'','company'=>'','dispatchMeta'=>array());
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
				<h3 class="mb-4"> Add PA Warehousings</h3>
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item');$this->session->set_flashdata('item',''); ?></h4> 
					</div>
					<?php } 
					
					$js_companies = $js_cities = $js_location = '';
					
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
				<form class="form" id="addPaLogisticform" method="post" action="<?php echo base_url('admin/paWarehouseAdd');?>" enctype="multipart/form-data">
					<?php  echo validation_errors();?>
					<div class="clearfix"></div>
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
											<select class="form-control select2" name="truckingCompany" required>
												<option value="">Select Service Provider</option>
												<?php 
													if(!empty($truckingCompanies)){
														foreach($truckingCompanies as $val){
															if ($val['status'] == 'Active') {
																echo '<option value="'.$val['id'].'"';
																if($this->input->post('truckingCompany')==$val['id']) { echo ' selected="selected" '; }
																echo '>'.$val['company'].'</option>';
															}
														}
													}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-4 hide d-none">	     
										<div class="form-group">
											<label for="contain">Driver</label>
											<select class="form-control" name="driver" required readonly>
												<option value="">Select Driver</option>
												<?php 
													if(!empty($drivers)){
														foreach($drivers as $val){
															echo '<option value="'.$val['id'].'"';
															if('45'==$val['id']) { echo ' selected="selected" '; }
															echo '>'.$val['dname'].'</option>';
														}
													}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-3 ">
										<div class="form-group">
											<label>Booked Under</label>
											<select class="form-control" name="bookedUnderNew" required>
												<option value="">Select Booked Under</option>
												<?php 
													if(!empty($booked_under)){
														foreach($booked_under as $val){
															echo '<option value="'.$val['id'].'"';
															if($this->input->post('bookedUnder')==$val['id']) { echo ' selected="selected" '; }
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
											<select class="form-control invoicePDF" name="invoicePDF" required>
												<option>Select Shipment Type</option>
												<option>Warehousing</option>
											</select>
										</div>
									</div>
									<div class="col-sm-3 ">
										<div class="form-group">
											<label>Type Of Service</label>
												<select class="form-control" name="warehouseServices" id="warehouseServices">
												<option value="">Select Service</option>
												<?php 
													if(!empty($warehouseServices)){
														foreach($warehouseServices as $val){
															echo '<option value="'.$val['id'].'">'.$val['title'].'</option>';
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
													<option value="">Select Trip</option>
													<?php for($i=1;$i<16;$i++){ 
														echo '<option value="'.$i.'"';
															if($premadetrip['trip']==$i) { echo ' selected="selected" '; }
															echo '>'.$i.'</option>';
														} 
													?>
												</select>
												<input name="pudate" id="pudate" type="date" class="form-control datepicker" required value="<?php if($this->input->post('pudate')!='') { echo $this->input->post('pudate'); } else { echo $premadetrip['pudate']; } ?>">
											</div>
										</div>
										<div class="col-sm-4 ">
											<div class="form-group">
												<label>End Date</label>
												<input name="edate" type="date" class="form-control datepicker" required value="<?php if($this->input->post('edate')!='') { echo $this->input->post('edate'); } else { echo $premadetrip['edate']; } ?>">
											</div>
										</div>
										<div class="col-sm-4"></div>
										<!-- <div class="col-sm-4">
											<div class="form-group">
												<label for="contain">Appointment Type</label>
												<select class="form-control appointmentTypeP" name="appointmentTypeP" id="appointmentTypeP" required>
													<option value="">Select Appointment Type</option>
													<option value="Appointment">By Appointment</option>
													<option value="FCFS">First Come First Serve (FCFS)</option>
												</select>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="contain">Pick Up Time</label>	
												<div class="input-group mb-2">
													<input readonly name="ptime" type="text" style="pointer-events: none;" class="form-control timeInput" id="ptime" value="<?php if($this->input->post('ptime')!='') { echo $this->input->post('ptime'); } else { echo $premadetrip['ptime']; } ?>">
													<div class="tDropdown"></div>
													<div class="input-group-append">
														<div class="input-group-text timedd" style="width: 32px;padding: 2px; background:white;">=<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
													</div>
												</div>
											</div>
										</div> -->
										<div class="col-sm-4">
											<div class="form-group">
												<label for="contain">Company (Location)</label>
												<div class="getAddressParent">
													<input type="text" id="plocation" class="form-control getAddress companyI" data-type="company" name="plocation" required value="<?php if($this->input->post('plocation')!='') { echo $this->input->post('plocation'); } else { echo $premadetrip['plocation']; } ?>">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="contain">City</label>
												<div class="getAddressParent">
													<input type="text" id="pcity" class="form-control getAddress cityI" data-type="city" name="pcity" required value="<?php if($this->input->post('pcity')!='') { echo $this->input->post('pcity'); } else { echo $premadetrip['pcity']; } ?>">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="contain">Address</label>
												<div class="getAddressParent">
													<input type="hidden" name="paddressid" class="addressidI" value="<?php if($this->input->post('paddressid')!='') { echo $this->input->post('paddressid'); } else { echo $premadetrip['paddressid']; } ?>">
													<input type="text" id="paddress" class="form-control getAddress addressI" data-type="address" name="paddress" value="<?php if($this->input->post('paddress')!='') { echo $this->input->post('paddress'); } else { echo $premadetrip['paddress']; } ?>"> 
												</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label for="contain">Quantity</label>
												<input type="text" class="form-control" name="quantityP" value="">
											</div>
										</div>
										
										<div class="col-sm-2">
											<div class="form-group">
												<label for="contain">Weight</label>
												<input required type="text" class="form-control weight" name="weightP" id="" value="">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="contain">Commodity</label>
												<input required type="text" class="form-control" name="commodityP" value="">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="contain">Description</label>
												<textarea class="form-control" style="height: 49px; border-radius: 6px;" name="metaDescriptionP"></textarea>
											</div>
										</div>
										<div class="col-sm-12 pnotes">
											<div class="form-group">
												<label for="contain">Notes</label> 
												<textarea name="pnotes" style="height: 49px; border-radius: 6px;" class="form-control"><?php if($this->input->post('pnotes')!='') { echo $this->input->post('pnotes'); } else { echo $premadetrip['pnotes']; } ?></textarea>
											</div>
										</div>								
											
									<?php
										$pcodeValue = $this->input->post('pcode');
										

										if (empty($pcodeValue)) { 
											$pcode = array(' '); 
										} elseif (is_string($pcodeValue)) {
											$pcode = explode('~-~', $pcodeValue); 
										} else {
											$pcode = array(); 
										}

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
							
					<div class="pickupExtra"></div>
					<!-- drop off -->
					<!-- <fieldset>
						<div class="floating-wrapper ">
							<div class="card-header-floating">
								<legend>
									<input type="text" style="height: 35px; width:91.5%" class="form-control" name="dropoff" value="<?php if($this->input->post('dropoff')!='') { echo $this->input->post('dropoff'); } else { echo 'Drop Off'; } ?>">
								</legend>
								<div class="card-button-floating">
									<button class="btn btn-primary btn-sm pick-drop-btn drop-btn-add" type="button">Add New +</button>
								</div>
							</div>
							<div class="card border shadow no-overflow">
								<div class="card-body">
									<div class="row">
										<div class="col-sm-4 ">
											<div class="form-group">
												<label>Date</label>
												<input name="dodate" type="date" class="form-control datepicker" value="<?php if($this->input->post('dodate')!='') { echo $this->input->post('dodate'); } else { echo $premadetrip['dodate']; } ?>" required>
											</div>
										</div>
										<div class="col-sm-4 ">
											<div class="form-group">
												<label>Appointment Type</label>
												<select class="form-control appointmentTypeD" name="appointmentTypeD" id="appointmentTypeD" required>
													<option value="">Select Appointment Type</option>
													<option value="Appointment">By Appointment</option>
													<option value="FCFS">First Come First Serve (FCFS)</option>
												</select>
											</div>
										</div>
										<div class="col-sm-4 ">
											<div class="form-group">
												<label>Time</label>
												<div class="input-group mb-2">
												<input name="dtime"  id="dtime" type="text" class="form-control timeInput" value="<?php if($this->input->post('dtime')!='') { echo $this->input->post('dtime'); } else { echo $premadetrip['dtime']; } ?>" readonly required>
												<div class="tDropdown"></div>
												<div class="input-group-append">
													<div class="input-group-text timedd" style="width: 32px;padding: 2px; background: white;"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
												</div>
											</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4 ">
											<div class="form-group">
												<label>Company (Location)</label>
											<div class="getAddressParent">
												<input type="text" id="dlocation" class="form-control getAddress companyI" data-type="company" name="dlocation" required value="<?php if($this->input->post('dlocation')!='') { echo $this->input->post('dlocation'); } else { echo $premadetrip['dlocation']; } ?>">
											</div>
											</div>
										</div>
										<div class="col-sm-4 ">
											<div class="form-group">
												<label>City</label>
												<div class="getAddressParent">
													<input type="text" id="dcity" class="form-control getAddress cityI" data-type="city" name="dcity" required value="<?php if($this->input->post('dcity')!='') { echo $this->input->post('dcity'); } else { echo $premadetrip['dcity']; } ?>">
												</div>
											</div>
										</div>
										<div class="col-sm-4 ">
											<div class="form-group">
												<label>Address</label>
												<div class="getAddressParent">
													<input type="hidden" name="daddressid" class="addressidI" value="<?php if($this->input->post('daddressid')!='') { echo $this->input->post('daddressid'); } else { echo $premadetrip['daddressid']; } ?>"> 
													<input type="text" id="daddress" class="form-control getAddress addressI" data-type="address" name="daddress" value="<?php if($this->input->post('daddress')!='') { echo $this->input->post('daddress'); } else { echo $premadetrip['daddress']; } ?>"> 
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-2 ">
											<div class="form-group">
												<label>Quantity</label>
												<input type="text" class="form-control" name="quantityD" value="" placeholder="Enter quantity">
											</div>
										</div>
										<div class="col-sm-2 ">
											<div class="form-group">
												<label>Weight</label>
												<input required type="text" class="form-control weight" name="weightD" placeholder="Enter Weight">
											</div>
										</div>
										<div class="col-sm-4 ">
											<div class="form-group">
												<label>Description</label>
												<textarea class="form-control" name="metaDescriptionD" style="height: 49px; border-radius: 6px;"></textarea>
											</div>
										</div>
										<div class="col-sm-4 driver-notes-code">
											<div class="form-group">
												<label>Notes</label>
												<textarea name="dnotes" class="form-control" style="height: 49px; border-radius: 6px;"><?php if($this->input->post('dnotes')!='') { echo $this->input->post('dnotes'); } else { echo $premadetrip['dnotes']; } ?></textarea>
											</div>
										</div>
									</div>
									<div class="row" id="dropoff-container">
										<?php
												$dcodeValue = $this->input->post('dcode');
												if (empty($dcodeValue)) { 
													$dcode = array(''); 
												} elseif (is_string($dcodeValue)) {
													$dcode = explode('~-~', $dcodeValue); 
												} else {
													$dcode = array(); 
												}

												for ($i = 0; $i < count($dcode); $i++) {
												if ($i > 0) {
													$class = ' dcode-id-' . $i;
													$button = '<button class="btn btn-danger code-delete" type="button" data-cls=".dcode-id-' . $i . '">
																	<i class="fas fa-trash"></i>
															</button>';
												} else {
													$class = '';
													$button = '<button class="btn btn-success dcode-add " type="button">
																	<i class="fas fa-plus"></i>
															</button>';
												}
												?>
												<div class="col-sm-2 mb-1 <?php echo $class; ?>">
													<div class="form-group">
														<label>Drop Off#</label>
														<div class="input-group">
															<input name="dcode[]" type="text" class="form-control" value="<?php echo $dcode[$i]; ?>">
															<div class="input-group-append">
																<?php echo $button; ?>
															</div>
														</div>
													</div>
												</div>
											<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</fieldset> -->
					<!-- <div class="dropoffExtra" id="sortable"></div> -->
				
					<!-- Dispatch Info  -->
					<fieldset>
						<div class="floating-wrapper ">
							<div class="card-header-floating">
								<div class="card-title-floating">Dispatch Info</div>
								<div class="card-button-floating">
									<button class="btn btn-primary btn-sm pick-drop-btn dispatchInfo-btn" type="button">Add Dispatch Info+</button>
								</div>
							</div>
							<div class="card border shadow">
								<div class="card-body">
									<div class="row dispatchInfo-cls">
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				
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
											<input readonly name="rate" step="0.01" type="number" min="0" class="form-control rateInput rate-cls" value="<?php if($this->input->post('rate')!='') { echo $this->input->post('rate'); } else { echo $premadetrip['rate']; } ?>">
										</div>
									</div>
								</div>
								<div class="row carrier-expense-cls">

								</div>
							</div>
						</div>
					</div>
				
					<!-- Expenses  -->
					<div class="floating-wrapper">
						<div class="card-header-floating">
							<div class="card-title-floating">Customer Expenses</div>
							<div class="card-button-floating">
								<button class="btn btn-primary btn-sm pick-drop-btn expense-btn" type="button">Add Expenses +</button>
								<button class="btn btn-secondary btn-sm expense-custom-btn" type="button">Add Custom Expense +</button>
							</div>
						</div>
						<div class="card border shadow">
							<div class="card-body">
								<div class="row">
									<div class="col-sm-3 ">
										<div class="form-group">
											<label>Invoice Amount</label>
											<input readonly name="parate" step="0.01" type="number" data-price="0" min="0" class="form-control parate rate-cls" value="<?php if($this->input->post('parate')!='') { echo $this->input->post('parate'); } else { echo $premadetrip['parate']; } ?>">
										</div>
									</div>
									<div class="col-sm-3 ">
										<div class="form-group">
											<label>Margin</label>
											<input name="pamargin" readonly type="number" step="0.01" class="form-control pamargin" value="<?php if($this->input->post('pamargin')!='') { echo $this->input->post('pamargin'); } ?>">
										</div>
									</div>
								</div>
								<div class="row expense-cls">
									<?php
										$e = 1;
										$paRate = $premadetrip['parate'];
										if ($premadetrip['dispatchMeta']) {
											$dispatchMeta = json_decode($premadetrip['dispatchMeta'], true);
											foreach ($dispatchMeta['expense'] as $expVal) {
												$e++;
												if (in_array($expVal[0], $expenseN)) {
													$paRate += $expVal[1];
												} else {
													$paRate -= $expVal[1];
												}
											?>
											<div class="col-sm-3 mb-1 expense-div-<?= $e ?>">
												<div class="form-group d-flex m-0">
													<select name="expenseName[]" class="form-control expenseNameSelect expenseName-<?= $e ?>">
														<?php foreach ($expenses as $exp) {
															$selected = ($exp['id'] == $expVal[0]) ? 'selected' : '';
															echo '<option value="' . $exp['id'] . '" ' . $selected . '>' . $exp['title'] . '</option>';
														} ?>
													</select>
													<div class="input-group-append ml-2">
														<button class="btn btn-danger btn-sm pick-drop-remove-btn" type="button" data-removecls=".expense-div-<?= $e ?>">
															<i class="fas fa-trash-alt"></i>
														</button>
													</div>
												</div>
												<div class="input-group">
													<input name="expensePrice[]" data-cls=".expenseName-<?= $e ?>" required type="number" min="1" class="form-control expenseAmt" value="<?= $expVal[1] ?>" step="0.01">
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
					<!-- Company Tracking -->
					<div class="floating-wrapper mt-4">
						<div class="card-header-floating">
							<div class="card-title-floating">Company Tracking</div>
						</div>
						<div class="card border shadow  no-overflow">
							<div class="card-body">
								<div class="row">
									<div class="col-sm-4 ">
										<div class="form-group">
											<label>Company</label>
										<div class="getCompanyParent">
												<input type="text" id="companies" class="form-control getCompany" name="company" required value="<?php if($this->input->post('company')!='') { echo $this->input->post('company'); } else { echo $premadetrip['company']; } ?>">
											</div>
										</div>
									</div>
									<div class="col-sm-4 ">
										<div class="form-group">
											<label>Tracking Number</label>
											<input required name="tracking" type="text" class="form-control" value="<?php if($this->input->post('tracking')!='') { echo $this->input->post('tracking'); } else { echo $premadetrip['tracking']; } ?>">
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
											<input readonly name="invoice" type="text" class="form-control" value="<?php if($this->input->post('invoice')!='') { echo $this->input->post('invoice'); } else { echo $premadetrip['invoice']; } ?>">
										</div>
									</div>
									<div class="col-sm-4 ">
										<div class="form-group">
											<label>Invoice Date</label>
											<input name="invoiceDate" type="date" class="form-control datepicker" value="<?php if($this->input->post('invoiceDate')!='') { echo $this->input->post('invoiceDate'); } ?>">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Customer -->
					<div class="floating-wrapper">
						<div class="card-header-floating">
							<div class="card-title-floating">Customer Details</div>
						</div>
						<div class="card border shadow">
							<div class="card-body">
							<div class="row">
									<div class="col-sm-4 ">
										<div class="d-flex justify-content-between align-items-center mb-2">
											<div class="form-check mb-0">
												<input type="checkbox" class="form-check-input" id="customControlInline" name="inwrd" value="AK" <?php if($this->input->post('inwrd')=='AK') { echo ' checked'; } ?>>
												<label class="form-check-label" for="inward">Inward RD</label>
											</div>
										</div>
										<div class="custom-file mb-2">
											<input name="inwrd_d[]" type="file" class="custom-file-input" multiple>
											<label class="custom-file-label" for="fileInward">Choose File</label>
										</div>
									</div>
									<div class="col-sm-4 ">
										<div class="d-flex justify-content-between align-items-center mb-2">
											<div class="form-check mb-0">
												<input type="checkbox" class="form-check-input" id="customControlInlineOutwrdDD" name="outwrd" value="AK" <?php if($this->input->post('outwrd')=='AK') { echo ' checked'; } ?>>
												<label class="form-check-label" for="inward">Outward DD</label>
											</div>
										</div>
										<div class="custom-file mb-2">
											<input name="outwrd_d[]" type="file" class="custom-file-input" multiple>
											<label class="custom-file-label" for="fileInward">Choose File</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="d-flex justify-content-between align-items-center mb-2">
											<div class="form-check mb-0">
												<input type="checkbox" name="gd" class="form-check-input" id="customControlInlinegd" value="AK" <?php if($this->input->post('gd')=='AK') { echo ' checked'; } ?>>
												<label class="form-check-label" for="inward">Payment Proof</label>
											</div>
										</div>
										<div class="custom-file mb-2">
											<input name="gd_d" type="file" class="custom-file-input">
											<label class="custom-file-label" for="fileInward">Choose File</label>
										</div>
									</div>
									<!-- <div class="col-sm-3 ">
										<div class="d-flex justify-content-between align-items-center mb-2">
											<div class="form-check mb-0">
												<label class="form-check-label" for="inward">Invoice</label>
											</div>
										</div>
										<div class="custom-file mb-2">
											<input type="file" class="custom-file-input">
											<label class="custom-file-label" for="fileInward">Choose File</label>
										</div>
									</div> -->
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
										<div class="d-flex justify-content-between align-items-center mb-2">
											<div class="form-check mb-0">
												<input type="hidden" value="0" name="carrierInvoiceCheck">
												<input type="checkbox" class="form-check-input" id="carrierInvoiceCheck" name="carrierInvoiceCheck" value="1">
												<label class="form-check-label" for="carrierInvoiceCheck"> Invoice</label>
											</div>
										</div>
										<div class="custom-file mb-2">
											<input name="carrierInvoice[]" type="file" class="custom-file-input">
											<label class="custom-file-label" for="fileInward">Choose File</label>
										</div>
									</div>
									<div class="col-sm-4 ">
										<div class="d-flex justify-content-between align-items-center mb-2">
											<div class="form-check mb-0">
												<input type="hidden" value="0" name="carrier_gd">
												<input type="checkbox" class="form-check-input" id="carrierControlInlinegd" name="carrier_gd" value="1" >
												<label class="form-check-label" for="carrierControlInlinegd">Payment Proof</label>
											</div>
										</div>
										<div class="custom-file mb-2">
											<input name="carrier_gd_d[]" type="file" class="custom-file-input" multiple>
											<label class="custom-file-label" for="fileInward">Choose File</label>
										</div>
									</div>
										<div class="col-sm-4 ">
										<div class="form-check mb-2">
											<input type="hidden" value="0" name="carrierPayoutCheck">
											<input type="checkbox" class="form-check-input" id="carrierPayoutCheck" name="carrierPayoutCheck" value="1">
											<label class="form-check-label" for="closed">Payout Date</label>
										</div>
										<div class="form-group">
											<input name="carrierPayoutDate"  type="date" class="form-control datepicker" value="">
										</div>
									</div>

								</div>
							
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
												<option value="Pending">Select Driver Status</option>
												<?php
													foreach($shipmentStatus as $ds){
														echo '<option value="'.$ds['title'].'">'.$ds['title'].'</option>';
													}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-8">
										<div class="form-group">
											<label>Remarks</label>
										<input name="status" type="text" class="form-control" value="<?php if($this->input->post('status')!='') { echo $this->input->post('status'); } else { echo $premadetrip['status']; } ?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label>Notes</label>
											<textarea name="notes" class="form-control"><?php if($this->input->post('notes')!='') { echo $this->input->post('notes'); } else { echo $premadetrip['notes']; } ?></textarea>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label>Invoice Description</label>
											<textarea required name="invoiceNotes" class="form-control invoiceNotes"><?php echo $disp['invoiceNotes'] ?></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12  ">
										<button class="btn btn-success btn-sm p-2 mt-2 " name="save" type="submit" value="Add Dispatch">Add PA Warehousing</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
    </div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<script>
	$(document).ready(function() {
      $('.select2').select2();
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
						// console.log(shipmentType);
						if (shipmentType == 'Drayage') {
							$('#dispatchInfoCarrierRefNo').val('');
                  		} else {
							$('#dispatchInfoCarrierRefNo').val(data.invoice);
                    	}
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

</script>

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
    		fieldset.find('.addressI').val($(this).attr('data-address')+' '+$(this).attr('data-city')+' '+$(this).attr('data-zip'));
    		fieldset.find('.companyI').val($(this).attr('data-company'));
    		fieldset.find('.cityI').val($(this).attr('data-city'));
    		fieldset.find('.addressidI').val($(this).attr('data-id'));
    		fieldset.find('.addressList').html('').remove();
		});
		
		//// company address
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
    		fieldset.find('.getCompany').val($(this).attr('data-company'));
    		fieldset.find('.companyList').html('').remove();
		});
		$('body').on('keydown', '.getCompany', function () {
			clearTimeout(typingTimer);
		});
		
		$('body').on('focus',".datepicker", function(){
			$(this).datepicker({dateFormat: 'yy-mm-dd'});
		});
		$(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
        });
		
		$( "#sortable" ).sortable();
		
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
				let $this = $(this); 
				clearTimeout(timeoutID); 
				timeoutID = setTimeout(function(){
					let surcharge = $this; 
					let samt = surcharge.val();
					if(samt == '' || samt == 'undefined' || samt == 'NaN' || isNaN(samt)) { samt = 0; }
					
					$(".carrierExpenseAmt").each(function(index) {
						let insideCls = $(this).attr('data-cls');
						if($(insideCls).val() == 'Line Haul') {
							let amt = $(this).val();
							if(amt == '' || amt == 'undefined' || amt == 'NaN' || isNaN(amt)) { amt = 0; }
							let result = (samt / 100) * amt;
							surcharge.val(parseFloat(result).toFixed(2));
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
				var parate = $('.parate').val();
				if(parate=='' || parate=='NaN'){ parate = 0; }
				let pamargin = parseFloat(parate) - parseFloat(rateInput); 
				$('.pamargin').val(pamargin.toFixed(2));
			});
		});
	
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
		var dcid = 9999;
		 
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
					<input name="expensePrice[]" data-cls=".expenseName-${dcid}" required type="number" min="1" step="0.01" class="form-control expenseAmt" value="0">
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
			var pickup = '<div class="col-sm-2 pcode-id-'+dcid+'"><div class="form-group"><label for="contain">Pick Up#</label><div class="input-group mb-2"><input name="pcode[]" type="text" required class="form-control" value=""><div class="input-group-append"><div class=""><button class="btn btn-danger code-delete" type="button" data-cls=".pcode-id-'+dcid+'" style="height:100%;"><i class="fa fa-trash"></i></button></div></div></div></div></div>';
			$('.pickup-no').before(pickup);
			dcid++;
		}); 

		
		$('body').on('click', '.pcode1-add', function () {
			const index = $(this).data('index'); 
			const name = $(this).data('name');   
			const pickup = `
				<div class="col-sm-2 mb-1 pcode1-id-${dcid}">
					<div class="form-group">
						<label>Pick Up#</label>
						<div class="input-group">
							<input name="pcode1[${name}][]" type="text" required class="form-control" value="">
							<div class="input-group-append">
								<button type="button" class="btn btn-danger code-delete" data-cls=".pcode1-id-${dcid}">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</div>
					</div>
				</div>`;
			$(`.pickup-row-${index}`).append(pickup);
			dcid++;
		});

		
		$(document).on('click', '.dcode-add', function () {
			let dropoff = `
				<div class="col-sm-2 mb-1 dcode-id-${dcid}">
					<div class="form-group">
						<label>Drop Off#</label>
						<div class="input-group">
							<input name="dcode[]" type="text" required class="form-control" value="">
							<div class="input-group-append">
								<button class="btn btn-danger code-delete" type="button" data-cls=".dcode-id-${dcid}">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</div>
					</div>
				</div>`;
			
			$('#dropoff-container').append(dropoff);
			dcid++;
		});

		$('body').on('click', '.dcode1-add', function () {
			const index = $(this).data('index'); 
			const name = $(this).data('name');   
			const dropoff = `
				<div class="col-sm-2 dcode1-id-${dcid}">
					<div class="form-group">
						<label>Drop Off#</label>
						<div class="input-group mb-2">
							<input name="dcode1[${name}][]" type="text" required class="form-control" value="">
							<div class="input-group-append">
								<button type="button" class="btn btn-danger code-delete" data-cls=".dcode1-id-${dcid}">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</div>
					</div>
				</div>`;
				$(`.dropoff-row-${index}`).append(dropoff);
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
			calculateCarrierRate();
		});
		
		var pid = 2;
		var did = 2;
		
		$('select.invoicePDF').change(function(){
			let valu = $(this).val();
			if(valu == 'Drayage'){
				$('.Trucking').hide();
				$('.Drayage').show();
				$('.erInformation').show();
				$('#invoiceDrayage').prop('required', true);
				$('#erInformation').prop('required', true);
				$('#carrierRefNo, #poNo').hide();
				$('#dispatchInfoCarrierRefNo').prop('required', false);
				$('#dispatchInfoPoNo').prop('required', false);

				// $('.carrierRefInput, .poInput').prop('required', false);
			}
			else if(valu == 'Trucking'){
				$('.Trucking').show();
				$('.Drayage').hide();
				$('.erInformation').hide();
				$('#invoiceTrucking').prop('required', true);
				$('#invoiceDrayage').prop('required', false);
				$('#erInformation').prop('required', false);
				$('#carrierRefNo, #poNo').show();
				$('#dispatchInfoCarrierRefNo').prop('required', true);
				$('#dispatchInfoPoNo').prop('required', true);
			}
			else {
				$('.Trucking').hide();
				$('.Drayage').hide();
				$('.erInformation').hide();
				$('#invoiceDrayage').prop('required', false);
				$('#erInformation').prop('required', false);
				$('#invoiceTrucking').prop('required', false);
				$('#carrierRefNo, #poNo').show();
				$('#dispatchInfoCarrierRefNo').prop('required', true);
				$('#dispatchInfoPoNo').prop('required', true);
			}
		});
		
		$('.parate').keyup(function(){
			let valu = $(this).val();
			$(this).attr('data-price',valu);
		});
		$('.parate').click(function(){
			let valu = $(this).val();
			$(this).attr('data-price',valu);
		});
		
		
	
		
	} );

	const discountTitles = <?php echo json_encode($expenseN); ?>;

function calculatePaRate() {
	let expenseAmt = 0;

	$(".expenseAmt").each(function() {
		let clsSelector = $(this).attr('data-cls');
		let amt = parseFloat($(this).val().replace(/[^0-9.-]/g, '')) || 0;

		let typeText = $(clsSelector + " option:selected").text().trim().toLowerCase();
		let isNegative = discountTitles.map(t => t.trim().toLowerCase()).includes(typeText);

		if (isNegative) {
			expenseAmt -= amt;
		} else {
			expenseAmt += amt;
		}
	});

	let paAmt = parseFloat($('.parate').attr('data-price')) || 0;
	let finalAmt = paAmt + expenseAmt;
	$('.parate').val(finalAmt.toFixed(2));

	let rateInput = parseFloat($('.rateInput').val()) || 0;
	let pamargin = finalAmt - rateInput;
	$('.pamargin').val(pamargin.toFixed(2));
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
			
			let pamargin = parseFloat(parateInput) - parseFloat(rateInput); 
			$('.pamargin').val(parseFloat(pamargin).toFixed(2));
			//$('.pamargin').val(Math.round(pamargin));

		}

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
		if (event.target.matches('[name="carrierInvoice[]"]')) {
			const checkbox = document.getElementById('carrierInvoiceCheck');
			if (event.target.value) {
				checkbox.setAttribute('required', 'required');
			} else {
				checkbox.removeAttribute('required');
			}
		}
	});
	
	// document.querySelector('#addPaLogisticform').addEventListener('submit', function (e) {
    //     const ptimeInput = document.querySelector('#ptime');
	// 	const dtimeInput = document.querySelector('#dtime');
	// 	const dtimeChildInputs = document.querySelectorAll('input[name="dtime1[]"]'); 

    //     if (ptimeInput.value.trim() === '') {
    //         e.preventDefault(); 
    //         alert('Pick Up Time is required!');
	// 		return;
    //     }
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

</script>

<style>
	.timeInput {padding:13px 15px;}
    .input-group{position:relative;}
  .tDropdown {position:absolute;width:calc(100% - 30px);max-height:240px;overflow-y:auto;background:white;border:1px solid #ccc;display:none;z-index:1000;top:98%;left:0;}
  .tDropdown div {padding: 5px;cursor: pointer;}
  .tDropdown div:hover {background: #f0f0f0;}
  
	.custom-control-label::before {width: 20px;height: 20px;}
	.custom-control-label::after {width: 20px;height: 20px;}
	form fieldset{position:relative;}
	fieldset .pick-drop-btn{position:absolute;right:30px;}
	.card .container-fluid, .card .mobile_content, .card .container{padding-left:0;padding-right:0;}
	  .card, .pt-content-body > .container-fluid {    padding: 10px;  }
	  #ui-datepicker-div {  z-index: 99999 !important;}
	
	.getAddressParent, .getCompanyParent{position:relative;}
	.addressList, .companyList{position:absolute;top:99%;left:0px;background: #eee;z-index: 999;width: 100%;border: 1px solid #aaa;}
	.addressList li, .companyList li {list-style: none;line-height: 23px;padding: 4px;cursor: pointer;}
	.addressList li:hover, .companyList li:hover {background:#fff;}

	.time-option.focused {
		background-color: #007bff;
		color: white;
	}
	
	.select2-container .select2-selection--single {
    	min-height: 50px !important;
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
