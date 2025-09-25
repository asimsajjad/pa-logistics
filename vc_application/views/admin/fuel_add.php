<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Fuel <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/fuel');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				    <h3> Add Fuel</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
					<form method="post" action="<?php echo base_url('admin/fuel/add');?>" class="row" enctype="multipart/form-data">
					<div class="col-sm-4">	     
						<div class="form-group">
                            <label for="contain"> Driver</label>
                            <select name="driver_id" class="form-control" required>
        						<option value="">Select Driver</option>
        						<?php 
        							if(!empty($drivers)){
        								foreach($drivers as $val){
        									echo '<option value="'.$val['id'].'"';
        									if($this->input->post('driver_id')==$val['id']) { echo ' selected '; }
        									echo '>'.$val['dname'].'</option>';
        								}
        							}
        						?>
        					</select>
                        </div>
					</div>
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Amount</label>
                            <input name="amount" type="number" class="form-control" required>
                        </div>
                     </div>
					<div class="col-sm-4">   
                        <div class="form-group">
                            <label for="contain"> Truck</label>
                            <input name="truck" type="text" class="form-control" required>
                        </div>
                    </div>  
				
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Date</label>
                            <input readonly name="fdate" type="text" class="form-control datepicker">
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
                            <label for="contain"> Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </div> 
					<div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" name="save" value="Add Fuel" class="btn btn-success"/>
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
    $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
  });
  </script>