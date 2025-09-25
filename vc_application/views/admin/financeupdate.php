<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Finance <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/finance');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				     
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
		   <?php
                       
                  if(!empty($finance)){
                  $key = $finance[0];
                   
			$total_expence = $total_pay = $total_income = 0;
			
                    ?>  

						 <?php  echo validation_errors();?>
						 
					<div class="row">
					<div class="col-sm-6">
					<form method="post" action="<?php echo base_url('admin/finance/update/').$this->uri->segment(4);?>" class="row1">
					<div class="row">
					
					<div class="col-sm-12"> <strong>Pay Period <?php echo $key['fweek'];?></strong> </div>
					<?php 
					$unit_group = array(); 
					if(!empty($units)) {
							//$ukey = $units[0]['vehicle'].'-'.$units[0]['driver'];
							//$unit_group[$ukey] = array($units[0]['vname'],$units[0]['dcode'],$units[0]['rate']);
						foreach($units as $unit){
							$ukey = $unit['vehicle'].'-'.$unit['driver'];
							if(array_key_exists($ukey, $unit_group)) {
								$unit_group[$ukey][2] = $unit_group[$ukey][2] + $unit['rate'] + 0;
							} else {
								$unit_group[$ukey] = array($unit['vname'],$unit['dcode'],$unit['rate']);
							}
						}
					}
					if(!empty($unit_group)) {
							foreach($unit_group as $ukey=>$group){ 
							
							if($key['unit_pay'] > 0) { $unit_pay = $key['unit_pay']; }
							else { $unit_pay = $group[2]; }
							
							$total_income = $total_income + $unit_pay;
							
							?>
								<div class="col-sm-8">
									<div class="form-group">
										<?php echo $group[0].' ['.$group[1].'] ($'.$group[2].')'; ?>
									</div> 
								</div>  
								<div class="col-sm-4">
									<div class="form-group">
										<div class="input-group mb-2">
											<div class="input-group-prepend">
												<div class="input-group-text">$</div>
											</div>
											<input name="unit_pay" type="number" step="0.01" min="0" required value="<?php echo $unit_pay;?>" class="form-control unit_pay">
										</div>
									</div> 
								</div> 
							  <?php 
							  } 
					}
					?>
					 <div class="clearfix" style="width:100%"></div>
					 <div class="col-sm-12"> <strong>Expences</strong> </div>
					 
					<?php 
					$drivers = array(); 
					if(!empty($driver_pay)) {
						foreach($driver_pay as $unit){ 
							if(array_key_exists($unit['driver'], $drivers)) {
								$drivers[$unit['driver']][1] = $drivers[$unit['driver']][1] + $unit['rate'] + 0;
							} else {
								$drivers[$unit['driver']] = array($unit['dcode'],$unit['rate']);
							}
						}
					}
					if(!empty($drivers)) {
							foreach($drivers as $driver){ 
							
							if($key['driver_pay'] > 0) { $driverpay = $key['driver_pay']; }
							else { $driverpay = $driver[1]; }
							
							$total_expence = $total_expence + $driverpay;
							?>
								<div class="col-sm-8">
									<div class="form-group">
										<?php echo $driver[0]; ?> Driver Pay  ($<?php echo $driverpay;?>)
									</div> 
								</div>  
								<div class="col-sm-4">
									<div class="form-group">
										<div class="input-group mb-2">
											<div class="input-group-prepend">
												<div class="input-group-text">-$</div>
											</div>
											<input name="driver_pay" type="number" step="0.01" min="0" required value="<?php echo $driverpay;?>" class="form-control f-amt">
										</div>
									</div> 
								</div> 
							  <?php 
							  } 
					}
					?>
					
					<div class="clearfix" style="width:100%"></div>
					
					<?php 
					$first_time = 'true';
					if($key['expenses']!='') {
						$expence_arr = explode('--~~--',$key['expenses']);
						if($expence_arr[0]!='' && $expence_arr[1]!='') {
							$expence_txt = explode('-~-',$expence_arr[0]);
							$expence_val = explode('-~-',$expence_arr[1]);
							
							for($i=0;$i<count($expence_txt);$i++){
							  if($expence_txt[$i]!='') {
								  $first_time = 'false';
								  $total_expence = $total_expence + $expence_val[$i];
							?>
								<div class="col-sm-8">
									<div class="form-group">
										<input name="expenses_txt[]" type="text" value="<?php echo $expence_txt[$i];?>" class="form-control">
									</div> 
								</div>  
								<div class="col-sm-4">
									<div class="form-group">
										<input name="expenses_val[]" type="number" step="0.01" value="<?php echo $expence_val[$i];?>" class="form-control f-amt" min="0">
									</div> 
								</div>  
							  <?php 
							  }
							} 
						}
					} 
					?>
					<div class="clearfix" style="width:100%"></div>
					<?php
					if($first_time == 'true') {
						$expens_label = array('Fuel','Toll','Repair','Maintenance','Dispatch 5%','Factoring 3%');
						
						foreach($expens_label as $label) {
							if(!empty($unit_group)) {
								foreach($unit_group as $ukey=>$group){ 
								?>
									<div class="col-sm-8">
										<div class="form-group">
											<input type="text" value="<?php echo $label.' '.$group[0]; ?>" class="form-control" name="expenses_txt[]" required>
										</div> 
									</div>  
									<div class="col-sm-4">
										<div class="form-group">
										<?php 
										$value = 0;
										if($label == 'Dispatch 5%') {
											$value = (5/100) * $group[2];
											$value = round($value,2);
										}
										elseif($label == 'Factoring 3%') {
											$value = (5/100) * $group[2];
											$value = round($value,2);
										}
										$total_expence = $total_expence + $value;
										?>
											<input type="number" step="0.01" value="<?php echo $value;?>" min="0" required class="form-control f-amt" name="expenses_val[]">
										</div> 
									</div> 
								  <?php 
								  } 
							}
						}
					}
					?>
					<div class="clearfix" style="width:100%"></div>
					 
					<div class="col-sm-12"> <strong>Other Expences</strong> </div>
					
					<?php 
					if($key['other_option']!='') {
						$other_option_arr = explode('--~~--',$key['other_option']);
						if($other_option_arr[0]!='' && $other_option_arr[1]!='') {
							$other_option_txt = explode('-~-',$other_option_arr[0]);
							$other_option_val = explode('-~-',$other_option_arr[1]);
							 
							for($i=0;$i<count($other_option_txt);$i++){
							  if($other_option_txt[$i]!='') {
							?>
								<div class="col-sm-8 oed-<?php echo $i;?>">
									<div class="form-group">
										<input name="other_option_txt[]" type="text" value="<?php echo $other_option_txt[$i];?>" class="form-control">
									</div> 
								</div>  
								<div class="col-sm-4 oed-<?php echo $i;?>">
									<div class="form-group">
									  <?php if($i < 2) { ?>
										<input name="other_option_val[]" type="number" step="0.01" value="<?php echo $other_option_val[$i];?>" class="form-control f-amt" min="0">
									  <?php } else { ?>
										<div class="input-group mb-2">
											<input name="other_option_val[]" type="number" step="0.01" min="0" required value="<?php echo $other_option_val[$i];?>" class="form-control f-amt">
											<div class="input-group-append">
												<div class="input-group-text delete-div" style="color:#ff0000;cursor:pointer;" data-cls=".oed-<?php echo $i;?>"><i class="fas fa-trash-alt" title="Delete"></i></div>
											</div>
										</div>
									  <?php } ?>
									</div> 
								</div> 
							  <?php 
							  }
							} 
						}
					}
					else {
						?>
						<div class="col-sm-8">
							<div class="form-group">
								<input name="other_option_txt[]" type="text" value="Parking" class="form-control">
							</div> 
						</div>  
						<div class="col-sm-4">
							<div class="form-group">
								<input name="other_option_val[]" type="number" step="0.01" min="0" required value="0" class="form-control f-amt">
							</div> 
						</div>  
						<div class="col-sm-8">
							<div class="form-group">
								<input name="other_option_txt[]" type="text" value="Parking Temp" class="form-control">
							</div> 
						</div>  
						<div class="col-sm-4">
							<div class="form-group">
								<input name="other_option_val[]" type="number" step="0.01" min="0" required value="0" class="form-control f-amt">
							</div> 
						</div> 
						<?php
					}
					?>
					</div>
					<div class="row other-expense-div"></div>
					
					<div class="row">
					<div class="clearfix" style="width:100%"></div>
					<div class="col-sm-12"><button class="btn btn-success btn-sm add-other-expense" type="button">Add New Field</button></div>
					<div class="clearfix" style="width:100%"></div>
					
					<div class="col-sm-3">
						<div class="form-group">&nbsp;</div> 
					</div>  
					<div class="clearfix" style="width:100%"></div>
					
					<div class="col-sm-7">
						<div class="form-group">
							<strong>Total Income:</strong>
						</div> 
					</div>  
					<div class="col-sm-5">
						<div class="form-group">
							<div class="input-group mb-2">
								<div class="input-group-prepend">
									<div class="input-group-text">$</div>
								</div>
								<?php if($key['total_income'] > 0) { $total_income = $key['total_income']; } ?>
								<input type="number" step="0.01" name="total_income" min="0" required class="form-control total-income in-ex-input" value="<?php echo $total_income;?>">
							</div>  
						</div> 
					</div>  
					<div class="clearfix" style="width:100%"></div>
					
					<div class="col-sm-7">
						<div class="form-group">
							<strong>Total Expences:</strong>
						</div> 
					</div>  
					<div class="col-sm-5">
						<div class="form-group">
							<div class="input-group mb-2">
								<div class="input-group-prepend">
									<div class="input-group-text">-$</div>
								</div>
								<?php if($key['total_expense'] > 0) { $total_expence = $key['total_expense']; } ?>
								<input type="number" step="0.01" min="0" name="total_expense" required class="form-control total-expence in-ex-input" value="<?php echo $total_expence;?>">
							</div> 
						</div> 
					</div>  
					<div class="clearfix" style="width:100%"></div>
					
					<div class="col-sm-7">
						<div class="form-group">
							<strong>Total Pay:</strong>
						</div> 
					</div>  
					<div class="col-sm-5">
						<div class="form-group">
							<div class="input-group mb-2">
								<div class="input-group-prepend">
									<div class="input-group-text">$</div>
								</div>
								<?php 
								$total_pay = $total_income - $total_expence;
								if($key['total_amt'] > 0) { $total_pay = $key['total_amt']; } ?>
								<input type="number" step="0.01" name="total_amt" min="0" required class="form-control total-pay" value="<?php echo $total_pay;?>">
							</div>  
						</div> 
					</div>  
					<div class="clearfix"></div>	
					  
					
					 <div class="col-sm-12">    
						<div class="form-group">
                            <label for="contain">Notes</label>
                            <textarea name="notes" class="form-control"><?php echo $key['notes'];?></textarea>
                        </div> 
                     </div> 
                    <div class="col-sm-12"> 
                        <div class="form-group">
                            <input type="submit" name="save" value="Update" class="btn btn-success"/>
                        </div>
					</div>
					
					</div>
					</form>
				  
				  </div>
				  <div class="col-sm-6"></div>
				  </div>
				  <?php } ?>
				</div>
			</div>

		</div>	
			
		</div>	
		 
		 
		 
		
		
