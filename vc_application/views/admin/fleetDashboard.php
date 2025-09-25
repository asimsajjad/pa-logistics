<style>
    body {
      background: #f8f9fa;
      color: #000;
    }

    .card-custom {
      border: none;
      border-radius: 12px;
      background: #ffffff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      cursor: pointer;
    }

    .card-header-custom {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .card-title-icon {
      display: flex;
      align-items: center;
      font-weight: 600;
      color: #000;
    }

    .icon-box {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      border-radius: 6px;
      background-color: #f7f7f7;
      margin-right: 10px;
      font-size: 1.2rem;
      color: #000;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .icon-box:hover {
      background-color: #e0e0e0;
    }

    .view-all-btn {
      font-size: 0.875rem;
      background-color: #f7f7f7;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      color: #000;
      text-decoration: none;
    }

    .status-block {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.6rem 1rem;
      margin-bottom: 10px;
      border-radius: 10px;
      background-color: #f1f5f9;
    }

    .status-title {
      display: flex;
      align-items: center;
      font-weight: 500;
    }

    .badge-count {
      background-color: #f7f7f7;
      color: #000;
      margin-left: 8px;
      font-size: 0.75rem;
      padding: 4px 8px;
      border-radius: 6px;
    }

    .report-table {
      display: none;
      margin-top: 15px;
    }

    .report-table.active {
      display: block;
    }

	#details-section {
		overflow-x: auto; 
		white-space: nowrap; 
	}
  #booking-details-section {
		overflow-x: auto; 
		white-space: nowrap; 
	}
  .shipment-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr); 
    gap: 15px; 
    align-items: center;
  }
  .cRed, .cRed a{font-weight:bold;color:red;}

</style>
<?php
  $shipmentStatusCounts =$shipmentStatusCounts[0];
  $bookingsCounts =$bookingsCounts[0];
  $receivableInvoicesCounts =$receivableInvoicesCounts;
?>

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

<form class="form hide d-none" method="post" action="" id="editrowform">
	<input type="text" name="did_input" placeholder="ID" id="did_input" value="" required>
	<input type="text" name="driver_status_input" placeholder="driver_status" id="driver_status_input" value="">
	<input type="text" name="status_input" placeholder="status" id="status_input" value="">
</form>

<div class="card" style="padding: 18px !important">
  <div class="d-flex align-items-center justify-content-between w-100 border-bottom pd-2 pb-2">
    <h3 class="pt-page-title mb-0 mr-3">Fleet Dashboard</h3>
    <ul class="nav d-flex" id="myTab" role="tablist">
      <li class="nav-item">
        <button class="btn" id="logistics-dashboard" type="button" style="width: 130px; background-color: #6cff6c; border-radius: 99px;height: 50px; cursor: pointer;">Logistics</button>
      </li> &nbsp; 
      <li class="nav-item">
        <button class="btn" id="fleet-dashboard" type="button" style="width: 130px; border-radius: 99px;height: 50px; background-color: #02c302 !important; cursor: pointer;">Fleet</button>
      </li> &nbsp; 
    </ul>
    <div class="d-block text-center">
			<?php 
			if($this->input->post('sdate')) { 
				$sdate = $this->input->post('sdate'); 
			} 
			else {
				$sdate =''; 
			}
			if($this->input->post('edate')) {
				$edate = $this->input->post('edate'); 
			}
			else {
				$edate =''; 
			}
			?> 
			<form class="form form-inline" method="post" action="">	
			&nbsp;	
			<input type="text" required readonly placeholder="Start Date" value="<?php echo $sdate; ?>" name="sdate" style="width: 130px;" class="form-control datepicker"> &nbsp;
				<input type="text" required style="width: 130px;" readonly placeholder="End Date" value="<?php echo $edate; ?>" name="edate" class="form-control datepicker"> &nbsp;	
					 &nbsp; 
				<input type="submit" value="Search" name="search" class="btn btn-success pt-cta">
			</form>
		</div>
    <div class="ml-auto mb-1">
      <!-- <a href="<?php echo base_url('AdminDashboard');?>" class="btn btn-success pt-cta">Refresh Location</a> -->
    </div>
  </div>

	<div class="row mt-2">
    <div class="col-md-12">
      <?php $totalShipments = array_sum($shipmentStatusCounts); ?>
      <div class="card card-custom p-3" style="margin-top: 20px;">
        <div class="shipment-container">
          <div class="status-block clickable-status" data-type="shipment" data-status="Delivery">
            <div class="status-title"><span class="icon-box"><i class="bi bi-truck text-warning"></i></span> Delivery</div>
            <span><?php echo $shipmentStatusCounts['Delivery']; ?></span>
          </div>
          <div class="status-block clickable-status" data-type="shipment" data-status="Pickup">
            <div class="status-title"><span class="icon-box"><i class="bi bi-box-arrow-in-down text-primary"></i></span> Pickup</div>
            <span><?php echo $shipmentStatusCounts['Pickup']; ?></span>
          </div>

          <div class="status-block clickable-status" data-type="shipment" data-status="pendingInvoices">
            <div class="status-title">
              <span class="icon-box"><i class="bi bi-file-earmark-text text-danger"></i></span> Pending Customer's invoices
            </div>
            <span><?php echo $shipmentStatusCounts['PendingInvoices']; ?></span>
          </div>

          <div class="status-block clickable-status" data-type="shipment" data-status="Pending">
            <div class="status-title"><span class="icon-box"><i class="bi bi-clock text-warning"></i></span>Shipments Awaiting Closure</div>
            <span><?php echo $shipmentStatusCounts['Pending']; ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="details" style="display: none; margin-top: -30px;">
		<div class="col-sm-12 mb-5">
      <div id="details-section" class="table-responsive pt-tbl-responsive  p-3" style="overflow-x: auto; white-space: nowrap;"></div>
    </div>
  </div>

