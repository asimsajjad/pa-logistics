
<div class="card mb-3">
	<div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3 class="m-0">Warehouses Address</h3>
		<div class="add_page" style="float: right;">
			<a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/address/warehouseAdd';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
			</a>  
		</div>
	</div>
	<div class="card-bodys table_style">
		<div class="table-responsive pt-datatbl">
			<table class="table table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Sr.No</th>
						<th>Warehouse</th>
						<th>Address</th>
						<th>City</th>
						<th>State</th>
						<th>Zip Code</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php
							
							if(!empty($warehouse)){
								$n=1;
								foreach($warehouse as $key)
								{
								?>
								<td><?php echo $n;?></td>
								<td><?php echo $key['warehouse'];?></td> 
								<td><?php echo $key['address'];?></td> 
								<td><?php echo $key['city'];?></td> 
								<td><?php echo $key['state'];?></td>
								<td><?php echo $key['zip'];?></td>
								<td>
									<a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/address/warehouseUpdate/'.$key['id']; ?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
									<a style="color:#ff0000;" class="ml-2" href="<?php echo base_url().'admin/address/warehouseDelete/'.$key['id']; ?>" onclick="return confirm('Are you sure delete this menu ?')"><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a>
									<br>
									<a class="btn btn-sm btn-info mt-1" href="<?php echo base_url().'admin/address/warehouseAddSublocation/'.$key['id']; ?>">Add Sublocation</a>
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
