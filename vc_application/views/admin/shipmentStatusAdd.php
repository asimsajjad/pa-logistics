 <!-- order update -->
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Add Shipment Status 
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/shipment-status');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-8 col-md-8 mobile_content">
			<div class="container">
				<h3> Add Shipment Status</h3>
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item');$this->session->set_flashdata('item',''); ?></h4> 
					</div>
				<?php } ?>
				<form method="post" action="<?php echo base_url('admin/shipment-status/add');?>">
					<?php  echo validation_errors();?>
					<div class="form-group">
						<label for="contain">Title</label>
						<input name="title" type="text" class="form-control" required>
					</div> 
					
					<div class="form-group">
						<label for="contain">Status</label>
						<select name="status" class="form-control" required>
							<option value="Active">Active</option>
							<option value="Deactive">Deactive</option>
						</select>
					</div> 

					<div class="form-group">
						<label for="contain">Dropdown Order</label>
						<input name="order" type="text" class="form-control" required>
					</div> 

					<div class="form-group">
						<input type="submit" name="save" value="Add Shipment" class="btn btn-success"/>
					</div>
				</form>
			</div>
		</div>
	</div>	
</div>	