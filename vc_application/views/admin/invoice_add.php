<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Add Invoice <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/invoice');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			    <div class="container">
			        
			       
					<?php 
					$premadetrip = array('puDate'=>'','invoiceNo'=>'','company'=>'','tracking'=>'','rate'=>'','paRate'=>'','payoutAmount'=>'','week'=>'','invoiceDate'=>'TBD','invoiceType'=>'','expectPayDate'=>'','status'=>'');


					if(!empty($duplicate)) {
						$premadetrip = $duplicate[0];
					} 
			        ?>
				    <h3> Add Invoice</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); ?></h4> 
           </div>
          <?php } 
		  
		  $js_companies = $js_cities = $js_location = '';
		   
			if(!empty($companies)){
				$i = 1;
                foreach($companies as $val){
					if($i > 1) { $js_companies .= ','; }
					$js_companies .= '"'.$val['company'].'"';
					if($premadetrip['company']==$val['id']) { $premadetrip['company'] = $val['company']; } 
					$i++;
                }
            }
			
		  ?>
					<form class="form" method="post" action="<?php echo base_url('admin/invoice/add');?>" enctype="multipart/form-data">
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
				<div class="row">
                	
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Pickup Date</label>
                            <input name="puDate" type="text" class="form-control datepicker" readonly required value="<?php if($this->input->post('puDate')!='') { echo $this->input->post('puDate'); } else { echo $premadetrip['puDate']; } ?>">
                        </div>
                    </div>
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Invoice No #</label>
                            <input name="invoiceNo" type="text" class="form-control" value="<?php if($this->input->post('invoiceNo')!='') { echo $this->input->post('invoiceNo'); } else { echo $premadetrip['invoiceNo']; } ?>">
                        </div>
                    </div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Company</label>
							<input type="text" id="companies" class="form-control" name="company" required value="<?php if($this->input->post('company')!='') { echo $this->input->post('company'); } else { echo $premadetrip['company']; } ?>">
                        </div>
                    </div>
                    
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Tracking #</label>
                            <input name="tracking" type="text" class="form-control" value="<?php if($this->input->post('tracking')!='') { echo $this->input->post('tracking'); } else { echo $premadetrip['tracking']; } ?>">
                        </div>
                    </div>
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Rate</label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
								  <div class="input-group-text">$</div>
								</div>
								<input name="rate" step="0.01" type="number" min="0" class="form-control" value="<?php if($this->input->post('rate')!='') { echo $this->input->post('rate'); } else { echo $premadetrip['rate']; } ?>">
							  </div> 
                        </div>
                    </div>
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">PA Rate</label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
								  <div class="input-group-text">$</div>
								</div>
								<input name="paRate" step="0.01" type="number" min="0" class="form-control" value="<?php if($this->input->post('paRate')!='') { echo $this->input->post('paRate'); } else { echo $premadetrip['paRate']; } ?>">
							  </div> 
                        </div>
                    </div>
					<div class="col-sm-4 hide d-none">
						<div class="form-group">
                            <label for="contain">Payout Amount</label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
								  <div class="input-group-text">$</div>
								</div>
								<input name="payoutAmount" step="0.01" type="number" min="0" class="form-control" value="<?php if($this->input->post('payoutAmount')!='') { echo $this->input->post('payoutAmount'); } else { echo $premadetrip['payoutAmount']; } ?>">
							  </div> 
                        </div>
                    </div>
					<div class="col-sm-4 hide d-none">
						<div class="form-group">
                            <label for="contain">Week</label>
                            <input name="week" type="text" class="form-control" value="<?php if($this->input->post('week')!='') { echo $this->input->post('week'); } else { echo $premadetrip['week']; } ?>">
                        </div>
                    </div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Invoice Date</label>
                            <input name="invoiceDate" type="text" class="form-control datepicker" readonly value="<?php if($this->input->post('invoiceDate')!='') { echo $this->input->post('invoiceDate'); } else { echo $premadetrip['invoiceDate']; } ?>">
                        </div>
                    </div>
                   
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Status</label> 
                            <input name="status" type="text" class="form-control" value="<?php if($this->input->post('status')!='') { echo $this->input->post('status'); } else { echo $premadetrip['status']; } ?>">
                        </div>
                    </div> 
                    
                    <div class="col-sm-4">
						<div class="form-group">
                            <div class="custom-control custom-checkbox my-1 mr-sm-2">
                            <input type="checkbox" class="custom-control-input" id="customControlInline" name="bol" value="Yes" <?php if($this->input->post('bol')=='Yes') { echo ' checked'; } ?>>
                            <label class="custom-control-label" for="customControlInline">BOL</label>
                         </div>
                            <input name="bol_d" type="file" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-4">
						<div class="form-group"> 
                            <div class="custom-control custom-checkbox my-1 mr-sm-2">
                            <input type="checkbox" name="rc" class="custom-control-input" id="customControlInlinerc" value="Yes" <?php if($this->input->post('rc')=='Yes') { echo ' checked'; } ?>>
                            <label class="custom-control-label" for="customControlInlinerc">RC</label>
                         </div>
                            <input name="rc_d" type="file" class="form-control">
                        </div>
                    </div> 
                    <div class="col-sm-4">
						<div class="form-group">
                            <div class="custom-control custom-checkbox my-1 mr-sm-2">
                            <input type="checkbox" name="gd" class="custom-control-input" id="customControlInlinegd" value="Yes" <?php if($this->input->post('gd')=='Yes') { echo ' checked'; } ?>>
                            <label class="custom-control-label" for="customControlInlinegd">$</label>
                         </div>
                            <input name="gd_d" type="file" class="form-control">
                        </div>
                    </div>
					 
					
                    <div class="col-sm-12"> 
                        <div class="form-group">
                            <input type="submit" name="save" value="Add Invoice" class="btn btn-success"/>
                        </div>
                    </div>
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
	
    //$( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
    $('body').on('focus',".datepicker", function(){
        $(this).datepicker({dateFormat: 'yy-mm-dd',      changeMonth: true,
      changeYear: true});
    });
      var companies = [<?php echo $js_companies; ?>];
    $( "#companies" ).autocomplete({ source: companies }); 
	
	
    
  } );
  </script>
  
  <style>
      .custom-control-label::before {width: 20px;height: 20px;}
      .custom-control-label::after {width: 20px;height: 20px;}
	  fieldset{position:relative;}
fieldset .pick-drop-btn{position:absolute;right:15px;top:-34px;}
  </style>
  