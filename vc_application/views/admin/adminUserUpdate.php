<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Update Admin User 
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/admin-user');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			<div class="container">
				<h3> Update Admin User</h3>
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
					</div>
				<?php } ?>
				
				<?php
					
					if(!empty($adminUser)){
						$key = $adminUser[0];
						$id = $this->uri->segment(4);
					?>  
					
					<?php  echo validation_errors();?>
					<div class="clearfix"></div>
					<form method="post" action="<?php echo base_url('admin/admin-user/update/').$id;?>" class="row" enctype="multipart/form-data">
						
						<div class="col-sm-4">   
							<div class="form-group">
								<label for="contain"> Name</label>
								<input name="uname" value="<?php echo $key['uname'];?>" type="text" class="form-control" required>
							</div>
						</div>  
						<div class="col-sm-4">    
							<div class="form-group">
								<label for="contain">Email</label>
								<input name="email" type="email" class="form-control" value="<?php echo $key['email'];?>">
							</div>
						</div> 
						<div class="col-sm-4">    
							<div class="form-group">
								<label for="contain">Phone</label>
								<input name="phone" type="tel" class="form-control" value="<?php echo $key['phone'];?>">
							</div>
						</div> 
						<div class="col-sm-4">    
							<div class="form-group">
								<label for="contain">Username</label>
								<input required name="username" type="text" class="form-control"value="<?php echo $key['track'];?>" readonly>
							</div>
						</div> 
						<div class="col-sm-4">	
							<div class="form-group">
								<label for="contain"> Password</label>
								<input name="password" type="password" class="form-control" value="">
							</div>
						</div>
						
						<div class="col-sm-4">    
							<div class="form-group">
								<label for="contain"> Status</label>
								<select name="status" class="form-control">
									<option value="Active">Active</option>
									<option <?php if($key['status']=='Deactive') { echo 'selected'; } ?> value="Deactive">Deactive</option>
								</select>
							</div>
						</div> 
						
						<div class="col-sm-12"> 
							<label for="contain"> Permission</label><br>
							<?php 
							$permissions = array();
							if($key['permission']!=''){
								$permissions = explode(',',$key['permission']);
							}
							foreach($permission as $pkey=>$per){
								echo '<label><input type="checkbox" name="permission[]" value="'.$pkey.'"';
								if(in_array($pkey,$permissions)) { echo ' checked '; }
								echo '> '.$per.'</label> &nbsp;';
							}
							?>
							
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<input type="submit" name="save" value="Update User" class="btn btn-success"/>
							</div>
						</div>
					</form>
				<?php } ?>
			</div>
		</div>
		
	</div>	
	
</div>	


<!--link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
	$( function() {
    $( ".datepicker" ).datepicker({
	dateFormat: 'yy-mm-dd',
	changeMonth: true,
	changeYear: true
    });
	});
</script-->
  <script src="<?php echo base_url('assets/js/jquery.inputmask.min.js'); ?>"></script>
<script>
	$(document).ready(function() {
        $('input[type="tel"]').inputmask("(999) 999-9999");
    });
</script>	