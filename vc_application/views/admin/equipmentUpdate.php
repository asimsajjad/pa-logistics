<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Equipment 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/equipment');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">

				    <h3> Update Equipment</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($equipment)){
                  $key = $equipment[0];
                  
                    ?>    
					
					<?php  echo validation_errors();?>
						 <div class="clearfix"></div>
						 
		           <form method="post" class="row" action="<?php echo base_url('admin/equipment/update/').$this->uri->segment(4);?>" enctype="multipart/form-data">
                   
						<div class="col-sm-4">   
                        <div class="form-group">
                            <label for="contain"> Trailer #</label>
                            <!--input name="trailer" type="text" value="<?php echo $key['trailer'];?>" class="form-control" required-->
                            <select name="trailer" class="form-control" required>
                                <option value="">Select Vehicle</option>
                                <?php
                                if($vehicles){
                                    foreach($vehicles as $val){
                                        echo '<option value="'.$val['vname'].'"';
                                        if($key['trailer']==$val['vname']) { echo ' selected '; }
                                        echo '>'.$val['vname'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>  
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Ownership Status</label>
                            <input name="ownershipStatus" value="<?php echo $key['ownershipStatus'];?>" type="text" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">License Plate</label>
                            <input name="licensePlate" value="<?php echo $key['licensePlate'];?>" type="text" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain">Vin</label>
                            <input name="vVin" type="text" value="<?php echo $key['vVin'];?>" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain">Make</label>
                            <input name="vMake" type="text" value="<?php echo $key['vMake'];?>" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain">Model</label>
                            <input name="vModel" type="text" value="<?php echo $key['vModel'];?>" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain">Year</label>
                            <input name="vYear" type="number" value="<?php echo $key['vYear'];?>" class="form-control">
                        </div>
                    </div> 
                    
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Inspection Date</label>
                            <input readonly name="inspectioDate" type="text" value="<?php echo $key['inspectioDate'];?>" class="form-control datepicker">
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Inspection Exp. Date</label>
                            <input readonly name="inspectionExpDate" type="text" value="<?php echo $key['inspectionExpDate'];?>" class="form-control datepicker">
                        </div>
                    </div>  
                   
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> Document</label>
                            <input name="document" type="file" class="form-control">
                        </div>
                    </div> 
                    <div class="col-sm-12">
					   <label for="contain">&nbsp;</label><br>
					   <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='equipment') { 
										echo '<span class="doc-file">
										<a href="'.base_url().'admin/equipment/removeSingleDocument/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a>
										<a target="_blank" href="'.base_url('assets/equipment/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
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
 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });
  });
  </script>	