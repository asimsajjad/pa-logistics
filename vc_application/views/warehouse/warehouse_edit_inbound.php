<div class="card mb-3">
	<div class="card-header">
		<div style="float: left; margin-top:2%;">
			<i class="fas fa-table"></i>
			Edit Inbound
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
					
					<form method="post" action=""  enctype="multipart/form-data">
						<?php  echo validation_errors();?>
						<input type="hidden" name="detail_ids" value='<?= json_encode($detail_ids, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'/>
						<input type="hidden" name="inbound_id" value="<?php echo $bound_id; ?>" />
						<div class="row">
							<div class="col-sm-2 col-md-2 col-xl-2 p-0">
								<div class="form-group">
									<label for="contain">Date In</label>
									<input class="form-control" type="date" name="dated" value="<?php echo $dated; ?>"/>
								</div>
							</div>
							<input type="hidden" name="customerId" value="<?php echo $customer_id; ?>"/>
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
							<div class="col-sm-2 col-md-2 col-xl-2 p-0">
								<div class="form-group">
									<label for="contain">Warehouse</label>
									<select class="form-control select2" name="warehouse_id" id="">
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
							<div class="col-sm-2 col-md-2 col-xl-2 p-0">
								<div class="form-group">
									<label for="contain">Sublocation</label>
									<select class="form-control select2" name="sublocationId" id="sublocationDropdown" required>
										<option value="">Select Sublocation</option>
									</select>
								</div>
							</div>
							<div class="col-sm-3 col-md-3 col-xl-3 p-0">
								<div class="form-group">
									<label>Inbound File</label>
									<input type="file" name="inboundFile[]" class="form-control" multiple />
								</div>
							</div>
						</div>
						<div id="materialContainer">
							<?php
							foreach($materialDetails as $index => $material): ?>
							<div class="material-group row">
								<input type="hidden" name="detail_id[]" value="<?= $material['id']; ?>" />
								<div class="col-xl-2 p-0">
									<label>Material</label>
									<input type="hidden" name="material_id[]" value="<?= $material['material_id']; ?>">
									<select name="materialId[]" class="form-control materialDropdown">
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
									<input name="lotNumber[]" value="<?= $material['lot_number'] ?>" class="form-control"/>
								</div>
								<div class="col-xl-1 p-0">
									<label>Pallet #</label>
									<input name="palletNumber[]" value="<?= $material['pallet_number'] ?>" class="form-control"/>
								</div>
								<div class="col-xl-1 p-0">
									<label>Pallet Pos.</label>
									<input name="palletPosition[]" value="<?= $material['pallet_position'] ?>" class="form-control" />
								</div>
								<div class="col-xl-1 p-0">
									<label>Pallet Qty</label>
									<input name="palletQuantity[]" value="<?= $material['pallet_quantity'] ?>" class="form-control" />
								</div>
								<div class="col-xl-3 col-md-3 col-sm-6 p-0">
									<div class="form-group">
										<label>Pieces Qty</label>
										<div class="d-flex gap-2">
											<input name="piecesQuantity[]" value="<?= $material['pieces_quantity'] ?>" class="form-control" />
											<input type="file" name="inboundMaterialFile[<?= $index ?>][]" class="form-control inboundMaterialFile" multiple />
											<?php if ($index === 0): ?>
												<button type="button" class="btn btn-primary ml-1" id="addMaterial">+</button>
											<?php else: ?>
												<button type="button" class="btn btn-danger ml-1 removeMaterial">-</button>
											<?php endif; ?>
										</div>
									</div>
								</div>
								<div class="col-xl-12 col-md-12 col-sm-12 p-0" style="margin-top: -20px;">
									<div class="form-group">
										<textarea name="notes[]" class="form-control notes" rows="1" style="height:48px; overflow: hidden;" placeholder="Enter notes"><?= $material['notes'] ?></textarea>
									</div>
								</div>
							</div>

							<?php endforeach; ?>
						</div>
						<div class="form-group">
							<input type="submit" name="save" value="Update Inbound" class="btn btn-success mt-2"/>
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
	.select2 .select2-container .select2-container--default .select2-container--below .select2-container--open{
		width : 169px !important;
	}
	.p-0 {
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

	$(document).on('click', '#addMaterial', function () {
		const newRow = `
		<div class="material-group row">
			<input type="hidden" name="detail_id[]" value="">
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
					<label>Description</label>
					<textarea class="form-control description" rows="1" name="description[]" style="height:48px; overflow: hidden;" readonly></textarea>
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0">
				<div class="form-group">
					<label>Batch</label>
					<select class="form-control batchDropdown" name="batch[]" readonly></select>
				</div>
			</div>
			<div class="col-xl-1 col-md-1 col-sm-6 p-0">
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
			<div class="col-xl-3 col-md-3 col-sm-6 p-0">
				<div class="form-group">
					<label>Pieces Quantity</label>
					<div class="d-flex gap-2">
						<input type="number" name="piecesQuantity[]" class="form-control piecesQuantity" required/>
						<input type="file" class="form-control inboundMaterialFile" multiple />
						<button type="button" class="btn btn-danger ml-1 removeMaterial">-</button>
					</div>
				</div>
			</div>
			<div class="col-xl-12 col-md-12 col-sm-12 p-0" style="margin-top: -20px;">
				<div class="form-group">
					<textarea name="notes[]" class="form-control notes" rows="1" style="height:48px; overflow: hidden;" placeholder="Enter notes"></textarea>
				</div>
			</div>
		</div>`;

		$('#materialContainer').append(newRow);

		$('#materialContainer .materialDropdown').last().select2({ 
			width: '100%' 
		});
		reindexFileInputs();
	});

	$(document).on('click', '.removeMaterial', function () {
		$(this).closest('.material-group').remove();
		reindexFileInputs();
	});

    function loadSublocations(warehouseId, selectedId = '') {
        if (warehouseId) {
            $.ajax({
                url: "<?php echo base_url('Warehouse/getSublocationsByWarehouse'); ?>",
                type: "POST",
                data: { warehouse_id: warehouseId },
                dataType: "json",
                success: function (data) {
                    var options = '<option value="">Select Sublocation</option>';
                    $.each(data, function (index, value) {
                        var selected = (value.id == selectedId) ? 'selected' : '';
                        options += '<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>';
                    });
                    $('#sublocationDropdown').html(options);
                }
            });
        } else {
            $('#sublocationDropdown').html('<option value="">Select Sublocation</option>');
        }
    }

    $('select[name="warehouse_id"]').on('change', function () {
        var warehouseId = $(this).val();
        loadSublocations(warehouseId);
    });

    var initialWarehouseId = $('select[name="warehouse_id"]').val();
    var initialSublocationId = "<?php echo $sublocation_id; ?>";
    if (initialWarehouseId) {
        loadSublocations(initialWarehouseId, initialSublocationId);
    }

	function reindexFileInputs() {
		$('.material-group').each(function (index) {
			$(this).find('.inboundMaterialFile').attr('name', `inboundMaterialFile[${index}][]`);
		});
	}
});

</script>
