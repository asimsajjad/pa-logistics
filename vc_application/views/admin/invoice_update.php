<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Invoice <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/invoice');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			    <div class="container">
			        
				    <h3> Update Invoice</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); ?></h4> 
           </div>
          <?php } 
		  $invoice = $invoices[0];
		  $js_companies = $js_cities = $js_location = $company = '';
		   
			if(!empty($companies)){
				$i = 1;
                foreach($companies as $val){
					if($i > 1) { $js_companies .= ','; }
					$js_companies .= '"'.$val['company'].'"';
					if($invoice['company']==$val['id']) { $company = $val['company']; } 
					$i++;
                }
            }
			
		  ?>
					<form class="form" method="post" action="<?php echo base_url('admin/invoice/update/'.$this->uri->segment(4));?>" enctype="multipart/form-data">
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
				<div class="row">
                	
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Pickup Date</label>
                            <input name="puDate" readonly type="text" class="form-control datepicker" required value="<?=$invoice['puDate']?>">
                        </div>
                    </div>
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Invoice No #</label>
                            <input name="invoiceNo" type="text" class="form-control" value="<?=$invoice['invoiceNo']?>">
                        </div>
                    </div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Company</label>
							<input type="text" id="companies" class="form-control" name="company" required value="<?=$company?>">
                        </div>
                    </div>
                    
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Tracking #</label>
                            <input name="tracking" type="text" class="form-control" value="<?=$invoice['tracking']?>">
                        </div>
                    </div>
                    <div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Rate</label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
								  <div class="input-group-text">$</div>
								</div>
								<input name="rate" step="0.01" type="number" min="0" class="form-control" value="<?=$invoice['rate']?>">
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
								<input name="paRate" step="0.01" type="number" min="0" class="form-control" value="<?=$invoice['paRate']?>">
							  </div> 
                        </div>
                    </div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Payout Amount</label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
								  <div class="input-group-text">$</div>
								</div>
								<input name="payoutAmount" step="0.01" type="number" min="0" class="form-control" value="<?=$invoice['payoutAmount']?>">
							  </div> 
                        </div>
                    </div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Week</label>
                            <input name="week" readonly type="text" class="form-control" value="<?=$invoice['week']?>">
                        </div>
                    </div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Invoice Date</label>
                            <input name="invoiceDate" type="text" class="form-control datepicker" value="<?=$invoice['invoiceDate']?>">
                        </div>
                    </div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Invoice Type</label>
                            <input name="invoiceType" readonly type="text" class="form-control" value="<?=$invoice['invoiceType']?>">
                        </div>
                    </div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Expect Pay Date</label>
                            <input name="expectPayDate" type="text" class="form-control datepicker" value="<?=$invoice['expectPayDate']?>">
                        </div>
                    </div>
                   
					<div class="col-sm-4">
						<div class="form-group">
                            <label for="contain">Status</label> 
                            <input name="status" type="text" class="form-control" value="<?=$invoice['status']?>">
                        </div>
                    </div> 
                    
                    <div class="col-sm-12">
                     <div class="row">
                      <div class="col-sm-4">
						<div class="form-group">
							<div class="custom-control custom-checkbox my-1 mr-sm-2">
                            <input type="checkbox" class="custom-control-input" id="customControlInline" name="bol" value="Yes" <?php if($invoice['bol']=='Yes') { echo ' checked'; } ?>>
                            <label class="custom-control-label" for="customControlInline">BOL</label>
                         </div>
                            <input name="bol_d" type="file" class="form-control">
                        </div>
                       </div>
                       <div class="col-sm-8">
					   <label for="contain">&nbsp;</label><br>
						<?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='bol') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/invoice/removefile/bol/'.$doc['id'].'/'.$invoice['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/invoice/bol/').''.$doc['fileurl'].'?id='.rand(10,99).'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?>
                       </div>
                     </div>
                    </div>
					<div class="col-sm-12">
                     <div class="row">
                      <div class="col-sm-4">
						<div class="form-group"> 
							<div class="custom-control custom-checkbox my-1 mr-sm-2">
                                <input type="checkbox" name="rc" class="custom-control-input" id="customControlInlinerc" value="Yes" <?php if($invoice['rc']=='Yes') { echo ' checked'; } ?>>
                                <label class="custom-control-label" for="customControlInlinerc">RC</label>
                             </div>
                            <input name="rc_d" type="file" class="form-control">
                        </div>
                       </div>
                       <div class="col-sm-8">
					   <label for="contain">&nbsp;</label><br>
					   <?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='rc') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/invoice/removefile/rc/'.$doc['id'].'/'.$invoice['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/invoice/rc/').''.$doc['fileurl'].'?id='.rand(10,99).'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?>
                       </div>
                     </div>
                    </div>
                    
					<div class="col-sm-12">
                     <div class="row">
                      <div class="col-sm-4">
						<div class="form-group"> 
                            <div class="custom-control custom-checkbox my-1 mr-sm-2">
                            <input type="checkbox" name="gd" class="custom-control-input" id="customControlInlinegd" value="Yes" <?php if($invoice['gd']=='Yes') { echo ' checked'; } ?>>
                            <label class="custom-control-label" for="customControlInlinegd">$</label>
                         </div>
                            <input name="gd_d" type="file" class="form-control">
                        </div>
                       </div>
                       <div class="col-sm-8">
					   <label for="contain">&nbsp;</label><br>
						<?php if(!empty($documents)) { 
								foreach($documents as $doc) {
									if($doc['type']=='gd') { 
										echo '<span class="doc-file"><a href="'.base_url().'admin/invoice/removefile/gd/'.$doc['id'].'/'.$invoice['id'].'" class="remove-file" onclick="return confirm(\'Are you sure delete this file ?\')">X</a><a target="_blank" href="'.base_url('assets/invoice/gd/').''.$doc['fileurl'].'?id='.rand(10,99).'"><i class="far fa-file" style="font-size: 26px;"></i><span>'.$doc['fileurl'].'</span></a></span> &nbsp; '; 
									}
								}
							}
						?>
					
                       </div>
                     </div>
                    </div>
					 
					
                    <div class="col-sm-12"> 
                        <div class="form-group">
                            <input type="submit" name="save" value="Update Invoice" class="btn btn-success"/>
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
        $(this).datepicker({dateFormat: 'yy-mm-dd'});
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
  