<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Driver GPS Location <div class="add_page" style="float: right;">
        <a href="<?php echo base_url('admin/drivers');?>" class="nav-link"><input type="button" name="Back" value="Back" class="btn btn-danger btn-sm"/></a>
                     </div> </div> 
<div class="container-fluid">
		<div class="col-sm-12 mobile_content">
			    <div class="container">
				    <h3> Driver Live Location</h3>
				    <!--form class="form" method="post" action="">
				        <button type="submit" name="gps" value="gps" class="btn btn-success">Get Live GPS Location</button> &nbsp; &nbsp;
				        <a href="" class="btn btn-primary">Reload</a>
				    </form-->
				    
				    <p>&nbsp;</p>
				    
				    <?php //echo '<strong>'.$driver[0]['live_gps'].'</strong>';
				    $gps = explode(',',$driver[0]['live_gps']);
				    
				    if($gps[0] !='' && $gps[1] != '') {
				    ?>
				    
				    <div class="col-sm-12">
						<div id="mapid" style="width: 100%; height:400px;"></div>
						<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
  
						<script>

	var mymap = L.map('mapid').setView([<?php echo $gps[1].', '.$gps[0]; ?>], 10); 
	L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1Ijoidml2ZWt3ZWJ4IiwiYSI6ImNrcWF1MDFwdTBjNW8yb2tkbWs1aWN0bnQifQ.SlS3iaotQTmHsIGUHbm8zQ', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a target="_blank" href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
			'Imagery 哀卻? <a target="_blank" href="https://www.mapbox.com/">Mapbox</a>',
		id: 'mapbox/streets-v11',
		tileSize: 512,
		zoomOffset: -1
	}).addTo(mymap);

	var mapIcon = L.icon({ iconUrl: '<?php echo base_url("assets/images/map-pin1.png");?>' });

  L.marker([<?php echo $gps[1].', '.$gps[0]; ?>],{icon: mapIcon}).addTo(mymap).bindPopup('<b>He was there at <?php echo $gps[2];?></b>'); 
	var popup = L.popup();

</script>
					</div>
				    <?php } else { ?>
				    <p>No last location found.</p>
				    <?php } ?>
				 </div>
			</div>

		</div>	
			
		</div>	
			