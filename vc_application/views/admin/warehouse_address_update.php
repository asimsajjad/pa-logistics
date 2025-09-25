<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Update Warehouse Address
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/warehouseAddress');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
		</div> 
	</div>
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-8 col-md-8 mobile_content">
			<div class="container">
				<h3> Update Warehouse Address</h3> 
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item');$this->session->set_flashdata('item',''); ?></h4>
					</div>
				<?php } 
					if(!empty($warehouse)){
						$key = $warehouse[0];
					?>    
					<form method="post" action="<?php echo base_url('admin/address/warehouseUpdate/').$this->uri->segment(4);?>">
						<?php  echo validation_errors();?>
						<div class="row">
						    <div class="col-sm-8">
						        <div class="form-group">
        							<label for="contain">Warehouse</label>
							        <input class="form-control" type="text" name="warehouse" value="<?php echo $key['warehouse'] ;?>" required />
        						</div>
						    </div>
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">Email</label>
        							<input class="form-control" type="email" name="email" value="<?php echo $key['email'] ;?>" />
        						</div>
						    </div>
						</div>
						<div class="row">
						    <div class="col-sm-8">
						        <div class="form-group">
        							<label for="contain">Address</label>
        							<input class="form-control" type="text" name="address" value="<?php echo $key['address'] ;?>" required />
        						</div>
						    </div>
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">Phone</label>
        							<input class="form-control" type="tel" name="phone" value="<?php echo $key['phone'] ;?>" />
        						</div>
						    </div>
						</div>
						<div class="row">
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">City</label>
        							<input class="form-control" type="text" name="city" value="<?php echo $key['city'] ;?>" required />
        						</div>
						    </div>
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">State</label>
        							<input class="form-control" type="text" name="state" value="<?php echo $key['state'] ;?>" required />
        						</div>
						    </div>
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">Zip Code</label>
        							<input class="form-control" type="text" name="zip" value="<?php echo $key['zip'] ;?>" />
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
		
  <script src="<?php echo base_url('assets/js/jquery.inputmask.min.js'); ?>"></script>
<script>
	$(document).ready(function() {
        $('input[type="tel"]').inputmask("(999) 999-9999");
    });
</script>	