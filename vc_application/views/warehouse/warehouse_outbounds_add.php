<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Add Outbounds <div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/warehouseOutbounds');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			<div class="container">
				<h3 style="margin-left:-14px"> Add Outbounds</h3>
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
				<form method="post" action="<?php echo base_url('admin/warehouse/addOutbounds');?>">
					<?php  echo validation_errors();?>
					<div class="row" style="">
						<div class="col-xl-2 col-md-4 col-sm-12 p-0">
								<div class="form-group">
									<label for="contain">Date Out</label>
									<input class="form-control" type="date" name="dateOut" value="<?php echo date('Y-m-d'); ?>"/>
								</div>
						</div>
						<div class="col-xl-3 col-md-4 col-sm-4 p-0">
							<div class="form-group">
								<label for="contain" >Customer</label>
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
						<div class="col-xl-3 col-md-4 col-sm-12 p-0">
							<div class="form-group">
								<label for="contain" >Warehouse</label>
								<select class="form-control select2" name="warehouse_id" required>
									<option value="">Select Warehouse</option>
									<?php 
										if(!empty($warehouse_address)){
											foreach($warehouse_address as $val){
												echo '<option value="'.$val['id'].'"';
												if($this->input->post('warehouse_id')==$val['id']) { echo ' selected="selected" '; }
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
					<!-- <div class="form-group form-check mb-3">
						<input type="checkbox" class="form-check-input" id="outboundAll" name="outboundAll">
						<label class="form-check-label" for="outboundAll">Outbound all pallets and pieces for selected material(s)</label>
					</div> -->
					<div id="materialContainer">
						<div class="material-group row">
							<div class="col-xl-2 col-md-2 col-sm-6 p-0">
								<div class="form-group">
									<label style="width : 100%;">Material</label>
									<select class="form-control select2 materialDropdown" name="materialId[]" required>
										<option value="">Select Material</option>
									</select>
								</div>
							</div>
							<div class="col-xl-1 col-md-1 col-sm-6 p-0">
								<div class="form-group">
									<label>Batch</label>
									<select name="batch[]" class="form-control batchDropdown" readonly>
										<option value="">Select Batch</option>
									</select>
								</div>
							</div>
							<div class="col-xl-2 col-md-4 col-sm-6 p-0">
								<div class="form-group">
									<label for="contain">Description</label>
									<textarea class="form-control description" type="tel" rows="1" name="description" value="" style="height:48px; overflow: hidden;" readonly></textarea>
								</div>
							</div>
							<div class="col-xl-1 col-md-4 col-sm-2 p-0 toggle-outbound-field">
								<div class="form-group">
									<label for="contain">Lot Pos.</label>
									<input class="form-control lotNumber" type="text" name="lotNumber[]" value=""/>
								</div>
							</div>
							<div class="col-xl-1 col-md-4 col-sm-1 p-0 toggle-outbound-field">
								<div class="form-group">
									<label>Pallet #</label>
									<select name="palletNumber[]" class="form-control palletNumberDropdown">
										<option value="">Select Pallet Number</option>
									</select>
								</div>
							</div>
							<div class="col-xl-1 col-md-4 col-sm-6 p-0 toggle-outbound-field">
								<div class="form-group">
									<label style="width: 105%;">Pallet Pos.</label>
									<input type="text" name="palletPosition[]" class="form-control palletPosition" />
								</div>
							</div>
							<div class="col-xl-1 col-md-2 col-sm-6 p-0 toggle-outbound-field">
								<div class="form-group">
									<label>Pallet Qty</label>
									<input type="number" name="palletQuantity[]" class="form-control palletQuantity" />
								</div>
							</div>
							<div class="form-group totalPalletsWrapper" style="display: none;">
								<label>Total Pallets</label>
								<input type="number" name="total_pallets[]" class="form-control totalPallets" readonly/>
							</div>
							<div class="form-group totalPiecesWrapper" style="display: none;">
								<label>Total Pieces</label>
								<input type="number" name="total_pieces[]" class="form-control totalPieces" readonly/>
							</div>
							<div class="col-xl-2 col-md-2 col-sm-6 p-0">
								<div class="form-group d-flex gap-2 align-items-end">
									<div class="outbound-input-wrapper w-100">
										<label>Pieces Qty</label>
										<input type="number" name="piecesQuantity[]" class="form-control piecesQuantity" required />
									</div>
									<button type="button" class="btn btn-primary ml-1 addMaterial" id="addMaterial" style="margin-top: 38px;">+</button>
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
<!-- <script src="<?php echo base_url().'assets/js/sweetalert2@11.js'; ?>"></script> -->
<link href="<?php echo base_url('assets/sweet_alert/sweetalert2.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/sweet_alert/sweetalert2@11.js'); ?>"></script>

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
				url: "<?php echo base_url('Warehouse/getOutboundMaterialsByCustomer'); ?>",
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
			<div class="col-xl-1 col-md-1 col-sm-6 p-0">
				<div class="form-group">
					<label>Batch</label>
					<select class="form-control batchDropdown" name="batch[]" readonly>
					</select>
				</div>
			</div>
			<div class="col-xl-2 col-md-4 col-sm-6 p-0">
				<div class="form-group">
					<label for="contain">Description</label>
					<textarea class="form-control description" type="tel" rows="1" name="description" value="" style="height:48px; overflow: hidden;" readonly></textarea>
				</div>
			</div>
			<div class="col-xl-1 col-md-4 col-sm-2 p-0 toggle-outbound-field">
				<div class="form-group">
					<label for="contain">Lot Pos.</label>
					<input class="form-control lotNumber" type="text" name="lotNumber[]" value=""/>
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0 toggle-outbound-field">
				<div class="form-group">
					<label>Pallet #</label>
					<select name="palletNumber[]" class="form-control palletNumberDropdown">
						<option value="">Select Pallet Number</option>
					</select>
				</div>
			</div>
			<div class="col-xl-1 col-md-4 col-sm-6 p-0 toggle-outbound-field">
				<div class="form-group">
					<label style="width:105%;">Pallet Pos.</label>
					<input type="text" name="palletPosition[]" class="form-control palletPosition" />
				</div>
			</div>
			<div class="col-xl-1 col-md-2 col-sm-6 p-0 toggle-outbound-field">
				<div class="form-group">
					<label>Pallet Qty</label>
					<input type="number" name="palletQuantity[]" class="form-control palletQuantity" />
				</div>
			</div>
			<div class="form-group totalPalletsWrapper" style="display: none;">
				<label>Total Pallets</label>
				<input type="number" name="total_pallets[]" class="form-control totalPallets" readonly/>
			</div>
			<div class="form-group totalPiecesWrapper" style="display: none;">
				<label>Total Pieces</label>
				<input type="number" name="total_pieces[]" class="form-control totalPieces" readonly/>
			</div>
			<div class="col-xl-2 col-md-2 col-sm-6 p-0">
				<div class="form-group d-flex gap-2 align-items-end">
					<div class="outbound-input-wrapper w-100">
						<label>Pieces Qty</label>
						<input type="number" name="piecesQuantity[]" class="form-control piecesQuantity" required />
					</div>
					<button type="button" class="btn btn-danger ml-1 removeMaterial" style="margin-top: 38px;">-</button>
				</div>
			</div>			
		</div>`;

		$('#materialContainer').append(newRow);

		$('.materialDropdown').select2({
			width: '100%'
		});
		toggleMaterialFields();
	});

	$(document).on('click', '.removeMaterial', function () {
		$(this).closest('.material-group').remove();
	});

	$(document).on('change', '.materialDropdown', function () {
		const $group = $(this).closest('.material-group');
		const materialId = $(this).val();
		const $batchSelect = $group.find('.batchDropdown');
		const $descTextarea = $group.find('.description');
		const warehouse_id = $('select[name="warehouse_id"]').val();
		const customerId = $('select[name="customerId"]').val();
		const sublocationId = $('select[name="sublocationId"]').val();
		const $totalPallets = $group.find('.totalPallets');
		const $totalPieces = $group.find('.totalPieces');
		if (materialId) {
			$.ajax({
				url: "<?php echo base_url('Warehouse/getBatchesAndPQtyByMaterial'); ?>",
				type: 'POST',
				data: { materialId,	warehouse_id,customerId,sublocationId },
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
						if (response.total_pallets !== undefined) {
							$totalPallets.val(response.total_pallets);
						}

						if (response.total_pieces !== undefined) {
							$totalPieces.val(response.total_pieces);
						}
						fetchPalletNumbers($group);
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

	$(document).on('change', '.batchDropdown', function () {
		const $group = $(this).closest('.material-group');
		fetchPalletNumbers($group);
	});

	let palletMap = {}; 
	function resetPalletDetails($group) {
		$group.find('.lotNumber').val('');
		$group.find('.palletQuantity').val('');
		$group.find('.piecesQuantity').val('');
		$group.find('.palletPosition').val('');
	}
	function fetchPalletNumbers($group) {
		const materialId = $group.find('.materialDropdown').val();
		const batch = $group.find('.batchDropdown').val();
		const $palletDropdown = $group.find('.palletNumberDropdown');
		const warehouse_id = $('select[name="warehouse_id"]').val();
		const sublocation_id = $('select[name="sublocationId"]').val();

		// if (materialId && batch && warehouse_id) {
			$.ajax({
				url: "<?php echo base_url('Warehouse/getPalletsByMaterialAndBatch'); ?>",
				type: 'POST',
				data: { materialId, batch, warehouse_id, sublocation_id},
				dataType: 'json',
				success: function (response) {
					if (response.status === 'success') {
						let options = '<option value="">Select Pallet Number</option>';
						const key = `${materialId}|${batch}`;
						palletMap[key] = response.pallets;
						$.each(response.pallets, function (index, pallet) {
							options += `<option value="${pallet.palletNumber}" data-pallet-pos="${pallet.palletPosition}" data-lot="${pallet.lotNumber}" data-qty="${pallet.palletQuantity}" data-pieces="${pallet.piecesQuantity}">
								${pallet.palletNumber}</option>`;
						});
						$palletDropdown.html(options);
						if (response.pallets.length > 0) {
							const firstPallet = response.pallets[0];
							$palletDropdown.val(firstPallet.palletNumber).trigger('change');
						}
					} else {
						$palletDropdown.html('<option value="">Select Pallet Number</option>');
						resetPalletDetails($group);
					}
				},
				error: function () {
					$palletDropdown.html('<option value="">Select Pallet Number</option>');
					resetPalletDetails($group);
				}
			});
		// }
	}

	$(document).on('change', '.palletNumberDropdown', function () {
		const $selected = $(this).find('option:selected');
		const $group = $(this).closest('.material-group');

		const lotNumber = $selected.data('lot');
		const palletQty = $selected.data('qty');
		const piecesQty = $selected.data('pieces');
		const palletPosition = $selected.data('pallet-pos');

		$group.find('.lotNumber').val(lotNumber !== undefined ? lotNumber : '');
		$group.find('.palletQuantity').val(palletQty !== undefined ? palletQty : '');
		$group.find('.piecesQuantity').val(piecesQty !== undefined ? piecesQty : '');
		$group.find('.palletPosition').val(palletPosition !== undefined ? palletPosition : '');
	});

	function toggleMaterialFields() {
		const isChecked = $('#outboundAll').is(':checked');
		if (isChecked) {
			$('.toggle-outbound-field').hide(); 
			$('.outbound-input-wrapper').hide();
			$('.totalPallets').show();
			 $('.totalPalletsWrapper, .totalPiecesWrapper').show()
                .addClass('col-xl-2 col-md-2 col-sm-6 p-0');
		} else {
			$('.toggle-outbound-field').show();
			$('.outbound-input-wrapper').show();
			 $('.totalPalletsWrapper, .totalPiecesWrapper').hide()
                .removeClass('col-xl-2 col-md-2 col-sm-6 p-0');
		}
	}
	$(document).on('change', '#outboundAll', function () {
		toggleMaterialFields();
	});

});
</script>
<script>
	$(document).ready(function() {
		$('select[name="warehouse_id"]').on('change', function() {
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

	$(document).ready(function () {
		const form = $('form');
		const originalAction = form.attr('action'); 
		const altAction = "<?php echo base_url('Warehouse/outboundAll');?>"; 
		form.on('submit', function (e) {
			if ($('#outboundAll').is(':checked')) {
				e.preventDefault(); 
				Swal.fire({
					title: 'Are you sure?',
					text: 'Do you want to outbound all pallets and pieces for the selected material(s)?',
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes, outbound all',
					cancelButtonText: 'Cancel'
				}).then((result) => {
					if (result.isConfirmed) {
						form.attr('action', altAction); 
						form.off('submit').submit();
					}
				});
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