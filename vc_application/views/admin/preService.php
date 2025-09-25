 <!-- DataTables Example -->
<div class="card mb-3">
    <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
      <h3 class="m-0">Predefined Service</h3>
      <div class="add_page" style="float: right;">
           <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/pre-service/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
           </a>  </div></div>
    <div class="card-bodys table_style">
        
      <div class="table-responsive pt-tbl-responsive">
        <table class="table table-bordereds" width="100%" cellspacing="0">
          <tbody>
            <tr>
		  <th>Sr. No.</th>
		  <th>Service</th> 
		  <th>Frequency</th>  
                <th>Action</th>
            </tr>
                       
          
            
               <?php

          if(!empty($preService)){
              $n=1;
          foreach($preService as $key)
          {
            ?> 
        <tr>     
    <td><?php echo $n;?></td>    
    <td><a href="<?php echo base_url().'admin/pre-service/update/'.$key['id'];?>"><?php echo $key['title'];?></a></td>  
    <td><?php echo $key['frequency'];?></td>    
    <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/pre-service/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
              <a style="color:#ff0000;" class="ml-2"  href="<?php echo base_url().'admin/pre-service/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></a></td></tr> 

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