</div>

<div class="card" style="padding: 18px !important; margin-top: -30px !important;">
  <div class="row mt-2">	  
    <div class="col-md-4">
      <div class="card card-custom p-3">
        <div class="card-header-custom">
          <div class="card-title-icon">
            <span class="icon-box"><i class="bi bi-bookmark-check text-success"></i></span><?php if($sdate || $edate){?>Searched Bookings<?php } else { ?> Weekly Bookings <?php } ?>
          </div>
        </div>
        <div class="status-block booking-clickable-status" data-type="bookings" data-status="weeklyBookings">
          <div class="status-title"><span class="icon-box"><i class="bi bi-check-circle text-success"></i></span> Bookings</div>
          <span><?php echo $bookingsCounts['weeklyBookings'] ?></span>
        </div>
        <div class="status-block booking-clickable-status" data-type="bookings" data-status="Unassigned">
          <div class="status-title"><span class="icon-box"><i class="bi bi-pencil text-secondary"></i></span> Unassigned</div>
          <span><?php echo $bookingsCounts['unassignedBookings'] ?></span>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card card-custom p-3">
        <div class="card-header-custom text-center">
          <div class="card-title-icon">
            <span class="icon-box"><i class="bi bi-receipt-cutoff text-primary"></i></span><?php if($sdate || $edate){?>Searched Receivable Invoices<?php } else { ?>Total Receivable Invoices <?php } ?>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <div style="width: 125px; height: 125px;">
            <canvas id="receivableInvoiceDonutChart"></canvas>
          </div>
          <div class="ml-3 text-left">
            <div class="booking-clickable-status" data-type="receivableInvoices" data-status="Received" style="cursor: pointer;">
              <small><span style="color: #4CAF50;">&#9632; Received</span>: <?php echo '$'.number_format($receivableInvoicesCounts['receivedAmt'],2); echo ' (' .number_format($receivableInvoicesCounts['received']). ')';?></small>
            </div>
            <div class="booking-clickable-status" data-type="receivableInvoices" data-status="Receivable" style="cursor: pointer;">
              <small><span style="color: #F44336;">&#9632; Receivable</span>: <?php echo '$'.number_format($receivableInvoicesCounts['notReceivedAmt'],2); echo ' ('.number_format($receivableInvoicesCounts['notReceived']).')'; ?></small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-custom p-3">
        <div class="card-header-custom text-center">
          <div class="card-title-icon">
            <span class="icon-box"><i class="bi bi-cash-coin text-warning"></i></span>
            <?php if($sdate || $edate){ ?>Searched Reimbursements<?php } else { ?>Total Reimbursements<?php } ?>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <div style="width: 125px; height: 125px;">
            <canvas id="reimbursementsDonutChart"></canvas>
          </div>
          <div class="ml-3 text-left">
            <div class="booking-clickable-status" data-type="reimbursements" data-status="Reimbursed" style="cursor: pointer;">
              <small><span style="color: #4CAF50;">&#9632;  Reimbursed</span>: <?php echo '$'.number_format($reimbursementCounts['reimbursedAmt'],2); echo ' (' .number_format($reimbursementCounts['reimbursedCount']). ')';?></small>
            </div>
            <div class="booking-clickable-status" data-type="reimbursements" data-status="Reimbursable" style="cursor: pointer;">
              <small><span style="color: #ffc107;">&#9632; Reimbursable</span>: <?php echo '$'.number_format($reimbursementCounts['reimbursableAmt'],2); echo ' ('.number_format($reimbursementCounts['reimbursableCount']).')'; ?></small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="booking-details" style="display: none; margin-top: -30px;">
		<div class="col-sm-12 mb-5">
      <div id="booking-details-section" class="table-responsive pt-tbl-responsive  p-3" style="overflow-x: auto; white-space: nowrap;"></div>
    </div>
  </div>
<div>
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

