 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
            <h3>Paysheet </h3>
			  <div class="add_page" style="float: right;">
                   <a class="nav-link" title="Create Section" href="javascript:void();" onclick="window.print();"><input type="button" name="add" value="Print" class="btn btn-success pt-cta">
                   </a>  
                   </div>
				   
            </div>
            
            
            <div class="card-bodys table_style">
                
            <div class="d-block text-center">
                <form class="form form-inline" method="post" action="">
                    <input type="text" readonly placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 120px;" class="form-control datepicker"> &nbsp;
                    <input type="text"  style="width: 120px;" readonly placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker"> &nbsp;
					
                    <select name="unit" class="form-control">
						<option value="">Select Unit</option>
						<?php 
							if(!empty($vehicles)){
								foreach($vehicles as $val){
									echo '<option value="'.$val['id'].'"';
									if($this->input->post('unit')==$val['id']) { echo ' selected '; }
									echo '>'.$val['vname'].' ('.$val['vnumber'].')</option>';
								}
							}
						?>
					</select> &nbsp;
                    <select name="driver" class="form-control">
						<option value="">Select Driver</option>
						<?php 
							if(!empty($drivers)){
								foreach($drivers as $val){
									echo '<option value="'.$val['id'].'"';
									if($this->input->post('driver')==$val['id']) { echo ' selected '; }
									echo '>'.$val['dname'].'</option>';
								}
							}
						?>
					</select>&nbsp;
                    <select name="company" class="form-control" required>
						<option value="">Select Company</option>
						<?php 
							if(!empty($companies)){
								foreach($companies as $val){
									echo '<option value="'.$val['id'].'"';
									if($this->input->post('company')==$val['id']) { echo ' selected '; }
									echo '>'.$val['company'].'</option>';
								}
							}
						?>
					</select>&nbsp;
                    <input type="submit" value="Search" name="search" class="btn btn-success pt-cta">
                </form>
            </div>
              <div class="table-responsive pt-datatbl">
                <table class="table table-bordered display nowrap" id="dataTable1" width="100%" cellspacing="0">
                  <thead>
                    <tr>
						  <th>PU Date</th>
						  <th>Truck #</th>
						  <th>Trailer #</th>
						  <th>Tracking #</th>
						  <th>Pick Up Location</th>
						  <th>Drop Off Location</th> 
						  <th>Delivered</th> 
						  <th>Office Use Only</th>
                    </tr> 
                  </thead>
                 
                  <tbody>
                    
                       <?php

                  if(!empty($dispatch)){
                      $n=1; $rate = $parate = 0;
                  foreach($dispatch as $key) {
					  $rate = $rate + $key['rate'];
					  $parate = $parate + $key['parate'];
                    ?>
                    <tr class="tr-<?php echo $key['id'];?>">
            <td><?php echo date('m-d-Y',strtotime($key['pudate']));?></td>
            <td><?php 
                if(!empty($vehicles)){
                    foreach($vehicles as $val){
                        if($key['vehicle']==$val['id']) { echo $val['vname']; }
                    }
                }
            ?></td> 
            <td><?php echo $key['trailer'];?></td> 
            <td><?php echo $key['tracking'];?></td>
            <td><?php
                if(!empty($locations)){
                    foreach($locations as $val){
                        if($key['plocation']==$val['id']) { echo $val['location']; }
                    }
                }
				if(!empty($cities)){
                    foreach($cities as $val){
                        if($key['pcity']==$val['id']) { echo ' ['.$val['city'].']'; }
                    }
                }
            ?></td> 
            <td><?php
                if(!empty($locations)){
                    foreach($locations as $val){
                        if($key['dlocation']==$val['id']) { echo $val['location']; }
                    }
                }
				
				if(!empty($cities)){
                    foreach($cities as $val){
                        if($key['dcity']==$val['id']) { echo ' ['.$val['city'].']'; }
                    }
                }
                if($key['detention_check']=='yes'){
                    echo '<br> + Detention '.$key['detention'].' Hours';
                }
                if($key['dassist_check']=='yes'){
                    echo '<br> + Dassist '.$key['dassist'];
                }
            ?></td> 
            <td><?php if($key['delivered']=='yes') { echo $key['delivered']; } else { echo 'no'; } ?></td> 
            <td><?php if($key['parate'] > 0) { echo '$'; } echo $key['parate'];?></td>  
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
    $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
    /*$('#dataTable').DataTable({
        "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]]
    });*/
	
  });
  </script>
  <style>
      form.form {margin-bottom:25px;}
	  @media print {
.navbar, .sidebar.navbar-nav, .form.form-inline {
    display: none;
}
.table_style table tr th {
    background: #fff;
    color: #000;
    font-weight: bold;
}
}
  </style>