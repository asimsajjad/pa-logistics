<div class="card mb-3">
  <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
    <h3 class="m-0">Carriers</h3>  
    <div class="add_page" style="float: right;">
      <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/trucking-company/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success btn-sm pt-cta"/>
      </a>  
    </div>
  </div>
  <div class="card-bodys table_style">   
    <div class="d-block text-center">
			<?php
			if($this->input->post('agingSearch')) {
				$agingSearch = $this->input->post('agingSearch'); 
			}
			else {
				$agingSearch =''; 
			}
			?>
			<form class="form form-inline" method="post" action="" style="margin-bottom: 15px; margin-top: -25px;">
				<select class="form-control select2" name="truckingCompany[]" data-placeholder="Select Carrier" multiple="multiple" style="max-width: 300px;" >
					<option value="">Select Carrier</option>
					<?php 
					$selected_truckingCompanies = $this->input->post('truckingCompany');
					$truckingCompanyArr = array();
					if (!empty($truckingCompaniesForSelect)) {
						foreach ($truckingCompaniesForSelect as $val) {
							$truckingCompanyArr[$val['id']] = $val['company'];
							echo '<option value="'.$val['id'].'"';
							if(!empty($selected_truckingCompanies) && in_array($val['id'], $selected_truckingCompanies)) { echo ' selected '; }
							echo '>'.$val['company'].'</option>';
						}
					}
					?>
				</select>
				&nbsp;
				<input type="submit" name="search" value="Search" class="btn btn-success pt-cta">
			</form>
		</div>           
    <div class="table-responsive pt-datatbl"  style="overflow-y: auto;max-height: 90vh;">
    <table class="table table-bordered display nowrap" id="dataTable1" width="100%" cellspacing="0">
  <thead>
    <tr>
      <th>Sr.No</th>
      <th>Company</th>
      <th>Email</th>
      <th>Owner</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if (!empty($truckingCompanies)) {
      $n = 1;
      foreach ($truckingCompanies as $key) {
        ?>
        <tr>
          <td><?php echo $n; ?></td>
          <td><a href="<?php echo base_url().'admin/trucking-company/update/'.$key['id'];?>"><?php echo $key['company'];?></a></td> 
          <td><a href="<?php echo base_url().'admin/trucking-company/update/'.$key['id'];?>"><?php echo $key['email'];?></a></td> 
          <td><?php echo $key['owner'];?></td> 
          <td><?php echo $key['status'];?></td> 
          <td>
            <a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/trucking-company/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
            <a style="color:#ff0000;" class="ml-2" href="<?php echo base_url().'admin/trucking-company/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this menu ?')"><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a>
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
<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<!-- <link href="<?php echo base_url().'assets/css/5.3.0bootstrap.min.css'; ?>" rel="stylesheet"> -->
<!-- <script src="<?php echo base_url().'assets/js/5.3.0bootstrap.bundle.min.js'; ?>"></script> -->
<script>
   $(document).ready(function() {
      $('.select2').select2();
   });
   $('#dataTable1').DataTable({
    responsive: true,
    lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]]
  });
</script>
<style>
  .select2-container--default .select2-selection--multiple {  
    border-radius: 25px !important;
  }
  .select2-container .select2-selection--multiple {
    min-height: 47px !important;
  }
  .select2-container .select2-search--inline .select2-search__field {
    margin-top: 10px !important;
  }
</style>