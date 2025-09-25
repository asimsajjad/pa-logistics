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
<div class="card mb-3">
	<div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
    	<h3 class="mb-0">Warehouse Inbounds</h3>
    	<div class="add_page d-flex" style="float: right;">
    		<a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/warehouse/addInbounds';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
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
				<input type="text" required readonly placeholder="Start Date" value="<?php echo $sdate; ?>" name="sdate" style="width: 120px;" class="form-control datepicker"> &nbsp;
				<input type="text" required style="width: 120px;" readonly placeholder="End Date" value="<?php echo $edate; ?>" name="edate" class="form-control datepicker"> &nbsp;
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
				</select>
				&nbsp; 
        		<select name="materialId[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Material" style="max-width: 250px;">
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
				<input type="submit" id="submitBtn" class="btn btn-success pt-cta">
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
						<th>Action</th>
					</tr>
				</thead>    
				<tbody>
					<?php if (!empty($warehouse)): 
						$n = 1;
						foreach ($warehouse as $summary): ?>
							<tr class="parent-row" data-details='<?php echo json_encode($summary["details"]); ?>'>
								<td style=""><?php echo $n; ?></td>
								<td style=""><?php echo $summary['dateIn']; ?></td>
								<td style=""><?php echo $summary['customer']; ?></td>
								<td style=""><?php echo $summary['warehouse'] .' ( ' .$summary['warehouseAddress'].' )';?></td>
								<td style=""><?php echo $summary['sublocation']; ?></td>
								<td style="cursor:pointer; color: blue;" class="toggle-details"><?php echo $summary['palletQuantity']; ?></td>
								<td style=""><?php echo $summary['piecesQuantity']; ?></td>
								<td>
									<a class="btn btn-sm btn-danger" href="<?php echo base_url('Warehouse/downloadBoundPDF/'.$summary['id']);?>?date=<?=$summary['dateIn'] ?>&customerId=<?=$summary['customerId'] ?>&warehouseId=<?=$summary['warehouseAddressId'] ?>&sublocationId=<?=$summary['sublocationId'] ?>&dTable=warehouseInbounds">Get Inward RD</a> 
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
			'<th style="width: 0px;">SR.#</th>' +
			'<th style="">Material Number</th>' +
			'<th style="">Batch</th>' +
			'<th style="">Lot Position</th>' +
			'<th style="">Pallet Number</th>' +
			'<th style="">Pallet Position</th>' +
			'<th style="">Pallet Qty</th>' +
			'<th style="">Pieces Qty</th>' +
			'<th style="">Action</th>' +
		'</tr></thead><tbody>';

		details.forEach(function(detail, index) {
			html += '<tr>' +
				'<td style="">' + (index + 1) + '</td>' +
				'<td style="">' + (detail.materialNumber ?? '') + '</td>' +
				'<td style="">' + (detail.batch ?? '') + '</td>' +
				'<td style="">' + (detail.lotNumber ?? '') + '</td>' +
				'<td style="">' + (detail.palletNumber ?? '') + '</td>' +
				'<td style="">' + (detail.palletPosition ?? '') + '</td>' +
				'<td style="">' + (detail.palletQuantity ?? '') + '</td>' +
				'<td style="">' + (detail.piecesQuantity ?? '') + '</td>' +
				'<td style="">' +
					'<a class="btn btn-sm btn-success" target="_blank" href="' + baseUrl + 'admin/warehouse/updateInbounds/' + detail.id + '">Edit</a> ' +
					'<a class="btn btn-sm btn-danger" href="' + baseUrl + 'admin/warehouse/deleteInbounds/' + detail.id + '" onclick="return confirm(\'Delete this item?\')">Delete</a>' +
				'</td>' +
			'</tr>';
		});

		html += '</tbody></table>';
		return html;
	}
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
</style>