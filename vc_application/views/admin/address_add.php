<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Add Company Address <div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/company-address');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-8 col-md-8 mobile_content">
			<div class="container">
				<h3> Add Company Address</h3>
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
					</div>
				<?php } ?>
				<form method="post" action="<?php echo base_url('admin/address/add');?>">
					<?php  echo validation_errors();?>
					 
					<div class="row">
					    <div class="col-sm-8">
					        <div class="form-group">
    							<label for="contain">Company</label>
						        <input name="company" type="text" class="form-control" required>
    						</div>
					    </div>
					    <div class="col-sm-4">
					        <div class="form-group">
    							<label for="contain">Email</label>
    							<input class="form-control" type="email" name="email"  />
    						</div>
					    </div>
					</div>
					<div class="row">
					    <div class="col-sm-8">
					        <div class="form-group">
    							<label for="contain">Address</label>
    							<input class="form-control" type="text" name="address" required />
    						</div>
					    </div>
					    <div class="col-sm-4">
					        <div class="form-group">
    							<label for="contain">Phone</label>
    							<input class="form-control" type="tel" name="phone"  />
    						</div>
					    </div>
					</div>
					<div class="row">
					    <div class="col-sm-4">
					        <div class="form-group">
    							<label for="contain">City</label>
    							<input class="form-control" type="text" name="city" required />
    						</div>
					    </div>
					    <div class="col-sm-4">
					        <div class="form-group">
    							<label for="contain">State</label>
    							<input class="form-control" type="text" name="state" required />
    						</div>
					    </div>
					    <div class="col-sm-4">
					        <div class="form-group">
    							<label for="contain">Zip Code</label>
    							<input class="form-control" type="text" name="zip" />
    						</div>
					    </div>
					    
					    <div class="col-sm-6">
					        <div class="form-group">
    							<label for="contain">Shipping Hours</label>
    							<input class="form-control" type="text" name="shippingHours" value=""  />
    						</div>
					    </div>
						    <div class="col-sm-6">
						        <div class="form-group">
        							<label for="contain">Receiving Hours</label>
        							<input class="form-control" type="text" name="receivingHours" value=""  />
        						</div>
						    </div>
					</div>
					 
					
					
					<div class="form-group">
						<input type="submit" name="save" value="Add Company" class="btn btn-success"/>
					</div>
				</form>
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