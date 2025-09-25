 <!-- DataTables Example -->
<div class="card mb-3">
      <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
        <h3 class="m-0">Reimbursements</h3>
        <div class="add_page" style="float: right;">
             <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/reimbursement/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
             </a>  </div></div>
      <div class="card-bodys table_style">
          
          
      <div class="mb-3">
          <form class="form form-inline" method="post" action="">
              <input type="text" readonly placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 120px;" class="form-control datepicker"> &nbsp;
              <input type="text"  style="width: 120px;" readonly placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker"> &nbsp;
		
              <input type="text" placeholder="Truck" value="<?php if($this->input->post('truck')) { echo $this->input->post('truck'); } ?>" name="truck" class="form-control"> &nbsp;
              
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
              <input type="submit" value="Search" name="search" class="btn btn-success pt-cta">
          </form>
      </div>
      
        <div class="table-responsive pt-tbl-responsive">
          <table class="table table-bordereds" id="dataTable1" width="100%" cellspacing="0">
            <tbody>
              <tr> 
			  <th>Driver</th>
			  <th>Amount</th>
			  <th>Truck</th>
			  <th>Notes</th>
			  <th>Date</th>
                  <th>Action</th>
              </tr>
                           
            
              
                 <?php

            if(!empty($reimbursement)){
                $n=1;
            foreach($reimbursement as $key)
            {
              ?> 
      <tr><td><a href="<?php echo base_url().'admin/reimbursement/update/'.$key['id'];?>"><?php echo $key['dname'];?></a></td> 
      <td><?php echo $key['amount'];?></td> 
      <td><?php echo $key['truck'];?></td> 
      <td><?php echo $key['notes'];?></td> 
      <td><?php echo $key['fdate'];?></td> 
      <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/reimbursement/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                <a style="color:#ff0000;" class="ml-2" href="<?php echo base_url().'admin/reimbursement/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></a></td></tr> 

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

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
    
  });
  </script>
  