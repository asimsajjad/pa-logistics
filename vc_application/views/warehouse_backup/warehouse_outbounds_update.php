<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Outbounds Update
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/warehouseOutbounds');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
		</div> 
	</div>
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-12 col-md-12 mobile_content">
			<div class="container">		
				<h3 style="margin-left:-14px"> Outbounds Update</h3> 
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
					if(!empty($warehouse)){
						$key = $warehouse[0];
					?>    
					
					<form method="post" action="<?php echo base_url('admin/warehouse/updateOutbounds/').$this->uri->segment(4);?>">
						<?php  echo validation_errors();?>
						<div class="row">
							<div class="col-sm-4 col-md-4 col-xl-2 p-0">
								<div class="form-group">
									<label for="contain">Date Out</label>
									<input class="form-control" type="date" name="dateOut" value="<?php echo date($key['dateOut']); ?>"/>
								</div>
							</div>
							<div class="col-sm-4 col-md-4 col-xl-3 p-0">
								<div class="form-group">
									<label for="contain">Customer</label>
									<select class="form-control select2" name="customerId" id="customerDropdown"  required>
										<option value="">Select Customer</option>
										<?php  
										if (!empty($companies)) {
											foreach ($companies as $val) {
												$selected = ($key['customerId'] == $val['id']) ? 'selected="selected"' : '';
												echo '<option value="' . $val['id'] . '" ' . $selected . '>' . $val['company'] . ' (' . $val['owner'] . ')</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="col-sm-4 col-md-4 col-xl-3 p-0">
								<div class="form-group">
									<label for="contain">Warehouse</label>
									<select class="form-control select2" name="warehouseAddressId" id=""  required>
										<option value="">Select Warehouse</option>
										<?php  
										if (!empty($warehouse_address)) {
											foreach ($warehouse_address as $val) {
												$selected = ($key['warehouseAddressId'] == $val['id']) ? 'selected="selected"' : '';
												echo '<option value="' . $val['id'] . '" ' . $selected . '>' . $val['warehouse'] . ' (' . $val['address'] . ')</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="col-sm-4 col-md-4 col-xl-3 p-0">
								<div class="form-group">
									<label for="contain">Sublocation</label>
									<select class="form-control select2" name="sublocationId" id="sublocationDropdown" required>
										<option value="">Select Sublocation</option>
									</select>
								</div>
							</div>
						</div>

						<div id="materialContainer">
						<div class="material-group row">
							<div class="col-xl-2 col-md-4 col-sm-6 p-0">
								<div class="form-group">
									<label>Material</label>
									<select class="form-control select2 materialDropdown" name="materialId" required>
									<option value="">Select Material</option>
									<?php  
									
									?>
								</select>
								</div>
							</div>
							<div class="col-xl-1 col-md-4 col-sm-6 p-0">
								<div class="form-group">
									<label>Batch</label>
									<select class="form-control batchDropdown" name="batch" readonly>
									<option value="">Select Batch</option>
									<?php  
									
									?>
								</select>
								</div>
							</div>
							<div class="col-xl-2 col-md-4 col-sm-6 p-0">
								<div class="form-group">
									<label for="contain">Description</label>
									<textarea class="form-control description" type="tel" rows="1" name="description" value="" style="height:48px; overflow: hidden;" readonly></textarea>
								</div>
							</div>
							<div class="col-xl-2 col-md-4 col-sm-6 p-00">
								<div class="form-group">
									<label for="contain">Lot Position</label>
									<input class="form-control" type="text" name="lotNumber" value="<?php echo $key['lotNumber']; ?>"/>
								</div>
							</div>
							<div class="col-xl-1 col-md-4 col-sm-6 p-0">
								<div class="form-group">
									<label>Pallet #</label>
									<input type="text" name="palletNumber" class="form-control" value="<?php echo $key['palletNumber']; ?>"/>
								</div>
							</div>
							<div class="col-xl-1 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label>Pallet Pos.</label>
									<input type="text" name="palletPosition" class="form-control palletPosition" value="<?php echo $key['palletPosition']; ?>"/>
								</div>
							</div>
							<div class="col-xl-1 col-md-4 col-sm-6 p-0">
								<div class="form-group">
									<label>Pallet Qty</label>
									<input type="number" name="palletQuantity" class="form-control" value="<?php echo $key['palletQuantity']; ?>"/>
								</div>
							</div>
							<div class="col-xl-2 col-md-4 col-sm-6 p-0">
								<div class="form-group">
									<label>Pieces Quantity</label>
									<div class="d-flex gap-2">
										<input type="number" name="piecesQuantity" class="form-control" value="<?php echo $key['piecesQuantity']; ?>" required/>
									</div>
								</div>
							</div>
							
						</div>
					</div>
						<div class="form-group">
							<input type="submit" name="save" value="Update" class="btn btn-success"/>
						</div>
					</form>
					<?php
					}
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
                            $(this).html(materialOptions);

                            // Auto-select material from warehouse if available
                            let autoSelectMaterialId = "<?php echo !empty($warehouse) ? $warehouse[0]['materialId'] : ''; ?>";
                            
                            if (autoSelectMaterialId) {
                                $(this).val(autoSelectMaterialId).trigger('change');
                            }
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
        const $batchSelect = $(this).closest('.material-group').find('.batchDropdown');
		const $descTextarea =  $(this).closest('.material-group').find('.description');

        if (materialId) {
            $.ajax({
                url: "<?php echo base_url('Warehouse/getBatchesByMaterial'); ?>",
                type: 'POST',
                data: { materialId },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success' && response.batch) {
                        $batchSelect.html(`<option value="${response.batch}" selected>${response.batch}</option>`);
                    } else {
                        $batchSelect.html('<option value="">Select Batch</option>');
                    }
					if (response.description) {
						$descTextarea.val(response.description);
					} else {
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

    $('#customerDropdown').trigger('change');
});

$(document).ready(function() {
	const warehouseDropdown = $('select[name="warehouseAddressId"]');
	const sublocationDropdown = $('#sublocationDropdown');
	const selectedSublocationId = '<?php echo $key["sublocationId"]; ?>';

	function loadSublocations(warehouseId, selectedId = null) {
		if (warehouseId) {
			$.ajax({
				url: "<?php echo base_url('Warehouse/getSublocationsByWarehouse'); ?>",
				type: "POST",
				data: { warehouse_id: warehouseId },
				dataType: "json",
				success: function(data) {
					let options = '<option value="">Select Sublocation</option>';
					$.each(data, function(index, sublocation) {
						let selected = (sublocation.id == selectedId) ? ' selected' : '';
						options += `<option value="${sublocation.id}"${selected}>${sublocation.name}</option>`;
					});
					sublocationDropdown.html(options);
				}
			});
		} else {
			sublocationDropdown.html('<option value="">Select Sublocation</option>');
		}
	}

	// Initial load on form render if warehouse is pre-selected
	const initialWarehouseId = warehouseDropdown.val();
	if (initialWarehouseId) {
		loadSublocations(initialWarehouseId, selectedSublocationId);
	}

	// On warehouse change, reload sublocations
	warehouseDropdown.on('change', function() {
		loadSublocations($(this).val());
	});
});
</script>
