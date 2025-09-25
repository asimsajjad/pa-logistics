<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Driver <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/drivers');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-8 col-sm-8 col-md-8 mobile_content">
			    <div class="container">
				    <h3> Add Driver</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
					<form method="post" action="<?php echo base_url('admin/driver/add');?>">
						 <?php  echo validation_errors();?>
						     
						<div class="form-group">
                            <label for="contain">Driver Code</label>
                            <input name="dcode" type="text" class="form-control" required>
                        </div>
						<div class="form-group">
                            <label for="contain">Driver</label>
                            <input name="dname" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="contain">Phone</label>
                            <input name="phone" type="tel" class="form-control">
                        </div>
						<div class="form-group">
                            <label for="contain">Email</label>
                            <input name="email" type="email" class="form-control">
                        </div>
						<div class="form-group">
                            <label for="contain">Address</label>
                            <input name="address" type="text" class="form-control">
                        </div>
                      

                        <div class="form-group">
                            <input type="submit" name="save" value="Add Driver" class="btn btn-success"/>
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