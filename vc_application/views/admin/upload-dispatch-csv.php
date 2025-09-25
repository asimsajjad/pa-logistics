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
              Upload Dispatch CSV  
              <div class="add_page" style="float: right;">
                   <a class="nav-link" title="Create Section" href="<?php echo  base_url().'admin/dispatch';?>"><input type="button" name="add" value="Back" class="btn btn-success btn-sm"/>
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
                        <li>Required fields: Vehicle, Driver, Pick Up Date, Pick Up City, Pick Up Company, Drop Off City, Drop Off Company, Tracking, Company</li>
                        <li>Driver and Vehicle must be same as in Driver & Vehicle List csv.</li>
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
                        <a href="<?php echo base_url('admin/dispatch/upload-csv/?dummy=csv');?>" style="margin: 0 0 0 10px;" class="btn btn-primary">Dummy CSV</a> 
                        <a href="<?php echo base_url('admin/dispatch/upload-csv/?driver-vehicle=csv');?>" style="margin: 0 0 0 10px;" class="btn btn-primary">Driver & Vehicle List</a>
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
            url: "<?php echo base_url('admin/dispatch/ajaxedit');?>",
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
            url: "<?php echo base_url('admin/dispatch');?>",
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
            url: "<?php echo base_url('admin/dispatch/ajaxdelete');?>",
            data: form_data,
            success: function(responseData) {
                //alert("data saved")
				$('.status-success-msg').html('Dispatch removed successfully.');
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
