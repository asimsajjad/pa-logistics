<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Update Material
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/warehouseMaterials');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
		</div> 
	</div>
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-12 col-md-12 mobile_content">
			<div class="container">		
				<h3> Update Material</h3> 
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success p-0">
						<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
					</div>
				<?php } elseif($this->session->flashdata('error')){ ?>
					<div class="alert alert-danger p-0">
						<h4><?php echo $this->session->flashdata('error'); $this->session->set_flashdata('error',''); ?></h4> 
					</div>
				<?php } else if($this->session->flashdata('warning')) { ?>
					<div class="alert alert-danger p-0">
						<h4><?php echo $this->session->flashdata('warning'); $this->session->set_flashdata('warning',''); ?></h4> 
					</div>
				<?php } ?>
				<?php
					if(!empty($warehouse)){
						$key = $warehouse[0];
					?>    
					
					<form method="post" action="<?php echo base_url('admin/warehouse/updateMaterials/').$this->uri->segment(4);?>">
						<?php  echo validation_errors();?>
						<div class="row">
							<div class="col-sm-3 col-md-3 col-xs-3">
							<div class="form-group">
								<label for="contain">Customer</label>
								<select class="form-control select2" name="customerId" required>
									<option value="">Select Customer</option>
									<?php  
									if (!empty($companies)) {
										foreach ($companies as $val) {
											$selected = ($key['customerId'] == $val['id']) ? 'selected="selected"' : '';
											echo '<option value="' . $val['id'] . '" ' . $selected . '>' . $val['company'] . ' (' . $val['owner'] . ')</option>';
										}
									}
									?>
								</select>
							</div>
						</div>
							<div class="col-sm-3 col-md-3 col-xs-3">
								<div class="form-group">
									<label for="contain">Material Number</label>
									<input class="form-control" type="text" name="materialNumber" value="<?php echo $key['materialNumber'] ;?>" required/>
								</div>
							</div>
							<div class="col-sm-6 col-md-6 col-xs-6">
								<div class="form-group">
									<label for="contain">Description</label>
									<textarea class="form-control" name="description" style="height:48px; overflow: hidden;"><?php echo $key['description']; ?></textarea>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4 col-md-4 col-xs-4">
								<div class="form-group">
									<label for="contain">Batch</label>
									<input name="batch" type="tel" class="form-control"  value="<?php echo $key['batch'] ;?>" required/>
								</div>
							</div>
							<!-- <div class="col-sm-4 col-md-4 col-xs-4">
								<div class="form-group">
									<label for="contain">Lot Number</label>
									<input class="form-control" type="text" name="lotNumber" value="<?php echo $key['lotNumber'] ;?>"/>
								</div>
							</div> -->
							<div class="col-sm-4 col-md-4 col-xs-4">
								<div class="form-group">
									<label for="contain">Expiration Date</label>
									<input class="form-control" type="date" name="expirationDate" value="<?php echo date('Y-m-d', strtotime($key['expirationDate'])); ?>"/>
								</div>
							</div>
						</div>
						<div class="form-group">
							<input type="submit" name="save" value="Update" class="btn btn-success"/>
						</div>
					</form>
					<?php
					}
				?> 
			</div>
		</div>
	</div>
</div>	

	
<script>

</script>
