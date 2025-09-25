<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Add/Update Warehouse Sublocation
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/warehouseAddress');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
		</div> 
	</div>
	<div class="container-fluid">
		<div class="col-xs-8 col-sm-8 col-md-8 mobile_content">
			<div class="container">
				<h3> Add/Update Warehouse Sublocation</h3> 
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item');$this->session->set_flashdata('item',''); ?></h4>
					</div>
				<?php } 
					if(!empty($warehouse)){
						$key = $warehouse[0];
					?>    
					<form method="post" action="<?php echo base_url('admin/address/warehouseAddSublocation/').$this->uri->segment(4);?>">
						<?php echo validation_errors(); ?>
						
						<div class="row">
						    <div class="col-sm-8">
						        <div class="form-group">
        							<label for="contain">Warehouse</label>
							        <input class="form-control" type="text" name="warehouse" value="<?php echo $key['warehouse'] ;?>" required readonly/>
        						</div>
						    </div>
						</div>

						<div class="row">
							<div class="col-sm-8">
								<label>Sublocations</label>
								<div id="sublocation-wrapper">
									<?php
									if (!empty($sublocations)) {
										$i = 0;
										foreach ($sublocations as $subloc) {
											echo '<div class="d-flex mb-2 align-items-center">';
											echo '<input type="text" name="sublocations[' . $subloc['id'] . ']" class="form-control" value="' . htmlspecialchars($subloc['name']) . '" />';
											if ($i == 0) {
												echo '<button type="button" class="btn btn-success btn-sm ml-2 add-email2">+</button>';
											} else {
												echo '<button type="button" class="btn btn-danger btn-sm ml-2 remove-email2">-</button>';
											}
											echo '</div>';
											$i++;
										}
									} else {
										echo '<div class="d-flex mb-2 align-items-center">
												<input type="text" name="sublocations[new][]" class="form-control" placeholder="Enter sublocation" />
												<button type="button" class="btn btn-success btn-sm ml-2 add-email2">+</button>
											</div>';
									}
									?>
								</div>
							</div>
						</div>

						<div class="form-group mt-3">
							<input type="submit" name="save" value="Add / Update" class="btn btn-success"/>
						</div>						
					</form>
				<?php } ?> 
			</div>
		</div>
	</div>	
</div>

<script>
document.addEventListener('click', function (e) {
	if (e.target && e.target.classList.contains('add-email2')) {
		const wrapper = document.getElementById('sublocation-wrapper');
		const div = document.createElement('div');
		div.className = 'd-flex mb-2 align-items-center';
		div.innerHTML = `
			<input type="text" name="sublocations[new][]" class="form-control" placeholder="Enter sublocation" />
			<button type="button" class="btn btn-danger btn-sm ml-2 remove-email2">-</button>
		`;
		wrapper.appendChild(div);
	}

	if (e.target && e.target.classList.contains('remove-email2')) {
		e.target.closest('.d-flex').remove();
	}
});
</script>
