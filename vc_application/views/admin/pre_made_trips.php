 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Pre Made Trips  
              <div class="add_page" style="float: right;">
                   <a class="nav-link" title="Create Section" href="<?php echo  base_url().'admin/pre_made_trips/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success"/>
                   </a>  
                   </div>
            </div>
            
            
            <div class="card-body table_style">
                 
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                  <thead>
                    <tr>
						  <th>Trip Name</th>
						  <th>Pickup City</th>
						  <th>Pick Up Location</th>
						  <th>Drop Off City</th>
						  <th>Drop Off Location</th>
						  <th>Rate</th>
						  <th>PA Rate</th>
						  <th>Company</th>
                        <th>Action</th>
                    </tr> 
                  </thead>
                 
                  <tbody>
                    
                       <?php

                  if(!empty($pre_made_trips)){
                      $n=1; $rate = $parate = 0;
                  foreach($pre_made_trips as $key) { 
                    ?>
                    <tr>
            <td><a href="<?php echo base_url().'admin/pre_made_trips/update/'.$key['id'];?>"><?php echo $key['pname'];?></a></td>
            <td><a href="<?php echo base_url().'admin/pre_made_trips/update/'.$key['id'];?>"><?php echo $key['pcity'];?></a></td>
            <td><a href="<?php echo base_url().'admin/pre_made_trips/update/'.$key['id'];?>"><?php echo $key['plocation'];?></a></td> 
            <td><?php echo $key['dcity'];?></td> 
            <td><?php echo $key['dlocation'];?></td> 
            <td><?php if($key['rate'] > 0) { echo '$'; } echo $key['rate'];?></td> 
            <td><?php if($key['parate'] > 0) { echo '$'; } echo $key['parate'];?></td>  
            <td><?php echo $key['company'];?></td> 
           
            <td>
                <a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/pre_made_trips/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                      <a style="color:#ff0000;"  href="<?php echo base_url().'admin/pre_made_trips/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this menu ?')"><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a>
                      </td>
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
 
  <style>
      form.form input{margin-bottom:10px;}
  </style>