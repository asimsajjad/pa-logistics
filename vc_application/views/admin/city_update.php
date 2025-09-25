<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update City 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/cities');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-xs-8 col-sm-8 col-md-8 mobile_content">
			    <div class="container">

				    <h3> Update City</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($city)){
                  $key = $city[0];
                  
                    ?>    
		           <form method="post" action="<?php echo base_url('admin/city/update/').$this->uri->segment(4);?>">
                      
						 <?php  echo validation_errors();?>
                         
						<div class="form-group">
                            <label for="contain">City</label>
                            <input class="form-control" type="text" name="city" value="<?php echo $key['city'] ;?>" required/>
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
			
			
			