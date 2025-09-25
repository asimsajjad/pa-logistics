<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3>Invoice Pending</h3>
		<div class="add_page" style="float: right;">
		</div>
	</div>
	
	
	<div class="card-bodys table_style">
		
		
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
		
		<div class="table-responsive pt-datatbl pt-invoice-page" style="overflow-y: auto;max-height: 90vh;">
			<table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Sr no.</th>
						<th>PU Date</th>
						<th>Invoice #</th>
						<th>Company</th>
						<th>Tracking #</th>
						<th>Rate</th>
						<th>Invoice Amt</th>
						<th>Payout Amt</th>
						<th>Week</th>
						<th>Invoice Date</th>
						<th>Invoice Type</th>
						<th>BOL</th>
						<th>RC</th>
						<th>$</th>
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
								    $bgcolor = 'bgcolor="#CC4B4B"';
								}
								//echo 'stristr('.$key['dispatchMeta'].',"invoicePaid":"0") && '.$key['expectPayDate']."!='0000-00-00' && strtotime(".$key['expectPayDate'].') > strtotime('.date('Y-m-d').')';
							?>
							<tr class="tr-<?php echo $key['id'];?>" <?=$bgcolor?>>
								<td><?php echo $n;?></td>
								<td><a href="<?php echo base_url().'admin/'.$dispatchURL.'/update/'.$key['id'];?>"><?php echo date('m-d-Y',strtotime($key['pudate']));?></a></td>
								<td><?php //echo $key['invoice'];?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_invoice_txt_<?php echo $key['id'];?>"><?php echo $key['invoice'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_invoice_input_<?php echo $key['id'];?> current_input" data-id="#invoice_input" value="<?php echo $key['invoice'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								<td><?php //echo $key['company'];
									$payoutRate = 0;
									if(!empty($companies)){
										foreach($companies as $val){
											if($key['company']==$val['id']) { 
												echo $val['company']; 
												$payoutRate = $val['payoutRate'];
											}
										}
									}
									if(!is_numeric($payoutRate)) { $payoutRate = 0; }
								?></td> 
								<td><?php echo $key['tracking'];?></td> 
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['rate'] > 0) { echo '$'; } echo '<span class="c_rate_txt_'.$key['id'].' rateTxt">'.$key['rate'].'</span>';?> &nbsp; <i class="fas fa-edit d-none" data-id="<?php echo $key['id'];?>" title="Edit" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_rate_input_<?php echo $key['id'];?> current_input" data-id="#rate_input" value="<?php echo $key['rate'];?>" onkeyup="this.value=this.value.replace(/[^\d.]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['parate'] > 0) { echo '$'; } echo '<span class="c_parate_txt_'.$key['id'].' paRateTxt">'.$key['parate'].'</span>';?> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_parate_input_<?php echo $key['id'];?> current_input" data-id="#parate_input" value="<?php echo $key['parate'];?>" onkeyup="this.value=this.value.replace(/[^\d.]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td>  
								
								<td><?php //$key['payoutAmount']?>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><?php if($key['payoutAmount'] > 0) { echo '$'; } echo '<span class="c_payoutAmount_txt_'.$key['id'].' payoutAmount">'.$key['payoutAmount'].'</span>';?> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_payoutAmount_input_<?php echo $key['id'];?> current_input" data-id="#payoutAmount_input" value="<?php echo $key['payoutAmount'];?>" onkeyup="this.value=this.value.replace(/[^\d.]/,'')">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td>
								
								<td><?=$key['dWeek']?></td>
								
								<td><?php //$key['invoiceDate']?>
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
								
								<td><?php //$key['invoiceType']?>
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
								</td>
								
								<td bgcolor="<?php if($key['bol']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php if($key['bol'] !='') { echo 'Yes'; }?></td> 
								
								<td bgcolor="<?php if($key['rc']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php echo $key['rc'];?></td> 
								
								<td bgcolor="<?php if($key['gd']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php echo $key['gd'];?></td> 
								
								<td>
									<span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_status_txt_<?php echo $key['id'];?> "><?php echo $key['status'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span>
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
									<a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/'.$dispatchURL.'/update/'.$key['id'];?>">Edit Dispatch <i class="fas fa-edit" title="Edit" alt="Edit"></i></a>  
									<br>
									<?php //if($this->input->post('dispatchType') == 'paDispatch') {  ?>
										<a class="btn btn-sm btn-primary pt-cta" href="<?php echo base_url().'admin/'.$dispatchURL.'/update/'.$key['id'];?>?invoice">Edit Invoice</a>
										<a class="btn btn-sm btn-primary pt-cta" href="<?php echo base_url('Invoice/downloadInvoicePDF/'.$key['id']);?>">Generate Invoice</a>
									<?php  //} ?>
								</td>
							</tr> 
							
							<?php
								$n++;
							}
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
	$( function() {
		$( ".datepicker" ).datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
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
			$('.c_payoutAmount_txt_'+tdid).html($('#payoutAmount_input').val());
			$('.c_invoiceDate_txt_'+tdid).html($('#invoiceDate_input').val());
			$('.c_expectPayDate_txt_'+tdid).html($('#expectPayDate_input').val());
			$('.c_invoiceType_txt_'+tdid).html($('#invoiceType_input').val());
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
			$( ".paRateTxt" ).each(function( index ) {
				var currentPrice = $(this).html();
				parate = parate + parseInt(currentPrice);
			});
			$( ".rateTxt" ).each(function( index ) {
				var currentPrice = $(this).html();
				rate = rate + parseInt(currentPrice);
			});
			$('.paRateTotal').html(parate);
			$('.rateTotal').html(rate);
		}
		function beforeCalculateRate(){
			var parate = 0;  var rate = 0;
			
			$('.paRateTotal').html(parate);
			$('.rateTotal').html(rate);
		}
		
	});
</script>
