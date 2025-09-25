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
        Upload Warehouse Outbounds  
        <div class="add_page" style="float: right;">
            <a class="" title="Create Section" href="<?php echo  base_url().'admin/warehouseOutbounds';?>"><input type="button" name="add" value="Back" class="btn btn-success btn-sm"/>
            </a>  
        </div>
    </div>
    <div class="card-body table_style">
    <div class="col-sm-12">
        <p><strong>Please must follow these instructions</strong></p>
        <ol>
            <li>Don't change records in <strong> outbound_id, bound_id, Date Out, Customer, Warehouse, Sublocation & Material Number </strong> columns.</li>
            <li>Don't change column order.</li>
            <li>If you want to add new records than please keep <strong>outbound_id & bound_id</strong> column blank.</li>
            <li>Don't add new column.</li>
            <li>Required fields: Date In, Customer, Warehouse, Sublocation & Material Number.</li>
            <li>Date format will be month/day/year MM/DD/YYYY 6/30/2025.</li>
        </ol>
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger">
                <strong>The following errors occurred while processing the CSV:</strong>
                <ul style="margin-top:10px">
                    <?php foreach ($error as $msg): ?>
                        <li><?= $msg ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
                <?php } ?>
                  <?php if (!empty($skipped_rows)) { ?>
                        <div class="alert alert-warning">
                            <strong>The following rows were skipped due to stock quantity mismatch:</strong>
                            <ul style="margin-top:10px">
                            <?php foreach ($skipped_rows as $msg): ?>
                                <li><?= $msg ?></li>
                            <?php endforeach; ?>
                        </ul>
                        </div>
                    <?php } ?>
                <?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success p-0" style="margin-left:-14px">
						<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
					</div>
				<?php } elseif($this->session->flashdata('error')){ ?>
					<div class="alert alert-danger p-0" style="margin-left:-14px">
						<h4><?php echo $this->session->flashdata('error'); $this->session->set_flashdata('error',''); ?></h4> 
					</div>
				<?php } else if($this->session->flashdata('warning')) { ?>
					<div class="alert alert-danger p-0" style="margin-left:-14px">
						<h4><?php echo $this->session->flashdata('warning'); $this->session->set_flashdata('warning',''); ?></h4> 
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
                        <a href="<?php echo base_url('admin/warehouse/uploadOutbounds/?dummy=csv');?>" style="margin: 0 0 0 10px;" class="btn btn-primary">Dummy CSV</a> 
                    </p>
            </div>
              
			  
            </div>
         
          </div>

