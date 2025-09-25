<style>
	form.form {margin-bottom:25px;}
	.td-input{display:none;}
	.fas {cursor: pointer;}
	.fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	a.btn {margin-bottom: 5px;}
</style>
<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3>Statement Of Account</h3> 
		<div class="add_page" style="float: right;">
		</div>
	</div>
	
	
	<div class="card-bodys table_style">
		
		<div class="d-block text-center">
			<form class="form form-inline" method="post" action="">
				<input type="text" required readonly placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $sdate = $this->input->post('sdate'); } else { $sdate = date('Y-m-d',strtotime("-4 months",strtotime(date('Y-m-d')))); } ?>" name="sdate" style="width: 120px;" class="form-control datepicker"> &nbsp;
				<input type="text" required style="width: 120px;" readonly placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $edate = $this->input->post('edate'); } else { $edate = date('Y-m-d'); } ?>" name="edate" class="form-control datepicker"> &nbsp;
				
				<?php 
					$aging = 0;
					if($this->input->post('aging') != '') { $aging = $this->input->post('aging'); }
					
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
				&nbsp;
				<select name="dispatchType" required class="form-control" style="max-width: 150px;">
					<option value="">Select Dispatch</option>
					<option value="paDispatch" <?php if($this->input->post('dispatchType') == 'paDispatch') { echo 'selected'; } ?>>PA Fleet Dispatch</option>
					<option value="outsideDispatch" <?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo 'selected'; } ?>>Outside Dispatch</option>
				</select> &nbsp;
				<select name="company" class="form-control" required style="max-width: 150px;">
					<option value="">All Companies</option>
					<?php 
						$companyArr = array();
						if(!empty($companies)){
							foreach($companies as $val){
								$companyArr[$val['id']] = $val['company'];
								echo '<option value="'.$val['id'].'"';
								if($this->input->post('company')==$val['id']) { echo ' selected '; }
								echo '>'.$val['company'].'</option>';
							}
						}
					?>
				</select> &nbsp;
				<select name="aging" class="form-control" style="max-width: 150px;">
					<option value="">Aging Days</option>
					<option value="30" <?php if($aging == '30') { echo 'selected'; } ?>>Less than 30</option>
					<option value="45" <?php if($aging == '45') { echo 'selected'; } ?>>30-45</option>
					<option value="60" <?php if($aging == '60') { echo 'selected'; } ?>>45-60</option>
					<option value="75" <?php if($aging == '75') { echo 'selected'; } ?>>60-75</option>
					<option value="90" <?php if($aging == '90') { echo 'selected'; } ?>>75-90</option>
					<option value="90+" <?php if($aging == '90+') { echo 'selected'; } ?>>90+</option>
				</select> &nbsp;
				<input type="submit" value="Search" name="search" class="btn btn-success pt-cta">
			</form>
		</div>
		
		
		<div class="table-responsive pt-datatbl" style="overflow-y: auto;max-height: 90vh;">
			<table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead style="position:sticky;top:0">
                    <tr>
						<th>Sr no.</th>
						<th>Invoice</th>
						<th>Company</th>
						<th>Delivery Date</th>
						<th>Invoice Date</th>
						<th>Carrier Rate</th>
						<th>Invoice Amt</th>
						<th>Payable Amt</th>
						<th>Aging Days</th>
                        <th>Action</th>
					</tr> 
				</thead>
				
				<tbody>
					<?php
						if(!empty($dispatch)){
							$n=1; $rate = $parate = 0;
							foreach($dispatch as $key) {
							    $agingTxt = '';
								$showAging = 'false';
								$aDays = 0;
								if($key['invoiceType']=='Direct Bill'){  $aDays = 30; }
								elseif($key['invoiceType']=='Quick Pay'){ $aDays = 7; }
								elseif($key['invoiceType']=='RTS'){ $aDays = 3; }
								
								if($key['invoiceDate'] != '0000-00-00'){ $showAging = 'true'; }
								if($dispatchMeta['invoicePaidDate'] != ''){ $showAging = 'false';  }
								if($dispatchMeta['invoiceCloseDate'] != ''){ $showAging='false';  }
								if($showAging == 'true'){
									$date1 = new DateTime($key['invoiceDate']);
									$date2 = new DateTime(date('Y-m-d'));
									$diff = $date1->diff($date2);
									$agingDay = $diff->days;
									
									//if($agingDay  > $aDays && $aDays > 0) { $agingTxt = ''.$agingDay.' Days'; }
									if($agingDay  > 30) { $agingTxt = '<strong style="color:red">'.$agingDay.' Days</strong>'; }
									else { $agingTxt = ''.$agingDay.' Days'; }
									
									if($aging == '30') { 
										if($agingDay > 30) { continue; }
									}
									if($aging == '45') { 
										if($agingDay < 30 || $agingDay > 45) { continue; }
									}
									if($aging == '60') { 
										if($agingDay < 45 || $agingDay > 60) { continue; }
									}
									if($aging == '75') { 
										if($agingDay < 60 || $agingDay > 75) { continue; }
									}
									if($aging == '90') { 
										if($agingDay < 75 || $agingDay > 90) { continue; }
									}
									if($aging == '90+') { 
										if($agingDay < 90) { continue; }
									}
								}
									
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
								if($this->input->post('dispatchType') == 'outsideDispatch') { 
									$dispatchURL = base_url('admin/outside-dispatch/update/').$key['id'];
								} else {	
									$dispatchURL = base_url('admin/dispatch/update/').$key['id'];
								}
							?>
							<tr class="tr-<?php echo $key['id'];?>" <?=$bgcolor?>>
								<td><?php echo $n;?></td>
								
								<td><a target="_blank" href="<?=$dispatchURL?>"><?php echo $key['invoice'];?></a></td>
								<td><?php //echo $key['company'];
									if(array_key_exists($key['company'],$companyArr)){ 
										echo $companyArr[$key['company']]; 
									}
								?></td> 
								<td><?php echo date('m-d-Y',strtotime($key['dodate']));?></td>
								<td><?php echo date('m-d-Y',strtotime($key['invoiceDate']));?></td>
								
								<td>$<span class="rateTxt"><?php echo $key['rate']?></span></td> 
								
								<td>$<span class="paRateTxt"><?php echo $key['parate']?></span></td>  
								
								<td>$<?php echo number_format($key['payableAmt'], 2, '.', '')?></td>
								<td><?=$agingTxt?></td>
								
								<td>
									<?php if($this->input->post('dispatchType') == 'paDispatch') {  ?>
										<a class="btn btn-sm btn-primary" href="<?php echo base_url('Invoice/downloadStatementPDF/'.$key['company']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>">Statement</a>
									<?php  } 
									if($this->input->post('dispatchType') == 'outsideDispatch') {  ?>
										<a class="btn btn-sm btn-primary" href="<?php echo base_url('Invoice/downloadStatementPDF/'.$key['company']);?>?dTable=dispatchOutside&sdate=<?=$sdate?>&edate=<?=$edate?>">Statement All</a> 
										<a class="btn btn-sm btn-primary" href="<?php echo base_url('Invoice/downloadStatementPDF/'.$key['company']);?>?dTable=dispatchOutside&sdate=<?=$sdate?>&edate=<?=$edate?>&type=Trucking">Statement (Trucking)</a> 
										<a class="btn btn-sm btn-primary" href="<?php echo base_url('Invoice/downloadStatementPDF/'.$key['company']);?>?dTable=dispatchOutside&sdate=<?=$sdate?>&edate=<?=$edate?>&type=Drayage">Statement (Drayage)</a>
									<?php  } ?>
                    									
                    				<div class="dropdown" style="margin-left:10px;">
                    					<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Download
                    					</button>
                    					<div class="dropdown-menu" aria-labelledby="dropdownMenu2" style="padding: 10px;text-align: center;">
                    					    <a class="btn btn-sm btn-success" href="<?php echo base_url('Invoice/downloadStatementPDF/'.$key['company']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&generateCSV<?php if($this->input->post('dispatchType') == 'outsideDispatch'){ echo '&dTable=dispatchOutside'; } ?>">Download CSV</a> 
                    					    <a class="btn btn-sm btn-success" href="<?php echo base_url('Invoice/downloadStatementPDF/'.$key['company']);?>?sdate=<?=$sdate?>&edate=<?=$edate?>&generateXls<?php if($this->input->post('dispatchType') == 'outsideDispatch'){ echo '&dTable=dispatchOutside'; } ?>">Download Excel</a> 
                    					</div>
                    				</div>
                    				
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
						</tr>
					</tfoot>
				</tbody>
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
				parate = parate + parseFloat(currentPrice);
			});
			$( ".rateTxt" ).each(function( index ) {
				var currentPrice = $(this).html();
				rate = rate + parseFloat(currentPrice);
			});
			//$('.paRateTotal').val(parseFloat(parate).toFixed(2));
			$('.paRateTotal').html(parseFloat(parate).toFixed(2));
			$('.rateTotal').html(parseFloat(rate).toFixed(2));
			//$('.rateTotal').val(parseFloat(rate).toFixed(2));
		}
		function beforeCalculateRate(){
			var parate = 0;  var rate = 0;
			
			$('.paRateTotal').html(parate);
			$('.rateTotal').html(rate);
		}
		
	});
</script>
