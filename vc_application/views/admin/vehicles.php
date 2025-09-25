 <!-- DataTables Example -->
<div class="card">
    <div class="d-flex justify-content-between align-items-center pt-page-title pt-title-border flex-wrap mb-5">
        <h3 class="pt-page-title mb-0">Vehicles</h3>
        <div class="add_page">
           <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/vehicle/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
           </a>  
        </div>
    </div>
    <div class="pt-contents">
        
      <div class="table-responsive pt-tbl-responsive">
        <table class="table" id="dataTable1" width="100%" cellspacing="0">
          <tbody>
            <tr> 
  						  <th>Vehicle</th>
  						  <th>Vehicle No.</th>
  						  <th>License Plate</th>
  						  <th>VIN</th>
  						  <th>Make</th>
  						  <th>Model</th>
  						  <th>Year</th>
                <th>Action</th>
            </tr>                           
            <tr>
              <?php

                if(!empty($vehicles)){
                    $n=1;
                foreach($vehicles as $key)
                {
            ?> 
          <td><a href="<?php echo base_url().'admin/vehicle/update/'.$key['id'];?>"><?php echo $key['vname'];?></a></td> 
          <td><?php echo $key['vnumber'];?></td> 
          <td><?php echo $key['license_plate'];?></td> 
          <td><?php echo $key['vin'];?></td> 
          <td><?php echo $key['vmake'];?></td> 
          <td><?php echo $key['vmodel'];?></td> 
          <td><?php echo $key['vyear'];?></td> 
          <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/vehicle/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                    <a class="ml-2" style="color:#ff0000;"  href="<?php echo base_url().'admin/vehicle/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this menu ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a></td></tr>

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
