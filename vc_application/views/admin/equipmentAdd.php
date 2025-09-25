<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Equipment <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/equipment');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				    <h3> Add Equipment</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
					<form method="post" action="<?php echo base_url('admin/equipment/add');?>" class="row" enctype="multipart/form-data">
					 
					<div class="col-sm-4">   
                        <div class="form-group">
                            <label for="contain"> Trailer #</label>
                            <!--input name="trailer" type="text" class="form-control" required-->
                            <select name="trailer" class="form-control" required>
                                <option value="">Select Vehicle</option>
                                <?php
                                if($vehicles){
                                    foreach($vehicles as $val){
                                        echo '<option value="'.$val['vname'].'">'.$val['vname'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>  
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Ownership Status</label>
                            <input name="ownershipStatus" type="text" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">License Plate</label>
                            <input name="licensePlate" type="text" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain">Vin</label>
                            <input name="vVin" type="text" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain">Make</label>
                            <input name="vMake" type="text" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain">Model</label>
                            <input name="vModel" type="text" class="form-control">
                        </div>
                    </div>   
					<div class="col-sm-3">    
                        <div class="form-group">
                            <label for="contain">Year</label>
                            <input name="vYear" type="number" class="form-control">
                        </div>
                    </div> 
                    
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Inspection Date</label>
                            <input readonly name="inspectioDate" type="text" class="form-control datepicker">
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Inspection Exp. Date</label>
                            <input readonly name="inspectionExpDate" type="text" class="form-control datepicker">
                        </div>
                    </div>  
                     <div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Document</label>
                            <input name="document" type="file" class="form-control">
                        </div>
                    </div> 
                     
					<div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" name="save" value="Add Equipment" class="btn btn-success"/>
                        </div>
					</div>
					</form>
				</div>
			</div>

		</div>	
			
		</div>	
			
			
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