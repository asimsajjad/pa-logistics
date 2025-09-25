<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Pre Made Trip <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/pre_made_trips');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			    <div class="container">
				    <h3> Add Pre Made Trip</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
		   
		  
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
					<form class="row" method="post" action="<?php echo base_url('admin/pre_made_trips/add/');?>">
						 
						 
					<div class="col-sm-12">
						<div class="form-group">
                            <label for="contain">Trip Title</label> 
								<input name="pname" type="text" class="form-control" value="<?php if($this->input->post('pname')!='') { echo $this->input->post('pname'); } ?>"> 
                        </div>
                    </div>
                    
                    <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Pickup Time</label> 
								<input name="ptime" type="text" class="form-control" value=""> 
                        </div>
                    </div>
                    <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Pickup Address</label> 
								<input name="paddress" type="text" class="form-control" value=""> 
                        </div>
                    </div>
					<div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Pickup City</label>
                            <select class="form-control" name="pcity" required>
                                <option value="">Select City</option>
                                <?php 
                                if(!empty($cities)){
                                    foreach($cities as $val){
                                        echo '<option value="'.$val['city'].'"';
										if($this->input->post('pcity')==$val['city']) { echo ' selected="selected" '; }
                                        echo '>'.$val['city'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    
					<div class="col-sm-3">	     
						<div class="form-group">
                            <label for="contain">Pickup Location</label>
                            <select class="form-control" name="plocation" required>
                                <option value="">Select Location</option>
                                <?php 
                                if(!empty($locations)){
                                    foreach($locations as $val){
                                        echo '<option value="'.$val['location'].'"';
                                        if($this->input->post('plocation')==$val['location']) { echo ' selected="selected" '; }
                                        echo '>'.$val['location'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Drop Off Time</label> 
								<input name="dtime" type="text" class="form-control" value=""> 
                        </div>
                    </div>
                    <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Drop Off Address</label> 
								<input name="daddress" type="text" class="form-control" value=""> 
                        </div>
                    </div>
                    <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Drop Off City</label>
                            <select class="form-control" name="dcity" required>
                                <option value="">Select City</option>
                                <?php 
                                if(!empty($cities)){
                                    foreach($cities as $val){
                                        echo '<option value="'.$val['city'].'"';
                                        if($this->input->post('dcity')==$val['city']) { echo ' selected="selected" '; }
                                        echo '>'.$val['city'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
					<div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Drop Off Location</label>
                            <select class="form-control" name="dlocation" required>
                                <option value="">Select Location</option>
                                <?php 
                                if(!empty($locations)){
                                    foreach($locations as $val){
                                        echo '<option value="'.$val['location'].'"';
                                        if($this->input->post('dlocation')==$val['location']) { echo ' selected="selected" '; }
                                        echo '>'.$val['location'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                            
                        </div>
                    </div>
                    
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Rate</label>
                            <div class="input-group mb-2">
								<div class="input-group-prepend">
								  <div class="input-group-text">$</div>
								</div>
								<input name="rate" step="0.01" type="number" min="0" class="form-control" value="<?php if($this->input->post('rate')!='') { echo $this->input->post('rate'); } ?>">
							  </div> 
                            
                        </div>
                    </div>
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">PA Rate</label>
                            <div class="input-group mb-2">
								<div class="input-group-prepend">
								  <div class="input-group-text">$</div>
								</div>
								<input name="parate" step="0.01" type="number" class="form-control" value="<?php if($this->input->post('parate')!='') { echo $this->input->post('parate'); } ?>">
							  </div> 
                            
                        </div>
                    </div>
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Company</label>
                            <select class="form-control" name="company" required>
                                <option value="">Select Company</option>
                                <?php 
                                if(!empty($companies)){
                                    foreach($companies as $val){
                                        echo '<option value="'.$val['company'].'"';
                                        if($this->input->post('company')==$val['company']) { echo ' selected="selected" '; }
                                        echo '>'.$val['company'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
					
                    <div class="col-sm-12"> 
                        <div class="form-group">
                            <input type="submit" name="save" value="Add Pre Made Trip" class="btn btn-success"/>
                        </div>
                    </div>
					</form>
				</div>
			</div>

		</div>	
			
		</div>	
			
	 
   <style>
      .custom-control-label::before {width: 20px;height: 20px;}
      .custom-control-label::after {width: 20px;height: 20px;}
      .doc-file {display: inline-block;text-align: center;}
    .doc-file span {display: block;}
  </style>
  