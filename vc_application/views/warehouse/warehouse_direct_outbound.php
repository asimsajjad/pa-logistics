<div class="card mb-3">
	<div class="card-header">
		<div style="float: left; margin-top:2%;">
			<i class="fas fa-table"></i>
			Add Outbound
		</div>
		
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/warehouseInbounds');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
		</div> 
	</div>
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-12 col-md-12 mobile_content">
			<div class="container">		
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success p-0" style="margin-left:-14px">
						<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
					</div>
				<?php } elseif($this->session->flashdata('error')){ ?>
					<div class="alert alert-danger p-0" style="margin-left:-14px">
						<h4><?php echo $this->session->flashdata('error'); $this->session->set_flashdata('error',''); ?></h4> 
					</div>
				<?php } else if($this->session->flashdata('warning')) { ?>
					<div class="alert alert-danger p-0" style="margin-left:-14px">
						<h4><?php echo $this->session->flashdata('warning'); $this->session->set_flashdata('warning',''); ?></h4> 
					</div>
				<?php } ?>
				<?php
						$key = $warehouse[0];
					?>    
					
					<form method="post" action="">
						<?php  echo validation_errors();?>
						<input type="hidden" name="detail_ids" value='<?= json_encode($detail_ids, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'/>
						<div class="row">
							<div class="col-sm-4 col-md-4 col-xl-2 p-0">
								<div class="form-group">
									<label for="contain">Date Out</label>
									<input class="form-control" type="date" name="dated" value="<?php echo date('Y-m-d'); ?>"/>
								</div>
							</div>
							<input type="hidden" name="customer_id" value="<?= $customer_id; ?>">
							<div class="col-sm-4 col-md-4 col-xl-3 p-0">
								<div class="form-group">
									<label for="contain">Customer</label>
									<select class="form-control select2" name="customerId" id="customerDropdown" disabled>
										<option value="">Select Customer</option>
										<?php  
										if (!empty($companies)) {
											foreach ($companies as $val) {
												$selected = ($customer_id == $val['id']) ? 'selected="selected"' : '';
												echo '<option value="' . $val['id'] . '" ' . $selected . '>' . $val['company'] . ' (' . $val['owner'] . ')</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
							<input type="hidden" name="warehouse_id" value="<?= $warehouse_id; ?>">
							<div class="col-sm-4 col-md-4 col-xl-3 p-0">
								<div class="form-group">
									<label for="contain">Warehouse</label>
									<select class="form-control select2" name="warehouse_id" id="" disabled>
										<option value="">Select Warehouse</option>
										<?php  
										if (!empty($warehouse_address)) {
											foreach ($warehouse_address as $val) {
												$selected = ($warehouse_id == $val['id']) ? 'selected="selected"' : '';
												echo '<option value="' . $val['id'] . '" ' . $selected . '>' . $val['warehouse'] . ' (' . $val['address'] . ')</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
							<input type="hidden" name="sublocation_id" value="<?= $sublocation_id; ?>">
							<div class="col-sm-4 col-md-4 col-xl-3 p-0">
								<div class="form-group">
									<label for="contain">Sublocation</label>
									<select class="form-control select2" name="sublocationId" id="sublocationDropdown" disabled>
										<?php 
											if(!empty($warehouse_sublocations)){
												foreach($warehouse_sublocations as $val){
													$selected = ($sublocation_id == $val['id']) ? 'selected="selected"' : '';
													echo '<option value="' . $val['id'] . '" ' . $selected . '>' . $val['name'] . '</option>';
												}
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<?php foreach($materialDetails as $material): ?>
						<div class="material-group row">
							<div class="col-xl-2 p-0">
								<label>Material</label>
								<input type="hidden" name="material_id[]" value="<?= $material['material_id']; ?>">
								<select name="materialId[]" class="form-control materialDropdown" disabled>
									<option value="<?= $material['material_id'] ?>" selected><?= $material['name'] ?></option>
								</select>
							</div>
							<div class="col-xl-2 p-0">
								<label>Description</label>
								<textarea name="description[]" style="height:48px; overflow: hidden;" class="form-control description" readonly><?= $material['description'] ?></textarea>
							</div>
							<div class="col-xl-1 p-0">
								<label>Batch</label>
								<select name="batch[]" class="form-control batchDropdown" readonly>
									<option value="<?= $material['batch'] ?>" selected><?= $material['batch'] ?></option>
								</select>
							</div>
							<div class="col-xl-1 p-0">
								<label>Lot Pos.</label>
								<input name="lotNumber[]" value="<?= $material['lot_number'] ?>" class="form-control" readonly/>
							</div>
							<div class="col-xl-1 p-0">
								<label>Pallet #</label>
								<input name="palletNumber[]" value="<?= $material['pallet_number'] ?>" class="form-control" readonly/>
							</div>
							<div class="col-xl-1 p-0">
								<label>Pallet Pos.</label>
								<input name="palletPosition[]" value="<?= $material['pallet_position'] ?>" class="form-control" readonly/>
							</div>
							<div class="col-xl-1 p-0">
								<label>Pallet Qty</label>
								<input name="palletQuantity[]" value="<?= $material['pallet_quantity'] ?>" class="form-control" readonly/>
							</div>
							<div class="col-xl-2 p-0">
								<label>Pieces Qty</label>
								<input name="piecesQuantity[]" value="<?= $material['pieces_quantity'] ?>" class="form-control" readonly/>
							</div>
						</div>
						<?php endforeach; ?>
						<div class="form-group">
							<input type="submit" name="save" value="Outbound" class="btn btn-success mt-2"/>
						</div>
					</form>
					<?php
				?> 
			</div>
		</div>
	</div>
</div>	

	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<script>
   $(document).ready(function() {
      $('.select2').select2();
   });

</script>


<style>
	.select2-container--default .select2-selection--single {
    border-radius: 26px !important;
	}
	.select2-container .select2-selection--single {
    min-height: 46px !important;
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
	.p-0{
		padding: 3px !important;	
	}
	
	.container{
      max-width: 100%;
	}
</style>

<script>
$(document).ready(function () {
	let materialOptions = '';

	function populateMaterialDropdowns(materials) {
		materialOptions = '<option value="">Select Material</option>';
		$.each(materials, function (index, material) {
			materialOptions += `<option value="${material.id}">${material.materialNumber} (${material.batch})</option>`;
		});
		$('.materialDropdown').each(function () {
			const selectedId = $(this).find('option[selected]').val();
			$(this).html(materialOptions);

			if (selectedId) {
				$(this).val(selectedId).trigger('change');
			}
		});
	}

	function autoLoadBatchAndDescription(materialId, $row) {
		const $batchSelect = $row.find('.batchDropdown');
		const $descTextarea = $row.find('.description');
		if (materialId) {
			$.ajax({
				url: "<?php echo base_url('Warehouse/getBatchesByMaterial'); ?>",
				type: 'POST',
				data: { materialId },
				dataType: 'json',
				success: function (response) {
					if (response.status === 'success') {
						if (response.batch) {
							$batchSelect.html(`<option value="${response.batch}" selected>${response.batch}</option>`);
						} else {
							$batchSelect.html('<option value="">Select Batch</option>');
						}
						$descTextarea.val(response.description || '');
					}
				},
				error: function () {
					$batchSelect.html('<option value="">Select Batch</option>');
					$descTextarea.val('');
				}
			});
		}
	}
	$('select[name="customerId"]').on('change', function () {
		const customerId = $(this).val();
		if (customerId) {
			$.ajax({
				url: "<?php echo base_url('Warehouse/getMaterialsByCustomer'); ?>",
				type: "POST",
				data: { customerId: customerId },
				success: function (response) {
					try {
						let materials = JSON.parse(response);
						populateMaterialDropdowns(materials);
						$('.materialDropdown').each(function () {
							const materialId = $(this).val();
							autoLoadBatchAndDescription(materialId, $(this).closest('.material-group'));
						});
					} catch (e) {
						alert('Failed to load materials.');
					}
				}
			});
		}
	});
	$(document).on('change', '.materialDropdown', function () {
		const materialId = $(this).val();
		const $row = $(this).closest('.material-group');
		autoLoadBatchAndDescription(materialId, $row);
	});
	$('#customerDropdown').trigger('change');
});
</script>