<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<!-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<link href="<?php echo base_url('assets/sweet_alert/sweetalert2.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/sweet_alert/sweetalert2@11.js'); ?>"></script>
<script>
   $(document).ready(function() {
		$( ".datepicker" ).datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			autoclose: true
		});
		$(".datepicker").on("change", function() {
      var selectedDate = $(this).datepicker("getDate");
      var californiaDate = selectedDate.toLocaleString("en-US", { timeZone: "America/Los_Angeles" });
    });
  });
	$('#details-table').DataTable();
	document.addEventListener("DOMContentLoaded", function () {
    let lastClickedButton = null;
    const rows = document.querySelectorAll(".clickable-status");
    rows.forEach(row => {
      row.addEventListener("click", function () {
        const type = this.dataset.type;
        const status = this.dataset.status;
        const detailsElement = document.getElementById("details");
        if (lastClickedButton === this) {
          detailsElement.style.display = detailsElement.style.display === "none" ? "block" : "none"; 
        } else {
          detailsElement.style.display = "block";
          fetchDetails(type, status); 
        }
        lastClickedButton = this;
        // fetchDetails(type, status);
      });
    });
    const bookingRows = document.querySelectorAll(".booking-clickable-status");
    bookingRows.forEach(row => {
      row.addEventListener("click", function () {
        const type = this.dataset.type;
        const status = this.dataset.status;
        const detailsElement = document.getElementById("booking-details");
        if (lastClickedButton === this) {
          detailsElement.style.display = detailsElement.style.display === "none" ? "block" : "none"; 
        } else {
          detailsElement.style.display = "block";
          fetchDetails(type, status); 
        }
        lastClickedButton = this;
        // fetchDetails(type, status);
      });
    });
  });

  const baseUrl = '<?php echo base_url(); ?>';
  const shipmentStatusOptions = <?php echo json_encode($shipmentStatus); ?>;

  function fetchDetails(type, status) {
    const sdate = document.querySelector('input[name="sdate"]')?.value || '';
    const edate = document.querySelector('input[name="edate"]')?.value || '';
    if (type === "shipment") {
      document.getElementById("details").style.display = "block";
      document.getElementById("details-section").innerHTML = `
        <div style="text-align: center; padding: 40px;">
          <img src="${baseUrl}assets/images/Spin-loader.gif" alt="Loading..." style="width: 100px;" />
        </div>
      `;
    }
    if (type === "bookings" || type === "receivableInvoices" || type === "reimbursements") {
      document.getElementById("booking-details").style.display = "block";
      document.getElementById("booking-details-section").innerHTML = `
        <div style="text-align: center; padding: 40px;">
          <img src="${baseUrl}assets/images/Spin-loader.gif" alt="Loading..." style="width: 100px;" />
        </div>
      `;
    }
    let weeklyBookingsDetails = '';
    let receivedDetails ='';
    let receivableDetails='';
    if((sdate || edate)){
       weeklyBookingsDetails = "Searched Bookings Details";
       receivedDetails = "Searched Received Invoices Details";
       receivableDetails = "Searched Receivable Invoices Details";
    }else{
       weeklyBookingsDetails=  "Weekly Bookings Details";
       receivedDetails = "Total Received Invoices Details";
       receivableDetails = "Total Receivable Invoices Details";
    }
    const detailsTextMap = {
      "shipment-Delivery": "Delivery Shipments Details",
      "shipment-Pickup": "Pickup Shipments Details",
      "shipment-Pending": "Shipments Awaiting Closure Details",
      "shipment-pendingInvoices": "Pending Invoices Details",
      "bookings-weeklyBookings": weeklyBookingsDetails,
      "bookings-Unassigned": "Unassigned Bookings Details",
      "receivableInvoices-Received": receivedDetails,
      "receivableInvoices-Receivable": receivableDetails,
      "reimbursements-Reimbursed": "Reimbursement Details",
      "reimbursements-Reimbursable": "Reimburseable Details"
    };

    const key = `${type}-${status}`;
    const detailsText = detailsTextMap[key] || "Details";
    

    $.ajax({
      url: baseUrl + "FleetDashboard/getDetailsData",
      method: "POST",
      data: { type, status, sdate, edate },
      dataType: "json",
      success: function (records) {
        // console.log(records);
        if (type === "shipment" && status === "Pending") {
          generatePendingShipmentTable("Truck #", "PU Location", "PU Time", "D Location","D Time", "Tracking No", "Company", "Driver", "Status", "Driver Status", "Track",detailsText, records);
        } else if (type === "shipment" && status === "pendingInvoices") {
          generatePendingInvoicesTable("PU Date & Time", "PU Info", "Del Date & Time", "Del Info", "Company", "Tracking #", "Invoice #", "Shipment Status", "Missing Pendency", detailsText, records);
        } else if (type === "shipment") {
          generateTable(
            status === "Delivery" ? "Delivery Date" : "Pickup Date", "Truck #", "Driver", status === "Delivery" ? "Drop Off City" : "Pick Up City", "Tracking #", "Invoice #",  "Shipment Notes", "Shipment Status",detailsText,status, records);
        } else if (type === "bookings") {
          generateBookingsTable("PU Date & Time", "PU Info", "Del Date & Time", "Del Info", "Truck #", "Company",  "Tracking #", "Invoice #", "Status", "pickup", detailsText, records);
        } else if (type === "receivableInvoices") {
          generateReceivableInvoiceTable("Company", "Invoice", "Delivery Date", "Invoice Date", "Carrier Rate", "Invoice Amt", detailsText, records);
        } else if (type === "reimbursements") {
          generateReimbursementsTable("Date", "Driver", "Amount", "Truck", "Notes", "Action", detailsText, records);
        } 
      },
      error: function (xhr) {
        console.error("Failed to fetch details", xhr.responseText);
      }
    });
  }

  function generateTable(col1, col2, col3, col4, col5, col6, col7, col8, detailsText, status, records) {
    let html = `
    <h4 class="mt-4">${detailsText}</h4>
    <table id="details-table" class="display nowrap" style="table-layout: auto; width: 100%;">
      <thead>
        <tr>
           <th style="width: 50px;">Sr. #</th>
          <th style="width: 150px;">${col1}</th>
          <th style="width: 150px;">${col2}</th>
          <th style="width: 150px;">${col3}</th>
          <th style="width: 150px;">${col4}</th>
          <th style="width: 150px;">${col5 || ""}</th>
          <th style="width: 150px;">${col6 || ""}</th>
          <th style="width: 150px;">${col7 || ""}</th>
          <th style="width: 150px;">${col8}</th>
        </tr>
      </thead>
      <tbody>`;
      const statusColors = {
        'Driver On Site': '#1e90ff',
        'Driver on Tracking': '#0073e6',
        'Pending': '#ffa500',
        'Shipment at Risk': '#ff4500',
        'Shipment Delayed': '#ff6347',
        'Shipment Delivered': '#32cd32',
        'Shipment in Transit': '#3096a0',
        'Shipment On Hold': '#ffc107',
        'Shipment Picked up': '#228b22',
        'Shipment Scheduled': '#6a5acd'
      };
        records.forEach((record, index) => {
          let dispatchCities = '';
          let dispatchDateTime = '';
          if (Array.isArray(record.dispatchInfo)) {
            const filteredInfo = record.dispatchInfo.filter(info => {
              if (status.toLowerCase() === 'pickup') {
                return info.pd_type === 'pickup';
              } else if (status.toLowerCase() === 'delivery') {
                return info.pd_type === 'dropoff';
              }
              return false;
            });

            dispatchCities = filteredInfo.map(info => `[${info.pd_city_name}]`).join('<br>');
            dispatchDateTime = filteredInfo .map(info => {
            const time = info.pd_time;
            if (time.includes('-')) {
              // Time range: add <br> after @
              return `${info.pd_date} <strong>@</strong><br><strong>${time}</strong>`;
            } else {
              // Single time: no <br> after @
              return `${info.pd_date} <strong>@ ${time}</strong>`;
            }
          })
          .join('<br>');

          }
          const color = statusColors[record.driver_status] || ''; 
        let dropdownStyle = color ? `border: 2px solid ${color}; background-color: ${color}; color:white; font-weight: bold;` : '';

        let optionsHtml = '';
          let statusExistsInOptions = shipmentStatusOptions.some(opt => opt.title === record.driver_status);

          if (!statusExistsInOptions) {
            optionsHtml += `<option value="${record.driver_status}" style="display:none" selected>${record.driver_status}</option>`;
          }

          optionsHtml += shipmentStatusOptions.map(opt => 
            `<option value="${opt.title}" ${opt.title === record.driver_status ? 'selected' : ''}>${opt.title}</option>`
          ).join('');

          let lastUpdateInfoHtml = '';
          if (Array.isArray(record.lastUpdateInfo) && record.lastUpdateInfo.length > 0) {
            const lastUpdate = record.lastUpdateInfo[0];
            const lastUpdateDate = new Date(lastUpdate.rDate); 
            const formattedDate = lastUpdateDate.toLocaleDateString('en-US', {
              month: '2-digit', 
              day: '2-digit',   
              year: '2-digit'   
            });

            const formattedTime = lastUpdateDate.toLocaleTimeString('en-US', {
              hour: '2-digit',
              minute: '2-digit',
              hour12: true
            }).replace(':00', ''); 
            lastUpdateInfoHtml = `Last updated: <strong>${lastUpdate.uname}</strong> ${formattedDate} @ ${formattedTime}`;
          } else {
            lastUpdateInfoHtml = '';
          }
           let time = record.time;
          let timeHtml = '';

          if (time.includes('-')) {
            timeHtml = `<strong>@</strong><br><strong>${time}</strong>`;
          } else {
            timeHtml = `<strong>@ ${time}</strong>`;
          }
           const invoiceStyle = record.overdue_status ? 'color: red; font-weight: bold;' : '';

          html += `<tr class="">
            <td style="text-align: center; vertical-align: middle; min-width: 50px;">${index + 1}</td>
           <td style="min-width: 200px;">
              ${record.date} ${timeHtml}<br>
              ${dispatchDateTime}
            </td>
            <td style="min-width: 200px;">${record.vname} (${record.vnumber})</td>
            <td style="min-width: 200px;">${record.driver}</td>
            <td style="min-width: 180px;">${record.city}<br>${dispatchCities}</td>
            <td style="min-width: 170px;"><a class="dispatch-tracking-${record.dispatchid}" href="${baseUrl}admin/dispatch/update/${record.dispatchid}">${record.tracking}</a></td> 
            <td style="min-width: 130px;"><a class="dispatch-invoice-${record.dispatchid}"  href="${baseUrl}admin/dispatch/update/${record.dispatchid}" style="${invoiceStyle}">${record.invoice}</a></td> 
            <td style="min-width: 250px;">
              <span class="td-txt td-txt-${record.dispatchid}">
                <span class="c_status_txt_${record.dispatchid}">${record.status}</span> &nbsp; 
                <i class="fas fa-edit" title="Edit" data-id="${record.dispatchid}" alt="Edit" style="cursor:pointer"></i>
              </span>
              <span class="td-input td-input-${record.dispatchid}" style="display: none;">
                <input type="text" class="c_status_input_${record.dispatchid} current_input" value="${record.status}">
                <i class="fa fa-paper-plane" data-id="${record.dispatchid}" aria-hidden="true" style="cursor:pointer"></i>
              </span>
            </td>
             <td style="min-width: 350px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <select name="driver_status" class="form-control c_driver_status_input_${record.dispatchid}" 
                  style="width:auto; ${dropdownStyle}" disabled>
                  ${optionsHtml}
                </select>
                <span style="font-style: italic; font-size: 14px;">${lastUpdateInfoHtml}</span>
              </div>
            </td>
          </tr>`;
        });
    html += `</tbody></table>`;
    document.getElementById("details-section").innerHTML = html;
    $('#details-table').DataTable({
        paging: true,
        searching: true,
        ordering: false,
        info: true,
        lengthMenu: [ [15, 30, -1], [15, 30, "All"] ],
        autoWidth: false
      });
  }
  $(document).on('click', '.fa-edit', function () {
    var tdid = $(this).attr('data-id');
		$('.td-txt').show();
		$('.td-input').hide();
		$('.td-txt-'+tdid).hide();
    $('.td-input-'+tdid).show();
    $('.c_driver_status_input_' + tdid).prop('disabled', false);
		
    $('#did_input').val(tdid);
    var driver_status = $('.c_driver_status_input_'+tdid).val();
		$('#driver_status_input').val(driver_status);
		var status = $('.c_status_input_'+tdid).val();
		$('#status_input').val(status);
			
		var $scrollable = $('.table-responsive');
    $scrollable.animate({scrollLeft: $scrollable[0].scrollWidth - $scrollable.width()}, 800); 
  });

  $(document).on('click', '.fa-paper-plane', function () {
    var tdid = $(this).attr('data-id');
    var newStatus = $('.c_status_input_' + tdid).val();
    var newDriverStatus = $('.c_driver_status_input_' + tdid).val();

    $('#status_input').val(newStatus);
    $('.c_status_txt_' + tdid).html(newStatus);
    $('#driver_status_input').val(newDriverStatus);
    $('#did_input').val(tdid);
    $('.td-txt').show();
    $('.td-input').hide();  
    $('.c_driver_status_input_' + tdid).prop('disabled', true);

    const dropdown = $('.c_driver_status_input_' + tdid);
    let borderStyle = '';
    switch (newDriverStatus) {
      case 'Driver On Site':
        borderStyle = '2px solid #1e90ff'; break;
      case 'Driver on Tracking':
        borderStyle = '2px solid #0073e6'; break;
      case 'Pending':
        borderStyle = '2px solid #ffa500'; break;
      case 'Shipment at Risk':
        borderStyle = '2px solid #ff4500'; break;
      case 'Shipment Delayed':
        borderStyle = '2px solid #ff6347'; break;
      case 'Shipment Delivered':
        borderStyle = '2px solid #32cd32'; break;
      case 'Shipment in Transit':
        borderStyle = '2px solid #3096a0'; break;
      case 'Shipment On Hold':
        borderStyle = '2px solid #ffc107'; break;
      case 'Shipment Picked up':
        borderStyle = '2px solid #228b22'; break;
      case 'Shipment Scheduled':
        borderStyle = '2px solid #6a5acd'; break;
      default:
        borderStyle = '';
    }
    if (borderStyle) {
      const textColor = borderStyle.split(' ')[2];
      dropdown.css({
        'border': borderStyle,
        'background-color': textColor,
        'color': 'white',
        'font-weight': 'bold'
      });
    } else {
      dropdown.css({
        'border': '',
        'background-color': 'white',
        'color': '',
        'font-weight': ''
      });
    }
    
    $('#editrowform').submit();
  });

  $('#editrowform').submit(function(e){
  e.preventDefault();
  var form_data = $(this).serialize();
  var tdid = $('#did_input').val(); 
  $.ajax({
    type: "post",
    url: baseUrl + "FleetDashboard/shipementStatuAndNotesEdit",
    data: form_data,
    success: function(responseData) {
      if (responseData.trim() === 'done') {
        $('#editrowform input').val('');
        $('.c_driver_status_input_' + tdid).prop('disabled', true);

        Swal.fire({
          icon: 'success',
          title: 'Updated!',
          text: 'Shipment Notes and Shipment Status updated successfully.',
          showConfirmButton: false,
          timer: 2000
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Something went wrong!',
        });
      }
    }
  });
});
 
  function generatePendingInvoicesTable(col1, col2, col3, col4, col5, col6, col7, col8, col9, detailsText, records) {
    let html = `
    <h4 class="mt-4">${detailsText}</h4>
    <table id="details-table" class="display nowrap" style="table-layout: auto; width: 100%;">
      <thead>
        <tr>
          <th>Sr. #</th>
          <th>${col1}</th>
          <th>${col2}</th>
          <th>${col3}</th>
          <th>${col4}</th>
          <th>${col5}</th>
          <th>${col6}</th>
          <th>${col7}</th>
          <th>${col8}</th>
          <th>${col9}</th>
        </tr>
      </thead>
      <tbody>`;
      records.forEach((record, index) => {
        let time = record.time;
        let timeHtml = '';
        if (time.includes('-')) {
          timeHtml = `<strong>@</strong><br><strong>${time}</strong>`;
        } else {
          timeHtml = `<strong>@ ${time}</strong>`;
        }

        let dtime = record.dtime;
        let dtimeHtml = '';
        if (dtime.includes('-')) {
          dtimeHtml = `<strong>@</strong><br><strong>${dtime}</strong>`;
        } else {
          dtimeHtml = `<strong>@ ${dtime}</strong>`;
        }
        let missingPendancy = '';
        if (record.bol_status && record.rc_status) {
          missingPendancy = `${record.bol_status}, ${record.rc_status}`;
        } else if (record.bol_status) {
          missingPendancy = record.bol_status;
        } else if (record.rc_status) {
          missingPendancy = record.rc_status;
        } else {
          missingPendancy = 'No pendency missing';
        }
        html += `<tr>
          <td style="min-width: 50px;">${index + 1}</td>
           <td style="min-width: 200px;">
              ${record.date} ${timeHtml}
          </td>
          <td style="min-width: 180px;">${record.paddress}<br>${record.city}</td>
          <td style="min-width: 200px;">
            ${record.dodate} ${dtimeHtml}<br>
          </td>
          <td style="min-width: 180px;">${record.daddress}<br>${record.dcity}<br></td>
          <td style="min-width: 250px;">${record.company}</td>
          <td style="min-width: 170px;">${record.tracking}</td>
          <td style="min-width: 130px;"><a href="${baseUrl}admin/dispatch/update/${record.dispatchid}">${record.invoice}</a></td> 
          <td style="min-width: 250px;">${record.driver_status}</td>
          <td style="min-width: 250px;">${missingPendancy}</td>
        </tr>`;
      });
    html += `</tbody></table>`;
    document.getElementById("details-section").innerHTML = html;
    $('#details-table').DataTable({
      paging: true,
      searching: true,
      ordering: false,
      info: true,
      lengthMenu: [ [15, 30, -1], [15, 30, "All"] ]
    });
  }

  function generatePendingShipmentTable(col1, col2, col3, col4, col5, col6, col7, col8, col9, col10, col11, detailsText, records) {
    let html = `
    <h4 class="mt-4">${detailsText}</h4>
    <table id="details-table" class="display nowrap" style="table-layout: auto; width: 100%;">
      <thead>
        <tr>
          <th>Sr. #</th>
          <th>${col1}</th>
          <th>${col2}</th>
          <th>${col3}</th>
          <th>${col4}</th>
          <th>${col5}</th>
          <th>${col6}</th>
          <th>${col7}</th>
          <th>${col8}</th>
          <th>${col9}</th>
          <th>${col10}</th>
	    		<th>${col11}</th>
        </tr>
      </thead>
      <tbody>`;
    	records.forEach((record, index) => {
        let cRed = ""; 
        let nextdDate = ""; 
        if (record.pd_date && !record.pd_date.includes("0000")) {
            nextdDate = record.pd_date;
        } else if (record.dodate && !record.dodate.includes("0000")) {
            nextdDate = record.dodate;
        }

        let nextPdate = nextdDate ? new Date(new Date(nextdDate).getTime() + 5 * 24 * 60 * 60 * 1000) : null;
        // let nextPdate = new Date(new Date(nextdDate).getTime() + 5 * 24 * 60 * 60 * 1000); 
        let today = new Date();
        // if (record.dispatchMeta.invoiceReady !== "1" && nextPdate < today && nextdDate !== "") {
        //     cRed = "cRed"; 
        // }
        if (record.dispatchMeta && record.dispatchMeta.invoiceReady !== "1" && nextPdate && nextPdate < new Date()) {
            cRed = "cRed"; 
        }
        html += `<tr>
          <td>${index + 1}</td> 
          <td>${record.vname} (${record.vnumber})</td>
          <td>${record.plocation}, ${record.pcity}</td>
          <td>${record.pudate}<br>${record.ptime}</td>
          <td>${record.dlocation}, ${record.dcity}</td>
          <td>${nextdDate}<br>${record.dtime}</td>
          <td class="${cRed}"><a href="${baseUrl}admin/dispatch/update/${record.dispatchid}">${record.tracking}</a></td> 
          <td>${record.company}</td>
          <td>${record.dname}</td>
          <td>${record.status}</td>
          <td>${record.driver_status}</td>
          <td><a class="btn btn-sm btn-success pt-cta" href="admin/driver/gps-location/${record.driver}">
            <i class="fas fa-map-marker" title="GPS Location" alt="Edit"></i> GPS</a>
          </td>
        </tr>`;
      });
    html += `</tbody></table>`;
    document.getElementById("details-section").innerHTML = html;
    $('#details-table').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [ [15, 30, -1], [15, 30, "All"] ]
      });
}

  function generateBookingsTable(col1, col2, col3, col4, col5, col6, col7, col8, col9, status, detailsText, records) {
    let html = `
    <h4 class="mt-4">${detailsText}</h4>
    <table id="booking-details-table" class="display nowrap" style="table-layout: auto; width: 100%;">
      <thead>
        <tr>
           <th style="width: 50px;">Sr. #</th>
          <th style="width: 150px;">${col1}</th>
          <th style="width: 150px;">${col2}</th>
          <th style="width: 150px;">${col3}</th>
          <th style="width: 150px;">${col4}</th>
          <th style="width: 150px;">${col5}</th>
          <th style="width: 150px;">${col6}</th>
          <th style="width: 150px;">${col7 || ""}</th>
          <th style="width: 150px;">${col8 || ""}</th>
          <th style="width: 150px;">${col9 || ""}</th>
        </tr>
      </thead>
      <tbody>`;
      records.forEach((record, index) => {
        let dispatchCities = '';
        let dispatchDateTime = '';
        let dispatchDCities = '';
        let dispatchDDateTime = '';
        if (Array.isArray(record.pdispatchInfo)) {
          const filteredInfo = record.pdispatchInfo.filter(info => {
            if (status.toLowerCase() === 'pickup') {
              return info.pd_type === 'pickup';
            } 
            return false;
          });
          dispatchCities = filteredInfo.map(info => `${info.pd_paddress}<br>[${info.pd_city_name}]`).join('<br>');  
          dispatchDateTime = filteredInfo
            .map(info => {
              const time = info.pd_time;
              if (time.includes('-')) {
                return `${info.pd_date} <strong>@</strong><br><strong>${time}</strong>`;
              } else {
                return `${info.pd_date} <strong>@ ${time}</strong>`;
              }
          })
          .join('<br>');
        }
        if (Array.isArray(record.ddispatchInfo)) {
          const filtereddInfo = record.ddispatchInfo.filter(info => true); 
          dispatchDCities = filtereddInfo.map(info => `${info.pd_paddress}<br>[${info.pd_city_name}]`).join('<br>');  
          dispatchDDateTime = filtereddInfo
            .map(info => {
              const time = info.pd_time;
              if (time.includes('-')) {
                // Time range: add <br> after @
                return `${info.pd_date} <strong>@</strong><br><strong>${time}</strong>`;
              } else {
                // Single time: no <br> after @
                return `${info.pd_date} <strong>@ ${time}</strong>`;
              }
          })
          .join('<br>');
        }
        let time = record.time;
        let timeHtml = '';

        if (time.includes('-')) {
          timeHtml = `<strong>@</strong><br><strong>${time}</strong>`;
        } else {
          timeHtml = `<strong>@ ${time}</strong>`;
        }

        let dtime = record.dtime;
        let dtimeHtml = '';

        if (dtime.includes('-')) {
          dtimeHtml = `<strong>@</strong><br><strong>${dtime}</strong>`;
        } else {
          dtimeHtml = `<strong>@ ${dtime}</strong>`;
        }

        html += `<tr>
          <td style="min-width: 50px;">${index + 1}</td>
          <td style="min-width: 200px;">
            ${record.date} ${timeHtml}<br>
            ${dispatchDateTime}
          </td>
          <td style="min-width: 180px;">${record.paddress}<br>${record.city}<br>${dispatchCities}</td>
          <td style="min-width: 200px;">
            ${record.dodate} ${dtimeHtml}<br>
            ${dispatchDDateTime}
          </td>
          <td style="min-width: 180px;">${record.daddress}<br>${record.dcity}<br>${dispatchDCities}</td>
          <td style="min-width: 200px;">${record.vname} (${record.vnumber})</td>
          <td style="min-width: 250px;">${record.company}</td>
          <td style="min-width: 170px;">${record.tracking}</td>
          <td style="min-width: 130px;"><a href="${baseUrl}admin/dispatch/update/${record.dispatchid}">${record.invoice}</a></td> 
          <td style="min-width: 250px;">${record.status}</td>
        </tr>`;
      });

    html += `</tbody></table>`;
    document.getElementById("booking-details-section").innerHTML = html;
    $('#booking-details-table').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [ [15, 30, -1], [15, 30, "All"] ]
      });
  }

  function generateReceivableInvoiceTable(col1, col2, col3, col4, col5, col6, detailsText, records) {
    let html = `
    <h4 class="mt-4">${detailsText}</h4>
    <table id="booking-details-table" class="display nowrap" style="table-layout: auto; width: 100%;">
      <thead>
        <tr>
          <th style="width: 50px;">Sr. #</th>
          <th style="width: 150px;">${col1}</th>
          <th style="width: 150px;">${col2}</th>
          <th style="width: 150px;">${col3}</th>
          <th style="width: 150px;">${col4}</th>
          <th style="width: 150px;">${col5}</th>
          <th style="width: 150px;">${col6}</th>
        </tr>
      </thead>
      <tbody>`;
      records.forEach((record, index) => {
        const dodateObj = new Date(record.dodate);
        const formatteddodate = ("0" + (dodateObj.getMonth() + 1)).slice(-2) + "-" + ("0" + dodateObj.getDate()).slice(-2) + "-" + dodateObj.getFullYear();
        const invoiceDateObj = new Date(record.invoiceDate);
        const formattedInvoiceDate = ("0" + (invoiceDateObj.getMonth() + 1)).slice(-2) + "-" + ("0" + invoiceDateObj.getDate()).slice(-2) + "-" + invoiceDateObj.getFullYear();

        const formatAmount = (val) => (parseFloat(val) || 0).toLocaleString('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });

        html += `<tr>
          <td style="min-width: 50px;">${index + 1}</td>
          <td style="min-width: 250px;">${record.company}</td>
          <td style="min-width: 130px;"><a href="${baseUrl}admin/dispatch/update/${record.dispatchid}">${record.invoice}</a></td> 
          <td style="min-width: 130px;">${formatteddodate}</td>
          <td style="min-width: 130px;">${formattedInvoiceDate}</td>
          <td style="min-width: 100px;">$${formatAmount(record.rate)}</td> 
          <td style="min-width: 100px;">$${formatAmount(record.parate)}</td>
        </tr>`;
      });
    html += `</tbody></table>`;
    document.getElementById("booking-details-section").innerHTML = html;
    $('#booking-details-table').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [ [15, 30, -1], [15, 30, "All"] ]
      });
  }

  function generateReimbursementsTable(col1, col2, col3, col4, col5, col6, detailsText, records) {
    const baseUrl = "<?= base_url() ?>";
    let html = `
    <h4 class="mt-4">${detailsText}</h4>
    <table id="booking-details-table" class="display nowrap" style="table-layout: auto; width: 100%;">
      <thead>
        <tr>
          <th style="width: 50px;">Sr. #</th>
          <th style="width: 150px;">${col1}</th>
          <th style="width: 150px;">${col2}</th>
          <th style="width: 150px;">${col3}</th>
          <th style="width: 150px;">${col4}</th>
          <th style="width: 150px;">${col5}</th>`;
          if (detailsText === 'Reimburseable Details') {
            html += `<th style="width: 150px;">${col6}</th>`;
          }

          html += `</tr>
              </thead>
              <tbody>`;
      records.forEach((record, index) => {
        const fdateObj = new Date(record.fdate);
        const formattedfdate = ("0" + (fdateObj.getMonth() + 1)).slice(-2) + "-" + ("0" + fdateObj.getDate()).slice(-2) + "-" + fdateObj.getFullYear();

         const formatReimbAmount = (val) => (parseFloat(val) || 0).toLocaleString('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
        
        html += `<tr>
          <td style="min-width: 50px;">${index + 1}</td>
          <td style="min-width: 130px;">${formattedfdate}</td>
          <td style="min-width: 130px;"><a href="${baseUrl}admin/reimbursement/update/${record.id}">${record.dname}</a></td> 
          <td style="min-width: 130px;">$${formatReimbAmount(record.amount)}</td> 
          <td style="min-width: 130px;">${record.truck}</td>
          <td style="min-width: 100px;">${record.notes}</td>`; 
          if (detailsText === 'Reimburseable Details') {
            html += `<td>
              <a href="#" data-id="${record.id}" class="btn btn-sm btn-success reimbursementBtn pt-cta">Reimburse Now</a>
            </td>`;
          }
        html += `</tr>`; 
     });
    html += `</tbody></table>`;
    document.getElementById("booking-details-section").innerHTML = html;
    $('#booking-details-table').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [ [15, 30, -1], [15, 30, "All"] ]
      });
  }

  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }
  document.getElementById("fleet-dashboard").addEventListener("click", function() {
    window.location.href = "<?php echo base_url('FleetDashboard'); ?>";
  });
  document.getElementById("logistics-dashboard").addEventListener("click", function() {
    window.location.href = "<?php echo base_url('AdminDashboard'); ?>";
  });
  document.addEventListener("DOMContentLoaded", function () {
    const receivableInvoiceCanvas = document.getElementById('receivableInvoiceDonutChart');
    const receivableInvoice = receivableInvoiceCanvas.getContext('2d');
    const receivableInvoiceChart = new Chart(receivableInvoice, {
      type: 'doughnut',
      data: {
        labels: ['Received', 'Receivable'],
        datasets: [{
          data: [
            <?php echo $receivableInvoicesCounts['received']; ?>,
            <?php echo $receivableInvoicesCounts['notReceived']; ?>
          ],
          backgroundColor: ['#4CAF50', '#F44336'],
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: {
            display: false
          }
        },
        onClick: function (event, elements) {
          if (elements.length > 0) {
            const chartElement = elements[0];
            const label = this.data.labels[chartElement.index];
            document.querySelector(`.booking-clickable-status[data-type="receivableInvoices"][data-status="${label}"]`)?.click();
          }
        }
      }
    });
  });
  document.addEventListener("DOMContentLoaded", function () {
    const reimbursementsCanvas = document.getElementById('reimbursementsDonutChart');
    const reimbursements = reimbursementsCanvas.getContext('2d');
    const reimbursementsChart = new Chart(reimbursements, {
      type: 'doughnut',
      data: {
        labels: ['Reimbursed', 'Reimbursable'],
        datasets: [{
          data: [
            <?php echo $reimbursementCounts['reimbursedCount']; ?>,
            <?php echo $reimbursementCounts['reimbursableCount']; ?>
          ],
          backgroundColor: ['#4CAF50', '#ffc107'],
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: {
            display: false
          }
        },
        onClick: function (event, elements) {
          if (elements.length > 0) {
            const chartElement = elements[0];
            const label = this.data.labels[chartElement.index];
            document.querySelector(`.booking-clickable-status[data-type="reimbursements"][data-status="${label}"]`)?.click();
          }
        }
      }
    });
  });
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.reimbursementBtn');
    if (btn) {
      e.preventDefault();

      const id = btn.getAttribute('data-id');
      console.log(id); 

      $.ajax({
        url: "<?php echo base_url('Comancontroler/reimbursementCheckboxUpdate/'); ?>" + id,
        type: "POST",
        dataType: "json",
        success: function (response) {
          if (response.success) {
            const row = btn.closest('tr');
            if (row) row.remove();

            Swal.fire({
              title: 'Success!',
              text: response.message,
              icon: 'success'
            });
          } else {
            Swal.fire({
              title: 'Error!',
              text: response.message || 'Something went wrong.',
              icon: 'error'
            });
          }
        },
        error: function (xhr, status, error) {
          Swal.fire({
            title: 'AJAX Error',
            text: 'Status: ' + status + ', Error: ' + error,
            icon: 'error'
          });
        }
      });
    }
  });

</script>
<style>
  #details-table thead th {
    width: 0px !important; 
  } 
  #booking-details-table th,
  #booking-details-table td {
    white-space: normal;
    word-wrap: break-word;
    text-align: left;
    vertical-align: middle;
  }
  #details-table th,
  #details-table td {
    white-space: normal;
    word-wrap: break-word;
    text-align: left;
    vertical-align: middle;
  }
  #booking-details-table th:nth-child(7),
  #booking-details-table td:nth-child(7) {
    width: auto; 
  }

</style>