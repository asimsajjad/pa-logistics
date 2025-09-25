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
    <h3 class="mb-0">Material History Report</h3>
    <div class="add_page" style="float: right;">
    </div>
  </div>
  <div class="card-bodys table_style">   
    	<div class="d-block text-center">
			<form class="form form-inline" method="post" id="" action="">	
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
            <th style="">Material Number (Batch)</th>
			<th>Lot position</th>
			<th>Pallet Number</th>
			<th>Pallet Position</th>
			<th style="">Pallet Quantity</th>
            <th style="">Pieces Quantity</th>
            <th style="">Movement Date</th> 
			<th>Movement Type</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <?php
            if(!empty($materialHistory)){
            	$n=1;
              foreach($materialHistory as $key)
              {
            ?>
                <td style=""><?php echo $n;?></td> 
                <td style=""><?php echo $key['customer'];?></td> 
				<td style=""><?php echo $key['warehouse'];?></td> 
				<td style=""><?php echo $key['sublocation'];?></td> 
                <td style=""><?php echo $key['material'];?></td> 
				<td style=""><?php echo $key['lot_number'];?></td> 
				<td style=""><?php echo $key['pallet_number'];?></td> 
				<td style=""><?php echo $key['pallet_position'];?></td> 
				<td style=""><?php echo $key['pallet_quantity'];?></td> 
                <td style=""><?php echo $key['pieces_quantity'];?></td> 
                <td style="">
					<?php 
						$dated = $key['dated'];
						echo date('Y-m-d', strtotime($dated)); 
					?>
				</td>
				<td><?php echo $key['movement_type'] ?></td>
            </tr>
			<?php $n++; } 
			}?>
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