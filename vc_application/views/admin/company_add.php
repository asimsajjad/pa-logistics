<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Add Company <div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/companies');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
		</div> 
	</div> 
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-12 col-md-12 mobile_content">
			<div class="container">
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
					</div>
				<?php } ?>
				<form method="post" action="<?php echo base_url('admin/company/add');?>">
					<?php  echo validation_errors();?>
					<div class="row">
						<div class="col-sm-3 p-1">
							<div class="form-group">
								<label for="contain">Company</label>
								<input name="company" type="text" class="form-control" required>
							</div>
						</div>
						<div class="col-sm-5 p-1">
							<div class="form-group">
								<label for="contain">Address</label>
								<input name="address" type="text" class="form-control">
							</div>
						</div>
						<div class="col-sm-2 p-1">
							<div class="form-group">
								<label for="contain">Company Phone</label>
								<input name="phone" type="tel" class="form-control">
							</div>
						</div>
						<div class="col-sm-2 p-1">
							<div class="form-group">
								<label for="contain">Phone 2</label>
								<input name="phone2" type="tel" class="form-control">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="d-flex align-items-center">
								<hr class="flex-grow-1">
								<span class="px-3 text-muted fw-bold">Shipping Contacts</span>
								<hr class="flex-grow-1">
							</div>
						</div>
						<div id="shipping-contacts-container" class="col-12">
							<div class="row shipping-contact-group align-items-end mb-2">
								<div class="col-sm-3 p-1">
									<div class="form-group">
										<label>Contact Person</label>
										<input class="form-control" type="text" name="shipping_contact_person[]" value="" required/>
									</div>
								</div>
								<div class="col-sm-3 p-1">
									<div class="form-group">
										<label>Email</label>
										<input class="form-control" type="email" name="shipping_email[]" value=""/>
									</div>
								</div>
								<div class="col-sm-2 p-1">
									<div class="form-group">
										<label>Phone</label>
										<input class="form-control" type="tel" name="shipping_phone[]" value=""/>
									</div>
								</div>
								<div class="col-sm-2 p-1">
									<div class="form-group">
										<label>Department</label>
										<input class="form-control" type="text" name="shipping_department[]" value=""/>
									</div>
								</div>
								<div class="col-sm-2 p-1 d-flex">
									<div class="form-group flex-grow-1">
										<label>Designation</label>
										<div class="email2-group d-flex align-items-center">
											<input class="form-control" type="text" name="shipping_designation[]" value=""/>
											<button type="button" class="btn btn-success btn-sm ms-2 add-shipping-contact">+</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="d-flex align-items-center">
								<hr class="flex-grow-1">
								<span class="px-3 text-muted fw-bold">Accounting Contact</span>
								<hr class="flex-grow-1">
							</div>
						</div>
						<div class="col-sm-4 p-1">
							<div class="form-group">
								<label for="contain">Accounting Contact Person</label>
								<input class="form-control" type="text" name="accounting_contact_person" value=""/>
							</div>
						</div>
						<div class="col-sm-4 p-1">
							<div class="form-group">
								<label for="contain">Email</label>
								<input class="form-control" type="email" name="accounting_email" value=""/>
							</div>
						</div>
						<div class="col-sm-4 p-1">
							<div class="form-group">
								<label for="contain">Phone</label>
								<input class="form-control" type="tel" name="accounting_phone" value=""/>
							</div>
						</div>							
					</div>
					<!-- <div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label for="contain">Contact Person</label>
								<input class="form-control" type="text" name="contactPerson" value=""/>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="contain">Department</label>
								<input class="form-control" type="text" name="department" value=""/>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="contain">Designation</label>
								<input class="form-control" type="text" name="designation" value=""/>
							</div>
						</div>
					</div> -->
					<div class="row">
						<div class="col-12">
							<div class="d-flex align-items-center">
								<hr class="flex-grow-1">
								<span class="px-3 text-muted fw-bold">Invoicing Emails</span>
								<hr class="flex-grow-1">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="contain">Email</label>
								<input name="email" type="email" class="form-control">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="contain">Email 2</label>
								<div id="email2-container">
									<div class="email2-group d-flex align-items-center mb-2">
										<input name="email2[]" type="email" class="form-control me-2" placeholder="">
										<button type="button" class="btn btn-success btn-sm add-email2">+</button>
									</div>
								</div>
							</div>
						</div>						
					</div>
					
					<div class="row">
						<div class="col-12">
							<div class="d-flex align-items-center">
								<hr class="flex-grow-1">
								<span class="px-3 text-muted fw-bold">Payment Terms / Status</span>
								<hr class="flex-grow-1">
							</div>
						</div>
						<div class="col-sm-5">
							<div class="form-group">
								<label for="contain">Payment Terms</label>
								<select name="paymenTerms" class="form-control paymenTerms">
									<option value="">Select Payment Terms</option>
									<option value="RTS">RTS</option>
									<option value="Direct Bill">Direct Bill</option>
									<option value="Quick Pay">Quick Pay</option>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="contain">Payment Terms</label>
								<input name="payoutRate" type="text" class="form-control payoutRate">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="contain">Warehouse Customer</label>
								<select name="warehouseCustomer" class="form-control">
									<option value="">Select an option</option>	
									<option value="Yes">Yes</option>
									<option value="No">No</option>
								</select>
							</div>
						</div>
					</div>
					
					
					<div class="form-group">
						<input type="submit" name="save" value="Add Company" class="btn btn-success"/>
					</div>
				</form>
			</div>
		</div>
		
	</div>	
	
