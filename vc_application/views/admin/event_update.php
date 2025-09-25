<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Event 
             <div class="add_page" style="float: right;">
       
 <a href="<?php echo base_url('admin/calendar_weekly');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
                     </div> </div>
<div class="container-fluid">
		<div class="col-xs-8 col-sm-8 col-md-8 mobile_content">
			    <div class="container">

				    <h3> Update Event</h3> 
				    <?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4>
           </div>
          <?php } ?>
                   <?php
                       
                  if(!empty($events)){
                  $key = $events[0];
                  
                    ?>    
		           <form method="post" action="<?php echo base_url('admin/event/update/').$this->uri->segment(4);?>">
                      
						 <?php  echo validation_errors();?>
                         
						<div class="form-group">
                            <label for="contain">Date</label>
                            <input class="form-control datepicker" type="text" name="cdate" value="<?php echo $key['cdate'] ;?>" required/>
                        </div> 
                        
						<div class="form-group">
                            <label for="contain">Title</label>
                            <input class="form-control" type="text" name="title" value="<?php echo $key['title'] ;?>" required/>
                        </div> 
                         
                       
                        <div class="form-group">
                            <input type="submit" name="save" value="Update" class="btn btn-success"/> &nbsp;
                            <a class="btn btn-danger"  href="<?php echo base_url().'admin/event/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this event ?')" >Delete</a>
                        </div>

					</form>
                    <?php
                    }
                  ?> 
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
			