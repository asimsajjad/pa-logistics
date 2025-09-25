<?php
if($invoice) { 
$info = $invoice[0];
$dispatchMeta = json_decode($info['dispatchMeta'],true);
$invoicePDF = $cExpense = '';
$expense = array();
if($expName){
    for($e=0;count($expName)>$e;$e++){
        if(array_key_exists($expName[$e],$expense)){
			$expense[$expName[$e]]['price'] = $expense[$expName[$e]]['price'] + $expPrice[$e];
			$expense[$expName[$e]]['unit'][] = $expPrice[$e];
		} else {
			$expense[$expName[$e]] = array('price'=>$expPrice[$e],'unit'=>array($expPrice[$e]));
		}
    }
    if($childTrailer){
        foreach($childTrailer as $val){ 
        	$childDispMetaArr = json_decode($val['dispatchMeta'],true);
        	if($childDispMetaArr){
        		if($childDispMetaArr['expense']){
        			$cExpense = 'true';
        		}
        	}
        }
    }
} else {
  if($childTrailer){
    foreach($childTrailer as $val){ 
    	$childDispMetaArr = json_decode($val['dispatchMeta'],true);
    	if($childDispMetaArr){
    		if($childDispMetaArr['expense']){
    			foreach($childDispMetaArr['expense'] as $cVal) {
    				$cExpense = 'true';
    				if(array_key_exists($cVal[0],$expense)){
    					$expense[$cVal[0]]['price'] = $expense[$cVal[0]]['price'] + $cVal[1];
    					$expense[$cVal[0]]['unit'][] = $cVal[1];
    				} else {
    					$expense[$cVal[0]] = array('price'=>$cVal[1],'unit'=>array($cVal[1]));
    				}
    			}
    		}
    	}
    }
  }
    if($dispatchMeta['expense']) { 
    	foreach($dispatchMeta['expense'] as $expVal) {
    		if(array_key_exists($expVal[0],$expense)){
    			$expense[$expVal[0]]['price'] = $expense[$expVal[0]]['price'] + $expVal[1];
    			$expense[$expVal[0]]['unit'][] = $expVal[1];
    		} else {
    			$expense[$expVal[0]] = array('price'=>$expVal[1],'unit'=>array($expVal[1]));
    		}
    	}
    }
}

if($dispatchMeta['invoicePDF'] == 'Trucking') { $invoicePDF = 'Trucking'; }
?>
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
body {font-family: 'calibri';color: #000;font-size: 12px;line-height: 24px;padding: 0;margin: 0;display: flex;justify-content: center;align-items: center;height: 100vh; position: relative;z-index: 1;background-image: url('<?php echo base_url()?>assets/images/jpg-pa-icon.jpg');background-size: 480px; background-position: 70px 210px;background-repeat: no-repeat;}	
.invoice-box {position:relative; width: 1000px;margin: auto;font-size: 12px;line-height: 24px;font-family: 'calibri';color: #000;}

.invoice-box table {width: 1000px;line-height: inherit;text-align: left;border-collapse: collapse;position:relative;z-index:999;}
.invoice-box table td {vertical-align: top;}
.invoice-box .invoicetb table td {vertical-align: middle;}
.invoice-box table td td {padding: 8px;}
.invoice-box table tr td:nth-child(2) {/*text-align: right;*/}
.invoice-box table tr.top table td.title {font-size: 45px;line-height: 45px;color: #333;}
.invoice-box table tr.information table td {padding-bottom: 10px;}
.invoice-box table tr.heading td {color:#fff;background: #1f3864;border-bottom: 1px solid #ddd;font-weight: bold;}
.invoice-box table tr.details td {padding-bottom: 20px;}
.invoice-box table tr.total td:nth-child(2) {border-top: 2px solid #eee;font-weight: bold;}
.invoice-box table tr.notes  td{padding:0 12px 12px 12px; border:0px solid;}
.invoice-box table tr.notes td td {padding: 3px;}
.invoice-box table tr.notes td {font-size: 12px;color: #666;}
table tr.notes table{margin-bottom:5px;}
.invoicetb{padding:12px 12px 0 12px;}
.invoice-box table tr .invoicetb td{border: 1px solid #bab7b7;padding:3px;font-size: 12px;}
.invoicetb table{border: 0px solid #bab7b7;}
.footer {color:red;margin-top: 20px;text-align: center;font-weight: bold;font-size: 19px;letter-spacing: 1px;}
	</style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-box-bg"></div>
        <table cellpadding="0" cellspacing="0">
           <?php 
           if($invoiceNotes != ''){ $info['invoiceNotes'] = $invoiceNotes; }
           if($tracking != ''){ $info['tracking'] = $tracking; }
           ?>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td width="65%">
                                <img src="<?php echo base_url()?>assets/images/jpg-llc2.jpg" style="width:350px;"><br><br>
                                438 N Orinda Ct<br>
                                Tracy CA, 95391<br>
                                (925) 430-5355<br>
                                Accounts: Accounts@patransportca.com<br>
                                Fleet Manager: Akash@patransportca.com
                            </td>
                            <td width="35%" align="left">
                                <h3 style="display:inline;font-size:30"><br>INVOICE</h3><br><br>
								<strong>Invoice No.:</strong> <?=$info['invoice']?><br>
                                <strong>Invoice Date:</strong> <?php if($invoiceDate != '') { echo date('m-d-Y',strtotime($invoiceDate)); }
                                elseif($info['invoiceDate'] != '0000-00-00') { echo date('m-d-Y',strtotime($info['invoiceDate'])); } ?><br><br>
								<img src="<?php echo base_url()?>assets/images/barcode.jpg" style="width:100%; max-width:150px;">
							</td>
                        </tr>
						<tr>
                            <td>
                                <strong>Bill To:<br>
                                <?=$info['ccompany']?></strong><br>
                                <?=str_replace('--','<br>',$info['caddress'])?>
                            </td>
                            <td align="left">
                                <strong>Shipping Contact:</strong><br>
                                <?php 
                                if($contactPerson != '') { echo ''.$contactPerson.'<br>'; } 
                                elseif($info['contactPerson'] != '') { echo ''.$info['contactPerson'].'<br>'; } 
                                if($cdepartment != '') { echo ''.$cdepartment.'<br>'; } 
                                elseif($info['cdepartment'] != '') { echo ''.$info['cdepartment'].'<br>'; } 
                                if($cemail != '') { echo '(E) '.$cemail.'<br>'; } 
                                elseif($info['cemail'] != '') { echo '(E) '.$info['cemail'].'<br>'; } 
                                if($cphone != '') { echo '(O) '.$cphone.'<br>'; } 
                                elseif($info['cphone'] != '') { echo '(O) '.$info['cphone'].'<br>'; } 
                                ?>
                            </td>
                        </tr>
						<tr>
                            <td>
                                <strong>Dispatch Information</strong><br>
                                From:  <?php 
                                if($pickup != '') { echo $pickup; }
                                else { echo $info['pplocation'].' ['.$info['ppcity'].']'; }
                                echo '<br>'; ?>
                                To: 
                                <?php 
                                if($dropoff == '') { $dropoff = $info['ddlocation'].' ['.$info['ddcity'].']'; }
                                if($extraDispatch) { 
                                    $d = 1;
                                    echo '<strong>DO # '.$d.': </strong>'.$dropoff;
                                    if(is_array($dropoffExtra)){
                                        foreach($dropoffExtra as $ex){
                                            $d++;
                                            echo '<br><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; DO # '.$d.': </strong>'.$ex; 
                                        }
                                    } else {
                                        foreach($extraDispatch as $ex){
                                            $d++;
                                            echo '<br><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; DO # '.$d.': </strong>'.$ex['ppd_location'].' ['.$ex['ppd_city'].']'; 
                                        }
                                    }
                                } else {
                                    echo $dropoff;
                                }
                                ?>
                            </td>
                            <td align="left">
                                
								<?php 
								//if($invoicePDF == 'Trucking'){
								    echo '<strong>'.$trackingLabel.': '.$info['tracking'].'</strong><br>';
								//}
								if($bookingno != '') { echo '<strong>'.$bookingnoLabel.': '.$bookingno.'</strong><br>'; }
								if($dispatchMeta['dispatchInfo']) { 
									foreach($dispatchMeta['dispatchInfo'] as $diVal) { 
										if($diVal[0]=='Booking No' && $invoicePDF != 'Trucking' && $bookingno == '') { echo '<strong>'.$diVal[0].': '.$diVal[1].'</strong><br>'; }
										//if($diVal[0]=='BOL #' && $invoicePDF != 'Trucking') { echo '<strong>'.$diVal[0].': '.$diVal[1].'</strong><br>'; }
										//if($diVal[0]=='Container No' && $invoicePDF != 'Trucking') { echo '<strong>'.$diVal[0].': '.$diVal[1].'</strong><br>'; }
									}
								}
								
                                    $trailer = array();
									
									if(strstr($info['trailer'],',')) {
										$trailer = explode(',',$info['trailer']);
									}
									elseif($info['trailer'] != ''){ $trailer[] = $info['trailer']; }
								   
									foreach($childTrailer as $val){ $trailer[] = $val['trailer']; }
									
									if($trailerValLabel != ''){ echo '<strong>'.$trailerValLabel.': '; }
									elseif($invoicePDF == 'Trucking') { echo '<strong>Trailer No.: '; }
									else { echo '<strong>Container No.: '; }
									
									if($trailerVal != ''){ echo $trailerVal; }
									elseif(count($trailer) == 0) { echo 'N/A'; } 
									//elseif(count($trailer) > 1) { echo 'Multiple'; } 
									else { 
									    $trailerss = implode(', ',$trailer); 
									    echo str_replace(' ,',',',str_replace('TBA','N/A',$trailerss));
									} 
									echo '</strong><br>';
									
                                    ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
			<!--tr class="notes">
                <td colspan="2"><hr></td>
			</tr-->
			
			<tr class="top">
                <td colspan="2" class="invoicetb">
                    <table border="1">
                        <tr class="heading">
							<td align="center" width="18%">Shipment&nbsp;Date</td>
							<td align="center" width="20%">PA Reference #</td>
							<td width="48%">Shipment Information</td>
							<td align="right" width="14%">Amount</td>
						</tr>
						<?php 
						/*
						<tr>
							<td align="center">
							    <?php 
							    $dropDate = date('m-d-Y',strtotime($info['dodate']));
							    if($extraDispatch){
							        foreach($extraDispatch as $exDispatch){
							            if($exDispatch['pd_type']=='dropoff'){
							                $dropDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
							            }
							        }
							    }
							    echo $dropDate;
							    ?>
							</td>
							<td align="center"><?=$info['invoice']?></td>
							<td><?=$info['invoiceNotes']?> <?php //if(count($trailer) > 1) { echo '(Trailer No.: '.implode(', ',$trailer).')'; } ?></td>
							<td align="right">
							<?php if($invoicePDF == 'Trucking'){ echo '$ '.$info['parate']; } ?>
							</td>
						</tr>
						*/ 
						$dropDate = date('m-d-Y',strtotime($info['dodate']));
						if($extraDispatch){
							foreach($extraDispatch as $exDispatch){
								if($exDispatch['pd_type']=='dropoff'){
									$dropDate = date('m-d-Y',strtotime($exDispatch['pd_date']));
								}
							}
						}
								
						if($invoicePDF != 'Trucking'){ ?>
							<tr><td align="center"><?php echo $dropDate; ?></td><td align="center"><?=$info['invoice']?></td><td><?=$info['invoiceNotes']?></td><td align="right"></td></tr>
							
							<tr><td>&nbsp;</td><td></td><td align="center" colspan="2"><strong>Itemized Charges</strong></td></tr>
							<?php  
							
							$totalAmt = 0; $disAmt = 0.00; 
							$trCount = 0;
							if($expense) { 
								foreach($expense as $key=>$expVal) {
									 if($key=='Discount'){
										$disAmt = $disAmt - $expVal['price'];
									 } else {
										$trCount++;
										echo '<tr><td></td><td></td><td align="right">'.$key.'';
										if($cExpense == 'true' && count($expVal['unit']) > 1){ 
											echo ' ('.implode(' + ',$expVal['unit']).') '; 
										}
										echo '</td><td align="right">';
										 $totalAmt = $totalAmt + $expVal['price'];
										echo '$ '.number_format($expVal['price'],2).'</td></tr>';
									 }
								}
							}
							/*if($dispatchMeta['expense']) { 
								foreach($dispatchMeta['expense'] as $expVal) {
									 if($expVal[0]=='Discount'){
										$disAmt = $disAmt - $expVal[1];
									 } else {
										$trCount++;
										echo '<tr><td></td><td></td><td align="right">'.$expVal[0].'</td><td align="right">';
										 $totalAmt = $totalAmt + $expVal[1];
										echo '$ '.number_format($expVal[1],2).'</td></tr>';
									 }
								}
							}*/
							for($c=$trCount;$c<4;$c++) {
								echo '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
							}
							?>
							<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
							<tr><td></td><td></td><td align="right"><strong>Subtotal</strong></td><td align="right">$ <?=number_format($totalAmt,2)?></td></tr>
							<?php if($disAmt > 0) { echo '<tr><td colspan="3" align="right"><strong>Discount</strong></td><td  align="right">$ '.number_format($disAmt,2).'</td></tr>'; } ?> 
							<tr><td colspan="3" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?=number_format(($totalAmt - $disAmt),2)?></strong></td></tr>
						<?php }
						else {  //////////// Trucking format ////////////
						    $discountAmt = $subtotal = $totalAmt = 0;
							$tr = $emptyTr = '';
							if($expense) { 
								
								//echo '<tr><td colspan="3" align="right"><strong>Subtotal</strong></td><td align="right">$ '.$info['parate'].'</td></tr>';
								foreach($expense as $key=>$expVal) {
									 $tr .= '<tr><td colspan="3" align="right"><strong>'.$key.'</strong>';
										if($cExpense == 'true' && count($expVal['unit']) > 1){ $tr .= ' ('.implode(' + ',$expVal['unit']).') '; }
										$tr .= '</td><td  align="right">';
									 if($key=='Discount'){ 
										$discountAmt = $discountAmt + $expVal['price'];
									 } else {
										 $totalAmt = $totalAmt + $expVal['price'];
									 }
									 $tr .= '$ '.number_format($expVal['price'],2).'</td></tr>';
								}
								$emptyTr .= '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
								for($c=count($expense);$c<5;$c++) {
									$emptyTr .= '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
								}
								$subtotal = ($info['parate'] + $discountAmt) - $totalAmt;
								$emptyTr .= '<tr><td colspan="3" align="right"><strong>Subtotal</strong></td> <td align="right">$ '.number_format($subtotal,2).'</td></tr>';
								$tr = $emptyTr.$tr;
								
							} else {
								$subtotal = $info['parate'];
								$tr .= '<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
								<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
								<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
								<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
								<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
								<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
								<tr><td colspan="3" align="right"><strong>Subtotal</strong></td><td align="right">$ '.number_format($info['parate'],2).'</td></tr>
								<tr><td colspan="3" align="right"><strong>Accessorial</strong></td><td  align="right">$ 0.00</td></tr>';
								//echo '<tr><td colspan="3" align="right"><strong>Discount</strong></td><td  align="right">$ 0.00</td></tr>';
							}
							?> 
							<tr><td align="center"><?php echo $dropDate; ?></td><td align="center"><?=$info['invoice']?></td><td><?=$info['invoiceNotes']?></td><td align="right"><?php echo '$ '.number_format($subtotal,2); ?></td></tr>
							<?=$tr?>
							<tr><td colspan="3" align="right"><strong>Total Amount Due</strong></td><td align="right"><strong>$ <?php 
							$grandTotal = $info['parate'] + $discountAmt;
							echo number_format($grandTotal,2)?><!--?=number_format($totalAmt,2)?--></strong></td></tr>
						<?php } ?>
                    </table>
                </td>
            </tr>
            
            <tr class="notes">
                <td colspan="2">
					<table border="0">
                        <tr class="heading">
							<td><p style="color: #fff;background: #1f3864;font-weight:normal;font-size: 12px;display: block;padding:0px;margin:0;">Notes:</p></td>
						</tr>
					</table>
                    1. Include the invoice number in your payment method to ensure accurate processing.<br>
                    2. Earn a 1.5% discount for payments within 7 days, and a 1% discount for payments within 7–15 days.<br>
                    3. Late payments will incur a 2% monthly fee on invoices aged beyond 30 days.<br>
                    4. Report service discrepancies within 10 days of receipt for resolution.<br>
                    5. Payment defaults may result in recovery actions; responsible parties will bear all associated legal costs.<br>
                    6. This invoice is legally binding. Retain a copy for your records.<br>
                    7. Make checks payable to <strong>PA Transport LLC.</strong>
                </td>
            </tr>
        </table>
        <div class="footer">
            Thank you for your business!<br>
        </div>
    </div>
</body>
</html>
<?php }
else {
    echo '<h2>This Invoice is not exit please check URL.</h2>';
}