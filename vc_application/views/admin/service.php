 <!-- DataTables Example -->
<div class="card mb-3">
    <div class="pt-card-header pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
      <h3 class="m-0">Service</h3>
      <div class="add_page" style="float: right;">
           <a class="nav-link p-0" title="Create Section" href="<?php echo  base_url().'admin/service/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success pt-cta"/>
           </a>  </div></div>
    <div class="card-bodys table_style">
        
        
    <div class="">
        <form class="form form-inline" method="post" action="">
            <input type="text" readonly placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 150px;" class="form-control datepicker"> &nbsp;
            <input type="text"  style="width: 150px;" readonly placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker"> &nbsp;
            <select name="service" class="form-control">
		<option value="">Select Service</option>
		<?php 
			if(!empty($preServices)){
				foreach($preServices as $val){
					echo '<option value="'.$val['id'].'"';
					if($this->input->post('service')==$val['id']) { echo ' selected '; }
					echo '>'.$val['title'].'</option>';
				}
			}
		?>
	</select> &nbsp;
            <select name="equipment" class="form-control">
		<option value="">Select Equipment</option>
		<?php 
			if(!empty($equipment)){
				foreach($equipment as $val){
					echo '<option value="'.$val['id'].'"';
					if($this->input->post('equipment')==$val['id']) { echo ' selected '; }
					echo '>'.$val['trailer'].'</option>';
				}
			}
		?>
	</select>&nbsp;
            <input type="submit" value="Search" name="search" class="btn btn-success pt-cta">
        </form>
    </div>
      <p>&nbsp;</p>
      
      <div class="table-responsive pt-tbl-responsive">
        <table class="table table-bordereds" width="100%" cellspacing="0">
          <tbody>
            <tr>
		  <th>Equipment</th>
		  <th>Repair</th> 
		  <th>Date</th> 
		  <th>Vendor</th>
		  <th>Coast</th> 
		  <th>Next Date</th>  
                <th>Action</th>
            </tr>
                       
          
               <?php
          if(!empty($service)){
              $n=1;
          foreach($service as $key) {
            ?> 
        <tr>         
    <td><a href="<?php echo base_url().'admin/service/update/'.$key['id'];?>"><?php echo $key['equipment'];?></a></td>  
    <td><?php echo $key['repair'];?></td>  
    <td><?php echo $key['serviceDate'];?></td>  
    <td><?php echo $key['vendor'];?></td>  
    <td><?php echo $key['coast'];?></td>  
    <td><?php echo $key['nextServiceDate'];?></td>  
    
    <td><a class="btn btn-sm btn-success pt-cta" href="<?php echo base_url().'admin/service/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
              <a style="color:#ff0000;" class="ml-2" href="<?php echo base_url().'admin/service/delete/'.$key['id'];?>" onclick="return confirm('Are you sure delete this ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></a></td>
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
  