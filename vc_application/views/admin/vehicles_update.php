<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Vehicle 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/vehicles');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">

				    <h3> Update Vehicle</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($vehicle)){
                  $key = $vehicle[0];
                  
                    ?>    
					
					<?php  echo validation_errors();?>
						 <div class="clearfix"></div>
						 
		           <form method="post" class="row" action="<?php echo base_url('admin/vehicle/update/').$this->uri->segment(4);?>" enctype="multipart/form-data">
                   
					<div class="col-sm-4">  
						<div class="form-group">
                            <label for="contain">Vehicle</label>
                            <input class="form-control" type="text" name="vname" value="<?php echo $key['vname'] ;?>" required/>
                        </div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Vehicle Number</label>
                            <input class="form-control" type="text" name="vnumber" value="<?php echo $key['vnumber'] ;?>" required/>
                        </div>
                    </div>
					<div class="col-sm-4">   
                        <div class="form-group">
                            <label for="contain"> License Plate</label>
                            <input name="license_plate" type="text" class="form-control" value="<?php echo $key['license_plate'] ;?>">
                        </div>
                    </div>
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> VIN</label>
                            <input name="vin" type="text" class="form-control" value="<?php echo $key['vin'] ;?>">
                        </div>
                    </div>  
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> Make</label>
                            <input name="vmake" type="text" class="form-control" value="<?php echo $key['vmake'] ;?>">
                        </div>
                    </div> 
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> Model</label>
                            <input name="vmodel" type="text" class="form-control" value="<?php echo $key['vmodel'] ;?>">
                        </div>
                    </div> 
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain"> Year</label>
                            <input name="vyear" type="number" class="form-control" value="<?php echo $key['vyear'] ;?>">
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Cab Card</label>
                            <input name="cabcard" type="file" class="form-control">
							<br>
							<?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='cabcard') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/dispatch/removedriverfile/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/driver/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
							?>  
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Insurance</label>
                            <input name="insurance" type="file" class="form-control">
							<br>
							<?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='insurance') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/dispatch/removedriverfile/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/driver/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
							?> 
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Unit Pictures</label>
                            <input name="unitpicture" type="file" class="form-control">
							<br>
							<?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='unitpicture') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/dispatch/removedriverfile/'.$doc['id'].'/'.$key['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/driver/').''.$doc['fileurl'].'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
							?> 
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
	