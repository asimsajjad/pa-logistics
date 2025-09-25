<style>
      form.form {margin-bottom:25px;}
	  .td-input{display:none;}
	  .fas {cursor: pointer;}
	  .fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	  a.btn {margin-bottom: 5px;}
	  ol li {margin-left: 25px;color: red;}
  </style>
<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Upload Outside Dispatch CSV  
              <div class="add_page" style="float: right;">
                   <a class="nav-link" title="Create Section" href="<?php echo  base_url().'admin/outside-dispatch';?>"><input type="button" name="add" value="Back" class="btn btn-success btn-sm"/>
                   </a>  
                   </div>
            </div>
            
            
            <div class="card-body table_style">
                
            <div class="col-sm-12">
                    <p><strong>Please must follow these instructions</strong></p>
                    <ol>
                        <li>Don't change records in <strong>Dispatch ID</strong> column</li>
                        <li>Don't change column order</li>
                        <li>If you want to add new records than please keep <strong>Dispatch ID</strong> column blank</li>
                        <li>Don't add new column</li>
                        <li>Required fields: Trucking Company, Driver, Booked Under, Pick Up Date, Pick Up City, Pick Up Company, Drop Off City, Drop Off Company, Tracking, Company</li>
                        <li>Trucking Company, Driver, Booked Under must be same as in Driver & Trucking Company List csv file.</li>
                        <li>Date format will be month/day/year MM/DD/YYYY 5/31/2024</li>
                    </ol>
                    
                    
                    <?php
                    if($error){
                        echo '<p class="alert alert-danger">'.implode('<br>',$error).'</p>';
                    } 
                    if($upload == 'done'){
                        echo '<p class="alert alert-success">All data upload successfully.</p>';
                    }
                    ?>
                    <p>&nbsp;</p>
                <form class="form form-inline" method="post" action="" enctype="multipart/form-data">
                    <input type="file" required name="csvfile" class="form-control" accept=".csv"> &nbsp;
                    &nbsp;
                    <input type="hidden" value="Upload CSVf" name="csvfile1">
                    <input type="submit" value="Upload CSV" name="uploadcsv" class="btn btn-success"> 
                    
                </form>
                
                    <p>
                        <a href="<?php echo base_url('admin/outside-dispatch/upload-csv/?dummy=csv');?>" style="margin: 0 0 0 10px;" class="btn btn-primary">Dummy CSV</a> 
                        <a href="<?php echo base_url('admin/outside-dispatch/upload-csv/?driver-company=csv');?>" style="margin: 0 0 0 10px;" class="btn btn-primary">Driver & Trucking Company List</a>
                    </p>
            </div>
              
			  
            </div>
         
          </div>

		 

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script>
  $( function() {
    $( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });
    
  });
  </script>