</div>	
<script src="<?php echo base_url('assets/js/jquery.inputmask.min.js'); ?>"></script>
<script>
	jQuery(document).ready(function(){
		jQuery('.paymenTerms').change(function(){
			var valu = jQuery(this).val();
			var rate = '0';
			if(valu=='RTS'){ rate = '0.985'; }
			else if(valu=='Direct Bill'){ rate = '1.000'; }
			else if(valu=='Quick Pay'){ rate = '0.980'; }
			jQuery('.payoutRate').val(rate);
		});
		$('input[type="tel"]').inputmask("(999) 999-9999");
	});
	// $(document).ready(function() {
    // $('input[type="tel"]').inputmask("(999) 999-9999");
	// });
	
</script>
<script>
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

$(document).on('click', '.add-shipping-contact', function () {
    const newContact = `
        <div class="row shipping-contact-group align-items-end mb-2">
            <div class="col-sm-3 p-1">
                <div class="form-group">
                    <label>Contact Person</label>
                    <input class="form-control" type="text" name="shipping_contact_person[]" value="" required/>
                </div>
            </div>
            <div class="col-sm-3 p-1">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="shipping_email[]" value=""/>
                </div>
            </div>
            <div class="col-sm-2 p-1">
                <div class="form-group">
                    <label>Phone</label>
                    <input class="form-control" type="tel" name="shipping_phone[]" value=""/>
                </div>
            </div>
            <div class="col-sm-2 p-1">
                <div class="form-group">
                    <label>Department</label>
                    <input class="form-control" type="text" name="shipping_department[]" value=""/>
                </div>
            </div>
            <div class="col-sm-2 p-1 d-flex">
                <div class="form-group flex-grow-1">
                    <label>Designation</label>
					<div class="email2-group d-flex align-items-center">
						<input class="form-control" type="text" name="shipping_designation[]" value=""/>
						<button type="button" class="btn btn-danger btn-sm remove-shipping-contact">-</button>
					</div>
                </div>
            </div>
        </div>
    `;
    $('#shipping-contacts-container').append(newContact);
	$('input[type="tel"]').inputmask("(999) 999-9999");
});

$(document).on('click', '.remove-shipping-contact', function () {
    $(this).closest('.shipping-contact-group').remove();
});
</script>

