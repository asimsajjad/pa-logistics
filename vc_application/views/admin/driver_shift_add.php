<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Driver Shift <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/driver_shift');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				    <h3> Add Driver Shift</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
					<form method="post" action="<?php echo base_url('admin/driver_shift/add');?>" class="row">
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
                            <label for="contain"> Start Date</label>
                            <input readonly name="sdate" type="text" class="form-control datepicker" required>
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Start Time</label>
                            <select name="stime" class="form-control">
								<option value="00:00:00">00:00:00</option>
								<?php 
								$time = date('Y-m-d').' 00:00:00';
								for($i=0; $i < 47;$i++) {
									$currentTime = date('H:i:s',strtotime("+30 minutes",strtotime($time)));
									echo '<option value="'.$currentTime.'">'.$currentTime.'</option>';
									$time = $currentTime;
								} ?>
							</select>
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> End Date</label>
                            <input readonly name="edate" type="text" class="form-control datepicker" required>
                        </div>
                    </div> 
					
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> End Time</label>
                            <select name="etime" class="form-control">
								<option value="00:00:00">00:00:00</option>
								<?php 
								$time = date('Y-m-d').' 00:00:00';
								for($i=0; $i < 47;$i++) {
									$currentTime = date('H:i:s',strtotime("+30 minutes",strtotime($time)));
									echo '<option value="'.$currentTime.'">'.$currentTime.'</option>';
									$time = $currentTime;
								} ?>
							</select>
                        </div>
                    </div> 
					
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Start Latitude</label>
                            <input name="slatitude" type="text" class="form-control">
                        </div>
                     </div> 
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Start Longitude</label>
                            <input name="slongitude" type="text" class="form-control">
                        </div>
                    </div> 
					
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> End Latitude</label>
                            <input name="elatitude" type="text" class="form-control">
                        </div>
                     </div> 
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> End Longitude</label>
                            <input name="elongitude" type="text" class="form-control">
                        </div>
                    </div> 
					
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Status</label>
                            <select name="status" class="form-control" required>
								<option value="">Select Status</option>
								<option value="true">Start</option>
								<option value="closed">Closed</option>
							</select>
                        </div>
                    </div> 
 
					<div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" name="save" value="Add Driver Shift" class="btn btn-success"/>
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
    $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true});
    $(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
  });
  </script>