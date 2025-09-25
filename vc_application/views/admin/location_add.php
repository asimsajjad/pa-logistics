<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Location <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/locations');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-8 col-sm-8 col-md-8 mobile_content">
			    <div class="container">
				    <h3> Add Location</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item');$this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
					<form method="post" action="<?php echo base_url('admin/location/add');?>">
						 <?php  echo validation_errors();?>
						     
						<div class="form-group">
                            <label for="contain">City</label>
                            <input name="city" type="text" class="form-control" required>
                        </div> 
                        
                        
						<div class="form-group">
                            <label for="contain">Location</label>
                            <input name="location" type="text" class="form-control" required>
                        </div> 
                        
						<div class="form-group">
                            <label for="contain">Address</label>
                            <input name="address" type="text" class="form-control">
                        </div> 
                        
						<div class="form-group">
                            <label for="contain">Notes</label>
                            <input name="notes" type="text" class="form-control" >
                        </div> 
                      

                        <div class="form-group">
                            <input type="submit" name="save" value="Add Location" class="btn btn-success"/>
                        </div>
					</form>
				</div>
			</div>

		</div>	
			
		</div>	
			