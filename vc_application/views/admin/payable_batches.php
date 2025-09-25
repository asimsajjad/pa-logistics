<style>
	form.form {
		margin-bottom: 25px;
	}

	.td-input {
		display: none;
	}

	.fas {
		cursor: pointer;
	}

	.fa {
		cursor: pointer;
		font-size: 26px;
		margin-top: 5px;
	}

	a.btn {
		margin-bottom: 5px;
	}

	.select2-container--default .select2-selection--multiple {
		border-radius: 20px !important;
	}

	.select2-container .select2-selection--multiple {
		min-height: 46px !important;
	}

	.form-control {
		height: 39px !important;
	}

</style>

<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3>Payable Batches</h3>
		<div class="add_page" style="float: right;">
		</div>
	</div>
	<div class="card-bodys table_style">
		<div class="d-block text-center">
				<?php 
				if($this->input->post('sdate')) { 
					$sdate = $this->input->post('sdate'); 
				} 
				else {
					$sdate =''; 
				}
				if($this->input->post('edate')) {
					$edate = $this->input->post('edate'); 
				}
				else {
					$edate =''; 
				}
			?> 
			<form class="form form-inline" method="post" action="">
				<input type="text" required readonly placeholder="Starting Batch Date" value="<?php echo $sdate; ?>" name="sdate" style="width: 165px;" class="form-control datepicker"> &nbsp;
				<input type="text" required style="width: 160px;" readonly placeholder="Ending Batch Date" value="<?php echo $edate; ?>" name="edate" class="form-control datepicker"> &nbsp;
			<select class="form-control select2" name="truckingCompany[]" data-placeholder="Select Carrier" multiple="multiple" style="max-width: 250px;">
						<option value="">Select Carrier</option>
						<?php 
						$selected_truckingCompanies = $this->input->post('truckingCompany');
						$truckingCompanyArr = array();
							if (!empty($truckingCompanies)) {
								foreach ($truckingCompanies as $val) {
									$truckingCompanyArr[$val['id']] = $val['company'];
									echo '<option value="'.$val['id'].'"';
									if(!empty($selected_truckingCompanies) && in_array($val['id'], $selected_truckingCompanies)) { echo ' selected '; }
									echo '>'.$val['company'].'</option>';
								}
							}
						?>
					</select>
				&nbsp;
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
				<thead style="position:sticky;top:0; background-color:#f8f9fa; z-index:1">
					<tr>
						<th  style="text-align: center; vertical-align: middle;">Sr no.</th>
						<th>Added By</th>
						<th>Carrier</th>
						<th>Carrier Payout Date</th>
						<th>Total Amount</th>
						<th>Batch No</th>
						<th>Batch Date</th>
					</tr>
				</thead>

				<tbody>
					<?php
					if (!empty($customersPayables)) {
						$n = 1;
						$totalAmount = 0;
						// echo '<pre> <br>';
						// print_r($customersPayables); die;
						foreach ($customersPayables as $customer) {
							$totalInvoiceAmount = $customer['0-15_days_amount'] + $customer['16-30_days_amount'] + $customer['31-45_days_amount'] +
								$customer['46-60_days_amount'] + $customer['61-75_days_amount'] + $customer['76-90_days_amount'] +
								$customer['90_days_amount'];
								$totalAmount = $totalAmount + $customer['totalAmount'];
							?>
							<tr class="tr-<?php echo $customer['id']; ?>" >
								<td  style="text-align: center; vertical-align: middle;"><?php echo $n; ?></td>
								<td><?php echo $customer['added_by'];?></td>
								<td><span class="companyName"><?php echo $customer['company']; ?></span></td>
								<td>
									<?php echo date('m-d-Y', strtotime($customer['carrierPayoutDate'])); ?>									
								</td>
								<td>$<span class="totalAmount"><?php echo number_format($customer['totalAmount'],2); ?></span></td>
								<?php 
									$batchDateBatchNo = date('mdY', strtotime($customer['date'])) . $customer['batchNo'];
								?>
									
								<td class="toggle-details" data-details="<?php echo htmlspecialchars(json_encode($customer), ENT_QUOTES, 'UTF-8'); ?>" style="cursor:pointer; color: blue;" >
									<?php echo $batchDateBatchNo; ?>
								</td>
								<td>
									<?php echo date('m-d-Y h:i:s A', strtotime($customer['date'])); ?>
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
						<td colspan="4" class="text-end"><strong>Total</strong></td>
						<td><strong>$<span class="visibleTotalAmount">0.00</span></strong></td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<link href="<?php echo base_url().'assets/css/5.3.0bootstrap.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/5.3.0bootstrap.bundle.min.js'; ?>"></script>

<script>
$(document).ready(function () {
     $('.select2').select2();
	 	$( ".datepicker" ).datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			autoclose: true
		});
		$(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
      });
});
</script>
<script>
	const baseUrl = "<?= base_url(); ?>";
	function formatChildRows(invoices, company, company_id) {
		let html = `
			<table class="table table-bordered mb-0" id="details-table" style="background-color:#f9f9f9;">
				<thead>
					<tr>
						<th>#</th>
						<th>INVOICE</th>
						<th>CARRIER REF #</th>
						<th>CARRIER INV. REF #</th>`;
						if (company === 'Multiple Carriers' || company_id == 4) {
							html += `<th>CARRIER</th>`;
						}
						if (company_id == 4) {
							html += `<th>BOOKED UNDER</th>`;
						}
						html += `
						<th>DELIVERY DATE</th>
						<th>CARRIER INVOICE DATE</th>
						<th>CARRIER RATE</th>
						<th>PAYMENT DAYS</th>
						<th>PAYMENT PROOF</th>
					</tr>
				</thead>
				<tbody>`;
				if (Array.isArray(invoices) && invoices.length > 0) {
					let totalAmount = 0;
					invoices.forEach((inv, idx) => {
						const dispatchMeta = JSON.parse(inv.dispatchMeta || '{}');
						const dispatchInfo = dispatchMeta.dispatchInfo || [];
						let carrierRef = '';
						dispatchInfo.forEach(item => {
							if (item[0] === 'Carrier Ref No') {
								carrierRef = item[1];
							}
						});
						const custInvDate = formatDate(dispatchMeta.custInvDate);
						const dodate = formatDate(inv.dodate);
						const docLinks = (inv.documentsOutside || []).map(doc => {
							const fileName = doc.fileurl;
							const displayName = fileName.length > 20 
								? fileName.substring(0, 10) + '...' + fileName.slice(-10) 
								: fileName;

							return `<a href="${baseUrl}assets/outside-dispatch/gd/${fileName}" target="_blank" 
										style="display: inline-block; margin-right: 10px; margin-bottom: 5px;">
										<span>${displayName}</span>
									</a>`;
						}).join('');

						const wrappedDocLinks = `
							<div style="display: flex; flex-wrap: wrap; gap: 5px;">
								${docLinks || '<span>No documents</span>'}
							</div>
						`;


						totalAmount += parseFloat(inv.rate || 0);
						
						html += `<tr>
							<td>${idx + 1}</td>
							<td><a target="_blank" href="${baseUrl}admin/outside-dispatch/update/${inv.id}">${inv.invoice}</a></td>
							<td>${carrierRef}</td>
							<td>${inv.carrierInvoiceRefNo || ''}</td>`;
						if (company === 'Multiple Carriers' || company_id == 4) {
							html += `<td>${inv.company || ''}</td>`;
						}
						if (company_id == 4) {
							html += `<td>${inv.bookedUnder || inv.bookedUnderOld || ''}</td>`;
						}
						html += `
							<td>${dodate}</td>
							<td>${custInvDate}</td>`;
							const formattedRate = (parseFloat(inv.rate) || 0).toLocaleString('en-US', {
								minimumFractionDigits: 2,
								maximumFractionDigits: 2
							});

						html += `
							<td>$${formattedRate}</td>
							<td>${inv.pay_days} Days</td>
							<td>${wrappedDocLinks}</td>
						</tr>`;				
					});
					const formattedTotal = totalAmount.toLocaleString('en-US', {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					});
					if (company_id == 4) {
						html += `<tr><td colspan="8" class="text-end"><strong>Total Amount</strong></td><td colspan="3">$${formattedTotal}</td></tr>`;	
					}else{
						html += `<tr><td colspan="6" class="text-end"><strong>Total Amount</strong></td><td colspan="3">$${formattedTotal}</td></tr>`;
					}
				} else {
					html += `<tr><td colspan="11">No invoice details available</td></tr>`;
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
				const html = formatChildRows(customer.invoiceDetails || [], customer.company, customer.company_id);
				row.child(html).show();
				tr.addClass('shown');
			}
		});
		$('#dataTable').on('draw.dt', function () {
			calculateRate();
		});
		calculateRate();
	});
	function calculateRate(){
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
<style>
	#details-table th,
	#details-table td {
		text-align: left;
		vertical-align: middle;
	}
</style>