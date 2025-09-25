<style>
	form.form {margin-bottom:25px;}
	.td-input{display:none;}
	.fas {cursor: pointer;}
	.fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	a.btn {margin-bottom: 5px;}
</style>
<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3>Receivable Batches</h3> 
		<div class="add_page" style="float: right;">
		</div>
	</div>
	
	
	<div class="card-bodys table_style">
		
		<div class="d-block text-center">
			<form class="form form-inline" method="post" action="">
				<input type="text" required readonly placeholder="Starting Batch Date" value="<?php if($this->input->post('sdate')) { echo $sdate = $this->input->post('sdate'); } else { $sdate = date('Y-m-d',strtotime("-4 months",strtotime(date('Y-m-d')))); } ?>" name="sdate" style="width: 165px;" class="form-control datepicker"> &nbsp;
				<input type="text" required style="width: 160px;" readonly placeholder="Ending Batch Date" value="<?php if($this->input->post('edate')) { echo $edate = $this->input->post('edate'); } else { $edate = date('Y-m-d'); } ?>" name="edate" class="form-control datepicker"> &nbsp;
				
				
				<select name="dispatchType" required class="form-control" style="max-width: 150px;">
					<option value="">Select Dispatch</option>
					<option value="paDispatch" <?php if($this->input->post('dispatchType') == 'paDispatch') { echo 'selected'; } ?>>PA Fleet Dispatch</option>
					<option value="outsideDispatch" <?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo 'selected'; } ?>>Outside Dispatch</option>
				</select>
				&nbsp;
				<select name="company[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Customer" style="max-width: 250px;">
					<option value="">All Company</option>
					<?php 
						$selected_companies = $this->input->post('company');
						$companyArr = array();
						if(!empty($companies)){
							foreach($companies as $val){
								$companyArr[$val['id']] = $val['company'];
								echo '<option value="'.$val['id'].'"';
								if(!empty($selected_companies) && in_array($val['id'], $selected_companies)) { echo ' selected '; }
								echo '>'.$val['company'].'</option>';
							}
						}
						
					?>
					</select>&nbsp;
				<input type="submit" value="Search" name="search" class="btn btn-success pt-cta">
			</form>
		</div>
		
		
		<div class="table-responsive pt-datatbl" style="overflow-y: auto;max-height: 90vh;">
			<table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead style="position:sticky;top:0">
                    <tr>
						<th style="text-align: center; vertical-align: middle;">Sr no.</th>
						<th >Added By</th>
						<th>Company</th>
						<th>Received Date</th>
						<th>Total Amt</th>
						<th>Batch No</th>
						<th>Batch Date</th>
					</tr> 
				</thead>
				
				<tbody>
					<?php
						if(!empty($dispatch)){
							$n=1; $totalAmount = $parate = 0;
							foreach($dispatch as $key) {
								$totalAmount = $totalAmount + $key['totalAmount'];
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
								<td style="text-align: center; vertical-align: middle;"><?php echo $n;?></td>
								<td><?php echo $key['added_by'];?></td>
								<td><span class="company"><?php echo $key['company']?></span></td>  
								<td><?php if (isset($dispatchMeta['invoicePaidDate']) && !empty($dispatchMeta['invoicePaidDate'])) {
												echo date('m-d-Y', strtotime($dispatchMeta['invoicePaidDate']));
											} else {
												echo ''; 
											}?>
								</td>
								<td>$<span class="totalAmount"><?php echo number_format($key['totalAmount'],2)?></span></td>  
								<td>
								<?php 
									$batchDateBatchNo = date('mdY', strtotime($key['date'])) . $key['batchNo'];
									?>
									<a href="javascript:void(0);" class="toggle-row" style="color: blue; text-decoration: none;" data-id="<?php echo $key['id']; ?>">
										<?php echo $batchDateBatchNo; ?>
									</a>
								</td> 
								<td><?php echo date('m-d-Y h:i:s A',strtotime($key['date']));?></td>
							</tr>
							<tr class="hidden-row" id="details-row-<?php echo $key['id']; ?>" style="display: none;">
								<td colspan="1"></td>
								<td colspan="6">
									<table class="table table-sm">
										<thead>
											<tr>
											<th>#</th>
											<th>INVOICE</th>
											<th>CARRIER REF #</th>
											<?php if($this->input->post('dispatchType') == 'outsideDispatch') {?> 
											<th>CARRIER INV. REF #</th>
											<?php 
											}
											if($key['company']== 'Multiple Companies'){ ?>
											<th>COMMPANY</th>
											<?php }
											?>
											<th>DELIVERY DATE</th>
											<th>INVOICE DATE</th>
											<!-- <th>CARRIER RATE</th> -->
											<th>INVOICE AMT</th>
											<th>RECEIVED DAYS</th>
											<th>CUSTOMER PAYMENT PROOF</th>
											<!-- <th>AGING DAYS</th> -->
											</tr>
										</thead>
										<tbody>
                    <?php if (!empty($key['invoiceDetails'])):
						$count = 1;
					?>
                        <?php foreach ($key['invoiceDetails'] as $invoice): 
							$dispatchMeta = json_decode($invoice['dispatchMeta'],true);
							$carrierRefNo = '';
							if (!empty($dispatchMeta['dispatchInfo'])) {
								foreach ($dispatchMeta['dispatchInfo'] as $item) {
									if ($item[0] === 'Carrier Ref No') {
										$carrierRefNo = $item[1];
										break;
									}
								}
							}
							if(is_numeric($dispatchMeta['partialAmount'])) {
								$partialAmt = $dispatchMeta['partialAmount'];
							}
							if($this->input->post('dispatchType') == 'outsideDispatch') { 
								$dispatchURL = base_url('admin/outside-dispatch/update/').$invoice['id'];
								$table = 'dispatchOutside';
								$filePath='assets/outside-dispatch/gd/';
							} else {	
								$dispatchURL = base_url('admin/dispatch/update/').$invoice['id'];
								$table = 'dispatch';
								$filePath='assets/upload/';
							}
							?>
							
                            <tr>
							<td style="text-align: center; vertical-align: middle;"><?php echo $count; ?></td>

							<td style="text-align: center; vertical-align: middle;"><a target="_blank" href="<?=$dispatchURL?>"><?php echo $invoice['invoice']; ?></td>
							<td style="text-align: center; vertical-align: middle;"><?php echo $carrierRefNo; ?></td>
							<?php if($this->input->post('dispatchType') == 'outsideDispatch') { ?>
							<td style="text-align: center; vertical-align: middle;"><?php echo $invoice['carrierInvoiceRefNo']; ?></td>
								<?php 
							}
					if($key['company']== 'Multiple Companies'){ ?>
						<td style="vertical-align: middle;"><?php echo $invoice['company']; ?></td>
					<?php }
					?>
								<td style="text-align: center; vertical-align: middle;"><?php echo date('m-d-Y',strtotime( $invoice['dodate'])); ?></td>
								<td style="text-align: center; vertical-align: middle;"><?php echo date('m-d-Y',strtotime( $invoice['invoiceDate'])); ?></td>
								<!-- <td style="text-align: center; vertical-align: middle;">$<?php echo $invoice['rate']; ?></td> -->
                            	<td style="text-align: center; vertical-align: middle;">$<?php echo $invoice['parate']; ?></td>
								<td style="text-align: center; vertical-align: middle;"><?php echo $invoice['received_days']; ?> Days</td>
								<td>
									<?php if (!empty($invoice['documents'])): ?>
										<div style="display: flex; flex-wrap: wrap;">
											<?php 
												$docCount = count($invoice['documents']);
												$i = 0;
											?>
											<?php foreach ($invoice['documents'] as $doc): ?>
												<?php 
													$fullPath = base_url($filePath . $doc['fileurl']); 
													$fileName = $doc['fileurl'];
													$startChars = 10; 
													$endChars = 10; 

													if (strlen($fileName) > ($startChars + $endChars)) {
														$previewName = substr($fileName, 0, $startChars) . '...' . substr($fileName, -$endChars);
													} else {
														$previewName = $fileName;
													}
												?>

												<!-- Start a new row after every 2 documents -->
												<?php if ($i > 0 && $i % 2 == 0): ?>
													</div><div style="display: flex; flex-wrap: wrap; margin-top: 5px;">
												<?php endif; ?>

												<a href="<?= $fullPath ?>" target="_blank" style="display: inline-block; margin-right: 10px;">
													<span><?= $previewName ?></span>
												</a>

												<?php $i++; ?>
											<?php endforeach; ?>
										</div>
									<?php else: ?>
										<span>No files</span>
									<?php endif; ?>
								</td>
                            </tr>
                        <?php $count++ ;
						 endforeach;
						?>
						<tr>
							<?php if($this->input->post('dispatchType') == 'outsideDispatch') { ?>
								<td></td>
								<?php } ?>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
								<?php 
								if($key['company']== 'Multiple Companies'){ ?>
								<td></td>
								<?php } ?>
								<td style="text-align: center; vertical-align: middle;">Total Amount </td>
								<td style="text-align: center; vertical-align: middle;">$<span class="totalAmount"><?php echo number_format($key['totalAmount'],2); ?></span></td>		
								<td></td>
								<td></td>
						</tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No invoices available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </td>
    </tr>					
							<?php
								$n++;
							}
						}
					?>
					
					<tfoot>  
						<!-- <tr>
							<td></td>						
							<td></td>
							<td><strong>Total</strong></td>
							<td><strong>$<span class="totalAmount"><?php echo $totalAmount; ?></span></strong></td>
							<td></td>

						</tr> -->
					</tfoot>
				</tbody>
			</table>
		</div>
	</div>
	
</div>

</div>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- jQuery UI -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

<!-- Select2 CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Bootstrap CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
	  $(document).ready(function() {
      $('.select2').select2();
   });
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
		

		$('#dataTable').DataTable({
			"lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]]
		});
	});

	$(document).ready(function() {
    // Toggle Invoice Details
    $('.toggle-row').on('click', function() {
        var id = $(this).data('id'); // Batch ID
        var detailsRow = $('#details-row-' + id);

        // Show/Hide the row
        if (detailsRow.is(':visible')) {
            detailsRow.hide();
        } else {
            detailsRow.show();
        }
    });
});

$(document).off('click', '.download-all-btn').on('click', '.download-all-btn', function (e) {
    e.preventDefault();
    
    var invoiceId = $(this).data('invoice-id');
    var processedFiles = []; 

    $('.document-link[data-invoice-id="' + invoiceId + '"]').each(function () {
        var fileUrl = $(this).attr('href');
        var fileName = $(this).attr('download');

        if (!processedFiles.includes(fileUrl)) {
            processedFiles.push(fileUrl);

            var link = document.createElement('a');
            link.href = fileUrl;
            link.download = fileName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });
});
</script>
