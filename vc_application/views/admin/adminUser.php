 <!-- DataTables Example -->
<div class="card mb-3">
    <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
      <h3 class="m-0">Admin User</h3>
      <div class="add_page" style="float: right;">
           <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/admin-user/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
           </a>  </div>
    </div>
    <div class="card-bodys table_style">
        
      <div class="table-responsive pt-tbl-responsive">
        <table class="table table-bordereds" width="100%" cellspacing="0">
          <tbody>
            <tr>
		  <th>User</th>
		  <th>Username</th> 
		  <th>Email</th> 
		  <th>Register Date</th>  
		  <th>Status</th>  
                <th>Action</th>
            </tr>
                           
          
            <tr>
               <?php

          if(!empty($adminUser)){
              $n=1;
          foreach($adminUser as $key)
          {
            ?> 
    <td><a href="<?php echo base_url().'admin/admin-user/update/'.$key['id'];?>"><?php echo $key['uname'];?></a></td>  
    <td><?php echo $key['track'];?></td>  
    <td><?php echo $key['email'];?></td>  
    <td><?php echo $key['created_on'];?></td>  
    <td><?php echo $key['status'];?></td>  
    <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/admin-user/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
    <?php if($key['id'] > 1) { ?>
              <a style="color:#ff0000;" class="ml-2"  href="<?php echo base_url().'admin/admin-user/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this user?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a>
    <?php } ?>
              </td></tr> 

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
