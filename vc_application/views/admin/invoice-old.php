  <style>
      form.form {margin-bottom:25px;}
	  .td-input{display:none;}
	  .fas {cursor: pointer;}
	  .fa {cursor: pointer;font-size: 26px;margin-top: 5px;}
	  a.btn {margin-bottom: 5px;}
  </style>
<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Invoice  
              <div class="add_page" style="float: right;">
                   <a class="nav-link" title="Create Section" href="<?php echo  base_url().'admin/invoice/add';?>"><input type="button" name="add" value="Add New" class="btn btn-success btn-sm"/>
                   </a>  
                   </div>
            </div>
            
            
            <div class="card-body table_style">
                
            <div class="col-sm-12 text-center">
                <form class="form form-inline" method="post" action="">
                    <input type="text" readonly placeholder="Start Date" value="<?php if($this->input->post('sdate')) { echo $this->input->post('sdate'); } ?>" name="sdate" style="width: 120px;" class="form-control datepicker"> &nbsp;
                    <input type="text"  style="width: 120px;" readonly placeholder="End Date" value="<?php if($this->input->post('edate')) { echo $this->input->post('edate'); } ?>" name="edate" class="form-control datepicker"> &nbsp;
					
					
                    <select name="companies" class="form-control">
						<option value="">Company</option>
						<?php 
							if(!empty($companies)){
								foreach($companies as $val){
									echo '<option value="'.$val['id'].'"';
									if($this->input->post('companies')==$val['id']) { echo ' selected '; }
									echo '>'.$val['company'].'</option>';
								}
							}
						?>
					</select> &nbsp;
                    <input type="submit" value="Search" name="search" class="btn btn-success">
                </form>
            </div>
              
			  
			   
		<form class="form hide d-none" method="post" action="" id="editrowform">
		<input type="text" name="did_input" placeholder="ID" id="did_input" value="" required>
		<input type="text" name="rate_input" placeholder="rate" id="rate_input" value="" required>
		<input type="text" name="parate_input" placeholder="pa rate" id="parate_input" value="" required>
		<input type="text" name="trailer_input" placeholder="trailer" id="trailer_input" value="">
		<input type="text" name="tracking_input" placeholder="tracking" id="tracking_input" value="">
		<input type="text" name="invoice_input" placeholder="invoice" id="invoice_input" value="">
		<input type="text" name="bol_input" placeholder="bol" id="bol_input" value="">
		<input type="text" name="rc_input" placeholder="rc" id="rc_input" value="">
		<input type="text" name="gd_input" placeholder="gd" id="gd_input" value="">
		<input type="text" name="status_input" placeholder="status" id="status_input" value="">
		</form>
		
		<div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                          <th>Sr no.</th>
						  <th>PU Date</th>
						  <th>Invoice #</th>
						  <th>Company</th>
						  <th>Tracking #</th>
						  <th>Rate</th>
						  <th>PA Rate</th>
						  <th>Payout Amount</th>
						  <th>Week</th>
						  <th>Inv Date</th>
						  <th>Inv Type</th>
						  <th>BOL</th>
						  <th>RC</th>
						  <th>$</th>
						  <th>Status</th>
						  <th>Expect Date</th> 
                        <th>Action</th>
                    </tr> 
                  </thead>
                 
                  <tbody>
                    
                       <?php

                  if(!empty($invoice)){
                      $n=1; $rate = $parate = 0;
                  foreach($invoice as $key) {
                      if($n < 16) {
					    $rate = $rate + $key['rate'];
					    $parate = $parate + $key['parate'];
                      }
                    ?>
                    <tr class="tr-<?php echo $key['id'];?>">
                        <td><?php echo $n;?></td>
            <td><a href="<?php echo base_url().'admin/invoice/update/'.$key['id'];?>"><?php echo date('m-d-Y',strtotime($key['puDate']));?></a></td>
            <td><?php echo $key['invoiceNo']; ?></td> 
            <td><?php
                if(!empty($companies)){
                    foreach($companies as $val){
                        if($key['company']==$val['id']) { echo $val['company']; }
                    }
                }
            ?></td> 
			<td><?php echo $key['tracking']; ?></td> 
			<td><?php echo $key['rate']; ?></td> 
			<td><?php echo $key['paRate']; ?></td> 
			<td><?php echo $key['payoutAmount']; ?></td> 
			<td><?php echo $key['week']; ?></td> 
			<td><?php if($key['invoiceDate']=='0000-00-00') { echo 'TBD'; } else { echo $key['invoiceDate']; } ?></td> 
			<td><?php echo $key['invoiceType']; ?></td> 
			
			<td bgcolor="<?php if($key['bol']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php echo $key['bol'];?></td> 
            <td bgcolor="<?php if($key['rc']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php echo $key['rc'];?></td> 
            <td bgcolor="<?php if($key['gd']=='') { echo '#f3cdcd'; } else { echo '#73ac4d'; } ?>"><?php echo $key['gd'];?></td> 
            
			<td><?php echo $key['status']; ?></td> 
			<td><?php echo $key['expectPayDate']; ?></td> 
            
			
            <td>
                <a class="btn btn-sm btn-success" href="<?php echo base_url().'admin/invoice/update/'.$key['id'];?>">Edit <i class="fas fa-edit" title="Edit" alt="Edit"></i></a> 
                
				<a class="btn btn-sm btn-danger delete-tr" href="#" data-cls=".tr-<?php echo $key['id'];?>" data-id="<?php echo $key['id'];?>" data-toggle="modal" data-target="#deleteModal">Delete</a>
				
                      </td>
                      </tr> 
		  
                    <?php
                    $n++;
                    }
                    }


                  ?>

                  <tfoot>  
                   <tr>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td><strong>Total</strong></td>
						  <td><strong>$<span class="rateTotal"><?php echo $rate; ?></span></strong></td>
						  <td><strong>$<span class="paRateTotal"><?php echo $parate; ?></span></strong></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
                    </tr>
                    </tfoot>
                  </tbody>
                </table>
              </div>
            </div>
         
          </div>

        </div>
		 
