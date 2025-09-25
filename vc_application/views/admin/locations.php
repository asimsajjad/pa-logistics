 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
              <h3 class="m-0">Locations</h3>  
              <div class="add_page" style="float: right;">
                   <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/location/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
                   </a>  
                 </div>
            </div>
            <div class="card-bodys table_style">
                
              <div class="table-responsive pt-tbl-responsive">
                <table class="table table-bordereds" id="dataTable1" width="100%" cellspacing="0">
                  <tbody>
                    <tr>
						  <th>Sr.No</th>
						  <th>City</th> 
						  <th>Location</th> 
						  <th>Address</th> 
						  <th>Notes</th> 
                        <th>Action</th>
                    </tr>
               
                 
                  
                    <tr>
                       <?php

                  if(!empty($locations)){
                      $n=1;
                  foreach($locations as $key)
                  {
                    ?>
            <td><?php echo $n;?></td>
            <td><a href="<?php echo base_url().'admin/location/update/'.$key['id'];?>"><?php echo $key['city'];?></a></td>  
            <td><a href="<?php echo base_url().'admin/location/update/'.$key['id'];?>"><?php echo $key['location'];?></a></td>  
            <td><?php echo $key['address'];?></td>  
            <td><?php echo $key['notes'];?></td>  
            <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/location/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                      <a style="color:#ff0000;" class="ml-2"  href="<?php echo base_url().'admin/location/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this menu ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></a></td></tr> 
		  
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
