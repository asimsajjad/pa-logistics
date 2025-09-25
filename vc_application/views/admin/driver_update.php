<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Driver 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/drivers');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			    <div class="container">

				    <h3> Update Driver</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($driver)){
                  $key = $driver[0];
                  
                    ?>    
		           <form method="post" action="<?php echo base_url('admin/driver/update/').$this->uri->segment(4);?>" enctype="multipart/form-data" class="row">
                      
						 <?php  echo validation_errors();?>
                    <div class="col-sm-4">     
						<div class="form-group">
                            <label for="contain">Driver</label>
                            <input class="form-control" type="text" name="dname" value="<?php echo $key['dname'] ;?>" required/>
                        </div>
					</div>	
					<div class="col-sm-2">     
						<div class="form-group">
                            <label for="contain">Driver Code</label>
                            <input class="form-control" type="text" name="dcode" value="<?php echo $key['dcode'] ;?>" required/>
                        </div>
					</div>	
					<div class="col-sm-3">     
						<div class="form-group">
                            <label for="contain">SSN</label>
                            <input class="form-control" type="text" name="ssn" value="<?php echo $key['ssn'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-3">     
						<div class="form-group">
                            <label for="contain">Start Date</label>
                            <input class="form-control datepicker" type="text" name="sdate" value="<?php echo $key['sdate'] ;?>"/>
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
                            <label for="contain">Date Of Birth</label>
                            <input class="form-control datepicker" type="text" name="dob" value="<?php echo $key['dob'] ;?>"/>
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
                            <label for="contain">Deposit Account</label>
                            <input class="form-control" type="text" name="account_no" value="<?php echo $key['account_no'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-4"> 
						<div class="form-group">
                            <label for="contain">Deposit Routing</label>
                            <input class="form-control" type="text" name="routing_no" value="<?php echo $key['routing_no'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-4"> 
						<div class="form-group">
                            <label for="contain">Deposit Bank</label>
                            <input class="form-control" type="text" name="bank" value="<?php echo $key['bank'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-10"> 
						<div class="form-group">
                            <label for="contain">Address</label>
                            <input class="form-control" type="text" name="address" value="<?php echo $key['address'] ;?>"/>
                        </div>
                    </div>
					<div class="col-sm-2"> 
						<div class="form-group">
                            <label for="contain">Status</label>
                            <select class="form-control" name="status">
                                <option value="Active">Active</option>
                                <option <?php if($key['status']=='Inactive') { echo 'selected'; } ?> value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
					<div class="col-sm-3">     
                        <div class="form-group">
                            <label for="contain">Drivers license</label>
                            <input name="license" type="file" class="form-control"> 
                        </div>
                    </div>
					<div class="col-sm-3"> 
						<div class="form-group">
                            <label for="contain">License No.</label>
                            <input class="form-control" type="text" name="license_no" value="<?php echo $key['license_no'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-3"> 
						<div class="form-group">
                            <label for="contain">License Issue Date</label>
                            <input class="form-control datepicker" type="text" name="lsdate" value="<?php echo $key['lsdate'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-3"> 
						<div class="form-group">
                            <label for="contain">License Expiration Date</label>
                            <input class="form-control datepicker" type="text" name="ledate" value="<?php echo $key['ledate'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-12">
					   <label for="contain">Drivers license Documents</label><br>
					   <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='license') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/dispatch/removedriverfile/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/driver/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?> 
                    </div>
					   
					   
					<div class="col-sm-4">     
                        <div class="form-group">
                            <label for="contain">Medical Card</label>
                            <input name="medical_card" type="file" class="form-control">
                        </div>
                    </div>
					<div class="col-sm-3"> 
						<div class="form-group">
                            <label for="contain">Medical Card Expiration Date</label>
                            <input class="form-control datepicker" type="text" name="medate" value="<?php echo $key['medate'] ;?>"/>
                        </div>
					</div>
					<div class="col-sm-12">
					   <label for="contain">Medical Card Documents</label><br>
					 
						<?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='medical_card') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/dispatch/removedriverfile/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/driver/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?>
                    </div>
					   
					<div class="col-sm-12">     
                     <div class="row">
                      <div class="col-sm-4">
                        <div class="form-group">
                            <label for="contain">Driving Records</label>
                            <input name="driving_record" type="file" class="form-control">
                        </div>
					   </div>
					   <div class="col-sm-8">
					   <label for="contain">&nbsp;</label><br> 
						<?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='driving_record') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/dispatch/removedriverfile/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/driver/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?>
                       </div>
					  </div>
                    </div>
					
					<div class="col-sm-12">     
                     <div class="row">
                      <div class="col-sm-4">
                        <div class="form-group">
                            <label for="contain">Onboarding Documents</label>
                            <input name="onboarding" type="file" class="form-control">
                        </div>
					   </div>
					   <div class="col-sm-8">
					   <label for="contain">&nbsp;</label><br>
					   <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='onboarding') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/dispatch/removedriverfile/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/driver/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?>
                       </div>
					  </div>
                    </div>
					
					
					<div class="col-sm-12"> 
						<div class="form-group">
                            <label for="contain">Notes</label>
                            <textarea class="form-control" name="notes"><?php echo $key['notes'] ;?></textarea>
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
			
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="<?php echo base_url('assets/js/jquery.inputmask.min.js'); ?>"></script>

  <!--script src="/assets/bootstrap-select.js"></script>
<link href="/assets/bootstrap-select.css" rel="stylesheet" /-->

  <script>
  $( function() {
    $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,
      changeYear: true}); 
  } );
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