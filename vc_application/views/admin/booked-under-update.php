<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Booked Under 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/booked-under');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			    <div class="container">

				    <h3> Update Booked Under</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($bookedUnder)){
                  $key = $bookedUnder[0];
                  
                    ?>    
		           <form method="post" action="<?php echo base_url('admin/booked-under/update/').$this->uri->segment(4);?>" enctype="multipart/form-data" class="row">
                      
						 <?php  echo validation_errors();?>
						 
                    <div class="col-sm-4">     
						<div class="form-group">
                            <label for="contain">Company</label>
                            <input class="form-control" type="text" name="company" value="<?php echo $key['company'] ;?>" required/>
                        </div>
					</div>	
					<div class="col-sm-4">     
						<div class="form-group">
                            <label for="contain">Email</label>
                            <input class="form-control" type="email" name="email" value="<?php echo $key['email'] ;?>"/>
                        </div>
					</div>	
					<div class="col-sm-4">     
						<div class="form-group">
                            <label for="contain">Phone</label>
                            <input class="form-control" type="tel" name="phone" value="<?php echo $key['phone'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-4">     
						<div class="form-group">
                            <label for="contain">#MC</label>
                            <input class="form-control" type="text" name="mc" value="<?php echo $key['mc'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-4"> 
						<div class="form-group">
                            <label for="contain">#EIN</label>
                            <input class="form-control" type="text" name="ein" value="<?php echo $key['ein'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-4"> 
						<div class="form-group">
                            <label for="contain">#DOT</label>
                            <input class="form-control" type="text" name="dot" value="<?php echo $key['dot'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-4"> 
						<div class="form-group">
                            <label for="contain">Owner</label>
                            <input class="form-control" type="text" name="owner" value="<?php echo $key['owner'] ;?>"/>
                        </div>
					</div>
					
					<div class="col-sm-4"> 
						<div class="form-group">
                            <label for="contain">Status</label>
                            <select class="form-control" name="status">
                                <option value="Active">Active</option>
                                <option <?php if($key['status']=='Inactive') { echo 'selected'; } ?> value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-sm-4"> </div>
					   
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
<script src="<?php echo base_url('assets/js/jquery.inputmask.min.js'); ?>"></script>
<script>
	$(document).ready(function() {
        $('input[type="tel"]').inputmask("(999) 999-9999");
    });
</script>				
	<style>
	  form  label[for="contain"]{font-weight:bold;}
	.doc-file span {display: block;}
    .doc-file {
    display: inline-block;
    text-align: center;
    max-width: 145px;
    position: relative;
}
.doc-file .remove-file {
    position: absolute;
    right: 0;
    top: -10px;
    padding: 3px;
    color: red;
    font-weight: bold;
    border: 1px solid red;
    line-height: 13px;
}
	</style>			