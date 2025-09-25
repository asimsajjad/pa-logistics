<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Drayage Equipments <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/drayage-equipments');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				    <h3> Add Drayage Equipment</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
                <div class="alert alert-success">
                    <h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
                </div>
          <?php } ?>
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
					<form method="post" action="<?php echo base_url('admin/drayage-equipments/add');?>" class="row" enctype="multipart/form-data">
					<div class="row">
					    <div class="col-sm-12">
					        <div class="form-group">
    							<label for="contain">Equipment Name</label>
						        <input name="name" type="text" class="form-control" required>
    						</div>
					    </div>
					</div>                    
					<div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" name="save" value="Add equipments" class="btn btn-success"/>
                        </div>
					</div>
					</form>
				</div>
			</div>

		</div>	
			
		</div>	
			
			
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });
  });
  </script>