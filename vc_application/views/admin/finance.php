 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
   			<h3>Finances </h3>
              
              <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/finance/multiple_view');?>" class="nav-link"><input type="button" name="Back" value="Multiple View" class="btn btn-success btn-sm pt-cta"/></a>
                     </div> 
              </div>
            <div class="card-bodys table_style">
                
                <div class="d-block text-center">
                <form class="form form-inline" method="post" action="">
                    <input type="text" placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 120px;" class="form-control datepicker"> &nbsp;
                    <input type="text"  style="width: 120px;" placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker"> &nbsp;
					
					<?php 
					if($this->input->post('sdate')) { 
						$sdate = $this->input->post('sdate');
						$lastDay = date('t',strtotime($sdate));
					} else {
						$lastDay = date('t');
					}
					$week1 = '-01,-08';
					$week2 = '-09,-15';
					$week3 = '-16,-23';
					$week4 = '-24,'.$lastDay.'';
					$week5 = '-01,-15';
					$week6 = '-01,-23';
					$week7 = '-09,-23';
					$week8 = '-09,'.$lastDay.'';
					$week9 = '-16,'.$lastDay.'';
					$week10 = '-01,'.$lastDay.'';
					/*
					$week1 = date('Y-m').'-01,'.date('Y-m').'-08';
					$week2 = date('Y-m').'-09,'.date('Y-m').'-15';
					$week3 = date('Y-m').'-16,'.date('Y-m').'-23';
					$week4 = date('Y-m').'-24,'.date('Y-m-t').'';
					$week5 = date('Y-m').'-01,'.date('Y-m').'-15';
					$week6 = date('Y-m').'-01,'.date('Y-m').'-23';
					$week7 = date('Y-m').'-09,'.date('Y-m').'-23';
					$week8 = date('Y-m').'-09,'.date('Y-m-t').'';
					$week9 = date('Y-m').'-16,'.date('Y-m-t').'';
					$week10 = date('Y-m').'-01,'.date('Y-m-t').'';
					*/
					
					if($this->input->post('week')=='all' || $this->input->post('month')!='') { $curernt_w = '0'; }
					elseif($this->input->post('week')==$week1) { $curernt_w = '1'; }
					elseif($this->input->post('week')==$week2) { $curernt_w = '2'; }
					elseif($this->input->post('week')==$week3) { $curernt_w = '3'; }
					elseif($this->input->post('week')==$week4) { $curernt_w = '4'; }
					elseif($this->input->post('week')==$week5) { $curernt_w = '5'; }
					elseif($this->input->post('week')==$week6) { $curernt_w = '6'; }
					elseif($this->input->post('week')==$week7) { $curernt_w = '7'; }
					elseif($this->input->post('week')==$week8) { $curernt_w = '8'; }
					elseif($this->input->post('week')==$week9) { $curernt_w = '9'; }
					elseif($this->input->post('week')==$week10) { $curernt_w = '10'; }
					/*elseif(date('d') < 9) { $curernt_w = '1';}
                    elseif(date('d') < 16) { $curernt_w = '2'; }
                    elseif(date('d') < 24) { $curernt_w = '3'; }
                    else { $curernt_w = '4'; }*/
                    else { $curernt_w = 'all'; }
					?>
					<select name="week" class="form-control">
						<option value="all" <?php if($curernt_w == '0') { echo 'selected'; } ?>>All Week</option>
						<option value="<?php echo $week1;?>" <?php if($curernt_w == '1') { echo 'selected'; } ?>>Week 1</option>
						<option value="<?php echo $week2;?>" <?php if($curernt_w == '2') { echo 'selected'; } ?>>Week 2</option>
						<option value="<?php echo $week3;?>" <?php if($curernt_w == '3') { echo 'selected'; } ?>>Week 3</option>
						<option value="<?php echo $week4;?>" <?php if($curernt_w == '4') { echo 'selected'; } ?>>Week 4</option>
						<!--option value="<?php echo $week5;?>" <?php if($curernt_w == '5') { echo 'selected'; } ?>>Week 1 & Week 2</option>
						<option value="<?php echo $week6;?>" <?php if($curernt_w == '6') { echo 'selected'; } ?>>Week 1 to Week 3</option>
						<option value="<?php echo $week7;?>" <?php if($curernt_w == '7') { echo 'selected'; } ?>>Week 2 & Week 3</option>
						<option value="<?php echo $week8;?>" <?php if($curernt_w == '8') { echo 'selected'; } ?>>Week 2 to Week 4</option>
						<option value="<?php echo $week9;?>" <?php if($curernt_w == '9') { echo 'selected'; } ?>>Week 3 & Week 4</option>
						<option value="<?php echo $week10;?>" <?php if($curernt_w == '10') { echo 'selected'; } ?>>All 4 Week</option-->
					</select>&nbsp;
					
					<?php 
					$month1 = date('Y').'-01-01,'.date('Y').'-01-31';
					$month2 = date('Y').'-02-01,'.date('Y').'-02-31';
					$month3 = date('Y').'-03-01,'.date('Y').'-03-31';
					$month4 = date('Y').'-04-01,'.date('Y').'-04-31';
					$month5 = date('Y').'-05-01,'.date('Y').'-05-31';
					$month6 = date('Y').'-06-01,'.date('Y').'-06-31';
					$month7 = date('Y').'-07-01,'.date('Y').'-07-31';
					$month8 = date('Y').'-08-01,'.date('Y').'-08-31';
					$month9 = date('Y').'-09-01,'.date('Y').'-09-31';
					$month10 = date('Y').'-10-01,'.date('Y').'-10-31';
					$month11 = date('Y').'-11-01,'.date('Y').'-11-31';
					$month12 = date('Y').'-12-01,'.date('Y').'-12-31';
					?>
                    <select name="month" class="form-control">
						<option value="">Select Month</option>
						<option value="<?php echo $month1;?>" <?php if($this->input->post('month')==$month1) { echo ' selected '; } ?>>January</option>
						<option value="<?php echo $month2;?>" <?php if($this->input->post('month')==$month2) { echo ' selected '; } ?>>February</option>
						<option value="<?php echo $month3;?>" <?php if($this->input->post('month')==$month3) { echo ' selected '; } ?>>March</option>
						<option value="<?php echo $month4;?>" <?php if($this->input->post('month')==$month4) { echo ' selected '; } ?>>April</option>
						<option value="<?php echo $month5;?>" <?php if($this->input->post('month')==$month5) { echo ' selected '; } ?>>May</option>
						<option value="<?php echo $month6;?>" <?php if($this->input->post('month')==$month6) { echo ' selected '; } ?>>June</option>
						<option value="<?php echo $month7;?>" <?php if($this->input->post('month')==$month7) { echo ' selected '; } ?>>July</option>
						<option value="<?php echo $month8;?>" <?php if($this->input->post('month')==$month8) { echo ' selected '; } ?>>August</option>
						<option value="<?php echo $month9;?>" <?php if($this->input->post('month')==$month9) { echo ' selected '; } ?>>September</option>
						<option value="<?php echo $month10;?>" <?php if($this->input->post('month')==$month10) { echo ' selected '; } ?>>October</option>
						<option value="<?php echo $month11;?>" <?php if($this->input->post('month')==$month11) { echo ' selected '; } ?>>November</option>
						<option value="<?php echo $month12;?>" <?php if($this->input->post('month')==$month12) { echo ' selected '; } ?>>December</option>
					</select> &nbsp;
                    <select name="unit" class="form-control">
						<option value="">Select Unit</option>
						<?php 
							if(!empty($vehicles)){
								foreach($vehicles as $val){
									echo '<option value="'.$val['id'].'"';
									if($this->input->post('unit')==$val['id']) { echo ' selected '; }
									echo '>'.$val['vname'].' ('.$val['vnumber'].')</option>';
								}
							}
						?>
					</select> &nbsp; 
                    <input type="submit" value="Search" name="search" class="btn btn-success pt-cta">
                </form>
            </div>
            
              <div class="table-responsive pt-datatbl">
                <table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
						  <th>Sr.No</th>
						  <th>Pay Period</th>
						  <th>Unit</th>
						  <th>Expence</th>
						  <th>Income</th>
						  <th>Total Pay</th>
                        <th>Action</th>
                    </tr>
                  </thead>
                 
                  <tbody>
                       <?php

                  if(!empty($finance)){
                      $n=1;
                  foreach($finance as $key)
                  {
                    ?>
                    <tr>
            <td><?php echo $n;?></td>
            <td><a href="<?php echo base_url().'admin/finance/update/'.$key['id'];?>"><?php echo date('M Y',strtotime($key['fdate'])).' Week '.$key['fweek'];?></a></td> 
            <td><a href="<?php echo base_url().'admin/finance/update/'.$key['id'];?>"><?php echo $key['vname'];?></a></td> 
            
            <td>$<?php echo $key['total_expense'];?></td> 
            <td>$<?php echo $key['total_income'];?></td> 
            <td>$<?php echo $key['total_amt'];?></td> 
            
            <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/finance/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                      <a style="color:#ff0000;" class="ml-2"  href="<?php echo base_url().'admin/finance/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this entry ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a></td>
                      </tr> 
		  
                    <?php
                    $n++;
                    }
                    }


                  ?> 
                   
                  </tbody>
                </table>
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
    
	$('#dataTable').DataTable({
        "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]]
    });
	
  });
  </script>
  <style>
      form.form {margin-bottom:25px;}
  </style>
  