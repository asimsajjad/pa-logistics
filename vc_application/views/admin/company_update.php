<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Update Company 
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/companies');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
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
				<?php
					
					if(!empty($company)){
						$key = $company[0];
						
					?>    
					<form method="post" action="<?php echo base_url('admin/company/update/').$this->uri->segment(4);?>" enctype="multipart/form-data">
						
						<?php  echo validation_errors();?>
						<div class="row">
							<div class="col-sm-3 p-1">
								<div class="form-group">
									<label for="contain">Company</label>
									<input class="form-control" type="text" name="company" value="<?php echo $key['company'] ;?>" required/>
								</div>	
							</div>
							<div class="col-sm-5 p-1">
								<div class="form-group">
									<label for="contain">Company Address</label>
									<input class="form-control" type="text" name="address" value="<?php echo $key['address'] ;?>"/>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label for="contain">Company Phone</label>
									<input class="form-control" type="tel" name="phone" value="<?php echo $key['phone'] ;?>"/>
								</div>
							</div>
							<div class="col-sm-2 p-1">
								<div class="form-group">
									<label for="contain">Phone 2</label>
									<input class="form-control" type="tel" name="phone2" value="<?php echo $key['phone2'] ;?>"/>
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
								<?php 
								if (!empty($shipping_contacts)) :
									foreach ($shipping_contacts as $i => $sc): ?>
										<div class="row shipping-contact-group align-items-end mb-2">
											<input type="hidden" name="shipping_id[]" value="<?= $sc['id']; ?>">
											<div class="col-sm-3 p-1">
												<div class="form-group">
													<label>Shipping Contact Person</label>
													<input class="form-control" type="text" name="shipping_contact_person[]" value="<?= htmlspecialchars($sc['contact_person']); ?>"/>
												</div>
											</div>
											<div class="col-sm-3 p-1">
												<div class="form-group">
													<label>Email</label>
													<input class="form-control" type="email" name="shipping_email[]" value="<?= htmlspecialchars($sc['email']); ?>"/>
												</div>
											</div>
											<div class="col-sm-2 p-1">
												<div class="form-group">
													<label>Phone</label>
													<input class="form-control" type="tel" name="shipping_phone[]" value="<?= htmlspecialchars($sc['phone']); ?>"/>
												</div>
											</div>
											<div class="col-sm-2 p-1">
												<div class="form-group">
													<label>Department</label>
													<input class="form-control" type="text" name="shipping_department[]" value="<?= htmlspecialchars($sc['department']); ?>"/>
												</div>
											</div>
											<div class="col-sm-2 p-1 d-flex">
												<div class="form-group flex-grow-1">
													<label>Designation</label>
													<div class="email2-group d-flex align-items-center">
														<input class="form-control" type="text" name="shipping_designation[]" value="<?= htmlspecialchars($sc['designation']); ?>"/>
														<?php if ($i == 0): ?>
															<button type="button" class="btn btn-success btn-sm ms-2 add-shipping-contact">+</button>
														<?php else: ?>
															<button type="button" class="btn btn-danger btn-sm ms-2 remove-shipping-contact">-</button>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									<?php endforeach; 
								else: ?>
									<!-- Empty row if no shipping contacts exist -->
									<div class="row shipping-contact-group align-items-end mb-2">
										<div class="col-sm-3 p-1">
											<div class="form-group">
												<label>Contact Person</label>
												<input class="form-control" type="text" name="shipping_contactPerson[]" value=""/>
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
								<?php endif; ?>
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
									<input class="form-control" type="text" name="accounting_contact_person"value="<?php echo $key['accounting_contact_person'] ;?>"/>
								</div>
							</div>
							<div class="col-sm-4 p-1">
								<div class="form-group">
									<label for="contain">Email</label>
									<input class="form-control" type="email" name="accounting_email" value="<?php echo $key['accounting_email'] ;?>"/>
								</div>
							</div>
							<div class="col-sm-4 p-1">
								<div class="form-group">
									<label for="contain">Phone</label>
									<input class="form-control" type="tel" name="accounting_phone" value="<?php echo $key['accounting_phone'] ;?>"/>
								</div>
							</div>							
						</div>
						<div class="row">
							<div class="col-12">
								<div class="d-flex align-items-center">
									<hr class="flex-grow-1">
									<span class="px-3 text-muted fw-bold">Invoicing Emails</span>
									<hr class="flex-grow-1">
								</div>
							</div>
							<div class="col-sm-6 p-1">
								<div class="form-group">
									<label for="contain">Email</label>
									<input class="form-control" type="email" name="email" value="<?php echo $key['email'] ;?>"/>
								</div>
							</div>
							<div id="email2-container" class="col-sm-6 p-1">
								<label for="contain">Email 2</label>
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
							<div class="col-12">
								<div class="d-flex align-items-center">
									<hr class="flex-grow-1">
									<span class="px-3 text-muted fw-bold">Payment Terms / Status</span>
									<hr class="flex-grow-1">
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="contain">Payment Terms</label>
									<select name="paymenTerms" class="form-control paymenTerms">
										<option value="">Select Payment Terms</option>
										<option value="RTS" <?php if($key['paymenTerms']=='RTS'){ echo 'selected'; } ?>>RTS</option>
										<option value="Direct Bill" <?php if($key['paymenTerms']=='Direct Bill'){ echo 'selected'; } ?>>Direct Bill</option>
										<option value="Quick Pay" <?php if($key['paymenTerms']=='Quick Pay'){ echo 'selected'; } ?>>Quick Pay</option>
										<option value="Deleted" <?php if($key['paymenTerms']=='Deleted'){ echo 'selected'; } ?>>Deleted</option>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="contain">Payment Terms</label>
									<input name="payoutRate" type="text" class="form-control payoutRate" value="<?php echo $key['payoutRate'] ;?>">
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label for="contain">Days To Pay</label>
									<input name="dayToPay" type="number" min="0" max="121" class="form-control dayToPay" value="<?php echo $key['dayToPay'] ;?>">
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label for="contain" style="width: 129%;">Warehouse Customer</label>
									<select name="warehouseCustomer" class="form-control">
										<option value="">Select an option</option>	
										<option value="Yes" <?php if($key['warehouseCustomer']=='Yes'){ echo 'selected'; } ?>>Yes</option>
										<option value="No" <?php if($key['warehouseCustomer']=='No'){ echo 'selected'; } ?>>No</option>
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label for="contain" style="width: 129%;">Status</label>
									<select name="status" class="form-control">
										<option value="">Select Status</option>	
										<option value="Active" <?php if($key['status']=='Active'){ echo 'selected'; } ?>>Active</option>
										<option value="In-Active" <?php if($key['status']=='In-Active'){ echo 'selected'; } ?>>In-Active</option>
										<option value="Write-Off" <?php if($key['status']=='Write-Off'){ echo 'selected'; } ?>>Write-Off</option>
									</select>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-12">
								<div class="d-flex align-items-center">
									<hr class="flex-grow-1">
									<span class="px-3 text-muted fw-bold">Customer Quotes</span>
									<hr class="flex-grow-1">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group"> 
									<label style="z-index:0;" for="">Customer Quote</label> 
									<!-- <a data-cls=".d-quote" href="#" class="download-pdf">Download All</a> -->
									<input name="quote_d[]" type="file" class="form-control" multiple>
								</div>
							</div>
							<div class="col-sm-12" style="margin-top: -20px;">
								<?php 
									$quotefile = '';
									if(!empty($documents)) { 
										foreach($documents as $doc) {
											if($doc['type']=='quote') { 
												$dateDisplay = date("M d, Y", strtotime($doc['date']));
												$quotefile = 'yes';
												$pdfArray[] = array('customer--quote',$doc['fileurl']);
												echo '<div class="doc-file d-inline-block border rounded p-2 m-1 bg-light shadow-sm" style="min-width:200px;">';
												echo '  <div class="d-flex justify-content-between align-items-center mb-1">
															<a target="_blank" download href="'.base_url().'assets/customer/quote/'.$doc['fileurl'].'" class="me-2">
																<img src="/assets/images/download_icon.png" style="width:18px;">
															</a>
															<a href="'.base_url().'admin/company/removefile/'.$doc['id'].'/'.$key['id'].'" class="remove-file text-danger" style="font-size:14px;">x</a>
														</div>';
												echo '  <div>
															<a target="_blank" href="'.base_url('assets/customer/quote/').$doc['fileurl'].'?id='.rand(10,99).'">
																<i class="far fa-file" style="font-size: 22px;"></i>
																<span class="ms-1">'.$doc['fileurl'].'</span>
															</a>
														</div>';
												echo '  <div class="text-muted small mt-1">
															<i class="far fa-calendar-alt"></i> '.$dateDisplay.'
														</div>';
												echo '</div>';
											}
										}
									}
								?>
							</div>
						</div>
						<div class="form-group" style="margin-top: 10px;">
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
	jQuery(document).ready(function(){
		jQuery('.paymenTerms').change(function(){
			var valu = jQuery(this).val();
			var rate = '0';
			var days = '0';
			if(valu=='RTS'){ rate = '0.985'; days = '1'; }
			else if(valu=='Direct Bill'){ rate = '1.000';  days = '45'; }
			else if(valu=='Quick Pay'){ rate = '0.980';  days = '5'; }
			jQuery('.payoutRate').val(rate);
			jQuery('.dayToPay').val(days);
		});
		$('input[type="tel"]').inputmask("(999) 999-9999");
	});
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
                    <input class="form-control" type="text" name="shipping_contact_person[]" value=""/>
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

	$('.remove-file').click(function(e){
		e.preventDefault();
	    var href = $(this).attr('href');
	    // return confirm(\'Are you sure delete this file ?\')
		if(confirm('Are you sure delete this file ?')) {
			var card = $(this).closest('.doc-file');
    	    $(this).parent('span').hide();
		    $.ajax({
    			type: "GET",
    			url: href,
    			data: "",
    			success: function(response) { 
					card.fadeOut(300, function(){ $(this).remove(); });
				    $('.flashMsgCls').show();
    				setTimeout(function(){
                        $('.flashMsgCls').hide();
            	    }, 5000);
    			}
    		});
	    }
	});
</script>
<style>
	.fileDownload img{width: 20px;position: absolute;top:0;left: 0;z-index: 0;}
	.doc-file {display: inline-block;text-align: center;font-size: 14px;}
	.doc-file span {display: block;}
	.doc-file {display: inline-block;text-align: center;max-width: 145px;position: relative;}
	.doc-file .remove-file {position: absolute;right: 8px;top: 11px;padding: 3px;color: red;font-weight: bold;border: 1px solid red;line-height: 13px;}

</style>