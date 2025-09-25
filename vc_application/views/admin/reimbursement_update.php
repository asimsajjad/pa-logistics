<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Reimbursement 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/reimbursement');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">

				    <h3> Update Reimbursement</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($reimbursement)){
                  $key = $reimbursement[0];
                  
                    ?>    
					
					<?php  echo validation_errors();?>
						 <div class="clearfix"></div>
						 
		           <form method="post" class="row" action="<?php echo base_url('admin/reimbursement/update/').$this->uri->segment(4);?>" enctype="multipart/form-data">
                   
						<div class="col-sm-3">	     
						<div class="form-group">
                            <label for="contain"> Driver</label>
                            <select name="driver_id" class="form-control" required>
        						<option value="">Select Driver</option>
        						<?php 
        							if(!empty($drivers)){
        								foreach($drivers as $val){
        									echo '<option value="'.$val['id'].'"';
        									if($key['driver_id']==$val['id']) { echo ' selected '; }
        									echo '>'.$val['dname'].'</option>';
        								}
        							}
        						?>
        					</select>
                        </div>
					</div>
					<div class="col-sm-3">	
                        <div class="form-group">
                            <label for="contain"> Amount</label>
                            <input name="amount" type="number" class="form-control"  step="0.01" min="0" value="<?php echo $key['amount'];?>" required>
                        </div>
                     </div>
					<div class="col-sm-3">   
                        <div class="form-group">
                            <label for="contain"> Truck</label>
                            <input name="truck" type="text" class="form-control" value="<?php echo $key['truck'];?>" required>
                        </div>
                    </div>  
					<div class="col-sm-3">   
                        <div class="form-group">
                            <label for="contain"> Reimbursed</label>
                            <div class="custom-control custom-checkbox my-1 mr-sm-2" style="display: inline;color:#ff0047;"> &nbsp;
                                <input type="checkbox" class="custom-control-input" id="delivered" name="rembursCheck" value="1" <?php if($key['rembursCheck']=='1') { echo ' checked '; }  ?>>
                                <label class="custom-control-label" for="delivered">&nbsp;</label>
                            </div>
                            <input name="rembursDate" type="text" readonly class="form-control" value="<?php echo $key['rembursDate'];?>" required>
                        </div>
                    </div> 
				
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> Date</label>
                            <input readonly name="fdate" type="text" class="form-control datepicker"  value="<?php echo $key['fdate'];?>">
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Document</label>
                            <input name="document" type="file" class="form-control">
                        </div>
                    </div> 
                   
                    <div class="col-sm-5">
					   <label for="contain">&nbsp;</label><br>
					   <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='reimbursement') { 
										echo '<span class="doc-file">
										<a href="'.base_url().'admin/reimbursement/removeotherfile/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a>
										<a target="_blank" href="'.base_url('assets/reimbursement/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?>
                       </div>

					<div class="col-sm-12">    
                        <div class="form-group">
                            <label for="contain"> Notes</label>
                            <textarea name="notes" class="form-control"><?php echo $key['notes'];?></textarea>
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
 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
  });
  </script>	