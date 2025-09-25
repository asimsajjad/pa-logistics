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
    <h3 class="mb-0">Warehouse Stock Report</h3>
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
			<form class="form form-inline" method="post" id="receivableSearchForm" action="">	
				<input type="hidden" name="agingSearch" id="agingSearch" value="<?php echo $agingSearch; ?>">
				<input type="hidden" name="search" id="agingSearch" value="Search">
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
					<select name="warehouseAddressId[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Warehouse" style="max-width: 250px;">
						<option value="">Select a Warehouse</option>
						<?php 
							$selected_warehouse = $this->input->post('warehouseAddressId');
							$warehouseArr = array();
							if(!empty($warehouse_address)){
								foreach($warehouse_address as $val){
									$warehouseArr[$val['id']] = $val['warehouseAddressId'];
									echo '<option value="'.$val['id'].'"';
									if(!empty($selected_warehouse) && in_array($val['id'], $selected_warehouse)) { echo ' selected '; }
									echo '>'.$val['warehouse'].' ('.$val['address'].')</option>';
								}
							}
						?>
					</select>
					&nbsp; 
					<select name="sublocationId[]" class="form-control select2" multiple="multiple" data-placeholder="Select a sublocation" style="max-width: 250px;">
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
				<input type="submit" id="submitBtn" class="btn btn-success pt-cta">
			</form>
		</div>             
    <div class="table-responsive pt-datatbl">
      <table class="table  table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th style="width: 0px;">Sr.#</th>
            <th style="width: 0px;">Customer</th>
			<th style="width: 0px;">Warehouse</th>
			<th style="width: 0px;">Sublocation</th>
            <th style="">Material Number</th>
			<th style="">Description</th>
            <th style="">Batch</th>
			<th style="">Pallet Quantity</th>
            <th style="">Quantity</th>
            <th style="">Expiration Date</th> 
          </tr>
        </thead>
        <tbody>
          <tr>
            <?php
            if(!empty($warehouse)){
            	$n=1;
				$totalPalletQty = 0;
				$totalQty = 0;
              foreach($warehouse as $key)
              {
				// if ($key['quantity'] == 0 && $key['palletQuantity'] == 0) {
				// 	continue;
				// }
            ?>
                <td style=""><?php echo $n;?></td> 
                <td style=""><?php echo $key['customer'];?></td> 
				<td style=""><?php echo $key['warehouse'];?></td> 
				<td style=""><?php echo $key['sublocation'];?></td> 
                <td style=""><?php echo $key['materialNumber'];?></td> 
				<td style=""><?php echo $key['description'];?></td> 
                <td style=""><?php echo $key['batch'];?></td> 
				<td style=""><?php echo $key['palletQuantity'];?></td> 
                <td style=""><?php echo $key['quantity'];?></td> 
                <td style="">
					<?php 
						$expDate = $key['expirationDate'];
						echo (!empty($expDate) && $expDate !== '0000-00-00' && strtotime($expDate)) 
							? date('Y-m-d', strtotime($expDate)) 
							: 'N/A'; 
					?>
				</td>
            </tr>
                <?php
				$totalPalletQty += (float) $key['palletQuantity'];
				$totalQty += (float) $key['quantity'];
                $n++;
              }
            }
            ?>
            </tbody>
			<tfoot>
				<tr>
					<th colspan="6"></th>
					<th style="text-align:right;"><strong>Total:</strong></th>
					<th><?php echo $totalPalletQty; ?></th>
					<th><?php echo $totalQty; ?></th>
					<th></th>
				</tr>
			</tfoot>
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
<style>
	.table td, .table th {
		vertical-align: middle !important;
		text-align: center !important;
	}
	.table th {
		font-weight: bold !important;
	}
</style>