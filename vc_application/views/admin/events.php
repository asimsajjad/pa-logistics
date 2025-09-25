 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              All Events  <div class="add_page" style="float: right;">
                   <a class="nav-link" title="Create Section" href="<?php echo  base_url().'admin/event/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success"/>
                   </a>  </div></div>
            <div class="card-body table_style">
                
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                  <thead>
                    <tr>
						  <th>Sr.No</th>
						  <th>Date</th> 
						  <th>Title</th> 
                        <th>Action</th>
                    </tr>
                  </thead>
                 
                  <tbody>
                    <tr>
                       <?php

                  if(!empty($events)){
                      $n=1;
                  foreach($events as $key)
                  {
                    ?>
            <td><?php echo $n;?></td>
            <td><a href="<?php echo base_url().'admin/event/update/'.$key['id'];?>"><?php echo date('m-d-Y',strtotime($key['cdate']));?></a></td>  
            <td><a href="<?php echo base_url().'admin/event/update/'.$key['id'];?>"><?php echo $key['title'];?></a></td>  
           
            <td><a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/event/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                      <a style="color:#ff0000;"  href="<?php echo base_url().'admin/event/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this menu ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a></td></tr> 
		  
                    <?php
                    $n++;
                    }
                    }


                  ?>

                   
                    </tr>
                   
                  </tbody>
                </table>
              </div>
            </div>
         
          </div>

        </div>
