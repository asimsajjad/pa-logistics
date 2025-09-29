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
				<input type="text" required style="width: 160px;" readonly placeholder="Ending Batch Date" value="<?php if($this->input->post('edate')) { echo $edate = $this->input->post('edate'); } else { $edate = date('Y-m-d'); } ?>" name="edate" class="form-control datepicker"> 
				&nbsp;				
				<select name="dispatchType" required class="form-control" style="max-width: 150px;">
					<option value="">Select Division</option>
					<option value="paDispatch" <?php if($this->input->post('dispatchType') == 'paDispatch') { echo 'selected'; } ?>>PA Fleet Dispatch</option>
					<option value="outsideDispatch" <?php if($this->input->post('dispatchType') == 'outsideDispatch') { echo 'selected'; } ?>>PA Logistics</option>
					<option value="warehouse_dispatch" <?php if($this->input->post('dispatchType') == 'warehouse_dispatch') { echo 'selected'; } ?>>PA Warehousing</option>
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
					<div class="form-inline">
						<input type="number" name="aging_from" class="form-control" placeholder="Aging From" 
							value="<?= $this->input->post('aging_from') ?? '' ?>" style="width: 125px;"> &nbsp;
						<input type="number" name="aging_to" class="form-control" placeholder="Aging To" 
							value="<?= $this->input->post('aging_to') ?? '' ?>" style="width: 120px;">
					</div>
					&nbsp;
				<input type="submit" value="Search" name="search" class="btn btn-success pt-cta">
				<div class="dropdown" style="margin-left:10px;">
					<button class="btn btn-primary dropdown-toggle pt-cta" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Download
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <input type="submit" value="Download CSV" name="generateCSV" class="dropdown-item" >
                        <input type="submit" value="Download Excel" name="generateXls" class="dropdown-item" >
					</div>
				</div>
			</form>
		</div>
		
		
		<div class="table-responsive pt-datatbl" style="">
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
								if($this->input->post('dispatchType') == 'outsideDispatch') { 
									$dispatchURL = base_url('admin/outside-dispatch/update/').$key['id'];
								} else if ($this->input->post('dispatchType') == 'warehouse_dispatch'){
									$dispatchURL = base_url('admin/paWarehouse/update/').$key['id'];
								}
								else {	
									$dispatchURL = base_url('admin/dispatch/update/').$key['id'];
								}
							?>
							<tr class="tr-<?php echo $key['id'];?>" <?=$bgcolor?>>
								<td style="text-align: center; vertical-align: middle;"><?php echo $n;?></td>
								<td><?php echo $key['added_by'];?></td>
								<td><span class="company"><?php echo $key['company']?></span></td>  
								<td><?php 
										if ($this->input->post('dispatchType') == 'warehouse_dispatch'){
											if (isset($key['invoicePaidDate']) && !empty($key['invoicePaidDate'])) {
												echo date('m-d-Y', strtotime($key['invoicePaidDate']));
											} else {
												echo ''; 
											}
										}else{
											if (isset($dispatchMeta['invoicePaidDate']) && !empty($dispatchMeta['invoicePaidDate'])) {
												echo date('m-d-Y', strtotime($dispatchMeta['invoicePaidDate']));
											} else {
												echo ''; 
											}
										}
									?>
								</td>
								<td>$<span class="totalAmount"><?php echo number_format($key['totalAmount'],2)?></span></td>  
								<?php 
									$batchDateBatchNo = date('mdY', strtotime($key['date'])) . $key['batchNo'];
								?>	
								<td class="toggle-details" data-details="<?php echo htmlspecialchars(json_encode($key), ENT_QUOTES, 'UTF-8'); ?>" style="cursor:pointer; color: blue;" >
									<?php echo $batchDateBatchNo; ?>
								</td>
								<td><?php echo date('m-d-Y h:i:s A',strtotime($key['date']));?></td>
							</tr>
						
							<?php
								$n++;
							}
						}
					?>
					<tfoot>
					<tr>
						<td colspan="4" class="text-end"><strong>Total</strong></td>
						<td><strong>$<span class="visibleTotalAmount">0.00</span></strong></td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
				</tbody>
			</table>
		</div>
	</div>
	
</div>

</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<link href="<?php echo base_url().'assets/css/5.3.0bootstrap.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/5.3.0bootstrap.bundle.min.js'; ?>"></script>
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
		
	});
