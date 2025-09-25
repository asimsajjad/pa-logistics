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
              Upload Warehouse Materials  
              <div class="add_page" style="float: right;">
                   <a class="" title="Create Section" href="<?php echo  base_url().'admin/warehouseMaterials';?>"><input type="button" name="add" value="Back" class="btn btn-success btn-sm"/>
                   </a>  
                   </div>
            </div>
            
            
            <div class="card-body table_style">
                
            <div class="col-sm-12">
                    <p><strong>Please must follow these instructions</strong></p>
                    <ol>
                        <li>Don't change records in <strong>Material ID</strong> column</li>
                        <li>Don't change column order</li>
                        <li>If you want to add new records than please keep <strong>Material ID</strong> column blank</li>
                        <li>Don't add new column</li>
                        <li>Required fields: Customer, Material Number, Batch</li>
                        <li>Date format will be month/day/year MM/DD/YYYY 6/30/2025</li>
                    </ol>
                    <?php
                    // if($error){
                    //     echo '<p class="alert alert-danger">'.implode('<br>',$error).'</p>';
                    // } 
                    if($upload == 'done'){
                        echo '<p class="alert alert-success">Material upload successfully.</p>';
                    }
                    ?>
                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger">
                            <?php foreach ($error as $msg) {
                                echo "<p>$msg</p>";
                            } ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($skipped)) { ?>
                        <div class="alert alert-warning">
                            <strong>The following rows were skipped due to duplicates:</strong>
                            <ul>
                            <?php foreach ($skipped as $skip) { ?>
                                <li>
                                    <strong>Row #<?php echo $skip['rowNumber']; ?> skipped</strong>: Material Number 
                                    <strong><?php echo htmlspecialchars($skip['materialNumber']); ?></strong> 
                                    with same Batch 
                                    <strong><?php echo htmlspecialchars($skip['batch']); ?></strong> 
                                    and Customer 
                                    <strong><?php echo htmlspecialchars($skip['customer']); ?></strong> 
                                    already exists.
                                </li>
                            <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                    <p>&nbsp;</p>
                <form class="form form-inline" method="post" action="" enctype="multipart/form-data">
                    <input type="file" required name="csvfile" class="form-control" accept=".csv"> &nbsp;
                    &nbsp;
                    <input type="hidden" value="Upload CSVf" name="csvfile1">
                    <input type="submit" value="Upload CSV" name="uploadcsv" class="btn btn-success"> 
                </form>
                    <p>
                        <a href="<?php echo base_url('admin/warehouse/uploadMaterials/?dummy=csv');?>" style="margin: 0 0 0 10px;" class="btn btn-primary">Dummy CSV</a> 
                    </p>
            </div>
              
			  
            </div>
         
          </div>

