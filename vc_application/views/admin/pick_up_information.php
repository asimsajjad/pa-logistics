 <!-- DataTables Example -->
<div class="card mb-3">
      <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
        <h3 class="m-0">Empty Pick Up Information</h3>
        <div class="add_page" style="float: right;">
             <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/pick-up-Information/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
             </a>  </div></div>
      <div class="card-bodys table_style">
          
        <div class="table-responsive pt-tbl-responsive">
          <table class="table table-bordereds" width="100%" cellspacing="0">
            <tbody>
              <tr>
                <th>Company</th>
                <th>Address</th> 
                <th>City</th> 
                <th>State</th> 
                <th>Zip Code</th> 
                <th>Phone</th> 
                <th>Email</th> 
                <th>Action</th>
              </tr>
              <tr>
                 <?php

            if(!empty($PickUpInformation)){
                $n=1;
            foreach($PickUpInformation as $key)
            {
              ?> 
      <td><?php echo $key['company'];?></td>  
      <td><?php echo $key['address'];?></td>  
      <td><?php echo $key['city'];?></td>
      <td><?php echo $key['state'];?></td> 
      <td><?php echo $key['zip'];?></td>  
      <td><?php echo $key['phone'];?></td>
      <td><?php echo $key['email'];?></td>  
      <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/pick-up-Information/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                <a style="color:#ff0000;" class="ml-2"  href="<?php echo base_url().'admin/pick-up-Information/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></a></td></tr> 

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
