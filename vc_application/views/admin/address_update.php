<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Update Company Address
		<div class="add_page" style="float: right;">
			<a href="<?php echo base_url('admin/company-address');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger"/></a>
		</div> 
	</div>
	<div class="container-fluid">
		<div class="col-xs-10 col-sm-10 col-md-10 mobile_content">
			<div class="container">
				
				<h3> Update Company Address</h3> 
				<?php if($this->session->flashdata('item')){ ?>
					<div class="alert alert-success">
						<h4><?php echo $this->session->flashdata('item');$this->session->set_flashdata('item',''); ?></h4>
					</div>
				<?php } 
					
					if(!empty($company)){
						$key = $company[0];
						
					?>    
					<form method="post" action="<?php echo base_url('admin/address/update/').$this->uri->segment(4);?>">
						
						<?php  echo validation_errors();?>
						
						
						<div class="row">
						    <div class="col-sm-8">
						        <div class="form-group">
        							<label for="contain">Company</label>
							        <input class="form-control" type="text" name="company" value="<?php echo $key['company'] ;?>" required />
        						</div>
						    </div>
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">Email</label>
        							<input class="form-control" type="email" name="email" value="<?php echo $key['email'] ;?>" />
        						</div>
						    </div>
						</div>
						<div class="row">
						    <div class="col-sm-8">
						        <div class="form-group">
        							<label for="contain">Address</label>
        							<input class="form-control" type="text" name="address" value="<?php echo $key['address'] ;?>" required />
        						</div>
						    </div>
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">Phone</label>
        							<input class="form-control" type="tel" name="phone" value="<?php echo $key['phone'] ;?>" />
        						</div>
						    </div>
						</div>
						<div class="row">
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">City</label>
        							<input class="form-control" type="text" name="city" value="<?php echo $key['city'] ;?>" required />
        						</div>
						    </div>
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">State</label>
        							<input class="form-control" type="text" name="state" value="<?php echo $key['state'] ;?>" required />
        						</div>
						    </div>
						    <div class="col-sm-4">
						        <div class="form-group">
        							<label for="contain">Zip Code</label>
        							<input class="form-control" type="text" name="zip" value="<?php echo $key['zip'] ;?>" />
        						</div>
						    </div>
						    
						    <div class="col-sm-6">
						        <div class="form-group">
        							<label for="contain">Shipping Hours</label>
        							<div class="input-group mb-2">
        							    <input class="timeInput form-control" type="text" name="shippingHours" value="<?php echo $key['shippingHours'] ;?>" autocomplete="off" />
        							    <div class="tDropdown"></div>
										<div class="input-group-append">
											<div class="input-group-text timedd" style="width: 32px;padding: 2px;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
										</div>
        							</div>
        						</div>
						    </div>
						    <div class="col-sm-6">
						        <div class="form-group">
        							<label for="contain">Receiving Hours</label>
        							<div class="input-group mb-2">
        							    <input class="timeInput form-control" type="text" name="receivingHours" value="<?php echo $key['receivingHours'] ;?>" autocomplete="off" />
        							    <div class="tDropdown"></div>
										<div class="input-group-append">
											<div class="input-group-text timedd" style="width: 32px;padding: 2px;"><!--?xml version="1.0" ?--><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="34-Time" id="_34-Time"><path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm1,29.95V26H15v3.95A14,14,0,0,1,2.05,17H6V15H2.05A14,14,0,0,1,15,2.05V6h2V2.05A14,14,0,0,1,29.95,15H26v2h3.95A14,14,0,0,1,17,29.95Z"/><path d="M17,9H15v7a1,1,0,0,0,.29.71l5,5,1.41-1.41L17,15.59Z"/></g></svg></div>
										</div>
        							</div>
        						</div>
						    </div>
						    
						</div>
						
						
						
						
						<div class="form-group">
							<input type="submit" name="save" value="Update" class="btn btn-success"/>
						</div>
						
					</form>
					<?php
					}
				?> 
			</div>
		</div>
		
	</div>
	
</div>	

<style>
  .card{overflow:visible;}
  .input-group{position:relative;}
  .tDropdown {position:absolute;width:calc(100% - 30px);max-height:240px;overflow-y:auto;background:white;border:1px solid #ccc;display:none;z-index:1000;top:98%;left:0;}
  .tDropdown div {padding: 5px;cursor: pointer;}
  .tDropdown div:hover {background: #f0f0f0;}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo base_url('assets/js/jquery.inputmask.min.js'); ?>"></script>

<script>
$(document).ready(function () {
    let activeInput = null; // Track currently active input field

    // Generate time options
    const generateTimeOptions = () => {
        let times = [];
        for (let h = 0; h < 24; h++) {
            for (let m = 0; m < 60; m += 15) {
                let hour = h % 12 === 0 ? 12 : h % 12;
                let meridian = h < 12 ? "AM" : "PM";
                times.push(`${hour.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')} ${meridian}`);
            }
        }
        return times;
    };

    // Add times to each tDropdown
    $(".input-group").each(function () {
        let timeOptions = generateTimeOptions();
        let dropdown = $(this).find(".tDropdown");
        timeOptions.forEach(time => {
		dropdown.append('<div>'+time+'</div>');
        });
    });

    // Show dropdown when clicking the .timedd icon
    $(document).on("click", ".timedd", function () {
        let parentGroup = $(this).closest(".input-group");
        activeInput = parentGroup.find(".timeInput"); // Set active input
        let dropdown = parentGroup.find(".tDropdown");

        dropdown.toggle().css({
            //top: activeInput.offset().top + activeInput.outerHeight(),
            //left: activeInput.offset().left,
            //position: "absolute"
        });
    });

    // Select time when clicking an option (Replaces only the second time)
    $(document).on("click", ".tDropdown div", function () {
        let dropdown = $(this).parent("div");
        let selectedTime = $(this).text();

        if (activeInput) {
            let currentValue = activeInput.val().trim();
            let times = currentValue.split(" - ");

            if (times.length === 1 && times[0] !== "") {
                activeInput.val(times[0] + " - " + selectedTime); // Add second time
            } else if (times.length === 2) {
                activeInput.val(times[0] + " - " + selectedTime); // Replace only the second time
            } else {
                activeInput.val(selectedTime); // If empty, insert first time
            }
        }

        dropdown.hide();
    });

    // Hide dropdown if clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".tDropdown, .timedd").length) {
            $(".tDropdown").hide();
        }
    });
});
</script>
		
<script>
	$(document).ready(function() {
        $('input[type="tel"]').inputmask("(999) 999-9999");
    });
</script>	