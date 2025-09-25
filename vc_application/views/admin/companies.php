 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
                <h3 class="mb-0">Companies</h3>
                <div class="add_page" style="float: right;">
                   <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/company/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
                   </a>  
                 </div>
               </div>
               <form class="form form-inline mb-2" method="post" action="">
                <input type="hidden" name="search" id="" value="Search">
                <select name="company[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Customer" style="max-width: 250px;">
                  <option value="">All Company</option>
                  <?php 
                    $selected_companies = $this->input->post('company');
                    $companyArr = array();
                    if(!empty($customers)){
                      foreach($customers as $val){
                        $companyArr[$val['id']] = $val['company'];
                        echo '<option value="'.$val['id'].'"';
                        if(!empty($selected_companies) && in_array($val['id'], $selected_companies)) { echo ' selected '; }
                        echo '>'.$val['company'].'</option>';
                      }
                    }
                
                  ?>
                </select>
                &nbsp; 
                <input type="submit" value="search" class="btn btn-success pt-cta">
              </form>
            <div class="card-bodys table_style">
                
              <div class="table-responsive pt-datatbl">
                <table class="table  table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
						  <th>Sr.No</th>
						  <th>Company</th>
						  <th>Address</th>
						  <th>Company Phone</th>
						  <th>Invoicing Email</th>
						  <th>Payment Terms</th>
              <th>warehouse Customer</th>
              <th>Status</th>
                        <th>Action</th>
                    </tr>
                  </thead>
                 
                  <tbody>
                    <tr>
                       <?php

                  if(!empty($companies)){
                      $n=1;
                  foreach($companies as $key)
                  {
                    ?>
            <td><?php echo $n;?></td>
            <td><?php echo $key['company'];?></td> 
            <td><?php echo $key['address'];?></td> 
            <td><?php echo $key['phone'];?></td> 
            <td><?php echo $key['email'];?></td> 
            <td><?php echo $key['paymenTerms'];?></td> 
            <td><?php echo $key['warehouseCustomer'];?></td> 
            <td><?php echo $key['status'];?></td> 
            <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/company/update/'.$key['id'];?> ">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                      <a style="color:#ff0000;" class="ml-2"  href="<?php echo base_url().'admin/company/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this menu ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a></td></tr>
		  
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
<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<script>
   $(document).ready(function() {
      $('.select2').select2();
   });
</script>