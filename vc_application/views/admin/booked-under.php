 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
              <h3 class="m-0">Booked Under</h3>
              <div class="add_page" style="float: right;">
                   <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/booked-under/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success btn-sm pt-cta"/>
                   </a>  </div></div>
            <div class="card-bodys table_style">
                
              <div class="table-responsive pt-tbl-responsive">
                <table class="table table-bordereds " id="dataTable1" width="100%" cellspacing="0">
                  <tbody>
                    <tr>
						  <th>Sr.No</th>
						  <th>Company</th>
						  <th>Email</th>
						  <th>Owner</th>
						  <th>Status</th>
                        <th>Action</th>
                    </tr>
                 
                 
                  
                    
                       <?php

                  if(!empty($bookedUnder)){
                      $n=1;
                  foreach($bookedUnder as $key)
                  {
                    ?><tr>
            <td><?php echo $n;?></td>
            <td><a href="<?php echo base_url().'admin/booked-under/update/'.$key['id'];?>"><?php echo $key['company'];?></a></td> 
            <td><a href="<?php echo base_url().'admin/booked-under/update/'.$key['id'];?>"><?php echo $key['email'];?></a></td> 
            <td><?php echo $key['owner'];?></td> 
            <td><?php echo $key['status'];?></td> 
            <td>
                <a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/booked-under/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                      <a style="color:#ff0000;" class="ml-2" href="<?php echo base_url().'admin/booked-under/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this menu ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a></td>
                      </tr> 
		  
                    <?php
                    $n++;
                    }
                    }
					else{
						echo "<td>No data found</td><td></td><td></td><td></td><td></td><td></td>";
						
					}


                  ?>

                    
                   
                  </tbody>
                </table>
              </div>
            </div>
         
          </div>

  
