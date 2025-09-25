<style>
	form.form {margin-bottom:25px;}
	.td-input{display:none;}
	.fas {cursor: pointer;}
	.fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	a.btn {margin-bottom: 5px;}


   
</style>
<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
	<h3 class="m-0">Invoice Email History</h3>
		<div class="add_page" style="float: right;">
		</div>
	</div>
	
	
	<div class="pt-card-body">
		
		<div class="d-block text-center">
			<form class="form form-inline pt-gap-15" method="post" action="">
				<input type="text" readonly placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 130px;" class="form-control datepicker">
				<input type="text"  style="width: 130px;" readonly placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker">
				
				<select name="dispatchType" required class="form-control" style="max-width: 150px;">
					<option value="">Select Division</option>
					<option value="paDispatch" <?php if($this->input->post('dispatchType') == 'paDispatch') { echo 'selected'; } ?>>PA Fleet Dispatch</option>
					<option value="outsideDispatch" <?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo 'selected'; } ?>>PA Logistics</option>
				</select>
				<select name="company" class="form-control d-none" style="max-width: 150px;">
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
				</select>
				<input type="submit" value="Search" name="search" class="btn btn-success">
			</form>
		</div>
				
		<div class="table-responsive pt-datatbl" style="overflow-y: auto;max-height: 90vh;">
			<table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead style="position:sticky;top:0">
                    <tr>
						<th>Sr no.</th>
						<th>Email Date</th>
						<th>Invoice #</th>
						<th>Company</th>
						<th>Tracking #</th>
						<th>Invoice File</th>
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
								}
								$invoiceType = $agingTxt = '';
								$showAging = 'false';
								$aDays = 0;
								if($key['invoiceType']=='Direct Bill'){ $invoiceType = 'DB'; $aDays = 30; }
								elseif($key['invoiceType']=='Quick Pay'){ $invoiceType = 'QP'; $aDays = 7; }
								elseif($key['invoiceType']=='RTS'){ $invoiceType = 'RTS'; $aDays = 3; }
								
								if($key['invoiceDate'] != '0000-00-00') { $showAging = 'true'; }
								if($dispatchMeta['invoicePaidDate'] != '') { $showAging = 'false'; }
								if($dispatchMeta['invoiceCloseDate'] != '') { $showAging = 'false'; }
								
								if($showAging == 'true'){
									$date1 = new DateTime($key['invoiceDate']);
									$date2 = new DateTime(date('Y-m-d'));
									$diff = $date1->diff($date2);
									$aging = $diff->days;
									
									if($aging  > $aDays && $aDays > 0) {
										$agingTxt = '<br><strong style="color:red">Inv. Aging: '.$aging.' Days</strong>';
									} else {
										$agingTxt = '<br>Inv. Aging: '.$aging.' Days';
									}
								}
									?>
							<tr class="tr-<?php echo $key['id'];?>" <?=$bgcolor?>>
								<td><?php echo $n;?></td>
								<td><?php echo date('m-d-Y',strtotime($key['date']));?></td>
								<td><?php //echo $key['invoice'];?>
									<a href="<?php echo base_url().'admin/'.$dispatchURL.'/update/'.$key['id'];?>"><span class="td-txt td-txt-<?php echo $key['id'];?>"><span class="c_invoice_txt_<?php echo $key['id'];?>"><?php echo $key['invoice'];?></span> &nbsp; <i class="fas fa-edit d-none" title="Edit" data-id="<?php echo $key['id'];?>" alt="Edit"></i> </span></a>
									<span class="td-input td-input-<?php echo $key['id'];?>">
										<input type="text" class="c_invoice_input_<?php echo $key['id'];?> current_input" data-id="#invoice_input" value="<?php echo $key['invoice'];?>">
										<i class="fa fa-paper-plane d-none" data-id="<?php echo $key['id'];?>" aria-hidden="true"></i>
									</span>
								</td> 
								<td><?php //echo $key['company'];
									if(array_key_exists($key['company'],$companyArr)){ 
										echo $companyArr[$key['company']]; 
									}
								?></td> 
								<td><?php echo $key['tracking'];?></td> 
								<td><?php 
								if($dispatchURL=='dispatch'){
									$filePath='assets/paInvoice/';
								}else{
									$filePath='assets/outside-dispatch/invoice/';
								}
													$fullPath = base_url($filePath .  $key['file']); 
													$fileName = $key['file'];
													$startChars = 10; 
													$endChars = 10; 

													if (strlen($fileName) > ($startChars + $endChars)) {
														$previewName = substr($fileName, 0, $startChars) . '...' . substr($fileName, -$endChars);
													} else {
														$previewName = $fileName;
													}
												?>
												<a href="<?= $fullPath ?>" target="_blank" style="display: inline-block; margin-right: 10px;">
													<span><?= $previewName ?></span>
												</a>
											</td>								
							</tr> 
							
							<?php
								$n++;
							}
						}
					?>
					
					<tfoot>  
						<tr>
						</tr>
					</tfoot>
				</tbody>
			</table>
		</div>
	</div>
	
</div>

</div>


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	$( function() {
		/*$( ".datepicker" ).datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});*/
		$(document).on('focus', '.datepicker', function() {
            $(this).datepicker({
    			dateFormat: 'yy-mm-dd',
    			changeMonth: true,
    			changeYear: true
    		});
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
		
		$('.editInvoice').click(function(e){
			e.preventDefault();
			$('.invoiceAjaxForm').html('.....'); 
			var dTable = $(this).attr('data-dTable');
			var did = $(this).attr('data-id');
			var href = $(this).attr('href');
			$('.generatePdfBtn').attr('href',href);
			$('#editinvoiceform').attr('action',href);
			let cBtn = '<?php echo base_url('Invoice/downloadInvoicePDF/');?>'+did+'?invoiceWithPdf=bol-rc<?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo '&type=outside&dTable=dispatchOutside'; } ?>';
			$('.combibePdfBtn').attr('href',cBtn);
			
			let dBtn = '<?php echo base_url('Invoice/downloadInvoicePDF/');?>'+did+'<?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo '?dTable=dispatchOutside'; } ?>';
			$('.downloadPDF').attr('data-id',did);
			$('.downloadPDF').attr('href',dBtn);
			$.ajax({
				type: "post",
				url: "<?php echo base_url('Invoice/editInvoiceForm?editInvoiceID=');?>"+did+"&dTable="+dTable,
				data: "editInvoiceID="+did,
				success: function(responseData) { 
					$('.invoiceAjaxForm').html(responseData);
				}
			});
		});
		$('.editInvoiceBtn').click(function(e){
			e.preventDefault();
			var form_data = $('#editinvoiceform').serialize();
			$.ajax({
				type: "post",
				url: "<?php echo base_url('admin/invoice');?>",
				data: form_data,
				success: function(responseData) { 
					alert(responseData);
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
			$( ".paRateTxt:visible" ).each(function( index ) {
				var currentPrice = $(this).html();
				parate = parate + parseFloat(currentPrice);
			});
			$( ".rateTxt:visible" ).each(function( index ) {
				var currentPrice = $(this).html();
				rate = rate + parseFloat(currentPrice); 
			});
			$('.paRateTotal').html(parseFloat(parate).toFixed(2));
			$('.rateTotal').html(parseFloat(rate).toFixed(2));
		}
		function beforeCalculateRate(){
			var parate = 0;  var rate = 0;
			
			$('.paRateTotal').html(parate);
			$('.rateTotal').html(rate);
		}
		
	});




</script>
