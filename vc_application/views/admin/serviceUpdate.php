<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Service 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/services');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">

				    <h3> Update Service</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item');$this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($service)){
                  $key = $service[0];
                  
                    ?>    
					
					<?php  echo validation_errors();?>
						 <div class="clearfix"></div>
						 
		           <form method="post" class="row" action="<?php echo base_url('admin/service/update/').$this->uri->segment(4);?>" enctype="multipart/form-data">
                   
					<div class="col-sm-6">   
                        <div class="form-group">
                            <label for="contain"> Equipment</label>
                            <select name="equipment" class="form-control" required>
                                <option value="">Select Equipment</option>
                                <?php if($equipment) {
                                    foreach($equipment as $val){
                                        echo '<option value="'.$val['id'].'"';
                                        if($val['id'] == $key['equipment']) { echo ' selected '; }
                                        echo '>'.$val['trailer'].'</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>  
					<div class="col-sm-6">    
                        <div class="form-group">
                            <label for="contain">Repair</label>
                            <select name="repair" class="form-control" required>
                                <option value="">Select Repair</option>
                                <?php if($preServices) {
                                    foreach($preServices as $val){
                                        echo '<option value="'.$val['id'].'"';
                                        if($val['id'] == $key['repair']) { echo ' selected '; }
                                        echo '>'.$val['title'].'</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                    </div> 
                    
                    <div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Service Date</label>
                            <input readonly value="<?php echo $key['serviceDate'];?>" name="serviceDate" type="text" class="form-control datepicker">
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Next Service Date</label>
                            <input readonly value="<?php echo $key['nextServiceDate'];?>" name="nextServiceDate" type="text" class="form-control datepicker">
                        </div>
                    </div> 
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Cost</label>
                            <input name="coast" value="<?php echo $key['coast'];?>" type="number" class="form-control">
                        </div>
                    </div>
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Vendor</label>
                            <input name="vendor" type="text" value="<?php echo $key['vendor'];?>" class="form-control">
                        </div>
                    </div>
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Equipment Mileage</label>
                            <input name="mileage" type="text" value="<?php echo $key['mileage'];?>" class="form-control">
                        </div>
                    </div>
					<div class="col-sm-12">	
                        <div class="form-group">
                            <label for="contain"> Notes</label>
                            <textarea name="notes" class="form-control"><?php echo $key['notes'];?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Attach Invoice</label>
                            <input name="invoice" type="file" class="form-control">
                            <br>
                        </div>
                        <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='invoice') { 
										echo '<span class="doc-file">
										<a href="'.base_url().'admin/service/removeSingleDocument/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a>
										<a target="_blank" href="'.base_url('assets/service/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?>
                    </div> 
                    <div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Equipment Picture Before</label>
                            <input name="imageBefore" type="file" class="form-control"><br>
                        </div>
                        <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='imageBefore') { 
										echo '<span class="doc-file">
										<a href="'.base_url().'admin/service/removeSingleDocument/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a>
										<a target="_blank" href="'.base_url('assets/service/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?>
                    </div> 
                    <div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Equipment Picture After</label>
                            <input name="imageAfter" type="file" class="form-control"><br>
                        </div>
                        <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='imageAfter') { 
										echo '<span class="doc-file">
										<a href="'.base_url().'admin/service/removeSingleDocument/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a>
										<a target="_blank" href="'.base_url('assets/service/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
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
			
			
<style>
	form  label[for="contain"]{font-weight:bold;}
	.doc-file span {display: block;}
    .doc-file {display: inline-block;text-align: center;max-width: 145px;position: relative;}
.doc-file .remove-file {position: absolute;right: 0;top: -10px;padding: 3px;color: red;font-weight: bold;border: 1px solid red;line-height: 13px;}
	</style>	
 