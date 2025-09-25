<style>
  	form.form {margin-bottom:25px;}
	.td-input{display:none;}
	.fas {cursor: pointer;}
	.fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	a.btn {margin-bottom: 5px;}
  	.select2-container--default .select2-selection--multiple {
    border-radius: 20px !important;
	}
	.select2-container .select2-selection--multiple {
    min-height: 46px !important;
	}
	.form-control {
    	height: 39px !important;
	}
  </style>
<div class="card mb-3">
	<div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
		<h3 class="m-0">Materials</h3>
		<div class="add_page" style="float: right;">
			<a class="nav-link p-0" style="display: inline;" title="Create Section" href="<?php echo  base_url().'admin/warehouse/addMaterials';?>"> <input type="button" name="add" value="Add New" class="btn btn-success btn-sm pt-cta"/>
			</a>
			<a class="nav-link p-0" title="Upload Materials" href="<?php echo  base_url().'admin/warehouse/uploadMaterials';?>" style="display: inline;"><input type="button" name="add" value="Upload Materials" class="btn btn-primary btn-sm pt-cta"/>
			</a>
		</div>
	</div>
  <div style="margin-top: -25px;">
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
	</div>
  <div class="card-bodys table_style">
    <div class="d-block text-center">
			<?php 
			if($this->input->post('sdate')) { 
				$sdate = $this->input->post('sdate'); 
			} 
			else {
				$sdate =''; 
			}
			if($this->input->post('edate')) {
				$edate = $this->input->post('edate'); 
			}
			else {
				$edate =''; 
			}
			?> 
			<form class="form form-inline" method="post" id="receivableSearchForm" action="">	
        <input type="hidden" name="search" id="agingSearch" value="Search">
				<select name="company[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Customer" style="max-width: 250px;">
					<option value="">All Company</option>
					<?php 
						$selected_companies = $this->input->post('company');
						$companyArr = array();
						if(!empty($companies)){
							foreach($companies as $val){
								$companyArr[$val['id']] = $val['company'];
								echo '<option value="'.$val['id'].'"';
								if(!empty($selected_companies) && in_array($val['id'], $selected_companies)) { echo ' selected '; }
								echo '>'.$val['company'].'</option>';
							}
						}
					?>
					</select>
					 &nbsp; 
           <select name="materialId[]" class="form-control select2" multiple="multiple" data-placeholder="Select a Material" style="max-width: 250px;">
					<option value="">All Materials</option>
					<?php 
						$selected_material = $this->input->post('materialId');
						$materialArr = array();
						if(!empty($materials)){
							foreach($materials as $val){
								$materialArr[$val['id']] = $val['materialId'];
								echo '<option value="'.$val['id'].'"';
								if(!empty($selected_material) && in_array($val['id'], $selected_material)) { echo ' selected '; }
								echo '>'.$val['materialNumber'].' ('.$val['batch'].')</option>';
							}
						}
						
					?>
					</select>
					 &nbsp; 
				<input type="submit" id="submitBtn" class="btn btn-primary pt-cta">
				<input type="submit" value="Download CSV" name="generateCSV" class="btn btn-success pt-cta ml-2" >
			</form>
		</div>                 
    <div class="table-responsive pt-datatbl">
      <table class="table  table-bordered display nowrap" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th style=" width: 0px;">Sr.#</th>
            <th style="">Customer</th>
            <th style="">Material Number</th>
            <th style="">Description</th>
            <th style="">Batch</th>
            <th style="">Exipiration</th> 
            <th style="">Action</th>
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
                <td style=""><?php echo $n;?></td>
                <td style=""><?php echo $key['customer'];?></td> 
                <td style=""><?php echo $key['materialNumber'];?></td> 
                <td style=""><?php echo $key['description'];?></td> 
                <td style=""><?php echo $key['batch'];?></td> 
                <td style="">
					<?php 
						$expDate = $key['expirationDate'];
						echo (!empty($expDate) && $expDate !== '0000-00-00' && strtotime($expDate)) 
							? date('Y-m-d', strtotime($expDate)) 
							: 'N/A'; 
					?>
				</td>
                <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/warehouse/updateMaterials/'.$key['id'];?> ">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                <a class="btn btn-sm btn-danger delete-tr pt-cta" href="<?php echo base_url().'admin/warehouse/deleteMaterials/'.$key['id'];?>"  onclick="return confirm('Are you sure delete this item ?')" >Delete</a>
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
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="<?php echo base_url().'assets/css/select2.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/select2.min.js'; ?>"></script>
<link href="<?php echo base_url().'assets/css/5.3.0bootstrap.min.css'; ?>" rel="stylesheet">
<script src="<?php echo base_url().'assets/js/5.3.0bootstrap.bundle.min.js'; ?>"></script>

<script>
   $(document).ready(function() {
      $('.select2').select2();
   });
</script>
<style>
	.table td, .table th {
		vertical-align: middle !important;
		text-align: center !important;
	}
	.table th {
		font-weight: bold !important;
	}
</style>