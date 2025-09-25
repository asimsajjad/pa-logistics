<style>
  	form.form {margin-bottom:25px;}
	.td-input{display:none;}
	.fas {cursor: pointer;}
	.fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	a.btn {margin-bottom: 5px;}
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
<div class="card mb-3" style="padding: 25px;">
	<div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
    	<h3 class="mb-0">Warehouse Inbounds</h3>
    	<div class="add_page d-flex" style="float: right;">
    		<a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/warehouse/addInbounds';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
      		</a>  
			<a class="nav-link p-0 ml-1" title="Upload Inbounds" href="<?php echo  base_url().'admin/warehouse/uploadInbounds';?>" style="display: inline;"><input type="button" name="add" value="Upload Inbounds" class="btn btn-primary pt-cta"/>
			</a>
    	</div>
  	</div>
	<div style="margin-top: -25px;">
		<?php if($this->session->flashdata('item')){ ?>
		<div class="alert alert-success p-0">
			<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
		</div>
		<?php } elseif($this->session->flashdata('error')){ ?>
			<div class="alert alert-danger p-0">
				<h4><?php echo $this->session->flashdata('error'); $this->session->set_flashdata('error',''); ?></h4> 
			</div>
		<?php } else if($this->session->flashdata('warning')) { ?>
			<div class="alert alert-danger p-0">
				<h4><?php echo $this->session->flashdata('warning'); $this->session->set_flashdata('warning',''); ?></h4> 
			</div>
		<?php } ?>
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
			<form class="form form-inline" method="post" id="receivableSearchForm" action="">	
				<input type="hidden" name="agingSearch" id="agingSearch" value="<?php echo $agingSearch; ?>">
				<input type="hidden" name="search" id="agingSearch" value="Search">
				<input type="text" required readonly placeholder="Start Date" value="<?php echo $sdate; ?>" name="sdate" style="width: 108px;" class="form-control datepicker"> &nbsp;
				<input type="text" required style="width: 108px;" readonly placeholder="End Date" value="<?php echo $edate; ?>" name="edate" class="form-control datepicker"> &nbsp;
				<select name="company[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Customer" style="width: 18%;">
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
				</select>
				&nbsp; 
        		<select name="materialId[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Material" style="width: 18%;">
					<option value="">All Materials</option>
					<?php 
						$selected_material = $this->input->post('materialId');
						$materialArr = array();
						if(!empty($materials)){
							foreach($materials as $val){
								$materialArr[$val['id']] = $val['materialId'];
								echo '<option value="'.$val['id'].'"';
								if(!empty($selected_material) && in_array($val['id'], $selected_material)) { echo ' selected '; }
								echo '>'.$val['materialNumber'].' ('.$val['batch'].')</option>';
							}
						}	
					?>
				</select>
				&nbsp;  
				<select name="warehouse_id[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Warehouse" style="width: 18%;">
						<option value="">Select a Warehouse</option>
						<?php 
							$selected_warehouse = $this->input->post('warehouse_id');
							$warehouseArr = array();
							if(!empty($warehouse_address)){
								foreach($warehouse_address as $val){
									$warehouseArr[$val['id']] = $val['warehouse_id'];
									echo '<option value="'.$val['id'].'"';
									if(!empty($selected_warehouse) && in_array($val['id'], $selected_warehouse)) { echo ' selected '; }
									echo '>'.$val['warehouse'].' ('.$val['address'].')</option>';
								}
							}
						?>
					</select>
					&nbsp; 
					<select name="sublocationId[]" class="form-control select2" multiple="multiple" data-placeholder="Select a sublocation" style="width: 17%;">
						<option value="">Select Sublocation</option>
						<?php 
							$selected_sublocation = $this->input->post('sublocationId');
							$sublocationArr = array();
							if(!empty($warehouse_sublocations)){
								foreach($warehouse_sublocations as $val){
									$warehouseArr[$val['id']] = $val['sublocationId'];
									echo '<option value="'.$val['id'].'"';
									if(!empty($selected_sublocation) && in_array($val['id'], $selected_sublocation)) { echo ' selected '; }
									echo '>'.$val['name'].'</option>';
								}
							}
						?>
					</select>
					&nbsp; 
				<input type="submit" id="submitBtn" style="width: 80px;" class="btn btn-success pt-cta">
				<input type="submit" value="Download CSV" name="generateCSV" class="btn btn-success pt-cta ml-2" >
			</form>
		</div>                
		<div class="table-responsive pt-datatbl">
			<table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th style="width : 0px;">Sr.#</th>
						<th style="">Date In</th>
						<th style="">Customer</th>
						<th style="">Warehouse ( Address )</th>
						<th style="">Sublocation</th>
						<th style="">Total Pallet Qty</th>
						<th style="">Total Pieces Qty</th>
						<th style="">Inbound files</th>
						<th>Action</th>
					</tr>
				</thead>    
				<tbody>
					<?php if (!empty($warehouse)): 
						$n = 1;
						foreach ($warehouse as $summary): ?>
						<?php 
							$detailIds = array_column($summary['details'], 'id');
							// print_r(htmlspecialchars(json_encode($detailIds), ENT_QUOTES, 'UTF-8'));exit;
							$detailsWithDate = array_map(function($d) use ($summary) {
								$d['dated'] = $summary['dated'];
								if (!empty($d['file'])) {
									$d['files'] = array_map('trim', explode(',', $d['file']));
								} else {
									$d['files'] = [];
								}
								return $d;
							}, $summary["details"]);
							?>
							<tr class="parent-row" data-details='<?= json_encode($detailsWithDate); ?>'>
							<!-- <tr class="parent-row" data-details='<?php echo json_encode($summary["details"]); ?>'> -->
								<td style=""><?php echo $n; ?></td>
								<td style=""><?php echo $summary['dated']; ?></td>
								<td style=""><?php echo $summary['customer']; ?></td>
								<td style=""><?php echo $summary['warehouse'] .' ( ' .$summary['warehouseAddress'].' )';?></td>
								<td style=""><?php echo $summary['sublocation']; ?></td>
								<td style="cursor:pointer; color: blue;" class="toggle-details"><?php echo $summary['palletQuantity']; ?></td>
								<td style=""><?php echo $summary['piecesQuantity']; ?></td>
								<?php
								$fileLinks = [];
								if (!empty($summary['file']) && trim($summary['file']) !== '') {
									$fileNames = explode(',', $summary['file']);
									
									foreach ($fileNames as $file) {
										if (trim($file) === '') continue; // skip empty entries

										$shortName = strlen($file) > 20 ? substr($file, 0, 15) . '...' : $file;
										$url = base_url('assets/warehouse-inbounds/inbound-files/' . $file);
										$fileLinks[] = '<a href="' . $url . '" target="_blank" title="' . $file . '" style="color:blue;">' . $shortName . '</a>';
									}
								}
								?>
								<td>
									<?= !empty($fileLinks) ? implode('<br>', $fileLinks) : 'No file'; ?>
								</td>
								<td>
									<a class="btn btn-sm btn-danger" href="<?php echo base_url('Warehouse/downloadBoundPDF/'.$summary['id']);?>?date=<?=$summary['dated'] ?>&customerId=<?=$summary['customer_id'] ?>&warehouseId=<?=$summary['warehouse_id'] ?>&sublocationId=<?=$summary['sublocation_id'] ?>&type=inbound">Get Inward RD</a> 
									<a class="btn btn-sm btn-info internal-transfer-btn"
										href="<?php echo base_url('Warehouse/internalTransfer'); ?>"
										data-id="<?= $summary['id']; ?>"
										data-detail-ids="<?= htmlspecialchars(json_encode($detailIds), ENT_QUOTES, 'UTF-8'); ?>"
										data-date="<?= $summary['dated']; ?>"
										data-customer-id="<?= $summary['customer_id']; ?>"
										data-warehouse-id="<?= $summary['warehouse_id']; ?>"
										data-sublocation-id="<?= $summary['sublocation_id']; ?>">
										Transfer
									</a>
									<a class="btn btn-sm btn-primary outbound-btn"
										href="<?php echo base_url('Warehouse/directOutbound'); ?>"
										data-id="<?= $summary['id']; ?>"
										data-detail-ids="<?= htmlspecialchars(json_encode($detailIds), ENT_QUOTES, 'UTF-8'); ?>"
										data-date="<?= $summary['dated']; ?>"
										data-customer-id="<?= $summary['customer_id']; ?>"
										data-warehouse-id="<?= $summary['warehouse_id']; ?>"
										data-sublocation-id="<?= $summary['sublocation_id']; ?>">
										Outbound
									</a>
									<a class="btn btn-sm btn-success edit-btn"
										href="<?php echo base_url('Warehouse/editInbound'); ?>"
										data-id="<?= $summary['id']; ?>"
										data-detail-ids="<?= htmlspecialchars(json_encode($detailIds), ENT_QUOTES, 'UTF-8'); ?>"
										data-date="<?= $summary['dated']; ?>"
										data-customer-id="<?= $summary['customer_id']; ?>"
										data-warehouse-id="<?= $summary['warehouse_id']; ?>"
										data-sublocation-id="<?= $summary['sublocation_id']; ?>">
										Edit
									</a>
								</td>
								
							</tr>
					<?php $n++; endforeach; endif; ?>
				</tbody>
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
	$(document).ready(function() {
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
   $(document).ready(function() {
      $('.select2').select2();
   });
</script>
<script>
$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        responsive: true
    });

    $('#dataTable tbody').on('click', '.toggle-details', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
        } else {
            var details = tr.data('details');
            var html = formatChildRows(details);
            row.child(html).show();
        }
    });
	
	var baseUrl = "<?php echo base_url(); ?>";
	function formatChildRows(details) {
		var html = '<table class="table table-bordered mb-0" style="background-color:#f9f9f9;">';
		html += '<thead><tr>' +
			'<th>SR.#</th>' +
			'<th>Material Number</th>' +
			'<th>Batch</th>' +
			'<th>Lot Position</th>' +
			'<th>Pallet Number</th>' +
			'<th>Pallet Position</th>' +
			'<th>Pallet Qty</th>' +
			'<th>Pieces Qty</th>' +
			'<th>Notes</th>' +
			'<th>Material files</th>' +
			'<th>Action</th>' +
		'</tr></thead><tbody>';

		details.forEach(function(detail, index) {
			html += '<tr>' +
				'<td>' + (index + 1) + '</td>' +
				'<td>' + (detail.materialNumber ?? '') + '</td>' +
				'<td>' + (detail.batch ?? '') + '</td>' +
				'<td>' + (detail.lotNumber ?? '') + '</td>' +
				'<td>' + (detail.palletNumber ?? '') + '</td>' +
				'<td>' + (detail.palletPosition ?? '') + '</td>' +
				'<td>' + (detail.palletQuantity ?? '') + '</td>' +
				'<td>' + (detail.piecesQuantity ?? '') + '</td>' +
				'<td>' + (detail.notes ?? '') + '</td>' +
				'<td>';

			if (Array.isArray(detail.files) && detail.files.length > 0) {
				detail.files.forEach(function(file, i) {
					let shortName = file.length > 20 ? file.substring(0, 16) + '...' : file;
					html += '<a href="' + baseUrl + 'assets/warehouse-inbounds/inbound-materials/' + file + '" target="_blank" style="color:blue;">' + shortName + '</a><br>';
				});
			} else if (detail.file) {
				let shortName = detail.file.length > 20 ? detail.file.substring(0, 16) + '...' : detail.file;
				html += '<a href="' + baseUrl + 'assets/warehouse-inbounds/inbound-materials/' + detail.file + '" target="_blank" style="color:blue;">' + shortName + '</a>';
			} else {
				html += 'No File';
			}

			html += '</td>' +
				'<td>' +
					'<a class="btn btn-sm btn-success" target="_blank" href="' + baseUrl + 'admin/warehouse/updateInbounds/' + detail.id + '?dated=' + encodeURIComponent(detail.dated) + '">Edit</a> ' +
					'<a class="btn btn-sm btn-danger" href="' + baseUrl + 'admin/warehouse/deleteInbounds/' + detail.id + '" onclick="return confirm(\'Delete this item?\')">Delete</a>' +
				'</td>' +
			'</tr>';
		});

		html += '</tbody></table>';
		return html;
	}

	$(document).on('click', '.internal-transfer-btn', function (e) {
		e.preventDefault();

		let bound_id = $(this).data('id');
		let detailIds = $(this).data('detail-ids'); 
		// console.log(detailIds);

		let date = $(this).data('date');
		let customerId = $(this).data('customer-id');
		let warehouseId = $(this).data('warehouse-id');
		let sublocationId = $(this).data('sublocation-id');
		let detailParam = encodeURIComponent(JSON.stringify(detailIds));
		let url = $(this).attr('href') +
			'?bound_id=' + bound_id +
			'&detailIds=' + detailParam +
			'&dated=' + date +
			'&customerId=' + customerId +
			'&warehouseId=' + warehouseId +
			'&sublocationId=' + sublocationId;

		window.location.href = url;
	});

	$(document).on('click', '.outbound-btn', function (e) {
		e.preventDefault();

		let bound_id = $(this).data('id');
		let detailIds = $(this).data('detail-ids'); 

		let date = $(this).data('date');
		let customerId = $(this).data('customer-id');
		let warehouseId = $(this).data('warehouse-id');
		let sublocationId = $(this).data('sublocation-id');
		let detailParam = encodeURIComponent(JSON.stringify(detailIds));
		let url = $(this).attr('href') +
			'?bound_id=' + bound_id +
			'&detailIds=' + detailParam +
			'&dated=' + date +
			'&customerId=' + customerId +
			'&warehouseId=' + warehouseId +
			'&sublocationId=' + sublocationId;

		window.location.href = url;
	});

	$(document).on('click', '.edit-btn', function (e) {
		e.preventDefault();

		let bound_id = $(this).data('id');
		let detailIds = $(this).data('detail-ids'); 

		let date = $(this).data('date');
		let customerId = $(this).data('customer-id');
		let warehouseId = $(this).data('warehouse-id');
		let sublocationId = $(this).data('sublocation-id');
		let detailParam = encodeURIComponent(JSON.stringify(detailIds));
		let url = $(this).attr('href') +
			'?bound_id=' + bound_id +
			'&detailIds=' + detailParam +
			'&dated=' + date +
			'&customerId=' + customerId +
			'&warehouseId=' + warehouseId +
			'&sublocationId=' + sublocationId;

		window.location.href = url;
	});

});
</script>
<style>
	.table td, .table th {
		vertical-align: middle !important;
		text-align: center !important;
	}
	.table th {
		font-weight: bold !important;
	}
	.select2 .select2-container .select2-container--default{
		width: 200px !important;
	}
</style>