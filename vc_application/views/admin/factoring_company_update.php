<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Update Factoring Company 
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/factoringCompany');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
		</div> 
	</div>
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-12 col-md-12 mobile_content">
			<div class="container">		
				<h3> Update Factoring Company</h3> 
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
					</div>
				<?php } ?>
				<?php
					if(!empty($company)){
						$key = $company[0];	
					?>    
					<form method="post" action="<?php echo base_url('admin/factoringCompany/update/').$this->uri->segment(4);?>">
						<?php  echo validation_errors();?>
						<div class="row">
							<div class="col-sm-3 col-md-3 col-xs-3">
								<div class="form-group">
									<label for="contain">Company</label>
									<input class="form-control" type="text" name="company" value="<?php echo $key['company'] ;?>" required/>
								</div>
							</div>
							<div class="col-sm-6 col-md-6 col-xs-6">
								<div class="form-group">
									<label for="contain">Address</label>
									<input class="form-control" type="text" name="address" value="<?php echo $key['address'] ;?>"/>
								</div>
							</div>	
							<div class="col-sm-3 col-md-3 col-xs-3">
								<div class="form-group">
									<label for="contain">Company Contact</label>
									<input class="form-control" type="tel" name="phone" value="<?php echo $key['phone'] ;?>"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4 col-md-4 col-xs-4">
								<div class="form-group">
									<label for="contain">Account Rep.</label>
									<input name="contactPerson" type="tel" class="form-control"  value="<?php echo $key['contactPerson'] ;?>"/>
								</div>
							</div>
							<div class="col-sm-4 col-md-4 col-xs-4">
								<div class="form-group">
									<label for="contain">Email</label>
									<input name="email" type="email" class="form-control" value="<?php echo $key['email'] ;?>"/>
								</div>
							</div>
							<div id="email2-container" class="col-sm-4 col-md-4 col-xs-4">
								<label for="contain">Other Emails</label>
								<?php 
								$emails = explode(',', $key['email2']);
								foreach($emails as $i => $email): ?>
									<div class="email2-group d-flex align-items-center mb-2">
										<input type="email" name="email2[]" class="form-control me-2" value="<?= trim($email); ?>" placeholder="">
										<?php if($i == 0): ?>
											<button type="button" class="btn btn-success btn-sm add-email2">+</button>
										<?php else: ?>
											<button type="button" class="btn btn-danger btn-sm remove-email2">-</button>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3 col-md-3 col-xs-3">
								<div class="form-group">
									<label for="contain">Phone</label>
									<input class="form-control" type="tel" name="phone2" value="<?php echo $key['phone2'] ;?>"/>
								</div>
							</div>
							<div class="col-sm-3 col-md-3 col-xs-3">
								<div class="form-group">
									<label for="contain">Bank</label>
									<input class="form-control" type="text" name="bankName" value="<?php echo $key['bankName'] ;?>"/>
								</div>
							</div>
							<div class="col-sm-3 col-md-3 col-xs-3">
								<div class="form-group">
									<label for="contain">Routing #</label>
									<input class="form-control" type="text" name="routingNumber" value="<?php echo $key['routingNumber'] ;?>"/>
								</div>
							</div>
							<div class="col-sm-3 col-md-3 col-xs-3">
								<div class="form-group">
									<label for="contain">Account #</label>
									<input name="accountNumber" type="text" class="form-control" value="<?php echo $key['accountNumber'] ;?>"/>
								</div>
							</div>
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
