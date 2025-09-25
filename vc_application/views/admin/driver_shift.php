  <style>
      form.form {margin-bottom:25px;}
	  .td-input{display:none;}
	  .fas {cursor: pointer;}
	  .fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	  a.btn {margin-bottom: 5px;}
  </style>
  
  <!-- DataTables Example -->
<div class="card mb-3">
            <div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
              <h3>Driver Shift </h3>
               <div class="add_page" style="float: right;">
                   <a class="nav-link" title="Create Section" href="<?php echo  base_url().'admin/driver_shift/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
                   </a>  </div></div>
            <div class="card-bodys table_style">
                
                
                
            <div class="d-block text-center">
                <form class="form form-inline" method="post" action="">
                    <input type="text" readonly placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 120px;" class="form-control datepicker"> &nbsp;
                    <input type="text"  style="width: 120px;" readonly placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker"> &nbsp;
					 
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
            
            
              <div class="table-responsive pt-datatbl">
                <table class="table table-bordered display nowrap" id="dataTable1" width="100%" cellspacing="0">
                  <thead>
                    <tr> 
						  <th>Driver</th>
						  <th>Start</th>
						  <th>End</th>
                        <th>Action</th>
                    </tr>
                  </thead>
                 
                  <tbody>
                    
                       <?php

                  if(!empty($driver_shift)){
                      $n=1;
                  foreach($driver_shift as $key)
                  {
                    ?> 
            <tr><td><a href="<?php echo base_url().'admin/driver_shift/update/'.$key['id'];?>"><?php echo $key['dname'];?></a></td> 
            <td><?php echo $key['start_date'];?></td> 
            <td><?php echo $key['end_date'];?></td> 
            <td><a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/driver_shift/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                      <a style="color:#ff0000;"  href="<?php echo base_url().'admin/driver_shift/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></a></td></tr> 
		  
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
    $(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
  });
  </script>
  