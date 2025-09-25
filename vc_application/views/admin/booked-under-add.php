<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Booked Under <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/booked-under');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			    <div class="container">
				    <h3> Add Booked Under</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
					<form method="post" action="<?php echo base_url('admin/booked-under/add');?>" class="row">
						 <?php  echo validation_errors();?>
						     
						<div class="form-group col-sm-4">
                            <label for="contain">Company</label>
                            <input name="company" type="text" class="form-control" required>
                        </div> 
						<div class="form-group col-sm-4">
                            <label for="contain">Email</label>
                            <input name="email" type="email" class="form-control">
                        </div>
						<div class="form-group col-sm-4">
                            <label for="contain">Phone</label>
                            <input name="phone" type="tel" class="form-control">
                        </div>
						<div class="form-group col-sm-3">
                            <label for="contain">#MC</label>
                            <input name="mc" type="text" class="form-control">
                        </div>
						<div class="form-group col-sm-3">
                            <label for="contain">#DOT</label>
                            <input name="dot" type="text" class="form-control">
                        </div>
						<div class="form-group col-sm-3">
                            <label for="contain">#EIN</label>
                            <input name="ein" type="text" class="form-control">
                        </div>
						<div class="form-group col-sm-3">
                            <label for="contain">Owner</label>
                            <input name="owner" type="text" class="form-control">
                        </div>
                      

                        <div class="form-group">
                            <input type="submit" name="save" value="Submit" class="btn btn-success"/>
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