<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Finance Multiple View <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/finance');?>" class="nav-link"><input type="button" name="Back" value="Finance" class="btn btn-success btn-sm"/></a>
                     </div> 
					 </div> 
					 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				     
				     <div class="col-sm-12 text-center">
                <form class="form form-inline" method="post" action="">
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
					
					$current_month = date('Y-m').'-01,'.date('Y-m-t');
					if($this->input->post('month')!='') { $current_month = '11'; }
					?>
                    <select name="month" class="form-control" required>
						<option value="">Select Month</option>
						<option value="<?php echo $month1;?>" <?php if($this->input->post('month')==$month1 || $current_month==$month1) { echo ' selected '; } ?>>January <?php echo date('Y');?></option>
						<option value="<?php echo $month2;?>" <?php if($this->input->post('month')==$month2 || $current_month==$month2) { echo ' selected '; } ?>>February <?php echo date('Y');?></option>
						<option value="<?php echo $month3;?>" <?php if($this->input->post('month')==$month3 || $current_month==$month3) { echo ' selected '; } ?>>March <?php echo date('Y');?></option>
						<option value="<?php echo $month4;?>" <?php if($this->input->post('month')==$month4 || $current_month==$month4) { echo ' selected '; } ?>>April <?php echo date('Y');?></option>
						<option value="<?php echo $month5;?>" <?php if($this->input->post('month')==$month5 || $current_month==$month5) { echo ' selected '; } ?>>May <?php echo date('Y');?></option>
						<option value="<?php echo $month6;?>" <?php if($this->input->post('month')==$month6 || $current_month==$month6) { echo ' selected '; } ?>>June <?php echo date('Y');?></option>
						<option value="<?php echo $month7;?>" <?php if($this->input->post('month')==$month7 || $current_month==$month7) { echo ' selected '; } ?>>July <?php echo date('Y');?></option>
						<option value="<?php echo $month8;?>" <?php if($this->input->post('month')==$month8 || $current_month==$month8) { echo ' selected '; } ?>>August <?php echo date('Y');?></option>
						<option value="<?php echo $month9;?>" <?php if($this->input->post('month')==$month9 || $current_month==$month9) { echo ' selected '; } ?>>September <?php echo date('Y');?></option>
						<option value="<?php echo $month10;?>" <?php if($this->input->post('month')==$month10 || $current_month==$month10) { echo ' selected '; } ?>>October <?php echo date('Y');?></option>
						<option value="<?php echo $month11;?>" <?php if($this->input->post('month')==$month11 || $current_month==$month11) { echo ' selected '; } ?>>November <?php echo date('Y');?></option>
						<option value="<?php echo $month12;?>" <?php if($this->input->post('month')==$month12 || $current_month==$month12) { echo ' selected '; } ?>>December <?php echo date('Y');?></option>
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
                    <input type="submit" value="Search" name="search" class="btn btn-success">
                </form>
            </div>
            <br><br>
				 	<?php if($this->session->flashdata('item')){ ?>
						<div class="alert alert-success">
							<h4><?php echo $this->session->flashdata('item'); ?></h4> 
						</div>
					<?php } ?>
		  
		  
					<div class="row">
					
		   <?php
                       
                  if(!empty($finance)){
					foreach($finance as $key) { 
                    ?>  
 
					<div class="col-sm-3">
					
					<div class="table-resposnive">
					<table class="table table-bordered">
					<tr> <th colspan="2">Pay Period <?php echo $key['fweek'];?><br>
					    <?php echo date('d M Y',strtotime($key['fdate'])); ?>
					</th></tr>
					<?php  $total_income = $total_income + $key['unit_pay']; ?>
					<tr> <td><?php echo $key['vname']; ?></td>  
					<td>$ <?php echo $key['unit_pay'];?></td></tr>  
					 <tr> <th colspan="2">Expences</th> </tr>
					 <tr> <td><?php echo $key['dname']; ?></td>  
					<td>$ <?php echo $key['driver_pay'];?></td> </tr> 
					
					
					<?php 
					if($key['expenses']!='') {
						$expence_arr = explode('--~~--',$key['expenses']);
						if($expence_arr[0]!='' && $expence_arr[1]!='') {
							$expence_txt = explode('-~-',$expence_arr[0]);
							$expence_val = explode('-~-',$expence_arr[1]);
							
							for($i=0;$i<count($expence_txt);$i++){
							  if($expence_txt[$i]!='') {
							?>
								<tr> <td><?php echo $expence_txt[$i];?></td>  
								<td><?php echo $expence_val[$i];?></td> </tr> 
							  <?php 
							  }
							} 
						}
					} 
					?>
					 
					<tr> <th colspan="2">Other Expences</th> </tr>
					
					<?php 
					if($key['other_option']!='') {
						$other_option_arr = explode('--~~--',$key['other_option']);
						if($other_option_arr[0]!='' && $other_option_arr[1]!='') {
							$other_option_txt = explode('-~-',$other_option_arr[0]);
							$other_option_val = explode('-~-',$other_option_arr[1]);
							 
							for($i=0;$i<count($other_option_txt);$i++){
							  if($other_option_txt[$i]!='') {
							?>
								<tr> <td><?php echo $other_option_txt[$i];?></td>  
								<td><?php echo $other_option_val[$i];?></td> </tr>
							  <?php 
							  }
							} 
						}
					}
					?>
					 
					
					<tr><th>Total Income:</th> 
					<th>$ <?php echo $key['total_income']; ?></th></tr>
					
					<tr> <th>Total Expences:</th>  
					<th>-$ <?php echo $key['total_expense']; ?></th></tr>
					
					<tr> <th>Total Pay:</th>  
					<th>$ <?php echo $key['total_amt']; ?></th></tr>
					<?php if($key['notes']!='') { ?>
					<tr> <td colspan="2"><?php echo $key['notes'];?></td> </tr>
					<?php } ?>
				</table> 
					
					</div> 
				  
				  </div>
				  <?php }
				  }				  ?>
				</div>
			</div>

		</div>	
			
		</div>	
		 
</div>		 
		 
