<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Carriers 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/trucking-companies');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			    <div class="container">

				    <h3> Update Carriers</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($truckingCompany)){
                  $key = $truckingCompany[0];
                  
                    ?>    
		           <form method="post" action="<?php echo base_url('admin/trucking-company/update/').$this->uri->segment(4);?>" enctype="multipart/form-data" class="row">
                      
						 <?php  echo validation_errors();?>
						 
                    <div class="col-sm-3">     
						<div class="form-group">
                            <label for="contain">Company</label>
                            <input class="form-control" type="text" name="company" value="<?php echo $key['company'] ;?>" required/>
                        </div>
					</div>	
					<div class="col-sm-3">     
						<div class="form-group">
                            <label for="contain">Phone</label>
                            <input class="form-control" type="tel" name="password" value="<?php echo $key['password'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-3">     
						<div class="form-group">
                            <label for="contain">Email</label>
                            <input class="form-control" type="email" name="email" value="<?php echo $key['email'] ;?>"/>
                        </div>
					</div>	
					<div id="email2-container" class="col-sm-3 col-md-3 col-xs-3">
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
					
					<div class="col-sm-3">
					   <label for="contain">#MC</label><br>
					   <input name="tc_mc" type="file" class="form-control"><br>
					   <?php  if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='tc_mc') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/trucking-company/remove-file/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/truckingCompanies/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?> 
                    </div>
					<div class="col-sm-3">
					   <label for="contain">#EIN</label><br>
					   <input name="tc_ein" type="file" class="form-control"><br>
					   <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='tc_ein') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/trucking-company/remove-file/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/truckingCompanies/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?> 
                    </div>
					<div class="col-sm-3">
					   <label for="contain">#Owner ID</label><br>
					   <input name="tc_ownerID" type="file" class="form-control"><br>
					   <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='tc_ownerID') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/trucking-company/remove-file/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/truckingCompanies/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?> 
                    </div>
					<div class="col-sm-3">
					   <label for="contain">#W9</label><br>
					   <input name="tc_w9" type="file" class="form-control"><br>
					   <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='tc_w9') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/trucking-company/remove-file/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/truckingCompanies/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?> 
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