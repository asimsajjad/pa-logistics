<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Invoice</title>
		<style>
			@font-face {
			font-family: 'calibri';
			src: url('<?php echo base_url()?>assets/css/Calibri-Regular.ttf');
			}
			body::before {content: '';position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: rgba(255, 255, 255, 0.9);  z-index: -1;}
			body {font-family: 'calibri';color: #000;font-size: 11px;line-height: 24px;padding: 0;margin: 0;display: flex;justify-content: center;align-items: center;height: 100vh; position: relative;z-index: 1;background-image: url('<?php echo base_url()?>assets/images/jpg-pa-icon.jpg');background-size: 480px; background-position: 70px 210px;background-repeat: no-repeat;}	
			.invoice-box {max-width:690px;margin:auto;position:relative; width: 690px;margin: auto;font-size: 12px;line-height: 24px;font-family: 'calibri';color: #000;}
			
			.invoice-box table {width: 100%;line-height: inherit;text-align: left;border-collapse: collapse;position:relative;z-index:999;}
			.invoice-box table table{width:100%;}
			.invoice-box table td {vertical-align: top;}
			.invoice-box .invoicetb table td {vertical-align: middle;}
			.invoice-box .information table.address-tb{max-width:100%;min-width:100%;width:100%;}
			.invoice-box  table.address-tb td.cls1{width:50%;}
			.invoice-box  table.address-tb td.cls2{width:50%;}
			.invoice-box table td td {padding: 8px;}
			.invoice-box table tr td:nth-child(2) {/*text-align: right;*/}
			.invoice-box table tr.top table td.title {font-size: 45px;line-height: 45px;color: #333;}
			.invoice-box table tr.information table td {padding-bottom: 10px;}
			.invoice-box table tr.heading td {color:#fff;background: #1f3864;border-bottom: 1px solid #ddd;font-weight: bold;}
			.invoice-box table tr.details td {padding-bottom: 20px;}
			.invoice-box table tr.total td:nth-child(2) {border-top: 2px solid #eee;font-weight: bold;}
			.invoice-box table tr.notes  td{padding:0 12px 12px 12px;border:0px solid;}
			.invoice-box table tr.notes td td {padding: 3px;}
			table tr.notes table{width: 100%;}
			.invoicetb{padding:12px 12px 0 8px;}
			.invoice-box table tr .invoicetb td{border: 1px solid #bab7b7;padding:3px;font-size: 12px;}
			.invoicetb table{border: 0px solid #bab7b7;width:100%;}
			.invoicetb tr { page-break-inside: avoid; }
			.invoicetb thead {display: table-header-group;} 
			.invoicetb tbody {display: table-row-group; }
			.footer {color:red;margin-top: 20px;text-align: center;font-weight: bold;font-size: 19px;letter-spacing: 1px;}
			
			/*pdf CSS*/
			table {width: 100%; border-collapse: collapse;}
			.header-table {border:none;}
			.noborder td{border:0px;}
			th, td{ border: 0.5px solid #EAEAEA; padding: 6px; text-align: left; }
			.showborder td, .noborder{ border: 0.5px solid #EAEAEA;}
			th { background-color: #f2f2f2; font-weight: bold; }
			
			.section-title { font-weight: bold; margin-top: 0px; margin-bottom:0px; }
			.invoice-box table td td {padding: 3px;}table.header-table h2 {margin: 0;}
			table.header-table p {margin: 0;}
			table.header-table img { width: 250px !important; }
			td.address-main { width: 26%;line-height: 18px; }
			table.address td + td + td { width: 30%; } 
			table.address tr td { border: none; padding: 1px; line-height: 16px;}
			.termsAndCondition ol li { line-height: 17px; font-size: 11px; }
			.blackbg th{background:#000;color:#fff;}

		</style>
	</head>
	<body>
		<?php
			$plocation = $dlocation = $pphoneNumber = $dphoneNumber = $shippingHours = $receivingHours = '' ;
			$comAddArr = array();
			$disp = $dispatch[0]; 
			$dispatchMeta = json_decode($disp['dispatchMeta'],true);
			$paddress = $disp['paddress'];
			$daddress = $disp['daddress'];
			
			if(!empty($companyAddress)){
				foreach($companyAddress as $val){
					if($disp['paddressid']==$val['id'] && $disp['paddressid'] > 0) {   
						$plocation = $val['company'];
						$shippingHours = $val['shippingHours'];
						$pphoneNumber = $val['phone'];
						$pcity = $val['city'].', '.$val['state'];
						$paddress = $val['address'].' <br>'.$val['city'].', '.$val['state'].' '.$val['zip'];
					}
					if($disp['daddressid']==$val['id'] && $disp['daddressid'] > 0) { 
						$dlocation = $val['company'];
						$receivingHours = $val['receivingHours'];
						$dphoneNumber = $val['phone'];
						$dcity = $val['city'].', '.$val['state'];
						$daddress = $val['address'].' <br>'.$val['city'].', '.$val['state'].' '.$val['zip'];
					}
					$comAddArr[$val['id']] = array($val['company'],$val['city'].', '.$val['state'],$val['address'].' <br>'.$val['city'].', '.$val['state'].' '.$val['zip'], $val['phone'], $val['shippingHours'], $val['receivingHours']);
				}
			}
		// 	echo "<pre>";
		// print_r($comAddArr[939]);	exit;
		?>
		<div class="invoice-box">
			<div class="invoice-box-bg"></div>
			<h2 style="text-align:center;">RATE & LOAD CONFIRMATION</h2>
			<table class="header-table">
				<tr>
					<td style="border:none;">
						<img src="<?php echo base_url()?>assets/images/logo1.png" alt="PA Logistics" style="width: 140px;">
						<!-- <p><strong>MC No.: 956423 | DOT #: 3339378</strong></p> -->
						<p><strong>MC&nbsp;No.:&nbsp;956423&nbsp;|&nbsp;DOT&nbsp;#:&nbsp;3339378</strong></p>
					</td>
					<td style="border:none;  width:8%;"></td>
					<td style="border:none;">
						<table border="1" cellspacing="0" cellpadding="5">
							<tr>
								<td width="35%"><strong>Dispatcher:</strong></td>
								<td width="65%"><?php echo $userinfo[0]['uname']?></td>
								<td width="44%"><strong>Load #</strong></td>
								<td width="56%"><?php echo $disp['invoice']; ?></td>
							</tr>
							<tr>
								<td><strong>Phone #:</strong></td>
								<td><?=$userinfo[0]['phone']?></td>
								<td><strong>Ship Date:</strong></td>
								<td><?php echo date('m-d-Y',strtotime($disp['pudate'])); ?></td>
							</tr>
							<tr>
								<td><strong>Fax #:</strong></td>
								<td></td>
								<td><strong>Today's Date:</strong></td>
								<td><?=date('m-d-Y')?></td>
							</tr>
							<tr>
								<td><strong>Email:</strong></td>
								<td colspan="3"><?= !empty($userinfo[0]['email']) ? $userinfo[0]['email'] . ', ' : '' ?> CC: ops@palogisticsgroup.com</td>
							</tr>
							<tr>
								<td><strong>W/O:</strong></td>
								<td colspan="3"></td>
							</tr>	
							</tr>
						</table>
						
					</td>
				</tr>
			</table>
			
			<div style="border: 0.5px solid #EAEAEA;overflow:hidden;">
			<table class="bb-none noborder">
				<!--tr class="blackbg">
					<th width="27.6%">Carrier</th>
					<th width="17.4%">Phone #</th>
					<th width="15%">Email #</th>
					<th width="13%">Equipment</th>
					<th width="15%">Agreed Amount</th>
					<th width="11%">Load Status</th>
				</tr-->
				<tr class="blackbg">
					<th>Carrier</th>
					<th>Phone&nbsp;#</th>
					<th>Email</th>
					<th colspan="2">Equipment</th>
					<th>Agreed Amount</th>
				</tr>
				<tr class="showborder">
					<td><?=$truckCompany[0]['company'] ?><br> (DOT # <?= $truckCompany[0]['dot'] ?>) </td>
					<td><?=str_replace(' ','&nbsp;',$truckCompany[0]['password'])?></td>
					<td><?=$truckCompany[0]['email']?></td>
					<td colspan="2"><?php if($dispatchMeta['invoicePDF']=='Drayage') { echo $dispatchMeta['invoiceDrayage']; }
					elseif($dispatchMeta['invoicePDF']=='Trucking') { echo $dispatchMeta['invoiceTrucking']; } ?></td>
					<td>$<?php echo $disp['rate']; ?></td> 
				</tr>
				<?php if($dispatchMeta['invoicePDF']=='Drayage'  && !empty($erInformation) && isset($erInformation[0]) ){?>

				<tr class="showborder">
					<td class="" style="padding-bottom: 0px;" colspan="1">
						<strong class="section-title" style="">EMPTY PICKUP INFORMATION </strong> 
					</td>
					<td class="" style="padding-bottom: 0px;" colspan="5">
						<strong class="section-title" style=""> <?=$erInformation[0]['company']?></strong> 
						<?=$erInformation[0]['address']?>
						<?=$erInformation[0]['city'].','?>
						<?=$erInformation[0]['state']?>
						<?=$erInformation[0]['zip']?>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td class="address-main">
						<?php 
							$extraPickup = false;
							if($extraDispatch) {
								foreach($extraDispatch as $info){ 
									if($info['pd_type']=='pickup') {
										$extraPickup = true;
									}
								}
							}
							$extraPickCount = 1; 
							if($extraPickup){ ?>
								<strong class="section-title" style="line-height:17px;display:block;padding:2px;">PICK UP # <?php echo $extraPickCount;  ?></strong><br>
								<strong><?php echo $plocation; ?></strong><br>
								<?php echo $paddress; ?><br>
						<?php }else{ ?>
							<strong class="section-title" style="line-height:17px;display:block;padding:2px;">SHIPPER INFORMATION</strong><br>
						<strong><?php echo $plocation; ?></strong><br>
						<?php echo $paddress; ?><br>
						<?php }
						?>
						
						
					</td>
					<td colspan="2">
						<table  class="address">
							<tr>
								<td width="40%" style="font-weight: bold;">Date:</td>
								<td width="60%"><?php echo date('m-d-Y',strtotime($disp['pudate'])); ?></td>
							</tr>
							<tr><td style="font-weight: bold;">Appointment:</td><td><?php echo $disp['ptime'].' '.$dispatchMeta['appointmentTypeP'];  ?></td></tr>
							<tr><td style="font-weight: bold;">Shipping&nbsp;Hours:</td><td><?=$shippingHours?></td></tr>
							<tr><td style="font-weight: bold;">Commodity:</td><td><?=$dispatchMeta['commodityP']?></td></tr>
							<tr><td style="font-weight: bold;">Quantity:</td><td><?php if($dispatchMeta['quantityP']==''){ echo '-'; } else { echo $dispatchMeta['quantityP']; } ?></td></tr>
							<tr><td style="font-weight: bold;">Weight:</td><td><?php if($dispatchMeta['weightP']==''){ echo '-'; } else { echo $dispatchMeta['weightP']; } ?></td></tr>
						</table>
					</td> 
					<td colspan="3">
						<table  class="address">
						    <?php if($dispatchMeta['invoicePDF']=='Trucking') { ?>
						        <tr><td style="font-weight: bold;">Tracking / PO No.:</td><td><?php if(!stristr($disp['tracking'],'TBA')){ echo $disp['tracking']; } ?></td></tr>
						        <?php 
								$hasTrailerNo = false;
    							if($dispatchMeta['dispatchInfo']) { 
    								foreach($dispatchMeta['dispatchInfo'] as $diVal) {
										if ($diVal[0] =='Trailer No') {
											$hasTrailerNo = true;
										}
    								    if($diVal[0] == 'PO No'){}
    									else { echo '<tr><td style="font-weight: bold;">'.$diVal[0].':</td><td>'.$diVal[1].'</td></tr>'; }
										
    								}
    							}
								if (!$hasTrailerNo){?>
						        <tr><td style="font-weight: bold;">Trailer No.:</td><td><?php if(!stristr($disp['trailer'],'TBA')){ echo $disp['trailer']; } ?></td></tr>
						 <?php }
						 } else { ?>
    							<tr>
    								<td width="40%" style="font-weight: bold;">Tracking / PO No.:</td>
    								<td width="60%"><?php if(!stristr($disp['tracking'],'TBA') && !stristr($disp['trailer'], 'TBD')){ echo $disp['tracking']; } ?></td>
    							</tr>
    							<?php 
    							if($dispatchMeta['dispatchInfo']) { 
    								foreach($dispatchMeta['dispatchInfo'] as $diVal) {
    									echo '<tr><td style="font-weight: bold;">'.$diVal[0].':</td><td>'.$diVal[1].'</td></tr>';
    								}
    							}
    							?>
							<?php } ?>
						</table>
					</td>
				<tr>
				    <td colspan="6" style="padding-top:0px;border-bottom:0.5px solid #EAEAEA;">
				        <table  class="address">
				            <tr>
				                <td width="12%" style="font-weight: bold;">Pickup Notes:</td>
				                <td  width="38%"><?=nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>',$disp['pnotes']))?></td>
				                <td width="11%"  style="font-weight: bold;">Description:</td>
				                <td width="39%" ><?=nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>',$dispatchMeta['metaDescriptionP']))?></td>
				            </tr>
				        </table>
				    </td>
				</tr>
				<?php
				$extraPickCount = 2; 
				if($extraDispatch) {
					foreach($extraDispatch as $info){ 
						if($info['pd_type']=='pickup') {
							if($info['pd_meta'] != '') {
								$pdMeta = json_decode($info['pd_meta'],true);
							} else { $pdMeta = array(); }
						?>
							
            			
							<tr>
					<td class="address-main">
						<strong class="section-title" style="line-height:17px;display:block;padding:2px;">PICK UP # <?php echo $extraPickCount; ?></strong><br><strong><?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][0]; } else { echo $info['pd_location']; }?></strong><br>
						<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][2]; } else { echo $info['pd_address']; }?><br>
					</td>
					<td colspan="2">
						<table  class="address">
							<tr>
								<td width="40%" style="font-weight: bold;">Date:</td>
								<td width="60%"><?php if(!strstr($info['pd_date'],'0000')) { echo date('m-d-Y',strtotime($info['pd_date'])); } ?></td>
							</tr>
							<tr><td style="font-weight: bold;">Appointment:</td><td><?php echo $info['pd_time'].' '.$dispatchMeta['appointmentTypeP'];  ?></td></tr>
							<tr><td style="font-weight: bold;">Shipping&nbsp;Hours:</td><td><?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][4]; }?></td></tr>
						</table>
					</td> 
					<td colspan="3">
						<table  class="address">
						   <tr><td style="font-weight: bold;">Commodity:</td><td><?=$pdMeta['commodityP']?></td></tr>
							<tr><td style="font-weight: bold;">Quantity:</td><td><?php if($pdMeta['quantityP']==''){ echo '-'; } else { echo $pdMeta['quantityP']; } ?></td></tr>
							<tr><td style="font-weight: bold;">Weight:</td><td><?php if($pdMeta['weightP']==''){ echo '-'; } else { echo $pdMeta['weightP']; } ?></td></tr>
						</table>
					</td>
				<tr>
				    <td colspan="6" style="padding-top:0px;border-bottom:0.5px solid #EAEAEA;">
				        <table  class="address">
				            <tr>
				                <td width="12%" style="font-weight: bold;">Pickup Notes:</td>
				                <td  width="38%"><?=nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>',$info['pd_notes']))?></td>
				                <td width="11%"  style="font-weight: bold;">Description:</td>
				                <td width="39%" ><?=nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>',$pdMeta['metaDescriptionP']))?></td>
				            </tr>
				        </table>
				    </td>
				</tr>
							
						<?php
							$extraPickCount++;
						}
					}
				} ?>
				</tr>
				<tr>
					<td class="address-main"> 
						<strong class="section-title" style="line-height:17px;display:block;padding:2px;">
						    <?php if($dispatchMeta['invoicePDF']=='Drayage') { echo strtoupper('Container Drop Off Location'); }
						    else { echo strtoupper('Consignee ');
						        if($extraDispatch) { echo '# 1'; } else { echo strtoupper('Information'); }
						    }
						    ?>
						    </strong><br>
						<strong><?php echo $dlocation;?></strong><br>
						<?php echo $daddress; ?><br> 
					</td>
					<td colspan="2">
						<table  class="address">
							<tr>
								<td width="40%" style="font-weight: bold;">Date:</td>
								<td width="60%"><?php if(!strstr($disp['dodate'],'0000')) { echo date('m-d-Y',strtotime($disp['dodate'])); } ?></td>
							</tr>
							<tr><td style="font-weight: bold;">Appointment #:</td><td><?php echo $disp['dtime'].' '.$dispatchMeta['appointmentTypeD'];  ?></td></tr>
							<tr><td style="font-weight: bold;">Receiving&nbsp;Hours:</td><td><?=$receivingHours?></td></tr>
						</table>
					</td> 
					<td colspan="3">
						<table  class="address">
						    <tr><td style="font-weight: bold;">Commodity:</td><td><?=$dispatchMeta['commodityP']?></td></tr>
							<tr><td style="font-weight: bold;">Quantity:</td><td><?php if($dispatchMeta['quantityD']==''){ echo '-'; } else { echo $dispatchMeta['quantityD']; } ?></td></tr>
							<tr><td style="font-weight: bold;">Weight:</td><td><?php if($dispatchMeta['weightD']==''){ echo '-'; } else { echo $dispatchMeta['weightD']; } ?></td></tr>
						</table>
					</td>
				</tr>
				<tr>
				    <td colspan="6" style="padding-top:0px;border-bottom:0.5px solid #EAEAEA;">
				        <table  class="address">
				            <tr>
				                <td width="15%" style="font-weight: bold;">Drop Off Notes:</td>
				                <td  width="85%"><?=nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>',$disp['dnotes']))?></td>
				            </tr>
				        </table>
				    </td>
				</tr>
				
				<?php
				$extraCount = 2; 
				if($extraDispatch) {
					foreach($extraDispatch as $info){ 
						if($info['pd_type']=='dropoff') {
							if($info['pd_meta'] != '') {
								$pdMeta = json_decode($info['pd_meta'],true);
							} else { $pdMeta = array(); }
							
						?>
							<tr>
								<td class="address-main">
									<strong class="section-title" style="line-height:17px;display:block;padding:2px;">CONSIGNEE # <?php echo $extraCount; ?></strong><br>
									<strong><?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][0]; } else { echo $info['pd_location']; }?></strong><br>
									<?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][2]; } else { echo $info['pd_address']; }?><br>
								</td>
								<td colspan="2">
									<table  class="address">
										<tr>
											<td width="40%" style="font-weight: bold;">Date:</td>
											<td width="60%"><?php if(!strstr($info['pd_date'],'0000')) { echo date('m-d-Y',strtotime($info['pd_date'])); } ?></td>
										</tr>
										<tr><td style="font-weight: bold;">Appointment #:</td><td><?php echo $info['pd_time'].' '.$pdMeta['appointmentType'];  ?></td></tr>
										<tr><td style="font-weight: bold;">Receiving Hours:</td><td><?php if(array_key_exists($info['pd_addressid'],$comAddArr)){ echo $comAddArr[$info['pd_addressid']][5]; }?></td></tr>
									</table>
								</td> 
								<td colspan="3">
									<table  class="address">
									    <tr><td style="font-weight: bold;">Commodity:</td><td><?=$dispatchMeta['commodityP']?></td></tr>
										<tr><td style="font-weight: bold;">Quantity:</td><td><?php if($pdMeta['quantityD']==''){ echo '-'; } else { echo $pdMeta['quantityD']; } ?></td></tr>
										<tr><td style="font-weight: bold;">Weight:</td><td><?php if($pdMeta['weightD']==''){ echo '-'; } else { echo $pdMeta['weightD']; }?></td></tr>
									</table>
								</td>
							</tr>
            				<tr>
            				    <td colspan="6" style="padding-top:0px;border-bottom:0.5px solid #EAEAEA;">
            				        <table  class="address">
            				            <tr>
            				                <td width="15%" style="font-weight: bold;">Drop Off Notes:</td>
            				                <td  width="85%"><?=nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>',$info['pd_notes']))?></td>
            				            </tr>
            				        </table>
            				    </td>
            				</tr>
						<?php
							$extraCount++;
						}
					}
				} ?>
			</table>
			</div>
			
				<div class="termsAndCondition">
					<h3>Carrier Terms and Conditions</h3>
					<ol>
						<li>Bills of Lading (BOLs) must be submitted within 24 hours of delivery. Failure to comply may result in penalties for late submission.</li>
						<li>Late pickups and deliveries, including delays caused by vehicle breakdowns without verifiable proof, will incur a late fee.</li>
						<li>The carrier is fully responsible for the cargo from pickup to delivery, including proper handling, securement, and compliance with all applicable regulations.</li>
						<li>If the carrier picks up damaged equipment and fails to notify the broker in writing of the damage, it will be considered to have occurred while in the carrier's possession, and the carrier will be held liable for the cost of repairs.</li>
						<li>The carrier may not broker, assign, or sub-contract this load to any third party; unauthorized re-brokering will result in non-payment and liability for damages or claims</li>
						<li>Detention, layover, or other accessorial charges require pre-approval and proper documentation to qualify for reimbursement. The carrier must notify the broker 30 minutes before entering into detention.</li>
						<li>If this is a refrigerated shipment, the carrier must ensure that the temperature and instructions are clearly noted on the Bill of Lading (BOL). The reefer unit must be set and maintained to run accordingly throughout transit.</li>
						<li>The carrier must maintain open communication and immediately notify the broker of any delays, issues, or emergencies during transit.</li>
						<li>Tracking is mandatory, and drivers are required to accept and enable tracking.</li>
						<li>All invoices must be sent to: <strong>accounts@palogisticsgroup.com<?php //$userinfo[0]['email']?></strong></li>
					</ol>
					
				</div>
				
				<div class="">
					<?php
						$carrierPay = $disp['rate'];
						$expenseList = [];
						if (!empty($dispatchMeta['carrierExpense'])) {
							foreach ($dispatchMeta['carrierExpense'] as $expense) {
								$label = $expense[0];
								$amount = (float)$expense[1];
								if (stripos($label, 'discount') !== false) {
									$carrierPay += $amount;
								} else {
									$carrierPay -= $amount;
								}
								$expenseList[] = $label . ': $' . number_format($amount, 2);
							}
						}
						?>
						
					<p style="margin: 2px 0;"><strong>Carrier Pay:</strong> Line Haul: $<?php echo number_format($carrierPay, 2); ?>, <?php if (!empty($expenseList)): echo implode(', ', $expenseList); echo ','; endif; ?> <strong>TOTAL: $<?php echo $disp['rate']; ?> USD</strong></p>
					<p style="margin: 25px 0 2px 0;">
						<strong>Accepted By:</strong> ___________________
						<strong>Date:</strong> ___________________
						<strong>Signature:</strong> ____________________
					</p>
					<p style="margin: 25px 0 2px 0;">
						<strong>Driver Name:</strong> ________________
						<strong>Cell #:</strong> ________________
						<strong>Truck #:</strong> __________
						<strong>Trailer:</strong> __________
					</p>
				</div>
		</div>
		<?php // echo 'test';exit;?>
	</body>
</html>
