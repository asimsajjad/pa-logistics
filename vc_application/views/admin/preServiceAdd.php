<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Predefined Service <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/pre-services');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				    <h3> Add Predefined Service</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
					<form method="post" action="<?php echo base_url('admin/pre-service/add');?>" class="row" enctype="multipart/form-data">
					 
					<div class="col-sm-6">   
                        <div class="form-group">
                            <label for="contain"> Title</label>
                            <input name="title" type="text" class="form-control" required>
                        </div>
                    </div>  
					<div class="col-sm-6">    
                        <div class="form-group">
                            <label for="contain">Frequency</label>
                            <input  name="frequency" type="text" class="form-control">
                        </div>
                    </div> 
				 
					<div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" name="save" value="Add Service" class="btn btn-success"/>
                        </div>
					</div>
					</form>
				</div>
			</div>

		</div>	
			
		</div>	
			
