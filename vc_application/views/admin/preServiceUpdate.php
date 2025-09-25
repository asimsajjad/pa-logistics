<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Predefined Service 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/pre-services');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">

				    <h3> Update Predefined Service</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($preService)){
                  $key = $preService[0];
                  
                    ?>    
					
					<?php  echo validation_errors();?>
						 <div class="clearfix"></div>
						 
		           <form method="post" class="row" action="<?php echo base_url('admin/pre-service/update/').$this->uri->segment(4);?>" enctype="multipart/form-data">
                   
						<div class="col-sm-6">	     
						<div class="form-group">
                            <label for="contain"> Title</label>
                            <input name="title" type="text" class="form-control" value="<?php echo $key['title'];?>" required>
                        </div>
					</div>
					<div class="col-sm-6">   
                        <div class="form-group">
                            <label for="contain"> Frequency</label>
                            <input name="frequency" type="text" class="form-control" value="<?php echo $key['frequency'];?>"> 
                        </div>
                    </div>  
				
                    
					<div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" name="save" value="Update" class="btn btn-success"/>
                        </div>
					</div>
					</form>
                    <?php
                    }
                  ?> 
				</div>
			</div>

</div>

</div>	
			
			
<style>
	form  label[for="contain"]{font-weight:bold;}
	.doc-file span {display: block;}
    .doc-file {display: inline-block;text-align: center;max-width: 145px;position: relative;}
.doc-file .remove-file {position: absolute;right: 0;top: -10px;padding: 3px;color: red;font-weight: bold;border: 1px solid red;line-height: 13px;}
	</style>	
 