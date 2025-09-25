<!-- DataTables Example -->
<div class="card mb-3">
	<div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3 class="m-0">Dispatch Info </h3>
		<div class="add_page" style="float: right;">
			<a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/dispatch-info/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
			</a>  
		</div>
	</div>
	<div class="card-bodys table_style">
		<div class="table-responsive pt-tbl-responsive">
			<table class="table table-bordereds" id="dataTable1" width="100%" cellspacing="0">
				<tbody>
					<tr>
						<th>Sr.No</th>
						<th>Dispatch Info</th>  
						<th>Status</th>  
						<th>Action</th>
					</tr>
				
				
					<tr>
						<?php
							if(!empty($dispatchInfo)){
								$n=1;
								foreach($dispatchInfo as $key)
								{
								?>
								<td><?php echo $n;?></td>
								<td><a href="<?php echo base_url().'admin/dispatch-info/update/'.$key['id'];?>"><?php echo $key['title'];?></a></td>  
								<td><?php echo $key['status'];?></td>  
								<td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/dispatch-info/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
								<a style="color:#ff0000;" class="ml-2" href="<?php echo base_url().'admin/dispatch-info/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this shipment ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a></td></tr> 
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