<script>
jQuery(document).ready(function(){
	var oeid = 9999;
	jQuery('.add-other-expense').click(function(){
		oeid++;
		var field = '<div class="col-sm-8 oed-'+oeid+'"><div class="form-group"><input name="other_option_txt[]" type="text" value="Field Name" class="form-control"></div> </div>  <div class="col-sm-4 oed-'+oeid+'"><div class="form-group"><div class="input-group mb-2"><input name="other_option_val[]" type="number" step="0.01" min="0" required value="0" class="form-control f-amt"><div class="input-group-append"><div class="input-group-text delete-div" style="color:#ff0000;cursor:pointer;" data-cls=".oed-'+oeid+'"><i class="fas fa-trash-alt" title="Delete"></i></div></div></div></div> </div> ';
		jQuery('.other-expense-div').append(field);
	});
	
	
	jQuery('.unit_pay').click(function(){
		var valu = jQuery(this).val();
		jQuery('.total-income').val(valu);
	});
	jQuery('.unit_pay').keyup(function(){
		var valu = jQuery(this).val();
		jQuery('.total-income').val(valu);
	});
	jQuery('.in-ex-input').click(function(){
		var income = jQuery('.total-income').val();
		var totalamt = jQuery('.total-expence').val();
		var total_pay = parseFloat(income) - parseFloat(totalamt);
		jQuery('.total-pay').val(total_pay.toFixed(2));
	});
	jQuery('.in-ex-input').keyup(function(){
		var income = jQuery('.total-income').val();
		var totalamt = jQuery('.total-expence').val();
		var total_pay = parseFloat(income) - parseFloat(totalamt);
		jQuery('.total-pay').val(total_pay.toFixed(2));
	});
	jQuery('html,body').on('click','.f-amt',function(){
		var amt = 0;
		jQuery('.f-amt').each(function( index ) {
			amt = amt + parseFloat(jQuery(this).val());
		});
		var totalamt = amt.toFixed(2);
		jQuery('.total-expence').val(totalamt);
		
		var income = jQuery('.total-income').val();
		var total_pay = parseFloat(income) - parseFloat(totalamt);
		jQuery('.total-pay').val(total_pay.toFixed(2));
	});
	jQuery('html,body').on('keyup','.f-amt',function(){
		var amt = 0;
		jQuery('.f-amt').each(function( index ) {
			amt = amt + parseFloat(jQuery(this).val());
		});
		var totalamt = amt.toFixed(2);
		jQuery('.total-expence').val(totalamt);
		
		var income = jQuery('.total-income').val();
		var total_pay = parseFloat(income) - parseFloat(totalamt);
		jQuery('.total-pay').val(total_pay.toFixed(2));
	});
	jQuery('html,body').on('click','.delete-div',function(){
		var cls = jQuery(this).attr('data-cls');
		jQuery(cls).html('');
		jQuery(cls).remove();
		
		var amt = 0;
		jQuery('.f-amt').each(function( index ) {
			amt = amt + parseFloat(jQuery(this).val());
		});
		var totalamt = amt.toFixed(2);
		jQuery('.total-expence').val(totalamt);
		
		var income = jQuery('.total-income').val();
		var total_pay = parseFloat(income) - parseFloat(totalamt);
		jQuery('.total-pay').val(total_pay.toFixed(2));
	});
});
</script>	
		