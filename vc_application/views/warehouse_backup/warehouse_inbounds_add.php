<?php 				
?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Add Recieving <div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/warehouseInbounds');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-12 col-md-12 mobile_content">
			<div class="container">
				<h3 style="margin-left:-14px"> Add Recieving</h3>
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
				<form method="post" action="<?php echo base_url('admin/warehouse/addInbounds');?>">
					<?php  echo validation_errors();?>
					<div class="row">
						<div class="col-sm-4 col-md-4 col-xl-2 p-0">
								<div class="form-group">
									<label for="contain">Date IN</label>
									<input class="form-control" type="date" name="dateIn" value="<?php echo date('Y-m-d'); ?>"/>
								</div>
						</div>
						<div class="col-sm-4 col-md-4 col-xl-3 p-0">
							<div class="form-group">
								<label for="contain">Customer</label>
								<select class="form-control select2" name="customerId" required>
									<option value="">Select Customer</option>
									<?php 
										if(!empty($companies)){
											foreach($companies as $val){
													echo '<option value="'.$val['id'].'"';
													if($this->input->post('customerId')==$val['id']) { echo ' selected="selected" '; }
													echo '>'.$val['company'].' ('.$val['owner'].')</option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-4 col-md-4 col-xl-3 p-0">
							<div class="form-group">
								<label for="contain">Warehouse</label>
								<select class="form-control select2" name="warehouseAddressId" required>
									<option value="">Select Warehouse</option>
									<?php 
										if(!empty($warehouse_address)){
											foreach($warehouse_address as $val){
												echo '<option value="'.$val['id'].'"';
												if($this->input->post('warehouseAddressId')==$val['id']) { echo ' selected="selected" '; }
												echo '>'.$val['warehouse'].' ('.$val['address'].')</option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-4 col-md-4 col-xl-3 p-0">
							<div class="form-group">
								<label for="contain">Sublocation</label>
								<select class="form-control select2" name="sublocationId" id="sublocation-dropdown" required>
									<option value="">Select Sublocation</option>
								</select>
							</div>
						</div>
					</div>
					<div id="materialContainer">
						<div class="material-group row">
							<div class="col-xl-2 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label>Material</label>
									<select class="form-control select2 materialDropdown" name="materialId[]" required>
										<option value="">Select Material</option>
									</select>
								</div>
							</div>
							<div class="col-xl-2 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label for="contain">Description</label>
									<textarea class="form-control description" type="tel" rows="1" name="description" value="" style="height:48px; overflow: hidden;" readonly></textarea>
								</div>
							</div>
							<div class="col-xl-1 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label>Batch</label>
									<select name="batch[]" class="form-control batchDropdown" readonly>
										<option value="">Select Batch</option>
									</select>
								</div>
							</div>
							<div class="col-xl-2 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label for="contain">Lot Position</label>
									<input class="form-control lotNumber" type="text" name="lotNumber[]"/>
								</div>
							</div>
							<div class="col-xl-1 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label>Pallet #</label>
									<input type="text" name="palletNumber[]" class="form-control palletNumber" />
								</div>
							</div>
							<div class="col-xl-1 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label>Pallet Pos.</label>
									<input type="text" name="palletPosition[]" class="form-control palletPosition" />
								</div>
							</div>
							<div class="col-xl-1 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label>Pallet Qty</label>
									<input type="number" name="palletQuantity[]" class="form-control palletQuantity" />
								</div>
							</div>
							<div class="col-xl-2 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label>Pieces Qty</label>
									<div class="d-flex gap-2">
										<input type="number" name="piecesQuantity[]" class="form-control piecesQuantity" required/>
										<button type="button" class="btn btn-primary ml-1" id="addMaterial">+</button>
										<button type="button" class="btn btn-secondary ml-1 duplicateMaterial">⧉</button>
									</div>
								</div>
							</div>
							
						</div>
					</div>
					<div class="form-group">
						<input type="submit" name="save" value="Add Item" class="btn btn-success"/>
					</div>
				</form>
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
<script>
$(document).ready(function () {
	let materialOptions = '';
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
						materialOptions = '<option value="">Select Material</option>';
						$.each(materials, function (index, material) {
							materialOptions += `<option value="${material.id}">${material.materialNumber} (${material.batch})</option>`;
						});

						$('.materialDropdown').each(function () {
							$(this).html(materialOptions).trigger('change');
						});
					} catch (e) {
						alert('Failed to load materials.');
					}
				}
			});
		}
	});

	$(document).on('click', '#addMaterial', function () {
		const newRow = `
		<div class="material-group row">
			<div class="col-xl-2 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Material</label>
					<select class="form-control select2 materialDropdown" name="materialId[]" required>
						${materialOptions}
					</select>
				</div>
			</div>
			<div class="col-xl-2 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label for="contain">Description</label>
					<textarea class="form-control description" type="tel" rows="1" name="description" value="" style="height:48px; overflow: hidden;" readonly></textarea>
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Batch</label>
					<select class="form-control batchDropdown" name="batch[]" readonly>
					</select>
				</div>
			</div>
			<div class="col-xl-2 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Lot Pos.</label>
					<input type="text" name="lotNumber[]" class="form-control lotNumber" />
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Pallet #</label>
					<input type="text" name="palletNumber[]" class="form-control palletNumber" />
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Pallet Pos.</label>
					<input type="text" name="palletPosition[]" class="form-control palletPosition" />
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Pallet Qty</label>
					<input type="number" name="palletQuantity[]" class="form-control palletQuantity" />
				</div>
			</div>
			<div class="col-xl-2 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Pieces Quantity</label>
					<div class="d-flex gap-2">
					<input type="number" name="piecesQuantity[]" class="form-control piecesQuantity" required/>
					<button type="button" class="btn btn-danger ml-1 removeMaterial" style="">-</button>
					<button type="button" class="btn btn-secondary ml-1 duplicateMaterial">⧉</button>
				</div>
			</div>
			</div>
		</div>`;

		$('#materialContainer').append(newRow);

		$('.materialDropdown').select2({
			width: '100%'
		});
	});

	$(document).on('click', '.removeMaterial', function () {
		$(this).closest('.material-group').remove();
	});
	
	$(document).on('change', '.materialDropdown', function () {
	const $group = $(this).closest('.material-group');
	const materialId = $(this).val();
	const $batchSelect = $group.find('.batchDropdown');
	const $descTextarea = $group.find('.description');

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
					if (response.description) {
						$descTextarea.val(response.description);
					} else {
						$descTextarea.val('');
					}
				} else {
					$batchSelect.html('<option value="">Select Batch</option>');
					$descTextarea.val('');
				}
			},
			error: function () {
				$batchSelect.html('<option value="">Select Batch</option>');
				$descTextarea.val('');
			}
		});
	} else {
		$batchSelect.html('<option value="">Select Batch</option>');
		$descTextarea.val('');
	}
});


	$(document).on('click', '.duplicateMaterial', function () {
		const $currentGroup = $(this).closest('.material-group');
		const materialVal = $currentGroup.find('.materialDropdown').val();
		const batchVal = $currentGroup.find('.batchDropdown').val();
		const descriptionVal = $currentGroup.find('.description').val();
		const lotNumber = $currentGroup.find('.lotNumber').val();
		const palletNumber = $currentGroup.find('.palletNumber').val();
		const palletPosition = $currentGroup.find('.palletPosition').val();
		const palletQuantity = $currentGroup.find('.palletQuantity').val();
		const piecesQuantity = $currentGroup.find('.piecesQuantity').val();
		// console.log($currentGroup.get(0));
		const newRow = `
		<div class="material-group row">
			<div class="col-xl-2 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Material</label>
					<select class="form-control select2 materialDropdown" name="materialId[]" required>
						${materialOptions}
					</select>
				</div>
			</div>
			<div class="col-xl-2 col-md-4 col-sm-6 p-0">
				<div class="form-group">
					<label for="contain">Description</label>
					<textarea class="form-control description" type="tel" rows="1" name="description" value="" style="height:48px; overflow: hidden;" readonly>${descriptionVal}</textarea>
				</div>
			</div>
			<div class="col-xl-1 col-md-1 col-sm-6 p-0">
				<div class="form-group">
					<label>Batch</label>
					<select class="form-control batchDropdown" name="batch[]" readonly>
						${batchVal ? `<option value="${batchVal}" selected>${batchVal}</option>` : '<option value="">Select Batch</option>'}
					</select>
				</div>
			</div>
			<div class="col-xl-2 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Lot Pos.</label>
					<input type="text" name="lotNumber[]" class="form-control lotNumber" value="${lotNumber}"/>
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Pallet #</label>
					<input type="text" name="palletNumber[]" class="form-control palletNumber" value="${palletNumber}"/>
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Pallet Pos.</label>
					<input type="text" name="palletPosition[]" class="form-control palletPosition" value="${palletPosition}"/>
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Pallet Qty</label>
					<input type="number" name="palletQuantity[]" class="form-control palletQuantity" value="${palletQuantity}"/>
				</div>
			</div>
			<div class="col-xl-2 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Pieces Quantity</label>
					<div class="d-flex gap-2">
						<input type="number" name="piecesQuantity[]" class="form-control piecesQuantity" value="${piecesQuantity}" required/>
						<button type="button" class="btn btn-danger ml-1 removeMaterial">-</button>
						<button type="button" class="btn btn-secondary ml-1 duplicateMaterial">⧉</button>
					</div>
				</div>
			</div>
		</div>`;

		const $newElement = $(newRow);
		$('#materialContainer').append($newElement);

		// Initialize select2 and set selected material
		const $materialSelect = $newElement.find('.materialDropdown');
		$materialSelect.val(materialVal);

		// Reinitialize select2 correctly
		if ($materialSelect.hasClass("select2-hidden-accessible")) {
			$materialSelect.select2('destroy');
		}
		$materialSelect.select2({ width: '100%' });
	});


});
</script>
<script>
$(document).ready(function() {
	$('select[name="warehouseAddressId"]').on('change', function() {
		var warehouseId = $(this).val();
		if(warehouseId) {
			$.ajax({
				url: "<?php echo base_url('Warehouse/getSublocationsByWarehouse'); ?>",
				type: "POST",
				data: { warehouse_id: warehouseId },
				dataType: "json",
				success: function(data) {
					var options = '<option value="">Select Sublocation</option>';
					$.each(data, function(index, value) {
						options += '<option value="' + value.id + '">' + value.name + '</option>';
					});
					$('#sublocation-dropdown').html(options);
				}
			});
		} else {
			$('#sublocation-dropdown').html('<option value="">Select Sublocation</option>');
		}
	});
});
</script>

<style>
	.select2-container--default .select2-selection--single {
    	border-radius: 26px !important;
	    width: 100% !important;

	}
	.select2 select2-container select2-container--default{
 	 	width: 100% !important;
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
	.p-0 {
		padding: 3px !important;
	}
	.container{
      max-width: 100%;
	}
</style>