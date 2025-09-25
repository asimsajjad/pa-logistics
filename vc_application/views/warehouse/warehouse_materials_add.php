<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Add Material <div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/warehouseMaterials');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-12 col-md-12 mobile_content">
			<div class="container">
				<h3> Add Material</h3>
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
				<form method="post" action="<?php echo base_url('admin/warehouse/addMaterials');?>">
					<?php  echo validation_errors();?>
					<div class="row">
						<div class="col-sm-3 col-md-3 col-xs-3">
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
							<div class="col-sm-3 col-md-3 col-xs-3">
								<div class="form-group">
									<label for="contain">Material Number</label>
									<input class="form-control" type="text" name="materialNumber" value="" required/>
								</div>
							</div>	
							<div class="col-sm-6 col-md-6 col-xs-6">
								<div class="form-group">
									<label for="contain">Description</label>
									<textarea class="form-control" type="tel" rows="1" name="description" value="" style="height:48px; overflow: hidden;"></textarea>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4 col-md-4 col-xs-4">
								<div class="form-group">
									<label for="contain">Batch</label>
									<input name="batch" type="tel" class="form-control"  value="" required/>
								</div>
							</div>						
							
							<div class="col-sm-4 col-md-4 col-xs-4">
								<div class="form-group">
									<label for="contain">Expiration Date</label>
									<input class="form-control" type="date" name="expirationDate" value=""/>
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
</style>