</script>
<script>
	const baseUrl = "<?= base_url(); ?>";
	const dispatchType = "<?= $this->input->post('dispatchType'); ?>";
	function formatChildRows(invoiceDetails, company) {
	let html = `
	<table class="table table-bordered mb-0" style="background-color:#f9f9f9;">
		<thead>
			<tr>
				<th>#</th>
				<th>INVOICE</th>
				<th>CARRIER REF #</th>
				${(dispatchType === 'outsideDispatch' || dispatchType === 'warehouse_dispatch') ? '<th>CARRIER INV. REF #</th>' : ''}
				${company === 'Multiple Companies' ? '<th>COMPANY</th>' : ''}
				${dispatchType === 'warehouse_dispatch' ? '<th>END DATE</th>' : '<th> DELIVERY DATE</th>'}
				<th>INVOICE DATE</th>
				<th>INVOICE AMT</th>
				<th>RECEIVED DAYS</th>
				<th>CUSTOMER PAYMENT PROOF</th>
			</tr>
		</thead>
		<tbody>`;


	if (Array.isArray(invoiceDetails) && invoiceDetails.length > 0) {
		let totalAmount = 0;
		invoiceDetails.forEach((inv, idx) => {
			const dispatchMeta = JSON.parse(inv.dispatchMeta || '{}');
			let carrierRef = '';
			if(dispatchType === 'warehouse_dispatch'){
				carrierRef = inv.dispatchValue;
			}else{
				if (Array.isArray(dispatchMeta.dispatchInfo)) {
					dispatchMeta.dispatchInfo.forEach(item => {
						if (item[0] === 'Carrier Ref No') carrierRef = item[1];
					});
				}
			}
			if (!carrierRef || carrierRef === 'null') {
				carrierRef = '';
			}
			let deliveryDate  = '';
			if(dispatchType === 'warehouse_dispatch'){
				deliveryDate = formatDate(inv.edate);
			}else{
				deliveryDate = formatDate(inv.dodate);
			}
			
			const invoiceDate = formatDate(inv.invoiceDate);
			const docs = inv.documents || [];
			let docPath = '';
			let docLinks = docs.length > 0 ? docs.map(doc => {
				let name = doc.fileurl;
				let short = name.length > 20 ? name.slice(0, 10) + '...' + name.slice(-10) : name;
				if (dispatchType == 'outsideDispatch') {
					docPath = 'assets/outside-dispatch/gd/';
				}else if (dispatchType == 'warehouse_dispatch'){
					docPath ='assets/warehouse/gd/';
				}else{
					docPath = 'assets/upload/';
				}
				
				return `<a href="${baseUrl}${docPath}${name}" target="_blank" 
							style="display: inline-block; margin-right: 10px; margin-bottom: 5px;">
							<span>${short}</span>
						</a>`;
			}).join('') : '<span>No documents</span>';

			let docsHtml = `
			<div style="display: flex; flex-wrap: wrap; gap: 5px;">
				${docLinks}
			</div>`;

			let dispatchURL = baseUrl + 'admin/dispatch/update/' + inv.id;
			if (dispatchType === 'outsideDispatch') {
				dispatchURL = baseUrl + 'admin/outside-dispatch/update/' + inv.id;
			}else if (dispatchType === 'warehouse_dispatch'){
				dispatchURL = baseUrl + 'admin/paWarehouse/update/' + inv.id;
			}
			totalAmount += parseFloat(inv.parate || 0);
			html += `
			<tr>
				<td>${idx + 1}</td>
				<td><a target="_blank" href="${dispatchURL}">${inv.invoice}</a></td>
				<td>${carrierRef}</td>`;

			if (dispatchType === 'outsideDispatch' || dispatchType === 'warehouse_dispatch') {
				html += `<td>${inv.carrierInvoiceRefNo || ''}</td>`;
			}

			if (company === 'Multiple Companies') {
				html += `<td>${inv.company}</td>`;
			}

			html += `
				<td>${deliveryDate}</td>
				<td>${invoiceDate}</td>`;
				const formattedPaRate = (parseFloat(inv.parate) || 0).toLocaleString('en-US', {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				});

			html += `
				<td>$${formattedPaRate}</td>
				<td>${inv.received_days} Days</td>
				<td>${docsHtml}</td>
			</tr>`;
		});
		const formattedTotal = totalAmount.toLocaleString('en-US', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2
		});
		if (dispatchType === 'outsideDispatch' || dispatchType === 'warehouse_dispatch') {
			if (company === 'Multiple Companies') {
				html += `<tr><td colspan="7" class="text-end"><strong>Total Amount</strong></td><td colspan="3">$${formattedTotal}</td></tr>`;	
			}else{
				html += `<tr><td colspan="6" class="text-end"><strong>Total Amount</strong></td><td colspan="3">$${formattedTotal}</td></tr>`;	
			}
		}else{
			html += `<tr><td colspan="5" class="text-end"><strong>Total Amount</strong></td><td colspan="3">$${formattedTotal}</td></tr>`;
		}
	} else {
		html += `<tr><td colspan="10">No data available</td></tr>`;
	}

	html += `</tbody></table>`;
	return html;
}

function formatDate(dateStr) {
	if (!dateStr) return '';
	const date = new Date(dateStr);
	const mm = String(date.getMonth() + 1).padStart(2, '0');
	const dd = String(date.getDate()).padStart(2, '0');
	const yyyy = date.getFullYear();
	return `${mm}-${dd}-${yyyy}`;
}
$(document).ready(function () {
	const dataTable = $('#dataTable').DataTable({
			"pageLength": 20,
			"lengthMenu": [[20, 25, 50, -1], [20, 25, 50, "All"]],
			"scrollX": true
		});
	$(document).on('click', '.toggle-details', function () {
		let data = $(this).attr('data-details');
		let customer;
		try {
			customer = JSON.parse(data);
		} catch (e) {
			console.error("Invalid JSON in data-details", e);
			return;
		}

		const tr = $(this).closest('tr');
		const row = dataTable.row(tr);

		if (row.child.isShown()) {
			row.child.hide();
			tr.removeClass('shown');
		} else {
			// const html = formatChildRows(customer.invoiceDetails || []);
			const html = formatChildRows(customer.invoiceDetails || [], customer.company);
			row.child(html).show();
			tr.addClass('shown');
		}
	});
	$('#dataTable').on('draw.dt', function () {
			calculatePaRate();
		});
		calculatePaRate();
});
function calculatePaRate(){
		let totalAmount = 0; 
		$(".totalAmount:visible").each(function() {
			const val = $(this).text().replace(/,/g, '');
			totalAmount += parseFloat(val) || 0;
		});
		const formatted = totalAmount.toLocaleString('en-US', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2
		});
		$('.visibleTotalAmount').html(formatted);
	}

</script>