<!-- Modal -->


<div id="deleteModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete Invoice</h4>
      </div>
      <div class="modal-body">
        <form class="form" method="post" action="" id="deleteform">
		<div class="alert alert-success status-success-msg" style="display:none">Please wait deleting....</div>
		<p><strong>Are you sure delete this invoice ?</strong>
		<input type="hidden" name="ajaxdelete" class="form-control" value="true" required>
		<input type="hidden" name="deleteid" id="deleteid-input" value="" required></p>
		<p> 
		<input type="submit" name="cdelete" id="sdelete-input" class="btn btn-danger" value="Delete">
		</p>
		</form>
      </div> 
    </div>

  </div>
</div>


<div id="statusModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" style="color: #000;" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Status</h4>
      </div>
      <div class="modal-body">
        <form class="form" method="post" action="" id="editstatusform">
		<div class="alert alert-success status-success-msg" style="display:none">Please wait updating....</div>
		<p><input type="text" name="statusonly" id="status-input" class="form-control" value="" required>
		<input type="hidden" name="statusid" id="dstatus-input" value="" required></p>
		<p><input type="submit" name="sstatus" id="sstatus-input" class="btn btn-primary" value="Update"></p>
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
    $('html, body').on('keyup','#dataTable_filter input',function(){
        calculateRate();
    });
    $('html, body').on('change','#dataTable_length select',function(){
        calculateRate();
    });
    /*$('html, body').on('click','#dataTable_paginate a',function(){
        calculateRate();alert('yes');
    });*/ 
    $('html, body').on('click','a.paginate_button',function(){
        //calculateRate();
    });
    $('#dataTable').on( 'page.dt', function () {
        beforeCalculateRate();
        setTimeout(function() { calculateRate(); }, 1000);
    });
    setTimeout(function() {
        calculateRate();
    }, 2500);
    
	$('.td-txt .fa-edit').click(function(){
		var tdid = $(this).attr('data-id');
		
		$('.td-txt').show();
		$('.td-input').hide();
		
		$('.td-txt-'+tdid).hide();
		$('.td-input-'+tdid).show();
		
		$('#did_input').val(tdid);
		
		var rate = $('.c_rate_input_'+tdid).val();
		$('#rate_input').val(rate);
		
		var parate = $('.c_parate_input_'+tdid).val();
		$('#parate_input').val(parate);
		
		var trailer = $('.c_trailer_input_'+tdid).val();
		$('#trailer_input').val(trailer);
		
		var tracking = $('.c_tracking_input_'+tdid).val();
		$('#tracking_input').val(tracking);
		
		var invoice = $('.c_invoice_input_'+tdid).val();
		$('#invoice_input').val(invoice);
		
		
		if($('.c_bol_input_'+tdid).prop('checked')) { var bol = 'AK'; }
		else { var bol = ''; }
		$('#bol_input').val(bol);
		
		if($('.c_rc_input_'+tdid).prop('checked')) { var rc = 'AK'; }
		else { var rc = ''; }
		$('#rc_input').val(rc);
		
		if($('.c_gd_input_'+tdid).prop('checked')) { var gd = 'AK'; }
		else { var gd = ''; }
		$('#gd_input').val(gd);
		
		var status = $('.c_status_input_'+tdid).val();
		$('#status_input').val(status);
	});
	$('.current_checkbox').click(function(e){
		var id = jQuery(this).attr('data-id');
		if($(this).prop('checked')) {
			var valu = 'AK'; 
		} else {
			var valu = ''; 
		}
		jQuery(id).val(valu);
	});
	$('.current_input').keyup(function(e){
		//var cls = jQuery(this).attr('data-cls');
		var id = jQuery(this).attr('data-id');
		var valu = jQuery(this).val();
		//jQuery(cls).html(valu);
		jQuery(id).val(valu);
	});
	$('.fa-paper-plane').click(function(e){
		var tdid = jQuery(this).attr('data-id'); 
		
		$('.c_rate_txt_'+tdid).html($('#rate_input').val());
		$('.c_parate_txt_'+tdid).html($('#parate_input').val());
		$('.c_trailer_txt_'+tdid).html($('#trailer_input').val());
		$('.c_tracking_txt_'+tdid).html($('#tracking_input').val());
		$('.c_invoice_txt_'+tdid).html($('#invoice_input').val());
		$('.c_status_txt_'+tdid).html($('#status_input').val());
		
		if($('#bol_input').val()=='AK') { var bol = 'Yes'; }
		else { var bol = ''; }
		$('.c_bol_txt_'+tdid).html(bol);
		$('.c_rc_txt_'+tdid).html($('#rc_input').val());
		$('.c_gd_txt_'+tdid).html($('#gd_input').val());
		
		$('.td-txt').show();
		$('.td-input').hide();
		
		$('#editrowform').submit();
		
		
		//if($('#bol_input').val() == 'AK') { $('.c_rc_input_'+tdid).prop('checked','true'); }
		//else { $('.c_rc_input_'+tdid).prop('checked','false'); }
		//$('#rc_input').val(rc);
	});
	$('#editrowform').submit(function(e){
		e.preventDefault();
		var form_data = $(this).serialize();
		 $.ajax({
            type: "post",
            url: "<?php echo base_url('admin/invoice/ajaxedit');?>",
            data: form_data,
            success: function(responseData) { 
				$('#editrowform input').val('');
            }
        });
	});
	$('.status-edit').click(function(e){
		e.preventDefault();
		var did = $(this).attr('data-id');
		var dstatus = $(this).attr('data-status');
		$('#dstatus-input').val(did);
		$('#status-input').val(dstatus);
	});
	$('#editstatusform').submit(function(e){
		e.preventDefault();
		var form_data = $(this).serialize();
		var did = $('#dstatus-input').val();
		var dstatus = $('#status-input').val();
		$('.status-success-msg').show();
		 $.ajax({
            type: "post",
            url: "<?php echo base_url('admin/invoice');?>",
            data: form_data,
            success: function(responseData) {
                //alert("data saved")
				$('.status-success-msg').html('Status Updated Successfully.');
				$('.status-'+did).html(dstatus);
            }
        });
	});
	
	$('.delete-tr').click(function(e){
		e.preventDefault();
		var did = $(this).attr('data-id');  
		$('#deleteid-input').val(did); 
	});
	$('#deleteform').submit(function(e){
		e.preventDefault();
		var form_data = $(this).serialize();
		var did = $('#deleteid-input').val(); 
		$('.status-success-msg').show();
		 $.ajax({
            type: "post",
            url: "<?php echo base_url('admin/invoice/ajaxdelete');?>",
            data: form_data,
            success: function(responseData) {
                //alert("data saved")
				$('.status-success-msg').html('Outside invoice removed successfully.');
				$('.tr-'+did).html('');
				$('.tr-'+did).remove();
            }
        });
	});
	$('#dataTable').DataTable({
        "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]]
    });
    
    function calculateRate(){
        var parate = 0;  var rate = 0;
        $( ".paRateTxt" ).each(function( index ) {
           var currentPrice = $(this).html();
           parate = parate + parseInt(currentPrice);
        });
        $( ".rateTxt" ).each(function( index ) {
           var currentPrice = $(this).html();
           rate = rate + parseInt(currentPrice);
        });
        $('.paRateTotal').html(parate);
        $('.rateTotal').html(rate);
    }
    function beforeCalculateRate(){
        var parate = 0;  var rate = 0;
        
        $('.paRateTotal').html(parate);
        $('.rateTotal').html(rate);
    }
	
  });
  </script>
