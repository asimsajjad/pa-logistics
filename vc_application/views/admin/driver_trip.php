 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
              <h3>Driver Trip Tracking</h3>  
              <div class="add_page" style="float: right;">
                   <a class="nav-link" title="Create Section" href="<?php echo  base_url().'admin/driver_trip/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
                   </a>  
                   </div>
            </div>
            
            <div class="card-bodys table_style">
                 
            <div class="d-block text-center">
                <form class="form form-inline" method="post" action="">
                    <input type="text" readonly placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 120px;" class="form-control datepicker"> &nbsp;
                    <input type="text"  style="width: 120px;" readonly placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker"> &nbsp;
					
					<?php 
					$week1 = date('Y-m').'-01,'.date('Y-m').'-08';
					$week2 = date('Y-m').'-09,'.date('Y-m').'-15';
					$week3 = date('Y-m').'-16,'.date('Y-m').'-23';
					$week4 = date('Y-m').'-24,'.date('Y-m-t').'';
					
					if($this->input->post('week')=='all') { $curernt_w = '0'; }
					elseif($this->input->post('week')==$week1) { $curernt_w = '1'; }
					elseif($this->input->post('week')==$week2) { $curernt_w = '2'; }
					elseif($this->input->post('week')==$week3) { $curernt_w = '3'; }
					elseif($this->input->post('week')==$week4) { $curernt_w = '4'; }
					elseif(date('d') < 9) { $curernt_w = '1';}
                    elseif(date('d') < 16) { $curernt_w = '2'; }
                    elseif(date('d') < 24) { $curernt_w = '3'; }
                    else { $curernt_w = '4'; }
        
					?>
					<select name="week" class="form-control">
						<option value="all" <?php if($curernt_w == '0') { echo 'selected'; } ?>>All Week</option>
						<option value="<?php echo $week1;?>" <?php if($curernt_w == '1') { echo 'selected'; } ?>>Week 1</option>
						<option value="<?php echo $week2;?>" <?php if($curernt_w == '2') { echo 'selected'; } ?>>Week 2</option>
						<option value="<?php echo $week3;?>" <?php if($curernt_w == '3') { echo 'selected'; } ?>>Week 3</option>
						<option value="<?php echo $week4;?>" <?php if($curernt_w == '4') { echo 'selected'; } ?>>Week 4</option>
					</select>
                         &nbsp;
                    <select name="driver" class="form-control">
						<option value="">Select Driver</option>
						<?php 
							if(!empty($drivers)){
								foreach($drivers as $val){
									echo '<option value="'.$val['id'].'"';
									if($this->input->post('driver')==$val['id']) { echo ' selected '; }
									echo '>'.$val['dname'].'</option>';
								}
							}
						?>
					</select>&nbsp;
                    <input type="submit" value="Search" name="search" class="btn btn-success pt-cta"> &nbsp; &nbsp; 
                     <input type="reset" value="Clear Filter" name="reset" class="btn btn-danger pt-cta">
                </form>
            </div>
			
              <div class="table-responsive pt-tbl-responsive pt-datatbl ">
                <table class="table " id="dataTable1" width="100%" cellspacing="0">
                  <thead>
                    <tr>
						  <th>PU Date</th>
						  <th>Trip 1</th>
						  <th>Trip 2</th>
						  <th>Trip 3</th>
						  <th>Trip 4</th>
						  <th>Rate</th>
						  <th>Start Time</th>
						  <th>End Time</th>
						  <th>Hours</th>
						  <th>Reimbursement</th>
						  <th>Deduction</th> 
                        <th>Action</th>
                    </tr> 
                  </thead>
                 
                  <tbody>
                    
            <?php
			$loca_array = array();
			if(!empty($locations)){ 
                foreach($locations as $val){ 
					if(!array_key_exists($val['id'], $loca_array)) { $loca_array[$val['id']] = $val['location']; } 
                }
            }
			$rate = $spend_amt = $deduction = 0;
                  if(!empty($driver_trips)){
                      $n=1;  
                  foreach($driver_trips as $key) {
					  $trip1pu = $trip1do = $trip2pu = $trip2do = $trip3pu = $trip3do = $trip4pu = $trip4do = '';
					 
					  $rate = $rate + $key['rate'];
					  $spend_amt = $spend_amt + $key['spend_amt'];
					  $deduction = $deduction + $key['deduction'];
					  
			if($key['trip1']!='') {
				$trip_data = explode(',',$key['trip1']);
				if(array_key_exists($trip_data[1], $loca_array)) { $trip1pu = $loca_array[$trip_data[1]]; } 
				else { $trip1pu = $trip_data[0]; }
				if(array_key_exists($trip_data[2], $loca_array)) { $trip1do = $loca_array[$trip_data[2]]; } 
				else { $trip1do = $trip_data[1]; }
			}
			if($key['trip2']!='') {
				$trip_data2 = explode(',',$key['trip2']);
				if(array_key_exists($trip_data2[1], $loca_array)) { $trip2pu = $loca_array[$trip_data2[1]]; } 
				else { $trip2pu = $trip_data2[0]; }
				if(array_key_exists($trip_data2[2], $loca_array)) { $trip2do = $loca_array[$trip_data2[2]]; } 
				else { $trip2do = $trip_data2[1]; }
			}
			if($key['trip3']!='') {
				$trip_data3 = explode(',',$key['trip3']);
				if(array_key_exists($trip_data3[1], $loca_array)) { $trip3pu = $loca_array[$trip_data3[1]]; } 
				else { $trip3pu = $trip_data3[0]; }
				if(array_key_exists($trip_data3[2], $loca_array)) { $trip3do = $loca_array[$trip_data3[2]]; } 
				else { $trip3do = $trip_data3[1]; }
			}
			if($key['trip4']!='') {
				$trip_data4 = explode(',',$key['trip4']);
				if(array_key_exists($trip_data4[1], $loca_array)) { $trip4pu = $loca_array[$trip_data4[1]]; } 
				else { $trip4pu = $trip_data4[0]; }
				if(array_key_exists($trip_data4[2], $loca_array)) { $trip4do = $loca_array[$trip_data4[2]]; } 
				else { $trip4do = $trip_data4[1]; }
			}
                    ?>
                    <tr>
            <td><a href="<?php echo base_url().'admin/driver_trip/update/'.$key['id'];?>"><?php echo date('m-d-Y',strtotime($key['tripdate']));?></a></td>
            <td><a href="<?php echo base_url().'admin/driver_trip/update/'.$key['id'];?>"><?php echo $trip1pu.' -> '.$trip1do.''; ?></a></td>  
            <td><?php if($trip2pu!='') { echo $trip2pu.' -> '.$trip2do.''; } ?></td>  
            <td><?php if($trip3pu!='') { echo $trip3pu.' -> '.$trip3do.''; } ?></td>  
            <td><?php if($trip4pu!='') { echo $trip4pu.' -> '.$trip4do.''; }  ?></td>  
          
            <td>$<?php echo $key['rate'];?></td> 
            <td><?php echo $key['stime'];?></td> 
            <td><?php echo $key['etime'];?></td> 
            <td><?php echo $key['total_hour'];?></td>  
            <td>$<?php echo $key['spend_amt'];?></td>  
            <td>$<?php echo $key['deduction'];?></td> 
            <td>
                <a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/driver_trip/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                      <a style="color:#ff0000;"  href="<?php echo base_url().'admin/driver_trip/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this menu ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a>
                      </td>
                      </tr> 
		  
                    <?php
                    $n++;
                    }
                    }


                  ?>

               
                   <tr>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td><strong>Total</strong></td>
						  <td><strong><?php echo '$'.$rate; ?></strong></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td><strong><?php echo '$'.$spend_amt; ?></strong></td>
						  <td><strong><?php echo '$'.$deduction; ?></strong></td>
						  <td><strong>Total Pay<br>$<?php echo (($rate + $spend_amt) - $deduction) ; ?></strong></td> 
                    </tr>
				
                  </tbody>
                </table>
              </div>
         
          </div>

        </div>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,
      changeYear: true
    });
    $(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
  } );
  </script>
  <style>
      form.form {margin-bottom:10px;}
  </style>