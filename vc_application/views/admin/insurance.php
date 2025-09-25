 <!-- DataTables Example -->
<div class="card mb-3">
      <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
        <h3 class="m-0">Insurance</h3>
        <div class="add_page" style="float: right;">
             <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/insurance/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
             </a>  
          </div>
        </div>
      <div class="card-bodys table_style">
          
        <div class="table-responsive pt-tbl-responsive">
          <table class="table table-bordereds" width="100%" cellspacing="0">
            <tbody>
              <tr>
			  <th>Title</th>
			  <th>Start Date</th> 
			  <th>End Date</th> 
			  <th>Coverage Amount</th> 
			  <th>Policy number</th> 
			  <th>Insurance Provider</th> 
			  <th>Cost</th>
			  <th>Complete</th>
                  <th>Action</th>
              </tr>
                           
            
                 <?php

            if(!empty($insurance)){
                $n=1;
            foreach($insurance as $key)
            {
              ?>
              <tr> 
      <td><a href="<?php echo base_url().'admin/insurance/update/'.$key['id'];?>"><?php echo $key['title'];?></a></td>  
      <td><?php echo $key['startDate'];?></td>  
      <td><?php echo $key['endDate'];?></td>  
      <td><?php echo $key['coverageAmount'];?></td>
      <td><?php echo $key['policyNumber'];?></td>  
      <td><?php echo $key['insuranceProvider'];?></td>  
      <td><?php echo $key['coast'];?></td>  
      <td><?php echo $key['complete'];?></td>  
      <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/insurance/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                <a style="color:#ff0000;" class="ml-2"  href="<?php echo base_url().'admin/insurance/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></a></td>
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
