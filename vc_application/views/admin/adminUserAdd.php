<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Add Admin User 
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/admin-user');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			<div class="container">
				<h3> Add Admin User</h3>
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
					</div>
				<?php } ?>
				<?php  echo validation_errors();?>
				<div class="clearfix"></div>
				<form method="post" action="<?php echo base_url('admin/admin-user/add');?>" class="row" enctype="multipart/form-data">
					
					<div class="col-sm-4">   
						<div class="form-group">
							<label for="contain"> Name</label>
							<input name="uname" type="text" class="form-control" required>
						</div>
					</div>  
					<div class="col-sm-4">    
						<div class="form-group">
							<label for="contain">Email</label>
							<input name="email" type="email" class="form-control">
						</div>
					</div> 
					<div class="col-sm-4">    
						<div class="form-group">
							<label for="contain">Phone</label>
							<input name="phone" type="tel" class="form-control">
						</div>
					</div> 
					<div class="col-sm-4">    
						<div class="form-group">
							<label for="contain">Username</label>
							<input required name="username" type="text" class="form-control">
						</div>
					</div> 
					<div class="col-sm-4">	
						<div class="form-group">
							<label for="contain"> Password</label>
							<input name="password" type="password" class="form-control" required>
						</div>
					</div>
					
					<div class="col-sm-4">    
						<div class="form-group">
							<label for="contain"> Status</label>
							<select name="status" class="form-control">
								<option value="Active">Active</option>
								<option value="Deactive">Deactive</option>
							</select>
						</div>
					</div> 
					
					<div class="col-sm-12"> 
						<label for="contain"> Permission</label><br>
						<?php 
						foreach($permission as $pkey=>$per){
							echo '<label><input type="checkbox" name="permission[]" value="'.$pkey.'"> '.$per.'</label> &nbsp;';
						}
						?>
					</div>
						
					<div class="col-sm-12">
						<div class="form-group">
							<input type="submit" name="save" value="Add User" class="btn btn-success"/>
						</div>
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