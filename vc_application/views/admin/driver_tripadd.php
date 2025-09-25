<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Driver Trip Tracking <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/driver_trip');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				    <h3> Add Driver Trip Tracking</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
		  
						 <?php  echo validation_errors();?>
					<form method="post" action="<?php echo base_url('admin/driver_trip/add/');?>" class="row">
					
					<div class="col-sm-2">
						<div class="form-group">
                            <label for="contain">Pickup Date</label>
                            <input name="pdate" type="text" value="<?php //echo $key['tripdate'] ;?>" class="form-control datepicker" required readonly>
                        </div> 
                    </div>  
					<div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Driver</label>
                            <select name="driver" class="form-control">
                            <?php
                                if(!empty($drivers)){
                                    foreach($drivers as $val){
                                      if($val['status']=='Active') {
                                        echo '<option value="'.$val['id'].'">'.$val['dname'].'</option>';
                                      }
                                    }
                                }
                                ?>
                            </select>
                        </div> 
                    </div> 
					
					<div class="clearfix" style="width:100%;"></div>
					
					<div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Trip1 Pick Up</label>
                            <input name="trip1pu" type="text" value="<?php //echo $trip1pu ;?>" class="form-control">
                        </div> 
					  </div>
					  <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Trip2 Pick Up</label>
                            <input name="trip2pu" type="text" value="<?php //echo $trip2pu ;?>" class="form-control">
                        </div> 
					  </div>
					  <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Trip3 Pick Up</label>
                            <input name="trip3pu" type="text" value="<?php //echo $trip3pu ;?>" class="form-control">
                        </div> 
					  </div>
					  <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Trip4 Pick Up</label>
                            <input name="trip4pu" type="text" value="<?php //echo $trip4pu ;?>" class="form-control">
                        </div> 
					  </div>
					  
					  <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Trip1 Drop Off</label>
                            <input name="trip1do" type="text" value="<?php //echo $trip1do ;?>" class="form-control">
                        </div> 
					  </div> 
					  <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Trip2 Drop Off</label>
                            <input name="trip2do" type="text" value="<?php //echo $trip2do ;?>" class="form-control">
                        </div> 
					  </div>
					  <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Trip3 Drop Off</label>
                            <input name="trip3do" type="text" value="<?php //echo $trip3do ;?>" class="form-control">
                        </div> 
					  </div>
					  <div class="col-sm-3">
						<div class="form-group">
                            <label for="contain">Trip4 Drop Off</label>
                            <input name="trip4do" type="text" value="<?php //echo $trip4do ;?>" class="form-control">
                        </div> 
					  </div>
					  
					   
					<div class="col-sm-2 hide d-none">
						<div class="form-group">
                            <label for="contain">Rate</label>
                            <input id="rate_input" name="rate" type="number" value="<?php //echo $key['rate']; ?>" class="form-control tpay" >
                        </div> 
                    </div>  
					 
                    <div class="col-sm-4">    
						<div class="form-group">
                            <label for="contain">Start Time</label>
                            <input name="stime" value="<?php //echo $key['stime'];?>" type="text" class="form-control" required>
                        </div> 
                    </div>    
                    <div class="col-sm-4">    
						<div class="form-group">
                            <label for="contain">End Time</label>
                            <input name="etime" type="text" value="<?php //echo $key['etime'];?>" class="form-control">
                        </div> 
                    </div>    
                    <div class="col-sm-4">    
						<div class="form-group">
                            <label for="contain">Total Hour</label>
                            <input name="total_hour" type="number" value="<?php //echo $key['total_hour'];?>" class="form-control tpay" id="total_hour_input">
                        </div> 
                     </div>    
					 <div class="col-sm-2">    
						<div class="form-group">
                            <label for="contain">Reinbursement</label>
                            <input name="spend_amt" id="spend_amt_input" type="number" value="<?php //echo $key['spend_amt'];?>" class="form-control tpay" >
                        </div> 
                     </div>    
					 <div class="col-sm-10">    
						<div class="form-group">
                            <label for="contain">Reinbursement Description</label>
                            <input name="spendamt_txt" type="text" value="<?php //echo $key['spendamt_txt'];?>" class="form-control" >
                        </div> 
                     </div>  
					  <div class="col-sm-2">    
						<div class="form-group">
                            <label for="contain">Deduction</label>
                            <input name="deduction" id="deduction_input" type="number" value="<?php //echo $key['deduction'];?>" class="form-control tpay" >
                        </div> 
                     </div>    
					 <div class="col-sm-10">    
						<div class="form-group">
                            <label for="contain">Deduction Description</label>
                            <input name="deduction_txt" type="text" value="<?php //echo $key['deduction_txt'];?>" class="form-control" >
                        </div> 
                     </div>  
					 
                    <div class="col-sm-4">    
						<div class="form-group">
                            <label for="contain">Total Pay</label>
                            <input name="total_amt" id="total_amt_input" type="number" value="<?php //echo $key['total_amt'];?>" class="form-control" >
                        </div> 
                     </div>     
                      
                     <div class="col-sm-4">    
						<div class="form-group">
                            <label for="contain">Status</label>
                            <input name="status" type="text" value="<?php //echo $key['status'];?>" class="form-control" >
                        </div> 
                     </div> 
                     <div class="col-sm-12">    
						<div class="form-group">
                            <label for="contain">Notes</label>
                            <textarea name="notes" class="form-control"><?php //echo $key['notes'];?></textarea>
                        </div> 
                     </div> 
                    <div class="col-sm-12"> 
                        <div class="form-group">
                            <input type="submit" name="save" value="Update" class="btn btn-success"/>
                        </div>
					</div>
					</form>
				  
				</div>
			</div>

		</div>	
			
		</div>	
		
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
  <!--script src="https://code.jquery.com/jquery-1.12.4.js"></script-->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  
<script>
jQuery(document).ready(function(){
	jQuery('.tpay').keyup(function(){
		var hour = jQuery('#total_hour_input').val();
		var rate = jQuery('#rate_input').val();
		var spendamt = jQuery('#spend_amt_input').val();
		var deduction = jQuery('#deduction_input').val();
		if(hour > 0) {
			if(hour < 7) { rate = 150; }
			else if(hour < 13) { rate = 250; }
			else {
				var extra = parseFloat(hour) - 12;
				rate = parseFloat(rate) + (parseFloat(extra) * 20);
			}
			//var tpay = parseFloat(rate) + parseFloat(spendamt) - parseFloat(deduction);
			var tpay = parseFloat(rate);
			jQuery('#total_amt_input').val(tpay);
		}
	});
	jQuery('.tpay').click(function(){
		var hour = jQuery('#total_hour_input').val();
		var rate = jQuery('#rate_input').val();
		var spendamt = jQuery('#spend_amt_input').val();
		var deduction = jQuery('#deduction_input').val();
		if(hour > 0) {
			if(hour < 7) { rate = 150; }
			else if(hour < 13) { rate = 250; }
			else {
				var extra = parseFloat(hour) - 12;
				rate = parseFloat(rate) + (parseFloat(extra) * 20);
			}
			//var tpay = parseFloat(rate) + parseFloat(spendamt) - parseFloat(deduction);
			var tpay = parseFloat(rate);
			jQuery('#total_amt_input').val(tpay);
		}
	});
	jQuery( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,
      changeYear: true
    });
    $(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
});
</script>			





			