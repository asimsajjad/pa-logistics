<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Event <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/calendar_weekly');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-8 col-sm-8 col-md-8 mobile_content">
			    <div class="container">
				    <h3> Add Event</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
					<form method="post" action="<?php echo base_url('admin/event/add');?>">
						 <?php  echo validation_errors();?>
						     
						<div class="form-group">
                            <label for="contain">Date</label>
                            <input name="cdate" type="text" class="form-control datepicker" required>
                        </div> 
                        
                        
						<div class="form-group">
                            <label for="contain">Title</label>
                            <input name="title" type="text" class="form-control" required>
                        </div>  
                      

                        <div class="form-group">
                            <input type="submit" name="save" value="Add Event" class="btn btn-success"/>
                        </div>
					</form>
				</div>
			</div>

		</div>	
			
		</div>	
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
   
 
  <script>
  $( function() {
    $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd'}); 
    $(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
  } );
  </script>
  