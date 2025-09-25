	<div class="card">
		<h3 class="pt-page-title pt-title-border">Current Trips</h3>

		<!-- Breadcrumbs-->
		<ol class="breadcrumb px-0 position-relative bg-transparent mb-4 align-items-center">
			<li class="breadcrumb-item">
			<a href="<?php echo base_url('AdminDashboard');?>">Dashboard</a>
			</li>
			<li class="breadcrumb-item active">Overview</li>
			

			<li class="breadcrumb-item1 ml-auto"><a href="<?php echo base_url('AdminDashboard');?>" class="btn btn-success pt-cta">Refresh Location</a></li>
			
		</ol>
		

		<div class="pt-contents">

			<?php 
				//print_r($gps);
				$centerPoint = $popup = '';
				if($gps){
					$i = 1;
					foreach($gps as $val){
						$latLog = explode(',',$val['live_gps']);
						if($i==1){ 
							$centerPoint = $latLog[1].', '.$latLog[0];
						}
						$date = date('F d, Y H:i',strtotime($latLog[2]));
						$popup .= 'var infowindow = new google.maps.InfoWindow();
						var marker, i;
		
						marker = new google.maps.Marker({
							position: new google.maps.LatLng('.$latLog[1].', '.$latLog[0].'),
							map: map,
							icon: image
						});

						google.maps.event.addListener(marker, "click", (function(marker, i) {
							return function() {
							infowindow.setContent("<b>'.$val['dname'].' </b><br>He was here at<br>'.$date.'");
							infowindow.open(map, marker);
							}
						})(marker, i));
						infowindow.setContent("<b>'.$val['dname'].' </b><br>He was here at<br>'.$date.'");
							infowindow.open(map, marker);
						';
						$i++;
					}
				}
			
			?>
			<div class="row">
				<div class="col-sm-12 mb-5">
					<div class="table-responsive pt-tbl-responsive">
						<table class="table">
							<tr>
								<th class="pt-w-100">Truck #</th>
								<th>PU Location</th>
								<th>PU Time</th>
								<th>D Location</th>
								<th>D Time</th>
								<th>Tracking No</th>
								<th>Company</th>
								<th>Driver</th>
								<th>Status</th>
								<th>Driver Status</th>
								<th>Track</th>
							</tr>
						<?php
						if($currentTrip){
							foreach($currentTrip as $val){
								
								if(!stristr($val['delivered'],'yes')) {
								
									$dispatchMeta = json_decode($val['dispatchMeta'],true);
									$cRed = $nextdDate = '';
									if(!strstr($val['pd_date'],'0000') && $val['pd_date']!='') { $nextdDate = $val['pd_date']; }
									elseif(strstr($val['dodate'],'0000')) {} 
									else { $nextdDate = $val['dodate']; } 
									$nextPdate = strtotime("+ 5 days",strtotime($nextdDate));
									if($dispatchMeta['invoiceReady']!='1' && $nextPdate < strtotime(date('Y-m-d')) && $nextdDate != '') { $cRed = ' cRed '; }
											
									echo '<tr>
									<td>'.$val['vname'].' ('.$val['vnumber'].'</td>
									<td><span>'.$val['plocation'].', '.$val['pcity'].'</span></td>
									<td class="pt-nowrap">'.$val['pudate'].'<br>'.$val['ptime'].'</td>
									<td><span>'.$val['dlocation'].', '.$val['dcity'].'</span></td>
									<td class="pt-nowrap">'.$nextdDate.'<br>'.$val['dtime'].'</td>
									<td class="'.$cRed.'"><a href="'.base_url('admin/dispatch/update/'.$val['dispatchid']).'">'.$val['tracking'].'</a></td>
									<td><span>'.$val['company'].'</span></td>
									<td>'.$val['dname'].'</td>
									<td><span>'.$val['status'].'</span></td>
									<td>'.$val['driver_status'].'</td>
									<td><a class="btn btn-sm btn-success pt-cta" href="'.base_url().'admin/driver/gps-location/'.$val['driver'].'"><i class="fas fa-map-marker" title="GPS Location" alt="Edit"></i> GPS</a></td>
									</tr>';
								}
							}
						} else {
							echo '<tr><td colspan="10">No current trip for today.</td></tr>';
						}
						?>
						</table>
					</div>
				</div>
				<div class="col-sm-12 mb-5">
					<h3 class="pt-page-title">Reimbursement</h3>
					<div class="table-responsive pt-tbl-responsive">
						<table class="table">
							<tr>
								<th>Date</th>
								<th>Driver</th>
								<th>Amount</th>
								<th>Truck</th>
								<th>Notes</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
						<?php
						if($reimbursement){
							foreach($reimbursement as $val){ 
								if($val['rembursCheck']=='0') {
									echo '<tr class="rtr-'.$val['id'].'">
									<td>'.$val['fdate'].'</td>
									<td><a href="'.base_url().'admin/reimbursement/update/'.$val['id'].'">'.$val['dname'].'</a></td>
									<td>'.$val['amount'].'</td>
									<td>'.$val['truck'].'</td>
									<td>'.$val['notes'].'</td>
									<td>'.$val['fdate'].'</td>
									<td><a data-cls=".rtr-'.$val['id'].'" class="btn btn-sm btn-success reimbursementBtn pt-cta" href="'.base_url().'admin/reimbursement/checkUpdate/'.$val['id'].'">Reimburse Now</a></td>
									</tr>';
								}
							}
						} else {
							echo '<tr><td colspan="11">No open reimbursement.</td></tr>';
						}
						?>
						</table>
					</div>
				</div>
				<div class="col-sm-6">
					<?php if($centerPoint != '') { ?>
						<div id="mapidss" style="width:100%;height:500px;max-width:1200px;"></div>
						<script src="https://maps.google.com/maps/api/js?key=AIzaSyCtNP18NPVpCB1HR9k8tQ1hQU1-UBKCMLo" type="text/javascript"></script>
						<script type="text/javascript">
			
						var map = new google.maps.Map(document.getElementById('mapidss'), {
							zoom: 8,
							center: new google.maps.LatLng(<?php echo $centerPoint;?>),
							scrollwheel: true,
							mapTypeId: google.maps.MapTypeId.ROADMAP
							});
							
							var image = "https://techacscorp.com/nassau-map/assets/images/community-support.png";
							
							<?php echo $popup; ?>
										
						</script>
						<!--div id="mapid" style="width: 100%; height:400px;"></div>
						<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
						<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script><script>
							<?php echo $centerPoint; ?>  
							L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1Ijoidml2ZWt3ZWJ4IiwiYSI6ImNrcWF1MDFwdTBjNW8yb2tkbWs1aWN0bnQifQ.SlS3iaotQTmHsIGUHbm8zQ', {
								maxZoom: 18,
								attribution: 'Map data &copy; <a target="_blank" href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
								'Imagery 哀卻? <a target="_blank" href="https://www.mapbox.com/">Mapbox</a>',
								id: 'mapbox/streets-v11',
								tileSize: 512,
								zoomOffset: -1
							}).addTo(mymap);
							var mapIcon = L.icon({ iconUrl: '<?php //echo base_url("assets/images/map-pin1.png");?>' });
							<?php echo $popup; ?> 
							var popup = L.popup();
						</script-->
					<?php } else { 
						echo '<p>No data available for map.</p>';
					} ?>
				</div>
			</div>
						
			<div class="row">
				<div class="col-sm-6">
					<h3 class="pt-page-title">Upcoming Dates</h3>
					<?php 
					if($permits){
						foreach($permits as $info){
							echo '<p>'.date('M d, Y',strtotime($info['expDate'])).' '.$info['title'].' (Permits)</p>';
						}
					}
					if($insurance){
						foreach($insurance as $info){
							echo '<p>'.date('M d, Y',strtotime($info['endDate'])).' '.$info['title'].' (Insurance)</p>';
						}
					} 
					if($hireDate){
						foreach($hireDate as $info){
						if($info['status']=='Active'){
							echo '<p>'.date('M d, Y',strtotime($info['sdate'])).' '.$info['dname'].' (Hire Date)</p>';
						}
						}
					}
					if($dob){
						foreach($dob as $info){
						if($info['status']=='Active'){
							echo '<p>'.date('M d, Y',strtotime($info['dob'])).' '.$info['dname'].' (Birthday)</p>';
						}
						}
					}
					if($medicalExpDate){
						foreach($medicalExpDate as $info){
						if($info['status']=='Active'){
							echo '<p>'.date('M d, Y',strtotime($info['medate'])).' '.$info['dname'].' (Medical exp. date)</p>';
						}
						}
					}
					if($licenseExpDate){
						foreach($licenseExpDate as $info){
						if($info['status']=='Active'){
							echo '<p>'.date('M d, Y',strtotime($info['ledate'])).' '.$info['dname'].' (License exp. date)</p>';
						}
						}
					} ?>
				</div>
				<div class="col-sm-6">
					<h3 class="pt-page-title">Upcoming Services</h3>
					<?php 
					if($servicesExpDate){
						foreach($servicesExpDate as $info){
							echo '<p>'.date('M d, Y',strtotime($info['nextServiceDate'])).' '.$info['trailer'].' ('.$info['title'].')</p>';
						}
					} ?>
				</div>
				<div class="col-sm-4 d-none">
					<h3>Drivers</h3>
					<?php 
					/*if($drivers){
						foreach($drivers as $info){
							echo '<p><strong>'.$info['dname'].'</strong><br> 
							Birthday: '.$info['dob'].'<br>
							License Exp Date: '.$info['ledate'].'<br>
							Medical Exp Date: '.$info['medate'].'<br>
							</p>';
						}
					}*/ ?>
				</div>
			</div>
		</div>
	</div>
		
	<script>
	    jQuery(document).ready(function(){
	       jQuery('.reimbursementBtn').click(function(e){
	           e.preventDefault();
	           var url = $(this).attr('href');
	           var trCls = $(this).attr('data-cls');
	    		jQuery.ajax({
	                type: "post",
	                url: url,
	                data: "",
	                success: function(responseData) { 
	    				jQuery(trCls).val('');
	    				jQuery(trCls).remove();
	                }
	            });
	       }) 
	    });
	</script>

	<style>
		.cRed, .cRed a{font-weight:bold;color:red;}
	</style>
