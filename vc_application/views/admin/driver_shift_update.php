<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Update Driver Shift <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/driver_shift');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 mobile_content">
			    <div class="container">
				    <h3> Update Driver Shift</h3>
				 	<?php if($this->session->flashdata('item')){ ?>
<div class="alert alert-success">
<h4><?php echo $this->session->flashdata('item'); $this->session->set_flashdata('item',''); ?></h4> 
           </div>
          <?php } ?>
		  
		   <?php
                       
                  if(!empty($driver_shift)){
                  $key = $driver_shift[0];
                  $startDateTime = explode(' ',$key['start_date']);
                  $endDateTime = explode(' ',$key['end_date']);
                    ?>   
						 <?php  echo validation_errors();?>
						 <div class="clearfix"></div>
					<form method="post" action="<?php echo base_url('admin/driver_shift/update/').$this->uri->segment(4);?>" class="row">
					<div class="col-sm-4">	     
						<div class="form-group">
                            <label for="contain"> Driver</label>
                            <select name="driver_id" class="form-control" required>
        						<option value="">Select Driver</option>
        						<?php 
        							if(!empty($drivers)){
        								foreach($drivers as $val){
        									echo '<option value="'.$val['id'].'"';
        									if($key['driver_id']==$val['id']) { echo ' selected '; }
        									echo '>'.$val['dname'].'</option>';
        								}
        							}
        						?>
        					</select>
                        </div>
					</div>
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Start Date</label>
                            <input readonly value="<?php echo $startDateTime[0];?>" name="sdate" type="text" class="form-control datepicker" required>
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> Start Time</label>
                            <select name="stime" class="form-control">
								<option value="00:00:00">00:00:00</option>
								<?php 
								$time = date('Y-m-d').' 00:00:00';
								for($i=0; $i < 47;$i++) {
									$currentTime = date('H:i:s',strtotime("+30 minutes",strtotime($time)));
									echo '<option value="'.$currentTime.'"';
									if($startDateTime[1] == $currentTime) { echo ' selected '; }
									echo '>'.$currentTime.'</option>';
									$time = $currentTime;
								} ?>
							</select>
                        </div>
                    </div> 
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> End Date</label>
                            <input readonly value="<?php echo $endDateTime[0];?>" name="edate" type="text" class="form-control datepicker" required>
                        </div>
                    </div> 
					
					<div class="col-sm-4">    
                        <div class="form-group">
                            <label for="contain"> End Time</label>
                            <select name="etime" class="form-control">
								<option value="00:00:00">00:00:00</option>
								<?php 
								$time = date('Y-m-d').' 00:00:00';
								for($i=0; $i < 47;$i++) {
									$currentTime = date('H:i:s',strtotime("+30 minutes",strtotime($time)));
									echo '<option value="'.$currentTime.'"';
									if($endDateTime[1] == $currentTime) { echo ' selected '; }
									echo '>'.$currentTime.'</option>';
									$time = $currentTime;
								} ?>
							</select>
                        </div>
                    </div> 
					
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Start Latitude</label>
                            <input name="slatitude" value="<?php echo $key['start_latitude'];?>" type="text" class="form-control">
                        </div>
                     </div> 
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Start Longitude</label>
                            <input name="slongitude" value="<?php echo $key['start_longitude'];?>" type="text" class="form-control">
                        </div>
                    </div> 
					
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> End Latitude</label>
                            <input name="elatitude" value="<?php echo $key['end_latitude'];?>" type="text" class="form-control">
                        </div>
                     </div> 
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> End Longitude</label>
                            <input name="elongitude" value="<?php echo $key['end_longitude'];?>" type="text" class="form-control">
                        </div>
                    </div> 
					
					<div class="col-sm-4">	
                        <div class="form-group">
                            <label for="contain"> Status</label>
                            <select name="status" class="form-control" required>
								<option value="">Select Status</option>
								<option <?php if($key['status']=='true') { echo 'selected'; } ?> value="true">Start</option>
								<option <?php if($key['status']=='closed') { echo 'selected'; } ?> value="closed">Closed</option>
							</select>
                        </div>
                    </div> 
 
					<div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" name="save" value="Update" class="btn btn-success"/>
                        </div>
					</div>
					
					<?php if($key['start_latitude'] && $key['start_longitude']) { ?>
					<div class="col-sm-12">
						<div id="mapid" style="width: 100%; height:400px;"></div>
						<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
  
						<script>

	var mymap = L.map('mapid').setView([<?php echo $key['start_latitude'].', '.$key['start_longitude']; ?>], 7); 
	L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1Ijoidml2ZWt3ZWJ4IiwiYSI6ImNrcWF1MDFwdTBjNW8yb2tkbWs1aWN0bnQifQ.SlS3iaotQTmHsIGUHbm8zQ', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a target="_blank" href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
			'Imagery 哀卻? <a target="_blank" href="https://www.mapbox.com/">Mapbox</a>',
		id: 'mapbox/streets-v11',
		tileSize: 512,
		zoomOffset: -1
	}).addTo(mymap);

	var mapIcon = L.icon({ iconUrl: '<?php echo base_url("assets/images/map-pin1.png");?>' });

  L.marker([<?php echo $key['start_latitude'].', '.$key['start_longitude']; ?>],{icon: mapIcon}).addTo(mymap).bindPopup("<b>Start Trip</b>");
  <?php if($key['end_latitude'] && $key['end_longitude']) { ?>
	L.marker([<?php echo $key['end_latitude'].', '.$key['end_longitude']; ?>],{icon: mapIcon}).addTo(mymap).bindPopup("<b>End Trip</b>");
  <?php } ?>
	var popup = L.popup();

</script>
					</div>
					<?php } ?>
					
					</form>
					
				  <?php } ?>
				  
				</div>
			</div>

		</div>	
			
		</div>	
			
			
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true});
    $(".datepicker").on("change", function() {
            var selectedDate = $(this).datepicker("getDate");
            var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
            //console.log("Date in California timezone:", californiaDate);
        });
  });
  </script>