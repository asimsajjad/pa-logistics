<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Vehicle <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/vehicles');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				    <h3> Add Vehicle</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
					<form method="post" action="<?php echo base_url('admin/vehicle/add');?>" class="row">
					<div class="col-sm-4">	     
						<div class="form-group">
                            <label for="contain"> Vehicle</label>
                            <input name="vname" type="text" class="form-control" required>
                        </div>
					</div>
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Vehicle Number</label>
                            <input name="vnumber" type="text" class="form-control" required>
                        </div>
                     </div>
					<div class="col-sm-4">   
                        <div class="form-group">
                            <label for="contain"> License Plate</label>
                            <input name="license_plate" type="text" class="form-control" value="">
                        </div>
                    </div>
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> VIN</label>
                            <input name="vin" type="text" class="form-control" value="">
                        </div>
                    </div>  
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> Make</label>
                            <input name="vmake" type="text" class="form-control" value="">
                        </div>
                    </div> 
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> Model</label>
                            <input name="vmodel" type="text" class="form-control" value="">
                        </div>
                    </div> 
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> Year</label>
                            <input name="vyear" type="number" class="form-control" value="">
                        </div>
                    </div> 
					<!--div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Cab Card</label>
                            <input name="cabcard" type="file" class="form-control">
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Insurance</label>
                            <input name="insurance" type="file" class="form-control">
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Unit Pictures</label>
                            <input name="unitpicture" type="file" class="form-control">
                        </div>
                    </div --> 

					<div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" name="save" value="Add Vehicle" class="btn btn-success"/>
                        </div>
					</div>
					</form>
				</div>
			</div>

		</div>	
			
		</div>	
			