<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Service <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/services');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				    <h3> Add Service</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
					<form method="post" action="<?php echo base_url('admin/service/add');?>" class="row" enctype="multipart/form-data">
					 
					<div class="col-sm-6">   
                        <div class="form-group">
                            <label for="contain"> Equipment</label>
                            <select name="equipment" class="form-control" required>
                                <option value="">Select Equipment</option>
                                <?php if($equipment) {
                                    foreach($equipment as $val){
                                        echo '<option value="'.$val['id'].'">'.$val['trailer'].'</option>';
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
                                        echo '<option value="'.$val['id'].'">'.$val['title'].'</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                    </div> 
                    
                    <div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Service Date</label>
                            <input readonly name="serviceDate" type="text" class="form-control datepicker">
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain">Next Service Date</label>
                            <input readonly name="nextServiceDate" type="text" class="form-control datepicker">
                        </div>
                    </div> 
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Coast</label>
                            <input name="coast" type="number" class="form-control">
                        </div>
                    </div>
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Vendor</label>
                            <input name="vendor" type="text" class="form-control">
                        </div>
                    </div>
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Equipment Mileage</label>
                            <input name="mileage" type="text" class="form-control">
                        </div>
                    </div>
					<div class="col-sm-12">	
                        <div class="form-group">
                            <label for="contain"> Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </div>
                    
                    <div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Attach Invoice</label>
                            <input name="invoice" type="file" class="form-control">
                        </div>
                    </div> 
                    <div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Equipment Picture Before</label>
                            <input name="imageBefore" type="file" class="form-control">
                        </div>
                    </div> 
                    <div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Equipment Picture After</label>
                            <input name="imageAfter" type="file" class="form-control">
                        </div>
                    </div> 
				 
					<div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" name="save" value="Add Service" class="btn btn-success"/>
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
  