<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Carriers <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/trucking-companies');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			    <div class="container">
				    <h3> Add Carriers</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
					<form method="post" action="<?php echo base_url('admin/trucking-company/add');?>" class="row">
						 <?php  echo validation_errors();?>
						     
						<div class="form-group col-sm-3">
                            <label for="contain">Company</label>
                            <input name="company" type="text" class="form-control" required>
                        </div> 
						<div class="form-group col-sm-3">
                            <label for="contain">Phone</label>
                            <input name="password" type="tel" class="form-control">
                        </div>
                        <div class="form-group col-sm-3">
                            <label for="contain">Email</label>
                            <input name="email" type="email" class="form-control">
                        </div>
                        <div class="col-sm-3 col-md-3 col-xs-3">
							<div class="form-group">
								<label for="contain">Other Emails</label>
								<div id="email2-container">
									<div class="email2-group d-flex align-items-center mb-2">
										<input name="email2[]" type="email" class="form-control me-2" placeholder="">
										<button type="button" class="btn btn-success btn-sm add-email2">+</button>
									</div>
								</div>
							</div>
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
                            <input type="submit" name="save" value="Add Trucking Company" class="btn btn-success"/>
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
$(document).on('click', '.add-email2', function() {
    const newInput = `
        <div class="email2-group d-flex align-items-center mb-2">
            <input type="email" name="email2[]" class="form-control me-2" placeholder="">
            <button type="button" class="btn btn-danger btn-sm remove-email2">-</button>
        </div>`;
    $('#email2-container').append(newInput);
});

$(document).on('click', '.remove-email2', function() {
    $(this).closest('.email2-group').remove();
});

</script>